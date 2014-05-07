<?php
if (!class_exists("Dimdim_output")) {
	die("no class found");
}
$id = $_REQUEST["id"];
$subtitle = ($id) ? gettext("Edit") : gettext("New");

/* get user info */
$user_data = new User_data();
$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);

/* generate the screen */
$output = new Layout_output();
$output->layout_page(gettext("Dimdim")." ".$subtitle, 1);

$output->addTag("form", array(
	"id"     => "dimdimedit",
	"method" => "POST",
	"action" => "index.php"
));
$output->addHiddenField("mod" ,"dimdim");
$output->addHiddenField("action", "save_meeting");
$output->addHiddenField("address_id", $_REQUEST["address_id"]);

/* sanitizing dates for datetime field */
$from = explode("-", $_REQUEST["from_hour"]);
$from_time = $from[0].':';
$from_time .= sprintf("%02d", $from[1]);
$from_time .= ':00';

$to = explode("-", $_REQUEST["to_hour"]);
$to_time = $to[0].':';
$to_time .= sprintf("%02d", $to[1]);
$to_time .= ':00';

$output->addHiddenField("dimdim[startdate]", $_REQUEST["date"].' '.$from_time);
$output->addHiddenField("dimdim[enddate]", $_REQUEST["date"].' '.$to_time);

$dimdim_meeting["meeting_room"] = ($dimdim_meeting["meeting_room"]) ? $dimdim_meeting["meeting_room"] : $userinfo["dimdim_username"];
$output->addHiddenField("dimdim[room]", $dimdim_meeting["meeting_room"]);

$settings = array(
	"title"    => gettext("Dimdim"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
$venster->addVensterData();
	$dimdim_meeting["meeting_name"] = ($dimdim_meeting["meeting_name"]) ? $dimdim_meeting["meeting_name"] : '';
	$dimdim_meeting["meeting_description"] = ($dimdim_meeting["meeting_description"]) ? $dimdim_meeting["meeting_description"] : '';
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("Meeting name"), '', "header");
		$table->addTableData("", "data");
			$table->addTextField("dimdim[name]", $dimdim_meeting["meeting_name"], array("style"=>"width: 400px;"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("Meeting description"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("dimdim[description]", $dimdim_meeting["meeting_description"]);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("Attendees"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("dimdim[attendees]", $dimdim_meeting["meeting_attendees"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("dimdimattendees", $dimdim_meeting["meeting_attendees"], array("multiple" => 1, "groups" => 1)) );
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("External attendees"), "", "header");
		$table->addTableData("", "data");
			$table->addTag("div", array(
				"id" => "mail_addresses",
				"class"  => "limit_height",
				"style" => "width: 99%;"
			));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
		$table->addTableData("", "data");
			$table->addSpace(2);
			$table->insertAction("save", gettext("save"), "javascript: submit_meeting();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
$output->load_javascript(self::include_dir."edit_meeting.js");
$output->start_javascript();
$output->addCode("addLoadEvent( update_mail_list_onload() );\n");
$output->end_javascript();
$output->layout_page_end();
echo $output->generate_output();
?>
