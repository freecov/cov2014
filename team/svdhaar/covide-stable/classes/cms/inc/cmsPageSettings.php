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

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => sprintf('%s [%d]', gettext("Page settings"), $_REQUEST['id'])
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getPageById($_REQUEST["id"]);

	$cms_license = $cms_data->getCmsSettings();

	$user_data = new User_data();
	$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

	$p = $cms_data->getUserPermissions($_REQUEST["id"], $_SESSION["user_id"]);

	$this->addMenuItems(&$venster);
	$venster->generateMenuItems();
	$venster->addVensterData();

	switch ($_REQUEST["reason"]) {
		case "redirect":
			$venster->addCode(gettext("You were redirected to page options. The page contents will never be visible because of the redirect type of this page."));
			$venster->addTag("br");
			$venster->addTag("br");
			break;
		case "sticky":
			$venster->addCode(gettext("You were redirected to page options. Tthis page is blocked (sticky flag) by a manager or admin"));
			$venster->addTag("br");
			$venster->addTag("br");
			break;
	}

	$w = "450px";

		$tbl = new Layout_table(array(
			"width"       => "100%",
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		/* global settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>4), "header");
				$tbl->insertAction("view_all", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("Global settings"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("close"), "javascript: saveSettings();");
				#$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* trefwoorden */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("keywords"));
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>3), "data");
				$tbl->addTextField("cms[keywords]", $cms["keywords"], array("style"=>"width:".$w.";"));
				$tbl->addTag("br");
				$tbl->addSpace();
				$tbl->addCode(gettext("keywords for this page, seperated by a space or comma"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* redirect url */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("redirect url"));
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>3), "data");
				$tbl->addTextField("cms[pageRedirect]", $cms["pageRedirect"], array("style"=>"width:".$w.";"));
				$tbl->addTag("br");
				$tbl->addSpace();
				$tbl->addCode(gettext("provide redirect url of this page, or pick a page number").": ");
				$tbl->insertAction("choose", gettext("pick a page"), "javascript: pickPage();");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* redirect url in a popup? */
		$tbl->addTableRow();
			$tbl->addTableData(array("valign" => "top"), "header");
				$tbl->addCode(gettext("redirect in new window"));
			$tbl->endTableData();
			$tbl->addTableData(array("valign" => "top"), "data");
				$tbl->insertAction("toggle", "", "");
			$tbl->endTableData();
			$tbl->addTableData(array("valign" => "top", "colspan" => 2), "data nowrap");
				$tbl->insertCheckBox("cms[pageRedirectPopup]", 1, ($cms["pageRedirectPopup"]) ? 1:0);

				/* popup options */
				$t2 = new Layout_table(array(
					"id"    => "popup_options",
					"style" => "display: none;"
				));
				$t2->addTableRow();
					$t2->addTableData();
						$t2->addCode(gettext("width"));
					$t2->endTableData();
					$t2->addTableData();
						$t2->addTextField("cms[popup_width]", $cms["popup_width"]);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->addTableRow();
					$t2->addTableData();
						$t2->addCode(gettext("height"));
					$t2->endTableData();
					$t2->addTableData();
						$t2->addTextField("cms[popup_height]", $cms["popup_height"]);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->addTableRow();
					$t2->addTableData(array("colspan" => 2));
						$t2->addCode(gettext("hide navigation"));
						$t2->insertCheckBox("cms[popup_hidenav]", 1, ($cms["popup_hidenav"]) ? 1:0);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->endTable();

				$tbl->addCode($t2->generate_output());
		$tbl->endTableRow();

		/* useInternal */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("document system"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertAction("covide", "", "");
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>2), "data");
				$tbl->insertCheckBox("cms[useInternal]", 1, ($cms["useInternal"]) ? 1:0);
				$tbl->addCode(gettext("This page is startpoint on the Covide desktop"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isActive */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is active"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertAction("enabled", "", "");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isActive]", 1, ($cms["isActive"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode("this page is accessible and enabled");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isPublic */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is listed in listings"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertAction("go_support", "", "");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isPublic]", 1, ($cms["isPublic"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode("this page is visible in listings like lists, searches, address- and metadata searches");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is a menuitem */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page is a menu item"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertAction("go_desktop", "", "");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isMenuItem]", 1, ($cms["isMenuItem"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode("this page is visible in the menu (if available)");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* shopping module */
		if ($cms_license["cms_shop"]) {
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("webshop"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->insertAction("file_add", "", "");
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->insertCheckBox("cms[isShop]", 1, ($cms["isShop"]) ? 1:0);
					$tbl->addSpace();
					$tbl->addCode("this page is available as shop article");

					/* popup options */
					$t2 = new Layout_table(array(
						"id"    => "shop_options",
						"style" => "display: none;"
					));
					$t2->addTableRow();
						$t2->addTableData();
							$t2->addCode(gettext("Article price"));
							$t2->addSpace();
						$t2->endTableData();
						$t2->addTableData();
							$t2->addTextField("cms[shopPrice]", $cms["shopPrice"]);
						$t2->endTableData();
					$t2->endTableRow();
					$t2->endTable();

					$tbl->addCode($t2->generate_output());

				$tbl->endTableData();
			$tbl->endTableRow();
		}

		/* is protected */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page or subpages are protected"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertAction("users", "", "");
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>2), "data");
				$sel = array(
					0 => "+ ".gettext("this page is public accessible"),
					1 => "- ".gettext("this page is locked by login/password"),
					2 => "!&nbsp; ".gettext("this page and subpages are locked")
				);
				$tbl->addSelectField("cms[isProtected]", $sel, $cms["isProtected"]);
				$tbl->addSpace();
				$tbl->addCode("visitor permissions can be set in authorisations");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is SSL */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page or subpages are encrypted"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("logout", gettext("ssl"), "");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = array(
					0 => "- ".gettext("this page is not encrypted"),
					1 => "+ ".gettext("this page is ecrypted over SSL"),
					2 => "!&nbsp; ".gettext("this page and subpages are SSL ecrypted")
				);
				if (!$GLOBALS["covide"]->sslmode) {
					$sel = array(
						0 => "- ".gettext("this page is not encrypted"),
					);
				}

				$tbl->addSelectField("cms[useSSL]", $sel, $cms["useSSL"]);
				if (!$GLOBALS["covide"]->sslmode) {
					$tbl->insertTag("b", gettext(" SSL is not activated or available for this office"));
				}
			$tbl->endTableData();
		$tbl->endTableRow();

		/* address */
		if ($cms_license["cms_address"]) {
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("contact selection"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addHiddenField("cms[address_id]", "");
					$tbl->insertTag("span", "", array(
						"id" => "searchrel"
					));
					$tbl->addSpace(1);
					$tbl->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
					$tbl->start_javascript();
						$address_data = new Address_data();
						$a_ids = explode(",", $cms["address_ids"]);
						foreach ($a_ids as $a_id) {
							$tbl->addCode(sprintf("selectRel(%d, '%s');\n", $a_id, $address_data->getAddressNameById($a_id)));
						}
					$tbl->end_javascript();

				$tbl->endTableData();
			$tbl->endTableRow();
			/* address level */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("contact visibility"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->insertAction("state_special", "", "");
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$sel = array(
						0 => "+ ".gettext("this page is visible in address search"),
						1 => "- ".gettext("this page is only related to an address")
					);
					$tbl->addSelectField("cms[address_level]", $sel, $cms["address_level"]);
				$tbl->endTableData();
			$tbl->endTableRow();
		}

		/* search engine settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>4), "header");
				$tbl->insertAction("search", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("searchengine information"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has specific options */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page specific information"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[search_override]", 1, ($cms["search_override"]) ? 1:0);
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("By default, pages inherit information from their parent page.If no parent page with specific information is present the website options apply."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* meta keywords */
		$tbl->addTableRow(array(
			"id"    => "search1",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("searchengine keywords"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSpace();
			$tbl->endTableData();

			$tbl->addTableData(array("colspan" => 2), "data");
				$tbl->addTextField("cms[search_fields]", $cms["search_fields"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* meta description */
		$tbl->addTableRow(array(
			"id"    => "search2",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("searchengine description"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData(array("colspan" => 2), "data");
				$tbl->addTextField("cms[search_descr]", $cms["search_descr"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* custom title */
		$tbl->addTableRow(array(
			"id"    => "search3",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("specific page title"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData(array("colspan" => 2), "data");
				$tbl->addTextField("cms[search_title]", $cms["search_title"], array("style"=>"width:".$w.";"));
				$tbl->addTag("br");
				$tbl->addSpace();
				$tbl->addCode(gettext("title that will overwrite the default website title"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* custom title */
		$tbl->addTableRow(array(
			"id"    => "search4",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("conversion script"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData(array("colspan" => 2), "data");
				$tbl->addTextArea("cms[conversion_script]", $cms["conversion_script"], 
					array("style"=>"width:".$w."; height: 80px;"));
				$tbl->addTag("br");
				$tbl->addSpace();
				$tbl->addCode(gettext("a specific conversion script like google analytics"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* languages */
		$tbl->addTableRow(array(
			"id"    => "search4",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("searchengine language information"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = $cms_data->lang;
				foreach ($sel as $k=>$v) {
					$tbl->insertCheckBox("cms[search_language][$k]", $k, (in_array($k, $cms["search_language"])) ? 1:0);
					$tbl->addSpace();
					$tbl->addCode($v." (".$k.")");
					$tbl->addTag("br");
				}
			$tbl->endTableData();
		$tbl->endTableRow();

		/* google engine settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>4), "header");
				$tbl->insertAction("view_tree", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("Google Sitemap information"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* change frequency */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("change frequency"));
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>2), "data");
				$sel = array();
				$g_changes = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
				foreach ($g_changes as $g) {
					$sel[$g] = $g;
				}
				if ($user_info["xs_cms_level"] >= 2 || $p["manageRight"]) {
					$tbl->addSelectField("cms[google_changefreq]", $sel, $cms["google_changefreq"]);
				} else {
					$tbl->addHiddenField("cms[google_changefreq]", $cms["google_changefreq"]);
					$tbl->addCode($sel[$cms["google_changefreq"]]);
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		/* priority */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("priority"));
			$tbl->endTableData();
			$tbl->addTableData(array("colspan"=>2), "data");
				$sel = array();
				for ($i=1; $i<=9; $i++) {
					$pri = "0.".$i;
					$sel[$pri] = $pri;
				}
				$sel["1.0"] = "1.0";
				if ($user_info["xs_cms_level"] >= 2 || $p["manageRight"]) {
					$tbl->addSelectField("cms[google_priority]", $sel, $cms["google_priority"]);
				} else {
					$tbl->addHiddenField("cms[google_priority]", $cms["google_priority"]);
					$tbl->addCode($cms["google_priority"]);
				}
			$tbl->endTableData();
		$tbl->endTableRow();

		/* manager or admin settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>4), "header");
				$tbl->insertAction("state_special", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("manager options"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is a template */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page is a template"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				if ($user_info["xs_cms_level"] >= 2) {
					$tbl->insertCheckBox("cms[isTemplate]", 1, ($cms["isTemplate"]) ? 1:0);
				} else {
					$tbl->addHiddenField("cms[isTemplate]", ($cms["isTemplate"]) ? 1:0);
					$tbl->addCode(($cms["isTemplate"]) ? gettext("yes"):gettext("no"));
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is sticky */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page is locked by cms manager or admin"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				if ($user_info["xs_cms_level"] >= 2 || $p["manageRight"]) {
					$tbl->insertCheckBox("cms[isSticky]", 1, ($cms["isSticky"]) ? 1:0);
				} else {
					$tbl->addHiddenField("cms[isSticky]", ($cms["isSticky"]) ? 1:0);
					$tbl->addCode(($cms["isSticky"]) ? gettext("yes"):gettext("no"));
				}

			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("this option prevents the page from being deleted or altered"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* module opties */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>4), "header");
				$tbl->insertAction("open", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("module specific options"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
				#$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* no module */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("none"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "none", (!$cms["isList"]  && !$cms["isForm"] && !$cms["isGallery"]) ? "none":"");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a list */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("list"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "list", ($cms["isList"]) ? "list":"");
				if (!$cms_license["cms_list"])
					$tbl->insertTag("i", gettext("this module is disabled and is not available"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("after saving the page, you will get a new option. This option can conflict with certain other options."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a formfield */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("form"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "form", ($cms["isForm"]) ? "form":"");
				if (!$cms_license["cms_forms"])
					$tbl->insertTag("i", gettext("this module is disabled and is not available"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("this form will be shown at the bottom of the page. "));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a gallery */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("image gallery"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "gallery", ($cms["isGallery"]) ? "gallery":"");
				if (!$cms_license["cms_gallery"])
					$tbl->insertTag("i", gettext("this module is disabled and is not available"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("this page will show a photo gallery below normal page content."));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* can have feedback */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("feedback"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "feedback", ($cms["isFeedback"]) ? "feedback":"");
				if (!$cms_license["cms_feedback"])
					$tbl->insertTag("i", gettext("this module is disabled and is not available"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("with this option, users can give feedback and respond on this page."));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* can have inherit */
		if ($cms_license['cms_page_elements']) {
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("inherit"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "inherit", ($cms["isInherit"]) ? "inherit":"");
				$tbl->addTextField('cms[inheritpage]', ($cms['inheritpage']) ? $cms['inheritpage'] : '', array('style' => 'width: 40px'));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("with this option, you can inherit the module and metadata settings from another page"));
			$tbl->endTableData();
		$tbl->endTableRow();
		}


		$tbl->endTable();
		$venster->addCode($tbl->generate_output());

	$venster->endVensterData();

	$output->load_javascript(self::include_dir."cmsPageSettingsRel.js");
	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("id", $_REQUEST["id"]);
	$output->addHiddenField("action", "savePageSettings");

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."cmsPageSettings.js");
	$output->load_javascript(self::include_dir."script_cms.js");
	$output->layout_page_end();
	$output->exit_buffer();
?>
