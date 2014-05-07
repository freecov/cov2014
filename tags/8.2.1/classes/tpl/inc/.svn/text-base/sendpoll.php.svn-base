<?php
/**
 * Covide Template poll handler
 *
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("Tpl_output")) {
	die("no class definition found");
}

$sql = sprintf("INSERT INTO poll_answers (poll_id, item_id) VALUES (%d, %d)", $req["pollid"], $req["pollanswer"]);
$res = sql_query($sql);
setcookie("pollvoted".$req["pollid"], "yes", mktime()+(60*60*24*356), "/");
/* now grab the poll results */
$polldata = $this->showPollResults($req["pollid"]);
$output = new Layout_output();
$output->start_javascript();
	$output->addCode("parent.document.getElementById('pollcontainer').innerHTML = '".str_replace("'", "\'", str_replace("\n", "", $polldata))."';");
$output->end_javascript();
$output->exit_buffer();
?>
