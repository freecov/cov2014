<?
	if (!class_exists("Filesys_output")) {
		exit("no class definition found");
	}

	$fsdata = new Filesys_data();

	$output = new Layout_output();
	$output->layout_page("filesys", 1);

	$folder = $_REQUEST["folder"];

	$venster = new Layout_venster(array(
		"title" => gettext("bestandsbeheer"),
		"subtitle" => gettext("mapstructuur verplaatsen")
	));
	$venster->addVensterData();

		$table = new Layout_table( array(
			"width"   => "100%",
			"cellspacing" => 0,
			"cellpadding" => 0
		));
		$table->addTableRow();
			$table->addTableData("", "data");

				$fsdata = new Filesys_data();
				$folders = $fsdata->getFolderArray($_REQUEST["folder"]);

				$table->addCode( gettext("U staat op het punt de volgende mappen te verplaatsen").": ");
				$table->addTag("br");
				foreach ($folders as $v) {
					if ($v["permissions"] != "W") {
						$deny = 1;
					}
				}
				if ($deny == 1) {
					$table->addTag("br");

					$tbl = new Layout_table( array(
						"style" => "border: 2px dotted red"
					));
					$tbl->addTableRow();
						$tbl->addTableData();
							$tbl->insertAction("important", "", "");
						$tbl->endTableData();
						$tbl->addTableData();
							$tbl->addCode( gettext("U kunt de gekozen map niet compleet verplaatsen omdat te weinig rechten hebt op de met rood aangegeven mappen."));
							$tbl->addTag("br");
							$tbl->addCode( gettext("Neem contact op met iemand die u deze rechten wel kan gegeven. "));
						$tbl->endTableData();
						$tbl->addTableData();
							$tbl->insertAction("important", "", "");
						$tbl->endTableData();
						$tbl->endTable();

						$table->addCode( $tbl->generate_output() );
						unset($tbl);
				}
				$table->addTag("br");

				$view = new Layout_view();
				$view->addData($folders);
				$view->addMapping(gettext("mappen"), "%%complex_name");
				#$view->addMapping(gettext("omschrijving"), "%description");

				$view->defineComplexMapping("complex_name", array(
					array(
						"text"  => "%spacing"
					),
					array(
						"type"  => "action",
						"src"   => "%foldericon"
					),
					array(
						"text"  => array(" ","%name")
					)
				));

				$table->addCode( $view->generate_output() );

				$table->addTag("br");
				$table->insertAction("back", gettext("terug"), "javascript: window.close();");
				$table->addSpace(3);
				if (!$deny) {
					$table->insertAction("ok", gettext("verder met verwijderen"), "javascript: update_pastebuffer()");
					$table->start_javascript();
					$table->addCode("
						function update_pastebuffer() {
							var f = opener.document.getElementById('velden');
							f.pastebuffer.value = 'folder,$folder';
							f.submit();
							window.close();
						}
					");
					$table->end_javascript();
				}


			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$venster->addCode( $table->generate_output() );

	$venster->endVensterData();
	$output->addCode( $venster->generate_output() );

	$output->exit_buffer();
?>