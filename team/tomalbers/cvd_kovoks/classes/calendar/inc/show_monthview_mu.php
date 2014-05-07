<?php
	// Based on show_monthview.php, copyright Covide.
	// Copyright (c) 2006 T.M. Albers - KovoKs <toma@kovoks.nl>

	if (!class_exists("Calendar_output")) {
		die("no class definition found");
	}
	/* grab user to lookat */
	$users = explode(",",$_REQUEST["user"]);

	/* Groups are evil */
	$user_data = new User_data();
	foreach ($users as $k=>$v) {
		if (strpos($v, "G") !== false) {
			unset($users[$k]);
			$groupid = substr($v, 1);
			$groupinfo = $user_data->getGroupInfo($groupid);
			$members = explode(",", $groupinfo["members"]);
			$users = array_merge($users, $members);
		}
	}
	unset($user_data);
	
	/* unset session var that forces daily view */
	unset($_SESSION["calendar_forceday"]);

	/* prepare some vars we need */
	$months = array();
	for ($i=1;$i<=12;$i++) {
		$months[$i] = $i;
	}
	$years = array();
	for ($i=(date("Y")-2); $i<(date("Y")+5);$i++) {
		$years[$i] = $i;
	}
	/* grab some possible imput vars */
	if ((int)$_REQUEST["month"] > 0) { $month = $_REQUEST["month"]; } else { $month = date("m"); }
	if ((int)$_REQUEST["day"] > 0)   { $day   = $_REQUEST["day"];   } else { $day   = date("d"); }
	if ((int)$_REQUEST["year"] > 0)  { $year  = $_REQUEST["year"];  } else { $year  = date("Y"); }

	$calendar_data = new Calendar_data();
	$user_data = new User_data();

	$output = new Layout_output();
	$output->layout_page();
	/* form to allow deletion of items */
	$output->addTag("form", array(
		"id" => "calendarform",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "calendar");
	$output->addHiddenField("action", "");
	$output->addHiddenField("id", "");
	$output->endTag("form");

	$venster = new Layout_venster(array(
		"title" => gettext("monthly overview")
	));
	$venster->addMenuItem(gettext("daily view"), "?mod=calendar&forceday=1&extrauser=".implode(",",$users));
        $venster->addMenuItem(gettext("daily view"),  sprintf("./?mod=calendar&forceday=1&extrauser=".implode(",",$users).
			"&day=%d&month=%d&year=%d",$_REQUEST["day"], $_REQUEST["month"], $_REQUEST["year"]));
	$venster->generateMenuItems();
	$venster->addVensterData();

	$table = new Layout_table(array("width" => "100%"));
	$table->addTableRow();
		$table->addTableData();
			$table_cal = new Layout_table(array("cellspacing" => 1, "width" => "100%", "style" => "border: 1px solid #CCC"));
			$table_cal->addTableRow();
			$table_cal->insertTableData(date("M",mktime(0,0,0,$month,1,$year)), array("style" => "border: 1px solid #CCC"), "header");
			foreach ($users as $user) {
				$table_cal->insertTableData($user_data->getUsernameById($user), array("style" => "border: 1px solid #CCC"), "header");
			}
			$table_cal->endTableRow();

			$table_cal->addTableRow();
				for ($i=1; $i<=date("t",mktime(0,0,0,$month,1,$year)); $i++) {
					if ($i == date("d") && $month == date("m")) {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "header");
					} else {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "data");
					}
					$table_cal->addCode($i . date(" D", mktime(0,0,0,$month,$i,$year)));

					// Loop throught the users and add their appointments.
					foreach ($users as $user) {
						$items_arr = $calendar_data->_get_appointments($user, $month, $i, $year, 1);
						$table_cal->addTableData(array("style" => "border: 1px solid #CCC"), "data");
						if (count($calendar_data->calendar_items)) {
							foreach ($calendar_data->calendar_items as $v) {
								$table_cal->insertAction("info", gettext("show"), "javascript: toonInfo(".$v["id"].");");
								if ($v["is_event"] != 1) {
									$table_cal->addCode($v["shuman"]."&nbsp;");
								}
								$table_cal->addCode(substr(strip_tags($v["subject"]), 0, 25));
								$table_cal->addTag("br");
							}
						} else {
							$table_cal->addSpace(1);
						}
						unset($calendar_data->calendar_items);
						unset($items_arr);
						$table_cal->endTableData();
					}
					$table_cal->endTableRow(); 
				}
			$table_cal->endTable();
			$table->addCode($table_cal->generate_output());
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addTag("form", array(
				"method" => "post",
				"id"     => "search",
				"action" => "index.php"
			));
			$table->addHiddenField("mod", "calendar");
			$table->addHiddenField("action", "monthview_mu");
			$table->addCode(gettext("month")." ");
   		   	// date might be: $month = 15, $year: 2006. We need to convert that to 3/2007 for the pulldowns.
			$table->addSelectField("month", $months, date("m",mktime(0,0,0,$month,1,$year)));
			$table->addSelectField("year", $years, date("Y",mktime(0,0,0,$month,1,$year)));
			$table->addHiddenField("user", implode(",", $users));
			$table->insertAction("forward", gettext("select"), "javascript: document.getElementById('search').submit();");
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("user", implode(",",$users),  1, 0, 0, 0, 1) );
			$table->endTag("form");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->load_javascript(self::include_dir."show_main.js");
	$output->layout_page_end();
	$output->exit_buffer();
?>
