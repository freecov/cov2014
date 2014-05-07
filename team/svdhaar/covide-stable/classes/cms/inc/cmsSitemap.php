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

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

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

	/* define cms actions */
	require("classes/html/inc/insertactions.php");
	require("conf/offices.php");

	$prefix      = "cms_icon_";
	$cms_actions = array();

	foreach ($avail_actions as $k=>$v) {
		if (substr($k, 0, strlen($prefix)) == $prefix)
			$cms_actions[$k] = $output->external_file_cache_handler(sprintf("themes/default/icons/%s.png", $v["src"]));

		if ($html["no_static_gzip_compression"])
			$cms_actions[$k] = preg_replace("/\.png$/s", "", $cms_actions[$k]);
	}
	$output->start_javascript();
	$output->addCode("var cms_actions_icons = new Array();");
	foreach ($cms_actions as $k=>$v) {
		$output->addCode(sprintf("\n\tcms_actions_icons['%s'] = '%s'", $k, $v));
	}
	$output->end_javascript();

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

	if (($user_info["xs_cms_level"] == 3 && !$GLOBALS["covide"]->license["cms_lock_settings"])
		|| $user_info["username"] == "administrator") {
		$menuitems[gettext("license settings")] = "popup('?mod=cms&action=editCmsSettings', 'settings', 640, 480, 1);";
		$menuitems[gettext("site templates")] = "popup('?mod=cms&action=siteTemplates', randWin(), 970, 680, 1);";
	} elseif ($user_info["xs_cms_level"] == 3) {
		$menuitems[gettext("site templates")] = "popup('?mod=cms&action=siteTemplates', randWin(), 970, 680, 1);";
	}
	if ($user_info["xs_cms_level"] >= 2) {
		$menuitems[gettext("hit counters")] = "popup('?mod=cms&action=siteCounters', 'hitcounters', 500, 400, 1);";
		$menuitems[gettext("polls")] = "popup('?mod=cms&action=polllist', 'polls', 600, 400, 1);";
	}

	if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_permissions"]) {
		$menuitems[gettext("manage accounts")] = "popup('?mod=cms&action=editAccountsList', 'permissions', 640, 480, 1);";
		$menuitems[gettext("login log")] = "popup('?mod=cms&action=loginlog', 'loginlog', 640, 480, 1);";
	}

	if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_meta"])
		$menuitems[gettext("metadata definitions")] = "popup('?mod=cms&action=metadataDefinitions', 'metadata', 640, 480, 1);";

	$menuitems[gettext("file management")] = "popup('index.php?mod=cms&action=filesys');";
	if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_linkchecker"])
		$menuitems[gettext("linkchecker")] = "popup('?mod=cms&action=linkchecker', 'linkchecker', 840, 480, 1);";

	if ($user_info["xs_cms_level"] >= 2) {
		$menuitems[gettext("site information and settings")] = "popup('?mod=cms&action=editSiteInfo', 'siteinfo', 640, 480, 1);";
		$menuitems[gettext("abbreviation and translations")] = "popup('?mod=cms&action=editAbbreviations', 'abbr', 640, 480, 1);";
	}

	if ($user_info["xs_cms_level"] >= 2 && $cms_license["cms_banners"])
		$menuitems[gettext("manage banners")] = "popup('?mod=cms&action=cmsgallery&id=-1', 'banners', 900, 550, 1);";

	#if ($cms_license["cms_changelog"] || $cms_license["cms_versioncontrol"])
	#	$menuitems[gettext("changes overview")] = "";

	$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/CMS", array("target" => "_blank"), 0);

	/* add piwik analytics to the menu if found */
	if ($cms_data->hasPiwik()) {
		if (isset($cms) && array_key_exists('piwik_host', $cms)) {
			$piwik = $cms['piwik_host'];
		} else {
			$piwik = $_SERVER['HTTP_HOST'];
		}
		$venster->addMenuItem(gettext('piwik analytics'), sprintf('http://%s/piwik/', $piwik), array('target' => '_blank'), 0);
	}

	natcasesort($menuitems);
	$venster->addMenuItem(sprintf('<b>%s</b>', gettext("CMS options")), "");
	foreach ($menuitems as $k=>$v) {
		// lucky me, all $v's are javascript actions :)
		$venster->addMenuItem($k, sprintf('javascript: %s', $v));
	}
	$venster->generateMenuItems();



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
				$tbl->addSpace(2);
				$tbl->insertAction("search", gettext("search for pages"), "");
				$tbl->addSpace();
				$tbl->addCode(gettext("search").": ");
				$tbl->addTextField("cms[search]", $cms["search"], "", "", 1);
				//$tbl->insertAction("edit", gettext("open page"), "javascript: cmsSearchPage();");
				$tbl->insertAction("forward", gettext("search"), "javascript: cmsSearch();");
				if ($_REQUEST["cms"]["search"])
					$tbl->insertAction("toggle", gettext("reset search results"), "javascript: document.getElementById('cmssearch').value = ''; cmsReload();");
			$tbl->endTableData();
			#$tbl->addTableData(array("align" => "right"));
			#	$tbl->addCode(gettext("CMS options").": ");
			#	$tbl->addSelectField("menuitems", $menuitems, "void(0);");
			#$tbl->endTableData();
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
						
						$tbl->insertTag('a', gettext('properties'), array('href' => "javascript: popup('?mod=cms&action=editSiteInfo&siteroot=".$cms_data->opts["siteroot"]."');"));
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
							"togglebuffer"      => gettext("toggle selection state inside buffer"),
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
?>
