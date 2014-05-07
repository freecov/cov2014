<?php
if (!class_exists("Project_output")) {
	exit("no class definition found");
}
$project_data = new Project_data();
if ($master) {
	/* we want info about a master project. */
	$projectinfo = $project_data->getProjectById($projectid, 1);
	$subprojects = $project_data->getSubprojectsById($projectid, $_REQUEST["top"], $_REQUEST["sort"]);
} else {
	$projectinfo = $project_data->getProjectById($projectid, 0);
}

/* start output */
$output = new Layout_output();
$output->layout_page();
$history = new Layout_history();
$output->addCode($history->generate_history_call());
/* make nice window widget */
$venster_settings = array(
	"title"    => ($this->has_declaration) ? gettext("dossier") : gettext("projects"),
	"subtitle" => gettext("information")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
/* menu items for window widget */
if ($projectinfo[0]["allow_edit"]) {
	$venster->addMenuItem(gettext("change"), sprintf("javascript: popup('index.php?mod=project&action=edit&master=%d&id=%d')", $master, $projectid));
	if ($master) {
		$venster->addMenuItem(gettext("new project"), sprintf("javascript: popup('index.php?mod=project&action=edit&id=0&master=0&sub_of=%d');", $projectid));
	}
}
if (!$master) {
	$venster->addMenuItem(gettext("overview"), sprintf("index.php?mod=project&action=showhours&id=%d", $projectid));
}
$venster->addMenuItem(gettext("back"), "javascript: history_goback();");

$venster->generateMenuItems();
/* start adding data */
$venster->addVensterData();
	/* define view object */
	$view = new Layout_view();
	$view->addData($projectinfo);
	/* our mappings/fields */
	$view->addMapping(gettext("number")."/".gettext("name"), "%name");
	$view->addMapping(gettext("description"), "%description");
	$view->addMapping(gettext("masterproject"), "%%complex_master");
	$view->addMapping(gettext("project manager"), "%manager_name");
	$view->addMapping(gettext("active"), "%%complex_active");
	$view->addMapping(gettext("relations"), "%%complex_relname");

	$view->defineComplexMapping("complex_active", array(
		array(
			"type"  => "action",
			"src"   => "enabled",
			"check" => "%is_active"
		),
		array(
			"type"  => "action",
			"src"   => "disabled",
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
			"type" => "multilink",
			"link" => array("index.php?mod=address&action=relcard&id=", "%all_address_ids"),
			"text" => "%relname"
		)
	));
	/* end view, put in window widget buffer */
	$venster->addCode($view->generate_output_vertical());
	unset($view);

	if ($master) {
		$venster->addTag("br");
		$venster->addCheckBox("show_ext", 1, ($_REQUEST["show_ext"]) ? 1:0);
		$venster->addCode(gettext("show hours and numbers"));
		$venster->addSpace(3);
		$venster->addCode(gettext("search").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('hiddeninfo').submit();");
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
$output->addHiddenField("sort", $_REQUEST["sort"]);
$output->endTag("form");
/* if this is a master project, show info about children */
if ($master) {
	$venster_settings = array(
		"title"    => ($this->has_declaration) ? gettext("group") : gettext("projects"),
		"subtitle" => ($this->has_declaration) ? gettext("dossier") : gettext("subprojects")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	/* start data in window */
	$venster->addVensterData();
		/* view object */
		$view = new Layout_view();
		$view->addMapping("", "%%complex_actions", "", "top nowrap");
		$view->addMapping(gettext("name"), "%name");
		if ($_REQUEST["show_ext"]) {
			$view->addMapping(gettext("description"), array(
				"%description",
				"\n",
				gettext("billable hours"), ": ", "%billable_hours",
				"\n",
				gettext("Service hours"), ": ", "%service_hours",
				"\n",
				gettext("Total costs"), ": &euro; ", "%total_costs"
			));
		} else {
			$view->addMapping(gettext("description"), "%description");
		}
		$view->addMapping(gettext("executor"), "%executor_name");
		$view->addMapping(gettext("active"), "%%complex_active");

		if ($GLOBALS["covide"]->license["has_project_ext"]) {
			$projectext_output = new ProjectExt_output();
			$projectext_output->addMetaFieldsToList($view, $subprojects["data"]);
		}

		/* define sort columns */
		$view->defineSortForm("sort", "hiddeninfo");
		$view->defineSort(gettext("name"), "name");
		$view->defineSort(gettext("description"), "description");
		$view->defineSort(gettext("active"), "is_active");

		$view->addData($subprojects["data"]);

		/* define the actions per record */
		$view->defineComplexMapping("complex_actions", array(
			array(
				"text"  => "<nobr>"
			),
			array(
				"type"  => "action",
				"src"   => "edit",
				"alt"   => gettext("change:"),
				"link"  => array("javascript: popup('index.php?mod=project&action=edit&id=", "%id", "&master=", "%master", "');"),
				"check" => "%allow_xs"
			),
			array(
				"type"  => "action",
				"src"   => "calendar_reg_hour",
				"alt"   => gettext("overview"),
				"link"  => array("index.php?mod=project&action=showhours&id=", "%id"),
				"check" => "%allow_xs"
			),
			array(
				"type"  => "action",
				"src"   => "ftype_doc",
				"alt"   => "genereer document",
				"link"  => array("javascript: popup('?mod=projectext&action=extGenerateDocumentTree&id=", "%id", "');"),
				"check" => "%has_project_ext_samba"
			),
			array(
				"text"  => "</nobr>"
			)
		));
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "enabled",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "disabled",
				"check" => "%is_nonactive"
			)
		));
		/* end view object, put it in window widget buffer */
		$venster->addCode($view->generate_output());
		unset($view);

		$paging = new Layout_paging();
		$paging->setOptions($_REQUEST["top"], $subprojects["total_records"], "javascript: document.getElementById('hiddeninfo').top.value = '%%'; document.getElementById('hiddeninfo').submit();");
		$venster->addCode( $paging->generate_output() );

	$venster->endVensterData();
	/* end of window widget, put it in the output buffer */
	$output->addCode($venster->generate_output());
	unset($venster);
}

$history = new Layout_history();
$output->addCode( $history->generate_save_state() );

$output->load_javascript(self::include_dir."show_info.js");
$output->layout_page_end();
$output->exit_buffer();
?>
