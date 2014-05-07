<?php
/**
 * Covide Groupware-CRM Snack module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
if (!class_exists("Snack_output")) {
	die("no class found");
}

/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Snacks")." ".$subtitle, 1);



$settings = array(
	"title"    => gettext("Snacks"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
$venster->addMenuItem(gettext("close"), "javascript: close_saved_snacks();");
$venster->generateMenuItems();


	$snackdata  = new Snack_data();
	$snack_count = $snackdata->count_snacks("snack_items");
	$realCount = count($snack_count);
	$newSnackArr = array();
	for($i=0; $i<=$realCount; $i++){
		$newId = $snack_count[$i][id];
		if($_REQUEST[$newId]){
			$newSnackArr[$newId] = 1;
		}
	}
	$snackdata->save_snacks($newSnackArr);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();



	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->addTableHeader();
			$table->insertTableHeader(gettext("Saved snacks"));
		$table->endTableHeader();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
			$table->insertTableData(gettext("Your snacks have been saved into the database"));
		$table->endTableData();
	$table->endTableRow();

	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
$output->load_javascript(self::include_dir."snack_actions.js");
$output->layout_page_end();
echo $output->generate_output();
?>
