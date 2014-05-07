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
$view->addMapping(gettext("naam"), "%%complex_name");
$view->addMapping(gettext("omschrijving"), "%description");
$view->addMapping(gettext("grootte"), "%size_human");
if (!$_REQUEST["subaction"]) {
	$view->addMapping(gettext("datum"), "%date_human");
	$view->addMapping(gettext("gebruiker"), "%user_name");
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
$view->defineSort(gettext("naam"), "name");
$view->defineSort(gettext("omschrijving"), "description");
$view->defineSort(gettext("datum"), "size");
$view->defineSort(gettext("gebruiker"), "username");


/* define complex mapping for header actions */
if ($_REQUEST["subaction"]=="add_attachment") {
	$view->defineComplexMapping("header_actions", array(
		array(
			"type"  => "action",
			"src"   => "file_zip",
			"alt"   => gettext("de geselecteerde bestanden downloaden in een zip bestand"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("de geselecteerde bestanden downloaden"),
			"link"  => "javascript: selection_files_download();"
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_files_toggle_all"), "1", 0, 1)
		),
		array(
			"type"  => "action",
			"src"   => "file_attach",
			"alt"   => gettext("voeg toe als attachment"),
			"link"  => "javascript: file_attach_multi()",
		)
	));
} elseif ($noactions) {
	$view->defineComplexMapping("header_actions", array(
		array(
			"type"  => "action",
			"src"   => "file_zip",
			"alt"   => gettext("de geselecteerde bestanden downloaden in een zip bestand"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("de geselecteerde bestanden downloaden"),
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
			"alt"   => gettext("de geselecteerde bestanden downloaden in een zip bestand"),
			"link"  => "javascript: selection_files_zip();"
		),
		array(
			"type"  => "action",
			"src"   => "save",
			"alt"   => gettext("de geselecteerde bestanden downloaden"),
			"link"  => "javascript: selection_files_download();"
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("de geselecteerde bestanden verwijderen"),
			"link"  => "javascript: file_remove_multi();"
		),
		array(
			"type"  => "action",
			"src"   => "cut",
			"alt"   => gettext("de geselecteerde bestanden verplaatsen"),
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
		"alt"   => gettext("tonen"),
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
		"alt"   => gettext("bewerk"),
		"link"  => array("javascript: file_edit(", "%id", ");"),
		"check" => "%show_actions"
	),
	array(
		"type"  => "action",
		"src"   => "delete",
		"alt"   => gettext("verwijderen"),
		"link"  => array("javascript: file_remove(", "%id", ", $id);"),
		"check" => "%show_actions"
	),
	array(
		"text" => $output->insertCheckbox(array("checkbox_file[","%id","]"), "1", 0, 1)
	),
	array(
		"type"  => "action",
		"src"   => "file_attach",
		"alt"   => gettext("voeg toe als attachment"),
		"link"  => array("javascript: opener.add_attachment_covide(", "%id", "); window.close();"),
		"check" => "%attachment"
	)
));
?>
