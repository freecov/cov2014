<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Pagina instellingen")
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
				$tbl->addCode(gettext("globale instellingen"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("sluiten"), "javascript: saveSettings();");
				$tbl->insertAction("close", gettext("sluiten"), "javascript: window.close();");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* trefwoorden */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("trefwoorden"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[keywords]", $cms["keywords"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("trefwoorden voor deze pagina gescheiden door een komma of spatie"));
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
				$tbl->addCode(gettext("type de redirection-url van deze pagina of kies een pagina nummer."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* redirect url in a popup? */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("redirect in een nieuwe venster"));
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
						$t2->addCode(gettext("breedte"));
					$t2->endTableData();
					$t2->addTableData();
						$t2->addTextField("cms[popup_width]", $cms["popup_width"]);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->addTableRow();
					$t2->addTableData();
						$t2->addCode(gettext("hoogte"));
					$t2->endTableData();
					$t2->addTableData();
						$t2->addTextField("cms[popup_height]", $cms["popup_height"]);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->addTableRow();
					$t2->addTableData(array("colspan" => 2));
						$t2->addCode(gettext("verberg navigatie"));
						$t2->insertCheckBox("cms[popup_hidenav]", 1, ($cms["popup_hidenav"]) ? 1:0);
					$t2->endTableData();
				$t2->endTableRow();
				$t2->endTable();

				$tbl->addCode($t2->generate_output());
		$tbl->endTableRow();

		/* isActive */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("pagina is actief"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isActive]", 1, ($cms["isActive"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isPublic */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("pagina is publiek"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isPublic]", 1, ($cms["isPublic"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* search engine settings */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
				$tbl->insertAction("search", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("zoekmachine informatie"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has specific options */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("paginaspecifieke informatie"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[search_override]", 1, ($cms["search_override"]) ? 1:0);
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("standaard erft de pagina zijn informatie over van de bovenliggende pagina. Indien er geen bovenliggende pagina is die aangepaste informatie bevat wordt er naar de website opties gekeken."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* meta keywords */
		$tbl->addTableRow(array(
			"id"    => "search1",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("zoekmachine keywords"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[search_fields]", $cms["search_fields"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData(array("rowspan"=>2), "data");
				$tbl->addCode(gettext("dit en zoekmachine omschrijving zijn pagina specifieke zoekmachine informatie ter vervanging van de globale informatie. de trefwoorden moeten worden gescheiding door een komma."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* meta description */
		$tbl->addTableRow(array(
			"id"    => "search2",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("zoekmachine omschrijving"));
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
				$tbl->addCode(gettext("aangepaste pagina titel"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[search_title]", $cms["search_title"], array("style"=>"width:".$w.";"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(gettext("titel ter vervanging van de standaard website titel"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* languages */
		$tbl->addTableRow(array(
			"id"    => "search4",
			"style" => "display: none;"
		));
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("zoekmachine taalinformatie"));
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
				$tbl->addCode(gettext("Google sitemap informatie"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* change frequency */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("wijzigingen frequentie"));
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
				$tbl->addCode(gettext("prioriteit"));
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
				$tbl->addCode(gettext("beheerders opties"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is a menuitem */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("deze pagina is een menuitem"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isMenuItem]", 1, ($cms["isMenuItem"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is a template */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("deze pagina is beschikbaar als template"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isTemplate]", 1, ($cms["isTemplate"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* is sticky */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("deze pagina is gelocked"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isSticky]", 1, ($cms["isSticky"]) ? 1:0);
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("deze optie zorgt ervoor dat een pagina niet verwijderd of gewijzigd kan worden"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* module opties */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>3), "header");
				$tbl->insertAction("open", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("module specifieke opties"));
			$tbl->endTableData();
			$tbl->addTableData("", "data nowrap");
				$tbl->insertAction("save", gettext("sluiten"), "javascript: saveSettings();");
				$tbl->insertAction("close", gettext("sluiten"), "javascript: window.close();");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* no module */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("geen"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "none", (!$cms["isList"]  && !$cms["isForm"] && !$cms["isGallery"]) ? "none":"");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a list */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("lijst"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "list", ($cms["isList"]) ? "list":"");
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("na het opslaan heeft u hieronder een nieuwe optie tot uw beschikking om dit in te stellen. deze optie kan niet samen worden gebruikt met sommige andere opties hieronder."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a formfield */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("formulier"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "form", ($cms["isForm"]) ? "form":"");
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("dit formulier wordt onderaan de paginadata getoond. u heeft na het opslaan van deze instelling een nieuwe optie tot uw beschikking waar u dit formulier kunt samenstellen. deze optie kan niet samen worden gebruikt met sommige andere opties."));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* has a gallery */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("fotoboek"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addRadioField("cms[module]", "", "gallery", ($cms["isGallery"]) ? "gallery":"");
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode(gettext("op deze pagina wordt een fotoboek getoond onder de pagina data. u kunt zelf uw soort fotoboek samenstellen. deze optie kan niet samen worden gebruikt met sommige andere opties."));
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