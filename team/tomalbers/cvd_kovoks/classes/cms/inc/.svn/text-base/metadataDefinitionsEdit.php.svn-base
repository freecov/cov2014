<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("metadata definitie toevoegen/wijzigen")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getMetadataDefinitionById($_REQUEST["id"]);
	$venster->addVensterData();

		$tbl = new Layout_table(array(
			"cellspacing" => 1
		));
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("Metadata definitie"), array("colspan"=>2), "header");
		$tbl->endTableRow();
		/* field name */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("naam"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[field_name]", $cms["field_name"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* group */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("groep"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[group]", $cms["group"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* field type */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("soort veld"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addSelectField("cms[field_type]", $cms_data->meta_field_types, $cms["field_type"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* order */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("volgorde"), "", "header");
			$tbl->addTableData("", "data");
				$sel = array();
				for ($i=0;$i<=50;$i++) {
					$sel[$i] = $i;
				}
				$tbl->addSelectField("cms[order]", $sel, $cms["order"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* show frontpage */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("actief op site"), "", "header");
			$tbl->addTableData("", "data");
				$sel = array(
					0 => "+ ".gettext("toon dit item in de site"),
					1 => "- ".gettext("verberg dit item in de site")
				);
				$tbl->addSelectField("cms[fphide]", $sel, $cms["fphide"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default value */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("standaard waarde")."*", "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[field_value]", $cms["field_value"], array(
					"style" => "width: 300px; height: 150px;",
					"wrap"  => "off"
				));
				$tbl->addTag("i");
				$tbl->addTag("br");
				$tbl->addCode("* ".gettext("Deze waarde zal standaard zijn ingevuld."));
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("Bij het dropdown menu kunnen de beschikbare items gescheiden door een enter worden ingegeven, elk item op één regel."));
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext(" De eerste waarde hierbij zal de standaard waarde zijn. De waarde '--' (2 streepjes) zal niet worden getoond op de site."));
				$tbl->endTag("i");

			$tbl->endTableData();

		$tbl->endTableRow();


		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );


		$venster->insertAction("back", gettext("terug"), "?mod=cms&action=metadataDefinitions");
		$venster->insertAction("save", gettext("nieuw item"), "javascript: saveSettings();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveMetadataDefinition");
	$output->addHiddenField("id", $_REQUEST["id"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>