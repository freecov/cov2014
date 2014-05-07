<?php
/**
 * Covide Groupware-CRM Todo module show todolist
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
		"title"    => gettext("todos"),
		"subtitle" => gettext("to-do today")
	));
	$venster->addMenuItem(gettext("new to do"), "index.php?mod=todo&action=edit_todo");
	$venster->addMenuItem(gettext("back"), "javascript: history.go(-1);");
	$venster->addMenuItem(gettext("notes"), "index.php?mod=note");
	$venster->addMenuItem(gettext("calendar"), "index.php?mod=calendar");
	$venster->addMenuItem(gettext("print to do's"), "javascript: popup('index.php?mod=todo&print=1', 'printtodo', 0, 0, 1, 0);");
	$venster->addMenuItem(gettext("refresh"), "index.php?mod=todo");
	if (!$_REQUEST["print"]) {
		$venster->generateMenuItems();
	}
	$venster->addVensterData();
		/* make a view object for our records */
		$view = new Layout_view();
		$view->addData($todolist);
		$view->addSubMapping("%%complex_subject", "%overdue");
		$view->addMapping(gettext("date"), "%humanstart");
		$view->addMapping(gettext("end date"), "%humanend");
		if ($_REQUEST["print"]) {
			$view->addMapping(gettext("content"), "%body");
			$view->setHtmlField("body");
		}
		$view->addMapping(gettext("contact"), "%%complex_relname");
		if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
			$view->addMapping(gettext("project"), "%%complex_project");
			$view->defineComplexMapping("complex_project", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%project_id"),
					"text" => "%project_name"
				)
			));
		}
		$view->addMapping(gettext("customercontact"), "%%complex_customercontact");
		if (!$_REQUEST["print"]) {
			$view->addMapping("%%header_actions", "%%complex_actions", "right");
		}
		$view->defineComplexMapping("header_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete selected to-dos"),
				"link" => "javascript: selection_todo_delete();"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("alter selected to-dos"),
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
				"text"  => "[A] ",
				"check" => "%is_active"
			),
			array(
				"text"  => "[P] ",
				"check" => "%is_passive"
			),
			array(
				"text"  => array(" (", "%priority", ") ")
			),
			array(
				"type" => "link",
				"link" => array("javascript: toonInfo(", "%id", ");"),
				"text" => "%subject"
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
				"alt"  => gettext("information"),
				"link" => array("javascript: toonInfo(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: todo_delete(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("change:"),
				"link" => array("javascript: todo_edit(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "go_calendar",
				"alt"  => gettext("plan in calendar"),
				"link" => array("javascript: todo_to_cal(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"alt"  => gettext("register hours"),
				"link" => array("javascript: todo_to_reg(", "%id", ");")
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
