<?php
if (!class_exists("Project_output")) {
	die("no class definition found");
}
$output = new Layout_output();
$output->layout_page();
if (!$_REQUEST["from_day"]) {
	/* show date selector */
	$output->addTag("form", array(
		"id" => "overview",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", $_REQUEST["mod"]);
	$output->addHiddenField("action", $_REQUEST["action"]);
	$venster = new Layout_venster(array(
		"title" => gettext("uren"),
		"subtitle" => gettext("overzicht")
	));
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("van"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("from_day", "01", array("style" => "width: 40px;"));
				$table->addTextField("from_month", "01", array("style" => "width: 40px;"));
				$table->addTextField("from_year", date("Y"), array("style" => "width: 60px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("tot"));
			$table->endTableData();
			$table->addTableData();
				$table->addTextField("to_day", date("d"), array("style" => "width: 40px;"));
				$table->addTextField("to_month", date("m"), array("style" => "width: 40px;"));
				$table->addTextField("to_year", date("Y"), array("style" => "width: 60px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan" => "2", "align" => "right"));
				$table->insertAction("forward", gettext("verder"), "javascript: document.getElementById('overview').submit();");
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
	$timestamp_from = mktime(0, 0, 0, $_REQUEST["from_month"], $_REQUEST["from_day"], $_REQUEST["from_year"]);
	$timestamp_to   = mktime(0, 0, 0, $_REQUEST["to_month"], $_REQUEST["to_day"], $_REQUEST["to_year"]);
	$projectinfo = $projectdata->getOverviewData($timestamp_from, $timestamp_to);
	/* window widget */
	$venster = new Layout_venster(array(
		"title" => gettext("uren overzicht"),
		"subtitle" => date("d-m-Y", $timestamp_from)." ".gettext("t/m")." ".date("d-m-Y", $timestamp_to)
	));
	$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1);");
	$venster->generateMenuItems();
	$venster->addVensterData();
		/* we cannot use a view here, so built the table ourselves */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("gebruiker"), "", "header");
			$table->insertTableData(gettext("niet factureerbare uren"), "", "header");
			$table->insertTableData(gettext("wel factureerbare uren"), "", "header");
			$table->insertTableData(gettext("totaal aantal uren"), "", "header");
			$table->insertTableData(gettext("vakantie uren"), "", "header");
		$table->endTableRow();
		foreach ($projectinfo["users"] as $userid=>$userinfo) {
			$table->addTableRow();
				$table->insertTableData($userdata->getUsernameById($userid), "", "data");
				$table->addTableData(array("align" => "right"), "data");
					$table->addCode($userinfo["total_nofac"]);
					$table->addTag("br");
					$table->addCode(gettext("ziek").": ".$userinfo["total_ill"]);
					$table->addTag("br");
					$table->addCode(gettext("bijzonder verlof").": ".$userinfo["total_sl"]);
				$table->endTableData();
				$table->insertTableData($userinfo["total_fac"], array("align" => "right"), "data");
				$table->insertTableData(($userinfo["total_fac"]+$userinfo["total_nofac"]), array("align" => "right"), "data");
				$table->insertTableData("0", array("align" => "right"), "data");
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->insertTableData($projectinfo["total_nofac"], array("align"=>"right"), "header");
			$table->insertTableData($projectinfo["total_fac"], array("align"=>"right"), "header");
			$table->insertTableData(($projectinfo["total_nofac"]+$projectinfo["total_fac"]), array("align"=>"right"), "header");
			$table->insertTableData("", "", "header");
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
