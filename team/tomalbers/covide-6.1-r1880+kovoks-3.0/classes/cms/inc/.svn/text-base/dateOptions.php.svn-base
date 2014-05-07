<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("datum opties")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getPageById($id);

	$this->addMenuItems($venster);
	$venster->generateMenuItems();

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("calendar_reg_hour", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("publicatie datum range"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("start datum"));
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
				$tbl->addCheckBox("cms[s_timestamp_enable]", 1, ($cms["date_start"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addTag("span", array(
					"id" => "s_timestamp_layer",
					"style" => "visibility: hidden;"
				));
					$tbl->addSelectField("cms[s_timestamp_day]",   $days,   date("d", $cms["date_start"]));
					$tbl->addSelectField("cms[s_timestamp_month]", $months, date("m", $cms["date_start"]));
					$tbl->addSelectField("cms[s_timestamp_year]",  $years,  date("Y", $cms["date_start"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmss_timestamp_day')", "document.getElementById('cmss_timestamp_month')", "document.getElementById('cmss_timestamp_year')" ));
					$tbl->addSpace();
					$tbl->addSelectField("cms[s_timestamp_hour]",  $hour,  date("H", $cms["date_start"]));
					$tbl->addCode(":");
					$tbl->addSelectField("cms[s_timestamp_min]",  $min,  date("i", $cms["date_start"]));

				$tbl->endTag("span");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("eind datum"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");

				$tbl->addCheckBox("cms[e_timestamp_enable]", 1, ($cms["date_end"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addTag("span", array(
					"id" => "e_timestamp_layer",
					"style" => "visibility: hidden;"
				));
					$tbl->addSelectField("cms[e_timestamp_day]",   $days,   date("d", $cms["date_end"]));
					$tbl->addSelectField("cms[e_timestamp_month]", $months, date("m", $cms["date_end"]));
					$tbl->addSelectField("cms[e_timestamp_year]",  $years,  date("Y", $cms["date_end"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmse_timestamp_day')", "document.getElementById('cmse_timestamp_month')", "document.getElementById('cmse_timestamp_year')" ));
					$tbl->addSpace();
					$tbl->addSelectField("cms[e_timestamp_hour]",  $hour,  date("H", $cms["date_end"]));
					$tbl->addCode(":");
					$tbl->addSelectField("cms[e_timestamp_min]",  $min,  date("i", $cms["date_end"]));


			$tbl->endTableData();
		$tbl->endTableRow();

		/* calendar items */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("calendar_today", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("evenementen en kalenderdata"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "data");

				/* get calendar items */
				$items = $cms_data->getCalendarItems($id);

				$view = new Layout_view();
				$view->addData($items);

				$view->addMapping( gettext("vanaf"), "%username" );
				$view->addMapping( gettext("tot"), "%is_enabled_h" );
				$view->addMapping( gettext("repeterend"), "%is_enabled_h" );
				$view->addMapping( gettext("omschrijving"), "%is_enabled_h" );
				$view->addMapping( " ", "%%complex_actions" );

				$view->defineComplexMapping("complex_actions", array(
					array(
						"type" => "action",
						"src"  => "edit",
						"alt"  => gettext("bewerken"),
						"link" => array("?mod=cms&action=editAccount&id=", "%id", "&user_id=", $user_id)
					),
					array(
						"type" => "action",
						"src"  => "delete",
						"alt"  => gettext("verwijderen"),
						"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAccount&id=", "%id", "&user_id=", $user_id, "';")
					)
				));
				$tbl->addCode($view->generate_output());

			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->endTable();
		$venster->addCode($tbl->generate_output());

		$venster->insertAction("new", gettext("nieuw item"), "javascript: popup('?mod=cms&action=dateOptionsItemEdit', 'settings', 640, 480, 1);");
		$venster->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveDateOptions");
	$output->addHiddenField("id", $id);

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."dateOptions.js");
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>