<?php
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
$userdata = new User_data();
$addressdata = new Address_data();
$calendardata = new Calendar_data();
$activities = $calendardata->getActivityNames();
$actcosts   = $calendardata->getActivityTarifs();
$sql = sprintf("SELECT * FROM project WHERE group_id=%d ORDER BY UPPER(name)", $projectid);
$res = sql_query($sql);
while ($row = sql_fetch_assoc($res)) {
	$row["manager_name"] = $userdata->getUsernameById($row["manager"]);
	$row["relname"]      = $addressdata->getAddressNameById($row["address_id"]);
	$row["master"]       = 0;
	/* gather hour regs */
	if (!$row["lfact"]) {
		$row["lfact"] = 0;
	}
	/* permissions */
	if ($row["manager"] == $_SESSION["user_id"] || $userperms["xs_projectmanage"]) {
		$row["allow_xs"] = 1;
	} else {
		$row2 = $this->getProjectById($row["master"]);
		if ($row2["manager"] == $_SESSION["user_id"]) {
			$row["allow_xs"] = 1;
		}
	}

	$sql_reg = sprintf("SELECT * FROM hours_registration WHERE project_id=%d AND timestamp_start > %d", $row["id"], $row["lfact"]);
	$row["service_hours"]  = 0;
	$row["billable_hours"] = 0;
	$row["total_costs"]    = 0;
	$res_reg = sql_query($sql_reg);
	while ($row_reg = sql_fetch_assoc($res_reg)) {
		if ($row_reg["is_billable"] == 1) {
			$row["billable_hours"] += ($row_reg["timestamp_end"]-$row_reg["timestamp_start"]);
			$row["total_costs"]    += ((($row_reg["timestamp_end"]-$row_reg["timestamp_start"])/3600)*$actcosts[$row_reg["activity_id"]]);
		} else {
			$row["service_hours"] += ($row_reg["timestamp_end"]-$row_reg["timestamp_start"]);
		}
	}
	$row["service_hours"]  = round($row["service_hours"]/3600).":".date("i", $row["service_hours"]);
	$row["billable_hours"] = round($row["billable_hours"]/3600).":".date("i", $row["billable_hours"]);
	$row["total_costs"]    = number_format($row["total_costs"], 2);
	$row["is_nonactive"] = 1;
	if ($row["is_active"]) {
		$row["is_nonactive"] = 0;
	}
	if ($row["allow_xs"]) {
		$projectinfo[] = $row;
	}
	unset($row);
}
?>
