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
$user_data = new User_data();

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
if ($_REQUEST["extrauser"]) {
	$extrauser = explode(",", $_REQUEST["extrauser"]);
	$extrauser = array_unique($extrauser);
} else {
	$extrauser = array($_SESSION["user_id"]);
}
$output = new Layout_output();
$output->layout_page("", 1);

/* enable group options for this action */
$tmp = $extrauser;
$extrauser = array();
foreach ($tmp as $t) {
	if (preg_match("/^G/s", $t)) {
		$t = (int)preg_replace("/^G/s", "", $t);
		$members = $user_data->getGroupInfo($t);
		$members = explode(",", $members["members"]);

		foreach ($members as $m)
			$extrauser[] = $m;
	} else {
		$extrauser[] = $t;
	}
}
$extrauser = array_unique($extrauser);

foreach ($extrauser as $user) {
	if (!$user) {
		continue;
	}
	$calendar_data = new Calendar_data();

	$items_arr  = $calendar_data->_get_appointments($user, $month, $day, $year);
	$calendar_items = $calendar_data->calendar_items;
	$username = $user_data->getUsernameById($user);
	$width = "100";
	$cal = $calendar_data->calendar_items;
	$output->addCode($username."&nbsp;".$day."-".$month."-".$year);
	$output->addCode($this->show_day($cal, $datestamp, 1));
}
$output->start_javascript();
	$output->addCode("
		window.print();
		setTimeout('window.close();', 2000);
	");
$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();
?>
