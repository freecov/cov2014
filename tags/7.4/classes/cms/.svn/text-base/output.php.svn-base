<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Cms_output {
	/* constants */
	const include_dir = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "cms";

	public $buffer = '';
	private $current_root;
	private $sitemap;
	private $pagesize;


	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function cmsSitemap() {

		$cms_data = new Cms_data();
		$cms_data->decodeOptions($_REQUEST);
		$cms_license = $cms_data->getCmsSettings();

		/* process deleted items */
		$cms_data->processDeletedItems();

		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$cms = $_REQUEST["cms"];

		$output = new Layout_output();
		$output->layout_page("CMS");
		$output->load_javascript(self::include_dir."script_fp.js");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->addTag("div", array("id"=>"cms_icon_new", "style"=>"display:none"));
			$output->insertAction("new", gettext("new page"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_info", "style"=>"display:none"));
			$output->insertAction("info", gettext("show options legend"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_delete", "style"=>"display:none"));
			$output->insertAction("cancel", gettext("delete this page and subpages of this page"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_copy", "style"=>"display:none"));
			$output->insertAction("copy", gettext("copy page structure"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_important", "style"=>"display:none"));
			$output->insertAction("important", gettext("this page contains an autosave value"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_paste", "style"=>"display:none"));
			$output->insertAction("paste", gettext("paste selection under this page"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_collapse", "style"=>"display:none"));
			$output->insertAction("view_tree", gettext("collapse sitemap except for this page"), "");
		$output->endTag("div");

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("Sitemap")
		));

		/* menu items */
		$menuitems[sprintf("- %s -", gettext("choose"))] = "void(0);";

		if (($user_info["xs_cms_level"] == 3 && !$GLOBALS["covide"]->license["cms_lock_settings"])
			|| $user_info["username"] == "administrator") {
			$menuitems[gettext("license settings")] = "popup('?mod=cms&action=editCmsSettings', 'settings', 640, 480, 1);";
			$menuitems[gettext("site templates")] = "popup('?mod=cms&action=siteTemplates', randWin(), 970, 680, 1);";
		} elseif ($user_info["xs_cms_level"] == 3) {
			$menuitems[gettext("site templates")] = "popup('?mod=cms&action=siteTemplates', randWin(), 970, 680, 1);";
		}
		if ($user_info["xs_cms_level"] >= 2)
			$menuitems[gettext("hit counters")] = "popup('?mod=cms&action=siteCounters', 'hitcounters', 500, 400, 1);";

		if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_permissions"])
			$menuitems[gettext("manage accounts")] = "popup('?mod=cms&action=editAccountsList', 'permissions', 640, 480, 1);";

		if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_meta"])
			$menuitems[gettext("metadata definitions")] = "popup('?mod=cms&action=metadataDefinitions', 'metadata', 640, 480, 1);";

		$menuitems[gettext("file management")] = "popup('index.php?mod=cms&action=filesys');";
		if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_linkchecker"])
			$menuitems[gettext("linkchecker")] = "popup('?mod=cms&action=linkchecker', 'linkchecker', 840, 480, 1);";

		if ($user_info["xs_cms_level"] >= 2) {
			$menuitems[gettext("site information and settings")] = "popup('?mod=cms&action=editSiteInfo', 'siteinfo', 640, 480, 1);";
			$menuitems[gettext("abbreviation management")] = "popup('?mod=cms&action=editAbbreviations', 'abbr', 640, 480, 1);";
		}

		if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_banners"])
			$menuitems[gettext("manage banners")] = "popup('?mod=cms&action=cmsgallery&id=-1', 'banners', 900, 550, 1);";

		#if ($cms_license["cms_changelog"] || $cms_license["cms_versioncontrol"])
		#	$menuitems[gettext("changes overview")] = "";


		foreach ($menuitems as $k=>$v) {
			unset($menuitems[$k]);
			$menuitems[$v] = $k;
		}
		natcasesort($menuitems);

		$venster->addVensterData();
			$venster->start_javascript();
				$venster->addCode("
					function randWin() {
						var s = new String().concat('templates', Math.random()*(Math.pow(2,20)));
						s = s.replace(/[^a-z0-9]/gi, '');
						return s;
					}
				");
			$venster->end_javascript();

			/* version control / inklappen / uitklappen / reload / batch options */
			$tbl = new Layout_table(array(
				"style" => "width: 100%;"
			));
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("Provide pagenumber or searchkey an hit search").": ");
					$tbl->addTextField("cms[search]", $cms["search"]);
					$tbl->insertAction("edit", gettext("open page"), "javascript: cmsSearchPage();");
					$tbl->insertAction("forward", gettext("search"), "javascript: cmsSearch();");
					if ($_REQUEST["cms"]["search"])
						$tbl->insertAction("toggle", gettext("reset search results"), "javascript: document.getElementById('cmssearch').value = ''; cmsReload();");
				$tbl->endTableData();
				$tbl->addTableData(array("align" => "right"));
					$tbl->addCode(gettext("CMS options").": ");
					$tbl->addSelectField("menuitems", $menuitems, "void(0);");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addTag("br");
					$tbl->insertLink(gettext("expand all"), array(
						"href" => "javascript: cmsExpand(-1);"
					));
					$tbl->insertAction("down", gettext("collapse all"), "javascript: cmsExpand(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("collapse all"), array(
						"href" => "javascript: cmsCollapse(-1);"
					));
					$tbl->insertAction("up", gettext("uncollapse all"), "javascript: cmsCollapse(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("reload sitemap"), array(
						"href" => "javascript: cmsReload();"
					));
					$tbl->insertAction("reload", gettext("reload sitemap"), "javascript: cmsReload();");
					$tbl->addSpace(2);

					$sel[gettext("cms roots")]["R"]  = gettext("cms root");
					$sel[gettext("cms roots")]["D"]  = gettext("deleted items");

					#if ($cms_license["cms_protected"])
					#	$sel[gettext("cms roots")]["X"]  = gettext("protected items");

					$sroots = $cms_data->getUserSitemapRoots();
					$sel[gettext("custom website roots")] = $sroots;

					$tbl->addSelectField("siteroot", $sel, $cms_data->opts["siteroot"]);

					if ($user_info["xs_cms_level"] >= 2) {
						if ($cms_license["multiple_sitemaps"])
							$tbl->insertAction("file_attach", gettext("add new site root"), "javascript: addSiteRoot();");

						if ($user_info["xs_cms_level"] >= 2 && is_numeric($cms_data->opts["siteroot"])) {
							$tbl->insertAction("delete", gettext("delete sitemap"), "javascript: deleteSiteRoot('".$cms_data->opts["siteroot"]."');");
						}
						if (!in_array($cms_data->opts["siteroot"], array("X", "D"))) {
							$state = $cms_data->getSiteRootPublicState($cms_data->opts["siteroot"]);
							if ($state == 1)
								$tbl->insertAction("state_special", gettext("siteroot is non-public"), "javascript: popup('?mod=cms&action=editSiteInfo&siteroot=".$cms_data->opts["siteroot"]."');");
							else
								$tbl->insertAction("state_private", gettext("siteroot is public "), "javascript: popup('?mod=cms&action=editSiteInfo&siteroot=".$cms_data->opts["siteroot"]."');");
						}
						//$tbl->insertAction("mail_tracking", gettext("validate sitemap"), "javascript: popup('?mod=cms&action=validateSitemap&siteroot=".$cms_data->opts["siteroot"]."');");
					}
					$tbl->start_javascript();
					$tbl->addCode("checkSiteRootChange();");
					$tbl->end_javascript();

				$tbl->endTableData();
				$tbl->addTableData(array(
					"valign" => "bottom",
					"align"  => "right"
				));
					if (count($cms_data->opts["buffer"])==0) {
						$tbl->insertTag("a", gettext("add selected items to buffer"), array(
							"href" => "javascript: fillBuffer();"
						));
						$tbl->insertAction("forward", gettext("copy selected items to buffer"), "javascript: fillBuffer();");
					} else {
						$tbl->addCode(gettext("buffer actions").": ");
						$sel = array(
							" "       => gettext("choose an action"),
							gettext("page properties") => array(
								"bufferActive"      => gettext("activate selection")." (+A)",
								"bufferActiveDis"   => gettext("deactivate selection")." (-A)",
								"bufferPublic"      => gettext("make selection public")." (+P)",
								"bufferPublicDis"   => gettext("make selection private")." (-P)",
								"bufferMenuitem"    => gettext("set menuitem on selection")." (+M)",
								"bufferMenuitemDis" => gettext("remove menuitem from selection")." (-M)",
								"bufferAddressLevel"    => gettext("set contact visibility to visible")." (+C)",
								"bufferAddressLevelDis" => gettext("set contact visibility to related")." (-C)",
							),
							gettext("page actions") => array(
								"erasePermissions"  => gettext("remove custom permissions from selection"),
								"deletebuffer"      => gettext("remove/delete all pages selected in buffer"),
							),
							gettext("buffer actions") => array(
								"togglebuffer"      => gettext("add/remove selected items from/to buffer"),
								"erasebuffer"       => gettext("deselect all items in buffer")
							)
						);
						if (!$cms_license["cms_address"]) {
							unset($sel[gettext("page properties")]["bufferAddressLevel"]);
							unset($sel[gettext("page properties")]["bufferAddressLevelDis"]);
						}
						$tbl->addSelectField("cms[buffer]", $sel, "");
						$tbl->start_javascript();
							$tbl->addCode("
								document.getElementById('cmsbuffer').onchange = function() {
									exec_buffer();
								}
							");
						$tbl->end_javascript();
					}
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->addTag("br");
			unset($tbl);


			/* sitemap generation code here */
			$this->sitemap["offset"] = $_REQUEST["offset"];

			$venster->addCode($this->genFpHeader());
			$venster->addCode($this->generateSpecialPage($cms_data->opts["siteroot"], $cms_data));
			$venster->addCode($this->genFpFooter());
			unset($tbl);

			$paging = new Layout_paging();
			$paging->setOptions(
				$this->sitemap["offset"],
				$this->sitemap["curr"],
				"javascript: browse('%%');"
				,0,0,1
			);
			$venster->addCode($paging->generate_output());

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("cmd", "");
		$output->addHiddenField("id", "");
		$output->addHiddenField("offset", $_REQUEST["offset"]);
		$output->addHiddenField("options_state", $_REQUEST["options_state"]);
		$output->addHiddenField("jump_to_anchor", "");
		$output->addCode( $venster->generate_output() );

		$cms_data->saveOptions();

		$output->endTag("form");
		if ($_REQUEST["jump_to_anchor"]) {
			$output->start_javascript();
				$output->addCode(sprintf("addLoadEvent(document.location.href = document.location.href.concat('#id%d'));",
					$_REQUEST["jump_to_anchor"]));
			$output->end_javascript();
		}
		switch ($_REQUEST["cmd"]) {
			case "bufferActive":
			case "bufferActiveDis":
			case "bufferPublic":
			case "bufferPublicDis":
			case "bufferMenuitem":
			case "bufferMenuitemDis":
			case "erasePermissions":
			case "deletebuffer":
				$output->start_javascript();
				$output->addCode("
					function askResetBuffer() {
						var p = confirm(gettext('Buffer action executed. Do you want to clear the buffer now?'));
						if (p == true) {
							document.getElementById('cmsbuffer').value = 'erasebuffer';
							exec_buffer();
						}
					}
					addLoadEvent(setTimeout('askResetBuffer()', 500));
				");
				$output->end_javascript();
		}

		$output->layout_page_end();
		$output->exit_buffer();
	}

	private function genFpHeader() {
		$tbl = new Layout_table(array(
			"cellspacing" => 0,
			"cellpadding" => 0,
			"class" => "cms"
		));
		$tbl->addTableRow(array("style" => "background-color: #ddd;"));
			$tbl->insertTableHeader(gettext("level"), array("class" => "cms_right cms_bottom"));
			$tbl->insertTableHeader(gettext("page"),  array("class" => "cms_right cms_bottom"));
			$tbl->insertTableHeader("#", array("class" => "cms_right cms_bottom"));
			$tbl->addTableHeader(array("colspan" => 2, "align" => "right", "class" => "cms_right cms_bottom"));
				$tbl->addTag("nobr");
					$tbl->insertAction("view_all", gettext("toggle extra icons"), "javascript: toggle_cms_table();");
				$tbl->endTag("nobr");
			$tbl->endTableHeader();
			$tbl->addTableHeader(array("colspan"=>2, "class" => "cms_bottom"));
				$tbl->addCode(gettext("actions"));
				$tbl->addSpace();
				$tbl->addCheckBox("checkbox_cms_toggle_all", 1, 0);
			$tbl->endTableHeader();
		$tbl->endTableRow();
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				document.write('%s');\n",
				str_replace("/", "\\/",
					preg_replace("/(\n|\r|\t)/s", "",
						addslashes(
							$tbl->generate_output()
						)
					)
				)
			));
		return $output->generate_output();
	}
	private function genFpFooter() {
		$output = new Layout_output();

		$output->addCode("
			document.write('<tr><td colspan=\"6\" bgcolor=\"#ededed\">&nbsp;<\/td><\/tr>');
			document.write('<\/table>');"
		);
		$output->end_javascript();
		return $output->generate_output();
	}

	public function cmsEditor($text_only=0) {
		require(self::include_dir."cmsEditor.php");
	}

	public function generateSpecialPage($special, &$cms_data) {
		if (is_numeric($special)) {
			$q = "select id from cms_data where id = ".(int)$special;
		} else {
			$q = "select id from cms_data where isSpecial = '".$special."' and parentPage = 0";
		}
		$res = sql_query($q) or die($q);
		$id = sql_result($res,0);

		/* set current (special) root */
		$this->current_root = $id;

		$cms_data->permission_prefetch = 1;
		$this->generateSiteMap($id, 0, 1, $cms_data, $restricted);
		return $this->buffer;
	}

	public function generateSitemap($id, $level, $special=0, &$cms_data) {
		require(self::include_dir."generateSitemap.php");
	}

	// function to generate one javascript trigger (record)
	public function genFp($opts) {
		$this->buffer .= "fp(".implode(",",$opts).");\n";
	}

	public function viewRestorePoint($id) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($id);

		$mail_data = new Email_data();
		#print_r($data);
		$body = $mail_data->stylehtml($data["autosave_data"]);
		$body = preg_replace("/(<body[^>]*?>)/six", "$1\n<h4>".$data["autosave_header"]."</h4>", $body);
		$body = preg_replace("/(<body[^>]*?>)/six", "$1\n<h3>".$data["pageTitle"]."</h3>", $body);
		echo $body;
		exit();
	}

	public function addMenuItems($venster) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($_REQUEST["id"]);
		$cms_license = $cms_data->getCmsSettings();

		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$p = $cms_data->getUserPermissions($_REQUEST["id"], $_SESSION["user_id"]);

		$venster->addMenuItem(gettext("show on website"), "javascript: popup('http://".$_SERVER["HTTP_HOST"]."/page/".$_REQUEST["id"].".htm');");
		$venster->addMenuItem(gettext("design"), "?mod=cms&action=editpage&noredir=1&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("textmode"), "?mod=cms&action=editpagetext&id=".$_REQUEST["id"]);

		$venster->addMenuItem(gettext("alias history"), "?mod=cms&action=aliashistory&id=".$_REQUEST["id"]);

		if ($cms_license["cms_mailings"])
			$venster->addMenuItem(gettext("mailings"), "?mod=cms&action=mailings&id=".$_REQUEST["id"]);

		//$venster->addMenuItem(gettext("file management"), "javascript: popup('index.php?mod=cms&action=filesys');");
		$venster->addMenuItem(gettext("page properties"), "?mod=cms&action=editSettings&id=".$_REQUEST["id"]);

		if ($user_info["xs_cms_level"] >= 2 || $p["manageRight"])
			$venster->addMenuItem(gettext("authorization"), "?mod=cms&action=authorisations&id=".$_REQUEST["id"]);

		$venster->addMenuItem(gettext("date options"), "?mod=cms&action=dateoptions&id=".$_REQUEST["id"]);
		if ($cms_license["cms_meta"])
			$venster->addMenuItem(gettext("meta data"), "?mod=cms&action=metadata&id=".$_REQUEST["id"]);

		if ($data["isList"] && $cms_license["cms_list"])
			$venster->addMenuItem(gettext("list generator"), "?mod=cms&action=cmslist&id=".$_REQUEST["id"]);
		if ($data["isGallery"] && $cms_license["cms_gallery"])
		$venster->addMenuItem(gettext("photo gallery"), "?mod=cms&action=cmsgallery&id=".$_REQUEST["id"]);
		if ($data["isForm"] && $cms_license["cms_forms"]) {
			$venster->addMenuItem(gettext("form generator"), "?mod=cms&action=cmsform&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
			//$venster->addMenuItem(gettext("form results"), "?mod=cms&action=cmsformresults&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
		}
	}

	public function cmsPageSettings() {
		require(self::include_dir."cmsPageSettings.php");
	}

	public function editCmsSettings() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS settings")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getCmsSettings();

		$venster->addVensterData();
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS modules"));
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();
			/* modules */
			$modules =& $cms_data->modules;
			$tbl->addTableRow();
			foreach ($modules as $k=>$v) {
				$i++;
				if ($i > 2) {
					$tbl->endTableRow();
					$tbl->addTableRow();
					$i=1;
				}
				$tbl->insertTableData($v, "", "header");
				$tbl->addTableData("", "data");
					$tbl->insertCheckBox("cms[$k]", 1, ($cms[$k]) ? 1:0);
				$tbl->endTableData();
			}
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->insertTag("a", gettext("Import non-oop Covide CMS 6 data"), array(
				"href" => "?mod=cms&action=cmsImport"
			));

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveCmsSettings");

		$output->addCode($venster->generate_output());
		$output->endTag("form");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editSiteInfo($siteroot="") {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS site settings")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getCmsSettings($siteroot);
		$cms_license = $cms_data->getCmsSettings();
		if (!$cms_license["cms_searchengine"])
			$searchopt = array("style" => "display: none");

		#$cms["search_language"] = explode(",", $cms["search_language"]);

		if ($_REQUEST["custom"]) {
			$custom = $cms_data->getPageById($_REQUEST["custom"]);
		}

		$venster->addVensterData();
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS site information"));
					if ($_REQUEST["custom"]) {
						$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
					}
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();

			/* cms title */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("website title"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_name]", $cms["cms_name"], array(
						"style" => "width: 350px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* keywords */
			$tbl->addTableRow($searchopt);
				$tbl->insertTableData(gettext("keywords"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[search_fields]", $cms["search_fields"], array(
						"style" => "width: 350px; height: 100px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* description */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("description"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[search_descr]", $cms["search_descr"], array(
						"style" => "width: 350px; height: 100px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* author */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("author"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_author]", $cms["search_author"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* copyright */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("copyright"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_copyright]", $cms["search_copyright"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("webmaster email"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_email]", $cms["search_email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* use pagetile in title bar */
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS site options"));
					if ($_REQUEST["custom"]) {
						$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
					}
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->addTableRow($searchopt);
				$tbl->insertTableData(gettext("add page title to browser title"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addCheckBox("cms[search_use_pagetitle]", 1, $cms["search_use_pagetitle"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* siteroot is public */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("this siteroot is public"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addCheckBox("cms[isPublic]", 1, $cms_data->getSiteRootPublicState($siteroot));
					$tbl->addSpace();
					$tbl->addCode(gettext("pages are also available in all other siteroots"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* some basic info */
			$tbl->addTableRow();
				$tbl->insertTableData("standaard pagina id", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_defaultpage]", $cms["cms_defaultpage"], array("style" => "width: 60px"));
					$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscms_defaultpage');");
				$tbl->endTableData();
			$tbl->endTableRow();
			/* favicons */
			$tbl->addTableRow();
				$tbl->insertTableData("custom favicon uri", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_favicon]", $cms["cms_favicon"], array("style" => "width: 300px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData("custom logo uri", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_logo]", $cms["cms_logo"], array("style" => "width: 300px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* cms manage hostname */
			if ($siteroot == "R" || !$siteroot) {
				$tbl->addTableRow();
					$tbl->insertTableData("cms manage hostname", "", "header");
					$tbl->addTableData(array("colspan"=>3), "data");
						$tbl->addTextField("cms[cms_manage_hostname]", $cms["cms_manage_hostname"], array("style" => "width: 300px;"));
					$tbl->endTableData();
				$tbl->endTableRow();
			}

			/* cms hostnames */
			$tbl->addTableRow();
				$tbl->insertTableData("cms hostnames", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[cms_hostnames]", $cms["cms_hostnames"], array("style" => "width: 300px; height: 80px;"));
				$tbl->endTableData();
			$tbl->endTableRow();

			/* languages */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("site language"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$sel = $cms_data->lang;
					$tbl->addRadioField("cms[search_language]", "not specified", "", "");
					foreach ($sel as $k=>$v) {
						$tbl->addRadioField("cms[search_language]", $v." (".$k.")", $k, $cms["search_language"]);
					}
				$tbl->endTableData();
			$tbl->endTableRow();

			/* search opts */
			$tbl->addTableRow($searchopt);
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS search engine options"));
					if ($_REQUEST["custom"]) {
						$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
					}
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->addTableRow($searchopt);
				/* yahoo sitemaps check */
				$tbl->addTabledata("", "header");
					$tbl->addCode(gettext("Yahoo sitemap key"));
				$tbl->endTableData();
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[yahoo_key]", $cms["yahoo_key"], array("style" => "width: 300px"));
					$tbl->insertAction("help", gettext("info"), "javascript: popup('http://siteexplorer.search.yahoo.com', 'googlev1')");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow($searchopt);
				/* google sitemaps check */
				$tbl->addTabledata("", "header");
					$tbl->addCode(gettext("Google verify v1"));
				$tbl->endTableData();
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[google_verify]", $cms["google_verify"], array("style" => "width: 300px"));
					$tbl->insertAction("help", gettext("info"), "javascript: popup('http://www.google.com/webmasters/sitemaps/', 'googlev1')");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow($searchopt);
				$tbl->addTabledata("", "header");
					/* google analytics is free available */
					$tbl->addCode(gettext("Google analytics code"));
				$tbl->endTableData();
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[google_analytics]", $cms["google_analytics"], array("style" => "width: 300px"));
					$tbl->insertAction("help", gettext("info"), "javascript: popup('http://www.google.com/analytics/', 'googlestats')");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow($searchopt);
				$tbl->addTabledata("", "header");
					$tbl->addCode(gettext("LetsStat sid code"));
					/* letsstat X1 is free available */
				$tbl->endTableData();
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[letsstat_analytics]", $cms["letsstat_analytics"], array("style" => "width: 300px"));
					$tbl->insertAction("help", gettext("info"), "javascript: popup('http://www.letsstat.com/', 'letsstat')");
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->addTableRow($searchopt);
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS search engine options"));
					if ($_REQUEST["custom"]) {
						$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
					}
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();
			$ary = array(401,403,404,602);
			foreach ($ary as $a) {
				$tbl->addTableRow();
					$tbl->insertTableData(gettext("Custom error page")." ".$a, "", "header");
					$tbl->addTableData(array("colspan"=>3), "data");
						$tbl->addTextField(sprintf("cms[custom_%d]", $a), $cms["custom_".$a], array("style" => "width: 80px;"));
						$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscustom_$a');");
					$tbl->endTableData();
				$tbl->endTableRow();
			}
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("Custom loginprofile page"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField(sprintf("cms[custom_loginprofile]", $a), $cms["custom_loginprofile"], array("style" => "width: 80px;"));
					$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscustom_loginprofile');");
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->addTableRow();
				$tbl->insertTableData(gettext("Custom feedback login page"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField(sprintf("cms[custom_feedback]", $a), $cms["custom_feedback"], array("style" => "width: 80px;"));
					$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscustom_feedback');");
				$tbl->endTableData();
			$tbl->endTableRow();
			if ($siteroot == "R" || !$siteroot) {
				if ($cms["cms_shop"]) {
					/* shop options */
					$tbl->addTableRow($searchopt);
						$tbl->addTableData(array("colspan" => 4), "header");
							$tbl->insertAction("view_all", "", "");
							$tbl->addSpace();
							$tbl->addCode(gettext("CMS shop information"));
							if ($_REQUEST["custom"]) {
								$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
							}
						$tbl->endTableData();
						$tbl->addTableData();
							$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
							$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
						$tbl->endTableData();
					$tbl->endTableRow();

					$tbl->addTableRow();
						$tbl->insertTableData(gettext("shopping cart info page"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[cms_shop_page]", $cms["cms_shop_page"], array("style" => "width: 80px;"));
							$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscms_shop_page');");
						$tbl->endTableData();
					$tbl->endTableRow();
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("shopping order page"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[cms_shop_results]", $cms["cms_shop_results"], array("style" => "width: 80px;"));
							$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscms_shop_results');");
						$tbl->endTableData();
					$tbl->endTableRow();
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("custom iDeal error page"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[custom_shop_error]", $cms["custom_shop_error"], array("style" => "width: 80px;"));
							$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscustom_shop_error');");
						$tbl->endTableData();
					$tbl->endTableRow();
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("custom iDeal cancel page"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[custom_shop_cancel]", $cms["custom_shop_cancel"], array("style" => "width: 80px;"));
							$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage('cmscustom_shop_cancel');");
						$tbl->endTableData();
					$tbl->endTableRow();

					/* ideal info */
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("shop valuta"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$sel = array(
								"EUR" => "EUR &euro;"
							);
							$tbl->addSelectField("cms[ideal_currency]", $sel, $cms["ideal_currency"]);
						$tbl->endTableData();
					$tbl->endTableRow();

					/* ideal info */
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("iDeal test mode"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$sel = array(
								0 => gettext("production mode"),
								1 => gettext("test mode")
							);
							$tbl->addSelectField("cms[ideal_test_mode]", $sel, $cms["ideal_test_mode"]);
						$tbl->endTableData();
					$tbl->endTableRow();

					$tbl->addTableRow();
						$tbl->insertTableData(gettext("iDeal type"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							//$tbl->addTextField("cms[cms_shop_results]", $cms["cms_shop_results"], array("style" => "width: 300px;"));
							$sel = array(
								"0" => "no iDeal integration",
								"rabolite" => "RaboLite integration"
							);
							$tbl->addSelectField("cms[ideal_type]", $sel, $cms["ideal_type"]);
						$tbl->endTableData();
					$tbl->endTableRow();
					/* ideal info */
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("iDeal Merchant ID"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[ideal_merchant_id]", $cms["ideal_merchant_id"], array("style" => "width: 300px;"));
						$tbl->endTableData();
					$tbl->endTableRow();
					/* ideal info */
					$tbl->addTableRow();
						$tbl->insertTableData(gettext("iDeal Secret key"), "", "header");
						$tbl->addTableData(array("colspan"=>3), "data");
							$tbl->addTextField("cms[ideal_secret_key]", $cms["ideal_secret_key"], array("style" => "width: 300px;"));
							$tbl->insertAction("toggle", gettext("generate"), "javascript: genRandomKey();");
							$tbl->start_javascript();
							$tbl->addCode("
								function genRandomKey()
								{
									var cf = confirm('Are you sure you want to generate a new key?');
									if (cf == true) {
										var length = 16;
										var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
										var pass = '';
										for(x=0; x < length; x++) {
											i = Math.floor(Math.random() * 62);
											pass += chars.charAt(i);
										}
										document.getElementById('cmsideal_secret_key').value = pass;
									}
								}
							");
							$tbl->end_javascript();
						$tbl->endTableData();
					$tbl->endTableRow();
				}
			}
			$tbl->endTable();
			$tbl->start_javascript();
			$tbl->addCode("
				var pick_field = '';
				function pageValue(id) {
					document.getElementById(pick_field).value = id;
				}
				function pickPage(field) {
					pick_field = field;
					var infile = document.getElementById(pick_field).value;
					popup('?mod=cms&action=cms_pagelist&in=' + infile, 'pagepick', 640, 400, 1);
				}
			");
			$tbl->end_javascript();
			$venster->addCode($tbl->generate_output());

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveSiteInfo");
		$output->addHiddenField("siteroot", $siteroot);

		$output->addCode($venster->generate_output());
		$output->endTag("form");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editAccountsList() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("external accounts")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAccountList();

		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("username"), "%username" );
		$view->addMapping( gettext("email"), "%email" );
		$view->addMapping( gettext("enabled"), "%is_enabled_h" );
		$view->addMapping( gettext("registered"), "%is_active_h" );
		$view->addMapping( gettext("registration date"), "%registration_date_h" );
		$view->addMapping( gettext("address"), "%address_name" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=cms&action=editAccount&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAccount&id=", "%id", "&user_id=", $user_id, "';")
			)
		));
		$venster->addCode($view->generate_output());

		$venster->insertAction("new", gettext("new user"), "?mod=cms&action=editAccount");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editAccount($id) {
		require(self::include_dir."editAccount.php");
	}

	public function editAbbreviation($id) {
		require(self::include_dir."editAbbr.php");
	}

	public function editAuthorisations($id) {
		require(self::include_dir."editAuthorisations.php");
	}
	public function dateOptions($id) {
		require(self::include_dir."dateOptions.php");
	}
	public function siteTemplates() {
		require(self::include_dir."siteTemplates.php");
	}
	public function editTemplate($id) {
		require(self::include_dir."editTemplate.php");
	}

	public function highlight_init() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);
			$output->addTag("form", array(
				"id" => "velden",
				"method" => "post",
				"action" => "index.php"
			));
			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "highlight_show");
			$output->addTextArea("contents", array(
				"style" => "display: none",
				"wrap"  => "off",
				"width" => "800",
				"height" => "10"
			));
			$output->start_javascript();
				$output->addCode("
					document.getElementById('contents').value = parent.document.getElementById('cmsdata').value;
					document.getElementById('velden').submit();
			");
			$output->end_javascript();

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function highlight_show($type) {
		$output = new Layout_output();
		$output->load_javascript("editarea/edit_area_full.js", 1);
		$output->start_javascript();
		if ($type == "main")
			$type = "php";

		$output->addCode("
  			// initialisation
  			function handleClassFocus() {
  				void(0);
  			}
				editAreaLoader.init({
					id: \"contents\"       // id of the textarea to transform
					,start_highlight: true  // if start with highlight
					,allow_resize: \"no\"
					,language: \"en\"
					,syntax: \"$type\"
					,min_height: 460
					,min_width: 740
					,save_callback: \"my_save\"
					,plugins: \"charmap\"
					,allow_toggle: true
					,font_size: \"9\"
					,font_family: \"monospace\"
					,charmap_default: \"arrows\"
					,toolbar: \"save, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help\"
				});
        function my_save(content){
        	document.getElementById('contents').value = editAreaLoader.getValue('contents');
        	saveSettings();
        }
		");
		$output->end_javascript();
		return $output->generate_output();
	}

	public function dateOptionsItemEdit($id) {
		require(self::include_dir."dateOptionsItemEdit.php");
	}

	public function metadataDefinitions() {
		require(self::include_dir."metadataDefinitions.php");
	}
	public function metadataDefinitionsEdit() {
		require(self::include_dir."metadataDefinitionsEdit.php");
	}

	public function metadata($id) {
		require(self::include_dir."metadata.php");
	}

	private function switchFieldType(&$tbl, &$v) {
		$meta = "meta";
		$return = false;

		switch ($v["field_type"]) {
			case "text":
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextField(sprintf($meta."[%s]", $v["id"]), $v["value"]);
				}
				break;
			case "textarea":
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextArea(sprintf($meta."[%s]", $v["id"]), $v["value"], array(
						"style" => "width: 600px; height: 180px;"
					), $meta.$v["id"]);
					$return = $meta.$v["id"];
				}
				break;
			case "datetime":
				$days[0] = "-";
				for ($i=1;$i<=31;$i++) {
					$days[$i] = $i;
				}
				$months[0] = "-";
				for ($i=1;$i<=12;$i++) {
					$months[$i] = $i;
				}
				$year[0] = "-";
				for ($i=1990;$i!=date("Y")+5;$i++) {
					$year[$i] = $i;
				}
				if ($output_only) {
					if ($v["value"] > 0) {
						$tbl->addCode(date("d-m-Y", $v["value"]));
					} else {
						$tbl->addCode("--");
					}
				} else {
					if ($v["value"] > 0) {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["id"]), $days, date("d", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["id"]), $months, date("m", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["id"]), $year, date("Y", $v["value"]));
					} else {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["id"]), $days, 0);
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["id"]), $months, 0);
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["id"]), $year, 0);
					}
				}
				break;
			case "shop":
				$tbl->addCode(gettext("please specify in page properties"));
				break;
			case "select":
				$sel = array();
				$v["default_value"] = explode("\n", $v["default_value"]);
				foreach ($v["default_value"] as $z) {
					$z = trim($z);
					$sel[$z] = $z;
				}
				if ($output_only) {
					$tbl->addCode($sel[$v["value"]]);
				} else {
					$tbl->addSelectField(sprintf($meta."[%s]", $v["id"]), $sel, $v["value"]);
				}
				break;
			case "checkbox":
				if ($output_only) {
					$tbl->addCode(nl2br($v["value"]));
				} else {

					$sel = array();
					$values = explode("\n", $v["value"]);
					$v["default_value"] = explode("\n", $v["default_value"]);
					foreach ($v["default_value"] as $z) {
						if (in_array(trim($z), $values)) {
							$checked = 1;
						} else {
							$checked = 0;
						}
						$tbl->addCheckBox(sprintf($meta."[%s][%s]", $v["id"], $z), $z, $checked);
						$tbl->addCode($z);
						$tbl->addTag("br");
					}
					$tbl->addTag("span", array("style" => "display: none;"));
						$tbl->addCheckBox(sprintf($meta."[%s][%s]", $v["id"], 0), "", 1);
					$tbl->endTag("span");
				}
				break;
		}
		return $return;

	}
	public function cmsList($id) {
		require(self::include_dir."cmsList.php");
	}

	public function cmsForm($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("form generator")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getFormData($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);
		$view->addMapping(gettext("order"), "%order");
		$view->addMapping(gettext("name"), "%field_name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("type"), "%field_type_h");
		$view->addMapping(gettext("value"), "%field_value");
		$view->addMapping(gettext("properties"), "%options");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: popup('?mod=cms&action=cmsformedit&pageid=", "%pageid", "&id=", "%id", "&user_id=", $user_id, "');")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=cmsformdelete&id=", "%pageid", "&itemid=", "%id", "';")
			)
		));

		$venster->addCode($view->generate_output());
		$venster->insertAction("new", gettext("add new field"), "javascript: popup('?mod=cms&action=cmsformedit&pageid=".$_REQUEST["id"]."', 'forms', 650, 580, 1);");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");

		$venster->addTag("br");
		$venster->addTag("br");

		$mode = $cms_data->getFormMode($id);

		/*
		$venster->addTag("form", array(
			"id" => "formmode",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "saveFormMode");
		$venster->addHiddenField("id", $id);
		$sel = array(
			//0 => gettext("send with mail"),
			1 => gettext("store in database and send mail"),
			//2 => gettext("use as enquete or poll (no mail)")
		);
		$venster->addCode(gettext("Select mode for this form").": ");
		$venster->addSelectField("cms[mode]", $sel, $mode);
		$venster->insertAction("forward", gettext("selecteer"), "javascript: document.getElementById('formmode').submit();");

		$venster->endTag("form");
		$venster->addTag("br");
		$venster->addTag("br");
		*/
		$err = $cms_data->getFormErrors($id);
		$err = implode("<br>", $err);
		$venster->insertTag("b", $err);


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function cmsFormResults($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("form results")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getFormResults($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms["data"]);

		$view->addSubMapping(array(
			gettext("date")." ", "%datetime_start", " ".gettext("till")." ", "%datetime_end", " (ip: ", "%ip_address", ")"
		), 1);

		foreach ($cms["fields"] as $k=>$v) {
			$view->addMapping($v, "%".$k);
		}
		$view->addMapping("", "%%complex_actions");
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=cmsresultdelete&id=", $id, "&itemid=", "%id", "';")
			)
		));

		$venster->addCode($view->generate_output());
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->addTag("br");

		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function cmsGallery($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => ($id==-1) ? gettext("banners"):gettext("photo gallery")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getGalleryData($id, $_REQUEST["cmsfilter"]);

		if ($id > -1) {
			$this->addMenuItems(&$venster);
			$venster->generateMenuItems();
		}
		$venster->addVensterData();

		$cmsfilter = $_REQUEST["cmsfilter"];
		if (!$cmsfilter["s_timestamp_year"]) {
			$cmsfilter["date_start"] = mktime();
		} else {
			$cmsfilter["date_start"] = mktime(0,0,0,
				$cmsfilter["s_timestamp_month"],
				$cmsfilter["s_timestamp_day"],
				$cmsfilter["s_timestamp_year"]
			);
		}
		if (!$cmsfilter["e_timestamp_year"]) {
			$cmsfilter["date_end"] = mktime();
		} else {
			$cmsfilter["date_end"] = mktime(0,0,0,
				$cmsfilter["e_timestamp_month"],
				$cmsfilter["e_timestamp_day"],
				$cmsfilter["e_timestamp_year"]
			);
		}

		$venster->addTag("form", array(
			"id"     => "date_range",
			"method" => "get",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "cmsgallery");
		$venster->addHiddenField("id", $id);

		$tbl = new Layout_table();
		if ($id == -1) {
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "header");
					$tbl->insertAction("calendar_reg_hour", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("select date range"));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addCode(gettext("start date"));
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					for ($i=1; $i<=31; $i++) {
						$days[$i] = $i;
					}
					for ($i=1; $i<=12; $i++) {
						$months[$i] = $i;
					}
					for ($i=2000; $i<=date("Y")+5; $i++) {
						$years[$i] = $i;
					}
					for ($i=0; $i<=23; $i++) {
						$hour[$i] = $i;
					}
					for ($i=0; $i<60; $i+=15) {
						$min[$i] = sprintf("%02s", $i);
					}

					$tbl->addSelectField("cmsfilter[s_timestamp_day]",   $days,   date("d", $cmsfilter["date_start"]));
					$tbl->addSelectField("cmsfilter[s_timestamp_month]", $months, date("m", $cmsfilter["date_start"]));
					$tbl->addSelectField("cmsfilter[s_timestamp_year]",  $years,  date("Y", $cmsfilter["date_start"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmsfilters_timestamp_day')", "document.getElementById('cmsfilters_timestamp_month')", "document.getElementById('cmsfilters_timestamp_year')" ));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addCode(gettext("end date"));
				$tbl->endTableData();
				$tbl->addTableData("", "data");

					$tbl->addSelectField("cmsfilter[e_timestamp_day]",   $days,   date("d", $cmsfilter["date_end"]));
					$tbl->addSelectField("cmsfilter[e_timestamp_month]", $months, date("m", $cmsfilter["date_end"]));
					$tbl->addSelectField("cmsfilter[e_timestamp_year]",  $years,  date("Y", $cmsfilter["date_end"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmsfiltere_timestamp_day')", "document.getElementById('cmsfiltere_timestamp_month')", "document.getElementById('cmsfiltere_timestamp_year')" ));
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("search"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cmsfilter[search]", $cmsfilter["search"]);
				$tbl->insertAction("forward", gettext("filter"), "javascript: document.getElementById('date_range').submit();");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addCode($tbl->generate_output());

		$venster->endTag("form");

		$view = new Layout_view();
		$view->addData($cms);
		$view->addMapping(gettext("order"), "%order");
		if ($id > 0)
			$view->addMapping(gettext("change"), "%%complex_order");

		$view->addMapping(gettext("name"), "%file_short");
		if ($id == -1) {
			$view->addMapping(gettext("rating"), "%rating_h");
			$view->addMapping(gettext("website"), "%url");
			$view->addMapping(gettext("views"), "%views");
			$view->addMapping(gettext("visits"), "%visits");
		} else {
			$view->addMapping(gettext("description"), "%description_h");
		}
		$view->addMapping("", "%%complex_actions", "", "nowrap");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: popup('?mod=cms&action=cmsgalleryitemedit&id=", "%pageid", "&itemid=", "%id", "', 'gallery', 780, 550, 1);")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Are you sure you want to delete this entry?'))) document.location.href='index.php?mod=cms&action=cmsgalleryitemdelete&item=", "%id" ,"&id=", "%pageid", "&itemid=", "%id", "';")
			)
		));
		$view->defineComplexMapping("complex_order", array(
			array(
				"type" => "action",
				"src"  => "up",
				"alt"  => gettext("up"),
				"link" => array("?mod=cms&action=galleryitemswitch&direction=up&id=", "%pageid", "&itemid=", "%id")
			),
			array(
				"type" => "action",
				"src"  => "down",
				"alt"  => gettext("down"),
				"link" => array("?mod=cms&action=galleryitemswitch&direction=down&id=", "%pageid", "&itemid=", "%id")
			),
		));

		if (count($cms) >= 6) {
			$limit_height = "height: 300px; overflow:auto;";
		} else {
			$limit_height = "";
		}

		$venster->addTag("div", array(
			"class"  => "limit_height",
			"style" => $limit_height
		));
			$venster->addCode($view->generate_output());
		$venster->endTag("div");

		$fs_output = new Filesys_output();
		$venster->addTag("hr");
		$venster->addTag("form", array(
			"id"     => "file_upload",
			"method" => "post",
			"action" => "index.php",
			"enctype" => "multipart/form-data"
		));
		$venster->addCode($fs_output->show_fileupload()->generate_output());
		$venster->endTag("form");
		$venster->start_javascript();
			$venster->addCode("
				function filesys_upload_files() {
					document.getElementById('file_upload').submit();
				}
			");
		$venster->end_javascript();

		#$venster->insertAction("new", gettext("add new field"), "javascript: popup('https://covide.atreides.aol/index.php?mod=filesys&action=opendir&subaction=cmsgallery', 'forms', 650, 580, 1);");
		#$venster->insertAction("close", gettext("close"), "javascript: window.close();");

		$venster->addTag("br");
		$venster->addTag("br");

		$cms = $cms_data->getGallerySettings($id);

		$table = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		/* layout settings */
		if ($id > -1) {
			$table->addTableRow();
				$table->insertTableData(gettext("Font settings"), "", "header");
				$table->addTableData();
					$table->addSelectField("cms[font]", array(
						"arial,serif"       => gettext("Arial"),
						"courier,monospace" => gettext("Courier New"),
						"georgia,serif"     => gettext("Georgia"),
						"tahoma,serif"      => gettext("Tahoma"),
						"times,serif"       => gettext("Times new roman"),
						"verdana,serif"     => gettext("Verdana"),
						"palatino linotype,serif" => gettext("Palatino Linotype"),
						"book antiqua,serif"      => gettext("Book Antiqua")
					), $cms["font"]);
					$table->addSelectField("cms[fontsize]", array(
						"1" => "1 (8pt)",
						"2" => "2 (10pt)",
						"3" => "3 (12pt)",
						"4" => "4 (14pt)",
						"5" => "5 (18pt)",
						"6" => "6 (24pt)",
						"7" => "7 (36pt)"
					), $cms["font_size"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("Layout"), "", "header");
				$table->addTableData("", "data");
					$sel = array(
						0 => gettext("table"),
						1 => gettext("slideshow"),
						//2 => gettext("slideshow (horizontal)"),
						3 => gettext("listing")
					);
					$table->addSelectField("cms[gallerytype]", $sel, $cms["gallerytype"]);
				$table->endTableData();
			$table->endTableRow();
			/* cols */
			$table->addTableRow(array(
				"id" => "layer_cols"
			));
				$table->addTableData("", "header");
					$table->addCode(gettext("Number of columns"));
					$table->addTag("br");
					$table->addSpace();
					$table->addCode(gettext("(if table layout)"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = array();
					for ($i=1;$i<=10;$i++) {
						$sel[$i]=$i;
					}
					$table->addSelectField("cms[cols]", $sel, $cms["cols"]);
				$table->endTableData();
			$table->endTableRow();
			/* cols */
			$table->addTableRow(array(
				"id" => "layer_rows"
			));
				$table->addTableData("", "header");
					$table->addCode(gettext("Number of rows per page"));
					$table->addTag("br");
					$table->addSpace();
					$table->addCode(gettext("(if table or list layout)"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = array();
					for ($i=1;$i<=10;$i++) {
						$sel[$i]=$i;
					}
					$table->addSelectField("cms[rows]", $sel, $cms["rows"]);
				$table->endTableData();
			$table->endTableRow();
			/* allow max size */
			$table->addTableRow();
				$table->insertTableData(gettext("Allow viewing full size"), "", "header");
				$table->addTableData("", "data");
					$sel = array(
						0 => gettext("no"),
						1 => gettext("yes")
					);
					$table->addSelectField("cms[fullsize]", $sel, $cms["fullsize"]);
				$table->endTableData();
			$table->endTableRow();
			/* thumbnail size medium */
			$table->addTableRow();
				$table->insertTableData(gettext("Max thumbnail size (medium)"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("cms[bigsize]", $cms["bigsize"]);
				$table->endTableData();
			$table->endTableRow();
		} else {
			$table->addHiddenField("cms[thumbsize]", 0);
			$table->addHiddenField("cms[gallerytype]", 0);
			$table->insertTag("span", "", array(
				"style" => "display: none;",
				"id"    => "layer_cols"
			));
			$table->insertTag("span", "", array(
				"style" => "display: none;",
				"id"    => "layer_rows"
			));
		}
		/* thumbnail size small */
		$table->addTableRow();
			$table->insertTableData(gettext("Max thumbnail size (small)"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("cms[thumbsize]", $cms["thumbsize"]);
			$table->endTableData();
		$table->endTableRow();

		$div = new Layout_output();
		$div->addTag("img", array(
			"src" => "img/bar.png"
		));
		$table->addTableRow(array(
			"id" => "layer_busy",
			"style" => "xdisplay: none;"
		));
			$table->addTableData("", "data");
				$table->insertTag("marquee", $div->generate_output(), array(
					"id"           => "marquee_progressbar",
					"behavoir"     => "scroll",
					"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; visibility:hidden; margin-top: 10px;",
					"scrollamount" => 3,
					"direction"    => "right",
					"scrolldelay"  => 60
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array(
			"id" => "layer_actions"
		));
			$table->addTableData("", "data");
				$table->insertAction("save", gettext("save"), "javascript: saveSettingsGallery();");
				$table->insertAction("close", gettext("close"), "javascript: window.close();");
			$table->endTableData();
		$table->endTableRow();

		$table->endTable();
		$venster->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "saveGallerySettings");
		$venster->addHiddenField("id", $id);
		$venster->addCode($table->generate_output());
		$venster->endTag("form");


		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."script_cms.js");
		$output->start_javascript();
			$output->addCode("
				function updateLayerCols() {
					return true;
					document.getElementById('layer_cols').style.visibility = 'hidden';
					document.getElementById('layer_rows').style.visibility = 'hidden';
					if (document.getElementById('cmsgallerytype').value == 0) {
						document.getElementById('layer_cols').style.visibility = 'visible';
						document.getElementById('layer_rows').style.visibility = 'visible';
					}
					if (document.getElementById('cmsgallerytype').value == 3) {
						document.getElementById('layer_cols').style.visibility = 'visible';
					}
				}
				document.getElementById('cmsgallerytype').onchange = function() {
					updateLayerCols();
				}
				addLoadEvent(updateLayerCols());
			");
		$output->end_javascript();

		/* add a container for multiple downloads */
		$output->insertTag("div", "", array(
			"id"    => "download_container",
			"style" => "display: none; width: 0px; height: 0px;"
		));

		$output->exit_buffer();
	}

	public function cmsFormEdit($pageid, $id) {
		require(self::include_dir."cmsFormEdit.php");
	}

	public function preparePageDelete($pageid) {
		set_time_limit(60*60);
		session_write_close();

		require(self::include_dir."deletePageConfirm.php");
	}

	public function cmsGalleryItemEdit($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$cms_data = new Cms_data();
		$cms = $cms_data->getGalleryItem($id);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => ($cms["pageid"] == -1) ? gettext("alter banner"):gettext("alter gallery item")
		));

		$venster->addVensterData();

			$tbl = new Layout_table(array(
				"cellspacing" => 1
			));
			/* order */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("order"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextField("cms[order]", $cms["order"], array("style" => "width: 50px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			if ($cms["pageid"] == -1) {
				/* website */
				$tbl->addTableRow();
					$tbl->insertTableData(gettext("website"), "", "header");
					$tbl->addTableData("", "data");
						$tbl->addTextField("cms[url]", $cms["url"], array("style" => "width: 250px"));
					$tbl->endTableData();
				$tbl->endTableRow();
				/* rating */
				$tbl->addTableRow();
					$tbl->insertTableData(gettext("rating"), "", "header");
					$tbl->addTableData("", "data");
						$sel = array(
							0 => gettext("inactive")
						);
						for ($i=1;$i<=10;$i++) {
							$sel[$i] = $i."x";
						}
						for ($i=15;$i<=30;$i+=5) {
							$sel[$i] = $i."x";
						}
						for ($i=40;$i<=100;$i+=10) {
							$sel[$i] = $i."x";
						}
						$tbl->addSelectField("cms[rating]", $sel, $cms["rating"]);
					$tbl->endTableData();
				$tbl->endTableRow();
			}
			/* field name */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("name"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextField("cms[file]", $cms["file"], array("style" => "width: 250px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* default value */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("description"), "", "header");
				$tbl->addTableData("", "data");
					/*
					$tbl->addTextArea("cms[description]", $cms["description"], array(
						"style" => "width: 300px; height: 150px;"
					));
					*/
					$editor = new Layout_editor();
					$ret = $editor->generate_editor(1);
					$tbl->addTextArea("cms[description]", trim($cms["description"]), array(
						"style" => "width: 570px; height: 270px;"), "contents"
					);
					if ($ret !== false)
						$tbl->addCode($ret);

				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );
			$venster->start_javascript();
				$venster->addCode("
					function syncDescription() {
						if (window.sync_editor_mini)
							sync_editor_mini();
					}
				");
			$venster->end_javascript();
			$venster->insertAction("back", gettext("back"), "javascript: window.close();");
			$venster->insertAction("save", gettext("new item"), "javascript: syncDescription(); saveSettings();");

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveGalleryItem");
		$output->addHiddenField("id", $id);

		$output->addCode($venster->generate_output());
		$output->endTag("form");

		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function linkchecker() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS linkchecker")
		));
		$cms_data = new Cms_data();

		$div = new Layout_output();
		$div->addTag("img", array(
			"src" => "img/bar.png"
		));
		$venster->addVensterData();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData();
					if ($cms_data->checkLinkcheckerStatus()) {
						$tbl->addCode(gettext("Linkchecker is running")."...");
						$tbl->insertTag("marquee", $div->generate_output(), array(
							"id"           => "marquee_progressbar",
							"behavoir"     => "scroll",
							"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; margin-top: 10px;",
							"scrollamount" => 3,
							"direction"    => "right",
							"scrolldelay"  => 60
						));
						$tbl->start_javascript();
							$tbl->addCode("
								setTimeout('location.href = location.href;', 10000);
							");
						$tbl->end_javascript();

					} else {
						$tbl->insertTag("a", gettext("Start linkchecker"), array(
							"href" => "javascript: startLinkChecker();"
						));
						$tbl->insertAction("ok", gettext("Start linkchecker"), "javascript: startLinkChecker();");
						$tbl->addSpace();
							$lr = $cms_data->lastLinkchecker();
							$tbl->insertTag("b", sprintf("%s: %s", gettext("last run"), $lr));
						$tbl->addSpace();
					}
					$tbl->addTag("br");
					$tbl->addTag("br");
					$tbl->insertTag("b", gettext("The following errors are found").":");

				$tbl->endTableData();
				$tbl->addTableData(array(
					"valign" => "top"
				));
					$tbl->insertAction("close", gettext("Close window"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();

					$start = $_REQUEST["start"];
					if (!$start)
						$start = 0;

					$lc = $cms_data->checkLinkcheckerResults();

					$total = count($lc);
					$lc = array_slice($lc, $start, $this->pagesize);

					$view = new Layout_view();
					$view->addData($lc);

					$view->addMapping( gettext("page"), "%pageid" );
					$view->addMapping( gettext("name"), "%name" );
					$view->addMapping( gettext("result"), "%result" );
					$view->setHtmlField("url");

					$view->addMapping( " ", "%%complex_actions" );
					$view->addSubMapping("%url", 0);

					$view->defineComplexMapping("complex_actions", array(
						array(
							"type" => "action",
							"src"  => "edit",
							"alt"  => gettext("edit"),
							"link" => array("javascript: opener.cmsEdit('cmsEditor','", "%pageid", "','');")
						)
					));
					$tbl->addCode($view->generate_output());

					$paging = new Layout_paging();
					$paging->setOptions($start, $total, "?mod=cms&action=linkchecker&start=%%");
					$tbl->addCode($paging->generate_output());

				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();

			$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function showOptionsInfo($id) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($id);

		$table = new Layout_table(array(
			"cellspacing" => 1, "cellpadding" => 1
		));
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("details of pageid")." [".$id."]");
				$table->addSpace();
				$table->insertAction("view_all", gettext("view page"), sprintf(
					"javascript: popup('http://%s/page/%s.htm');", $_SERVER["HTTP_HOST"], (($data["pageAlias"])?$data["pageAlias"]:$data["id"])));
				$table->insertAction("edit", gettext("view page"), sprintf(
					"javascript:cmsEdit('cmsEditor','%d','');", $id));

			$table->endTableData();
		$table->endTableRow();
		/* page title */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page title"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode($data["pageTitle"]);
			$table->endTableData();
		$table->endTableRow();
		/* page alias */
		if ($data["pageAlias"]) {
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("page alias"));
				$table->endTableData();
				$table->addTableData();
					$table->addCode($data["pageAlias"].".htm");
				$table->endTableData();
			$table->endTableRow();
		}
		/* page label */
		if ($data["pageLabel"]) {
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("page label"));
				$table->endTableData();
				$table->addTableData();
					$table->addCode($data["pageLabel"]);
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("publication date"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode(date("d-m-Y H:i", $data["datePublication"]));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("last modification"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode(date("d-m-Y H:i", $data["date_changed"]));
			$table->endTableData();
		$table->endTableRow();

		/* start page options */
		$opts = new Layout_table(array(
			"cellspacing" => 1, "cellpadding" => 2
		));
		$opts->addTableRow();
			$opts->addTableData("", "data");
				if ($data["isActive"])
					$opts->insertAction("enabled", "", "");
				else
					$opts->insertAction("disabled", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				if ($data["isActive"])
					$opts->addCode(gettext("This page is active"));
				else
					$opts->addCode(gettext("This page is inactive"));
			$opts->endTableData();
		$opts->endTableRow();
		/* ispublic */
		if ($data["isPublic"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("go_support", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is public"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* ismenuitem */
		if ($data["isMenuItem"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("go_desktop", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is a menuitem"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* istemplate */
		if ($data["isTemplate"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("ftype_calc", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is available as template"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* istemplate */
		if ($data["pageRedirect"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("reload", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a redirect"));
					$opts->addTag("br");
					$opts->addCode(gettext("Location").": ");
					$opts->insertTag("a", $data["pageRedirect"], array(
						"href" => sprintf("javascript: popup('%s');", $data["pageRedirect"])
					));
					$opts->addTag("br");
					if ($data["pageRedirectPopup"]) {
						$opts->addCode(gettext("Redirect in a popup"));
						$opts->addSpace();
						if ($data["popup_height"] && $data["popup_width"])
							$opts->addCode(sprintf("%dx%d ", $data["popup_width"], $data["popup_height"]));
						if ($data["popup_hidenav"])
							$opts->addCode(gettext("and hide navigation"));

					}
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* search override */
		if ($data["search_override"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("mail_tracking", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has custom SEO settings"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* date info */
		if ($data["isDateRange"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("calendar_today", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a publication range"));
					$opts->addTag("br");
					if ($data["date_start"] && $data["date_end"]) {
						$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
						$opts->addCode(" - ");
						$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
					}	elseif ($data["date_start"]) {
						$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
						$opts->addCode(" - ".gettext("no end date"));
					} else {
						$opts->addCode(gettext("no start date")." - ");
						$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
					}

				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isDate"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("calendar_reg_hour", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has calendar items"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isList"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("mail_templates", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a listing"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isForm"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("state_special", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a contact form"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["useMetaData"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("mail_readconfirm", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has extra meta information"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isSticky"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("down", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is sticky / locked"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isGallery"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("ftype_image", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has an image gallery"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($cms_data->checkPagePermissions($id)) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("permissions", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has custom page permissions"));
				$opts->endTableData();
			$opts->endTableRow();
		}



		$opts->endTable();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page options"));
			$table->endTableData();
			$table->addTableData("", "header");
				$table->addCode($opts->generate_output());
			$table->endTableData();
		$table->endTableRow();

		$table->endTable();

		$buf = str_replace("'", "\'", preg_replace("/(\r|\n)/si", "", $table->generate_output()) );
		echo sprintf("infoLayer('%s');", $buf);
	}

	public function editAbbreviations() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("abbreviations")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAbbreviations();

		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("abbreviation"), "%abbreviation" );
		$view->addMapping( gettext("description"), "%description" );
		$view->addMapping( gettext("languages"), "%lang_h" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=cms&action=editAbbreviation&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAbbreviation&id=", "%id", "&user_id=", $user_id, "';")
			)
		));
		$venster->addCode($view->generate_output());

		$venster->insertAction("new", gettext("new abbreviation"), "?mod=cms&action=editAbbreviation");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsMailings($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("last mailings for this page")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getMailings($id);
		$emails = $cms_data->handleUpload();

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("date"), "%datetime_h" );
		$view->addMapping( gettext("emails"), "%email" );
		$venster->addCode($view->generate_output());


		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				if ($emails) {

					$email_data = new Email_data();
					$data = $cms_data->getPageById($id);

					$table->addTag("form", array(
						"id" => "mailing",
						"method" => "POST",
						"action" => "index.php"
					));
					$table->addTag("br");
					$html = sprintf("<b>%s</b> (%s)<br><br>", $data["pageTitle"], date("d-m-Y H:i", $data["datePublication"]));
					$html.= $email_data->html2text($data["pageData"]);
					$html = preg_replace("/\[\d{1,}\]/s", "", $html);

					$table->addCode(gettext("Total characters in this article").": ");
					$chars = mb_strlen($html);
					$table->insertTag("b", $chars);
					$table->addCode(".");
					$table->addCode(" ".gettext("Send % of article").": ");
					$sel = array(
						"10" => "10% - ".ceil($chars * (10/100))." ".gettext("chars"),
						"15" => "15% - ".ceil($chars * (15/100))." ".gettext("chars"),
						"25" => "25% - ".ceil($chars * (25/100))." ".gettext("chars"),
						"50" => "50% - ".ceil($chars * (50/100))." ".gettext("chars"),
						"75" => "75% - ".ceil($chars * (75/100))." ".gettext("chars"),
						"100" => "100% - ".($chars)." ".gettext("chars")
					);
					$table->addSelectField("length", $sel, "25");
					$table->addTag("br");

					$table->insertTag("b", gettext("The notification is going to be send to the following receipients")).":";
					$table->addSpace();
					$table->insertAction("mail_send", gettext("send notification"), "javascript: document.getElementById('mailing').submit();");
					$table->addTag("br");
					$table->addTag("br");
					$table->insertTag("div", $emails, array(
						"style" => "border: 1px solid #999; padding: 3px;"
					));
					$table->addHiddenField("mod", $_REQUEST["mod"]);
					$table->addHiddenField("id", $_REQUEST["id"]);
					$table->addHiddenField("action", "send_mailing");
					$table->addHiddenField("emails", $emails);
					$table->endTag("form");

				} else {
					$table->addTag("form", array(
						"id" => "uploadform",
						"method" => "POST",
						"enctype" => "multipart/form-data",
						"action" => "index.php"
					));
					$table->addTag("br");
					$table->insertTag("b", gettext("upload file"));
					$table->addTag("br");
					$table->addCheckBox("skipfirst", 1, 1);
					$table->addCode(gettext("skip first line in csv file (default)"));
					$table->addTag("br");
						$table->addHiddenField("MAX_FILE_SIZE", "67108864");
						$table->addHiddenField("mod", $_REQUEST["mod"]);
						$table->addHiddenField("id", $_REQUEST["id"]);
						$table->addHiddenField("action", "init_mailing");
						$table->addUploadField("binFile[]", array("size"=>"45") );
						$table->insertAction("forward", gettext("upload file"), "javascript: document.getElementById('uploadform').submit();");
					$table->endTag("form");
				}
				$table->addTag("br");
				$table->insertAction("close", gettext("close"), "javascript: window.close();");

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$venster->addCode($table->generate_output());

		$venster->endVensterData();
		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsBannerList($pick=0) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS banner management")
		));

		$cms_data = new Cms_data();
		$infile = $cms_data->stripHosts("\"".$_REQUEST["in"], 1);
		if (preg_match("/^\"\/{0,1}cmsgallery\/sponsors\/(\d{1,})$/s", $infile)) {
			$infile = (int)preg_replace("/^\"\/{0,1}cmsgallery\/sponsors\/(\d{1,})$/s", "$1", $infile);
		} else {
			$infile = 0;
		}

		$cms = $cms_data->getGalleryData(-1, array(
			"search" => $_REQUEST["search"],
			"highlight" => $infile
		));

		$venster->addVensterData();
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("name"), "%file" );
		$view->addMapping( gettext("rating"), "%rating_h" );
		$view->addMapping( gettext("url"), "%url" );
		$view->addMapping( " ", "%%complex_actions" );

		if ($pick) {
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"  => "action",
					"src"   => "important",
					"alt"   => gettext("this file is currently selected"),
					"link"  => array("javascript: alert('", gettext("this file is currently selected"), "');"),
					"check" => "%highlight"
				),
				array(
					"type"  => "action",
					"src"   => "file_attach",
					"alt"   => gettext("use as cms file"),
					"link"  => array("javascript: cmsPreview('", "%id", "', '".$GLOBALS["covide"]->webroot."');")
				),
				array(
					"type"  => "text",
					"text"  => array("<a name=\"file_", "%id", "\"></a>")
				)
			));
		} else {
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("edit"),
					"link" => array("?mod=cms&action=editBanner&id=", "%id")
				),
				array(
					"type" => "action",
					"src"  => "delete",
					"alt"  => gettext("delete"),
					"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteBanner&id=", "%id", "');")
				)
			));
		}
		$venster->addTag("form", array(
			"id" => "velden",
			"action" => "index.php",
			"method" => "get"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "pick_banner");
		$venster->addCode(gettext("Search").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit();");
		$venster->addTag("br");

		$venster->addCode($view->generate_output());
		$venster->endTag("form");

		$venster->start_javascript();
		$venster->addCode("
			function cmsPreview(id, webroot) {
				if (parent.document.getElementById('f_href')) {
					parent.document.getElementById('f_href').value = webroot + 'cmsgallery/sponsors/' + id + '&size=small';
				} else if (parent.document.getElementById('f_url')) {
					parent.document.getElementById('f_url').value = webroot + 'cmsgallery/sponsors/' + id + '&size=small'
					parent.onPreview();
				}
				void(0);
			}
		");
		$venster->end_javascript();
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsAliasHistory($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("alias history for this page")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAliasHistory($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("alias"), "%alias_h" );
		$view->addMapping( gettext("removed on"), "%datetime_h" );

		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("?mod=cms&action=deletealiashistory&itemid=", "%id", "&id=", $id)
			)
		));


		$venster->addCode($view->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsBannerEdit($id=0) {
		require(self::include_dir."editBanner.php");
	}

	public function validateSitemap($sitemap) {
		require(self::include_dir."validateSitemap.php");
	}

	public function cmsImport() {
		require(self::include_dir."cmsImport.php");
	}

	public function registerAccount($data) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("new visitor account registration")
		));

		$venster->addVensterData();

		if ($data["subaction"] == "save") {
			$cms_data = new Cms_data();
			$ret = $cms_data->registrationCheckErrors($data);
			if ($ret !== true) {
				$venster->insertTag("b", gettext("An error occured").": ");
				$venster->insertTag("b", $ret);
				$venster->addTag("br");
				$venster->addTag("br");
			} else {
				/* save registration */
				$cms_data->saveRegistration($data);
			}
		}

		if ($ret === true) {
			$venster->addCode("Your registration request has been received.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("You will receive an email within a few minutes with the confirmation message to activate your account.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertTag("a", gettext("close window"), array(
				"href" => "javascript: window.close();"
			));
			$venster->endVensterData();
			$output->addCode( $venster->generate_output() );

		} else {
			$venster->addCode("You are about to register a visitor account for this website.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("Please fill in the following form. After this, you will receive a confirmation email you have to confirm within 24 hours. After this confirmation your account will be activated.");
			$venster->addTag("br");
			$venster->addTag("br");

			$tbl = new Layout_table();
			/* username */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("preferred username"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[username]", $data["username"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email]", $data["email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email_cf]", $data["email_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* password */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("password"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addPasswordField("data[password]", $data["password"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* password cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm password"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addPasswordField("data[password_cf]", $data["password_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "right", "colspan" => 2));
					$tbl->insertTag("a", gettext("register account")." &gt;&gt;", array(
						"href" => "javascript: registerAccount();"
					));
				$tbl->endTableHeader();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->endVensterData();

			$output->addTag("form", array(
				"action" => "index.php",
				"method" => "post",
				"id"     => "formident"
			));

			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "registerAccount");
			$output->addHiddenField("data[subaction]", "save");
			$output->addHiddenField("data[uri]", ($data["uri"]) ? $data["uri"]:$_REQUEST["uri"]);
			$output->addHiddenField("data[siteroot]", ($data["siteroot"]) ? $data["siteroot"]:$_REQUEST["siteroot"]);

			$output->addCode( $venster->generate_output() );
			$output->endTag("form");
			$output->load_javascript(self::include_dir."registerAccount.js");
		}
		$output->exit_buffer();
	}

	public function registerAccountConfirm($userid, $hash, $site) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("new visitor account registration")
		));

		$venster->addVensterData();
			$cms_data = new Cms_data();
			$ret = $cms_data->updateRegistration($userid, $hash);
			switch ($ret) {
				case 0:
					$venster->addCode(gettext("Your account is now activated, you can now login with your username and password."));
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->insertTag("a", gettext("click here to go to the website"), array(
						"href" => sprintf(base64_decode($site))
					));

				case 1:
				case 2:
			}
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function recoverAccountPassword($data) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("recover account password")
		));

		$venster->addVensterData();

		if ($data["subaction"] == "save") {
			$cms_data = new Cms_data();
			$ret = $cms_data->recoverPassword($data);
			if ($ret !== true) {
				$venster->insertTag("b", $ret);
				$venster->addTag("br");
				$venster->addTag("br");
			}
		}

		if ($ret === true) {
			$venster->addCode("Your account information has been recovered.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("You will receive an email within a few minutes with your account details.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertTag("a", gettext("close window"), array(
				"href" => "javascript: window.close();"
			));
			$venster->endVensterData();
			$output->addCode( $venster->generate_output() );

		} else {
			$venster->addCode("You are about to recover a visitor account password for this website.");
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("Please fill in the following form. After this, you will receive a confirmation email with your password");
			$venster->addTag("br");
			$venster->addTag("br");

			if ($_REQUEST["email"]) {
				$data["email"]    = $_REQUEST["email"];
				$data["email_cf"] = $_REQUEST["email"];
			}

			$tbl = new Layout_table();
			/* email */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("your email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email]", $data["email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email_cf]", $data["email_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "right", "colspan" => 2));
					$tbl->insertTag("a", gettext("check account")." &gt;&gt;", array(
						"href" => "javascript: recoverAccount();"
					));
				$tbl->endTableHeader();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->endVensterData();

			$output->addTag("form", array(
				"action" => "index.php",
				"method" => "post",
				"id"     => "formident"
			));

			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "recoverAccountPassword");
			$output->addHiddenField("data[subaction]", "save");
			$output->addHiddenField("data[uri]", ($data["uri"]) ? $data["uri"]:$_REQUEST["uri"]);
			$output->addHiddenField("data[siteroot]", ($data["siteroot"]) ? $data["siteroot"]:$_REQUEST["siteroot"]);

			$output->addCode( $venster->generate_output() );
			$output->endTag("form");
			$output->load_javascript(self::include_dir."registerAccount.js");
		}
		$output->exit_buffer();
	}

	public function siteCounters() {
		require(self::include_dir."siteCounters.php");
	}

	public function cmsPageList() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("list of pages")
		));

		$cms_data = new Cms_data();
		$data = $cms_data->searchPages($_REQUEST["search"], $_REQUEST["start"], $_REQUEST["in"]);

		$venster->addVensterData();

		$venster->addCode(gettext("search for page").":");
		$venster->addSpace(2);

		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit();");

		$venster->start_javascript();
		$venster->addCode("
			function blader(page) {
				document.getElementById('start').value = page;
				document.getElementById('velden').submit();
			}
			function cmsPreview(id, webroot) {
				if (parent.document.getElementById('f_href')) {
					parent.document.getElementById('f_href').value = webroot + 'page/'+id;
				} else if (parent.document.getElementById('f_url')) {
					parent.document.getElementById('f_url').value = webroot + 'page/'+id;
					parent.onPreview();
				} else if (opener.document.getElementById('cmspageRedirect')) {
					opener.document.getElementById('cmspageRedirect').value = '/page/'+id;
					window.close();
				} else if (opener.document.getElementById('cmscms_name')) {
					opener.pageValue(id);
					window.close();
				}
				void(0);
			}
		");
		$venster->end_javascript();

		$view = new Layout_view();
		$view->addData($data["pages"]);

		$view->addMapping( gettext("page id"), "%id" );
		$view->addMapping( gettext("page title"), "%pageTitle" );
		$view->addMapping( gettext("url"), "%pageAlias_h" );
		$view->addMapping( gettext("publication date"), "%datePublication_h" );
		$view->addMapping("", "%%complex_link");

		$view->defineComplexMapping("complex_link", array(
			array(
				"type"  => "action",
				"src"   => "view",
				"alt"   => gettext("view on website"),
				"link"  => array("javascript: popup('http://".$_SERVER["HTTP_HOST"]."/page/", "%id",".htm');")
			),
			array(
				"type"  => "action",
				"src"   => "file_attach",
				"alt"   => gettext("use as cms file"),
				"link"  => array("javascript: cmsPreview('", "%id", "', '".$GLOBALS["covide"]->webroot."');")
			)
		));

		$venster->addCode($view->generate_output());

		$paging = new Layout_paging();
		$paging->setOptions($_REQUEST["start"], $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$venster->endVensterData();

			$output->addTag("form", array(
				"action" => "index.php",
				"method" => "get",
				"id"     => "velden"
			));

		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "cms_pagelist");
		$output->addHiddenField("start", $_REQUEST["start"]);

		$output->addCode($venster->generate_output());

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}

}
?>
