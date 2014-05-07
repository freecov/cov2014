<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */


if (!class_exists("Address_output")) {
	die("no class definition found");
}

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
			$table->addCode(gettext("choose file to import (VCF format)"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addUploadField("import_file");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("close", gettext("close"), "javascript: window.close();");
			$table->insertAction("forward", gettext("next"), "javascript: to_vCard_process();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addTag("form", array(
	"id"      => "vcard",
	"method"  => "post",
	"action"  => "index.php",
	"enctype" => "multipart/form-data"
));
$output->addHiddenField("mod", "address");
$output->addHiddenField("action", "importVcard_process");
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
$output->load_javascript(self::include_dir."import_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
