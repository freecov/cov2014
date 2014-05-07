<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Pagina bewerken")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getPageById($_REQUEST["id"], $_REQUEST["parentpage"]);

	if ($_REQUEST["id"]) {
		$this->addMenuItems(&$venster);
	} else {
		$venster->addMenuItem(gettext("sluiten"), "javascript: window.close();");
		$venster->addMenuItem(gettext("opslaan"), "");
	}
	$venster->generateMenuItems();

	$venster->addVensterData();

	$tbl = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1,
		"width" => "100%"
	));
	if ($cms["autosave_info"]) {
		$tbl->addTableRow(array("id" => "autosave_info"));
			$tbl->addTableData(array("colspan"=>4), "data");
			$tbl->addTag("div", array("style" => "border: 1px dashed red; padding: 2px;"));
				$tbl->addTag("b");
				$tbl->addCode(gettext("Deze pagina is de vorige keer niet opgeslagen, terwijl er wel wijzigingen aan zijn doorgevoerd."));
				$tbl->endTag("b");
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("Wuilt u dit document herstellen?")." ");
				$tbl->insertAction("ok", gettext("ja"), "javascript: loadRestorePoint();");
				$tbl->insertAction("cancel", gettext("nee"), "javascript: truncateRestorePoint();");
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("Pagina data van het herstelpunt"));

				$user_data = new User_data();
				$tmp = explode("|", $cms["autosave_info"]);
				$tbl->addCode("(".gettext("gebruiker").": ".$user_data->getUsernameById($tmp[0]));
				$tbl->addCode(",".gettext("tijdstip").": ".date("d-m-Y H:i:s", $tmp[1])."):");
				$tbl->addTag("iframe", array(
					"name" => "restorepoint",
					"src" => "?mod=cms&action=viewRestorePoint&id=".$cms["id"],
					"width" => "760px",
					"height" => "160px;",
					"style" => "border: 1px solid black;"
				));
				$tbl->endTag("iframe");

			$tbl->endTag("div");
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();

		$tbl->addTableData(array(
			"colspan" => 4
		), "header");
			if ($_REQUEST["id"]) {
				$tbl->addCode(gettext("pagina id")." [".$cms["id"]."], ".
					gettext("laatst gewijzigd op").": ".date("d-m-Y H:i"));
			} else {
				$tbl->addCode(gettext("nieuwe pagina aanmaken"));
				$tbl->start_javascript();
					$tbl->addCode("function sync_editor_contents() { void(0); }");
				$tbl->end_javascript();
			}
		$tbl->addTableData();
			$tbl->insertAction("close", gettext("sluiten"), "javascript: closePage();");
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("pagina titel"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cms[pageTitle]", $cms["pageTitle"], array("style"=>"width: 250px;"));
		$tbl->endTableData();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("datum publicatie"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			for ($i=1; $i<=31; $i++) {
				$days[$i] = $i;
			}
			for ($i=1; $i<=12; $i++) {
				$months[$i] = $i;
			}
			for ($i=2003; $i<=date("Y")+5; $i++) {
				$years[$i] = $i;
			}
			for ($i=0; $i<=23; $i++) {
				$hour[$i] = $i;
			}
			for ($i=0; $i<60; $i+=15) {
				$min[$i] = sprintf("%02s", $i);
			}
			$tbl->addSelectField("cms[timestamp_day]",   $days,   date("d", $cms["timestamp"]));
			$tbl->addSelectField("cms[timestamp_month]", $months, date("m", $cms["timestamp"]));
			$tbl->addSelectField("cms[timestamp_year]",  $years,  date("Y", $cms["timestamp"]));
			$calendar = new Calendar_output();
			$tbl->addCode( $calendar->show_calendar("document.getElementById('cmstimestamp_day')", "document.getElementById('cmstimestamp_month')", "document.getElementById('cmstimestamp_year')" ));
			$tbl->addSpace();
			$tbl->addSelectField("cms[timestamp_hour]",  $hour,  date("H", $cms["timestamp"]));
			$tbl->addCode(":");
			$tbl->addSelectField("cms[timestamp_min]",  $min,  date("i", $cms["timestamp"]));

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("pagina alias"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cms[pageAlias]", $cms["pageAlias"], array("style"=>"width: 250px;"));
			$tbl->insertTag("span", "", array(
				"id" => "alias_layer"
			));
		$tbl->endTableData();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("pagina label"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cms[pageLabel]", $cms["pageLabel"], array("style"=>"width: 200px;"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
	$venster->addCode($tbl->generate_output());

	if ($_REQUEST["id"]) {
		$venster->addTextArea("contents", $cms["pageData"], array(
			"style" => "width: 700px; height: 400px;"
		));
		if (!$text_only) {
			$editor = new Layout_editor();
			$venster->addCode( $editor->generate_editor("", $cms["pageData"]) );
		} else {
			$venster->start_javascript();
				$venster->addCode("function sync_editor_contents() { return true; }");
			$venster->end_javascript();
		}
		$venster->addTag("br");
		$venster->addTag("span", array(
			"id" => "save_page_layer"
		));
			$venster->insertAction("save", gettext("opslaan"), "javascript: savePage();");
		$venster->endTag("span");
		$venster->insertAction("close", gettext("sluiten"), "javascript: closePage();");

		$venster->insertAction("last", gettext("forceer herstelpunt"), "javascript: saveRestorePoint();");
		$venster->addSpace(2);
		$venster->addCode( gettext("herstelpunt opslaan").": " );
		$venster->insertTag("span", "", array("id"=>"autosave_progressbar"));

	}	else {
		$tbl = new Layout_table();
		/* isActive */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("pagina is actief"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isActive]", 1, 1);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isPublic */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("pagina is publiek"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isPublic]", 1, 1);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isMenuItem */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("pagina is een menuitem"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isMenuItem]", 1, 0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* templates */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("gebruik template"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = $cms_data->getTemplates();
				$tbl->addSelectField("cms[template]", $sel, "");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
		$venster->addSpace();
		$venster->insertAction("forward", gettext("volgende"), "javascript: savePage();");
	}
	$venster->load_javascript(self::include_dir."script_cms.js");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "post",
		"target" => "dbhandler"
	));
	if ($cms["autosave_info"]) {
		$block = 1;
	} elseif (!$_REQUEST["id"]) {
		$block = 2;
	} else {
		$block = 0;
	}
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("block_autosave", $block);
	$output->addHiddenField("action", "savePage");
	$output->addHiddenField("cms[id]", $cms["id"]);
	$output->addHiddenField("cms[parentPage]", $cms["parentPage"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->addTag("iframe", array(
		"id"          => "dbhandler",
		"name"        => "dbhandler",
		"src"         => "blank.htm",
		"width"       => "0px",
		"frameborder" =>  0,
		"border"      =>  0,
		"height"      => "0px;",
		"visiblity"   => "hidden"
	));
	$output->endTag("iframe");
	$output->load_javascript(self::include_dir."cmsEditor.js");


	$output->layout_page_end();
	$output->exit_buffer();

?>