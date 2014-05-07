<?php
	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	/* start output handler */
	$output = new Layout_output();
	$output->layout_page();

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
					$venster->insertAction("important", gettext("migratie wordt uitgevoerd"), "");
					$venster->addCode( gettext("De migratie van de email wordt momenteel uitgevoerd, dit kan enkele minuten tot lange tijd duren. ") );
					$venster->insertAction("important", gettext("migratie wordt uitgevoerd"), "");
					$venster->addTag("br");
					$venster->addCode( gettext("De migratie wordt momenteel in de achtergrond uitgevoerd...") );
				} else {
					/* if no lockfile */
					/* write lockfile */
					$mailMigration->writeLock();

					$load_migration = 1;
					$venster->insertAction("important", gettext("migratie wordt uitgevoerd"), "");
					$venster->addCode( gettext("De migratie van de email wordt momenteel uitgevoerd, dit kan enkele minuten tot lange tijd duren. ") );
					$venster->insertAction("important", gettext("migratie wordt uitgevoerd"), "");
					$venster->addTag("br");
					$venster->addCode( gettext("Sluit dit venster niet af voordat de migratie voltooid is.") );
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->addTag("span", array("id"=>"migration_progressbar"));
						$venster->addCode("0% (".gettext("bezig met initialiseren").")" );
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
	$list_from        = $_REQUEST["list_from"];
	$search           = $_REQUEST["search"];
	$action_value     = $_REQUEST["action_value"];

	if ($_REQUEST["address_id"]) {
		$list_of_address = $_REQUEST["address_id"];
	}

	/* define basic email data object */
	$mailData = new Email_data();

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
	$folderName = $mailData->_folders[$folder_id]["name"];

	if ($list_of_address) {
		$addressData = new Address_data();
		$addressinfo = sprintf(" %s %s", gettext("van"), $addressData->getAddressNameById($list_of_address));
	} elseif ($project_id) {
		$project_data = new Project_data();
		$addressinfo = sprintf(" %s %s", gettext("van project"), $project_data->getProjectNameById($project_id));
	} else {
		$addressinfo = "";
	}

	$subtitle = " ".gettext("in")." ".$folderName;
	if ($search) {
		$subtitle .= " - ".gettext("zoekwoord").": ".$search;
	}
	$settings = array(
		"title"    => gettext("Berichten").$addressinfo,
		"subtitle" => $subtitle
	);

	$formdata = array(
		"action"                     => $action,
		"action_value"               => $action_value,
		"folder_id"                  => $folder_id,
		"mail_id"                    => $mail_id,
		"sort"                       => $sort,
		"attachment_id"              => $attachment_id,
		"list_of_address"            => $list_of_address,
		"list_of_address_alt"        => $list_of_address_alt,
		"list_from"                  => $list_from,
		"mod"                        => "email",
		"sort"                       => $_REQUEST["sort"]
	);

	$venster = new Layout_venster($settings);
	$venster->addVensterData();

	$table = new Layout_table( array("width"=>"100%") );
	$table->addTableRow();
		$table->addTableData();
			if ($list_of_address) {
				$table->insertAction("addressbook", gettext("relatiekaart"), "?mod=address&action=relcard&id=".$list_of_address);

				/* get projects for this address */
				if ($GLOBALS["covide"]->license["has_project"]) {
					/* get the project data */

					$project_data = new Project_data();
					$projectoptions = array("address_id" => $list_of_address);
					$projectinfo = $project_data->getProjectsBySearch($projectoptions);
					unset($projectoptions);

					$sel = array(
						"0" => "- ".gettext("projecten")." -"
					);
					foreach ($projectinfo as $k=>$v) {
						$sel[$v["id"]] = $v["name"];
					}
					$table->addSpace(2);
					$table->addSelectField("project_id", $sel, $project_id);
				}
			}
			//if the current view is a project
			if ($project_id) {
				$projectData = new Project_data();
				$projectinfo = $projectData->getProjectById($project_id);

				if ($projectinfo[0]["address_id"]) {
					$addressData = new Address_data();
					$addressinfo = $addressData->getAddressNameById($projectinfo[0]["address_id"]);
					$table->insertAction("addressbook", gettext("Ga naar de relatiekaart van: ").$addressinfo, "?mod=address&action=relcard&id=".$projectinfo[0]["address_id"]);
					$table->insertAction("view_all", gettext("Toon emails van de relatie: ").$addressinfo, "?mod=email&folder_id=".$folder_id."&address_id=".$projectinfo[0]["address_id"]);
				}
				$table->insertAction("folder_project", gettext("Ga naar de projectkaart"), "?mod=project&action=showinfo&id=".$project_id);


			}
			//display the map usage
			$table->insertSpacer(20);
		$table->endTableData();
		$table->addTableData();
			$table->addCode( $output->nbspace(3) );
			$table->addCode( gettext("zoeken op").": ");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search", $search, "", "", 1);
			$table->insertAction("forward", gettext("zoeken"), "javascript:set('action',''); set('list_from', ''); submitform();");
			if ($search) {
				$table->insertAction("toggle", "alles tonen", "javascript:set('action',''); set('search',''); submitform();");
			}
		$table->endTableData();
		$table->addTableData( array("align"=>"right") );
			$table->addSpace(10);
			$table->insertAction("mail_signatures", gettext("mail signatures"), "javascript:set('action', 'signatures'); submitform();");
			$table->insertAction("mail_filters", gettext("mail filters bewerken"), "javascript:set('action', 'filters'); submitform();");
			$table->insertAction("mail_templates", gettext("mail templates"), "javascript:set('action', 'templates'); submitform();");
			$table->insertAction("mail_retrieve", gettext("forceer mail ophalen"), "javascript: popup('index.php?mod=email&action=retrieve&user_id=".$_SESSION["user_id"]."', 'mailfetch', 400, 350, 1);");
			$table->addSpace(10);

			$table->insertAction("mail_new", gettext("nieuwe email"), "javascript: emailSelectFrom()");
			$table->insertAction("addressbook", gettext("nieuwe email vanuit het adresboek"), "?mod=address");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster->addCode ($table->generate_output());
	$venster->addTag("br");

	$options = array(
		"search"   => $search,
		"project"  => $project_id,
		"relation" => $list_of_address,
		"folder"   => $folder_id,
		"user"     => $_SESSION["user_id"]
	);

	/* get the data we need */
	$part  =  $mailData->getEmailBySearch($options, $list_from, $_REQUEST["sort"]);
	$data  =& $part["data"];
	$total =  $part["count"];

	/* create a new view and add the data */
	$view = new Layout_view();
	$view->addData($data);

	#$output->debug_output($data);

	/* add the mappings (columns) we needed */
	$view->addMapping("%%header_fromto", "%%data_fromto");
	$view->addMapping(gettext("datum"), "%%data_datum", "right");
	$view->addMapping("%%header_attachments", "%%data_attachments", "", "", 1, 1);
	$view->addMapping("%%header_actions", "%%data_actions", "right");

	$view->addSubMapping("%%data_subject", "%is_new");
	$view->addSubMapping("%%data_description", "");

	$escape = sql_syntax("escape_char");
	/* define sort columns */
	$view->defineSortForm("sort", "velden");
	$view->defineSort("subject", "subject");
	$view->defineSort(gettext("datum"), "date");

	/* define complex header fromto */
	$view->defineComplexMapping("header_fromto", array(
		array(
			"text"  => gettext("onderwerp"),
			"alias" => "subject"
		),
		array(
			"text"  =>	"\n"
		),
		array(
			"text"  => gettext("afzender"),
			"alias" => "sender"
		),
		array(
			"text"  =>	"/"
		),
		array(
			"text"  => gettext("ontvanger"),
			"alias" => "receipient"
		)
	));
	/* define complex data fromto */
	$view->defineComplexMapping("data_fromto", array(
		array(
			"text" => array(
				gettext("van"), ": ",	"%sender_emailaddress", "\n",
				gettext("aan"), ": ",	"%to"
			)
		)
	));
	/* define complex mapping for subject */

	/* if this folder is concepts, then directly edit the email */
	$concept_folder = $mailData->getSpecialFolder("Concepten", $_SESSION["user_id"]);
	if ($concept_folder["id"] == $folder_id) {
		$_action = "compose";
	} else {
		$_action = "open";
	}
	$view->defineComplexMapping("data_subject", array(
		array(
			"type" => "link",
			"text" => "%subject",
			"link" => array("?mod=email&action=$_action&id=", "%id")
		)
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
		array(
			"type" => "action",
			"src"  => "save",
			"alt"  => gettext("de geselecteerde bijlage(n) opslaan in bestandsbeheer"),
			"link" => "javascript: selection_attachments_save();"
		),
		array(
			"type" => "action",
			"src"  => "open",
			"alt"  => gettext("de geselecteerde bijlage(n) downloaden"),
			"link" => "javascript: selection_attachments_download();"
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("de geselecteerde bijlage(n) verwijderen"),
			"link" => "javascript: selection_attachments_delete();"
		),
		array(
			"type" => "action",
			"src"  => "file_zip",
			"alt"  => gettext("de geselecteerde bijlage(n) downloaden als zip-bestand"),
			"link" => "javascript: selection_attachments_zip();"
		)
	));
	/* define complex mapping for header actions */
	$view->defineComplexMapping("header_actions", array(
		array(
			"type" => "action",
			"src"  => "save",
			"alt"  => gettext("de geselecteerde email verplaatsen"),
			"link" => "javascript: selection_email_move();"
		),
		array(
			"type" => "action",
			"src"  => "toggle",
			"alt"  => gettext("de geselecteerde email op gelezen/ongelezen zetten"),
			"link" => "javascript: selection_email_togglestate();"
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("de geselecteerde email verwijderen"),
			"link" => "javascript: selection_email_delete();"
		),
		array(
			"text" => $output->insertCheckbox(array("checkbox_mail_toggle_all"), "1", 0, 1)
		)
	));
	/* define complex mapping for data actions */
	$view->defineComplexMapping("data_actions", array(
		array(
			"type"  => "action",
			"src"   => "addressbook",
			"alt"   => gettext("deze email is gekoppeld aan een relatie"),
			"link"  => array("javascript: popup('?mod=address&action=relcard&id=", "%address_id", "');"),
			"check" => "%address_id"
		),
		array(
			"type"  => "action",
			"src"   => "%is_public_i",
			"alt"   => "%is_public_h"
		),
		array(
			"text"  => array("<span id='relation_", "%id", "'>")
		),
		array(
			"type"  => "action",
			"src"   => "important",
			"alt"   => gettext("kies een relatie"),
			"link"  => array("javascript: showInfo('", "%id", "')"),
			"check" => "%check_askwichrel"
		),
		array(
			"text"  => "</span>"
		),
		array(
			"type"  => "action",
			"src"   => "mail_tracking",
			"alt"   => gettext("bekijk ontvanger statistieken"),
			"link"  => array("javascript: showTracking('", "%id", "');"),
			"check" => "%tracking"
		),
		array(
			"type"  => "action",
			"alt"   => gettext("toon info"),
			"src"   => "info",
			"link"  => array("javascript: showInfo('", "%id", "');")
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("verwijderen"),
			"link"  => array("javascript: deleteMail('", "%id", "');")
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

	$table_folders = new Layout_table();
		$table_folders->addTableRow();
			$table_folders->addTableData();
			$table_folders->insertAction("new", gettext("nieuwe map aanmaken in de huidige map"), "javascript: createNewFolder();");
			if (!$mailData->checkSpecialFolder($folder_id)) {
				$table_folders->insertAction("edit", gettext("de huidige map bewerken"), "javascript: editCurrentFolder();");
				$table_folders->insertAction("delete", gettext("de huidige map verwijderen"), "javascript: deleteCurrentFolder();");
				$table_folders->insertAction("cut", gettext("de huidige map verplaatsen"), "javascript: moveCurrentFolder();");
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
				$folders = $mailData->getFolders( array("count"=>1, "relation"=>$list_of_address ), $archive );
				$table_folders->addCode( $this->getFolderList($folders, $folder_id) );
			$table_folders->endTableData();
		$table_folders->endTableRow();
	$table_folders->endTable();


	$venster_mappen = new Layout_venster(array("title" => gettext("Email mappen")));
	$venster_mappen->addVensterData();
	$venster_mappen->addCode( $table_folders->generate_output() );
	$venster_mappen->endVensterData();

	$table_container = new Layout_table( array("class"=>"fullwidth") );
		$table_container->addTableRow();
			$table_container->insertTableData( $output->nbspace() );
			$table_container->addTableData( array("valign"=>"top") );
				if (!$GLOBALS["covide"]->mobile) {
					$table_container->addCode( $venster_mappen->generate_output() );
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
	$output->addHiddenField("msg_confirm_delete", gettext("Weet u zeker dat u de geselecteerde items wilt verwijderen?"));

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
			$output->start_javascript();
			$output->addCode("alert('Uw email is verzonden.')");
			$output->end_javascript();
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
