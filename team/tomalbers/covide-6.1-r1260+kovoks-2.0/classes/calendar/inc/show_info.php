<?php
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
$calendar_data = new Calendar_data();
$calendar_item = $calendar_data->getCalendarItemById($_REQUEST["id"]);

$fields[gettext("van")] = $calendar_item["begin_day"]."-".$calendar_item["begin_month"]."-".$calendar_item["begin_year"]."&nbsp;".$calendar_item["begin_hour"].":".$calendar_item["begin_min"];
$fields[gettext("tot")] = $calendar_item["begin_day"]."-".$calendar_item["begin_month"]."-".$calendar_item["begin_year"]."&nbsp;".$calendar_item["end_hour"].":".$calendar_item["end_min"];
$fields[gettext("laatst gewijzigd door")] =  $calendar_item["h_modified_by"]." ".gettext("op").": ".$calendar_item["h_modified"];
$fields[gettext("onderwerp")] = $calendar_item["subject"];
$fields[gettext("omschrijving")] = nl2br($calendar_item["description"]);
$fields[gettext("locatie")] = $calendar_item["location"];
/* ugly hack. we should come up with something else for this
 * TODO: fix this ugly html only thing
 */
$fields[gettext("relation")] = "<a href=\"index.php?mod=address&action=relcard&id=".$calendar_item["address_id"]."\">".$calendar_item["address_name"]."</a>";
$fields[gettext("project")]  = "<a href=\"index.php?mod=project&action=showhours&id=".$calendar_item["project_id"]."\">".$calendar_item["project_name"]."</a>";

$table = new Layout_table();
foreach ($fields as $k=>$v) {
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode($k);
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode($v);
		$table->endTableData();
	$table->endTableRow();
}
$table->endTable();
$buf = addslashes( preg_replace("/(\r|\n)/si", "", $table->generate_output() ) );
echo sprintf("infoLayer('%s');", $buf);
?>
