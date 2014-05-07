<?php
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
/* get all users */
$user = new User_data();
$users = $user->getUserlist(1);
if (!$id) {
	$id = 0;
}
/* get all groups */
$grouplist = $user->getGroupList();
$groups = array(0 => gettext("geen"));
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
for ($i=date("Y")-1; $i!=date("Y")+5; $i++) {
	$years_to[$i] = $i;
}
$hours_to = array();
for ($i=0;$i<=23;$i++) {
	if ($i<10) {
		$hours_to[$i] = "0".$i;
	} else {
		$hours_to[$i] = $i;
	}
}
$minutes_to = array();
for ($i=0;$i!=60;$i=$i+15) {
	if ($i<10) {
		$minutes_to[$i] = "0".$i;
	} else {
		$minutes_to[$i] = $i;
	}
}
$repeat = array(
	0  => gettext("nvt"),
	1  => gettext("iedere werkdag"),
	7  => gettext("iedere week"),
	31 => gettext("iedere 2 weken")
);
$repeat_type = array(
	"0" => gettext("nvt"),
	"M" => gettext("iedere maand"),
	"Y" => gettext("ieder jaar")
);
$notifytime = array(
	mktime(1,15,0,1,1,1970) => "15 ".gettext("minuten"),
	mktime(1,30,0,1,1,1970) => "30 ".gettext("minuten"),
	mktime(2,0,0,1,1,1970)  => "1 ".gettext("uur"),
	mktime(3,0,0,1,1,1970)  => "2 ".gettext("uur"),
	mktime(6,0,0,1,1,1970)  => "5 ".gettext("uur"),
	mktime(0,0,1,1,2,1970)  => "1 ".gettext("dag"),
	mktime(0,0,1,1,3,1970)  => "2 ".gettext("dagen"),
	mktime(0,0,1,1,6,1970)  => "5 ".gettext("dagen"),
	mktime(0,0,1,1,8,1970)  => "1 ".gettext("week"),
	mktime(0,0,1,1,15,1970) => "2 ".gettext("weken"),
	mktime(0,0,1,1,22,1970) => "3 ".gettext("weken"),
	mktime(0,0,1,2,1,1970)  => "1 ".gettext("maand")
);

$app_type = array(
	1 => gettext("standaard"),
	2 => gettext("prive"),
	3 => gettext("vakantie"),
	4 => gettext("bijzonder verlof"),
	5 => gettext("ziek")
);

$calendar_data = new Calendar_data();
$calendar_item = $calendar_data->getCalendarItemById($id);

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
if ($_REQUEST["address_id"]) {
	$calendar_item["address_id"] = sprintf("%d", $_REQUEST["address_id"]);
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
/* and projectname */
$project = new Project_data();
$projectinfo = $project->getProjectById($calendar_item["project_id"]);
$projectname = $projectinfo[0]["name"];

$output = new Layout_output();
$output->layout_page(gettext("agenda"), 1);

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
if ($_REQUEST["user"]) {
	$output->addHiddenField("appointment[user]", (int)$_REQUEST["user"]);
	$subtitle = gettext("afspraak voor ").$users[$_REQUEST["user"]];
} else {
	$output->addHiddenField("appointment[user]", $_SESSION["user_id"]);
	$subtitle = gettext("afspraak voor ").$users[$_SESSION["user_id"]];
}
$venster_settings = array(
	"title"    => gettext("agenda"),
	"subtitle" => $subtitle 
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$table = new Layout_table(array("cellspacing"=>"1"));
	$table->addTableRow();
		$table->insertTableData(gettext("datum"), "", "header");

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
				$table_date->addCode(gettext("van")."&nbsp;");
				$table_date->addSelectField("appointment[from_hour]", $hours_to, $calendar_item["begin_hour"],0);
				$table_date->addCode(" : ");
				$table_date->addSelectField("appointment[from_min]", $minutes_to, $calendar_item["begin_min"],0);
				$table_date->addTag("br");
				$table_date->addCode(gettext("tot")."&nbsp;");
				$table_date->addSelectField("appointment[to_hour]", $hours_to, $calendar_item["end_hour"],0);
				$table_date->addCode(" : ");
				$table_date->addSelectField("appointment[to_minute]", $minutes_to, $calendar_item["end_min"],0);
				$table_date->addTag("br");
				$table_date->addCode(gettext("All day event "));
				$table_date->addCheckBox("appointment[is_event]",1,$calendar_item["is_event"]);
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
		$table->insertTableData(gettext("herhalen"), "", "header");
		$table->addTableData("", "data");
			$table1 = new Layout_table(array("width" => "100%"));
			$table1->addTableRow();
				$table1->addTableData("", "data");
					$table1->addSelectField("appointment[is_repeat]", $repeat, $calendar_item["is_repeat"]);
					$table1->addSelectField("appointment[repeat_type]", $repeat_type, $calendar_item["repeat_type"]);
				$table1->endTableData();
				$table1->addTableData(array("align" => "right"), "data");
					$table1->insertAction("ftype_html", gettext("gebruikt html"), "javascript: init_editor('convert_html');", "convert_html");
					$table1->insertAction("back", gettext("terug"), "javascript:window.close();");
					if ($id) {
						$table1->insertAction("delete", gettext("verwijderen"), sprintf("javascript:calendaritem_remove(%d);", $id));
					}
					$table1->addTag("span", array("id"=>"action_save_top", "style"=>"visibility: hidden;"));
					$table1->insertAction("ok", gettext("opslaan"), sprintf("javascript:calendaritem_save(%d);", $id));
					$table1->endTag("span");
				$table1->endTableData();
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
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
		$table->insertTableData(gettext("onderwerp"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("appointment[subject]", $calendar_item["subject"], array("style"=>"width: 500px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("omschrijving"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("appointment[description]", $calendar_item["description"], array("style"=>"width: 500px; height: 200px;"), "contents");

			$editor = new Layout_editor();
			$table->addCode( $editor->generate_editor(1) );

		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("locatie"), "", "header");
		$table->addTableData("", "data");
			$table_location = new Layout_table();
			$table_location->addTableRow();
				$table_location->addTableData();
					$table_location->addTextField("appointment[location]", $calendar_item["location"]);
				$table_location->endTableData();
				$table_location->addTableData(array("align"=>"left"), "data");
					$table_location->addSpace(3);
					$table_location->addTag("b");
					$table_location->addCode(gettext("afstand"));
					$table_location->endTag("b");
					$table_location->addSpace(2);
					$table_location->addTextField("appointment[kilometers]", $calendar_item["kilometers"]);
					$table_location->addSpace(2);
					$table_location->addCode(gettext("km"));
					$table_location->addSpace(2);
					$table_location->insertLink(gettext("routeplanner"), array("href"=>"http://www.routenet.nl", "target"=>"_blank"));
				$table_location->endTableData();
			$table_location->endTableRow();
			$table_location->addTableRow();
				$table_location->addTableData();
					$table_location->addCode(gettext("declarabele kilometers"));
					$table_location->addCheckBox("appointment[deckm]", 1, $calendar_item["deckm"]);
					$table_location->addCode(gettext("ja"));
				$table_location->endTableData();
			$table_location->endTableRow();
			$table_location->endTable();
			$table->addCode( $table_location->generate_output() );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("belangrijke afspraak"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("appointment[is_important]",1,$calendar_item["is_important"]);
			$table->addSelectField("appointment[notifytime]", $notifytime, $calendar_item["notifytime"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("niet storen"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("appointment[is_dnd]",1,$calendar_item["is_dnd"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("type afspraak"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("appointment[app_type]", $app_type, $calendar_item["app_type"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("groepsafspraak"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("appointment[is_group]", $groups, $calendar_item["is_group"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("andere gebruiker(s)"), "", "header");
		$table->addTableData("", "data");
			$useroutput = new User_output();
			$table->addHiddenField("appointment[user_id]", $calendar_item["extra_users"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("appointmentuser_id", $calendar_item["extra_users"], 1) );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("relatie"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("appointment[address_id]", $calendar_item["address_id"]);
			$table->insertTag("span", $relname, array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_project"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("appointment[project_id]", $calendar_item["project_id"]);
				$table->insertTag("span", $projectname, array("id"=>"searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("wijzigen"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("laatst gewijzigd door"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($calendar_item["h_modified_by"]." ".gettext("op")." ".$calendar_item["h_modified"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("melden aan gebruiker(s)"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("appointment[notify_user]", 1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("melden aan relatie"), "", "header");
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
			$table->insertAction("back", gettext("terug"), "javascript:window.close();");
			if ($id) {
				$table->insertAction("delete", gettext("verwijderen"), sprintf("javascript:calendaritem_remove(%d);", $id));
			}
			$table->addTag("span", array("id"=>"action_save", "style"=>"visibility: hidden;"));
			$table->insertAction("ok", gettext("opslaan"), sprintf("javascript:calendaritem_save(%d);", $id));
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
$output->exit_buffer();
?>
