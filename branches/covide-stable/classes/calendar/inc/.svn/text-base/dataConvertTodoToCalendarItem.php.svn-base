<?php
/**
 * Covide Groupware-CRM calendar module
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
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
/* fetch the todo */
$todo_data = new Todo_data();
$item  = $todo_data->getTodoById($todoid);
$item["begin_day"] = date("d", $item["timestamp"]);
$item["begin_month"] = date("m", $item["timestamp"]);
$item["begin_year"] = date("Y", $item["timestamp"]);
$item["begin_hour"] = date("H");
$item["begin_min"] = date("i");
$item["end_hour"] = date("H");
$item["end_min"] = date("i");
$item["description"] = $item["body"];
$item["todoid"] = $todoid;
?>
