<?php
/**
 * Covide Groupware-CRM Filesys module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
if (!class_exists("Filesys_output")) {
	exit("no class definition found");
}

$user_data = new User_data();
$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

$fs_data =& $data;
if ($fs_data["is_shortcut"]) {
	if ($fs_data["is_relations"]) {
		$descr = gettext("related contact folders");
	} elseif ($fs_data["is_projects"]) {
		$descr = gettext("related project folders");
	} else {
		$descr = gettext("related folders");
	}
} else {
	$descr = gettext("folders");
}

/* create view for folders */
$view = new Layout_view();
$view->addData($fs_data["data"]);
$view->hideWhenEmpty(1);
$view->addMapping($descr, "%%complex_name");

if ($fs_data["current_folder"]["hp_name"] == "cms" && $user_info["xs_cms_level"] == 3)
	$view->addMapping(gettext("# files/folders/id"), array("%filecount", " / ", "%foldercount", " / ", "%id"));
else
	$view->addMapping(gettext("# files/folders"), array("%filecount", " / ", "%foldercount"));

$view->addMapping(gettext("description"), "%description");
$view->addMapping("%%header_actions", "%%complex_actions", "right");

if ($_REQUEST["id"]) {
	$view->defineSortForm("sortfolder", "velden");
	$view->defineSort($descr, "name");
	$view->defineSort(gettext("description"), "description");
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
		"alt"   => gettext("change:"),
		"link"  => array("javascript: editFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "delete",
		"alt"   => gettext("delete"),
		"link"  => array("javascript: deleteFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "cut",
		"alt"   => gettext("cut/move"),
		"link"  => array("javascript: cutFolder('", "%id", "');"),
		"check" => "%xs_folder_actions"
	),
	array(
		"type"  => "action",
		"src"   => "permissions",
		"alt"   => gettext("grant permissions"),
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
		"alt"   => gettext("open folder"),
		"link"  => array("index.php?mod=filesys&action=opendir&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"]."&subaction=".$_REQUEST["subaction"]."&address=".$_REQUEST["address"]."&id=", "%id"),
		"check" => "%allow"
	),
	array(
		"type"  => "action",
		"src"   => "%foldericon",
		"alt"   => gettext("no access"),
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
