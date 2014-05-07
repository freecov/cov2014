<?php
/**
 * Covide Groupware-CRM Calendar module - hour registration save
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
/* gather data and create query */
$regitem = $_REQUEST["regitem"];
$regitem["subject"] = $regitem["subject"];
$costs = $this->getActivityTarifs(-1);
// if we have HRM, check if gross wage and overhead are specified
$purchase = 0;
$overhead = 1;
if ($GLOBALS["covide"]->license["has_hrm"]) {
	$address_data = new Address_data();
	$hrminfo = $address_data->getHRMinfo($regitem["user_id"]);
	if ($hrminfo[0]["gross_wage"]) {
		$purchase = $hrminfo[0]["gross_wage"];
	}
	if ($hrminfo[0]["overhead"]) {
		$overhead = ($hrminfo[0]["overhead"]/100)+1;
	}
}

if ((array_key_exists("timestamp_start", $regitem) && array_key_exists("timestamp_end", $regitem)) || array_key_exists("fromhour", $regitem)) {
	$regitem["timestamp_start"] = mktime($regitem["fromhour"], $regitem["fromminute"], 0, $regitem["month"], $regitem["day"], $regitem["year"]);
	$regitem["timestamp_end"] = mktime($regitem["tohour"], $regitem["tominute"], 0, $regitem["month"], $regitem["day"], $regitem["year"]);
} elseif ($regitem["misc_input"]) {
	$misc_input = 1;
} else {
	$batch_input = 1;
}

if ($batch_input) {
	if ($regitem["day"] != "---" && $regitem["month"] != "---" && $regitem["year"] != "---") {
		$regitem["date"] = mktime(0, 0, 0, $regitem["month"], $regitem["day"], $regitem["year"]);
	}
	$hours = (int)$regitem["hours"];
	$regitem["purchase"] = ($purchase*$overhead)*$hours;
	$regitem["tarif"] = $costs[$regitem["activity_id"]]["tarif"]*$hours;
	$regitem["marge"] = $regitem["tarif"]-$regitem["purchase"];
	if ($regitem["id"]) {
		$sql = sprintf("UPDATE hours_registration SET user_id = %d, project_id = %d, activity_id = %d, description = '%s', is_billable = %d, hours = %d, purchase=%01.2f, marge = %01.2f, tarif = %01.2f, date = %d WHERE id = %d",
			$regitem["user_id"], $regitem["project_id"], $regitem["activity_id"], $regitem["description"], $regitem["is_billable"], $regitem["hours"],
			$regitem["purchase"], $regitem["marge"], $regitem["tarif"], $regitem["date"], $regitem["id"]);
		$res = sql_query($sql);
		$insertid = $regitem["id"];
	} else {
		$sql  = "INSERT INTO hours_registration (user_id, project_id, activity_id, description, is_billable, hours, purchase, marge, tarif, date) VALUES ";
		$sql .= sprintf("(%d, %d, %d, '%s', %d, %d, %01.2f, %01.2f, %01.2f, %d)", $regitem["user_id"], $regitem["project_id"], $regitem["activity_id"], $regitem["description"], $regitem["is_billable"], $regitem["hours"],
			$regitem["purchase"], $regitem["marge"], $regitem["tarif"], $regitem["date"]);
		$res = sql_query($sql);
		$insertid = sql_insert_id("hours_registration");
	}
} elseif ($misc_input) {
	// handle input like 300,50
	if (substr($regitem["price"], -3, 1) == ",") {
		$regitem["price"] = preg_replace("/,(\d\d)$/", ".$1", $regitem["price"]);
	}
	if (substr($regitem["purchase"], -3, 1) == ",") {
		$regitem["purchase"] = preg_replace("/,(\d\d)$/", ".$1", $regitem["purchase"]);
	}
	$regitem["marge"] = $regitem["price"]-$regitem["purchase"];
	if ($regitem["id"]) {
		$sql = sprintf("UPDATE hours_registration SET activity_id = %d, user_id = %d, project_id = %d, price = %01.2f, purchase = %01.2f, marge = %01.2f, description = '%s', is_billable = %d WHERE id = %d",
			$regitem["costid"], $regitem["user_id"], $regitem["project_id"], $regitem["price"], $regitem["purchase"], $regitem["marge"], $regitem["description"], $regitem["is_billable"], $regitem["id"]);
		$res = sql_query($sql);
		$insertid = $regitem["id"];
	} else {
		$sql  = "INSERT INTO hours_registration (activity_id, user_id, project_id, price, purchase, marge, description, is_billable) VALUES ";
		$sql .= sprintf("(%d, %d, %d, %01.2f, %01.2f, %01.2f, '%s', %d)", $regitem["costid"], $regitem["user_id"], $regitem["project_id"], $regitem["price"], $regitem["purchase"], $regitem["marge"], $regitem["description"], $regitem["is_billable"]);
		$res = sql_query($sql);
		$insertid = sql_insert_id("hours_registration");
	}
} else {
	$hours = ($regitem["timestamp_end"]-$regitem["timestamp_start"])/3600;
	$regitem["purchase"] = ($purchase*$overhead)*$hours;
	$regitem["tarif"] = $costs[$regitem["activity_id"]]["tarif"]*$hours;
	$regitem["marge"] = $regitem["tarif"]-$regitem["purchase"];
	if ($regitem["id_reg"]) {
		$sql = sprintf("UPDATE hours_registration SET user_id=%d, project_id=%d, timestamp_start=%d, timestamp_end=%d, activity_id=%d, description='%s', is_billable=%d, type=%d, overtime=%d, purchase=%01.2f, marge=%01.2f, tarif=%01.2f WHERE id=%d;",
			$regitem["user_id"], $regitem["project_id"], $regitem["timestamp_start"], $regitem["timestamp_end"],
			$regitem["activity_id"], sql_escape_string($regitem["description"]), $regitem["is_billable"], $regitem["app_type"], $regitem["overtime"],
			$regitem["purchase"], $regitem["marge"], $regitem["tarif"], $regitem["id_reg"]
		);
		/* fire query to db */
		$res = sql_query($sql);
		$insertid = $regitem["id_reg"];
	} else {
		$sql  = "INSERT INTO hours_registration (user_id, project_id, timestamp_start, timestamp_end, activity_id, description, is_billable, type, overtime, purchase, marge, tarif) VALUES ";
		$sql .= sprintf("(%d, %d, %d, %d, %d, '%s', %d, %d, %d, %01.2f, %01.2f, %01.2f)",
			$regitem["user_id"], $regitem["project_id"], $regitem["timestamp_start"], $regitem["timestamp_end"],
			$regitem["activity_id"], sql_escape_string($regitem["description"]), $regitem["is_billable"], $regitem["app_type"], $regitem["overtime"],
			$regitem["purchase"], $regitem["marge"], $regitem["tarif"]
		);
		/* fire query to db */
		$res = sql_query($sql);
		$insertid = sql_insert_id("hours_registration");
	}
}

/* put project on active if not already */
$sql = sprintf("UPDATE project SET is_active=1 WHERE id = %d", $regitem["project_id"]);
$res = sql_query($sql);
if ($regitem["calendar_id"] && $regitem["update_calendar"] == 1) {
	$calendaritem = $this->getCalendarItemById($regitem["calendar_id"], $_SESSION["user_id"]);
	//put item in registered state
	if (!$calendaritem["is_repeat"] || !$calendaritem["repeat_type"]) {
		$sql = sprintf("UPDATE calendar_user SET status = 4 WHERE calendar_id = %d AND user_id = %d", $regitem["calendar_id"], $_SESSION["user_id"]);
		$res = sql_query($sql);
	}

	$users = $calendaritem["user_id"].",".$calendaritem["extra_users"];
	$users = explode(",", $users);
	$users = array_unique($users);

	/* if item is part of repeat, dont do this but make a copy */
	if ($calendaritem["is_repeat"] || $calendaritem["repeat_type"]) {
		unset($calendaritem["id"]);
		unset($calendaritem["is_repeat"]);
		unset($calendaritem["repeat_type"]);
	}

	/* unset some database retreived fields */
	unset($calendaritem["begin_day"]);
	unset($calendaritem["begin_month"]);
	unset($calendaritem["begin_year"]);
	unset($calendaritem["begin_hour"]);
	unset($calendaritem["begin_min"]);
	unset($calendaritem["end_hour"]);
	unset($calendaritem["end_min"]);
	unset($calendaritem["timestamp_start"]);
	unset($calendaritem["timestamp_end"]);

	/* set to values user supplied in reg_hour_input form */
	$calendaritem["from_day"]   = array($regitem["day"]);
	$calendaritem["from_month"] = $regitem["month"];
	$calendaritem["from_year"]  = $regitem["year"];
	$calendaritem["from_hour"]  = $regitem["fromhour"];
	$calendaritem["from_min"]   = $regitem["fromminute"];
	$calendaritem["to_hour"]    = $regitem["tohour"];
	$calendaritem["to_minute"]  = $regitem["tominute"];
	$calendaritem["description"]= $regitem["description"];
	$calendaritem["subject"]    = addslashes($calendaritem["subject"]);
	$calendaritem["location"]   = addslashes($calendaritem["location"]);
	$calendaritem["project_id"] = $regitem["project_id"];
	$calendaritem["user"]       = $calendaritem["user_id"];
	$calendaritem["app_type"]   = $regitem["app_type"];
	$calendaritem["is_registered"] = 1;
	$calendaritem["user_id"] = $users;

	/* fire the new data into the db */
	$this->save2db($calendaritem);
} else {
	if ($regitem["todo_id"]) {
		$todo_data = new Todo_data();
		$todo_data->delete_todo($regitem["todo_id"], 1);
	}
	// small output to refresh opener and close window
	$output = new Layout_output();
	$output->start_javascript();
	/*
	$output->addCode("
	parent.location.href=parent.location.href;
	closepopup();
	");
	 */
	$output->addCode(sprintf("document.location.href='index.php?mod=calendar&action=reg_input&id=0&timestamp=%d&project_id=%d&address_id=%d';", mktime(), $regitem["project_id"], $regitem["user_id"]));
	$output->end_javascript();
	$output->exit_buffer();
}
?>
