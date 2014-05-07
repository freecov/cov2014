<?php
if (!class_exists("Classification_output")) {
	die("no class definition found");
}
/* get the data if id is given, otherwise init empty array */
if ($id) {
	$classification_data = new Classification_data();
	$classification_info = $classification_data->getClassificationById($id);
} else {
	$classification_info = array(
		"id"          => 0,
		"is_active"   => 1,
		"description" => ""
	);
}

/* start output buffer */
$output = new Layout_output();
$output->layout_page(gettext("classificatie wijzigen"), 1);
/* make nice window */
$venster = new Layout_venster(array(
	"title"    => gettext("classificaties"),
	"subtitle" => gettext("classificatie hernoemen")
));
$venster->addMenuItem(gettext("terug"), "javascript: window.close();");
$venster->generateMenuItems();
$venster->addVensterData();
	/* create form */
	$venster->addTag("form", array(
		"method" => "get",
		"id"     => "claedit",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "classification");
	$venster->addHiddenField("action", "cla_save");
	$venster->addHiddenField("cla[id]", $id);
	/* put a table here for the layout */
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("classificatie"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("cla[description]", $classification_info["description"], array("style"=>"width: 300px;"));
		$table->endTableData();
	$table->endTableRow();

	$user = new User_data();
	$user->getUserPermissionsById($_SESSION["user_id"]);
	if ($GLOBALS["covide"]->license["has_hypo"] && $user->checkPermission("xs_hypo")) {
		$table->addTableRow();
			$table->insertTableData(gettext("soort"), "", "header");
			$table->addTableData("", "data");
				$available_subtypes = array(
					0 => "--",
					1 => gettext("geldverstrekker"),
					2 => gettext("verzekeraar")
				);
				$table->addSelectField("cla[subtype]", $available_subtypes, $classification_info["subtype"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$table->addHiddenField("cla[subtype]", $classification_info["subtype"]);
	}

	$table->addTableRow();
		$table->insertTableData(gettext("actief"), "", "header");
		$table->addTableData("", "data");
			$table->insertCheckBox("cla[is_active]", "1", $classification_info["is_active"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("save", gettext("opslaan"), "javascript: cla_save();");
			if ($id) {
				$table->insertAction("delete", gettext("verwijderen"), "javascript: cla_remove($id)");
			}
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	$venster->endTag("form");
	/* end form */
$venster->endVensterData();
/* include window in output buffer */
$output->addCode($venster->generate_output());
$output->load_javascript(self::include_dir."classification_actions.js");
$output->layout_page_end();
/* flush the buffer to the browser */
$output->exit_buffer();
?>
