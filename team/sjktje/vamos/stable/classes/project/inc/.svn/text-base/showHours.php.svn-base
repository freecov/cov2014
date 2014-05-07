<?php
if (!class_exists("Project_output")) {
	exit("no class definition found");
}

if ($_REQUEST["start_day"]) {
	$start = mktime(0, 0, 0, $_REQUEST["start_month"], $_REQUEST["start_day"], $_REQUEST["start_year"]);
}

if ($_REQUEST["end_day"]) {
	$end = mktime(0, 0, 0, $_REQUEST["end_month"], $_REQUEST["end_day"], $_REQUEST["end_year"]);
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

/* get the project folder in filesys */
$filesys_data = new Filesys_data();
$filesysfolder = $filesys_data->getProjectFolder($_REQUEST["id"]);
unset($filesys_data);

/* get the archive id of mailfolders */
$email_data = new Email_data();
$archive_mail = $email_data->getSpecialFolder("Archief", 0);

$projectdata = new Project_data();
$projectinfo = $projectdata->getProjectById($_REQUEST["id"]);
if ($start) {
	if ($end) {
		$hourslist = $projectdata->getHoursList(array("projectid" => $_REQUEST["id"], "start" => $start, "end" => $end));
	} else {
		$hourslist = $projectdata->getHoursList(array("projectid" => $_REQUEST["id"], "start" => $start));
	}
} else {
	$hourslist = $projectdata->getHoursList(array("projectid"=>$_REQUEST["id"], "lfact"=>$projectinfo[0]["lfact"]));
}
$hoursviewdata = $hourslist["items"];
if ($projectinfo[0]["hours"]) {
	$hour_perc = number_format(($hourslist["total_hours_raw"]/$projectinfo[0]["hours"])*100, 2);
} else {
	$hour_perc = 0;
}
if ($projectinfo[0]["budget"]) {
	$cost_perc = number_format(($hourslist["total_costs_raw"]/$projectinfo[0]["budget"])*100, 2);
} else {
	$cost_perc = 0;
}

if (!$this->has_declaration) {
	/* define hourslist view and map data */
	$view_hourlist = new Layout_view();
	$view_hourlist->addData($hoursviewdata);
	$view_hourlist->addMapping(gettext("date"), "%human_start_date");
	$view_hourlist->addMapping(gettext("time"), "%hours_bill");
	$view_hourlist->addMapping("", "%%complex_flip");
	$view_hourlist->addMapping(gettext("service hours"), "%hours_service");
	$view_hourlist->addMapping(gettext("user"), "%user_name");
	$view_hourlist->addMapping(gettext("activity"), "%activityname");
	$view_hourlist->addMapping(gettext("description"), "%description");
	$view_hourlist->addMapping(gettext("price"), "%costs");
	$view_hourlist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");")
		)
	));
	$venster_hourlist = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>gettext("registration list")));
	$venster_hourlist->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2));
				$table->addCode($view_hourlist->generate_output());
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("total time").": ".$hourslist["total_hours_billable"]);
			$table->endTableData();
			$table->addTableData(array("align"=>"right"));
				$table->addCode(gettext("total costs").": &euro; ".$hourslist["total_costs"]);
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster_hourlist->addCode($table->generate_output());
		unset($table);
	$venster_hourlist->endVensterData();
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
	$view_hourlist->addMapping("", "%%complex_flip");

	$view_hourlist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_registration(", "%id", ");")
		),
		array(
			"type" => "action",
			"src"  => "info",
			"link" => array("javascript: alert('", gettext("input by user: "), "%user_name_input", "');")
		)
	));
	$view_hourlist->defineComplexMapping("complex_username", array(
		array(
			"type" => "link",
			"text" => "%user_name",
			"link" => array("javascript: view_user(", "%id", ");")
		)
	));

	$venster_hourlist = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>gettext("registration list")));
	$venster_hourlist->addVensterData();
	$venster_hourlist->load_javascript("classes/projectdeclaration/inc/editProject.js");

		$venster_hourlist->addCode(gettext("current view").": ");
		$venster_hourlist->addSelectField("declaration_view", $declaration_data->getRegistrationHistory($projectinfo[0]["id"]), $_REQUEST["history"]);
		$venster_hourlist->insertAction("forward", gettext("verder"), sprintf("javascript: location.href='?mod=project&action=showhours&id=%d&master=0&history='+document.getElementById('declaration_view').value", $projectinfo[0]["id"]));
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2));
				$table->addCode($view_hourlist->generate_output());
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster_hourlist->addCode($table->generate_output());
		if ($_REQUEST["history"]) {
			$venster_hourlist->insertAction("file_download", gettext("genereer document"), "?mod=projectdeclaration&action=send_batch&project_id=".$projectinfo[0]["id"]."&manager=".$projectinfo[0]["manager"]."&history=".$_REQUEST["history"]);
		}
		unset($table);
	$venster_hourlist->endVensterData();
	/* }}} */
}

$venster_status = new Layout_venster(array("title"=>gettext("Status"), "subtitle"=>gettext("hours and budget")));
$venster_status->addVensterData();
	$table = new Layout_table(array("width"=>"100%", "cellspacing"=>1, "cellpadding"=>1));
	$table->addTableRow();
		$table->addTableData();
			$table->addCode(gettext("hours")."&nbsp;(".$hourslist["total_hours_billable"]."/".$projectinfo[0]["hours"].")");
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
			$table->addCode(gettext("budget")."&nbsp;(".$hourslist["total_costs"]."/".$projectinfo[0]["budget"].")");
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
	$venster_status->addCode($table->generate_output());
	unset($table);
$venster_status->endVensterData();

/* main venster */
$venster_main = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>($this->has_declaration) ? gettext("declaration information") : gettext("project information")));
$venster_main->addMenuItem(($this->has_declaration) ? gettext("back to declarations") : gettext("back to projects"), "javascript: history_goback();");
if ($this->has_declaration) {
	$venster_main->addMenuItem(gettext("register new item"), "?mod=projectdeclaration&action=register_item&project_id=".$projectinfo[0]["id"]);
	$venster_main->addMenuItem(gettext("send declaration"), "?mod=projectdeclaration&action=send_batch&project_id=".$projectinfo[0]["id"]."&manager=".$projectinfo[0]["manager"]);
}

if ($projectinfo[0]["group_id"]) {
	$venster_main->addMenuItem(($this->has_declaration) ? gettext("to overview") : gettext("to masterprojects"), "?mod=project&action=showinfo&id=".$projectinfo[0]["group_id"]."&master=1");
}
if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
	$venster_main->addMenuItem(gettext("generate document"), "javascript: popup('?mod=projectext&action=extGenerateDocumentTree&id=".$_REQUEST["id"]."');");
}
$venster_main->generateMenuItems();
$venster_main->addVensterData();
	$table = new Layout_table();
	if (!$this->has_declaration) {
		$table->addTableRow();
			$table->insertTableData(gettext("last sent invoice"), "", "header");
			$table->addTableData("", "data");
				if ($projectinfo[0]["lfact"]) {
					$table->addCode(strftime("%d %B %Y", $projectinfo[0]["lfact"]));
				} else {
					$table->addCode(gettext("never"));
				}
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
				$table->insertAction("ok", gettext("set"), "javascript: document.getElementById('setlfact').submit();");
				$table->endTag("form");
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("manager"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["manager_name"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("active"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("span", array("id"=>"pr_is_active"));
			if ($projectinfo[0]["is_active"]) {
				$table->addCode(gettext("yes"));
			} else {
				$table->addCode(gettext("no"));
			}
			$table->endTag("span");
			$table->insertAction("toggle", gettext("toggle active state"), "javascript: toggle_active(".$projectinfo[0]["id"].", ".$projectinfo[0]["master"].");");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("contact"), "", "header");
		$table->addTableData("", "data");
			$table->insertLink($projectinfo[0]["relname"], array("href"=>"index.php?mod=address&action=relcard&id=".$projectinfo[0]["address_id"]));
		$table->endTableData();
	$table->endTableRow();

	// address_businesscard_id
	if ($projectinfo[0]["address_businesscard_id"]) {
		$address_data = new Address_data();
		$bcard = $address_data->getAddressById($projectinfo[0]["address_businesscard_id"], "bcards");

		$table->addTableRow();
			$table->insertTableData(gettext("businesscard"), "", "header");
			$table->addTableData("", "data");
				$table->insertLink($bcard["fullname"], array("href"=>"javascript: popup('index.php?mod=address&action=cardshow&cardid=".$bcard["id"]."', 'bcardshow', 0, 0, 1);"));
			$table->endTableData();
		$table->endTableRow();
	}

	$table->addTableRow();
		$table->insertTableData(gettext("notes"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("note", gettext("view"), "javascript: popup('index.php?mod=project&action=shownotes&project_id=".$projectinfo[0]["id"]."', 'shownotes', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("calendar"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("calendar_today", gettext("view"), "javascript: popup('index.php?mod=project&action=showcal&project_id=".$projectinfo[0]["id"]."', 'showcal', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("files"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("folder_open", gettext("view"), array("href" => "index.php?mod=filesys&action=opendir&id=".$filesysfolder));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("email"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("mail_forward", gettext("view"), array("href" => "index.php?mod=email&folder_id=".$archive_mail["id"]."&project_id=".$projectinfo[0]["id"]));
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster_main->addCode($table->generate_output());
	unset($table);
$venster_main->endVensterData();

/* search window */
if (!$this->has_declaration) {
	$venster_search = new Layout_venster(array("title"=>gettext("selection")));
	$venster_search->addVensterData();
		/* use table */
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("from"));
			$table->endTableData();
			$table->addTableData();
				$table->addSelectField("start_day", $days, (int)$_REQUEST["start_day"]);
				$table->addSelectField("start_month", $months, (int)$_REQUEST["start_month"]);
				$table->addSelectField("start_year", $years, (int)$_REQUEST["start_year"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("till"));
			$table->endTableData();
			$table->addTableData();
				$table->addSelectField("end_day", $days, (int)$_REQUEST["end_day"]);
				$table->addSelectField("end_month", $months, (int)$_REQUEST["end_month"]);
				$table->addSelectField("end_year", $years, (int)$_REQUEST["end_year"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("align" => "right", "colspan" => 2));
				$table->insertAction("search", gettext("search"), "javascript: document.getElementById('projsearch').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster_search->addCode($table->generate_output());
		unset($table);
	$venster_search->endVensterData();
} else {
	/* a dummy object */
	$venster_search = new Layout_venster();
}

/* start the page */
$output = new Layout_output();
$output->layout_page();

/* table for everything */
$table = new Layout_table(array("cellspacing"=>1, "cellpadding"=>1));
$table->addTableRow();
	$table->addTableData();
		$table->addCode($venster_main->generate_output());
	/* add view */
	$table->endTableData();
	$table->addTableData("", "top");
		$table->addTag("form", array(
			"id"     => "projsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$table->addHiddenField("mod", "project");
		$table->addHiddenField("action", $_REQUEST["action"]);
		$table->addHiddenField("id", $_REQUEST["id"]);
		$table->addCode($venster_search->generate_output());
		$table->endTag("form");
	$table->endTableData();
$table->endTableRow();

/* if extended project module */
if ($GLOBALS["covide"]->license["has_project_ext"]) {
	$project_ext = new ProjectExt_output();

	$venster_project_ext = new Layout_venster(array("title"=>gettext("Additional information")));
	$venster_project_ext->addVensterData();
		$venster_project_ext->addCode( $project_ext->genExtraProjectFields($_REQUEST["id"], 1) );
	$venster_project_ext->endVensterData();

	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));
			$table->addCode($venster_project_ext->generate_output());
		$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
	$table->addTableData(array("colspan"=>2));
		if (!$this->has_declaration) {
			$table->addCode($venster_status->generate_output());
			unset($venster_status);
		}
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData(array("colspan"=>2));
		$table->addCode($venster_hourlist->generate_output());
		unset($venster_hourlist);
	$table->endTableData();
$table->endTableRow();
$table->endTable();
/* put table in page buffer */
$output->addCode($table->generate_output());
$output->load_javascript(self::include_dir_main."xmlhttp.js");
$output->load_javascript(self::include_dir."hour_operations.js");

$history = new Layout_history();
$output->addCode( $history->generate_history_call() );

$output->layout_page_end();
$output->exit_buffer();
?>
