<?php
if (!class_exists("Support_data")) {
	die("no class definition found");
}
/*
External support issues have types
1 = Contact request
2 = Question
3 = support request

Both type 1 and 2 can be forwarded inside Covide as note
Type 3 will be saved as support issue
*/
$types = array(
	1 => gettext("contact"),
	2 => gettext("vraag"),
	3 => gettext("klacht")
);
$sql = "SELECT * FROM support ORDER BY timestamp, type DESC";
$res = sql_query($sql);
$issues = array();
while ($row = sql_fetch_assoc($res)) {
	$row["human_type"] = $types[$row["type"]];
	$row["human_date"] = date("d-m-Y H:i", $row["timestamp"]);
	$issues[] = $row;
}
?>
