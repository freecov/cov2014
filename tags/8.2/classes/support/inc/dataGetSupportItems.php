<?php
/**
 * Covide Groupware-CRM support module
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
if (!class_exists("Support_data")) {
	die("no class definition found");
}
/* init data arrays */
$userdata = new User_data();
$userlist = $userdata->getUserList();
$user_xs = $userdata->getUserPermissionsById($_SESSION["user_id"]);
unset($userdata);
$addressdata = new Address_data();
$addresses = $addressdata->getRelationsArray();
unset($addressdata);
$projectdata = new Project_data();
$like = sql_syntax("like");

if (!isset($options["active"]))
	$is_solved = "is_solved = 1";
elseif ($options["active"])
	$is_solved = "is_solved != 1";
else
	$is_solved = "is_solved = 1";

$join = "LEFT JOIN address ON address.id = issues.address_id";
$join.= " LEFT JOIN users ON users.id = issues.user_id";
$join.= " LEFT JOIN project ON project.id = issues.project_id";

$options["search"] = ($options["search"]) ? $options["search"] : $_REQUEST["search"]["keyword"];
if ($options["search"]) {
	$search = sprintf(" AND (issues.solution $like '%%%1\$s%%' OR issues.description $like '%%%1\$s%%' OR issues.reference_nr = %1\$d)", $options["search"]);
}
if ($options["date"]) {
	$date_sql = sprintf(" AND issues.timestamp BETWEEN %d AND %d", $options["date"]["start"], $options["date"]["end"]);
}

if ($options["address_id"]) {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved AND issues.address_id = %d  %s %s ORDER BY ", $join, $options["address_id"], $search, $date_sql);
	$sql_count = sprintf("SELECT COUNT(*) FROM issues WHERE $is_solved AND address_id = %d", $options["address_id"]);
} elseif ($options["user_id"]) {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved AND issues.user_id=%d  %s %s ORDER BY ", $join, $options["user_id"], $search, $date_sql);
	$sql_count = sprintf("SELECT COUNT(*) FROM issues WHERE $is_solved AND user_id=%d", $options["user_id"]);
} else {
	$sql = sprintf("SELECT issues.*, address.companyname, project.name as projectname, users.username FROM issues %s WHERE $is_solved %s %s ORDER BY ", $join, $search, $date_sql);
	$sql_count = "SELECT COUNT(*) FROM issues WHERE $is_solved";
}


if (!$options["sort"]) {
	$sql.= "is_solved,timestamp DESC";
} else {
	$sql.= sql_filter_col($options["sort"]);
	if ($options["sort2"])
		$sql.= ", ".sql_filter_col($options["sort2"]);
}

$res_count = sql_query($sql_count);
$row_count = sql_fetch_row($res_count);

if ($options["nolimit"]) {
	$res = sql_query($sql);
} else {
	$res = sql_query($sql, "", (int)$options["top"], $GLOBALS["covide"]->pagesize);
}

if ($filter["address_id"] < 0)
	unset($filter["address_id"]);
if ($filter["project_id"] < 1)
	unset($filter["project_id"]);
if ($filter["user_id"] < 1)
	unset($filter["user_id"]);

$count = sql_num_rows($res);
$supportItems = array("count" => $row_count[0]);
$supportItems["items"] = array();
while ($row = sql_fetch_assoc($res)) {

	$filter = $options["filter"];
	$row["rcpt_name"]    = $userlist[$row["user_id"]];
	$row["sender_name"]  = $userlist[$row["registering_id"]];
	$row["human_date"]   = date("d-m-Y", $row["timestamp"]);
	$row["relname"]      = $addresses[$row["address_id"]];
	$row["project_name"] = $projectdata->getProjectNameById($row["project_id"]);
	$row["short_desc"]   = substr($row["description"], 0, 150);
	$row["short_sol"]    = substr($row["solution"], 0, 150);
	if ($row["is_solved"]) { $row["active"] = 0; } else { $row["active"] = 1; }
	if ($row["user_id"] == $_SESSION["user_id"] || $user_xs["xs_issuemanage"]) {
		$row["has_xs"]       = 1;
	}

	if ((!$filter["address_id"] || $row["address_id"] == $filter["address_id"])
		&& (!$filter["project_id"] || $row["project_id"] == $filter["project_id"])
		&& (!$filter["user_id"] || $row["user_id"] == $filter["user_id"]
	)) {
		$supportItems["items"][] = $row;
	}

}
?>
