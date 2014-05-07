<?php
if (!class_exists("Support_output")) {
	die("no class definition found");
}
/* catch url params we can use in the search */
/* default we show not solved only, but we can view all by unchecking a box */
if ($_REQUEST["search"]["history"] == 1) {
	$search["active"] = 0;
} else {
	$search["active"] = 1;
}

if ($_REQUEST["search"]["user_id"]) {
	$search["user_id"] = (int)$_REQUEST["search"]["user_id"];
}

if ($_REQUEST["search"]["keyword"]) {
	$search["keyword"] = $_REQUEST["search"]["keyword"];
}
$search["sort"] = $_REQUEST["sort"];
$search["top"]  = $_REQUEST["top"];

/* fetch support items */
$supportdata = new Support_data();
$supportinfo = $supportdata->getSupportItems($search);

/* get users */
$user_data = new User_data();
$user_list = $user_data->getUserList();
$user_list[0] = "--";

/* define search screen */
$searchoptions = new Layout_table(array("cellspacing" => 2));
$searchoptions->addTableRow();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("history"));
		$searchoptions->addTag("br");
		$searchoptions->addCheckBox("search[history]", 1, $_REQUEST["search"]["history"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("executor"));
		$searchoptions->addTag("br");
		$searchoptions->addSelectField("search[user_id]", $user_list, $search["user_id"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("search criteria"));
		$searchoptions->addTag("br");
		$searchoptions->addTextField("search[keyword]", $search["keyword"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData(array());
		$searchoptions->insertAction("forward", gettext("search"), "javascript: search();");
	$searchoptions->endTableData();
$searchoptions->endTableRow();
$searchoptions->endTable();

/* start output */
$output = new Layout_output();
$output->layout_page();

/* window widget for module */
$venster = new Layout_venster(array(
	"title" => gettext("complaints/incidents")
));
/* menu items */
$venster->addMenuItem(gettext("new"), "javascript: edit_support(0);");
$venster->addMenuItem(gettext("support requests"), "index.php?mod=support&action=list_external");
$venster->addMenuItem(gettext("print/export"), "javascript: print_support();");
/* end menu items */
$venster->generateMenuItems();
$venster->addVensterData();
	/* put it all in a table */
	$table = new Layout_table();
	$table->addTableRow();
		$table->addTableData();
			$table->addTag("form", array(
				"id"     => "searchissue",
				"method" => "get",
				"action" => "index.php"
			));
			$table->addHiddenField("mod", "support");
			$table->addHiddenField("action", "search");
			$table->addHiddenField("sort", $_REQUEST["sort"]);
			$table->addHiddenField("top", $_REQUEST["top"]);
			$table->addCode($searchoptions->generate_output());
			$table->endTag("form");
			unset($searchoptions);
			/* map fields to output columns */
			$view = new Layout_view();
			$view->addData($supportinfo["items"]);
			$view->addMapping("&nbsp;", "%%complex_actions");
			$view->addMapping(gettext("date"), "%human_date");
			$view->addMapping(gettext("complaint/incident"), "%short_desc");
			$view->addMapping(gettext("dispatching"), "%short_sol");
			$view->addMapping(gettext("project"), "%project_name");
			$view->addMapping(gettext("contact"), "%relname");
			$view->addMapping(gettext("executor"), "%rcpt_name");
			$view->addMapping(gettext("done"), "%%complex_done");

			/* define sort columns */
			$view->defineSortForm("sort", "searchissue");
			$view->defineSort(gettext("date"), "timestamp");
			$view->defineSort(gettext("complaint/incident"), "description");
			$view->defineSort(gettext("dispatching"), "solution");
			$view->defineSort(gettext("project"), "projectname");
			$view->defineSort(gettext("contact"), "companyname");
			$view->defineSort(gettext("executor"), "username");
			$view->defineSort(gettext("done"), "is_solved");


			/* define what we want to do with the complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "info",
					"link" => array("javascript: show_support_item(", "%id", ");")
				),
				array(
					"type" => "action",
					"src"  => "edit",
					"link" => array("javascript: edit_support(", "%id", ");")
				)
			), "nowrap");
			$view->defineComplexMapping("complex_done", array(
				array(
					"type"  => "action",
					"src"   => "disabled",
					"check" => "%active"
				),
				array(
					"type"  => "action",
					"src"   => "enabled",
					"check" => "%is_solved"
				)
			));

			$table->addCode($view->generate_output());
			//$venster->addCode("<pre>".print_r($supportinfo, true)."</pre>");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$paging = new Layout_paging();
			$url = "javascript: document.getElementById('searchissue').top.value = '%%'; document.getElementById('searchissue').submit();";
			$paging->setOptions($_REQUEST["top"], $supportinfo["count"], $url);
			$table->addCode( $paging->generate_output() );
		$table->endTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
/* end window widget, attach to output buffer */
$venster->endVensterData();
$output->addCode($venster->generate_output());
/* attach some js */
$output->load_javascript(self::include_dir."issue_actions.js");
/* end output, flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
