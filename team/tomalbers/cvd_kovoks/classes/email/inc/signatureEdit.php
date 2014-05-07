<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$fspath = $GLOBALS["covide"]->filesyspath;

	$output = new layout_output();
	$output->layout_page();

	$venster = new Layout_venster( array(
		"title" => gettext("E-mail signatures"),
		"subtitle" => gettext("bewerken")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_signature_list($id, $user_id);
	}

	$venster->addMenuItem(gettext("signatures"), "?mod=email&action=signatures");
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );
	/* email */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("email adres"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[email]", $list[0]["email"], array("style"=>"width: 350px"));
		$tbl->endTableData();
	$tbl->endTableRow();
	/* subject */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("omschrijving"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[subject]", $list[0]["subject"], array("style"=>"width: 350px"));
		$tbl->endTableData();
	$tbl->endTableRow();
	/* realname */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("afzender naam"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[realname]", $list[0]["realname"], array("style"=>"width: 350px"));
			//$tbl->insertTag("b", " *");
		$tbl->endTableData();
	$tbl->endTableRow();
	/* realname */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("bedrijfsnaam"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[companyname]", $list[0]["companyname"], array("style"=>"width: 350px"));
			//$tbl->insertTag("b", " *");
		$tbl->endTableData();
	$tbl->endTableRow();
	/* signature */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("handtekening"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextArea("mail[signature]", $list[0]["signature"], array("style"=>"width: 350px; height: 180px;"));
		$tbl->endTableData();
	$tbl->endTableRow();

	/*
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->addCode(" * = ");
			$tbl->addCode( gettext("Indien u dit veld leeg laat worden de standaard waarden gebruikt van de medewerkerskaart.") );
		$tbl->endTableData();
	$tbl->endTableRow();
	*/

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("terug"), "?mod=email&action=signatures");
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
	$output->addHiddenField("action", "signatureSave");
	$output->addHiddenField("id", $id);
	$output->addHiddenField("user_id", $user_id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."signatureEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
