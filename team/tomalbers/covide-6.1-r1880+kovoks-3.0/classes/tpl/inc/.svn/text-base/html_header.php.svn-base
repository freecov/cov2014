<?php
	//global $_rewrite_id, $_default_page;

	$q = "select * from cms_license";
	$resmain = mysql_query($q) or die($q);
	$rowmain = mysql_fetch_array($resmain);

	$rowmain = $this->cms->getCmsSettings();

	//follow parent tree
	if (!$pageid) {
		$pageid = $this->cms->default_page;
	}
	$path = $this->cms->getPath($pageid);

	$ids = implode(",", $path);
	$q = "select id, pageTitle from cms_data where id IN ($ids) and search_override = 1";
	$res = sql_query($q);
	$ids = "";
	while ($row = @mysql_fetch_assoc($res)) {
		$ids[$row["id"]]= $row["pageTitle"];
	}
	$flag = 0;
	foreach ($path as $k=>$v) {
		if ($ids[$v] && !$flag) {
			$flag = $v;
		}
	}

	if ($flag > 0) {
		$q = "select search_fields, search_descr, search_title, search_language from cms_data where id = ".$flag;
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
	$q = "select date_changed, pageTitle from cms_data where id = ".(int)$pageid;
	$res = sql_query($q);
	if (sql_num_rows($res) > 0) {
		$date_changed = sql_result($res, 0, "date_changed");
		if ($date_changed > 0) {
			$date_changed = date("r", $date_changed);
		} else {
			$date_changed = date("r", mktime(0,0,0,1,1,2005));
		}
		if ($rowmain["search_use_pagetitle"]) {
			$search_title.= " - ".sql_result($res, 0, "paginaTitel");
		}
	}

	$output = new Layout_output();
	$output->insertTag("title", $search_title);
	$output->addCode("\n");
	$output->addTag("base", array(
		"href" => "http://".$_SERVER["HTTP_HOST"]."/"
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
		"content"    => preg_replace("/(\n)|(\r)|(\t)/si", "", $search_descr)
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "Keywords",
		"content"    => preg_replace("/(\n)|(\r)|(\t)/si", "", $search_fields)
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "robots",
		"content"    => "all"
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "copyright",
		"content"    => $rowmain["search_copyright"]
	));
	$output->addCode("\n");
	$output->addTag("meta", array(
		"name"       => "Author",
		"content"    => $rowmain["search_author"]
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
	$output->addTag("meta", array(
		"name"       => "verify-v1",
		"content"    => $rowmain["google_verify"]
	));
	$output->addCode("\n");
	if ($search_lang) {
		$output->addTag("meta", array(
			"http-equiv"  => "content-language",
			"content"     => $search_lang
		));
	}
	$output->addCode("\n");
	$output->addTag("link", array(
		"rel"  => "shortcut icon",
		"href" => "/img/cms/favicon.png"
	));
	$output->addCode("\n");
	$output->addTag("link", array(
		"rel"  => "icon",
		"href" => "/img/cms/favicon.png"
	));
	$output->addCode("\n");
	if ($pageid != $this->cms->default_page) {
		$uri["rss"] = "http://".$_SERVER["HTTP_HOST"]."/rss/live/".$this->checkAlias($pageid);
	} else {
		$uri["rss"] = "http://".$_SERVER["HTTP_HOST"]."/rss";
	}
	$uri["txt"] = "http://".$_SERVER["HTTP_HOST"]."/text/".$this->checkAlias($pageid);
	$uri["def"] = "http://".$_SERVER["HTTP_HOST"]."/page/".$this->checkAlias($pageid);

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

	$output->load_javascript("classes/html/inc/xmlhttp.js");
	$output->load_javascript("classes/html/inc/js_classes.js");
	$output->load_javascript("classes/html/inc/js_popups.js");
	$output->load_javascript("classes/tpl/inc/site.js");

?>