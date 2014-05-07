<?php
if (!class_exists("Project_data")) {
	exit("no class definition found");
}
$debug = 0;
$projectinfo = $_REQUEST["project"];
/* find out what table we are going to alter */
if ($projectinfo["master"]) {
	$table = "projects_master";
} else {
	$table = "project";
	$addresses = explode(",", $projectinfo["address_id"]);
	/* strip empty items */
	$addresses = array_unique($addresses);
	foreach ($addresses as $k=>$v) {
		if (!$v) {
			unset($addresses[$k]);
		}
	}
	sort($addresses);
	/* put first id we find in address_id. If more are found set multirel to the remaining values */
	$projectinfo["address_id"] = $addresses[0];
	if (count($addresses) > 1) {
		unset($addresses[0]);
		$projectinfo["multirel"] = implode(",", $addresses);
	}
}
/* -- */
if ($projectinfo["id"]) {
	/* update project */
	$sql = "UPDATE $table SET ";
	$sql .= sprintf("name='%s', ", $projectinfo["name"]);
	$sql .= sprintf("description='%s', ", $projectinfo["description"]);
	$sql .= sprintf("manager=%d, ", $projectinfo["manager"]);
	$sql .= sprintf("address_id=%d, ", $projectinfo["address_id"]);
	$sql .= sprintf("is_active=%d", $projectinfo["is_active"]);
	/* we cast them to ints with sprintf, so we dont need to check for their existance anymore */
	if (!$projectinfo["master"]) {
		$sql .= sprintf(", group_id=%d", $projectinfo["group_id"]);
		$sql .= sprintf(", budget=%d", $projectinfo["budget"]);
		$sql .= sprintf(", hours=%d", $projectinfo["hours"]);
		$sql .= sprintf(", address_businesscard_id=%d", $projectinfo["bcard"]);
		$sql .= sprintf(", multirel='%s'", $projectinfo["multirel"]);
	}
	$sql .= sprintf(" WHERE id=%d", $projectinfo["id"]);
} else {
	/* insert new project */
	$fields = array();         $values = array();
	$fields[] = "name";        $values[] = sprintf("'%s'", $projectinfo["name"]);
	$fields[] = "description"; $values[] = sprintf("'%s'", $projectinfo["description"]);
	$fields[] = "manager";     $values[] = sprintf("%d",   $projectinfo["manager"]);
	$fields[] = "address_id";  $values[] = sprintf("%d",   $projectinfo["address_id"]);
	$fields[] = "is_active";   $values[] = sprintf("%d",   $projectinfo["is_active"]);
	if (!$projectinfo["master"]) {
		$fields[] = "group_id";    $values[] = sprintf("%d",   $projectinfo["group_id"]);
		$fields[] = "budget";      $values[] = sprintf("%d",   $projectinfo["budget"]);
		$fields[] = "hours";       $values[] = sprintf("%d",   $projectinfo["hours"]);
		$fields[] = "multirel";    $values[] = sprintf("'%s'", $projectinfo["multirel"]);
	}
	$sql = "INSERT INTO $table (".implode(",", $fields).") VALUES (".implode(",", $values).");";
}

if ($debug) {
	echo $sql;
} else {
	$res = sql_query($sql);

	/* small piece of output to close window */
	$output = new Layout_output();
	$output->layout_page("", 1);
		$output->start_javascript();
			if ($GLOBALS["covide"]->license["has_project_ext"] && $table == "project") {
				if (!$projectinfo["id"]) {
					$projectinfo["id"] = sql_insert_id("project");
				}
				$projectext = new ProjectExt_data();
				$projectext->extSaveMetaFieldValues($_REQUEST["meta"], $projectinfo["id"], $_REQUEST["ext"]["activity"], $_REQUEST["dmeta"]);
				$output->addCode("
					opener.document.location.href = opener.document.location.href;
					location.href='index.php?mod=project&action=edit&id=".$projectinfo["id"]."';
				");
			} else {
				$output->addCode("
					opener.document.location.href = opener.document.location.href;
					window.close();
				");
			}
		$output->end_javascript();
	$output->layout_page_end();
	$output->exit_buffer();
}
?>
