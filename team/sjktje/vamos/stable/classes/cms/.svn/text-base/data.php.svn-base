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
		"html"   => "html pagina (html)",
		"js"     => "javascript (js)",
		"css"    => "style sheet (css)",
		"php"    => "scripting (php)",
		"main"   => "main template (main)"
	);
	public $meta_field_types;
	public $default_page = 0;
	private $gallery_cache;
	public $sitemap_cache;

	private $linkchecker;

	public function __construct() {
		/* declare default field types */
		$this->weekdays = array(
			"zo" => gettext("sunday"),
			"ma" => gettext("monday"),
			"di" => gettext("tuesday"),
			"wo" => gettext("wednesday"),
			"do" => gettext("thursday"),
			"vr" => gettext("friday"),
			"za" => gettext("saturday"),
		);

		$this->modules = array(
			"cms_meta"         => gettext("metadata"),
			"cms_date"         => gettext("date options"),
			"cms_forms"        => gettext("forms"),
			"cms_list"         => gettext("listoptions"),
			"cms_linkchecker"  => gettext("linkchecker"),
			"cms_changelist"   => gettext("modification overview"),
			"cms_banners"      => gettext("banners"),
			"cms_searchengine" => gettext("searchengine"),
			"cms_gallery"      => gettext("image gallery"),
			"cms_versioncontrol" => gettext("version controle"),
			"cms_page_elements"  => gettext("page elements"),
			"multiple_sitemaps"  => gettext("multiple sitemaps"),
			"cms_permissions"    => gettext("protected items")
		);

		$this->meta_field_types = array(
			"text"     => gettext("text field"),
			"textarea" => gettext("text area"),
			"select"   => gettext("select box"),
			"checkbox" => gettext("check box")
		);

		$this->cms_xs_levels = array(
			0 => gettext("no access"),
			1 => gettext("cms user"),
			2 => gettext("cms manager"),
			3 => gettext("cms admin")
		);
		$this->default_page = 0;

		/* some linkchecker constants */
		$this->linkchecker["outfile"] = sprintf("%slinkchecker_%s.xml",
			$GLOBALS["covide"]->filesyspath,
			$GLOBALS["covide"]->license["code"]
		);
		$this->linkchecker["startcmd"] = "/usr/bin/linkchecker -a -C -r2 -t4 --no-status -ocsv --timeout=15 #site#";
		$this->linkchecker["checkcmd"] = "ps afx | grep linkchecker | grep '#site#' | grep  -v 'grep' | cut -d ' ' -f 1";
		$this->linkchecker["url"]      = sprintf("http://%s/mode/linkchecker#param#", $_SERVER["HTTP_HOST"]);

	}
	public function decodeOptions($req = array()) {
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

		if (!$this->opts["siteroot"])
			$this->switchSiteRoot("R");

		switch ($req["cmd"]) {
			case "switchsiteroot":
				$this->switchSiteRoot($req["id"]);
				break;
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
			case "fillbuffer":
				$this->fillbuffer($req["page"]);
				break;
			case "pastebuffer":
				$this->pastebuffer($req["id"]);
				$this->expandPage($req["id"]);
				break;
			case "erasebuffer":
				$this->erasebuffer();
				break;
			case "bufferActive":
				$this->bufferOperation("isActive", 1);
				break;
			case "bufferActiveDis":
				$this->bufferOperation("isActive", 0);
				break;
			case "bufferPublic":
				$this->bufferOperation("isPublic", 1);
				break;
			case "bufferPublicDis":
				$this->bufferOperation("isPublic", 0);
				break;
			case "bufferMenuitem":
				$this->bufferOperation("isMenuItem", 1);
				break;
			case "bufferMenuitemDis":
				$this->bufferOperation("isMenuItem", 0);
				break;
			default:
				if ($req["cmd"])
					echo "Unknown command: ".$req["cmd"];
		}
		if ($req["cmd"] != "search" && $req["cms"]["search"]) {
			$this->highlightSearch($req["cms"]["search"]);
		}
	}

	public function getSiteRootPublicState($siteroot) {
		/* get siteroot public state */
		if (is_numeric($siteroot))
			$q = sprintf("select isPublic from cms_data where id = %d", $siteroot);
		elseif ($siteroot == "R")
			$q = sprintf("select isPublic from cms_data where isSpecial = '%s'", $siteroot);
		else
			return 0;

		$res = sql_query($q);
		return sql_result($res,0);
	}

	private function switchSiteRoot($id) {
		$this->opts["siteroot"] = $id;
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
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$spids[]=$row["id"];
			$this->fillParents($row["parentPage"]);
		}
	}

	public function fillbuffer($ids) {
		$buffer =& $this->opts["buffer"];
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$buffer[]=$id;
			}
		}
	}

	public function pastebuffer($id) {
		$buffer =& $this->opts["buffer"];
		$ids = implode(",", $buffer);
		if ($id) {
			$q = sprintf("update cms_data set parentPage = %d where id IN (%s)", $id, $ids);
			sql_query($q);
		}
	}

	public function erasebuffer() {
		unset($this->opts["buffer"]);
	}

	public function bufferOperation($field, $state) {
		$buffer =& $this->opts["buffer"];
		$ids = implode(",", $buffer);
		$q = sprintf("update cms_data set %s = %d where id IN (%s)", $field, $state, $ids);
		sql_query($q);
	}

	public function saveOptions() {
		$opts = $this->opts;
		unset($opts["sids"]);
		unset($opts["pids"]);
		unset($opts["spids"]);
		unset($opts["paste_state"]);
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
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
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
			if (!$this->sitemap_cache["check_permissions"][$pageid]) {
				$q = "select count(*) from cms_permissions where pid = $pageid";
				$res = sql_query($q) or die($q);
				if (sql_result($res,0) > 0)
					$this->sitemap_cache["check_permissions"][$pageid] = 1;
				else
					$this->sitemap_cache["check_permissions"][$pageid] = -1;
			}
			if ($this->sitemap_cache["check_permissions"][$pageid] == 1)
				return true;
			else
				return false;
		}
	}

	public function getParent($pageid) {
		if (!$pageid) die ("recursion error!");
		$q = "select parentPage from cms_data where id = ".(int)$pageid;
		$res = sql_query($q) or die($q);
		if (sql_num_rows($res)>0) {
			return sql_result($res,0);
		}
	}

	public function getPagePermissions($pageid) {
		$arr = array();

		if (!$this->sitemap_cache["page_permissions"][$pageid]) {
			$q = "select id from users order by username";
			$res = sql_query($q) or die($q);
			while ($row = sql_fetch_assoc($res)) {
				if ($pageid) {
					$q = "select * from cms_permissions where uid = ".$row["id"]." and pid = ".$pageid;
					$res2 = sql_query($q) or die($q);
					$row2 = sql_fetch_assoc($res2);
				}
				$arr[$row["id"]]["viewRight"] = (int)$row2["viewRight"];
				$arr[$row["id"]]["editRight"] = (int)$row2["editRight"];
				$arr[$row["id"]]["deleteRight"] = (int)$row2["deleteRight"];
				$arr[$row["id"]]["manageRight"] = (int)$row2["manageRight"];
			}
			$this->sitemap_cache["page_permissions"][$pageid] = $arr;
		}
		return ($this->sitemap_cache["page_permissions"][$pageid]);
	}

	public function getTemplates() {
		$data = array(
			0 => gettext("no template")
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
			$q = sprintf("select cms_data.*, cms_date.pageid as isDate from cms_data left join cms_date on cms_date.pageid = cms_data.id where cms_data.id = %d", $id);
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
			if ($data["date_start"] > 0 || $data["date_end"] > 0)
				$data["isDateRange"] = 1;


		} else {
			$data = array(
				"timestamp"  => mktime(),
				"parentPage" => $parentPage
			);
		}
		$conversion = new Layout_conversion();
		$data["pageData"]      = ($data["pageData"]);
		$data["autosave_data"] = ($data["autosave_data"]);

		if ($data["isForm"])
			$data["form_mode"] = $this->getFormMode($data["id"]);

		//do some cleanup
		if (!$data["isGallery"]) {
			$q = sprintf("delete from cms_gallery where pageid = %d", $data["id"]);
			sql_query($q);
			$q = sprintf("delete from cms_gallery_photos where pageid = %d", $data["id"]);
			sql_query($q);
		}
		if (!$data["isList"]) {
			$q = sprintf("delete from cms_list where pageid = %d", $data["id"]);
			sql_query($q);
		}
		if (!$data["isForm"]) {
			$q = sprintf("delete from cms_formulieren where pageid = %d", $data["id"]);
			sql_query($q);
		}

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

		if ($ts < mktime(0,0,0,1,1,2000))
			$ts = mktime();

		$q = sprintf("update cms_data set autosave_data = '', autosave_info = '',
			pageTitle = '%s', pageAlias = '%s', pageLabel = '%s', datePublication = %d where id = %d",
			$req["cms"]["pageTitle"], $req["cms"]["pageAlias"], $req["cms"]["pageLabel"], $ts, $id);
		sql_query($q);

		$alias = $GLOBALS["covide"]->webroot."page/";
		$alias = preg_replace("/^https/si", "http", $alias);
		if ($req["cms"]["pageAlias"]) {
			$alias .= $req["cms"]["pageAlias"];
		} else {
			$alias .= $id;
		}
		$alias.=".htm";

		/* update apEnabled */
		$hp = $this->getHighestParent($id);
		$this->updateApEnabled($id, $hp);

		if (!$skip_close) {
			echo "<script>
				var cf = parent.gettext('The page is saved, do you want to close it now?');
				/* call was from website, resync it */
				if ('1' == '".$_REQUEST["syncweb"]."') {
					parent.opener.location.href='$alias';
				} else {
					parent.opener.location.href='?mod=cms';
				}
				if (confirm(cf)==true) {
					setTimeout('parent.window.close();', 200);
				}
			</script>";
			exit();
		}
	}
	public function loadRestorePoint($id) {
		#$this->savePageData($id, 1);
		$q = sprintf("update cms_data set pageData = autosave_data where id = %d", $id);
		sql_query($q);

		$q = sprintf("update cms_data set autosave_data = '', autosave_info = '' where id = %d", $id);
		sql_query($q);

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
			$output->insertAction("cancel", gettext("alias already in use")." ".$id, "");
		}
		$output->exit_buffer();
	}

	public function checkUsername($exclude, $username) {
		$output = new Layout_output();
		$q = sprintf("select id from cms_users where id != %d and username = '%s'", $exclude, $username);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0 && strlen($username) > 2) {
			$output->addCode("1|");
			$output->insertAction("ok", gettext("username is ok"), "");
		} else {
			$output->addCode("0|");
			$output->insertAction("cancel", gettext("username already excists or invalid"), "");
		}
		$output->exit_buffer();
	}

	public function getCmsSettings($siteroot="") {
		if (!$siteroot)
			$siteroot = "R";

		if (is_numeric($siteroot)) {
			$q = sprintf("select * from cms_license_siteroots where pageid = %d", $siteroot);
			$res = sql_query($q);
			if (sql_num_rows($res) == 0) {
				$q = sprintf("insert into cms_license_siteroots (pageid) values (%d)", $siteroot);
				sql_query($q);
				$row = array(
					"pageid" => $siteroot
				);
			} else {
				$row = sql_fetch_assoc($res);
			}
		} else {
			$q = "select * from cms_license";
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
		}
		return $row;
	}
	public function saveCmsSettings($req) {
		$q = sprintf("update cms_license set cms_defaultpage = %d", $req["cms"]["cms_defaultpage"]);
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
				$row["is_enabled_h"] = gettext("yes");
			} else {
				$row["is_enabled_h"] = gettext("no");
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
			$res = sql_query($q) or die($q);
			if (sql_num_rows($res)>0) {
				$ret = sql_result($res,0);
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

	public function getMetadataDefinitions($show_only_fp=0) {
		$data = array();
		$esc = sql_syntax("escape_char");
		if (!$show_only_fp)
			$q = "select * from cms_metadef order by ".$esc."group".$esc.", ".$esc."order".$esc;
		else
			$q = "select * from cms_metadef where fphide = 0 order by ".$esc."group".$esc.", ".$esc."order".$esc;

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
		$q = sprintf("select count(*) from cms_list where pageid = %d", $req["id"]);
		$res = sql_query($q);
		if (sql_result($res,0) == 0) {
			$q = sprintf("insert into cms_list (pageid) values (%d)", $req["id"]);
			sql_query($q);
		}

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

	public function getFormResults($pageid) {
		$esc = sql_syntax("escape_char");
		$data = array();
		$fields = array();

		$q = sprintf("select * from cms_form_results_visitors where pageid = %d order by datetime_start", $pageid);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$q = sprintf("select cms_form_results.* from cms_form_results
				left join cms_formulieren on cms_formulieren.field_name = cms_form_results.field_name
				where visitor_id = %d order by ".$esc."order".$esc.", field_name", $row["id"]);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2)) {
				$row[sprintf("field_%s", $row2["field_name"])] = $row2["user_value"];
				if (!in_array($row2["field_name"], $fields))
					$fields[] = $row2["field_name"];
			}
			/* modify some values */
			unset($row["pageid"]);
			unset($row["visitor_hash"]);
			$row["ip_address"] = preg_replace("/\d{1,3}$/s", "x", $row["ip_address"]);
			$row["datetime_start"] = date("d-m-Y H:i", $row["datetime_start"]);
			$row["datetime_end"]   = date("d-m-Y H:i", $row["datetime_end"]);

			$data["data"][] = $row;
		}
		$data["fields"] = $fields;
		return $data;
	}
	public function deleteFormResultData($pageid, $id) {
		$q = sprintf("delete from cms_form_results where visitor_id = %d", $id);
		sql_query($q);
		$q = sprintf("delete from cms_form_results_visitors where id = %d", $id);
		sql_query($q);
	}
	public function getFormData($pageid, $id=0) {
		if ($id) {
			$q = sprintf("select * from cms_formulieren where pageid = %d and id = %d",
				$pageid, $id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			return $row;

		} else {
			$esc = sql_syntax("escape_char");
			$data = array();

			$q = sprintf("select * from cms_formulieren where pageid = %d order by %sorder%s, field_name",
				$pageid, $esc, $esc);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$opts = array();
				if ($row["is_required"])
					$opts[]= gettext("mandatory");
				if ($row["is_mailto"])
					$opts[]= gettext("receipient");
				if ($row["is_mailfrom"])
					$opts[]= gettext("sender");
				if ($row["is_mailsubject"])
					$opts[]= gettext("subject");
				if ($row["is_redirect"])
					$opts[]= gettext("result");

				$row["options"] = implode(", ", $opts);

				$this->meta_field_types["hidden"] = gettext("hidden field");
				$row["field_type_h"] = $this->meta_field_types[$row["field_type"]];
				$data[] = $row;
			}
			return $data;
		}
	}
	public function getFormMode($id) {
		$q = sprintf("select mode from cms_form_settings where pageid = %d", $id);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			return sql_result($res,0);
		}
	}
	public function saveFormMode($req) {
		$q = sprintf("select count(*) from cms_form_settings where pageid = %d", $req["id"]);
		$res = sql_query($q);
		if (sql_result($res,0) == 0) {
			$q = sprintf("insert into cms_form_settings (pageid, mode) values (%d, %d)", $req["id"], $req["cms"]["mode"]);
			sql_query($q);
		} else {
			$q = sprintf("update cms_form_settings set mode = %d where pageid = %d", $req["cms"]["mode"], $req["id"]);
			sql_query($q);
		}
	}
	public function saveFormData($req) {
		$cms = $req["cms"];
		$esc = sql_syntax("escape_char");

		$fields["pageid"]       = array("d", $req["pageid"]);
		$fields["field_name"]   = array("s", $cms["field_name"]);
		$fields["description"]  = array("s", $cms["description"]);
		$fields["field_type"]   = array("s", $cms["field_type"]);
		$fields["field_value"]  = array("s", $cms["field_value"]);
		$fields[$esc."order".$esc] = array("d", $cms["order"]);

		$fields["is_required"]    = array("d", $cms["is_required"]);
		$fields["is_mailto"]      = array("d", $cms["is_mailto"]);
		$fields["is_mailfrom"]    = array("d", $cms["is_mailfrom"]);
		$fields["is_mailsubject"] = array("d", $cms["is_mailsubject"]);
		$fields["is_redirect"]    = array("d", $cms["is_redirect"]);


		$vals = array();
		$keys = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
			  //addslashes already done
				$vals[$k]="'".$v[1]."'";
			} else {
				$vals[$k]=(int)$v[1];
			}
			$keys[] = $k;
		}

		if (!$req["id"]) {
			$q = sprintf("insert into cms_formulieren (%s) values (%s)",
				implode(",", $keys), implode(",", $vals));
			sql_query($q);
		} else {
			$i = 0;
			$q = "update cms_formulieren set ";
			foreach ($vals as $k=>$v) {
				if ($i > 0)
					$q.= ",";
				$q.= sprintf(" %s = %s ", $k, $v);
				$i++;
			}
			$q.= sprintf("where id = %d", $req["id"]);
			sql_query($q);
		}

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("opener.location.href = opener.location.href; window.close();");
		$output->end_javascript();
		$output->exit_buffer();

	}
	public function deleteFormData($pageid, $id) {
		$q = sprintf("delete from cms_formulieren where pageid = %d and id = %d", $pageid, $id);
		sql_query($q);
	}

	public function getFormErrors($pageid) {
		$q = "select count(*), sum(is_redirect), sum(is_mailto), sum(is_mailfrom), sum(is_mailsubject) ";
		$q.= "from cms_formulieren where pageid = ".$pageid;
		$res = sql_query($q);
		$row = sql_fetch_array($res);
		$i=0;
		foreach ($row as $k=>$v) {
			$row[$i] = $v;
			$i++;
		}
		$err = 0;
		if ($row[1]==0) {
			$err++; $ret[]= gettext("no resultpage found");
		} elseif ($row[1]>1) {
			$err++; $ret[]= gettext("more then one result page found");
		}
		if ($row[2]==0) {
			$err++; $ret[]= gettext("no receipient field found");
		} elseif ($row[2]>1) {
			$err++; $ret[]= gettext("more then one receipient field found");
		}
		if ($row[3]==0) {
			$err++; $ret[]= gettext("no sender field found");
		} elseif ($row[3]>1) {
			$err++; $ret[]= gettext("more then one sender field found");
		}
		if ($row[4]==0) {
			$err++; $ret[]= gettext("no subject field found");
		} elseif ($row[4]>1) {
			$err++; $ret[]= gettext("more then one subject field found");
		}
		if ($err==0) {
			$ret[]= gettext("no errors");
		}
		return $ret;
	}

	public function getChildPages($pageid) {
		$data   = array();
		$denied = 0;

		$cms = $this->getPageById($pageid);

		/* get basic user data */
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		$r = $this->getUserPermissions($pageid, $_SESSION["user_id"]);

		if ($r["deleteRight"] || $perms["xs_cms_level"] >= 2) {
			$ok = 1;
		} else {
			$ok = 0;
			$denied++;
		}
		$record = array(
			"id"    => $pageid,
			"title" => $cms["pageTitle"],
			"xs"    => $ok,
			"xs_rv" => ($ok) ? 0:1,
			"level" => 0
		);
		$data[]= $record;

		$this->getChilds($pageid, $data, $perms, $denied, 0);

		$return = array(
			"data"   => $data,
			"denied" => $denied
		);
		return $return;
	}

	public function getChilds($pageid, &$data, &$perms, &$denied, $level) {
		$q = sprintf("select id, pageTitle from cms_data where parentpage = %d", $pageid);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$r = $this->getUserPermissions($row["id"], $_SESSION["user_id"]);
			if ($r["deleteRight"] || $perms["xs_cms_level"] >= 2) {
				$ok = 1;
			} else {
				$ok = 0;
				$denied++;
			}
			$lh = " ";

			$record = array(
				"id"    => $row["id"],
				"title" => $row["pageTitle"],
				"xs"    => $ok,
				"xs_rv" => ($ok) ? 0:1,
				"level" => $level+1
			);
			$output = new Layout_output();
			for ($i=0;$i<$level+1;$i++) {
				$output->insertAction("tree", "", "");
			}
			$record["spacing"] = $output->generate_output();
			unset($output);

			$data[]= $record;
			if ($ok)
				$this->getChilds($row["id"], $data, $perms, $denied, $level+1);
		}
	}

	public function getHighestParent($id) {
		$curid     = $id;
		$curid_new = $id;
		while ($curid_new > 0) {
			$curid_new = $this->getParent($curid);
			if ($curid_new > 0)
				$curid = $curid_new;
		}
		return $curid;
	}

	public function getSpecialPageId($special) {
		$q = sprintf("select id from cms_data where (parentPage = 0 OR parentPage IS NULL) AND isSpecial = '%s'", $special);
		$res = sql_query($q);
		return sql_result($res, 0);
	}

	public function deletePages($ids) {
		/* retreive all child pages if page is part of 'deleted items' */
		/* get high parent 'deleted items' */
		$cids = array();
		$ids = explode(",", $ids);

		$del_hp = $this->getSpecialPageId("D");
		if ($this->getHighestParent($ids[0] == $del_hp)) {

			foreach ($ids as $id) {
				$tmp = $this->getChildPages($id);
				foreach ($tmp["data"] as $item) {
					$cids[$item["id"]] = $item["id"];
				}
			}

			/* delete all pages */
			foreach ($cids as $id) {
				$hp = $this->getHighestParent($id);
				/* if hp is equal to deleted items */
				if ($hp == $del_hp) {
					/* delete page including all references */
					$q = array();
					$q[] = sprintf("delete from cms_date where pageid = %d", $id);
					$q[] = sprintf("delete from cms_date_index where pageid = %d", $id);
					$q[] = sprintf("delete from cms_formulieren where pageid = %d", $id);
					$q[] = sprintf("delete from cms_form_results where pageid = %d", $id);
					$q[] = sprintf("delete from cms_form_results_visitors where pageid = %d", $id);
					$q[] = sprintf("delete from cms_gallery where pageid = %d", $id);
					$q[] = sprintf("delete from cms_gallery_photos where pageid = %d", $id);
					$q[] = sprintf("delete from cms_form_results where pageid = %d", $id);
					$q[] = sprintf("delete from cms_license_siteroots where pageid = %d", $id);
					$q[] = sprintf("delete from cms_list where pageid = %d", $id);
					$q[] = sprintf("delete from cms_metadata where pageid = %d", $id);
					$q[] = sprintf("delete from cms_permissions where pid = %d", $id);
					$q[] = sprintf("delete from cms_data where id = %d", $id);
					foreach ($q as $sql) {
						echo $sql.'<BR>';
					}
				}
			}

		} else {
			/* if not deleted items, move to deleted items and set custom permissions */
			foreach ($ids as $id) {
				$q = sprintf("delete from cms_permissions where pid = %d and uid = '%d'", $id, $_SESSION["user_id"]);
				sql_query($q);

				$q = sprintf("insert into cms_permissions (pid, uid, editRight, viewRight, manageRight,
					deleteRight) values (%d, '%d', 1, 1, 0, 1)", $id, $_SESSION["user_id"]);
				sql_query($q);

				$q = sprintf("update cms_data set parentPage = %d where id = %d", $del_hp, $id);
				sql_query($q);
			}
		}
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("
				opener.cmsReload();
				setTimeout('window.close();', 200);
			");
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function getGalleryData($id) {
		$data = array();
		$esc = sql_syntax("escape_char");
		$q = sprintf("select * from cms_gallery_photos where pageid = %d order by ".$esc."order".$esc.", file", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["file_short"] = basename($row["file"]);
			$data[$row["id"]]=$row;
		}
		return $data;
	}
	public function getGalleryItem($id) {
		$q = sprintf("select * from cms_gallery_photos where id = %d", $id);
		$res = sql_query($q);
		$data = sql_fetch_assoc($res);
		return $data;
	}

	public function getGallerySettings($id) {
		$q = sprintf("select * from cms_gallery where pageid = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		if (!$row["bigsize"])   $row["bigsize"]   = "800";
		if (!$row["thumbsize"]) $row["thumbsize"] = "240";
		if (!$row["cols"])      $row["cols"]      = "3";
		if (!$row["rows"])      $row["rows"]      = "4";
		return $row;
	}
	public function saveGallerySettings($req) {
		$q = sprintf("select * from cms_gallery where pageid = %d", $req["id"]);
		$res = sql_query($q);

		if (sql_num_rows($res)==0) {
			$q = sprintf("insert into cms_gallery (gallerytype, cols, fullsize,
				bigsize, thumbsize, pageid, rows) values (%d, %d, %d, %d, %d, %d)",
				$req["cms"]["gallerytype"], $req["cms"]["cols"], $req["cms"]["fullsize"],
				$req["cms"]["bigsize"], $req["cms"]["thumbsize"], $req["id"], $req["cms"]["rows"]);
			sql_query($q);
		} else {
			$row = sql_fetch_assoc($res);
			$thumbsize = $row["thumbsize"];
			$bigsize   = $row["bigsize"];
			$q = sprintf("update cms_gallery set gallerytype = %d, cols = %d, fullsize = %d,
				bigsize = %d, thumbsize= %d, rows = %d where pageid = %d",
				$req["cms"]["gallerytype"], $req["cms"]["cols"], $req["cms"]["fullsize"],
				$req["cms"]["bigsize"], $req["cms"]["thumbsize"], $req["cms"]["rows"], $req["id"]);
			sql_query($q);

			if ($thumbsize != $req["cms"]["thumbsize"] || $bigsize != $req["cms"]["bigsize"]) {
				/* prevent session lockup */
				session_write_close();
				$items = $this->getGalleryData($req["id"]);
				foreach ($items as $k=>$v)
					$this->createCache($k);
			}

		}

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf(" location.href = '?mod=cms&action=cmsgallery&id=%d';", $req["id"]));
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function createCache($file_id) {
		require(self::include_dir."dataCreateCache.php");
	}

	public function convertThumb($filename) {
		//extract image information
		$type   = getimagesize($filename);
		$mime   = &$type["mime"];
		$width  = &$type[0];
		$height = &$type[1];

		//only gif,png,jpeg
		if ($mime=="image/jpeg" || $mime=="image/pjpeg"){
			$source = imagecreatefromjpeg($filename);
		} elseif ($mime=="image/gif") {
			$source = imagecreatefromgif($filename);
		} elseif ($mime=="image/png") {
			$source = imagecreatefrompng($filename);
		} else {
			echo gettext("filetype not supperted");
		}
		imagejpeg($source, $filename, 100);
	}

	public function galleryUpload($req) {
		$filesys = new Filesys_data();
		$files =& $_FILES["binFile"];

		foreach ($files["tmp_name"] as $pos=>$tmp_name) {
			/* if file position is filled with a tmp_name */
			if ($files["error"][$pos] == UPLOAD_ERR_OK && $tmp_name) {

				/* gather some file info */
				$name = $files["name"][$pos];
				$type = $filesys->detectMimetype($tmp_name);
				$size = $files["size"][$pos];

				$ext = $filesys->get_extension($name);
				if (in_array($ext, array("jpg", "png", "gif", "jpeg"))) {

					//convert orig input to jpeg, max quality
					$this->convertThumb($tmp_name);

					/* get order + 1 */
					$esc = sql_syntax("escape_char");
					$q = sprintf("select max(%1\$sorder%1\$s) from cms_gallery_photos where pageid = %2\$d", $esc, $req["id"]);
					$res = sql_query($q);
					$order = sql_result($res,0)+1;

					/* insert file into dbase */
					$q = sprintf("insert into cms_gallery_photos (pageid, file, description, %1\$sorder%1\$s) values ", $esc);
					$q.= sprintf("(%d, '%s', '%s', %d)",
						$req["id"], $name, $req["filedata"]["description"], $order);
					sql_query($q);
					$dbid = sql_insert_id("cms_gallery_photos");

					/* move thumb to destination */
					$dest = sprintf("%s/%s/%d_full.jpg",
						$GLOBALS["covide"]->filesyspath, "gallery", $dbid);
					move_uploaded_file($tmp_name, $dest);

					//create cached thumbnails
					$this->createCache($dbid);
				}
			}
		}
	}
	public function cmsGalleryItemDelete($req) {
		$types = array("full", "medium", "small");
		foreach ($types as $t) {
			$file = sprintf("%s/%s/%d_%s.jpg",
				$GLOBALS["covide"]->filesyspath, "gallery", $req["item"], $t);
			if (file_exists($file))
				unlink($file);
		}
		$q = sprintf("delete from cms_gallery_photos where id = %d", $req["item"]);
		sql_query($q);
	}
	public function cmsGalleryItemSave($req) {
		echo "<PRE>";
		print_r($req);
		$esc = sql_syntax("escape_char");
		$q = sprintf("update cms_gallery_photos set file = '%2\$s', description = '%3\$s',
			%1\$sorder%1\$s = %4\$d where id = %5\$d",
			$esc, $req["cms"]["file"], $req["cms"]["description"], $req["cms"]["order"], $req["id"]);
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(" opener.location.href = opener.location.href; window.close(); ");
		$output->end_javascript();
		$output->exit_buffer();
	}
	public function cmsGalleryItemSwitch($req) {
		$data = $this->getGalleryData($req["id"]);
		$data_keys = array();
		foreach ($data as $k=>$v) {
			$data_keys[]=$k;
		}
		$pos = array_search($req["itemid"], $data_keys);
		if ($req["direction"] == "up") {
			if ($pos > 0) {
				$target = $data_keys[$pos-1];
			} else {
				end($data_keys);
				$target = current($data_keys);
			}
		} else {
			if ($pos < count($data_keys)) {
				$target = $data_keys[$pos+1];
			} else {
				$target = $data_keys[0];
			}
		}
		echo $target;

		$esc = sql_syntax("escape_char");
		$q = sprintf("update cms_gallery_photos set %1\$sorder%1\$s = %2\$d where id = %3\$d", $esc, $data[$target]["order"], $req["itemid"]);
		#sql_query($q);
		$q = sprintf("update cms_gallery_photos set %1\$sorder%1\$s = %2\$d where id = %3\$d", $esc, $data[$req["id"]]["order"], $target);
		#sql_query($q);
	}
	public function loadGalleryFile($id, $size) {
		$file = sprintf("%s/%s/%d_%s.jpg",
			$GLOBALS["covide"]->filesyspath, "gallery", $id, $size);

		header("Content-Type: image/jpeg");
		echo file_get_contents($file);
		exit();
	}
	public function addSiteRoot($name) {
		$q = sprintf("insert into cms_data (parentPage, pageTitle) values (0, '%s')", $name);
		sql_query($q);
	}
	public function saveSiteInfo($req) {
		$req["cms"]["search_language"] = @implode(",", $req["cms"]["search_language"]);

		if (is_numeric($req["siteroot"])) {
			$table = "cms_license_siteroots";
			$baseq = "id = id";
		} else {
			$table = "cms_license";
			$baseq = "db_version = db_version";
		}

		$q = sprintf("update %s set %s, cms_defaultpage = %d,
			google_verify = '%s', cms_hostnames = '%s'", $table, $baseq, $req["cms"]["cms_defaultpage"],
			$req["cms"]["google_verify"], $req["cms"]["cms_hostnames"]);

		foreach ($req["cms"] as $k=>$v) {
			if ($k != "isPublic")
				if ($k == "search_use_pagetitle" || $k == "cms_defaultpage")
					$q.= sprintf(", %s = %d ", $k, $v);
				else
					$q.= sprintf(", %s = '%s' ", $k, $v);
		}

		if (is_numeric($req["siteroot"]))
			$q.= sprintf(" where pageid = %d", $req["siteroot"]);

		sql_query($q);

		if ($req["cms"]["isPublic"])
			$new = 1;
		else
			$new = 0;

		if (is_numeric($req["siteroot"]))
			$q = sprintf("update cms_data set isPublic = %d where id = %d", $new, $req["siteroot"]);
		else
			$q = sprintf("update cms_data set isPublic = %d where isSpecial = '%s'", $new, $req["siteroot"]);

		sql_query($q);

	}
	public function checkLinkcheckerResults() {
		$file = $this->linkchecker["outfile"];
		if (!file_exists($file))
			return array();

		$csv = array();
		$handle = fopen($this->linkchecker["outfile"], "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$row++;
			for ($c=0; $c < $num; $c++) {
					$csv[$row][$c] = $data[$c];
			}
		}
		$data = array();
		fclose($handle);

		foreach ($csv as $k=>$v) {
			if (!preg_match("/^#/s", $v[0])) {
				/* we got data */
				$i++;
				if ($i==1)
					$fields = $v;
				else
					foreach ($v as $c=>$f)
						$data[$i][$fields[$c]] = urldecode($f);
			}
		}
		foreach ($data as $k=>$v) {
			if ($v["result"] == "200 OK" || $v["valid"] == "True")
				unset($data[$k]);
			else {
				$data[$k]["pageid"]    = preg_replace("/[^0-9]/s", "", urldecode(basename($v["parentname"])));
				$data[$k]["url"]       = sprintf("<a target=\"_blank\" href=\"%1\$s\">%1\$s</a>", $v["url"]);
			}

		}
		return $data;
	}
	public function startLinkchecker() {
		$output = new Layout_output();
		if (!$this->checkLinkCheckerStatus()) {
			$cmd = str_replace("#site#", "'".$this->linkchecker["url"], $this->linkchecker["startcmd"]);
			$param = sprintf("&user=%s&hash=%s", $_SESSION["user_id"], $this->linkcheckerHash());
			$cmd = str_replace("#param#", $param, $cmd);
			$cmd.= "' > ".$this->linkchecker["outfile"];
			$cmd.= " &";
			exec($cmd, $ret, $rv);
			$output->addCode(gettext("linkchecker started"));
		} else {
			$output->addCode(gettext("linkchecker is already running"));
		}
		$output->start_javascript();
			$output->addCode("
				opener.location.href = opener.location.href;
				setTimeout('window.close();', 100);
			");
		$output->end_javascript();
		$output->exit_buffer();
	}
	public function checkLinkCheckerStatus() {
		$cmd = str_replace("#site#", $this->linkchecker["url"], $this->linkchecker["checkcmd"]);
		$cmd = str_replace("#param#", "", $cmd);
		exec($cmd, $ret, $retval);
		if (count($ret)==0)
			return false;
		else
			return true;
	}
	public function linkcheckerHash($user_id="") {
		if (!$user_id) $user_id = $_SESSION["user_id"];
		$user_data = new User_data();
		$data = $user_data->getUserDetailsById($user_id);
		$hash = md5($data["username"].$data["id"].$data["password"]);
		return $hash;
	}
	public function lastLinkchecker() {
		if (file_exists($this->linkchecker["outfile"]))
			return date("d-m-Y H:i", filectime($this->linkchecker["outfile"]));
		else
			return gettext("never");
	}
	public function updateApEnabled($pageid, $rootid) {
		$q = sprintf("update cms_data set apEnabled = %2\$d where id = %1\$d and apEnabled != %2\$d",
			$pageid, $rootid);
		sql_query($q);
	}
	public function load_function_overview() {
		require_once(self::include_dir."functions.php");
	}
	public function scanForFiles(&$files) {
		$like = sql_syntax("like");
		if (is_array($files)) {
			foreach ($files as $k=>$file) {
				$q = sprintf("select id from cms_data where pageData %s '%%/cmsfile/%s%%' order by id",
					$like, $file["id"]);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$files[$k]["pages"][] = array(
						"ispage" => 1,
						"type"   => "page",
						"name"   => gettext("pageid"),
						"id"     => $row["id"]
					);
				}
				$q = sprintf("select id from cms_templates where data %s '%%/cmsfile/%s%%' order by id",
					$like, $file["id"]);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$files[$k]["pages"][] = array(
						"istpl"  => 1,
						"type"   => "template",
						"name"   => gettext("template"),
						"id"     => $row["id"]
					);
				}
			}
		}
	}
}
?>
