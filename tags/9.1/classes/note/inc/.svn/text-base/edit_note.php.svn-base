<?php
if (!class_exists("Note_output")) {
	die("no class found");
}
$id = $_REQUEST["id"];
if (!$id) {
	/* new note, set some sane defaults */
	$note["id"] = 0;
	$subtitle = gettext("new note");
} else {
	/* get the note from db */
	$notes_data = new Note_data();
	$note = $notes_data->getNoteById($id);
	/* get relation name */
	$address_data = new Address_data();
	$relname = $address_data->getAddressNameById($note["address_id"]);
	$project_data = new Project_data();
	$projectname = $project_data->getProjectNameById($note["project_id"]);
	if ($_REQUEST["action"] == "reply" || $_REQUEST["action"] == "reply_single") {
		$subtitle = ($_REQUEST["action"] == "reply") ? gettext("answer all") : gettext("answer sender");
		$rcpt = $note["sender"];
		$note["subject"] = "RE: ".$note["subject"];
		if ($_REQUEST["action"] == "reply") {
			if (strlen($note["extra_recipients"]) && $note["extra_recipients"]) {
				$rcpt .= ",".$note["extra_recipients"];
			}
		}
		$note["oldid"] = $note["id"];
		$note["body"] = "> ".preg_replace("/\n/s", "\n> ", $note["body"]);
		$note["body"] = "\n\n---".gettext("original message from")." ".$note["from_name"]." [".$note["human_date"]."] ---".preg_replace("/(\n|^)/s", "\n", $note["body"])."\n---".gettext("end original message")."---";

		$note["id"] = 0;
	} elseif ($_REQUEST["action"] == "forward") {
		$subtitle = gettext("forward");
		$note["subject"] = "FW: ".$note["subject"];
		$note["body"] = "\n\n---".gettext("original message from")." ".$note["from_name"]." ---".preg_replace("/(\n|^)/s", "\n", $note["body"])."\n---".gettext("end original message")."---";
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
/* get SMS settings */
if ($GLOBALS["covide"]->license["has_voip"]) {
	$sms = false;
	$voip_data = new Voip_data();
	$sms_settings = $voip_data->getSMSSettings();
	if (is_array($sms_settings))
		$sms = true;
}
/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Notes")." ".$subtitle, 1);

$output->addTag("form", array(
	"id"     => "noteinput",
	"method" => "POST",
	"action" => "index.php"
));
$output->addHiddenField("mod" ,"note");
$output->addHiddenField("action", "store");
$output->addHiddenField("msg_id", $note["id"]);
$output->addHiddenField("note[id]", $note["id"]);
$output->addHiddenField("note[from]", $_SESSION["user_id"]);
$output->addHiddenField("note[is_draft]", 0);

if ($_REQUEST["support_id"] && !$id) {
	$support_data = new Support_data();
	$supportcall = $support_data->getExternalIssues($_REQUEST["support_id"]);
	$note["subject"] = sprintf("[%s] %s: %s",
		$supportcall[0]["reference_nr"],
		($supportcall[0]["type"]==2) ? gettext("contact request"):gettext("question"),
		$supportcall[0]["relation_name"]
	);
	$desc = sprintf("%s: %s\n", gettext("type"), ($supportcall[0]["type"]==2) ? gettext("contact request"):gettext("question"));
	$desc = sprintf("%s: %s\n", gettext("email"), $supportcall[0]["email"]);
	$desc.= sprintf("%s: %s\n", gettext("reference nr"), $supportcall[0]["reference_nr"]);
	$desc.= sprintf("%s: %s\n", gettext("relation name"), $supportcall[0]["relation_name"]);
	$desc.= sprintf("\n%s: %s\n", gettext("description"), $supportcall[0]["body"]);
	$note["body"] = $desc;
}

$output->addHiddenField("note[support_id]", $_REQUEST["support_id"]);
if ($note["oldid"]) {
	$output->addHiddenField("note[oldid]", $note["oldid"]);
}
/* we can make notes from calendar, so put extra hidden field here if that is true */
if ($_REQUEST["calendar_id"]) {
	$output->addHiddenField("note[calendar_id]", $_REQUEST["calendar_id"]);
}

/* we can make notes from campaign, so put extra hidden field here if that is true */
if ($_REQUEST["campaign_id"]) {
	$output->addHiddenField("note[campaign_id]", $_REQUEST["campaign_id"]);
}
if ($id && $_REQUEST["action"] == "edit") {
	$subtitle = gettext("edit draft");
}
$settings = array(
	"title"    => gettext("Notes"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
//$venster->addMenuItem(gettext("back"), "javascript: window.close();");
//$venster->generateMenuItems();
$venster->addVensterData();

/* If we send a rcpt_id from the agenda option "ask_for_permission" we'd like to pre-define some fields */
if($_REQUEST["rcpt_id"]) {
	$rcpt = $_REQUEST["rcpt_id"];
	$note["subject"] = gettext("Permission request");
	$note["body"] = gettext("I would like to get permission to access your agenda");
	$output->addHiddenField("note[afp]", "1");
}

	/* catch to_id */
	if ($_REQUEST["to_id"]) {
		$rcpt = $_REQUEST["to_id"];
	}
	/* If users already filled in before.. */
	$action = $_REQUEST["action"];
	/* This is no reply or reply_single. This is a pre-filled draft */
	if ($action != "reply_single" && $action != "reply") {
		$rcpt .= ",".$note["user_id"];
	}
	if ($note["extra_recipients"] && $action != "reply_single") {
		$rcpt .= ",".$note["extra_recipients"];
		$rcpts = explode(",", $rcpt);
		$rcpts = array_unique($rcpts);
		foreach ($rcpts as $k=>$v) {
			if ($v == $_SESSION["user_id"]) {
				unset($rcpts[$k]);
			}
		}
		$rcpt = implode(",", $rcpts);
	}
	/* if it's a forward, we don't want any receipcients */
	if ($action == "forward") {
		unset($rcpt);
	}	
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("to"), "", "header");
		$table->addTableData("", "data");
			/* remove double entries */
			$rcpt = explode(",", $rcpt);
			$rcpt = array_unique($rcpt);
			$rcpt = implode(",", $rcpt);
			$table->addHiddenField("note[to]", $rcpt);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("noteto", $rcpt, 1, 1, 0, 1, 1) );

		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("save", gettext("save"), "javascript: note_draft();");
			$table->addSpace(2);
			//$table->insertAction("ftype_html", gettext("use html"), "javascript: init_editor('convert_html');", "convert_html");
			$table->insertAction("mail_send", gettext("send"), "javascript:note_save();");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("subject"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("note[subject]", $note["subject"], array("style" => "width: 570px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("message"), "", "header");
		$table->addTableData("", "data");
			$editor = new Layout_editor();
			$ret = $editor->generate_editor(1);
			if ($ret !== false) {
				$table->addTextArea("note[body]", nl2br($note["body"]), array("style" => "width: 570px; height: 300px;"), "contents");
				$table->addCode($ret);
			} else {
				$table->addTextArea("note[body]", $note["body"], array("style" => "width: 570px; height: 300px;"), "contents");
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("contact"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("note[address_id]", $note["address_id"]);
			$table->insertTag("span", $relname, array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 600, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	if (($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->insertTableData(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("note[project_id]", $note["project_id"]);
				$table->insertTag("span", $projectname, array("id" => "searchproject"));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: pickProject();");
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_funambol"]) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->insertAction("folder_sync", "", "");
				$table->addSpace();
				$table->addCode(gettext("to mobile device"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("note[to_mobile]", 1, $note["to_mobile"]);
				$table->addSpace(3);
				$table->addCode(gettext("also send this note as email to mobile devices of the recipients"));
			$table->endTableData();
		$table->endTableRow();
	}
	if ($sms) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->insertAction("ftype_text", "", "");
				$table->addSpace();
				$table->addCode(gettext("send as sms to user"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("note[sms]", 1);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->insertAction("state_special", "", "");
			$table->addSpace();
			$table->addCode(gettext("customercontact"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("note[is_support]", 1, $note["is_support"]);
		$table->endTableData();
	$table->endTableRow();
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->insertAction("calendar_reg_hour", "", "");
				$table->addSpace();
				$table->addCode(gettext("create todo"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("note[is_todo]", 1, 0);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_sales"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->insertAction("mail_tracking", "", "");
				$table->addSpace();
				$table->addCode(gettext("create salesitem"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("note[is_sales]", 1, 0);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("close", gettext("close window"), "javascript: window.close();");
			$table->addSpace(2);
			$table->insertAction("save", gettext("save"), "javascript: note_draft();");
			$table->addSpace(2);
			$table->insertAction("mail_send", gettext("send"), "javascript: note_save();");
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
