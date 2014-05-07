<?php
if (!class_exists("User_output")) {
	die("no class definition found");
}
$userdata = new User_data();
$userinfo = $userdata->getUserPermissionsById($_SESSION["user_id"]);

if (!$userinfo["xs_usermanage"]) {
	if (!$userinfo["xs_limitusermanage"]) {
		/* we dont have access to other users. Redirect to edit screen for user */
		header("Location: index.php?mod=user&action=useredit&id=".$_SESSION["user_id"]);
		exit;
	}
}
/* ok, we have access to edit other users. show selection thingie */
$active_users = $userdata->getUserList(1);
$nonactive_users = $userdata->getUserList(0);
$output = new Layout_output();
$output->layout_page();
	/* put a form around it */
	$output->addTag("form", array(
		"id"     => "userselect",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "user");
	$output->addHiddenField("action", "useredit");
	/* window around it all */
	$venster = new Layout_venster(array("title" => gettext("gebruikersinstellingen")));
	$venster->addMenuItem(gettext("groepsbeleid"), "?mod=user&action=groupindex");
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("actief"), "", "header");
			$table->addTableData("", "header");
				$table->insertAction("new", gettext("nieuwe gebruiker"), "?mod=user&action=useredit&id=0");
			$table->endTableData();
			$table->insertTableData(gettext("niet actief"), "", "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "top");
				$table->addSelectField("id", $active_users, $_SESSION["user_id"], 0, array("size" => 10), "act");
			$table->endTableData();
			$table->addTableData("", "top");
				$table->insertAction("back", gettext("maak actief"), "javascript: user_activate();");
				$table->addTag("br");
				$table->insertAction("forward", gettext("maak niet actief"), "javascript: user_deactivate();");
			$table->endTableData();
			$table->addTableData(array("valign" => "top"));
				$table->addSelectField("userid_nonactive", $nonactive_users, "", 0, array("size" => 10), "nonact");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;");
			$table->addTableData();
				$table->insertAction("edit", gettext("aanpassen"), "javascript: user_edit();");
			$table->endTableData();
			$table->insertTableData("&nbsp;");
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."show_index.js");
$output->layout_page_end();
$output->exit_buffer();
?>
