<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class Cms_data {
	/* constants {{{ */
	const include_dir      = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name       = "cms";
	/** @var int cache freshness for cms files in seconds */
	const CACHE_CMSFILE_EXPIRES = 3600;
	/* }}} */

	/* variables {{{ */
	/**
	 * @var int $delete_interval Days deleted items are kept
	 */
	public $delete_interval = 14;
	/**
	 * @var array $opts Page options
	 */
	public $opts = array();
	/**
	 * @var array $lang Possible site languages
	 */
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
		"sv" => "svenska",
		"ja" => "japanese",
		"zh-cn" => "chinese",
		"ru" => "russian"
	);
	/**
	 * @var array $weekdays days of the week
	 */
	public $weekdays = array();
	/**
	 * @var array $modules Available CMS modules
	 */
	public $modules = array();
	/**
	 * @var array $cms_xs_levels Available permission levels in the CMS
	 */
	public $cms_xs_levels = array();
	/**
	 * @var array $include_category Available site template types
	 */
	public $include_category = array(
		"html"   => "html pagina (html)",
		"js"     => "javascript (js)",
		"css"    => "style sheet (css)",
		"php"    => "scripting (php)",
		"main"   => "main template (main)",
		"smarty" => "smarty template (smarty)"
	);
	/**
	 * @var array $meta_field_types Possible metadata types
	 */
	public $meta_field_types;
	/**
	 * @var array $abbr_field_types Possible abbrieviation types
	 */
	public $abbr_field_types;
	/**
	 * @var int $default_page Id of default homepage for a siteroot
	 */
	public $default_page = 0;
	/**
	 * @var array $gallery_cache Cached gallery pages
	 */
	private $gallery_cache;
	/**
	 * @var array $sitemap_cache Cached sitemap entries
	 */
	public $sitemap_cache;
	/**
	 * @var $repeat_table Cached repeating calendar items
	 */
	public $repeat_table = array();
	/**
	 * @var array $parent_cache
	 */
	private $parent_cache = array();
	/**
	 * @var array the translation cache
	 */
	private $translation_cache = array();
	/**
	 * @var int $permission_prefetch Should we grab all permissions before grabbing pagestructures?
	 */
	public $permission_prefetch = 0;
	/**
	 * @var array $linkchecker Data needed to run a linkcheck on the site
	 */
	public $linkchecker;
	/* }}} */
	/* methods */
	/* __construct {{{ */
	/**
	 * Class constructor.
	 *
	 * Populate class variables
	 */
	public function __construct() {

		/* check for a specific database upgrade inside patches_runonce */
		$this->checkCmsDataTable();

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

		$this->repeat_table = $this->weekdays;
		$this->repeat_table["maand"] = gettext("monthly");
		$this->repeat_table["jaar"]  = gettext("yearly");

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
			"cms_versioncontrol" => gettext("version control")." (n/a)",
			"cms_page_elements"  => gettext("page elements"),
			"multiple_sitemaps"  => gettext("multiple sitemaps (protected items)"),
			"cms_permissions"    => gettext("protected items (multiple_sitemaps)"),
			"cms_mailings"       => gettext("mailing module"),
			"cms_address"        => gettext("address link module"),
			"cms_protected"      => gettext("protected items"),
			"cms_feedback"       => gettext("feedback system"),
			"cms_user_register"  => gettext("register new users"),
			"cms_shop"           => gettext("internet shop"),
			"cms_use_strict_mode" => gettext("use xhtml mode")
		);
		

		$this->meta_field_types = array(
			"text"     => gettext("text field"),
			"textarea" => gettext("text area"),
			"select"   => gettext("select box"),
			"multiselect"   => gettext("multiple select box"),
			"checkbox" => gettext("check box"),
			"numeric"  => gettext("text/numeric field"),
			"financial" => gettext("valuta field"),
			"shop"     => gettext("shop field")
		);

		$this->abbr_field_types = array(
			0 => gettext('use as abbrieviation'),
			1 => gettext('use as translation')
		);

		$this->cms_xs_levels = array(
			0 => gettext("no access at all"),
			1 => sprintf("%s (%s)", gettext("cms user"), gettext("access based on page permissions")),
			2 => sprintf("%s (%s)", gettext("cms manager"), gettext("full content access")),
			3 => sprintf("%s (%s%s)",
				gettext("cms admin"),
				($GLOBALS["covide"]->license["cms_lock_settings"]) ? "css ":"",
				gettext("template and full content access"))
		);
		$this->default_page = 0;

		/* some linkchecker constants */
		$this->linkchecker["outfile"] = sprintf("%s/linkchecker_%s.xml",
			$GLOBALS["covide"]->filesyspath,
			$GLOBALS["covide"]->license["code"]
		);
		$this->linkchecker["startcmd"] = "/usr/bin/linkchecker -C -a -r2 -t1 --no-status -ocsv --timeout=15 #site#";
		$this->linkchecker["checkcmd"] = "ps afx | grep linkchecker | grep '#site#' | grep  -v 'grep' | cut -d ' ' -f 1";
		$this->linkchecker["url"]      = sprintf("http://%s/mode/linkchecker#param#", $_SERVER["HTTP_HOST"]);
	}
	/* }}} */
	/* checkCmsDataTable {{{ */
	/**
	 * check for a specific cms patch inside patches_runonce
	 * this patch could block a future update
	 */
	private function checkCmsDataTable() {
		/* check if there is no data inside the table cms_data */
		$q = sprintf("select count(id) from cms_data");
		$res = sql_query($q);

		if (sql_result($res,0) == 0) {
			/* check if the table cms_data does exist and if pageAlias is defined there */
			$q = "describe cms_data";
			$res = sql_query($q);
			$found = 0;
			while ($row = sql_fetch_assoc($res)) {
				if ($row["Field"] == "pageAlias")
					$found++;
			}
			if ($found == 0) {
				/* run a destructive patch - we can drop these tables */
				$ary = array(
					"cms_abbreviations",
					"cms_alias_history",
					"cms_banner_views",
					"cms_cache",
					"cms_counters",
					"cms_data",
					"cms_date",
					"cms_date_index",
					"cms_feedback",
					"cms_files",
					"cms_form_results",
					"cms_form_results_visitors",
					"cms_form_settings",
					"cms_formulieren",
					"cms_gallery",
					"cms_gallery_photos",
					"cms_image_cache",
					"cms_images",
					"cms_keys",
					"cms_languages",
					"cms_license",
					"cms_license_siteroots",
					"cms_list",
					"cms_logins_log",
					"cms_mailings",
					"cms_metadata",
					"cms_metadef",
					"cms_permissions",
					"cms_siteviews",
					"cms_temp",
					"cms_templates",
					"cms_users"
				);
				foreach ($ary as $tbl) {
					$q = sprintf("drop table %s", $tbl);
					sql_query($q);
				}

				/* reset autopatcher in license table */
				$q = "update license set autopatcher_lastpatch = 0";
				sql_query($q);

				/* redir the client */
				$output = new Layout_output();
				$output->addCode(gettext("Covide (CMS) database upgrade in progress, please wait..."));
				$output->start_javascript();
				$output->addCode("setTimeout(\"document.location.href = 'index.php?mod=desktop';\", 100);");
				$output->end_javascript();
				$output->exit_buffer();
			}
		}
	}
	/* }}} */
	/* processDeletedItems {{{ */
	/**
	 * Cleanup deleted items when the savetime is reached
	 */
	public function processDeletedItems() {
		/* get all deleted items pages, rely on the apEnabled flag */
		$deltime = time() - ($this->delete_interval*60*60*24);
		$q = sprintf("select id from cms_data where isSpecial = 'D' and parentPage = 0");
		$res = sql_query($q);
		$hp = sql_result($res,0,"",2);

		$ids = array();
		$q = sprintf("select id from cms_data where apEnabled = '%d' and date_last_action <= %d", $hp, $deltime);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$ids[]=$row["id"];
		}
		if (count($ids) > 0) {
			$ids = implode(",", $ids);
			$this->deletePages($ids, 1);
		}

		$q = sprintf("update cms_data set isActive = 0 where apEnabled = '%d' and isActive = 1", $hp);
		sql_query($q);

		/* process empty site roots with no name */
		$q = sprintf("select id from cms_data where (pageTitle = '' or pageTitle is null) and parentPage = 0");
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$q = sprintf("select count(*) from cms_data where parentPage = %d", $row["id"]);
			$res2 = sql_query($q);
			if (sql_result($res2,0,"",2) == 0) {
				$q = sprintf("delete from cms_data where id = %d", $row["id"]);
				sql_query($q);

				if ($this->opts["siteroot"] == $row["id"])
					$this->opts["siteroot"] = "R";
			}
		}
	}
	/* }}} */
	/* decodeOptions {{{ */
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

		/* check if siteroot does still exist */
		if (is_numeric($this->opts["siteroot"])) {
			$q = sprintf("select count(*) from cms_data where id = %d", $this->opts["siteroot"]);
			$res = sql_query($q);
			if (sql_result($res,0,"",2) == 0)
				unset($this->opts["siteroot"]);
		}
		$this->opts["cmd"] = $req["cmd"];

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
				if ($req["jump_to_anchor"])
					$this->expandTree((int)preg_replace("/^id/s", "", $req["jump_to_anchor"]));
				break;
			case "fillbuffer":
				$this->fillbuffer($req["page"]);
				break;
			case "pastebuffer":
				$this->pastebuffer($req["id"]);
				$this->expandPage($req["id"]);
				break;
			case "copybuffer":
				$this->copybuffer($req["id"]);
				$this->expandPage($req["id"]);

				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode(" location.href='?mod=cms'; ");
				$output->end_javascript();
				$output->exit_buffer();
				break;
			case "erasebuffer":
				$this->erasebuffer();
				break;
			case "togglebuffer":
				$this->togglebuffer($req["page"]);
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
			case "bufferAddressLevel":
				$this->bufferOperation("address_level", 0);
				break;
			case "bufferAddressLevelDis":
				$this->bufferOperation("address_level", 1);
				break;
			default:
				if ($req["cmd"])
					echo "Unknown command: ".$req["cmd"];
		}
		if ($req["cmd"] != "search" && $req["cms"]["search"]) {
			$this->highlightSearch($req["cms"]["search"]);
		}
	}
	/* }}} */
	/* getSiteRootPublicState {{{ */
	/**
	 * Find out wether a siteroot is public
	 *
	 * @param int $siteroot The siteroot id
	 * @return int 0 if not else the isPublic value
	 */
	public function getSiteRootPublicState($siteroot) {
		/* get siteroot public state */
		if (is_numeric($siteroot))
			$q = sprintf("select isPublic from cms_data where id = %d", $siteroot);
		elseif ($siteroot == "R")
			$q = sprintf("select isPublic from cms_data where isSpecial = '%s'", $siteroot);
		else
			return 0;

		$res = sql_query($q);
		return sql_result($res,0,"",2);
	}
	/* }}} */
	/* switchSiteRoot {{{ */
	/**
	 * Switch view to a different siteroot
	 *
	 * @param int $id The new siteroot id
	 */
	private function switchSiteRoot($id) {
		$this->opts["siteroot"] = $id;
	}
	/* }}} */
	/* highlightSearch {{{ */
	/**
	 * Mark matching pages so sitemap can highlight them
	 *
	 * @param string $str The searchphrase
	 */
	private function highlightSearch($str) {
		$sids  =& $this->opts["sids"];
		$sids  =  array();

		$q = sprintf("select id, parentPage from cms_data where (pageTitle like '%%%1\$s%%'
			OR pageLabel like '%%%1\$s%%' OR pageData like '%%%1\$s%%' OR pageAlias like '%%%1\$s%%'
			OR id = %2\$d)", $str, (int)$str);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			if ($row["parentPage"]>0) {
				$sids[] = $row["id"];
			}
		}
	}
	/* }}} */
	/* searchPageXML {{{ */
	/**
	 * check permission on searchresult and return int so ajax framework can handle it
	 *
	 * @param int $id The pageid to check
	 * @return int 0 if page does not exists, 1 if viewacces else -1
	 */
	public function searchPageXML($id) {
		$q = sprintf("select count(*) from cms_data where id = %d", $id);
		$res = sql_query($q);
		if (sql_result($res,0,"",2)==1) {
			$r = $this->getUserPermissions($id, $_SESSION["user_id"]);
			if ($r["viewRight"])
				echo "1";
			else
				echo "-1";
		} else {
			echo "0";
		}
		exit();
	}
	/* }}} */
	/* searchPage {{{ */
	/**
	 * Search CMS pages for specified string
	 *
	 * @param string $str The searchphrase
	 */
	private function searchPage($str) {
		$this->collapsePage(-1);

		$sids  =& $this->opts["sids"];
		$sids  =  array();

		$spids =& $this->opts["spids"];
		$spids =  array();

		$q = sprintf("select id, parentPage from cms_data where (pageTitle like '%%%1\$s%%'
			OR pageLabel like '%%%1\$s%%' OR pageHeader like '%%%1\$s%%' 
			OR pageData like '%%%1\$s%%' OR pageAlias like '%%%1\$s%%'
			OR id = %2\$d)", $str, (int)$str);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			if ($row["parentPage"] > 0) {
				$this->fillParents($row["parentPage"]);
				$sids[] = $row["id"];
			}
		}
		$this->opts["toonpages"] = array_unique($spids);
	}
	/* }}} */
	/* searchPages {{{ */
	/**
	 * Search in pages and return the first 150 matches starting at result $start
	 *
	 * @param string $str The searchphrase
	 * @param int $start The result record to start from
	 * @param int $infile if set it will also use beagle to search inside filedata
	 * @return array matching pages data
	 */
	public function searchPages($str, $start, $infile=0) {
		$infile = $this->stripHosts("\"".$infile, 1);
		$infile = preg_replace("/^\"/s", "", $infile);

		if (preg_match("/^\/{0,1}page\/\d{1,}$/s", $infile))
			$infile = (int)preg_replace("/^\/{0,1}page\/(\d{1,})$/s", "$1", $infile);
		else
			$infile = 0;

		/* get basic user data */
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		$q    = "";
		$sids = array(-1);
		if ($infile) {
			#$q = sprintf("select id, parentPage from cms_data where id = %d UNION ", $infile);
			$sq = sprintf(" AND NOT id = %d ", $infile);
		}
		$q .= sprintf("select id, parentPage from cms_data where (pageTitle like '%%%1\$s%%'
			OR pageLabel like '%%%1\$s%%' OR pageData like '%%%1\$s%%' OR pageAlias like '%%%1\$s%%'
			OR id = %2\$d) %3\$s LIMIT 150", $str, (int)$str, $sq);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			if ($row["parentPage"]>0) {
				$r = $this->getUserPermissions($row["id"], $_SESSION["user_id"]);
				if ($r["viewRight"] || $perms["xs_cms_level"] >= 2) {
					$sids[$row["id"]] = $row["id"];
				}
			}
		}
		$data["count"] = count($sids);

		$sids = implode(",", $sids);
		$start = (int)$start;

		$q = "";
		if ($infile)
			$q = sprintf("select 1 as current, id, pageTitle, pageAlias, datePublication from cms_data where id = %d UNION ", $infile);

		$q .= sprintf("select 0 as current, id, pageTitle, pageAlias, datePublication from cms_data where
			id IN (%s) and not id = %d order by current desc, id", $sids, $infile);
		$res = sql_query($q, "", $start, $GLOBALS["covide"]->pagesize);
		while ($row = sql_fetch_assoc($res)) {
			if ($infile == $row["id"])
				$row["highlight"] = 1;
			else
				$row["highlight"] = 0;

			$row["pageAlias_h"] = sprintf("/page/%s.htm", ($row["pageAlias"]) ? $row["pageAlias"]:$row["id"]);
			$row["datePublication_h"] = date("d-m-Y H:i", $row["datePublication"]);
			$data["pages"][] = $row;
		}
		return $data;
	}
	/* }}} */
	/* fillParents {{{ */
	/**
	 * Populate the spids array with parent id's
	 * This can be used to create a path or check where the page is
	 *
	 * @param int $id The page id to calculate the tree of parants from
	 */
	private function fillParents($id) {
		$spids =& $this->opts["spids"];
		$id = (int)$id;
		$q = "select id, parentPage from cms_data where id = $id group by parentpage";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$spids[]=$row["id"];
			$this->fillParents($row["parentPage"]);
		}
	}
	/* }}} */
	/* fillbuffer {{{ */
	/**
	 * Put ids in global CMS buffer
	 *
	 * @param array $ids The pageids to store in the buffer
	 */
	public function fillbuffer($ids) {
		$buffer =& $this->opts["buffer"];
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$buffer[]=$id;
			}
		}
	}
	/* }}} */
	/* pastebuffer {{{ */
	/**
	 * Paste items in buffer to specified place
	 *
	 * @param int $id The place where the buffer should be pasted
	 */
	public function pastebuffer($id) {
		$buffer =& $this->opts["buffer"];
		$ids = implode(",", $buffer);
		if ($id) {
			/* get the siteroot for this page */
			$hp = $this->getHighestParent($id);

			/* get target childs */
			$target_ids = array(-1);
			foreach ($buffer as $b) {
				$target = $this->getChildPages($b, 1);
				if (is_array($target["data"])) {
					foreach ($target["data"] as $k=>$v) {
						$target_ids[] = $v["id"];
					}
				}
			}
			$q = sprintf("update cms_data set apEnabled = %d where id IN (%s)", $hp, implode(",", $target_ids));
			sql_query($q);

			$q = sprintf("update cms_data set parentPage = %d where id IN (%s)", $id, $ids);
			sql_query($q);
		}
	}
	/* }}} */
	/* copyCmsRecord {{{ */
	/**
	 * Copy a record in a table
	 * @todo it returns the last inserted id of the table cms_data, no matter what table the copy was on
	 *
	 * @param string $table The table to do the copy action in
	 * @param array $row The database fields + values to insert
	 *
	 * @return int The newly inserted id
	 */
	private function copyCmsRecord($table, $row) {
		/* always unset id */
		unset($row["id"]);

		$esc = sql_syntax("escape_char");

		$fields = array();
		$values = array();

		foreach ($row as $k=>$v) {
			$fields[] = sprintf("%1\$s%2\$s%1\$s", $esc, $k);
			$values[] = sprintf("'%s'", addslashes($v));
		}
		$q = sprintf("insert into %s (%s) values (%s)",
			$esc.$table.$esc, implode(",", $fields), implode(",", $values));
		sql_query($q);
		return sql_insert_id("cms_data");
	}
	/* }}} */
	/* pageCopy {{{ */
	/**
	 * Copy a cms page
	 *
	 * @param int $src The source page id
	 * @param int $dest The new parentPage id for the source page
	 * @param string $suffix pageTitle suffix for the new page
	 */
	private function pageCopy($src, $dest, $suffix="") {
		if (!$src)
			return;

		$q = sprintf("select * from cms_data where id = %d", $src);
		$res = sql_query($q);
		$data = sql_fetch_assoc($res);

		/* reassign parent page */
		$data["parentPage"] = $dest;

		/* unset page id and alias */
		unset($data["id"]);
		unset($data["pageAlias"]);

		$data["pageTitle"].=$suffix;

		$newid = $this->copyCmsRecord("cms_data", $data);

		/* retreive permissions, forms, lists, meta options */
		$q = sprintf("select * from cms_metadata where pageid = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["pageid"] = $newid;
			$this->copyCmsRecord("cms_metadata", $row);
		}
		/* copy list data */
		$q = sprintf("select * from cms_list where pageid = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["pageid"] = $newid;
			$this->copyCmsRecord("cms_list", $row);
		}
		/* copy permissions */
		$q = sprintf("select * from cms_permissions where pid = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["pid"] = $newid;
			$this->copyCmsRecord("cms_permissions", $row);
		}
		/* copy forms */
		$q = sprintf("select * from cms_formulieren where pageid = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["pageid"] = $newid;
			$this->copyCmsRecord("cms_formulieren", $row);
		}
		$q = sprintf("select * from cms_form_settings where pageid = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["pageid"] = $newid;
			$this->copyCmsRecord("cms_form_settings", $row);
		}

		//scan for child pages and copy them
		$q = sprintf("select id from cms_data where parentPage = %d", $src);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->pageCopy($row["id"], $newid);
		}
	}
	/* }}} */
	/* copybuffer {{{ */
	/**
	 * Copy pages in global buffer to specified parentpage
	 *
	 * @param int $id The parentpage where we want to paste the buffer
	 */
	public function copybuffer($id) {
		set_time_limit(60*60);

		$buffer =& $this->opts["buffer"];
		$ids = implode(",", $buffer);
		if ($id) {
			/* get the siteroot for this page */
			$hp = $this->getHighestParent($id);

			/* get target childs */
			$target_ids = array(-1);
			foreach ($buffer as $b) {
				$target = $this->getChildPages($b, 1);
				if (is_array($target["data"])) {
					foreach ($target["data"] as $k=>$v) {
						$target_ids[] = $v["id"];
					}
				}
				/* copy them over */
				$this->pageCopy($b, $id, sprintf(" (%s: %s)", gettext("copy"), date("d-m-Y H:i")));
			}

			/* update apEnabled information */
			$q = sprintf("update cms_data set apEnabled = %d where id IN (%s)", $hp, implode(",", $target_ids));
			sql_query($q);
		}
	}
	/* }}} */
	/* erasebuffer {{{ */
	/**
	 * Erase global buffer
	 */
	public function erasebuffer() {
		unset($this->opts["buffer"]);
	}
	/* }}} */
	/* togglebuffer {{{ */
	/**
	 * Add/Remove ids from global buffer.
	 * The input array will be walked, and if the id is already in the buffer it will be deleted,
	 * and if the id is not yet in the buffer it will be added.
	 *
	 * @param array $ids ID's to toggle in the buffer
	 */
	public function togglebuffer($ids) {
		if (!is_array($ids))
			$ids = array();

		$buffer =& $this->opts["buffer"];

		foreach ($ids as $id) {
			if (in_array($id, $buffer))
				unset($buffer[array_search($id, $buffer)]);
			else
				$buffer[] = $id;
		}
	}
	/* }}} */
	/* bufferOperation {{{ */
	/**
	 * Set field content for all items in the global buffer
	 *
	 * @param string $field The database fieldname
	 * @param string $state The new value for fieldname
	 */
	public function bufferOperation($field, $state) {
		$buffer =& $this->opts["buffer"];
		$ids = implode(",", $buffer);
		$q = sprintf("update cms_data set %s = %d where id IN (%s)", $field, $state, $ids);
		sql_query($q);
	}
	/* }}} */
	public function saveOptions() {
		$opts = $this->opts;
		unset($opts["sids"]);
		unset($opts["pids"]);
		//unset($opts["spids"]);
		unset($opts["paste_state"]);
		$view = serialize($opts);

		$q = sprintf("update cms_siteviews set view = '%s' where user_id = %d",
			$view, $_SESSION["user_id"]);
		sql_query($q);
	}


	private function expandTree($id) {
		$id = (int)$id;
		if ($id > 0) {
			$this->expandPage($id);
			$q = "select id, parentPage from cms_data where id = $id group by parentpage";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				$this->expandTree($row["parentPage"]);
			}
		}
	}
	private function expandPage($id) {
		if ($id > 0) {
			$this->opts["toonpages"][] = (int)$id;
			$this->opts["toonpages"] = array_unique($this->opts["toonpages"]);
		} elseif ($id == -1) {
			$q = "select parentPage from cms_data group by parentPage";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
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
		while ($row = sql_fetch_assoc($res,2)) {
			$pids[]=$row["parentPage"];
		}
		$pids = array_unique($pids);
		return $pids;
	}

	public function getUserSitemapRoots() {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$data = array();
		$q = "select id, pageTitle from cms_data where (isSpecial = '' OR isSpecial IS NULL) and parentPage = 0 order by pageTitle";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* check if the user has cms access level of 2 or higher
				 OR has permissions to at least one sub page of the site root
			*/

			if ($user_info["xs_cms_level"] >= 2) {
				$xs = 1;
			} else {
				$xs = 0;
				$q = sprintf("select id from cms_data where parentPage = %d", $row["id"]);
				$res2 = sql_query($q);
				while ($row2 = sql_fetch_assoc($res2,2)) {
					$q = sprintf("select count(*) from cms_permissions where uid = %d and pid = %d",
						$_SESSION["user_id"], $row2["id"]);
					$res3 = sql_query($q);
					if (sql_result($res3, 0,"",2) > 0)
						$xs = 1;
				}
			}
			if ($xs == 1)
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

	public function updateAuthorisations($subact, $id) {
		if ($subact == "enable" || $subact == "disable") {
			/* first, disable it all */
			$q = sprintf("delete from cms_permissions where pid = %d", $id);
			sql_query($q);
		}

		if ($subact == "enable") {
			/* get archive user */
			$user_data = new User_data();
			$archive = $user_data->getArchiveUserId();

			/* insert the archive user, just to have at least one record */
			$q = sprintf("insert into cms_permissions (pid, uid) values (%d, %d)",
				$id, $archive);
			sql_query($q);
		}
	}
	public function checkPagePermissions($pageid) {
		/* prefetch permissions */
		if ($this->permission_prefetch == 1) {
			$this->permission_prefetch = 2; //= done

			$q = sprintf("select pid from cms_permissions group by pid");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				$this->sitemap_cache["check_permissions"][$row["pid"]] = 1;
			}
			$q = sprintf("select id, parentPage from cms_data");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res,2)) {
				$this->parent_cache[$row["id"]] = (int)$row["parentPage"];
			}
		}
		/* normal code */
		if ($pageid == 0) {
			return true;
		} else {
			if ($this->permission_prefetch && !$this->sitemap_cache["check_permissions"][$pageid])
				$this->sitemap_cache["check_permissions"][$pageid] = -1;

			if (!$this->sitemap_cache["check_permissions"][$pageid]) {
				$q = sprintf("select count(pid) from cms_permissions where pid = %d", $pageid);
				$res = sql_query($q);
				if (sql_result($res,0,"",2) > 0)
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
	public function precachePagePermissionsByParent($parent_id) {
		$q = sprintf("select cms_data.id, cms_permissions.pid from cms_data
			left join cms_permissions on cms_data.id = cms_permissions.pid
			where parentPage = %d", $parent_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			if (!$this->sitemap_cache["check_permissions"][$row["id"]]) {
				if ($row["pid"] > 0)
					$this->sitemap_cache["check_permissions"][$pageid] = 1;
				else
					$this->sitemap_cache["check_permissions"][$pageid] = -1;
			}
		}
	}

	public function getParent($pageid) {
		if (!$pageid) die ("recursion error!");

		if (!$this->parent_cache[$pageid]) {
			$q = sprintf("select parentPage from cms_data where id = %d", $pageid);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0) {
				$result = sql_result($res,0,"",2);
				if ($result == 0)
					$result = -1;

				$this->parent_cache[$pageid] = $result;
			}
		}
		return ($this->parent_cache[$pageid] == -1) ? 0:$this->parent_cache[$pageid];
	}

	public function getPagePermissions($pageid) {
		if (!$pageid)
			return array();

		$arr = array();
		if (!$this->sitemap_cache["page_permissions"])
			$this->sitemap_cache["page_permissions"] = array();

		if (!is_array($this->sitemap_cache["page_permissions"][$pageid])) {
			if (!is_array($this->sitemap_cache["users"])) {
				$q = "select id from users order by username";
				$res = sql_query($q) or die($q);
				while ($row = sql_fetch_assoc($res,2)) {
					$this->sitemap_cache["users"][] = $row["id"];
				}
			}
			foreach ($this->sitemap_cache["users"] as $user) {
				$arr[$row["id"]]["viewRight"] = 0;
				$arr[$row["id"]]["editRight"] = 0;
				$arr[$row["id"]]["deleteRight"] = 0;
				$arr[$row["id"]]["manageRight"] = 0;
			}
			$q = sprintf("select * from cms_permissions where pid = %d", $pageid);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2,2)) {
				$arr[$row2["uid"]]["viewRight"] = (int)$row2["viewRight"];
				$arr[$row2["uid"]]["editRight"] = (int)$row2["editRight"];
				$arr[$row2["uid"]]["deleteRight"] = (int)$row2["deleteRight"];
				$arr[$row2["uid"]]["manageRight"] = (int)$row2["manageRight"];
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

	public function stripHosts($data, $no_hostname=0) {
		$settings = $this->getCmsSettings();

		$hosts = explode("\n", str_replace("\r", "", $settings["cms_hostnames"]));
		$hosts = array_merge($hosts, array($_SERVER["HTTP_HOST"], $_SERVER["SERVER_ADDR"]));
		$hosts = array_unique($hosts);

		foreach ($hosts as $host) {
			$host = trim($host);
			if ($host) {
				$regex  = "/\"http(s){0,1}:\/\/".$host."\//si";
				if ($no_hostname) {
					$target = "\"/";
				} else {
					$target = "\"".$GLOBALS["covide"]->webroot;
				}
				$data = preg_replace($regex, $target, $data);
			}
		}
		return $data;
	}
	public function getPageById($id=0, $parentPage=0, $no_hostname=0) {
		if ($id) {
			$q = sprintf("select cms_data.*, cms_date.pageid as isDate from cms_data left join cms_date on cms_date.pageid = cms_data.id where cms_data.id = %d", $id);
			$res = sql_query($q);
			$data = sql_fetch_assoc($res);
			$data["pageData"]   = trim($data["pageData"]);
			$data["pageHeader"] = trim($data["pageHeader"]);

			$popup_data = explode("|", $data["popup_data"]);
			$data["popup_height"]  = $popup_data[0];
			$data["popup_width"]   = $popup_data[1];
			$data["popup_hidenav"] = $popup_data[2];

			$data["search_language"] = explode(",", $data["search_language"]);

			$data["pageData"]      = $this->stripHosts($data["pageData"], $no_hostname);
			$data["autosave_data"] = $this->stripHosts($data["autosave_data"], $no_hostname);

			if ($data["date_start"] > 0 || $data["date_end"] > 0)
				$data["isDateRange"] = 1;


		} else {
			$data = array(
				"timestamp"  => time(),
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

			$this->eraseGalleryCache($data["id"]);
		}
		if (!$data["isList"]) {
			$q = sprintf("delete from cms_list where pageid = %d", $data["id"]);
			sql_query($q);
		}
		if (!$data["isFeedback"]) {
			$q = sprintf("delete from cms_feedback where page_id = %d", $data["id"]);
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


		if ($cms["template"]) {
			$q = sprintf("select pageData from cms_data where id = %d", $cms["template"]);
			$res = sql_query($q);
			$data = addslashes(sql_result($res,0));
		} else {
			$data = "";
		}

		if (!$cms["id"]) {
			$q = sprintf("insert into cms_data (parentPage, pageData, pageTitle,
				pageLabel, pageAlias, isActive, isMenuitem, isPublic, datePublication) values (
				%d, '%s', '%s', '%s', '%s', %d, %d, %d, %d)",
				$cms["parentPage"], $data, $cms["pageTitle"], $cms["pageLabel"], $cms["pageAlias"],
				$cms["isActive"], $cms["isMenuItem"], $cms["isPublic"], $date);
			sql_query($q);
			$cms["id"] = sql_insert_id("cms_data");
		}
		echo "<script>parent.location.href='?mod=cms&action=editpage&id=".$cms["id"]."&parentpage=".$cms["parentPage"]."';</script>";
		exit();
	}

	public function saveRestorePoint($req) {
		$info = $_SESSION["user_id"]."|".time();
		$q = sprintf("update cms_data set autosave_header = '%s', autosave_info = '%s',
			autosave_data = '%s' where id = %d", $_REQUEST["cms"]["pageHeader"],
			$info, $_REQUEST["contents"], $_REQUEST["cms"]["id"]);
		sql_query($q);
	}

	public function truncateRestorePoint($id, $close_window=0) {
		$q = sprintf("update cms_data set autosave_header = '',
			autosave_info = '', autosave_data = '' where id = %d", $id);
		sql_query($q);
		if ($close_window) {
			echo "closepopup();";
			exit();
		}
	}

	public function savePageData($id, $skip_close=0, $req="") {
		if (!$id)
			$id = $req["cms"]["id"];

		if ($req) {
			$this->saveRestorePoint($req);
		}
		/* grab global license settings */
		$cmslicense = $this->getCmsSettings();
		/* if changelog is enabled, store the old version in the archive table */
		if ($cmslicense["cms_changelist"]) {
			$oldpage = $this->getPageById($id);
			$oldpage["page_id"] = $oldpage["id"];
			$oldpage["versiondate"] = time();
			$oldpage["search_language"] = implode(",", $oldpage["search_language"]);
			$oldpage["editor"] = $_SESSION["user_id"];
			unset($oldpage["id"]);
			unset($oldpage["popup_height"]);
			unset($oldpage["popup_width"]);
			unset($oldpage["popup_hidenav"]);
			unset($oldpage["search_language"]);
			unset($oldpage["search_language"]);
			unset($oldpage["isDate"]);
			unset($oldpage["isDateRange"]);
			unset($oldpage["form_mode"]);
			foreach($oldpage as $k=>$v) {
				$fields[] = $k;
				$values[] = "'".sql_escape_string($v)."'";
			}
			$sql = sprintf("INSERT INTO cms_data_revisions (%s) VALUES (%s);", implode(",", $fields), implode(",", $values));
			$res = sql_query($sql);
		}
		$q = sprintf("update cms_data set pageHeader = autosave_header,
			pageData = autosave_data where id = %d", $id);
		sql_query($q);

		/* process alias history */
		$q = sprintf("select pageAlias from cms_data where id = %d", $id);
		$res = sql_query($q);
		$old_alias = sql_result($res,0);
		if ($old_alias != $req["cms"]["pageAlias"]) {
			/* add entry to old alias table */
			$q = sprintf("insert into cms_alias_history (pageid, datetime, alias) values
				(%d, %d, '%s')", $id, time(), $old_alias);
			sql_query($q);
		}

		$ts = mktime($req["cms"]["timestamp_hour"], $req["cms"]["timestamp_min"], 0,
			$req["cms"]["timestamp_month"], $req["cms"]["timestamp_day"], $req["cms"]["timestamp_year"]);

		if ($ts < mktime(0,0,0,1,1,2000))
			$ts = time();

		$q = sprintf("update cms_data set date_changed = %d, autosave_header = '', autosave_data = '', autosave_info = '',
			pageTitle = '%s', pageAlias = '%s', pageLabel = '%s', datePublication = %d, isSource = %d where id = %d",
			time(), $req["cms"]["pageTitle"], $req["cms"]["pageAlias"], $req["cms"]["pageLabel"], $ts, $req["cms"]["isSource"], $id);
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
				}
				if (confirm(cf)==true) {
					if (parent.opener.document.getElementById('velden'))
						parent.opener.document.getElementById('velden').submit();
					parent.window.close();
				}
			</script>";
			exit();
		}
	}
	public function loadRestorePoint($id) {
		#$this->savePageData($id, 1);
		$q = sprintf("update cms_data set pageHeader = autosave_header,
			pageData = autosave_data where id = %d", $id);
		sql_query($q);

		$q = sprintf("update cms_data set autosave_header = '', autosave_data = '',
			autosave_info = '' where id = %d", $id);
		sql_query($q);

		echo "document.location.href='?mod=cms&action=editpage&id=".$id."';";
		exit();
	}

	public function gotoFilesys($hidenav=0, $ftype=null, $infile=0) {
		$infile = $this->stripHosts("\"".$infile, 1);
		$infile = preg_replace("/^\"/s", "", $infile);

		if (preg_match("/^\/{0,1}cmsfile\/\d{1,}$/s", $infile))
			$infile = (int)preg_replace("/^\/{0,1}cmsfile\/(\d{1,})$/s", "$1", $infile);

		$filesys_data = new Filesys_data();
		if ($infile) {
			$file = $filesys_data->getFileById($infile, 1);
			$id = $file["folder_id"];
		}
		//if (!$id)
		//	$id = $filesys_data->getCmsFolder();

		if ($hidenav) {
			$uri = sprintf("index.php?mod=filesys&action=opendir&subaction=%s&id=%d&infile=%d&jump_to_anchor=file_%d", $ftype, $id, $infile, $infile);
		} else {
			$uri = "index.php?mod=filesys&action=opendir&id=".$id;
		}
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode(sprintf("location.href='%s';", $uri));
		$output->end_javascript();
		$output->exit_buffer();
	}

	public function savePageSettings($req) {
		$cms =& $req["cms"];

		$fields["keywords"]            = array("s", $cms["keywords"]);
		$fields["pageRedirect"]        = array("s", $cms["pageRedirect"]);
		$fields["pageRedirectPopup"]   = array("d", $cms["pageRedirectPopup"]);
		$fields["address_ids"]         = array("s", $cms["address_id"]);
		$fields["address_level"]       = array("d", $cms["address_level"]);

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
		$fields["isProtected"]         = array("d", $cms["isProtected"]);
		$fields["useSSL"]              = array("d", $cms["useSSL"]);
		$fields["useInternal"]         = array("d", $cms["useInternal"]);
		$fields["search_override"]     = array("d", $cms["search_override"]);
		$fields["search_fields"]       = array("s", $cms["search_fields"]);
		$fields["search_descr"]        = array("s", $cms["search_descr"]);
		$fields["search_title"]        = array("s", $cms["search_title"]);
		$fields["conversion_script"]   = array("s", $cms["conversion_script"]);

		if (!is_array($cms["search_language"])) $cms["search_language"] = array();
		$fields["search_language"]     = array("s", implode(",", $cms["search_language"]));

		$fields["google_changefreq"]   = array("s", $cms["google_changefreq"]);
		$fields["google_priority"]     = array("s", $cms["google_priority"]);
		$fields["isMenuItem"]          = array("d", $cms["isMenuItem"]);
		$fields["isTemplate"]          = array("d", $cms["isTemplate"]);
		$fields["isSticky"]            = array("d", $cms["isSticky"]);

		$fields["isGallery"]  = array("d",0);
		$fields["isForm"]     = array("d",0);
		$fields["isList"]     = array("d",0);
		$fields["isFeedback"] = array("d",0);
		$fields["isInherit"] = array("d",0);
		$fields["inheritpage"] = array("d", $cms['inheritpage']);

		$fields["isShop"]     = array("d", $cms["isShop"]);
		$fields["shopPrice"]  = array("f", $cms["shopPrice"]);


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
			case "feedback":
				$fields["isFeedback"] = array("d", 1);
				break;
			case 'inherit':
				$fields['isInherit'] = array('d', 1);
				break;
		}

		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
			  //addslashes already done
				$vals[$k]="'".$v[1]."'";
			} elseif ($v[0]=="f") {
				$vals[$k]=(float)$v[1];
			} else {
				$vals[$k]=(int)$v[1];
			}
		}
		$q = "update cms_data set date_changed = ".time();
		foreach ($vals as $k=>$v) {
			$q.= sprintf(", %s = %s ", $k, $v);
		}
		$q.= sprintf(" where id = %d", $req["id"]);
		sql_query($q);

		echo "<script>location.href='?mod=cms&action=editSettings&id=".$req["id"]."';</script>";
		exit();
	}

	public function checkAlias($exclude, $alias) {
		$alias = trim($alias);

		if (in_array($alias, array("sitemap", "sitemap_plain")))
			$err = "in sitemap";

		$output = new Layout_output();
		$q = sprintf("select id from cms_data where id != %d and pageAlias = '%s'", $exclude, $alias);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0 || !$alias) {
			$err = 0;
		} else {
			$err = sql_result($res,0,"",2);
		}

		if (!$err && $alias) {
			/* check for alias in history object */
			$q = sprintf("select pageid from cms_alias_history where alias = '%s'", $alias);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0) {
				$err = sql_result($res,0,"",2);
				$histerr = 1;
			}
		}

		if (!$err) {
			$output->addCode("1|");
			$output->insertAction("ok", gettext("alias is ok"), "");
			$output->addTag("br");
			$output->addSpace();
		} else {
			$output->addCode("0|");
			$output->insertAction("cancel", gettext("alias already used")." ".$err, "");
			$output->addTag("br");
			$output->addSpace();

			$output2 = new Layout_output();
			$output2->insertTag("a", $err, array(
				"href" => "javascript: cmsForceEdit($err);"
			));
			$err = $output2->generate_output();

			if ($histerr)
				$output->addCode(gettext("alias is already used in alias history of page")." ".$err);
			else
				$output->addCode(gettext("alias is already used in page")." ".$err);
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

	public function getCmsSettings($siteroot="", $realfetch=0) {
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
		$q = sprintf("update cms_license set cms_defaultpage = cms_defaultpage");
		foreach ($this->modules as $k=>$v) {
			$q.= sprintf(", %s = %d", $k, $req["cms"][$k]);
		}
		sql_query($q);
		echo "
			<script>
				parent.closepopup();
			</script>
		";
	}
	public function getUserNameById($id) {
		$user = $this->getAccountList($id);
		return ($user[0]["username"]);
	}

	/* getAccountList {{{ */
	/**
	 * Get list of cms accounts
	 *
	 * @param int $id If given, only return specified account
	 * @param int $start The place to start in the recordset for the paging object
	 * @param string $search Optional search string to limit the result to
	 * @param int $nolimit if set dont return subset but return all
	 *
	 * @return array The account list that matches
	 */
	public function getAccountList($id=0, $start=0, $search="", $nolimit = 0) {
		$data = array();
		$address_data = new Address_data();

		$like = sql_syntax("like");

		if ($id) {
			$q = sprintf("select * from cms_users where id = %d", $id);
		} else {
			$q = "select * from cms_users ";
			if ($search) {
				$sq = sprintf(" where username %1\$s '%%%2\$s%%' or email %1\$s '%%%2\$s%%' ",
					$like, $search);
				$q.= $sq;
			}
			$q.= " order by username";
		}
		#echo $q;

		//$res = sql_query($q);
		if ($nolimit) {
			$res = sql_query($q);
		} else {
			$res = sql_query($q, "", $start, $GLOBALS["covide"]->pagesize);
		}

		while ($row = sql_fetch_assoc($res)) {
			if ($row["is_enabled"]) {
				$row["is_enabled_h"] = gettext("yes");
			} else {
				$row["is_enabled_h"] = gettext("no");
			}
			if ($row["is_active"]) {
				$row["is_active_h"] = gettext("yes");
			} else {
				$row["is_active_h"] = gettext("no");
			}
			if (!$row["registration_date"]) {
				$row["registration_date"] = mktime(0,0,0,1,1,2000);
				$row["registration_date_h"] = gettext("none");
			} else {
				$row["registration_date_h"] = date("d-m-Y H:i", $row["registration_date"]);
			}

			$row["address_name"] = $address_data->getAddressNameById($row["address_id"]);
			if ($row["address_id"]) {
				$row["address_data"] = $address_data->getAddressById($row["address_id"]);
			}
			$data[] = $row;
		}
		if (!$id) {
			$ret["data"] = $data;
			$q = "select count(*) from cms_users ".$sq;
			$res = sql_query($q);
			$ret["count"] = sql_result($res,0);
			unset($data);
			$data = $ret;
		}
		return $data;
	}
	/* }}} */
	public function saveAccount($req) {
		if ($req["id"]) {
			$q = sprintf("update cms_users set email = '%s', username = '%s', password = '%s', is_enabled = %d, is_active = %d, address_id = %d where id = %d",
				$req["cms"]["email"], $req["cms"]["username"], $req["cms"]["password"], $req["cms"]["is_enabled"], $req["cms"]["is_active"], $req["cms"]["address_id"], $req["id"]);
			sql_query($q);
		} else {
			$q = sprintf("insert into cms_users (email, username, password, is_enabled, is_active, address_id) values ('%s', '%s', '%s', %d, %d, %d)",
				$req["cms"]["email"], $req["cms"]["username"], $req["cms"]["password"], $req["cms"]["is_enabled"], $req["cms"]["is_active"], $req["cms"]["address_id"]);
			sql_query($q);
		}
	}
	public function deleteAccount($id) {
		$q = sprintf("delete from cms_users where id = %d", $id);
		sql_query($q);
	}

	public function getCmsFile($id, $mailtracking = 0) {
		$filesys_data = new Filesys_data();
		$file = $filesys_data->getFileById($id, 1);

	  if (!$file["timestamp"])
	  	$file["timestamp"] = mktime(0,0,0,1,1,date("Y")-1);
		if ($file["id"]) {
			$hp = $filesys_data->getHighestParent($file["folder_id"]);
			if ($hp["name"] == "cms") {

				/* Checking if the client is validating his cache and if it is current. */
				if (!$_REQUEST["save"] && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $file["timestamp"])) {
					/* Client's cache IS current, so we just respond '304 Not Modified'. */
					header('Last-Modified: '.date('r', $file["timestamp"]), true, 304);
				} else {
					if (!$mailtracking)
						ob_clean();

					header("Content-Transfer-Encoding: binary");
					header("Expires: ".date('r', mktime() + self::CACHE_CMSFILE_EXPIRES), true);
					header('Last-Modified: '.date('r', mktime() - 3600), true, 200);
					header(sprintf('Cache-Control: must-revalidate, max-age=%1$d, s-maxage=%1$d', self::CACHE_CMSFILE_EXPIRES));
					header("Pragma: public");

					if (!$_REQUEST["save"])
						$filesys_data->file_download($id, 2);
					else
						$filesys_data->file_download($id);
				}
			}
		}
		exit();
	}
	public function getCmsCache($id) {

		$filename = sprintf("%s/%s/%d.jpg",
			$GLOBALS["covide"]->filesyspath, "cmscache", $id);

		if (!file_exists($filename))
			die("file not found");

		/* file is accessed, update the TTL */
		$q = sprintf("update cms_image_cache set datetime = %d where id = %d",
			time(), $id);
		sql_query($q);

		/* get file details from cache */
		$q = sprintf("select * from cms_image_cache where id = %d", $id);
		$res = sql_query($q);
		$file = sql_fetch_assoc($res);

	  if (!$file["datetime"])
	  	$file["datetime"] = mktime(0,0,0,1,1,date("Y")-1);

		/* Checking if the client is validating his cache and if it is current. */
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $file["timestamp"])) {
			/* Client's cache IS current, so we just respond '304 Not Modified'. */
			header('Last-Modified: '.date('r', $file["datetime"]), true, 304);
		} else {
			ob_clean();

			header("Content-Transfer-Encoding: binary");
			header("Expires: ".date('r', mktime() + self::CACHE_CMSFILE_EXPIRES));
			header('Last-Modified: '.date('r', mktime() - 3600), true, 200);
			header(sprintf('Cache-Control: must-revalidate, max-age=%1$d, s-maxage=%1$d', self::CACHE_CMSFILE_EXPIRES));
			header("Pragma: public");
			header("Content-Type: image/jpeg");

			$handle = fopen($filename, "r");
			fpassthru($handle);
		}
		exit();
	}

	public function saveAuthorisations($req) {

		/* reset authorisations */
		$this->updateAuthorisations("enable", $req["id"]);

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
		while ($row = sql_fetch_assoc($res,2)) {
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
		$user_data = new User_data();
		$perms = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$data = array();
		if ($id) {
			$q = sprintf("select * from cms_templates where id = %d", $id);
		} else {
			$q = sprintf("select * from cms_templates order by title");
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
				case "smarty":
					$row["category_h"] = "ftype_ppt";
			}
			if (in_array($row["category"], array("php", "main"))) {
				if ($GLOBALS["covide"]->license["cms_lock_settings"]
					&& $perms["username"] != "administrator") {

					$row["del_access"]  = 0;
					$row["edit_access"] = 0;
				} else {
					$row["del_access"]  = 1;
					$row["edit_access"] = 1;
				}
			} else {
				$row["edit_access"] = 1;
				$row["del_access"]  = 1;
			}

			$data[$row["id"]] = $row;
		}
		return $data;
	}

	public function getCmsTmpPrefix() {
		$tmp = sprintf("%s../tmp_cms/%s_",
			$GLOBALS["covide"]->temppath, $GLOBALS["covide"]->license["code"]);
		return $tmp;
	}

	public function saveTemplateCache($id, $type, $data) {
		$tmp = sprintf("%s%d.%s", $this->getCmsTmpPrefix(), $id, $type);

		if (in_array($type, array("js", "css")) && is_writable(dirname($tmp))) {
			file_put_contents($tmp, $data);
		}
			
		if ($type == "smarty" && is_writable(dirname($tmp))) {
			$tmp_dir = $GLOBALS['covide']->temppath.'../tmp_cms/smarty/';
			if (!file_exists($tmp_dir)) {
				mkdir($tmp_dir);
			}
			foreach (array('compiled', 'cache', 'config', 'templates') as $dir) {
				if (!file_exists($tmp_dir.$dir)) {
					mkdir($tmp_dir.$dir);
				}
			}
			// check if the template does exist
			$file = sprintf("%stemplates/%s_%d.tpl", 
				$tmp_dir, $GLOBALS['covide']->license['code'], $id
			);
			file_put_contents($file, $data);
		}
	}

	/* {{{ doLint($req) */
	/**
	 * doLint($req)
	 *
	 * This should be used in conjunction with saveTemplate(). doLint performs
	 * a lint code check on $req['cms']['data']. If no errors are found, doLint
	 * returns 1. If an error is found, 0 is returned. If doLink fails to find
	 * the php binary it will return 2.
	 *
	 * @author Svante Kvarnström <sjk@ankeborg.nu>
	 * @param array $req Request data
	 * @return bool
	 * @see saveTemplate()
	 */
	public function doLint($req) {
		/*
		 * runkit_lint() comes with PHP 5.1 (and higher.) Let's check if
		 * it exists. If not we'll have to pass the command line switch -l (lint)
		 * to php5.
		 */
		 if (function_exists("runkit_lint")) {
			/* function_exists returns true if the function does exist */

			if (runkit_lint(stripslashes($req['cms']['data']))) {
				/*
				 * runkit_lint peforms a syntax check on $code and returns
				 * true if it came out all right.
				 */
				return 1;
			} else {
				return 0; /* Return false on syntax error */
			}

		} else {
			/*
			 * The runkit_lint function is missing, we're forced to use the
			 * command line alternative, php5 -l -f filename.txt. We'll have
			 * to generate a temporary php file and run lint on that.
			 */
			$tmpfile = sprintf("%sphplint_%s_%d_%d",
				$GLOBALS['covide']->temppath, $GLOBALS['covide']->license['code'],
				$req['id'], time().rand(0,9999));

			file_put_contents($tmpfile, stripslashes($req['cms']['data']));

			/*
			 * Lets check if the "php5" binary exists. If not we'll try "php," and
			 * if that fails as well we'll return 2.
			 * We can get the php binary location from the configuration file.
			 * If not given, we assume it's in the webservers path.
			 */
			require("conf/offices.php");
			if (!is_array($cms)) $cms = array();
			if (array_key_exists("phpbin", $cms)) {
				if (is_executable($cms["phpbin"])) {
					$phpbin = $cms["phpbin"];
				}
			}
			if (!$phpbin) {
				if (is_executable("/usr/bin/php5")) {
					$phpbin = "/usr/bin/php5";
				} elseif (is_executable("php")) {
					$phpbin = "php";
				} else {
					return 2; /* Could not find php binary! */
				}
			}

			$cmd = sprintf("$phpbin -l -f %s", escapeshellarg($tmpfile));
			exec($cmd, $ret, $retval);


			if ($retval) {
				/* php -l returned an error. */
				return 0;
			} else {
				return 1; /* If retval is 0 php -l had nothing to complain about. */
			}

		}
	} /* }}} */

	public function saveTemplate($req) {
		switch ($req["cms"]["category"]) {
			case "php":
			case "main":
				/* Let's do the lint check. */
				$dolint = $this->doLint($req);

				if ($dolint == 0) {
					/* Erroneous code. Tell the user and exit. */
					$output = new Layout_output();
					$output->start_javascript();
						$output->addCode(sprintf("alert('%s\n%s');",
							addslashes(gettext("your template contains errors")),
							addslashes(gettext("the template is not saved!"))
						));
					$output->end_javascript();
					$output->exit_buffer();
				} elseif ($dolint == 2) {
					/* Php binary was not found. Tell the user and exit. */
					$output = new Layout_output();
					$output->start_javascript();
						$output->addCode(sprintf("alert('%s');",
							addslashes(gettext("PHP binary was not found - could not perform lint check"))
						));
					$output->end_javascript();
					$output->exit_buffer();
				}

				break;
		}
		if ($req["cms"]["category"] == "main") {
			/* allow only one main page */
			$q = "update cms_templates set category = 'php' where category = 'main'";
			sql_query($q);
		}
		if ($req["id"]) {
			$q = sprintf("update cms_templates set category = '%s', title = '%s', data = '%s' where id = %d",
				$req["cms"]["category"], $req["cms"]["title"], $req["cms"]["data"], $req["id"]);
			sql_query($q);
			$id = $req["id"];
		} else {
			$q = sprintf("insert into cms_templates (category, title, data) values ('%s', '%s', '%s')",
				$req["cms"]["category"], $req["cms"]["title"], $req["cms"]["data"]);
			sql_query($q);
			$id = sql_insert_id("cms_templates");

			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(sprintf("
					parent.location.href = '?mod=cms&action=editTemplate&id=%d';
				", $id
				));
			$output->end_javascript();
			$output->exit_buffer();


		}
		$this->saveTemplateCache($id, $req["cms"]["category"], stripslashes($req["cms"]["data"]));
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				alert('%s');
			", addslashes(gettext("your template has been saved"))
			));
		$output->end_javascript();
		$output->exit_buffer();
		return $id;
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
			/*
			$q = "select parentpage from cms_data where id = ".(int)$pageid;
			$res = sql_query($q) or die($q);
			if (sql_num_rows($res)>0) {
			*/
			$ret = $this->getParent($pageid);
			if ($ret) {
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
		foreach ($this->repeat_table as $k=>$v) {
			$find[] = $k;
			$repl[] = $v;
		}
		$data = array();
		if ($pageid) {
			$q = sprintf("select * from cms_date where pageid = %d order by date_begin", $pageid);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["date_begin_h"] = date("d-m-Y H:i", $row["date_begin"]);
				$row["date_end_h"]   = date("d-m-Y H:i", $row["date_end"]);
				$row["repeating"]    = explode("|", $row["repeating"]);
				$row["repeating_h"]  = str_replace($find, $repl, $row["repeating"]);
				$row["repeating_h"]  = implode(", ", $row["repeating_h"]);

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
	private function dateOptionGenIndex($dateid) {
		$q = "delete from cms_date_index where dateid = ".$dateid;
		sql_query($q);

		$q = "select * from cms_date where id = ".$dateid;
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {

			$start       = $row["date_begin"];
			$start_day   = date("d", $start);
			$start_month = date("m", $start);
			$start_year  = date("Y", $start);

			$rp = explode("|", $row["repeating"]);
			$pageid = $row["pageid"];

			$end = $row["date_end"];

			$days = ($end-$start) / (60 * 60 * 24);
			$s = date("d", $start); //start day

			$ary = array("zo","ma","di","wo","do","vr","za");
			for ($i=$s; $i<=($days+$s); $i++) {

				//create current timestamp
				$ts = mktime(0,0,0,$start_month,$i,$start_year);

				//check for day of the week
				$weekday = $ary[ date("w", mktime(0,0,0,$start_month,$i,$start_year)) ];

				//weekday is same as given day
				if (in_array($weekday, $rp)) {
					$q = "insert into cms_date_index (pageid, dateid, datetime) values ($pageid, $dateid, $ts)";
					sql_query($q);
				}

				if (in_array("maand", $rp)) {
					if (date("d",$ts) == $start_day) {
						$q = "insert into cms_date_index (pageid, dateid, datetime) values ($pageid, $dateid, $ts)";
						sql_query($q);
					}
				}
				if (in_array("jaar",$rp)) {
					if (date("d",$ts) == $start_day && date("m", $current) == $start_month) {
						$q = "insert into cms_date_index (pageid, dateid, datetime) values ($pageid, $dateid, $ts)";
						sql_query($q);
					}
				}
				if (!$row["repeating"]) {
						$q = "insert into cms_date_index (pageid, dateid, datetime) values ($pageid, $dateid, $ts)";
						sql_query($q);
				}
			}
		}
	}

	public function dateOptionsItemDelete($id) {
		$q = sprintf("delete from cms_date where id = %d", $id);
		sql_query($q);

		$this->dateOptionGenIndex($id);
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
		$data["useMetaData"] = sql_result($res,0,"",2);

		$q = sprintf("select * from cms_metadef order by cms_metadef.group, cms_metadef.order");

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$q = sprintf("select * from cms_metadata where fieldid = %d and pageid = %d",
				$row["id"], $id);
			$res2 = sql_query($q);
			if (sql_num_rows($res2) == 1) {
				$row2 = sql_fetch_assoc($res2);
				#print_r($row2);
				$row["value"] = $row2["value"];
			}
			$row["default_value"] = $row["field_value"];
			$data["data"][$row["group"]][] = $row;
		}
		//try to grab crm classification
		$q = sprintf("SELECT * FROM cms_metadata WHERE fieldid = -1 AND pageid = %d", $id);
		$res = sql_query($q);
		if (sql_num_rows($res) == 1) {
			$row = sql_fetch_assoc($res);
			$data["data"][-1] = $row["value"];
		}
		return $data;
	}
	public function saveMetadata($req) {
		foreach ($req["meta"] as $k=>$v) {
			if (is_array($v)) {
				unset($v[0]);
				//cmscrmclassification is handles differently
				if ($k == "crmcla") {
					$k = -1;
					//get classifications
					$cla_data = new Classification_data();
					$cmscla = $cla_data->getClassifications("", 0, "", 1);
					foreach ($cmscla as $cla) {
						$cmsclalink[$cla["description"]] = $cla["id"];
					}
					foreach ($v as $key=>$value) {
						$v[$key] = $cmsclalink[$value];
					}
				}
				$v = implode("\n", $v);
			}
			if (!trim($v)) {
				/* cleanup */
				$q = sprintf("delete from cms_metadata where fieldid = %d and pageid = %d",
					$k, $req["id"]);
				sql_query($q);
			} else {
				$q = sprintf("select count(*) from cms_metadata where fieldid = %d and pageid = %d", $k, $req["id"]);
				$res = sql_query($q);
				if (sql_result($res,0,"",2)==0) {
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
		if (sql_result($res,0,"",2) == 0) {
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
			$i = 0;
			while ($row2 = sql_fetch_assoc($res2)) {
				$i++;
				$row["field_".$i] = $row2["user_value"];
				if (!in_array($row2["field_name"], $fields))
					$fields["field_".$i] = $row2["field_name"];
			}

			/* modify some values */
			unset($row["pageid"]);
			unset($row["visitor_hash"]);
			$row["ip_address"] = preg_replace("/\d{1,3}$/s", "x", $row["ip_address"]);
			$row["datetime_start"] = date("d-m-Y H:i", $row["datetime_start"]);
			$row["datetime_end"]   = date("d-m-Y H:i", $row["datetime_end"]);

			$data["data"][$row["id"]] = $row;
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
					$opts[]= gettext("recipient");
				if ($row["is_mailfrom"])
					$opts[]= gettext("sender");
				if ($row["is_mailsubject"])
					$opts[]= gettext("subject");
				if ($row["is_redirect"])
					$opts[]= gettext("result");

				$row["options"] = implode(", ", $opts);

				$this->meta_field_types["hidden"]   = gettext("hidden field");
				$this->meta_field_types["upload"]   = gettext("file upload field");
				$this->meta_field_types["date"]     = gettext("date field");
				$this->meta_field_types["datetime"] = gettext("datetime field");
				$this->meta_field_types["address"]  = gettext("address block");
				$this->meta_field_types["crm"]      = gettext("CRM block");

				$row["field_type_h"] = $this->meta_field_types[$row["field_type"]];
				$data[] = $row;
			}
			return $data;
		}
	}
	/* getFormSettings {{{ */
	/**
	 * Get settings linked to a form. Only used with crm forms right now
	 *
	 * @param int $id The formid to grab settings for
	 *
	 * @ return array All relevant records. empty array if no valid id given.
	 */
	public function getFormSettings($id) {
		$return = array();
		if ($id) {
			$sql = sprintf("SELECT * FROM cms_formsettings WHERE form_id = %d", $id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				// small migration step
				if ($row["settingsname"] == "user_classifications") {
					$row["settingsname"] = "user_classifications_1";
					$q = sprintf("UPDATE cms_formsettings SET settingsname='user_classifications_1' WHERE settingsname='user_classifications' AND form_id = %d", $id);
					$r = sql_query($q);
				}
				$return[] = $row;
			}
		}
		return $return;
	}
	/* }}} */

	/* saveFormSettings {{{ */
	/**
	 * Save form settings to db
	 *
	 * @param int $id Form id
	 * @param array $data The formsettings in an assoc array with key the fieldname in the database and value the data
	 *
	 * @return bool true on success, false on failure
	 */
	public function saveFormSettings($id, $data) {
		foreach ($data as $field=>$value) {
			//look if field is already in database. If so, update. If not, insert.
			$sql = sprintf("SELECT COUNT(*) AS count FROM cms_formsettings WHERE form_id = %d AND settingsname = '%s'", $id, $field);
			$res = sql_query($sql);
			$count = sql_result($res, 0);
			if (is_array($value)) {
				$value = implode(",", $value);
			}
			if ($count["count"]) {
				$sql = sprintf("UPDATE cms_formsettings SET settingsvalue = '%s' WHERE form_id = %d and settingsname = '%s'", $value, $id, $field);
				$res = sql_query($sql);
			} else {
				$sql = sprintf("INSERT INTO cms_formsettings VALUES (%d, '%s', '%s')", $id, $field, $value);
				$res = sql_query($sql);
			}
		}
		return true;
	}
	/* }}} */
	/* addUserCla {{{ */
	/**
	 * Add a new user classification settings field to formsettings.
	 * Will not return, but echo back javascript to save the settings.
	 * Should be used with a loadXML call
	 *
	 * @param int $number The new number
	 * @param int $id The pageid where the form is located
	 */
	public function addUserCla($number, $id) {
		//sanitize input
		$number = sprintf("%d", $number);
		$id = sprintf("%d", $id);
		if ($id && $number) {
			$sql = sprintf("INSERT INTO cms_formsettings VALUES (%d, 'user_classifications_%d', '')", $id, $number);
			$res = sql_query($sql);
			echo "document.getElementById('cmsFormSettings').submit();\n";
		} else {
			exit();
		}
	}
	/* }}} */
	/* removeUserCla {{{ */
	/**
	 * Remove a user classification settings field to formsettings.
	 * Will not return, but echo back javascript to save the settings.
	 * Should be used with a loadXML call
	 *
	 * @param string $name The name of the field
	 * @param int $id The pageid where the form is located
	 */
	public function removeUserCla($name, $id) {
		//sanitize input
		$name = sprintf("%s", $name);
		$id = sprintf("%d", $id);
		if ($id && $name) {
			$sql = sprintf("DELETE FROM cms_formsettings WHERE settingsname = '%s' AND form_id = %d", $name, $id);
			$res = sql_query($sql);
			$sql = sprintf("DELETE FROM cms_formsettings WHERE settingsname = 'name_%s' AND form_id = %d", $name, $id);
			$res = sql_query($sql);
			echo "document.getElementById('cmsFormSettings').submit();\n";
		} else {
			exit();
		}
	}
	/* }}} */

	public function getFormMode($id) {
		$q = sprintf("select mode from cms_form_settings where pageid = %d", $id);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			return sql_result($res,0,"",2);
		}
	}
	public function saveFormMode($req) {
		$q = sprintf("select count(*) from cms_form_settings where pageid = %d", $req["id"]);
		$res = sql_query($q);
		if (sql_result($res,0,"",2) == 0) {
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
			$output->addCode("parent.location.href = parent.location.href;");
		$output->end_javascript();
		$output->exit_buffer();

	}
	public function deleteFormData($pageid, $id) {
		//find out the type of the field
		$q = sprintf("SELECT field_type FROM cms_formulieren WHERE pageid = %d AND id = %d", $pageid, $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		//delete form item
		$q = sprintf("delete from cms_formulieren where pageid = %d and id = %d", $pageid, $id);
		sql_query($q);
		//remove the settings as well if it's a crm fieldtype
		if ($row["field_type"] == "crm") {
			$q = sprintf("DELETE FROM cms_formsettings WHERE form_id = %d", $pageid);
			sql_query($q);
		}
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
			$err++; $ret[]= gettext("no recipient field found");
		} elseif ($row[2]>1) {
			$err++; $ret[]= gettext("more then one recipient field found");
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

		$q = sprintf("select mode from cms_form_settings where pageid = %d",
			$pageid);
		$res = sql_query($q);
		$mode = sql_result($res,0);
		/* if mode = enquete */
		if ($mode == 2) {
			$q = sprintf("select count(*) from cms_formulieren where field_type = 'upload'
				and pageid = %d", $pageid);
			$res = sql_query($q);
			if (sql_result($res,0) > 0) {
				$err++;
				$ret[] = gettext("Cannot use field type upload combined with option enquete");
			}
		}

		if ($err==0) {
			$ret[]= gettext("no errors found in this form configuration");
		} else {
			foreach ($ret as $k=>$v) {
				$ret[$k] = "<font color='red'>".$v."</font>";
			}
		}
		return $ret;
	}

	public function getChildPages($pageid, $skip_permissions_check=0) {
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

		$this->getChilds($pageid, $data, $perms, $denied, 0, $skip_permissions_check);

		$return = array(
			"data"   => $data,
			"denied" => $denied
		);
		return $return;
	}

	public function getChilds($pageid, &$data, &$perms, &$denied, $level, $skip_permissions_check=0) {
		$q = sprintf("select id, pageTitle from cms_data where parentpage = %d", $pageid);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
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
			if ($ok || $skip_permissions_check)
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
		return sql_result($res, 0,"",2);
	}

	public function deletePages($ids, $internal_request=0) {
		set_time_limit(60*60);
		session_write_close();

		/* retreive all child pages if page is part of 'deleted items' */
		/* get high parent 'deleted items' */
		$cids = array();
		$ids = explode(",", $ids);

		$del_hp = $this->getSpecialPageId("D");

		if ($this->getHighestParent($ids[0]) == $del_hp) {
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
					$q[] = sprintf("delete from cms_feedback where page_id = %d", $id);
					$q[] = sprintf("delete from cms_form_results where pageid = %d", $id);
					$q[] = sprintf("delete from cms_license_siteroots where pageid = %d", $id);
					$q[] = sprintf("delete from cms_list where pageid = %d", $id);
					$q[] = sprintf("delete from cms_metadata where pageid = %d", $id);
					$q[] = sprintf("delete from cms_permissions where pid = %d", $id);
					$q[] = sprintf("delete from cms_data where id = %d", $id);
					foreach ($q as $sql) {
						sql_query($sql);
					}

					$this->eraseGalleryCache($id);
				}
			}

		} else {
			/* if not deleted items, move to deleted items and set custom permissions */
			foreach ($ids as $id) {

				/* delete siteroot info (if any) */
				$q = sprintf("delete from cms_license_siteroots where pageid = %d", $id);
				sql_query($q);

				/* delete old permissions */
				$q = sprintf("delete from cms_permissions where pid = %d and uid = '%d'", $id, $_SESSION["user_id"]);
				sql_query($q);

				$q = sprintf("insert into cms_permissions (pid, uid, editRight, viewRight, manageRight,
					deleteRight) values (%d, '%d', 1, 1, 0, 1)", $id, $_SESSION["user_id"]);
				sql_query($q);

				$q = sprintf("update cms_data set isSpecial = '', apEnabled = %d, parentPage = %d where id = %d", $del_hp, $del_hp, $id);
				sql_query($q);

				/* now update all child pages */
				$target_ids = array(-1);
				$target = $this->getChildPages($id, 1);
				if (is_array($target["data"])) {
					foreach ($target["data"] as $k=>$v) {
						$target_ids[] = $v["id"];
					}
				}
				$q = sprintf("update cms_data set apEnabled = %d, date_last_action = %d where id IN (%s)",
					$del_hp, time(), implode(",", $target_ids));
				sql_query($q);
			}
		}

		if (!$internal_request) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					//opener.cmsReload();
					//setTimeout('closepopup();', 200);
					parent.document.location=href = '?mod=cms';

				");
			$output->end_javascript();
			$output->exit_buffer();
		}
	}

	private function eraseGalleryCache($id) {
		$dir = sprintf("%s/gallery/%d", $GLOBALS["covide"]->filesyspath, $id);
		if (file_exists($dir)) {
			$files = scandir($dir);
			foreach ($files as $file) {
				$f = sprintf("%s/%s", $dir, $file);
				if (!is_dir($f))
					unlink($f);
			}
			rmdir($dir);
		}
	}
	public function getGalleryData($id, $filter=array()) {
		$data = array();
		$esc = sql_syntax("escape_char");

		if ($filter["search"]) {
			$like = sql_syntax("like");
			$fq.= sprintf(" AND (%1\$s%3\$s%1\$s %2\$s '%%%4\$s%%'
				OR %1\$s%5\$s%1\$s %2\$s '%%%4\$s%%'
				OR %1\$s%6\$s%1\$s %2\$s '%%%4\$s%%') ",
				$esc, $like, "file", $filter["search"], "description", "url");
		}
		$q = sprintf("select * from cms_gallery_photos where pageid = %d %s order by ".$esc."order".$esc.", file", $id, $fq);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["file_short"] = basename($row["file"]);
			if (!$row["rating"])
				$row["rating_h"] = "0x";
			else
				$row["rating_h"] = $row["rating"]."x";

			/* if banners */
			if ($id == -1) {
				if ($filter["s_timestamp_year"] && $filter["e_timestamp_year"]) {
					$start = mktime(0,0,0,
						$filter["s_timestamp_month"],
						$filter["s_timestamp_day"],
						$filter["s_timestamp_year"]
					);
					$end = mktime(0,0,0,
						$filter["e_timestamp_month"],
						$filter["e_timestamp_day"]+1,
						$filter["e_timestamp_year"]
					);
					$sq = sprintf(" and datetime between %d and %d ", $start, $end);
				} else {
					$sq = "";
				}
				$q = sprintf("select sum(visited) as views, sum(clicked) as clicks from
					cms_banner_views where banner_id = %d %s", $row["id"], $sq);
				$res2 = sql_query($q);
				$row2 = sql_fetch_assoc($res2,2);

				$row2["views"]  = (int)$row2["views"];
				$row2["clicks"] = (int)$row2["clicks"];

				$row["views"]  = $row2["views"]."x";
				$row["visits"] = $row2["clicks"]."x";

				if ($filter["highlight"] == $row["id"])
					$row["highlight"] = 1;
			}
			$row["description_h"] = trim(strip_tags($row["description"]));

			$filename = sprintf("%s/%s/%s/%d_%s.jpg", $GLOBALS["covide"]->filesyspath, "gallery", $id, $row['id'], "full");
			if (file_exists($filename)) {
				$info = getimagesize($filename);
				$row['resolution'] = sprintf('%dx%d', current($info), next($info));
			}
			$data[$row["id"]]=$row;
		}
		return $data;
	}
	public function getGalleryItem($id) {
		$q = sprintf("select * from cms_gallery_photos where id = %d", $id);
		$res = sql_query($q);
		$data = sql_fetch_assoc($res);
		$pageid = ($data['pageid'] == -1) ? 'banner' : $data['pageid'];
		$filename = sprintf("%s/%s/%s/%d_%s.jpg", $GLOBALS["covide"]->filesyspath, "gallery", $pageid, $id, "full");
		if (file_exists($filename)) {
			$info = getimagesize($filename);
			$data['resolution'] = sprintf('%dx%d', current($info), next($info));
		}
		$data['preview_file'] = sprintf('/cmsgallery/page%d/%d&size=small&file=%s&m=%d',
			$data['pageid'],
			$id,
			basename($filename),
			filemtime($filename)
		);
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
	public function saveGallerySettings($req, $no_redir=0) {
		/* no_redir can be used for batch imports */
		$q = sprintf("select * from cms_gallery where pageid = %d", $req["id"]);
		$res = sql_query($q);

		if (sql_num_rows($res)==0) {
			$q = sprintf("insert into cms_gallery (gallerytype, cols, fullsize,
				bigsize, thumbsize, pageid, rows, font, font_size) values (%d, %d, %d, %d, %d, %d, %d, '%s', %d)",
				$req["cms"]["gallerytype"], $req["cms"]["cols"], $req["cms"]["fullsize"],
				$req["cms"]["bigsize"], $req["cms"]["thumbsize"], $req["id"], $req["cms"]["rows"],
				$req["cms"]["font"], $req["cms"]["fontsize"]);
			sql_query($q);
		} else {
			$row = sql_fetch_assoc($res);
			$thumbsize = $row["thumbsize"];
			$bigsize   = $row["bigsize"];
			$q = sprintf("update cms_gallery set gallerytype = %d, cols = %d, fullsize = %d,
				bigsize = %d, thumbsize= %d, rows = %d, font = '%s', font_size = %d where pageid = %d",
				$req["cms"]["gallerytype"], $req["cms"]["cols"], $req["cms"]["fullsize"],
				$req["cms"]["bigsize"], $req["cms"]["thumbsize"], $req["cms"]["rows"],
				$req["cms"]["font"], $req["cms"]["fontsize"], $req["id"]);
			sql_query($q);

			if (!$no_redir && ($thumbsize != $req["cms"]["thumbsize"] || $bigsize != $req["cms"]["bigsize"])) {
				/* update last mtime */
				$q = sprintf("update cms_gallery set last_update = %d where pageid = %d",
					time(), $req["id"]);
				sql_query($q);

				/* prevent session lockup */
				session_write_close();
				$items = $this->getGalleryData($req["id"]);
				foreach ($items as $k=>$v)
					$this->createCache($k);
			}
		}

		if (!$no_redir) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(sprintf(" location.href = '?mod=cms&action=cmsgallery&id=%d';", $req["id"]));
			$output->end_javascript();
			$output->exit_buffer();
		}
	}
	/* createCache {{{ */
	/**
	 * Create cached thumbnails for galleryitems.
	 * These images will be jpg default quality in _full, _medium and _small format
	 *
	 * @param int $id The fileid as in the gallery_images database table
	 */
	public function createCache($file_id) {
		require(self::include_dir."dataCreateCache.php");
	}
	/* }}} */
	/* convertThumb {{{ */
	/**
	 * Create a jpg version from a file uploaded as galleryitem
	 *
	 * @param string $filename The filename to process. This will be replaced by the jpg generated version
	 */
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
	/* }}} */
	/* galleryUpload {{{ */
	/**
	 * Process freshly uploaded files for a gallery
	 *
	 * The file will be uploaded and saved, and a full set of jpg thumbnails in various sizes will be created.
	 *
	 * @param array $req Request parameters from the upload field
	 * @param int $force_no_upload if set we did not upload a file, but copy one.
	 */
	public function galleryUpload($req, $force_no_upload=0) {
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
				if (in_array(strtolower($ext), array("jpg", "png", "gif", "jpeg"))) {
					/* get order + 1 */
					$esc = sql_syntax("escape_char");
					$q = sprintf("select max(%1\$sorder%1\$s) from cms_gallery_photos where pageid = %2\$d", $esc, $req["id"]);
					$res = sql_query($q);
					$order = sql_result($res,0,"",2)+1;

					/* insert file into dbase */
					$q = sprintf("insert into cms_gallery_photos (pageid, file, description, %1\$sorder%1\$s) values ", $esc);
					$q.= sprintf("(%d, '%s', '%s', %d)",
						$req["id"], $name, $req["filedata"]["description"], $order);
					sql_query($q);
					$dbid = sql_insert_id("cms_gallery_photos");

					/* move original file to destination */
					//if req[id] is -1 it's a banner
					if ($req["id"] == "-1") {
						$pageid = "banner";
					} else {
						$pageid = $req["id"];
					}
					$dest = sprintf("%s/%s/%s/%d_orig.%s",
						$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $dbid, $ext);

					// if the target directory does not exist, create it
					if (!file_exists(dirname($dest)))
						mkdir(dirname($dest), 0777, 1);

					if ($force_no_upload)
						copy($tmp_name, $dest);
					else
						move_uploaded_file($tmp_name, $dest);

					// now create a full version that is jpg
					// we need this for the creation of the cached thumbnails
					$dest_jpg = sprintf("%s/%s/%s/%d_full.jpg",
						$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $dbid);
					copy($dest, $dest_jpg);
					//convert orig input to jpeg, max quality
					$this->convertThumb($dest_jpg);

					//create cached thumbnails
					$this->createCache($dbid);
				}
			}
		}
	}
	/* }}} */
	/* cmsGalleryItemDelete {{{ */
	/**
	 * Delete a galleryitem, including the thumbnails etc
	 *
	 * @param array $req Request array
	 */
	public function cmsGalleryItemDelete($req) {
		if ($req['id'] == -1) {
			$req['id'] = 'banner';
		}

		if ($req["item"] == -1) {
			$q = sprintf("select id from cms_gallery_photos where pageid = %d",
				$req["pageid"]);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$file = sprintf("%s/%s/%s/%d_*",
					$GLOBALS["covide"]->filesyspath, "gallery", $req["id"], $row["id"]);
				$cmd = sprintf('rm -f %s', $file);
				exec($cmd, $ret, $retval);

				$q = sprintf("delete from cms_gallery_photos where id = %d", $row["id"]);
				sql_query($q);
			}
		} else {
			$file = sprintf("%s/%s/%s/%d_*",
				$GLOBALS["covide"]->filesyspath, "gallery", $req["id"], $req["item"]);
			$cmd = sprintf('rm -f %s', $file);
			exec($cmd, $ret, $retval);

			$q = sprintf("delete from cms_gallery_photos where id = %d", $req["item"]);
			sql_query($q);
		}
	}
	/* }}} */
	/* cmsGalleryItemSave {{{ */
	/**
	 * Save changes made to a gallery item
	 *
	 * @param array $req Request data
	 */
	public function cmsGalleryItemSave($req) {
		$esc = sql_syntax("escape_char");
		$q = sprintf("update cms_gallery_photos set file = '%2\$s', description = '%3\$s',
			%1\$sorder%1\$s = %4\$d, rating=%6\$d, url='%7\$s' where id = %5\$d",
			$esc, $req["cms"]["file"], $req["cms"]["description"], $req["cms"]["order"], $req["id"],
			$req["cms"]["rating"], $req["cms"]["url"]);
		sql_query($q);
	}
	/* }}} */
	/* cmsGalleryItemSwitch {{{ */
	/**
	 * Move a galleryitem up or down in the order of the gallery it's in
	 *
	 * @param array key id => galleryid, key itemid => photoid, key direction => up/down
	 */
	public function cmsGalleryItemSwitch($req) {
		$data = $this->getGalleryData($req["id"]);
		$data_keys = array();
		foreach ($data as $k=>$v) {
			$data_keys[count($data_keys)+1]=$v["id"];
		}

		$pos = array_search($req["itemid"], $data_keys);

		if ($req["direction"] == "up") {
			if ($pos > 1) {
				$prev = $data_keys[$pos-1];
				$data_keys[$pos] = $prev;
				$data_keys[$pos-1] = $req["itemid"];
			}
		} else {
			if ($pos < count($data_keys)) {
				$next = $data_keys[$pos+1];
				$data_keys[$pos] = $next;
				$data_keys[$pos+1] = $req["itemid"];
			}
		}
		$esc = sql_syntax("escape_char");
		foreach($data_keys as $position => $photoid) {
			$q = sprintf("UPDATE cms_gallery_photos SET %1\$sorder%1\$s = %2\$d WHERE id = %3\$d", $esc, $position, $photoid);
			sql_query($q);
		}
	}
	/* }}} */
	/* loadGalleryFile {{{ */
	/**
	 * Show a gallery item.
	 * Beware, if the pageid in the database is -1 it will look for the file in gallery/banner/
	 *
	 * @param int $id The galleryphoto to show
	 * @param string $size The size of the thumbnail, can be full, medium or small
	 */
	public function loadGalleryFile($id, $size) {

		$q = sprintf("select pageid from cms_gallery_photos where id = %d", $id);
		$res = sql_query($q);
		$pageid = sql_result($res,0);
		if ($pageid == -1) {
			$pageid = "banner";
			$f = $this->getGalleryItem($id);
			$name = $f["file"];
			$filesys = new Filesys_data();
			$ext = $filesys->get_extension($name);
			$origfile = sprintf("%s/%s/%s/%d_orig.%s", $GLOBALS["covide"]->filesyspath, "gallery", $pageid, $id, $ext);
			if (file_exists($origfile) && $ext == "gif") {
				$mimetype = strtolower($filesys->detectMimetype($origfile));
				$file = $origfile;
			} else {
				$mimetype = "image/jpeg";
				if ($ext != "jpg") {
					$name = str_replace($ext, "jpg", $name);
				}
				$file = sprintf("%s/%s/%s/%d_%s.jpg",
					$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $id, $size);

				/* if new file is not found, look at the old location */
				if (!file_exists($file)) {
					$file = sprintf("%s/%s/%d_%s.jpg",
						$GLOBALS["covide"]->filesyspath, "gallery", $id, $size);
				}
			}
		} else {
			$file = sprintf("%s/%s/%s/%d_%s.jpg",
				$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $id, $size);

			/* if new file is not found, look at the old location */
			if (!file_exists($file)) {
				$file = sprintf("%s/%s/%d_%s.jpg",
					$GLOBALS["covide"]->filesyspath, "gallery", $id, $size);
			}
		}

		$q = sprintf("select last_update from cms_gallery where pageid IN (
			select pageid from cms_gallery_photos where id = %d)", $id);
		$res = sql_query($q);
		$last_update = sql_result($res,0,"",2);
		if (!$last_update)
			$last_update = mktime(0,0,0,1,1,2000);

		/* Checking if the client is validating his cache and if it is current. */
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_update)) {
			/* Client's cache IS current, so we just respond '304 Not Modified'. */
			header('Last-Modified: '.date('r', $last_update), true, 304);
			header('Connection: close');
		} else {
			ob_clean();
			header("Content-Transfer-Encoding: binary");
			header("Expires: ".date('r', mktime() + self::CACHE_CMSFILE_EXPIRES), true);
			header('Last-Modified: '.date('r', mktime() - 3600), true, 200);
			header(sprintf('Cache-Control: must-revalidate, max-age=%1$d, s-maxage=%1$d', self::CACHE_CMSFILE_EXPIRES));
			header("Pragma: public");
		}

		if ($size == "full") {
			// find out if we have a file called _orig etc
			$f = $this->getGalleryItem($id);
			$name = $f["file"];
			$filesys = new Filesys_data();
			$ext = $filesys->get_extension($name);
			$origfile = sprintf("%s/%s/%s/%d_orig.%s", $GLOBALS["covide"]->filesyspath, "gallery", $pageid, $id, $ext);
			if (file_exists($origfile)) {
				$mimetype = strtolower($filesys->detectMimetype($origfile));
				$file = $origfile;
			} else {
				$mimetype = "image/jpeg";
				if ($ext != "jpg") {
					$name = str_replace($ext, "jpg", $name);
				}
			}
			if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
				header("Content-Disposition: filename=\"".$name."\"");
			} else {
				header("Content-Disposition: attachment; filename=\"".$name."\"");
			}
		}
		header("Content-Type: ".$mimetype);
		echo file_get_contents($file);
		exit();
	}
	/* }}} */
	public function addSiteRoot($name) {
		$q = sprintf("insert into cms_data (parentPage, pageTitle) values (0, '%s')", $name);
		sql_query($q);
	}
	public function saveSiteInfo($req) {
		if (is_numeric($req["siteroot"])) {
			$table = "cms_license_siteroots";
			$baseq = "id = id";
		} else {
			$table = "cms_license";
			$baseq = sprintf("cms_manage_hostname = '%s'", $req["cms"]["cms_manage_hostname"]);
			$baseq .= sprintf(", cms_shop_page = %d", $req["cms"]["cms_shop_page"]);
			$baseq .= sprintf(", cms_shop_results = %d", $req["cms"]["cms_shop_results"]);
		}

		$q = sprintf("update %s set %s, cms_favicon = '%s', cms_logo = '%s', cms_defaultpage = %d,
			google_verify = '%s', google_analytics = '%s', letsstat_analytics = '%s',
			piwik_analytics = %d, cms_hostnames = '%s'", $table, $baseq,
			$req["cms"]["cms_favicon"], $req["cms"]["cms_logo"],
			$req["cms"]["cms_defaultpage"],	$req["cms"]["google_verify"],
			$req["cms"]["google_analytics"], $req["cms"]["letsstat_analytics"],
			$req["cms"]["piwik_analytics"], $req["cms"]["cms_hostnames"]);

		foreach ($req["cms"] as $k=>$v) {
			if ($k != "isPublic")
				if ($k == "search_use_pagetitle" || $k == "cms_defaultpage")
					$q.= sprintf(", %s = %d ", $k, $v);
				else
					$q.= sprintf(", %s = '%s' ", $k, $v);
		}

		if (is_numeric($req["siteroot"])) {
			$q.= sprintf(" where pageid = %d", $req["siteroot"]);

			if ($req['siteroot_title']) {
				$q2 = sprintf("update cms_data set pageTitle = '%s' where id = %d", $req['siteroot_title'], $req['siteroot']);
				sql_query($q2);
			}
		}
		sql_query($q);

		if ($req["cms"]["isPublic"])
			$new = 1;
		else
			$new = 0;

		if (is_numeric($req["siteroot"]))
			$q = sprintf("update cms_data set isPublic = %d where id = %d", $new, $req["siteroot"]);
		elseif ($req["siteroot"])
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
			if (!preg_match("/^(#)|(mailto\:)/s", $v[0])) {
				/* we got data */
				$i++;
				if ($i==1)
					$fields = $v;
				else
					foreach ($v as $c=>$f)
						$data[$i][$fields[$c]] = urldecode($f);
			}
		}

		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		$cms_data->sitemap_cache["user_perms"] = $perms;
		foreach ($data as $k=>$v) {
			if ($v["result"] == "200 OK" || $v["valid"] == "True") {
				unset($data[$k]);
			} else {
				/* check access permissions */
				$pid = (int)preg_replace("/^.*\&page=(\d{1,})$/s", "$1", urldecode(basename($v["parentname"])));
				$r = $this->getUserPermissions($pid, $_SESSION["user_id"]);
				if ($r["editRight"] || $perms["xs_cms_level"] >= 2) {
					$data[$k]["pageid"]    = $pid;
					$data[$k]["url"]       = sprintf("<a target=\"_blank\" href=\"%1\$s\">%1\$s</a>", $v["url"]);
				} else {
					/* no permissions */
					unset($data[$k]);
				}
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

			file_put_contents("/tmp/linkchecker", $cmd);

			exec($cmd, $ret, $rv);
		}
		$output->start_javascript();
			$output->addCode("location.href = '?mod=cms&action=linkchecker';");
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
		require(self::include_dir."functions.php");
	}
	public function scanForFiles(&$files) {
		$like = sql_syntax("like");
		if (is_array($files)) {
			foreach ($files as $k=>$file) {
				$q = sprintf("select id from cms_data where pageData REGEXP 'cmsfile/%d[^0-9]' order by id",
					$file["id"]);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res,2)) {
					$files[$k]["pages"][] = array(
						"ispage" => 1,
						"type"   => "page",
						"name"   => gettext("pageid"),
						"id"     => $row["id"]
					);
				}
				$q = sprintf("select id from cms_templates where data REGEXP 'cmsfile/%d[^0-9]' order by id",
					$file["id"]);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res,2)) {
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
	public function getAbbreviations($id=0) {
		$data = array();
		if ($id)
			$q = sprintf("select * from cms_abbreviations where id = %d", $id);
		else
			$q = sprintf("select * from cms_abbreviations order by abbreviation");

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["lang_h"] = $row["lang"];
			$row["lang"] = explode(",", $row["lang"]);
			$row['type_h'] = $this->abbr_field_types[(int)$row['itemtype']];
			$data[] = $row;
		}
		return $data;
	}
	public function saveAbbreviation($req) {
		$req["cms"]["lang"] = @implode(",", $req["cms"]["lang"]);
		if ($req["id"]) {
			$q = sprintf("update cms_abbreviations set lang = '%s', abbreviation = '%s', description = '%s', itemtype = %d
				where id = %d", $req["cms"]["lang"], $req["cms"]["abbreviation"], $req["cms"]["description"], 
				$req['cms']['itemtype'], $req["id"]);
		} else {
			$q = sprintf("insert into cms_abbreviations (lang, abbreviation, description, itemtype) values ('%s', '%s', '%s', %d)",
				$req["cms"]["lang"], $req["cms"]["abbreviation"], $req["cms"]["description"], $req['cms']['itemtype']);
		}
		sql_query($q);
	}
	public function deleteAbbreviation($id) {
		$q = sprintf("delete from cms_abbreviations where id = %d", $id);
		sql_query($q);
	}
	public function getMailings($id) {
		$data = array();
		$q = sprintf("select * from cms_mailings where pageid = %d order by datetime", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["datetime_h"] = date("d-m-Y H:i", $row["datetime"]);
			$data[] = $row;
		}
		return $data;
	}
	public function handleUpload() {
		/* if file position is filled with a tmp_name */
		$email_data = new Email_data();

		if ($_FILES["binFile"]["error"][0] == UPLOAD_ERR_OK &&
			$_FILES["binFile"]["tmp_name"][0]) {

			$data = explode("\n", file_get_contents($_FILES["binFile"]["tmp_name"][0]));
			/* a quick and dirty filter, the csv only has one column */
			$data = preg_replace("/(\r)|(\t)|(,)|(;)|(\")/s", "", $data);
			if ($_REQUEST["skipfirst"])
				unset($data[0]);

			foreach ($data as $k=>$email) {
				$email = trim($email);
				if (!$email || $email_data->validateEmail($email) == false)
					unset($data[$k]);
				else
					$data[$k] = $email;
			}
			$data = implode(", ", $data);
		}
		return $data;
	}

	public function getHostnameByPage($id) {
		$hp = $this->getHighestParent($id);
		$s = $this->getCmsSettings($hp);
		if (!$s["cms_hostnames"])
			$s = $this->getCmsSettings();
		if (!$s["cms_hostnames"])
			$s["cms_hostnames"] = $_SERVER["HTTP_HOST"];

		$host = explode("\n", $s["cms_hostnames"]);
		return trim($host[0]);
	}
	public function getEmailByPage($id) {
		$hp = $this->getHighestParent($id);
		$s = $this->getCmsSettings($hp);
		if (!$s["search_email"])
			$s = $this->getCmsSettings();
		if (!$s["search_email"])
			$s["search_email"] = $GLOBALS["covide"]->license["email"];

		return trim($s["search_email"]);
	}

	public function send_mailing($page, $emails, $length="25") {
		$email_data = new Email_data();
		$data = $this->getPageById($page);

		$content = $email_data->html2text($data["pageData"]);
		$content = mb_substr($content, 0, ceil(mb_strlen($content)*($length/100)));

		$html = sprintf("<b>%s</b> (%s)<br><br>", $data["pageTitle"], date("d-m-Y H:i"));
		$html.= $content;

		$host = $this->getHostnameByPage($page);
		$uri = "http://".$host."/page/";
		if ($data["pageAlias"])
			$uri.=$data["pageAlias"];
		else
			$uri.= $page;
		$uri.= ".htm";

		$html = preg_replace("/\[\d{1,}\]/s", "", $html);
		$html = nl2br($html)." . . . . . <br><br>";
		$html.= sprintf("<a href='%s' target='_new'>click here for the complete version</a>",
			$uri);

		$html = $email_data->stylehtml($html);
		$from = $this->getEmailByPage($page);

		$headers  = "MIME-Version: 1.0"."\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8"."\r\n";
		$headers .= "From: ".$from."\r\n";

		$emails = explode(",", $emails);
		foreach ($emails as $email) {
			mail($email, strip_tags($data["pageTitle"]), $html, $headers, "-f".$from);
		}

		//insert into db
		$q = sprintf("insert into cms_mailings (pageid, datetime, email) values (%d, %d, '%s')",
			$page, time(), implode(", ",$emails));
		sql_query($q);

		header("Location: index.php?mod=cms&action=mailings&id=".$page);
		exit();
	}

	public function dateOptionsItemSave($req) {

		$cms =& $req["cms"];
		$cms["start"] = mktime(
			$cms["s_timestamp_hour"], $cms["s_timestamp_min"], 0,
			$cms["s_timestamp_month"], $cms["s_timestamp_day"], $cms["s_timestamp_year"]
		);
		$cms["end"] = mktime(
			$cms["e_timestamp_hour"], $cms["e_timestamp_min"], 0,
			$cms["e_timestamp_month"], $cms["e_timestamp_day"], $cms["e_timestamp_year"]
		);

		$repeat = array();
		if (is_array($cms["repeating"])) {
			foreach ($cms["repeating"] as $k=>$v) {
				if ($v)
					$repeat[] = $k;
			}
		}
		$cms["repeating"] = @implode("|", $repeat);

		if ($req["id"]) {
			$q = sprintf("update cms_date set date_begin = %d, date_end = %d,
				description = '%s', repeating = '%s' where id = %d",
				$cms["start"], $cms["end"], $cms["description"], $cms["repeating"],
				$req["id"]);
			sql_query($q);
		} else {
			$q = sprintf("insert into cms_date (pageid, date_begin, date_end,
				description, repeating) values (%d, %d, %d, '%s', '%s')",
				$req["pageid"], $cms["start"], $cms["end"], $cms["description"], $cms["repeating"]);
			sql_query($q);
			$req["id"] = sql_insert_id("cms_date");
		}

		$this->dateOptionGenIndex($req["id"]);

		echo "<script>parent.location.href = parent.location.href</script>";
	}

	public function createInlineThumb($id, $width, $height, $pageid) {
		/* lookup file in cache */
		$q = sprintf("select * from cms_image_cache where img_id = %d and height = %d
			and width = %d", $id, $height, $width);
		$res = sql_query($q);
		if (sql_num_rows($res) == 1) {
			$row = sql_fetch_assoc($res);
			if ($row["use_original"])
				return sprintf("/cmsfile/%d", $row["img_id"]);
			else
				/* thumb is available */
				$ret = sprintf("/cmscache/%d", $row["id"]);
		} else {
			/* create thumb */
			require_once(self::include_dir."createInlineThumb.php");
			$ret = CMS_data_createinlinethumb::createInlineThumb($id, $width, $height);
		}
		return $ret;
	}

	public function removeThumbCache($imgid) {
		$q = sprintf("select * from cms_image_cache where img_id = %d", $imgid);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->removeThumbCacheFile($row["id"]);
		}
	}

	public function removeThumbCacheFile($id) {
		$filename = sprintf("%s/%s/%d.jpg",
			$GLOBALS["covide"]->filesyspath, "cmscache", $id);

		/* remove file data */
		if (file_exists($filename))
			@unlink($filename);

		$q = sprintf("delete from cms_image_cache where id = %d", $id);
		sql_query($q);
	}

	public function getAliasHistory($id) {
		$data = array();
		$q = sprintf("select * from cms_alias_history where pageid = %d order by datetime desc",
			$id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["datetime_h"] = date("d-m-Y H:i", $row["datetime"]);
			$row["alias_h"] = $row["alias"].".htm";
			$data[] = $row;
		}
		return $data;
	}

	public function cmsDeleteAliasHistory($id) {
		$q = sprintf("delete from cms_alias_history where id = %d", $id);
		sql_query($q);
	}

	public function getInternalStartPoints() {
		$data = array();
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$q = sprintf("select id, pageTitle, pageAlias, datePublication from cms_data where useInternal = 1 order by pageTitle");
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$xs = 0;
			if ($user_info["xs_cms_level"] >= 2) {
				$xs = 1;
			} else {
				$r = $this->getUserPermissions($row["id"], $_SESSION["user_id"]);
				if ($r["viewRight"])
					$xs = 1;
			}
			if ($xs)
				$data[$row["id"]] = array(
					"name" => $row["pageTitle"],
					"link" => sprintf("/page/%s.htm", ($row["pageAlias"]) ? $row["pageAlias"]:$row["id"]),
					"date" => date("d-m-Y")
				);
		}
		return $data;
	}

	public function validateSitemap($uri, $schema) {
		$cmd = sprintf("xmllint --htmlout --schema classes/tpl/inc/%s.xsd %s", $schema, $uri);
		exec($cmd, $ret, $retval);

		$ret = implode("\n", $ret);
		$ret = str_replace(array("<",">"), array("&lt;", "&gt;"), $ret);
		if ($retval == 0) {
			$ret = sprintf("<font color='green'><PRE>%s</PRE></font>", $ret);
		} else {
			$ret = sprintf("<font color='red'><PRE>%s</PRE></font>", $ret);
		}
		return $ret;
	}

	private function deleteTemplateCache($id, $type) {
		$tmp = sprintf("%s%d.%s", $this->getCmsTmpPrefix(), $id, $type);

		if (in_array($type, array("js", "css")) && is_writable(dirname($tmp)))
			@unlink($tmp);
	}

	public function deleteTemplate($id) {
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		if ($perms["xs_cms_level"] < 3)
			die("access denied");

		$template = $this->getTemplateById($id);
		$this->deleteTemplateCache($id, $template["category"]);

		$q = sprintf("delete from cms_templates where id = %d", $id);
		sql_query($q);
	}

	public function cmsImportExec($req) {
		require(self::include_dir."cmsImportExec.php");
	}

	public function getCounter($name) {
		$q = sprintf("select counter1 from cms_counters where name = '%s'", addslashes($name));
		$res = sql_query($q);
		if (sql_num_rows($res) == 0) {
			$q = sprintf("insert into cms_counters (name, counter1) values ('%s', 0)", addslashes($name));
			sql_query($q);
			return 0;
		} else {
			return sql_result($res,0);
		}
	}
	public function getHitCounters() {
		$data = array();
		$q = "select * from cms_counters order by name";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row;
		}
		return $data;
	}
	public function raiseCounter($name) {
		$c = $this->getCounter($name);
		$q = sprintf("update cms_counters set counter1 = %d where name = '%s'", $c+1, addslashes($name));
		sql_query($q);
		return $c+1;
	}

	public function removeCounter($id) {
		$q = sprintf("delete from cms_counters where id = %d", $id);
		sql_query($q);
	}

	public function getFeedbackItems($pageid, $start="", $pagesize="") {
		$data = array();
		$q = sprintf("select count(*) from cms_feedback where page_id = %d", $pageid);
		$res = sql_query($q);
		$data["count"] = sql_result($res,0);

		$q = sprintf("select * from cms_feedback where page_id = %d order by datetime desc",
			$pageid);
		$res = sql_query($q, "", $start, $pagesize);
		while ($row = sql_fetch_assoc($res)) {
			$data["data"][$row["id"]] = $row;
		}
		return $data;
	}
	public function registrationCheckErrors($data) {
		$email_data = new Email_data();

		$q = sprintf("select count(*) from cms_users where username like '%s'", $data["username"]);
		$res_user = sql_query($q);

		$q = sprintf("select count(*) from cms_users where email like '%s'", $data["email"]);
		$res_email = sql_query($q);

		if (mb_strlen($data["username"]) < 2)
			$err = gettext("username must be at least 2 characters long");
		elseif ($data["email"] != $data["email_cf"])
			$err = gettext("email addresses does not match");
		elseif (!$email_data->validateEmail($data["email"]))
			$err = gettext("email address is not valid");
		elseif ($data["password"] != $data["password_cf"])
			$err = gettext("passwords does not match");
		elseif (mb_strlen($data["password"]) < 5)
			$err = gettext("password has to be at least 5 characters long");
		elseif (sql_result($res_user,0) > 0)
			$err = gettext("the requested username is not available");
		elseif (sql_result($res_email,0) > 0) {
			$eo = new Layout_output();
			$eo->addCode(gettext("the requested email address is already in use"));
			$eo->addTag("br");
			$eo->addTag("br");
			$eo->insertTag("a", gettext("click here to recover your password"), array(
				"href" => sprintf("javascript: recoverPassword('%s', '%s', '%s');",
					$data["uri"], $data["siteroot"], $data["email"]
				)
			));
			$err = $eo->generate_output();
		}



		if ($err)
			return $err;
		else
			return true;
	}

	public function saveRegistration($data) {
		$hash = md5(session_id().rand().time().$data["username"]);

		$q = sprintf("insert into cms_users (confirm_hash, username, password, email, registration_date, is_enabled)
			values ('%s', '%s', '%s', '%s', %d, 1)", $hash, $data["username"], $data["password"],
			$data["email"], time());
		sql_query($q);
		$new_id = sql_insert_id("cms_users");

		if (preg_match("/^https:\/\//si", $data["uri"]))
			$ssl = 1;

		$data["uri_clean"] = explode("/", preg_replace("/^http(s){0,1}:\/\//si", "", $data["uri"]));
		$data["uri_clean"] = $data["uri_clean"][0];
		$host              = $data["uri_clean"][0];

		if ($ssl)
			$data["uri_clean"] = "https://".$data["uri_clean"];
		else
			$data["uri_clean"] = "http://".$data["uri_clean"];

		/* prepare email */
		$output = new Layout_output();
		$output->insertTag("b", gettext("Account registration"));
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("You have registered a new account on the following website").": ");
		$output->insertTag("a", $data["uri"], array(
			"href" => $data["uri"]
		));
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("You account login data are").":");
		$output->addTag("br");
		$output->addCode(gettext("Login").": ".$data["username"]);
		$output->addTag("br");
		$output->addCode(gettext("Password").": ".$data["password"]);
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("To activate your account, please click on the following link. You have to activate your account within 24 hours or your account will be removed."));
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("To activate your account now click here").":");
		$output->addTag("br");
		$act = sprintf("%s/activate/id=%d&amp;hash=%s&amp;site=%s",	$data["uri_clean"], $new_id, $hash, urlencode(base64_encode($data["uri_clean"])));
		$output->insertTag("a", $act, array("href" => $act));
		$output->addTag("br");
		$mdata = $output->generate_output();

		$mail_data = new Email_data();
		$mdata = $mail_data->stylehtml($mdata);

		/* get siteroot data */
		$settings = $this->getCmsSettings($data["siteroot"]);
		if (!$mail_data->validateEmail($settings["search_email"])) {
			$settings = $this->getCmsSettings("R");
			if (!$mail_data->validateEmail($settings["search_email"])) {
				$settings["search_email"] = $GLOBALS["covide"]->license["email"];
			}
		}

		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		$headers .= 'From: '.$settings["search_email"]."\r\n";
		mail($data["email"], gettext("Account registration"), $mdata, $headers, "-f".$settings["search_email"]);
	}

	public function updateRegistration($userid, $hash) {
		$q = sprintf("select * from cms_users where id = %d and confirm_hash = '%s'",
			$userid, $hash);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0) {
			return 1;
		} else {
			$row = sql_fetch_assoc($res);
			if ($row["is_active"]) {
				return 2;
			} else {
				$q = sprintf("update cms_users set is_active = 1, confirm_hash = '' where id = %d",
					$userid);
				sql_query($q);
				return 0;
			}
		}
	}
	public function recoverPassword($data) {
		$uri = $data["uri"];

		$mail_data = new Email_data();
		if ($data["email"] != $data["email_cf"])
			$err = gettext("email addresses does not match");
		elseif (!$mail_data->validateEmail($data["email"]))
			$err = gettext("no valid email address specified");
		else {
			$q = sprintf("select * from cms_users where email like '%s'", $data["email"]);
			$res = sql_query($q);
			if (sql_num_rows($res) == 0)
				$err = gettext("the specified email address could not be found");
			else {
				$data = sql_fetch_assoc($res);
				/* prepare email */
				$output = new Layout_output();
				$output->insertTag("b", gettext("Account password recovery"));
				$output->addTag("br");
				$output->addTag("br");
				$output->addCode(gettext("You have requested your password on the following website").": ");
				$output->insertTag("a", $uri, array(
					"href" => $uri
				));
				$output->addTag("br");
				$output->addTag("br");
				$output->addCode(gettext("You account login data are").":");
				$output->addTag("br");
				$output->addTag("br");
				$output->addCode(gettext("Login").": ".$data["username"]);
				$output->addTag("br");
				$output->addCode(gettext("Password").": ".$data["password"]);
				$output->addTag("br");
				$output->addTag("br");
				$output->insertTag("a", gettext("Click here to go to the website"), array(
					"href" => $uri
				));
				$output->addTag("br");
				$mdata = $output->generate_output();

				$mail_data = new Email_data();
				$mdata = $mail_data->stylehtml($mdata);

				/* get siteroot data */
				$settings = $this->getCmsSettings($data["siteroot"]);
				if (!$mail_data->validateEmail($settings["search_email"])) {
					$settings = $this->getCmsSettings("R");
					if (!$mail_data->validateEmail($settings["search_email"])) {
						$settings["search_email"] = $GLOBALS["covide"]->license["email"];
					}
				}

				$headers  = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
				$headers .= 'From: '.$settings["search_email"]."\r\n";
				mail($data["email"], gettext("Account password recovery"), $mdata, $headers, "-f".$settings["search_email"]);
				$err = true;
			}
		}
		return $err;
	}

	private function createCmsDir($name, $parent_id, $truncate = 0) {
		/* search for a specific directory and create if not exists */

		/* create fs object */
		$fsdata = new Filesys_data();
		$cms_root_folder = $fsdata->getCmsFolder();

		/* if no parent id, use cms root folder */
		if (!$parent_id)
			$parent_id = $cms_root_folder;

		/* get requested parent folder */
		$folders = $fsdata->getFolders(array("parentfolder" => $parent_id));
		if (!is_array($folders["data"]))
			$folders["data"] = array();

		/* search for the folder name */
		$found = 0;
		foreach ($folders["data"] as $v) {
			if ($v["name"] == $name) {
				/* check if the found folder has to be truncated */
				if ($truncate) {
					/* delete folder */
					$fsdata->deleteFolderExec($v["id"], 1);
				} else {
					$found++;
					$return_id = $v["id"];
				}
			}
		}
		if (!$found) {
			/* create folder */
			$dirdata["folder"] = array(
				"name" => addslashes($name),
				"description" => "cms import"
			);
			$dirdata["id"] = $parent_id;
			$return_id = $fsdata->create_dir($dirdata, 1);
		}
		return $return_id;
	}

	private function createCmsStruct($filesys, $folder_id) {
		$fsdata = new Filesys_data();

		$dir = scandir($filesys);
		foreach ($dir as $v) {
			$file = sprintf("%s/%s", $filesys, $v);
			/* exclude some files */
			if (!in_array($v, array(".", "..", ".cache"))) {
				if (is_dir($file)) {
					/* create directory */
					$new_dir = $this->createCmsDir($v, $folder_id, 1);

					$new_folder = sprintf("%s/%s", $filesys, $v);
					$this->createCmsStruct($new_folder, $new_dir, 1);
				} else {
					/* copy file */
					$filedata = array(
						"name"      => addslashes($v),
						"size"      => filesize($file),
						"folder_id" => $folder_id,
						"raw"       => 1, //is raw data, not base64
						"bindata"   => file_get_contents($file)
					);
					$fsdata->file_upload_alt($filedata);
				}
			}
		}
	}

	private function copyCmsTable($table, $remap_fields = array(), $new_table = "") {
		/* re-insert all pages */
		if (!$new_table)
			$new_table = $table;

		$q = sprintf("describe `%s`", $new_table);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$struct[$row["Field"]] = $row;
		}

		$q = sprintf("select * from `%s` order by id", $table);
		$res = mysql_query($q) or die(mysql_error());

		while ($row = mysql_fetch_assoc($res)) {
			/* re-mapping */
			foreach ($remap_fields as $k=>$v) {
				$row[$v] = $row[$k];
				unset($row[$k]);
			}
			/* apply variable casting and escaping */
			$keys   = array();
			$values = array();

			foreach ($row as $k=>$v) {
				$type = $struct[$k]["Type"];
				if (preg_match("/((varchar)|(text))/si", $type)) {
					/* string */
					$row[$k] = sprintf("'%s'", mysql_real_escape_string($row[$k]));
				} else {
					/* assume integer */
					$row[$k] = (int)$row[$k];
				}
				/* sync vars */
				$v = $row[$k];

				$keys[]   = sprintf("`%s`", $k);
				$values[] = $v;
			}
			$q = sprintf("insert into `%s` (%s) values (%s)", $new_table,
				implode(",", $keys), implode(",", $values));
			sql_query($q);
		}
	}

	private function getCmsOldSiteroot($type) {
		$q = sprintf("select id from cms_data where isSpecial = '%s'", $type);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0)
			return sql_result($res,0,"",2);
	}

	private function applyCmsSiteRootPatches() {
		/* get roots */
		$siteroot_id  = $this->getCmsOldSiteroot("R");
		$del_id       = $this->getCmsOldSiteroot("D");
		$protected_id = $this->getCmsOldSiteroot("X");

		/* delete the old siteroots */
		$q = sprintf("delete from `cms_data` where `id` IN (%d, %d, %d)",
			$siteroot_id, $del_id, $protected_id);
		sql_query($q);

		/* re-insert the old roots */
		$q = sprintf("
			INSERT INTO `cms_data` (`id`, `parentPage`, `pageTitle`, `pageLabel`, `datePublication`, `pageData`, `pageRedirect`, `isPublic`, `isActive`, `isMenuItem`, `keywords`, `apEnabled`, `isTemplate`, `isList`, `useMetaData`, `isSticky`, `search_fields`, `search_descr`, `isForm`, `date_start`, `date_end`, `date_changed`, `notifyManager`, `isGallery`, `pageRedirectPopup`, `popup_data`, `new_code`, `new_state`, `search_title`, `search_language`, `search_override`, `pageAlias`, `isSpecial`, `date_last_action`, `google_changefreq`, `google_priority`, `autosave_info`, `autosave_data`, `address_ids`, `address_level`, `isProtected`, `useSSL`, `useInternal`, `isFeedback`) VALUES
				(%d, 0, 'site root', '', 0, '', '', 0, 0, 0, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'R', NULL, 'monthly', '0.5', '', '', NULL, NULL, 0, 0, 0, NULL),
				(%d, 0, 'deleted items', '', 0, NULL, '', 1, 0, 1, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'D', NULL, 'monthly', '0.5', '', '', NULL, NULL, 0, 0, 0, NULL),
				(%d, 0, 'protected items', '', 0, NULL, '', 1, 1, 1, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'X', NULL, 'monthly', '0.5', '', '', NULL, NULL, 0, 0, 0, NULL);",
			$siteroot_id, $del_id, $protected_id);
		sql_query($q);
	}

	private function copyCmsGalleryThumbs($old_cmsfiles_path) {
		/*
			old formats:
				large_<cache_ident>.jpg
				thumb_<cache_ident>.jpg

			new formats:
				<dbid>_full.jpg
				<dbid>_small.jpg
				<dbid>_medium.jpg
		*/

		$cache_dir = sprintf("%s/%s", $old_cmsfiles_path, ".cache");
		$new_dir = sprintf("%s/gallery", $GLOBALS["covide"]->filesyspath);

		$dir = scandir($new_dir);
		foreach ($dir as $f) {
			$file = sprintf("%s/%s", $dir, $f);
			if (is_file($file)) {
				unlink($file);
			}
		}

		$q = sprintf("select * from cms_gallery_photos order by id");
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$old["full"]   = sprintf("%s/large_%s.jpg", $cache_dir, $row["cachefile"]);
			$old["thumb"]  = sprintf("%s/thumb_%s.jpg", $cache_dir, $row["cachefile"]);

			$new["full"]   = sprintf("%s/%d/%s_full.jpg", $new_dir, $row["pageid"], $row["id"]);
			$new["medium"] = sprintf("%s/%d/%s_medium.jpg", $new_dir, $row["pageid"], $row["id"]);
			$new["small"]  = sprintf("%s/%d/%s_small.jpg", $new_dir, $row["pageid"], $row["id"]);

			copy($old["full"], $new["full"]);
			copy($old["full"], $new["medium"]);
			copy($old["thumb"], $new["small"]);
		}
	}

	public function handleOldCmsFile($file, $folder_id=0, $level=0) {
		$cache = new Tpl_cache();

		$ident = sprintf("oldimage_%s", md5($file));
		$fetch = $cache->getApcCache($ident);

		if (!$fetch) {

			$dir = explode("/", dirname($file));
			$fname = basename($file);

			$fsdata = new Filesys_data();

			/* loop protection */
			if ($level > 10)
				exit();

			if (!$folder_id) {
				$cms_root_folder = $fsdata->getCmsFolder();
				$folder_id = $fsdata->getFolderIdByName("cmsfiles", $cms_root_folder);
			}

			if (!$dir[$level]) {
				/* search for a file */
				$file_id = $fsdata->getFileIdByName($fname, $folder_id);

				$cache->setApcCache($ident, $file_id);

				#header("Status: 301 Moved Permanently", true, 301);
				header("HTTP/1.1 301 Moved Permanently", true, 301);
				header(sprintf("Location: /cmsfile/%d", $file_id));
				exit();

			} else {
				$folder_id = $fsdata->getFolderIdByName($dir[$level], $folder_id);
				if ($folder_id === false)
					exit();

				$this->handleOldCmsFile($file, $folder_id, $level+1);
			}
		} else {
			#header("Status: 301 Moved Permanently", true, 301);
			header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently", true, 301);
			header(sprintf("Location: /cmsfile/%d", $fetch));
			exit();
		}
	}
	/* migrateGalleryItems {{{ */
	/**
	 * Move gallery images into a subdir structure so we wont end up with too much files in one directory.
	 */
	public function migrateGalleryItems() {
		/* gallery data */
		$dir = sprintf("%s/gallery", $GLOBALS["covide"]->filesyspath);
		$files = scandir($dir);
		foreach ($files as $file) {
			$f = sprintf("%s/%s", $dir, $file);
			if (!is_dir($f)) {
				/* file */
				$fn = (int)preg_replace("/^(\d{1,})\_.*$/s", "$1", basename($f));

				if ($fn > 0) {
					$q = sprintf("select pageid from cms_gallery_photos where id = %d", $fn);
					$res = sql_query($q);
					if (sql_num_rows($res) == 0) {
						unlink($f);
					} else {
						$row = sql_fetch_assoc($res);
						if ($row["pageid"] == -1)
							$row["pageid"] = "banner";

						$dest = sprintf("%s/gallery/%s/%s",
							$GLOBALS["covide"]->filesyspath,
							$row["pageid"],
							basename($f));

						if (!file_exists(dirname($dest)))
							mkdir(dirname($dest), 0777, 1);

						rename($f, $dest);
					}
				}
			}
		}
	}
	/* }}} */
	/* getPolls {{{ */
	/**
	 * Get all available polls
	 *
	 * @return array polls
	 */
	public function getPolls() {
		$sql = "SELECT * FROM polls ORDER BY id";
		$res = sql_query($sql);
		$polls = array();
		while ($row = sql_fetch_assoc($res)) {
			$polls[$row["id"]] = $row;
		}
		return $polls;
	}
	/* }}} */
	/* getPollById {{{ */
	/**
	 * Get a specific poll with possible answers
	 *
	 * @param int $id The pollid
	 * @return array Polldata
	 */
	public function getPollById($id = -1) {
		if ($id == -1) {
			// get latest active poll from database
			$sql = sprintf("SELECT * FROM polls WHERE is_active = 1 ORDER BY id desc LIMIT 1");
		} else {
			$sql = sprintf("SELECT * FROM polls WHERE id=%d", $id);
		}
		$res = sql_query($sql);
		$polldata = sql_fetch_assoc($res);
		/* grab possible answers */
		$sql = sprintf("SELECT * FROM poll_items WHERE polls_id=%d ORDER BY position", $polldata["id"]);
		$res = sql_query($sql);
		$totalvotes = 0;
		while ($row = sql_fetch_assoc($res)) {
			/* grab votecount for this item */
			$q = sprintf("SELECT COUNT(*) FROM poll_answers WHERE poll_id = %d AND item_id = %d", $polldata["id"], $row["id"]);
			$r = sql_query($q);
			$row["votecount"] = sql_result($r, 0);
			$totalvotes = $totalvotes + $row["votecount"];
			$polldata["items"][$row["id"]] = $row;
		}
		$polldata["totalvotes"] = $totalvotes;
		return $polldata;
	}
	/* }}} */
	/* cmsPollDelete {{{ */
	/**
	 * Delete poll from database
	 *
	 * @param int $id The poll to delete
	 */
	public function cmsPollDelete($id){
		/* first cleanup answers */
		$sql = sprintf("DELETE FROM poll_answers WHERE poll_id = %d", $id);
		$res = sql_query($sql);
		/* then delete poll options */
		$sql = sprintf("DELETE FROM poll_items WHERE polls_id = %d", $id);
		$res = sql_query($sql);
		/* and remove poll */
		$sql = sprintf("DELETE FROM polls WHERE id = %d", $id);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* cmsPollSave {{{ */
	/**
	 * Save polldata to database
	 */
	public function cmsPollSave($data) {
		if ($data["poll"]["id"]) {
			$sql = sprintf("UPDATE polls SET question = '%s', is_active = %d WHERE id = %d",
				$data["poll"]["question"], $data["poll"]["is_active"], $data["poll"]["id"]);
			$res = sql_query($sql);
			/* find out wether the options are already in the database.
				if not, we need to add them. if so we need to update them */
			foreach ($data["poll"]["options"] as $k=>$v) {
				if ($k) {
					$sql = sprintf("SELECT COUNT(*) FROM poll_items WHERE id = %d and polls_id = %d",
						$k, $data["poll"]["id"]);
					$res = sql_query($sql);
					$count = sql_result($res, 0);
				} else {
					$count = 0;
				}
				if (trim($v["value"])) {
					if ($count) {
						$sql = sprintf("UPDATE poll_items SET position = %d, value = '%s' WHERE id = %d",
							$v["position"], $v["value"], $k);
					} else {
						$sql = sprintf("INSERT INTO poll_items (polls_id, position, value) VALUES (%d, %d, '%s')",
							$data["poll"]["id"], $v["position"], $v["value"]);
					}
					$res = sql_query($sql);
				}
			}
			return $data["poll"]["id"];
		} else {
			$sql = sprintf("INSERT INTO polls (question, is_active) VALUES ('%s', %d)",
				$data["poll"]["question"], $data["poll"]["is_active"]);
			$res = sql_query($sql);
			$pollid = sql_insert_id("polls");
			/* add items */
			foreach ($data["poll"]["options"] as $k=>$v) {
				$sql = sprintf("INSERT INTO poll_items (polls_id, position, value) VALUES (%d, %d, '%s')",
					$pollid, $v["position"], $v["value"]);
				$res = sql_query($sql);
			}
			return $pollid;
		}
	}
	/* }}} */
	/* cmsPollDeleteAnswer {{{ */
	/**
	 * Delete a specific answer/option from a poll
	 *
	 * @param int $id The option id to remove
	 * @return int the poll_id where this option was used
	 */
	public function cmsPollDeleteAnswer($id) {
		$sql = sprintf("SELECT polls_id FROM poll_items WHERE id = %d", $id);
		$res = sql_query($sql);
		$pollid = sql_result($res, 0);
		$sql = sprintf("DELETE FROM poll_items WHERE id = %d", $id);
		$res = sql_query($sql);
		$sql = sprintf("DELETE FROM poll_answers WHERE poll_id = %d AND item_id = %d", $pollid, $id);
		$res = sql_query($sql);
		return $pollid;
	}
	/* }}} */
	/* getPageHistory {{{ */
	/**
	 * Get the history of a page
	 *
	 * @param int $page_id the page to grab the history for
	 * @return array set of all changes
	 */
	public function getPageHistory($page_id) {
		$user_data = new User_data();
		$sql = sprintf("SELECT * FROM cms_data_revisions WHERE page_id = %d ORDER BY versiondate DESC", $page_id);
		$res = sql_query($sql);
		$history = array();
		while ($row = sql_fetch_assoc($res)) {
			$row["datetime_h"] = date("d-m-Y H:i:s", $row["versiondate"]);
			$row["username"] = $user_data->getUsernameById($row["editor"]);
			$history[$row["id"]] = $row;
		}
		return $history;
	}
	/* }}} */
	/* cmsDeletePageHistory {{{ */
	/**
	 * Deletes a specific page history entry
	 *
	 * @param int $id The itemid to remove
	 */
	public function cmsDeletePageHistory($id) {
		$q = sprintf("delete from cms_data_revisions where id = %d", $id);
		sql_query($q);
	}
	/* }}} */
	/* cmsRestorePageHistory {{{ */
	/**
	 * Restore a specific version of the page
	 *
	 * @param int $restore_id the record id from cms_data_revisions
	 * @param int $pageid the page to restore. We use this to check that we are dealing with correct info
	 */
	public function cmsRestorePageHistory($restore_id, $pageid) {
		if ((int)$pageid > 0 && (int)$restore_id > 0) {
			$oldpage = $this->getPageById($pageid);
			$oldpage["page_id"] = $oldpage["id"];
			$oldpage["versiondate"] = time();
			$oldpage["search_language"] = implode(",", $oldpage["search_language"]);
			$oldpage["editor"] = $_SESSION["user_id"];
			unset($oldpage["id"]);
			unset($oldpage["popup_height"]);
			unset($oldpage["popup_width"]);
			unset($oldpage["popup_hidenav"]);
			unset($oldpage["search_language"]);
			unset($oldpage["search_language"]);
			unset($oldpage["isDate"]);
			unset($oldpage["isDateRange"]);
			unset($oldpage["form_mode"]);
			foreach($oldpage as $k=>$v) {
				$fields[] = $k;
				$values[] = "'".addslashes($v)."'";
			}
			$sql = sprintf("INSERT INTO cms_data_revisions (%s) VALUES (%s);", implode(",", $fields), implode(",", $values));
			$res = sql_query($sql);

			$sql = sprintf("SELECT * FROM cms_data_revisions WHERE id = %d AND page_id = %d", $restore_id, $pageid);
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				$row_restore = sql_fetch_assoc($res);
				unset($row_restore["page_id"]);
				unset($row_restore["id"]);
				unset($row_restore["versiondate"]);
				unset($row_restore["editor"]);
				$i = 0;
				$fields = array();
				foreach($row_restore as $k=>$v) {
					$i++;
					$fields[$i] = $k."='".$v."'";
				}
				$sql = sprintf("UPDATE cms_data SET %s WHERE id = %d LIMIT 1;", implode(",", $fields), $pageid);
				$res = sql_query($sql);
			}
		}
	}
	/* }}} */
	/* getLoginLog {{{ */
	/**
	 * Get all the records in the loginlog
	 *
	 * @return array
	 */
	public function getLoginLog() {
		$return = array();
		$sql = "SELECT * FROM cms_login_log ORDER BY timestamp DESC";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$row["h_timestamp"] = date("d-m-Y H:i:s", $row["timestamp"]);
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */
	/* hasPiwik {{{ */
	/**
	  * Checks if piwik is active in this cms
	  *
	  * @author svdhaar@users.sourceforge.net
	  * @since 1.0 8-11-2009
	  * @return bool true of false
	  */
	public function hasPiwik() {
		$settings = $this->getCmsSettings();
		if ($settings['piwik_analytics']) {
			return true;
		} else {
			$q = sprintf('SELECT COUNT(piwik_analytics) FROM cms_license_siteroots WHERE piwik_analytics > 0');
			$res = sql_query($q);
			return (sql_result($res,0)) ? true : false;
		}
	}
	/* }}} */
	/* hasPiwik {{{ */
	/**
	  * Get translation from the translation table
	  *
	  * @author svdhaar@users.sourceforge.net
	  * @since 1.0 9-1-2010
	  * @param string string the string to translate
	  * @return string the translated string
	  */
	public function getTranslation($str) {
		$key = strtolower($str);
		$locale = preg_replace('/\_.*$/s', '', $_SESSION['locale']);
		if (!count($this->translation_cache)) {
			$q = sprintf("SELECT abbreviation, description FROM cms_abbreviations WHERE lang LIKE '%%%s%%'", $locale);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$this->translation_cache[trim(strtolower($row['abbreviation']))] = trim($row['description']);
			}
		}
		if (array_key_exists($key, $this->translation_cache)) {
			return $this->translation_cache[$key];
		} else {
			// try gettext
			return gettext($str);
		}
	}
	/* }}} */

}
?>
