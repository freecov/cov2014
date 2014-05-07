<?php
/* only allow this file to be included in the class scope */
if (!class_exists("Email_output")) {
	die("no class definition found");
}
/* get username */
if (!$user_id) { $user_id = $_SESSION["user_id"]; }
$user_data = new User_data();
$username = $user_data->getUsernameById($user_id);
/* get current permission delegation records for this user */
$email_data = new Email_data();
$email_delegation = $email_data->getDelegationByUser($user_id, $_REQUEST["folder_id"], "from");
$permusers = array();
$rw_users  = array();
/* make array with users that have access */
foreach ($email_delegation as $k=>$v) {
	$rw_users[] = $v["visitor"];
}
/* make array hold one item if it is empty */
if (!count($access_users)) {
	$access_users = array();
}

/* get users from db */
$userlist = $user_data->getUserList();
$useroutput = new User_output();
/* output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id"     => "permform",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "email");
	$output->addHiddenField("action", "permissionsave");
	$output->addHiddenField("mailperm[user_id]", $user_id);
	$output->addHiddenField("mailperm[folder_id]", $_REQUEST["folder_id"]);
	$output->addHiddenField("mailperm[closewin]", "1");
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("email"),
		"subtitle" => gettext("delen")
	));
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing"=>1));
		$table->addTableRow();
			$table->insertTableData(gettext("gebruiker").": ".$username, array("colspan"=>5));
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("gedeeld met"), "", "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("style"=>"vertical-align: bottom;"), "data");
				$table->addHiddenField("mailperm[rw]", implode(",", $rw_users));
				$table->addCode($useroutput->user_selection("mailpermrw", implode(",", $rw_users), 1, 0, 0, 0));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("cancel", gettext("anuleren"), "javascript: window.close();");
				$table->insertAction("save", gettext("opslaan"), "javascript: perm_save();");
			$table->endTableData();
		$table->endTableRow();
		/* add table to window and destroy object */
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
		
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->start_javascript();
		$output->addCode(
			"
			function perm_save() {
				document.getElementById('permform').submit();
			}
			"
		);
	$output->end_javascript();
/* end page and flush buffer to client */
$output->layout_page_end();
$output->exit_buffer();
?>
