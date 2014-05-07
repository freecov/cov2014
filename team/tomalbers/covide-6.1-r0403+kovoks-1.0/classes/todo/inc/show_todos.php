<?php
if (!class_exists("Todo_output")) {
	die("no class definition found");
}

if ($_REQUEST["userid"]) {
	$userid = $_REQUEST["userid"];
} else {
	$userid = $_SESSION["user_id"];
}

$todo_data = new Todo_data();
$todolist = $todo_data->getTodosByUserId($userid);

$output = new Layout_output();
/* start output buffer */
if ($_REQUEST["print"]) {
	$output->layout_page("", 1);
} else {
	$output->layout_page();
}
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("agenda"),
		"subtitle" => gettext("todo today")
	));
	$venster->addMenuItem(gettext("nieuwe todo"), "index.php?mod=todo&action=edit_todo");
	$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1);");
	$venster->addMenuItem(gettext("notities"), "index.php?mod=note");
	$venster->addMenuItem(gettext("agenda"), "index.php?mod=calendar");
	if (!$_REQUEST["print"]) {
		$venster->generateMenuItems();
	}
	$venster->addVensterData();
		/* make a view object for our records */
		$view = new Layout_view();
		$view->addData($todolist);
		$view->addMapping(gettext("datum"), "%humanstart");
		$view->addMapping(gettext("einddatum"), "%humanend");
		$view->addMapping(gettext("titel"), "%%complex_subject");
		if ($_REQUEST["print"]) {
			$view->addMapping(gettext("inhoud"), "%body");
		}
		$view->addMapping(gettext("relatie"), "%%complex_relname");
		$view->addMapping(gettext("klantcontact"), "%%complex_customercontact");
		if (!$_REQUEST["print"]) {
			$view->addMapping("%%header_actions", "%%complex_actions", "right");
		}
		$view->defineComplexMapping("header_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("de geselecteerde todos verwijderen"),
				"link" => "javascript: selection_todo_delete();"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("de geselecteerde todos aanpassen"),
				"link" => "javascript: selection_todo_edit();"
			),
			array(
				"text" => $output->insertCheckbox(array("checkbox_todo_toggle_all"), "1", 0, 1)
			)		
		));
		$view->defineComplexMapping("complex_subject", array(
			array(
				"type"  => "action",
				"src"   => "important",
				"check" => "%is_alert"
			),
			array(
				"text" => array(
					"<span onclick=\"toonInfo(", "%id", ")\">",
					"%subject",
					"</span>"
				)
			)
		));
		$view->defineComplexMapping("complex_relname", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
				"text" => "%relname"
			)
		));
		$view->defineComplexMapping("complex_customercontact", array(
			array(
				"type" => "action",
				"src"  => "ok",
				"check" => "%is_customercontact"
			)
		));
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "info",
				"link" => array("javascript: toonInfo(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"link" => array("javascript: todo_delete(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"link" => array("javascript: todo_edit(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"link" => array("javascript: todo_to_cal(", "%id", ");")
			),
			array(
				"text"  => $output->insertCheckbox(array("checkbox_todo[","%id","]"), "1", 0, 1)
			)
		));
		/* end of view, add it to the window buffer */
		$venster->addCode($view->generate_output());
	$venster->endVensterData();
	/* form */
	$output->addTag("form", array(
		"id"     => "todoform",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "todo");
	$output->addHiddenField("action", "");
	/* end of window, put in in output buffer */
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."todo_actions.js");
	if ($_REQUEST["print"]) {
		$output->start_javascript();
			$output->addCode("
				window.print();
				window.close();
			");
		$output->end_javascript();
	}
$output->layout_page_end();
$output->exit_buffer();
?>
