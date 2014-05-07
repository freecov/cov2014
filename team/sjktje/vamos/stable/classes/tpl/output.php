<?php
Class Tpl_output {
	/* constants */
	const include_dir = "classes/tpl/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "tpl";

	private $buffer = '';
	private $cms;
	private $path;
	private $output_started = 0;
	private $file_loader = array();

	public $default_page;
	public $pageid;
	public $vars = array();
	private $base_dir;
	private $plugins;

	private $page_cache = array();
	private $page_aliases = array();
	private $base_condition;
	private $base_condition_menu;

	private $page_elements = array();
	private $public_roots = array(-1);
	private $special_roots = array();

	private $menu_loaded = 0;
	public $pagesize = 20;

	public $http_host;
	public $siteroot;
	private $apc_fetch;

	public function __construct() {

		$this->http_host = strtolower($_SERVER["HTTP_HOST"]);

		$this->cms = new Cms_data();
		$GLOBALS["covide"]->webroot = preg_replace("/page\/$/s", "", $GLOBALS["covide"]->webroot);

		$q = " isPublic=1 and isActive=1 ";
		$q.= " and ((date_start = 0 OR date_start IS NULL OR date_start <= ".mktime().") ";
		$q.= " and (date_end = 0 OR date_end IS NULL OR date_end >= ".mktime().")) ";
		$this->base_condition = $q;

		$q = "isMenuItem=1 and ".$q;
		$this->base_condition_menu = $q;

		$this->menu_cache_file = $GLOBALS["covide"]->temppath."menu";

		/* get default page from db */
		$q = "select cms_defaultpage from cms_license";
		$res = sql_query($q);
		$this->default_page       = sql_result($res,0);
		$this->siteroot           = 0;
		$this->special_roots["R"] = $this->default_page;

		/* try to determine the default siteroot page */
		$q = sprintf("select cms_defaultpage, pageid from cms_license_siteroots where
			cms_hostnames like '%%%s%%'", $this->http_host);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->siteroot     = $row["pageid"];
			$this->default_page = $row["cms_defaultpage"];
		}

		/* get default public root (domains) */
		$q = "select id, isSpecial from cms_data where (parentPage = 0 or parentPage is null) and (isPublic = 1 or apEnabled = ".$this->siteroot.")";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if (!in_array($row["isSpecial"], array("X","D")))
				$this->addPublicRoot($row["id"]);
			else
				$this->special_roots[$row["isSpecial"]] = $row["id"];
		}

		/* handle current request or page */
		$this->handlePage();
		$this->switchCustomModes();

	}

	public function addPublicRoot($siteroot) {
		$this->public_roots[$siteroot]=$siteroot;
	}

	private function checkInternalRequest($page=0) {
		if (!$page)
			$page = $this->pageid;

		if ( substr($page, 0, 2) == "__" ) {
			return 1;
		} else {
			return 0;
		}
	}
	public function init_aliaslist() {
		/* retrieve alias table */
		$ids_alias =& $this->page_aliases;
		$q = "select id, pageAlias from cms_data where pageAlias != ''";
		$res_alias = sql_query($q);
		while ($row_alias = sql_fetch_assoc($res_alias)) {
			$ids_alias[$row_alias["id"]]=$row_alias["pageAlias"];
		}
	}

	public function getTemplateById($id) {
		$cms_data =& $this->cms;
		$data = $cms_data->getTemplateById($id);
		return $data;
	}

	/* start html output */
	public function start_html($header=0, $textmode=0) {
		$output = new Layout_output();
		$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
		$output->addTag("html");
		$output->addTag("head");
		echo $output->generate_output();
		if ($header) {
			$this->html_header("", $textmode);
		}
		foreach ($this->file_loader as $file) {
			echo $file;
		}
		echo "</head>\n";
		echo "<body>\n";
		echo sprintf("<!-- page id [%d] -->\n", $this->pageid);
		$this->output_started = 1;
	}
	/* end html output */
	public function end_html() {
		$output = new Layout_output();
		$output->endTag("body");
		$output->endTag("html");
		echo $output->generate_output();
	}
	public function html_header($page=0, $textmode=0) {
		require(self::include_dir."html_header.php");
		echo $output->generate_output();
	}

	public function exec_inline($id, $return=0) {
		$data = $this->getTemplateById($id);
		if ($data["category"] != "php" && $data["category"] != "main") {
			echo "script execution denied due wrong file type: ".$data["category"]."<br>";
		}
		#$prepare = str_replace($this->parser["IN"], $this->parser["OUT"], $data["data"]);
		$prepare = $data["data"];
		if ($return) {
			return $prepare;
		} else {
			eval("global \$template; ?>".$prepare."<?");
		}
	}
	public function load_inline($id) {
		if (is_numeric($id)) {
			$data = $this->getTemplateById($id);
		} elseif ($id == "mvblog") {
			/* load all css files */
			$q = sprintf("select * from cms_templates where category = 'mvblog' order by id");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$data["data"].= sprintf("\n/* %s */\n", $row["title"]);
				$data["data"].= $row["data"]."\n";
			}
		}
		echo $data["data"];
	}
	public function getPath($pageid, $sub=0) {
		$path =& $this->path;
		if ($sub==0) {
			$path[] = $pageid;
		}
		if ($pageid > 0) {
			$q = sprintf("select parentpage from cms_data where id = %d", $pageid);
			$res = sql_query($q) or die($q);
			if (sql_num_rows($res)>0) {
				$ret = sql_result($res,0);
				$path[] = $ret;
				$foo = $this->getPath($ret, 1);
			}
		}
		if (!$sub) {
			$flag = 0;
			foreach ($path as $k=>$v) {
				if ($flag) {
					unset($path[$k]);
				}
				if ($v == $this->default_page) {
					$flag = 1;
				}
			}
		}
		return array_reverse($path);
	}
	public function displayPath($pageid) {
		if ($this->checkInternalRequest($pageid)==0) {
			$path = $this->getPath($pageid);
			$pages = $this->getPagesById($path);

			foreach ($path as $id) {
				$v = $pages[$id];
				$i++;
				if ($i > 1) {
					echo " - ";
				}
				if ($v["id"] == $this->default_page) {
					echo sprintf("<a href='%s'>%s</a>", "/", $v["pageTitle"]);
				} elseif ($v["id"] == $this->pageid) {
					echo sprintf("%s", $v["pageTitle"]);
				} else {
					echo sprintf("<a href='/page/%s'>%s</a>", $this->checkAlias($v["id"]), $v["pageTitle"]);
				}
			}
		}
	}
	public function getPagesById($ids) {
		$data = array();
		$q = sprintf("select id, pageTitle from cms_data where id IN (%s)", "0".implode(",", $ids));
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = "/page/".$this->checkAlias($row["id"]);
			$data[$row["id"]] = $row;
		}
		return $data;
	}
	public function getPagesByParent($parent, $order="pageLabel, pageTitle", $limit=0) {
		$data = array();
		$condition = $this->base_condition;
		if ($limit) {
			$sql_limit = " LIMIT ".(int)$limit;
		}
		$q = sprintf("select id, pageTitle, datePublication from cms_data where parentpage = %d AND %s order by %s %s",
			$parent, $condition, $order, $sql_limit);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = "/page/".$this->checkAlias($row["id"]);
			$data[$row["id"]] = $row;
		}
		return $data;
	}

	public function getMainPage() {
		$q = "select id from cms_templates where category = 'main'";
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			die("error: main page not defined!");
		else
			return sql_result($res,0);
	}

	private function linkcheckerPageList() {
		if ($_REQUEST["page"]) {
			/* get _all_ cms pages including the protected ones */
			$q = sprintf("select pageRedirect from cms_data where id = %d", $_REQUEST["page"]);
			$res = sql_query($q);
			$redir = sql_result($res,0);
			if ($redir)
				echo sprintf("<a href='%s'>page redirect</a><br>", $redir);

			echo $this->getPageData($_REQUEST["page"], "", 1);

		} else {
			$q = sprintf("select id from cms_data order by id");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				echo sprintf("<a href='/mode/linkchecker&page=%1\$d'>page id %1\$d</a><br>", $row["id"]);
			}
		}
	}
	private function linkcheckerAuth() {
		if ($_REQUEST["user"])
			$_SESSION["linkchecker_auth"] = 0;

		if (!$_SESSION["linkchecker_auth"]) {
			$user = (int)$_REQUEST["user"];
			$hash = $_REQUEST["hash"];

			$db_hash = $this->cms->linkcheckerHash($user);
			if ($hash != $db_hash) {
				$this->triggerError(403);
				exit();
			}
			else
				$_SESSION["linkchecker_auth"] = 1;
		}
	}
	private function handlePage() {
		$page =& $this->pageid;

		if ($_REQUEST["mode"]) {
			switch ($_REQUEST["mode"]) {
				case "sitemap":
				case "sitemap_plain":
					$page = "__sitemap"; break;
				case "sitemap"  : $page = "__sitemap";  break;
				case "search"   : $page = "__search";   break;
				case "google"   : $page = "__google";   break;
				case "googlegz" : $page = "__googlegz"; break;
				case "forum"    : $page = "__forum";    break;
				case "rss"      : $page = "__rss";      break;
				case "metadata" : $page = "__metadata"; break;
				case "linkchecker" : $page = "__linkchecker";
					$this->linkcheckerAuth();
					$this->linkcheckerPageList();
					exit();
					break;
				case "blog"     :
					$page = "__blog";
					/* filesys dir */
					$base_dir = "plugins/mvblog/";
					/* web base dir */
					$this->base_dir = "/blog/";
					/* load the class */
					require_once($base_dir."common/mvblog.php");
					$this->plugins["mvblog"] = new MvBlog($base_dir);
					break;
				case "blogadmin":
					$page = "__blog";
					$this->syncBlogAuthors();
					$this->redirBlogAdmin();
					break;
			}
		}
		if (!preg_match("/^__/s", $page)) {
			if ($_REQUEST["id"]) {
				$page = $_REQUEST["id"];
			} elseif ($_REQUEST["page"]) {
				$page = $_REQUEST["page"];
			} else {
				$page = "__def";
			}
		}

		if (is_numeric($page)) {
			/* check for domain permissions */
			$q = sprintf("select apEnabled from cms_data where id = %d", $page);
			$res = sql_query($q);
			$ap = sql_result($res,0);

			if (!in_array($ap, $this->public_roots))
				$page = "__err403";
		}

		if ($page == "__def")
			$this->pageid = $this->default_page;

		if ($this->checkInternalRequest()==0) {
			$page = preg_replace("/\.((htm)|(html))/si","",$page);
			if (!is_numeric($page)) {
				/* check alias existance */
				$q = "select id from cms_data where pageAlias like '$page'";
				$res = sql_query($q);
				if (sql_num_rows($res)==1) {
					$page = sql_result($res,0);
				} else {
					$page = "__err404"; // go to 404 later
				}
			} else {
				/* check page existance */
				$q = "select count(id) from cms_data where id = ".(int)$page;
				$res = sql_query($q);
				if (sql_result($res,0) != 1) {
					$page = "__err404";
				}
			}
		}
		if (is_numeric($page))
			$this->getRedirEndPoint($page);
	}

	public function getRedirEndPoint($page) {
		$q = sprintf("select pageRedirect, pageRedirectPopup, popup_data from cms_data where id = %d", $page);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		if ($row["pageRedirect"]) {
			if ($row["pageRedirectPopup"]) {

				if (!preg_match("/(^\/)|(:\/\/)/s", $row["pageRedirect"]))
					$row["pageRedirect"] = "/page/".$row["pageRedirect"];

				$popup_data = explode("|", $row["popup_data"]);
				$opts = "top=0,left=0";
				if ($popup_data[0] && $popup_data[1])
					$opts.= sprintf(",height=%s,width=%s", $popup_data[0], $popup_data[1]);

				if ($popup_data[2])
					$opts.= ",toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1";
				else
					$opts.= "toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1";

				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode( sprintf("var cvd_%s = setTimeout(\"window.open('%s', '%s', '%s');\", 500);",
						md5(rand()), $row["pageRedirect"], "cmswindow_".mktime(), $opts ));
				$output->end_javascript();
				echo $output->generate_output();

			} else {
				header( sprintf("Location: %s", $row["pageRedirect"]) );
				exit();
			}
		}
	}

	public function setDefaultPage($id) {
		$this->default_page = $id;
	}

	public function checkAlias($id) {
		$ids_alias =& $this->page_aliases;
		if ($ids_alias[$id]) {
			return $ids_alias[$id].".htm";
		} else {
			return $id.".htm";
		}
	}
	public function load_css($id) {
		$code = sprintf("<link rel='stylesheet' type='text/css' href='/include/%d'>\n", $id);
		if ($this->output_started) {
			echo $code;
		} else {
			$this->file_loader[] = $code;
		}
	}
	public function load_js($id) {
		$code = sprintf("<script language='Javascript1.2' type='text/javascript' src='/include/%d'></script>\n", $id);
		if ($this->output_started) {
			echo $code;
		} else {
			$this->file_loader[] = $code;
		}
	}
	public function getPageTitle($id, $size=1) {
		if ($this->checkInternalRequest($id)==1) {
			switch ($id) {
				case "__sitemap":
					$title = "Sitemap";
					break;
				case "__search":
					$title = "Zoekresultaten";
					break;
				case "__metadata":
					$title = "Custom search";
					break;
			}
		} else {
			if (!$this->page_cache[$id]) {
				$this->page_cache[$id] = $this->cms->getPageById($id, "", 1);
			}
			$page  =& $this->page_cache[$id];
			$title =& $page["pageTitle"];

		}
		if ($size == -1) {
			echo $title;
		} else {
			echo sprintf("<H%d>%s</H%d>", $size, $title, $size);
		}
	}
	public function getPageAlias($id) {
		return $this->checkAlias($id);
	}
	public function getPageById($id) {
		$data = $this->getPagesById(array($id), 0, 1);

		$q = sprintf("select pageData from cms_data where id = %d", $id);
		$res = sql_query($q);
		$data[$id]["pageData"] = sql_result($res,0);
		return $data;
	}

	private function generateSitemap($startPage=0, $parse_level=2) {
		if (!$startPage)
			$startPage = $this->default_page;

		$data = $this->getPagesById(array($startPage));
		$this->generateSitemapItem($data[$startPage]);
		$this->generateSitemapTree($startPage, 1, $parse_level);
		echo "<BR>";
	}
	private function generateSitemapTree($page, $level, $parse_level) {
		if ($level <= $parse_level) {
			$pages = $this->getPagesByParent($page);
			foreach ($pages as $v) {
				$this->generateSitemapItem($v, $level);
				$this->generateSitemapTree($v["id"], $level+1, $parse_level);
			}
		}
	}
	private function generateSitemapItem($v, $level=0) {
			if ($level>0) {
			$class = "sitemap_item_sub";
		} else {
			$class = "sitemap_item_main";
		}
		if ($_REQUEST["mode"] == "sitemap_plain") {
			if ($page == $this->default_page) {
				$url = "/text/";
			} else {
				$url = str_replace("/page/", "/text/", $v["alias"]);
			}
		} else {
			if ($page == $this->default_page) {
				$url = "/";
			} else {
				$url = $v["alias"];
			}
		}
		echo "<div style='padding-left: ".($level*8)."px;' class='$class'><a href='$url'>".$v["pageTitle"]."</a></div>";
	}

	public function getPageData($id, $prefix="page", $no_inline_edit=0) {
		if ($this->checkInternalRequest($id)==1) {
			switch ($id) {
				case "__metadata":
					switch ($_REQUEST["show"]) {
						case "result":
						case "rss":
						default:
							if (is_array($_REQUEST["data"]))
								$this->metaShowResults($_REQUEST["data"]);
							else
								$this->metaShowOptions();
							break;
					}
					break;
				case "__sitemap":
					$this->generateSitemap();
					break;
				case "__err403":
					$this->triggerError(403);
					break;
				case "__err404":
					$this->triggerError(404);
					//fallback to search (next case)
				case "__search":
					require(self::include_dir."search.php");
					break;
				case "__blog":
					$this->plugins["mvblog"]->blog_content(0,0);
					break;
				case "__forum":
					$output_alt = new Layout_output();
					$output_alt->insertTag("iframe", "", array(
						"onload" => "forum_resize_frame();",
						"id"     => "iframe",
						"src"    => "plugins/punbb/upload/",
						"style"  => "width:100%; height: 600px;",
						"frameborder" => 0,
						"border" => 0
					));
					echo $output_alt->generate_output();
					break;
			}
		} else {
			if (!$this->page_cache[$id]) {
				$this->page_cache[$id] = $this->cms->getPageById($id, "", 1);
			}
			$page =& $this->page_cache[$id];
			$data =& $page["pageData"];

			/* apply filters */
			$this->handleRewrites($data, $prefix);
			$this->handleAltArgs($data);

			/* only allow lists to selected page id */
			if ($this->page_cache[$id]["isList"])
				$this->handleList($data, $id);

			if ($this->page_cache[$id]["isGallery"])
				$this->handleGallery($data, $id);

			if ($this->page_cache[$id]["isForm"]) {
				if ($this->page_cache[$id]["form_mode"] == 2)
					$this->handleEnquete($data, $id);
				else
					$this->handleForm($data, $id);
			}
			$data = str_replace(" />", ">", $data);

			$data = $this->checkPageElements($data);
			echo $data;

		}
		if ($_SESSION["user_id"]) {
			$output_alt = new Layout_output();
			$user_data = new User_data();
			$user_perm = $user_data->getUserDetailsById($_SESSION["user_id"]);
			if ($id == "__blog") {
				/* set some blog session vars */
				$_SESSION["author_id"]       = $_SESSION["user_id"];

				$_SESSION["author_name"]     = $user_perm["username"];
				$_SESSION["author_fullname"] = $user_perm["username"];
				$_SESSION["author_email"]    = $user_perm["mail_email"];
				$_SESSION["author_website"]  = "http://".$_SERVER["HTTP_HOST"];
				$_SESSION["blog_user"]       = 1;

				$output_alt->insertAction("view_all", gettext("admin mode"), sprintf("javascript: blogAdmin();", $this->pageid));
				$output_alt->addSpace(2);
			} else {
				if ($user_perm["xs_cms_level"] > 0) {
					if (in_array($user_perm["xs_cms_level"], array(2,3))) {
						$page_xs = 1;
					} else {
						$perm = $this->cms->getUserPermissions($this->pageid, $_SESSION["user_id"]);
						if ($perm["editRight"]) {
							$page_xs = 1;
						}
					}
				}
				if (!$no_inline_edit) {
					if ($page_xs) {
						$output_alt->insertAction("edit", gettext("edit page"), sprintf("javascript: cmsEdit('%d')", $this->pageid));
						$output_alt->addSpace(2);
					}
					$output_alt->insertAction("view_all", gettext("sitemap"), "?mod=cms");
					$output_alt->addTag("a", array(
						"target" => "_blank",
						"href"   => "http://www.covide.net"
					));
					$output_alt->addCode(sprintf(" <small>%s</small>", gettext("powered by Covide-CMS")));
					$output_alt->endTag("a");
				}
			}
			echo $output_alt->generate_output();
		}
	}

	private function metaShowResults($data) {
		require(self::include_dir."showMetaResults.php");
	}

	private function metaShowOptions() {
		require(self::include_dir."showMetaOptions.php");
	}

	private function getVeldValue($fieldname, $pageid) {
		if (preg_match("/^meta\d{1,}/si", $fieldname)) {
			$name = preg_replace("/^meta/si", "", $fieldname);
			$q = sprintf("select * from cms_metadata where fieldid = %d and pageid = %d",
				$name, $pageid);
			$res = sql_query($q);
			return sql_result($res,0);

		} else {
			/* conversion 5.x to 6.x+ */
			switch ($fieldname) {
				case "paginaTitel":
					$name = "pageTitle";
					break;
				case "datumPublicatie":
					$name = "datePublication";
					break;
				default:
					//die("invalid field name specified!");
			}
			if ($name) {
				$q = sprintf("select %s from cms_data where id = %d", $name, $pageid);
				$res = sql_query($q);
				if ($name == "datePublication")
					return date("d-m-Y", sql_result($res,0));
				else
					return sql_result($res,0);
			}

		}
	}

	private function handleRewrites(&$str, $prefix) {
		preg_match_all("/<a[^>]*?>/si", $str, $link);
		foreach ($link[0] as $orig) {
			$l = $orig;
			//replace server name
			$regex = "/ href=(\"|')http(s){0,1}:\/\/".$_SERVER["HTTP_HOST"]."/";
			$l = preg_replace($regex, "href=$1", $l);
			$l = preg_replace("/ href=(\"|')((index)|(site))\.php/si", " href=$1", $l);

			preg_match("/ href=(\"|')\?id=(\d{1,})[^(\"|')]*?(\"|')/si", $l, $ids);
			if ($ids[0]) {
				$alias = $this->page_aliases[$ids[2]];
				if ($alias) {
					$repl = str_replace("?id=".$ids[2], "/".$prefix."/".$alias.".htm", $ids[0]);
					$r = str_replace($ids[0], $repl, $l);
					$str = str_replace($orig, $r, $str);
				}
			}
		}
	}
	private function handleAltArgs(&$str) {
		preg_match_all("/<img[^>]*?>/si", $str, $img);
		foreach ($img[0] as $l) {
			//remove all existing title tags
			$r = preg_replace("/ title=\"[^\"]*?\"/si", "", $l);

			//if no alt tag is detected
			if (!preg_match("/ alt=\"[^\"]*?\"/si", $r)) {
				$r = preg_replace("/<img /si", "<img alt=\"\" ", $r);
			} else {
				//copy alt tags to title tags
				$r = preg_replace("/ alt=(\"[^\"]*?\")/si"," alt=$1 title=$1", $r);
			}

			$str = str_replace($l, $r, $str);
		}
	}
	public function preload_menu() {
		$output = new Layout_output();
		$output->load_javascript("/menudata/libjs/layersmenu-browser_detection.js", 1);
		$output->load_javascript("/menudata/libjs/layersmenu-library.js", 1);
		$output->load_javascript("/menudata/libjs/layersmenu.js", 1);
		$this->file_loader[] = $output->generate_output();
		$this->menu_loaded = 1;
	}
	public function generate_menu($id) {
		$output = new Layout_output();

		if (!$this->menu_loaded)
			$this->preload_menu();

		$output->start_javascript();
		$output->addCode(sprintf("addLoadEvent( document.write( loadXMLContent('/menu/%d') ) );", $id));
		$output->end_javascript();

		echo $output->generate_output();
		//$this->generate_menu_loader($id);
	}
	public function generate_menu_loader($id) {
		global $db;

		$fetch = $this->getApcCache("menu");
		if ($fetch) {
			echo $fetch;
		} else {
			$output = new Layout_output();
			require_once 'menudata/lib/PHPLIB.php';
			require_once 'menudata/lib/layersmenu-common.inc.php';
			require_once 'menudata/lib/layersmenu.inc.php';
			$mid = new LayersMenu();
			$mid->setTableName("cms_data");
			$mid->setTableFields(array(
				"id"		     => "id",
				"parent_id"	 => "parentPage",
				"text"	   	 => "pageTitle",
				"href"		   => "pageAlias",
				"icon"       => "",
				"title"		   => "pageTitle",
				"orderfield" => "pageLabel, datePublication desc",
				"expanded"	 => ""	,
				"target"     => ""
			));
			$mid->setPrependedUrl("/page/");
			$mid->setIconsize(16, 16);

			$q = $this->base_condition_menu;

			$mid->scanTableForMenu("hormenu1", "", $q, &$db, $id);
			$mid->newHorizontalMenu("hormenu1");

			$output->addCode( $mid->getHeader() );
			$output->addCode( $mid->getMenu('hormenu1') );
			$output->addCode( $mid->getFooter() );
			$buffer = $output->generate_output();

			$this->setApcCache("menu", $buffer);
			echo $buffer;
		}
	}

	public function getRandomPage($parent) {
		$rand = sql_syntax("random");
		$q = sprintf("select * from cms_data where parentPage = %d AND %s ORDER BY %s LIMIT 1", $parent, $this->base_condition, $rand);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			$row = sql_fetch_assoc($res);
			return $row;
		}
	}
	public function html2txt($data) {
		$data = preg_replace("/<br[^>]*?>/si", "<br>", $data);
		$data = strip_tags($data, "<br>");
		$data = preg_replace("/(\n)|(\r)|(\t)/s", "", $data);

		return $data;
	}
	public function triggerError($code) {
		switch ($code) {
			case 404:
				header("Status: 404 Not Found");
				echo "<h1>404 Not Found</h1>";
				break;
			case 403:
				header("Status: 403 Forbidden");
				echo "<h1>403 Forbidden</h1>";
				break;
			case 307:
				header("Status: 307 Temporary Redirect");
				break;
		}
	}

	public function handleList(&$data, $id=0) {
		if (!$pageid) $pageid = $this->pageid;
		require(self::include_dir."handleList.php");
	}

	public function handleForm(&$data, $id=0) {
		if (!$pageid) $pageid = $this->pageid;
		require(self::include_dir."handleForm.php");
	}
	public function handleEnquete(&$data, $id=0) {
		if (!$pageid) $pageid = $this->pageid;
		require(self::include_dir."handleEnquete.php");
	}

	public function handleGallery(&$data, $id=0) {
		if (!$pageid) $pageid = $this->pageid;
		require(self::include_dir."handleGallery.php");
	}

	public function getPageFooter($pageid) {
		echo "\n\n<div style='visibility: hidden; position: absolute; bottom: 0px; right: 0px;' id='print_container'></div>\n";
		echo "<a href='javascript: pageHistory();'>terug</a>";
		if (is_numeric($pageid) || !$pageid) {
			if (!$pageid) $pageid = $template->default_page;
			echo " | <a href='javascript: pagePrint(\"".$_REQUEST["page"]."\");'>print</a>";
			echo " | <a href='javascript: pageText(\"".$_REQUEST["page"]."\");'>textversie</a>";
		} elseif ($pageid == "__sitemap") {
			echo " | <a href='javascript: pageSitemapText();'>textversie</a>";
		}
		echo " | <a href='javascript: void();'>login</a>";
	}

	public function textPage() {
		$pageid =& $this->pageid;
		$this->init_aliaslist();

		if ($pageid == "__def")
			$pageid = "__sitemap";

		if ($pageid == "__sitemap")
			$alias = "/sitemap.htm";
		else
			$alias = "/page/".$this->checkAlias($pageid);

		$this->file_loader[]= "<link rel='stylesheet' type='text/css' href='/classes/tpl/inc/style_print.css'>";
		$this->start_html(1, 1);

		echo "<span class='noprint'>volledige versie van:</span>";
		echo "<a href='http://".$_SERVER["HTTP_HOST"].$alias."'>";
		echo "http://".$_SERVER["HTTP_HOST"]."/page/".$this->checkAlias($pageid);
		echo "</a>";

		$this->getPageTitle($pageid);
		$this->getPageData($pageid, "text");

		if ($_REQUEST["print"]) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(" setTimeout('window.print();', 200); ");
			$output->end_javascript();
			echo $output->generate_output();
		}
		echo "<span class='noprint'>\n<br><br>\n";
		echo "<a href='javascript: history.go(-1);'>terug</a>";
		echo " | <a href='javascript: window.print();'>print</a>";
		echo " | <a href='".$alias."'>volledige versie</a>";
		if ($pageid != "__sitemap")
			echo " | <a href='/sitemap_plain.htm'>sitemap</a>";
		echo "</span>\n";

		$this->end_html();
	}

	public function redir_default_page() {
		if ($this->pageid == $this->default_page && ($_REQUEST["page"] || $_REQUEST["id"])) {
			$this->triggerError(307);
			header("Location: /");
			exit();
		}
	}
	public function generate_robots_file() {
		$fetch = $this->getApcCache("robots");
		if ($fetch) {
			echo $fetch;
		} else {
			header("Content-Type: text/plain", 1);

			/* list root contents */
			$files = scandir(".");
			$files[]="include/";
			$files[]="covide/";
			$files[]="menu/";
			natcasesort($files);

			echo "User-agent: *\n";
			foreach ($files as $f) {
				if ($f != "." && $f != ".." && $f) {
					echo "Disallow: /".$f;
					if (file_exists($f) && is_dir($f))
						echo "/";
					echo "\n";
				}
			}
			$this->setApcCache("robots", ob_get_contents());
		}
	}
	public function switchCustomModes() {
		switch ($_REQUEST["mode"]) {
			case "form":
				$this->sendform($_REQUEST);
				exit();
			case "formresult":
				$this->formresult($_REQUEST["result"]);
				exit();
			case "menu":
				$this->generate_menu_loader((int)$_REQUEST["pid"]);
				exit();
			case "text":
				$this->textPage();
				exit();
			case "rss":
				$this->rssPage();
				exit();
			case "google":
				$this->googleMaps();
				break;
			case "googlegz":
				$this->googleItems();
				break;
			case "sitemap_plain":
				$this->textPage();
				exit();
			case "robots":
				$this->generate_robots_file();
				exit();
		}
	}
	public function googleMaps() {
		header("Content-Type: application/xhtml+xml");

		/* get basic info */
		$xml_main = file_get_contents(self::include_dir."google_map.xml");

		$q = sprintf("select max(date_changed) from cms_data where %s",	$this->base_condition);
		$res = sql_query($q);
		$last_modification = sql_result($res,0);

		$xml_main = str_replace("{location}", $GLOBALS["covide"]->webroot."sitemap.xml.gz", $xml_main);
		$xml_main = str_replace("{date}", date("c", $last_modification), $xml_main);
		echo $xml_main;
		exit();
	}
	public function googleItems() {
		//header("Content-Type: application/xhtml+xml");
		$devnull = ob_get_contents();
		ob_end_flush();
		ob_start();

		/* get basic info */
		$xml_main = file_get_contents(self::include_dir."google_main.xml");
		$xml_item = file_get_contents(self::include_dir."google_item.xml");

		/* get default page */
		$data = $this->cms->getPageById($this->default_page);
		$this->googleRecord($xml_main, $xml_item, $data);

		/* get all (public) items */
		$google_limit = 50000-2;

		$q = sprintf("select id, google_changefreq, google_priority, date_changed
			from cms_data where apEnabled IN (%s) and %s and id != %d order by date_changed desc LIMIT %d",
				implode(",", $this->public_roots),	$this->base_condition, $this->default_page, $google_limit);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->googleRecord($xml_main, $xml_item, $row);
		}
		$xml_main = str_replace("{records}", "", $xml_main);

		/* gz encode the result */
		$xml_main = gzencode($xml_main, 5);
		header("Content-type: application/x-gzip");
		echo $xml_main;
		exit();
	}
	private function googleRecord(&$xml_main, $xml_item, $record) {
		$vars = array(
			"location"    => sprintf("http://%s/page/%s", $this->http_host, $this->checkAlias($record["id"])),
			"date"        => date("c", $record["date_changed"]),
			"change_freq" => $record["google_changefreq"],
			"priority"    => ($record["google_priority"] != "0.5") ? $record["google_priority"]:""
		);
		if ($this->default_page == $record["id"])
			$vars["location"] = sprintf("http://%s", $this->http_host);

		foreach ($vars as $k=>$v) {
			$v = strip_tags($v);
			$xml_item = str_replace(sprintf("{%s}", $k), $v, $xml_item);
			if ($k == "priority" && !$v)
				$xml_item = str_replace("<priority></priority>\n\t\t", "", $xml_item);
		}
		$xml_main = preg_replace("/(\{records\})/s", "\n".$xml_item."\n$1", $xml_main);
	}

	public function rssPage() {
		header("Content-Type: application/xhtml+xml");

		/* get basic info */
		$xml_main = file_get_contents(self::include_dir."rss_main.xml");
		$xml_item = file_get_contents(self::include_dir."rss_item.xml");

		/* some global vars */
		$settings = $this->cms->getCmsSettings();
		$vars = array(
			"title"       => $settings["cms_name"],
			"link"        => $GLOBALS["covide"]->webroot,
			"description" => $settings["search_descr"],
			"language"    => $settings["search_language"],
			"copyright"   => $settings["search_copyright"],
			#"date"        => mktime(),
			"webmaster"   => $settings["search_email"],
			"author"      => $settings["search_author"],
			"favicon"     => $GLOBALS["covide"]->webroot."img/logo.gif"
		);
		foreach ($vars as $k=>$v) {
			$v = strip_tags($v);
			$xml_main = str_replace(sprintf("{%s}", $k), $v, $xml_main);
		}

		if ($_REQUEST["live"]) {
			$data = $this->cms->getPageById($_REQUEST["live"]);
			$row["author"] = $vars["author"];
			$this->rssRecord($xml_main, $xml_item, $data);

		} elseif ($_REQUEST["parent"]) {
			$pages = $this->getPagesByParent($_REQUEST["parent"]);
			foreach ($pages as $id=>$page) {
				$data = $this->cms->getPageById($id);
				$this->rssRecord($xml_main, $xml_item, $data);
			}

		} else {
			/* get current feeds of the current public domain list */
			$q = sprintf("select * from cms_data where
				apEnabled IN (%s) and %s order by datePublication desc LIMIT 20",
				implode(",", $this->public_roots), $this->base_condition);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["author"] = $vars["author"];
				$this->rssRecord($xml_main, $xml_item, $row);
			}
		}
		$xml_main = str_replace("{records}", "", $xml_main);
		echo $xml_main;
		exit();
	}
	private function rssRecord(&$xml_main, $xml_item, $record) {
		$vars = array(
			"title"       => $record["pageTitle"],
			"date"        => date("r", $record["datePublication"]),
			"description" => trim(preg_replace("/ {2,}/s", " ", $this->html2txt($record["pageData"]))),
			"author"      => $record["author"],
			"category"    => "last updates",
			"link"        => sprintf("http://%s/page/%s", $this->http_host, $this->checkAlias($record["id"]))
		);
		if (mb_strlen($vars["description"]))
		 	$vars["description"] = mb_substr($vars["description"], 0, 500)."...";

		if ($vars["description"] == "...")
			$vars["description"] = $vars["title"];

		foreach ($vars as $k=>$v) {
			if ($k == "date")
				$xml_main = str_replace("{date}", $v, $xml_main);

			$v = strip_tags($v);
			$xml_item = str_replace(sprintf("{%s}", $k), $v, $xml_item);
		}
		$xml_main = preg_replace("/(\{records\})/s", "\n".$xml_item."\n$1", $xml_main);
	}

	private function createVistorRecord($pageid) {
		/* generate unique visitor hash */
		$hash = md5(rand().mktime());

		/* insert visitor record */
		$q = sprintf("insert into cms_form_results_visitors (pageid, visitor_hash,
			datetime_start, datetime_end, ip_address) values (%d, '%s', %d, %d, '%s')",
			$pageid, $hash, mktime(), mktime(), $_SERVER["REMOTE_ADDR"]);
		sql_query($q);
		$newid = sql_insert_id("cms_form_results_visitors");
		return $newid;
	}
	public function updateFieldRecord($pageid, $field, $value, $visitor_id) {
		$q = sprintf("select id from cms_form_results where pageid = %d and
			visitor_id = %d and field_name = '%s'", $pageid, $visitor_id, addslashes($field));
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			$rowid = sql_result($res,0);
			$q = sprintf("update cms_form_results set user_value = '%s' where id = %d",
				addslashes($value), $rowid);
			sql_query($q);
		} else {
			$q = sprintf("insert into cms_form_results (pageid, field_name, visitor_id,
				user_value) values (%d, '%s', %d, '%s')", $pageid, addslashes($field),
				$visitor_id, addslashes($value));
			sql_query($q);
		}
	}
	private function sendform($req) {
		/* get form mode */
		$mode = $this->cms->getFormMode($req["system"]["pageid"]);
		/* if store to db */
		if ($mode == 1) {

			$newid = $this->createVistorRecord($req["system"]["pageid"]);
			foreach ($req["data"] as $k=>$v) {
				$this->updateFieldRecord($req["system"]["pageid"], $k, $v, $visitor_id);
			}
		}

		$key = sprintf("s:{%s} p:{%s}", session_id(), $req["system"]["pageid"]);

		$q = sprintf("select count(*) from cms_temp where userkey = '%s' and ids = '%s'",
			$key, $_REQUEST["system"]["challenge"]);
		$res = sql_query($q);
		$num = sql_result($res,0);
		if ($num == 1) {
			$forms = $this->cms->getFormData($req["system"]["pageid"]);

			$email_data = new Email_data();

			$smtp["rcpt"]    = "";
			$smtp["from"]    = "";
			$smtp["subject"] = "";
			$smtp["result"]  = "";

			$uri = sprintf("http://%s/page/%s", $_SERVER["HTTP_HOST"], $this->checkAlias($req["system"]["pageid"]));

			$html = "<table class='table1' style='background-color: white;'>";
			$html.= sprintf("<tr><td class='head1' colspan='2'>%s</td></tr>",
				gettext("Information from form on website").":");

			foreach ($forms as $k=>$v) {
				if ($v["field_type"] == "hidden")
					$req["data"][$v["field_name"]] = $v["field_value"];

				if ($v["is_mailto"]) {
					$smtp["rcpt"] = $req["data"][$v["field_name"]];
				} elseif ($v["is_mailfrom"]) {
					$smtp["from"] = $req["data"][$v["field_name"]];
				} elseif ($v["is_mailsubject"]) {
					$smtp["subject"] = $req["data"][$v["field_name"]];
				} elseif ($v["is_redirect"]) {
					$smtp["result"] = $req["data"][$v["field_name"]];
				} else {
					/* normal field */
					if ($v["field_type"] == "checkbox")
						$req["data"][$v["field_name"]] = implode(", ", $req["data"][$v["field_name"]]);

					$html.= sprintf("<tr><td class='cell1'>%s</td><td class='cell2'>%s</td></tr>",
						$v["field_name"], $req["data"][$v["field_name"]]);
				}
			}
			$html.= sprintf("<tr><td class='head1' colspan='2'>%s</td></tr>",
				sprintf("<a href='%s' target='_new'>%s</a>", $uri, $uri));
			$html.= "</table>";
			$html = $email_data->stylehtml($html);

			$headers  = "MIME-Version: 1.0"."\n";
			$headers .= "Content-type: text/html; charset=UTF-8"."\n";
			$headers .= "Content-Transfer-Encoding: quoted-printable\n";
			$headers .= sprintf("From: <%s>", $smtp["from"])."\n";
			$headers .= sprintf("To: <%s>", $smtp["rcpt"])."\n";
			$headers .= sprintf("Subject: %s", $smtp["subject"])."\n";

			mail(
				$smtp["rcpt"],
				$email_data->mime_encode($smtp["subject"]),
				$email_data->quoted_printable_encode($html),
				$headers,
				"-f".$smtp["from"]
			);

			/* remove key */
			$output = new Layout_output();
			$output->start_javascript();
				/*
				$output->addCode(sprintf(" alert('%s'); ",
					addslashes(gettext(
						"Uw aanvraag is verstuurd."
					))
				));
				*/
				$output->addCode(sprintf(" document.location.href = 'site.php?mode=formresult&result=%s'; ",
					addslashes(urlencode($smtp["result"])) ));
			$output->end_javascript();
			echo $output->generate_output();

		} else {
			/* not accepted */
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(sprintf(" alert('%s'); ",
					addslashes(gettext(
						"Het formulier kon niet verstuurd worden. Mogelijke oorzaken zijn dat u geen geldige aanroep gebruikt of de pagina voor meerdere uren open hebt laten staan alvorens te versturen."
					))."\\n\\n".
					addslashes(gettext(
						"Ververs de pagina en probeer het opnieuw."
					))
				));
			$output->end_javascript();
			echo $output->generate_output();
		}
	}
	private function formresult($result) {
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				parent.document.location.href = '%s';
			", $result));
		$output->end_javascript();
		$output->exit_buffer();
	}
	public function loadGalleryFile($id, $size) {
		$this->cms->loadGalleryFile($id, $size);
	}
	public function loadBlogNavigation() {
		$output = new Layout_output();
		$output->insertTag("a", gettext("Blog home"), array(
			"href" => "index.php"
		));
		$output->addTag("br");
		$output->addTag("br");
		echo $output->generate_output();

		$this->plugins["mvblog"]->blog_archive_links();
		echo "<br>";
		$this->plugins["mvblog"]->blog_cats_links();

		$output->buffer = "";
		$output->addTag("form", array(
			"action"   => "#",
			"id"       => "searchform",
			"method"   => "post",
			"onsubmit" => "if (this.submitted) return true; else return false;"
		));
		$output->addTag("input", array(
			"type" => "text",
			"name" => "blog_search",
			"id"   => "blog_search",
			"alt"  => "search"
		));
		$output->endTag("form");
		$output->insertTag("div", "", array("id" => "searchresults"));
		$output->load_javascript(self::include_dir."blogLiveSearch.js");
		$output->addTag("br");
		$output->addTag("a", array(
			"target" => "_blank",
			"href"   => "http://www.mvblog.org"
		));
		$output->insertTag("small", gettext("powered by MvBlog"));
		$output->endTag("a");

		$this->plugins["mvblog"]->blog_show_menulinks();
		echo $output->generate_output();
	}

	private function syncBlogAuthors() {
		/* filesys dir */
		$base_dir = "plugins/mvblog/";
		/* web base dir */
		$this->base_dir = "/blog/";
		/* load the class */
		require_once($base_dir."common/mvblog_admin.php");
		$mvblog = new MvBlog_Admin($base_dir);

		/* get all mvblog authors */
		$mvblog->_get_authors();

		/* get all covide users with blog access (currently cms manage users) */
		$user_data = new User_data();
		$user_list = $user_data->getUserList(1);
		#print_r($user_list);
		foreach ($user_list as $id=>$name) {
			/* get user details */
			$user_details = $user_data->getUserDetailsById($id);

			/* some basic data */
			$passwd = md5(rand());
			$user = array(
				"password"  => $passwd,
				"password1" => $passwd,
				"login"     => $name,
				"email"     => $user_details["mail_email"],
				"fullname"  => $name,
				"active"    => 1,
				"website"   => "http://".$_SERVER["HTTP_HOST"],
				"id"        => $id
			);
			/* remove default mvblog admin */
			$mvblog->db->query("delete from authors where login = 'mvblog' and password = 'mvblog'");

			/* if the user already exist */
			if (!$mvblog->authors[$id]) {
				$res = $mvblog->db->query(sprintf("insert into authors (id, login) values (%d, '%s')", $id, $name));
				if (PEAR::isError($res))
					die($res->getUserInfo());
			}
			$mvblog->save_author($user, 1);
		}
	}

	private function checkPageElements($data) {
		$rcel =& $this->page_elements;
		if (preg_match("/\%\%\%\d{1,}\%\%\%/s", $data)) {
			$el = preg_split("/(\%\%\%\d{1,}\%\%\%)/s", $data, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach ($el as $k=>$v) {
				if (preg_match("/\%\%\%\d{1,}\%\%\%/s", $v)) {
					$v = str_replace("%", "", $v);
					if (in_array($v, $rcel)) {
						$el[$k] = "<font color='red'><small>(error: recursion detected in page element)</small></font><br>";
					} else {
						$rcel = array_merge($rcel, array($v));
						$pdata = $this->getPageById((int)$v);
 						$el[$k] = $this->checkPageElements($pdata[$v]["pageData"]);
					}
				}
			}
			$data = implode("\n", $el);
		}
		return $data;
	}

	public function redirBlogAdmin() {
		header("Location: /plugins/mvblog/admin/");
	}

	public function getApcCache($ident) {
		/* if apc functions do not exists or if a user is logged in, we bypass the cache */
		if (function_exists('apc_fetch') && !$_SESSION["user_id"]) {
			$fetch = apc_fetch(sprintf($ident));
			if ($fetch) {
				$this->apc_fetch = 1; //this is a apc fetched result
				header("Apc-cache: true");
				return unserialize(gzuncompress($fetch));

			}
		}
	}
	public function setApcCache($ident, $contents) {
		/* if apc functions do not exists or if a user is logged in or this call was done
				after a successfull apcfetch command, we bypass the cache */
		if (function_exists('apc_fetch') && !$_SESSION["user_id"] && !$this->apc_fetch)
			apc_store($ident, gzcompress(serialize($contents),1), 60);
	}
}
