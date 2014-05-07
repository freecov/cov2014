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


/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Snacks")." ".$subtitle, 1);

$output->addTag("form", array(
	"id"     => "snackinput",
	"method" => "POST",
	"action" => "index.php?mod=snack&action=savesnacks"
));



$settings = array(
	"title"    => gettext("Snacks"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
//$venster->addMenuItem(gettext("back"), "javascript: window.close();");
//$venster->generateMenuItems();



	/* prepare grid */
	$settings = array();
	$snackdata  = new Snack_data();
	$snack_arr = $snackdata->getSnackItems();
	$data = $snack_arr;

	$view = new Layout_view();

	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */


		$view->addMapping(gettext("choose"), "%%ammountbox");
		$view->addMapping(gettext("snacks"), "%name");

		$view->defineComplexMapping("ammountbox", array(
			array(
				"text" => $output->insertCheckbox(array("%id"), "1", 0, 1)
			),

		));

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();
	$venster->addCode( $view->generate_output() );
	unset($view);


	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("close", gettext("close window"), "javascript: window.close();");
			$table->addSpace(2);
			$table->insertAction("mail_send", gettext("send"), "javascript: save_snacks();");
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
