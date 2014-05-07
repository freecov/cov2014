<?php
if (!class_exists("Support_output")) {
	die("no class definition found");
}
if ($_REQUEST["id"]) {
	$supportdata = new Support_data();
	$supportitem = $supportdata->getSupportItemById($_REQUEST["id"]);
	$supportitem["day"]   = date("d", $supportitem["timestamp"]);
	$supportitem["month"] = date("m", $supportitem["timestamp"]);
	$supportitem["year"]  = date("Y", $supportitem["timestamp"]);
	$relname = $supportitem["relname"];
	$rcpt = $supportitem["user_id"];
} else {
	$supportitem["id"]             = 0;
	$supportitem["registering_id"] = $_SESSION["user_id"];
	$supportitem["day"]   = date("d");
	$supportitem["month"] = date("m");
	$supportitem["year"]  = date("Y");
	$rcpt = $_SESSION["user_id"];
}
$priorities = array(
	1 => gettext("hoog"),
	2 => gettext("medium"),
	3 => gettext("laag")
);
$days = array(0 => "---");
for ($i=1; $i<=31; $i++) {
	$days[$i] = $i;
}
$months = array(0 => "---");
for ($i=1; $i<=12; $i++) {
	$months[$i] = $i;
}
$years = array(0 => "---");
for ($i=date("Y")-3; $i<=date("Y"); $i++) {
	$years[$i] = $i;
}
/* get users */
$user = new User_data();
$user_arr = $user->getUserList();

$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id" => "issueedit",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "support");
	$output->addHiddenField("action", "save");
	$output->addHiddenField("issue[id]", $supportitem["id"]);
	$output->addHiddenField("issue[reference_nr]", $supportitem["reference_nr"]);
	$output->addHiddenField("issue[email]", $supportitem["email"]);
	$output->addHiddenField("issue[registering_id]", $supportitem["registering_id"]);
	if ($supportitem["id"]) {
		$subtitle = gettext("aanpassen");
	} else {
		$subtitle = gettext("toevoegen");
	}
	$venster = new Layout_venster(array(
		"title" => gettext("klachten/support"),
		"subtitle" => $subtitle
	));
	$venster->addMenuItem(gettext("terug"), "javascript: window.close();");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("datum"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[day]", $days, $supportitem["day"]);
				$table->addSelectField("issue[month]", $months, $supportitem["month"]);
				$table->addSelectField("issue[year]", $years, $supportitem["year"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("klacht/incident"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[description]", $supportitem["description"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("afhandeling"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[solution]", $supportitem["solution"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[project_id]", $note["project_id"]);
				$table->insertTag("span", $projectname, array("id" => "searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=project&action=searchProject', 'searchproject', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("relatie"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[address_id]", $note["address_id"]);
				$table->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("prioriteit"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[priority]", $priorities, $supportitem["priority"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("afgehandeld"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("issue[is_solved]", 1, $supportitem["is_solved"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("uitvoerder"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[user_id]", $rcpt);
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("issueuser_id", $rcpt, 0, 0, 0, 1) );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData();
				$table->insertAction("save", gettext("opslaan"), "javascript: save_support();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."issue_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
