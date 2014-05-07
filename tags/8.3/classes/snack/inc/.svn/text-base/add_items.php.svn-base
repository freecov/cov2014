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
if (!class_exists("Snack_data")) {
	die("no class found");
}
/* get the permissions for the user */
$user = new User_data();
$access = $user->getUserPermissionsById($_SESSION["user_id"]);
if($access[xs_addressmanage] == 0) {
	$output = new Layout_output();
	$output->layout_page("snacks");

	$venster = new Layout_venster(array(
		"title" => gettext("Snack"),
		"subtitle" => gettext("No permissions")
	));
	$venster->addVensterData();
		$venster->addCode(gettext("You have no permissions to access the snackitem list"));
		$venster->addTag("br");
		$history = new Layout_history();
		$link = $history->generate_history_call();
		$venster->addCode($link);

		$venster->insertAction("back", gettext("back"), "javascript: history_goback();");
	$venster->endVensterData();

	$table = new Layout_table();

	$output->addCode($table->createEmptyTable($venster->generate_output()));
	$output->exit_buffer();
}
/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Snacks")." ".$subtitle, 1);


if(isset($_REQUEST["new_item"])) {
	if(isset($_REQUEST['hiddenIdNumber'])){
			$snackdata  = new Snack_data();
			$snack_save = $snackdata->update_item(array($_REQUEST["hiddenIdNumber"], $_REQUEST["new_item"]));
		} else {
			$snackdata  = new Snack_data();
			$snack_save = $snackdata->save_new_item($_REQUEST["new_item"]);
		}
}

$settings = array(
	"title"    => gettext("Snacks"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
//$venster->addMenuItem(gettext("back"), "javascript: window.close();");
//$venster->generateMenuItems();

	$output->addTag("form", array(
		"id"     => "additemsform",
		"method" => "POST",
		"action" => "index.php?mod=snack&action=additems"
	));

if($_REQUEST["id"] != 'undefined') {
	$snackdata  = new Snack_data();
	$snack_getID = $snackdata->getSnackById($_REQUEST["id"]);
	$editFieldValue = $snack_getID[0][name];
	$output->addHiddenField("hiddenIdNumber",$_REQUEST["id"]);
} else {
	$editFieldValue = "";
}

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->addTableData();
          $table->insertTableData(gettext("new snack item"), "", "header");
		$table->endTableData();
		$table->addTableData();
          $table->addTextField("new_item", "$editFieldValue");
		$table->endTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);


	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
			$table->insertAction("close", gettext("close window"), "javascript: window.close();");
			$table->addSpace(2);
			$table->insertAction("ok", gettext("insert"), "javascript: save_add_items();");
		$table->endTableData();
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
