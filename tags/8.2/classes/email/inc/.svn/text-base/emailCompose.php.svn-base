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

	set_time_limit(60*5);

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
	$output->layout_page("mail", 1);
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
	$output->addHiddenField("campaign_id", $_REQUEST["campaign_id"]);

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
			$table->addCode( gettext("from") );
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
					$table->addCode( gettext("Sending of newsletter aborted.")." " );
					$table->addCode( gettext("The newsletter is already partially sent.") );
					$table->addTag("br");
					$table->addTag("br");
					$table->addCode( gettext("You can resume sending by clicking 'send'") );
					$table->addTag("br");
					$table->addCode( gettext("The system will make sure you resume where it was aborted") );
					$table->addTag("br");
					$table->addTag("br");
					$table->addCode( gettext("Resume sending") ).
					$table->insertAction("mail_send", gettext("Send"), "javascript: mail_send();");
				$table->endTableData();
			$table->endTableRow();
		}
		/* rcpt */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("total recipients") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode( $newsletter["count"]." ".gettext("ontvanger(s)") );
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("newsletter recipients") );
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

		$show_cc  = ($mdata["cc"])  ? "":"display:none;";
		$show_bcc = ($mdata["bcc"]) ? "":"display:none;";
		if (!$show_cc && !$show_bcc)
			$show_toggle = "display:none;";

		/* rcpt */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode( gettext("to") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[rcpt]", $mdata["to"], array("style"=>"width: 400px; height: 60px;"));
				$table->insertAction("view_all", gettext("show all related email addresses"), "javascript: autoemail_complete_field('mailrcpt', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("add a classification"), "javascript: popup('?mod=email&action=selectCla&field=mailrcpt', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();
		/* cc */
		$table->addTableRow(array(
			"id" => "mail_cc_layer",
			"style" => $show_cc
		));
			$table->addTableData("", "header");
				$table->addCode( gettext("cc") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[cc]", $mdata["cc"], array("style"=>"width: 400px; height: 60px;"));
				$table->insertAction("view_all", gettext("show all related email addresses"), "javascript: autoemail_complete_field('mailcc', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("add a classification"), "javascript: popup('?mod=email&action=selectCla&field=mailcc', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();
		/* bcc */
		$table->addTableRow(array(
			"id" => "mail_bcc_layer",
			"style" => $show_bcc
		));
			$table->addTableData("", "header");
				$table->addCode( gettext("bcc") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addTextArea("mail[bcc]", $mdata["bcc"], array("style"=>"width: 400px; height: 60px;"));
				$table->insertAction("view_all", gettext("show all related email addresses"), "javascript: autoemail_complete_field('mailbcc', '".(int)$mdata["related_id"]."', document.getElementById('mailrelation').value);");
				$table->insertAction("state_special", gettext("add a classification"), "javascript: popup('?mod=email&action=selectCla&field=mailbcc', 'search_address', 450, 500);");
			$table->endTableData();
		$table->endTableRow();
		/* toggle */
		$table->addTableRow(array(
			"id" => "mail_toggle_layer",
			"style" => $show_toggle
		));
			$table->addTableData("", "header");
				$table->addCode( gettext("cc")."/".gettext("bcc") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->insertAction("file_attach", gettext("add cc/bcc"), "javascript: showCcBcc();");
			$table->endTableData();
		$table->endTableRow();

	}

	/* template stuff */
	if (!$mdata["is_text"]) {
		$user_data = new User_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
		$mail_template = ($mdata["template"]) ? $mdata["template"] : $user_info["mail_default_template"];
		
		$table->addTableRow("", 1);
			$table->addTableData("", "header");
				$table->addCode( gettext("template") );
			$table->endTableData();
			$table->addTableData(array("colspan"=>2), "data");
				$table->addSelectField("mail[template]", $mailData->getTemplates(), $mail_template);
				$table->addTag("div", array("id"=>"template_view", "style"=>"display:none") );
				$table->addTag("br");
				$table->endTag("div");
				$table->addTag("div", array("id"=>"template_view_standard", "style"=>"display:none") );
				$table_template = new Layout_table();
				$table_template->addTableRow();
					$table_template->addTableData();
						$table_template->addCode( gettext("type of template").": " );
					$table_template->endTableData();
					$table_template->addTableData();
						$table_template->addSelectField("mail[template_type]", array(
							"external" => gettext("naladen van internet (standaard)"),
							"tracking" => gettext("load from the internet and track this"),
							"inline"   => gettext("inline images")
						), $mdata["template_type"]
					);
					$table_template->endTableData();
				$table_template->endTableRow();
				$table_template->addTableRow();
					$table_template->addTableData();
						$table_template->addCode( gettext("use commencement") );
					$table_template->endTableData();
					$table_template->addTableData();
					if ($newsletter["count"] > 0) {
						$table_template->addSelectField("mail[template_cmnt]", array(
							"0" => gettext("geen persoonlijke aanhef"),
							"1" => gettext("gebruik persoonlijke aanhef")
						), $mdata["template_cmnt"]);
					} else {
						$table_template->addSpace(3);
						$table_template->addCode(gettext("only available in newsletters"));
					}
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
				$table->addCode( gettext("priority") );
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addSelectField("mail[priority]", $mailData->getPriorityList(), $mdata["priority"]);
				$table->addSpace(3);
				$table->addCheckbox("mail[readconfirm]", "1", $mdata["readconfirm"]);
				$table->addSpace();
				$table->addCode( gettext("read confirmation") );
			$table->endTableData();
		$table->endTableRow();
	}

	/* relation */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("link to contact") );
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
			$table->addCode(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addHiddenField("mail[project_id]", $mdata["project_id"]);
			$table->addTag("span", array("id"=>"project_name"));
			if ($mdata["project_id"]) {
				$project = new Project_data();
				$project_info = $project->getProjectById($mdata["project_id"]);
				$table->addCode( $project_info[0]["name"] );
			} else {
				$table->addCode(gettext("none"));
			}
			$table->endTag("span");
			$table->insertAction("edit", "wijzigen", "javascript: pickProject();");
		$table->endTableData();
	$table->endTableRow();

	/* publiek/niet publiek */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("public"));
		$table->endTableData();
		$table->addTableData("", "data" );
			$table->addTag("span", array(
				"id" => "private_state"
			));
			if ($mdata["is_public"]==0) {
				$table->addCode( gettext("this email is public") );
				$table->insertAction("state_public", gettext("this email is public"), "");
			} else {
				$table->addCode( gettext("this email is private") );
				$table->insertAction("state_private", gettext("this email is private"), "");
			}
			$table->endTag("span");
			$table->insertAction("toggle", gettext("alter public/private state"), "javascript: toggle_private_state();");
		$table->endTableData();
	$table->endTableRow();


	/* subject */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->insertTag("a", "", array("name"=>"editor_marker"));
			$table->addCode( gettext("subject") );
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
			$table->insertAction("close", gettext("back"), "javascript: window.close();");
			$table->addSpace();
			$table->insertAction("save", gettext("save"), "javascript: mail_save_db();");
			if ($mdata["is_text"]) {
				$table->insertAction("ftype_html", gettext("convert to html"), "javascript: mail_convert();", "", 1);
			} else {
				$table->insertAction("ftype_text", gettext("convert to text"), "javascript: mail_convert();");
			}
			$table->addSpace(3);
			$table->insertAction("mail_send", gettext("Send"), "javascript: mail_send();");
			$table->addSpace(5);
			$table->addCode( gettext("save automatically").": " );
			$table->insertTag("span", "", array("id"=>"autosave_progressbar"));


		$table->endTableData();
	$table->endTableRow();
	/* content / body */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode( gettext("content") );
		$table->endTableData();
		$table->addTableData("", "data");

		/* get letterhead if it has an address Id */
		// Disabled. Need to pass the bcard id instead of the relation id.
		// Also use generate_letterinfo instead of generating the contact_person ourselves.
		if (1==0 && $mdata["address_id"]) {
			$address_data = new Address_data;
			$bcard = $address_data->getRCBCByAddressId($mdata["address_id"]);
			$bcard["contact_givenname"]    = $bcard["givenname"];
			$bcard["contact_initials"]     = $bcard["initials"];
			$bcard["contact_infix"]        = $bcard["infix"];
			$bcard["contact_surname"]      = $bcard["surname"];
			$bcard["contact_commencement"] = $bcard["commencement"];
			$bcard["contact_letterhead"]   = $bcard["letterhead"];
			$bcard["contact_title"]        = $bcard["title"];
			$letterinfo = $address_data->generate_letterinfo($bcard);
			$cmnt = preg_replace("/ {2,}/si", " ", $letterinfo["contact_person"]);
			if (strlen(trim($cmnt))) {
				$mdata["body"] = $cmnt.",\n".$mdata["body"];
				$mdata["body_html"] = $cmnt.",<br>".$mdata["body_html"];
			}
		}
		if (!$mdata["is_text"]) {
			/* is mode = html */
			$table->addTextArea("contents", $mdata["body_html"], array(
				"style" => "width: 700px; height: 400px;"
			));
			$editor = new Layout_editor();
			$table->addCode( $editor->generate_editor("", $mdata["body_html"], "false") );
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
			$table->addCode( gettext("from covide filesystem") );
		$table->endTableData();
		$table->addTableData("", "data");
			$table->insertAction("attachment", gettext("add attachment from covide filesystem"), "javascript: add_from_filesys()");
			$table->addSpace(2);
			$table->addTag("input", array(
				"type"    => "button",
				"class"   => "defaultbutton",
				"name"    => "cvdfilesys",
				"value"   => gettext("covide filesystem"),
				"onclick" => "add_from_filesys()"
			));

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
			$table->insertAction("file_add", gettext("add another attachment"), "javascript: add_upload_field();");
			
			// quota checks
			if (!$fsdata)
				$fsdata = new Filesys_data();

			$quota = $fsdata->checkFilesysQuota();

			if ($quota === false || $quota["left"] > 0) {
				if ($quota !== false) {
					$table->addCode(gettext("quota usage"));
					$table->addSpace();
					$table->addCode($quota["info"]);
					$table->addSpace();
				}
				$table->insertAction("file_upload", gettext("bestand(en) uploaden"), "javascript: mail_upload_files();");
			} elseif ($quota !== false) {
				$table->addTag("b");
				$table->addCode(gettext("maximum quota is reached"));
				$table->addSpace();
				$table->addCode($quota["info"]);
				$table->endTag("b");
			}

			$table->endTag("span");
			$table->addTag("span", array("id"=>"upload_msg", "style"=>"visibility: hidden") );
				$table->insertTag("b", gettext("uploading")." ...");
			$table->endTag("span");
			$table->endTag("form");
		$table->endTableData();
	$table->endTableRow();

	/* action again */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addSpace();
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTag("br");
			$table->insertAction("close", gettext("back"), "javascript: window.close();");
			$table->addSpace();
			$table->insertAction("save", gettext("save"), "javascript: mail_save_db();");
			if ($mdata["is_text"]) {
				$table->insertAction("ftype_html", gettext("convert to html"), "javascript: mail_convert();", "", 1);
			} else {
				$table->insertAction("ftype_text", gettext("convert to text"), "javascript: mail_convert();");
			}
			$table->addSpace(3);
			$table->insertAction("mail_send", gettext("Send"), "javascript: mail_send();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster = new Layout_venster(Array(
		"title"    => gettext("Email"),
		"subtitle" => gettext("compose")
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
		"id"          => "uploadhandler",
		"name"        => "uploadhandler",
		"src"         => "blank.htm",
		"width"       => "0px",
		"frameborder" => 0,
		"border"      => 0,
		"height"      => "0px;",
		"visiblity"   => "hidden"
	));
	$output->endTag("iframe");

	$output->start_javascript();
	$output->addCode( sprintf("
		var complete_msg_extend    = '%s';
		var complete_msg_noresults = '%s';
		var complete_msg_close     = '%s';
		",
		addslashes(gettext("extended search / all results")),
		addslashes(gettext("no results")),
		addslashes(gettext("close"))
	));
	if ($_REQUEST["fatal"]) {
		$output->addCode("alert('".addslashes(gettext("There was an error while sending the email. Please check the email addresses and try again."))."');");
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
