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
if (!$inline) {
	$addressdata = new Address_data();
	$options = $addressdata->getExportInfo($_REQUEST["info"]);
	if (!is_array($options)) {
		die("something went wrong, options is not an array");
	}
}
/* start building output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	/* use a form */
	$output->addTag("form", array(
		"id"     => "addclamulti",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "savecla_multi");
	if ($inline) {
		$output->addHiddenField("addresstype", $_REQUEST["type"]);
		$output->addHiddenField("addressid", $_REQUEST["id"]);
		$output->addHiddenField("inline", 1);
	} else {
		foreach ($options as $k=>$v) {
			if (is_array($v)) {
				foreach ($v as $h=>$i) {
					$output->addHiddenField("options[$k][$h]", $i);
				}
			} else {
				$output->addHiddenField("options[$k]", $v);
			}
		}
	}
	/* window widget */
	$venster = new Layout_venster(array("title" => gettext("classifications")));
	$venster->addVensterData();
	/* Let them pick an existing classification */
	$classifications = array(0 => "---");
	$cla_data = new Classification_data;
	$classi = $cla_data->getClassifications();
	foreach ($classi as $cla) {
		$classifications[$cla["id"]] = $cla["description"];
	}

	/* use a table for layout */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("pick an existing classification"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("name"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("addcla[classification_id]", $classifications, $_REQUEST["addcla"]["name"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "data");
				if (!$inline) {
					$table->insertAction("new", gettext("create a new classification"), "javascript: popup('index.php?mod=classification&action=cla_edit&id=0', 'claform', 700, 500, 1);");
				}
				$table->insertAction("cancel", gettext("back"), "javascript: window.close();");
				$table->insertAction("save", gettext("save"), "javascript: document.getElementById('addclamulti').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	/* add window to output */
	$output->addCode($venster->generate_output());
	unset($venster);
	/* end form */
	$output->endTag("form");
/* end buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
