<?php
if (!class_exists("Support_output")) {
	die("no class definition found");
}
/* get external support requests from db */
$support_data = new Support_data();
$supportitems = $support_data->getExternalIssues();

$output = new Layout_output();
$output->layout_page();

	$venster = new Layout_venster(array(
		"title"    => gettext("support"),
		"subtitle" => gettext("overview of the filled in forms")
	));
	$venster->addMenuItem(gettext("back"), "?mod=support");
	/* end menu items */
	$venster->generateMenuItems();
	
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($supportitems);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("name"), "%relation_name");
		$view->addMapping(gettext("reference nr"), "%reference_nr");
		$view->addMapping(gettext("email"), "%email");
		$view->addMapping(gettext("type"), "%human_type");
		$view->addMapping(gettext("remark"), "%body");
		$view->addMapping("", "%%complex_actions");
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "mail_forward",
				"alt"  => gettext("forward"),
				"link" => array("javascript: forward_issue(", "%id", ", ", "%type", ", '", "%customer_id", "');")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: remove_issue(", "%id", ");")
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	$output->load_javascript(self::include_dir."external_support_actions.js");
$output->layout_page_end();
$output->exit_buffer();
print_r($supportitems);
?>
