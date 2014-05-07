<?php
/**
 * Covide Groupware-CRM Project showHours
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
if (!class_exists("Project_output")) {
	exit("no class definition found");
}
if ($_REQUEST["start_day"]) {
	$start = mktime(0, 0, 0, $_REQUEST["start_month"], $_REQUEST["start_day"], $_REQUEST["start_year"]);
}
if ($_REQUEST["end_day"]) {
	$end = mktime(0, 0, 0, $_REQUEST["end_month"], $_REQUEST["end_day"]+1, $_REQUEST["end_year"]);
}
if ($_REQUEST["user"]) {
	$user = (int)$_REQUEST["user"];
}
for ($i=1; $i<=31; $i++) {
	$days[$i] = $i;
}
for ($i=1; $i<=12; $i++) {
	$months[$i] = $i;
}
for ($i=date("Y")-10; $i<=date("Y"); $i++) {
	$years[$i] = $i;
}
$calendar = new Calendar_output();
/* get the project folder in filesys */
$filesys_data = new Filesys_data();
$filesysfolder = $filesys_data->getProjectFolder($_REQUEST["id"]);
$filesysinfo = $filesys_data->getFolders(array("ids" => $filesysfolder));
unset($filesys_data);

/* get the archive id of mailfolders */
$email_data = new Email_data();
$archive_mail = $email_data->getSpecialFolder("Archief", 0);
//ugly, should move to mail object
$q = sprintf("SELECT COUNT(mail_messages.id) FROM mail_messages inner join mail_projects on mail_messages.id = mail_projects.message_id WHERE mail_messages.folder_id = %d AND mail_projects.project_id = %d", $archive_mail["id"], $_REQUEST["id"]);
$r = sql_query($q);
$mailcount = sql_result($r, 0);

$projectdata = new Project_data();
$projectinfo = $projectdata->getProjectById($_REQUEST["id"]);

$user_data = new User_data();
$userinfo = $user_data->getUserPermissionsById($_SESSION["user_id"]);

if ($projectinfo[0]["group_id"]) {
	$projectmaster = $projectdata->getProjectById($projectinfo[0]["group_id"], 1);
}

$hoursaccess = 0;
if ($userinfo["xs_projectmanage"]) {
	$hoursaccess = 1;
}

$notes_data = new Note_data();
$notes_info = $notes_data->getNotes(array("project_id" => $projectinfo[0]["id"], "user_id" => "all", "no_limit" => 1, "note_type" => "all"));
$todo_data = new Todo_data();
$todo_info = $todo_data->getTodosByProjectId($projectinfo[0]["id"]);
$calendar_data = new Calendar_data();
$calendar_items = $calendar_data->getAppointmentsBySearch(array("all" => 1, "project_id" => $projectinfo[0]["id"], "sortorder" => "DESC", "max_hits" => 100));
$sales_data = new Sales_data();
$sales_items = $sales_data->getSalesBySearch(array("no_limit" => 1, "project_id" => $projectinfo[0]["id"]));
$support_data = new Support_data();
$support_items = $support_data->getSupportItems(array("project_id" => $projectinfo[0]["id"], "active" => -1));

/* kilometers */
if (!$start) {
	$kmstart = (int)$projectinfo[0]["lfact"]+1;
} else {
	$kmstart = $start;
}
if (!$end) {
	$kmend = time();
} else {
	$kmend = $end;
}
$kmitems = $calendar_data->getKmItems(array("allusers" => 1, "start" => $kmstart, "end" => $kmend, "noreg" => 1, "project_id" => $projectinfo[0]["id"]));
//communication items
if ($_REQUEST["showcomm"] == 1) {
	$limit_default = "";
	if (!$start || !$end) {
		$limit_default = " LIMIT 5";
		$start = 0;
		$end = time();
	}
	$commitems = array();
	$sql = sprintf("SELECT id, timestamp, user_id, subject, 'note' as type FROM notes WHERE project_id = %1\$d AND timestamp BETWEEN %3\$d AND %4\$d
		UNION
		SELECT id, timestamp, user_id, subject, 'todo' as type FROM todo WHERE project_id = %1\$d AND timestamp BETWEEN %3\$d AND %4\$d
		UNION
		SELECT id, timestamp_start as timestamp, calendar_user.user_id as user_id, subject, 'calendar' as type FROM calendar,calendar_user WHERE calendar.id = calendar_user.calendar_id AND project_id = %1\$d AND timestamp_start BETWEEN %3\$d AND %4\$d
		UNION
		SELECT id, timestamp_proposal as timestamp, user_sales_id as user_id, subject, 'sales' as type FROM sales WHERE project_id = %1\$d AND timestamp_proposal BETWEEN %3\$d AND %4\$d
		UNION
		SELECT id, IF(date_received, date_received, date) as timestamp, user_id, subject, 'mail' as type FROM mail_messages INNER JOIN mail_projects ON mail_messages.id = mail_projects.message_id WHERE folder_id = %2\$d AND mail_projects.project_id = %1\$d AND date_received BETWEEN %3\$d AND %4\$d
		UNION
		SELECT id, timestamp, user_id, description AS subject, 'support' as type FROM issues WHERE project_id = %1\$d AND timestamp BETWEEN %3\$d AND %4\$d
		ORDER BY timestamp DESC %5\$s
		", $projectinfo[0]["id"], $archive_mail["id"], $start, $end, $limit_default);
	$res = sql_query($sql);
	while ($row = sql_fetch_array($res)) {
		$row["h_type"] = gettext($row["type"]);
		$row["h_time"] = date("d-m-Y H:i:s", $row["timestamp"]);
		$row["h_username"] = $user_data->getUsernameById($row["user_id"]);
		switch($row["type"]) {
		case "calendar":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=calendar&action=show_info&id=%d&user_id=%d');", $row["id"], $row["user_id"]);
			break;
		case "mail":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=email&action=show_info&id=%d');", $row["id"]);
			break;
		case "todo":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=todo&action=show_info&id=%d');", $row["id"]);
			break;
		case "note":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=note&action=show_info&id=%d');", $row["id"]);
			break;
		case "sales":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=sales&action=show_info&id=%d');", $row["id"]);
			break;
		case "support":
			$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=support&action=show_info&id=%d');", $row["id"]);
			break;
		}
		$commitems[] = $row;
	}
} else {
	$commitems = "";
}
$commitems_planning = array();
$sql = sprintf("SELECT id, timestamp, user_id, subject, 'note' as type FROM notes WHERE project_id = %1\$d AND timestamp >= %3\$d
	UNION
	SELECT id, timestamp, user_id, subject, 'todo' as type FROM todo WHERE project_id = %1\$d AND timestamp >= %3\$d
	UNION
	SELECT id, timestamp_start as timestamp, calendar_user.user_id as user_id, subject, 'calendar' as type FROM calendar,calendar_user WHERE calendar.id = calendar_user.calendar_id AND project_id = %1\$d AND timestamp_start >= %3\$d
	UNION
	SELECT id, timestamp_proposal as timestamp, user_sales_id as user_id, subject, 'sales' as type FROM sales WHERE project_id = %1\$d AND timestamp_proposal >= %3\$d
	UNION
	SELECT id, IF(date_received, date_received, date) as timestamp, user_id, subject, 'mail' as type FROM mail_messages WHERE folder_id = %2\$d AND project_id = %1\$d AND date_received >= %3\$d
	UNION
	SELECT id, timestamp, user_id, description AS subject, 'support' as type FROM issues WHERE project_id = %1\$d AND timestamp >= %3\$d
	ORDER BY timestamp DESC
	", $projectinfo[0]["id"], $archive_mail["id"], time());
$res = sql_query($sql);
while ($row = sql_fetch_array($res)) {
	$row["h_type"] = gettext($row["type"]);
	$row["h_time"] = date("d-m-Y H:i:s", $row["timestamp"]);
	$row["h_username"] = $user_data->getUsernameById($row["user_id"]);
	switch($row["type"]) {
	case "calendar":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=calendar&action=show_info&id=%d&user_id=%d');", $row["id"], $row["user_id"]);
		break;
	case "mail":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=email&action=show_info&id=%d');", $row["id"]);
		break;
	case "todo":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=todo&action=show_info&id=%d');", $row["id"]);
		break;
	case "note":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=note&action=show_info&id=%d');", $row["id"]);
		break;
	case "sales":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=sales&action=show_info&id=%d');", $row["id"]);
		break;
	case "support":
		$row["infolink"] = sprintf("javascript: loadXML('index.php?mod=support&action=show_info&id=%d');", $row["id"]);
		break;
	}
	$commitems_planning[] = $row;
}

if (array_key_exists("showall", $_REQUEST) && $_REQUEST["showall"] == 1 && ($userinfo["xs_projectmanage"] || $_SESSION["user_id"] == $projectinfo[0]["manager"] || $_SESSION["user_id"] == $projectinfo[0]["executor"])) {
	$_SESSION["showallprojectdetails"][$projectinfo[0]["id"]] = 1;
}
if (array_key_exists("showall", $_REQUEST) && $_REQUEST["showall"] == 0 && ($userinfo["xs_projectmanage"] || $_SESSION["user_id"] == $projectinfo[0]["manager"] || $_SESSION["user_id"] == $projectinfo[0]["executor"])) {
	$_SESSION["showallprojectdetails"][$projectinfo[0]["id"]] = 0;
}
if (!$projectdata->dataCheckPermissions($projectinfo[0]) && !$projectdata->dataCheckPermissions($projectmaster[0])) {
	$output = new Layout_output();
	$output->layout_page(gettext("Project"));

	$venster = new Layout_venster(array(
		"title" => gettext("Project Card"),
		"subtitle" => gettext("No permissions")
	));
	$venster->addVensterData();
		$venster->addCode(gettext("You have no permissions to access the following project").": ");
		$venster->insertTag("b", $projectinfo[0]["name"]);
		$venster->addTag("br");

		$history = new Layout_history();
		$link = $history->generate_history_call();
		$venster->addCode($link);

		$venster->insertAction("back", gettext("back"), "javascript: history_goback();");
	$venster->endVensterData();

	$table = new Layout_table();

	$output->addCode($table->createEmptyTable($venster->generate_output()));
	$output->exit_buffer();
}
//XXX: The bulklist and misclist should get dates so we can filter those as well
$listoptions = array("projectid" => $_REQUEST["id"]);
$bulklistoptions = array("projectid" => $_REQUEST["id"], "bulk" => 1);
$misclistoptions = array("projectid" => $_REQUEST["id"], "misc" => 1);
if ($start) {
	$listoptions["start"] = $start;
	$bulklistoptions["start"] = $start;
	if ($end) {
		$listoptions["end"] = $end;
		$bulklistoptions["end"] = $end;
	}
} else {
	$listoptions["lfact"] = $projectinfo[0]["lfact"];
	$bulklistoptions["lfact"] = $projectinfo[0]["lfact"];
}

if ($user) {
	$listoptions["user"] = $user;
	$bulklistoptions["user"] = $user;
	$misclistoptions["user"] = $user;
	$kmitem["user_name"] = $user;
}
$totallistoptions = $listoptions;
$totallistoptions["lfact"] = 0;

$hourslist = $projectdata->getHoursList($listoptions);
$bulklist = $projectdata->getHoursList($bulklistoptions);
$misclist = $projectdata->getHoursList($misclistoptions);


$hourslist_total = $projectdata->getHoursList($totallistoptions);
$bulklist_total = $projectdata->getHoursList($bulklistoptions);
$misclist_total = $projectdata->getHoursList($misclistoptions);


$hoursviewdata = $hourslist["items"];
$bulkviewdata = $bulklist["items"];
$miscviewdata = $misclist["items"];

// put data in array for foreach
$allCosts[] = $hoursviewdata;
$allCosts[] = $bulkviewdata;
$allCosts[] = $miscviewdata;

$users = array();
//funcion calculate costs per user( costs km is calculate in the table of km)
foreach ($allCosts as $key=>$value) {
	//if there are costs made value is am array
	if (is_array($value)) {
		foreach ($value as $k=>$v) {
			$userId = $v["user_id"];
			//per user id count the costs
			if (in_array($userId, $users)) {
				$count[$userId] = $count[$userId] + $v["costs"];
			} else {
				$users[] = $userId;
				$ar_userName[] = $v["user_name"];
				$count[$userId] = $v["costs"];
			}
		}
	}
}

$grand_total_purchase_raw = $bulklist["total_purchase_raw"] + $hourslist["total_purchase_raw"] + $misclist["total_purchase_raw"];
$grand_total_marge_raw = $bulklist["total_marge_raw"] + $hourslist["total_marge_raw"] + $misclist["total_marge_raw"];
$grand_total_hours_raw = str_replace(array(".25",".50",".75"), array(":15",":30",":45"), ($bulklist["total_hours_raw"] + $hourslist["total_hours_raw"]));
$grand_total_service_raw = str_replace(array(".25",".50",".75"), array(":15",":30",":45"), ($bulklist["total_service_raw"] + $hourslist["total_service_raw"]));
$grand_total_costs_raw = $bulklist["total_costs_raw"] + $hourslist["total_costs_raw"] + $misclist["total_costs_raw"];
$total_km_purchase = $total_km_marge = $total_km_costs = 0;

if (substr_count($grand_total_hours_raw, ":") == 0) {
	$grand_total_hours_raw .= ":00";
}
if (substr_count($grand_total_service_raw, ":") == 0) {
	$grand_total_service_raw .= ":00";
}

if (!$this->has_declaration) {
	$hoursviewdata[] = array(
		"human_start_date" => "<b>".gettext("total")."</b>",
		"hours_bill" => "<b>".$hourslist["total_hours_billable"]."</b>",
		"hours_service" => "<b>".$hourslist["total_hours_service"]."</b>",
		"purchase" => "<b>".$hourslist["total_purchase"]."</b>",
		"marge" => "<b>".$hourslist["total_marge"]."</b>",
		"costs" => "<b>".$hourslist["total_costs"]."</b>",
	);
	/* define hourslist view and map data */
	$view_hourlist = new Layout_view();
	$view_hourlist->addData($hoursviewdata);
	$view_hourlist->addMapping(gettext("date"), "%human_start_date");
	$view_hourlist->addMapping(gettext("time"), "%hours_bill");
	$view_hourlist->addMapping("", "%%complex_flip", array("class" => "project_empty"));
	$view_hourlist->addMapping(gettext("service hours"), "%hours_service", array("class" => "project_service_hours"));
	$view_hourlist->addMapping(gettext("user"), "%user_name", array("class" => "project_user"));
	$view_hourlist->addMapping(gettext("activity"), "%activityname", array("class" => "project_activity"));
	$view_hourlist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	if ($hoursaccess) {
		$view_hourlist->addMapping(gettext("purchase"), "%purchase", array("align" => "right", "class" => "project_purchase"));
		$view_hourlist->addMapping(gettext("marge"), "%marge", array("align" => "right", "class" => "project_margin"));
		$view_hourlist->addMapping(gettext("price"), "%costs", array("align" => "right", "class" => "project_price"));
		$view_hourlist->setHTMLField("purchase");
		$view_hourlist->setHTMLField("marge");
		$view_hourlist->setHTMLField("costs");
	}
	$view_hourlist->addMapping(gettext("actions"), "%%complex_actions", array("class" => "project_actions"));
	$view_hourlist->setHTMLField("human_start_date");
	$view_hourlist->setHTMLField("hours_bill");
	$view_hourlist->setHTMLField("hours_service");
	$view_hourlist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");"),
			"check" => "%id"
		)
	));
	$view_hourlist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=reg_input&id_reg=", "%id", "', 'edit', 750, 520, 1);"),
			"check" => "%id",
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');"),
			"check" => "%id",
		),
	));

	// bulk added items
	$bulkviewdata[] = array(
		"hours_bill" => "<b>".$bulklist["total_hours_billable"]."</b>",
		"hours_service" => "<b>".$bulklist["total_hours_service"]."</b>",
		"purchase" => "<b>".$bulklist["total_purchase"]."</b>",
		"marge" => "<b>".$bulklist["total_marge"]."</b>",
		"costs" => "<b>".$bulklist["total_costs"]."</b>",
	);

	$view_bulklist = new Layout_view();
	$view_bulklist->addData($bulkviewdata);
	$view_bulklist->addMapping(gettext("date"), "%human_date", array("class" => "project_date"));
	$view_bulklist->addMapping(gettext("time"), "%hours_bill", array("class" => "project_time"));
	$view_bulklist->addMapping("", "%%complex_flip", array("class" => "project_empty"));
	$view_bulklist->addMapping(gettext("service hours"), "%hours_service", array("class" => "project_service_hours"));
	$view_bulklist->addMapping(gettext("user"), "%user_name", array("class" => "project_user"));
	$view_bulklist->addMapping(gettext("activity"), "%activityname", array("class" => "project_activity"));
	$view_bulklist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	if ($hoursaccess) {
		$view_bulklist->addMapping(gettext("purchase"), "%purchase", array("align" => "right", "class" => "project_purchase"));
		$view_bulklist->addMapping(gettext("marge"), "%marge", array("align" => "right", "class" => "project_margin"));
		$view_bulklist->addMapping(gettext("price"), "%costs", array("align" => "right", "class" => "project_price"));
		$view_bulklist->setHTMLField("purchase");
		$view_bulklist->setHTMLField("marge");
		$view_bulklist->setHTMLField("costs");
	}
	$view_bulklist->addMapping(gettext("actions"), "%%complex_actions", array("class" => "project_actions"));
	$view_bulklist->setHTMLField("hours_bill");
	$view_bulklist->setHTMLField("hours_service");
	$view_bulklist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");"),
			"check" => "%id",
		)
	));
	$view_bulklist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=batch_reg_input&id=", "%id", "', 'edit', 750, 520, 1);"),
			"check" => "%id",
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');"),
			"check" => "%id",
		),
	));

	// non hour items
	$miscviewdata[] = array(
		"user_name" => "<b>".gettext("total")."</b>",
		"costs_service" => "<b>".$misclist["total_costs_service"]."</b>",
		"purchase" => "<b>".$misclist["total_purchase"]."</b>",
		"marge" => "<b>".$misclist["total_marge"]."</b>",
		"costs" => "<b>".$misclist["total_costs"]."</b>",
	);
	$view_misclist = new Layout_view();
	$view_misclist->addData($miscviewdata);
	$view_misclist->addMapping(gettext("user"), "%user_name", array("class" => "project_time"));
	if ($hoursaccess) {
		$view_misclist->addMapping("", "%%complex_flip", array("class" => "project_empty"));
		$view_misclist->addMapping(gettext("service"), "%costs_service", array("class" => "project_service_hours"));
	}
	$view_misclist->addMapping(gettext("type"), "%activityname", array("class" => "project_type"));
	$view_misclist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	if ($hoursaccess) {
		$view_misclist->addMapping(gettext("purchase"), "%purchase", array("align" => "right", "class" => "project_purchase"));
		$view_misclist->addMapping(gettext("marge"), "%marge", array("align" => "right", "class" => "project_margin"));
		$view_misclist->addMapping(gettext("price"), "%costs", array("align" => "right", "class" => "project_price"));
		$view_misclist->setHTMLField("costs_service");
		$view_misclist->setHTMLField("purchase");
		$view_misclist->setHTMLField("marge");
		$view_misclist->setHTMLField("costs");
	}
	$view_misclist->addMapping(gettext("actions"), "%%complex_actions", array("class" => "project_actions") );
	$view_misclist->setHTMLField("user_name");
	$view_misclist->defineComplexMapping("complex_flip", array(
	array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");"),
			"check" => "%id",
		)
	));
	$view_misclist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=misc_reg_input&id=", "%id", "', 'edit', 750, 520, 1);"),
			"check" => "%id",
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');"),
			"check" => "%id",
		),

	));

		$table = new Layout_table(array("width" => "100%"));
		if (is_array($commitems)) {
			$table->addTableRow();
				$table->insertTableData(gettext("history"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2));
					$view_com = new Layout_view();
					$view_com->addData($commitems);
					$view_com->addMapping(gettext("date"), "%h_time");
					$view_com->addMapping(gettext("type"), "%h_type");
					$view_com->addMapping(gettext("user"), "%h_username");
					$view_com->addMapping(gettext("subject"), "%%subject");
					$view_com->defineComplexMapping("subject", array(
						array(
							"type" => "link",
							"link" => "%infolink",
							"text" => "%subject"
						),
					));
					$table->addCode($view_com->generate_output());
					unset($view_com);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("&nbsp;", array("colspan" => 2));
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData(gettext("planning"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2));
				$view_com = new Layout_view();
				$view_com->addData($commitems_planning);
				$view_com->addMapping(gettext("date"), "%h_time");
				$view_com->addMapping(gettext("type"), "%h_type");
				$view_com->addMapping(gettext("user"), "%h_username");
				$view_com->addMapping(gettext("subject"), "%%subject");
				$view_com->defineComplexMapping("subject", array(
					array(
						"type" => "link",
						"link" => "%infolink",
						"text" => "%subject"
					),
				));
				$table->addCode($view_com->generate_output());
				unset($view_com);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan" => 2));
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("kilometers"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2));
				$table_km = new Layout_table(array("width" => "100%", "class" => "view_header table_data", "cellspacing" => 1, "cellpadding" => 3));
				$table_km->addTableRow(array("class" => "list_record"));
					$table_km->insertTableHeader(gettext("date"), array("class" => "project_time list_header_center"));
					$table_km->insertTableHeader(gettext("user"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("subject"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("location"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("kilometers"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("purchase"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("marge"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("price"), array("class" => "list_header_center"));
					$table_km->insertTableHeader(gettext("actions"), array("class" => "list_header_center"));
				$table_km->endTableRow();
				foreach($kmitems as $userid => $kminfo) {
					if (is_array($kminfo["items"]) && ($userid == $user || $user == "")) {
						//put username in User for charts users
						$users[] = $kminfo["username"];
						$kminfo["items"] = array_reverse($kminfo["items"]);
						$table_km->addTableRow();
							$table_km->insertTableData($kminfo["username"], array("colspan" => 9), "header");
						$table_km->endTableRow();
						foreach ($kminfo["items"] as $kmitem) {
							$table_km->addTableRow(array("class" => "list_record"));
								$table_km->insertTableData($kmitem["human_date"], array("class" => "list_data_clean valign_top"));
								$table_km->insertTableData($kmitem["user_name"], array("class" => "list_data_clean valign_top"));
								$table_km->insertTableData($kmitem["subject"], array("class" => "list_data_clean valign_top"));
								$table_km->insertTableData($kmitem["location"], array("class" => "list_data_clean valign_top"));
								$table_km->insertTableData($kmitem["kilometers"], array("class" => "list_data_clean valign_top", "align" => "right"));
								if ($kmitem["deckm"]) {
									$costs = ($projectinfo[0]["kilometer_allowance"]/100)*$kmitem["kilometers"];
									$purchase = $kmitem["costs"];
									$marge = $costs-$purchase;
									$total_km_purchase += $purchase;
									$total_km_marge += $marge;
									$total_km_costs += $costs;
								} else {
									$purchase = $costs = $marge = 0;
								}
								//count total costs km per user on total costs user
								$userId = $kmitem["user_id"];
								if (in_array($userId, $users)) {
									$count[$userId] = $count[$userId] + $costs;
								} else {
									$users[] = $userId;
									$ar_userName[] = $v["user_name"];
									$count[$userId] = $userId;
								}
								$table_km->insertTableData(number_format($purchase, 2), array("class" => "list_data_clean project_purchase valign_top", "align" => "right"));
								$table_km->insertTableData(number_format($marge, 2), array("class" => "list_data_clean project_margin valign_top", "align" => "right"));
								$table_km->insertTableData(number_format($costs, 2), array("class" => "list_data_clean project_price valign_top", "align" => "right"));
								$table_km->addTableData(array("class" => "list_data_clean project_actions valign_top"), "");
								$table_km->insertAction("edit", gettext("edit"), sprintf("javascript: popup('index.php?mod=calendar&action=edit&id=%d&user=%d');", $kmitem["id"], $kmitem["user_id"]));
								$table_km->endTableData();
							$table_km->endTableRow();
						}
					}
				}
				$table_km->addTableRow(array("class" => "list_record"));
						$table_km->addTableData(array("class" => "data", "colspan" => 5));
							$table_km->addCode('<b>'.gettext("total").'</b>');
						$table_km->endTableData();
						$table_km->addTableData(array("class" =>"project_purchase", "align" => "right"));
						$table_km->addCode('<b>'.number_format($total_km_purchase, 2).'</b>');
						$table_km->endTableData();
						$table_km->addTableData(array("class" => "project_margin", "align" => "right"));
							$table_km->addCode('<b>'.number_format($total_km_marge, 2).'</b>');
						$table_km->endTableData();
						$table_km->addTableData(array("class" => "project_price", "align" => "right"));
							$table_km->addCode('<b>'.number_format($total_km_costs, 2).'</b>');
						$table_km->endTableData();
						$table_km->addTableData(array("class" => "project_actions", "align" => "right"));
						$table_km->endTableData();
				$table_km->endTableRow();
				$table_km->endTable();
				$table->addCode($table_km->generate_output());
				unset($table_km);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan" => 2));
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData(gettext("registered hours"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2));
				$table->addCode($view_hourlist->generate_output());
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan" => 2));
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("bulk added hours"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2));
				$table->addCode($view_bulklist->generate_output());
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan" => 2));
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("other project costs"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2));
				$table->addCode($view_misclist->generate_output());
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan" => 2));
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("totals"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("total time").": ".$grand_total_hours_raw);
				$table->addSpace(2);
				$table->addCode(gettext("total service hours").": ".$grand_total_service_raw);
			$table->endTableData();
			$table->addTableData(array("align"=>"right"), "header");
				if ($hoursaccess) {
					$table->addCode(gettext("total purchase").":&nbsp; &euro; ".number_format($grand_total_purchase_raw+$total_km_purchase, 2, ".", ","));
					$table->addSpace(12);
					$table->addCode(gettext("total marge").":&nbsp; &euro; ".number_format($grand_total_marge_raw+$total_km_marge, 2, ".", ","));
					$table->addSpace(12);
					$table->addCode(gettext("total costs").":&nbsp; &euro; ".number_format($grand_total_costs_raw+$total_km_costs, 2, ".", ","));
					$table->addSpace();
				}
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		// if start and end are set, give them to the export as well
		if ($hoursaccess) {
			if ($start && $end) {
				$table->insertAction("file_download", gettext("export"), sprintf("?mod=project&action=exporthours&id=%d&start=%d&end=%d", $projectinfo[0]["id"], $start, $end));
				$table->addSpace();
				$table->insertAction("file_xml", gettext("export"), sprintf("?mod=project&action=exporthoursxml&id=%d&start=%d&end=%d", $projectinfo[0]["id"], $start, $end));
				$table->addSpace();
				$table->insertAction("print", gettext("print"), "javascript: window.print();");
			} else {
				$table->insertAction("file_download", gettext("export"), sprintf("?mod=project&action=exporthours&id=%d", $projectinfo[0]["id"]));
				$table->addSpace();
				$table->insertAction("file_xml", gettext("export"), sprintf("?mod=project&action=exporthoursxml&id=%d", $projectinfo[0]["id"]));
				$table->addSpace();
				$table->insertAction("print", gettext("print"), "javascript: window.print();");
			}
		}
		$hourlist = $table->generate_output();
		unset($table);

} else {
	/* declaration module {{{ */
	$declaration_data = new ProjectDeclaration_data();
	$registration_data = $declaration_data->getRegistrationItems($projectinfo[0]["id"], $_REQUEST["history"]);

	/* define hourslist view and map data */
	$view_hourlist = new Layout_view();
	$view_hourlist->addData($registration_data);
	$view_hourlist->addMapping(gettext("date"), "%human_date");
	$view_hourlist->addMapping(gettext("declaration type"), "%declaration_type");
	$view_hourlist->addMapping(gettext("user"), "%%complex_username");
	$view_hourlist->addMapping(gettext("description"), "%description");
	$view_hourlist->addMapping(gettext("kilometers"), "%kilometers");
	$view_hourlist->addMapping(gettext("minutes"), "%time_units");
	$view_hourlist->addMapping(gettext("price ex btw"), "%price");
	$view_hourlist->addMapping(gettext("% NCNP"), "%perc_NCNP");
	$view_hourlist->addMapping(gettext("% btw"), "%perc_btw");
	$view_hourlist->addMapping(gettext("total price"), "%total_price");
	$view_hourlist->addMapping("%%complex_flip_header", "%%complex_flip", "right");

	if (!$_REQUEST["history"]) {
		$output_alt = new Layout_output();
		$view_hourlist->defineComplexMapping("complex_flip_header", array(
			array(
				"text" => "<p align=\"right\">"
			),
			array(
				"type" => "action",
				"src" => "delete",
				"link" => "javascript: regitem_delete_multi();"
			),
			array(
				"text" => $output_alt->insertCheckbox(array("checkbox_regitem_toggle_all"), "1", 0, 1)
			),
			array(
				"text" => "</p>"
			)
		));
		$view_hourlist->defineComplexMapping("complex_flip", array(
			array(
				"type" => "action",
				"src" => "edit",
				"link" => array("javascript:document.location.href='index.php?mod=projectdeclaration&action=register_item&project_id=".$projectinfo[0]["id"]."&id=", "%id", "';")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"link" => array("javascript: delete_registration(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "info",
				"link" => array("javascript: alert('", gettext("input by user: "), "%user_name_input", "');")
			),
			array(
				"text"  => $output_alt->insertCheckbox(array("checkbox_regitem[","%id","]"), "1", 0, 1)
			)
		));
	} else {
		$view_hourlist->defineComplexMapping("complex_flip", array(
			array(
				"type" => "action",
				"src"  => "info",
				"link" => array("javascript: alert('", gettext("input by user: "), "%user_name_input", "');")
			)
		));
	}
	$view_hourlist->defineComplexMapping("complex_username", array(
		array(
			"type" => "link",
			"text" => "%user_name",
			"link" => array("javascript: view_user(", "%id", ");")
		)
	));

	$table = new Layout_table(array("width" => "100%"));
	$table->addTableRow();
		$table->addTableData();
			$table->addCode(gettext("current view").": ");
			$table->addSelectField("declaration_view", $declaration_data->getRegistrationHistory($projectinfo[0]["id"]), $_REQUEST["history"]);
			$table->insertAction("forward", gettext("verder"), sprintf("javascript: location.href='?mod=project&action=showhours&id=%d&master=0&history='+document.getElementById('declaration_view').value", $projectinfo[0]["id"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("registration list"), "", "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addTag("form", array("id" => "regitemform"));
			$table->addCode($view_hourlist->generate_output());
			$table->endTag("form");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$table->load_javascript("classes/projectdeclaration/inc/editProject.js");
	if ($_REQUEST["history"]) {
		$table->insertAction("file_download", gettext("genereer document"), "?mod=projectdeclaration&action=send_batch&project_id=".$projectinfo[0]["id"]."&manager=".$projectinfo[0]["manager"]."&history=".$_REQUEST["history"]);
	}
	$hourlist = $table->generate_output();
	unset($table);
	/* }}} */
}
if ($projectinfo[0]["hours"]) {
	$hour_perc = number_format(($grand_total_hours_raw/$projectinfo[0]["hours"])*100, 2);
} else {
	$hour_perc = 0;
}
if ($projectinfo[0]["budget"]) {
	$cost_perc = number_format((($grand_total_costs_raw+$total_km_costs)/$projectinfo[0]["budget"])*100, 2);
} else {
	$cost_perc = 0;
}

if ($projectinfo[0]["hours"]) {
	$hour_perc_total = number_format((($hourslist_total["total_hours_raw"]+$bulklist_total["total_hours_raw"])/$projectinfo[0]["hours"])*100, 2);
} else {
	$hour_perc_total = 0;
}
if ($projectinfo[0]["budget"]) {
	$cost_perc_total = number_format((($hourslist_total["total_costs_raw"]+$bulklist_total["total_costs_raw"]+$misclist_total["total_costs_raw"]+$total_km_costs)/$projectinfo[0]["budget"])*100, 2);
} else {
	$cost_perc_total = 0;
}

	$table = new Layout_table(array("width"=>"100%", "cellspacing"=>1, "cellpadding"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("Status")." ".gettext("overall hours and budget"), array("colspan" => 3), "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addCode(gettext("hours")."&nbsp;(".($hourslist_total["total_hours_raw"]+$bulklist_total["total_hours_raw"]).":00/".$projectinfo[0]["hours"].")");
			$table->addSpace(3);
		$table->endTableData();
		$table->addTableData(array("width"=>"100%"));
			$table1 = new Layout_table(array(
				"width" => "100%",
				"style" => "border: 1px solid #000000;"
			));
			$table1->addTableRow();
				if ($hour_perc_total > 90) {
					$bgcolor = "red";
				} elseif ($hour_perc_total > 0) {
					$bgcolor = "green";
				} else {
					$bgcolor = "";
				}
				$table1->insertTableData("&nbsp;", array("width"=>$hour_perc_total."%", "style"=>"background-color: $bgcolor;"));
				$table1->insertTableData("&nbsp;");
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
			unset($table1);
		$table->endTableData();
		$table->addTableData(array("align"=>"right"));
			$table->addSpace(3);
			$table->addCode($hour_perc_total."%");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addCode(gettext("budget"));
			if ($hoursaccess) {
				$table->addCode("&nbsp;(".number_format(($hourslist_total["total_costs_raw"]+$bulklist_total["total_costs_raw"]+$misclist_total["total_costs_raw"]+$total_km_costs), 2, ",", ".")."/".number_format($projectinfo[0]["budget"], 2, ",", ".").")");
			}
			$table->addSpace(3);
		$table->endTableData();
		$table->addTableData(array("width"=>"100%"));
			$table1 = new Layout_table(array(
				"width" => "100%",
				"style" => "border: 1px solid #000000;"
			));
			$table1->addTableRow();
				if ($cost_perc_total > 90) {
					$bgcolor = "red";
				} elseif ($cost_perc_total > 0) {
					$bgcolor = "green";
				} else {
					$bgcolor = "";
				}
				$table1->insertTableData("&nbsp;", array("width"=>$cost_perc_total."%", "style"=>"background-color: $bgcolor;"));
				$table1->insertTableData("&nbsp;");
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
			unset($table1);
		$table->endTableData();
		$table->addTableData(array("align"=>"right"));
			$table->addSpace(3);
			$table->addCode($cost_perc_total."%");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$status_total = $table->generate_output();
	unset($table);

	$table = new Layout_table(array("width"=>"100%", "cellspacing"=>1, "cellpadding"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("Status")." ".gettext("hours and budget"), array("colspan" => 3), "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();

			$table->addCode(gettext("hours")."&nbsp;(".$grand_total_hours_raw.":00/".$projectinfo[0]["hours"].")");
			$table->addSpace(3);
		$table->endTableData();
		$table->addTableData(array("width"=>"100%"));
			$table1 = new Layout_table(array(
				"width" => "100%",
				"style" => "border: 1px solid #000000;"
			));
			$table1->addTableRow();
				if ($hour_perc > 90) {
					$bgcolor = "red";
				} elseif ($hour_perc>0) {
					$bgcolor = "green";
				} else {
					$bgcolor = "";
				}
				$table1->insertTableData("&nbsp;", array("width"=>$hour_perc."%", "style"=>"background-color: $bgcolor;"));
				$table1->insertTableData("&nbsp;");
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
			unset($table1);
		$table->endTableData();
		$table->addTableData(array("align"=>"right"));
			$table->addSpace(3);
			$table->addCode($hour_perc."%");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addCode(gettext("budget"));
			if ($hoursaccess) {
				$table->addCode("&nbsp;(".number_format($grand_total_costs_raw+$total_km_costs, 2, ",", ".")."/".number_format($projectinfo[0]["budget"], 2, ",", ".").")");
			}
			$table->addSpace(3);
		$table->endTableData();
		$table->addTableData(array("width"=>"100%"));
			$table1 = new Layout_table(array(
				"width" => "100%",
				"style" => "border: 1px solid #000000;"
			));
			$table1->addTableRow();
				if ($cost_perc > 90) {
					$bgcolor = "red";
				} elseif ($cost_perc > 0) {
					$bgcolor = "green";
				} else {
					$bgcolor = "";
				}
				$table1->insertTableData("&nbsp;", array("width"=>$cost_perc."%", "style"=>"background-color: $bgcolor;"));
				$table1->insertTableData("&nbsp;");
			$table1->endTableRow();
			$table1->endTable();
			$table->addCode($table1->generate_output());
			unset($table1);
		$table->endTableData();
		$table->addTableData(array("align"=>"right"));
			$table->addSpace(3);
			$table->addCode($cost_perc."%");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$status = $table->generate_output();
	unset($table);

/* main venster */
$venster_main = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>($this->has_declaration) ? gettext("file activities") : gettext("projectoverview")));
if ($projectinfo[0]["allow_edit"] == 1) {
	$venster_main->addMenuItem(gettext("edit"), sprintf("javascript: popup('index.php?mod=project&action=edit&id=%d&master=%d');", $projectinfo[0]["id"], $projectinfo[0]["master"]));
}

if ($this->has_declaration) {
	$venster_main->addMenuItem(gettext("register new item"), "javascript: document.location.href='index.php?mod=projectdeclaration&action=register_item&project_id=".$projectinfo[0]["id"]."';");
	$venster_main->addMenuItem(gettext("send declaration"), "javascript: document.location.href='index.php?mod=projectdeclaration&action=send_batch&project_id=".$projectinfo[0]["id"]."&manager=".$projectinfo[0]["manager"]."';");
} else {
	$venster_main->addMenuItem(gettext("input costs"), "javascript: popup('?mod=calendar&action=reg_input&id=0&project_id=".$projectinfo[0]["id"]."', 'reginput', 750, 600, 2);");
	if ($userinfo["xs_projectmanage"] || $_SESSION["user_id"] == $projectinfo[0]["manager"] || $_SESSION["user_id"] == $projectinfo[0]["executor"]) {
		if (!is_array($commitems)) {
			$venster_main->addMenuItem(gettext("show communication items"), "javascript: document.location.href=document.location.href+'&showcomm=1';");
		}
		$venster_main->addMenuItem(gettext("extended information"), "javascript: showExtraLNK('".$_SESSION["showallprojectdetails"][$projectinfo[0]["id"]]."');");
	}
	//$venster_main->addMenuItem(gettext("print")."/".gettext("pdf"), "javascript: popup('index.php?mod=project&action=showprojecthours&id=".$projectinfo[0]["id"]."', 'hour_overview', 750, 600, 1);");
}

if ($projectinfo[0]["group_id"]) {
	$venster_main->addMenuItem(($this->has_declaration) ? gettext("to overview") : gettext("to masterprojects"), "javascript: document.location.href='index.php?mod=project&action=showinfo&id=".$projectinfo[0]["group_id"]."&master=1';");
}
if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
	$venster_main->addMenuItem(gettext("generate document"), "javascript: popup('?mod=projectext&action=extGenerateDocumentTree&id=".$_REQUEST["id"]."', 'generate', 750, 600, 1);");
	$venster_main->addMenuItem(gettext("open networkshare"), "javascript: popup('index.php?mod=projectext&action=netshare&id=".$projectinfo[0]["id"]."', 'share', '800', '600', 0);");
}

$venster_main->addMenuItem(gettext("back"), "javascript: history_goback();");

$venster_main->generateMenuItems();
$venster_main->addVensterData();
	if (!$this->has_declaration) {
		$venster_main->start_javascript();
		$venster_main->addCode("
			function showAdditionalInfo() {
				if (document.getElementById('additionalinfodiv')) {
					document.getElementById('additionalinfodiv').style.display='block';
				}
			}
			function hideAdditionalInfo() {
				if (document.getElementById('additionalinfodiv')) {
					document.getElementById('additionalinfodiv').style.display='none';
				}
			}
			function showHourInfo() {
				if (document.getElementById('hours')) {
					document.getElementById('hours').style.display='block';
				}
			}
			function hideHourInfo() {
				if (document.getElementById('hours')) {
					document.getElementById('hours').style.display='none';
				}
			}
			function showExtra() {
				showAdditionalInfo();
				showHourInfo();
			}
			function hideExtra() {
				hideAdditionalInfo();
				hideHourInfo();
			}
			function toggleAdditionalInfo() {
				if (document.getElementById('additionalinfodiv') && document.getElementById('additionalinfodiv').style.display == 'block') {
					hideAdditionalInfo();
				} else {
					showAdditionalInfo();
				}
			}
			function toggleHourInfo() {
				if (document.getElementById('hours') && document.getElementById('hours').style.display == 'block') {
					hideHourInfo();
				} else {
					showHourInfo();
				}
			}
			function toggleExtra() {
				toggleAdditionalInfo();
				toggleHourInfo();
			}
			function showExtraLNK(hide) {
				if (hide == 1) {
					var showall = 0;
				} else {
					var showall = 1;
				}
				var url = 'index.php?mod=project&action=showhours&id=".$projectinfo[0]["id"]."&showall='+showall;
				document.location.href=url;
			}
			function popupclosed() {
				var url = 'index.php?mod=project&action=showhours&id=".$projectinfo[0]["id"]."';
				document.location.href=url;
			}
		");
		$venster_main->end_javascript();
	}

	$table = new Layout_table(array("border" => 0, "width" => "100%"));
	$table->addTableRow();
		$table->insertTableData(gettext("last sent invoice"), array("width" => "15%"), "header");
		$table->addTableData(array("width" => "35%"), "data");
			if ($projectinfo[0]["lfact"]) {
				$table->addCode(utf8_encode(strftime("%d %B %Y", $projectinfo[0]["lfact"])));
			} else {
				$table->addCode(gettext("never"));
			}
		$table->endTableData();
		$table->insertTableData(gettext("notes"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("note", gettext("view"), "javascript: popup('index.php?mod=project&action=shownotes&project_id=".$projectinfo[0]["id"]."', 'shownotes', 0, 0, 1);");
			$table->addCode(sprintf(" (%d/%d)", $notes_info["new_count"], $notes_info["total_count"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("invoice to"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("form", array(
				"id"     => "setlfact",
				"method" => "get",
				"action" => "index.php"
			));
			$table->addHiddenField("mod", "project");
			$table->addHiddenField("action", "setlfact");
			$table->addHiddenField("id", $_REQUEST["id"]);
			$table->addSelectField("setlfact_day", $days, date("d"));
			$table->addSelectField("setlfact_month", $months, date("m"));
			$table->addSelectField("setlfact_year", $years, date("Y"));
			$table->addCode( $calendar->show_calendar("document.getElementById('setlfact_day')", "document.getElementById('setlfact_month')", "document.getElementById('setlfact_year')" ));
			$table->insertAction("ok", gettext("set"), "javascript: document.getElementById('setlfact').submit();");
			$table->endTag("form");
		$table->endTableData();
		$table->insertTableData(gettext("todos"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("go_todo", gettext("view"), "javascript: popup('index.php?mod=project&action=showtodos&project_id=".$projectinfo[0]["id"]."', 'showtodos', 0, 0, 1);");
			$table->addCode(sprintf(" (%d)", count($todo_info)));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("description"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["description"]);
		$table->endTableData();
		$table->insertTableData(gettext("calendar"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("calendar_today", gettext("view"), "javascript: popup('index.php?mod=project&action=showcal&project_id=".$projectinfo[0]["id"]."', 'showcal', 0, 0, 1);");
			$table->addCode(sprintf(" (%d)", count($calendar_items)));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("manager"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["manager_name"]);
		$table->endTableData();
		$table->insertTableData(gettext("files"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("folder_open", gettext("view"), array("href" => "javascript: popup('index.php?mod=filesys&action=opendir&id=".$filesysfolder."', 'showfiles', 0, 0, 1);"));
			$table->addCode(sprintf(" (%d/%d)", $filesysinfo["data"][0]["filecount"], $filesysinfo["data"][0]["foldercount"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("executor"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["executor_name"]);
		$table->endTableData();
		$table->insertTableData(gettext("sales"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("go_sales", gettext("view"), array("href" => "javascript: popup('index.php?mod=project&action=showsales&project_id=".$projectinfo[0]["id"]."','showsales',0,0,1);"));
			$table->addCode(sprintf(" (%d)", $sales_items["count"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("access"), "", "header");
		$table->addTableData("", "data");
			foreach ($projectinfo[0]["access_name"] as $name) {
				$table->addCode($name);
				$table->addTag("br");
			}
		$table->endTableData();
		$table->insertTableData(gettext("email"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("mail_forward", gettext("view"), array("href" => "javascript: popup('index.php?mod=email&folder_id=".$archive_mail["id"]."&project_id=".$projectinfo[0]["id"]."','showmail',0,0,1);"));
			$table->addCode(sprintf(" (%d)", $mailcount));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("active"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("span", array("id"=>"pr_is_active"));
			if ($projectinfo[0]["is_active"]) {
				$table->insertAction("enabled", gettext("yes"), '');
			} else {
				$table->insertAction("disabled", gettext("no"), '');
			}
			$table->endTag("span");
			$table->insertAction("toggle", gettext("toggle active state"), "javascript: toggle_active(".$projectinfo[0]["id"].", ".$projectinfo[0]["master"].");");
		$table->endTableData();
		$table->insertTableData(gettext("support"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("support", gettext("view"), array("href" => "javascript: popup('index.php?mod=support&search[project_id]=".$projectinfo[0]["id"]."','showsupport',0,0,1);"));
			$table->addCode(sprintf(" (%d/%d)", $support_items["unsolved"], $support_items["count"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("contact"), "", "header");
		$table->addTableData("", "data");
			$address_data = new Address_data();
			$a_ids[$projectinfo[0]["address_id"]] = $address_data->getAddressNameById($projectinfo[0]["address_id"]);
			$tmp = explode(",", $projectinfo[0]["multirel"]);
			foreach ($tmp as $t) {
				if ($t) {
					$a_ids[$t] = $address_data->getAddressNameById($t);
				}
			}
			foreach ($a_ids as $k=>$v) {
				$table->insertLink($v, array("href"=>"index.php?mod=address&action=relcard&id=".$k));
				$table->addTag("br");
			}
		$table->endTableData();
		$table->insertTableData(" ", "", "header");
		$table->insertTableData(" ", "", "data");
	$table->endTableRow();

	// address_businesscard_id
	if ($projectinfo[0]["address_businesscard_id"]) {
		$address_data = new Address_data();
		$bcard = $address_data->getAddressById($projectinfo[0]["address_businesscard_id"], "bcards");

		$table->addTableRow();
			$table->insertTableData(gettext("businesscard"), "", "header");
			$table->addTableData("", "data");
				$table->insertLink($bcard["givenname"]." ".$bcard["infix"]." ".$bcard["surname"], array("href"=>"javascript: popup('index.php?mod=address&action=show_bcard&id=".$bcard["id"]."', 'bcardshow', 0, 0, 1);"));
			$table->endTableData();
		$table->endTableRow();
	}

	$table->endTable();
	$venster_main->addCode($table->generate_output());
	unset($table);

	/* Insert Charts
	*	Get total cost: make pie chart & bar chart with Google Charts
	*	Get cost per user: make pie & bar chart with Google Charts (AJAX call-> showcharts.js)
	*/

	// get information which user work on this project
	foreach ($hoursviewdata as $k=>$v) {
		$usersId[] = $v["user_id"];
		$usersName[] = $v["user_name"];
	}
	foreach ($bulkviewdata as $k=>$v) {
		$usersId[] = $v["user_id"];
		$usersName[] = $v["user_name"];
	}
	foreach ($miscviewdata as $k=>$v) {
		$usersId[] = $v["user_id"];
		$usersName[] = $v["user_name"];
	}

	$usersId = array_unique($usersId);
	$usersName = array_unique($usersName);

	/* show charts image and text */
	$table = new Layout_table(array("width" => "100%"));
	$table->addTableRow();
		$table->addTableData(array("colspan" => 2));
			$table->insertAction("arrowHide", gettext("show"), '', 'arrowShow', '', '', array("style" => "display:none;"));
			$table->insertLink(gettext(" show charts total cost"), array("href" => "javascript: showCharts();"));
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$linkCharts = $table->generate_output();
	unset($table);

	/* Get costs for charts */
	$ar_totals = array();
	$highest_amount = 0;
	$total_costs = 0;
	$ar_totals[] = $hourslist["total_costs_raw"];
	$ar_totals[] = $total_km_costs;
	$ar_totals[] = $bulklist_total["total_costs_raw"];
	$ar_totals[] = $misclist["total_costs_raw"];
	$total_costs = $grand_total_costs_raw + $total_km_costs;
	$highest_amount = max($ar_totals);
	$numberYlabel = $highest_amount;
	$total_costsUser = $total_costs;
	/* If ajax call get the $total_cost of session -> otherwise it count the total cost per user and not of all the users */
	if ($_REQUEST["ajCall"]) {
		$total_costs = $_SESSION['total_costs'];
	}
	/* count percentage */
	if (!$total_costs == 0) {
		foreach ($ar_totals as $k=>$v) {
			$prc_costs  = $v * 100 / $total_costs;
			$prc_costs = round($prc_costs, 1);
			$ar_prc_total += $prc_costs;
			$ar_prc_costs[] = $prc_costs;
		}
	} else {
		for ($i=0; $i<=3; $i++) {
			$ar_prc_costs[$i] = 0;
		}
	}
	/* if AJAX call show charts per users */
	if ($_REQUEST["ajCall"]) {
		$name = $_REQUEST["name"];
			/* create pie chart total costs see 'http://code.google.com/intl/nl/apis/chart/' for more information */
			$pieChartRead = array(
				"chs" => "477x200",
				"cht" => "p",
				"chco" => "",
				"chtt" => gettext("Overview costs whole project ").$name.": ".$ar_prc_total."%",
				"chd" => "t:".$ar_prc_costs[0].",".$ar_prc_costs[1].",".$ar_prc_costs[2].",".$ar_prc_costs[3],
				"chl" => gettext("registered hours")." (".$ar_prc_costs[0]." %) |".gettext("km")." (".$ar_prc_costs[1]." %) |".gettext("bulk hours")." (".$ar_prc_costs[2]." %)|".gettext("project costs")." (".$ar_prc_costs[3]." %)",
			);
					$gPieChartCosts = "index.php?mod=google&action=chart";
					foreach ($pieChartRead as $k => $v) {
						$gPieChartCosts .= "&param[".$k."]=".urlencode($v);
					}
		echo $gPieChartCosts;
		/**
		* Echo space for split function
		* Set barcharts image: display:none;
		* When user click on costs bar chart it will be set to visible
		*/
		echo ' ';
		/* create bar chart total costs */
		$barChartCosts = array(
			"chs" => "477x200",
			"cht" => "bvs",
			"chtt" => gettext("Total costs ").$name.' : &#8364; '.$total_costsUser,
			"chd" => "t:".$hourslist_total['total_costs_raw'].",".$total_km_costs.",".$bulklist_total['total_costs_raw'].",".$misclist['total_costs_raw'],
			"chxt" => "x,y",
			"chds" => "0,".$highest_amount,
			"chxl" => '0:|'.gettext("reg hours")."|".gettext("km")."|".gettext("bulk hours")."|".gettext("project costs").'|1:|0|'.$numberYlabel,
			"chbh" => "40,50",
			"chm"  => "N,727272,0,-1,12",
			"chco" => "ff9900|ffa319|ffae33|ffb84c",
		);
		$gbarChartCosts = "index.php?mod=google&action=chart";
		foreach ($barChartCosts as $k => $v) {
			$gbarChartCosts .= "&param[".$k."]=".urlencode($v);
		}
		echo $gbarChartCosts;
		exit();
	}

	/* create pie chart total costs */
	$pieChartRead = array(
		"chs" => "477x200",
		"cht" => "p",
		"chco" => "",
		"chtt" => gettext("Total Costs: 100")."%",
		"chd" => "t:".$ar_prc_costs[0].",".$ar_prc_costs[1].",".$ar_prc_costs[2].",".$ar_prc_costs[3],
		"chl" => gettext("registered hours")." (".$ar_prc_costs[0]." %) |".gettext("km")." (".$ar_prc_costs[1]." %) |".gettext("bulk hours")." (".$ar_prc_costs[2]." %)|".gettext("project costs")." (".$ar_prc_costs[3]." %)",
	);
	$gPieChartCosts = "index.php?mod=google&action=chart";
	foreach ($pieChartRead as $k => $v) {
		$gPieChartCosts .= "&param[".$k."]=".urlencode($v);
	}

	/* count the percentage costs per user */
	$ar_prc_costsPerUser = array();
	//if there are total costs made calculate percentage
	if (is_array($count)) {
		$i = 0;
		if (!$total_costs == 0) {
			foreach ($count as $k=>$v) {
				$prc_costsPerUser  = $v * 100 / $total_costs;
				$prc_costsPerUser = round($prc_costsPerUser, 1);
				$ar_prc_costsPerUser[] = $prc_costsPerUser;
				$userName = $ar_userName[$i];
				$userName_Costs_ar[] = $userName." (".$prc_costsPerUser."%)";
				$i++;
			}
		}
		$costspPerUser = implode(",", $ar_prc_costsPerUser);
	}
	if (is_array($userName_Costs_ar)) {
		$userName_Costs = implode("|", $userName_Costs_ar);
	}

	/* create pie chart costs % per user in project */
	$pieChartUserinProject = array(
		"chs" => "477x200",
		"cht" => "p",
		"chco" => "",
		"chtt" => gettext("Total Costs: 100")."%",
		"chd" => "t:".$costspPerUser,
		"chl" => $userName_Costs,
	);
	$gPieChartUserinProject = "index.php?mod=google&action=chart";
	foreach ($pieChartUserinProject as $k => $v) {
		$gPieChartUserinProject .= "&param[".$k."]=".urlencode($v);
	}

	/* create bar chart total costs */
	$barChartCosts = array(
		"chs" => "477x200",
		"cht" => "bvs",
		"chtt" => gettext("Total Costs:").' &#8364; '.$total_costs,
		"chd" => "t:".$hourslist_total['total_costs_raw'].",".$total_km_costs.",".$bulklist_total['total_costs_raw'].",".$misclist['total_costs_raw'],
		"chxt" => "x,y",
		"chds" => "0,".$highest_amount,
		"chxl" => '0:|'.gettext("reg hours")."|".gettext("km")."|".gettext("bulk hours")."|".gettext("project costs").'|1:|0|'.$numberYlabel,
		"chbh" => "40,50",
		"chm"  => "N,727272,0,-1,12",
		"chco" => "ff9900|ffa319|ffae33|ffb84c",
	);
	$gbarChartCosts = "index.php?mod=google&action=chart";
	foreach ($barChartCosts as $k => $v) {
		$gbarChartCosts .= "&param[".$k."]=".urlencode($v);
	}

	/* Create session total costs for count per user  */
	$_SESSION['total_costs'] = $total_costs;

	/* Create table for charts  */
	$table = new Layout_table(array("width" => "100%", "style" => "background-color:#FFFFFF", "id" => "tblChartsProjects"));
	$table->addTableRow();
		$table->addTableData(array("colspan" => 3, "bgcolor" => "#eeeeee"));
			$table->addSpace();
		$table->endTableData();
	$table->endTableRow();
	/* Header table: choose pie or bar chart */
	$table->addTableRow();
		$table->addTableData(array("bgcolor" => "#eeeeee"));
			$table->insertLink(gettext("total costs(%)"), array("href" => "javascript: showPie();"));
			$table->addCode(" | ");
			$table->insertLink(gettext("total costs(amount)"), array("href" => "javascript: showBarChart();"));
			$table->addCode(" | ");
			$table->insertLink(gettext("costs all users(%)"), array("href" => "javascript: showPieAllUserCost();"));
		$table->endTableData();
		$table->addTableData(array("colspan" => 2, "bgcolor" => "#eeeeee"));
			$table->insertLink(gettext("costs per user(%)"), array("href" => "javascript: showPieChartUser();"));
			$table->addCode(" | ");
			$table->insertLink(gettext("costs per user(amount)"), array("href" => "javascript: showBarChartUser();"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData(array("colspan" => 3, "bgcolor" => "#eeeeee"));
			$table->addSpace();
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		/* display chart: total costs */
		$table->addTableData(array("width" => "40%"));
		if (!$total_costs == 0) {
			$table->addTag("img", array("src"=>$gPieChartCosts, "id"=> "gPieChartCosts", "style" => "display:block;"));
			$table->addTag("img", array("src"=>$gbarChartCosts, "id"=> "gbarChartCosts", "style" => "display:none;"));
			$table->addTag("img", array("src"=>$gPieChartUserinProject, "id"=> "gPieChartUserinProject", "style" => "display:none;"));
		} else {
			$table->addCode(gettext("No data for charts"));
		}
		$table->endTableData();
		/* display chart: total costs per user */
		$table->addTableData(array("valign" => "top", "width" => "10%", "class" => "project_border_user"));
			$table->addTag("br");
			$table->addCode(gettext("Choose user:"));
			$table->addTag("div" , array("id" => "project_overflow_users"));
			$projectId = $listoptions['projectid'];
			foreach($usersId as $k=>$v){
				if(!$usersName[$k] == ''){
					$table->insertLink($usersName[$k], array("href" => "javascript: getChartUser(".$v.", ".$listoptions['projectid'].", '".$usersName[$k]."');"));
					$table->addTag("br");
				}
			}
			$table->endTag("div");
		$table->endTableData();
		$table->addTableData(array("id" => "chartUser", "width" => "40%"));
			$table->addCode("&nbsp");
		$table->endTableData();
	$table->endTableRow();
		$table->addTableData();
			$table->addSpace();
		$table->endTableData();
		$table->addTableData(array("colspan" => 2, "class" => "project_border_user"));
			$table->addSpace();
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$charts = $table->generate_output();
	unset($table);
	$venster_main->endVensterData();

/* search window */
if (!$this->has_declaration) {
	if ($projectinfo[0]["lfact"]) {
		$start_day   = date("j", $projectinfo[0]["lfact"]);
		$start_month = date("m", $projectinfo[0]["lfact"]);
		$start_year  = date("Y", $projectinfo[0]["lfact"]);
	}
	if ($_REQUEST["start_day"]) {
		$start_day = $_REQUEST["start_day"];
	}
	if ($_REQUEST["start_month"]) {
		$start_month = $_REQUEST["start_month"];
	}
	if ($_REQUEST["start_year"]) {
		$start_year = $_REQUEST["start_year"];
	}
	if ($_REQUEST["end_day"]) {
		$end_day = $_REQUEST["end_day"];
	} else {
		$end_day = date("j");
	}
	if ($_REQUEST["end_month"]) {
		$end_month = $_REQUEST["end_month"];
	} else {
		$end_month = date("m");
	}
	if ($_REQUEST["end_year"]) {
		$end_year = $_REQUEST["end_year"];
	} else {
		$end_year = date("Y");
	}
	if ($_REQUEST["user"]) {
		$user = $_REQUEST["user"];
	} else {
		$user = "";
	}
		/* use table */
		$table = new Layout_table();
		$table->addTableRow();
		$table->addTableRow();
			$table->insertTableData("<br> ", array("colspan" => 2));
		$table->endTableRow();
			$table->addTableData();
				$table->addCode(gettext("from"));
			$table->endTableData();
			$table->addTableData();
				$table->addSelectField("start_day", $days, $start_day);
				$table->addSelectField("start_month", $months, $start_month);
				$table->addSelectField("start_year", $years, $start_year);
				$table->addCode( $calendar->show_calendar("document.getElementById('start_day')", "document.getElementById('start_month')", "document.getElementById('start_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("till"));
			$table->endTableData();
			$table->addTableData();
				$table->addSelectField("end_day", $days, $end_day);
				$table->addSelectField("end_month", $months, $end_month);
				$table->addSelectField("end_year", $years, $end_year);
				$table->addCode( $calendar->show_calendar("document.getElementById('end_day')", "document.getElementById('end_month')", "document.getElementById('end_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("user"));
			$table->endTableData();
			$table->addTableData();
				$table->addHiddenField("user", $user);
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("user", $user, 0, 0, 0, 1, 1, 0, 1) );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("align" => "right", "colspan" => 2));
				$table->insertAction("search", gettext("search"), "javascript: document.getElementById('projsearch').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$search = $table->generate_output();
		unset($table);
} else {
	/* a dummy object */
	$search = "";
}

/* start the page */
$output = new Layout_output();
$output->layout_page(gettext("Projects"));

/* table for everything */
$table = new Layout_table(array("cellspacing"=>1, "cellpadding"=>1, "border" => 0, "width" => "100%"));
$table->addTableRow();
	$table->addTableData();
		$table->addCode($venster_main->generate_output());
	/* add view */
	$table->endTableData();
$table->endTableRow();

/* if extended project module */
if ($GLOBALS["covide"]->license["has_project_ext"]) {
	$project_ext = new ProjectExt_output();
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));
			$table->addTag("div", array("style" => "display: none;", "id" => "additionalinfodiv"));
			$table->addCode( $project_ext->genExtraProjectFields($_REQUEST["id"], 1) );
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
}
if (!$this->has_declaration) {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));
			$table->addTag("div");
			$table->addCode($status_total);
			$table->addCode($status);
			unset($status_status);
			unset($status);
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
	$table->addTableData(array("colspan"=>2));
		if (!$this->has_declaration) {
			$table->addTag("div", array("style" => "display: none;", "id" => "hours"));
		} else {
			$table->addTag("div", array("id" => "hours"));
		}
		$table->addTag("form", array(
			"id"     => "projsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$table->addHiddenField("mod", "project");
		$table->addHiddenField("action", $_REQUEST["action"]);
		$table->addHiddenField("id", $_REQUEST["id"]);
		$table->addHiddenField("showad", 1);
		$table->addHiddenField("showcomm", $_REQUEST["showcomm"]);
		$table->addCode($search);
		$table->endTag("form");
		/* show div charts if there are costs, else set div style none */
		if(!$total_costs == 0){
			$table->addCode($linkCharts);
			$table->addTag("div", array("id" => "charts", "height" => "250px"));
		} else {
			$table->addTag("div", array("id" => "charts", "height" => "250px", "style" => "display:none"));
		}
		$table->addCode($charts);
		$table->endTag("div");
		$table->addCode($hourlist);
		unset($hourlist);
		$table->endTag("div");
	$table->endTableData();
$table->endTableRow();
$table->endTable();
/* put table in page buffer */
$output->addCode($table->generate_output());
if ($_REQUEST["showad"] == 1) {
	$output->start_javascript();
	$output->addCode("showAdditionalInfo();");
	$output->end_javascript();
}

$output->load_javascript(self::include_dir_main."xmlhttp.js");
$output->load_javascript(self::include_dir."hour_operations.js");
$output->load_javascript(self::include_dir."showCharts.js");

$output->start_javascript();
if ($_SESSION["showallprojectdetails"][$projectinfo[0]["id"]] == 0) {
	$output->addCode("addLoadEvent(hideExtra());");
} else {
	$output->addCode("addLoadEvent(showExtra());");
}
$output->end_javascript();
$output->start_javascript();
$output->addCode("getChartUser(".$usersId[0].", ".$projectId.", '".$usersName[0]."');");
$output->end_javascript();

$history = new Layout_history();
$output->addCode( $history->generate_history_call() );

$output->layout_page_end();
$output->exit_buffer();
?>
