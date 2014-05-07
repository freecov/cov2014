<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

/* only allow this file to be included in the class scope */
if (!class_exists("Email_output")) {
	die("no class definition found");
}
$output = new Layout_output();
$output->layout_page("email", 1);

	$venster = new Layout_venster(Array(
		"title"    => gettext("Email"),
		"subtitle" => gettext("share folders")
	));
	$venster->addVensterData();

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

	$venster->endVensterData();

$output->addCode($venster->generate_output());


$output->layout_page_end();
$output->exit_buffer();
?>
