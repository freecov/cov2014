<?php
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
