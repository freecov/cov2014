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
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
/* get user settings for interval */
$user = new User_data();
$userdetails = $user->getUserdetailsById($_SESSION["user_id"]);
$intervalAmount = $userdetails["calendarinterval"];
// if direct registration, allow date and user selection. Defaults to false.
$allow_date_selection = false;
$allow_user_selection = false;
/* make arrays for time dropdowns */
for ($i=0; $i<24; $i++) {
	if ($i<10) {
		$hours[$i] = "0".$i;
	} else {
		$hours[$i] = $i;
	}
}
for ($i=0; $i<60; $i+=$intervalAmount) {
	if ($i<10) {
		$minutes[$i] = "0".$i;
	} else {
		$minutes[$i] = $i;
	}
}
/* get the appointment we are trying to register */
$calendar_data = new Calendar_data();
$calendaritem = $calendar_data->getCalendarItemById($_REQUEST["id"], $_SESSION["user_id"]);
$calendaritem["is_billable"] =1;
$calendaritem["user_id"] = $_SESSION["user_id"];
if ($_REQUEST["timestamp"]) {
	$calendaritem["begin_day"]   = date("d", $_REQUEST["timestamp"]);
	$calendaritem["begin_month"] = date("m", $_REQUEST["timestamp"]);
	$calendaritem["begin_year"]  = date("Y", $_REQUEST["timestamp"]);
}

//direct registration
if ($_REQUEST["id"] == 0) {
	$calendaritem["address_id"] = sprintf("%d", $_REQUEST["address_id"]);
	$calendaritem["project_id"] = sprintf("%d", $_REQUEST["project_id"]);
	$project_data = new Project_data();
	$calendaritem["project_name"] = $project_data->getProjectNameById($_REQUEST["project_id"]);
	$allow_date_selection = true;
	$allow_user_selection = true;
}

//edit registration item
if ($_REQUEST["id_reg"]) {
	$reg_item = $calendar_data->getRegistrationItemById($_REQUEST["id_reg"]);
	$calendaritem["timestamp_start"] = $reg_item["timestamp_start"];
	$calendaritem["begin_day"] = date("d", $reg_item["timestamp_start"]);
	$calendaritem["begin_month"] = date("m", $reg_item["timestamp_start"]);
	$calendaritem["begin_year"] = date("Y", $reg_item["timestamp_start"]);
	$calendaritem["begin_hour"] = date("H", $reg_item["timestamp_start"]);
	$calendaritem["begin_min"] = date("i", $reg_item["timestamp_start"]);
	$calendaritem["timestamp_end"] = $reg_item["timestamp_end"];
	$calendaritem["end_day"] = date("d", $reg_item["timestamp_end"]);
	$calendaritem["end_month"] = date("m", $reg_item["timestamp_end"]);
	$calendaritem["end_year"] = date("Y", $reg_item["timestamp_end"]);
	$calendaritem["end_hour"] = date("H", $reg_item["timestamp_end"]);
	$calendaritem["end_min"] = date("i", $reg_item["timestamp_end"]);
	$calendaritem["project_id"] = $reg_item["project_id"];
	$project_data = new Project_data();
	$calendaritem["project_name"] = $project_data->getProjectNameById($reg_item["project_id"]);
	$calendaritem["activity_id"] = $reg_item["activity_id"];
	$calendaritem["is_billable"] = $reg_item["is_billable"];
	$calendaritem["description"] = $reg_item["description"];
	$calendaritem["user_id"] = $reg_item["user_id"];
	$allow_date_selection = true;
	$allow_user_selection = true;
}

//edit registration item
if ($_REQUEST["todo_id"]) {
	$todo_data = new Todo_data();
	$reg_item = $todo_data->getTodoById($_REQUEST["todo_id"]);
	$calendaritem["timestamp_start"] = $reg_item["timestamp"];
	$calendaritem["begin_day"] = date("d", $reg_item["timestamp"]);
	$calendaritem["begin_month"] = date("m", $reg_item["timestamp"]);
	$calendaritem["begin_year"] = date("Y", $reg_item["timestamp"]);
	$calendaritem["begin_hour"] = date("H", $reg_item["timestamp"]);
	$calendaritem["begin_min"] = date("i", $reg_item["timestamp"]);
	$calendaritem["timestamp_end"] = $reg_item["timestamp_end"];
	$calendaritem["end_day"] = date("d", $reg_item["timestamp_end"]);
	$calendaritem["end_month"] = date("m", $reg_item["timestamp_end"]);
	$calendaritem["end_year"] = date("Y", $reg_item["timestamp_end"]);
	$calendaritem["end_hour"] = date("H", $reg_item["timestamp_end"]);
	$calendaritem["end_min"] = date("i", $reg_item["timestamp_end"]);
	$calendaritem["project_id"] = $reg_item["project_id"];
	$project_data = new Project_data();
	$calendaritem["project_name"] = $project_data->getProjectNameById($reg_item["project_id"]);
	$calendaritem["activity_id"] = $reg_item["activity_id"];
	$calendaritem["is_billable"] = $reg_item["is_billable"];
	$calendaritem["description"] = $reg_item["body"];
	$calendaritem["user_id"] = $reg_item["user_id"];
	$allow_date_selection = true;
	$allow_user_selection = true;
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
		"method" => "post",
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
	$output->addHiddenField("regitem[user_id]", $calendaritem["user_id"]);
	if ($_REQUEST["id_reg"]) {
		$output->addHiddenField("regitem[id_reg]", $_REQUEST["id_reg"]);
	}
	if ($_REQUEST["todo_id"]) {
		$output->addHiddenField("regitem[todo_id]", $_REQUEST["todo_id"]);
	}

	if (!$allow_date_selection) {
		$output->addHiddenField("regitem[day]", $calendaritem["begin_day"]);
		$output->addHiddenField("regitem[month]", $calendaritem["begin_month"]);
		$output->addHiddenField("regitem[year]", $calendaritem["begin_year"]);
	}
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("hour registration"),
		"subtitle" => $calendaritem["begin_day"]." ".date("F", $calendaritem["timestamp_start"])." ".$calendaritem["begin_year"]
	));
	$venster->addVensterData();
		/* use a table to align stuff */
		$table = new Layout_table();
		if ($allow_date_selection) {
			for ($i=1; $i<=31; $i++) {
				$days[$i] = $i;
			}

			for ($i=1; $i<=12; $i++) {
				$months[$i] = $i;
			}

			for ($i=date("Y")-10; $i<=date("Y"); $i++) {
				$years[$i] = $i;
			}
			if ($allow_user_selection) {
				$table->addTableRow();
					$table->insertTableData(gettext("user"), "", "header");
					$table->addTableData("", "data");
						$useroutput = new User_output();
						$table->addCode( $useroutput->user_selection("regitemuser_id", $calendaritem["user_id"], 0, 0, 0, 1, 1, 0, 1) );
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->insertTableData(gettext("date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("regitem[day]", $days, ($calendaritem["begin_day"])?$calendaritem["begin_day"]:date("d"));
					$table->addSelectField("regitem[month]", $months, ($calendaritem["begin_month"])?$calendaritem["begin_month"]:date("m"));
					$table->addSelectField("regitem[year]", $years, ($calendaritem["begin_year"])?$calendaritem["begin_year"]:date("Y"));
					$calendar = new Calendar_output();
					$table->addCode( $calendar->show_calendar("document.getElementById('regitemday')", "document.getElementById('regitemmonth')", "document.getElementById('regitemyear')" ));
				$table->endTableData();
			$table->endTableRow();
		}
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
				$table->addSelectField("regitem[activity_id]", $activities, $calendaritem["activity_id"]);
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
				$editor = new Layout_editor("wyzz");
				$ret = $editor->generate_editor(1);
				if ($ret !== false) {
					$table->addTextArea("regitem[description]", nl2br($calendaritem["description"]), array("style"=>"width: 600px; height: 200px;"), "contents");
					$table->addCode($ret);
				} else {
					$table->addTextArea("regitem[description]", $calendaritem["description"], array("style"=>"width: 500px; height: 200px;"));
				}
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
