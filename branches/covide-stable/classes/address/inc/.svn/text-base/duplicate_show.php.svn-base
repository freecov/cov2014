<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author IT Outsourcing Asia <krishitosasia@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */


if (!class_exists("Address_output")) {
	die("no class definition found");
}

/* init address data class */
$address_data = new Address_data();

$duplicate_data = $address_data->getById($id);
$original_data = $address_data->getById($duplicate_with);

/* start output buffer */
$output = new Layout_output();
$output->layout_page("", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("Duplicate"),
		"subtitle" => gettext("show")
	));

	$venster->addVensterData();

		/* Set overall table buffer */
		$buffer = array();
		$table = new Layout_table(array("cellspacing" => "1"));

		$table->addTableRow();
			$table->insertTableData(gettext("Duplicate Address"), "", "header");
			$table->addTableData("", "data");
				$table->addCode("<a href=\"javascript: popup('index.php?mod=address&action=show_bcard&address_id=".$duplicate_data['id']."&addresstype=relations&sub=', 'addressedit', 700, 600, 1)\">". $duplicate_data['givenname'].' ' .$duplicate_data['surname'] . "(" . gettext("zip code") . ':' . $duplicate_data['zipcode'] . ', ' . gettext("telephone nr") . ': ' . $duplicate_data['phone_nr'] . ")</a>");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData(gettext("Original Address"), "", "header");
			$table->addTableData("", "data");
				$table->addCode("<a href=\"javascript: popup('index.php?mod=address&action=show_bcard&address_id=".$original_data['id']."&addresstype=relations&sub=', 'addressedit', 700, 600, 1)\">". $original_data['givenname'].' ' .$original_data['surname'] . "(" . gettext("zip code") . ':' . $original_data['zipcode'] . ', ' . gettext("telephone nr") . ': ' . $original_data['phone_nr'] . ")</a>");
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addCode("<a href='javascript: dublicate_approve()'>".gettext("approve")."</a> | <a href='javascript: dublicate_delete()'>".gettext("delete")."</a>");
			$table->endTableData();
		$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());

	/* end form */
	$venster->endVensterData();
	/* include window in output buffer */
	$output->addCode($venster->generate_output());

	$output->start_javascript();
	
	$output->addCode("function dublicate_approve() { \n");
		$output->addCode("location.href='index.php?mod=address&action=duplicate_approve&id=".$id."'\n");

	$output->addCode("\n}\n");

	$output->addCode("function dublicate_delete() { \n");
		$output->addCode("location.href='index.php?mod=address&action=duplicate_delete&id=".$id."'\n");

	$output->addCode("\n}\n");

	$output->end_javascript();

	$output->layout_page_end();
	/* flush the buffer to the browser */
	$output->exit_buffer();
?>
