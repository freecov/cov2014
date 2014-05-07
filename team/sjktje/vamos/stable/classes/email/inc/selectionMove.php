<?php
	if (!class_exists("Email")) {
		exit("no class definition found");
	}
	if ($_REQUEST["mail"]["ids"]) {
		$ids = explode(",", $_REQUEST["mail"]["ids"]);
	} else {
		$ids = $this->parseEmailCheckbox($_REQUEST["checkbox_mail"]);
	}
	if (!$ids) $ids = array(0);
	$ids_text = implode(",", $ids);

	$folder_id = $_REQUEST["folder_id"];
	$mailData = new Email_data();

	$output = new Layout_output();
	$output->layout_page();

	$settings = array(
		"title"    => "E-mail",
		"subtitle" => gettext("move")
	);

	/* get the relation(s) */
	if ($_REQUEST["mail"]["address_id"]) {
		$rel = $_REQUEST["mail"]["address_id"];
	} else {
		if (count($ids)>0) {
			$q = sprintf("select address_id, count(*) from mail_messages where id IN (%s) group by address_id", $ids_text);
			$res = sql_query($q);
			if (sql_num_rows($res)==1) {
				$rel = sql_result($res,0);
			}
		}
	}

	/* get the projects(s) */
	if ($_REQUEST["mail"]["project_id"]) {
		$proj = $_REQUEST["mail"]["project_id"];
	} else {
		if (count($ids)>0) {
			$q = sprintf("select project_id, count(*) from mail_messages where id IN (%s) group by project_id", $ids_text);
			$res = sql_query($q);
			if (sql_num_rows($res)==1) {
				$proj = sql_result($res,0);
			}
		}
	}


	$address = new Address_data();
	$address_info = $address->getAddressNameByID($rel);

	$project = new Project_data();
	$project_info = $project->getProjectById($proj);

	$tbl = new Layout_table( array("width"=>"100%") );
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("E-mails").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			/* get selected emails */
			foreach ($ids as $k=>$v) {
				$data = $mailData->getEmailById($v);
				$tbl->addCode( $data[0]["subject"] );
				$tbl->addTag("br");
			}
			$tbl->addTag("br");

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("Contact").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			/* get relation */
			if ($rel) {
				$tbl->addTag("a", array(
					"href"=>"javascript: popup('?mod=address&action=relcard&id=".$rel."', 'relation_card');"
				));
				$tbl->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
				$tbl->endTag("a");
			} else {
				$tbl->insertTag("span", $address_info, array("id"=>"layer_mail_relation"));
			}
			$tbl->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("Project").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			/* projects */
			$tbl->addTag("span", array("id"=>"layer_mail_project"));
			if ($project_info[0]["name"]) {
				$tbl->addCode( $project_info[0]["name"] );
			} else {
				$tbl->addCode(gettext("none"));
			}
			$tbl->endTag("span");
			$tbl->insertAction("edit", "wijzigen", "javascript: popup('?mod=project&action=search_project', 'search_project');");

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("Folder").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$folders = $mailData->getFolders("", $rel);
			$folders_shared = $mailData->getSharedFolderAccess($_SESSION["user_id"]);

			$tbl->addCode($this->getSelectList("folder_id", $folders, $folder_id, array("style"=>"width: 250px"), $folders_shared ) );
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData( array("colspan"=>2, "align"=>"right"), "data" );
			$tbl->insertAction("back", gettext("back"), "javascript: history.go(-1);");
			$tbl->insertAction("save", gettext("save"), "javascript: selection_save();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster = new Layout_venster($settings);
	$venster->addVensterData();
		$venster->addCode( $tbl->generate_output() );
	$venster->endVensterData();

	$table = new Layout_table();
	$table->addTableRow();
		$table->addTableData();
			$table->addCode( $venster->generate_output() );
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$output->addTag("form", array(
		"id" => "velden",
		"method" => "GET",
		"action" => "index.php"
	));

	//get archive id
	$archive = $mailData->getSpecialFolder("Archief", 0);

	$output->addHiddenField("mod", "email");
	$output->addHiddenField("action", "selection_move");
	$output->addHiddenField("mail[address_id]", $rel);
	$output->addHiddenField("mail[project_id]", $proj);
	$output->addHiddenField("mail[ids]", $ids_text);
	$output->addHiddenField("mail[archive]", $archive["id"]);

	$output->addCode( $table->generate_output() );
	$output->load_javascript(self::include_dir."selectionMove.js");
	$output->load_javascript(self::include_dir_main."js_form_actions.js");

	$output->endTag("form");

	$output->layout_page_end();
	$output->exit_buffer();

?>
