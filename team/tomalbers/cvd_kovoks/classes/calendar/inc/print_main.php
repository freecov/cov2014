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

if ($_REQUEST["extrauser"]) {
	$extrauser = explode(",", $_REQUEST["extrauser"]);
	$extrauser = array_unique($extrauser);
} else {
	$extrauser = array($_SESSION["user_id"]);
}
$output = new Layout_output();
$output->layout_page("", 1);

foreach ($extrauser as $user) {
	$calendar_data = new Calendar_data();

	$items_arr  = $calendar_data->_get_appointments($user, $month, $day, $year);
	$calendar_items = $calendar_data->calendar_items;
	$user_data = new User_data();
	$username = $user_data->getUsernameById($user);
	$width = "100";
	$cal = $calendar_data->calendar_items;
	$output->addCode($username."&nbsp;".$day."-".$month."-".$year);
	$output->addCode($this->show_day($cal, $datestamp, 1));
}
$output->start_javascript();
	$output->addCode("
		this.print();
		window.close();
	");
$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();
?>
