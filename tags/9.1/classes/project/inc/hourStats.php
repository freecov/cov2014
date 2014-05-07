<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}
$output = new Layout_output();
$output->layout_page();
$calendar = new Calendar_output();

	$projectdata = new Project_data();
	$userdata = new User_data();
	$conversion = new Layout_conversion();
	$user_id = $_REQUEST["user_id"];
	if (!$user_id) 
		$user_id = $_SESSION["user_id"];
	/* show overview */
	$timestamp_from = ($_REQUEST["t_from"]) ? $_REQUEST["t_from"] : mktime(0, 0, 0, $_REQUEST["from_month"], $_REQUEST["from_day"], $_REQUEST["from_year"]);
	$timestamp_to   = ($_REQUEST["t_to"]) ? $_REQUEST["t_to"] : mktime(0, 0, 0, $_REQUEST["to_month"], $_REQUEST["to_day"], $_REQUEST["to_year"]);
	$proj_info = $projectdata->getProjectHoursByUserId($user_id, $timestamp_from, $timestamp_to);
	$hour_data = $projectdata->getOverviewData($timestamp_from, $timestamp_to, $user_id);
	$hour_info = $hour_data["users"][$user_id];
	$total_hours_billable = 0;
	$total_hours_nonbillable = 0;

	/* window widget */
	$venster = new Layout_venster(array(
		"title" => gettext("hour statistics"),
		"subtitle" => gettext("for")." ".$userdata->getUsernameById($user_id)." ".date("d-m-Y", $timestamp_from)." ".gettext("to")." ".date("d-m-Y", $timestamp_to)
	));
	$venster->addMenuItem(gettext("hour overview"), "?mod=project&action=hour_overview&t_from=".$timestamp_from."&t_to=".$timestamp_to);
	$venster->addMenuItem(gettext("back"), "javascript: history.go(-1);");
	$venster->generateMenuItems();
	$venster->addVensterData();
		/* we cannot use a view here, so built the table ourselves */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->insertTableData(gettext("amount of activities"), "", "header");
			$table->insertTableData(gettext("billable hours"), "", "header");
			$table->insertTableData(gettext("non-billable hours"), "", "header");
			$table->insertTableData(gettext("total hours"), "", "header");
		$table->endTableRow();
		if (!count($proj_info)) {
			$table->insertTableData(gettext("no hours in this timepsan"));
		} else {
			foreach ($proj_info as $k=>$v) {
				$total_hours_billable += $v["total_hours"];
				$total_hours_nonbillable += $v["hours_nonbillable"];
				$table->addTableRow();
					$table->addTableData("", "", "data");
						$table->insertLink($v["project_name"], array("href"=>"index.php?mod=project&action=showhours&id=".$v["project_id"]));
					$table->endTableData();
					$table->insertTableData($v["count_project_hours"], array("align" => "right"), "data");
					$table->insertTableData($conversion->seconds_to_hours($v["total_hours"]), array("align" => "right"), "data");
					$table->insertTableData($conversion->seconds_to_hours($v["hours_nonbillable"]), array("align" => "right"), "data");
					$table->insertTableData($conversion->seconds_to_hours($v["total_hours"]+$v["hours_nonbillable"]), array("align" => "right"), "data");
				$table->endTableRow();
			}
		}
		/* generate human output for total hour */
		$output_total_hours = $conversion->seconds_to_hours($total_hours_billable+$total_hours_nonbillable);
			
		$table->addTableRow();
			$table->insertTableData(gettext("total"), "", "header");
			$table->insertTableData("", "", "header");
			$table->insertTableData($conversion->seconds_to_hours($total_hours_billable), "", "header");
			$table->insertTableData($conversion->seconds_to_hours($total_hours_nonbillable), "", "header");
			$table->insertTableData($output_total_hours, "", "header");
		$table->endTableRow();
		
		
		$venster->addCode($table->generate_output());
		unset($table);
		
		/* we cannot use a view here, so built the table ourselves */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("reported sick"), "", "header");
			$table->insertTableData($hour_info["total_ill"], "", "data");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("special leave"), "", "header");
			$table->insertTableData($hour_info["total_sl"], "", "data");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Holiday right"), "", "header");
			$table->insertTableData($hour_info["total_hol"], "", "data");
		$table->endTableRow();
		
		
		/* date jumping form */
			$output->addTag("form", array(
				"id" => "overview",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("mod", "project");
			$output->addHiddenField("action", "hour_stats");
		$table->addTableRow();
			$table->addTableData();
				$table->addHiddenField("user_id", $user_id);
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("user_id", $user_id, 0, 0, 0, 1, 1, 0, 0) );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("from"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("from_day", date("d", $timestamp_from), array("style" => "width: 40px;"));
				$table->addTextField("from_month", date("m", $timestamp_from), array("style" => "width: 40px;"));
				$table->addTextField("from_year", date("Y", $timestamp_from), array("style" => "width: 60px;"));
				$table->addCode( $calendar->show_calendar("document.getElementById('from_day')", "document.getElementById('from_month')", "document.getElementById('from_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("till"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("to_day", date("d", $timestamp_to), array("style" => "width: 40px;"));
				$table->addTextField("to_month", date("m", $timestamp_to), array("style" => "width: 40px;"));
				$table->addTextField("to_year", date("Y", $timestamp_to), array("style" => "width: 60px;"));
				$table->addCode( $calendar->show_calendar("document.getElementById('to_day')", "document.getElementById('to_month')", "document.getElementById('to_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => "2", "align" => "right"));
				$table->insertAction("forward", gettext("next"), "javascript: document.getElementById('overview').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	
	
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");

$output->layout_page_end();
$output->exit_buffer();
?>
