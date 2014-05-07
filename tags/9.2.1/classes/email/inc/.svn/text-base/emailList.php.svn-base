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

	/* start output handler */
	$output = new Layout_output();
	$output->layout_page(gettext("email"));

	/* first, check for email migration status (Covide 5.x to Covide 6.x (OOP)) */
	$mailMigration = new Email_Migration();
	$mailMigration_count = $mailMigration->needMigration();
	if ($mailMigration_count) {

		$mailMigration->prepareMigration();

		$venster = new Layout_venster();
		$venster->addVensterData();
			$venster->addTag("form", array("name"=>"migration"));

			$venster->addHiddenField("start", "");
			$venster->addHiddenField("current", 0);
			$venster->addHiddenField("total", $mailMigration_count);

			$venster->endTag("form");
			$venster->addTag("center");

				if ($mailMigration->readLock()) {
					/* if lockfile */
					$venster->insertAction("important", gettext("migration in progress"), "");
					$venster->addCode( gettext("The migration of email is currently running, this can take a long time") );
					$venster->insertAction("important", gettext("migration in progress"), "");
					$venster->addTag("br");
					$venster->addCode( gettext("The migration is running in the background...") );
				} else {
					/* if no lockfile */
					/* write lockfile */
					$mailMigration->writeLock();

					$load_migration = 1;
					$venster->insertAction("important", gettext("migration in progress"), "");
					$venster->addCode( gettext("The migration of email is currently running, this can take a long time") );
					$venster->insertAction("important", gettext("migration in progress"), "");
					$venster->addTag("br");
					$venster->addCode( gettext("Dont close this window while the migration is still running.") );
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->addTag("span", array("id"=>"migration_progressbar"));
						$venster->addCode("0% (".gettext("initializing").")" );
					$venster->endTag("span");
				}
			$venster->endTag("center");
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );

		if ($load_migration) {
			$output->load_javascript(self::include_dir_main."xmlhttp.js");
			$output->load_javascript(self::include_dir."migration.js");
		}

		$output->layout_page_end();

		/* end normal script execution */
		$output->exit_buffer();
	}

	if (!$folder_id) {
		$folder_id = $_REQUEST["folder_id"];
	}
	$action_value     = $_REQUEST["action_value"];
	$list_of_address  = $_REQUEST["list_of_address"];
	$project_id       = $_REQUEST["project_id"];
	$bcard_id         = $_REQUEST["bcard_id"];
	$list_from        = $_REQUEST["list_from"];
	$search           = $_REQUEST["search"];
	$action_value     = $_REQUEST["action_value"];

	if ($_REQUEST["address_id"]) {
		$list_of_address = $_REQUEST["address_id"];
	}

	/* define basic email data object */
	$mailData = new Email_data();
	$postvakin = $mailData->getSpecialFolder("Postvak-IN", $_SESSION["user_id"]);
	$trashfolder = $mailData->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
	$trashfolder = $trashfolder["id"];
	$folders = $mailData->getFolders( array("count"=>1, "relation"=>$list_of_address ), $archive );

	/* prefetch users mail folders and archive */
	if ($list_of_address || $project_id) {
		$mailData->_init_mail_mappen(1);
	} else {
		$mailData->_init_mail_mappen(0);
	}
	$this->_folders &= $mailData->_folders;

	/* TODO:check for double email */
	//$mailData->checkMailRepair();

	/* get user's inbox id */
	foreach ($mailData->_folders as $v) {
		if ($v["name"]=="Postvak-IN" && $v["user_id"]==$_SESSION["user_id"]) {
			$postvakin_id = $v["id"];
		}
	}

	/* default to users inbox */
	if(!$folder_id) {
		$folder_id=$postvakin_id;
	}

	/* get the archive folder id */
	$this->archive_id = $mailData->_get_archive_id();

	/* archive is not possible when no debnr and no project */
	if ($folder_id==$this->archive_id && (!$list_of_address && !$project_id)) {
		$output->redir_location("./?mod=email");
	}

	/* current folder */
	if (!$mailData->_folders[$folder_id]) {
		$_f = $mailData->getFolder($folder_id);
		$folderName = $_f["name"];
	} else {
		if (array_key_exists($folder_id, $folders)) {
			$folderName = $folders[$folder_id]["name"];
		} else {
			$folderName = $mailData->_folders[$folder_id]["name"];
		}
	}

	if ($folder_id == $postvakin_id) {
		$folderName = gettext("Inbox");
	}

	/* if folder is not in $mailData->_folders it's a shared folder. Make this clear in the name */
	if (!array_key_exists($folder_id, $mailData->_folders)) {
		$user_data = new User_data();
		$folder_username = $user_data->getUsernameById($_f["user_id"]);
		unset($user_data);
		$folderName = sprintf("%s (%s %s)", gettext($folderName), gettext("shared by"), $folder_username);
		$folder_user_id = $_f["user_id"];
	}

	if ($list_of_address) {
		$addressData = new Address_data();
		$addressinfo = sprintf(" %s %s", gettext("from"), $addressData->getAddressNameById($list_of_address));
		if ($project_id) {
			$project_data = new Project_data();
			$addressinfo .= sprintf(" %s %s", gettext("of project"), $project_data->getProjectNameById($project_id));
		}
	} elseif ($project_id) {
		$project_data = new Project_data();
		$addressinfo = sprintf(" %s %s", gettext("of project"), $project_data->getProjectNameById($project_id));
	} else {
		$addressinfo = "";
	}

	$subtitle = " ".gettext("in")." ".$folderName;
	if ($search) {
		$subtitle .= " - ".gettext("search for").": ".$search;
	}
	$settings = array(
		"title"    => gettext("Messages").$addressinfo,
		"subtitle" => $subtitle
	);

	if (!$_REQUEST["short_view"]) {
		$user_data = new User_data();
		$user_details = $user_data->getUserDetailsById($_SESSION["user_id"]);
		$_short_view = (int)$user_details["mail_shortview"];
	} else {
		$_short_view = (int)$_REQUEST["short_view"];
	}

	$formdata = array(
		"action"                     => $action,
		"action_value"               => $action_value,
		"folder_id"                  => $folder_id,
		"mail_id"                    => $mail_id,
		"attachment_id"              => $attachment_id,
		"list_of_address"            => $list_of_address,
		"list_of_address_alt"        => $list_of_address_alt,
		"list_from"                  => $list_from,
		"project_id"                 => $project_id,
		"bcard_id"                   => $bcard_id,
		"mod"                        => "email",
		"sort"                       => $_REQUEST["sort"],
		"short_view"                 => $_short_view
	);

	$venster = new Layout_venster($settings);
	$venster->addMenuItem(gettext("new email"), "javascript: emailSelectFrom();", "", 0);
	if ($list_of_address) {
		$venster->addMenuItem(gettext("contact profile"), "?mod=address&action=relcard&id=".$list_of_address, "", 0);
	}
	//if the current view is a project
	if ($project_id) {
		$projectData = new Project_data();
		$projectinfo = $projectData->getProjectById($project_id);
		$venster->addMenuItem(gettext("Go to the project card"), "?mod=project&action=showinfo&id=".$project_id, "", 0);
		if ($projectinfo[0]["address_id"]) {
			$addressData = new Address_data();
			$addressinfo = $addressData->getAddressNameById($projectinfo[0]["address_id"]);
			$venster->addMenuItem(gettext("Go to the contact card of: ").$addressinfo, "?mod=address&action=relcard&id=".$projectinfo[0]["address_id"]);
			$venster->addMenuItem(gettext("Show emails of contact: ").$addressinfo, "?mod=email&folder_id=".$folder_id."&address_id=".$projectinfo[0]["address_id"]);
		}
	}
	if ($_SESSION["locale"] == "nl_NL") {
		$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/E-Mail", array("target" => "_blank"), 0);
	}
	$venster->addMenuItem("<b>".gettext("selection actions")."</b>", "");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("move selected email"), "javascript: selection_email_move();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("toggle read/new status of selected emails"), "javascript: selection_email_togglestate();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("delete selected emails"), "javascript: selection_email_delete();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("try to link selected emails"), "javascript: selection_email_linkrel();");
	$venster->addMenuItem("<b>".gettext("attachment actions")."</b>", "");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("save selected attachments in filesystem module"), "javascript: selection_attachments_save();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("download selected attachments"), "javascript: selection_attachments_download();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("delete selected attachments"), "javascript: selection_attachments_delete();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("download selected attachments as zip-file"), "javascript: selection_attachments_zip();");
	$venster->addMenuItem("<b>".gettext("global actions")."</b>", "");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("new email"), "javascript: emailSelectFrom();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("predefined content"), "javascript: set('action', 'signatures'); submitform();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("alter mailfilters"), "javascript: popup('index.php?mod=email&action=filters');");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("email templates"), "javascript: set('action', 'templates'); submitform();");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("share folders"), "javascript: popup('index.php?mod=email&action=show_permissions&user_id=".$_SESSION["user_id"]."', 'mailpermissions', 500, 350, 1);");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("force mail retrieval"), "javascript: popup('index.php?mod=email&action=retrieve&user_id=".$_SESSION["user_id"]."', 'mailfetch', 400, 350, 1);");
	$venster->addMenuItem("&nbsp;&nbsp;".gettext("newsletter"), "javascript: popup('?mod=newsletter', '', 980, 700, 1);");
	if ($GLOBALS["covide"]->license["has_postfixadmin"] && $GLOBALS["covide"]->license["postfixdsn"]) {
		$venster->addMenuItem("&nbsp;&nbsp;".gettext("autoreply"), "javascript: popup('index.php?mod=email&action=edit_pa_autoreply&user_id=".$_SESSION["user_id"]."');");
	} else {
		$venster->addMenuItem("&nbsp;&nbsp;".gettext("autoreply"), "javascript: popup('index.php?mod=email&action=edit_autoreply&user_id=".$_SESSION["user_id"]."');");
	}

	$venster->generateMenuItems();
	$venster->addVensterData();

	$table = new Layout_table( array("width"=>"100%") );
	$table->addTableRow();
		$table->addTableData();
			if ($list_of_address) {
				/* get projects for this address */
				if ($GLOBALS["covide"]->license["has_project"]) {
					/* get the project data */
					$project_data = new Project_data();
					$projectoptions = array("address_id" => $list_of_address);
					$projectinfo = $project_data->getProjectsBySearch($projectoptions);
					unset($projectoptions);
					foreach($projectinfo as $k=>$v) {
						if (!$v["allow_edit"]) {
							unset($projectinfo[$k]);
						}
					}

					$sel = array(
						"0" => "- ".gettext("projects")." -"
					);
					foreach ($projectinfo as $k=>$v) {
						$sel[$v["id"]] = $v["name"];
					}
					$table->addSpace(2);
					$table->addSelectField("project_id", $sel, $project_id);
				}
				/* get bcards for this address */
				$bcards = $addressData->getBcardsByRelationID($list_of_address);

				$sel = array(
					"0" => "- ".gettext("businesscards")." -"
				);
				foreach ($bcards as $v) {
					$sel[$v["id"]] = $v["fullname"];
				}
				$table->addSpace(2);
				$table->addSelectField("bcard_id", $sel, $bcard_id);
			}
		$table->endTableData();
		$table->addTableData();
			$table->addCode( $output->nbspace(3) );
			$table->addCode( gettext("search").": ");
			$table->addTextField("search", $search, "", "", 1);
			$table->insertAction("forward", gettext("search"), "javascript:set('action',''); set('list_from', ''); submitform();");
			if ($search) {
				$table->insertAction("toggle", "alles tonen", "javascript:set('action',''); set('search',''); submitform();");
			}
			$table->insertAction("view_all", gettext("short / long display"), "javascript: toggle_shortview();");
		$table->endTableData();
	$table->endTableRow();
	/* if archive */
	if ($mailData->_archive_period > 0 && $this->archive_id == $folder_id) {
		$table->addTableRow();
			$table->addTableData();
				$table->addSpace();
			$table->endTableData();
			$table->addTableData(array("colspan" => 2));
				$table->addSpace(10);
				$table->addCode(sprintf("%s %d %s:",
					gettext("also search inside archived items older than"),
					$mailData->_archive_period,
					gettext("months")
				));
				$table->addSpace();
				$table->addCheckBox("search_archive", 1, $_REQUEST["search_archive"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();

	$venster->addCode ($table->generate_output());
	$venster->addTag("br");

	$options = array(
		"search"         => $search,
		"search_archive" => $_REQUEST["search_archive"],
		"project"        => $project_id,
		"relation"       => $list_of_address,
		"bcard"          => $bcard_id,
		"folder"         => $folder_id,
		"user"           => $_SESSION["user_id"]
	);

	/* get the data we need */
	$part  =  $mailData->getEmailBySearch($options, $list_from, $_REQUEST["sort"]);
	$data  =& $part["data"];
	$total =  $part["count"];

	/* create a new view and add the data */
	$view = new Layout_view();
	$view->addData($data);

	//$output->debug_output($data);

	/* add the mappings (columns) we needed */
	if ($_short_view != 1) {
		$view->addMapping("%%header_fromto", "%%data_fromto");
		$view->addMapping(gettext("date"), "%%data_datum", "right");
		$view->addMapping("%%header_attachments", "%%data_attachments", "", "", 1, 1);
		$view->addMapping("%%header_actions", "%%data_actions", "right");
		$view->addSubMapping("%%data_subject", "%is_new");
		$view->addSubMapping("%%data_description", "%h_description");
	} else {
		$view->addMapping("&nbsp; ", "%%is_new");
		$view->addMapping(gettext("subject"), "%%data_subject");
		$view->addMapping("&nbsp;", "%%complex_flags");

		/* get sent items folder */
		$sent_items = $mailData->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
		if ($folder_user_id) {
			/* also check if this folder is a shared sent items folder */
			$sent_items_user = $mailData->getSpecialFolder("Verzonden-Items", $folder_user_id);
		}
		/* swap sender and rcpt if the folder is sent items */
		if ($sent_items["id"] == $folder_id || (is_array($sent_items_user) && $sent_items_user["id"] == $folder_id))
			$view->addMapping(gettext("recipient"), "%to");
		else
			$view->addMapping(gettext("sender"), "%sender_emailaddress");

		$view->addMapping(gettext("date"), "%short_date");
		$view->addMapping("%%header_actions", "%%data_actions_short", "right");
		$view->defineComplexMapping("is_new", array(
			array(
				"type"  => "action",
				"src"   => "mail_new",
				"check" => "%is_new",
				"alt"   => gettext("this email is unread")
			)
		));
	}

	$escape = sql_syntax("escape_char");
	/* define sort columns */
	$view->defineSortForm("sort", "velden");
	$view->defineSort("subject", "subject");
	$view->defineSort(gettext("date"), "date");

	/* if this folder is concepts, then directly edit the email */
	$concept_folder = $mailData->getSpecialFolder("Concepten", $_SESSION["user_id"]);
	if ($concept_folder["id"] == $folder_id) {
		$_action = "compose";
		$popup1 = "javascript: popup('";
		$popup2 = "', '', 980, 500, 1);";
	} else {
		$_action = "open";
	}

	/* define complex flags fromto */
	$view->defineComplexMapping("complex_flags", array(
		array(
			"type"  => "action",
			"src"   => "mail_headers",
			"check" => "%h_description",
			"alt"   => gettext("this email has a remark"),
			"link" => array($popup1, "?mod=email&action=$_action&sort=".$_REQUEST["sort"]."&id=", "%id", $popup2)
		),
		array(
			"type"  => "action",
			"src"   => "attachment",
			"check" => "%has_attachments",
			"alt"   => gettext("this email has attachements"),
			"link"  => array($popup1, "?mod=email&action=$_action&sort=".$_REQUEST["sort"]."&id=", "%id", $popup2)
		),
	));

	/* define complex header fromto */
	$view->defineComplexMapping("header_fromto", array(
		array(
			"text"  => gettext("subject"),
			"alias" => "subject"
		),
		array(
			"text"  =>	"\n"
		),
		array(
			"text"  => gettext("sender"),
			"alias" => "sender"
		),
		array(
			"text"  =>	"/"
		),
		array(
			"text"  => gettext("recipient"),
			"alias" => "recipient"
		)
	));
	/* define complex data fromto */
	$view->defineComplexMapping("data_fromto", array(
		array(
			"text" => array(
				gettext("from"), ": ",	"%sender_emailaddress", "\n",
				gettext("to"), ": ",	"%to"
			)
		)
	));
	/* define complex mapping for subject */
	$view->defineComplexMapping("data_subject", array(
		array(
			"type" => "text",
			"text" => array("<div id=\"preview_", "%id", "\">")
		),
		array(
			"type" => "link",
			"text" => "%subject",
			"link"  => array($popup1, "?mod=email&action=$_action&sort=".$_REQUEST["sort"]."&id=", "%id", $popup2)
		),
		array(
			"type" => "text",
			"text" => "</div>"
		),
		array(
			"type" => "text",
			"text" => array("<div id=\"contentpreview_", "%id", "\" class=\"previewcontent\" style=\"position: absolute;\"><p>", "%h_body", "</p></div>"),
		),
	));
	$view->defineComplexMapping("data_description", array(
		array(
			"type" => "text",
			"text" => "%h_description"
		)
	));

	/* define complex mapping for datum */
	$view->defineComplexMapping("data_datum", array(
		array(
			"text" => array( "%short_date", "\n", "%short_time" )
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
			"text" => $output->insertCheckbox(array("checkbox_attachment[","%id","]"), "1", 0, 1),
			"check" => "%no_cid"
		),
		array(
			"type" => "action",
			"src" => "%fileicon",
			"check" => "%no_cid"
		),
		array(
			"text"  => array(" ","%name", " (", "%h_size", ")"),
			"check" => "%no_cid"
		),
	));
	/* define complex mapping for header attachments */
	$view->defineComplexMapping("header_attachments", array(
		array(
			"text" => gettext("attachments")." "
		),
		array(
			"text" => $output->insertCheckbox(array("checkbox_attachment_toggle_all"), "1", 0, 1)
		),
	));
	/* define complex mapping for header actions */
	$view->defineComplexMapping("header_actions", array(
		array(
			"text" => "<p align=\"right\">"
		),
		array(
			"text" => $output->insertCheckbox(array("checkbox_mail_toggle_all"), "1", 0, 1)
		),
		array(
			"text" => "</p>"
		)
	));
	/* define complex mapping for data actions */
	$view->defineComplexMapping("data_actions", array(
		array(
			"text"  => array("<span id='addresslink_", "%id", "'>")
		),
		array(
			"type"  => "action",
			"src"   => "addressbook",
			"alt"   => gettext("this email is linked to a contact"),
			"link"  => array("javascript: popup('?mod=address&hide=1&action=relcard&id=", "%address_id", "');"),
			"check" => "%address_id"
		),
		array(
			"text"  => "</span>"
		),
		array(
			"type"  => "action",
			"src"   => "%is_public_i",
			"alt"   => "%is_public_h",
			"link"	=> array("javascript: toggle_private_state('", "%id", "');")
		),
		array(
			"text"  => array("<span id='relation_", "%id", "'>")
		),
		array(
			"type"  => "action",
			"src"   => "important",
			"alt"   => gettext("pick a contact"),
			"link"  => array("javascript: showInfo('", "%id", "')"),
			"check" => "%check_askwichrel"
		),
		array(
			"text"  => "</span>"
		),
		array(
			"type"  => "action",
			"src"   => "important",
			"alt"   => array(
				gettext("this newsletter is interupted at"),
				"%tracking_status",
				" ".gettext("addresses").", ",
				gettext("see icon on the right")."."
			),
			"check" => "%tracking_resume"
		),
		array(
			"type"  => "action",
			"src"   => "folder_denied",
			"alt"   => gettext("this newsletter has been aborted, click here to resume"),
			"link"  => array("?mod=email&action=mail_send&dl=1&id=", "%id"),
			"check" => "%tracking_resume"
		),
		array(
			"type"  => "action",
			"src"   => "mail_tracking",
			"alt"   => gettext("show statistics"),
			"link"  => array("javascript: showTracking('", "%id", "');"),
			"check" => "%tracking"
		),
		array(
			"type"  => "action",
			"alt"   => gettext("show info"),
			"src"   => "info",
			"link"  => array("javascript: showInfo('", "%id", "');")
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("delete"),
			"link"  => array("javascript: deleteMail('", "%id", "');")
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_mail[","%id","]"), "1", 0, 1)
		)
	));

	/* define complex mapping for data actions */
	$view->defineComplexMapping("data_actions_short", array(
		array(
			"type"  => "action",
			"src"   => "important",
			"alt"   => array(
				gettext("this newsletter is interupted at"),
				"%tracking_status",
				" ".gettext("addresses").", ",
				gettext("see icon on the right")."."
			),
			"check" => "%tracking_resume"
		),
		array(
			"type"  => "action",
			"src"   => "folder_denied",
			"alt"   => gettext("this newsletter has been aborted, click here to resume"),
			"link"  => array("?mod=email&action=mail_send&id=", "%id"),
			"check" => "%tracking_resume"
		),
		array(
			"type"  => "action",
			"src"   => "mail_tracking",
			"alt"   => gettext("show statistics"),
			"link"  => array("javascript: showTracking('", "%id", "');"),
			"check" => "%tracking"
		),
		array(
			"type"  => "action",
			"alt"   => gettext("show info"),
			"src"   => "info",
			"link"  => array("javascript: showInfo('", "%id", "');")
		),
		array(
			"text"  => $output->insertCheckbox(array("checkbox_mail[","%id","]"), "1", 0, 1)
		)
	));

	$venster->addCode( $view->generate_output() );
	unset($view);

	/* load javascript handlers for this page */
	$venster->load_javascript(self::include_dir."emailList.js");

	$paging = new Layout_paging();
	$paging->setOptions($list_from, $total, "javascript: blader('%%');");
	$venster->addCode( $paging->generate_output() );

	$venster->endVensterData();
	$table_folders = new Layout_table(array("width" => "200"));
		$table_folders->addTableRow();
			$table_folders->addTableData();
			if ($mailData->_folders[$folder_id] && $mailData->_folders[$folder_id]["user_id"] == $_SESSION["user_id"]) {
				if (!$GLOBALS["covide"]->license["disable_basics"])
					$table_folders->insertAction("new", gettext("create new folder in current folder"), "javascript: createNewFolder();");

				if (!$mailData->checkSpecialFolder($folder_id)) {
					$table_folders->insertAction("edit", gettext("alter folder"), "javascript: editCurrentFolder();");
					$table_folders->insertAction("delete", gettext("delete folder"), "javascript: deleteCurrentFolder();");
					$table_folders->insertAction("cut", gettext("move folder"), "javascript: moveCurrentFolder();");
				}
				if ($folder_id == $trashfolder) {
					$table_folders->insertAction("delete", gettext("delete all"), "javascript: delete_all_trash();");
				}
			}
			$table_folders->endTableData();
		$table_folders->endTableRow();
		$table_folders->addTableRow();
			$table_folders->addTableData( array("valign"=>"top") );
				if ($list_of_address > 0) {
					$archive = 1;
				} else {
					$archive = 0;
				}
				$table_folders->addCode( $this->getFolderList($folders, $folder_id) );
			$table_folders->endTableData();
		$table_folders->endTableRow();
		$table_folders->addTableRow();
			$table_folders->addTableData( array("valign"=>"top") );
				$table_folders->addSpace(3);
			$table_folders->endTableData();
		$table_folders->endTableRow();
	$table_folders->endTable();

	/* shared folders */
	$shared_folders = $this->getSharedFolderList($folder_id);
	$table_folders_shared = new Layout_table(array("width" => "200"));
		$table_folders_shared->addTableRow();
			$table_folders_shared->addTableData( array("valign"=>"top") );
				//$folders = $mailData->getFolders();
				$table_folders_shared->addCode( $shared_folders );
			$table_folders_shared->endTableData();
		$table_folders_shared->endTableRow();
	$table_folders_shared->endTable();
/*

	$venster_mappen = new Layout_venster(array("title" => gettext("Mail folders")));
	$venster_mappen->addVensterData();
	$venster_mappen->addCode( $table_folders->generate_output() );
	$venster_mappen->endVensterData();

	if ($shared_folders) {
		$venster_mappen_shared = new Layout_venster(array("title" => gettext("Shared folders")));
		$venster_mappen_shared->addVensterData();
		$venster_mappen_shared->addCode( $table_folders_shared->generate_output() );
		$venster_mappen_shared->endVensterData();
	} else {
		$venster_mappen_shared = new layout_output();
	}
 */
	$table_container = new Layout_table( array("class"=>"fullwidth") );
		$table_container->addTableRow();
			$table_container->insertTableData( $output->nbspace() );
			$table_container->addTableData( array("valign"=>"top") );
				if (!$GLOBALS["covide"]->mobile) {
					$table_container->addTag("br");
					$table_container->insertTag("h1", gettext("Mail folders"));
					$table_container->addCode( $table_folders->generate_output() );
					$table_container->insertTag("h1", gettext("Shared folders"));
					$table_container->addCode( $table_folders_shared->generate_output() );
				} else {
					$table_container->addCode( $venster->generate_output() );
				}
			$table_container->endTableData();
			$table_container->addTableData();
				$table_container->addSpace();
			$table_container->endTableData();
			$table_container->addTableData( array("valign"=>"top", "class"=>"fullwidth") );
				if (!$GLOBALS["covide"]->mobile) {
					$table_container->addCode( $venster->generate_output() );
				} else {
					$table_container->addCode( $venster_mappen->generate_output() );
					$table_container->addCode( $venster_mappen_shared->generate_output() );
				}
			$table_container->endTableData();
		$table_container->endTableRow();
	$table_container->endTable();

	$output->addCode( $this->emailSelectFromPrepare() );

	$output->addTag("form", array(
		"id"=>"velden",
		"method"=>"POST",
		"action"=>"index.php",
	));

	//add some hidden fields
	foreach ($formdata as $k=>$v) {
		$output->addHiddenField($k, $v);
	}
	$output->addHiddenField("msg_confirm_delete", gettext("Are you sure you want to delete the selected items?"));

	$output->load_javascript(self::include_dir_main."xmlhttp.js");
	$output->addCode( $table_container->generate_output() );

	$output->endTag("form");

	/* add a container for multiple downloads */
	$output->insertTag("div", "", array(
		"id"    => "download_container",
		"style" => "display:none; width: 0px; height: 0px;"
	));

	/* handle status codes passed by parameter */
	switch ($status) {
		case "send":
			//$output->start_javascript();
			//$output->addCode("alert('Uw email is verzonden.')");
			//$output->end_javascript();
			break;
		case "error":
			$output->start_javascript();
			$output->addCode("alert('Uw email is verzonden, maar er zijn fouten opgetreden: een of meerdere email adressen zijn niet correct.')");
			$output->end_javascript();
		break;
	}

	$history = new Layout_history();
	$output->addCode( $history->generate_save_state("action") );

	if ($msg) {
		$output->start_javascript();
		$output->addCode("
			alert('$msg');
		");
		$output->end_javascript();
	}

	$output->layout_page_end();

	$output->exit_buffer();

?>
