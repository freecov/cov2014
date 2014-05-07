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
	$sql .= sprintf("is_active=%d,", $projectinfo["is_active"]);
	$sql .= sprintf("users='%s'", $projectinfo["users"]);
	/* we cast them to ints with sprintf, so we dont need to check for their existance anymore */
	if (!$projectinfo["master"]) {
		$sql .= sprintf(", group_id=%d", $projectinfo["group_id"]);
		$sql .= sprintf(", executor=%d", $projectinfo["executor"]);
		$sql .= sprintf(", budget=%d", $projectinfo["budget"]);
		$sql .= sprintf(", hours=%d", $projectinfo["hours"]);
		$sql .= sprintf(", address_businesscard_id=%d", $projectinfo["bcard"]);
		$sql .= sprintf(", multirel='%s'", $projectinfo["multirel"]);
	} else {
		$sql .= sprintf(", ext_department=%d", $projectinfo["ext_department"]);
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
	$fields[] = "users";       $values[] = sprintf("'%s'", $projectinfo["users"]);
	if (!$projectinfo["master"]) {
		$fields[] = "group_id";                $values[] = sprintf("%d",   $projectinfo["group_id"]);
		$fields[] = "budget";                  $values[] = sprintf("%d",   $projectinfo["budget"]);
		$fields[] = "hours";                   $values[] = sprintf("%d",   $projectinfo["hours"]);
		$fields[] = "address_businesscard_id"; $values[] = sprintf("%d",   $projectinfo["bcard"]);
		$fields[] = "executor";                $values[] = sprintf("%d",   $projectinfo["executor"]);
		$fields[] = "multirel";                $values[] = sprintf("'%s'", $projectinfo["multirel"]);
	} else {
		$fields[] = "ext_department";          $values[] = sprintf("%d",   $projectinfo["ext_department"]);
	}
	$sql = "INSERT INTO $table (".implode(",", $fields).") VALUES (".implode(",", $values).");";
}

if ($debug) {
	echo $sql;
} else {
	/* if normal project and id is set, also update the folder name (fixes bug #1536452) */
	if (!$projectinfo["master"] && $projectinfo["id"]) {
		$filesys_data = new Filesys_data();
		$filesys_folder = $filesys_data->getProjectFolder($projectinfo["id"]);
		$folderupdate = sprintf("UPDATE filesys_folders SET name='%s' WHERE id=%d", $projectinfo["name"], $filesys_folder);
	}
	$res = sql_query($sql);
	if ($GLOBALS["covide"]->license["has_project_declaration"]) {
		if (!$projectinfo["id"]) {
			$dbid = sql_insert_id($table);
			echo $dbid."//";
		}	else
			$dbid = $projectinfo["id"];
	}
	if ($folderupdate)
		$r = sql_query($folderupdate);


	if ($GLOBALS["covide"]->license["has_project_declaration"]) {
		$declaration_data = new ProjectDeclaration_Data();
		$declaration_data->saveProjectFields($dbid, $_REQUEST);
	}

	/* small piece of output to close window */
	$output = new Layout_output();
	$output->layout_page("", 1);
		$output->start_javascript();
			$output->addCode("
				if (opener.document.getElementById('projectsearch')) {
					opener.document.getElementById('projectsearch').submit();
				} else if (opener.document.getElementById('hiddeninfo')) {
					opener.document.getElementById('hiddeninfo').submit();
				} else if (opener.document) {
					opener.document.location.href = opener.document.location.href;
				}
			");
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				//sync filesys struct
				$projectext = new ProjectExt_data();
				$projectext->checkCompleteFolderStruct();
			}
			if ($GLOBALS["covide"]->license["has_project_ext"] && !$projectinfo["master"]) {
				if (!$projectinfo["id"]) {
					$projectinfo["id"] = sql_insert_id("project");
				}
				$projectext = new ProjectExt_data();
				$projectext->extSaveMetaFieldValues($_REQUEST["meta"], $projectinfo["id"], $_REQUEST["ext"]["activity"], $_REQUEST["dmeta"], $_REQUEST["ext"]["project_year"]);
				$output->addCode("
					//opener.document.location.href = opener.document.location.href;
					location.href='index.php?mod=project&action=edit&id=".$projectinfo["id"]."';
				");
			} else {
				$output->addCode("
					//opener.document.location.href = opener.document.location.href;
					window.close();
				");
			}
		$output->end_javascript();
	$output->layout_page_end();
	$output->exit_buffer();
}
?>
