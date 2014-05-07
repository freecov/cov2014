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
	$options = $addressdata->getExportInfo($searchoptions);

	$addresses = $addressdata->getRelationsList($options);
	$addresses = $addresses['address'];

	/* if nothing is selected, put all addresses in the $options array */
	if (!isset($options['ids'])) {
		$options['ids'] = implode("," , array_keys($addresses));
	}

	if (!is_array($options)) {
		die("something went wrong, options is not an array");
	} else {
		/*get classification*/
		$address_data = new Address_data();
		$id_r = explode(",", $options['ids']);
		$i = 0;
		foreach ($id_r as $id) {
			$address_info = $address_data->getAddressById($id, $_REQUEST['type']);
			$id = $address_info['id'];
			$classification[$i++] = explode("|", $address_info['classification']);
		}
		unset($address_info);

		$common = $classification[0];
		for ($j = 1; $j < $i; $j++) {
			$common = array_intersect($common, $classification[$j]);
		}
		$address_info['classification'] = implode("|", $common);
	}
} else {
	$address_data = new Address_data();
	$address_info = $address_data->getAddressById($_REQUEST['id'], $_REQUEST['type']);
}
/* start building output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	/* use a form */
	$output->addTag("form", array(
		"id"     => "addclamulti",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "savecla_multi");
	$output->addHiddenField("addresstype", $_REQUEST["type"]);
	if ($inline) {
		$output->addHiddenField("addressid", $_REQUEST["id"]);
		$output->addHiddenField("inline", 1);
	} else {
		$output->addHiddenField("options[common_cla]", $address_info['classification']);
		foreach ($options as $k => $v) {
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
	/* use a table for layout */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("affected relations"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData("", "", "header");
		$companynames = "";
		foreach ($addresses as $k) {
			$companynames .= "&nbsp;".$k["companyname"]."<br>";
		}
		$table->insertTableData($companynames, "");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("pick an existing classification"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData(gettext("classification"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("address[classification]", $address_info["classification"]);
			$table->endTag("span");
			$classification = new Classification_output();
			$table->addCode($classification->classification_selection("addressclassification", $address_info["classification"]));
		$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "data");
				if (!$inline) {
					$table->insertAction("new", gettext("create a new classification"), "javascript: popup('index.php?mod=classification&action=cla_edit&id=0', 'claform', 700, 500, 1);");
				}
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
