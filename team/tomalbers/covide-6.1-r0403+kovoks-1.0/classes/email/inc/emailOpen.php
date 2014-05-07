<?php

	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	$mail_id = $_REQUEST["id"];

	/* define basic email data object */
	$mailData = new Email_data();
	$data = $mailData->getEmailById($mail_id);
	$mdata =& $data[0];

	$mailData->updateReadStatus($mail_id, $_SESSION["user_id"]);

	$user = new User_data();
	$user->getUserPermissionsById($_SESSION["user_id"]);
	$usersettings = $user->getUserdetailsById($_SESSION["user_id"]);

	/* start output handler */
	$output = new Layout_output();
	$output->layout_page();

	$output->addTag("form", array(
		"id" => "velden"
	));

	$viewmode = $_REQUEST["viewmode"];
	if (!$viewmode) {
		if (!$usersettings["mail_view_textmail_only"] && !$mdata["is_text"]) {
			$viewmode = "html";
		} else {
			$viewmode = "text";
		}
	}

	$output->addHiddenField("mod", $_REQUEST["mod"]);
	$output->addHiddenField("action", $_REQUEST["action"]);
	$output->addHiddenField("id", $_REQUEST["id"]);
	$output->addHiddenField("viewmode", $viewmode);
	$output->addHiddenField("mail[relation]", $mdata["address_id"]);

	$view = new Layout_view();
	$view->addData($data);

	/* get Email Aliases */
	$aliases = $mailData->getEmailAliases();
	$alias_key = array_search( $mailData->cleanAddress($mdata["to"]), $mailData->getEmailAliasesPlain() );


	$output_alt = new Layout_output();
	$output_alt->addSelectField("mail[from]", $aliases, $alias_key );
	$buf = $output_alt->generate_output();
	unset($output_alt);

	/* add the mappings (columns) we needed */
	$view->addMapping(gettext("reply"), "%replyto");
	$view->addMapping(gettext("van"), "%sender_emailaddress_h");
	$view->addMapping(gettext("naar"), "%h_to", "", "", 1);
	$view->addMapping(gettext("onderwerp"), "%subject");
	$view->addMapping(gettext("cc"), "%h_cc", "", "", 1);
	$view->addMapping(gettext("bcc"), "%h_bcc", "", "", 1);
	$view->addMapping(gettext("reply naar"), "%replyto");
	$view->addMapping(gettext("datum verzonden"), "%h_date", "", "list_hidden");
	$view->addMapping(gettext("datum ontvangen"), "%h_date_received");
	$view->addMapping(gettext("mail tracking"), "%mail_tracking");
	$view->addMapping(gettext("leesbevestiging"), "%%complex_readconfirm");
	$view->addMapping(gettext("prioriteit"), "%priority");
	$view->addMapping(gettext("classificaties"), "%classifications_sent", "", "list_hidden");
	$view->addMapping(gettext("user agent"), "%user_agent_1");
	$view->addMapping(gettext("mail agent"), "%user_agent_2");
	$view->addMapping(gettext("mailbox"), "%mailbox");
	$view->addMapping(gettext("spam score"), "%%spam_score");
	$view->addMapping(gettext("bijlagen"), "%%data_attachments", "", "", 1);
	$view->addMapping(gettext("alle bijlagen"), "%%multi_download");
	$view->addMapping(gettext("forward/reply met"), $buf);

	/* define readconfirm icon and text */
	$view->defineComplexMapping("complex_readconfirm", array(
		array(
			"type"  => "action",
			"src"   => "mail_readconfirm",
			"check" => "%readconfirm"
		),
		array(
			"text"  => array(" ".gettext("De afzender heeft om leesbevestiging gevraagd")),
			"check" => "%readconfirm"
		)
	));

	/* define multiple download mapping */
	$view->defineComplexMapping("multi_download", array(
		array(
			"type"    => "action",
			"src"     => "save",
			"alt"     => gettext("alle bijlagen opslaan in Covide bestandsbeheer"),
			"link"    => array("javascript: save_attachments('", "%attachments_ids", "')"),
			"check" => "%attachments_count"
		),
		array(
			"type"  => "action",
			"link"  => "javascript: multiple_download();",
			"alt"   => gettext("download alle attachments als zip bestand"),
			"src"   => "file_zip",
			"check" => "%attachments_count"
		)
	));

	/* define spam score mapping */
	$view->defineComplexMapping("spam_score", array(
		array(
			"text" => array(
				"%spam_score_hits",
				" ",
				gettext("van"),
				" ",
				"%spam_score_max",
				" (",
				"%spam_percentage",
				")"
			),
			"check" => "%spam_percentage"
		)
	));

	/* define complex mapping for array of attachments */
	$view->defineComplexMapping("data_attachments", array(
		array(
			"type"    => "array",
			"array"   => "attachments",
			"mapping" => "%%data_attachment"
		)
	));
	/* define complex mapping for the single attachment row */
	/* use a new output object to prepare the format */
	$view->defineComplexMapping("data_attachment", array(
		array(
			"type"    => "action",
			"src"     => "save",
			"alt"     => gettext("opslaan in Covide bestandsbeheer"),
			"link"    => array("javascript: save_attachments('", "%id", "')")
		),
		array(
			"type"    => "action",
			"src"     => "file_download",
			"alt"     => gettext("download"),
			"link"    => array("javascript: attachment('", "%id", "', 'download')")
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("verwijderen"),
			"link"    => array("javascript: attachment('", "%id", "', 'delete', '", "%message_id","')")
		),
		array(
			"type"    => "action",
			"src"     => "open",
			"alt"     => gettext("tonen"),
			"link"    => array("javascript: attachment('", "%id", "', 'view')"),
			"check"   => "%subview"
		),
		array(
			"text" => " "
		),
		array(
			"type" => "action",
			"src" => "%fileicon"
		),
		array(
			"text" => " "
		),
		array(
			"text" => "%name"
		),
		array(
			"text" => " ("
		),
		array(
			"text" => "%h_size"
		),
		array(
			"text" => ")"
		)
	));

	$venster = new Layout_venster(Array(
		"title"    => gettext("Email"),
		"subtitle" => gettext("tonen")
	));


	$venster->addMenuItem( gettext("nieuw"), "javascript: mail_action('new')");
	$venster->addMenuItem( gettext("antwoorden"), "javascript: mail_action('reply')");
	$venster->addMenuItem( gettext("alle antwoorden"), "javascript: mail_action('reply_all')");
	$venster->addMenuItem( gettext("doorsturen"), "javascript: mail_action('forward')");

	if ($mdata["folder_id"] == 1 && $user->checkPermission("xs_relationmanage") || $mdata["folder_id"] > 1)
		$venster->addMenuItem( gettext("verwijderen"), "javascript: mail_action('delete')");

	$venster->addMenuItem( gettext("printen"), "javascript: mail_action('print')");
	$venster->addMenuItem( gettext("terug"), "javascript: history_goback()");
	$venster->generateMenuItems();
	$venster->addVensterData();

	$table_actions = new Layout_table( array("width"=>"100%", "style"=>"padding-top: 5px;") );
	$table_actions->addTableRow();
		$table_actions->addTableData(array("colspan"=>2));
			$table_actions->insertAction("back", gettext("terug"), "javascript: history_goback();");
			$table_actions->insertAction("mail_headers", gettext("header informatie"), "javascript: mail_action('info');");
			$table_actions->insertAction("print", gettext("printen"), "javascript: mail_action('print');");
			$table_actions->insertAction("mail_reply", gettext("antwoorden"), "javascript: mail_action('reply');");
			$table_actions->insertAction("mail_reply_all", gettext("alle antwoorden"), "javascript: mail_action('reply_all');");
			$table_actions->insertAction("mail_forward", gettext("doorsturen"), "javascript: mail_action('forward');");

			if ($mdata["folder_id"] == 1 && $user->checkPermission("xs_relationmanage") || $mdata["folder_id"] > 1)
				$table_actions->insertAction("delete", gettext("verwijderen"), "javascript: mail_action('delete');");

			if (!$mdata["is_text"]) {
				$table_actions->addSpace(5);
				if ($viewmode == "html") {
					$table_actions->insertAction("ftype_text", gettext("toon text"), "javascript: mail_view_html('text');");
					$table_actions->addCode(" ".gettext("toon text"));
				} else {
					$table_actions->insertAction("ftype_html", gettext("toon html"), "javascript: mail_view_html('html');");
					$table_actions->addCode(" ".gettext("toon html"));
				}
			}

		$table_actions->endTableData();
	$table_actions->endTableRow();
	$table_actions->endTable();

	$table_layout = new Layout_table( array("width"=>"100%") );
	$table_layout->addTableRow();
		$table_layout->addTableData( array("style"=>"vertical-align: top;") );
			$table_layout->addCode( $view->generate_output_vertical() );
		$table_layout->endTableData();
		$table_layout->addTableData( array("valign"=>"top") );

			$address = new Address_data();
			$address_info = $address->getAddressNameByID($mdata["address_id"]);

			$table_options = new Layout_table( array("cellspacing"=>1, "align"=>"right"), 1 );
			$table_options->addTableRow();
				$table_options->insertTableData(gettext("relatie").": ", "", "header");
				$table_options->addTableData(array("align"=>"right"), "data");
					if ($mdata["address_id"]) {
						$table_options->addTag("a", array(
							"href"=>"?mod=address&action=relcard&id=".$mdata["address_id"]
						));
						$table_options->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
						$table_options->endTag("a");
					} else {
						$table_options->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
					}
					$table_options->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');", 700, 600, 1);
				$table_options->endTableData();
			$table_options->endTableRow();
			$table_options->addTableRow();
				$table_options->insertTableData(gettext("project").": ", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array("id"=>"project_name"));
					if ($mdata["project_id"]) {
						$project = new Project_data();
						$project_info = $project->getProjectById($mdata["project_id"]);
						$table_options->addCode( $project_info[0]["name"] );
					} else {
						$table_options->addCode(gettext("geen"));
					}
					$table_options->endTag("span");
					$table_options->insertAction("edit", "wijzigen", "javascript: popup('?mod=project&action=search_project', 'search_project');");
				$table_options->endTableData();
			$table_options->endTableRow();

			/* description */
			$table_options->addTableRow();
				$table_options->addTableData("", "header");
					$table_options->addCode( gettext("omschrijving").":");
					$table_actions->addTag("br");
				$table_actions->endTableData();
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array("id"=>"description_notify") );
					$table_options->endTag("span");
					$table_options->insertAction("save", gettext("opslaan"), "javascript: description_save();");
					$table_options->addTag("br");
					$table_options->addTextArea("mail[description]", $mdata["description"], array("style"=>"width: 250px; height: 100px;"));
				$table_options->endTableData();
			$table_options->endTableRow();

			/* publiek/niet publiek */
			$table_options->addTableRow();
				$table_options->addTableData("", "header");
					$table_options->addCode( gettext("publiek").":");
				$table_options->endTableData();
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array(
						"id" => "private_state"
					));
					if ($mdata["is_public"]==0) {
						$table_options->addCode( gettext("deze email is publiek toegankelijk") );
						$table_options->insertAction("state_public", gettext("deze email is publiek toegankelijk"), "");
					} else {
						$table_options->addCode( gettext("deze email is alleen prive toegankelijk") );
						$table_options->insertAction("state_private", gettext("deze email is alleen prive toegankelijk"), "");
					}
					$table_options->endTag("span");
					$table_options->insertAction("toggle", gettext("wijzig publiek/prive status"), "javascript: toggle_private_state();");
				$table_options->endTableData();
			$table_options->endTableRow();

			/* folders */
			$table_options->addTableRow();
				$table_options->insertTableData(gettext("map").":", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );

				$table_options->insertAction("help", gettext("ga niet terug bij het kiezen van een andere map"), "javascript: alert('".gettext("help: ga niet terug bij het kiezen van een andere map")."');");
				$table_options->addCheckBox("mail[nojump]", 1);
				$table_options->addTag("br");

				$folders = $mailData->getFolders("", $mdata["address_id"]);
				$table_options->addCode($this->getSelectList("mail[folder]", $folders, $mdata["folder_id"], array("style"=>"width: 250px") ) );
				$table_options->endTableData();
			$table_options->endTableRow();

			/* users */
			$userObject = new User_output();
			$userData = new User_data();
			$users = $userData->getUserList(1);


			$table_options->addTableRow();
				$table_options->insertTableData(gettext("gebruiker").":", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data");

				$useroutput = new User_output();
				$table_options->addHiddenField("mail[users]", $mdata["user_id"]);
				$table_options->addTag("div", array("style"=>"text-align: left"));
					$table_options->addCode( $useroutput->user_selection("mailusers", $mdata["user_id"], 1, 0, 0, 1) );
					$table_options->insertAction("mail_move", gettext("verplaats naar gebruiker"), "javascript: user_move();");
					$table_options->insertAction("note", gettext("verplaats naar gebruiker en stuur notitie"), "javascript: user_move(1);");
				$table_options->endTag("div");



				$table_options->addSpace(2);
				$table_options->endTableData();
			$table_options->endTableRow();



			$table_options->endTable();
			$table_layout->addCode( $table_options->generate_output() );

		$table_layout->endTableData();
	$table_layout->endTableRow();
	$table_layout->addTableRow();
		$table_layout->addTableData();
			$table_layout->addCode( $table_actions->generate_output() );
		$table_layout->endTableData();
	$table_layout->endTableRow();
	$table_layout->endTable();

	$venster->addCode( $table_layout->generate_output() );
	$venster->endVensterData();

	$output->addCode( $venster->generate_output() );

	$mailcontent = new Layout_venster();
	$mailcontent->addVensterData();

	if ($viewmode == "html" && !$mdata["is_text"]) {
		/* if data is html and the user has requested html */
		$params = array("mod=email", "action=viewhtml", "id=".$_REQUEST["id"]);
		$mailcontent->addTag("iframe", array(
			"src"         => "index.php?".implode("&amp;", $params),
			"style"       => "border: 0px; width: 100%; height: 250px;",
			"name"        => "mailContent",
			"id"          => "mailContent",
			"frameborder" => "no",
			"border"      => "0"
		));
		$mailcontent->endTag("iframe");
	} else {
		/* display plain text version */
		$mailcontent->addTag("br");

		$mailcontent->addCode( $mdata["body_pre"]);
		$mailcontent->addTag("br");
		$mailcontent->addTag("br");
	}
	$mailcontent->endVensterData();

	$output->addTag("br");
	$output->addCode( $mailcontent->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir_main."xmlhttp.js");
	$output->load_javascript(self::include_dir_main."js_form_actions.js");
	$output->load_javascript(self::include_dir."emailOpen.js");

	$output->addTag("br");
	$output->addTag("center");
	$output->insertAction("back", gettext("terug"), "javascript: history_goback();");

	$url = $_SERVER["QUERY_STRING"];
	$output->insertAction("up", gettext("naar boven"), "?$url#top");
	$output->endTag("center");

	$history = new Layout_history();
	$output->addCode( $history->generate_history_call() );

	$output->layout_page_end();
	echo $output->generate_output();
?>
