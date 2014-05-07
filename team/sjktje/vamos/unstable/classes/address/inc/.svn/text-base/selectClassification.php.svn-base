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
$output = new Layout_output();
$output->layout_page("classifications");

$settings = array(
	"title"    => gettext("Addressbook"),
	"subtitle" => gettext("pick classification(s)")
);

$output->addTag("form", array(
	"id"     => "velden",
	"action" => "index.php",
	"method" => "post"
));
$output->addHiddenField("mod", "address");

$classification = new Classification_output();

$output_alt = new Layout_output();
$output_alt->insertAction("back", gettext("back"), "?mod=address");
$output_alt->insertAction("forward", gettext("next"), "javascript: step_next();");

$venster = new Layout_venster($settings, 1);
$venster->addVensterData();
	$venster->addCode( $classification->select_classification("", $output_alt->generate_output() ) );
$venster->endVensterData();

$placeholder = new Layout_table();
$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

$output->start_javascript();
$output->addCode("
	function step_next() {
		document.getElementById('velden').submit();
	}
");
$output->end_javascript();

$output->layout_page_end();
$output->exit_buffer();
?>
