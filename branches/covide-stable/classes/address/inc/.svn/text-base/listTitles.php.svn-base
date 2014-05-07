<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups of people
 * that want the most efficient way to work together.
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


/* start output buffer routines */
$output = new Layout_output();
$output->layout_page( gettext("Titles") );


$settings = array(
	"title"    => gettext("Titles"),
	"subtitle" => gettext("List titles"),
);
$venster = new Layout_venster($settings);
unset($settings);

/* Get data */
$data = new Address_data();
$data = $data->getTitles();


unset($data[0]);
$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
   		 $view->addMapping(gettext("title"), "%title");
		 $view->addMapping("", "%%complex_actions");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: edit_title('", "%id", "');"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_title('", "%id", "');"),
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
		$tbl->insertAction("new", gettext("new"), "javascript: edit_title(0);");
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addCode($tbl->generate_output());
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);


$history = new Layout_history();
$output->load_javascript(self::include_dir."title_actions.js");
$output->addCode( $history->generate_save_state("action") );
$output->layout_page_end();
echo $output->generate_output();
?>
