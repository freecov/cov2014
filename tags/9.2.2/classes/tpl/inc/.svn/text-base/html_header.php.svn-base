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
	//$defmain = $this->cms->getCmsSettings();
	$defmain = $this->cms_license;

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
	$q = sprintf("select date_changed, pageHeader, pageTitle, datePublication, search_title from cms_data where id = %d", $this->pageid);
	$res = sql_query($q);
	if (sql_num_rows($res) > 0) {
		$row = sql_fetch_assoc($res);
		$date_publication = $row["datePublication"];
		$date_changed     = $row["date_changed"];

		if ($row["search_title"]) $search_title = $row["search_title"];
		if ($row["search_descr"]) $search_descr = $row["search_descr"];
		if ($row["search_lang"])  $search_lang  = $row["search_lang"];

		if ($search_descr == $rowmain["search_descr"] && trim($row["pageHeader"]) && !$row["search_descr"])
			$search_descr = htmlentities(trim($row["pageHeader"]));
	}
	switch ($rowmain["search_use_pagetitle"]) {
		case 1:
			$search_title .= sprintf(" - ", $row["pageTitle"]);
			break;
		case 2:
			$search_title = sprintf('%s | %s', $row['pageTitle'], $search_title);
			break;
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
	if ($this->pageid == "__sitemap")
		$search_descr .= " (Sitemap)";

	$output = new Layout_output();
	$output->addCode("\n\t");
	$output->insertTag("title", $search_title);
	$output->addCode("\n");
	$output->addCode("
	<!-- Covide CMS : Cooperative Virtual Desktop (c) 2000-2009-->
	<!-- License    : Licensed under GPL -->
	<!-- Web        : http://www.covide.net, http://covide.sourceforge.net -->
	<!-- Author     : Stephan van de Haar (svdhaar@users.sourceforge.net) -->
	<!-- Info       : info@covide.nl -->\n\n");

	$output->addCode("\t<!-- [base href and document encoding] -->\n\t");
	$output->addTag("base", array(
		"href" => $this->protocol.$_SERVER["HTTP_HOST"].$this->base_dir.'/'
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"http-equiv" => "Content-Type",
		"content"    => "text/html; charset=UTF-8"
	));
	$output->addCode("\n\n");
	$output->addCode("\t<!-- [search engine information] -->\n\t");
	$output->addTag("meta", array(
		"name"       => "reply-to",
		"content"    => $rowmain["search_email"]
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "description",
		"content"    => str_replace("&", "&amp;", preg_replace("/(\n)|(\r)|()/si", "", $search_descr))
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "keywords",
		"content"    => str_replace("&", "&amp;", preg_replace("/(\n)|(\r)|()/si", "", $search_fields))
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "robots",
		"content"    => "INDEX, FOLLOW"
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "copyright",
		"content"    => str_replace("&", "&amp;", $rowmain["search_copyright"])
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "author",
		"content"    => str_replace("&", "&amp;", $rowmain["search_author"])
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "resource-type",
		"content"    => "document"
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "generator",
		"content"    => "Covide CMS System ".$GLOBALS["covide"]->vernr
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "audience",
		"content"    => "general"
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "web-rev",
		"content"    => "4.0"
	));
	$output->addCode("\n\t");
	$output->addTag("meta", array(
		"name"       => "last-modified",
		"content"    => $date_changed
	));
	$output->addCode("\n\t");
	if ($rowmain["google_verify"]) {
		$output->addTag("meta", array(
			"name"       => "google-site-verification",
			"content"    => $rowmain["google_verify"]
		));
		$output->addCode("\n\t");
	}
	if ($rowmain["yahoo_key"]) {
		$output->addTag("meta", array(
			"name"       => "y_key",
			"content"    => $rowmain["yahoo_key"]
		));
		$output->addCode("\n\t");
	}
	if ($rowmain["bing_key"]) {
		$output->addTag("meta", array(
			"name"       => "msvalidate.01",
			"content"    => $rowmain["bing_key"]
		));
		$output->addCode("\n\t");
	}

	if ($search_lang) {
		$output->addTag("meta", array(
			"http-equiv"  => "content-language",
			"content"     => $search_lang
		));
	}
	$output->addCode("\n\n");
	$output->addCode("\t<!-- [favicons] -->\n\t");
	$output->addTag("link", array(
		"rel"  => "shortcut icon",
		"href" => $this->favicon
	));
	$output->addCode("\n\t");
	$output->addTag("link", array(
		"rel"  => "icon",
		"href" => $this->favicon
	));
	$p = ($this->page_less_rewrites) ? "/":"/page/";
	if ($this->pageid != $this->default_page) {
		$alias = $this->checkAlias($this->pageid);
	} else {
		$alias = '';
	}

	$uri["txt"] = $this->protocol.$_SERVER["HTTP_HOST"]."/text/".$this->checkAlias($this->pageid);
	$uri["def"] = $this->protocol.$_SERVER["HTTP_HOST"].$p.$alias;
	
	#if (is_numeric($this->pageid)) {
		$output->addCode("\n\n\t");
		$output->addCode("<!-- canonical -->\n\t");
		$output->addTag('link', array(
			'rel' => 'canonical',
			'href' => $uri['def']
		));
	#}

	$output->addCode("\n\n");
	$output->addCode("\t<!-- [rss feed and text version] -->\n\t");
	if (is_array($this->rss)) {
		foreach ($this->rss as $name=>$u) {
			$output->addTag("link", array(
				"rel"   => "alternate",
				"title" => $name,
				"type"  => "application/rss+xml",
				"href"  => $u
			));
			$output->addCode("\n\t");
		}
	}
	$output->addTag("link", array(
		"rel"   => "search",
		"type"  => "application/opensearchdescription+xml",
		"href"  => sprintf("%s%s/search.xml", $this->protocol, $this->http_host),
		"title" => "opensearch"
	));

	$output->addCode("\n\n");
	$output->addCode("\t<!-- [cms style sheets] -->\n\t");

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
	$output->addCode("\n\t");
	$output->addTag("link", array(
		"rel"  => "stylesheet",
		"type" => "text/css",
		"href" => $output->external_file_cache_handler("classes/tpl/inc/site.css")
	));
	$output->addCode("<!-- classes/tpl/inc/site.css -->");
	if (!$this->textmode) {
		$output->addCode("\n\n");
		$output->addCode("\t<!-- [cms javascript files] -->\n\t");

		$output->load_javascript("classes/html/inc/jquery.js");
		$output->load_javascript("classes/html/inc/jqueryplugins/md5.js");
		$output->load_javascript("classes/html/inc/xmlhttp.js");
		$output->addCode("\t");
		$output->load_javascript("classes/html/inc/js_classes.js");
		$output->addCode("\t");
		$output->load_javascript("classes/html/inc/js_popups.js");
		$output->addCode("\t");
		$output->load_javascript("classes/html/inc/Prompt.js");
		$output->addCode("\t");
		$output->load_javascript("classes/tpl/inc/site.js");

		$output->addCode("\t<!--[if IE]>\n\t\t");
		$output->addTag("link", array(
			"rel"  => "stylesheet",
			"type" => "text/css",
			"href" => $output->external_file_cache_handler("classes/tpl/inc/site_msie.css")
		));
		$output->addCode("\n\t<![endif]-->\n\t");
		/*
		$output_gecko = new Layout_output();
		$output_gecko->addTag("link", array(
			"rel"  => "stylesheet",
			"type" => "text/css",
			"href" => $output->external_file_cache_handler("classes/tpl/inc/site_gecko.css")
		));
		$output_opera = new Layout_output();
		$output_opera->addTag("link", array(
			"rel"  => "stylesheet",
			"type" => "text/css",
			"href" => $output->external_file_cache_handler("classes/tpl/inc/site_opera.css")
		));
		$output->start_javascript(true);
			$output->addCode(sprintf("
	if (navigator.userAgent.indexOf('Gecko') != -1)
		document.write('%s');
	else if (navigator.userAgent.indexOf('Opera') != -1)
		document.write('%s');\n\t",
				addslashes($output_gecko->generate_output()),
				addslashes($output_opera->generate_output())
			));
		$output->end_javascript(true);
		*/
	}
	$output->addCode("\n\n");
	$output->addCode("\t<!-- [user template specific js and css include] -->\n");
?>
