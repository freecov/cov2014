<?php
if (!class_exists("Todo_output")) {
	die("no class definition found");
}
$todo_data = new Todo_data();
$todoinfo = $todo_data->getTodoById($_REQUEST["id"]);

$fields[gettext("from")] = $todoinfo["humanstart"];
$fields[gettext("till")] = $todoinfo["humanend"];
$fields[gettext("subject")] = $todoinfo["subject"];
$fields[gettext("description")] = nl2br($todoinfo["body"]);

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
