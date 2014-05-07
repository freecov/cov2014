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

$emptyList = $_REQUEST["empty"];
if($emptyList) {
	$sql = sprintf("DELETE FROM snack_order", $id);
	sql_query($sql);
}

/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
$user_arr = $user->getUserList();
/* get the snacks data */
$snackdata  = new Snack_data();
$snack_arr = $snackdata->getSnacks();

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
  /* get the permissions for the user */
$user = new User_data();
$access = $user->getUserPermissionsById($_SESSION["user_id"]);
	if($access[xs_addressmanage] != 0){
		$venster->addMenuItem(gettext("show item list"), "./?mod=snack&action=itemlist");
		$venster->addMenuItem(gettext("add items"), "javascript:add_items();");
	}

$venster->generateMenuItems();

$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$data = $snack_arr;
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */


		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("ammount"), "%ammount");


	/* define the mappings */

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	$paging = new Layout_paging();


	$tbl = new Layout_table();
	$tbl->addTableRow();
	$tbl->addTableData();
		$tbl->insertTableData(gettext("Order for: &nbsp;"));
		$tbl->insertTableData(date("d M Y"));
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
