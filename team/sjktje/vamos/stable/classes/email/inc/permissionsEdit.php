<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$fspath = $GLOBALS["covide"]->filesyspath;

	$output = new layout_output();
	$output->layout_page();

	$venster = new Layout_venster( array(
		"title" => gettext("Mail permissions"),
		"subtitle" => gettext("edit")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_permissions_list($user_id, $id);
	}

	$venster->addMenuItem(gettext("permissions"), "?mod=email&action=show_permissions&user_id=".$user_id);
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );
	/* email */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("mail folder"), "", "header");
		$tbl->addTableData("", "data");
			/* folders */
			$folders = $mailData->getFolders("", $rel);
			$tbl->addCode($this->getSelectList("folder_id", $folders, $folder_id, array("style"=>"width: 250px") ) );
		$tbl->endTableData();
	$tbl->endTableRow();
	/* subject */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("access"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addHiddenField("users", $list[$id]["users"]);
			$useroutput = new User_output();
			$tbl->addCode( $useroutput->user_selection("users", $list[$id]["users"], 1, 0, 1, 0, 1) );
			unset($useroutput);
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("back"), "?mod=email&action=show_permissions&user_id=".$user_id);
			$tbl->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addVensterData();
		$venster->addCode( $tbl->generate_output() );
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "POST",
		"enctype" => "multipart/form-data"
	));
	$output->addHiddenField("mod", "email");
	$output->addHiddenField("action", "permissionsSave");
	$output->addHiddenField("id", $id);
	$output->addHiddenField("user_id", $user_id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."permissionsEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
