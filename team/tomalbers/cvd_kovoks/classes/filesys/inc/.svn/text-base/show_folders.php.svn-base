<?php
if (!class_exists("Filesys_output")) {
	exit("no class definition found");
}

$fs_data =& $data;
if ($fs_data["is_shortcut"]) {
	$descr = gettext("gerelateerde relatie mappen");
} else {
	$descr = gettext("mappen");
}

/* create view for folders */
$view = new Layout_view();
$view->addData($fs_data["data"]);
$view->addMapping($descr, "%%complex_name");
$view->addMapping(gettext("omschrijving"), "%description");
$view->addMapping("%%header_actions", "%%complex_actions", "right");

if ($_REQUEST["id"]) {
	$view->defineSortForm("sortfolder", "velden");
	$view->defineSort($descr, "name");
	$view->defineSort(gettext("omschrijving"), "description");
}

$output = new Layout_output();

/* define complex mapping for header actions */
if ($_REQUEST["id"]) {
	$view->defineComplexMapping("header_actions", array(
		array(
			"text" => " "
		)
	));
} else {
	$view->defineComplexMapping("header_actions", array(
		array(
			"text" => " "
		)
	));
}

$view->defineComplexMapping("complex_actions", array(
	array(
		"type"  => "action",
		"src"   => "edit",
		"alt"   => gettext("wijzigen"),
		"link"  => array("javascript: editFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "delete",
		"alt"   => gettext("verwijderen"),
		"link"  => array("javascript: deleteFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "cut",
		"alt"   => gettext("knippen/verplaatsen"),
		"link"  => array("javascript: cutFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "permissions",
		"alt"   => gettext("aangepaste rechten uitdelen"),
		"link"  => array("javascript: editPermissions('", "%id", "');"),
		"check" => "%xs_edit"
	)
));

$view->defineComplexMapping("complex_name", array(
	#array(
	#	"text"  => "%xs"
	#),
	array(
		"type"  => "action",
		"src"   => "%foldericon",
		"alt"   => gettext("open map"),
		"link"  => array("index.php?mod=filesys&action=opendir&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"]."&subaction=".$_REQUEST["subaction"]."&address=".$_REQUEST["address"]."&id=", "%id"),
		"check" => "%allow"
	),
	array(
		"type"  => "action",
		"src"   => "%foldericon",
		"alt"   => gettext("geen toegang"),
		"link"  => "javascript: void(0);",
		"check" => "%disallow"
	),
	array(
		"text" => " "
	),
	array(
		"type"  => "link",
		"text"  => "%h_name",
		"link"  => array("index.php?mod=filesys&action=opendir&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"]."&subaction=".$_REQUEST["subaction"]."&address=".$_REQUEST["address"]."&id=", "%id"),
		"check" => "%allow"
	),
	array(
		"type"  => "text",
		"text"  => "%h_name",
		"check" => "%disallow"
	),
));
?>
