<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}
$output = new Layout_output();
$output->layout_page();
$calendar = new Calendar_output();
if (!$_REQUEST["from_day"] && !$_REQUEST["t_from"]) {
	/* show date selector */
	$output->addTag("form", array(
		"id" => "overview",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", $_REQUEST["mod"]);
	$output->addHiddenField("action", $_REQUEST["action"]);
	$venster = new Layout_venster(array(
		"title" => gettext("hours"),
		"subtitle" => gettext("overview")
	));
	$venster->addMenuItem(gettext("back"), "index.php?mod=project");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("from"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("from_day", "01", array("style" => "width: 40px;"));
				$table->addTextField("from_month", "01", array("style" => "width: 40px;"));
				$table->addTextField("from_year", date("Y"), array("style" => "width: 60px;"));
				$table->addCode( $calendar->show_calendar("document.getElementById('from_day')", "document.getElementById('from_month')", "document.getElementById('from_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("till"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("to_day", date("d"), array("style" => "width: 40px;"));
				$table->addTextField("to_month", date("m"), array("style" => "width: 40px;"));
				$table->addTextField("to_year", date("Y"), array("style" => "width: 60px;"));
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
} else {
	$projectdata = new Project_data();
	$userdata = new User_data();
	/* show overview */
	$timestamp_from = ($_REQUEST["t_from"]) ? $_REQUEST["t_from"] : mktime(0, 0, 0, $_REQUEST["from_month"], $_REQUEST["from_day"], $_REQUEST["from_year"]);
	$timestamp_to   = ($_REQUEST["t_to"]) ? $_REQUEST["t_to"] : mktime(0, 0, 0, $_REQUEST["to_month"], $_REQUEST["to_day"], $_REQUEST["to_year"]);
	$projectinfo = $projectdata->getOverviewData($timestamp_from, $timestamp_to);
	/* window widget */
	$venster = new Layout_venster(array(
		"title" => gettext("hour overview"),
		"subtitle" => date("d-m-Y", $timestamp_from)." ".gettext("to")." ".date("d-m-Y", $timestamp_to)
	));
	$venster->addMenuItem(gettext("back"), "javascript: history.go(-1);");
	$venster->addMenuItem(gettext("Payroll export"), "index.php?mod=project&action=payrollexport&start=$timestamp_from&end=$timestamp_to");
	$venster->generateMenuItems();
	$venster->addVensterData();
		/* we cannot use a view here, so built the table ourselves */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("user"), "", "header");
			$table->insertTableData(gettext("non-billable hours"), "", "header");
			$table->insertTableData(gettext("billable hours"), "", "header");
			$table->insertTableData(gettext("total ammount of hours"), "", "header");
			#$table->insertTableData(gettext("Holiday right"), "", "header");
		$table->endTableRow();
		foreach ($projectinfo["users"] as $userid=>$userinfo) {
			$table->addTableRow();
				$table->addTableData("", "", "data");
					$table->insertLink($userdata->getUsernameById($userid), array("href"=>"index.php?mod=project&action=hour_stats&user_id=".$userid."&t_from=".$timestamp_from."&t_to=".$timestamp_to));
				$table->endTableData();
				$table->addTableData(array("align" => "right"), "data");
					$table->addCode($userinfo["total_nofac"]);
					/*
					$table->addTag("br");
					$table->addCode(gettext("reported sick").": ".$userinfo["total_ill"]);
					$table->addTag("br");
					$table->addCode(gettext("special leave").": ".$userinfo["total_sl"]);
					 */
				$table->endTableData();
				$table->insertTableData($userinfo["total_fac"], array("align" => "right"), "data");
				$table->insertTableData(($userinfo["total_fac"]+$userinfo["total_nofac"]), array("align" => "right"), "data");
				#$table->insertTableData($userinfo["total_hol"], array("align" => "right"), "data");
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->insertTableData($projectinfo["total_nofac"], array("align"=>"right"), "header");
			$table->insertTableData($projectinfo["total_fac"], array("align"=>"right"), "header");
			$table->insertTableData(($projectinfo["total_nofac"]+$projectinfo["total_fac"]), array("align"=>"right"), "header");
			#$table->insertTableData($projectinfo["total_hol"], array("align"=>"right"), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Extra information per project is available on the projectcard of the project. Extra information per user is available on the usercard of the user."), array("colspan" => 4), "data");
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
}
$output->layout_page_end();
$output->exit_buffer();
?>
