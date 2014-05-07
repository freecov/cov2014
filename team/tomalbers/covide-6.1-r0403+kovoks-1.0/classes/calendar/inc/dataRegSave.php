<?php
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
/* gather data and create query */
$regitem = $_REQUEST["regitem"];
$regitem["subject"] = addslashes($regitem["subject"]);
$regitem["timestamp_start"] = mktime($regitem["fromhour"], $regitem["fromminute"], 0, $regitem["month"], $regitem["day"], $regitem["year"]);
$regitem["timestamp_end"] = mktime($regitem["tohour"], $regitem["tominute"], 0, $regitem["month"], $regitem["day"], $regitem["year"]);

$sql  = "INSERT INTO hours_registration (user_id, project_id, timestamp_start, timestamp_end, activity_id, description, is_billable, type) VALUES ";
$sql .= sprintf("(%d, %d, %d, %d, %d, '%s', %d, %d)",
	$_SESSION["user_id"], $regitem["project_id"], $regitem["timestamp_start"], $regitem["timestamp_end"],
	$regitem["activity_id"], addslashes($regitem["description"]), $regitem["is_billable"], $regitem["app_type"]
);

/* fire query to db */
$res = sql_query($sql);
$insertid = sql_insert_id("hours_registration");



/* put project on active if not already */
$sql = sprintf("UPDATE project SET is_active=1 WHERE id = %d", $regitem["project_id"]);
$res = sql_query($sql);

/* if item is part of repeat, dont do this but make a copy */
$calendaritem = $this->getCalendarItemById($regitem["calendar_id"]);
if ($calendaritem["is_repeat"]) {
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
$calendaritem["description"]= addslashes($regitem["description"]);
$calendaritem["subject"]    = addslashes($calendaritem["subject"]);
$calendaritem["project_id"] = $regitem["project_id"];
$calendaritem["user"]       = $calendaritem["user_id"];
$calendaritem["is_registered"] = 1;

/* fire the new data into the db */
$this->save2db($calendaritem);
?>
