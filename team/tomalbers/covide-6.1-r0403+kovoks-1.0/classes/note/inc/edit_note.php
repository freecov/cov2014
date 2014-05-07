<?php
if (!class_exists("Note_output")) {
	die("no class found");
}
$id = $_REQUEST["id"];
if (!$id) {
	/* new note, set some sane defaults */
	$note["id"] = 0;
	$subtitel = gettext("nieuw bericht");
} else {
	/* get the note from db */
	$notes_data = new Note_data();
	$note = $notes_data->getNoteById($id);
	/* get relation name */
	$address_data = new Address_data();
	$relname = $address_data->getAddressNameById($note["address_id"]);
	$project_data = new Project_data();
	$projectname = $project_data->getProjectNameById($note["project_id"]);
	if ($_REQUEST["action"] == "reply") {
		$subtitle = gettext("antwoord");
		$rcpt = $note["sender"];
		$note["subject"] = "RE: ".$note["subject"];
		if (strlen($note["extra_receipients"]) && $note["extra_receipients"]) {
			$rcpt .= ",".$note["extra_receipients"];
		}
		$note["oldid"] = $note["id"];
		$note["body"] = "\n\n---".gettext("origineel bericht van")." ".$note["from_name"]." ---".preg_replace("/(\n|^)/s", "\n", $note["body"])."\n---".gettext("einde originele bericht")."---";
		$note["id"] = 0;
	} elseif ($_REQUEST["action"] == "forward") {
		$subtitle = gettext("doorsturen");
		$note["subject"] = "FW: ".$note["subject"];
		$note["body"] = "\n\n---".gettext("origineel bericht van")." ".$note["from_name"]." ---".preg_replace("/(\n|^)/s", "\n", $note["body"])."\n---".gettext("einde originele bericht")."---";
		$note["oldid"] = $note["id"];
		$note["id"] = 0;
	}
}
/* we can overwrite the address_id. This is used from the relationcard */
if ($_REQUEST["address_id"]) {
	$note["address_id"] = $_REQUEST["address_id"];
	$address_data = new Address_data();
	$relname = $address_data->getAddressNameById($note["address_id"]);
}
/* same with is_support (which is actually customer contact flag, freaking reuse of table cells */
if ($_REQUEST["is_custcont"]) {
	$note["is_support"] = 1;
}
/* and same with project */
if ($_REQUEST["project_id"]) {
	$note["project_id"] = $_REQUEST["project_id"];
	$project_data = new Project_data();
	$projectname = $project_data->getProjectNameById($note["project_id"]);
	unset($project_data);
}
/* get users */
$user = new User_data();
$user_arr = $user->getUserList();
/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Notities")." ".$subtitle, 1);

$output->addTag("form", array(
	"id"     => "noteinput",
	"method" => "GET",
	"action" => "index.php"
));
$output->addHiddenField("mod" ,"note");
$output->addHiddenField("action", "store");
$output->addHiddenField("msg_id", $note["id"]);
$output->addHiddenField("note[id]", $note["id"]);
$output->addHiddenField("note[from]", $_SESSION["user_id"]);
if ($note["oldid"]) {
	$output->addHiddenField("note[oldid]", $note["oldid"]);
}
/* we can make notes from calendar, so put extra hidden field here if that is true */
if ($_REQUEST["calendar_id"]) {
	$output->addHiddenField("note[calendar_id]", $_REQUEST["calendar_id"]);
}
$settings = array(
	"title"    => gettext("Notities"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
$venster->addMenuItem(gettext("terug"), "javascript: window.close();");
$venster->generateMenuItems();
$venster->addVensterData();

	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("aan"), "", "header");
		$table->addTableData("", "data");
			/* remove double entries */
			$rcpt = explode(",", $rcpt);
			$rcpt = array_unique($rcpt);
			$rcpt = implode(",", $rcpt);
			$table->addHiddenField("note[to]", $rcpt);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("noteto", $rcpt, 1, 1, 0, 1) );

		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("ftype_html", gettext("gebruikt html"), "javascript: init_editor('convert_html');", "convert_html");
			$table->insertAction("mail_send", gettext("verstuur"), "javascript:note_save();");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("onderwerp"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("note[subject]", $note["subject"], array("style" => "width: 500px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("bericht"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("note[body]", $note["body"], array("style" => "width: 500px; height: 200px;"), "contents");

			$editor = new Layout_editor();
			$table->addCode( $editor->generate_editor(1) );

		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("relatie"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("note[address_id]", $note["address_id"]);
			$table->insertTag("span", $relname, array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_project"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("note[project_id]", $note["project_id"]);
				$table->insertTag("span", $projectname, array("id" => "searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=project&action=searchProject', 'searchproject', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("klantcontact"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("note[is_support]", 1, $note["is_support"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("plan vervolgaktie/todo"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("note[is_todo]", 1, 0);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_sales"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("maak een sales opdracht"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("note[is_sales]", 1, 0);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_voip"] && $GLOBALS["covide"]->license["code"] == "terrazur") {
		$table->addTableRow();
			$table->insertTableData(gettext("stuur als sms naar gebruiker"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("note[sms]", 1);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("mail_send", gettext("verstuur"), "javascript: note_save();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
$output->load_javascript(self::include_dir."edit_note.js");
$output->layout_page_end();
echo $output->generate_output();
?>
