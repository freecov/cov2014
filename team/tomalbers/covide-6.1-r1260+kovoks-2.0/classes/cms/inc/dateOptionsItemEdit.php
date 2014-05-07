<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("kalender items en evenementen")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getCalendarItems($id);

	$venster->addVensterData();

	$tbl = new Layout_table();
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
				$tbl->addTag("span", array(
					"id" => "s_timestamp_layer"
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

				$tbl->addTag("span", array(
					"id" => "e_timestamp_layer"
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
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("repeterend"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");


			$tbl->endTableData();
		$tbl->endTableRow();

	$tbl->endTable();

	$venster->addCode( $tbl->generate_output() );

	$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addCode($venster->generate_output());

	$output->layout_page_end();
	$output->exit_buffer();
?>