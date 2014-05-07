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
$calendar = new Calendar_output();
$types = array(
	0 => gettext("no type"),
	1 => gettext("problem report"),
	2 => gettext("question"),
	3 => gettext("complaint")
);

$refnr_change = 0;

if ($_REQUEST["id"]) {
	$supportdata = new Support_data();
	$supportitem = $supportdata->getSupportItemById($_REQUEST["id"]);
	$supportitem["day"]   = date("d", $supportitem["timestamp"]);
	$supportitem["month"] = date("m", $supportitem["timestamp"]);
	$supportitem["year"]  = date("Y", $supportitem["timestamp"]);
	if ($supportitem["execution_time"] > 0) {
		$supportitem["ex_day"]   = date("d", $supportitem["execution_time"]);
		$supportitem["ex_month"] = date("m", $supportitem["execution_time"]);
		$supportitem["ex_year"]  = date("Y", $supportitem["execution_time"]);
	}
	if ($supportitem["arrival"]) {
		$supportitem["a_hour"]   = date("H", $supportitem["arrival"]);
		$supportitem["a_minute"] = date("i", $supportitem["arrival"]);
	}
	if ($supportitem["departure"]) {
		$supportitem["d_hour"]   = date("H", $supportitem["departure"]);
		$supportitem["d_minute"] = date("i", $supportitem["departure"]);
	}
	$rcpt = $supportitem["user_id"];
} else {
	if ($_REQUEST["support_id"]) {
		$supportdata = new Support_data();
		$supportcall = $supportdata->getExternalIssues($_REQUEST["support_id"]);
		$supportitem["day"]   = date("d", $supportcall[0]["timestamp"]);
		$supportitem["month"] = date("m", $supportcall[0]["timestamp"]);
		$supportitem["year"]  = date("Y", $supportcall[0]["timestamp"]);
		if ($supportcall[0]["execution_time"] > 0) {
			$supportitem["ex_day"]   = date("d", $supportcall[0]["execution_time"]);
			$supportitem["ex_month"] = date("m", $supportcall[0]["execution_time"]);
			$supportitem["ex_year"]  = date("Y", $supportcall[0]["execution_time"]);
		}
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
		$refnr_change = 1;
	}
	$supportitem["priority"] = 2;
	$supportitem["id"]             = 0;
	$supportitem["registering_id"] = $_SESSION["user_id"];

	if ($_REQUEST["relation_id"]) {
		$supportdata = new Address_data();
		$supportitem["relname"] = $supportdata->getAddressNameById($_REQUEST["relation_id"]);
		$supportitem["address_id"] = $_REQUEST["relation_id"];
	}
	
	if ($_REQUEST["project_id"]) {
		$project_data = new Project_data();
		$supportitem["project_name"] = $project_data->getProjectNameById($_REQUEST["project_id"]);
		$supportitem["project_id"] = $_REQUEST["project_id"];
	}
	
	if ($_REQUEST["mail_id"]) {
		$emaildata = new Email_data();
		$mailoutput = $emaildata->getEmailById($_REQUEST["mail_id"]);
		if ($mailoutput[0]["is_text"]) {
			$supportitem["description"] = $mailoutput[0]["body"];
		} else {
			$supportitem["description"] = str_replace("<br>", "\n", $emaildata->html2text($mailoutput[0]["body_html"]));
		}
		unset($emaildata);
	}
}

$address_data = new Address_data();
if ($supportitem["address_id"]) {
	$bcardinfo = $address_data->getBcardsByRelationID($supportitem["address_id"]);
} else {
	$bcardinfo = array();
}
unset($address_data);
$bcards = array(0 => gettext("none"));
foreach($bcardinfo as $v) {
	$bcards[$v["id"]] = $v["givenname"]." ".$v["infix"]." ".$v["surname"];
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
for ($i=date("Y")-3; $i<=date("Y")+2; $i++) {
	$years[$i] = $i;
}
$hours = array(0 => "---");
for ($i=1; $i<24; $i++) {
	$hours[$i] = $i;
}
$minutes = array(0 => "---");
for ($i=1; $i<60; $i++) {
	$minutes[$i] = $i;
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
	$output->addHiddenField("issue[registering_id]", $supportitem["registering_id"]);
	if ($_REQUEST["support_id"]) {
		$output->addHiddenField("issue[support_id", $_REQUEST["support_id"]);
	}
	if ($supportitem["id"]) {
		$subtitle = gettext("change");
	} else {
		$subtitle = gettext("add");
	}
	$venster = new Layout_venster(array(
		"title" => gettext("Support"),
		"subtitle" => $subtitle
	));
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing" => 1));
		/* reference number */
		$table->addTableRow();
			$table->insertTableData(gettext("Reference nr"), "", "header");
			$table->addTableData("", "data");
				if ($refnr_change) {
					$table->addTextField("issue[reference_nr]", $supportitem["reference_nr"]);
				} else {
					$table->insertTag("b", $supportitem["reference_nr"]);
					$table->addHiddenField("issue[reference_nr]", $supportitem["reference_nr"]);
				}
			$table->endTableData();
		$table->endTableRow();
		/* date time */
		$table->addTableRow();
			$table->insertTableData(gettext("Date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[day]", $days, $supportitem["day"]);
				$table->addSelectField("issue[month]", $months, $supportitem["month"]);
				$table->addSelectField("issue[year]", $years, $supportitem["year"]);
				$table->addCode( $calendar->show_calendar("document.getElementById('issueday')", "document.getElementById('issuemonth')", "document.getElementById('issueyear')" ));
			$table->endTableData();
		$table->endTableRow();
		/* execution time */
		$table->addTableRow();
			$table->insertTableData(gettext("execution date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[ex_day]", $days, $supportitem["ex_day"]);
				$table->addSelectField("issue[ex_month]", $months, $supportitem["ex_month"]);
				$table->addSelectField("issue[ex_year]", $years, $supportitem["ex_year"]);
				$table->addCode( $calendar->show_calendar("document.getElementById('issueex_day')", "document.getElementById('issueex_month')", "document.getElementById('issueex_year')" ));
			$table->endTableData();
		$table->endTableRow();

		/* description */
		$table->addTableRow();
			$table->insertTableData(gettext("support issue description"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[description]", $supportitem["description"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("applied solutions"));
				$table->addTag("br");
				$table->addTag("br");
				$table->addCode(gettext("Will be mailed to customer if email address is set."));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("issue[solution]", $supportitem["solution"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("remarks"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[remarks]", $supportitem["remarks"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("used supplies"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[supplies]", $supportitem["supplies"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("internal notes"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("issue[internal_notes]", $supportitem["internal_notes"], array(
					"style" => "width: 500px; height: 200px;"
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("time of arrival"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[timeofarrival_hours]", $hours, $supportitem["a_hour"]);
				$table->addCode(":");
				$table->addSelectField("issue[timeofarrival_minutes]", $minutes, $supportitem["a_minute"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("time of departure"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("issue[timeofdeparture_hours]", $hours, $supportitem["d_hour"]);
				$table->addCode(":");
				$table->addSelectField("issue[timeofdeparture_minutes]", $minutes, $supportitem["d_minute"]);
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
				$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("businesscard"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("issue[bcard_selected]", $supportitem["bcard_id"]);
				$table->addTag("div", array("id" => "issue_bcard_layer"));
				$table->endTag("div");
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
				$table->insertAction("close", gettext("close"), "javascript: window.close();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."issue_actions.js");
	$output->start_javascript();
	$output->addCode("updateBcards();");
	$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();
?>
