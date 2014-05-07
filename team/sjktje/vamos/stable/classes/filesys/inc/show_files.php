<?php
if (!class_exists("Filesys_output")) {
	exit("no class definition found");
}

$fs_obj = new Filesys_data();
$fs_data = $fs_obj->getFiles(array("folderid" => $id, "no_xs" => $noactions, "sort"=>$_REQUEST["sortfile"]));

$output = new Layout_output();


/* create view for files */

$view = new Layout_view();
$view->addData($fs_data);
$view->addMapping(gettext("name"), "%%complex_name");
$view->addMapping(gettext("description"), "%description");
if ($_REQUEST["subaction"] != "cmsfile" && $_REQUEST["subaction"] != "cmsimage") {
	$view->addMapping(gettext("size"), "%size_human");
}

$view->addMapping(gettext("used by page"), "%%cms_pages");
$view->defineComplexMapping("cms_pages", array(
	array(
		"type"    => "array",
		"array"   => "pages",
		"mapping" => "%%cms_page"
	)
));
$view->defineComplexMapping("cms_page", array(
	array(
		"type"    => "link",
		"text"    => array("%name", " ", "%id"),
		"link"    => array("javascript: popup('?mod=cms&action=siteTemplates', 'cmstemplates', 700, 600, 1);"),
		"check"   => "%istpl"
	),
	array(
		"type"    => "link",
		"text"    => array("%name", " ", "%id", " "),
		"link"    => array("javascript: cmsEdit('cmsedit', '", "%id", "');"),
		"check"   => "%ispage"
	)
));
if (!$_REQUEST["subaction"]) {
	$view->addMapping(gettext("date"), "%date_human");
	$view->addMapping(gettext("user"), "%user_name");
}
$view->addMapping("%%header_actions", "%%complex_actions_data", "right", "nowrap");
$view->defineComplexMapping("complex_name", array(
	array(
		"type"  => "action",
		"src"   => "%fileicon"
	),
	array(
		"text"  => array(" ", "%name")
	)
));

$view->defineSortForm("sortfile", "velden");
$view->defineSort(gettext("name"), "name");
$view->defineSort(gettext("description"), "description");
$view->defineSort(gettext("date"), "size");
$view->defineSort(gettext("user"), "username");


/* define complex mapping for header actions */
if ($_REQUEST["subaction"]=="add_attachment") {
	$view->defineComplexMapping("header_actions", array(
		array(
			"type"  => "action",
			"src"   => "file_zip",
			"alt"   => gettext("download selected files as zip archive"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("download selected files"),
			"link"  => "javascript: selection_files_download();"
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_files_toggle_all"), "1", 0, 1)
		),
		array(
			"type"  => "action",
			"src"   => "file_attach",
			"alt"   => gettext("add as attachment"),
			"link"  => "javascript: file_attach_multi()",
		)
	));
} elseif ($noactions) {
	$view->defineComplexMapping("header_actions", array(
		array(
			"type"  => "action",
			"src"   => "file_zip",
			"alt"   => gettext("download selected files as zip archive"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("download selected files"),
			"link"  => "javascript: selection_files_download();"
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_files_toggle_all"), "1", 0, 1)
		)
	));
} else {
	$view->defineComplexMapping("header_actions", array(
		array(
			"type"  => "action",
			"src"   => "file_zip",
			"alt"   => gettext("download selected files as zip archive"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("download selected files"),
			"link"  => "javascript: selection_files_download();"
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("delete selected files"),
			"link"  => "javascript: file_remove_multi();"
		),
		array(
			"type"  => "action",
			"src"   => "cut",
			"alt"   => gettext("move selected files"),
			"link"  => "javascript: selection_files_move();"
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_files_toggle_all"), "1", 0, 1)
		)
	));
}


/* define the complex mappings */
$view->defineComplexMapping("complex_actions_data", array(
	array(
		"type"  => "action",
		"src"   => "open",
		"alt"   => gettext("show"),
		"link"  => array("javascript: view_file(", "%id", ", $id);"),
		"check" => "%subview"
	),
	array(
		"type"  => "action",
		"src"   => "file_download",
		"alt"   => gettext("download"),
		"link"  => array("javascript: download(", "%id", ");")
	),
	array(
		"type"  => "action",
		"src"   => "file_edit",
		"alt"   => gettext("edit"),
		"link"  => array("javascript: file_edit(", "%id", ");"),
		"check" => "%show_actions"
	),
	array(
		"type"  => "action",
		"src"   => "delete",
		"alt"   => gettext("delete"),
		"link"  => array("javascript: file_remove(", "%id", ", $id);"),
		"check" => "%show_actions"
	),
	array(
		"text" => $output->insertCheckbox(array("checkbox_file[","%id","]"), "1", 0, 1)
	),
	array(
		"type"  => "action",
		"src"   => "file_attach",
		"alt"   => gettext("add as attachment"),
		"link"  => array("javascript: opener.add_attachment_covide(", "%id", "); window.close();"),
		"check" => "%attachment"
	),
	array(
		"type"  => "action",
		"src"   => "file_attach",
		"alt"   => gettext("use as cms file"),
		"link"  => array("javascript: cmsPreview('", "%id", "', '".preg_replace("/^https/s", "http", $GLOBALS["covide"]->webroot)."');"),
		"check" => "%cmsaction"
	)

));
?>
