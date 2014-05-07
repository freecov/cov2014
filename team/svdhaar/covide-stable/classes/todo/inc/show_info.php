<?php
/**
 * Covide Groupware-CRM Todo module show info in infolayer.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 *
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (!class_exists("Todo_output")) {
	die("no class definition found");
}
$todo_data = new Todo_data();
$todoinfo = $todo_data->getTodoById($_REQUEST["id"]);

$fields[gettext("from")] = $todoinfo["humanstart"];
$fields[gettext("till")] = $todoinfo["humanend"];
$fields[gettext("subject")] = $todoinfo["subject"];
$fields[gettext("status")] = ($todoinfo["status"] == 0?gettext("active"):gettext("passive"));
$fields[gettext("priority")] = ($todoinfo["priority"]?$todoinfo["priority"]:5);
$fields[gettext("description")] = nl2br($todoinfo["body"]);
$fields[gettext("relation")] = $todoinfo["relation_name"];
if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
	$fields[gettext("project")] = $todoinfo["project_name"];
}
$output = new Layout_output();
$output->insertAction("info", gettext("view"), sprintf("index.php?mod=todo&action=&search=%s", urlencode(substr($todoinfo["subject"], 0, 100))));
$fields[gettext("actions")] = $output->generate_output();
unset($output);
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
