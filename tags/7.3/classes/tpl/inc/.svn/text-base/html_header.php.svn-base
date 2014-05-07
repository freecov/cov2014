<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	/* get default settings for this tree */
	$rowmain = $this->cms->getCmsSettings($this->siteroot);
	$defmain = $this->cms->getCmsSettings();

	if (!$rowmain["cms_name"])
		$rowmain["cms_name"] = $defmain["cms_name"];

	$path = $this->cms->getPath($this->pageid);

	foreach ($path as $k=>$v) {
		$path[$k] = (int)$v;
	}
	$ids = implode(",", $path);
	$q = "select id, pageTitle from cms_data where id IN ($ids) and search_override = 1";
	$res = sql_query($q);
	$ids = "";
	while ($row = sql_fetch_assoc($res)) {
		$ids[$row["id"]]= $row["pageTitle"];
	}
	$flag = 0;
	foreach ($path as $k=>$v) {
		if ($ids[$v] && !$flag) {
			$flag = $v;
		}
	}
	if ($flag > 0) {
		$q = sprintf("select search_fields, search_descr, search_title, search_language from cms_data where id = %d", $flag);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$search_fields 	= $row["search_fields"];
		$search_descr 	= $row["search_descr"];
		$search_title 	= $row["search_title"];
		$search_lang 		= $row["search_language"];

		if (!$search_fields) {
			$search_fields 	= $rowmain["search_fields"];
		}
		if (!$search_descr) {
			$search_descr 	= $rowmain["search_descr"];
		}
		if (!$search_title) {
			$search_title 	= $rowmain["cms_name"];
		}
		if (!$search_lang) {
			$search_lang 	= $rowmain["search_language"];
		}
	} else {
		$search_fields	= $rowmain["search_fields"];
		$search_descr		= $rowmain["search_descr"];
		$search_title		= $rowmain["cms_name"];
		$search_lang		= $rowmain["search_language"];
	}
	$q = sprintf("select date_changed, pageTitle, datePublication, search_title from cms_data where id = %d", $this->pageid);
	$res = sql_query($q);
	if (sql_num_rows($res) > 0) {
		$row = sql_fetch_assoc($res);
		$date_publication = $row["datePublication"];
		$date_changed     = $row["date_changed"];

		if ($row["search_title"]) $search_title = $row["search_title"];
		if ($row["search_descr"]) $search_descr = $row["search_descr"];
		if ($row["search_lang"])  $search_lang  = $row["search_lang"];

		if (!$row["search_title"]) {
			if ($rowmain["search_use_pagetitle"])
				$search_title.= " - ".$row["pageTitle"];
		}

	}
	if (!$date_changed) {
		/* update to cms */
		$date_changed = $date_publication;

		if (!$date_changed)
			$date_changed = mktime(0,0,0,1,1,2005);
	}
	$date_changed = date("r", $date_changed);

	/* check if some field are filled */
	if (!$rowmain["search_copyright"])
		$rowmain["search_copyright"] = $defmain["search_copyright"];
	if (!$rowmain["search_author"])
		$rowmain["search_author"] = $defmain["search_author"];
	if (!$rowmain["search_email"])
		$rowmain["search_email"] = $defmain["search_email"];

	if (!$search_fields)
		$search_fields = $defmain["search_fields"];
	if (!$search_descr)
		$search_descr = $defmain["search_descr"];
	if (!$search_title)
		$search_title = $defmain["cms_name"];
	if (!$search_lang)
		$search_lang  = $defmain["search_language"];
	if (!$search_lang || $search_lang == "on")
		$search_lang  = "en";

	$output = new Layout_output();
	$output->insertTag("title", $search_title);
	$output->addCode("\n");
	$output->addCode("
<!-- Covide CMS : Cooperative Virtual Desktop (c) 2000-2007-->
<!-- License    : Licensed under GPL -->
<!-- Web        : http://www.covide.net, http://covide.sourceforge.net -->
<!-- Author     : Stephan van de Haar (svdhaar@users.sourceforge.net) -->
<!-- Info       : info@covide.nl -->\n\n");
	$output->addTag("base", array(
		"href" => $this->protocol.$_SERVER["HTTP_HOST"].$this->base_dir
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"http-equiv" => "Content-Type",
		"content"    => "text/html; charset=UTF-8"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "reply-to",
		"content"    => $rowmain["search_email"]
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "Description",
		"content"    => str_replace("&", "&amp;", preg_replace("/(\n)|(\r)|(\t)/si", "", $search_descr))
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "Keywords",
		"content"    => str_replace("&", "&amp;", preg_replace("/(\n)|(\r)|(\t)/si", "", $search_fields))
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "robots",
		"content"    => "all"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "copyright",
		"content"    => str_replace("&", "&amp;", $rowmain["search_copyright"])
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "Author",
		"content"    => str_replace("&", "&amp;", $rowmain["search_author"])
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "resource-type",
		"content"    => "document"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "generator",
		"content"    => "Covide CMS System ".$GLOBALS["covide"]->vernr
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "audience",
		"content"    => "general"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "web-rev",
		"content"    => "4.0"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "last-modified",
		"content"    => $date_changed
	));
	$output->addCode("\n");
	if ($rowmain["google_verify"]) {
		$output->addTag("meta", array(
			"name"       => "verify-v1",
			"content"    => $rowmain["google_verify"]
		));
		$output->addCode("\n");
	}
	if ($search_lang) {
		$output->addTag("meta", array(
			"http-equiv"  => "content-language",
			"content"     => $search_lang
		));
	}
	$output->addCode("\n");
	$output->addTag("link", array(
		"rel"  => "shortcut icon",
		"href" => $this->favicon
	));
	$output->addCode("\n");
	$output->addTag("link", array(
		"rel"  => "icon",
		"href" => $this->favicon
	));
	$output->addCode("\n");

	$uri["rss"] = $this->rss;
	$uri["txt"] = $this->protocol.$_SERVER["HTTP_HOST"]."/text/".$this->checkAlias($this->pageid);
	$uri["def"] = $this->protocol.$_SERVER["HTTP_HOST"]."/page/".$this->checkAlias($this->pageid);

	$output->addTag("link", array(
		"rel"   => "alternate",
		"title" => "RSS Feed",
		"type"  => "application/rss+xml",
		"href"  => $uri["rss"]
	));
	$output->addCode("\n");
	if ($textmode) {
		$output->addTag("link", array(
			"rel"   => "alternate",
			"title" => "Html version",
			"type"  => "text/html",
			"href"  => $uri["def"],
			"media" => "screen, all"
		));
	} else {
		$output->addTag("link", array(
			"rel"   => "alternate",
			"title" => "Text version",
			"type"  => "text/html",
			"href"  => $uri["txt"],
			"media" => "tty, aural, braille, print, pda"
		));
	}
	$output->addCode("\n");
	$output->addCode("<link rel='stylesheet' type='text/css' href='/classes/tpl/inc/site.css'>\n");
	$output->load_javascript("classes/html/inc/xmlhttp.js");
	$output->load_javascript("classes/html/inc/js_classes.js");
	$output->load_javascript("classes/html/inc/js_popups.js");
	$output->load_javascript("classes/tpl/inc/site.js");

?>