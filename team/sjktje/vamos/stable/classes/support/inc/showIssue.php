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
	$view->addMapping(gettext("date"), "%human_date");
	$view->addMapping(gettext("reference nr"), "%reference_nr");
	$view->addMapping(gettext("complaint/incident"), "%description");
	$view->addMapping(gettext("dispatching"), "%solution");
	$view->addMapping(gettext("contact"), "%relname");
	$view->addMapping(gettext("project"), "%project_name");
	$view->addMapping(gettext("user"), "%sender_name");
	$view->addMapping(gettext("executor"), "%rcpt_name");
	$view->addMapping(gettext("priority"), "%priority");
	$view->addMapping(gettext("done"), "%%complex_done");
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
