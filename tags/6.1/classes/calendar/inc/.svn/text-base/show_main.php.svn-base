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
/* init user array */
$user_data = new User_data();
$users = $user_data->getUserlist(1);
$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);
/* check if we are going to fetch multiple users */
if ($_REQUEST["extrauser"] && $_REQUEST["extrauser"]!=$_SESSION["user_id"]) {
	$rcpt = $_REQUEST["extrauser"];
	$extrauser_tmp = $_SESSION["user_id"].",".$_REQUEST["extrauser"];
	$userarr = explode(",", $extrauser_tmp);
	/* some items may have G in their value. get rid of them and replace with current groupmembers */
	foreach ($userarr as $k=>$v) {
		if (strpos($v, "G") !== false) {
			unset($userarr[$k]);
			$groupid = substr($v, 1);
			$groupinfo = $user_data->getGroupInfo($groupid);
			$members = explode(",", $groupinfo["members"]);
			$userarr = $userarr+$members;
		}
	}
	$userarr = array_unique($userarr);
	$multiuser = true;
} else {
	if ($userinfo["calendarselection"]) {
		$extrauser_tmp = $_SESSION["user_id"].",".$userinfo["calendarselection"];
		$userarr = explode(",", $extrauser_tmp);
		$userarr = array_unique($userarr);
		if (count($userarr) > 1)
			$multiuser = true;
		else
			$multiuser = false;
	} else {
		$userarr[0] = $_SESSION["user_id"];
		$multiuser = false;
		$_SESSION["extrauser"] = "";
	}
	$rcpt = implode(",", $userarr);
}
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
		if ($searchArray > $last_item) {
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
$venster->addMenuItem(gettext("week overzicht"), "./?mod=calendar&action=show_planning");
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
		
		/* generate the view and put it in the window object */
		$venster->addCode($this->show_day($cal, $datestamp));
		/* }}} */
	}

	$venster->addTag("br");
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("andere gebruikers agenda:"), "", "");
		$table->addTableData("", "");
			$table->addHiddenField("extrauser", $rcpt);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("extrauser", $rcpt, 1, 0, 0, 0, 1) );
			$table->insertAction("forward", gettext("toepassen"), "javascript: document.getElementById('calendarform').submit();");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));
			$table->insertAction("print", gettext("printen"), "javascript: popup('index.php?mod=calendar&action=print_main&day=$day&month=$month&year=$year', 'printmain', 300, 300, 1);");
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
	if ($multiuser) {
		$extrausers = implode(",", $userarr);
	} else {
		$extrausers = 0;
	}
	$week = $this->show_week($useridtolookup, $month, $day, $year, $extrausers);
	$output->addCode($week->generate_output());
}
foreach ($formitems as $item=>$value) {
	$output->addHiddenField($item, $value);
}
$output->endTag("form");

$output->load_javascript(self::include_dir_main."xmlhttp.js");
$output->load_javascript(self::include_dir."show_main.js");
$output->layout_page_end();
echo $output->generate_output();

?>
