<?php
/**
 * Covide Groupware-CRM calendar module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
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
if (!isset($month)) { $month = date("m"); }
if (!isset($day))   { $day   = date("d"); }
if (!isset($year))  { $year  = date("Y"); }
$datestamp = mktime(0, 0, 0, $month, $day, $year);
/* regenerate date variables becaus they can contain stuff like day:45/month:10 */
$day   = date("d", $datestamp);
$month = date("m", $datestamp);
$year  = date("Y", $datestamp);
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
			$userarr = array_merge($userarr, $members);
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
/* strip empty items */
foreach ($userarr as $k=>$v) {
	if ((int)$v <= 0)
		unset($userarr[$k]);
}
/* init calendar data object */
$calendar_data = new Calendar_data();

if ($multiuser) {
	$first_item=480;
	$last_item=1080;
	$width = floor(100/count($userarr));
	foreach ($userarr as $uid) {
		$items  = $calendar_data->_get_appointments($uid, $month, $day, $year);

		// We can not rely on 1 being the first appointments, events will not be returned
		// in $items, so we have to find the lowest number not being 0 and search for that.
		$first_appointment = 1; // no appointments!
		$keys = array_unique(array_values($items));
		sort($keys);
		if (count($keys) > 1) {
			$first_appointment = $keys[1];
		}

		$searchArray = array_search($first_appointment,$items);
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
$output->layout_page(gettext("calendar"));
$venster_settings = array(
	"title"    => gettext("calendar"),
	"subtitle" => strftime("%A, %e %B %Y", mktime(0,0,0,$month,$day,$year))
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
/* menu items */
$venster->addMenuItem(gettext("new appointment"), "javascript: calendaritem_edit(0, ".$_SESSION["user_id"].", $datemask);");
$venster->addMenuItem(gettext("print calendar"), "javascript: popup('index.php?mod=calendar&action=print_main&day=$day&month=$month&year=$year&extrauser=$rcpt', 'printmain', 800, 300, 1);");
$venster->addMenuItem(gettext("print to do's"), "javascript: todos_print();");
$venster->addMenuItem(gettext("to do overview"), "./?mod=todo");
$venster->addMenuItem(gettext("weekly overview"), sprintf("./?mod=calendar&action=show_planning&users=%s&day=%d&month=%d&year=%d",
	implode(",", $userarr), $day, $month, $year
));
if ($multiuser)
	$venster->addMenuItem(gettext("monthly overview"), sprintf("./?mod=calendar&action=monthview_mu&user=%s&day=%d&month=%d&year=%d",
		implode(",", $userarr), $day, $month, $year
	));
else
	$venster->addMenuItem(gettext("monthly overview"), sprintf("./?mod=calendar&action=monthview&day=%d&month=%d&year=%d",
		$day, $month, $year
	));
$venster->addMenuItem(gettext("share calendar"), "javascript: popup('index.php?mod=calendar&action=permissionintro', 'perm', 0, 0, 1);");
$venster->addMenuItem(gettext("kilometers"), "./?mod=calendar&action=km");
$venster->generateMenuItems();
$venster->generateCalendar($month, $day, $year, $userarr);

/* page data */
$venster->addVensterData();
	/* users with setting "monthview as default" can overwrite this in monthview. Allow them to switch back */
	if ($_SESSION["calendar_forceday"]) {
		$venster->addCode(gettext("daily view is not the default calendarmode.")." ".gettext("Click the icon to return to monthly view."));
		$venster->insertAction("cancel", gettext("undo"), "index.php?mod=calendar&action=monthview");
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
					$tableusers->insertAction("new", gettext("new appointment"), "javascript: calendaritem_edit(0, $k, $datemask);");
				}
				$tableitems = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
				$_temp_id = 0;

				// First make a row for the events.
				$tableitems->addTableRow();
					$tableitems->addTableData("", "data");
						$tableitems->addCode(gettext("Events"));
					$tableitems->endTableData();
					$tableitems->addTableData("", "data");
						$tableitems->addSpace(3);
					$tableitems->endTableData();
					$tableitems->addTableData("", "data");
					$onefound = false;
					if (is_array($calendar_items[$k])) {
						foreach ($calendar_items[$k] as $item) {
							if ($item["is_event"]) {
								/* if we have permissions, make the title clickable so we can edit the event */
								$tableitems->addCode($item["subject"]);
								$tableitems->addSpace(1);
								$onefound = true;
							}
						}
					}
					if (!$onefound)
						$tableitems->addCode(gettext("None"));
					$tableitems->endTableData();
				$tableitems->endTableRow();	
				// Now iterate throught the appointments.
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
						$tableitems->insertAction("info", gettext("show"), "javascript: toonInfo(".$calendar_items[$k][$v[$i]]["id"].");");
								if ($calendar_items[$k][$v[$i]]["important"]) {
									$tableitems->insertAction("important", gettext("important meeting"), "", "");
								}
								if ($calendar_items[$k][$v[$i]]["is_private"] && $calendar_items[$k][$v[$i]]["user_id"] == $_SESSION["user_id"]) {
								//	$tableitems->addCode(gettext("private"));
									$tableitems->insertAction("state_private", gettext("private appointment"), "", "");
									$tableitems->addTag("br");
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
		$venster->insertAction("new", gettext("new appointment"), "javascript: calendaritem_edit(0, 0, $datemask);");
		$cal = $calendar_data->calendar_items;
		/* generate the view and put it in the window object */
		$venster->addCode($this->show_day($cal, $datestamp));
		/* }}} */
	}

	$venster->addTag("br");
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("different users' calendar:"), "", "");
		$table->addTableData("", "");
			$table->addHiddenField("extrauser", $rcpt);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("extrauser", $rcpt, 1, 0, 0, 0, 1) );
			$table->insertAction("forward", gettext("apply"), "javascript: document.getElementById('calendarform').submit();");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));
			$table->insertAction("print", gettext("print"), "javascript: popup('index.php?mod=calendar&action=print_main&day=$day&month=$month&year=$year&extrauser=$rcpt', 'printmain', 800, 300, 1);");
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
