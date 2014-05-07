<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("metadata definities")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getMetadataData($id, $_REQUEST["subaction"]);

	$this->addMenuItems(&$venster);
	$venster->generateMenuItems();

	$venster->addVensterData();
		if ($cms["useMetaData"] == 0) {
			$venster->addCode( gettext("Deze pagina heeft momenteel nog geen metadata.") );
			$venster->addCode( gettext("Klik")." " );
			$venster->insertTag("a", gettext("hier"), array(
				"href" => "?mod=cms&action=metadata&id=".$id."&subaction=enable"
			));
			$venster->addcode( " ".gettext("om metadata voor deze pagina in te schakelen.") );
			$venster->insertAction("ok", gettext("metadata inschakelen"), "?mod=cms&action=metadata&id=".$id."&subaction=enable");
		} else {
			$venster->addCode( gettext("Deze pagina beschikt over metadata.") );
			$venster->addCode( gettext("Klik")." " );
			$venster->insertTag("a", gettext("hier"), array(
				"href" => "?mod=cms&action=metadata&id=".$id."&subaction=disable"
			));
			$venster->addcode( " ".gettext("om metadata voor deze pagina uit te schakelen.") );
			$venster->insertAction("delete", gettext("metadata uitschakelen"), "?mod=cms&action=metadata&id=".$id."&subaction=disable");

			$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
			$venster->addTag("br");
			$venster->addTag("br");

			$tbl = new Layout_table(array("cellspacing"=>1));
			foreach ($cms["data"] as $group=>$data) {
				if (!$group) $group = sprintf("[%s]", gettext("standaard"));
				$tbl->addTableRow();
					$tbl->addTableData(array("colspan" => 2), "header");
						$tbl->insertAction("view_all", "", "");
						$tbl->addCode(" ". $group );
					$tbl->endTableData();
				$tbl->endTableRow();
				foreach ($data as $v) {
					$tbl->addTableRow();
						$tbl->addTableData("", "header");
							$tbl->addCode($v["field_name"]);
						$tbl->endTableData();
						$tbl->addTableData("", "data");
							$this->switchFieldType($tbl, $v);
						$tbl->endTableData();
					$tbl->endTableRow();
				}
			}
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );

			$venster->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
			$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");

	}

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveMetadata");
	$output->addHiddenField("id", $_REQUEST["id"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>