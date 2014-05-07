<?php
/**
 * Covide Groupware-CRM Project getHoursList
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
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
/* init some data objects and arrays we gonna need */
$userdata = new User_data();
$calendardata = new Calendar_data();
$activitycosts = $calendardata->getActivityTarifs(1);
$activitypurchase = $calendardata->getActivityTarifs(2);
$activitymarge = $calendardata->getActivityTarifs(3);
$activitynames = array();
if ($settings["misc"]) {
	$costinfo = $this->getCosts();
	foreach ($costinfo as $k=>$v) {
		$activitynames[$v["id"]] = $v["cost"];
	}
} else {
	$activitynames_tmp = $calendardata->getActivityNames();
	if (!is_array($activitynames_tmp)) {
		$activitynames_tmp = array();
	}
	foreach($activitynames_tmp as $k=>$v) {
		if (!is_array($v)) {
			$activitynames[$k] = $v;
		} else {
			foreach($v as $i=>$j) {
				$activitynames[$i] = $j;
			}
		}
	}
}
unset($calendardata);

if ($settings["bulk"] || $settings["misc"]) {
	if ($settings["lfact"]) { $q  = sprintf(" AND date >= %d",$settings["lfact"]); }
	if ($settings["start"]) { $q  = sprintf(" AND date >= %d", $settings["start"]); }
	if ($settings["end"])   { $q .= sprintf(" AND date <= %d", $settings["end"]); }
} else {
	if ($settings["lfact"]) { $q  = sprintf(" AND timestamp_start > %d",$settings["lfact"]); }
	if ($settings["start"]) { $q  = sprintf(" AND timestamp_start >= %d", $settings["start"]); }
	if ($settings["end"])   { $q .= sprintf(" AND timestamp_end <= %d", $settings["end"]); }
}
if ($settings["is_billable"])   { $q .= sprintf(" AND is_billable = 1"); }
if ($settings["bulk"]) {
	$q .= " AND timestamp_start IS NULL AND hours IS NOT NULL";
} elseif ($settings["misc"]) {
	$q .= " AND price IS NOT NULL AND hours IS NULL AND timestamp_start IS NULL";
} else {
	$q .= " AND timestamp_start IS NOT NULL";
}
if ($settings["user"]) { $q .= sprintf(" AND user_id IN (%s)", $settings["user"]); }

if ($settings["projectid"]) {
	$sql = sprintf("SELECT * FROM hours_registration WHERE project_id=%d", $settings["projectid"]);
} else {
	$sql = "SELECT * FROM hours_registration WHERE 1=1";
}
	$sql .= $q;
	$sql .= " ORDER BY timestamp_start DESC";
$res = sql_query($sql);
$i = 0;
$total_secs_billable = 0;
$total_secs_service = 0;
$total_secs = 0;
$total_costs = 0;
$total_costs_service = 0;
$total_purchase = 0;
$total_marge = 0;
while ($row = sql_fetch_assoc($res)) {
	$row["user_name"]        = $userdata->getUsernameById($row["user_id"]);
	if ($settings["bulk"]) {
		$bulkhours = str_replace(array(".25",".50",".75"), array(":15",":30",":45"), $row["hours"]);
		$total_secs += ($row["hours"]*3600);
		if ($row["date"]) {
			$row["human_date"] = date("d-m-Y", $row["date"]);
		}
		if ($row["is_billable"]) {
			$row["hours_bill"] = $bulkhours;
			if ($row["tarif"]) {
				$row["costs"] = $row["tarif"];
			} else {
				$costs = $activitycosts[$row["activity_id"]]*$row["hours_bill"];
				$purchase = $activitypurchase[$row["activity_id"]]*$row["hours_bill"];
				$marge = $activitymarge[$row["activity_id"]]*$row["hours_bill"];
				$row["costs"] = number_format($costs, 2);
				$row["purchase"] = number_format($purchase, 2);
				$row["marge"] = number_format($marge, 2);
			}
			$row["hours_service"] = 0;
			$total_secs_billable += ($row["hours"]*3600);
			$total_costs += sprintf("%01.2f", $row["costs"]*100);
			$total_purchase += sprintf("%01.2f", $row["purchase"]*100);
			$total_marge += sprintf("%01.2f", $row["marge"]*100);
		} else {
			/* service hours */
			$row["hours_bill"] = 0;
			$row["hours_service"] = $bulkhours;
			$total_secs_service += ($row["hours"]*3600);
		}
	} elseif ($settings["misc"]) {
		if ($row["date"]) {
			$row["human_date"] = date("d-m-Y", $row["date"]);
		}
		if ($row["is_billable"]) {
			$costs = $row["price"];
			$row["costs"] = number_format($costs, 2);
			$total_costs += sprintf("%d", $costs*100);
			$total_purchase += sprintf("%01.2f", $row["purchase"]*100);
			$total_marge += sprintf("%d", $row["marge"]*100);
			$total_secs_billable = 0;
			$total_secs_service = 0;
		} else {
			$costs = $row["price"];
			$row["costs_service"] = number_format($costs, 2);
			$total_costs_service += sprintf("%01.2f", $costs*100);
			$total_secs_billable = 0;
			$total_secs_service = 0;
		}
	} else {
		$row["human_start_date"] = date("d-m-Y", $row["timestamp_start"]);
		$row["human_end_date"]   = date("d-m-Y", $row["timestamp_end"]);
		$row["human_start_time"] = date("H:i", $row["timestamp_start"]);
		$row["human_end_time"]   = date("H:i", $row["timestamp_end"]);
		$total_secs += ($row["timestamp_end"]-$row["timestamp_start"]);
		if ($row["is_billable"]) {
			/* billable hours */
			$row["hours_bill"] = ($row["timestamp_end"]-$row["timestamp_start"])/3600;
			if ($row["tarif"]) {
				$row["costs"] = $row["tarif"];
			} else {
				$costs = $activitycosts[$row["activity_id"]]*$row["hours_bill"];
				$purchase = $activitypurchase[$row["activity_id"]]*$row["hours_bill"];
				$marge = $activitymarge[$row["activity_id"]]*$row["hours_bill"];
				$row["costs"] = number_format($costs, 2);
				$row["purchase"] = number_format($purchase, 2);
				$row["marge"] = number_format($marge, 2);
			}
			$time = mktime(0,0,$row["hours_bill"]*3600, 1, 1, 1970);
			$row["hours_bill"] = date("H:i", $time);
			$row["hours_service"] = 0;
			$total_secs_billable += ($row["timestamp_end"]-$row["timestamp_start"]);
			$total_costs += sprintf("%01.2f", $row["costs"]*100);
			$total_purchase += sprintf("%01.2f", $row["purchase"]*100);
			$total_marge += sprintf("%01.2f", $row["marge"]*100);
		} else {
			/* service hours */
			$row["hours_bill"] = 0;
			$row["hours_service"] = ($row["timestamp_end"]-$row["timestamp_start"])/3600;
			$time = mktime(0,0,$row["hours_service"]*3600, 1, 1, 1970);
			$row["hours_service"] = date("H:i", $time);
			$total_secs_service += ($row["timestamp_end"]-$row["timestamp_start"]);
			$total_purchase += sprintf("%01.2f", $row["purchase"]*100);
			$total_marge -= sprintf("%01.2f", $row["purchase"]*100);
			$row["marge"] = sprintf("%01.2f", $row["purchase"]*-1);
			$row["costs"] = "0.00";
		}
	}
	$row["activityname"]     = $activitynames[$row["activity_id"]];
	$hoursinfo["items"][$i] = $row;
	$i++;
}
$mins = date("i", mktime(0, 0, $total_secs_billable, 1, 1, 1970));
$hoursinfo["total_hours_billable"] = sprintf("%d", $total_secs_billable/3600).":".$mins;
$hoursinfo["total_hours_raw"] = $total_secs_billable/3600;
$mins = date("i", mktime(0, 0, $total_secs_service, 1, 1, 1970));
$hoursinfo["total_hours_service"] = sprintf("%d", $total_secs_service/3600).":".$mins;
$hoursinfo["total_service_raw"] = $total_secs_service/3600;
$hoursinfo["total_costs_raw"] = ($total_costs/100);
$hoursinfo["total_costs"] = number_format(($total_costs/100),2);
$hoursinfo["total_purchase_raw"] = ($total_purchase/100);
$hoursinfo["total_purchase"] = number_format(($total_purchase/100),2);
$hoursinfo["total_marge_raw"] = ($total_marge/100);
$hoursinfo["total_marge"] = number_format(($total_marge/100),2);
$hoursinfo["total_items"] = $i;
?>
