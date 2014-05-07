<?php
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
$sql = "SELECT id,name FROM projects_master ORDER BY UPPER(name)";
$projectlist[0] = gettext("geen");
$res = sql_query($sql);
while ($row = sql_fetch_assoc($res)) {
	$projectlist[$row["id"]] = $row["name"];
}
?>
