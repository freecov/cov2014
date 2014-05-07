<?php
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
/* init some data objects and arrays we gonna need */
$userdata = new User_data();
$calendardata = new Calendar_data();
$activitycosts = $calendardata->getActivityTarifs();
$activitynames = $calendardata->getActivityNames();
unset($calendardata);

if ($settings["projectid"]) {
	if ($settings["lfact"]) { $q  = sprintf(" AND timestamp_start > %d",$settings["lfact"]); }
	if ($settings["start"]) { $q  = sprintf(" AND timestamp_start >= %d", $settings["start"]); }
	if ($settings["end"])   { $q .= sprintf(" AND timestamp_end <= %d", $settings["end"]); }
	if ($settings["is_billable"])   { $q .= sprintf(" AND is_billable = 1"); }
	$sql = sprintf("SELECT * FROM hours_registration WHERE project_id=%d", $settings["projectid"]);
	$sql .= $q;
	$sql .= " ORDER BY timestamp_start DESC";
}
$res = sql_query($sql);
$i = 0;
$total_secs_billable = 0;
$total_secs_service = 0;
$total_secs = 0;
$total_costs = 0;
while ($row = sql_fetch_assoc($res)) {
	$row["user_name"]        = $userdata->getUsernameById($row["user_id"]);
	$row["human_start_date"] = date("d-m-Y", $row["timestamp_start"]);
	$row["human_end_date"]   = date("d-m-Y", $row["timestamp_end"]);
	$row["human_start_time"] = date("H:i", $row["timestamp_start"]);
	$row["human_end_time"]   = date("H:i", $row["timestamp_end"]);
	$row["activityname"]     = $activitynames[$row["activity_id"]];
	$total_secs += ($row["timestamp_end"]-$row["timestamp_start"]);
	if ($row["is_billable"]) {
		/* billable hours */
		$row["hours_bill"] = ($row["timestamp_end"]-$row["timestamp_start"])/3600;
		$costs = $activitycosts[$row["activity_id"]]*$row["hours_bill"];
		$row["costs"] = number_format($costs, 2);
		$time = mktime(0,0,$row["hours_bill"]*3600, 1, 1, 1970);
		$row["hours_bill"] = date("H:i", $time);
		$row["hours_service"] = 0;
		$total_secs_billable += ($row["timestamp_end"]-$row["timestamp_start"]);
		$total_costs += sprintf("%d", $costs*100);
	} else {
		/* service hours */
		$row["hours_bill"] = 0;
		$row["hours_service"] = ($row["timestamp_end"]-$row["timestamp_start"])/3600;
		$time = mktime(0,0,$row["hours_service"]*3600, 1, 1, 1970);
		$row["hours_service"] = date("H:i", $time);
		$total_secs_service += ($row["timestamp_end"]-$row["timestamp_start"]);
	}
	$hoursinfo["items"][$i] = $row;
	$i++;
}
$mins = date("i", mktime(0, 0, $total_secs_billable, 1, 1, 1970));
$hoursinfo["total_hours_billable"] = sprintf("%d", $total_secs_billable/3600).":".$mins;
$hoursinfo["total_hours_raw"] = $total_secs_billable/3600;
$mins = date("i", mktime(0, 0, $total_secs_service, 1, 1, 1970));
$hoursinfo["total_hours_service"] = sprintf("%d", $total_secs_service/3600).":".$mins;
$hoursinfo["total_costs_raw"] = ($total_costs/100);
$hoursinfo["total_costs"] = number_format(($total_costs/100),2);
$hoursinfo["total_items"] = $i;
?>
