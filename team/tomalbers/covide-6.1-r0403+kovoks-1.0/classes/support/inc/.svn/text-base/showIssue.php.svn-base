<?php
if (!class_exists("Support_output")) {
	die("no class definition found");
}
$supportdata = new Support_data();
$supportitem[] = $supportdata->getSupportItemById($_REQUEST["id"]);

$output = new Layout_output();
$output->layout_page("", 1);
	/* make a view object */
	$view = new Layout_view();
	$view->addData($supportitem);
	$view->addMapping(gettext("datum"), "%human_date");
	$view->addMapping(gettext("referentie nr"), "%reference_nr");
	$view->addMapping(gettext("klacht/incident"), "%description");
	$view->addMapping(gettext("afhandeling"), "%solution");
	$view->addMapping(gettext("relatie"), "%relname");
	$view->addMapping(gettext("project"), "%project_name");
	$view->addMapping(gettext("gebruiker"), "%sender_name");
	$view->addMapping(gettext("uitvoerder"), "%rcpt_name");
	$view->addMapping(gettext("prioriteit"), "%priority");
	$view->addMapping(gettext("afgehandeld"), "%%complex_done");
	$view->defineComplexMapping("complex_done", array(
		array(
			"type" => "action",
			"scr"  => "delete",
			"check" => "%active"
		),
		array(
			"type"  => "action",
			"src"   => "ok",
			"check" => "is_done"
		)
	));
	
	$output->addCode($view->generate_output_vertical());
$output->layout_page_end();
$output->exit_buffer();
?>
