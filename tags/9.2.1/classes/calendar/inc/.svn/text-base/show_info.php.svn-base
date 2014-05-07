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
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
$conversion = new Layout_conversion();
$calendar_data = new Calendar_data();
$calendar_item = $calendar_data->getCalendarItemById($_REQUEST["id"], $_REQUEST["user_id"]);

$datestamp = mktime(0, 0, 0, $calendar_item["begin_month"], $calendar_item["begin_day"], $calendar_item["begin_year"]);
$fields[gettext("from")] = $calendar_item["begin_day"]."-".$calendar_item["begin_month"]."-".$calendar_item["begin_year"]."&nbsp;".$calendar_item["begin_hour"].":".$calendar_item["begin_min"];
$fields[gettext("till")] = $calendar_item["begin_day"]."-".$calendar_item["begin_month"]."-".$calendar_item["begin_year"]."&nbsp;".$calendar_item["end_hour"].":".$calendar_item["end_min"];
$fields[gettext("last changed by")] =  $calendar_item["h_modified_by"]." ".gettext("on").": ".$calendar_item["h_modified"];
$fields[gettext("subject")] = $calendar_item["subject"];
$fields[gettext("description")] = nl2br($calendar_item["description"]);
$fields[gettext("location")] = $calendar_item["location"];
$fields[gettext("kilometers")] = $calendar_item["kilometers"];
/* ugly hack. we should come up with something else for this
 * TODO: fix this ugly html only thing
 */
if ($calendar_item["all_address_ids"]) {
	$address_ids = explode(",", $calendar_item["all_address_ids"]);
	$address_names = explode(",", $calendar_item["all_address_names"]);
	foreach($address_ids as $k=>$address_id)
		$fields[gettext("contact")] .= "<a href=\"index.php?mod=address&action=relcard&id=".$address_id."\">".$address_names[$k]."</a>";
}
$fields[($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("declaration") : gettext("project")]  = "<a href=\"index.php?mod=project&action=showhours&id=".$calendar_item["project_id"]."\">".$calendar_item["project_name"]."</a>";

if ($calendar_item["note_id"])
	$fields[gettext("note")] = "<a href=\"index.php?mod=note&action=message&msg_id=".$calendar_item["note_id"]."\">".$calendar_item["note_title"]."</a>";
else
	$fields[gettext("note")] = "<a href=\"javascript: popup('index.php?mod=note&action=edit&calendar_id=".$calendar_item["id"]."&address_id=".$calendar_item["address_id"]."&project_id=".$calendar_item["project_id"]."');\">".gettext("make note")."</a>";

$table = new Layout_table();
if ($calendar_item["is_repeat"]) {
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addSpace(1);
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("this is a repeating item"));
			$table->insertAction("calendar_repeat", ("this is a repeating item"), "");
		$table->endTableData();
	$table->endTableRow();
}
foreach ($fields as $k=>$v) {
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode($k);
		$table->endTableData();
		$table->addTableData("", "data");
			if ($k == gettext("subject")) {
				if ($calendar_item["is_private"]) {
					$table->insertAction("state_private", gettext("private appointment"), "", "");
					$table->addTag("b");
						$table->addCode(gettext("private appointment"));
					$table->endTag("b");
					$table->addTag("br");
				}
				$v = $conversion->convertHtmlTags($v);
			}

			$table->addCode($v);
		$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addSpace(1);
	$table->endTableData();
	$table->addTableData("", "data");
		if (!$calendar_item["is_registered"]) {
			$table->insertAction("edit", gettext("edit"), sprintf("javascript: calendaritem_edit(%d, %d, %d);", $calendar_item["id"], $calendar_item["user_id"], $datestamp));
			$table->insertAction("delete", gettext("delete"), sprintf("javascript: calendaritem_remove(%d, %d, %d, %d);", $calendar_item["id"], $_REQUEST["user_id"], $calendar_item["is_repeat"], $datestamp));
			if ($calendar_item["user_id"] == $_SESSION["user_id"] && $calendar_item["project_id"] && !$GLOBALS["covide"]->license["has_project_declaration"] && ($calendar_item["timestamp_start"] < time())) {
				$table->insertAction("calendar_reg_hour", gettext("register hours"), sprintf("javascript: calendaritem_reg(%d, %d, %d);", $calendar_item["id"], $calendar_item["user_id"], $datestamp));
			}
		}
	$table->endTableData();
$table->endTableRow();
$table->endTable();
$buf = str_replace("'", "\'", preg_replace("/(\r|\n)/si", "", $table->generate_output() ) );
echo sprintf("infoLayer('%s');", $buf);
?>
