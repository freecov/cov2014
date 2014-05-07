<?php
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
$userdata = new User_data();
$user_perm = $userdata->getUserdetailsById($_SESSION["user_id"]);

$addressdata = new Address_data();

if ($master) {
	$sql = sprintf("SELECT * FROM projects_master WHERE id=%d", $projectid);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	$row["manager_name"] = $userdata->getUsernameById($row["manager"]);
	$row["executor_name"] = $userdata->getUsernameById($row["executor"]);
	$current = str_replace(",", "|", $row["users"]);
	$current = explode("|", $current);
	foreach ($current as $k=>$v) {
		if (!$v) {
			unset($current[$k]);
		}
		$row["access_name"][] = $userdata->getUsernameById($v);
	}
	$row["relname"]      = $addressdata->getAddressNameById($row["address_id"]);
	$row["master"]       = 1;
	if ($this->dataCheckPermissions($row, 0, 1))
		$row["allow_edit"] = 1;
	$row["all_address_ids"] = preg_replace("/,$/si", "", $row["address_id"].",".$row["multirel"]);
	$projectinfo[0] = $row;
} else {
	$sql = sprintf("SELECT * FROM project WHERE id=%d", $projectid);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	$row["manager_name"] = $userdata->getUsernameById($row["manager"]);
	$row["executor_name"] = $userdata->getUsernameById($row["executor"]);
	$current = str_replace(",", "|", $row["users"]);
	$current = explode("|", $current);
	foreach ($current as $k=>$v) {
		if (!$v) {
			unset($current[$k]);
		}
		$row["access_name"][] = $userdata->getUsernameById($v);
	}
	$row["relname"]      = $addressdata->getAddressNameById($row["address_id"]);
	$extra_rel = explode(",", $row["multirel"]);
	foreach ($extra_rel as $k) {
		$row["relname"] .= ", ".$addressdata->getAddressNameById($k);
	}
	$row["master"]       = 0;
	$row["is_inactive"]  = ($row["is_active"]) ? 0 : 1;
	if ($this->dataCheckPermissions($row, 0, 1))
		$row["allow_edit"] = 1;
	$row["all_address_ids"] = preg_replace("/,$/si", "", $row["address_id"].",".$row["multirel"]);
	$projectinfo[0] = $row;
}
?>
