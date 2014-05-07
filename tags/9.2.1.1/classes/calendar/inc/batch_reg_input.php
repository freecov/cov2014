<?php
/**
 * Covide Groupware-CRM calendar module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("Calendar_output")) {
	exit("no class definition found");
}
/* data classes we need */
$calendar_data = new Calendar_data();
$project_data = new Project_data();

/* get the activities */
$activities = $calendar_data->getActivityNames();
$activities[0] = gettext("none");

/* create input for date picker */
$days = $months = $years = array("" => "---");
for ($i=1; $i<=31; $i++) {
	$days[$i] = $i;
}

for ($i=1; $i<=12; $i++) {
	$months[$i] = $i;
}

for ($i=date("Y")-10; $i<=date("Y"); $i++) {
	$years[$i] = $i;
}

/* create data array to provision the input fields */
if ($id == 0) {
	/* new item */
	$regitem = array(
		"project_id"  => $project_id,
		"user_id"     => $_SESSION["user_id"],
		"activity_id" => 0,
		"is_billable" => 0,
		"description" => "",
		"project_name" => $project_data->getProjectNameById($project_id),
	);
} else {
	$regitem = $calendar_data->getRegistrationItemById($id);
	if ($regitem["date"]) {
		$regitem["begin_day"] = date("d", $regitem["date"]);
		$regitem["begin_month"] = date("m", $regitem["date"]);
		$regitem["begin_year"] = date("Y", $regitem["date"]);
	}
	$regitem["project_name"] = $project_data->getProjectNameById($regitem["project_id"]);
}
if (!$regitem["begin_day"]) {
	$regitem["begin_day"] = date("d");
}
if (!$regitem["begin_month"]) {
	$regitem["begin_month"] = date("m");
}
if (!$regitem["begin_year"]) {
	$regitem["begin_year"] = date("Y");
}
/* start output handler */
$output = new Layout_output();
$output->layout_page("", 1);
	/* form for data */
	$output->addTag("form", array(
		"id"     => "reginput",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "calendar");
	$output->addHiddenField("action", "batch_reg_save");

	$output->addHiddenField("regitem[project_id]", $regitem["project_id"]);
	$output->addHiddenField("regitem[user_id]", $regitem["user_id"]);
	$output->addHiddenField("regitem[id]", $id);

	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("hour registration"),
		"subtitle" => gettext("batch hour input"),
	));
	$venster->addVensterData();
		$venster->addTag("ul", array("class" => "bcard_tab_ul", "style" => "list-style-type: none;"));
			$venster->addTag("li", array("id" => "bcard_li_relation", "class" => "bcard_tab_li"));
				$venster->insertLink(gettext("hour registration"), array("href" => sprintf("?mod=calendar&action=reg_input&id=0&timestamp=%d&project_id=%d&address_id=%d", mktime(), $regitem["project_id"], $regitem["user_id"])));
			$venster->endTag("li");
			$venster->addTag("li", array("id" => "bcard_li_relation", "class" => "bcard_tab_li selected"));
				$venster->insertLink(gettext("batch hour input"), array("href" => sprintf("?mod=calendar&action=batch_reg_input&id=0&project_id=%d", $regitem["project_id"])));
			$venster->endTag("li");
			$venster->addTag("li", array("id" => "bcard_li_relation", "class" => "bcard_tab_li"));
				$venster->insertLink(gettext("other project costs"), array("href" => sprintf("?mod=calendar&action=misc_reg_input&id=0&project_id=%d", $regitem["project_id"])));
			$venster->endTag("li");
		$venster->endTag("ul");
		/* use a table to align stuff */
		$table = new Layout_table();
		$table->addTableRow();
			$table->insertTableData(gettext("user"), "", "header");
			$table->addTableData("", "data");
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("regitemuser_id", $regitem["user_id"], 0, 0, 0, 1, 1, 0, 1) );
			$table->endTableData();
		$table->endTableRow();


		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->insertTag("span", $regitem["project_name"], array("id"=>"searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("regitem[day]", $days, ($regitem["begin_day"])?$regitem["begin_day"]:"");
				$table->addSelectField("regitem[month]", $months, ($regitem["begin_month"])?$regitem["begin_month"]:"");
				$table->addSelectField("regitem[year]", $years, ($regitem["begin_year"])?$regitem["begin_year"]:"");
				$calendar = new Calendar_output();
				$table->addCode( $calendar->show_calendar("document.getElementById('regitemday')", "document.getElementById('regitemmonth')", "document.getElementById('regitemyear')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("hours"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("regitem[hours]", $regitem["hours"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("activity"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("regitem[activity_id]", $activities, $regitem["activity_id"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("send an invoice"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckbox("regitem[is_billable]", 1, $regitem["is_billable"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("description"), "", "header");
			$table->addTableData("", "data");
				$editor = new Layout_editor();
				$ret = $editor->generate_editor(1);
				if ($ret !== false) {
					$table->addTextArea("regitem[description]", nl2br($regitem["description"]), array("style"=>"width: 600px; height: 200px;"), "contents");
					$table->addCode($ret);
				} else {
					$table->addTextArea("regitem[description]", $regitem["description"], array("style"=>"width: 500px; height: 200px;"));
				}
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->insertAction("save", gettext("save"), "javascript: reg_save();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		/* end table */
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	/* end window widget */
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."reg_input.js");
$output->layout_page_end();
$output->exit_buffer();
?>
