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
	$row["relname"]      = $addressdata->getAddressNameById($row["address_id"]);
	$row["master"]       = 1;
	if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
		$row["allow_edit"] = 1;
	$row["all_address_ids"] = preg_replace("/,$/si", "", $row["address_id"].",".$row["multirel"]);
	$projectinfo[0] = $row;
} else {
	$sql = sprintf("SELECT * FROM project WHERE id=%d", $projectid);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	$row["manager_name"] = $userdata->getUsernameById($row["manager"]);
	$row["relname"]      = $addressdata->getAddressNameById($row["address_id"]);
	$extra_rel = explode(",", $row["multirel"]);
	foreach ($extra_rel as $k) {
		$row["relname"] .= ", ".$addressdata->getAddressNameById($k);
	}
	$row["master"]       = 0;
	$row["is_inactive"]  = ($row["is_active"]) ? 0 : 1;
	if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
	$row["all_address_ids"] = preg_replace("/,$/si", "", $row["address_id"].",".$row["multirel"]);
		$row["allow_edit"] = 1;
	$projectinfo[0] = $row;
}
?>
