<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */


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
	$view->addMapping(gettext("from"), "%sender_emailaddress_h");
	$view->addMapping(gettext("to"), "%h_to", "", "", 1, "", 1);
	$view->addMapping(gettext("subject"), "%subject");
	$view->addMapping(gettext("cc"), "%h_cc", "", "", 1, "", 1);
	$view->addMapping(gettext("bcc"), "%h_bcc", "", "", 1, "", 1);
	$view->addMapping(gettext("reply to"), "%replyto");
	$view->addMapping(gettext("date sent"), "%h_date", "", "list_hidden");
	$view->addMapping(gettext("date received"), "%h_date_received");
	$view->addMapping(gettext("mail tracking"), "%mail_tracking");
	$view->addMapping(gettext("read confirmation"), "%%complex_readconfirm");
	$view->addMapping(gettext("priority"), "%priority");
	$view->addMapping(gettext("classifications"), "%classifications_sent", "", "list_hidden");
	$view->addMapping(gettext("user agent"), "%user_agent_1");
	$view->addMapping(gettext("mail agent"), "%user_agent_2");
	$view->addMapping(gettext("mailbox"), "%mailbox");
	$view->addMapping(gettext("spam score"), "%%spam_score");
	$view->addMapping(gettext("attachments"), "%%data_attachments", "", "", 1);
	$view->addMapping(gettext("all attachments"), "%%multi_download");
	$view->addMapping(gettext("forward/reply with"), $buf);

	/* define readconfirm icon and text */
	$view->defineComplexMapping("complex_readconfirm", array(
		array(
			"type"  => "action",
			"src"   => "mail_readconfirm",
			"check" => "%readconfirm"
		),
		array(
			"text"  => array(" ".gettext("Sender requisted read confirmation")),
			"check" => "%readconfirm"
		)
	));

	/* define multiple download mapping */
	$view->defineComplexMapping("multi_download", array(
		array(
			"type"    => "action",
			"src"     => "save",
			"alt"     => gettext("save all attachments in Covide filesystem"),
			"link"    => array("javascript: save_attachments('", "%attachments_ids", "')"),
			"check" => "%attachments_count"
		),
		array(
			"type"  => "action",
			"link"  => "javascript: multiple_download();",
			"alt"   => gettext("download all attachments as zip archive"),
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
				gettext("from"),
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
			"alt"     => gettext("save in covide filesystem"),
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
			"alt"     => gettext("delete"),
			"link"    => array("javascript: attachment('", "%id", "', 'delete', '", "%message_id","')")
		),
		array(
			"type"    => "action",
			"src"     => "open",
			"alt"     => gettext("show"),
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
		"subtitle" => gettext("show")
	));

	$venster->addMenuItem( gettext("new"), "javascript: mail_action('new')");
	$venster->addMenuItem( gettext("reply"), "javascript: mail_action('reply')");
	$venster->addMenuItem( gettext("reply all"), "javascript: mail_action('reply_all')");
	$venster->addMenuItem( gettext("forward"), "javascript: mail_action('forward')");

	if ($mdata["folder_id"] == 1 && $user->checkPermission("xs_relationmanage") || $mdata["folder_id"] > 1)
		$venster->addMenuItem( gettext("delete"), "javascript: mail_action('delete')");

	$venster->addMenuItem( gettext("print"), "javascript: mail_action('print')");
	$venster->addMenuItem( gettext("back"), "javascript: history_goback()");
	$venster->generateMenuItems();
	$venster->addVensterData();

	$table_actions = new Layout_table( array("width"=>"100%", "style"=>"padding-top: 5px;") );
	$table_actions->addTableRow();
		$table_actions->addTableData(array("colspan"=>2));
			$table_actions->insertAction("back", gettext("back"), "javascript: history_goback();");
			$table_actions->insertAction("mail_headers", gettext("header information"), "javascript: mail_action('info');");
			$table_actions->insertAction("print", gettext("print"), "javascript: mail_action('print');");
			$table_actions->insertAction("mail_reply", gettext("reply"), "javascript: mail_action('reply');");
			$table_actions->insertAction("mail_reply_all", gettext("reply all"), "javascript: mail_action('reply_all');");
			$table_actions->insertAction("mail_forward", gettext("forward"), "javascript: mail_action('forward');");

			if ($mdata["folder_id"] == 1 && $user->checkPermission("xs_relationmanage") || $mdata["folder_id"] > 1)
				$table_actions->insertAction("delete", gettext("delete"), "javascript: mail_action('delete');");

			if (!$mdata["is_text"]) {
				$table_actions->addSpace(5);
				if ($viewmode == "html") {
					$table_actions->insertAction("ftype_text", gettext("show text"), "javascript: mail_view_html('text');");
					$table_actions->addCode(" ".gettext("show text"));
				} else {
					$table_actions->insertAction("ftype_html", gettext("show html version"), "javascript: mail_view_html('html');");
					$table_actions->addCode(" ".gettext("show html version"));
				}
			}
			if ($mdata["vcard"]) {
				$table_actions->addSpace(5);
				$table_actions->insertAction("state_special", gettext("vcard contact information"), "javascript: attachment('".$mdata["vcard"]."', 'view')");
				$table_actions->addCode(" ".gettext("vcard contact information"));
			}
			if (!$mdata["is_text"]) {
				$table_actions->addTag("span", array(
					"id" => "js_show_inline",
					"style" => "display: none"
				));
					$table_actions->addTag("br");
					$table_actions->addTag("br");
					$table_actions->addTag("div", array(
						"style" => "border: 1px dotted #999;"
					));
					$table_actions->insertTag("span", $this->notfound_image."?", array(
						"style" => "display: none;",
						"id"    => "notfound_image"
					));
					$table_actions->insertTag("a", gettext("This email contains external images. To protect your privacy, these images have been disabled. Click here to display these images:")." ", array(
						"href"  => "javascript: enable_inline_images();"
					));
					$table_actions->endTag("div");
				$table_actions->endTag("span");

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
				$table_options->insertTableData(gettext("contact").": ", "", "header");
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
				$table_options->insertTableData(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project").": ", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array("id"=>"project_name"));
					if ($mdata["project_id"]) {
						$project = new Project_data();
						$project_info = $project->getProjectById($mdata["project_id"]);
						$table_options->addCode( $project_info[0]["name"] );
					} else {
						$table_options->addCode(gettext("none"));
					}
					$table_options->endTag("span");
					$table_options->insertAction("edit", gettext("change:"), "javascript: pickProject();");
				$table_options->endTableData();
			$table_options->endTableRow();

			/* description */
			$table_options->addTableRow();
				$table_options->addTableData("", "header");
					$table_options->addCode( gettext("description").":");
					$table_actions->addTag("br");
				$table_actions->endTableData();
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array("id"=>"description_notify") );
					$table_options->endTag("span");
					$table_options->insertAction("save", gettext("save"), "javascript: description_save();");
					$table_options->addTag("br");
					$table_options->addTextArea("mail[description]", $mdata["description"], array("style"=>"width: 250px; height: 150px;"));
				$table_options->endTableData();
			$table_options->endTableRow();

			/* publiek/niet publiek */
			$table_options->addTableRow();
				$table_options->addTableData("", "header");
					$table_options->addCode( gettext("public").":");
				$table_options->endTableData();
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array(
						"id" => "private_state"
					));
					if ($mdata["is_public"]==0) {
						$table_options->addCode( gettext("this email is public") );
						$table_options->insertAction("state_public", gettext("this email is public"), "");
					} else {
						$table_options->addCode( gettext("this email is private") );
						$table_options->insertAction("state_private", gettext("this email is private"), "");
					}
					$table_options->endTag("span");
					$table_options->insertAction("toggle", gettext("alter public/private state"), "javascript: toggle_private_state();");
				$table_options->endTableData();
			$table_options->endTableRow();

			/* folders */
			$table_options->addTableRow();
				$table_options->insertTableData(gettext("folder").":", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );

				$table_options->insertAction("help", gettext("dont return when changing folder"), "javascript: alert('".gettext("help: dont return when changing folder")."');");
				$table_options->addCheckBox("mail[nojump]", 1);
				$table_options->addTag("br");

				$folders = $mailData->getFolders("", $mdata["address_id"]);
				$folders_shared = $mailData->getSharedFolderAccess($_SESSION["user_id"]);

				$table_options->addCode($this->getSelectList("mail[folder]", $folders, $mdata["folder_id"], array("style"=>"width: 250px"), $folders_shared ) );
				$table_options->endTableData();
			$table_options->endTableRow();

			/* users */
			$userObject = new User_output();
			$userData = new User_data();
			$users = $userData->getUserList(1);


			$table_options->addTableRow();
				$table_options->insertTableData(gettext("user").":", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data");

				$useroutput = new User_output();
				$table_options->addHiddenField("mail[users]", $mdata["user_id"]);
				$table_options->addTag("div", array("style"=>"text-align: left"));
					$table_options->addCode( $useroutput->user_selection("mailusers", $mdata["user_id"], 1, 0, 0, 1) );
					$table_options->insertAction("mail_move", gettext("move to user"), "javascript: user_move();");
					$table_options->insertAction("note", gettext("move to user and send note"), "javascript: user_move(1);");
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

		$mailcontent->insertTag("div", $mdata["body_hl"], array(
			"style" => "overflow: auto; width: 100%;"
		));
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
	$output->insertAction("back", gettext("back"), "javascript: history_goback();");

	$url = $_SERVER["QUERY_STRING"];
	$output->insertAction("up", gettext("up"), "?$url#top");
	$output->endTag("center");

	$history = new Layout_history();
	$output->addCode( $history->generate_history_call() );

	$output->layout_page_end();
	echo $output->generate_output();
?>
