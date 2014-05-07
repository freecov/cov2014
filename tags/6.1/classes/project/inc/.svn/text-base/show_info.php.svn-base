<?php
if (!class_exists("Project_output")) {
	exit("no class definition found");
}
$project_data = new Project_data();
if ($master) {
	/* we want info about a master project. */
	$projectinfo = $project_data->getProjectById($projectid, 1);
	$subprojects = $project_data->getSubprojectsById($projectid, $_REQUEST["top"]);
} else {
	$projectinfo = $project_data->getProjectById($projectid, 0);
}

/* start output */
$output = new Layout_output();
$output->layout_page();
/* make nice window widget */
$venster_settings = array(
	"title"    => gettext("projecten"),
	"subtitle" => gettext("informatie")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
/* menu items for window widget */
if ($projectinfo[0]["allow_edit"]) {
	$venster->addMenuItem(gettext("wijzig"), "javascript: popup('index.php?mod=project&action=edit&master=$master&id=$projectid')");
}
if (!$master) {
	$venster->addMenuItem(gettext("overzicht"), "index.php?mod=project&action=showhours&id=$projectid");
}
$venster->generateMenuItems();
/* start adding data */
$venster->addVensterData();
	/* define view object */
	$view = new Layout_view();
	$view->addData($projectinfo);
	/* our mappings/fields */
	$view->addMapping(gettext("nummer")."/".gettext("naam"), "%name");
	$view->addMapping(gettext("omschrijving"), "%description");
	$view->addMapping(gettext("hoofdproject"), "%%complex_master");
	$view->addMapping(gettext("projectmanager"), "%manager_name");
	$view->addMapping(gettext("actief"), "%%complex_active");
	$view->addMapping(gettext("debiteur"), "%%complex_relname");

	$view->defineComplexMapping("complex_active", array(
		array(
			"type"  => "action",
			"src"   => "ok",
			"check" => "%is_active"
		),
		array(
			"type"  => "action",
			"src"   => "cancel",
			"check" => "%is_inactive"
		)
	));

	$view->defineComplexMapping("complex_master", array(
		array(
			"type"  => "action",
			"src"   => "ok",
			"check" => "%master"
		)
	));
	$view->defineComplexMapping("complex_relname", array(
		array(
			"type" => "link",
			"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
			"text" => "%relname"
		)
	));
	/* end view, put in window widget buffer */
	$venster->addCode($view->generate_output_vertical());
	unset($view);

	if ($master) {
		$venster->addTag("br");
		$venster->addCode(gettext("zoeken").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("forward", gettext("zoeken"), "javascript: document.getElementById('hiddeninfo').submit();");
		if ($GLOBALS["covide"]->license["has_project_ext"]) {
			$projectext = new ProjectExt_output();
			$venster->addCode( $projectext->genOverviewSearch("hiddeninfo") );
		}
	}
$venster->endVensterData();
/* end window widget, put it in main output buffer */
$output->addTag("form", array(
	"id"     => "hiddeninfo",
	"method" => "post",
	"action" => "index.php"
));
$output->addCode($venster->generate_output());
unset($venster);
/* hidden form for some of the javascript functions */
$output->addHiddenField("mod", "project");
$output->addHiddenField("project_id", $projectinfo[0]["id"]);
$output->addHiddenField("id", $_REQUEST["id"]);
$output->addHiddenField("master", $master);
$output->addHiddenField("top", $_REQUEST["top"]);
$output->addHiddenField("action", $_REQUEST["action"]);
$output->endTag("form");
/* if this is a master project, show info about children */
if ($master) {
	$venster_settings = array(
		"title"    => gettext("projecten"),
		"subtitle" => gettext("deelprojecten")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	/* start data in window */
	$venster->addVensterData();
		/* view object */
		$view = new Layout_view();
		$view->addMapping("", "%%complex_actions");
		$view->addMapping(gettext("naam"), "%name");
		$view->addMapping(gettext("omschrijving"), array(
			"%description",
			"\n",
			gettext("factureerbare uren"), ": ", "%billable_hours",
			"\n",
			gettext("Service uren"), ": ", "%service_hours",
			"\n",
			gettext("Totale kosten"), ": &euro; ", "%total_costs"
		));
		$view->addMapping(gettext("uitvoerder"), "%manager_name");
		$view->addMapping(gettext("actief"), "%%complex_active");

		if ($GLOBALS["covide"]->license["has_project_ext"]) {
			$projectext_output = new ProjectExt_output();
			$projectext_output->addMetaFieldsToList($view, $subprojects["data"]);
		}
		$view->addData($subprojects["data"]);

		/* define the actions per record */
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"  => "action",
				"src"   => "edit",
				"alt"   => gettext("wijzigen"),
				"link"  => array("javascript: popup('index.php?mod=project&action=edit&id=", "%id", "&master=", "%master", "');"),
				"check" => "%allow_xs"
			),
			array(
				"type"  => "action",
				"src"   => "calendar_reg_hour",
				"alt"   => gettext("overzicht"),
				"link"  => array("index.php?mod=project&action=showhours&id=", "%id"),
				"check" => "%allow_xs"
			),
			array(
				"type"  => "action",
				"src"   => "ftype_doc",
				"alt"   => "genereer document",
				"link"  => array("javascript: popup('?mod=projectext&action=extGenerateDocumentTree&id=", "%id", "');"),
				"check" => "%has_project_ext_samba"
			)
		));
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "ok",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "delete",
				"check" => "%is_nonactive"
			)
		));
		/* end view object, put it in window widget buffer */
		$venster->addCode($view->generate_output());
		unset($view);

		$paging = new Layout_paging();
		$paging->setOptions($_REQUEST["top"], $subprojects["total_records"], "javascript: document.getElementById('hiddeninfo').top.value = %%; document.getElementById('hiddeninfo').submit();");
		$venster->addCode( $paging->generate_output() );

	$venster->endVensterData();
	/* end of window widget, put it in the output buffer */
	$output->addCode($venster->generate_output());
	unset($venster);
}
$output->load_javascript(self::include_dir."show_info.js");
$output->layout_page_end();
$output->exit_buffer();
?>
