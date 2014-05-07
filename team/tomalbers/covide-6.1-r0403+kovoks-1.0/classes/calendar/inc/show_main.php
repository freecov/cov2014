<?php
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
$month = $_REQUEST["month"];
$day   = $_REQUEST["day"];
$year  = $_REQUEST["year"];
if ($_REQUEST["timestamp"]) {
	$month = date("m", $_REQUEST["timestamp"]);
	$day   = date("d", $_REQUEST["timestamp"]);
	$year  = date("Y", $_REQUEST["timestamp"]);
}
if (!$month) { $month = date("m"); }
if (!$day)   { $day   = date("d"); }
if (!$year)  { $year  = date("Y"); }
$datestamp = mktime(0, 0, 0, $month, $day, $year);
/* make array with possible vars, for form generation */
$formitems = array(
	"mod"    => "calendar",
	"month"  => $month,
	"day"    => $day,
	"year"   => $year,
	"action" => "",
	"id"     => ""
);

/* check if we are going to fetch multiple users */

if ($_REQUEST["extrauser"] && $_REQUEST["extrauser"]!=$_SESSION["user_id"]) {
	$extrauser_tmp = $_SESSION["user_id"].",".$_REQUEST["extrauser"];
	$userarr = explode(",", $extrauser_tmp);
	$userarr = array_unique($userarr);
	$multiuser = true;

	// Save it for the future.
	$_SESSION["extrauser"] = $userarr;
} else {
	if ($_SESSION["extrauser"] != "" && $_REQUEST["extrauser"]!=$_SESSION["user_id"]) {
		$userarr = $_SESSION["extrauser"];
		$multiuser = true;
	} else {
		$userarr[0] = $_SESSION["user_id"];
		$multiuser = false;
		$_SESSION["extrauser"] = '';
	}
}
/* init user array */
$user_data = new User_data();
$users = $user_data->getUserlist(1);
/* init calendar data object */
$calendar_data = new Calendar_data();

if ($multiuser) {
	$first_item=480;
	$last_item=1080;
	$width = floor(100/count($userarr));
	foreach ($userarr as $uid) {
		$items  = $calendar_data->_get_appointments($uid, $month, $day, $year);
		$searchArray = array_search(1,$items);
		if ($searchArray < $first_item && $searchArray > 0) {
			$first_item = $searchArray;
		}

		$searchArray = array_search(max(array_values($items)),array_reverse($items, true))+15;
		if ($searchArray > $last_item && max(array_values($items)) != 0) {
			$last_item = $searchArray;
		}
		$calendar_items[$uid] = $calendar_data->calendar_items;
		$items_arr[$uid] = $items;
		unset($items, $calendar_data->calendar_items);
	}
} else {
	$items_arr  = $calendar_data->_get_appointments($_SESSION["user_id"], $month, $day, $year);
	$calendar_items = $calendar_data->calendar_items;
	$width = "100";
}

/*
	$items_arr now has the following content:

	if multiple users are selected:

	Array(
		[user_id] => Array(
			[minute0]    => (int)array pos of $calendar_items[user_id],
			[minute1]    => (int)array pos of $calendar_items[user_id],
			...snip...
			[minute1440] => (int)array pos of $calendar_items[user_id]
		)
	)
	The array pos items are only set when there's an appointment going on that minute of the day.

	else if only one user is selected:

	Array(
		[user_id] => Array(
			[item0] => 0,
			[item1] => (int)array pos of $calendar_items[user_id],
			[item2] => 0,
			[item3] => (int)array pos of $calendar_items[user_id],
			[item4] => 0,
			[itemN] => ......
		)
	)

*/
$datemask = mktime(0, 0, 0, $month, $day, $year);

$output = new Layout_output();
$output->layout_page(gettext("agenda"));
$venster_settings = array(
	"title"    => gettext("agenda"),
	"subtitle" => strftime("%A, %e %B %Y", mktime(0,0,0,$month,$day,$year))
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
/* menu items */
$venster->addMenuItem(gettext("nieuwe afspraak"), "javascript: calendaritem_edit(0, ".$_SESSION["user_id"].", $datemask);");
$venster->addMenuItem(gettext("todo's printen"), "javascript: todos_print();");
$venster->addMenuItem(gettext("todo overzicht"), "./?mod=todo");
#$venster->addMenuItem(gettext("week overzicht"), "./?mod=calendar&action=weekview");
$venster->addMenuItem(gettext("maand overzicht"), "./?mod=calendar&action=monthview");
$venster->addMenuItem(gettext("agenda delen"), "javascript: popup('index.php?mod=calendar&action=permissionintro', 'perm', 0, 0, 1);");
$venster->addMenuItem(gettext("kilometers"), "./?mod=calendar&action=km");
$venster->generateMenuItems();
$venster->generateCalendar($month, $day, $year, $userarr);

/* page data */
$venster->addVensterData();
	/* users with setting "monthview as default" can overwrite this in monthview. Allow them to switch back */
	if ($_SESSION["calendar_forceday"]) {
		$venster->addCode(gettext("dagoverzicht geforceerd.")." ".gettext("Klik op het kruisje hiernaast om ongedaan te maken."));
		$venster->insertAction("cancel", gettext("ongedaan maken"), "index.php?mod=calendar&action=monthview");
		$venster->addTag("br");
	}
	if ($multiuser)  {
		/* {{{ multiple users view */
		$tableusers = new Layout_table();
		$tableusers->addTableRow();

		foreach ($items_arr as $k=>$v) {
			$permissions = $calendar_data->checkPermission($k, $_SESSION["user_id"]);
			$tableusers->addTableData(array("class"=>"calendar_user", "width" => $width."%"));
				$tableusers->addCode("<b>".$user_data->getUsernameById($k)."</b>");
				if (($permissions != "0" && $permissions == "RW") || $k == $_SESSION["user_id"]) {
					$tableusers->insertAction("new", gettext("nieuwe afspraak"), "javascript: calendaritem_edit(0, $k, $datemask);");
				}
				$tableitems = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
				$_temp_id = 0;
				for ($i=$first_item; $i<=$last_item; $i+=15) {
					$tableitems->addTableRow();
						$tableitems->addTableData("", "data");
							$tableitems->addCode(date("H:i", mktime(0,$i,0,date("m"), date("d"), date("Y"))));
						$tableitems->endTableData();
						$tableitems->addTableData("", "data");
							$tableitems->addSpace(3);
						$tableitems->endTableData();
						if ($v[$i] && $v[$i] != $_temp_id) {
							$tableitems->addTableData(array("rowspan" => $calendar_items[$k][$v[$i]]["rowspan"], "style" => "font-weight: normal;"), "header");
								if ($calendar_items[$k][$v[$i]]["permissions"]) {
									$tableitems->insertAction("edit", gettext("wijzigen"), "javascript: calendaritem_edit(".$calendar_items[$k][$v[$i]]["id"].", ".$k.", $datemask);");
								}
								$tableitems->insertAction("info", gettext("tonen"), "javascript: toonInfo(".$calendar_items[$k][$v[$i]]["id"].");");
								if ($calendar_items[$k][$v[$i]]["important"]) {
									$tableitems->insertAction("important", gettext("belangrijke afpraak"), "", "");
								}
								if ($calendar_items[$k][$v[$i]]["location"]) {
									$tableitems->addTag("br");
									$tableitems->addCode($calendar_items[$k][$v[$i]]["location"]);
									if ($calendar_items[$k][$v[$i]]["km"]) {
										$tableitems->addCode(", ");
										$tableitems->addCode($calendar_items[$k][$v[$i]]["km"]);
										$tableitems->addCode("km");
									}
									$tableitems->addTag("br");
								}
								$tableitems->addCode($calendar_items[$k][$v[$i]]["subject"]);
								if ($calendar_items[$k][$v[$i]]["rowspan"]>3) {
									$tableitems->addTag("br");
									$tableitems->addCode($calendar_items[$k][$v[$i]]["body"]);
								}
							$tableitems->endTableData();
						} elseif(!$v[$i]) {
							$tableitems->addTableData("", "data");
								$tableitems->addSpace(1);
							$tableitems->endTableData();
						}

						$_temp_id = $v[$i];
					$tableitems->endTableRow();
				}
				$tableitems->endTable();
				$tableusers->addCode($tableitems->generate_output());
			$tableusers->endTableData();
		}
		$tableusers->endTableRow();
		$tableusers->endTable();
		$venster->addCode($tableusers->generate_output());
		/* }}} */
	} else {
		/* {{{ single user view */
		$venster->addCode("<b>".$user_data->getUsernameById($_SESSION["user_id"])."</b>");
		$venster->addSpace(2);
		$venster->insertAction("new", gettext("nieuwe afspraak"), "javascript: calendaritem_edit(0, 0, $datemask);");

		$view = new Layout_view();


		$cal = $calendar_data->calendar_items;
		unset($flag);
		while (!$flag && count($cal)) {
			for ($i=0;$i<count($cal);$i++) {
				$flag = 1;
				if ($cal[$i]["start_time"] > $cal[$i+1]["start_time"]) {
					$tmp       = $cal[$i];
					$cal[$i]   = $cal[$i+1];
					$cal[$i+1] = $tmp;
					$flag = 0;
				}
			}
		}
		$view->addData($cal);


		/* map our columns */
		$view->addMapping(gettext("tijd"), array(
			"%shuman",
			"-",
			"%ehuman",
			"\n",
			"%%complex_time"
		));
		$view->addMapping(gettext("agendapunt"), array(
			"%%complex_important",
			"%%complex_location",
			"%subject",
			"\n",
			"%body",
			"\n",
			"%%complex_extrainfo"
		));
		/* define the mappings */
		$view->defineComplexMapping("complex_time", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("verwijderen"),
				"link" => array("javascript: calendaritem_remove(", "%id", ");"),
				"check" => "%show_actions"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("bewerken"),
				"link" => array("javascript: calendaritem_edit(", "%id", ",", "%user_id", ",$datestamp)"),
				"check" => "%show_actions"
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"alt"  => gettext("uren registreren"),
				"link" => array("javascript: calendaritem_reg(", "%id", ",", "%user_id", ",$datestamp)"),
				"check" => "%show_actions"
			),
			array(
				"type" => "action",
				"src"  => "info",
				"alt"  => gettext("tonen"),
				"link" => array("javascript: toonInfo(", "%id", ");")
			)
		));
		$view->defineComplexMapping("complex_important", array(
			array(
				"type"  => "action",
				"src"   => "important",
				"alt"   => gettext("belangrijke afspraak"),
				"check" => "%important"
			),
			array(
				"text"  => "<b>".gettext("belangrijke afspraak")."</b>\n",
				"check" => "%important"
			)
		));
		$view->defineComplexMapping("complex_location", array(
			array(
				"type"  => "text",
				"text"  => array(gettext("locatie"), ": ", "%location", ", ", gettext("kilometers"), ": ", "%km", "\n"),
				"check" => "%location"
			)
		));
		$view->defineComplexMapping("complex_extrainfo", array(
			array(
				"type" => "text",
				"text" => array(gettext("relatie"), ": "),
				"check" => "%relation"
			),
			array(
				"type" => "link",
				"link" => array("index.php?mod=address&action=relcard&id=", "%relation"),
				"text" => "%relation_name",
				"check" => "%relation"
			),
			array(
				"type" => "text",
				"text" => array(" ", gettext("project"), ": "),
				"check" => "%project_id"
			),
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showhours&id=", "%project_id"),
				"text" => "%project_name",
				"check" => "%project_id"
			),
			array(
				"type" => "text",
				"text" => array(" ", gettext("notitie"), ": ")
			),
			array(
				"type"  => "link",
				"link"  => array("index.php?mod=note&action=message&msg_id=", "%note_id"),
				"text"  => "%note_title",
				"check" => "%note_id"
			),
			array(
				"type"  => "link",
				"link"  => array("javascript: popup('index.php?mod=note&action=edit&calendar_id=", "%id", "&address_id=", "%relation", "&project_id=", "%project_id", "');"),
				"text"  => gettext("maak notitie"),
				"check" => "%no_note"
			)
		));
		/* generate the view and put it in the window object */
		$venster->addCode( $view->generate_output() );
		unset($view);
		/* }}} */
	}

	$rcpt = implode(",", $userarr);
	$venster->addTag("br");
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("andere gebruikers agenda:"), "", "");
		$table->addTableData("", "");
			$table->addHiddenField("extrauser", $rcpt);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("extrauser", $rcpt, 1, 0, 0, 0) );
			$table->insertAction("forward", gettext("toepassen"), "javascript: document.getElementById('calendarform').submit();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());

$venster->endVensterData();
$output->addTag("form", array(
	"id"     => "calendarform",
	"method" => "get",
	"action" => "index.php"
));

$output->addCode( $venster->generate_output() );
unset($venster);
unset($calendar_data->calendar_items);
unset($items_arr);

$output->addTag("br");

foreach ($userarr as $useridtolookup) {
	$venster_settings = array(
		"title"    => gettext("weekplanning")." ".$users[$useridtolookup],
		"subtitle" => gettext("week")." ".strftime("%V", mktime(0,0,0,$month,$day,$year))
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$table = new Layout_table(array("width" => "100%"));
		$table->addTableRow();
			$table->addTableData();

				/* weekview */
				$weekstart = mktime(0,0,0,$month,$day-date("w",mktime(0,0,0,$month,$day,$year)),$year);
				$buffer = array();

				$days = array(
					gettext("zo"),
					gettext("ma"),
					gettext("di"),
					gettext("wo"),
					gettext("do"),
					gettext("vr"),
					gettext("za")
				);
				for ($i=0;$i<7;$i++) {
					/* weekday */
					$start_day   = date("d", ($weekstart+(24*$i*60*60)));
					$start_month = date("m", ($weekstart+(24*$i*60*60)));
					$start_year  = date("Y", ($weekstart+(24*$i*60*60)));
					$items_arr = $calendar_data->_get_appointments($useridtolookup, $start_month, $start_day, $start_year, 1);
					$days_header[$days[$i]]["longdate"] = $days[$i]." (".$start_day."/".$start_month.")";
					$days_header[$days[$i]]["linkdate"] = "&day=$start_day&month=$start_month&year=$start_year";
					if ($multiuser) {
						$days_header[$days[$i]]["linkdate"] .= "&extrauser=".implode(",", $userarr);
					}
					if (count($calendar_data->calendar_items)) {
						foreach ($calendar_data->calendar_items as $v) {
							$buffer[$days[$i]][] = array(
								"now"     => $days[$i],
								"id"      => $v["id"],
								"shuman"  => $v["shuman"],
								"ehuman"  => $v["ehuman"],
								"subject" => substr(strip_tags($v["subject"]),0,25)
							);
						}
					} else {
						$buffer[$days[$i]][] = array();
					}
					//$table->addCode(print_r($calendar_data->calendar_items,true)."<br>");
					unset($item_arr);
					unset($calendar_data->calendar_items);
					/* weekday */
				}
				$weekdata = array($buffer);

				$weekview = new Layout_view();
				foreach ($days as $wd) {
					$weekview->addMapping("%%complex_weekday_header_".$wd, "%%complex_weekday_".$wd);
					$weekview->defineComplexMapping("complex_weekday_header_".$wd, array(
						array(
							"type" => "link",
							"text" => $days_header[$wd]["longdate"],
							"link" => "index.php?mod=calendar".$days_header[$wd]["linkdate"]
						)
					));
					$weekview->defineComplexMapping("complex_weekday_".$wd, array(
						array(
							"type"  => "array",
							"array" => $wd,
							"mapping" => "%%complex_appointment"
						)
					));
				}
				$weekview->defineComplexMapping("complex_appointment", array(
					array(
						"type" => "action",
						"src"  => "info",
						"alt"  => gettext("tonen"),
						"link" => array("javascript: toonInfo(", "%id", ");"),
						"check" => "%id"
					),
					array(
						"type" => "link",
						"text" =>
							array(
								"%shuman",
								" - ",
								"%ehuman",
								"\n",
								"%subject",
								"</span>",
								"\n"
							),
						"link" => array("javascript: calendaritem_edit(", "%id", ", ".$useridtolookup.", $datemask);")
					)
				));
				$weekview->addData($weekdata);

				$table->addCode( $weekview->generate_output() );

				/* end view */

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	foreach ($formitems as $item=>$value) {
		$output->addHiddenField($item, $value);
	}
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");
	unset($venster);
}
$output->load_javascript(self::include_dir_main."xmlhttp.js");
$output->load_javascript(self::include_dir."show_main.js");
$output->layout_page_end();
echo $output->generate_output();

?>
