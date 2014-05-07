<?php
if (!class_exists("Support_output")) {
	die("no class definition found");
}
/* catch url params we can use in the search */
if (!$_REQUEST["search"]["active"] == 1) {
	$search["active"] = 0;
} else {
	$search["active"] = 1;
}
if ($_REQUEST["search"]["user_id"]) {
	$search["user_id"] = (int)$_REQUEST["search"]["user_id"];
} else {
	$search["user_id"] = $_SESSION["user_id"];
}
if ($_REQUEST["search"]["keyword"]) {
	$search["keyword"] = $_REQUEST["search"]["keyword"];
}
$search["sort"] = $_REQUEST["sort"];

/* fetch support items */
$supportdata = new Support_data();
$supportinfo = $supportdata->getSupportItems($search);

/* get users */
$user_data = new User_data();
$user_list = $user_data->getUserList();

/* define search screen */
$searchoptions = new Layout_table();
$searchoptions->addTableRow();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("alleen open"));
		$searchoptions->addTag("br");
		$searchoptions->addCheckBox("search[active]", 1, $search["active"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("uitvoerder"));
		$searchoptions->addTag("br");
		$searchoptions->addSelectField("search[user_id]", $user_list, $search["user_id"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData();
		$searchoptions->addCode(gettext("zoekwoorden"));
		$searchoptions->addTag("br");
		$searchoptions->addTextField("search[keyword]", $search["keyword"]);
	$searchoptions->endTableData();
	$searchoptions->addTableData(array());
		$searchoptions->insertAction("forward", gettext("zoek"), "javascript: search();");
	$searchoptions->endTableData();
$searchoptions->endTableRow();
$searchoptions->endTable();

/* start output */
$output = new Layout_output();
$output->layout_page();

/* window widget for module */
$venster = new Layout_venster(array(
	"title" => gettext("klachten/incidenten")
));
/* menu items */
$venster->addMenuItem(gettext("nieuw"), "javascript: edit_support(0);");
$venster->addMenuItem(gettext("support aanvragen"), "index.php?mod=support&action=list_external");
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
			$table->addCode($searchoptions->generate_output());
			$table->endTag("form");
			unset($searchoptions);
			/* map fields to output columns */
			$view = new Layout_view();
			$view->addData($supportinfo["items"]);
			$view->addMapping("", "%%complex_actions");
			$view->addMapping(gettext("datum"), "%human_date");
			$view->addMapping(gettext("klacht/incident"), "%short_desc");
			$view->addMapping(gettext("afhandeling"), "%short_sol");
			$view->addMapping(gettext("project"), "%project_name");
			$view->addMapping(gettext("relatie"), "%relname");
			$view->addMapping(gettext("uitvoerder"), "%rcpt_name");
			$view->addMapping(gettext("afgehandeld"), "%%complex_done");

			/* define sort columns */
			$view->defineSortForm("sort", "searchissue");
			$view->defineSort(gettext("datum"), "timestamp");
			$view->defineSort(gettext("klacht/incident"), "description");
			$view->defineSort(gettext("afhandeling"), "solution");
			$view->defineSort(gettext("project"), "projectname");
			$view->defineSort(gettext("relatie"), "companyname");
			$view->defineSort(gettext("uitvoerder"), "username");
			$view->defineSort(gettext("afgehandeld"), "is_solved");


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
					"src"   => "delete",
					"check" => "%active"
				),
				array(
					"type"  => "action",
					"src"   => "ok",
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
			//TODO: where is $url defined?
			$paging->setOptions($top, $supportinfo["count"], $url);
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