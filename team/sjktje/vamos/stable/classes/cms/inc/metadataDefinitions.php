<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("metadata definitions")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getMetadataDefinitions();

	$venster->addVensterData();
		if (count($cms) == 0) {
			$venster->addCode(gettext("No metadata definitions found"));
			$venster->addTag("br");
		} else {

			$tbl = new Layout_table(array("cellspacing"=>1));
			foreach ($cms as $group=>$data) {
				if (!$group) $group = sprintf("[%s]", gettext("default"));
				$tbl->addTableRow();
					$tbl->addTableData("", "header");
						$tbl->insertAction("view_all", "", "");
						$tbl->addCode(" ". $group );
					$tbl->endTableData();
				$tbl->endTableRow();
				$tbl->addTableRow();
					$tbl->addTableData();

						$view = new Layout_view();
						$view->addData($data);

						$view->addMapping( gettext("order"), "%order" );
						$view->addMapping( gettext("fieldname"), "%field_name" );
						$view->addMapping( gettext("fieldtype"), "%field_type_h" );
						$view->addMapping( gettext("default value"), "%field_value" );
						$view->addMapping( gettext("show on website"), "%%complex_hidefp" );
						$view->addMapping( " ", "%%complex_actions" );

						$view->defineComplexMapping("complex_hidefp", array(
							array(
								"type"  => "action",
								"src"   => "ok",
								"check" => "%fpshow"
							),
							array(
								"type"  => "action",
								"src"   => "cancel",
								"check" => "%fphide"
							)
						));


						$view->defineComplexMapping("complex_actions", array(
							array(
								"type" => "action",
								"src"  => "edit",
								"alt"  => gettext("edit"),
								"link" => array("?mod=cms&action=?mod=cms&action=metadataDefinitionsEdit&id=", "%id", "&user_id=", $user_id)
							),
							array(
								"type" => "action",
								"src"  => "delete",
								"alt"  => gettext("delete"),
								"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=metadataDefinitionsDelete&id=", "%id", "&user_id=", $user_id, "';")
							)
						));
						$tbl->addCode($view->generate_output());

					$tbl->endTableData();
				$tbl->endTableRow();
			}
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );
		}


		$venster->insertAction("new", gettext("new item"), "?mod=cms&action=metadataDefinitionsEdit");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");

	$venster->endVensterData();

	$output->addCode($venster->generate_output());
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>