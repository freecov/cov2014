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

$fs_obj = new Filesys_data();
$fs_data = $fs_obj->getFiles(array(
	"folderid"  => $id,
	"no_xs"     => $noactions,
	"sort"      => $_REQUEST["sortfile"],
	"max_size"  => $max_size,
	"highlight" => $_REQUEST["infile"],
	"search"    => $_REQUEST["search"],
	"subaction" => $_REQUEST["subaction"]
));
$output = new Layout_output();

/* create view for files */
$view = new Layout_view();
$view->addData($fs_data);
$view->hideWhenEmpty(1);
$view->addMapping(gettext("name"), "%%complex_name");
$view->addMapping(gettext("description"), "%description");

$hp = $fs_obj->getHighestParent($id);
$view->addMapping(gettext("size"), "%size_human");
if ($GLOBALS["covide"]->license["has_cms"] && $hp["name"] == "cms") {
	if ($_REQUEST["subaction"] != "cmsfile" && $_REQUEST["subaction"] != "cmsimage") {
		$view->addMapping(gettext("location"), array("/cmsfile/", "%id"));
		$view->addMapping(gettext("used by page"), "%%cms_pages");
	}
}

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
$view->defineSort(gettext("date"), "timestamp");
$view->defineSort(gettext("size"), "size");
$view->defineSort(gettext("user"), "username");
$view->defineHighLight("%highlight", "color_buffer");
$view->setHtmlField("selectbox");

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
			"src"   => "multi_download",
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
	if ($_REQUEST["subaction"] != "cmsfile" && $_REQUEST["subaction"] != "cmsimage") {
		$view->defineComplexMapping("header_actions", array(
			array(
				"type"  => "action",
				"src"   => "file_zip",
				"alt"   => gettext("download selected files as zip archive"),
				"link"  => "javascript: selection_files_zip();"
			),
			array(
				"type"  => "action",
				"src"   => "multi_download",
				"alt"   => gettext("download selected files"),
				"link"  => "javascript: selection_files_download();"
			),
			array(
				"text"  => $output->insertCheckbox(array("checkbox_files_toggle_all"), "1", 0, 1)
			)
		));
	} else {
		$view->defineComplexMapping("header_actions", array());
	}
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
			"src"   => "multi_download",
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
	/*
	array(
		"type"  => "action",
		"src"   => "important",
		"alt"   => gettext("this file is currently selected"),
		"link"  => "javascript: alert(gettext('this file is currently selected'));",
		"check" => "%highlight"
	),
	*/
	array(
		"type"  => "action",
		"src"   => "file_attach",
		"alt"   => gettext("use as cms file"),
		"link"  => array("javascript: cmsPreview('", "%id", "', '".$GLOBALS["covide"]->webroot."');"),
		"check" => "%cmsaction"
	),
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
		"link"  => array("javascript: download(", "%id", ");"),
		"check" => "%id"
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
		"link"  => array("javascript: file_remove(", "%id", ", $id);")
		#"check" => "%show_actions"
	),
	array(
		"text"  => $output->insertCheckbox(array("checkbox_file[","%id","]"), "1", 0, 1),
		"check" => "%show_checkbox"
	),
	array(
		"type"  => "action",
		"src"   => "view",
		"alt"   => gettext("view"),
		"link"  => array("javascript: popup('", "%data_url", "');"),
		"check" => "%data_url"
	),
	array(
		"type"  => "action",
		"src"   => "edit",
		"alt"   => gettext("edit"),
		"link"  => array("javascript: popup('", "%edit_url", "');"),
		"check" => "%edit_url"
	),
	array(
		"text"  => "%selectbox",
		"check" => "%selectbox"
	),
	array(
		"type"  => "action",
		"src"   => "file_attach",
		"alt"   => gettext("add as attachment"),
		"link"  => array("javascript: add_attachment(", "%id", ");"),
		"check" => "%attachment"
	),
	array(
		"type"  => "text",
		"text"  => array("<a name=\"file_", "%id", "\"></a>")
	)
));
?>
