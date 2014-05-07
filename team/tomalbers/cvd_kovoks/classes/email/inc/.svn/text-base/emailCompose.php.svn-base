<?php

	if (!class_exists("Email")) {
		exit("no class definition found");
	}
	/* get all atrributes */
	$mail    = $_REQUEST["mail"];

	/* define basic email data object */
	$mailData = new Email_data();
	$data = $mailData->getEmailById($id);
	$mdata =& $data[0];

	/* start output handler */
	$output = new Layout_output();
	$output->layout_page();
	$output->addTag("form", array(
		"id" => "velden",
		"method" => "POST",
		"action" => "index.php",
		"target" => "dbhandler"
	));

	$output->addHiddenField("mod", $_REQUEST["mod"]);
	$output->addHiddenField("action", "save_concept");
	$output->addHiddenField("id", $id);
	$output->addHiddenField("is_text", $mdata["is_text"]);
	$output->addHiddenField("convert_on_save", 0);
	$output->addHiddenField("saved", 0);
	$output->addHiddenField("js_command", "");

	/* save related id */
	$output->addHiddenField("mail[related_id]", $mdata["related_id"]);

	$email = $mailData->getEmailAliases();


	$table = new Layout_table(array(
		"width"=>"100%",
		"cellspacing"=>1
	));

	/* define left column width */
	$width = "200px";


	/* from */
	$table->addTableRow();
		$table->addTableData(array("width"=>$width), "header");
			$table->addCode( gettext("van") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addSelectField("mail[from]", $email, $mdata["sender_emailaddress"]);
		$table->endTableData();
	$table->endTableRow();

	/* newsletter exception */
	$newsletter = $mailData->get_tracker_items($_REQUEST["id"]);
	if ($newsletter["count"] > 0) {

		$newsletter_fields = array(
			"classifications_positive",
			"classifications_negative",
			"classifications_target",
			"classifications_type",
			"newsletter_target"
		);
		foreach ($newsletter_fields as $v) {
			$output->addHiddenField("mail[".$v."]", $mdata[$v]);
		}


		$newsletter_resume = $mailData->detectResume($_REQUEST["id"]);
		if ($newsletter_resume) {

			/* rcpt */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("resume") );
				$table->endTableData();
				$table->addTableData(array("style"=>"border: 1px dashed red;"), "data");
					$table->addCode( gettext("Het versturen van deze nieuwsbrief is afgebroken.")." " );
					$table->addCode( gettext("De nieuwsbrief is al gedeeltelijk verstuurd.") );
					$table->addTag("br");
					$table->addTag("br");
					$table->addCode( gettext("U kunt het versturen weer oppakken vanaf het punt waar het is afgebroken door op 'verzenden' te klikken. ") );
					$table->addTag("br");
					$table->addCode( gettext("Het systeem zorgt ervoor dat het verzenden verder gaat waar het de vorige keer is gebleven.") );
					$table->addTag("br");
					$table->addTag("br");
					$table->addCode( gettext("Verder gaan met versturen ") ).
					$table->insertAction("mail_send", gettext("verzenden"), "javascript: mail_send();");
				$table->endTableData();
			$table->endTableRow();
		}
		/* rcpt */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("aantal ontvangers") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode( $newsletter["count"]." ".gettext("ontvanger(s)") );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("nieuwsbrief ontvangers") );
			$table->endTableData();
			$table->addTableData("", "data");

				$table->addTag("div", array(
					"style" => "width: 700px; height: 150px; overflow: auto;"
				));

				$counter=0;
				/* build resume array */
				if ($newsletter_resume) {
					$sent_mails = $mailData->detectResumeEmail($_REQUEST["id"]);
				} else {
					$sent_mails = array();
				}

				$_mails[0]= array();
				$_mails[1]= array();

				foreach ($newsletter["list"] as $k=>$v) {
					if ($sent_mails[$v["email"]]==1) {
						$mails[1][] = $v["email"];
					} else {
						$mails[0][] = $v["email"];
					}
				}

				if ($newsletter_resume) {
					$table->addTag("div", array("style"=>"color: #777; border-bottom: 1px solid black;"));
					$table->addCode( @implode(", ", $mails[1]) );
					$table->endTag("div");
				}
				$table->addCode( @implode(", ", $mails[0]) );

				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();

	} else {

		if ($mdata["to"])  $mdata["to"].= ", ";
		if ($mdata["cc"])  $mdata["cc"].= ", ";
		if ($mdata["bcc"]) $mdata["bcc"].= ", ";

		/* rcpt */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("naar") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[rcpt]", $mdata["to"], array("style"=>"width: 400px; height: 50px;"));
				$table->insertAction("view_all", gettext("toon alle gerelateerde email addressen"), "javascript: autoemail_complete_field('mailrcpt', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("voeg een classificatie in"), "javascript: popup('?mod=email&action=selectCla&field=mailrcpt', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();
		/* cc */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("cc") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[cc]", $mdata["cc"], array("style"=>"width: 400px; height: 50px;"));
				$table->insertAction("view_all", gettext("toon alle gerelateerde email addressen"), "javascript: autoemail_complete_field('mailcc', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("voeg een classificatie in"), "javascript: popup('?mod=email&action=selectCla&field=mailcc', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();
		/* bcc */
		$table->addTableRow("", 1);
			$table->addTableData("", "header");
				$table->addCode( gettext("bcc") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[bcc]", $mdata["bcc"], array("style"=>"width: 400px; height: 50px;"));
				$table->insertAction("view_all", gettext("toon alle gerelateerde email addressen"), "javascript: autoemail_complete_field('mailbcc', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("voeg een classificatie in"), "javascript: popup('?mod=email&action=selectCla&field=mailbcc', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();

	}

	if (!$mdata["is_text"]) {
		$table->addTableRow("", 1);
			$table->addTableData("", "header");
				$table->addCode( gettext("template") );
			$table->endTableData();
			$table->addTableData(array("colspan"=>2), "data");
				$table->addSelectField("mail[template]", $mailData->getTemplates(), $mdata["template"]);
				$table->addTag("div", array("id"=>"template_view", "style"=>"display:none") );
				$table->addTag("br");

				$table_template = new Layout_table();
				$table_template->addTableRow();
					$table_template->addTableData();
						$table_template->addCode( gettext("soort template").": " );
					$table_template->endTableData();
					$table_template->addTableData();
						$table_template->addSelectField("mail[template_type]", array(
							"external" => gettext("naladen van internet (standaard)"),
							"tracking" => gettext("naleden van internet met tracking"),
							"inline"   => gettext("inline afbeeldingen")
						), $mdata["template_type"]
					);
					$table_template->endTableData();
				$table_template->endTableRow();
				$table_template->addTableRow();
					$table_template->addTableData();
						$table_template->addCode( gettext("template lettertype").": " );
					$table_template->endTableData();
					$table_template->addTableData();
						$table_template->addSelectField("mail[template_font]", array(
							"arial,serif"       => gettext("Arial"),
							"courier,monospace" => gettext("Courier New"),
							"georgia,serif"     => gettext("Georgia"),
							"tahoma,serif"      => gettext("Tahoma"),
							"times,serif"       => gettext("Times new roman"),
							"verdana,serif"     => gettext("Verdana"),
							"palatino linotype,serif" => gettext("Palatino Linotype")
						), $mdata["template_font"]);
						$table_template->addSelectField("mail[template_size]", array(
							"1" => "1 (8pt)",
							"2" => "2 (10pt)",
							"3" => "3 (12pt)",
							"4" => "4 (14pt)",
							"5" => "5 (18pt)",
							"6" => "6 (24pt)",
							"7" => "7 (36pt)"
						), $mdata["template_size"]);
					$table_template->endTableData();
				$table_template->endTableRow();
				$table_template->addTableRow();
					$table_template->addTableData();
						$table_template->addCode( gettext("gebruik aanhef") );
					$table_template->endTableData();
					$table_template->addTableData();
						$table_template->addSelectField("mail[template_cmnt]", array(
							"0" => "geen persoonlijke aanhef",
							"1" => "gebruik persoonlijke aanhef"
						), $mdata["template_cmnt"]);
					$table_template->endTableData();
				$table_template->endTableRow();
				$table_template->endTable();

				$table->addCode ( $table_template->generate_output() );
				$table->addTag("br");
				$table->endTag("div");

			$table->endTableData();
		$table->endTableRow();
	}

	if ($newsletter["count"]==0) {
		/* prioritity */
		$table->addTableRow("", 1);
			$table->addTableData("", "header");
				$table->addCode( gettext("prioriteit") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addSelectField("mail[priority]", $mailData->getPriorityList(), $mdata["priority"]);
				$table->addSpace(3);
				$table->addCheckbox("mail[readconfirm]", "1", $mdata["readconfirm"]);
				$table->addSpace();
				$table->addCode( gettext("leesbevestiging") );
			$table->endTableData();
		$table->endTableRow();
	}

	/* relation */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("koppelen aan relatie") );
		$table->endTableData();
		$table->addTableData("", "data");
			/* relation selection */
			$table->addHiddenField("mail[relation]", $mdata["address_id"]);
			$address = new Address_data();
			$address_info = $address->getAddressNameByID($mdata["address_id"]);
			if ($mdata["address_id"]) {
				$table->addTag("a", array(
					"href"=>"javascript: popup('?mod=address&action=relcard&id=".$mdata["address_id"]."', 'relation_card');"
				));
				$table->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
				$table->endTag("a");
				$table->addSpace();
			} else {
				$table->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
			}
			$table->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");


		$table->endTableData();
	$table->endTableRow();
	/* project */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("koppelen aan project") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addHiddenField("mail[project_id]", $mdata["project_id"]);
			$table->addTag("span", array("id"=>"project_name"));
			if ($mdata["project_id"]) {
				$project = new Project_data();
				$project_info = $project->getProjectById($mdata["project_id"]);
				$table->addCode( $project_info[0]["name"] );
			} else {
				$table->addCode(gettext("geen"));
			}
			$table->endTag("span");
			$table->insertAction("edit", "wijzigen", "javascript: pickProject();");
		$table->endTableData();
	$table->endTableRow();

	/* publiek/niet publiek */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("publiek").":");
		$table->endTableData();
		$table->addTableData("", "data" );
			$table->addTag("span", array(
				"id" => "private_state"
			));
			if ($mdata["is_public"]==0) {
				$table->addCode( gettext("deze email is publiek toegankelijk") );
				$table->insertAction("state_public", gettext("deze email is publiek toegankelijk"), "");
			} else {
				$table->addCode( gettext("deze email is alleen prive toegankelijk") );
				$table->insertAction("state_private", gettext("deze email is alleen prive toegankelijk"), "");
			}
			$table->endTag("span");
			$table->insertAction("toggle", gettext("wijzig publiek/prive status"), "javascript: toggle_private_state();");
		$table->endTableData();
	$table->endTableRow();


	/* subject */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->insertTag("a", "", array("name"=>"editor_marker"));
			$table->addCode( gettext("onderwerp") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("mail[subject]", $mdata["subject"], array("style"=>"width: 300px"));
		$table->endTableData();
	$table->endTableRow();

	/* action */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addSpace();
		$table->endTableData();
		$table->addTableData("", "data");
			$table->insertAction("back", gettext("terug"), "javascript: history_goback();");
			$table->addSpace();
			$table->insertAction("save", gettext("opslaan"), "javascript: mail_save_db();");
			if ($mdata["is_text"]) {
				$table->insertAction("ftype_html", gettext("converteer naar html"), "javascript: mail_convert();", "", 1);
			} else {
				$table->insertAction("ftype_text", gettext("converteer naar text"), "javascript: mail_convert();");
			}
			$table->addSpace(3);
			$table->insertAction("mail_send", gettext("verzenden"), "javascript: mail_send();");
			$table->addSpace(5);
			$table->addCode( gettext("automatisch opslaan").": " );
			$table->insertTag("span", "", array("id"=>"autosave_progressbar"));


		$table->endTableData();
	$table->endTableRow();
	/* content / body */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("inhoud") );
		$table->endTableData();
		$table->addTableData("", "data");

		if (!$mdata["is_text"]) {
			/* is mode = html */
			$table->addTextArea("contents", $mdata["body_html"], array(
				"style" => "width: 700px; height: 400px;"
			));
			$editor = new Layout_editor();
			$table->addCode( $editor->generate_editor("", $mdata["body_html"]) );
		} else {
			/* if mode = text */
			$mdata["body"] = preg_replace("/<br[^>]*?> {1,5}/si", "\n", $mdata["body"]);
			$mdata["body"] = preg_replace("/<br[^>]*?>/si", "\n", $mdata["body"]);


			$table->addTextArea("contents", $mdata["body"], array(
				"style" => "width: 700px; height: 400px; font-family: courier new, monospace;",
				"wysiwyg" => "true"
			));
		}

		$table->endTableData();
	$table->endTableRow();
	/* current attachments */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("gekozen bijlage(n)") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->insertTag("span", "", array("id"=>"mail_attachments") );
		$table->endTableData();
	$table->endTableRow();
	/* add attachments from covide system */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("bijlage(n) toevoegen") );
			$table->addTag("br");
			$table->addSpace();
			$table->addCode( gettext("uit bestandsbeheer") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->insertAction("attachment", gettext("voeg bijlage toe uit Covide bestandsbeheer"), "javascript: add_from_filesys()");

		$table->endTableData();
	$table->endTableRow();

	/* action again */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addSpace();
		$table->endTableData();
		$table->addTableData("", "data");
			$table->insertAction("back", gettext("terug"), "javascript: history_goback();");
			$table->addSpace();
			$table->insertAction("save", gettext("opslaan"), "javascript: mail_save_db();");
			if ($mdata["is_text"]) {
				$table->insertAction("ftype_html", gettext("converteer naar html"), "javascript: mail_convert();", "", 1);
			} else {
				$table->insertAction("ftype_text", gettext("converteer naar text"), "javascript: mail_convert();");
			}
			$table->addSpace(3);
			$table->insertAction("mail_send", gettext("verzenden"), "javascript: mail_send();");
		$table->endTableData();
	$table->endTableRow();

	/* upload new attachments */
	$table->addTableRow("", 1);
		$table->addTableData("", "header");
			$table->addCode( gettext("bijlage(n) uploaden") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->endTag("form");
			$table->addTag("form", array(
				"id" => "uploadform",
				"method" => "POST",
				"target" => "uploadhandler",
				"enctype" => "multipart/form-data"
			));
			$max_filesize = ini_get('upload_max_filesize');
			$max_fs = $max_filesize;
			if (!is_numeric($max_filesize)) {
				$multipl = strtolower(substr($max_filesize, -1));
				$ammount = substr($max_filesize, 0, strlen($max_filesize)-1);
				switch ($multipl) {
					case "m" :
						$max_filesize = 1024*1024*$ammount;
						break;
					case "k" :
						$max_filesize = 1024*$ammount;
						break;
					case "g" :
						$max_filesize = 1024*1024*1024*$ammount;
						break;
					default :
						$max_filesize = 50*1024*1024;
						break;
				}
			}
			$table->addHiddenField("mod", $_REQUEST["mod"]);
			$table->addHiddenField("id", $id);
			$table->addHiddenField("action", "upload_files");
			$table->addHiddenField("MAX_FILE_SIZE", $max_filesize);
			$table->addTag("div", array("id"=>"uploadcode") );
			$table->addUploadField("binFile[]", array("size"=>"45") );
			$table->addCode("(max: $max_fs)");
			$table->addTag("br");
			$table->endTag("div");
			$table->addTag("div", array("id"=>"moreuploadcode") );
			$table->endTag("div");

			$table->addTag("span", array("id"=>"upload_controls") );
			$table->insertAction("file_add", gettext("voeg nog een attachment toe"), "javascript: add_upload_field();");
			$table->insertAction("file_upload", gettext("bestand(en) uploaden"), "javascript: mail_upload_files();");
			$table->endTag("span");
			$table->addTag("span", array("id"=>"upload_msg", "style"=>"visibility: hidden") );
				$table->insertTag("b", gettext("bezig met uploaden")." ...");
			$table->endTag("span");
			$table->endTag("form");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster = new Layout_venster(Array(
		"title"    => gettext("Email"),
		"subtitle" => gettext("opstellen")
	));
	$venster->addVensterData();
		$venster->addCode( $table->generate_output() );
	$venster->endVensterData();

	$output->addCode( $venster->generate_output() );

	$output->addTag("iframe", array(
		"id" => "dbhandler",
		"name" => "dbhandler",
		"src" => "blank.htm",
		"width" => "0px",
		"frameborder" => 0,
		"border" => 0,
		"height" => "0px;",
		"visiblity" => "hidden"
	));
	$output->endTag("iframe");
	$output->addTag("iframe", array(
		"id" => "uploadhandler",
		"name" => "uploadhandler",
		"src" => "blank.htm",
		"width" => "0px",
		"frameborder" => 0,
		"border" => 0,
		"height" => "0px;",
		"visiblity" => "hidden"
	));
	$output->endTag("iframe");

	$output->start_javascript();
	$output->addCode( sprintf("
		var complete_msg_extend    = '%s';
		var complete_msg_noresults = '%s';
		var complete_msg_close     = '%s';
		",
		addslashes(gettext("uitgebreid zoeken / meer resultaten")),
		addslashes(gettext("geen relevante resultaten gevonden")),
		addslashes(gettext("sluiten"))
	));
	if ($_REQUEST["fatal"]) {
		$output->addCode("alert('".addslashes(gettext("Er is een fout opgetreden tijdens het versturen. Controleer de email adressen en probeer het opnieuw."))."');");
	}
	$output->end_javascript();

	$autocomplete = new Layout_autocomplete();
	$output->addCode($autocomplete->generate_output());

	$output->addHiddenField("adresboek_search", "");

	$history = new Layout_history();
	$output->addCode( $history->generate_history_call() );

	$output->load_javascript(self::include_dir_main."xmlhttp.js");
	$output->load_javascript(self::include_dir."autocomplete.js");
	$output->load_javascript(self::include_dir."emailCompose.js");
	$output->load_javascript("classes/html/inc/tabs.js");
	$output->layout_page_end();
	$output->exit_buffer();
?>
