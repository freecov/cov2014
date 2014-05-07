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

if ($_REQUEST["done"] == 1) {
	$done = 1;
	$subtitle = gettext("to-do history");
} else {
	$done = 0;
	$subtitle = gettext("to-do today");
}

if ($_REQUEST["search"]) {
	$options["search"] = $_REQUEST["search"];
}
$top = $_REQUEST["top"];
$options["top"]        = $_REQUEST["top"];
$options["return_count"] = 1;

$todo_data = new Todo_data();
$todolist = $todo_data->getTodosByUserId($userid, $done, "", $options);
$total_count = $todolist["total_count"];
unset($todolist["total_count"]);
$output = new Layout_output();
/* start output buffer */
if ($_REQUEST["print"]) {
	$output->layout_page(gettext("Todo's"), 1);
} else {
	$output->layout_page(gettext("Todo's"));
}
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("Todo's"),
		"subtitle" => $subtitle
	));
	$venster->addMenuItem(gettext("new to do"), "index.php?mod=todo&action=edit_todo", "", 0);
	if ($_SESSION["locale"] == "nl_NL") {
		$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Todo", array("target" => "_blank"), 0);
	}
	$venster->addMenuItem("<b>".gettext("selection actions")."</b>", "");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("mark selected to-dos done"), "javascript: selection_todo_delete();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("alter selected to-dos"), "javascript: selection_todo_edit();");
	$venster->addMenuItem("<b>".gettext("global actions")."</b>", "");
	if ($done == 1) {
		$venster->addMenuItem("&nbsp;&nbsp;".gettext("current"), "index.php?mod=todo");
	} else {
		$venster->addMenuItem("&nbsp;&nbsp;".gettext("done"), "index.php?mod=todo&done=1");
	}
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("notes"), "index.php?mod=note");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("calendar"), "index.php?mod=calendar");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("print to do's"), "javascript: todos_print();");
	if (!$_REQUEST["print"]) {
		$venster->generateMenuItems();
	}
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode( $output->nbspace(3) );
				$table->addCode(gettext("search").": ");
				$table->addTextField("search", $_REQUEST["search"]);
				$table->insertAction("forward", gettext("search"), "javascript: document.getElementById('todoform').submit();");
				$table->start_javascript();
					$table->addCode("document.getElementById('search').focus();");
				$table->end_javascript();
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
		$venster->addTag("br");
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
		$view->addMapping(gettext("user"), "%%complex_user");
		if (!$_REQUEST["print"]) {
			if ($_REQUEST["done"] == 1) {
				$view->addMapping("", "%%complex_actions", "right");
			} else {
				$view->addMapping("%%header_actions", "%%complex_actions", "right");
			}
		}
		$view->defineComplexMapping("header_actions", array(
			array(
				"text" => "<p align=\"right\">"
			),
			array(
				"text" => $output->insertCheckbox(array("checkbox_todo_toggle_all"), "1", 0, 1)
			),
			array(
				"text" => "</p>"
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
		$view->defineComplexMapping("complex_user", array(
			array(
				"text" => "%username"
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
				"src"  => "ok",
				"alt"  => gettext("mark as done"),
				"link" => array("javascript: todo_delete(", "%id", ");"),
				"check" => "%is_current"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("change:"),
				"link" => array("javascript: todo_edit(", "%id", ");"),
				"check" => "%is_current"
			),
			array(
				"type" => "action",
				"src"  => "go_calendar",
				"alt"  => gettext("plan in calendar"),
				"link" => array("javascript: todo_to_cal(", "%id", ");"),
				"check" => "%is_current"
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"alt"  => gettext("register hours"),
				"link" => array("javascript: todo_to_reg(", "%id", ");"),
				"check" => "%project_id"
			),
			array(
				"text"  => $output->insertCheckbox(array("checkbox_todo[","%id","]"), "1", 0, 1),
				"check" => "%is_current"
			)
		));
		/* end of view, add it to the window buffer */
		$venster->addCode($view->generate_output());
		$paging = new Layout_paging();
		if ($done) {
			$url = "index.php?mod=todo&done=1&top=%%";
		} else {
			$url = "index.php?mod=todo&done=0&top=%%";
		}
		$paging->setOptions($top, $total_count, $url);
		$venster->addCode($paging->generate_output());
	$venster->endVensterData();
	/* form */
	$output->addTag("form", array(
		"id"     => "todoform",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "todo");
	$output->addHiddenField("action", "");
	$output->addHiddenField("done", $done);
	$output->addHiddenField("userid", $userid);
	/* end of window, put in in output buffer */
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."todo_actions.js");
	if ($_REQUEST["print"]) {
		$output->start_javascript();
			$output->addCode("
				window.print();
			");
		$output->end_javascript();
	}
$output->layout_page_end();
$output->exit_buffer();
?>
