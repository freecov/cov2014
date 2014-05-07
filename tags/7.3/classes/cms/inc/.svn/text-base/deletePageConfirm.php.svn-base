<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("delete page")
	));

	$cms_data = new Cms_data();

	if ($pageid == "buffer") {
		$cms_data->decodeOptions();
		$buffer = $cms_data->opts["buffer"];
		foreach ($buffer as $bufferid) {
			$ids[$bufferid] = $cms_data->getChildPages($bufferid);
		}
		$cms = array();
		foreach ($ids as $k=>$v) {
			foreach ($v["data"] as $part)
			$cms["data"][$part["id"]] = $part;
		}
		$delids = implode(",", $buffer);
	} else {
		$cms = $cms_data->getChildPages($pageid);
		$delids = $pageid;
	}

	$venster->addVensterData();

	$venster->addCode( gettext("You are about to remove the following pages").": ");
	$venster->addTag("br");
	foreach ($cms["data"] as $v) {
		if ($v["xs_rv"]) {
			$deny = 1;
		}
	}
	if ($deny == 1) {
		$venster->addTag("br");

		$tbl = new Layout_table( array(
			"style" => "border: 2px dotted red"
		));
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->insertAction("important", "", "");
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode( gettext("You cannot remove the pagetree because you have insufficient permissions on the pages marked red."));
				$tbl->addTag("br");
				$tbl->addCode( gettext("Contact someone who can grant you permissions."));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->insertAction("important", "", "");
			$tbl->endTableData();
			$tbl->endTable();

			$venster->addCode( $tbl->generate_output() );
			unset($tbl);
	}
	$venster->addTag("br");


	$view = new Layout_view();
	$view->addData($cms["data"]);

	$view->addMapping( gettext("page title"), "%%complex_name");

	$view->defineComplexMapping("complex_name", array(
		array(
			"text" => array("%spacing", " ")
		),
		array(
			"type" => "action",
			"src"  => "folder_open",
			"check" => "%xs"
		),
		array(
			"type" => "action",
			"src"  => "folder_denied",
			"check" => "%xs_rv"
		),
		array(
			"text" => array(" ", "%title")
		)
	));

	$venster->addCode($view->generate_output());

	if ($_REQUEST["switchsiteroot"]) {
		$venster->start_javascript();
			$venster->addCode(sprintf("
				function switchSiteRoot() {
					opener.location.href = '?mod=cms&cmd=switchsiteroot&id=%s';
				}
				var tx = setTimeout('switchSiteRoot()', 500);
			", $_REQUEST["switchsiteroot"]));
		$venster->end_javascript();
	}
	$venster->addTag("br");
	$venster->insertAction("back", gettext("back"), "javascript: window.close();");
	$venster->addSpace(3);
	if (!$deny) {
		$venster->insertAction("ok", gettext("continue to delete"), "?mod=cms&action=delete_pages_exec&id=".$delids);
	}
	$venster->endVensterData();


	$output->addCode( $venster->generate_output() );
	$output->exit_buffer();
?>