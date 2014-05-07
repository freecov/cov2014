<?php
	if (!class_exists("Calendar_output")) {
		die("no class definition found");
	}
	/* grab user to lookat */
	if ($_REQUEST["user_id"])
		$user_id = $_REQUEST["user_id"];
	else
		$user_id = $_SESSION["user_id"];
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
	/* regenerate date. Input can be 45-12-2006 */
	$timestamp = mktime(0, 0, 0, $month, $day, $year);
	$month = date("m", $timestamp);
	$day   = date("d", $timestamp);
	$year  = date("Y", $timestamp);
	$nextmonth = date("m", $timestamp+mktime(0,0,0,2,1,1970));
	$nextyear  = date("Y", $timestamp+mktime(0,0,0,2,1,1970));
	$prevmonth = date("m", $timestamp-mktime(0,0,0,2,1,1970));
	$prevyear  = date("Y", $timestamp-mktime(0,0,0,2,1,1970));
	
	$calendar_data = new Calendar_data();

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
	$output->addHiddenField("returnto", "monthview");
	$output->endTag("form");

	$venster = new Layout_venster(array(
		"title" => gettext("monthly calendar"),
		"subtitle" => $month." - ".$year
	));
	$venster->addMenuItem(gettext("daily view"), sprintf("./?mod=calendar&forceday=1&day=%d&month=%d&year=%d",
		$_REQUEST["day"], $_REQUEST["month"], $_REQUEST["year"]));
	$venster->generateMenuItems();
	$venster->addVensterData();

	$table = new Layout_table(array("width" => "100%"));
	$table->addTableRow();
		$table->addTableData();
			$table_cal = new Layout_table(array("cellspacing" => 1, "width" => "100%", "style" => "border: 1px solid #CCC"));
			$table_cal->addTableRow();
				$table_cal->insertTableData(gettext("sun"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("mon"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("tue"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("wed"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("thu"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("fri"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("sat"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
			$table_cal->endTableRow();
			$table_cal->addTableRow();
				/* skip the weekdays before first of the month */
				$daystoskip = date("w", mktime(0, 0, 0, $month, 1, $year));
				$dow = $daystoskip;
				for ($i = 0; $i < $daystoskip; $i++)
					$table_cal->insertTableData("&nbsp;", array("style" => "border: 1px solid #CCC"));
				for ($i=0; $i<date("t",mktime(0,0,0,$month,1,$year)); $i++) {
					if (($i+1) == date("d") && $month == date("m")) {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "header");
					} else {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "data");
					}
						$table_day = new Layout_table(array("width" => "100%"));
						$table_day->addTableRow();
							$table_day->addTableData(array("width" => "100%", "align" => "right"));
								$datemask = mktime(0, 0, 0, $month, ($i+1), $year);
								/* grab calendar items for this day */
								$items_arr = $calendar_data->_get_appointments($user_id, $month, ($i+1), $year, 1);
								$days_header[$days[$i]]["longdate"] = $days[$i]." (".($i+1)."/".$month.")";
								$days_header[$days[$i]]["linkdate"] = "&day=".($i+1)."&month=$month&year=$year";

								$table_day->insertAction("new", gettext("new"), "javascript: calendaritem_edit(0,".$user_id.",".$datemask.");");
								$table_day->addSpace(2);
								$table_day->insertLink($i+1, array("href"=>"index.php?mod=calendar&day=".($i+1)."&month=".$month));
								$table_day->addTag("br");
								$table_day->addTag("br");
							$table_day->endTableData();
						$table_day->endTableRow();
						$table_day->addTableRow();
							$table_day->addTableData();
								if (count($calendar_data->calendar_items)) {
									foreach ($calendar_data->calendar_items as $v) {
										$table_day->insertAction("info", gettext("show"), "javascript: toonInfo(".$v["id"].");");
										if (!$v["is_registered"]) {
											$table_day->insertAction("edit", gettext("change"), "javascript: calendaritem_edit(".$v["id"].", ".$user_id.", ".$datemask.");");
										}
										if ($v["is_event"] != 1) {
											$table_day->addCode($v["shuman"]."&nbsp;");
										}
										$table_day->addCode(substr(strip_tags($v["subject"]), 0, 25));
										$table_day->addTag("br");
									}
								}
								unset($calendar_data->calendar_items);
								unset($items_arr);
							$table_day->endTableData();
						$table_day->endTable();
						$table_cal->addCode($table_day->generate_output());
						unset($table_day);
					$table_cal->endTableData();
					$dow++;
					if ($dow==7) {
						/* End of the week. Start new row in calander */
						if (!(($i+1)==date("t",mktime(0,0,0,date("m"),1,date("Y"))))) {
							$table_cal->endTableRow();
							$table_cal->addTableRow();
						}
						$dow=0;
					}
				}
				// Complete fields of calander when month does not end on saturday
				if ($dow!=0) {
					for ($i=0; $i!=7-$dow; $i++) {
						$table_cal->insertTableData("&nbsp;", array("style" => "border: 1px solid #CCC"), "header");
					}
				}
			$table_cal->endTableRow();
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
			$table->addHiddenField("action", "monthview");
			$table->insertAction("back", gettext("back"), "javascript: goMonth($prevmonth, $prevyear);");
			$table->addSelectField("month", $months, date("m",mktime(0,0,0,$month,1,$year)), 0, array("onchange"=>"document.getElementById('search').submit()"));
			$table->addSelectField("year", $years, date("Y",mktime(0,0,0,$month,1,$year)), 0, array("onchange"=>"document.getElementById('search').submit()"));
			$table->insertAction("forward", gettext("next"), "javascript: goMonth($nextmonth, $nextyear);");
			$table->addHiddenField("user_id", $user_id);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("user_id", $user_id, 0, 0, 0, 1) );
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
