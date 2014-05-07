<?php
	if (!class_exists("Calendar_output")) {
		die("no class definition found");
	}
	/* we gonna use date dropdowns */
	$days = array();
	for ($a=1; $a<=31; $a++) {
		if ($a<10) {
			$days[$a] = "0".$a;
		} else {
			$days[$a] = $a;
		}
	}
	$months = array();
	for ($a=1; $a<=12; $a++) {
		if ($a<10) {
			$months[$a] = "0".$a;
		} else {
			$months[$a] = $a;
		}
	}
	$years = array();
	for ($a=date("Y")-3; $a<=date("Y")+1; $a++) {
		$years[$a] = $a;
	}
	/* we are going to print usernames */
	$user_data = new User_data();
	$calendar_data = new Calendar_data();
	if ($_REQUEST["users"]) {
		$rcpt = $_REQUEST["users"];
	} else {
		$rcpt = $_SESSION["user_id"];
	}

	if (!$_REQUEST["start_day"]) {
		$km["start_day"]   = 1;
		$km["start_month"] = date("m")-1;
		$km["start_year"]  = date("Y");
		$km["end_day"]     = 0;
		$km["end_month"]   = date("m");
		$km["end_year"]    = date("Y");
	} else {
		$km["start_day"]   = $_REQUEST["start_day"];
		$km["start_month"] = $_REQUEST["start_month"];
		$km["start_year"]  = $_REQUEST["start_year"];
		$km["end_day"]     = $_REQUEST["end_day"];
		$km["end_month"]   = $_REQUEST["end_month"];
		$km["end_year"]    = $_REQUEST["end_year"];

	}
	$kmoptions["start"] = mktime(0, 0, 0, $km["start_month"], $km["start_day"], $km["start_year"]);
	$kmoptions["end"]   = mktime(0, 0, 0, $km["end_month"], $km["end_day"], $km["end_year"]);


	$kmoptions["users"] = explode(",", $rcpt);
	$kminfo = $calendar_data->getKmItems($kmoptions);

	/* start output */
	$output = new Layout_output();
	$output->layout_page();

		foreach($kmoptions["users"] as $k) {
			/* nice window widget */
			$venster = new Layout_venster(array(
				"title" => gettext("kilometers"),
				"subtitle" => $user_data->getUsernameById($k)
			));
			$venster->addVensterData();
				/* view for the prepared data */
				$view = new Layout_view();
				$view->addData($kminfo[$k]["items"]);
				$view->addMapping(gettext("datum"), "%human_date");
				$view->addMapping(gettext("onderwerp"), "%subject");
				$view->addMapping(gettext("locatie"), "%location");
				$view->addMapping(gettext("declarabel"), "%%complex_dec");
				$view->addMapping(gettext("km"), "%kilometers");
				$view->defineComplexMapping("complex_dec", array(
					array(
						"type"  => "action",
						"src"   => "ok",
						"check" => "%deckm"
					)
				));
				$venster->addCode($view->generate_output());
				unset($view);

				/* print table with totals */
				$table = new Layout_table(array(
					"width" => "100%",
					"align" => "right",
					"border" => 0
				));
				$table->addTableRow();
					$table->addTableData(array("width" => "100%", "align" => "right"));
						$table->addCode(gettext("totaal declarabel"));
						$table->addSpace(2);
						$table->addCode($kminfo[$k]["total_dec"]);
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData(array("width" => "100%", "align" => "right"));
						$table->addCode(gettext("totaal niet declarabel"));
						$table->addSpace(2);
						$table->addCode($kminfo[$k]["total_non_dec"]);
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$venster->addCode($table->generate_output());
				unset($table);
			$venster->endVensterData();
			/* end of window, attacht to output buffer */
			$output->addCode($venster->generate_output());
			unset($venster);
		}
		/* search venster */
		$venster = new Layout_venster(array("title" => gettext("zoeken")));
		$venster->addVensterData();
			/* make nice table */
			$table = new Layout_table();
			$table->addTableRow();
				$table->addTableData(array("style" => "vertical-align: top;"));
					/* little table for date alignment */
					$table_date = new Layout_table();
					$table_date->addTableRow();
						$table_date->insertTableData(gettext("van"), "", "");
						$table_date->addTableData();
							$table_date->addSelectField("start_day", $days, $km["start_day"]);
							$table_date->addSelectField("start_month", $months, $km["start_month"]);
							$table_date->addSelectField("start_year", $years, $km["start_year"]);
						$table_date->endTableData();
					$table_date->endTableRow();
					$table_date->addTableRow();
						$table_date->insertTableData(gettext("tot"), "", "");
						$table_date->addTableData();
							$table_date->addSelectField("end_day", $days, $km["end_day"]);
							$table_date->addSelectField("end_month", $months, $km["end_month"]);
							$table_date->addSelectField("end_year", $years, $km["end_year"]);
						$table_date->endTableData();
					$table_date->endTableRow();
					$table_date->endTable();
					$table->addCode($table_date->generate_output());
					unset($table_date);
				$table->endTableData();
				$table->addTabledata();
					$table->addHiddenField("users", $rcpt);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("users", $rcpt, 1, 1, 0, 1) );
					$table->insertAction("forward", gettext("zoeken"), "javascript: km_search();");
				$table->endTableData();
			$table->endTableRow();
			/* end table and attach to window */
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
		$venster->endVensterData();
		$output->addTag("form", array(
			"id"     => "kmsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "calendar");
		$output->addHiddenField("action", "km");
		$output->addCode($venster->generate_output());
		$output->endTag("form");
		unset($venster);
		$output->start_javascript();
			$output->addCode(
				"
				function km_search() {
					document.getElementById('kmsearch').submit();
				}
				"
			);
		$output->end_javascript();
	/* end output, flush to client */
	$output->layout_page_end();
	$output->exit_buffer();
?>
