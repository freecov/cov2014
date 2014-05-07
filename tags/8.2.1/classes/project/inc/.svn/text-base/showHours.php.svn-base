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
$calendar = new Calendar_output();
/* get the project folder in filesys */
$filesys_data = new Filesys_data();
$filesysfolder = $filesys_data->getProjectFolder($_REQUEST["id"]);
unset($filesys_data);

/* get the archive id of mailfolders */
$email_data = new Email_data();
$archive_mail = $email_data->getSpecialFolder("Archief", 0);

$projectdata = new Project_data();
$projectinfo = $projectdata->getProjectById($_REQUEST["id"]);

if ($projectinfo[0]["group_id"]) {
	$projectmaster = $projectdata->getProjectById($projectinfo[0]["group_id"], 1);
}

if (!$projectdata->dataCheckPermissions($projectinfo[0]) && !$projectdata->dataCheckPermissions($projectmaster[0])) {
	$output = new Layout_output();
	$output->layout_page("project");

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

if ($start) {
	if ($end) {
		$hourslist = $projectdata->getHoursList(array("projectid" => $_REQUEST["id"], "start" => $start, "end" => $end));
	} else {
		$hourslist = $projectdata->getHoursList(array("projectid" => $_REQUEST["id"], "start" => $start));
	}
} else {
	$hourslist = $projectdata->getHoursList(array("projectid"=>$_REQUEST["id"], "lfact"=>$projectinfo[0]["lfact"]));
}

$hourslist_total = $projectdata->getHoursList(array("projectid"=>$_REQUEST["id"], "lfact"=>0));
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

if ($projectinfo[0]["hours"]) {
	$hour_perc_total = number_format(($hourslist_total["total_hours_raw"]/$projectinfo[0]["hours"])*100, 2);
} else {
	$hour_perc_total = 0;
}
if ($projectinfo[0]["budget"]) {
	$cost_perc_total = number_format(($hourslist_total["total_costs_raw"]/$projectinfo[0]["budget"])*100, 2);
} else {
	$cost_perc_total = 0;
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
	$view_hourlist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	$view_hourlist->addMapping(gettext("price"), "%costs");
	$view_hourlist->addMapping(gettext("actions"), "%%complex_actions");
	$view_hourlist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");")
		)
	));
	$view_hourlist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=reg_input&id_reg=", "%id", "', 'edit', 750, 520, 1);")
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');")
		),
	));

	// bulk added items
	$bulklist = $projectdata->getHoursList(array("projectid"=>$_REQUEST["id"], "bulk" => 1));

	$bulkviewdata = $bulklist["items"];
	$grand_total_costs_raw = $bulklist["total_costs_raw"]+ $hourslist["total_costs_raw"];
	$grand_total_hours_raw = $bulklist["total_hours_raw"]+ $hourslist["total_hours_raw"];

	$view_bulklist = new Layout_view();
	$view_bulklist->addData($bulkviewdata);
	$view_bulklist->addMapping(gettext("time"), "%hours_bill");
	$view_bulklist->addMapping("", "%%complex_flip");
	$view_bulklist->addMapping(gettext("service hours"), "%hours_service");
	$view_bulklist->addMapping(gettext("user"), "%user_name");
	$view_bulklist->addMapping(gettext("activity"), "%activityname");
	$view_bulklist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	$view_bulklist->addMapping(gettext("price"), "%costs");
	$view_bulklist->addMapping(gettext("actions"), "%%complex_actions");
	$view_bulklist->defineComplexMapping("complex_flip", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"link" => array("javascript: toggle_hours(", "%id", ");")
		)
	));
	$view_bulklist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=batch_reg_input&id=", "%id", "', 'edit', 750, 520, 1);")
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');")
		),
	));

	// non hour items
	$misclist = $projectdata->getHoursList(array("projectid" => $_REQUEST["id"], "misc" => 1));
	$miscviewdata = $misclist["items"];

	$grand_total_costs_raw = $bulklist["total_costs_raw"]+ $hourslist["total_costs_raw"]+ $misclist["total_costs_raw"];

	$view_misclist = new Layout_view();
	$view_misclist->addData($miscviewdata);
	$view_misclist->addMapping(gettext("user"), "%user_name");
	$view_misclist->addMapping(gettext("description"), "%description", array("allow_html" => 1));
	$view_misclist->addMapping(gettext("price"), "%price");
	$view_misclist->addMapping(gettext("actions"), "%%complex_actions");
	$view_misclist->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"link" => array("javascript: popup('?mod=calendar&action=misc_reg_input&id=", "%id", "', 'edit', 750, 520, 1);")
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript: delete_hours('", "%id", "');")
		),

	));

	$venster_hourlist = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>gettext("registration list")));
	$venster_hourlist->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData(array("colspan" => "2"));

				$table->insertAction("new", gettext("new registration entry"), "javascript: popup('index.php?mod=calendar&action=reg_input&id=0&timestamp=".mktime()."&project_id=".$projectinfo[0]["id"]."&address_id=".$projectinfo[0]["address_id"]."', 'hour_reg', 750, 550, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("registered hours"));
			$table->endTableData();
		$table->endTableRow();
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
			$table->addTableData();
				$table->addCode(gettext("total time").": ".$bulklist["total_hours_billable"]);
			$table->endTableData();
			$table->addTableData(array("align"=>"right"));
				$table->addCode(gettext("total costs").": &euro; ".$bulklist["total_costs"]);
				$table->addSpace();
			$table->endTableData();
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
			$table->addTableData();
				$table->addCode(gettext("total items").": ".count($misclist["items"]));
			$table->endTableData();
			$table->addTableData(array("align"=>"right"));
				$table->addCode(gettext("total costs").": &euro; ".$misclist["total_costs"]);
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("totals"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("total time").": ".$grand_total_hours_raw.":00");
			$table->endTableData();
			$table->addTableData(array("align"=>"right"));
				$table->addCode(gettext("total costs").": &euro; ".number_format($grand_total_costs_raw, 2, ".", ","));
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		// if start and end are set, give them to the export as well
		if ($start && $end) {
			$table->insertAction("file_download", gettext("export"), sprintf("javascript:popup('?mod=project&action=exporthours&id=%d&start=%d&end=%d', 0, 350, 200);", $projectinfo[0]["id"], $start, $end));
			$table->addSpace();
			$table->insertAction("file_xml", gettext("export"), sprintf("javascript:popup('?mod=project&action=exporthoursxml&id=%d&start=%d&end=%d', 0, 350, 200);", $projectinfo[0]["id"], $start, $end));
		} else {
			$table->insertAction("file_download", gettext("export"), sprintf("javascript:popup('?mod=project&action=exporthours&id=%d', 0, 350, 200);", $projectinfo[0]["id"]));
			$table->addSpace();
			$table->insertAction("file_xml", gettext("export"), sprintf("javascript:popup('?mod=project&action=exporthoursxml&id=%d', 0, 350, 200);", $projectinfo[0]["id"]));
		}
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

	if (!$_REQUEST["history"]) {
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
$venster_status_total = new Layout_venster(array("title"=>gettext("Status"), "subtitle"=>gettext("overall hours and budget")));
$venster_status_total->addVensterData();
	$table = new Layout_table(array("width"=>"100%", "cellspacing"=>1, "cellpadding"=>1));
	$table->addTableRow();
		$table->addTableData();

			$table->addCode(gettext("hours")."&nbsp;(".$hourslist_total["total_hours_billable"]."/".$projectinfo[0]["hours"].")");
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
			$table->addCode(gettext("budget")."&nbsp;(".$hourslist_total["total_costs"]."/".number_format($projectinfo[0]["budget"], 2, ",", ".").")");
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
	$venster_status_total->addCode($table->generate_output());
	unset($table);
$venster_status_total->endVensterData();


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
			$table->addCode(gettext("budget")."&nbsp;(".$hourslist["total_costs"]."/".number_format($projectinfo[0]["budget"], 2, ",", ".").")");
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
$venster_main = new Layout_venster(array("title"=>$projectinfo[0]["name"], "subtitle"=>($this->has_declaration) ? gettext("file activities") : gettext("project overview")));
$venster_main->addMenuItem(gettext("edit"), sprintf("javascript: popup('index.php?mod=project&action=edit&id=%d&master=%d');", $projectinfo[0]["id"], $projectinfo[0]["master"]));
$venster_main->addMenuItem(gettext("back"), "javascript: history_goback();");
if ($this->has_declaration) {
	$venster_main->addMenuItem(gettext("register new item"), "?mod=projectdeclaration&action=register_item&project_id=".$projectinfo[0]["id"]);
	$venster_main->addMenuItem(gettext("send declaration"), "?mod=projectdeclaration&action=send_batch&project_id=".$projectinfo[0]["id"]."&manager=".$projectinfo[0]["manager"]);
} else {
	$venster_main->addMenuItem(gettext("batch hour input"), "javascript: popup('?mod=calendar&action=batch_reg_input&id=0&project_id=".$projectinfo[0]["id"]."');");
	$venster_main->addMenuItem(gettext("other project costs"), "javascript: popup('?mod=calendar&action=misc_reg_input&id=0&project_id=".$projectinfo[0]["id"]."');");
	$venster_main->addMenuItem(gettext("print")."/".gettext("pdf"), "javascript: popup('index.php?mod=project&action=showprojecthours&id=".$projectinfo[0]["id"]."', 'hour_overview', 750, 600, 1);");
	$venster_main->addMenuItem(gettext("show additional info"), "javascript: showAdditionalInfo();");
	$venster_main->start_javascript();
	$venster_main->addCode("
		function showAdditionalInfo() {
			document.getElementById('additionalinfodiv').style.display='block';
			document.getElementById('status').style.display='block';
			document.getElementById('hours').style.display='block';
		}
		");
	$venster_main->end_javascript();
}

if ($projectinfo[0]["group_id"]) {
	$venster_main->addMenuItem(($this->has_declaration) ? gettext("to overview") : gettext("to masterprojects"), "?mod=project&action=showinfo&id=".$projectinfo[0]["group_id"]."&master=1");
}
if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
	$venster_main->addMenuItem(gettext("generate document"), "javascript: popup('?mod=projectext&action=extGenerateDocumentTree&id=".$_REQUEST["id"]."');");
	$venster_main->addMenuItem(gettext("open networkshare"), sprintf(
		"javascript: popup('index.php?mod=projectext&action=netshare&id=%d', 'share', '800', '600', 0);", $projectinfo[0]["id"]));
}
$venster_main->generateMenuItems();
$venster_main->addVensterData();
	$table = new Layout_table();
	if (!$this->has_declaration) {
		$table->addTableRow();
			$table->insertTableData(gettext("last sent invoice"), "", "header");
			$table->addTableData("", "data");
				if ($projectinfo[0]["lfact"]) {
					$table->addCode(utf8_encode(strftime("%d %B %Y", $projectinfo[0]["lfact"])));
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
				$table->addCode( $calendar->show_calendar("document.getElementById('setlfact_day')", "document.getElementById('setlfact_month')", "document.getElementById('setlfact_year')" ));
				$table->insertAction("ok", gettext("set"), "javascript: document.getElementById('setlfact').submit();");
				$table->endTag("form");
			$table->endTableData();
		$table->endTableRow();
	}
	$user_output = new User_output;
	$table->addTableRow();
		$table->insertTableData(gettext("description"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["description"]);
			//$table->addTextArea('', $projectinfo[0]["description"], array("rows" => 3, "cols" => 60, "disabled" => "disabled"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("manager"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["manager_name"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("executor"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($projectinfo[0]["executor_name"]);
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
				//$table->insertLink($projectinfo[0]["relname"], array("href"=>"index.php?mod=address&action=relcard&id=".$projectinfo[0]["address_id"]));
				$table->insertLink($v, array("href"=>"index.php?mod=address&action=relcard&id=".$k));
				$table->addTag("br");
			}
		$table->endTableData();
	$table->endTableRow();

	// address_businesscard_id
	if ($projectinfo[0]["address_businesscard_id"]) {
		$address_data = new Address_data();
		$bcard = $address_data->getAddressById($projectinfo[0]["address_businesscard_id"], "bcards");

		$table->addTableRow();
			$table->insertTableData(gettext("businesscard"), "", "header");
			$table->addTableData("", "data");
				$table->insertLink($bcard["fullname"], array("href"=>"javascript: popup('index.php?mod=address&action=show_bcard&id=".$bcard["id"]."', 'bcardshow', 0, 0, 1);"));
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
		$table->insertTableData(gettext("todos"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("go_todo", gettext("view"), "javascript: popup('index.php?mod=project&action=showtodos&project_id=".$projectinfo[0]["id"]."', 'showtodos', 0, 0, 1);");
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
		$table->insertTableData(gettext("sales"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("go_sales", gettext("view"), array("href" => "index.php?mod=sales&search[project_id]=".$projectinfo[0]["id"]));
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

/* If no end year is specified, get the current year */
if(!$_REQUEST["end_year"]) { $_REQUEST["end_year"] = date("Y"); }
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
			$table->addTag("div", array("style" => "display: none;", "id" => "additionalinfodiv"));
			$table->addCode($venster_project_ext->generate_output());
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
}
if (!$this->has_declaration) {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2));

			$table->addTag("div", array("style" => "display: none;", "id" => "status"));
			$table->addCode($venster_status_total->generate_output());
			$table->addCode($venster_status->generate_output());
			unset($venster_status_status);
			unset($venster_status);
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
	$table->addTableData(array("colspan"=>2));
		$table->addTag("div", array("style" => "display: none;", "id" => "hours"));
		$table->addTag("form", array(
			"id"     => "projsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$table->addHiddenField("mod", "project");
		$table->addHiddenField("action", $_REQUEST["action"]);
		$table->addHiddenField("id", $_REQUEST["id"]);
		$table->addHiddenField("showad", 1);
		$table->addCode($venster_search->generate_output());
		$table->endTag("form");

		$table->addCode($venster_hourlist->generate_output());
		unset($venster_hourlist);
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

$history = new Layout_history();
$output->addCode( $history->generate_history_call() );

$output->layout_page_end();
$output->exit_buffer();
?>
