<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}

$project_data = new Project_data();

if ($_REQUEST["subaction"] == "save") {
	/* store in database */
	$project_data->saveActivityGroup($_REQUEST["project"]);
}
if ($_REQUEST["subaction"] == "delete") {
	/* remove from database */
	$project_data->removeActivityGroup($_REQUEST["project"]["id"]);
}

/* create array we can use */
$activitygroups_tmp = $project_data->getActivityGroups();
unset($activitygroups_tmp[0]);
$activitygroups = array();
foreach ($activitygroups_tmp as $key => $value) {
	$activitygroups[$key] = array("id" => $key, "name" => $value);
}
unset($activitygroups_tmp);

/* make some output */
$output = new Layout_output();
$output->layout_page(gettext("Projects")." - ".gettext("manage activitygroups"));
	/* show a window object */
	$venster = new Layout_venster(array("title" => gettext("manage activitygroups")));
	$venster->addMenuItem(gettext("back"), "index.php?mod=project");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->insertTableData(gettext("name"), "", "header");
			$table->insertTableData("", "", "header");
		$table->endTableRow();
		foreach ($activitygroups as $activitygroup) {
			$table->addTableRow();
				$table->addTableData();
					$table->addTag("form", array(
						"id"     => "activitygroup_".$activitygroup["id"],
						"method" => "get",
						"action" => "index.php"
					));
					$table->addHiddenField("mod", "project");
					$table->addHiddenField("action", "show_activitygroups");
					$table->addHiddenField("subaction", "save");
					$table->addHiddenField("project[id]", $activitygroup["id"]);
					$table->addTextField("project[name]", $activitygroup["name"], array("style" => "width: 300px;"));
				$table->endTableData();
				$table->addTableData(array("align" => "right"));
					$table->insertAction("save", gettext("save"), "javascript: save_activitygroup(".$activitygroup["id"].");");
					$table->insertAction("delete", gettext("delete"), "javascript: remove_activitygroup(".$activitygroup["id"].");");
					$table->endTag("form");
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("new activitygroup"), array("colspan" => 2), "header");
		$table->endTableRow();
			$table->addTableData();
				$table->addTag("form", array(
					"id"     => "activitygroup_0",
					"method" => "get",
					"action" => "index.php"
				));
				$table->addHiddenField("mod", "project");
				$table->addHiddenField("action", "show_activitygroups");
				$table->addHiddenField("subaction", "save");
				$table->addHiddenField("project[id]", 0);
				$table->addTextField("project[name]", "", array("style" => "width: 300px;"));
			$table->endTableData();
			$table->addTableData(array("align" => "right"));
				$table->insertAction("save", gettext("save"), "javascript: save_activitygroup(0);");
				$table->endTag("form");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	/* end of window for adding new activities */
	$output->load_javascript(self::include_dir."activitygroups.js");
$output->layout_page_end();
$output->exit_buffer();
?>
