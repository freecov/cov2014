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

$sql = sprintf("SELECT * FROM issues WHERE id=%d", $id);
$res = sql_query($sql);
$row = sql_fetch_assoc($res);
$row["rcpt_name"]    = $userlist[$row["user_id"]];
$row["sender_name"]  = $userlist[$row["registering_id"]];
$row["human_date"]   = date("d-m-Y", $row["timestamp"]);
$row["relname"]      = $addresses[$row["address_id"]];
$row["project_name"] = $projectdata->getProjectNameById($row["project_id"]);
$row["short_desc"]   = substr($row["description"], 0, 150);
$row["short_sol"]    = substr($row["solution"], 0, 150);
if ($row["is_solved"]) { $row["active"] = 0; } else { $row["active"] = 1; }
$supportItem = $row;
?>
