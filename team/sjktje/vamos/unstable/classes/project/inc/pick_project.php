<?php
if (!class_exists("Project_output")) {
	exit("no class definition found");
}
$output = new Layout_output();
$output->layout_page("", 1);
/* put form around the whole thing */
$output->addTag("form", array(
	"id" => "velden",
	"method" => "get",
	"action" => "index.php"
));
$output->addHiddenField("mod", "project");
$output->addHiddenField("action", $_REQUEST["action"]);
$output->addHiddenField("start", $_REQUEST["start"]);

$deb = $_REQUEST["deb"];
if (strpos($deb, ",") !== false) {
	$debs = explode(",", $deb);
	foreach ($debs as $k=>$v) {
		if (!$v)
			unset($debs[$k]);
	}
	$deb = array_shift($debs);
}
$projectid = $_REQUEST["projectid"];

$output->addHiddenField("deb", $deb);
$output->load_javascript(self::include_dir."pick_project.js");
/* put a table in place to do some outlining */
$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addCode(gettext("search"));
		$table->addSpace(2);
		$table->addTextField("searchinfo", $_REQUEST["searchinfo"], "", "", 1);
		$table->insertAction("forward", gettext("search"), "javascript: search();");
		$table->addCode(gettext("active"));
		$table->addSpace(2);
		//TODO: fix this!
		$actief = $_REQUEST["actief"];
		if (!$actief) {
			$actief = 2;
		}
		$table->addSelectField("actief", array(
			1=>gettext("yes"),
			0=>gettext("no"),
			2=>gettext("both")), $actief);
		$table->insertAction("forward", gettext("search"), "javascript: search();");
		$table->addSpace(2);
		$table->insertAction("close", gettext("close"), "javascript: window.close();");

		$table->addTag("br");

		$address_data = new Address_data();
		$address_name = $address_data->getAddressNameById($deb);
		$table->addCode( gettext("contact").": ".$address_name);
		$table->addSpace(2);
		$table->insertAction("last", gettext("show all contacts / projects"), "javascript: drop_filter();");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData();
		$ids = 0;

		$project_data = new Project_data();
		$data = $project_data->dataPickProject($deb);


		$view = new Layout_view();
		$view->addData($data["data"]);
		$view->addMapping(gettext("name"), "%%complex_name");
		$view->addMapping(gettext("description"), "%description");

		$view->defineComplexMapping("complex_name", array(
			array(
				"type" => "link",
				"link" => array("javascript: selectProject(", "%id", ", '", "%name", "');"),
				"text" => "%name"
			)
		), "nowrap");
		$table->addCode($view->generate_output());

		$paging = new Layout_paging();
		$paging->setOptions($_REQUEST["start"], $data["count"], "javascript: blader('%%');");
		$table->addCode( $paging->generate_output() );
		$table->addTag("br");
		$table->insertAction("delete", gettext("no project"), sprintf("javascript: selectProject(0, '%s');", addslashes(gettext("none"))));


		$table->addTag("br");
	$table->endTableData();
$table->endTableRow();
/*
$table->addTableRow();
	$table->addTableData();
		$table->insertAction("delete", gettext("delete"), "javascript: selectProject(0, '".gettext("none")."');");
	$table->endTableData();
$table->endTableRow();
*/
$table->endTable();

$venster = new Layout_venster(array(
	"title" => ($this->has_declaration) ? gettext("Declarations") : gettext("Projects"),
	"subtitle" => ($this->has_declaration) ? gettext("pick declaration") : gettext("pick project")
));
$venster->addVensterData();
	$venster->addCode( $table->generate_output() );
$venster->endVensterData();

$output->addCode($venster->generate_output());
$output->endTag("form");
$output->layout_page_end();
$output->exit_buffer();
?>
