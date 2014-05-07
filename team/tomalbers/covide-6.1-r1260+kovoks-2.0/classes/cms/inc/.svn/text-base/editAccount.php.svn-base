<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("externe accounts")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getAccountList($id);
	$cms = $cms[0];

	$this->addMenuItems($venster);
	$venster->generateMenuItems();

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("gebruikersnaam"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[username]", $cms["username"]);
				$tbl->insertTag("span", "", array(
					"id"    => "username_layer"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("wachtwoord"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[password]", $cms["password"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("actief"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCheckBox("cms[is_enabled]", 1, ($cms["is_enabled"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("back", gettext("terug"), "?mod=cms&action=editAccountsList");
		$venster->addTag("span", array(
			"id"    => "save_page_layer",
			"style" => "visibility: hidden;"
		));
			$venster->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
		$venster->endTag("span");
		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveAccount");
	$output->addHiddenField("id", $id);

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."editAccount.js");
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();

?>