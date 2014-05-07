<?php
if (!class_exists("Support_data")) {
	die("no class definition found");
}
$issueinfo = $_REQUEST["issue"];
/* make array which can be imploded in sql queries */
$fields = array();
$values = array();
$fields[] = "reference_nr";   $values[] = sprintf("%d",   $issueinfo["reference_nr"]);
$fields[] = "email";          $values[] = sprintf("'%s'", $issueinfo["email"]);
$fields[] = "registering_id"; $values[] = sprintf("%d",   $issueinfo["registering_id"]);
$fields[] = "timestamp";      $values[] = sprintf("%d",   mktime(0, 0, 0, $issueinfo["month"], $issueinfo["day"], $issueinfo["year"]));
$fields[] = "description";    $values[] = sprintf("'%s'", $issueinfo["description"]);
$fields[] = "solution";       $values[] = sprintf("'%s'", $issueinfo["solution"]);
$fields[] = "project_id";     $values[] = sprintf("%d",   $issueinfo["project_id"]);
$fields[] = "user_id";        $values[] = sprintf("%d",   $issueinfo["user_id"]);
$fields[] = "priority";       $values[] = sprintf("%d",   $issueinfo["priority"]);
$fields[] = "is_solved";      $values[] = sprintf("%d",   $issueinfo["is_solved"]);
$fields[] = "address_id";     $values[] = sprintf("%d",   $issueinfo["address_id"]);

if ($issueinfo["id"]) {
	/* we are updating an item */
	$sql = "UPDATE issues SET ";
	foreach ($fields as $k=>$v) {
		$sql .= $v."=".$values[$k].", ";
	}
	$sql  = substr($sql, 0, strlen($sql)-2);
	$sql .= sprintf(" WHERE id=%d", $issueinfo["id"]);
} else {
	$sql = "INSERT INTO issues (".implode(",", $fields).") VALUES (".implode(",", $values).")";
}
sql_query($sql);
$output = new Layout_output();
$output->start_javascript();
	$output->addCode(
		"
		opener.document.location.href = opener.document.location.href;
		window.close();
		"
	);
$output->end_javascript();
$output->exit_buffer();
?>
