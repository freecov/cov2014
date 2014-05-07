<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

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
	"comma" => ", (".gettext("comma").")",
	"semicolon" => "; (".gettext("semicolon").")"
);
$skip_first = array(
	"0" => gettext("no"),
	"1" => gettext("skip first row"),
	"2" => gettext("skip first two rows"),
	"3" => gettext("skip first three rows")
);
$target = array(
	"relation" => gettext("relations "),
	"bcard" => gettext("business cards")
);
$output = new Layout_output();
$output->layout_page("", 1);
$venster = new Layout_venster(array(
	"title"    => gettext("addresses"),
	"subtitle" => gettext("import")
));
$venster->addVensterData();
	/* we gonna use a table to place all the elements */
	$table = new Layout_table();
	$table->addTableRow(array("cellspacing" => 1));
		$table->insertTableData(gettext("Step 1 of 2"), array("colspan" => 2), "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("choose seperation character"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addSelectField("import[seperator]", $seps);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("skip first line"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addSelectField("import[skip_first]", $skip_first);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("select import target"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addSelectField("import[target]", $target);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addCode(gettext("choose file to import (CSV format)"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addUploadField("import_file");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData(array("colspan" => 2), "data");
			$table->insertAction("forward", gettext("next"), "javascript: import_to_step2();");
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
