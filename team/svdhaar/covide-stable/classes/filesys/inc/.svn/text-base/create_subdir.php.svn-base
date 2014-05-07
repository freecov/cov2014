<?php
	/**
	 * Covide Groupware-CRM Filesys module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("Filesys_output")) {
		exit("no class definition found");
	}


/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Create subdirectory"), 1);


$settings = array(
	"title"    => gettext("Create subdirectory"),
	"subtitle" => ""
);
$venster = new Layout_venster($settings);
unset($settings);

/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
	$table = new Layout_table(array("cellspacing"=>1));


	$table->addTableRow();
		$table->addCode($this->show_newfolder(1)->generate_output());
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);

$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->load_javascript(self::include_dir."file_operations.js");
$output->layout_page_end();
echo $output->generate_output();
?>
