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
							"rabolite" => "RaboLite integration",
							"ogone"    => "Ogone integration"
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
?>