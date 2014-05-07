<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}

$project_data = new Project_data();

if ($_REQUEST["subaction"] == "save") {
	/* store in database */
	$project_data->saveActivity($_REQUEST["project"]);
}
if ($_REQUEST["subaction"] == "delete") {
	/* remove from database */
	$project_data->removeActivity($_REQUEST["project"]["id"]);
}
/* we gonna implement this on saturday 11 March ;) */
$activities = $project_data->getActivities();

/* make some output */
$output = new Layout_output();
$output->layout_page();
	/* show a window object */
	$venster = new Layout_venster(array("title" => gettext("manage activities")));
	$venster->addMenuItem(gettext("back"), "index.php?mod=project");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table();
		foreach ($activities as $activity) {
			$table->addTableRow();
				$table->addTableData();
					$table->addTag("form", array(
						"id"     => "activity_".$activity["id"],
						"method" => "get",
						"action" => "index.php"
					));
					$table->addHiddenField("mod", "project");
					$table->addHiddenField("action", "show_activities");
					$table->addHiddenField("subaction", "save");
					$table->addHiddenField("project[id]", $activity["id"]);
					$table->addTextField("project[activity]", $activity["activity"], array("style" => "width: 300px;"));
				$table->endTableData();
				$table->addTableData();
					$table->addTextField("project[tarif]", $activity["tarif"], array("style" => "width: 60px;"));
				$table->endTableData();
				$table->addTableData();
					$table->insertAction("save", gettext("save"), "javascript: save_activity(".$activity["id"].");");
					$table->insertAction("delete", gettext("delete"), "javascript: remove_activity(".$activity["id"].");");
					$table->endTag("form");
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	/* window widget with form to add new activity */
	$venster = new Layout_venster(array(
		"title" => gettext("add activities")
	));
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addTag("form", array(
					"id"     => "activity_0",
					"method" => "get",
					"action" => "index.php"
				));
				$table->addHiddenField("mod", "project");
				$table->addHiddenField("action", "show_activities");
				$table->addHiddenField("subaction", "save");
				$table->addHiddenField("project[id]", 0);
				$table->addTextField("project[activity]", "", array("style" => "width: 300px;"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("project[tarif]", "0.00", array("style" => "width: 60px;"));
			$table->endTableData();
			$table->addTableData();
				$table->insertAction("save", gettext("save"), "javascript: save_activity(0);");
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
	$output->load_javascript(self::include_dir."activities.js");
$output->layout_page_end();
$output->exit_buffer();
?>
