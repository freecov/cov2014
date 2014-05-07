<?
	if (!class_exists("Filesys_output")) {
		exit("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("filesys", ($_REQUEST["subaction"]) ? 1:0);

	if ($_REQUEST["search"]) {
		$subtitle = gettext("gezocht naar").": ".$_REQUEST["search"];
	} else {
		$subtitle = gettext("zoeken");
	}
	/* window widget */
	$venster = new Layout_venster(array(
		"title" => gettext("bestandsbeheer"),
		"subtitle" => $subtitle
	));
	$venster->addMenuItem(gettext("terug"), "./?mod=filesys&subaction=".$_REQUEST["subaction"]."&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"]);
	$venster->generateMenuItems();

	$venster->addVensterData();

	$venster->addCode( gettext("zoeken naar bestanden en mappen").": ");
	$venster->addTextField("search", $_REQUEST["search"]);
	$venster->start_javascript();
		$venster->addCode("
			document.getElementById('search').focus();
		");
	$venster->end_javascript();
	$venster->insertAction("forward", gettext("zoeken"), "javascript: document.getElementById('velden').submit();");

	if ($_REQUEST["search"]) {
		$filesys_data = new Filesys_data();
		$data = $filesys_data->searchAll($_REQUEST["search"]);

		/* create view for folders */
		$view = new Layout_view();
		$view->addData($data["folders"]);
		$view->addMapping(gettext("mapnaam"), "%%complex_name");
		$view->addMapping(gettext("omschrijving"), "%description");

		$view->defineComplexMapping("complex_name", array(
			array(
				"type"  => "action",
				"src"   => "folder_closed"
			),
			array(
				"type"  => "link",
				"text"  => array(" ", "%name"),
				"link"  => array("?mod=filesys&action=opendir&subaction=".$_REQUEST["subaction"]."&search=1&id=", "%id")
			)
		));

		$venster->addCode( $view->generate_output() );
		$venster->addTag("br");

		/* create view for files */
		$view = new Layout_view();
		$view->addData($data["files"]);
		$view->addMapping(gettext("bestandsnaam"), "%%complex_name");
		$view->addMapping(gettext("map"), "%%complex_folder");
		$view->addMapping(gettext("omschrijving"), "%description");
		$view->addMapping(gettext("datum"), "%date_human");
		$view->addMapping(gettext("gebruiker"), "%user_name");
		$view->addMapping(" ", "%%complex_att");

		$view->defineComplexMapping("complex_folder", array(
			array(
				"type"  => "action",
				"src"   => "folder_global"
			),
			array(
			"type"  => "link",
			"text"  => array(" ", "%folder_name"),
			"link"  => array("?mod=filesys&action=opendir&subaction=".$_REQUEST["subaction"]."&search=1&id=", "%folder_id")
			)
		));


		$view->defineComplexMapping("complex_name", array(
			array(
				"type"  => "action",
				"src"   => "%fileicon"
			),
			array(
				"type"  => "link",
				"link"  => array("?dl=1&mod=filesys&action=fdownload&id=", "%id"),
				"text"  => array(" ", "%name")
			)
		));
		$view->defineComplexMapping("complex_att", array(
			array(
				"type"  => "action",
				"src"   => "file_attach",
				"alt"   => gettext("voeg toe als attachment"),
				"link"  => array("javascript: opener.add_attachment_covide(", "%id", "); window.close();"),
				"check" => "%attachment"
			)
		));

		$venster->addCode( $view->generate_output() );
	}

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "filesys");
	$output->addHiddenField("action", "search");
	$output->addHiddenField("pastebuffer", $_REQUEST["pastebuffer"]);
	$output->addHiddenField("ids", $_REQUEST["ids"]);
	$output->addHiddenField("subaction", $_REQUEST["subaction"]);
	$output->addCode( $venster->generate_output() );

	$history = new Layout_history();
	$output->addCode( $history->generate_save_state() );

	$output->layout_page_end();

	$output->exit_buffer();
?>
