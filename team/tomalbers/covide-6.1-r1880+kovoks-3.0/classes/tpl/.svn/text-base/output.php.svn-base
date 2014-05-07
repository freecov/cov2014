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


	private $page_cache = array();
	private $page_aliases = array();
	private $base_condition;
	private $base_condition_menu;

	private $menu_loaded = 0;
	public $pagesize = 20;


	public function __construct() {
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
		$this->default_page = sql_result($res,0);

		/* handle current request or page */
		$this->handlePage();
		$this->switchCustomModes();

	}
	private function checkInteralRequest($page=0) {
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
		$data = $this->getTemplateById($id);
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
		if ($this->checkInteralRequest($pageid)==0) {
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
		if (sql_result($res,0) != 1) {
			die("error: main page not defined!");
		} else {
			return sql_result($res,0);
		}
	}

	private function handlePage() {
		$page =& $this->pageid;

		if ($_REQUEST["mode"]) {
			switch ($_REQUEST["mode"]) {
				case "sitemap":
				case "sitemap_plain":
					$page = "__sitemap"; break;
				case "sitemap": $page = "__sitemap"; break;
				case "search" : $page = "__search";  break;
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

		if ($this->checkInteralRequest()==0) {
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
		$q = sprintf("select pageRedirect, pageRedirectPopup from cms_data where id = %d", $page);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		if ($row["pageRedirect"]) {
			if ($row["pageRedirectPopup"]) {
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode( sprintf("var cvd_%s = window.open('%s');", md5(rand()), $row["pageRedirect"]) );
				$output->end_javascript();
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
		if ($this->checkInteralRequest($id)==1) {
			switch ($id) {
				case "__sitemap":
					$title = "Sitemap";
					break;
				case "__search":
					$title = "Zoekresultaten";
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
		$data = $this->cms->getPagesById($id, 0, 1);
		$data["alias"] = $this->checkAlias($id);
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

	public function getPageData($id, $prefix="page") {
		if ($this->checkInteralRequest($id)==1) {
			switch ($id) {
				case "__sitemap":
					$this->generateSitemap();
					break;
				case "__err404":
					$this->triggerError(404);
				case "__search":
					require(self::include_dir."search.php");
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

			if ($this->page_cache[$id]["isList"])
				$this->handleList($data, $id);

			/* handle list on this page */
			/*
			if ($page["isList"])
				$this->handleList($data);
			*/
			echo $data;
		}
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

		$out = fopen($this->menu_cache_file, "w");
		fwrite($out, $buffer);
		fclose($out);

		echo $buffer;
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
			case 307:
				header("Status: 307 Temporary Redirect");
				break;
		}
	}

	public function handleList(&$data, $id=0) {
		if (!$pageid) $pageid = $this->pageid;
		require(self::include_dir."handleList.php");
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


	}
	public function switchCustomModes() {
		switch ($_REQUEST["mode"]) {
			case "menu":
				$this->generate_menu_loader((int)$_REQUEST["pid"]);
				exit();
			case "text":
				$this->textPage();
				exit();
			case "sitemap_plain":
				$this->textPage();
				exit();
			case "robots":
				$this->generate_robots_file();
				exit();
		}
	}
}