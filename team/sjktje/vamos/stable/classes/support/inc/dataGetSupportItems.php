<?php
if (!class_exists("Support_data")) {
	die("no class definition found");
}
/* init data arrays */
$userdata = new User_data();
$userlist = $userdata->getUserList();
unset($userdata);
$addressdata = new Address_data();
$addresses = $addressdata->getRelationsArray();
unset($addressdata);
$projectdata = new Project_data();

if (!isset($options["active"])) {
	$is_solved = "1 = 1";
} elseif ($options["active"]) {
	$is_solved = "is_solved != 1";
} else {
	$is_solved = "is_solved = 1";
}

$join = "LEFT JOIN address ON address.id = issues.address_id";
$join.= " LEFT JOIN users ON users.id = issues.user_id";
$join.= " LEFT JOIN project ON project.id = issues.project_id";

if ($options["address_id"]) {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved AND issues.address_id = %d ORDER BY ", $join, $options["address_id"]);
	$sql_count = sprintf("SELECT COUNT(*) FROM issues WHERE $is_solved AND address_id = %d", $options["address_id"]);
} elseif ($options["user_id"]) {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved AND issues.user_id=%d ORDER BY ", $join, $options["user_id"]);
	$sql_count = sprintf("SELECT COUNT(*) FROM issues WHERE $is_solved AND user_id=%d", $options["user_id"]);
} else {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved ORDER BY ", $join);
	$sql_count = "SELECT COUNT(*) FROM issues WHERE $is_solved";
}
if (!$options["sort"]) {
	$sql.= "is_solved,timestamp DESC";
} else {
	$sql.= sql_filter_col($options["sort"]);
}

$res_count = sql_query($sql_count);
$row_count = sql_fetch_row($res_count);

if ($options["nolimit"]) {
	$res = sql_query($sql);
} else {
	$res = sql_query($sql, "", (int)$options["top"], $GLOBALS["covide"]->pagesize);
}
$count = sql_num_rows($res);
$supportItems = array("count" => $row_count[0]);
$supportItems["items"] = array();
while ($row = sql_fetch_assoc($res)) {
	$row["rcpt_name"]    = $userlist[$row["user_id"]];
	$row["sender_name"]  = $userlist[$row["registering_id"]];
	$row["human_date"]   = date("d-m-Y", $row["timestamp"]);
	$row["relname"]      = $addresses[$row["address_id"]];
	$row["project_name"] = $projectdata->getProjectNameById($row["project_id"]);
	$row["short_desc"]   = substr($row["description"], 0, 150);
	$row["short_sol"]    = substr($row["solution"], 0, 150);
	if ($row["is_solved"]) { $row["active"] = 0; } else { $row["active"] = 1; }
	$supportItems["items"][] = $row;
}
?>
