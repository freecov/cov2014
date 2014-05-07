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
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}

/* Get users prefered calendar time interval. ie. 5 15 or 30 */
$user_data = new User_data();
$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
$intervalAmount = $userinfo["calendarinterval"];

if ($_REQUEST["forceweek"] != -1 && ($_SESSION["showweek"] == 1 || $_REQUEST["forceweek"] == 1 || $userinfo["calendarmode"] == 4)) {
	$_SESSION["showweek"] = 1;
} else {
	$_SESSION["showweek"] = 0;
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
	"mod"     => "calendar",
	"month"   => $month,
	"day"     => $day,
	"year"    => $year,
	"action"  => "",
	"id"      => "",
	"user_id" => "",
	"datestamp" => ""
);
/* init user array */
$users = $user_data->getUserlist(1);
/* check if we are going to fetch multiple users */
if ($_REQUEST["extrauser"] && $_REQUEST["extrauser"]!=$_SESSION["user_id"]) {
	$rcpt = $_REQUEST["extrauser"];
	//$extrauser_tmp = $_SESSION["user_id"].",".$_REQUEST["extrauser"];
	$extrauser_tmp = $_REQUEST["extrauser"];
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
		$extrauser_tmp = $userinfo["calendarselection"];
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
	//$width = floor(100/count($userarr));
	$width="";
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
$output->layout_page(gettext("Calendar"));
$venster_settings = array(
	"title"    => gettext("Calendar"),
	"subtitle" => utf8_encode(strftime("%A, %e %B %Y", mktime(0,0,0,$month,$day,$year))),
	//"nofullwidth" => 1
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
/* menu items */
$venster->addMenuItem(gettext("new appointment"), "javascript: calendaritem_edit(0, ".$_SESSION["user_id"].", $datemask);", "", 0);
if ($_SESSION["locale"] == "nl_NL") {
	$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Agenda", array("target" => "_blank"), 0);
}
if (!$_SESSION["showweek"]) {
	$venster->addMenuItem(gettext("daily view with weekoverview"), sprintf("index.php?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d&forceweek=1",
		implode(",", $userarr), $day, $month, $year));
} else {
	$venster->addMenuItem(gettext("daily view without weekoverview"),  sprintf("index.php?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d&forceweek=-1",
		implode(",", $userarr), $day, $month, $year));
}
$venster->addMenuItem(gettext("print calendar"), "javascript: popup('index.php?mod=calendar&action=print_main&day=$day&month=$month&year=$year&extrauser=$rcpt', 'printmain', 800, 300, 1);");
$venster->addMenuItem(gettext("print to do's"), "javascript: todos_print();");
$venster->addMenuItem(gettext("to do overview"), "./?mod=todo");
$venster->addMenuItem(gettext("weekly planning"), sprintf("./?mod=calendar&action=show_planning&users=%s&day=%d&month=%d&year=%d",
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
//$venster->addMenuItem(gettext("kilometers"), "./?mod=calendar&action=km");
$venster->addMenuItem(gettext("notificationtext"), "./?mod=calendar&action=notificationtemplate&user_id=".$_SESSION["user_id"]);
$venster->generateMenuItems();
//$venster->generateCalendar($month, $day, $year, $userarr);

/* page data */
$venster->addVensterData();
if ($multiuser) {
	$venster->addTag("div", array("style" => "width: 100%; overflow-x: auto;"));
}
//XXX: Why is it that I cannot valign this content in the middle ? Ask arjenc
	$table_s = new Layout_table( array("width"=>"100%", "border" => 0) );
	$table_s->addTableRow();
		$table_s->addTableData(array("valign" => "middle", "colspan" => 2));
			$table_s->addCode( $output->nbspace(3) );
			$table_s->addCode( gettext("search").": ");
			$table_s->addTextField("search_term", "");
			$table_s->insertAction("forward", gettext("search"), "javascript: search_jump();");
			$table_s->start_javascript();
				$table_s->addCode("
					document.getElementById('search_term').focus();
				");
			$table_s->end_javascript();
		$table_s->endTableData();
	$table_s->endTableRow();
	$table_s->addTableRow();
	$table_s->addTableData(array("valign" => "top", "width" => "200"));
	/* users with setting "monthview as default" can overwrite this in monthview. Allow them to switch back */
	if ($_SESSION["calendar_forceday"]) {
		$table_s->addCode(gettext("daily view is not the default calendarmode.")." ".gettext("Click the icon to return to monthly view."));
		$multi_users = $_REQUEST["extrauser"];
		if ($multi_users)
			$table_s->insertAction("cancel", gettext("undo"), "index.php?mod=calendar&action=monthview_mu&user=".$multi_users);
		else
			$table_s->insertAction("cancel", gettext("undo"), "index.php?mod=calendar&action=monthview");
		$table_s->addTag("br");
	}
	$baseurl = "index.php?mod=calendar&day=1&extrauser=".implode(",", $userarr);
	$urlnext = $baseurl."&month=".($month+1)."&year=$year";
	if ($month == 1) {
		$urlprev = $baseurl."&year=".($year-1)."&month=12";
	} else {
		$urlprev = $baseurl."&year=$year&month=".($month-1);
	}
	$table_s->addTag("div", array("id" => "menucalendar", "style" => ""));
	$table_s->insertAction("back", gettext("previous month"), $urlprev);
	$table_s->addSpace(1);
	$table_s->addCode(utf8_encode(ucfirst(strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year)))));
	$table_s->addSpace(1);
	$table_s->insertAction("forward", gettext("next month"), $urlnext);
	/* table for calendar */

	$caltable = new Layout_table(array("cellspacing"=>1,
		"class"=>"calendar_table"
	));
	$caltable->addTableRow();
		$caltable->insertTableData(gettext("sun"), "", "header");
		$caltable->insertTableData(gettext("mon"), "", "header");
		$caltable->insertTableData(gettext("tue"), "", "header");
		$caltable->insertTableData(gettext("wed"), "", "header");
		$caltable->insertTableData(gettext("thu"), "", "header");
		$caltable->insertTableData(gettext("fri"), "", "header");
		$caltable->insertTableData(gettext("sat"), "", "header");
		$caltable->insertTableData("w", "", "header");
	$caltable->endTableRow();
	$caltable->addTableRow();
		/* Skip weekdays to first day of the month */
		$skipdays = date("w",mktime(0,0,0,$month,1,$year));
		$dow=$skipdays;
		for ($i=0; $i!=$skipdays; $i++) {
			$caltable->insertTableData("&nbsp;", "", "data");
		}
		/* display days of the month */
		for ($i=0; $i!=date("t",mktime(0,0,0,$month,1,$year)); $i++) {
			$url = "index.php?mod=calendar&day=".($i+1)."&month=$month&year=$year&extrauser=".implode(",", $userarr);
			if (($i+1) == date("d") && $month == date("m") && $year == date("Y")) {
				$caltable->addTableData("", "header");
			} else {
				$caltable->addTableData("", "data");
			}
				$caltable->insertLink($i+1, array("href" => $url));
			$caltable->endTableData();
			$dow++;
			if ($dow == 7) {
				/* end of the week, start new row, but first print week number */
				if (!(($i+1)==date("t",mktime(0,0,0,$month,1,$year)))) {
					$caltable->insertTableData("<i>".date("W",mktime(0,0,0,$month,$i,$year))."</i>", "", "data");
					$caltable->endTableRow();
					$caltable->addTableRow();
					$lastweek = date("W",mktime(0,0,0,$month,$i+7,$year));
				}
				$dow = 0;
			}
		}
		/* complete the fields so table looks better */
		if ($dow) {
			for ($i=0; $i!=7-$dow; $i++) {
				$caltable->insertTableData("&nbsp;", "", "data");
			}
		}
		$caltable->insertTableData("<i>".$lastweek."</i>", "", "data");
	$caltable->endTableRow();
	$caltable->endTable();
	$table_s->addCode($caltable->generate_output());
	unset($caltable);
	/* lil table and form for direct jumping in the calendar */
	$searchtable = new Layout_table();
	$searchtable->addTableRow();
		$searchtable->addTableData(array("align" => "left"));
			$searchtable->addTextField("search_day", $day, array("style"=>"width: 25px;"));
			$searchtable->addTextField("search_month", $month, array("style"=>"width: 25px;"));
			$searchtable->addTextField("search_year", $year, array("style"=>"width: 50px;"));
			$searchtable->insertAction("forward", gettext("search"), "javascript: date_jump();");
		$searchtable->endTableData();
	$searchtable->endTableRow();
	$searchtable->endTable();
	$table_s->addCode($searchtable->generate_output());

	$rcpt = implode(",", $userarr);
	$table_user = new Layout_table(array("cellspacing"=>1));
	$table_user->addTableRow();
		$table_user->insertTableData("<p>".gettext("different users' calendar:")."</p>", array(
			"class" => "valign_top",
			"align" => "left",
		), "");
	$table_user->endTableRow();
	$table_user->addTableRow();
		$table_user->addTableData(array("align" => "left"), "");
			$table_user->addHiddenField("extrauser", $rcpt);
			$useroutput = new User_output();
			$table_user->addCode( $useroutput->user_selection("extrauser", $rcpt, 1, 0, 0, 0, 1) );
			$table_user->insertAction("forward", gettext("apply"), "javascript: document.getElementById('calendarform').submit();");
		$table_user->endTableData();
	$table_user->endTableRow();
	$table_user->endTable();
	$table_s->addCode($table_user->generate_output());
	$table_s->endTag("div");
	$table_s->endTableData();
	$table_s->addTableData(array("valign" => "top"));


	if ($multiuser)  {
		/* {{{ multiple users view */
		$tableusers = new Layout_table(array("width" => "100%"));
		$tableusers->addTableRow();

		foreach ($items_arr as $k=>$v) { $arr[] = $k; }
		foreach ($items_arr as $k=>$v) {
			$permissions = $calendar_data->checkPermission($k, $_SESSION["user_id"]);
			$tableusers->addTableData(array("class"=>"calendar_user"));
				$tableusers->addTag("span", array("class" => "calendar_usercolumn"));
				$tableusers->addCode("<b>".$user_data->getUsernameById($k)."</b>");
				$tableusers->addSpace(1);
				if (($permissions != "0" && $permissions == "RW") || $k == $_SESSION["user_id"]) {
					$tableusers->insertAction("new", gettext("new appointment"), "javascript: calendaritem_edit(0, $k, $datemask);");
				}
				/* No permissions. Ask for permissions by the means of a note? */
				if (($permissions == "0") && $k != $_SESSION["user_id"]) {
					$tableusers->insertAction("logout", gettext("ask for permissions"), "javascript: ask_for_permissions($k);");
				}

				/* NOTE: Maybe someone can tidy this up */
				/* Make a button to close a specific agenda. i.e. Just refresh the page with the extra users except for.. etc.. */
				$removeMyselfFromExtraUsers = str_replace(",", " ", implode(",",$arr));
				$removeMyselfFromExtraUsers = str_replace($k, "", $removeMyselfFromExtraUsers);
				$removeMyselfFromExtraUsers = str_replace("  ", ",", $removeMyselfFromExtraUsers);
				$removeMyselfFromExtraUsers = str_replace(" ", ",", $removeMyselfFromExtraUsers);
				$tableusers->insertAction("close", gettext("close this agenda"),
					sprintf("./?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d",
						$removeMyselfFromExtraUsers, $day, $month, $year
				));
				$tableusers->insertAction("print", gettext("print"), "javascript: popup('index.php?mod=calendar&action=print_main&day=$day&month=$month&year=$year&extrauser=$k', 'printmain', 800, 300, 1);");
				$tableusers->endTag("span");

				$tableitems = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
				$_temp_id = 0;

				// First make a row for the events.
				$tableitems->addTableRow();
					$tableitems->addTableData(array("class" => "calendar_datecolumn"), "data");
						$tableitems->addCode(gettext("Events"));
					$tableitems->endTableData();
					$tableitems->addTableData(array("class" => "calendar_contentcolumn"), "data");
					$onefound = false;
					if (is_array($calendar_items[$k])) {
						foreach ($calendar_items[$k] as $item) {
							if ($item["is_event"] || $item["alldayevent"]) {
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
				for ($i=$first_item; $i<=$last_item; $i+=$intervalAmount) {
					$tableitems->addTableRow();
						$tableitems->addTableData(array("class" => "calendar_datecolumn"), "data");
							$tableitems->addCode(date("H:i", mktime(0,$i,0,date("m"), date("d"), date("Y"))));
						$tableitems->endTableData();
						$p = $v[$i];
						if (strpos($p, ",") !== false ) {
							$p = explode(",", $p);
						} else {
							$p = array($p);
						}
						if ($v[$i] && !in_array($_temp_id, $p)) {
							$rowspan = 0;
							if (strpos($v[$i], ",") !== false) {
								$_items = explode(",", $v[$i]);
								foreach ($_items as $x) {
									if ((int)($calendar_items[$k][$x]["rowspan"]/3) > $rowspan) {
										$ni = $x;
										$rowspan = (int)($calendar_items[$k][$x]["rowspan"]/3);
										$_temp_id = $x;
									}
								}
							} else {
								$rowspan = (int)($calendar_items[$k][$v[$i]]["rowspan"]/3);
								$ni = $i;
								$_temp_id = $v[$i];
							}
							$tableitems->addTableData(array("rowspan" => $rowspan, "style" => "font-weight: normal;", "class" => "calendar_contentcolumn"), "header");
								if ($calendar_items[$k][$v[$ni]]["important"]) {
									$tableitems->insertAction("important", gettext("important meeting"), "", "");
								}
								if ($calendar_items[$k][$v[$ni]]["is_private"] && $calendar_items[$k][$v[$ni]]["user_id"] == $_SESSION["user_id"]) {
									$tableitems->insertAction("state_private", gettext("private appointment"), "", "");
									$tableitems->addTag("br");
								}
								if ($calendar_items[$k][$v[$ni]]["location"]) {
									$tableitems->addTag("br");
									$tableitems->addCode($calendar_items[$k][$v[$ni]]["location"]);
									if ($calendar_items[$k][$v[$ni]]["km"]) {
										$tableitems->addCode(", ");
										$tableitems->addCode($calendar_items[$k][$v[$ni]]["km"]);
										$tableitems->addCode("km");
									}
									$tableitems->addTag("br");
								}
								$tableitems->insertLink($calendar_items[$k][$v[$ni]]["human_span_short"] ."&nbsp;-&nbsp;". $calendar_items[$k][$v[$ni]]["subject"], array("href" => "javascript: toonInfo(".$calendar_items[$k][$v[$ni]]["id"].", ".$calendar_items[$k][$v[$ni]]["user_id"].");"));

							$tableitems->endTableData();
						} elseif(!$v[$i]) {
							$tableitems->addTableData(array("class" => "calendar_contentcolumn"), "data");
								$tableitems->addSpace(1);
							$tableitems->endTableData();
						}
					$tableitems->endTableRow();
				}
				$tableitems->endTable();
				$tableusers->addCode($tableitems->generate_output());
			$tableusers->endTableData();
		}
		$tableusers->endTableRow();
		$tableusers->endTable();
		$table_s->addCode($tableusers->generate_output());
		/* }}} */
	} else {
		/* {{{ single user view */
		$table_s->addCode("<b>".$user_data->getUsernameById($_SESSION["user_id"])."</b>");
		$cal = $calendar_data->calendar_items;
		/* generate the view and put it in the window object */
		$table_s->addCode($this->show_day($cal, $datestamp));
		/* }}} */
	}
	$table_s->endTableData();
	$table_s->endTableRow();
	$table_s->endTable();

	$venster->addCode($table_s->generate_output());
	unset($table_s);

	$venster->addTag("br");
	if ($multiuser) {
		$venster->endTag("div");
	}
$venster->endVensterData();
$output->addTag("form", array(
	"id"     => "calendarform",
	"method" => "get",
	"action" => "index.php"
));

unset($calendar_data->calendar_items);
unset($items_arr);

if ($_SESSION["showweek"]) {
	foreach ($userarr as $useridtolookup) {
		if ($multiuser) {
			$extrausers = implode(",", $userarr);
		} else {
			$extrausers = 0;
		}
		$week = $this->show_week($useridtolookup, $month, $day, $year, $extrausers);
		$venster->addCode($week->generate_output());
	}
}
foreach ($formitems as $item=>$value) {
	$venster->addHiddenField($item, $value);
}
$venster->addHiddenField("notifyuser", "");

$output->addCode( $venster->generate_output() );
unset($venster);
$output->endTag("form");
$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );

$output->load_javascript(self::include_dir_main."xmlhttp.js");
$output->load_javascript(self::include_dir."show_main.js");
$output->layout_page_end();

echo $output->generate_output();

?>
