<?php
/**
 * Covide Groupware-CRM Addressbook module.
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
if (!$_REQUEST["address_id"]) {
	die("no address id given");
}

$output = new Layout_output();
$output->layout_page("", 1);
	$venster = new Layout_venster(array("title" => gettext("search")));
	$venster->addVensterData();
		$venster->addTag("form", array(
			"id"     => "relcardsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "address");
		$venster->addHiddenField("action", "relcardsearch");
		$venster->addHiddenField("address_id", $_REQUEST["address_id"]);
		$venster->addCode(gettext("search for"));
		$venster->addSpace(1);
		$venster->addTextField("searchkey", "", "", "", 1);
		$venster->addSpace(1);
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('relcardsearch').submit();");
		$venster->endTag("form");
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
$output->layout_page_end();
$output->exit_buffer();
?>
