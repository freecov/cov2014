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
/* make arrays for time dropdowns */
for ($i=0; $i<24; $i++) {
	if ($i<10) {
		$hours[$i] = "0".$i;
	} else {
		$hours[$i] = $i;
	}
}
for ($i=0; $i<60; $i+=15) {
	if ($i<10) {
		$minutes[$i] = "0".$i;
	} else {
		$minutes[$i] = $i;
	}
}
if ($_REQUEST["timestamp"])
/* get the appointment we are trying to register */
$calendar_data = new Calendar_data();
$calendaritem = $calendar_data->getCalendarItemById($_REQUEST["id"]);
$calendaritem["is_billable"] =1;
if ($_REQUEST["timestamp"]) {
	$calendaritem["begin_day"]   = date("d", $_REQUEST["timestamp"]);
	$calendaritem["begin_month"] = date("m", $_REQUEST["timestamp"]);
	$calendaritem["begin_year"]  = date("Y", $_REQUEST["timestamp"]);
}

$activities = $calendar_data->getActivityNames();
$activities[0] = gettext("none");

if (!$calendaritem["timestamp_start"] && !$calendaritem["timestamp_end"]) {
	die("fatal error, no appointment known");
}

/* start output handler */
$output = new Layout_output();
$output->layout_page("", 1);
	/* form for data */
	$output->addTag("form", array(
		"id"     => "reginput",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "calendar");
	$output->addHiddenField("action", "reg_save");

	if ($calendaritem["is_ill"])
		$output->addHiddenField("regitem[app_type]", 5);
	elseif ($calendaritem["is_specialleave"])
		$output->addHiddenField("regitem[app_type]", 4);
	elseif ($calendaritem["is_holiday"])
		$output->addHiddenField("regitem[app_type]", 3);
	elseif ($calendaritem["is_private"])
		$output->addHiddenField("regitem[app_type]", 2);
	else
		$output->addHiddenField("regitem[app_type]", 1);

	$output->addHiddenField("regitem[calendar_id]", $calendaritem["id"]);
	$output->addHiddenField("regitem[address_id]", $calendaritem["address_id"]);
	$output->addHiddenField("regitem[project_id]", $calendaritem["project_id"]);
	$output->addHiddenField("regitem[day]", $calendaritem["begin_day"]);
	$output->addHiddenField("regitem[month]", $calendaritem["begin_month"]);
	$output->addHiddenField("regitem[year]", $calendaritem["begin_year"]);
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("hour registration"),
		"subtitle" => $calendaritem["begin_day"]." ".date("F", $calendaritem["timestamp_start"])." ".$calendaritem["begin_year"]
	));
	$venster->addVensterData();
		/* use a table to align stuff */
		$table = new Layout_table();
		$table->addTableRow();
			$table->insertTableData(gettext("starttime"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("regitem[fromhour]", $hours, $calendaritem["begin_hour"]);
				$table->addSpace();
				$table->addCode(":");
				$table->addSpace();
				$table->addSelectField("regitem[fromminute]", $minutes, $calendaritem["begin_min"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("endtime"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("regitem[tohour]", $hours, $calendaritem["end_hour"]);
				$table->addSpace();
				$table->addCode(":");
				$table->addSpace();
				$table->addSelectField("regitem[tominute]", $minutes, $calendaritem["end_min"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->insertTag("span", $calendaritem["project_name"], array("id"=>"searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("activity"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("regitem[activity_id]", $activities);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("send an invoice"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckbox("regitem[is_billable]", 1, $calendaritem["is_billable"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("description"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("regitem[description]", $calendaritem["description"], array("style"=>"width: 500px; height: 200px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->insertAction("save", gettext("save"), "javascript: reg_save();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		/* end table */
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	/* end window widget */
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."reg_input.js");
$output->layout_page_end();
$output->exit_buffer();
?>
