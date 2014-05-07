<?php
/**
 * Covide Groupware-CRM Logbook module
 * 
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Svante Kvarnstrom <sjktje@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Svante Kvarnstrom
 * @package Covide
 */

if(!class_exists("Logbook_output")) 
	die("no class definition found");

$logbook_data = new Logbook_data(); 

/* Define top */
$top = $_REQUEST["top"];
if ($top == "") $top = 0;

$total_count = $logbook_data->getLogEntryCount($_REQUEST["regmod"], $_REQUEST["id"]);

$options = Array(
	"module"	=> $_REQUEST["regmod"],
	"record_id" => $_REQUEST["id"],
	"top"		=> $top
);

$entries = $logbook_data->getLogEntries($options);

$output = new Layout_output();
$output->layout_page("Logbook", 1);

$frame = new Layout_venster(array(
	"title"		=> gettext("Logbook"),
	"subtitle"  => gettext("View")
));

$frame->addVensterData();

foreach ($entries as $entry) {
	$venster = new Layout_venster();
	$venster->addVensterData();

	$table = new Layout_table(array("cellspacing" => 3));
	
		$table->addTableRow();
			$table->insertTableData(gettext("Added by").":", "", "header");
			$table->insertTableData($entry["username"], "", "data");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Date").":", "", "header");
			$table->insertTableData($entry["timestamp"], "", "data");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Message").":", "", "header");
			$table->insertTableData($entry["message"], "", "data");
		$table->endTableRow();
	$table->endTable();
	
	$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$venster->addTag("br");
	$frame->addCode($venster->generate_output());
	unset($table);
	unset($venster);
}

/* Page links ... */
$url = "index.php?mod=logbook&regmod=".$params["regmod"]."&id=".$entry["record_id"]."&top=%%";
$paging = new Layout_paging();
$paging->setOptions($top, $total_count, $url);

$frame->addCode($paging->generate_output());

$frame->endVensterData();

$output->addCode($frame->generate_output());
unset($frame);

$output->layout_page_end();
$output->exit_buffer();

?>
