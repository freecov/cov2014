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
		"subtitle" => gettext("CMS banner management")
	));

	$cms_data = new Cms_data();
	$infile = $cms_data->stripHosts("\"".$_REQUEST["in"], 1);
	if (preg_match("/^\"\/{0,1}cmsgallery\/sponsors\/(\d{1,})$/s", $infile)) {
		$infile = (int)preg_replace("/^\"\/{0,1}cmsgallery\/sponsors\/(\d{1,})$/s", "$1", $infile);
	} else {
		$infile = 0;
	}

	$cms = $cms_data->getGalleryData(-1, array(
		"search" => $_REQUEST["search"],
		"highlight" => $infile
	));

	$venster->addVensterData();
		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));

	$view = new Layout_view();
	$view->addData($cms);

	$view->addMapping( gettext("name"), "%file" );
	$view->addMapping( gettext("rating"), "%rating_h" );
	$view->addMapping( gettext("url"), "%url" );
	$view->addMapping( " ", "%%complex_actions" );

	if ($pick) {
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"  => "action",
				"src"   => "important",
				"alt"   => gettext("this file is currently selected"),
				"link"  => array("javascript: alert('", gettext("this file is currently selected"), "');"),
				"check" => "%highlight"
			),
			array(
				"type"  => "action",
				"src"   => "file_attach",
				"alt"   => gettext("use as cms file"),
				"link"  => array("javascript: cmsPreview('", "%id", "', '".$GLOBALS["covide"]->webroot."');")
			),
			array(
				"type"  => "text",
				"text"  => array("<a name=\"file_", "%id", "\"></a>")
			)
		));
	} else {
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=cms&action=editBanner&id=", "%id")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteBanner&id=", "%id", "');")
			)
		));
	}
	$venster->addTag("form", array(
		"id" => "velden",
		"action" => "index.php",
		"method" => "get"
	));
	$venster->addHiddenField("mod", "cms");
	$venster->addHiddenField("action", "pick_banner");
	$venster->addCode(gettext("Search").": ");
	$venster->addTextField("search", $_REQUEST["search"]);
	$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit();");
	$venster->addTag("br");

	$venster->addCode($view->generate_output());
	$venster->endTag("form");

	$venster->start_javascript();
	$venster->addCode("
		function cmsPreview(id, webroot) {
			if (parent.document.getElementById('f_href')) {
				parent.document.getElementById('f_href').value = webroot + 'cmsgallery/sponsors/' + id + '&size=small';
			} else if (parent.document.getElementById('f_url')) {
				parent.document.getElementById('f_url').value = webroot + 'cmsgallery/sponsors/' + id + '&size=small'
				parent.onPreview();
			}
			void(0);
		}
	");
	$venster->end_javascript();
	$venster->endVensterData();

	$output->addCode($venster->generate_output());
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>