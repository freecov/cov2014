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
	// set read
	$oldstatus = $mailData->updateReadStatus($mail_id, $_SESSION["user_id"]);
	$data = $mailData->getEmailById($mail_id);
	$mdata =& $data[0];
	if (!array_key_exists("is_public", $mdata)) {
		$restricted = true;
	} else {
		$mailData->updateReadStatus($mail_id, $_SESSION["user_id"]);
	}

	$user = new User_data();
	$user->getUserPermissionsById($_SESSION["user_id"]);
	$usersettings = $user->getUserdetailsById($_SESSION["user_id"]);

	/* start output handler */
	$output = new Layout_output();
	$output->layout_page(gettext("email"), $_REQUEST["hide"]);

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
	$output->addHiddenField("mail[bcard]", $mdata["bcard_id"]);
	$output->addHiddenField("mail[project]", $mdata["project_id"]);
	$output->addHiddenField("popup_newwindow", $usersettings["popup_newwindow"]);

	$view = new Layout_view();
	$view->addData($data);

	/* get Email Aliases */
	$aliases = $mailData->getEmailAliases();
	$alias_key = array_search( $mailData->cleanAddress($mdata["to"]), $mailData->getEmailAliasesPlain() );


	$output_alt = new Layout_output();
	$output_alt->addSelectField("mail[from]", $aliases, $alias_key );
	$buf = $output_alt->generate_output();
	unset($output_alt);
	$mail_stati = array(
		"0" => gettext("read"),
		"1" => gettext("unread"),
	);
	$output_read = new Layout_output();
	$output_read->addSelectField("readstatus", $mail_stati, $mdata["is_new"]);
	$buf_read = $output_read->generate_output();
	unset($output_read);

	$actions_sel = array(
		"void(0);" => " - ".gettext("more actions")." - ",
		"javascript: mail_action('new')" => gettext("new"),
		"javascript: mail_action('resend')" => gettext("resend"),
		"javascript: mail_action('support');" => gettext("new support"),
		"javascript: mail_action('info');" => gettext("header information"),
	);
	if (!$mdata["is_text"]) {
		if ($viewmode == "html") {
			$actions_sel["javascript: mail_view_html('text');"] = gettext("show text");
		} else {
			$actions_sel["javascript: mail_view_html('html');"] = gettext("show html version");
		}
	}
	if ($mdata["vcard"]) {
		$actions_sel["javascript: attachment('".$mdata["vcard"]."', 'view')"] = gettext("vcard contact information");
	}
	$actions_sel["javascript: popup('index.php?mod=address&action=edit_bcard&id=0&addresstype=bcards&sub=&email=".$mdata["clean_emailaddress"]."', 'addressedit', 800, 600, 1);"] = gettext("new businesscard");
	$output_moreactions = new Layout_output();
	$output_moreactions->addSelectField("email[actions]", $actions_sel);
	$buf_moreactions = $output_moreactions->generate_output();
	unset($output_moreactions);


	/* add the mappings (columns) we needed */
	$view->addMapping(gettext("reply"), "%replyto");
	$view->addMapping(gettext("from"), "%sender_emailaddress_h");
	$view->addMapping(gettext("to"), "%h_to", "", "", 1, "", 1);
	$view->addMapping(gettext("subject"), "%subject");
	if (!$restricted) {
		$view->addMapping(gettext("cc"), "%h_cc", "", "", 1, "", 1);
		$view->addMapping(gettext("bcc"), "%h_bcc", "", "list_hidden", 1, "", 1);
		$view->addMapping(gettext("reply to"), "%replyto", "", "list_hidden");
		$view->addMapping(gettext("status"), $buf_read);
		$view->addMapping(gettext("date sent"), "%h_date", "", "list_hidden");
		$view->addMapping(gettext("date received"), "%h_date_received", "");
		$view->addMapping(gettext("mail tracking"), "%mail_tracking", "", "list_hidden");
		$view->addMapping(gettext("read confirmation"), "%%complex_readconfirm", "", "list_hidden");
		$view->addMapping(gettext("priority"), "%priority", "", "list_hidden");
		$view->addMapping(gettext("classifications"), "%classifications_sent", "", "list_hidden");
		$view->addMapping(gettext("user agent"), "%user_agent_1", "", "list_hidden");
		$view->addMapping(gettext("mail agent"), "%user_agent_2", "", "list_hidden");
		$view->addMapping(gettext("mailbox"), "%mailbox", "", "list_hidden");
		$view->addMapping(gettext("spam score"), "%%spam_score", "", "list_hidden");
		$view->addMapping(gettext("attachments"), "%%data_attachments", "", "", 1);
		$view->addMapping(gettext("all attachments"), "%%multi_download");
		$view->addMapping(gettext("forward/reply with"), $buf);
	}
	$view->addMapping(gettext("actions"), "%%complex_actions");
	
	/* commonly used actions deserve an icon */
	$view->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "back",
			"alt"  => gettext("back"),
			"link" => "javascript: history_goback();",
		),
		array(
			"type" => "action",
			"src"  => "mail_reply",
			"alt"  => gettext("reply"),
			"link" => "javascript: mail_action('reply');",
			"check" => "%readaccess"
		),
		array(
			"type" => "action",
			"src"  => "mail_reply_all",
			"alt"  => gettext("reply all"),
			"link" => "javascript: mail_action('reply_all');",
			"check" => "%readaccess"
		),
		array(
			"type" => "action",
			"src"  => "mail_forward",
			"alt"  => gettext("forward"),
			"link" => "javascript: mail_action('forward');",
			"check" => "%readaccess"
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("delete"),
			"link" => "javascript: mail_action('delete');",
			"check" => "%readaccess"
		),
		array(
			"type" => "action",
			"src"  => "print",
			"alt"  => gettext("print"),
			"link" => "javascript: mail_action('print');",
			"check" => "%readaccess"
		),
		array(
			"type" => "action",
			"src"  => "tab_expand",
			"alt"  => gettext("show extra fields"),
			"link" => "javascript: show_extra_fields();",
			"check" => "%readaccess"
		),
		array(
			"text" => array(" ".$buf_moreactions),
			"check" => "%readaccess"
		),
	));

	/* define readconfirm icon and text */
	$view->defineComplexMapping("complex_readconfirm", array(
		array(
			"type"  => "action",
			"src"   => "mail_readconfirm",
			"check" => "%readconfirm"
		),
		array(
			"text"  => array(" ".gettext("Sender requested read confirmation")),
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

	$venster->addVensterData();

	$table_actions = new Layout_table( array("width"=>"100%", "style"=>"padding-top: 5px;") );
	$table_actions->addTableRow();
		$table_actions->addTableData(array("colspan"=>2));
			if (!$restricted && !$mdata["is_text"]) {
				$table_actions->addTag("span", array(
					"id" => "js_show_inline",
					"style" => "display: none"
				));
					$table_actions->addTag("br");
					$table_actions->addTag("br");
					$table_actions->addTag("div", array(
						"style" => "border: 1px dotted #999;"
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

	$table_layout = new Layout_table( array("width"=>"100%", "border" => 0) );
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
					$table_options->insertAction("edit", gettext("change"), "javascript: popup('?mod=address&action=searchRel', 'search_address', 700, 600, 1);");
				$table_options->endTableData();
			$table_options->endTableRow();

			if ($mdata["address_id"]) {
				$address_data = new Address_data();
				$bcardinfo = $address_data->getBcardsByRelationID($mdata["address_id"]);
				unset($address_data);
			} else {
				$bcardinfo = array();
			}
			$bcards = array(0 => gettext("none"));
			foreach($bcardinfo as $v) {
				$bcards[$v["id"]] = $v["givenname"]." ".$v["infix"]." ".$v["surname"];
			}
			$table_options->addTableRow();
				$table_options->insertTableData(gettext("businesscard"), "", "header");
				$table_options->addTableData(array("align"=>"right"), "data");
					$table_options->addHiddenField("mail[bcard_selected]", $mdata["bcard_id"]);
					$table_options->addTag("div", array("id" => "mail_bcard_layer"));
					$table_options->endTag("div");
				$table_options->endTableData();
			$table_options->endTableRow();

			$table_options->addTableRow();
				$table_options->insertTableData(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project").": ", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );
					$table_options->addTag("span", array("id"=>"project_name"));
					if ($mdata["project_id"]) {
						$project = new Project_data();
						$project_info = $project->getProjectById($mdata["project_id"]);
						$table_options->addTag("a", array(
							"href"=>"?mod=project&action=showhours&id=".$mdata["project_id"]
						));
						$table_options->addCode( $project_info[0]["name"] );
						$table_options->endTag("a");
					} else {
						$table_options->addCode(gettext("none"));
					}
					$table_options->endTag("span");
					$table_options->insertAction("edit", gettext("change"), "javascript: pickProject();");
				$table_options->endTableData();
			$table_options->endTableRow();
			
			$table_options->addTableRow(array("id" => "mailoptions_privateaddress", "class" => "list_hidden"));
				$table_options->insertTableData(gettext("private").": ", "", "header");
				$table_options->addTableData( array("align"=>"right"), "data" );
					if ($mdata["private_id"]) {
						$address = new Address_data();
						$address_info = $address->getRecord(array("id"=>$mdata["private_id"], "type"=>"user"));
						$table_options->addTag("a", array(
							"href"=>"?mod=address&action=showPrivate&private_id=".$mdata["private_id"]
						));
						$table_options->insertTag("span", $address_info["tav"], array("id"=>"layer_mail_private"));
						$table_options->endTag("a");
					} else {
						$table_options->insertTag("span", gettext("none"), array("id"=>"layer_mail_private"));
					}
					$table_options->insertAction("edit", gettext("change"), "javascript: pickPrivate();");
				$table_options->endTableData();
			$table_options->endTableRow();
			/* folders */
			$table_options->addTableRow(array("id" => "mailoptions_folders"));
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

			/* publiek/niet publiek */
			$table_options->addTableRow(array("id" => "mailoptions_private"));
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

			/* users */
			$userObject = new User_output();
			$userData = new User_data();
			$users = $userData->getUserList(1);

			$table_options->addTableRow();
			$table_options->addTableRow(array("id" => "mailoptions_users", "class" => "list_hidden"));
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
			if (!$restricted) {
				$table_layout->addCode( $table_options->generate_output() );
			}

		$table_layout->endTableData();
	$table_layout->endTableRow();
	$table_layout->addTableRow(array("id" => "mailoptions_description", "class" => "list_hidden"));
		$table_layout->addTableData(array("colspan" => 2));

			$table_desc = new Layout_table(array("width" => "100%"));
			$table_desc->addTableRow();
				$table_desc->addTableData(array("width" => "2%"), "header");
					$table_desc->addCode( gettext("description").":");
				$table_desc->endTableData();
				$table_desc->addTableData( array("align"=>"right", "width" => "92%"), "data" );
					$table_desc->addTag("span", array("id"=>"description_notify") );
					$table_desc->endTag("span");
					$table_desc->insertAction("save", gettext("save"), "javascript: description_save();");
					$table_desc->addTag("br");
					$table_desc->addTextArea("mail[description]", $mdata["description"], array("style"=>"width: 98%; height: 200px;"));
				$table_desc->endTableData();
			$table_desc->endTableRow();
			$table_desc->endTable();
			$table_layout->addCode($table_desc->generate_output());
		$table_layout->endTableData();
	$table_layout->endTableRow();
	$table_layout->addTableRow();
		$table_layout->addTableData();
			$table_layout->addCode( $table_actions->generate_output() );
		$table_layout->endTableData();
	$table_layout->endTableRow();
	$table_layout->endTable();

	$venster->addCode( $table_layout->generate_output() );
	if (!$restricted) {
		/* if data is html and the user has requested html */
		//mobile devices cannot handle iframes, so fall back to the old method
		if ($GLOBALS["covide"]->mobile) {
			$venster->addTag("br");

			$venster->insertTag("div", $mdata["body_hl"], array(
			));
			$venster->addTag("br");
			$venster->addTag("br");
		} else {
			if ($viewmode == "html" && !$mdata["is_text"])
				$act = "viewhtml";
			else
				$act = "viewtext";

			$params = array("mod=email", "action=".$act, "id=".$_REQUEST["id"]);
			$venster->addTag("iframe", array(
				"src"         => "index.php?".implode("&amp;", $params),
				"style"       => "border: 0px; width: 100%; height: 250px;",
				"name"        => "mailContent",
				"id"          => "mailContent",
				"frameborder" => "no",
				"border"      => "0"
			));
			$venster->endTag("iframe");
		}
	}
	$venster->endVensterData();

	$output->addTag("br");
	$output->addCode( $venster->generate_output() );

	$output->endTag("form");

	$output->load_javascript(self::include_dir_main."xmlhttp.js");
	$output->load_javascript(self::include_dir_main."js_form_actions.js");
	$output->load_javascript(self::include_dir."emailOpen.js");

	$history = new Layout_history();
	$output->addCode( $history->generate_history_call() );
	$output->start_javascript();
	$output->addCode("updateBcards();");
	// code to send read-notification
	if ($data[0]["readconfirm"] == 1 && $oldstatus == 1) {
		$output->addCode("
			if (confirm(gettext('".gettext("Sender asked for read-notification. Send notification?")."'))) {
				loadXML('index.php?mod=email&action=send_readnotification&mail_id=".$data[0]["id"]."');
			}
		");
	}
	$output->end_javascript();
	$output->layout_page_end();
	echo $output->generate_output();
?>
