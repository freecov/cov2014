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
		"subtitle" => gettext("list of pages")
	));

	$cms_data = new Cms_data();
	$data = $cms_data->searchPages($_REQUEST["search"], $_REQUEST["start"], $_REQUEST["in"]);

	$venster->addVensterData();

	$venster->addCode(gettext("search for page").":");
	$venster->addSpace(2);

	$venster->addTextField("search", $_REQUEST["search"]);
	$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit();");

	$view = new Layout_view();
	$view->addData($data["pages"]);

	$view->addMapping( gettext("page id"), "%id" );
	$view->addMapping( gettext("page title"), "%pageTitle" );
	$view->addMapping( gettext("url"), "%pageAlias_h" );
	$view->addMapping( gettext("publication date"), "%datePublication_h" );
	$view->addMapping("", "%%complex_link");
	$view->defineHighLight("%highlight", "color_buffer");

	$view->defineComplexMapping("complex_link", array(
		array(
			"type"  => "action",
			"src"   => "view",
			"alt"   => gettext("view on website"),
			"link"  => array("javascript: popup('http://".$_SERVER["HTTP_HOST"]."/page/", "%id",".htm');")
		),
		array(
			"type"  => "action",
			"src"   => "file_attach",
			"alt"   => gettext("use as cms file"),
			"link"  => array("javascript: cmsPreview('", "%id", "', '".$GLOBALS["covide"]->webroot."');")
		)
	));

	$venster->addCode($view->generate_output());

	$paging = new Layout_paging();
	$paging->setOptions($_REQUEST["start"], $data["count"], "javascript: blader('%%');", 0, 0, 1);
	$venster->addCode( $paging->generate_output() );

	$venster->endVensterData();

		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "get",
			"id"     => "velden"
		));

	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "cms_pagelist");
	$output->addHiddenField("start", $_REQUEST["start"]);
	$output->addHiddenField("in", $_REQUEST["in"]);

	$output->addCode($venster->generate_output());

	$output->endTag("form");
	$output->load_javascript(self::include_dir."cmsPageList.js");
	$output->layout_page_end();
	$output->exit_buffer();
?>