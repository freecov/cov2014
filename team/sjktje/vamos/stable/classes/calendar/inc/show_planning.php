<?php
if (!class_exists("Calendar_output"))
	die("no class definition found");

/* background colors we can use to identify types of appointments */

/*	tag definitions
 *	0 = Free
 *	1 = Busy, normal appointment
 *	2 = Busy, private appointment
 *	3 = holidays
 *	4 = special leave
 *	5 = reported sick
 *	6 = remote appointment
*/
$hours = array();
for ($i=0; $i<24; $i++) {
	$hours[$i*60] = $i;
}
$days = array();
for ($i=1;$i<32;$i++)
	$days[$i] = $i;
$months = array();
for ($i=1;$i<13;$i++)
	$months[$i] = $i;
$years = array();
for ($i=date("Y")-1; $i<date("Y")+5;$i++)
	$years[$i] = $i;
$legenda = array(
	0 => "",
	1 => " background-color: darkred;",
	2 => " background-color: red;",
	3 => " background-color: darkgreen;",
	4 => " background-color: navy;",
	5 => " background-color: lime;",
	6 => " background-color: yellow;"
);

$legenda_text = array(
	0 => gettext("available"),
	1 => gettext("business"),
	2 => gettext("private"),
	3 => gettext("holidays"),
	4 => gettext("special leave"),
	5 => gettext("reported sick"),
	6 => gettext("external appointment")
);

if (!$_REQUEST["users"])
	$users = array($_SESSION["user_id"]);
else
	$users = explode(",", $_REQUEST["users"]);

/* extract groups and add groupmembers */
$user_data = new User_data();
$_users = $users;
foreach ($_users as $k=>$v) {
	if (preg_match("/^G/", $v)) {
		/* unset entry */
		unset($_users[$k]);
		/* get groupinfo */
		$groupinfo = $user_data->getGroupInfo(substr($v, 1));
		$members = explode(",", $groupinfo["members"]);
		foreach ($members as $member)
			$_users[] = $member;
	}
}
/* make the array unique */
$_users = array_unique($_users);
sort($_users);

/* get the calendar data for this week */
$month = $_REQUEST["month"];
$day   = $_REQUEST["day"];
$year  = $_REQUEST["year"];
if (!$month) $month = date("m");
if (!$day)   $day   = date("d");
if (!$year)  $year  = date("Y");
/* make a normal date since ppl can choose 31-02 for example using the dropdowns */
$datestamp = mktime(0, 0, 0, $month, $day, $year);
$month = date("m", $datestamp);
$day   = date("d", $datestamp);
$year  = date("Y", $datestamp);

$daystart = $_REQUEST["daystart"];
$dayend   = $_REQUEST["dayend"];
if (!$daystart) $daystart = 9*60;
if (!$dayend)   $dayend   = 17*60;

$weekstart = mktime(0,0,0,$month,$day-date("w",mktime(0,0,0,$month,$day,$year)),$year);
$calendar_data = new Calendar_data();
for ($i=0;$i<7;$i++) {
	/* detect summer/winter time */
	$offset = $calendar_data->detectCestSwitch($weekstart, $weekstart+(24*$i*60*60));
	/* weekday */
	$start_day   = date("d", ($weekstart+$offset+(24*$i*60*60)));
	$start_month = date("m", ($weekstart+$offset+(24*$i*60*60)));
	$start_year  = date("Y", ($weekstart+$offset+(24*$i*60*60)));
	foreach ($_users as $user_id) {
		$items_arr[$user_id][$i]      = $calendar_data->_get_appointments($user_id, $start_month, $start_day, $start_year);
		$calendar_items[$user_id][$i] = $calendar_data->calendar_items;
		unset($calendar_data->calendar_items);
	}
}
/* start output buffer */
$output = new Layout_output();
$output->layout_page();
	$output->addTag("form", array(
		"id"     => "planning",
		"action" => "index.php",
		"method" => "post"
	));
	$output->addHiddenField("mod", "calendar");
	$output->addHiddenField("action", "show_planning");
	/* window widget */
	$venster = new Layout_venster(array("title" => gettext("scheduling")));
	$venster->addVensterData();
		/* table container for search and legenda */
		$table_al = new Layout_table();
		$table_al->addTableRow(array("style" => "vertical-align: top;"));
			$table_al->addTableData();
				/* table for search options */
				$table = new Layout_table(array("border" => 0));
				$table->addTableRow();
					$table->insertTableData("&nbsp;", "", "header");
					$table->insertTableData(gettext("users"), "", "header");
					$table->insertTableData(gettext("times"), "", "header");
					$table->insertTableData("&nbsp;", "", "header");
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData("&nbsp;", "", "data");
					$table->addTableData("", "data");
						$table->addHiddenField("users", implode(",", $users));
						$useroutput = new User_output();
						$table->addCode( $useroutput->user_selection("users", implode(",", $users), 1, 0, 0, 1, 1) );
					$table->endTableData();
					$table->addTableData(array("align" => "right"), "data");
						$table->addCode(gettext("from").": ");
						$table->addSelectField("daystart", $hours, $daystart);
						$table->addTag("br");
						$table->addCode(gettext("till").": ");
						$table->addSelectField("dayend", $hours, $dayend);
					$table->endTableData();
					$table->addTableData(array("style" => "vertical-align: bottom;"), "data");
						$table->insertAction("calendar_today", gettext("daily view"), sprintf("?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d",
							$_REQUEST["users"], $_REQUEST["day"], $_REQUEST["month"], $_REQUEST["year"]
						));
						$table->insertAction("ok", gettext("search"), "javascript: document.getElementById('planning').submit();");
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->insertAction("back", gettext("last week"), "javascript: change_week(-7);");
					$table->endTableData();
					$table->addTableData(array("colspan" => 2, "align" => "center"), "data");
						/* select boxen to pick date */
						$table->addSelectField("day", $days, $day);
						$table->addSelectField("month", $months, $month);
						$table->addSelectField("year", $years, $year);
					$table->endTableData();
					$table->addTableData("", "data");
						$table->insertAction("forward", gettext("next week"), "javascript: change_week(+7);");
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$table_al->addCode($table->generate_output());
				unset($table);
				$table_al->addTag("br");
			$table_al->endTableData();
			$table_al->addTableData();
				$table_al->addSpace(3);
			$table_al->endTableData();
			$table_al->addTableData(array("style" => "vertical-align: top;"));
				$table = new Layout_table();
				$table->addTableRow();
					$table->insertTableData(gettext("legenda"), "", "header");
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData("", "header");
						$table_leg = new Layout_table(array("cellspacing" => 1));
						foreach ($legenda_text as $nr=>$text) {
							$table_leg->addTableRow();
								$table_leg->insertTableData($text, "", "data");
								$table_leg->addTableData("", "data");
									$table_leg->insertTag("div", "&nbsp;", array("style" => "width: 100px;".$legenda[$nr]));
								$table_leg->endTableData();
							$table_leg->endTableRow();
						}
						$table_leg->endTable();
						$table->addCode($table_leg->generate_output());
						unset($table_leg);
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$table_al->addCode($table->generate_output());
				unset($table);
			$table_al->endTableData();
		$table_al->endTableRow();
		$table_al->endTable();
		$venster->addCode($table_al->generate_output());
		unset($table_al);
		/* table with actual data */
		$table = new Layout_table(array("cellspacing" => 1, "cellpadding" => 0));
		/* loop through all the days */
		for ($i=1;$i<6;$i++) {

			/* detect and get offset according to summer / winter time */
			$offset = $calendar_data->detectCestSwitch($weekstart, $weekstart+(24*$i*60*60)+($k*60));

			$datestring = strftime("%a %d %b %Y", ($weekstart+$offset+(24*$i*60*60)+($k*60)));
			$table->addTableRow();
				$table->insertTableData($datestring, "", "header");
				foreach ($_users as $user_id) {
					$table->insertTableData($user_data->getUsernameById($user_id), "", "header");
				}
			$table->endTableRow();


			foreach ($items_arr[$_users[0]][$i] as $k=>$v) {
				if ($k > $daystart-1 && $k < $dayend) {

				$table->addTableRow();
					if ($k % 60 == 0) {
						$datestring = date("H:i", ($weekstart+$offset+(24*$i*60*60)+($k*60)));
						$table->insertTableData($datestring, array("rowspan" => 4), "data");
					}
					foreach ($_users as $user_id) {
						if ($calendar_items[$user_id][$i][$items_arr[$user_id][$i][$k]]["km"])
							$calendar_items[$user_id][$i][$items_arr[$user_id][$i][$k]]["app_type"] = 6;
						$table->addTableData("", "data");
							if ($calendar_items[$user_id][$i][$items_arr[$user_id][$i][$k]]["show_actions"] || $user_id == $_SESSION["user_id"]) {
								if ($items_arr[$user_id][$i][$k])
									$act = "toonInfo(".$calendar_items[$user_id][$i][$items_arr[$user_id][$i][$k]]["id"].");";
								else
									$act = "calendaritem_edit(0, $user_id, ".($weekstart+$offset+(24*$i*60*60)+($k*60)).");";
							} else {
								$act = "void(0);";
							}
							$table->insertTag("div", "", array(
								"style" => "cursor: pointer; cursor: hand; width: 50px; height: 4px;".$legenda[$calendar_items[$user_id][$i][$items_arr[$user_id][$i][$k]]["app_type"]],
								"onclick" => $act
							));
						$table->endTableData();
					}
				$table->endTableRow();
				}
			}
		}
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."show_planning.js");
	$output->load_javascript(self::include_dir."show_main.js");
/* flush output buffer to client */
$output->layout_page_end();
$output->exit_buffer();
?>
