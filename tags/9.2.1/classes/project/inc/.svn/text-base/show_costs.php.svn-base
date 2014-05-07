<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}

$project_data = new Project_data();

if ($_REQUEST["subaction"] == "save") {
	/* store in database */
	$project_data->saveCost($_REQUEST["project"]);
}
if ($_REQUEST["subaction"] == "delete") {
	/* remove from database */
	$project_data->removeCost($_REQUEST["project"]["id"]);
}
/* we gonna implement this on saturday 11 March ;) */
$costs = $project_data->getCosts();

if ($GLOBALS["covide"]->license["has_project_ext"]) {
	$projext_data = new Projectext_data();
	$departments = $projext_data->extGetDepartments();
	$deb_sel = array(0 => "---");
	foreach ($departments as $k=>$v) {
		$deb_sel[$k] = $v["department"];
	}
}
/* make some output */
$output = new Layout_output();
$output->layout_page();
	/* show a window object */
	$venster = new Layout_venster(array("title" => gettext("manage project costs")));
	$venster->addMenuItem(gettext("back"), "index.php?mod=project");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table(array("width" => "100%"));
		$table->addTableRow();
			$table->insertTableData(gettext("name"), "", "header");
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$table->insertTableData(gettext("department"), "", "header");
			}
			$table->insertTableData(gettext("tarif"), "", "header");
			/*
			$table->insertTableData(gettext("purchase"), "", "header");
			$table->insertTableData(gettext("marge"), "", "header");
			 */
			$table->insertTableData("", "", "header");
		$table->endTableRow();
		foreach ($costs as $cost) {
			$table->addTableRow();
				$table->addTableData();
					$table->addTag("form", array(
						"id"     => "cost_".$cost["id"],
						"method" => "get",
						"action" => "index.php"
					));
					$table->addHiddenField("mod", "project");
					$table->addHiddenField("action", "show_costs");
					$table->addHiddenField("subaction", "save");
					$table->addHiddenField("project[id]", $cost["id"]);
					$table->addTextField("project[cost]", $cost["cost"], array("style" => "width: 300px;"));
				$table->endTableData();
				if ($GLOBALS["covide"]->license["has_project_ext"]) {
					$table->addTableData();
						$table->addSelectField("project[department_id]", $deb_sel, $cost["department_id"]);
					$table->endTableData();
				}
				$table->addTableData();
					$table->addTextField("project[tarif]", $cost["tarif"], array("style" => "width: 60px;"));
				$table->endTableData();
				/*
				$table->addTableData();
					$table->addTextField("project[purchase]", $cost["purchase"], array("style" => "width: 60px;"));
				$table->endTableData();
				$table->addTableData();
					$table->addTextField("project[marge]", $cost["marge"], array("style" => "width: 60px;"));
				$table->endTableData();
				 */
				$table->addTableData();
					$table->insertAction("save", gettext("save"), "javascript: save_cost(".$cost["id"].");");
					$table->insertAction("delete", gettext("delete"), "javascript: remove_cost(".$cost["id"].");");
					$table->endTag("form");
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("new cost"), array("colspan" => 6), "header");
		$table->endTableRow();
			$table->addTableData();
				$table->addTag("form", array(
					"id"     => "cost_0",
					"method" => "get",
					"action" => "index.php"
				));
				$table->addHiddenField("mod", "project");
				$table->addHiddenField("action", "show_costs");
				$table->addHiddenField("subaction", "save");
				$table->addHiddenField("project[id]", 0);
				$table->addTextField("project[cost]", "", array("style" => "width: 300px;"));
			$table->endTableData();
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$table->addTableData();
					$table->addSelectField("project[department_id]", $deb_sel, $cost["department_id"]);
				$table->endTableData();
			}
			$table->addTableData();
				$table->addTextField("project[tarif]", "0.00", array("style" => "width: 60px;"));
			$table->endTableData();
			/*
			$table->addTableData();
				$table->addTextField("project[purchase]", "0.00", array("style" => "width: 60px;"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("project[marge]", "0.00", array("style" => "width: 60px;"));
			$table->endTableData();
			 */
			$table->addTableData();
				$table->insertAction("save", gettext("save"), "javascript: save_cost(0);");
				$table->endTag("form");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	/* end of window for adding new costs */
	$output->load_javascript(self::include_dir."costs.js");
$output->layout_page_end();
$output->exit_buffer();
?>
