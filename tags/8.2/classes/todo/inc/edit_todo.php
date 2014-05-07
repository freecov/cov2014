<?php
	/**
	 * Covide Groupware-CRM Todo edit screen
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 *
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("Todo_output")) {
		die("no class definition found");
	}
	if ($_REQUEST["noteid"] || $noteid) {
		if ($noteid) { $_REQUEST["noteid"] = $noteid; }
		$note_data = new Note_data();
		$noteinfo  = $note_data->getNoteById($_REQUEST["noteid"]);
		$noteinfo["timestamp_end"]      = $noteinfo["timestamp"];
		$noteinfo["is_customercontact"] = $noteinfo["is_support"];
		$noteinfo["id"]       = 0;
		$noteinfo["status"]   = 0;
		$noteinfo["priority"] = 5;
	}
	if ($_REQUEST["todoid"]) {
		$note_data = new Todo_data();
		$noteinfo  = $note_data->getTodoById($_REQUEST["todoid"]);
	}
	if (!$noteinfo) {
		/* set some reasonable defaults */
		$noteinfo["timestamp"]     = date("U");
		$noteinfo["timestamp_end"] = $noteinfo["timestamp"];
		$noteinfo["status"]        = 0;
		$noteinfo["priority"]      = 5;
	}
	if ($_REQUEST["address_id"] && !$noteinfo["address_id"]) {
		$noteinfo["address_id"] = $_REQUEST["address_id"];
	}	
	
	$noiface = ($noiface) ? $noiface : $_REQUEST["hide"];
	$address_data = new Address_data();
	$days = array();
	for ($i=1; $i<=31; $i++) {
		$days[$i] = $i;
	}
	$months = array();
	for ($i=1; $i<=12; $i++) {
		$months[$i] = $i;
	}
	$years = array();
	for ($i=date("Y"); $i<=date("Y")+5; $i++) {
		$years[$i] = $i;
	}
	$status = array(
		"0" => gettext("active"),
		"1" => gettext("passive")
	);
	$priority = array();
	for ($i=1; $i <= 10; $i++) {
		switch ($i) {
		case 1:
			$priority[$i] = $i." - ".gettext("high");
			break;
		case 5:
			$priority[$i] = $i." - ".gettext("normal");
			break;
		case 10:
			$priority[$i] = $i." - ".gettext("low");
			break;
		default:
			$priority[$i] = $i;
			break;
		}
	}

	$output = new Layout_output();
	$output->layout_page("", $noiface);
	/* put a form around the page */
	$output->addTag("form", array(
		"id"     => "todoedit",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "todo");
	$output->addHiddenField("action", "save_todo");
	$output->addHiddenField("todo[id]", $noteinfo["id"]);
	$output->addHiddenField("todo[noteid]", $_REQUEST["noteid"]);
	if (!$GLOBALS["covide"]->license["has_project"] && !$GLOBALS["covide"]->license["has_project_declaration"])
		$output->addHiddenField("todo[project_id]", $noteinfo["project_id"]);
	$output->addHiddenField("todo[is_customercontact]", $noteinfo["is_customercontact"]);
	$output->addHiddenField("todo[noiface]", $noiface);
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("todo"),
		"subtitle" => gettext("plan in calendar")
	));
	
	/* delete menu if there's no interface */
	if (!$noiface) {
		$venster->addMenuItem(gettext("back"), "javascript: history.go(-1);");
		$venster->generateMenuItems();
	}

	$calendar = new Calendar_output();
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			/* start date */
			$table->insertTableData(gettext("day"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("todo[timestamp_day]", $days, date("d", $noteinfo["timestamp"]));
				$table->addSelectField("todo[timestamp_month]", $months, date("m", $noteinfo["timestamp"]));
				$table->addSelectField("todo[timestamp_year]", $years, date("Y", $noteinfo["timestamp"]));
				$table->addCode( $calendar->show_calendar("document.getElementById('todotimestamp_day')", "document.getElementById('todotimestamp_month')", "document.getElementById('todotimestamp_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			/* end date */
			$table->insertTableData(gettext("end date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("todo[timestamp_end_day]", $days, date("d", $noteinfo["timestamp_end"]));
				$table->addSelectField("todo[timestamp_end_month]", $months, date("m", $noteinfo["timestamp_end"]));
				$table->addSelectField("todo[timestamp_end_year]", $years, date("Y", $noteinfo["timestamp_end"]));
				$table->addCode( $calendar->show_calendar("document.getElementById('todotimestamp_end_day')", "document.getElementById('todotimestamp_end_month')", "document.getElementById('todotimestamp_end_year')" ));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("controle"), "", "header");
			$table->addTableData("", "data");
				$table->addTag("div", array("id"=>"todo_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			/* inhoud */
			$table->insertTableData(gettext("subject"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("todo[subject]", $noteinfo["subject"], array("style"=>"width: 300px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("status"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("todo[status]", $status, $noteinfo["status"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("priority"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("todo[priority]", $priority, $noteinfo["priority"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", "", "header");
			$table->addTableData("", "data");
				$table->insertAction("ftype_html", gettext("use html"), "javascript: init_editor('convert_html');", "convert_html");
				$table->insertAction("save", gettext("save"), "javascript: todo_save();");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("description"), "", "header");
			$table->addTableData("", "data");
				$editor = new Layout_editor();
				$ret = $editor->generate_editor(1);
				if ($ret !== false) {
					$table->addTextArea("todo[body]", nl2br($noteinfo["body"]), array("style" => "width: 560px; height: 300px;"), "contents");
					$table->addCode($ret);
				} else {
					$table->addTextArea("todo[body]", $noteinfo["body"], array("style" => "width: 560px; height: 300px;"), "contents");
				}
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData("", "data");
			$table->addHiddenField("todo[address_id]", $noteinfo["address_id"]);
				$table->insertTag("span", $address_data->getAddressNameById($noteinfo["address_id"]), array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
			$projectdata = new Project_data();
			$projectinfo = $projectdata->getProjectById($noteinfo["project_id"]);
			$projectname = $projectinfo[0]["name"];
			$table->addTableRow();
				$table->insertTableData(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("todo[project_id]", $noteinfo["project_id"]);
					$table->insertTag("span", $projectname, array("id" => "searchproject"));
					$table->addSpace(1);
					$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData(gettext("alert"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("todo[is_alert]", 1, $noteinfo["is_alert"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				if ($_REQUEST["todoid"] && $noteinfo["id"]) {
					$table->insertAction("delete", gettext("delete"), "javascript: todo_delete(".$noteinfo["id"].");");
				}
				$table->addTag("span", array("id"=>"action_save", "style"=>"visibility: hidden;"));
					$table->insertAction("save", gettext("save"), "javascript: todo_save();");
				$table->endTag("span");
				if ($noiface)
					$table->insertAction("close", gettext("close"), "javascript: window.close();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	/* close the form */
	$output->endTag("form");
	$output->load_javascript(self::include_dir."todo_actions.js");
	$output->layout_page_end();
	$output->exit_buffer();
?>
