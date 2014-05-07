<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$fspath = $GLOBALS["covide"]->filesyspath;

	$output = new layout_output();
	$output->layout_page();

	$venster = new Layout_venster( array(
		"title" => gettext("E-mail filters"),
		"subtitle" => gettext("bewerken")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_filter_list($id);
	}

	$venster->addMenuItem(gettext("filters"), "?mod=email&action=filters");
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );

	/* prioriteit */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("prioriteit"), "", "header");
		$tbl->addTableData("", "data");
			$prior = array();
			for ($i=0;$i<=100;$i++) {
				$prior[]=$i;
			}
			$tbl->addSelectField("mail[priority]", $prior, $list[0]["priority"]);
		$tbl->endTableData();
	$tbl->endTableRow();

	/* email from */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("van email"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[sender]", $list[0]["sender"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* email rcpt */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("ontvanger email"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[receipient]", $list[0]["receipient"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* subject */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("onderwerp"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[subject]", $list[0]["subject"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* to folder */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("naar map"), "", "header");
		$tbl->addTableData("", "data");
			$folders = $mailData->getFolders();
			$tbl->addCode($this->getSelectList("mail[to_mapid]", $folders, $list[0]["to_mapid"], array("style"=>"width: 250px") ) );
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->addCode(" * = ");
			$tbl->addCode( gettext("Onderwerp is een match op een gedeelte van het onderwerp, afzender en ontvanger is een exacte match.") );
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("terug"), "?mod=email&action=filters");
			$tbl->insertAction("save", gettext("opslaan"), "javascript: document.getElementById('velden').submit();");
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
	$output->addHiddenField("action", "filterSave");
	$output->addHiddenField("id", $id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."filtersEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
