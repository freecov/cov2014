<?php

Class Cms_data {
	/* constants */
	const include_dir      = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name       = "cms";

	public $opts = array();
	public $lang = array(
		"nl" => "dutch",
		"en" => "english",
		"de" => "german",
		"da" => "dansk",
		"es" => "espanol",
		"fr" => "francais",
		"it" => "italiano",
		"pl" => "polski",
		"pt" => "portugal",
		"fi" => "suomi",
		"sv" => "svenska"
	);
	public $weekdays = array();
	public $modules = array();
	public $cms_xs_levels = array();
	public $include_category = array(
		"html" => "html pagina (html)",
		"js"   => "javascript (js)",
		"css"  => "style sheet (css)",
		"php"  => "scripting (php)",
		"main" => "main template (main)"
	);
	public $meta_field_types;
	public $default_page = 0;

	/* {{{ function __construct() */
	public function __construct() {
		/* declare default field types */
		$this->weekdays = array(
			"zo" => gettext("zondag"),
			"ma" => gettext("maandag"),
			"di" => gettext("dinsdag"),
			"wo" => gettext("woensdag"),
			"do" => gettext("donderdag"),
			"vr" => gettext("vrijdag"),
			"za" => gettext("zaterdag"),
		);

		$this->modules = array(
			"cms_meta"         => gettext("metadata"),
			"cms_date"         => gettext("datum opties"),
			"cms_forms"        => gettext("formulieren"),
			"cms_list"         => gettext("lijstopties"),
			"cms_linkchecker"  => gettext("linkchecker"),
			"cms_changelist"   => gettext("wijzigingen overzicht"),
			"cms_banners"      => gettext("banners"),
			"cms_searchengine" => gettext("zoekmachine"),
			"cms_gallery"      => gettext("fotoboek"),
			"cms_versioncontrol" => gettext("versiecontrole"),
			"cms_page_elements"  => gettext("pagina elementen"),
			"multiple_sitemaps"  => gettext("meerdere sitemaps"),
			"cms_permissions"    => gettext("afgeschermde items")
		);

		$this->meta_field_types = array(
			"text"     => gettext("text veld"),
			"textarea" => gettext("text area"),
			"select"   => gettext("select box"),
			"checkbox" => gettext("check box")
		);

		$this->cms_xs_levels = array(
			0 => gettext("geen toegang"),
			1 => gettext("cms gebruiker"),
			2 => gettext("cms manager"),
			3 => gettext("cms admin")
		);
		$this->default_page = 152;
	}

	/* }}} */
	public function decodeOptions($req) {
		$q = "select * from cms_siteviews where user_id = ".$_SESSION["user_id"];
		$res = sql_query($q);
		if (sql_num_rows($res)==0) {
			$q = "insert into cms_siteviews (user_id) values (".$_SESSION["user_id"].")";
			sql_query($q);
			$opts = array();
		} else {
			$row = sql_fetch_assoc($res);
			$opts = unserialize($row["view"]);
		}
		$opts["pids"]  = $this->getAllParentPages();
		$this->opts = $opts;
		unset($opts);

		switch ($req["cmd"]) {
			case "search":
				$this->searchPage($req["cms"]["search"]);
				break;
			case "expand":
				$this->expandPage($req["id"]);
				break;
			case "expandAll":
				$this->expandPage(-1);
				break;
			case "collapse":
				$this->collapsePage($req["id"]);
				break;
			case "collapseAll":
				$this->collapsePage(-1);
				break;
		}
		if ($req["cmd"] != "search" && $req["cms"]["search"]) {
			$this->highlightSearch($req["cms"]["search"]);
		}
	}

	private function highlightSearch($str) {
		$sids  =& $this->opts["sids"];
		$sids  =  array();

		$q = sprintf("select id, parentPage from cms_data where (pageTitle like '%%%1\$s%%'
			OR pageLabel like '%%%1\$s%%' OR pageData like '%%%1\$s%%' OR pageAlias like '%%%1\$s%%'
			OR id = %2\$d)", $str, (int)$str);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["parentPage"]>0) {
				$sids[] = $row["id"];
			}
		}
	}
	public function searchPageXML($id) {
		$q = sprintf("select count(*) from cms_data where id = %d", $id);
		$res = sql_query($q);
		if (sql_result($res,0)==1) {
			echo "1";
		} else {
			echo "0";
		}
		exit();
	}

	private function searchPage($str) {
		$this->collapsePage(-1);

		$sids  =& $this->opts["sids"];
		$sids  =  array();

		$spids =& $this->opts["spids"];
		$spids =  array();

		$q = sprintf("select id, parentPage from cms_data where (pageTitle like '%%%1\$s%%'
			OR pageLabel like '%%%1\$s%%' OR pageData like '%%%1\$s%%' OR pageAlias like '%%%1\$s%%'
			OR id = %2\$d)", $str, (int)$str);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["parentPage"]>0) {
				$this->fillParents($row["parentPage"]);
				$sids[] = $row["id"];
			}
		}
		$this->opts["toonpages"] = array_unique($spids);
	}

	private function fillParents($id) {
		$spids =& $this->opts["spids"];
		$id = (int)$id;
		$q = "select id, parentPage from cms_data where id = $id group by parentpage";
		$res = mysql_query($q) or die($q.mysql_error());
		while ($row = mysql_fetch_array(	$res)) {
			$spids[]=$row["id"];
			$this->fillParents($row["parentPage"]);
		}
	}

	public function saveOptions() {
		$opts = $this->opts;
		unset($opts["sids"]);
		unset($opts["pids"]);
		unset($opts["spids"]);
		$view = serialize($opts);

		$q = sprintf("update cms_siteviews set view = '%s' where user_id = %d",
			$view, $_SESSION["user_id"]);
		sql_query($q);
	}

	private function expandPage($id) {
		if ($id > 0) {
			$this->opts["toonpages"][] = (int)$id;
			$this->opts["toonpages"] = array_unique($this->opts["toonpages"]);
		} elseif ($id == -1) {
			$q = "select parentPage from cms_data group by parentPage";
			$res = sql_query($q);
			while ($row = sql_fetch_array($res)) {
				$this->opts["toonpages"][] = (int)$row["parentPage"];
			}
			$this->opts["toonpages"] = array_unique($this->opts["toonpages"]);
		}
	}

	private function collapsePage($id) {
		if ($id > 0) {
			unset($this->opts["toonpages"][array_search($id, $this->opts["toonpages"])]);
			$this->opts["toonpages"] = array_unique($this->opts["toonpages"]);
		} else {
			$this->opts["toonpages"] = array();
		}
	}

	private function getAllParentPages() {
		$pids = array(0);
		// get all ids that have a parentPage
		$q = "select parentPage from cms_data order by id";
		$res = mysql_query($q);
		while ($row = mysql_fetch_array($res)) {
			$pids[]=$row["parentPage"];
		}
		$pids = array_unique($pids);
		return $pids;
	}

	public function getUserSitemapRoots() {
		$data = array();
		$q = "select id, pageTitle from cms_data where (isSpecial = '' OR isSpecial IS NULL) and parentPage = 0 order by pageTitle";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["id"]] = $row["pageTitle"];
		}
		return $data;
	}

	public function getUserPermissions($pageid, $uid) {
		$arr = $this->getPermissions($pageid);
		return $arr[$uid];
	}

	public function getPermissions($pageid) {
		//retrieve all permissions for this page
		$ok = $this->checkPagePermissions($pageid);
		//search for pageid
		while ($ok == 0) {
			$pageid = $this->getParent($pageid);
			$ok = $this->checkPagePermissions($pageid);
		}
		//return permissions
		return $this->getPagePermissions($pageid);
	}

	public function checkPagePermissions($pageid) {
		if ($pageid == 0) {
			return true;
		} else {
			$q = "select count(*) from cms_permissions where pid = $pageid";
			$res = sql_query($q) or die($q);
			if (sql_result($res,0)>0) {
				return true;
			} else {
				return false;
			}
		}
	}

	function getParent($pageid) {
		if (!$pageid) die ("recursion error!");
		$q = "select parentPage from cms_data where id = ".(int)$pageid;
		$res = sql_query($q) or die($q);
		if (sql_num_rows($res)>0) {
			return sql_result($res,0);
		}
	}

	function getPagePermissions($pageid) {
		$arr = array();
		$q = "select id from users order by username";
		$res = mysql_query($q) or die($q);
		while ($row = mysql_fetch_array($res)) {

			if ($pageid) {
				$q = "select * from cms_permissions where uid = ".$row["id"]." and pid = ".$pageid;
				$res2 = mysql_query($q) or die($q);
				$row2 = mysql_fetch_array($res2);
			}
			$arr[$row["id"]]["viewRight"] = (int)$row2["viewRight"];
			$arr[$row["id"]]["editRight"] = (int)$row2["editRight"];
			$arr[$row["id"]]["deleteRight"] = (int)$row2["deleteRight"];
			$arr[$row["id"]]["manageRight"] = (int)$row2["manageRight"];

		}
		return ($arr);
	}

	public function getTemplates() {
		$data = array(
			0 => gettext("geen template")
		);
		$q = "select id, pageTitle from cms_data where isTemplate = 1 order by pageTitle";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["id"]] = "- ".$row["pageTitle"];
		}
		return $data;
	}

	public function getPageById($id=0, $parentPage=0, $no_hostname=0) {
		if ($id) {
			$q = sprintf("select * from cms_data where id = %d", $id);
			$res = sql_query($q);
			$data = sql_fetch_assoc($res);

			$popup_data = explode("|", $data["popup_data"]);
			$data["popup_height"]  = $popup_data[0];
			$data["popup_width"]   = $popup_data[1];
			$data["popup_hidenav"] = $popup_data[2];

			$data["search_language"] = explode(",", $data["search_language"]);

			$settings = $this->getCmsSettings();

			$hosts = explode("\n", str_replace("\r", "", $settings["cms_hostnames"]));
			$hosts = array_merge($hosts, array($_SERVER["HTTP_HOST"], $_SERVER["SERVER_ADDR"]));
			$hosts = array_unique($hosts);

			foreach ($hosts as $host) {
				if ($host && !$match) {
					$regex  = "/\"http(s){0,1}:\/\/".$host."\//si";
					if ($no_hostname) {
						$target = "\"/";
					} else {
						$target = "\"".$GLOBALS["covide"]->webroot;
					}
					$data["pageData"]      = preg_replace($regex, $target, $data["pageData"]);
					$data["autosave_data"] = preg_replace($regex, $target, $data["autosave_data"]);
				}
			}

		} else {
			$data = array(
				"timestamp"  => mktime(),
				"parentPage" => $parentPage
			);
		}
		$conversion = new Layout_conversion();
		$data["pageData"]      = ($data["pageData"]);
		$data["autosave_data"] = ($data["autosave_data"]);

		return $data;
	}

	public function insertPage($req) {
		$cms =& $req["cms"];
		$date = mktime($cms["timestamp_hour"], $cms["timestamp_min"], 0,
			$cms["timestamp_month"], $cms["timestamp_day"], $cms["timestamp_year"]);


		if (!$cms["id"]) {
			$q = sprintf("insert into cms_data (parentPage, pageData, pageTitle,
				pageLabel, pageAlias, isActive, isMenuitem, isPublic, datePublication) values (
				%d, '%s', '%s', '%s', '%s', %d, %d, %d, %d)",
				$cms["parentPage"], $data, $cms["pageTitle"], $cms["pageLabel"], $cms["pageAlias"],
				$cms["isActive"], $cms["isMenuitem"], $cms["isPublic"], $date);
			sql_query($q);
			$cms["id"] = sql_insert_id("cms_data");
		}
		echo "<script>parent.location.href='?mod=cms&action=editpage&id=".$cms["id"]."&parentpage=".$cms["parentPage"]."';</script>";
		exit();
	}

	public function saveRestorePoint($req) {
		$info = $_SESSION["user_id"]."|".mktime();
		$q = sprintf("update cms_data set autosave_info = '%s', autosave_data = '%s' where id = %d",
			$info, $_REQUEST["contents"], $_REQUEST["cms"]["id"]);
		sql_query($q);
	}

	public function truncateRestorePoint($id, $close_window=0) {
		$q = sprintf("update cms_data set autosave_info = '', autosave_data = '' where id = %d", $id);
		sql_query($q);
		if ($close_window) {
			echo "window.close();";
			exit();
		}
	}

	public function savePageData($id, $skip_close=0, $req="") {
		if (!$id)
			$id = $req["cms"]["id"];

		if ($req) {
			$this->saveRestorePoint($req);
		}
		$q = sprintf("update cms_data set pageData = autosave_data where id = %d", $id);
		sql_query($q);

		$ts = mktime($req["cms"]["timestamp_hour"], $req["cms"]["timestamp_min"], 0,
			$req["cms"]["timestamp_month"], $req["cms"]["timestamp_day"], $req["cms"]["timestamp_day"]);

		$q = sprintf("update cms_data set autosave_data = '', autosave_info = '',
			pageTitle = '%s', pageAlias = '%s', pageLabel = '%s', datePublication = %d where id = %d",
			$req["cms"]["pageTitle"], $req["cms"]["pageAlias"], $req["cms"]["pageLabel"], $ts, $id);
		sql_query($q);

		if (!$skip_close) {
			echo "<script>
				var cf = parent.gettext('De pagina is opgeslagen. Wilt u dit venster sluiten?');
				if (confirm(cf)==true) {
					setTimeout('parent.window.close();', 200);
				}
			</script>";
			exit();
		}
	}
	public function loadRestorePoint($id) {
		$this->savePageData($id, 1);
		echo "document.location.href='?mod=cms&action=editpage&id=".$id."';";
		exit();
	}

	public function gotoFilesys($hidenav=0, $ftype) {
		$filesys_data = new Filesys_data();
		$id = $filesys_data->getCmsFolder();
		if ($hidenav) {
			header("Location: index.php?mod=filesys&action=opendir&subaction=$ftype&id=".$id);
		} else {
			header("Location: index.php?mod=filesys&action=opendir&id=".$id);
		}
		exit();
	}

	public function savePageSettings($req) {
		$cms =& $req["cms"];

		$fields["keywords"]            = array("s", $cms["keywords"]);
		$fields["pageRedirect"]        = array("s", $cms["pageRedirect"]);
		$fields["pageRedirectPopup"]   = array("d", $cms["pageRedirectPopup"]);

		if ($fields["pageRedirectPopup"]) {
				$popup_data = array();
				$popup_data[] = $cms["popup_height"];
				$popup_data[] = $cms["popup_width"];
				$popup_data[] = $cms["popup_hidenav"];
				$fields["popup_data"] = array("s", implode("|",$popup_data));
		} else {
			$fields["popup_data"]        = array("s", "");
		}
		$fields["isActive"]            = array("d", $cms["isActive"]);
		$fields["isPublic"]            = array("d", $cms["isPublic"]);
		$fields["search_override"]     = array("d", $cms["search_override"]);
		$fields["search_fields"]       = array("s", $cms["search_fields"]);
		$fields["search_descr"]        = array("s", $cms["search_descr"]);
		$fields["search_title"]        = array("s", $cms["search_title"]);

		if (!is_array($cms["search_language"])) $cms["search_language"] = array();
		$fields["search_language"]     = array("s", implode(",", $cms["search_language"]));

		$fields["google_changefreq"]   = array("s", $cms["google_changefreq"]);
		$fields["google_priority"]     = array("s", $cms["google_priority"]);
		$fields["isMenuItem"]          = array("d", $cms["isMenuItem"]);
		$fields["isTemplate"]          = array("d", $cms["isTemplate"]);
		$fields["isSticky"]            = array("d", $cms["isSticky"]);

		$fields["isGallery"] = array("d",0);
		$fields["isForm"]    = array("d",0);
		$fields["isList"]    = array("d",0);
		switch ($cms["module"]) {
			case "form":
				$fields["isForm"]    = array("d",1);
				break;
			case "list":
				$fields["isList"]    = array("d",1);
				break;
			case "gallery":
				$fields["isGallery"] = array("d",1);
				break;
		}

		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
			  //addslashes already done
				$vals[$k]="'".$v[1]."'";
			} else {
				$vals[$k]=(int)$v[1];
			}
		}
		$q = "update cms_data set date_changed = ".mktime();
		foreach ($vals as $k=>$v) {
			$q.= sprintf(", %s = %s ", $k, $v);
		}
		$q.= sprintf(" where id = %d", $req["id"]);
		sql_query($q);
		echo "<script>location.href='?mod=cms&action=editSettings&id=".$req["id"]."';</script>";
		exit();
	}

	public function checkAlias($exclude, $alias) {
		$output = new Layout_output();
		$q = sprintf("select id from cms_data where id != %d and pageAlias = '%s'", $exclude, $alias);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0 || !$alias) {
			$output->addCode("1|");
			$output->insertAction("ok", gettext("alias is ok"), "");
		} else {
			$id = sql_result($res,0);
			$output->addCode("0|");
			$output->insertAction("cancel", gettext("alias is al in gebruik op pagina")." ".$id, "");
		}
		$output->exit_buffer();
	}

	public function checkUsername($exclude, $username) {
		$output = new Layout_output();
		$q = sprintf("select id from cms_users where id != %d and username = '%s'", $exclude, $username);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0 && strlen($username) > 2) {
			$output->addCode("1|");
			$output->insertAction("ok", gettext("gebruikersnaam is ok"), "");
		} else {
			$output->addCode("0|");
			$output->insertAction("cancel", gettext("gebruikersnaam bestaat al of is ongeldig"), "");
		}
		$output->exit_buffer();
	}

	public function getCmsSettings() {
		$q = "select * from cms_license";
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}
	public function saveCmsSettings($req) {
		$q = sprintf("update cms_license set google_verify = '%s', cms_hostnames = '%s', cms_defaultpage = %d", $req["cms"]["google_verify"], $req["cms"]["cms_hostnames"], $req["cms"]["cms_defaultpage"]);
		foreach ($this->modules as $k=>$v) {
			$q.= sprintf(", %s = %d", $k, $req["cms"][$k]);
		}
		sql_query($q);
		echo "
			<script>
				if (opener.cmsReload) {
					opener.cmsReload();
				}
				setTimeout('window.close();', 200);
			</script>
		";
	}
	public function getAccountList($id=0) {
		$data = array();
		if ($id) {
			$q = sprintf("select * from cms_users where id = %d", $id);
		} else {
			$q = "select * from cms_users order by username";
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["is_enabled"]) {
				$row["is_enabled_h"] = gettext("ja");
			} else {
				$row["is_enabled_h"] = gettext("nee");
			}
			$data[] = $row;
		}
		return $data;
	}
	public function saveAccount($req) {
		if ($req["id"]) {
			$q = sprintf("update cms_users set username = '%s', password = '%s', is_enabled = %d where id = %d",
				$req["cms"]["username"], $req["cms"]["password"], $req["cms"]["is_enabled"], $req["id"]);
			sql_query($q);
		} else {
			$q = sprintf("insert into cms_users (username, password, is_enabled) values ('%s', '%s', %d)",
				$req["cms"]["username"], $req["cms"]["password"], $req["cms"]["is_enabled"]);
			sql_query($q);
		}
	}
	public function deleteAccount($id) {
		$q = sprintf("delete from cms_users where id = %d", $id);
		sql_query($q);
	}

	public function getCmsFile($id) {
		$filesys_data = new Filesys_data();
		$file = $filesys_data->getFileById($id, 1);
	  if (!$file["timestamp"])
	  	$file["timestamp"] = mktime(0,0,0,1,1,date("Y")-1);

		if ($file["id"]) {
			$hp = $filesys_data->getHighestParent($file["folder_id"]);
			if ($hp["name"] == "cms") {
				/* Checking if the client is validating his cache and if it is current. */
				if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $file["timestamp"])) {
					/* Client's cache IS current, so we just respond '304 Not Modified'. */
					header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file["timestamp"]).' GMT', true, 304);
					header('Connection: close');
				} else {
					Header("Expires: ".gmdate("D, j M Y H:i:s", mktime()+(24*60*60))." GMT");
					header('Last-Modified: '.gmdate('D, d M Y H:i:s', mktime()-(24*60*60)).' GMT', true, 200);
					header("Pragma: public");
					$filesys_data->file_download($id, 1);
				}
			}
		}
		exit();
	}

	public function saveAuthorisations($req) {
		/* revoke all permissions for this page */
		$q = sprintf("delete from cms_permissions where pid = %d", $req["id"]);
		sql_query($q);

		foreach ($req["auth"] as $uid=>$val) {
			$xs = array();
			switch ($val) {
				/* NO break */
				case "F": $xs["manage"] = 1;
				case "W": $xs["delete"] = 1;
				case "U": $xs["edit"] = 1;
				case "R": $xs["view"] = 1;
			}
			$q = sprintf("insert into cms_permissions (uid, pid, viewRight, editRight, deleteRight, manageRight)
				VALUES ('%s', %d, %d, %d, %d, %d)", $uid, $req["id"], $xs["view"], $xs["edit"], $xs["delete"], $xs["manage"]);
			sql_query($q);
		}
	}
	public function getAuthorisations($id) {
		$data = array();
		$q = sprintf("select * from cms_permissions where pid = %d", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["manageRight"]) {
				$xs = "F";
			} elseif ($row["deleteRight"]) {
				$xs = "W";
			} elseif ($row["editRight"]) {
				$xs = "U";
			} elseif ($row["viewRight"]) {
				$xs = "R";
			} else {
				$xs = "D";
			}
			$data[$row["uid"]] = $xs;
		}
		return $data;
	}
	public function getSiteTemplates($id=0) {
		$data = array();
		if ($id) {
			$q = sprintf("select * from cms_templates where id = %d", $id);
		} else {
			$q = sprintf("select * from cms_templates order by id");
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			switch ($row["category"]) {
				case "main":
					$row["category_h"] = "important";
					break;
				case "php":
					$row["category_h"] = "view_all";
					break;
				case "html":
					$row["category_h"] = "ftype_html";
					break;
				case "css":
					$row["category_h"] = "view_tree";
					break;
				case "js":
					$row["category_h"] = "view_new";
					break;
			}
			$data[$row["id"]] = $row;
		}
		return $data;
	}
	public function saveTemplate($req) {
		if ($req["cms"]["category"] == "main") {
			/* allow only one main page */
			$q = "update cms_templates set category = 'php' where category = 'main'";
			sql_query($q);
		}
		if ($req["id"]) {
			$q = sprintf("update cms_templates set category = '%s', title = '%s', data = '%s' where id = %d",
				$req["cms"]["category"], $req["cms"]["title"], $req["cms"]["data"], $req["id"]);
			sql_query($q);
			return $req["id"];
		} else {
			$q = sprintf("insert into cms_templates (category, title, data) values ('%s', '%s', '%s')",
				$req["cms"]["category"], $req["cms"]["title"], $req["cms"]["data"]);
			sql_query($q);
			return sql_insert_id("cms_templates");
		}
	}
	public function getTemplateById($id) {
		if ($id) {
			$q = sprintf("select * from cms_templates where id = %d", $id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
		} else {
			$row = array();
		}
		return $row;
	}

	public function getPath($pageid, $path=array(), $sub=0) {
		if ($sub==0) {
			$path[] = $pageid;
		}
		if ($pageid > 0) {
			$q = "select parentpage from cms_data where id = ".(int)$pageid;
			$res = mysql_query($q) or die($q);
			if (mysql_num_rows($res)>0) {
				$ret = mysql_result($res,0);
				$path[] = $ret;
				$this->getPath($ret, &$path, 1);
			}
		}
		return $path;
	}
	public function saveDateOptions($req) {
		if ($req["cms"]["s_timestamp_enable"]) {
			$ts_begin = mktime(
				$req["cms"]["s_timestamp_hour"],
				$req["cms"]["s_timestamp_min"],
				0,
				$req["cms"]["s_timestamp_month"],
				$req["cms"]["s_timestamp_day"],
				$req["cms"]["s_timestamp_year"]
			);
		} else {
			$ts_begin = 0;
		}
		if ($req["cms"]["e_timestamp_enable"]) {
			$ts_end = mktime(
				$req["cms"]["e_timestamp_hour"],
				$req["cms"]["e_timestamp_min"],
				0,
				$req["cms"]["e_timestamp_month"],
				$req["cms"]["e_timestamp_day"],
				$req["cms"]["e_timestamp_year"]
			);
		} else {
			$ts_end = 0;
		}

		$q = sprintf("update cms_data set date_start = %d, date_end = %d where id = %d",
			$ts_begin, $ts_end, $req["id"]);
		sql_query($q);
	}

	public function getCalendarItems($pageid=0, $itemid=0) {
		$data = array();
		if ($pageid) {
			$q = sprintf("select * from cms_date where pageid = %d order by date_begin", $pageid);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$data[]=$row;
			}
		} elseif ($itemid) {
			$q = sprintf("select * from cms_date where id = %d order by date_begin", $itemid);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			$data[]=$row;

		} else {
			//noting
		}
		return $data;
	}

	public function getMetadataDefinitions() {
		$data = array();
		$esc = sql_syntax("escape_char");
		$q = "select * from cms_metadef order by ".$esc."group".$esc.", ".$esc."order".$esc;
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["field_type_h"] = $this->meta_field_types[$row["field_type"]];
			$row["fpshow"] = ($row["fphide"]) ? 0:1;

			$data[$row["group"]][$row["id"]] = $row;
		}
		return $data;
	}
	public function getMetadataDefinitionById($id) {
		if ($id) {
			$q = sprintf("select * from cms_metadef where id = %d", $id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			return $row;
		}
	}
	public function saveMetadataDefinition($req) {
		$esc = sql_syntax("escape_char");

		$fields["field_name"]        = array("s", $req["cms"]["field_name"]);
		$fields["field_type"]        = array("s", $req["cms"]["field_type"]);
		$fields["field_value"]       = array("s", $req["cms"]["field_value"]);
		$fields[$esc."order".$esc]   = array("d", $req["cms"]["order"]);
		$fields[$esc."group".$esc]   = array("s", $req["cms"]["group"]);
		$fields["fphide"]            = array("d", $req["cms"]["fphide"]);


		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
			  //addslashes already done
				$vals[$k]="'".$v[1]."'";
			} else {
				$vals[$k]=(int)$v[1];
			}
		}

		if ($req["id"]) {
			$q = sprintf("update cms_metadef set ");
			foreach ($vals as $k=>$v) {
				$i++;
				if ($i > 1)
					$q.= ", ";

				$q.= sprintf(" %s = %s", $k, $v);
			}
			$q.= sprintf(" where id = %d", $req["id"]);
			sql_query($q);

		} else {
			foreach ($vals as $k=>$v) {
				$fld[]=$k;
			}
			$q = sprintf("insert into cms_metadef (%s) values (%s)", implode(",", $fld), implode(",", $vals));
			sql_query($q);
		}
	}
	public function metadataDefinitionsDelete($id) {
		$q = sprintf("delete from cms_metadef where id = %d", $id);
		sql_query($q);

		$q = sprintf("delete from cms_metadata where fieldid = %d", $id);
		sql_query($q);
	}
	public function getMetadataData($id, $subaction="") {

		switch ($subaction) {
			case "enable":
				$q = sprintf("update cms_data set useMetaData = 1 where id = %d", $id);
				sql_query($q);
				break;
			case "disable":
				$q = sprintf("delete from cms_metadata where pageid = %d", $id);
				sql_query($q);

				$q = sprintf("update cms_data set useMetaData = 0 where id = %d", $id);
				sql_query($q);
				break;
		}

		$data = array();
		$q = sprintf("select useMetaData from cms_data where id = %d", $id);
		$res = sql_query($q);
		$data["useMetaData"] = sql_result($res,0);

		$q = sprintf("select cms_metadef.*, cms_metadata.value from cms_metadef left join
			cms_metadata on cms_metadef.id = cms_metadata.fieldid where pageid = %d OR pageid is null order by cms_metadef.group, cms_metadef.order", $id);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["default_value"] = $row["field_value"];
			$data["data"][$row["group"]][] = $row;
		}
		return $data;
	}

	public function saveMetadata($req) {
		foreach ($req["meta"] as $k=>$v) {
			if (is_array($v))
				$v = implode("\n", $v);

			$q = sprintf("select count(*) from cms_metadata where fieldid = %d and pageid = %d", $k, $req["id"]);
			$res = sql_query($q);
			if (sql_result($res,0)==0) {
				$q = sprintf("insert into cms_metadata (pageid, fieldid, value) values (%d, %d, '%s')",
					$req["id"], $k, $v);
				sql_query($q);
			} else {
				$q = sprintf("update cms_metadata set value = '%s' where pageid = %d and fieldid = %d",
					$v, $req["id"], $k);
				sql_query($q);
			}

		}
	}

	public function getListData($id) {
		$q = sprintf("select * from cms_list where pageid = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		/* query field content:
		1:	name or id|operator|value|volgorde|enof\n
		2:	name or id|operator|value|volgorde|enof\n
		3:  ...
		*/
		$tmp = explode("\n",$row["query"]);
		$i=0;
		foreach ($tmp as $k=>$t) {
			$t = explode("|", $t);
			foreach ($t as $x=>$v) {
				$row["_query"][$k][$x] = $v;
			}
			$row["_query"][$k][5] = $i;
			$i++;
			if (!$row["_query"][$k][0])
				unset($row["_query"][$k]);
		}

		//fields content: veld1|veld2|veld3 (name or id)
		$tmp = explode("|", $row["fields"]);
		foreach ($tmp as $k=>$v) {
			$row["_fields"][$v] = $k+1;
		}

		/* order content:
		1:	name or id|desc or asc\n
		2:	name or id|desc or asc\n
		*/
		$row["_order"] = explode("\n", $row["order"]);
		$i = 0;
		foreach ($row["_order"] as $k=>$v) {
			$v = explode("|", $v);
			$row["_order"][$i] = array(
				"sort" => $v[0],
				"asc"  => $v[1]
			);
			$i++;
		}
		$row["_count"] = $row["count"];
		$row["_position"] = $row["listposition"];

		return $row;
	}

	public function saveCmsList($req) {
		$escape = sql_syntax("escape_char");
		$new =& $req["new"];

		/* if new field */

		if ($new["andor"] && ($new["operator"] || $new["newfield"] == "daterange"
			|| $new["newfield"] == "dateday") && $new["newfield"]) {

			switch ($new["newfield"]) {
				case "daterange":
					$new["newfield"] = "datum";
					/* create range, NOT a timestamp */
					$new["value"] = sprintf("%s/%s/%s-%s/%s/%s",
						$new["start_day"], $new["start_month"], $new["start_year"],
						$new["end_day"], $new["end_month"], $new["end_year"]
					);
					break;
				case "dateday":
					$new["value"] = "";
					break;
			}

			$new_field = sprintf("%s|%s|%s|%s|%s", $new["newfield"], $new["operator"],
				$new["value"], $new["position"], $new["andor"]);

			$list = $this->getListData($req["id"]);

			$query = explode("\n",$list["query"]);
			foreach ($query as $k=>$v) {
				if (!$v) unset($query[$k]);
			}
			$query[] = $new_field;

			$db_query = array();
			foreach($query as $k=>$v) {
				$d = explode("|",$v);
				$db_query[$d[3]]=$v;
			}

			$query = $db_query;
			ksort($query);
			$query = implode("\n",$query);

			$q = sprintf("update cms_list set %squery%s = '%s' where pageid = %d",
				$escape, $escape, $query, $req["id"]);
			sql_query($q);
		}

		/* fields */
		$db_fields = array();
		foreach ($req["fields"] as $k=>$v) {
			if ($v) {
				$db_fields[$v] = $k;
			}
		}
		ksort($db_fields);
		$req["fields"] = implode("|", $db_fields);

		$db_sort = array();
		for ($i=0;$i<3;$i++) {
			if ($req["order"][$i."_sort"]) {
				$db_sort[]= sprintf("%s|%s", $req["order"][$i."_sort"],
					($req["order"][$i."_asc"] == "desc") ? "desc":"asc");
			}
		}
		$req["order"] = implode("\n", $db_sort);

		$vals[$escape."fields".$escape] = $req["fields"];
		$vals[$escape."order".$escape]  = $req["order"];
		$vals["listposition"]           = $req["position"];

		$q = "update cms_list set count = 0 ";
		foreach ($vals as $k=>$v) {
			$q.= sprintf(", %s = '%s' ", $k, $v);
		}
		$q.= sprintf(" where pageid = %d", $req["id"]);
		sql_query($q);
	}

	public function deleteListItem($item, $id) {
		$list = $this->getListData($id);
		$query = explode("\n", $list["query"]);

		unset($query[$item]);
		$query = implode("\n",$query);

		$escape = sql_syntax("escape_char");

		$q = sprintf("update cms_list set %squery%s = '%s' where pageid = %d",
			$escape, $escape, $query, $id);
		sql_query($q);

	}
}
?>