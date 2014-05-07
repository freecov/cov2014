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

$emptyList = $_REQUEST["empty"];
if($emptyList) {
	$sql = sprintf("DELETE FROM snack_items WHERE id = '".$emptyList."'", $id);
	sql_query($sql);
}

/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
$user_arr = $user->getUserList();
/* get the snacks data */
$snackdata  = new Snack_data();
$snack_arr = $snackdata->getSnackItems();

/* start output buffer routines */
$output = new Layout_output();
$output->layout_page( gettext("Snacks") );


$settings = array(
	"title"    => gettext("Snacks"),
	"subtitle" => gettext("Mmm! Delicious!"),
);
$venster = new Layout_venster($settings);
unset($settings);

$venster->addMenuItem(gettext("show list"), "./?mod=snack");
$venster->addMenuItem(gettext("empty list"), "javascript:empty_snack();");
$venster->addMenuItem(gettext("add snacks"), "javascript:add_snack();");
$venster->addMenuItem(gettext("show item list"), "./?mod=snack&action=itemlist");
$venster->addMenuItem(gettext("add items"), "javascript:add_items();");

$venster->generateMenuItems();

$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$data = $snack_arr;
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */


		 $view->addMapping("", "%%complex_actions");
   		 $view->addMapping(gettext("name"), "%name");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: add_items('", "%id", "');"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_items('", "%id", "');"),
		)
	), "nowrap");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	$paging = new Layout_paging();


	$tbl = new Layout_table();
	$tbl->addTableRow();
	$tbl->addTableData();
	$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addCode($tbl->generate_output());
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->load_javascript(self::include_dir."snack_actions.js");
$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );
$output->layout_page_end();
echo $output->generate_output();


?>
