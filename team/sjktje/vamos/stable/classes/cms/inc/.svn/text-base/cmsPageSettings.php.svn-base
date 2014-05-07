<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Page settings")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getPageById($_REQUEST["id"]);

	$this->addMenuItems(&$venster);
	$venster->generateMenuItems();
	$venster->addVensterData();

	$w = "250px";

		$tbl = new Layout_table(array(
			"width"       => "100%",
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		/* global settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
				$tbl->insertAction("view_all", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("Global settings"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("close"), "javascript: saveSettings();");
				$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* trefwoorden */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("keywords"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[keywords]", $cms["keywords"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("keywords for this page, seperated by a space or comma"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* redirect url */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("redirect url"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[pageRedirect]", $cms["pageRedirect"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("provide redirect url of this page, or pick a page number"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* redirect url in a popup? */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("redirect in new window"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[pageRedirectPopup]", 1, ($cms["pageRedirectPopup"]) ? 1:0);
			$tbl->endTableData();
			$tbl->addTableData("", "data");

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

		/* isActive */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is active"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isActive]", 1, ($cms["isActive"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isPublic */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is public"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isPublic]", 1, ($cms["isPublic"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is a menuitem */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page is a menu item"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isMenuItem]", 1, ($cms["isMenuItem"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* search engine settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
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
				$tbl->addTextField("cms[search_fields]", $cms["search_fields"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData(array("rowspan"=>2), "data");
				$tbl->addCode(gettext("this information is specific information to overwrite the website defaults"));
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
				$tbl->addTextField("cms[search_title]", $cms["search_title"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("title that will overwrite default website title"));
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
			$tbl->addTableData(array("colspan"=>3), "header");
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
			$tbl->addTableData("", "data");
				$sel = array();
				$g_changes = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
				foreach ($g_changes as $g) {
					$sel[$g] = $g;
				}
				$tbl->addSelectField("cms[google_changefreq]", $sel, $cms["google_changefreq"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* priority */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("priority"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = array();
				for ($i=1; $i<=9; $i++) {
					$pri = "0.".$i;
					$sel[$pri] = $pri;
				}
				$sel["1.0"] = "1.0";
				$tbl->addSelectField("cms[google_priority]", $sel, $cms["google_priority"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* manager or admin settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
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
				$tbl->insertCheckBox("cms[isTemplate]", 1, ($cms["isTemplate"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is sticky */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("this page is blocked"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isSticky]", 1, ($cms["isSticky"]) ? 1:0);
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("this option prevents the page from being deleted or altered"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* module opties */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
				$tbl->insertAction("open", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("module specific options"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("close"), "javascript: saveSettings();");
				$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
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
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("this page will show a photo gallery below normal page content."));
			$tbl->endTableData();
		$tbl->endTableRow();


		$tbl->endTable();
		$venster->addCode($tbl->generate_output());

	$venster->endVensterData();

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