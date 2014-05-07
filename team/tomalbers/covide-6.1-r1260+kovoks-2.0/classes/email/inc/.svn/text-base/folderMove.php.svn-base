<?php
	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	$folder_id = $_REQUEST["folder_id"];
	$mailData = new Email_data();

	$output = new Layout_output();
	$output->layout_page();

	$settings = array(
		"title"    => "E-mail map",
		"subtitle" => gettext("verplaatsen")
	);

	$address = new Address_data();
	$address_info = $address->getAddressNameByID($rel);

	$project = new Project_data();
	$project_info = $project->getProjectById($proj);

	$tbl = new Layout_table( array("width"=>"100%") );
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("Map naam").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			/* get selected emails */
			$folder = $mailData->getFolder($_REQUEST["folder_id"]);
			$tbl->addCode( $folder["name"] );

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode( gettext("Verplaatsen naar").": " );
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			/* get folder list */
			$folders = $mailData->getFolders( array("count"=>1), 0);
			$tbl->addCode( $this->getFolderList($folders, $folder["id"], 1) );

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData( array("colspan"=>2, "align"=>"right"), "data" );
			$tbl->insertAction("back", gettext("terug"), "javascript: history.go(-1);");
			$tbl->insertAction("save", gettext("opslaan"), "javascript: selection_save();");
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

	$output->addHiddenField("mod", "email");
	$output->addHiddenField("target_id", "");
	$output->addHiddenField("folder_id", $_REQUEST["folder_id"]);
	$output->addHiddenField("action", "move_folder_exec");

	$output->addCode( $table->generate_output() );
	$output->start_javascript();
	$output->addCode("
		function setFolder(id) {
			document.getElementById('target_id').value = id;
			document.getElementById('velden').submit();
		}
	");
	$output->end_javascript();
	$output->load_javascript(self::include_dir_main."js_form_actions.js");

	$output->endTag("form");

	$output->layout_page_end();
	$output->exit_buffer();

?>