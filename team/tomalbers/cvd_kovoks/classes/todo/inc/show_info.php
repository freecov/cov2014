<?php
if (!class_exists("Todo_output")) {
	die("no class definition found");
}
$todo_data = new Todo_data();
$todoinfo = $todo_data->getTodoById($_REQUEST["id"]);

$fields[gettext("van")] = $todoinfo["humanstart"];
$fields[gettext("tot")] = $todoinfo["humanend"];
$fields[gettext("onderwerp")] = $todoinfo["subject"];
$fields[gettext("omschrijving")] = nl2br($todoinfo["body"]);

$table = new Layout_table();
foreach ($fields as $k=>$v) {
	$table->addTableRow();
		$table->insertTableData($k, "", "header");
		$table->insertTableData($v, "", "data");
	$table->endTableRow();
}
$table->endTable();
$buf = addslashes( preg_replace("/(\r|\n)/si", "", $table->generate_output() ) );
echo sprintf("infoLayer('%s');", $buf);
?>
