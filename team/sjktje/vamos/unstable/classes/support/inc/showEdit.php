<?php
/**
 * Covide Groupware-CRM support module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
if (!class_exists("Support_output")) {
	die("no class definition found");
}
$types = array(
	0 => gettext("no type"),
	1 => gettext("problem report"),
	2 => gettext("question"),
	3 => gettext("complaint")
);
if ($_REQUEST["id"]) {
	$supportdata = new Support_data();
	$supportitem = $supportdata->getSupportItemById($_REQUEST["id"]);
	$supportitem["day"]   = date("d", $supportitem["timestamp"]);
	$supportitem["month"] = date("m", $supportitem["timestamp"]);
	$supportitem["year"]  = date("Y", $supportitem["timestamp"]);
	$rcpt = $supportitem["user_id"];
} else {
	if ($_REQUEST["support_id"]) {
		$supportdata = new Support_data();
		$supportcall = $supportdata->getExternalIssues($_REQUEST["support_id"]);

		$supportitem["day"]   = date("d", $supportcall[0]["timestamp"]);
		$supportitem["month"] = date("m", $supportcall[0]["timestamp"]);
		$supportitem["year"]  = date("Y", $supportcall[0]["timestamp"]);
		$supportitem["reference_nr"] = $supportcall[0]["reference_nr"];
		$supportitem["email"]        = $supportcall[0]["email"];
		$supportitem["support_id"]   = $supportcall[0]["id"];
		$supportitem["address_id"]   = $supportcall[0]["customer_id"];


		$addressdata = new Address_data();
		$addresses = $addressdata->getRelationsArray();
		unset($addressdata);

		$desc = sprintf("%s: %s\n", gettext("email"), $supportcall[0]["email"]);
		$desc.= sprintf("%s: %s\n", gettext("reference nr"), $supportcall[0]["reference_nr"]);
		$desc.= sprintf("%s: %s\n", gettext("relation name"), $supportcall[0]["relation_name"]);
		$desc.= sprintf("%s: %s\n", gettext("support type"), $types[$supportcall[0]["type"]]);
		$desc.= sprintf("\n%s: %s\n", gettext("description"), $supportcall[0]["body"]);
		$supportitem["description"]  = $desc;

	} else {
		$supportitem["reference_nr"] = rand(100000000, 999999999);
		$supportitem["day"]   = date("d");
		$supportitem["month"] = date("m");
		$supportitem["year"]  = date("Y");
	}
	$supportitem["priority"] = 2;
	$supportitem["id"]             = 0;
	$supportitem["registering_id"] = $_SESSION["user_id"];

	if($_REQUEST["relation_id"]) {
		$supportdata = new Address_data();
		$supportitem["relname"] = $supportdata->getAddressNameById($_REQUEST["relation_id"]);
		$supportitem["address_id"] = $_REQUEST["relation_id"];
	}
}
$priorities = array(
	1 => gettext("high"),
	2 => gettext("medium"),
	3 => gettext("low")
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
	//$output->addHiddenField("issue[reference_nr]", $supportitem["reference_nr"]);
	//$output->addHiddenField("issue[email]", $supportitem["email"]);
	$output->addHiddenField("issue[registering_id]", $supportitem["registering_id"]);
	if ($supportitem["id"]) {
		$subtitle = gettext("change");
	} else {
		$subtitle = gettext("add");
	}
	$venster = new Layout_venster(array(
		"title" => gettext("issues/support"),
		"subtitle" => $subtitle
	));
	$venster->addMenuItem(gettext("back"), "javascript: window.close();");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing" => 1));
		/* reference number */
		$table->addTableRow();
			$table->insertTableData(gettext("reference nr"), "", "header");
			$table->addTableData("", "data");
				$table->insertTag("b", $supportitem["reference_nr"]);
				$table->addHiddenField("issue[reference_nr]", $supportitem["reference_nr"]);
			$table->endTableData();
		$table->endTableRow();
		/* date time */
		$table->addTableRow();
			$table->insertTableData(gettext("date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[day]", $days, $supportitem["day"]);
				$table->addSelectField("issue[month]", $months, $supportitem["month"]);
				$table->addSelectField("issue[year]", $years, $supportitem["year"]);
			$table->endTableData();
		$table->endTableRow();
		/* description */
		$table->addTableRow();
			$table->insertTableData(gettext("complaint/incident"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[description]", $supportitem["description"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("dispatching")."/".gettext("solution"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[solution]", $supportitem["solution"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[address_id]", $supportitem["address_id"]);
				$table->insertTag("span", $supportitem["relname"], array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[project_id]", $supportitem["project_id"]);
				$table->insertTag("span", $supportitem["project_name"], array("id" => "searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=project&action=searchProject', 'searchproject', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		/* email */
		$table->addTableRow();
			$table->insertTableData(gettext("communication email"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[old_email]", $supportitem["email"]);
				$table->addTextField("issue[email]", $supportitem["email"], array(
					"style" => "width: 250px;"
				));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData(gettext("priority"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[priority]", $priorities, $supportitem["priority"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("done"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("issue[is_solved]", 1, $supportitem["is_solved"]);
				$table->addHiddenField("issue[old_is_solved]", $supportitem["is_solved"]);
				$table->addHiddenField("issue[support_id]", $supportitem["support_id"]);
				$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("executor"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[user_id]", $rcpt);
				$table->addHiddenField("issue[old_user_id]", $rcpt);
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("issueuser_id", $rcpt) );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData();
				$table->insertAction("save", gettext("save"), "javascript: save_support();");
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
