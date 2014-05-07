<?php
/**
 * Covide CMS Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Tpl_output {
	/* constants */
	const include_dir = "classes/tpl/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "tpl";

	public $page_cache_timeout = 60; //1 min
	public $keys_cache_timeout = 14400; //4 hours
	public $rss_cache_timeout =  300; //5 minutes
	private $buffer = '';
	private $cms;
	private $filesys;
	private $filesys_folder_cache;

	private $_path;
	private $output_started = 0;
	private $file_loader = array();
	private $textmode;

	private $http_error;
	public $default_page;
	public $pageid;
	public $path;
	public $vars = array();

	private $base_dir;
	private $plugins;

	private $page_cache = array();
	private $meta_cache = array();
	private $address_cache = array();

	private $page_aliases = array();
	private $base_condition;
	private $base_condition_menu;
	private $need_authorisation = 0;

	private $page_elements = array();
	private $public_roots  = array(-1);
	private $special_roots = array();

	private $menu_loaded = 0;
	private $extra_footer;

	public $pagesize = 20;
	public $rss;
	public $http_host;
	public $siteroot;
	public $favicon;
	public $logo;
	public $display_login = 1;
	public $alternative_footer = 1;
	private $alternative_text = "";
	private $has_abbr = 0;

	private $menu_template;
	private $menu_type = "horizontal";
	public $mid;

	public $language;
	private $manage_hostname;
	private $protocol;
	private $apc_fetch;
	private $apc_disable = 0;

	public $browser;
	private $page_footer;
	private $google_items = 10000;

	private $cms_license;
	private $disableLists = 0;
	private $disableMeta  = 0;
	private $disableFileSearch = 1;
	private $sitemap_parse_level = 2;
	private $is_shop = 0;

	private $valuta = "&euro;";
	private $custom_status;
	private $redir;
	private $enable_ft_search = 0;

	private $apc_cache;
	private $no_status_header = 1;

	public function __construct() {

		/* create cache object */
		$this->apc_cache = new Tpl_cache();
		$this->setApcOptions();

		if ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")
			$this->protocol = "https://";
		else
			$this->protocol = "http://";

		/* detect supported browser versions */
		require_once("classes/covide/browser.php");
		$this->browser = browser_detection("full");

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

		/* set some default images */
		$this->favicon = "/img/cms/favicon.png";
		$this->logo    = "/img/cms/logo.gif";

		/* get default page from db */
		$q = "select * from cms_license";
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		/* save license for later use */
		$row["has_shop"] = $row["cms_shop"];
		$this->cms_license = $row;

		/* some vars */
		$this->default_page = $row["cms_defaultpage"];
		if ($row["cms_favicon"])     $this->favicon  = $row["cms_favicon"];
		if ($row["cms_logo"])        $this->logo     = $row["cms_logo"];
		if ($row["search_language"]) $this->language = $row["search_language"];
		if (trim($row["cms_manage_hostname"]))
			$this->manage_hostname = $row["cms_manage_hostname"];
		else
			$this->manage_hostname = $_SERVER["HTTP_HOST"]; //compatibility if not set

		$this->siteroot           = 0;
		$this->special_roots["R"] = $this->default_page;

		/* try to determine the default siteroot page */
		$q = sprintf("select cms_defaultpage, pageid, cms_favicon, cms_logo, search_language, cms_hostnames from cms_license_siteroots where
			cms_hostnames like '%%%s%%'", $this->http_host);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$hostnames = explode("\n", $row["cms_hostnames"]);
			foreach ($hostnames as $h) {
				if (strtolower($this->http_host) == trim(strtolower($h))) {
					$this->siteroot     = $row["pageid"];
					$this->default_page = $row["cms_defaultpage"];

					if ($row["cms_favicon"])     $this->favicon  = $row["cms_favicon"];
					if ($row["cms_logo"])        $this->logo     = $row["cms_logo"];
					if ($row["search_language"]) $this->language = $row["search_language"];
				}
			}
		}

		/* get default public root (domains) */
		$q = "select id, isSpecial from cms_data where (parentPage = 0 or parentPage is null) and (isPublic = 1 or apEnabled = ".$this->siteroot.")";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			if (!in_array($row["isSpecial"], array("X","D")))
				$this->addPublicRoot($row["id"]);
			else
				$this->special_roots[$row["isSpecial"]] = $row["id"];
		}

		/* for linkchecker, we make an expection */
		if ($_REQUEST["user"] && $_REQUEST["hash"] && $_REQUEST["page"]) {
			$q = "select pageid from cms_license_siteroots";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				$this->addPublicRoot($row["pageid"]);
			}
		}

		if ($this->siteroot == 0)
			$this->public_roots[0] = 0;

		if (!$this->manage_hostname)
			$this->manage_hostname = strtolower($_SERVER["HTTP_HOST"]);

		/* set a custom language (if any) */
		if ($this->language)
			$GLOBALS["covide"]->override_language(strtoupper($this->language));


		$this->sendHeaders();

		/* handle current request or page */
		if (!$_REQUEST["include"])
			$this->handlePage();

		/* check for internal mode status */
		$this->switchCustomModes();

		/* delete old keys (> 1 hour old) */
		$q = sprintf("delete from cms_temp where datetime <= %d", mktime()-$this->keys_cache_timeout);
		sql_query($q);
	}

	public function disableFulltextSearch() {
		$this->enable_ft_search = 0;
	}
	private function alternativeSearch($fields, $str) {
		foreach ($fields as $k=>$v) {
			$fields[$k] = sprintf(" `%s` like '%%%s%%' ", $v, $str);
		}
		return sprintf(" ( %s ) ", implode(" OR ", $fields));
	}

	private function sendHeaders() {
		/* send some headers */
		header("Expires: ".gmdate('D, d M Y H:i:s', mktime()-60*60)." GMT", true);
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', mktime())." GMT", true);

		header("Cache-Control: private", true);
		header("Pragma: private", true);
	}
	private function sendHeadersCache() {
		/* send some headers */
		header("Expires: ".gmdate('D, d M Y H:i:s', mktime()+$this->rss_cache_timeout)." GMT", true);
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', mktime())." GMT", true);

		header("Cache-Control: private", true);
		header("Pragma: private", true);
	}
	private function generate_gallery_image($item, $gallery) {
		$output = new Layout_output();
		$img = sprintf("/cmsgallery/page%d/%d&amp;size=small&amp;file=%s&amp;m=%d",
			$item["pageid"], $item["id"], $item["file_short"], $gallery["last_update"]);
		$output->addTag("img", array(
			"alt" => $this->limit_string(preg_replace("/(\n|\t|\r)/s", " ", $item["description"]), 50),
			"src" => $img,
			"border" => 0,
			"onmouseover" => "javascript: this.style.opacity = '0.8';",
			"onmouseout"  => "javascript: this.style.opacity = '1.0';"
		));
		return $output->generate_output();
	}
	public function disableLists($state) {
		$this->disableLists = (int)$state;
	}
	public function disableMetadata($state) {
		$this->disableMeta = (int)$state;
	}

	public function checkDomainRedir() {
		if ($_REQUEST["mode"] == "loginimage")
			return true;

		if ($this->siteroot > 0) {
			$q = sprintf("select cms_hostnames from cms_license_siteroots where pageid = %d",
				$this->siteroot);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0)
				$hosts = explode("\n", sql_result($res,0));
		}
		if (!is_array($hosts)) {
			$q = "select cms_hostnames from cms_license";
			$res = sql_query($q);
			if (sql_num_rows($res) > 0)
				$hosts = explode("\n", sql_result($res,0));
		}

		$hosts[0] = trim($hosts[0]);
		if (strtolower($this->http_host) != strtolower($hosts[0])) {
			$this->triggerError(301);
			header(sprintf("Location: http://%s%s", $hosts[0], $_SERVER["REQUEST_URI"]));
			exit();
		}
	}

	public function publicLoginLink($state) {
		/* this function is disabled */
		//$this->display_login = $state;
		$this->display_login = 1;
	}
	public function alternative_footer($state) {
		$this->alternative_footer = $state;
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

		if (in_array($data["category"], array("js", "css"))) {
			$tmp = sprintf("%s../tmp_cms/%s_%d.%s",
				$GLOBALS["covide"]->temppath, $GLOBALS["covide"]->license["code"], $id, $data["category"]);

			if (!file_exists($tmp) && is_writable(dirname($tmp)))
				$this->cms->saveTemplateCache($id, $data["category"], $data["data"]);
		}
		return $data;
	}

	/* start html output */
	public function start_html($header=0, $textmode=0) {
		/* set global textmode */
		$this->textmode = $textmode;

		$output = new Layout_output();
		if ($this->cms_license["cms_use_strict_mode"])
			$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n\t\"http://www.w3.org/TR/html4/loose.dtd\">\n");
		else
			$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");

		$output->addTag("html");
		$output->addCode(sprintf("\n<!-- page id [%d] -->\n", $this->pageid));
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
		echo "<!-- start of document template -->\n\n";

		$this->output_started = 1;
	}
	/* end html output */
	public function end_html() {
		$output = new Layout_output();
		$output->addCode($this->redir);
		$output->addCode("\n");
		if ($this->has_abbr && !$this->textmode) {
			$this->loadAbbreviations();
			$output->load_javascript("classes/tpl/inc/wz_tooltip.js");
		}

		/* show page number if logged in */
		if ($_SESSION["user_id"] && !$this->textmode) {
			$output->addCode("\n<!-- add page load handler -->\n");
			$output->start_javascript();
				$output->addCode(sprintf("addLoadEvent(init_status(%d));", $this->pageid));
			$output->end_javascript();
		}
		if ($this->alternative_footer) {
			$output->addCode("\n<!-- begin cms footer -->\n");
			$output->addCode($this->alternative_text);
			$output->addCode("\n<!-- end cms footer -->\n");
		}

		if ($this->siteroot > 0) {
			$q = sprintf("select letsstat_analytics, google_analytics from cms_license_siteroots
				where pageid = %d", $this->siteroot);
			$res = sql_query($q);
			$cms_siteroot = sql_fetch_assoc($res);
		} else {
			$cms_siteroot = $this->cms_license;
		}
		if ($cms_siteroot["letsstat_analytics"]) {
			$output->addCode("\n\n<!-- begin letsstat code -->\n");
			$output->addCode($this->letsstatAnalytics($cms_siteroot));
			$output->addCode("\n<!-- end letsstat code -->\n\n");
		}

		if ($cms_siteroot["google_analytics"]) {
			$output->addCode("\n\n<!-- begin google analytics code -->\n");
			$output->addCode($this->googleAnalytics($cms_siteroot));
			$output->addCode("\n<!-- end google analytics code -->\n\n");
		}

		$output->addCode("\n");
		$output->start_javascript();
			$output->addCode("page_loaded();");
		$output->end_javascript();
		$output->addCode("\n<!-- end of page -->\n");
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
			if ($data["category"] == "main") {
				header("Location: /");
				exit();
			} elseif ($data["category"] == "php") {
				$this->exec_inline($id);
				exit();
			}
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
	public function getPath($pageid=-1, $sub=0) {
		if ($pageid == -1)
			$pageid = $this->pageid;

		if (!is_numeric($pageid) && !$sub)
			$pageid = $_SESSION["cms_lastpage"];

		$path =& $this->_path;
		if ($sub==0) {
			$path[] = $pageid;
		}
		if ($pageid > 0) {
			$q = sprintf("select parentpage from cms_data where id = %d", $pageid);
			$res = sql_query($q) or die($q);
			if (sql_num_rows($res)>0) {
				$ret = sql_result($res,0,"",2);
				$path[] = $ret;
				$this->getPath($ret, 1);
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
		if (!$sub)
			return array_reverse($path);
	}
	public function displayPath($pageid=0) {
		if (!$pageid)
			$pageid = $this->pageid;

		if ($this->checkInternalRequest($pageid)==0) {
			if ($pageid == $this->pageid)
				$path = $this->path;
			else
				$path = $this->getPath($pageid);

			$pages = $this->getPagesById($path);

			foreach ($path as $id) {
				$v = $pages[$id];
				$i++;
				if ($i > 1) {
					echo " - ";
				}
				if (mb_strlen($v["pageTitle"]) > 50)
					$v["pageTitle"] = mb_substr($v["pageTitle"], 0, 50)."...";

				if ($v["id"] == $this->default_page) {
					echo sprintf("<a href='%s'>%s</a>", "/", $v["pageTitle"]);
				} elseif ($v["id"] == $this->pageid) {
					echo sprintf("%s", $v["pageTitle"]);
				} else {
					echo sprintf("<a href='/%s/%s'>%s</a>", ($this->textmode) ? "text":"page", $this->checkAlias($v["id"]), $v["pageTitle"]);
				}
			}
		}
	}
	public function getPageThumb($id, $width, $height) {
		$page = $this->getPageById($id);
		preg_match_all("/<img[^>]*?>/sxi", $page["pageData"], $imglist);

		foreach ($imglist[0] as $l) {
			$r = trim(preg_replace("/(^<img)|(>$) /sxi", "", $l));
			$img = explode("\"", $r);
			$keys = array();
			foreach ($img as $k=>$v) {
				$v = trim($v);
				if (preg_match("/\=$/s", $v)) {
					$keys[strtolower(str_replace("=", "", $v))] = trim($img[$k+1]);
				}
			}
			if (preg_match("/^cmsfile\/\d{1,}$/six", $keys["src"])) {

				$img_id = (int)preg_replace("/^cmsfile\//si", "", $keys["src"]);

				/* create if not already done */
				if (!$this->filesys)
					$this->filesys = new Filesys_data();
				$filesys_data =& $this->filesys;

				$file = $filesys_data->getFileById($img_id, 1);
				$file["ext"] = $filesys_data->get_extension($file["name"]);

				if (!in_array($file["ext"], array("gif", "png", "jpg")))
					return "";

				$f = sprintf("%s/%s/%d.%s", $GLOBALS["covide"]->filesyspath, "bestanden",
					$img_id, $file["ext"]);

				if (file_exists($f.".gz")) {
					/* gunzip this file, we need it */
					exec(sprintf("gunzip %s", escapeshellarg($f.".gz")), $xret, $xretval);
				}

				/* get current information */
				$sizes = getimagesize($f);

				if ($sizes[0] == 0)
					return "";

				$aspect_ratio = $sizes[1]/$sizes[0];

				/* new height / width */
				$new_h = $sizes[1];
				$new_w = $sizes[0];

				/* first make width fit */
				if ($new_w > $width) {
					$new_h = ($width * $aspect_ratio);
					$new_w = $width;
				}
				/* then height */
				if ($new_h > $height) {
					$new_w = ($height / $aspect_ratio);
					$new_h = $height;
				}
				$new_w = (int)$new_w;
				$new_h = (int)$new_h;

				/* set new info */
				$img = array(
					"src"    => $keys["src"],
					"height" => $new_h,
					"width"  => $new_w
				);
				$str = sprintf("<img src=\"%s\" width=\"%d\" height=\"%d\">",
					$img["src"], $img["width"], $img["height"]);


				$this->handleImages($str);
				return $str;
			}
		}
		return false;
	}

	public function getPagesById($ids, $order="datePublication desc") {
		if (!is_array($ids))
			return false;

		$data = array();
		$q = sprintf("select id, pageTitle, pageData, pageHeader from cms_data where id IN (%s)", "0".implode(",", $ids));
		if ($order)
			$q.= sprintf(" order by %s", $order);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = "/page/".$this->checkAlias($row["id"]);
			$row["pageText"] = trim(strip_tags($row["pageData"]));
			unset($row["pageData"]);

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
		$q = sprintf("select id, pageHeader, pageTitle, datePublication, pageData, isActive, isPublic from cms_data where parentpage = %d AND %s order by %s %s",
			$parent, $condition, $order, $sql_limit);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = "/page/".$this->checkAlias($row["id"]);
			$row["pageText"] = trim(strip_tags($row["pageData"]));
			unset($row["pageData"]);

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
			return sql_result($res,0,"",2);
	}

	private function linkcheckerPageList() {
		if ($_REQUEST["page"]) {
			/* get _all_ cms pages including the protected ones */
			$q = sprintf("select pageRedirect, pageData, apEnabled from cms_data where id = %d", $_REQUEST["page"]);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			$redir = $row["pageRedirect"];
			if ($redir)
				echo sprintf("<a href='%s'>page redirect</a><br>", $redir);

			$data = $row["pageData"];
			$this->handleRewrites($data);
			echo "<html><body>";
			echo sprintf("<base href=\"http://%s/\">", $this->http_host);
			echo $data;
			echo "</body></html>";
			exit();

		} else {
			$q = sprintf("select id from cms_data order by id");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				echo sprintf("<a href='/mode/linkchecker&user=%2\$d&hash=%3\$s&page=%1\$d'>page id %1\$d</a><br>",
					$row["id"], $_REQUEST["user"], $_REQUEST["hash"]);
			}
		}
	}
	private function linkcheckerAuth() {
		$user = (int)$_REQUEST["user"];
		$hash = $_REQUEST["hash"];
		$db_hash = $this->cms->linkcheckerHash($user);

		if ($hash != $db_hash) {
			$this->triggerError(403);
			echo "Wrong hash: ".$hash;
			exit();
		} else {
			return true;
		}
	}
	private function handlePage() {
		$page =& $this->pageid;

		if ($_REQUEST["mode"]) {
			switch ($_REQUEST["mode"]) {
				case "sitemap":
				case "sitemap_plain":
					$page = "__sitemap"; break;
				case "sitemap"     : $page = "__sitemap";  break;
				case "cmslogin"    : $page = "__cmslogin";  break;
				case "sponsor"     : $page = "__banner"; break;
				case "sponsors"    : $page = "__banners"; break;
				case "search"      : $page = "__search";   break;
				case "filesearch"  : $page = "__filesearch";   break;
				case "google"      : $page = "__google";   break;
				case "googlegz"    : $page = "__googlegz"; break;
				case "forum"       : $page = "__forum";    break;
				case "rss"         : $page = "__rss";      break;
				case "metadata"    : $page = "__metadata"; break;
				case "addressdata" : $page = "__addressdata"; break;
				case "abbreviations" : $page = "__abbreviations"; break;
				case "covideloginalt": $page = "__covidelogin"; break;
				case "shopadd"       : $page = "__shopadd"; break;
				case "shopcontents"  : $page = "__shopcontents"; break;
				case "linkchecker"   : $page = "__linkchecker";
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
		if ($page == "__def")
			$this->pageid = $this->default_page;

		if ($this->checkInternalRequest()==0) {
			$requested_page = $page;
			$page = preg_replace("/\.((htm)|(html))/si","",$page);

			if (!is_numeric($page)) {
				/* check current alias existance */
				$q = sprintf("select id, apEnabled from cms_data where pageAlias like '%s'", $page);
				$res = sql_query($q);
				if (sql_num_rows($res)==1) {
					$page = sql_result($res,0,"id",2);
				} else {
					$apEnabled = sql_result($res,0,"apEnabled",2);

					/* check if the alias did exists in the past */
					$q = sprintf("select pageid from cms_alias_history where alias like '%s'", $page);
					$res = sql_query($q);

					if (sql_num_rows($res) > 0) {
						/* check the current alias of the found page */
						$historypage = $this->checkAlias(sql_result($res,0,"",2));

						$this->triggerError(301);
						header(sprintf("Location: /page/%s.htm", $historypage));
						exit();
					} else {
						$page = "__err404"; // go to 404 later
					}
				}
			} else {
				/* check if a paging request was done */
				if ($_REQUEST["gallery"] || $_REQUEST["calendar"] || $_REQUEST["list"] || $_REQUEST["feedback"] || $_REQUEST["start"])
					$is_paging = 1;

				/* check page existance */
				$q = "select pageAlias, apEnabled from cms_data where id = ".(int)$page;
				$res = sql_query($q);
				if (sql_num_rows($res) != 1) {
					$page = "__err404";
				} else {
					$alias = sql_result($res,0,"pageAlias",2);
					$apEnabled = sql_result($res,0,"apEnabled",2);

					if ($alias && $page != $this->default_page && !$is_paging) {
						/* we should not access the page directly, go to the alias */
						$this->triggerError(301);

						$q = sprintf("select id from cms_data where isSpecial = 'D' and parentPage = 0");
						$res = sql_query($q);
						$hp = sql_result($res,0,"",2);

						if ($apEnabled == $hp) {
							$page = "__err404";
						} else {
							$this->triggerError(301);
							header(sprintf("Location: /page/%s.htm", $alias));
							exit();
						}
					}
				}
			}
			/* check if page was called without .htm */
			if ($page != "__err404" && $page != $this->default_page && !preg_match("/\.htm/si", $requested_page) && !$is_paging) {
				$this->triggerError(301);
				header(sprintf("Location: /page/%d.htm", $page));
				exit();
			}
		}

		/* save current path */
		$this->path = $this->getPath($page);

		/* get visitor page restrictions, flag isProtected */
		$this->getVisitorRestrictions($page, $this->path);

		/* get visitor page restrictions, flag isProtected */
		$this->getSSLmode($page, $this->path);

		/* rss feed */
		if ($this->pageid == "__addressdata" && $_REQUEST["address"]) {
			$qry = $_REQUEST["address"]."|".$_REQUEST["parent"];
			$this->rss = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/address/".$qry;
		} elseif ($this->pageid == "__metadata" && $_REQUEST["query"])
			$this->rss = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/meta/".$_REQUEST["query"];
		elseif ($this->pageid != $this->default_page && is_numeric($this->pageid))
			$this->rss = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/live/".$this->pageid;
		elseif ($this->pageid == $this->default_page)
			$this->rss = $this->protocol.$_SERVER["HTTP_HOST"]."/rss";

		/* add and check page log */
		$this->addPageLog();
		$this->checkPageLog();

		if (is_numeric($page)) {
			/* check for domain permissions */
			$q = sprintf("select apEnabled from cms_data where id = %d", $page);
			$res = sql_query($q);
			$ap = sql_result($res,0,"",2);

			if (!in_array($ap, $this->public_roots) && $this->siteroot) {
				//throw error

				$this->triggerError(301);

				//get authorized host name
				$host = $this->cms->getHostnameByPage($this->pageid);

				$uri = sprintf("%s%s/page/%s", $this->protocol, $host, $this->checkAlias($this->pageid));
				header("Location: ".$uri);
				exit();
			}

			/* save page to last visited page */
			if ($page > 0)
				$_SESSION["cms_lastpage"] = $page;


			/* check if a domain redirect to a preferred one needs to be done */
			$this->checkDomainRedir();

			/* check for other redirects */

			$this->getRedirEndPoint($page);
		}

		/* internal status codes should never end with .htm */
		if (preg_match("/^__/s", $page))
			$page = preg_replace("/\.htm$/s", "", $page);
	}

	private function checkPageLog() {
		return true;
		/* check if the current page occurs more than 5 times in the last 5 seconds */
		$limit = 5;
		$key = sprintf("s:{%s} p:redir", session_id(), $this->pageid);
		$q = sprintf("select count(ids) from cms_temp where userkey = '%s'
			and ids = %d having count(ids) > %d", $key, $this->pageid, $limit);
		$res = sql_query($q);
		if (sql_result($res,0,"",2) > 0) {
			$this->pageid = "__err602";
		}
	}

	private function addPageLog() {
		return true;
		$key = sprintf("s:{%s} p:redir", session_id(), $this->pageid);
		$timer = 10;

		/* delete items older than 10 seconds */
		$q = sprintf("delete from cms_temp where userkey = '%s' and datetime <= %d",
			$key, mktime()-$timer);
		sql_query($q);

		if (is_numeric($this->pageid)) {
			$q = sprintf("insert into cms_temp (userkey, ids, datetime) values ('%s', '%s', %d)",
				$key, $this->pageid, mktime());
			sql_query($q);
		}
	}

	private function getVisitorRestrictions($page, $path) {
		$pages = array();
		if (!$path[0])
			$path[0] = $this->default_page;

		$q = sprintf("select id, isProtected from cms_data where id IN (%s)", implode(",", $path));
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$pages[$row["id"]] = $row["isProtected"];
		}
		$pages = array_reverse($pages, 1);
		$locked = 0;
		if ($pages[$page] == 1) {
			$locked = 1;
			$key = $page;
		} elseif (in_array(2, $pages)) {
			/* get keys */
			$keys = array_search(2, $pages);
			$locked = 1;
			if (is_array($keys))
				$key = $keys[0];
			else
				$key = $keys;
		}
		/* if page is locked, prevent filesys caching (see common/headers.php) */
		if ($locked)
			no_cache_headers();

		/* check if the user has no permissions and the page is locked */
		if ($locked && is_numeric($key)) {
			/* get page permissions */
			$perms = $this->cms->getAuthorisations($page);
			$xs = 0;
			if ($_SESSION["user_id"]) {
				if ($perms[$_SESSION["user_id"]] != "D")
					$xs = 1;

				if (!$xs) {
					$user_data = new User_data();
					$groups = $user_data->getUserGroups($_SESSION["user_id"]);
					foreach ($groups as $g) {
						if ($perms["G".$g] != "D")
							$xs = 1;
					}
				}
			} elseif ($_SESSION["visitor_id"]) {
				if ($perms["U".$_SESSION["visitor_id"]] != "D")
					$xs = 1;
			}
			$this->apc_disable = 1;
			if (!$xs) {
				$this->need_authorisation = $this->pageid;
				$this->pageid = "__err401";
			}
		}
	}

	private function getSSLMode($page, $path) {
		if ($_REQUEST["mode"] == "menu" || $_REQUEST["mode"] == "abbreviations")
			return;

		$pages = array();
		if (!$path[0])
			$path[0] = $this->default_page;

		$q = sprintf("select id, useSSL from cms_data where id IN (%s)", implode(",", $path));
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$pages[$row["id"]] = $row["useSSL"];
		}
		$pages = array_reverse($pages, 1);
		if ($pages[$page] == 1 || in_array(2, $pages))
			$ssl = 1;
		else
			$ssl = 0;

		/* no cache for ecnrypted pages */
		if ($ssl)
			no_cache_headers();

		/* check if the user has no permissions and the page is locked */
		if ($ssl && (!$_SERVER["HTTPS"] == "on" || $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")) {
			session_write_close();
			$this->triggerError(307);
			$uri = sprintf("https://%s/page/%s", $this->http_host, $this->checkAlias($page));
			header( sprintf("Location: %s", $uri ));
			exit();
		} elseif (!$ssl && ($_SERVER["HTTPS"] == "on"|| $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")) {
			session_write_close();
			$this->triggerError(307);
			$uri = sprintf("http://%s/page/%s", $this->http_host, $this->checkAlias($page));
			header( sprintf("Location: %s", $uri ));
			exit();
		}
	}

	public function getRedirEndPoint($page) {
		$q = sprintf("select pageRedirect, pageRedirectPopup, popup_data from cms_data where id = %d", $page);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$row["pageRedirect"] = preg_replace("/^index\.php/s", "", $row["pageRedirect"]);
		if (preg_match("/^\?((id)|(page))=\d{1,}/s", $row["pageRedirect"])) {
			$row["pageRedirect"] = preg_replace("/^\?page=(\d{1,})/s", "?id=$1", $row["pageRedirect"]);
			$row["pageRedirect"] = preg_replace("/^\?id=(\d{1,})/s", "/page/$1", $row["pageRedirect"]);
		}

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

				if (!$this->textmode) {
					$output = new Layout_output();
					$output->start_javascript();
						$output->addCode( sprintf("var cvd_%s = setTimeout(\"window.open('%s', '%s', '%s');\", 500);",
							md5(rand()), $row["pageRedirect"], "cmswindow_".mktime(), $opts ));
					$output->end_javascript();
					$this->redir = $output->generate_output();
				}

			} else {
				session_write_close();

				/* check if request is no menu request */
				if (!$_REQUEST["mode"] == "menu") {
					/* check if the redir is internal */
					if (preg_match("/^\//s", $row["pageRedirect"]))
						$this->triggerError(301);
					else
						$this->triggerError(307);

					header( sprintf("Location: %s", $row["pageRedirect"]) );
					exit();
				}
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

	private function getCmsTmpFile($id, $type) {
		$f_int = sprintf("%s%d.%s", $this->cms->getCmsTmpPrefix(), $id, $type);
		if (file_exists($f_int)) {
			$f_ext = sprintf("/tmp_cms/%s_%d.%s?m=%d", $GLOBALS["covide"]->license["code"],
				$id, $type, filemtime($f_int));
			return $f_ext;
		} else {
			return sprintf("/include/%d", $id);
		}
	}

	public function load_css($id, $browsers="") {
		switch (strtolower($browsers)) {
			case "msie":
			case "msie 5":
			case "msie 6":
			case "msie 7":
			case "khtml":
				$browsers = strtoupper($browsers);
				break;
			case "gecko":
				$browsers = "Gecko";
				break;
			case "opera":
				$browsers = "Gecko";
				break;
		}
		if ($browsers) {
			$code = sprintf("<script type='text/javascript'>UA_css('%s', '%s');</script>\n",
				$browsers, $this->getCmsTmpFile($id, "css"));
		} else {
				$code = sprintf("<link rel='stylesheet' type='text/css' href='%s'>\n",
				$this->getCmsTmpFile($id, "css"));
		}

		if ($this->output_started) {
			echo $code;
		} else {
			$this->file_loader[] = $code;
		}
	}
	public function load_js($id) {
		if ($this->textmode)
			return false;

		$code = sprintf("<script language='Javascript1.2' type='text/javascript' src='%s'></script>\n",
			$this->getCmsTmpFile($id, "js"));

		if ($this->output_started) {
			echo $code;
		} else {
			$this->file_loader[] = $code;
		}
	}
	public function getPageTitle($id=0, $size=1, $return=0) {
		if (!$id)
			$id = $this->pageid;

		if ($this->checkInternalRequest($id)==1) {
			switch ($id) {
				case "__sitemap":
					$title = gettext("sitemap");
					break;
				case "__search":
					$title = gettext("search for pages");
					break;
				case "__filesearch":
					$title = gettext("search inside files");
					break;
				case "__metadata":
					$title = gettext("custom search");
					break;
				case "__addressdata":
					$title = gettext("address list");
					break;
				case "__shopcontents":
					$title = $this->getPageTitle($this->cms_license["cms_shop_page"], -1, 1);
					break;
			}
		} else {
			if (!$this->page_cache[$id]) {
				$this->page_cache[$id] = $this->cms->getPageById($id, "", 1);
			}
			$page  =& $this->page_cache[$id];
			$title =& $page["pageTitle"];

		}

		if ($return)
			return $title;
		elseif ($size == -1)
			echo $title;
		else
			echo sprintf("<H%d>%s</H%d>", $size, $title, $size);
	}
	public function getPageAlias($id) {
		return $this->checkAlias($id);
	}
	public function getPageById($id=-1) {
		if ($id == -1)
			$id = $this->pageid;

		if (!$this->page_cache[$id])
			$this->page_cache[$id] = $this->cms->getPageById($id);

		return $this->page_cache[$id];
	}

	public function sitemap_parse_level($state) {
		$this->sitemap_parse_level = (int)$state;
	}

	private function generateSitemap($startPage=0, $parse_level=0) {
		if (!$parse_level)
			$parse_level = $this->sitemap_parse_level;

		if (!$startPage)
			$startPage = $this->default_page;

		$data = $this->getPagesById(array($startPage));

		echo sprintf("<table cellpadding='0' cellspacing='0'>");
		$this->generateSitemapItem($data[$startPage]);
		$this->generateSitemapTree($startPage, 1, $parse_level);
		echo sprintf("</table>");
	}
	private function generateSitemapTree($page, $level, $parse_level) {
		if ($level <= $parse_level) {
			$pages = $this->getPagesByParent($page);
			foreach ($pages as $v) {
				if ($v["isActive"] && $v["isPublic"]) {
					$this->generateSitemapItem($v, $level);
					$this->generateSitemapTree($v["id"], $level+1, $parse_level);
				}
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
			if ($v["id"] == $this->default_page) {
				$url = "/text/";
			} else {
				$url = str_replace("/page/", "/text/", $v["alias"]);
			}
		} else {
			if ($v["id"] == $this->default_page) {
				$url = "/";
			} else {
				$url = $v["alias"];
			}
		}
		//echo "<div style='padding-left: ".($level*8)."px;' class='$class'><a href='$url'>".$v["pageTitle"]."</a></div>";
		echo "<tr><td>";
		for ($i=0;$i<$level;$i++)
			echo "<img src='img/cms/tree_left.gif?v=1' alt=''>";

		echo "<img src='img/cms/tree_mid.gif?v=1' alt=''>";
		if ($this->sitemap_parse_level > $level) {
			$q = sprintf("select count(*) from cms_data where parentPage = %d and %s",
				$v["id"], $this->base_condition);
			$res = sql_query($q);
			if (sql_result($res,0) > 0)
				echo "<img src='img/cms/page_struct.gif?v=1' alt=''>";
			else
				echo "<img src='img/cms/page.gif?v=1' alt=''>";
		} else
			echo "<img src='img/cms/page.gif?v=1' alt=''>";

		echo "</td><td>";
		echo sprintf("<a style='margin-left: %dpx;' href='%s'>%s</a>",
			(6 * $level), $url, $v["pageTitle"]);
		echo "</td></tr>";
	}

	private function cmsLoginVisitor($req) {
		$q = sprintf("select * from cms_users where username = '%s'
			and password = '%s'", $req["username"], $req["password"]);
		$res = sql_query($q);
		if (sql_num_rows($res) == 1) {
			$row = sql_fetch_assoc($res);
			$id = $row["id"];
		} else {
			$id = 0;
		}
		/* if user registration is not confirmed, it could be not active/enabled */
		if (!$row["is_active"])
			$row["is_enabled"] = 0;

		if ($id && $row["is_enabled"]) {
			$_SESSION["visitor_id"] = $id;
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(sprintf(" document.location.href='%s'; ", $req["uri"]));
			$output->end_javascript();
			echo $output->generate_output();

		} elseif ($id && !$row["is_enabled"]) {
			$_SESSION["visitor_id"] = $id;
			$this->triggerError(401);

			$output = new Layout_output();
			$output->addCode(gettext("This account has been disabled or is not yet activated."));
			$output->addTag("br");
			$output->addTag("br");
			$output->addCode(gettext("Click here to go back").": ");
			$output->insertAction("toggle", gettext("retry"), "javascript: pageHistory();");
			echo $output->generate_output();
		} else {
			$_SESSION["visitor_id"] = $id;
			$this->triggerError(401);

			$output = new Layout_output();
			$output->addCode(gettext("Wrong username and/or password specified."));
			$output->addTag("br");
			$output->addTag("br");
			$output->addCode(gettext("Click here to try again").": ");
			$output->insertAction("toggle", gettext("retry"), "javascript: pageHistory();");
			echo $output->generate_output();
		}
	}
	public function rewriteHtml($data) {
		$this->handleRewrites($data);
		return $data;
	}

	public function getPageData($id=0, $prefix="page", $no_inline_edit=0) {
		echo "\n\n<!-- start of page data -->\n\n";
		echo "<div>";
		if (!$id)
			$id = $this->pageid;

		if ($this->checkInternalRequest($id)==1) {
			switch ($id) {
				case "__cmslogin":
					$this->cmsLoginVisitor($_REQUEST);
					break;
				case "__metadata":
					switch ($_REQUEST["show"]) {
						case "result":
						case "rss":
						default:
							if ($_REQUEST["metainit"])
								$this->metaInitResults();
							elseif ($_REQUEST["query"])
								$this->metaShowResults($_REQUEST["query"]);
							else
								$this->metaShowOptions();
							break;
					}
					break;
				case "__addressdata":
					if ($_REQUEST["address"])
						$this->generateAddressRecords($_REQUEST["address"], $_REQUEST["parent"]);
					else
						$this->generateAddressList();
					break;
				case "__sitemap":
					$this->generateSitemap();
					break;
				case "__shopcontents":
					$this->shopContents();
					break;
				case "__err401":
					$this->triggerError(401);
					$this->triggerLogin($this->need_authorisation);
					break;
				case "__err403":
					$this->triggerError(403);
					if ($this->custom_status)
						$this->getPageData($this->custom_status);
					else
						echo gettext("Access to this page is forbidden. The page is not active or access is restricted.");
					break;
				case "__err602":
					$this->triggerError(602);
					if ($this->custom_status) {
						$this->getPageData($this->custom_status);
					} else {
						$output_alt = new Layout_output();
						$output_alt->addTag("br");
						$output_alt->addCode(gettext("The requested page is redirecting in a loop."));
						$output_alt->addCode(gettext("The current request has been cancelled."));
						$output_alt->addTag("br");
						$output_alt->addTag("br");
						$output_alt->insertAction("previous", gettext("back"), "javascript: pageHistory();");
						$output_alt->addSpace();
						$output_alt->insertTag("a", gettext("go back to the previous page"), array(
							"href" => "javascript: pageHistory();"
						));
						echo $output_alt->generate_output();
					}
					break;
				case "__err404":
					$this->triggerError(404);
					$custom = $this->custom_status;
					//fallback to search (next case)
				case "__search":
					require(self::include_dir."search.php");
					break;
				case "__filesearch":
					require(self::include_dir."filesearch.php");
					break;
				case "__blog":
					$this->plugins["mvblog"]->blog_content(0,0);
					break;
				case "__covidelogin":
					$q = "select cms_manage_hostname from cms_license";
					$res = sql_query($q);
					$manage = sql_result($res,0);

					if ($manage != $this->http_host) {
						$this->triggerError(403);
						$output_alt = new Layout_output();
						$output_alt->addTag("br");
						$output_alt->addCode(gettext("The requested action is not available for this domain."));
						$output_alt->addTag("br");
						$output_alt->addCode(gettext("Please go to the following location to login:"));

						if (!$manage) {
							$output_alt->addTag("br");
							$output_alt->addTag("br");
							$output_alt->insertTag("b", gettext("no manage domain set, please update your configuration or login with the login icon").": ");
							$output_alt->insertAction("covide", gettext("login"), "javascript: popup('/mode/covidelogin', 'cms_covide_login');");
						} else {
							$uri = sprintf("http://%s/covide", $manage);
							$output_alt->insertTag("a", $uri, array(
								"target" => "_blank",
								"href"   => $uri
							));
						}

						$output_alt->addTag("br");
						$output_alt->addTag("br");
						$output_alt->insertAction("previous", gettext("back"), "javascript: pageHistory();");
						$output_alt->addSpace();
						$output_alt->insertTag("a", gettext("go back to the previous page"), array(
							"href" => "javascript: pageHistory();"
						));
						echo $output_alt->generate_output();
					} else {
						$this->triggerError(301);
						header("Location: /?mod=desktop");
						exit();
					}
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

			$data = "";
			if ($page["pageHeader"])
				$data.= sprintf("\n<h2>%s</h2>", $page["pageHeader"]);

			/* handle lists */
			if ($page["isList"]) {
				$list = $this->cms->getListData($id);
				if (!$this->disableLists) {
					$listdata = "";
					$this->handleList($listdata, $id);
				}
			}
			/* only allow lists to selected page id (list on top) */
			if ($list["listposition"] == "boven")
				$data.= $listdata;

			/* metadata */
			if ($page["useMetaData"] && !$this->disableMeta)
				$data.= $this->handleMetaData($data, $id);

			/* shop */
			if ($this->page_cache[$id]["isShop"])
				$this->handleShop($data, $id);

			/* add page data */
			$data .= "\n".$page["pageData"];

			/* look for relation set */
			$this->getAddressNames($id, $page["address_ids"]);

			/* only allow lists to selected page id (list on top) */
			if ($list["listposition"] == "onder")
				$data.= $listdata;
			elseif ($list["listposition"] == "content")
				$data = str_replace("%%list%%", $listdata, $data);


			/* apply filters */
			$this->handleRewrites($data, $prefix);

			if ($this->page_cache[$id]["isGallery"])
				$this->handleGallery($data, $id);

			if ($this->page_cache[$id]["isFeedback"])
				$this->handleFeedback($data, $id);

			if ($this->page_cache[$id]["isForm"]) {
				//if ($this->page_cache[$id]["form_mode"] == 2)
				//	$this->handleEnquete($data, $id);
				//else
					$this->handleForm($data, $id);
			}

			$data = str_replace(" />", ">", $data);

			$data = $this->checkPageElements($data);

			$this->handleAbbr($data, $no_inline_edit);

			/* strip onmouser function */
			#$data = preg_replace("/onmouse((out)|(over))=\"setBgColor\([^\)]*?\);\"/six", "", $data);
			#$data = str_replace("ondblclick=\"return false;\"", "", $data);

			/* strip some commontable errors */
			$data = str_ireplace("<tr></tr>", "", $data);

			/* strip some text mode js */
			if ($this->textmode) {
				$data = preg_replace("/ onmouseover=\"return escape\(tt_abbr\[\d{1,}\]\);\"/s", "", $data);
				$data = preg_replace("/<em class=\"tt_tooltip\">/s", "<em>", $data);
			}

			$this->handleImages($data);
			echo $data;

			if ($this->checkCalendar($id))
				$this->getCalendar($id);
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
				$_SESSION["author_website"]  = $this->protocol.$_SERVER["HTTP_HOST"];
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
					if ($page_xs && is_numeric($this->pageid)) {
						$this->allow_edit[$id] = 1;
					}
				}
			}
		}
		echo "</div>";
		echo "\n\n<!-- end of page data -->\n\n";
	}

	private function shopContents() {
		/* set flag for later */
		$this->is_shop = 1;

		$shop =& $_SESSION["shop"];
		if (!is_array($shop))
			$shop = array();

		if ($this->cms_license["cms_shop_page"])
			$this->getPageData($this->cms_license["cms_shop_page"]);

		$output = new Layout_output();
		$output->insertAction("shop", "", "");
		$output->addSpace();
		$output->insertTag("b", gettext("current shopping cart contents").": ");
		$output->addTag("br");
		$output->addTag("br");
		$output->insertTag("span", gettext("enter new amount for article"), array(
			"style" => "display: none",
			"id"    => "shop_edit_msg"
		));
		$output->insertTag("span", gettext("remove article"), array(
			"style" => "display: none",
			"id"    => "shop_del_msg"
		));
		echo $output->generate_output();

		$data = array();
		$total = 0;
		foreach ($shop as $id=>$num) {
			$page = $this->getPageById($id);
			$page["num"] = $num;
			$page["total"] = $page["shopPrice"] * $page["num"];
			$page["thumb"] = $this->getPageThumb($page["id"], 100, 50);

			$total += $page["total"];
			$page["total"] = number_format($page["total"], 2);
			$page["shopPrice"] = number_format($page["shopPrice"], 2);
			$page["pageTitle"] = addslashes($page["pageTitle"]);

			$data[$id] = $page;
		}
		$view = new Layout_view();
		$view->addData($data);
		$view->addMapping(gettext("image"), "%thumb", "left");
		$view->addMapping(gettext("article"), "%pageTitle", "left");
		$view->addMapping(gettext("description"), "%pageHeader", "left");
		$view->addMapping(gettext("price"), array($this->valuta, "&nbsp;", "%shopPrice"), "left", "nowrap");
		$view->addMapping(gettext("count"), array("%num", "x"), "left");
		$view->addMapping(gettext("total"), array($this->valuta, "&nbsp;", "%total"), "left", "nowrap");
		$view->addMapping("", "%%complex_actions");


		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"  => "action",
				"src"   => "edit",
				"alt"   => gettext("edit"),
				"link"  => array("javascript: shopEdit('", "%id", "', '", "%num", "', '", "%pageTitle", "');")
			),
			array(
				"type"  => "action",
				"src"   => "delete",
				"alt"   => gettext("delete"),
				"link"  => array("javascript: shopDel('", "%id", "', '", "%pageTitle", "');")
			)
		));

		$view->setHtmlField("thumb");
		echo $view->generate_output();

		echo sprintf("<br><b>%s: %s %s</b><br><br>",
			gettext("Total price"), $this->valuta, number_format($total,2));

		if (count($_SESSION["shop"]) > 0) {
			if ($this->cms_license["cms_shop_results"])
				$this->getPageData($this->cms_license["cms_shop_results"]);
		} else {
			echo sprintf("<b>%s</b><br><br>", gettext("You have no articles in your shopping card. Please select one or more articles before you continue."));
		}
	}
	private function triggerLogin($page, $return=0, $use_feedback=0) {
		$output = new Layout_output();
		if (!$use_feedback) {
			if ($this->custom_status) {
				$this->getPageData($this->custom_status);
			} else {
				$output->addCode(gettext("You have not sufficient permissions to visit this page."));
				$output->addTag("br");
				$output->addCode(gettext("Please login with a username / password combination that have access to this page and try again."));
			}
		} else {
			$s = $this->cms->getCmsSettings($this->siteroot);
			if (!$s["custom_feedback"])
				$s["custom_feedback"] = $this->cms_license["feedback"];

			if ($s["custom_feedback"])
				$this->getPageData($s["custom_feedback"]);
			else
				$output->addCode(gettext("To give feedback on this page, please login with your account."));
		}
		$output->addTag("br");
		$output->addTag("br");

		/* create the uri */
		$uri = sprintf("%s%s/page/%s",
			$this->protocol, $_SERVER["HTTP_HOST"], $this->checkAlias($page));

		$txt = gettext("Are you sure you want to logout? All data that is not saved will be lost! Continue?");
		$output->insertTag("span", $txt, array(
			"style" => "display: none",
			"id"    => "logout_confirm"
		));

		if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
			$user_data = new User_data();
			if ($_SESSION["user_id"])
				$output->insertTag("b", gettext("You are already logged in as Covide user").": ".$user_data->getUserNameById($_SESSION["user_id"]));
			else
				$output->insertTag("b", gettext("You are already logged in as visitor").": ".$this->cms->getUserNameById($_SESSION["visitor_id"]));
			$output->addTag("br");
			$output->addTag("br");
			$output->addCode(gettext("Please logout and login again with a user with sufficient permissions to access this page."));
			$output->addTag("br");
			$output->addTag("br");
			$output->addCode(gettext("You can click here to logout").": ");
			if ($_SESSION["user_id"]) {
				$output->insertAction("logout", gettext("logout"),
					"javascript: cmsLoginPage('".$uri."', 1);"
				);
			} else {
				$output->insertAction("logout", gettext("logout"),
					"/?mod=user&action=logout&redir=".urlencode($uri)
				);
			}
		} else {
			$output->insertAction("state_special", "", "");
			$output->addSpace();
			$output->addCode(gettext("Login below here if you have a visitor account."));
			$output->addTag("br");
			$output->addTag("br");

			$output->addTag("form", array(
				"id" => "loginfrm",
				"action" => "site.php",
				"method" => "post"
			));
			$output->addHiddenField("uri", $uri);
			$output->addHiddenField("mode", "cmslogin");

			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("username").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addTextField("username", "", array(
						"style" => "width: 180px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("password").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addPasswordField("password", "", array(
						"style" => "width: 180px;"
					));
					$tbl->addSpace();
					$tbl->insertTag("a", gettext("login")." &gt;",
						array("href" =>  "javascript: document.getElementById('loginfrm').submit();"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 5));
					$tbl->addTag("br");
					/* cvd login */
					/*
					$tbl->insertAction("covide", gettext("login"), array(
						"href" => "javascript: cmsLoginPage('".$uri."');"));
					$tbl->addSpace();
					$tbl->insertTag("a", gettext("If you have an Covide account, click here to login."), array(
						"href" => "javascript: cmsLoginPage('".$uri."');"
					))
					$tbl->addTag("br");
					*/;

					//$clicense = $this->cms->getCmsSettings("R");
					$clicense = $this->cms_license;

					if ($clicense["cms_feedback"]) {
						/* new account */
						$tbl->insertAction("state_public", gettext("login"), array(
							"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("If you don't have an account, you can sign up here."), array(
							"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
						$tbl->addTag("br");
					}

					/* password recover */
					$tbl->insertAction("help", gettext("login"), array(
						"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
					$tbl->addSpace();
					$tbl->insertTag("a", gettext("If you forgot your password, you can recover it here."), array(
						"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
				$tbl->endTableData();
			$tbl->endTableRow();


			$tbl->endTable();

			$output->addCode($tbl->generate_output());
			$output->endTag("form");
		}
		if ($return)
			return $output->generate_output();
		else
			echo $output->generate_output();
	}

	private function handleAbbr(&$data, $no_inline_edit=0) {
		if (!$no_inline_edit) {
			/* match all tags */
			preg_match_all("/<[\w]+[^>]*>/sxi", $data, $matches);

			foreach ($matches[0] as $k=>$v) {
				$data = str_replace($v, "##$k##", $data);
			}
			$cms = $this->cms->getAbbreviations();
			$find = array();
			$repl = array();
			foreach ($cms as $k=>$c) {
				if (in_array($this->language, $c["lang"])) {
					$this->has_abbr = 1;
					$find[] = sprintf("/(%s)/six", $c["abbreviation"]);
					$repl[] = sprintf("<em class=\"tt_tooltip\" onmouseover=\"return escape(tt_abbr[%d]);\">$1</em>", $k);
				}
			}
			$data = preg_replace($find, $repl, $data, 1);

			foreach ($matches[0] as $k=>$v) {
				$data = str_replace("##$k##", $v, $data);
			}
		}
	}
	private function loadAbbreviations() {
		$q = sprintf("select search_language from cms_license_siteroots where pageid = %d",
			$this->siteroot);
		$res = sql_query($q);
		$lang = sql_result($res,0);

		if (!$lang) {
			$q = "select search_language from cms_license";
			$res = sql_query($q);
			$lang = sql_result($res,0);
		}
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("var tt_abbr = new Array();\n");

		/* load abbrieviations */
		$cms = $this->cms->getAbbreviations();
		foreach ($cms as $k=>$v) {
			if (in_array($lang, $v["lang"])) {
				$output->addCode(sprintf(" tt_abbr[%d] = '<b>%s<\\/b>: %s';\n", $k, addslashes($v["abbreviation"]), addslashes($v["description"])));
			}
		}
		$output->end_javascript();
		echo $output->generate_output();
	}

	private function metaShowResults($query) {
		require(self::include_dir."showMetaResults.php");
	}

	private function metaShowOptions() {
		require(self::include_dir."showMetaOptions.php");
	}
	private function addListShopLink($id) {
		$output = new Layout_output();
		$output->insertAction("shop", gettext("go to shopping cart"),
			"/mode/shopcontents");
		$output->insertAction("file_attach", gettext("add to shopping cart"),
			sprintf("javascript: shopAdd('%d', '%s');", $id,
				addslashes(gettext("enter number of articles"))));
		return $output->generate_output();
	}
	private function getVeldValue($fieldname, $pageid) {
		if (preg_match("/^meta\d{1,}/si", $fieldname)) {
			$name = (int)preg_replace("/^meta/si", "", $fieldname);
			$q = sprintf("select field_type from cms_metadef where id = %d", $name);
			$res = sql_query($q);
			if (sql_result($res,0,"",2) == "shop" && $this->cms_license["has_shop"]) {
				$q = sprintf("select shopPrice from cms_data where id = %d", $pageid);
				$res = sql_query($q);
				return sprintf("%s&nbsp;%s", $this->valuta, str_replace(".", ",", sql_result($res,0,"",2)));
			} else {
				$q = sprintf("select value from cms_metadata where fieldid = %d and pageid = %d",
					$name, $pageid);
				$res = sql_query($q);
				return sql_result($res,0);
			}
		} elseif (in_array($fieldname, array("datefull", "dateweekday", "datemonth"))) {
			$dates = array();
			$q = sprintf("select datetime, date_begin, date_end from cms_date_index
				left join cms_date on cms_date.id = cms_date_index.dateid
				where cms_date_index.pageid = %d
				order by datetime
				", $pageid);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$dates[] = $row;
			}
			switch ($fieldname) {
				case "datefull":
					foreach ($dates as $k=>$v) {
						$row = $v;
						$v = $row["datetime"];

						if (date("H:i", $row["date_begin"]) != date("H:i", $row["date_end"]))
							$end = sprintf(" - %s", date("H:i", $row["date_end"]));
						else
							$end = "";

						$dates[$k] = sprintf("%s %s%s %s",
							strftime("%d-%m-%y", $v), date("H:i", $row["date_begin"]), $end,
							strftime("(%a)", $v)
						);
					}
					if (count($dates) > 10) {
						return "<div style='height: 140px; width: 180px; overflow-y: auto;'>".implode("\n", $dates)."</div>";
					} else {
						return implode("\n", $dates);
					}
					break;
				case "dateweekday":
					$days = array();
					foreach ($dates as $k=>$v) {
						$row = $v;
						$v = $row["datetime"];
						$days[date("w", $v)] = strftime("%A", $v);
					}
					ksort($days);
					return implode("\n", $days);
					break;
				case "datemonth":
					$months = array();
					foreach ($dates as $k=>$v) {
						$row = $v;
						$v = $row["datetime"];
						$months[date("m", $v)] = strftime("%B", $v);
					}
					ksort($months);
					return implode("\n", $months);
					break;
				default:
					die($fieldname);
			}

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
					$name = $fieldname;
					//die("invalid field name specified!");
			}
			if ($fieldname == "thumb")
				return trim($this->getPageThumb($pageid, 100, 50));

			if ($name) {
				$q = sprintf("select %s from cms_data where id = %d", $name, $pageid);
				$res = sql_query($q);
				if ($name == "datePublication")
					return date("d-m-Y", sql_result($res,0,"",2));
				else
					return $this->limit_string(sql_result($res,0), 150, 0);
			}
		}
	}

	private function handleRewrites(&$str, $prefix="") {
		preg_match_all("/<a[^>]*?>/sxi", $str, $link);
		foreach ($link[0] as $orig) {
			$l = $orig;
			//replace server name
			$regex = "/ href=(\"|')http(s){0,1}:\/\/".$_SERVER["HTTP_HOST"]."/";
			$l = preg_replace($regex, "href=$1", $l);
			$l = preg_replace("/ href=(\"|')((index)|(site))\.php/sxi", " href=$1", $l);

			$r = trim(preg_replace("/(^<a)|(>$) /sxi", "", $l));
			$keys = array();
			$href = explode("\"", $r);

			foreach ($href as $k=>$v) {
				$v = trim($v);
				if (preg_match("/\=$/s", $v)) {
					$keys[strtolower(str_replace("=", "", $v))] = trim($href[$k+1]);
				}
			}

			$keys["href"] = preg_replace("/^\?((page)|(id))\=/s", "page/", $keys["href"]);

			if (preg_match("/^page\//six", $keys["href"])) {
				$pageid = preg_replace("/(^page\/)|(\.htm)$/s", "", $keys["href"]);
				if (is_numeric($pageid))
					$pageid = $this->checkAlias($pageid);
				else
					$pageid.= ".htm";

				$keys["href"] = sprintf("/page/%s", $pageid);
			}
			if (preg_match("/^cmsfile\//six", $keys["href"])) {
				/* image found */
				$keys["href"] = preg_replace("/cmsfile\/(\d{1,})/s", "savefile/$1", $keys["href"]);
				$file_id = (int)preg_replace("/^savefile\//s", "", $keys["href"]);

				if (!$this->filesys)
					$this->filesys = new Filesys_data();

				if ($file_id > 0) {
					$file = $this->filesys->getFileById($file_id);
					if (!$this->filesys_folder_cache[$file["folder_id"]]) {
						$tmp = explode(" -> ", $this->filesys->getFolderPath($file["folder_id"]));
						unset($tmp[0]);
						$this->filesys_folder_cache[$file["folder_id"]] = $tmp;
					}
					$keys["href"] = sprintf("/savefile/%d/%s/%s", $file_id,
						implode("/", $this->filesys_folder_cache[$file["folder_id"]]),
						$file["name"]);
				}
			}
			/* make url's relative */

			$keys["href"] = preg_replace("/^\//", "", $keys["href"]);
			/* check for textmode */
			if ($this->textmode) {
				$keys["href"] = preg_replace("/^page\//s", "text/", $keys["href"]);
			}

			/* re-create a tag */
			$r = "\n<a";
			foreach ($keys as $k=>$v) {
				$r.= sprintf(" %s=\"%s\"", $k, $v);
			}
			$r.= ">";
			$str = str_replace($orig, $r, $str);
		}
	}
	private function handleImages(&$str) {
		preg_match_all("/<img[^>]*?>/sxi", $str, $imglist);

		foreach ($imglist[0] as $l) {
			//$str = str_replace($l, "IMG", $str);

			$r = trim(preg_replace("/(^<img)|(>$) /sxi", "", $l));
			$img = explode("\"", $r);

			$keys = array();

			foreach ($img as $k=>$v) {
				$v = trim($v);
				if (preg_match("/\=$/s", $v)) {
					$keys[strtolower(str_replace("=", "", $v))] = trim($img[$k+1]);
				}
			}
			/* if no alt tag is set */
			if (!$keys["alt"])
				$keys["alt"] = "";

			/* copy alt value to title field */
			$keys["title"] = $keys["alt"];

			/* check if the uri is local */
			if (preg_match("/^\/{0,1}cmsfile\/\d{1,}$/si", $keys["src"])) {
				/* extract style key */
				if ($keys["style"]) {
					$style = explode(";", $keys["style"]);
					foreach ($style as $style_attr) {
						$style_attr = explode(":", $style_attr);
						/* parse style width/height */
						if (strtolower(trim($style_attr[0])) == "width" && !$keys["width"])
							$keys["width"] = trim($style_attr[1]);

						if (strtolower(trim($style_attr[0])) == "height" && !$keys["height"])
							$keys["height"] = trim($style_attr[1]);
					}
					/* parse width/height to int */
					$keys["width"]  = (int)preg_replace("/[^0-9]/s", "", $keys["width"]);
					$keys["height"] = (int)preg_replace("/[^0-9]/s", "", $keys["height"]);
				}

				/* check if width and height are set */
				if ($keys["width"] > 0 && $keys["height"] > 0) {
					/* check height width */
					/* resize the image to the H+W values */
					$img_id = (int)preg_replace("/[^0-9]/s", "", $keys["src"]);
					$cmscache = $this->cms->createInlineThumb($img_id, $keys["width"], $keys["height"], $this->pageid);
					if ($cmscache) {
						$keys["src"] = $cmscache;
					}
				}
			}
			$keys["src"] = preg_replace("/^\//s", "", $keys["src"]);
			if (preg_match("/^((cmscache)|(cmsfile)|(savefile))\/\d{1,}$/s", $keys["src"])) {
				if (!$this->filesys)
					$this->filesys = new Filesys_data();

				$src = explode("/", $keys["src"]);
				switch ($src[0]) {
					case "cmsgallery":
						break;
					case "cmscache":
						if ($img_id) {
							$src[2] = $img_id;
						} else {
							$q = sprintf("select img_id from cms_image_cache where id = %d", $src[1]);
							$res = sql_query($q);
							$src[2] = sql_result($res,0,"",2);
						}
						/* no break */
					case "cmsfile":
					case "savefile":
						$src[2] = $img_id;

						$file = $this->filesys->getFileById($src[2]);
						if (!$this->filesys_folder_cache[$file["folder_id"]]) {
							$tmp = explode(" -> ", $this->filesys->getFolderPath($file["folder_id"]));
							unset($tmp[0]);
							$this->filesys_folder_cache[$file["folder_id"]] = $tmp;
						}
						$keys["src"] = sprintf("/%s/%d/%s/%s", $src[0], $src[1],
							implode("/", $this->filesys_folder_cache[$file["folder_id"]]),
							$file["name"]);
				}
			}
			/* re-create img tag */
			$r = "\n<img";
			foreach ($keys as $k=>$v) {
				$r.= sprintf(" %s=\"%s\"", $k, $v);
			}
			$r.= ">";
			$str = str_replace($l, $r, $str);
		}
	}

	public function preload_menu() {
		$output = new Layout_output();
		$output->load_javascript("menudata/libjs/layersmenu-browser_detection.js", 0);
		$output->load_javascript("menudata/libjs/layersmenu-library.js", 0);
		$output->load_javascript("menudata/libjs/layersmenu.js", 0);
		$this->file_loader[] = $output->generate_output();
		$this->menu_loaded = 1;
	}
	public function generate_menu($id) {
		$output = new Layout_output();

		if (!$this->menu_loaded)
			$this->preload_menu();

		$output->start_javascript();
		$output->addCode(sprintf("menu_loader(%d, %d, '%s');", $id, $this->menu_template, $this->menu_type));
		$output->end_javascript();

		echo $output->generate_output();
		//$this->generate_menu_loader($id);
	}
	public function generate_menu_loader($id) {
		global $db, $template;

		$fetch = $this->getApcCache("menu_".$id);
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

			$sql = sprintf("select apEnabled from cms_data where id = %d", $id);
			$res = sql_query($sql);
			$apEnabled = sql_result($res,0,"",2);
			$q.= sprintf(" and apEnabled = %d ", $apEnabled);

			$mid->scanTableForMenu("menu", "", $q, &$db, $id);
			if ($_REQUEST["tpl"]) {
				$template->mid =& $mid;
				$this->exec_inline((int)$_REQUEST["tpl"]);
			}

			switch ($_REQUEST["type"]) {
				case "horizontal":
					$mid->newHorizontalMenu("menu");
					break;
				case "vertical":
					$mid->newVerticalMenu("menu");
					break;
				default:
					echo "Unknown menu type: ".$_REQUEST["type"];
					exit();
			}

			$output->addCode( $mid->getHeader() );
			$output->addCode( $mid->getMenu('menu') );
			$output->addCode( $mid->getFooter() );
			$buffer = $output->generate_output();

			$this->setApcCache("menu_".$id, $buffer);
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
	public function getRandomPages($parent, $num=1) {
		$rand = sql_syntax("random");
		$q = sprintf("select * from cms_data where parentPage = %d AND %s ORDER BY %s LIMIT %d", $parent, $this->base_condition, $rand, $num);
		$res = sql_query($q);
		$data = array();
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row;
		}
		return $data;
	}

	public function html2txt($data, $basic_html=0) {
		$data = preg_replace("/<((li)|(p))[^>]*?>/six", "<br>", $data);
		$data = preg_replace("/<br[^>]*?>/sxi", "<br>", $data);
		if ($basic_html)
			$data = strip_tags($data, "<br><b><u><i>");
		else
			$data = strip_tags($data, "<br>");

		$data = preg_replace("/(\n)|(\r)|(\t)/s", "", $data);

		return $data;
	}
	public function triggerError($code, $redir="") {
		$this->http_error = $code;
		$this->apc_disable = 1;

		$s = $this->cms->getCmsSettings($this->siteroot);
		$ary = array(401,403,404,602);
		foreach ($ary as $a) {
			if (!$s["custom_".$a])
				$s["custom_".$a] = $this->cms_license["custom_".$a];
		}

		switch ($code) {
			case 404:
				if (!$this->no_status_header)
					header("Status: 404 Not Found", true, 404);

				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);

				if ($s["custom_404"])
					$this->getPageTitle($s["custom_404"]);
				else
					echo sprintf("<h1>404 Not Found</h1>");
				break;
			case 403:
				if (!$this->no_status_header)
					header("Status: 403 Forbidden", true, 403);

				header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
				if ($s["custom_403"])
					$this->getPageTitle($s["custom_403"]);
				else
					echo sprintf("<h1>403 Forbidden</h1>");
				break;
			case 401:
				if (!$this->no_status_header)
					header("Status: 401 Unauthorized", true, 401);

				header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized", true, 401);
				if ($s["custom_401"])
					$this->getPageTitle($s["custom_401"]);
				else
					echo sprintf("<h1>401 Unauthorized</h1>");
				break;
			case 307:
				if (!$this->no_status_header)
					header("Status: 307 Temporary Redirect", true, 307);
				header($_SERVER["SERVER_PROTOCOL"]." 307 Temporary Redirect", true, 307);
				break;
			case 301:
				if (!$this->no_status_header)
					header("Status: 301 Moved Permanently", true, 301);

				header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently", true, 301);
				break;
			case 602:
				if (!$this->no_status_header)
					header("Status: 602 Unknown Error", true, 602);

				header($_SERVER["SERVER_PROTOCOL"]." 602 Unknown Error", true, 602);
				if ($s["custom_602"])
					$this->getPageTitle($s["custom_602"]);
				else
					echo sprintf("<h1>602 Unknown Error</h1>");
				break;
		}
		$this->custom_status = $s["custom_".$code];
	}

	public function getPageList($id=0) {
		if ($this->disableLists == 0)
			return false;

		if (!$id) $id = $this->pageid;

		$q = sprintf("select isList from cms_data where id = %d", $id);
		$res = sql_query($q);
		if (sql_result($res,0,"",2) == 1) {
			$data = "";
			$this->handleList($data, $id);
			echo $data;
		}
	}
	private function handleList(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleList.php");
	}

	private function handleForm(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleForm.php");
	}

	private function handleFeedback(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleFeedback.php");
	}

	private function handleEnquete(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleEnquete.php");
	}

	private function handleGallery(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleGallery.php");
	}
	private function handleMetaData(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handleMetaData.php");
	}
	private function handleShop(&$data, $id=0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;

		$row = $this->page_cache[$id];

		$output = new Layout_output();
		$output->addTag("form", array(
			"name"   => "shopfrm",
			"id"     => "shopfrm",
			"method" => "get",
			"action" => "site.php"
		));

		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1,
			"class"       => "view_header table_data"
		));
		$tbl->addTableRow();
			$tbl->addTableHeader(array(
				"colspan" => 2,
				"class" => "list_header_center",
				"style" => "text-align: left"
			), "header");
				$tbl->insertAction("shop", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("Add to shopping cart"));
			$tbl->endTableHeader();
		$tbl->endTableRow();
		$tbl->addTableRow(array("class" => "list_record"));
			$tbl->addTableData();
				$tbl->addHiddenField("shopid", $this->pageid);
				$tbl->addTextField("shopcount", 1, array("style" => "width: 30px;"));
				$tbl->addCode(sprintf(" x %s %s", $this->valuta, $row["shopPrice"]));
				$tbl->insertAction("forward", gettext("add to shopping cart"), sprintf("javascript: shopAdd('%d');", $this->pageid));
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->insertTag("a", gettext("go to my shopping cart"), array(
					"href" => "/mode/shopcontents"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$output->addCode($tbl->generate_output());
		$output->endTag("form");
		$data.= $output->generate_output();
	}

	public function getPageFooter($pageid=0) {
		$output  = new Layout_output();
		$output2 = new Layout_output();

		if (!$pageid)
			$pageid = $this->pageid;

		echo "\n\n<!-- start of page footer -->";
		echo "\n\n<div id='print_container'></div>\n";

		$output2->addSpace();
		$output2->addTag("span", array(
			"id"    => "cms_navigation",
			"class" => "cms_navigation"
		));

		$output2->insertAction("previous", gettext("back"), "javascript: pageHistory();");
		$output2->addSpace();
		$output2->addCode("\n");
		$output2->insertAction("access", gettext("text version"), "/text/".$_REQUEST["page"]);
		$output2->addSpace();
		$output2->addCode("\n");
		if ($_REQUEST["page"]) {
			$output2->insertAction("print", gettext("print"), "javascript: pagePrint('".$_REQUEST["page"]."');");
			$output2->addSpace();
			$output2->addCode("\n");
		}
		if ($this->rss) {
			$output2->insertAction("rss", gettext("rss feed"), $this->rss);
			$output2->addSpace();
			$output2->addCode("\n");
		}
		$output2->insertAction("search", gettext("search"), "/search/");
		$output2->addSpace();
		$output2->addCode("\n");

		if ($this->cms_license["cms_shop"]) {
			$output2->insertAction("shop", gettext("shopping cart"), "/mode/shopcontents");
			$output2->addSpace();
			$output2->addCode("\n");
		}

		$output2->insertAction("mail_templates", gettext("sitemap"), "/sitemap.htm");
		$output2->addSpace();
		$output2->addCode("\n");

		if ($this->allow_edit[$pageid] || $page_xs == 1) {
			$output2->insertAction("edit", gettext("edit this page"), sprintf("javascript: cmsEdit(%d);", $pageid));
			$output2->addSpace();
			$output2->addCode("\n");
		}
		if ($this->display_login || $_SESSION["user_id"]) {
			if ($_SESSION["user_id"]) {
				$uri = sprintf("javascript: popup('http://%s/?mod=desktop&amp;cmslogin=1', 'cms_covide_login');", $this->manage_hostname);
				$output2->addTag("a", array("href" => $uri, "style" => "text-decoration: none;"));
				if ($_SESSION["user_id"] && $this->manage_hostname == $this->http_host) {
					$output2->addCode("<img src='themes/default/icons/jabber_online.png' alt='login' id='remote_login_image' width='16' height='16' border='0'>");
					$output2->endTag("a");
				} else {
					$output2->addCode("<img src='themes/default/icons/jabber_offline.png' alt='login' id='remote_login_image' width='16' height='16' border='0'>");
					$output2->endTag("a");
					$output2->start_javascript();
						$output2->addCode(sprintf("addLoadEvent(checkRemoteLogin('%s'))", $this->manage_hostname));
					$output2->end_javascript();
				}
			} else {
				$uri = sprintf("javascript: cmsLoginPage('%s')", $_REQUEST["page"]);
				$output2->insertAction("covide", gettext("login to covide"), $uri);
			}
			$output2->addSpace();
			$output2->addCode("\n");
		}

		/* create the uri */
		$uri = sprintf("%s%s/page/%s",
			$this->protocol, $_SERVER["HTTP_HOST"], $this->checkAlias($this->pageid));

		if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
			$output2->insertAction("logout", gettext("logout"), sprintf(
				"javascript: cmsLogout('%s', '%s', '%s');",
				urlencode($uri),
				addslashes(gettext("Do you also want to logout from the Covide office / CMS backend?")),
				($this->manage_hostname == $this->http_host) ? "":$this->manage_hostname
			));
			$output2->addSpace();
			$output2->addCode("\n");
		}
		$pt = $this->getPageTitle($pageid, -1, 1);
		$pt = $this->limit_string($pt, 70, 0);
		$pt = preg_replace("/\.{3}$/s", "", $pt);

		if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
			if ($_SESSION["user_id"]) {
				$user_data = new User_data();
				$output->addCode(gettext("Logged in as user").": ");
				$output->insertTag("b", $user_data->getUserNameById($_SESSION["user_id"]));
			} else {
				$output->addCode(gettext("Logged in as visitor").": ");
				$output->insertTag("b", $this->cms->getUserNameById($_SESSION["visitor_id"]));
			}
			$output->addTag("br");
			$output->addTag("br");
			$output2->addCode("\n");
		}

		if ($this->pageid != $this->default_page) {
			$output->addTag("span", array(
				"id"    => "cms_textfooter",
				"class" => "cms_textfooter"
			));
			$output->insertTag("a", "&lt; ".gettext("terug"), array(
				"href" => "javascript: pageHistory();"
			));
			$output->addCode(" ");
			if (is_numeric($this->pageid)) {
				$output->addCode("| ");
				$output->insertTag("a", gettext("print"), array(
					"href" => "javascript: pagePrint('".$_REQUEST["page"]."');"
				));
				$output->addCode(" | ");
			} else {
				$output->addCode("| ");
				$output->insertTag("b", $pt);
			}
			$output->endTag("span");

			$output->addSpace(2);
			$output2->addCode("\n");
		}
		$output2->endTag("span");

		echo "<br><br>";
		echo $output->generate_output();

		if ($this->alternative_footer) {
			$this->alternative_text.= "\n<!-- start footer icons -->\n";
			$this->alternative_text.= "<div id='alternative_footer'>\n";
			$this->alternative_text.= $output2->generate_output();
			$this->alternative_text.= "</div>";
			$this->alternative_text.= "\n<!-- end footer icons -->\n";
		} else {
			echo $output2->generate_output();
		}
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

		$this->displayPath();
		echo sprintf(" (<a href='%s'>%s</a>)", $alias, gettext("full version"));

		$this->getPageTitle($pageid);
		$this->getPageData($pageid, "text");

		if ($_REQUEST["print"]) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(" setTimeout('window.print();', 200); ");
				if ($_REQUEST["close"])
					$output->addCode(" setTimeout('window.close();', 1000); ");

			$output->end_javascript();
			echo $output->generate_output();
		}
		echo "<span class='noprint'>\n<br><br>\n";
		echo "<a href='javascript: history.go(-1);'>".gettext("back")."</a>";
		echo " | <a href='javascript: window.print();'>".gettext("print")."</a>";
		echo " | <a href='".$alias."'>".gettext("full version")."</a>";
		if ($pageid != "__sitemap")
			echo " | <a href='/sitemap_plain.htm'>".gettext("sitemap")."</a>";
		echo "</span>\n";

		$this->end_html();
	}

	public function redir_default_page() {
		if ($this->pageid == $this->default_page && ($_REQUEST["page"] || $_REQUEST["id"])) {
			$this->triggerError(301);
			header("Location: /");
			exit();
		}
	}
	public function generate_robots_file() {
		if (preg_match("/LinkChecker\//s", $_SERVER["HTTP_USER_AGENT"])) {
			/* we allow our own linkchecker here */
			exit();
		}

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
			echo sprintf("Sitemap: http://%s/sitemap.xml\n", $this->http_host);
			$this->setApcCache("robots", ob_get_contents());
		}
	}
	public function switchCustomModes() {
		//$cms_license = $this->cms->getCmsSettings();
		$cms_license = $this->cms_license;

		switch ($_REQUEST["mode"]) {
			case "shopadd":
				if (!$_REQUEST["id"]) {
					echo sprintf("alert('%s');", addslashes(gettext("No article specified")));
					exit();
				} elseif ((int)$_REQUEST["num"] < 0) {
					echo sprintf("alert('%s');", addslashes(gettext("No valid count specified")));
					exit();
				} else {
					$q = sprintf("select shopPrice from cms_data where id = %d", $_REQUEST["id"]);
					$res = sql_query($q);
					$price = sql_result($res,0,"",2);

					if ($price <= 0) {
						echo sprintf("alert('%s');", addslashes(gettext("Article has no valid price")));
						exit();
					} else {
						if ($_REQUEST["reset"]) {
							if ($_REQUEST["num"] == 0) {
								unset($_SESSION["shop"][(int)$_REQUEST["id"]]);
							} else {
								$_SESSION["shop"][(int)$_REQUEST["id"]] = 0;
							}
						}
						if ($_REQUEST["num"] > 0)
							$_SESSION["shop"][(int)$_REQUEST["id"]] += (int)$_REQUEST["num"];

						if ($_REQUEST["reset"])
							echo "location.href='/mode/shopcontents';";
						else
							echo sprintf("alert('%s');",
								addslashes(gettext("The article has been added to your shopping cart.")));

						exit();
					}
				}
				exit();
				break;
			case "shopcontents":
				if (!$this->cms_license["has_shop"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				break;
			case "form":
				if (!$cms_license["cms_forms"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				$this->sendform($_REQUEST, $_FILES);
				exit();
				break;
			case "formresult":
				if (!$cms_license["cms_forms"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				$this->formresult($_REQUEST["result"]);
				exit();
			case "sponsors":
				$this->getBannersXML($_REQUEST["count"], $_REQUEST["h"]);
				exit();
				break;
			case "sponsor":
				$banner = (int)$_REQUEST["id"];
				if (!$banner) {
					$this->pageid = "__err404";
				} else {
					$q = sprintf("select url from cms_gallery_photos where id = %d", $banner);
					$res = sql_query($q);
					if (sql_num_rows($res) > 0) {
						$location = sql_result($res,0);

						$currdate = mktime(0,0,0,date("m"),date("d"),date("Y"));
						$q = sprintf("select count(*) from cms_banner_views where banner_id = %d
							and datetime = %d", $banner, $currdate);
						$res2 = sql_query($q);
						if (sql_result($res2,0,"",2) == 0) {
							$q = sprintf("insert into cms_banner_views (banner_id, datetime, visited, clicked)
								values (%d, %d, 1, 1)", $banner, $currdate);
							sql_query($q);
						} else {
							$q = sprintf("update cms_banner_views set clicked = clicked + 1 where
								banner_id = %d and datetime = %d", $banner, $currdate);
							sql_query($q);
						}
						if (!$location) {
							$this->pageid = "__err404";
						} else {
							$this->triggerError(301);
							header(sprintf("Location: %s", $location));
							exit();
						}
					} else {
						$this->triggerError(404);
					}
				}
				break;
			case "feedback":
				if (!$cms_license["cms_feedback"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				$this->saveFeedback($_REQUEST);
				exit();
			case "menu":
				$this->generate_menu_loader((int)$_REQUEST["pid"]);
				exit();
			case "text":
				$this->textPage();
				exit();
			case "calendar":
				$this->loadCalendar($_REQUEST["page"], $_REQUEST["start"]);
				exit();
			case "rss":
				$this->sendHeadersCache();
				$this->rssPage();
				exit();
			case "google":
				if (!$cms_license["cms_searchengine"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				$this->sendHeadersCache();
				$this->googleMaps();
				break;
			case "googlegz":
				if (!$cms_license["cms_searchengine"]) {
					$this->triggerError(403);
					exit("Module is disabled");
				}
				$this->sendHeadersCache();
				$this->googleItems();
				break;
			case "sitemap_plain":
				$this->textPage();
				exit();
			case "robots":
				$this->sendHeadersCache();
				$this->generate_robots_file();
				exit();
			case "abbreviations":
				$this->loadAbbreviations();
				exit();
			case "favicon":
				$this->triggerError(301);
				header(sprintf("Location: %s", $this->favicon));
				exit();
			case "covideloginalt":
				break;
			case "covidelogin":
				//$this->triggerError(403);
				//cms login
				$uri = sprintf("http://".$this->http_host);
				if ($_REQUEST["uri"]) {
					$uri .= "/page/".$_REQUEST["uri"];
				}
				header(sprintf("Location: /?mod=desktop&uri=%s", urlencode($uri)));
				exit();
				break;
			case "covidelogout":
				$this->start_html(1);
				$output = new Layout_output();
				$output->start_javascript();
				$output->addCode("window.close();");
				$output->end_javascript();
				echo $output->generate_output();
				$this->end_html();
				exit();
			case "loginimage":
				/*
				$name = "covide.png";
				header("Content-Type: image/png");
				if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
					header("Content-Disposition: filename=\"".$name."\"");
				} else {
					header("Content-Disposition: attachment; filename=\"".$name."\"");
				}
				*/
				if ($_SESSION["user_id"]) {
					header("Location: /themes/default/icons/jabber_online.png");
				} else {
					header("Location: /themes/default/icons/jabber_offline.png");
				}
				exit();
		}
	}
	public function googleMaps() {
		$this->apc_disable = 1; #debug
		header("Content-Type: application/xhtml+xml");

		$apckey = sprintf("googlemap_%d", $this->siteroot);
		$fetch = $this->getApcCache($apckey);
		if ($fetch) {
			echo $fetch;
		} else {

			//$cms_license = $this->cms->getCmsSettings();
			$cms_license = $this->cms_license;

			if (!$cms_license["cms_searchengine"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}

			/* get basic info */
			$xml_main = file_get_contents(self::include_dir."google_map.xml");
			$xml_item_base = file_get_contents(self::include_dir."google_map_item.xml");

			$q = sprintf("select max(date_changed) as lastchange, count(*) as total from cms_data where %s and apEnabled IN (%s)",
				$this->base_condition, implode(",", $this->public_roots));
			$res = sql_query($q);
			$last_modification = sql_result($res,0,"lastchange",2);
			$total_records     = sql_result($res,0,"total",2);
			$total_maps        = ceil($total_records/$this->google_items);

			for ($i=1;$i<=$total_maps;$i++) {
				$xml_item = str_replace("{location}", $GLOBALS["covide"]->webroot."sitemap.".$i.".xml.gz", $xml_item_base);
				$xml_item = str_replace("{tlocation}", $GLOBALS["covide"]->webroot."sitemap.".$i.".xml.text", $xml_item);
				$xml_item = str_replace("{date}", date("c", $last_modification), $xml_item);
				$xml_main = str_replace("{records}", $xml_item."{records}", $xml_main);
			}
			$xml_main = str_replace("{records}", "", $xml_main);

			$this->setApcCache($apckey, $xml_main);
			echo $xml_main;
		}
		exit();
	}
	public function googleItems() {
		$this->apc_disable = 1; #debug

		ob_end_flush();
		ob_start();

		/* gz encode the result */
		if ($_REQUEST["nocompress"]) {
			header("Content-Type: application/xhtml+xml");
		} else {
			header("Content-type: application/x-gzip");
		}

		$apckey = sprintf("googleitems_%d_%d_%d", $this->siteroot, $_REQUEST["part"], $_REQUEST["nocompress"]);
		$fetch = $this->getApcCache($apckey);
		if ($fetch) {
			echo $fetch;
		} else {

			/* get basic info */
			$xml_main = file_get_contents(self::include_dir."google_main.xml");
			$xml_item = file_get_contents(self::include_dir."google_item.xml");

			/* note: part one includes the default page */
			$num = $this->google_items;

			/* set start position */
			$start = (int)((($_REQUEST["part"]-2)*$this->google_items));

			/* get default page */
			if ($_REQUEST["part"] == 1) {
				$data = $this->cms->getPageById($this->default_page);
				$data["google_changefreq"] = "daily"; //set home page to daily
				$this->googleRecord($xml_main, $xml_item, $data);

				/* sitemap */
				$data_sitemap = $data;
				$data_sitemap["id"] = -1;
				$this->googleRecord($xml_main, $xml_item, $data_sitemap);
				$num-=2;
			} else {
				$start-=1;
			}

			/* get all (public) items */
			$q = sprintf("select id, pageAlias, google_changefreq, google_priority, date_changed
				from cms_data where apEnabled IN (%s) and %s and id != %d order by id",
					implode(",", $this->public_roots),	$this->base_condition, $this->default_page);
			$res = sql_query($q, "", $start, $num);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["pageAlias"])
					$row["alias"] = $row["pageAlias"].".htm";
				else
					$row["alias"] = $row["id"].".htm";

				$this->googleRecord($xml_main, $xml_item, $row);
			}
			$xml_main = str_replace("{records}", "", $xml_main);
			if (!$_REQUEST["nocompress"])
				$xml_main = gzencode($xml_main, 5);

			$this->setApcCache($apckey, $xml_main);
			echo $xml_main;
		}
		exit();
	}
	private function googleRecord(&$xml_main, $xml_item, $record) {
		if (date("Y", $record["date_changed"]) == 1970)
			$record["date_changed"] = $record["datePublication"];

		$vars = array(
			"location"    => sprintf("http://%s/page/%s", $this->http_host, $record["alias"]),
			"date"        => date("c", $record["date_changed"]),
			"change_freq" => $record["google_changefreq"],
			"priority"    => ($record["google_priority"] != "0.5") ? $record["google_priority"]:""
		);
		if ($this->default_page == $record["id"])
			$vars["location"] = sprintf("http://%s", $this->http_host);

		if ($record["id"] == -1)
			$vars["location"] = sprintf("http://%s/sitemap.htm", $this->http_host);

		foreach ($vars as $k=>$v) {
			$v = strip_tags($v);
			$xml_item = str_replace(sprintf("{%s}", $k), $v, $xml_item);
			if ($k == "priority" && !$v)
				$xml_item = str_replace("<priority></priority>", "", $xml_item);
		}
		$xml_main = preg_replace("/(\{records\})/s", "\n".$xml_item."$1", $xml_main);
	}

	public function rssPage() {
		/* ie6 has no integrated rss support */
		if (preg_match("/MSIE (6|5)/si", $_SERVER["HTTP_USER_AGENT"])) {
			header("Content-Type: application/xhtml+xml");
			if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
				header("Content-Disposition: filename=\"rssfeed.xml\"");
			} else {
				header("Content-Disposition: attachment; filename=\"rssfeed.xml\"");
			}
		}

		$apckey = sprintf("rss_%s", md5(serialize($_REQUEST).$this->siteroot));
		$fetch = $this->getApcCache($apckey);
		if ($fetch) {
			echo $fetch;
		} else {

			/* get basic info */
			$xml_main = file_get_contents(self::include_dir."rss_main.xml");
			$xml_item = file_get_contents(self::include_dir."rss_item.xml");

			/* some global vars */
			//$settings = $this->cms->getCmsSettings();
			$settings = $this->cms_license;

			$descr =& $settings["search_descr"];

			if ($_REQUEST["address"]) {
				$type = "address";
				$address_data = new Address_data();
				$a = explode("|", $_REQUEST["sel"]);
				$text = sprintf("%s: %s",
					gettext("Last updates of address"),
					$address_data->getAddressNameById($a[0]));

				if ($a[1]) {
					$text.= sprintf(", %s: %s",
						gettext("with category"),
						$this->getPageTitle($a[1], -1, 1));
				}
			} elseif ($_REQUEST["meta"]) {
				$type = "meta";

				$data = $this->queryDecodeMetadata($_REQUEST["sel"]);
				$text = array();
				foreach ($data as $k=>$v) {
					if (is_array($v))
						$v = implode(" ".gettext("and")." ", $v);

					$field = $this->cms->getMetadataDefinitionById($k);
					$text[] = sprintf("%s = %s", $field["field_name"], $v);
				}
				$text = gettext("Last updated pages meeting the following criteria").": ".implode(", ", $text);

			} elseif ($_REQUEST["live"]) {
				$type = "live";
				$text = sprintf("%s: %s%s/page/%s",
					gettext("Live feed of page"),
					$this->protocol,
					$_SERVER["HTTP_HOST"],
					$this->checkAlias($_REQUEST["live"]));
			} elseif ($_REQUEST["parent"]) {
				$type = "parent";
				$text = sprintf("%s: %s%s/page/%s",
					gettext("Last updated child pages of page"),
					$this->protocol,
					$_SERVER["HTTP_HOST"],
					$this->checkAlias($_REQUEST["parent"]));
			} else {
				$type = "default";
				$text = sprintf("%s: %s%s",
					gettext("Last updated pages of"),
					$this->protocol,
					$_SERVER["HTTP_HOST"]);
			}

			if (!preg_match("/^http(s){0,1}:\/\//si", $this->logo))
				$rsslogo = $this->protocol.$_SERVER["HTTP_HOST"]."/".$this->logo;
			else
				$rsslogo = $this->logo;

			$vars = array(
				"title"       => $settings["cms_name"],
				"link"        => $GLOBALS["covide"]->webroot,
				"description" => $text,
				"language"    => $settings["search_language"],
				"copyright"   => $settings["search_copyright"],
				#"date"        => mktime(),
				"webmaster"   => $settings["search_email"],
				"author"      => $settings["search_author"],
				"favicon"     => $rsslogo
			);

			$vars["title"]       = htmlentities($vars["title"]);
			$vars["description"] = htmlentities($vars["description"]);
			$vars["copyright"]   = htmlentities($vars["description"]);

			foreach ($vars as $k=>$v) {
				$xml_main = str_replace(sprintf("{%s}", $k), $v, $xml_main);
			}

			switch ($type) {
				case "address":
					$vars = explode("|", $_REQUEST["sel"]);
					$addressid = (int)$vars[0];
					$parent    = (int)$vars[1];

					$regex_syntax = sql_syntax("regex");
					$repl = " replace(address_ids, ',', '|') ";
					$reg = " ($repl $regex_syntax '(^|\\\\|)". $addressid ."(\\\\||$)') ";

					$start = (int)$_REQUEST["start"];

					$data = array();

					if ($parent)
						$subq = sprintf("address_level = 1 AND parentPage = %d", $parent);
					else
						$subq = "address_level is NULL or address_level = 0";

					$q = sprintf("select * from cms_data where (%s) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
						$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
					$res = sql_query($q, "", $start, $this->pagesize);
					while ($row = sql_fetch_assoc($res)) {
						$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
						$this->rssRecord($xml_main, $xml_item, $row);
					}
					break;
				case "meta":

					//$data = unserialize(stripslashes($_REQUEST["sel"]));
					$data = $this->queryDecodeMetaData($_REQUEST["sel"]);

					require_once(self::include_dir."showMetaResultsBase.php");
					$pages = array_slice($pages, 0, 20, TRUE);

					if (is_array($pages)) {
						foreach ($pages as $id) {
							if ($id > 0) {
								$data = $this->cms->getPageById($id);
								$this->rssRecord($xml_main, $xml_item, $data);
							}
						}
					}
					break;
				case "live":
					$data = $this->cms->getPageById($_REQUEST["live"]);
					$row["author"] = $vars["author"];
					$this->rssRecord($xml_main, $xml_item, $data);
					break;
				case "parent":
					$pages = $this->getPagesByParent($_REQUEST["parent"], "datePublication desc", 20);
					foreach ($pages as $id=>$page) {
						$data = $this->cms->getPageById($id);
						$this->rssRecord($xml_main, $xml_item, $data);
					}
					break;
				default:
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
			$this->setApcCache($apckey, $xml_main);
			echo $xml_main;
		}
		exit();
	}
	private function rssFilter(&$data, $allow_binary=1) {
		if ($allow_binary)
			$data = sprintf("<![CDATA[%s]]>", $data);
		else
			$data = preg_replace("/[^a-z0-9 -\.,\"']/si", "", $data);
	}

	private function rssRecord(&$xml_main, $xml_item, $record) {
		$vars = array(
			"title"       => strip_tags($record["pageTitle"]),
			"date"        => date("r", $record["datePublication"]),
			"description" => trim($record["pageHeader"]),
			"link"        => sprintf("%s%s/page/%s", $this->protocol, $this->http_host, $this->checkAlias($record["id"]))
		);
		//trim(preg_replace("/ {2,}/s", " ", $this->html2txt(($record["pageHeader"]) ? $record["pageHeader"]:$record["pageData"]))

		if (!$vars["description"]) {
			if (trim(strip_tags($record["pageData"]))) {
				$vars["description"] = $this->html2txt($record["pageData"]);
			} else {
				$vars["description"] = trim(strip_tags($record["pageTitle"]));
			}
		}
		if (mb_strlen($vars["description"]) < 400 && trim(strip_tags($record["pageData"]))) {
			$vars["description"] = sprintf("<b>%s</b> - %s",
				$vars["description"], trim(strip_tags($record["pageData"])));
		}

		preg_match_all("/<img [^>]*?>/sxi", $record["pageData"], $img);
		$img = implode("<br>", $img[0]);

		$thumb = $this->getPageThumb($record["id"], 70, 70);
		$vars["description"] = str_replace("<br>", " ", $vars["description"]);
		$vars["description"] = $this->limit_string($vars["description"], 800, 0);

		if ($thumb) {
			$thumb = preg_replace("/>$/s", " align=\"left\">", $thumb);
			$vars["description"] = sprintf("%s %s", $thumb, $vars["description"]);
		}

		$this->rssFilter($vars["description"]);
		$this->rssFilter($vars["title"], 1);

		if ($vars["description"] == "..." || !$vars["description"])
			$vars["description"] = $vars["title"];

		foreach ($vars as $k=>$v) {
			if ($k == "date")
				$xml_main = str_replace("{date}", $v, $xml_main);

			//$v = strip_tags($v);
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
			$q = sprintf("update cms_form_results set user_value = '%s' where field_name = '%s' and id = %d",
				addslashes($value), addslashes($field), $rowid);
			sql_query($q);

		} else {
			$q = sprintf("insert into cms_form_results (pageid, field_name, visitor_id,
				user_value) values (%d, '%s', %d, '%s')", $pageid, addslashes($field),
				$visitor_id, addslashes($value));
			sql_query($q);

		}
	}
	private function sendform($req, &$files) {

		/* get form mode */
		$mode = 1; //$this->cms->getFormMode($req["system"]["pageid"]);

		$key = sprintf("s:{%s} p:{%s}", session_id(), $req["system"]["pageid"]);

		$q = sprintf("select count(*) from cms_temp where userkey = '%s' and ids = '%s'",
			$key, $_REQUEST["system"]["challenge"]);
		$res = sql_query($q);
		$num = sql_result($res,0,"",2);
		if ($num == 1) {
			$forms = $this->cms->getFormData($req["system"]["pageid"]);
			if (is_array($art)) {
				$forms[] = array(
					"id" => -1,
					"pageid" => $req["system"]["pageid"],
					"field_name" => gettext("Artikelen"),
					"field_type" => "textarea"
				);
			}

			$email_data = new Email_data();
			$_id[1] = $email_data->save_concept();
			$_id[2] = $email_data->save_concept();

			$smtp["rcpt"]    = "";
			$smtp["from"]    = "";
			$smtp["subject"] = "";
			$smtp["result"]  = "";

			$uri  = sprintf("%s%s/page/%s", $this->protocol, $_SERVER["HTTP_HOST"], $this->checkAlias($req["system"]["pageid"]));
			$uri2 = sprintf("<a target=\"_blank\" href=\"http://%s/\">%s</a>",
				$this->http_host, $this->http_host);


			$frm["name"] = sprintf("<a target=\"_blank\" href=\"http://%s/\">%s</a>",
				$this->http_host, $frm["name"]);

			$html = "<table class='table1' style='background-color: white;'>";
			$html.= sprintf("<tr><td class='head1' colspan='2'>%s</td></tr>",
				gettext("Confirmation from website")." ".$uri2);


			$q = sprintf("select * from cms_license_siteroots where pageid = %d",
				$this->siteroot);
			$res = sql_query($q);
			if (sql_num_rows($res) == 1)
				$row = sql_fetch_assoc($res);
			else
				$row = array();

			/* website name */
			if ($row["search_fields"])
				$frm["name"] = $row["search_fields"];
			else
				$frm["name"] = $this->cms_license["search_fields"];

			/* website logo */
			if ($row["cms_logo"])
				$frm["logo"] = $row["cms_logo"];
			else
				$frm["logo"] = $this->cms_license["cms_logo"];

			$fpage = $this->getPageById($req["system"]["pageid"]);

			$html.= sprintf("<tr><td class='head1' colspan='2' style='text-align: left'>%s: <a href='%s' target='_blank'>%s</a></td></tr>",
				gettext("Page"), $uri, $fpage["pageTitle"]);

			foreach ($forms as $k=>$v) {
				$isspecial = 0;
				if ($v["field_type"] == "hidden") {
					$req["data"][$v["field_name"]] = $v["field_value"];
					$isspecial = 1;
				}
				if ($v["is_mailto"]) {
					$smtp["rcpt"] = $req["data"][$v["field_name"]];
					$req["data"][$v["field_name"]] = sprintf("<a href='mailto:%1\$s'>%1\$s</a>",
						$req["data"][$v["field_name"]]);
					$isspecial = 1;
				}
				if ($v["is_mailfrom"]) {
					$smtp["from"] = $req["data"][$v["field_name"]];
					$req["data"][$v["field_name"]] = sprintf("<a href='mailto:%1\$s'>%1\$s</a>",
						$req["data"][$v["field_name"]]);
					$isspecial = 1;
				}
				if ($v["is_mailsubject"]) {
					$smtp["subject"] = $req["data"][$v["field_name"]];
					$isspecial = 1;
				}
				if ($v["is_redirect"]) {
					$smtp["result"] = $req["data"][$v["field_name"]];
					$isspecial = 1;
				}
				if ($isspecial == 0 || $v["field_type"] != "hidden") {
					/* normal field */
					if ($v["field_type"] == "checkbox") {
						$req["data"][$v["field_name"]] = implode(", ", $req["data"][$v["field_name"]]);
					} elseif ($v["field_type"] == "upload") {
						if (is_array($files)) {
							foreach ($files["binFile"]["name"] as $f) {
								$req["data"][$v["field_name"]] .= $f."\n";
							}
						}
					}
					if (!$v["is_mailfrom"] && !$v["is_mailto"])
						$req["data"][$v["field_name"]] = str_replace(htmlentities($this->valuta), $this->valuta, nl2br(htmlentities($req["data"][$v["field_name"]])));

					$html.= sprintf("<tr bgcolor='#f6f6f6'><td class='cell1' valign='top'>%s: </td><td class='cell2'>%s</td></tr>",
						$v["field_name"], $req["data"][$v["field_name"]]);
				}
			}
			$html.= "</table>";

			/* if shop order */
			if ($req["system"]["is_shop"]) {
				$html.="<table>";
				$html.= sprintf("<tr><td class='head1' colspan='2' style='text-align: left'>%s</td></tr>",
					gettext("Articles"));

			}
			echo "<PRE>";
			print_r($req);
			print_r($_SESSION);
			echo "</PRE>";
			die();


			$emailData =& $email_data;

			$req["mail"] = array(
				"from"     => $smtp["from"],
				"to"       => $smtp["rcpt"],
				"rcpt"     => $smtp["rcpt"],
				"subject"  => sprintf("[%s] %s", $this->http_host, $smtp["subject"])
			);
			$req["contents"] = $html;
			$emailData->save_concept($_id[1], $req);

			$req["mail"] = array(
				"from"     => $smtp["rcpt"],
				"to"       => $smtp["from"],
				"rcpt"     => $smtp["from"],
				"subject"  => sprintf("[%s] %s", $this->http_host, $smtp["subject"])
			);
			$req["contents"] = $html;

			if (strtolower($smtp["from"]) == strtolower($smtp["rcpt"]))
				$double = 1;

			if (!$double)
				$emailData->save_concept($_id[2], $req);
			if (is_array($files)) {
				echo $emailData->upload_files($_id[1], 1);
				if (!$double)
					echo $emailData->upload_files($_id[2], 1);
			}
			$msg[1] = $emailData->sendMailComplex($_id[1], 1, 1);
			if (!$double)
				$msg[2] = $emailData->sendMailComplex($_id[2], 1, 1);

			/* move the new mail to its archive */
			$folder_id = $emailData->checkSharedFolders($req["system"]["pageid"]);
			$emailData->messageToFolder($_id[1], $folder_id);

			/* drop the second mail to deleted items */
			if (!$double)
				$emailData->mail_delete($_id[2]);

			/* check result uri */
			if (!preg_match("/^http(s){0,1}/s", $smtp["result"])) {
				$smtp["result"] = preg_replace("/^\//s", "", $smtp["result"]);
				$smtp["result"] = sprintf("http://%s/%s", $this->http_host, $smtp["result"]);
				$smtp["result"] = urldecode($smtp["result"]);
			}

			if ($req["system"]["is_shop"] && $this->cms_license["ideal_type"] == "rabolite") {
				$next_order = -1;
				$expected_order = 0;

				/* update database order number */
				while ($next_order != $expected_order) {
					$next_order = -1;
					$expected_order = 0;

					$q = "select ideal_last_order from cms_license";
					$res = sql_query($q);
					$expected_order = sql_result($res,0,"",2) + 1;

					/* reset counter if it goes too big, this way we can handle 99.999 transactions a day */
					if ($expected_order > 99999) {
						$q = "update cms_license set ideal_last_order = 0";
						$res = sql_query($q);
						$expected_order = 1;
					}

					/* set last order + 1 */
					$q = "update cms_license set ideal_last_order = ideal_last_order + 1";
					sql_query($q);

					/* now get the new order */
					$q = "select ideal_last_order from cms_license";
					$res = sql_query($q);
					$next_order = sql_result($res,0,"",2);
				}
				/* create unique order number */
				$ordernr = sprintf("%s-%05s-%03s", date("dmy"), (int)$next_order, rand(0, 999));

				/* create ideal form */
				$win = md5(mktime()*rand());
				$ideal = new Layout_output();
				$ideal->addTag("form", array(
					"action" => sprintf("https://ideal%s.rabobank.nl/ideal/mpiPayInitRabo.do", ($this->cms_license["ideal_test_mode"]) ? "test":""),
					"method" => "post",
					"id"     => "idealfrm",
					"target" => "_top" //sprintf("ideal_%s", $win)
				));
				$ts = date("Y-m-d\TG:i:s\Z", mktime()+(5*60)); //5 minutes

				$ideal->addHiddenField("merchantID",
					$this->ideal_filter($this->cms_license["ideal_merchant_id"], 9));

				$ideal->addHiddenField("subID", "0");
				$ideal->addHiddenField("amount",      $this->ideal_filter((int)($total*100), 12));
				$ideal->addHiddenField("purchaseID",  $this->ideal_filter($ordernr, 16));
				$ideal->addHiddenField("language",    ($this->language) ? $this->language:"en");
				$ideal->addHiddenField("currency",    "EUR");
				$ideal->addHiddenField("description", $this->ideal_filter($ordernr, 16));
				$ideal->addHiddenField("paymentType", "ideal");
				$ideal->addHiddenField("validUntil",  $ts);

				$ideal->addHiddenField("urlCancel",   $this->ideal_filter(sprintf("http://%s/mode/shop_cancel", $this->http_host), 512));
				$ideal->addHiddenField("urlError",    $this->ideal_filter(sprintf("http://%s/mode/shop_error", $this->http_host), 512));
				$ideal->addHiddenField("urlSuccess",  $this->ideal_filter(sprintf("%s", $smtp["result"]), 512));

				$hash = "";
				$i = 0;
				foreach ($_SESSION["shop"] as $k=>$v) {
					$i++;
					$page = $this->getPageById($k);
					$ideal->addHiddenField(sprintf("itemNumber%d", $i),      $this->ideal_filter($page["pageTitle"], 12));
					$ideal->addHiddenField(sprintf("itemDescription%d", $i), $this->ideal_filter($page["pageHeader"], 32));
					$ideal->addHiddenField(sprintf("itemQuantity%d", $i),    $this->ideal_filter((int)$v, 4));
					$ideal->addHiddenField(sprintf("itemPrice%d", $i),       $this->ideal_filter((int)($page["shopPrice"]*100), 12));

					$hash.= sprintf("%s%s%s%s",
						$this->ideal_filter($page["pageTitle"], 12),
						$this->ideal_filter($page["pageHeader"], 32),
						$this->ideal_filter((int)$v, 4),
						$this->ideal_filter((int)($page["shopPrice"]*100), 12)
					);
				}
				$hash = html_entity_decode(sprintf("%s%s%s%s%s%s%s%s",
					$this->cms_license["ideal_secret_key"],
					$this->ideal_filter($this->cms_license["ideal_merchant_id"], 9),
					0, $this->ideal_filter((int)($total*100), 12),
					$this->ideal_filter($ordernr, 16),
					"ideal", $ts, $hash
				));

				$hash = str_replace(array("\t", "\n", "\r", " "), "", $hash);
				$hash = sha1($hash, false);

				$ideal->addHiddenField("hash", $this->ideal_filter($hash, 50));
				$ideal->start_javascript();
					$ideal->addCode(sprintf("
						//parent.document.getElementById('ideal_order_button').style.display = 'none';
						//parent.document.getElementById('ideal_order_done').style.display = '';
						//parent.popup('blank.htm', 'ideal_%s', 980, 700, 1);
						setTimeout('document.getElementById(\"idealfrm\").submit();', 1000);
					", $win));
				$ideal->end_javascript();
				$ideal->endTag("form");
			}

			/* remove key */
			$output = new Layout_output();
			if ($ideal) {
				$output->addCode($ideal->generate_output());
			} else {
				$t = str_replace("http://".$this->http_host."/", "", $smtp["result"]);
				if (preg_match("/^index\.php\?id=\d{1,}/si", $t))
					$smtp["result"] = preg_replace("/^(.*)index\.php\?id=(\d{1,})/si", "/page/$2", $t);

				$output->start_javascript();
					$output->addCode(sprintf("parent.document.location.href = '%s'; ",
						addslashes($smtp["result"])));
				$output->end_javascript();
			}
			/* delete shop contents */
			unset($_SESSION["shop"]);
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
	private function ideal_filter($str, $len) {
		$str = trim(preg_replace("/((\\0)|(\t)|(\n)|(\r))/s", "", $str));
		$str = mb_substr($str, 0, $len);
		return $str;
	}
	public function getParentPage($id) {
		return $this->cms->getParent($id);
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
				"website"   => $this->protocol.$_SERVER["HTTP_HOST"],
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
		return $this->apc_cache->getApcCache($ident);
	}
	public function setApcCache($ident, $contents) {
		$this->apc_cache->setApcCache($ident, $contents);
	}

	private function setApcOptions() {
		$this->apc_cache->setOptions(
			&$this->siteroot,
			&$this->page_cache_timeout,
			&$this->apc_fetch,
			&$this->apc_disable
		);
	}

	private function checkOldThumbFiles() {
		/* prevent checking every x sec */
		$key = sprintf("thumbcache");
		$fetch = $this->getApcCache($key);
		if (!$fetch) {
			/* remove all aged thumb files > 24 hours */
			/* thumbs will be re-created automatically */
			$ts = mktime(date("h")-24,date("i"),0,date("m"),date("d"),date("Y"));
			$q = sprintf("select id from cms_image_cache where datetime < %d", $ts);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				$this->cms->removeThumbCacheFile($row["id"]);
			}
			$this->setApcCache($key, 1);
		}
	}

	private function queryEncodeMetaData($data) {
		if (!is_array($data))
			$data = array();

		foreach ($data as $k=>$v) {
			if (is_array($v)) {
				foreach ($v as $z=>$y) {
					if (!trim($y))
						unset($v[$z]);
				}
				if (count($v) == 0)
					unset($data[$k]);
			} else {
				if (!trim($v))
					unset($data[$k]);
			}
		}
		$data =
			urlencode(
				base64_encode(
					gzcompress(
						serialize($data)
					)
				)
			);
		return $data;
	}
	private function queryDecodeMetaData($data) {
		$data =
			@unserialize(
				@gzuncompress(
					@base64_decode(
						@stripslashes($data)
					)
				)
			);
			if (!is_array($data)) {
				$this->triggerError(404);
				echo "<b>".gettext("No valid metadata selection supplied.")."</b>";
				echo "<br><br>";
				echo gettext("Malformed request URI. Check your link/url.");
				exit();
			}

		return $data;
	}

	private function metaInitResults() {
		$data  = $_REQUEST["data"];
		$avail = $_REQUEST["avail"];

		/* unset not used values */
		foreach ($data as $k=>$v) {
			if ($avail[$k] != 1)
				unset($data[$k]);
		}
		$data = $this->queryEncodeMetaData($data);
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				document.location.href='/metadata/?query=%s'
			", $data));
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function getMetadataByPage($id) {
		return $this->cms->getMetadataById($id);
	}

	public function getMetadataById($pageid, $meta_field_id=0) {
		if (!$this->meta_cache[$pageid])
			$this->meta_cache[$pageid] = $this->cms->getMetadataData($pageid);

		$data =& $this->meta_cache[$pageid]["data"];
		$meta = array();
		foreach ($data as $groupname=>$g) {
			foreach ($g as $field=>$v) {
				$v["group"] = $groupname;
				$meta[$v["id"]] = $v;
			}
		}
		if ($meta_field_id)
			return $meta[$meta_field_id]["value"];
		else
			return $meta;
	}

	public function getPagesByMetadata($meta_criteria, $order="datePublication desc", $limit=0, $meta_operator="and") {
		if (!is_array($meta_criteria))
			return array();

		if (!in_array(strtolower($meta_operator), array("and", "or")))
			return array();

		if ($limit > 1000)
			$limit = 1000;

		$q = "select id from cms_data where id IN (select pageid from cms_metadata where 1=1 ";
		foreach ($meta_criteria as $field=>$v) {
			$q.= sprintf (" %s (fieldid = %d and value = '%s') ", $meta_operator, $field, addslashes($v));
		}
		$q.= sprintf(") and apEnabled IN (%s) and %s",
			implode(",", $this->public_roots), $this->base_condition);

		if ($order)
			$q.= sprintf(" order by %s", $order);

		if ($limit > 0)
			$q.= sprintf(" LIMIT %d", $limit);

		$ids = array();
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$ids[] = $row["id"];
		}
		$pages = $this->getPagesById($ids, $order);
		return $pages;
	}

	public function getCmsAddressList($pageid=0) {
		$address_data = new Address_data();

		$q = sprintf("select address_ids from cms_data where (address_level is NULL or address_level = 0) and apEnabled in (%s) and %s and (address_ids != '') group by address_ids",
			implode(",", $this->public_roots), $this->base_condition);

		if ($pageid)
			$q.= sprintf(" and id = %d ", $pageid);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$row["address_ids"] = explode(",", $row["address_ids"]);
			foreach ($row["address_ids"] as $a) {
				if ($a > 0) {
					if (!$this->address_cache[$a])
						$this->address_cache[$a] = trim($address_data->getAddressNameById($a));

					if ($this->address_cache[$a])
						$ids[$a] = $this->address_cache[$a];
				}
			}
		}
		if (!is_array($ids))
			$ids = array();

		natcasesort($ids);

		$regex_syntax = sql_syntax("regex");
		$repl = " replace(address_ids, ',', '|') ";
		foreach ($ids as $k=>$v) {
			$reg = " ($repl $regex_syntax '(^|\\\\|)". $k ."(\\\\||$)') ";

			$data = array();
			$q = sprintf("select count(*) from cms_data where (address_level IS NULL or address_level = 0) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
				implode(",", $this->public_roots), $this->base_condition, $reg);
			$res = sql_query($q);
			$ids[$k] = array(
				"count" => sql_result($res,0,"",2),
				"name"  => $v
			);
		}
		return $ids;
	}

	public function generateAddressList() {

		$fetch = $this->getApcCache("addresslist");
		if ($fetch) {
			echo $fetch;
		} else {
			//$cms_license = $this->cms->getCmsSettings();
			$cms_license = $this->cms_license;

			if (!$cms_license["cms_address"]) {
				$this->triggerError(403);
				echo ("Module is disabled");
				return;
			}

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 3,
				"class" => "view_header table_data"
			));

			$list = $this->getCmsAddressList();

			$i = 0;
			$data = array(
				1 => array(),
				2 => array()
			);
			foreach ($list as $k=>$v) {
				$i++;
				$data[($i % 2 != 0) ? 1:2][] = array(
					"name"  => $v["name"],
					"count" => $v["count"],
					"id"    => $k
				);
			}

			$table->addTableHeader(array(
				"colspan" => 2,
				"style"   => "text-align: left;"
			));
				$table->addCode(gettext("Addresses"));
			$table->endTableHeader();

			for ($i=0; $i < count($data[1]); $i++) {
				$table->addTableRow(array(
					"class" => "list_record"
				));
					$table->addTableData();
						$table->insertTag("a", sprintf("%s (%d)", $data[1][$i]["name"], $data[1][$i]["count"]), array(
							"href" => sprintf("/addressdata/?address=%d", $data[1][$i]["id"])
						));
					$table->endTableData();
					$table->addTableData();
						$table->insertAction("rss", gettext("rss"), sprintf("/rss/address/%d|", $data[1][$i]["id"]));
						$table->addSpace();
					$table->endTableData();
					$table->addTableData();
						if ($data[2][$i])
						$table->insertTag("a", sprintf("%s (%d)", $data[2][$i]["name"], $data[2][$i]["count"]), array(
							"href" => sprintf("/addressdata/?address=%d", $data[2][$i]["id"])
						));
					$table->endTableData();
					$table->addTableData();
						$table->insertAction("rss", gettext("rss"), sprintf("/rss/address/%d|", $data[2][$i]["id"]));
						$table->addSpace();
					$table->endTableData();
				$table->endTableRow();
			}
			$table->endTable();
			$buffer = $table->generate_output();
			$this->setApcCache("addresslist", $buffer);
			echo $buffer;
		}
	}

	public function generateAddressRecords($addressid, $parent=0) {

		$apckey = sprintf("address_%d_%d_%d", $addressid, $parent, (int)$_REQUEST["start"]);
		$fetch = $this->getApcCache($apckey);
		if ($fetch) {
			echo $fetch;
		} else {

			//$cms_license = $this->cms->getCmsSettings();
			$cms_license = $this->cms_license;

			if (!$cms_license["cms_address"]) {
				$this->triggerError(403);
				echo ("Module is disabled");
				return;
			}

			$output = new Layout_output();
			$address_data = new Address_data();

			$output->insertAction("addressbook", gettext("Address search"), "");
			$output->addSpace();
			$output->generate_output();
			$output->addTag("b", array(
				"id" => "address_relation"
			));
			$output->addCode(gettext("Relation").": ");
			$output->addCode(sprintf("<a href='/addressdata/?address=%d'>%s</a>",
				$addressid, $address_data->getAddressNameById($addressid)));
			if ($_REQUEST["parent"]) {
				$output->addCode(", ".gettext("category").": ");
				$output->addCode($this->getPageTitle($parent, -1, 1));
			}
			$output->addTag("br");
			$output->addTag("br");
			$output->endTag("b");

			$regex_syntax = sql_syntax("regex");
			$repl = " replace(address_ids, ',', '|') ";
			$reg = " ($repl $regex_syntax '(^|\\\\|)". $addressid ."(\\\\||$)') ";

			$start = (int)$_REQUEST["start"];

			$data = array();

			if ($parent)
				$subq = sprintf("address_level = 1 AND parentPage = %d", $parent);
			else
				$subq = "address_level is NULL or address_level = 0";

			$q = sprintf("select * from cms_data where (%s) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
				$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
			$res = sql_query($q, "", $start, $this->pagesize);
			while ($row = sql_fetch_assoc($res)) {
				$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
				if ($row["pageAlias"])
					$row["pageAlias"].= ".htm";
				else
					$row["pageAlias"] = $row["id"].".htm";

				$data[]= $row;
			}

			$q = sprintf("select count(*) from cms_data where (%s) and apEnabled IN (%s) and %s and %s",
				$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
			$res = sql_query($q);
			$num = sql_result($res,0,"",2);

			$q = sprintf("select parentPage, count(*) as num from cms_data where (address_level = 1) and apEnabled IN (%s) and %s and %s group by parentPage order by datePublication DESC",
				implode(",", $this->public_roots), $this->base_condition, $reg);
			$res = sql_query($q, "", (int)$_REQUEST["start"], $this->pagesize);

			if (sql_num_rows($res) > 0) {
				$output->addCode(gettext("Available subcategories").": ");
				$output->addTag("ul", array(
					"id" => "address_subcategories"
				));
				while ($row = sql_fetch_assoc($res)) {
					if ($parent == $row["parentPage"])
						if ($parent == $row["parentPage"]) {
							$bold1 = "<b>";
							$bold2 = "</b>";
						} else {
							$bold1 = "";
							$bold2 = "";
						}
						$output->addCode(
							sprintf("<li><a href='/addressdata/?address=%d&parent=%d'>%s%s (%d)%s</a></li>",
							$addressid, $row["parentPage"], $bold1, $this->getPageTitle($row["parentPage"], -1, 1), $row["num"], $bold2)
						);
				}
				$output->endTag("ul");
			}


			$view = new Layout_view(1);
			$view->addData($data);

			$view->setHtmlField("pageTitle");
			$view->addMapping(gettext("page name"), "%%complex", "left");
			$view->addMapping(gettext("date"), "%datePublication_h", "left");
			$view->defineComplexMapping("complex", array(
				array(
					"type" => "link",
					"link" => array("/page/", "%pageAlias"),
					"text" => "%pageTitle"
				)
			));

			$output->addCode($view->generate_output(1));

			if ($num > $this->pagesize) {
				$output->addTag("br");
				$output->addTag("br");
				$next_results = sprintf("/addressdata/?address=%d&amp;start=%%%%", $addressid);
				$paging = new Layout_paging();
				$paging->setOptions($start, $num, $next_results, $this->pagesize, 1);
				$output->addCode($paging->generate_output());
				$output->addTag("br");
			}
			$buffer = $output->generate_output();
			$this->setApcCache($apckey, $buffer);
			echo $buffer;
		}
	}
	public function getSiteRootHostnames($id) {
		$q = sprintf("select cms_hostnames from cms_license_siteroots where pageid = %d",
			$id);
		$res = sql_query($q);
		$host = explode("\n", sql_result($res,0));
		return $host;
	}

	public function getRelatedAddressPages($currentpage=-1, $parentpage=0) {
		if ($currentpage == -1)
			$currentpage = $this->pageid;

		if (!is_numeric($currentpage))
			return array();

		$data = $this->getPageById($currentpage);
		$addressid = trim(str_replace(",", "|", $data["address_ids"]));
		if (!$addressid)
			return array();

		$regex_syntax = sql_syntax("regex");
		$repl = " replace(address_ids, ',', '|') ";
		$reg = " ($repl $regex_syntax '(^|\\\\|)". $addressid ."(\\\\||$)') ";

		$data = array();
		$q = sprintf("select parentPage, pageTitle, id from cms_data where (address_level = 1) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
			implode(",", $this->public_roots), $this->base_condition, $reg);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["parentPage"]][] = $row;
		}
		return $data;
	}
	public function getAddressNames($pageid, $address_ids) {
		$address_data = new Address_data();
		$address_ids = explode(",", $address_ids);

		/* retrieve parent page */
		$parent = $this->cms->getParent($pageid);

		if ((count($address_ids > 0) && $address_ids[0] > 0) || count($address_ids) > 1) {
			echo "\n<ul id='address_names'>";
			foreach ($address_ids as $a) {
				if ($a) {
					$i++;
					echo sprintf("\n<li><a href='/addressdata/?address=%d'>%s</a>",
						$a, $address_data->getAddressNameById($a));

					$related = $this->getRelatedAddressPages($pageid);
					if (count($related) > 0) {
						echo "\n<ul>";
						foreach ($related as $k=>$record) {
							$titles[$k] = $this->getPageTitle($k, -1, 1);
						}
						natcasesort($related);
						foreach ($related as $k=>$record) {
							if ($parent == $k) {
								$bold1 = "<b>";
								$bold2 = "</b>";
							} else {
								$bold1 = "";
								$bold2 = "";
							}
							echo sprintf("\n<li>%s<a href='/addressdata/?address=%d&parent=%d'>%s</a> (%d)%s</li>",
								$bold1, $a, $k, $titles[$k], count($record), $bold2);
						}

						echo "\n</ul>";
					}
					echo "\n</li>";
				}
			}
			echo "\n</ul>";
		}
	}
	public function insertAction($action, $alt="", $url="") {
		$output = new Layout_output();
		$output->insertAction($action, $alt, $url);
		return $output->generate_output();
	}

	public function checkCalendar($id) {
		$q = sprintf("select count(*) from cms_date_index where pageid = %d", $id);
		$res = sql_query($q);
		return sql_result($res,0,"",2);
	}
	public function getCalendarInfo($id=0, $dateid=0) {
		$buf = "<ul>";
		if ($id > 0)
			$q = sprintf("select * from cms_date where pageid = %d order by date_begin", $id);
		else
			$q = sprintf("select * from cms_date where id IN (%s) order by date_begin", implode(",", $dateid));

		$res = sql_query($q);
		while ($row = sql_fetch_array($res)) {
			if ($row["repeating"]) {
				$repeat = str_replace("|",", ",$row["repeating"]);
			} else {
				$repeat = gettext("every day");
			}
			if ($row["date_begin"] != $row["date_end"]) {
				$buf .= sprintf("<li><b>%s %s %s %s</b><br>%s %s<br>%s</li>",
					gettext("from"),
					date("d-m-Y", $row["date_begin"]),
					gettext("till"),
					date("d-m-Y", $row["date_end"]),
					gettext("every"),
					$repeat,
					$row["description"]
				);
			} else {
				$buf .= sprintf("<li><b>%s %s</b><br>%s</li>",
					gettext("on"),
					strftime("%A, %d-%m-%Y",$row2["datum"]),
					$row["description"]
				);
			}
		}
		$buf.= "</ul>";
		return $buf;
	}
	public function getCalendar($pageid=0) {
		if (!$pageid) $pageid = $this->pageid;
		$this->loadCalendar($pageid, $_REQUEST["calstart"]);
	}

	public function searchCalendar($start, $end) {
		$pages = array();
		$q = sprintf("select pageid from cms_date_index where datum between %d and %d group by pageid",
			$start, $end);
		$res = sql_query($q);
		while ($row = sql_fetch_array($res)) {
			$pages[] = $row["pageid"];
		}
		return ($pages);
	}

	public function loadCalendar($id, $start=0) {
		require(self::include_dir."getCalendar.php");
	}

	private function get_urls($string, $strict=true) {
		$types = array("href", "src", "url");
		while (list(,$type) = each($types)) {
				$innerT = $strict?'[a-z0-9:?=&@/._-]+?':'.+?';
				preg_match_all ("|$type\=([\"'`])(".$innerT.")\\1|i", $string, &$matches);
				$ret[$type] = $matches[2];
		}
		return $ret;
	}

	public function getBanners($cound, $horizontal=0) {
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("banner_loader(%d, %d);",
				$cound, $horizontal));
		$output->end_javascript();
		echo $output->generate_output();
	}
	public function getBannersXML($count, $horizontal=0) {
		return true;
		require(self::include_dir."getBanners.php");
	}
	private function displayBanner($banner) {
		if ($banner["website_real"]) {
			echo sprintf("<a href='%s' target='_new'><img src='%s' class='banner'></a>",
				$banner["website"], $banner["location"]);
		} else {
			echo sprintf("<img src='%s' border='0'>", $banner["location"]);
		}
	}

	private function strtrim($str, $maxlen=100, $elli=NULL, $maxoverflow=50) {
		if (mb_strlen($str) > $maxlen) {
			$output = NULL;
			$body = explode(" ", $str);
			$body_count = count($body);
			$i=0;

			do {
				$output .= $body[$i]." ";
				$thisLen = mb_strlen($output);
				$cycle = ($thisLen < $maxlen && $i < $body_count-1 && ($thisLen+mb_strlen($body[$i+1])) < $maxlen+$maxoverflow?true:false);
				$i++;
			} while ($cycle);
			return $output.$elli;
		} else {
			return $str;
		}
	}

	public function limit_string($text, $len, $break_words=1, $tail="...") {
		$text = trim(strip_tags($text));
		$length = mb_strlen($text);
		if ($length <= $len)
			return $text;

		if (!$break_words) {
			$ret = mb_substr($text, 0, $len).$tail;
		} else {
			$ret = $this->strtrim($text, $len);
		}
		if ($ret != "...")
			return $ret;
	}
	public function disableCache() {
		//$this->apc_disable = 1;
	}
	public function disableFileSearch($state) {
		$this->disableFileSearch = (int)$state;
	}
	public function getRSSitems($feed, $count) {
		$rss_data = new Rss_data();
		return $rss_data->getRSSitems($feed, $count);
	}
	public function Iframe($settings) {
		/* create an iframe */
		echo "<iframe";
		foreach ($settings as $k=>$v) {
			echo sprintf(" %s=\"%s\"", $k, $v);
		}
		echo "></iframe>";
	}

	public function getRandomFile($folderid, $num=1, $images_only=1) {
		/* create filesys object */
		if (!$this->filesys)
			$this->filesys = new Filesys_data();
		$files = $this->filesys->getFiles(array("folderid" => $folderid, "no_cms_scan" => 1));

		$found = 0;
		$pages = array();

		/* do while we have not sufficient results */
		while ($found != $num) {
			/* if no candidates are left, return the result */
			if (count($files) == 0)
				return $pages;

			/* random position */
			$rand = rand(0, count($files)-1);

			/* if exists */
			if ($files[$rand]) {

				/* if this is an image or if no image filter is active */
				if (!$images_only || $files[$rand]["subtype"] == "image") {
					/* add to results */
					$pages[] = array(
						"id"   => $files[$rand]["id"],
						"src"  => sprintf("/cmsfile/%d", $files[$rand]["id"]),
						"name" => $files[$rand]["name"],
						"description" => $files[$rand]["description"]
					);
					$found++;
				}
				/* unset the position and reset the array */
				unset($files[$rand]);
				reset($files);
			}
		}
		return $pages;
	}
	private function saveFeedback($data) {
		$key = sprintf("s:{%s} p:{%s}", session_id(), $data["system"]["pageid"]);

		$q = sprintf("select count(*) from cms_temp where userkey = '%s' and ids = '%s'",
			$key, $_REQUEST["system"]["challenge"]);

		$res = sql_query($q);
		$num = sql_result($res,0,"",2);
		if ($num == 1) {
			if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
				$q = sprintf("insert into cms_feedback (page_id, datetime, user_id, subject, body, is_visitor)
					values (%d, %d, %d, '%s', '%s', %d)", $data["system"]["pageid"], mktime(),
					($_SESSION["user_id"]) ? $_SESSION["user_id"]:$_SESSION["visitor_id"],
					$data["feedback"]["subject"], $data["feedback"]["body"],
					($_SESSION["visitor_id"]) ? 1:0);
				sql_query($q);
			}
		}
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				parent.location.href = '/page/%s';
			", $this->checkAlias($data["system"]["pageid"])));
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function register_new_user() {
		require(self::include_dir."registerUser.php");
	}

	public function hitCounter($name) {
		$val = $this->cms->raiseCounter($name);
		return $val;
	}
	public function setMenuTemplate($id) {
		$this->menu_template = $id;
	}
	public function setMenuTypeVertical() {
		$this->menu_type = "vertical";
	}

	private function letsstatAnalytics($cms_siteroot) {
		$data = $this->getPageById($this->pageid);
		$output = new Layout_output();
		$output->load_javascript("http://engine.letsstat.nl/dev/letsstat.js", 1);
		$output->start_javascript();
		$output->addCode(sprintf("
			var lst_title = \"%s\";
			var lst_url = \"http://%s/page/%s.htm\";
			var lst_sid = \"%s\";
			lsttc();
		", addslashes($data["pageTitle"]), $this->http_host,
			($data["pageAlias"]) ? $data["pageAlias"]:$data["id"],
			addslashes($cms_siteroot["letsstat_analytics"])
		));
		$output->end_javascript();
		return $output->generate_output();
	}
	private function googleAnalytics($cms_siteroot) {
		$output = new Layout_output();
		$output = new Layout_output();
		$output->load_javascript("http://www.google-analytics.com/urchin.js", 1);
		$output->start_javascript();
		$output->addCode(sprintf("
			_uacct = \"%s\";
			urchinTracker();
		", addslashes($cms_siteroot["google_analytics"])
		));
		$output->end_javascript();
		return $output->generate_output();
	}
}
?>
