<?php
/**
 * Show start screen of import process.
 * User can choose seperator character and filename
 * The next step is the mapping screen, that will be shown
 * when the upload was succesfull
 */
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* supported seperator characters */
$seps = array(
	"comma" => ", (".gettext("komma").")",
	"semicolon" => "; (".gettext("puntkomma").")"
);
$skip_first = array(
	"0" => gettext("nee"),
	"1" => gettext("ja")
);
$output = new Layout_output();
$output->layout_page("", 1);
$venster = new Layout_venster(array(
	"title"    => gettext("adressen"),
	"subtitle" => gettext("import")
));
$venster->addVensterData();
	/* we gonna use a table to place all the elements */
	$table = new Layout_table();
	$table->addTableRow(array("cellspacing" => 1));
		$table->insertTableData(gettext("Stap 1 van 2"), "", "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("kies het scheidingsteken"));
			$table->addSpace(2);
			$table->addSelectField("import[seperator]", $seps);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("sla eerste regel over"));
			$table->addSpace(2);
			$table->addSelectField("import[skip_first]", $skip_first);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("kies het te importeren bestand"));
			$table->addTag("br");
			$table->addUploadField("import_file");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("cancel", gettext("annuleren"), "javascript: window.close()'");
			$table->insertAction("forward", gettext("verder"), "javascript: import_to_step2();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addTag("form", array(
	"id"      => "import",
	"method"  => "post",
	"action"  => "index.php",
	"enctype" => "multipart/form-data"
));
$output->addHiddenField("mod", "address");
$output->addHiddenField("action", "import_step_2");
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
$output->load_javascript(self::include_dir."import_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
