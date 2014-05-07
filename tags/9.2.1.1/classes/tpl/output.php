<?php
/**
 * Covide CMS Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class Tpl_output {
	/* constants */
	const include_dir = "classes/tpl/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "tpl";

	private $_rendertime;

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

	private $page_cache    = array();
	private $meta_cache    = array();
	private $address_cache = array();

	private $page_aliases  = array();
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
	private $has_abbr = array();

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

	private $login_text_username;
	private $login_text_password;
	private $crlf;

	private $page_less_rewrites = 0;

	private $address_fields = array(
		"companyname"  => "company name",
		"tav"          => "contact person",
		"address"      => "address",
		"zipcode"      => "zipcode",
		"city"         => "city",
		"country"      => "country",
		"phone_nr"     => "phone number",
		"fax_nr"       => "fax number",
		"email"        => "email address"
	);

	private $crm_fields_minimal = array(
		"surname"      => "last name",
		"infix"        => "infix",
		"givenname"    => "given name",
		"birthday"     => "birth date",
		"email"        => "email address",
		"cla"          => "classifications"
	);

	private $crm_fields = array(
		"companyname"  => "company name",
		"address"      => "address",
		"zipcode"      => "zipcode",
		"city"         => "city",
		"phone_nr"     => "phone_nr",
		"fax_nr"       => "fax number",
		"surname"      => "last name",
		"infix"        => "infix",
		"givenname"    => "given name",
		"commencement" => "commencement",
		"birthday"     => "birth date",
		"email"        => "email address",
		"cla"          => "classifications",
		"memo"         => "remarks",
	);
	private $crm_fields_bcard = array(
		"companyname"  => "company name",
		"commencement" => "commencement",
		"givenname"    => "given name",
		"infix"        => "infix",
		"surname"      => "last name",
		"address"      => "address",
		"zipcode"      => "zipcode",
		"city"         => "city",
		"phone_nr"     => "phone_nr",
		"mobile_nr"    => "mobile phone number",
		"fax_nr"       => "fax number",
		"email"        => "email address",
		"ssn"          => "SSN",
		"birthday"     => "birth date",
		"function"     => "job title",
		"cla"          => "classifications",
		"memo"         => "remarks",
	);

	/* __construct {{{ */
	/**
	 *
	 * Class constructor.
	 *
	 * The constructor will set a load of class variables.
	 */
	public function __construct() {
		require(self::include_dir."construct.php");
	}
	/* }}} */
	/* disableFulltextSearch {{{ */
	/**
	 * Disable the full text search routines
	 */
	public function disableFulltextSearch() {
		$this->enable_ft_search = 0;
	}
	/* }}} */
	/* alternativeSearch {{{ */
	/**
	 * Create sql syntax for the alternative to full text search
	 *
	 * @param array $fields The database fields to search in
	 * @param string $str The key to search for
	 *
	 * @return string The sql syntax for the search
	 */
	private function alternativeSearch($fields, $str) {
		foreach ($fields as $k=>$v) {
			$fields[$k] = sprintf(" `%s` like '%%%s%%' ", $v, $str);
		}
		return sprintf(" ( %s ) ", implode(" OR ", $fields));
	}
	/* }}} */
	/* sendHeaders {{{ */
	/**
	 * Function to send expire and cache control headers to the client
	 */
	private function sendHeaders() {
		/* send some headers */
		header("Expires: ".gmdate('D, d M Y H:i:s', mktime()-60*60)." GMT", true);
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', mktime())." GMT", true);

		header("Cache-Control: private", true);
		header("Pragma: private", true);

		//if ($_SESSION["user_id"] || $_SESSION["visitor_id"])
		header("Vary: *");
	}
	/* }}} */
	/* sendHeadersCache {{{ */
	/**
	 * Function to send headers to the client to make sure they will cache it
	 */
	private function sendHeadersCache() {
		/* send some headers */
		header("Expires: ".gmdate('D, d M Y H:i:s', mktime()+$this->rss_cache_timeout)." GMT", true);
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', mktime())." GMT", true);

		header("Cache-Control: private", true);
		header("Pragma: private", true);
	}
	/* }}} */
	/* generate_gallery_image {{{ */
	/**
	 * Create html output for a gallery image
	 *
	 * @param array $item The image properties like pageid, id, etc
	 * @param array $gallery The gallery properties like last_modified etc
	 *
	 * @return string The html code for this image
	 */
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
	/* }}} */
	/* disableLists {{{ */
	/**
	 * Disable or Enable creation of listings
	 *
	 * @param int $state 1 to disable lists, 0 to enable lists
	 */
	public function disableLists($state) {
		$this->disableLists = (int)$state;
	}
	/* }}} */
	/* disableMetadata {{{ */
	/**
	 * Disable or Enable metadata functionality
	 *
	 * @param int $state 1 to disable metadata, 0 to enable metadata
	 */
	public function disableMetadata($state) {
		$this->disableMeta = (int)$state;
	}
	/* }}} */
	/* checkDomainRedir {{{ */
	/**
	 * Check if we have to redir the visitor to another domain
	 * This is done when the visitor comes on a page with a domain that's not on top of the list
	 * of the current siteroots domain stack
	 *
	 * @return mixed true if we are visiting the loginimage url.
	 */
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
	/* }}} */
	/* publicLoginLink {{{ */
	/**
	 * Toggle state of public loginlink.
	 * This function is disabled, and will always set
	 * the state to 1.
	 * Dont use this function
	 */
	public function publicLoginLink($state) {
		/* this function is disabled */
		//$this->display_login = $state;
		$this->display_login = 1;
	}
	/* }}} */
	/* alternative_footer {{{ */
	/**
	 * Toggle state of the alternative footer
	 *
	 * @param int $state 1 for enable, 0 for disable
	 */
	public function alternative_footer($state) {
		$this->alternative_footer = $state;
	}
	/* }}} */
	/* addPublicRoot {{{ */
	/**
	 * Add a siteroot name to the list of public siteroots
	 *
	 * @param string $siteroot Siteroot name to add
	 */
	public function addPublicRoot($siteroot) {
		$this->public_roots[$siteroot]=$siteroot;
	}
	/* }}} */
	/* checkInternalRequest {{{ */
	/**
	 * Check if a given page is an internal request type
	 *
	 * @param int $page The pageid to check
	 *
	 * @return int 1 if this is an internal request, 0 if not
	 */
	private function checkInternalRequest($page=0) {
		if (!$page)
			$page = $this->pageid;

		if ( substr($page, 0, 2) == "__") {
			return 1;
		} else {
			return 0;
		}
	}
	/* }}} */
	/* init_aliaslist {{{ */
	/**
	 * Populate the page_aliases class variable
	 */
	public function init_aliaslist() {
		/* retrieve alias table */
		$ids_alias =& $this->page_aliases;
		$q = "select id, pageAlias from cms_data where pageAlias != ''";
		$res_alias = sql_query($q);
		while ($row_alias = sql_fetch_assoc($res_alias)) {
			$ids_alias[$row_alias["id"]]=$row_alias["pageAlias"];
		}
	}
	/* }}} */
	/* getTemplateById {{{ */
	/**
	 * Get a template and store it in a generated cachefile if it's a js or css template
	 *
	 * @param int $id The template to fetch
	 *
	 * @return array The template data
	 */
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
	/* }}} */
	/* start_html {{{ */
	/**
	 * start html output
	 *
	 * @param int $header if set, add a html header for text/nontext mode
	 * @param int $textmode if set, generate a page in txt mode.
	 */
	public function start_html($header=0, $textmode=0) {
		require(self::include_dir."start_html.php");
	}
	/* }}} */
	/* end_html {{{ */
	/**
	 * end html output
	 */
	public function end_html() {
		require(self::include_dir."end_html.php");
	}
	/* }}} */
	public function html_header($page=0, $textmode=0) {
		require(self::include_dir."html_header.php");
		echo $output->generate_output();
	}
	/* exec_inline {{{ */
	/**
	 * Executes a given php template inplace where it is called
	 *
	 * @param int $id The template id to execute
	 * @param int $return if set, return the outcome, if not set, eval the outcome. (defaults to eval)
	 *
	 * @return string The result of the template execution when the return argument is set
	 */
	public function exec_inline($id, $return=0) {
		$data = $this->getTemplateById($id);
		if ($data["category"] != "php" && $data["category"] != "main") {
			echo "script execution denied due wrong file type: ".$data["category"].$this->crlf;
		}
		#$prepare = str_replace($this->parser["IN"], $this->parser["OUT"], $data["data"]);
		$prepare = $data["data"];
		if ($return) {
			return $prepare;
		} else {
			eval("global \$template; ?>".$prepare);
		}
	}
	/* }}} */
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
	/* getPageThumb {{{ */
	/**
	 * Get a thumbnail of the first image on a page
	 *
	 * @param int $id The pageid to process
	 * @param int $width The width in pixels we want the thumbnail to be
	 * @param int $height The height in pixels we want the thumbnail to be
	 *
	 * @return mixed the html code to display this image on success, boolean false on failure
	 */
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

				if (!in_array(strtolower($file["ext"]), array("gif", "png", "jpg")))
					return "";

				$f = sprintf("%s/%s/%d.%s", $GLOBALS["covide"]->filesyspath, "bestanden",
					$img_id, $file["ext"]);

				$nf = $filesys_data->FS_calculatePath($f);

				if (file_exists($f.".gz")) {
					/* gunzip this file, we need it */
					exec(sprintf("gunzip %s", escapeshellarg($f.".gz")), $xret, $xretval);
				}
				if (file_exists($nf.".gz")) {
					/* gunzip this file, we need it */
					exec(sprintf("gunzip %s", escapeshellarg($nf.".gz")), $xret, $xretval);
				}

				/* get current information */
				if (file_exists($nf))
					$f = $nf;

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
	/* }}} */
	/* getPagesById {{{ */
	/**
	 * Get pages in an array
	 *
	 * @param array $ids Array with pageids to fetch
	 * @param string $order The fields and direction to sort the pages
	 *
	 * @return array page alias and page text in an array where the keys are the pageids
	 */
	public function getPagesById($ids, $order="datePublication desc") {
		if (!is_array($ids))
			return false;

		$data = array();
		$q = sprintf("select id, pageHeader, pageTitle, datePublication, pageData, isActive, isPublic from cms_data where id IN (%s)", "0".implode(",", $ids));
		if ($order)
			$q.= sprintf(" order by %s", $order);

		$p = ($this->page_less_rewrites) ? "/":"/page/";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = $p.$this->checkAlias($row["id"]);
			$row["pageText"] = trim(strip_tags($row["pageData"]));
			unset($row["pageData"]);

			$data[$row["id"]] = $row;
		}
		return $data;
	}
	/* }}} */
	/* getPagesByParent {{{ */
	/**
	 * Get a list of pages that are children of the given parent, optionally limited by menuitem status
	 *
	 * @param int $parent The parent page id
	 * @param string $order fields and direction to sort the result
	 * @param int $limit Only return this many items
	 * @param int $only_menuitems if set, only return pages that are marked as menuitem
	 * @param int $start use in combination with limit, this will start at the given record in the set
	 *
	 * @return array keys are the pageid, values are alias, pageText, pageHeader, pageTitle, datePublication, isActive and isPublic
	 */
	public function getPagesByParent($parent, $order="pageLabel, pageTitle", $limit=0, $only_menuitems=0, $start=0) {
		$data = array();
		if (!$only_menuitems)
			$condition = $this->base_condition;
		else
			$condition = $this->base_condition_menu;

		if ($limit) {
			$sql_limit = sprintf(" LIMIT %d, %d", $start, $limit);
		}
		$q = sprintf("select id, pageHeader, pageTitle, datePublication, pageData, isActive, isPublic, address_ids from cms_data where parentpage = %d AND %s order by %s %s",
			$parent, $condition, $order, $sql_limit);
		$res = sql_query($q);
		$p = ($this->page_less_rewrites) ? "/":"/page/";

		while ($row = sql_fetch_assoc($res)) {
			$row["alias"] = $p.$this->checkAlias($row["id"]);
			$row["pageText"] = trim(strip_tags($row["pageData"]));
			unset($row["pageData"]);

			$data[$row["id"]] = $row;
		}
		return $data;
	}
	/* }}} */
	/* getPagescountByParent {{{ */
	/**
	 * Get the number of child pages for a given page
	 *
	 * @param int $parent The parentpage
	 * @param int $only_menuitems if set, only count the number of children with the menuitem option set
	 *
	 * @return int The number of children
	 */
	public function getPagescountByParent($parent, $only_menuitems=0) {
		if (!$only_menuitems)
			$condition = $this->base_condition;
		else
			$condition = $this->base_condition_menu;

		$q = sprintf("SELECT COUNT(*) FROM cms_data WHERE parentpage = %d AND %s", $parent, $condition);
		$res = sql_query($q);
		$count = sql_result($res, 0);
		return $count;
	}
	/* }}} */
	/* getMainPage {{{ */
	/**
	 * Get the id of the 'main' template
	 *
	 * @return int the main template id
	 */
	public function getMainPage() {
		$q = "select id from cms_templates where category = 'main'";
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			die("error: main page not defined!");
		else
			return sql_result($res,0,"",2);
	}
	/* }}} */
	private function linkcheckerPageList() {
		if ($_REQUEST["page"]) {
			/* get _all_ cms pages including the protected ones */
			$q = sprintf("select pageRedirect, pageData, apEnabled from cms_data where id = %d", $_REQUEST["page"]);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			$redir = $row["pageRedirect"];
			if ($redir)
				echo sprintf("<a href='%s'>page redirect</a>".$this->crlf, $redir);

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
				echo sprintf("<a href='/mode/linkchecker&user=%2\$d&hash=%3\$s&page=%1\$d'>page id %1\$d</a>".$this->crlf,
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
		require(self::include_dir."handlePage.php");

		// A list of mobile devices
		$useragents = array (
			"Blazer", "Palm", "Handspring", "Nokia", "Kyocera", "Samsung", "Motorola",
			"Smartphone", "Windows CE", "Blackberry", "WAP", "SonyEricsson",
			"PlayStation Portable", "LG", "MMP", "OPWV", "Symbian", "EPOC"
		);
		$regex = sprintf("/((%s))/s", implode(")|(", $useragents));

		if (preg_match($regex, $_SERVER["HTTP_USER_AGENT"])
			&& $_REQUEST["mode"] != "text"
			&& !$_SESSION["text_redir"]) {
			$this->triggerError(302);

			$_SESSION["text_redir"] = 1;
			header("Location: /text/".$this->checkAlias($this->pageid));
			exit();
		}
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
		require(self::include_dir."getVisitorRestrictions.php");
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

			$uri = sprintf("https://%s%s%s",
				$this->http_host,
				($this->page_less_rewrites) ? "/":"/page/",
				$this->checkAlias($page));
			header( sprintf("Location: %s", $uri ));
			exit();
		} elseif (!$ssl && ($_SERVER["HTTPS"] == "on"|| $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")) {
			session_write_close();
			$this->triggerError(307);
			$uri = sprintf("http://%s%s%s",
				$this->http_host,
				($this->page_less_rewrites) ? "/":"/page",
				$this->checkAlias($page));
			header( sprintf("Location: %s", $uri ));
			exit();
		}
	}

	public function getRedirEndPoint($page) {
		require(self::include_dir."getRedirEndPoint.php");
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

			$f_ext = sprintf("tmp_cms/%s_%d.%s", $GLOBALS["covide"]->license["code"],
				$id, $type, filemtime($f_int));

			$output = new Layout_output();
			$f_new = $output->external_file_cache_handler($f_ext);

			return "/".$f_new;

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
				$browsers = "Opera";
				break;
		}
		if ($browsers && strstr($_SERVER["HTTP_USER_AGENT"], $browsers)) {
			$f = $this->getCmsTmpFile($id, "css");
			$output = new Layout_output();
			$output->addTag("link", array(
				"rel"  => "stylesheet",
				"type" => "text/css",
				"href" => $f
			));
			$output_code = new Layout_output();
			if (preg_match("/^msie/si", $browsers)) {
				$b = '';
				if (preg_match('/\d$/s', $browsers)) {
					$b = str_ireplace('msie', '', $browsers);
				}
				$output_code->addCode(sprintf("\t<!--[if IE%s]>", $b));
				$output_code->addCode(trim($output->generate_output()));
				$output_code->addCode("<![endif]-->");

			} else {
				$output_code->start_javascript(false);
					$output_code->addCode(sprintf("
	if (navigator.userAgent.indexOf('%s') != -1) document.write('%s');
					",
					$browsers,
					addslashes($output->generate_output())
					));
				$output_code->end_javascript(false);
			}

			$code = $output_code->generate_output();
			$code .= sprintf(" <!-- css include [%d] (%s) -->\n", $id, $browsers);
		} elseif (!$browsers) {
			$f = $this->getCmsTmpFile($id, "css");
			$output = new Layout_output();
			$output->addCode("\t");
			$output->addTag("link", array(
				"rel"  => "stylesheet",
				"type" => "text/css",
				"href" => $f
			));
			$code  = $output->generate_output();
			$code .= sprintf(" <!-- css include [%d] -->\n", $id);
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

		$f = $this->getCmsTmpFile($id, "js");
		$code = sprintf("\t<script type='text/javascript' src='%s'></script>", $f);
		$code.= "<!-- js include [$id] -->\n";

		if ($this->output_started) {
			echo $code;
		} else {
			$this->file_loader[] = $code;
		}
	}
	public function getPageTitle($id=0, $size=1, $return=0, $strip_hostnames = 1) {
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
				case "__login":
					$title = gettext("login");
					break;
				case "__loginprofile":
					$title = gettext("logged in");
					break;
				case "__err401":
					$title = gettext("authorization required");
					break;
			}
		} else {
			if (!$this->page_cache[$id]) {
				$this->page_cache[$id] = $this->cms->getPageById($id, "", $strip_hostnames);
			}
			$page  =& $this->page_cache[$id];
			$title =& $page["pageTitle"];

		}

		if ($return) {
			return $title;
		} elseif ($size == -1) {
			echo $title;
		} else {
			//echo sprintf("<H%d>%s</H%d>", $size, $title, $size);
			$output = new Layout_output();
			$output->insertTag(sprintf("H%d", $size), $title);
			echo $output->generate_output();
		}
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
		$buf = "";
		$key = sprintf("sitemap_%d_%d_%s", $startPage, $parse_level,
			($_REQUEST["mode"] == "sitemap_plain") ? "text":"html");

		$fetch = $this->getApcCache($key);
		if ($fetch) {
			echo $fetch;
		} else {

			if (!$parse_level)
				$parse_level = $this->sitemap_parse_level;

			if (!$startPage)
				$startPage = $this->default_page;

			$data = $this->getPagesById(array($startPage));

			if ($_REQUEST["mode"] != "sitemap_plain")
				$buf.= "<script type=\"text/javascript\">sitemap_tbl(1);</script>";

			$buf.= $this->generateSitemapItem($data[$startPage]);
			$buf.= $this->generateSitemapTree($startPage, 1, $parse_level);

			if ($_REQUEST["mode"] != "sitemap_plain")
				$buf.= "<script type=\"text/javascript\">sitemap_tbl(0);</script>";

			echo $buf;
			$this->setApcCache($key, $buf);
		}
	}
	private function generateSitemapTree($page, $level, $parse_level) {
		$buf = "";
		if ($level <= $parse_level) {
			$pages = $this->getPagesByParent($page);
			foreach ($pages as $v) {
				if ($v["isActive"] && $v["isPublic"]) {
					$buf.= $this->generateSitemapItem($v, $level);
					$buf.= $this->generateSitemapTree($v["id"], $level+1, $parse_level);
				}
			}
		}
		return $buf;
	}
	private function generateSitemapItem($v, $level=0) {
		$buf = "";

		if ($level > 0) {
			$class = "sitemap_item_sub";
		} else {
			$class = "sitemap_item_main";
		}
		if ($_REQUEST["mode"] == "sitemap_plain") {
			if ($v["id"] == $this->default_page) {
				$url = "/text/";
			} else {
				$url = sprintf("%s%s", "text/", $v["alias"]);
			}
		} else {
			if ($v["id"] == $this->default_page) {
				$url = "/";
			} else {
				$url = $v["alias"];
			}
		}
		$struct = 0;
		if ($this->sitemap_parse_level > $level) {
			$q = sprintf("select count(*) from cms_data where parentPage = %d and %s",
				$v["id"], $this->base_condition);
			$res = sql_query($q);
			if (sql_result($res,0) > 0)
				$struct = 1;
		}

		if ($_REQUEST["mode"] != "sitemap_plain") {
			$buf.= "<script type=\"text/javascript\">";
			$buf.= sprintf("sitemap_map(%d, %d);", $level, $struct);
			$buf.= "</script>\n";
		}
		$buf.= sprintf("<a href=\"%2\$s\" style=\"margin-left:%1\$dpx\">%3\$s</a>",
			(6 * $level), $url, $v["pageTitle"]);

		if ($_REQUEST["mode"] == "sitemap_plain")
			$buf.= $this->crlf."\n";

		return $buf;
	}

	private function cmsLoginVisitor($req) {

		$q = sprintf("select * from cms_users where username = '%s'", $req["username"]);
		$res = sql_query($q);

		if (sql_num_rows($res) == 1) {
			$row = sql_fetch_assoc($res);
			$id = $row["id"];

			$serverpw = md5($row["password"].$_SESSION["challenge"]);
			$clientpw = $_REQUEST["password"];

			if ($serverpw != $clientpw)
				$id = 0;

		} else {
			$id = 0;
		}
		/* if user registration is not confirmed, it could be not active/enabled */
		if (!$row["is_active"])
			$row["is_enabled"] = 0;

		if ($id && $row["is_enabled"]) {
			$_SESSION["visitor_id"] = $id;
			$this->_log_visitor_login($req);
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

	public function getPageData($id=0, $prefix="page", $no_inline_edit=0, $strip_hostnames = 1) {
		require(self::include_dir."getPageData.php");
	}

	private function shopContents() {
		require(self::include_dir."shopContents.php");
	}
	public function showLogin($notable = 0, $legend="", $pageid=0) {
		if ($_SESSION["user_id"] || $_SESSION["visitor_id"]
			|| in_array($this->pageid, array("__err401", "__login"))) {
			return true;
		} else {
			if (!$pageid) {
				$this->triggerLogin($this->pageid,0,0,1,$notable, $legend);
			} else {
				$this->triggerLogin((int)$pageid,0,0,1,$notable, $legend);
			}
			return false;
		}
	}
	public function showUsername() {
		if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
			$output2 = new Layout_output();
			if ($_SESSION["user_id"]) {
				$user_data = new User_data();
				$output2->addCode(gettext("manager").": ");
				$output2->addCode($user_data->getUserNameById($_SESSION["user_id"]));
			} else {
				$output2->addCode(gettext("user").": ");
				$output2->addCode($this->cms->getUserNameById($_SESSION["visitor_id"]));
			}
			echo $output2->generate_output();
			return true;
		} else {
			return false;
		}
	}
	private function triggerLogin($page=0, $return=0, $use_feedback=0, $compact=0, $notable=0, $legend="") {
		/* include */
		require(self::include_dir."triggerLogin.php");

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
					$_find = sprintf("/(%s)/six", $c["abbreviation"]);
					if (preg_match($_find, $data)) {
						$this->has_abbr[] = $k;
						$find[] = $_find;
						$repl[] = sprintf("<em class=\"tt_tooltip\" onmouseover=\"return escape(load_abbr(%d));\">$1</em>", $k);
					}
				}
			}
			$data = preg_replace($find, $repl, $data, 1);

			foreach ($matches[0] as $k=>$v) {
				$data = str_replace("##$k##", $v, $data);
			}
		}
	}
	private function loadAbbreviations() {
		if (count($this->has_abbr)) {
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
			$output->addCode("<!-- start abbreviations -->\n");

			/* load abbrieviations */
			$cms = $this->cms->getAbbreviations();
			foreach ($cms as $k=>$v) {
				if (in_array($lang, $v["lang"]) && in_array($k, $this->has_abbr)) {
					$output->addCode(sprintf('<span class="tt_abbr" id="tt_abbr_%d"><b>%s</b>: %s</span>', $k, addslashes($v["abbreviation"]), addslashes($v["description"])));
				}
			}
			$output->addCode("\n<!-- end abbreviations -->\n");
			echo $output->generate_output();
		}
	}

	private function metaShowResults($query) {
		require(self::include_dir."showMetaResults.php");
	}

	private function metaShowOptions() {
		require(self::include_dir."showMetaOptions.php");
	}
	private function addListShopLink($id) {
		$output = new Layout_output();
		$output->addTextField(sprintf("shopField_%d", $id), 1, array(
			"style" => "width: 20px;"
		));
		$output->insertAction("forward", gettext("add to shopping cart"),
			sprintf("javascript: shopAdd('%d', '%s');", $id, ""));

		$output->insertAction("shop", gettext("go to shopping cart"),
			"/mode/shopcontents");

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
						$months[date("m", $v)] = utf8_encode(strftime("%B", $v));
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
		require(self::include_dir."handleRewrites.php");
	}
	private function handleImages(&$str) {
		require(self::include_dir."handleImages.php");
	}

	public function preload_menu() {
		$output = new Layout_output();
		$output->addCode("\t");
		$output->load_javascript("menudata/libjs/layersmenu-browser_detection.js", 0);
		$output->addCode("\t");
		$output->load_javascript("menudata/libjs/layersmenu-library.js", 0);
		$output->addCode("\t");
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
		require(self::include_dir."generate_menu_loader.php");
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
		$crlf = $this->crlf;

		$data = preg_replace("/<((li)|(p))[^>]*?>/six", $crlf, $data);
		$data = preg_replace("/<br[^>]*?>/sxi", $crlf, $data);
		if ($basic_html)
			$data = strip_tags($data, "<br><b><u><i>");
		else
			$data = strip_tags($data, "<br>");

		$data = preg_replace("/(\n)|(\r)|(\t)/s", "", $data);

		return $data;
	}
	public function triggerError($code, $redir="") {
		require(self::include_dir."triggerError.php");
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
		require(self::include_dir."handleShop.php");
	}
	private function handlePoll(&$data, $id = 0) {
		$pageid =& $id;
		if (!$id) $id = $this->pageid;
		require(self::include_dir."handlePoll.php");
	}
	public function getPageFooter($pageid=0) {
		require(self::include_dir."getPageFooter.php");
	}

	public function textPage() {
		require(self::include_dir."textPage.php");
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
		header("Content-Type: text/plain", 1);

		$fetch = $this->getApcCache("robots");
		if ($fetch) {
			echo $fetch;
		} else {

			/* list root contents */
			$files = scandir(".");
			$files[]="include/";
			$files[]="text/";
			$files[]="page/classes/";
			$files[]="covide/";
			$files[]="menu/";
			$files[]="trace.php";

			// include search results with opensearch
			#$files[]="search/";
			#$files[]="find/";
			natcasesort($files);

			echo "User-agent: *\n";
			echo sprintf("Sitemap: http://%s/sitemap.xml\n", $this->http_host);
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
		require(self::include_dir."switchCustomModes.php");
	}

	public function xmlSearch() {
		header("Content-Type: application/xhtml+xml");

		$apckey = sprintf("xmlseach_%d", $this->siteroot);
		$fetch = $this->getApcCache($apckey);
		if ($fetch) {
			echo $fetch;
		} else {
			$xml_main = file_get_contents(self::include_dir."opensearch.xml");
			$search = array(
				"###title###",
				"###descr###",
				"###protocol###",
				"###website###"
			);
			$repl = array(
				$this->cms_license["cms_name"],
				$this->cms_license["search_descr"],
				$this->protocol,
				$this->http_host
			);
			$xml_main = str_replace($search, $repl, $xml_main);

			$this->setApcCache($apckey, $xml_main);
			echo $xml_main;
		}
		exit();

	}
	public function googleMaps() {
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
			"location"    => sprintf("http://%s%s%s",
				$this->http_host,
				($this->page_less_rewrites) ? "/":"/page/",
				$record["alias"]),
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
		require(self::include_dir."rssPage.php");
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
			"link"        => sprintf("%s%s%s%s",
				$this->protocol,
				$this->http_host,
				($this->page_less_rewrites) ? "/":"/page/",
				$this->checkAlias($record["id"]))
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
		$img = implode($this->crlf, $img[0]);

		$thumb = $this->getPageThumb($record["id"], 70, 70);
		$vars["description"] = preg_replace("/<br[^>]*?>/si", " ", $vars["description"]);
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
		require(self::include_dir."sendform.php");
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

	private function checkPageElements($data) {
		$rcel =& $this->page_elements;
		if (preg_match("/\%\%\%\d{1,}\%\%\%/s", $data)) {
			$el = preg_split("/(\%\%\%\d{1,}\%\%\%)/s", $data, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach ($el as $k=>$v) {
				if (preg_match("/\%\%\%\d{1,}\%\%\%/s", $v)) {
					$v = str_replace("%", "", $v);
					if (in_array($v, $rcel)) {
						$el[$k] = "<font color='red'><small>(error: recursion detected in page element)</small></font>".$this->crlf;
					} else {
						$rcel = array_merge($rcel, array($v));
						$pdata = $this->getPageById((int)$v);
 						$el[$k] = $this->checkPageElements($pdata["pageData"]);
					}
				}
			}
			$data = implode("\n", $el);
		}
		return $data;
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
				echo $this->crlf.$this->crlf;
				echo gettext("Malformed request URI. Check your link/url.");
				exit();
			}

		return $data;
	}

	private function metaInitResults() {
		$data  = $_REQUEST["data"];
		$avail = $_REQUEST["avail"];
		$order = $_REQUEST["order"];
		if (trim($order)) {
			$urlprefix = sprintf("order=%s&", trim($order));
		} else {
			$urlprefix = "";
		}

		/* unset not used values */
		foreach ($data as $k=>$v) {
			if ($avail[$k] != 1)
				unset($data[$k]);
		}
		$data = $this->queryEncodeMetaData($data);
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				document.location.href='/metadata/?%squery=%s'
			", $urlprefix, $data));
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function getMetadataByPage($id) {
		return $this->getMetadataById($id);
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
		if (!is_array($meta_criteria)) {
			return array();
		}

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
		require(self::include_dir."generateAddressList.php");
	}

	public function generateAddressRecords($addressid, $parent=0) {
		require(self::include_dir."generateAddressRecords.php");
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
				$repeat = gettext("day");
			}
			if ($row["date_begin"] != $row["date_end"]) {
				$buf .= sprintf("<li><b>%s %s %s %s</b>".$this->crlf."%s %s".$this->crlf."%s</li>",
					gettext("from"),
					date("d-m-Y", $row["date_begin"]),
					gettext("till"),
					date("d-m-Y", $row["date_end"]),
					gettext("every"),
					$repeat,
					$row["description"]
				);
			} else {
				$buf .= sprintf("<li><b>%s %s</b>".$this->crlf."%s</li>",
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
		//return true;
		require(self::include_dir."getBanners.php");
	}
	private function displayBanner($banner) {
		if ($banner["website_real"]) {
			return sprintf("<a href='%s' target='_new'><img src='%s' class='banner'></a>",
				$banner["website"], $banner["location"]);
		} else {
			return sprintf("<img src='%s' border='0'>", $banner["location"]);
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
		if ((int)$folderid != $folderid) {
			return false;
		}
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
				array_unshift($files, array_shift($files));
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
				parent.location.href = '%s%s';
			", ($this->page_less_rewrites) ? "/":"/page/", $this->checkAlias($data["system"]["pageid"])));
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
		#$file = $this->downloadFile("http://engine.letsstat.nl/dev/letsstat.js");
		$file = "http://engine.letsstat.nl/dev/letsstat.js";

		$output = new Layout_output();
		$output->load_javascript($file, 1);
		$output->start_javascript();
		$output->addCode(sprintf("
			var lst_title = \"%s\";
			var lst_url = \"http://%s%s%s.htm\";
			var lst_sid = \"%s\";
			lsttc();
		", addslashes($data["pageTitle"]), $this->http_host,
			($this->page_less_rewrites) ? "/":"/page/",
			($data["pageAlias"]) ? $data["pageAlias"]:$data["id"],
			addslashes($cms_siteroot["letsstat_analytics"])
		));
		$output->end_javascript();
		return $output->generate_output();
	}
	private function googleAnalytics($cms_siteroot) {
		$f = file_get_contents(self::include_dir."google_analytics.htm");
		$f = str_replace("###tracker###", addslashes($cms_siteroot["google_analytics"]), $f);
		return $f;
	}

	public function load_javascript($file) {
		if (!preg_match("/\.js$/si", $file)) {
			echo "wrong file type: ".$file;
		} else {
			$output = new Layout_output();
			$output->load_javascript($file, true);
			$output->addCode("\n");
			echo $output->generate_output();
		}
	}
	private function downloadFile($remote) {
		$temp = $GLOBALS["covide"]->temppath;
		$temp = preg_replace("/\/tmp\/$/s", "/tmp_cms/", $temp);
		$temp.= basename($remote);

		$time = mktime() - $keys_cache_timeout;
		if (!file_exists($temp) || filemtime($temp) < $time) {
			$data = @file_get_contents($remote);
			if ($data)
				file_put_contents($temp, $data);
		}
		if (file_exists($temp))
			return "/tmp_cms/".basename($remote);
		else
			return $remote;
	}
	public function updateLoginText($username, $password) {
		$this->login_text_username = $username;
		$this->login_text_password = $password;
	}

	public function cleanHtml($data) {
		return $data;
	}

	public function getRSSByCategory($category, $feedcount=2) {
		$rss_data = new Rss_data();
		$feed_ids = $rss_data->getFeedIDsByCategory($category);

		$rss_items = array();
		foreach($feed_ids as $feedid) {
			$tmpitems = $this->getRSSitems($feedid, $feedcount);
			if (is_array($tmpitems))
		   		$rss_items = array_merge($rss_items, $tmpitems);
	   	}

		$sorted = array();
		$sortarray = array();
		foreach($rss_items as $k=>$v) {
			$sortarray[$k] = $v["date"];
		}
		arsort($sortarray);
		foreach($sortarray as $k=>$v) {
			$sorted[] = $rss_items[$k];
		}
		return $sorted;
	}
	private function page2rewrite($page) {
		/* add .htm extension if none is found */
		if (!preg_match("/\.htm$/s", $page))
			$page .= ".htm";

		/* check if page_less_rewrites is active */
		if ($this->page_less_rewrites)
			return sprintf("/%s", $page);
		else
			return sprintf("/page/%s", $page);
	}
	public function displayPoll($pollid = -1, $hideresults = 0) {
		$cms_data =& $this->cms;
		$polldata = $cms_data->getPollById($pollid);
		if ($_COOKIE["pollvoted".$polldata["id"]] == "yes")
			return $this->showPollResults($polldata["id"], $hideresults);
		else
			return $this->showPoll($polldata["id"], $hideresults);
	}
	private function showPollResults($pollid, $hideresults = 0) {
		$cms_data =& $this->cms;
		$polldata = $cms_data->getPollById($pollid);
		$output = new Layout_output();
		$output->addTag("div", array("id" => "pollcontainer"));
		$output->addTag("p", array("id" => "pollquestion"));
			$output->addCode($polldata["question"]);
		$output->endTag("p");
		$output->addTag("p", array("id" => "pollanswers"));
		if (!$hideresults) {
			$table = new Layout_table();
			foreach($polldata["items"] as $k=>$v) {
				$table->addTableRow();
					$table->insertTableData($v["value"]." ");
					if ($polldata["totalvotes"]) {
						$perc = round(($v["votecount"]/$polldata["totalvotes"])*100);
					} else {
						$perc = 0;
					}
					$table->addTableData(array("width" => "100%"));
						$table->addSpace(1);
						$table->addTag("div", array("class" => "pollbar", "style" => "width: ".$perc."%;"));
						$table->addSpace(1);
						$table->endTag("div");
					$table->endTableData();
					$table->insertTableData($perc."%", array("align" => "right"));
				$table->endTableRow();
			}
			$table->endTable();
			$output->addCode($table->generate_output());
		}
		$output->endTag("p");
		$output->endTag("div");
		return $output->generate_output();
	}
	private function showPoll($pollid, $hideresults = 0) {
		$cms_data =& $this->cms;
		$polldata = $cms_data->getPollById($pollid);
		$output = new Layout_output();
		$output->addTag("div", array("id" => "pollcontainer"));
		$output->addTag("p", array("id" => "pollquestion"));
			$output->addCode($polldata["question"]);
		$output->endTag("p");
		$output->addTag("p", array("id" => "pollanswers"));
			$output->addTag("form", array(
				"method" => "post",
				"action" => "site.php",
				"target"  => "pollhandler",
				"id" => "pollform"
			));
			$output->addHiddenField("mode", "poll");
			$output->addHiddenField("pollid", $polldata["id"]);
			$output->addHiddenField("hideresults", $hideresults);
			foreach($polldata["items"] as $k=>$v) {
				$output->addTag("input", array(
					"type" => "radio",
					"class" => "pollanswer",
					"name" => "pollanswer",
					"value" => $k
				));
				$output->addSpace(1);
				$output->addCode($v["value"]);
				$output->addTag("br");
			}
			$output->endTag("form");
		$output->endTag("p");
		$output->addTag("p", array("id" => "pollactions"));
			$output->insertLink(gettext("vote"), array("href" => "javascript: document.getElementById('pollform').submit();"));
		$output->endTag("p");
		$output->endTag("div");
		$output->addTag("iframe", array(
			"id"          => "pollhandler",
			"name"        => "pollhandler",
			"src"         => "blank.htm",
			"width"       => "0",
			"frameborder" => 0,
			"border"      => 0,
			"height"      => "0",
			"visiblity"   => "hidden"
		));
		$output->endTag("iframe");
		return $output->generate_output();
	}
	private function sendpoll($req) {
		require(self::include_dir."sendpoll.php");
	}
	/* function to (readonly) access the private cms license info */
	public function getCmsLicense() {
		return $this->cms_license;
	}

	public function getFilesByFolder($folderid, $num=1, $image_only=1) {
		if ((int)$folderid != $folderid) {
			return false;
		}
		/* create filesys object */
		if (!$this->filesys)
			$this->filesys = new Filesys_data();
		$files = $this->filesys->getFiles(array("folderid" => $folderid, "no_cms_scan" => 1));

		foreach ($files as $file) {
			if ($image_only && $file["subtype"] == 'image') {
				$pages[] = array(
					"id"   => $file["id"],
					"src"  => sprintf("/cmsfile/%d", $file["id"]),
					"name" => $file["name"],
					"description" => $file["description"]
				);
			}
			if (!$image_only) {
				$pages[] = array(
					"id"   => $file["id"],
					"src"  => sprintf("/cmsfile/%d", $file["id"]),
					"name" => $file["name"],
					"description" => $file["description"]
				);
			}
		}
		return array_slice($pages, 0, $num);
	}

	/* loadJS {{{ */
	/**
	 * Loads a javascript file (or queus them into the buffer if no output is sent yet).
	 */
	private function loadJS($file, $include_dir = "") {
		$output = new Layout_output();
		if ($include_dir) {
			$output->load_javascript($include_dir.$file);
		} else {
			$output->load_javascript(self::include_dir.$file);
		}
		$code = $output->generate_output();
		if ($this->output_started)
			echo $code;
		else
			$this->file_loader[] = $code;
	}
	/* }}} */
	/* loadJQuery {{{ */
	/**
	 * Loads the jquery javascript library.
	 * This function does nothing anymore because we have jquery loaded by default now
	 */
	public function loadJQuery() {
		return true;
	}
	/* }}} */
	/* loadMooTools {{{ */
	/**
	 * Loads the mootools javascript library
	 */
	public function loadMooTools() {
		$this->loadJS("mootools-1.2-core.js");
	}
	/* }}} */
	/* _log_visitor_login {{{ */
	/**
	 * Create a logrecord for a successful cms visitor login
	 *
	 * @param array $data The username and password provided by the user
	 *
	 * @return void
	 */
	private function _log_visitor_login($data) {
		$sql = sprintf(
			"INSERT INTO cms_login_log VALUES (%d, '%s', '%s', %d, '%s')",
			$_SESSION["visitor_id"], $data["username"], $data["uri"], mktime(), $GLOBALS["covide"]->user_ip
		);
		$res = sql_query($sql);
	}
	/* }}} */
	/* setDocType {{{ */
	/**
	 * Sets a new doctype for the specified template
	 *
	 * @author Stephan van de Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0, 19-08-2009
	 * @param boolean xhtml use xhtml or not (html)
	 * @param version float the (x)html version to use
	 * @param string type the subtype to use, if none is specified, the old covide behavior is activated for compatibility
	 */
	public function setDocType($xhtml = false, $version = 4.01, $type = '') {
		if ($xhtml) {
			$GLOBALS["covide"]->output_xhtml = true;
		}
		if ($type && !in_array($type, array('strict', 'transitional', 'frameset', 'loose'))) {
			echo 'Type must be one of [strict|transitional|frameset]';
			$type = '';
		}
		$this->doctype = array(
			'xhtml' => $xhtml,
			'version' => $version,
			'type' => $type
		);
	}
	/* }}} */
	/* parseDocType {{{ */
	/**
	 * Parses a doctype with the current doctype parameters
	 *
	 * @author Stephan van de Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0, 19-08-2009
	 * @return string the parsed doctype
	 */
	private function parseDocType() {
		switch ($this->doctype['xhtml']) {
			case true:
				// xhtml
				switch ($this->doctype['version']) {
					case 1.1:
						$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
							"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
						break;
					default:
						$doctype = sprintf('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 %1$s//EN"
							"http://www.w3.org/TR/xhtml1/DTD/xhtml1-%1$s.dtd">',
							$this->doctype['type']);
						break;
				}
				break;
			case false:
				// html
				switch ($this->doctype['type']) {
					case 'strict':
						$doctype = sprintf('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML %s//EN"
							"http://www.w3.org/TR/html4/strict.dtd">',
							$this->doctype['version']);
						break;
					case 'frameset':
						$doctype = sprintf('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML %s Frameset//EN"
							"http://www.w3.org/TR/html4/frameset.dtd">',
							$this->doctype['version']);
						break;
					case 'loose':
						$doctype = sprintf('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML %s Transitional//EN"
							"http://www.w3.org/TR/html4/loose.dtd">',
							$this->doctype['version']);
						break;
					default:
						$doctype = sprintf('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML %s Transitional//EN">',
							$this->doctype['version']);
						break;
				}
				break;
		}
		$doctype = str_replace("\t", "", $doctype);
		$doctype = str_replace("\n", "\t\n", $doctype);
		return $doctype."\n";
	}
	/* }}} */
	/* addArrayOption {{{ */
	/**
	 * Adds an array item to template->vars, this is because smarty cannot do this
	 *
	 * @author Stephan van de Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0, 22-08-2009
	 * @param string var the $template->vars[$var]
	 * @param string key the key of the array item
	 * @param string value the item value
	 */
	public function addArrayOption($var, $key, $value) {
		if (!is_array($this->vars[$var])) {
			$this->vars[$var] = array();
		}
		$this->vars[$var][$key] = $value;
	}
	/* }}} */
}
?>
