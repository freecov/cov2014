<?php
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
$month = $_REQUEST["month"];
$day   = $_REQUEST["day"];
$year  = $_REQUEST["year"];
$userid = $_REQUEST["userid"];
if (!$userid) $userid = $_SESSION["user_id"];
if ($_REQUEST["timestamp"]) {
	$month = date("m", $_REQUEST["timestamp"]);
	$day   = date("d", $_REQUEST["timestamp"]);
	$year  = date("Y", $_REQUEST["timestamp"]);
}
if (!$month) { $month = date("m"); }
if (!$day)   { $day   = date("d"); }
if (!$year)  { $year  = date("Y"); }
$datestamp = mktime(0, 0, 0, $month, $day, $year);

$week = $this->show_week($userid, $month, $day, $year, $extrausers, 1);

$output = new Layout_output();
$output->layout_page("", 1);
$output->addCode($week->generate_output());
$output->start_javascript();
	$output->addCode("
		this.print();
		window.close();
	");
$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();
?>
