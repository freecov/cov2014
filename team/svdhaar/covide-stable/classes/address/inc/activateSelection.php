<?php
/**
 * Covide Groupware-CRM Addressbook module. multi classifications
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

if (!class_exists("Address_output")) {
	die("no class definition found");
}
$addressdata = new Address_data();
$options = $addressdata->getExportInfo($_REQUEST["info"]);

if (!is_array($options)) {
	die("something went wrong, options is not an array");
}

/* start building output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	/* use a form */
	$output->addTag("form", array(
		"id"     => "activateSelection",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "activateSelectionExec");
	foreach ($options as $k=>$v) {
		if (is_array($v)) {
			foreach ($v as $h=>$i) {
				$output->addHiddenField("options[$k][$h]", $i);
			}
		} else {
			$output->addHiddenField("options[$k]", $v);
		}
	}
	$addresses = $addressdata->getRelationsList($options);

	/* window widget */
	$venster = new Layout_venster(array("title" => gettext("addressbook")));
	$venster->addVensterData();
		/* use a table for layout */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
		$table->insertTableData(gettext("Are you sure you want to set these contacts on active?"), "", "header");
		$table->endTableRow();
		
		foreach ($addresses["address"] as $k) {
			$table->addTableRow();
				$table->insertTableData($k["companyname"], "");
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData("", "");
		$table->endTableRow();	
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("cancel", gettext("cancel"), "javascript: closepopup();");
				$table->insertAction("ok", gettext("ok"), "javascript: document.getElementById('activateSelection').submit();");
			$table->endTableData();
		$table->endTableRow();
		/* end table and attach to window */
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	/* add window to output */
	$output->addCode($venster->generate_output());
	unset($venster);
	/* end form */
	$output->endTag("form");
	//$output->load_javascript(self::include_dir."addclaMulti.js");
/* end buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
