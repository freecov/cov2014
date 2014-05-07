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
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
/* get all users */
$user = new User_data();
$users = $user->getUserlist(1);
/* get the users interval setting */
$userdetails = $user->getUserdetailsById($_SESSION["user_id"]);
$intervalAmount = $userdetails["calendarinterval"];

if (!$id) {
	$id = 0;
}
/* get all groups */
$grouplist = $user->getGroupList(0, "", 1);
$groups = array(0 => gettext("none"));
foreach ($grouplist as $group) {
	$groups[$group["id"]] = $group["name"];
}

/* generate arrays for date selectbox */
$days_to = array();
for ($i=1;$i<=31;$i++) {
	$days_to[$i] = $i;
}
$months_to = array();
for ($i=1;$i<=12;$i++) {
	$months_to[$i] = $i;
}
$years_to = array();
for ($i = date("Y")-1; $i != date("Y")+15; $i++) {
	$years_to[$i] = $i;
}
$hours_to = array();
for ($i=0;$i<=23;$i++) {
	if ($userdetails["hour_format"] == 1) {
		$prefDate = "g A";
	} else {
		$prefDate = "H";
	}
	$hour_time = mktime($i, 0, 0, date("m"), date("d"), date("Y"));
	$hours_to[$i] = date($prefDate, $hour_time);

}
$minutes_to = array();
for ($i=0;$i!=60;$i=$i+$intervalAmount) {
	$hour_time = mktime(0, $i, 0, date("m"), date("d"), date("Y"));
	$minutes_to[$i] = date("i", $hour_time);;
}
$repeat = array(
	0 => gettext("no"),
	1 => gettext("yes")
);
$repeat_type = array(
	0 => gettext("na"),
	1 => gettext("daily"),
	2 => gettext("weekly"),
	3 => gettext("Monthly (by day)"),
	4 => gettext("Monthly (by day (from end of month))"),
	5 => gettext("Monthly (by date)"),
	6 => gettext("yearly")
);

$notifytime = array(
	0                       => "--",
	mktime(1,15,0,1,1,1970) => "15 ".gettext("minutes"),
	mktime(1,30,0,1,1,1970) => "30 ".gettext("minutes"),
	mktime(2,0,0,1,1,1970)  => "1 ".gettext("hour"),
	mktime(3,0,0,1,1,1970)  => "2 ".gettext("hour"),
	mktime(6,0,0,1,1,1970)  => "5 ".gettext("hour"),
	mktime(0,0,1,1,2,1970)  => "1 ".gettext("day"),
	mktime(0,0,1,1,3,1970)  => "2 ".gettext("days"),
	mktime(0,0,1,1,6,1970)  => "5 ".gettext("days"),
	mktime(0,0,1,1,8,1970)  => "1 ".gettext("week"),
	mktime(0,0,1,1,15,1970) => "2 ".gettext("weeks"),
	mktime(0,0,1,1,22,1970) => "3 ".gettext("weeks"),
	mktime(0,0,1,2,1,1970)  => "1 ".gettext("month")
);

$app_type = array(
	1 => gettext("business"),
	2 => gettext("private"),
	3 => gettext("holidays"),
	4 => gettext("special leave"),
	5 => gettext("reported sick")
);

$importance = array(
	0 => gettext("low"),
	1 => gettext("normal"),
	2 => gettext("high"),
);

$calendar_data = new Calendar_data();
$calendar_item = $calendar_data->getCalendarItemById($id, $_REQUEST["user"]);
/* make app_type field based on is_ill etc */
if ($calendar_item["is_ill"]) {
	$calendar_item["app_type"] = 5;
}
if ($calendar_item["is_specialleave"]) {
	$calendar_item["app_type"] = 4;
}
if ($calendar_item["is_holiday"]) {
	$calendar_item["app_type"] = 3;
}
if ($calendar_item["is_private"]) {
	$calendar_item["app_type"] = 2;
}
if (!$calendar_item["app_type"]) {
	$calendar_item["app_type"] = 1;
}
if ($_REQUEST["datemask"]) {
	/* get date where we were editing the item */
	$calendar_item["begin_day"]   = date("d", $_REQUEST["datemask"]);
	$calendar_item["begin_month"] = date("m", $_REQUEST["datemask"]);
	$calendar_item["begin_year"]  = date("Y", $_REQUEST["datemask"]);
	$hour = date("H", $_REQUEST["datemask"]);
	if ($hour != "00") {
		$calendar_item["begin_hour"] = $hour;
		$calendar_item["end_hour"] = $hour+1;
	}
	$minute = date("i", $_REQUEST["datemask"]);
	if ($minute != "00")
		$calendar_item["begin_min"] = date("i", $_REQUEST["datemask"]);
}
/* check if we are planning a todo */
if ($_REQUEST["todoid"]) {
	/* grab todo and reformat info so we can use it in the screen */
	$calendar_item = $calendar_data->convertTodoToCalendarItem($_REQUEST["todoid"]);
}
/* check if we want to register a support*/
if ($_REQUEST["supportid"]) {
	/* grab support and reformat info so we can use it in the screen */
		$priorRay = array(1=>gettext("low"),2=>gettext("medium"),3=>gettext("high"));
		$support_data = new Support_data();
		$item  = $support_data->getSupportItemById($_REQUEST["supportid"]);
		$user_data = new User_data();
		$user_register = $user_data->getUsernameById($item["registering_id"]);
		$user_executor = $user_data->getUsernameById($item["user_id"]);
		$calendar_item["subject"] = "[".gettext("Support")."] - ".$item["reference_nr"];
		$calendar_item["address_id"] = $item["address_id"];
		$calendar_item["project_id"] = $item["project_id"];
		$calendar_item["private_id"] = $item["private_id"];
		$calendar_item["description"] =  gettext("description").": ".$item["description"]."\n";
		$calendar_item["description"] .= gettext("solution").": ".$item["solution"]."\n";
		$calendar_item["description"] .= gettext("priority").": ".$priorRay[$item["priority"]]."\n";
		$calendar_item["description"] .= gettext("registrant").": ".$user_register."\n";
		$calendar_item["description"] .= gettext("executor").": ".$user_executor."\n";
		$calendar_item["begin_day"] = date("d", $item["timestamp"]);
		$calendar_item["begin_month"] = date("m", $item["timestamp"]);
		$calendar_item["begin_year"] = date("Y", $item["timestamp"]);
}

if ($_REQUEST["address_id"]) {
	$calendar_item["address_id"] = sprintf("%d", $_REQUEST["address_id"]);
}
if ($_REQUEST["project_id"]) {
	$calendar_item["project_id"] = sprintf("%d", $_REQUEST["project_id"]);
}
/* get relation name */
$address = new Address_data();
if ((int)$calendar_item["address_id"] && !$calendar_item["multirel"]) {
	$relname = $address->getAddressNameById($calendar_item["address_id"]);
} else {
	$relname = "";
}
/* see if we need to do some magic on the selected addresses */
if ($calendar_item["multirel"]) {
	$address_ids = explode(",", $calendar_item["multirel"]);
	$address_ids[] = $calendar_item["address_id"];
	sort($address_ids);
	$multirel = array();
	foreach ($address_ids as $aid) {
		$multirel[$aid] = $address->getAddressNameById($aid);
	}
	unset($address_ids);
	unset($calendar_item["address_id"]);
	$relname = "";
} else {
	$multirel = array(
		$calendar_item["address_id"] => $address->getAddressNameById($calendar_item["address_id"])
	);
	unset($calendar_item["address_id"]);
	$relname = "";
}
/* see if we need to do some magic on the selected privates */
if ($calendar_item["multiprivate"]) {
	$address_ids = explode(",", $calendar_item["multiprivate"]);
	$address_ids[] = $calendar_item["private_id"];
	sort($address_ids);
	$multiprivate = array();
	foreach ($address_ids as $aid) {
		$info = $address->getRecord(array("id"=>$aid, "type"=>"user"));
		$multiprivate[$aid] = $info["tav"];
	}
	unset($address_ids);
	unset($calendar_item["private_id"]);
	$privname = "";
} elseif ($calendar_item["private_id"]) {
	$info = $address->getRecord(array("id"=>$calendar_item["private_id"], "type"=>"user"));
	$multiprivate = array(
		$calendar_item["private_id"] => $info["tav"],
	);
	unset($calendar_item["private_id"]);
	$privname = "";
}
/* and projectname */
$project = new Project_data();
$projectinfo = $project->getProjectById($calendar_item["project_id"]);
$projectname = $projectinfo[0]["name"];
$output = new Layout_output();
$output->layout_page(gettext("calendar"), 1);

$output->addTag("form", array(
	"id"     => "calendarinput",
	"method" => "post",
	"action" => "index.php"
));
$output->addHiddenField("mod", "calendar");
$output->addHiddenField("appointment[id]", "$id");
$output->addHiddenField("appointment[group_id]", $calendar_item["group_id"]);
$output->addHiddenField("action", "");
$output->addHiddenField("appointment[todoid]", $calendar_item["todoid"]);
$output->addHiddenField("appointment[campaign_id]", $_REQUEST["campaign_id"]);
$output->addHiddenField("appointment[remove_all]", "0");
if ($_REQUEST["user"]) {
	$calendar_item["user"] = (int)$_REQUEST["user"];
	$subtitle = $subtitle = gettext("appointment for ").$users[$_REQUEST["user"]];
} else {
	$calendar_item["user"] = $_SESSION["user_id"];
	$subtitle = $subtitle = gettext("appointment for ").$users[$_SESSION["user_id"]];
}
$venster_settings = array(
	"title"    => gettext("calendar"),
	"subtitle" => $subtitle
);

$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$table = new Layout_table(array("cellspacing"=>"1"));
	$table->addTableRow();
		$table->insertTableData(gettext("for user"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("appointment[user]", $calendar_item["user"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("appointmentuser", $calendar_item["user"], 0, 0, 0, 1, 1, 0, 1) );
		$table->endTableData();
	$table->endTableRow();
		$table->insertTableData(gettext("date"), "", "header");

		/* generate a table with all the dropdown items so we can specify a date */
		$table_date = new Layout_table();
		$table_date->addTableRow();
			$table_date->addTableData( array("rowspan"=>2), "data");
				if (!$calendar_item["id"])
					$table_date->addSelectField("appointment[from_day][]", $days_to, $calendar_item["begin_day"],1,array("size"=>"4"));
				else
					$table_date->addSelectField("appointment[from_day][]", $days_to, $calendar_item["begin_day"],0,array("size"=>"4"));
				$table_date->addSelectField("appointment[from_month]", $months_to, $calendar_item["begin_month"],0,array("size"=>"4"));
				$table_date->addSelectField("appointment[from_year]", $years_to, $calendar_item["begin_year"],0,array("size"=>"4"));
			$table_date->endTableData();
			$table_date->addTableData(array("align"=>"right"), "data");
				$table_date->addCode(gettext("from")."&nbsp;");
				$table_date->addSelectField("appointment[from_hour]", $hours_to, $calendar_item["begin_hour"],0);
				$table_date->addCode(" : ");
				$table_date->addSelectField("appointment[from_min]", $minutes_to, $calendar_item["begin_min"],0);
				$table_date->addTag("br");
				$table_date->addCode(gettext("till")."&nbsp;");
				$table_date->addSelectField("appointment[to_hour]", $hours_to, $calendar_item["end_hour"],0);
				$table_date->addCode(" : ");
				$table_date->addSelectField("appointment[to_minute]", $minutes_to, $calendar_item["end_min"],0);
				$table_date->addTag("br");
				$table_date->addCode(gettext("All day event "));
				$table_date->addCheckBox("appointment[is_event]",1,$calendar_item["alldayevent"]);
			$table_date->endTableData();
		$table_date->endTableRow();
		$table_date->addTableRow();
			$table_date->addTableData();
				$table_date->addCode( $this->show_calendar("document.getElementById('appointmentfrom_day')", "document.getElementById('appointmentfrom_month')", "document.getElementById('appointmentfrom_year')" ));
			$table_date->endTableData();
		$table_date->endTableRow();
		$table_date->endTable();

		$table->insertTableData($table_date->generate_output(), "", "data");
		unset($table_date);
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("repeat"), "", "header");
		$table->addTableData("", "data");
			$table1 = new Layout_table(array("width" => "100%"));
			$table1->addTableRow();
				$table1->addTableData("", "data");
					$table1->addSelectField("appointment[is_repeat]", $repeat, $calendar_item["is_repeat"]);
					$table1->start_javascript();
						$table1->addCode("
						function showHideRepeatLayer(showit) {
							if (showit == 1) {
								document.getElementById('repeat_layer').style.display = '';
							} else {
								document.getElementById('repeat_layer').style.display = 'none';
							}
						}
						repeat = document.getElementById('appointmentis_repeat');
						repeat.onchange = function() {
							showHideRepeatLayer(repeat.options.selectedIndex);
						}
						");
					$table1->end_javascript();
				$table1->endTableData();
				$table1->addTableData(array("align" => "right"), "data");
					//$table1->insertAction("ftype_html", gettext("use html"), "javascript: init_editor('convert_html');", "convert_html");
					$table1->insertAction("close", gettext("close"), "javascript:window.close();");
					if ($id) {
						$table1->insertAction("delete", gettext("delete"), sprintf("javascript:calendaritem_remove(%d, %d);", $id, $calendar_item["is_repeat"]));
					}
					$table1->addTag("span", array("id"=>"action_save_top", "style"=>"visibility: hidden;"));
					$table1->insertAction("save", gettext("save"), sprintf("javascript:calendaritem_save(%d);", $id));
					$table1->endTag("span");
				$table1->endTableData();
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
		$table->endTableData();
	$table->endTableRow();
	if ($calendar_item["is_repeat"]) {
		$table->addTableRow(array("id" => "repeat_layer", "style" => "display: table-row;"));
	} else {
		$table->addTableRow(array("id" => "repeat_layer", "style" => "display: none;"));
	}
		$table->insertTableData("repeat options", "", "header");
		$table->addTableData("", "data");
			$table1 = new Layout_table(array("width" => "100%"));
			$table1->addTableRow();
				$table1->insertTableData(gettext("repeat type"), "", "data");
				$table1->addTableData("", "data");
					$table1->addSelectField("appointment[repeat_type]", $repeat_type, $calendar_item["repeat_type"]);
				$table1->endTableData();
			$table1->endTableRow();
			$table1->addTableRow();
				$table1->insertTableData(gettext("repeat end date"), "", "data");
				$table1->addTableData("", "data");
					$table1->addCode(gettext("use end date"));
					$noneselected = array(0 => "---");
					// We do not use array_merge here because that will renumber the numeric keys of the second array
					$endday = $noneselected + $days_to;
					$endmonth = $noneselected + $months_to;
					$endyear = $noneselected + $years_to;
					$table1->addCheckBox("appointment[repeat_use_end_date]", 1, $calendar_item["repeat_use_end_date"]);
					$table1->addSelectField("appointment[repeat_end_day]", $endday, $calendar_item["repeat_end_day"]);
					$table1->addSelectField("appointment[repeat_end_month]", $endmonth, $calendar_item["repeat_end_month"]);
					$table1->addSelectField("appointment[repeat_end_year]", $endyear, $calendar_item["repeat_end_year"]);
	
				$table1->endTableData();
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
			unset($table1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("controle"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("div", array("id"=>"calendar_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("subject"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("appointment[subject]", $calendar_item["subject"], array("style"=>"width: 570px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("description"), "", "header");
		$table->addTableData("", "data");

			$editor = new Layout_editor();
			$ret = $editor->generate_editor(1);
			if ($ret !== false) {
				if (!$calendar_item["description"])
					$calendar_item["description"] = "&nbsp;";
				$table->addTextArea("appointment[description]", nl2br("<p>".$calendar_item["description"]."</p>"), array("style"=>"width: 570px; height: 300px;"), "contents");
				$table->addCode($ret);
			} else {
				$table->addTextArea("appointment[description]", $calendar_item["description"], array("style"=>"width: 570px; height: 300px;"), "contents");
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("location"), "", "header");
		$table->addTableData("", "data");
			$table_location = new Layout_table();
			$table_location->addTableRow();
				$table_location->addTableData();
					$table_location->addTextField("appointment[location]", $calendar_item["location"]);
				$table_location->endTableData();
				$table_location->addTableData(array("align"=>"left"), "data");
					$table_location->addSpace(3);
					$table_location->addTag("b");
					$table_location->addCode(gettext("distance"));
					$table_location->endTag("b");
					$table_location->addSpace(2);
					$table_location->addTextField("appointment[kilometers]", $calendar_item["kilometers"]);
					$table_location->addSpace(2);
					$table_location->addCode(gettext("km"));
					$table_location->addSpace(2);
					$table_location->insertAction("folder_my_docs", gettext("planner"), "javascript: getDistance();");
				$table_location->endTableData();
			$table_location->endTableRow();
			$table_location->addTableRow();
				$table_location->addTableData();
					$table_location->addCode(gettext("billable kilometers"));
					$table_location->addCheckBox("appointment[deckm]", 1, $calendar_item["deckm"]);
					$table_location->addCode(gettext("yes"));
				$table_location->endTableData();
			$table_location->endTableRow();
			$table_location->endTable();
			$table->addCode( $table_location->generate_output() );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("importance"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("appointment[importance]", $importance, $calendar_item["importance"]);
			$table->addSelectField("appointment[notifytime]", $notifytime, $calendar_item["notifytime"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_voip"] == 1) {
		$table->addTableRow();
			$table->insertTableData(gettext("do not disturb"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("appointment[is_dnd]",1,$calendar_item["is_dnd"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("type of appointment"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("appointment[app_type]", $app_type, $calendar_item["app_type"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("group meeting"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("appointment[is_group]", $groups, $calendar_item["is_group"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("other user(s)"), "", "header");
		$table->addTableData("", "data");
			$useroutput = new User_output();
			$table->addHiddenField("appointment[user_id]", $calendar_item["extra_users"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("appointmentuser_id", $calendar_item["extra_users"], 1, 0, 0, 0, 0, 0, 1) );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("contact"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("appointment[address_id]", $calendar_item["address_id"]);
			$table->insertTag("span", $relname, array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_project"]) {
		$table->addTableRow();
			if ($GLOBALS["covide"]->license["has_project_declaration"]) {
				$table->insertTableData(gettext("declaration"), "", "header");
			} else {
				$table->insertTableData(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project"), "", "header");
			}
			$table->addTableData("", "data");
				$table->addHiddenField("appointment[project_id]", $calendar_item["project_id"]);
				$table->insertTag("span", $projectname, array("id"=>"searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("private"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("appointment[private_id]", $calendar_item["private_id"]);
			$table->insertTag("span", $privname, array(
				"id" => "searchprivate"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("change:"), "javascript: pickPrivate();");
		$table->endTableData();
	$table->endTableRow();
	
	$table->addTableRow();
		$table->insertTableData(gettext("last changed by"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($calendar_item["h_modified_by"]." ".gettext("on")." ".$calendar_item["h_modified"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("melden aan gebruiker(s)"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("appointment[notify_user]", 1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("notify contact"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("div", array(
				"id" => "mail_addresses",
				"class"  => "limit_height",
				"style" => "width: 99%;"
			));
			$table->endTag("div");
			$table->addSpace(1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData(array("align"=>"right"), "data");
			$table->insertAction("close", gettext("close"), "javascript:window.close();");
			if ($id) {
				$table->insertAction("delete", gettext("delete"), sprintf("javascript:calendaritem_remove(%d, %d);", $id, $calendar_item["is_repeat"]));
			}
			$table->addTag("span", array("id"=>"action_save", "style"=>"visibility: hidden;"));
			$table->insertAction("save", gettext("save"), sprintf("javascript:calendaritem_save(%d);", $id));
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
$venster->endVensterData();
$output->addCode($venster->generate_output());
$output->endTag("form");
/* attach javascript */
$output->load_javascript(self::include_dir."show_edit.js");
/* do some more magic with the rel field if necessary */
if (is_array($multirel)) {
	$output->start_javascript();
	$output->addCode("addLoadEvent( update_relsearch() );\n");
	$output->addCode("function update_relsearch() { \n");
	foreach ($multirel as $i=>$n) {
		if ($i) {
			$output->addCode("\n");
			$output->addCode("selectRel($i, '$n');");
		}
	}
	$output->addCode("\n}\n");
	$output->end_javascript();
}
/* do some more magic with the private field if necessary */
if (is_array($multiprivate)) {
	$output->start_javascript();
	$output->addCode("addLoadEvent( update_privatesearch() );\n");
	$output->addCode("function update_privatesearch() { \n");
	foreach ($multiprivate as $i=>$n) {
		if ($i) {
			$output->addCode("\n");
			$output->addCode("selectPrivate($i, '$n');");
		}
	}
	$output->addCode("\n}\n");
	$output->end_javascript();
}
$output->layout_page_end();
$output->exit_buffer();
?>
