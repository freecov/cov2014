<?php
	if (!class_exists("Classification_output")) {
		exit("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("classification", 1);

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "classification");
	$output->addHiddenField("action", "pick_cla");
	$output->addHiddenField("sub_action", "pick_cla");

	$output->addHiddenField("classifications", "pick_cla");
	$output->addHiddenField("field_id", $_REQUEST["field_id"]);
	$output->addHiddenField("type", $_REQUEST["type"]);
	$output->addHiddenField("init_ids", "");

	$search = $_REQUEST["search"];

	/* get classifications from db */
	$classification_data = new Classification_data();
	$classifications = $classification_data->getClassifications("", "", $search);
	$classifications_all = $classification_data->getClassifications("", "", "");

	foreach ($classifications as $k=>$v) {
		if (!$search || stristr($v["description"], $search)) {
			$list[$v["id"]] = $v["description"];
		}
	}
	foreach ($classifications_all as $k=>$v) {
		$list_all[$v["id"]] = $v["description"];
	}

	$classifications_selected = array();

	if ($_REQUEST["init_ids"]) {
		/* if mode is init */
		$classifications_selected_ids = explode("|", $_REQUEST["init_ids"]);
	} else {
		/* read previous selection */
		$classifications_selected_ids = $_REQUEST["classifications_selected"];
	}
	/* if no selection create an empty array */
	if (!is_array($classifications_selected_ids)) {
		$classifications_selected_ids = array();
	}
	/* get descriptions */
	foreach ($classifications_selected_ids as $id) {
		if ($id) {
			$classifications_selected[$id] = $list_all[$id];
		}
	}

	if ($_REQUEST["sub_action"] == "add") {
		$cla_available = $_REQUEST["classifications_available"];
		if (is_array($cla_available)) {
			foreach ($cla_available as $id) {
				$classifications_selected[$id] = $list_all[$id];
			}
		}
	}

	/* second output handler for parent */
	$output_alt = new Layout_output();

	foreach ($classifications_selected as $k=>$v) {
		$output_alt->addTag("li", array("class"=>$_REQUEST["type"]));
		$output_alt->addCode($v);
		$output_alt->endTag("li");
	}

	natcasesort($classifications);
	natcasesort($classifications_selected);



	$output_alt->output = preg_replace("/((\n)|(\r)|(\t))/s","",$output_alt->output);


	$search = $_REQUEST["search"];
	$sub_action = $_REQUEST["sub_action"];

	if ($sub_action != "init") {

		$venster_settings = array(
			"title"    => gettext("classificaties"),
			"subtitle" => gettext("kiezen")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addVensterData();
			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "data");
					$tbl->addCode( gettext("zoeken naar classificatie").": " );
					$tbl->addTextField("search", $search);
					$tbl->insertAction("ok", gettext("zoeken"), "javascript: search_classification();");
					if ($search) {
						$tbl->insertAction("toggle", gettext("alles tonen"), "javascript: search_show_all();");
					}
				$tbl->endTableData();
				$tbl->addTableData( array("align"=>"right"), "data" );
					$tbl->insertAction("close", gettext("sluiten"), "javascript: window.close();");
				$tbl->endTableData();

			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("beschikbare classificaties").": " );
					$tbl->addTag("br");
					$tbl->addSelectField("classifications_available[]", $list, "", 1, array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->insertAction("forward", gettext("selectie toevoegen"), "javascript: selection_add();");
					$tbl->addTag("br");
					$tbl->addTag("br");
					$tbl->insertAction("back", gettext("selectie verwijderen"), "javascript: selection_remove();");
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("gekozen classificaties").": " );
					$tbl->addTag("br");
					$tbl->addSelectField("classifications_selected[]", $classifications_selected, "", 1, array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
		$venster->endVensterData();


		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->load_javascript(self::include_dir."pick_cla.js");
		$output->start_javascript();

		$keys = array();
		foreach ($classifications_selected as $k=>$v) {
			$keys[]=$k;
		}
		$output->addCode( sprintf("selection_update_parent('%s','%s','%s');",
			$_REQUEST["field_id"], "|".implode("|",$keys)."|", $output_alt->generate_output() )
		);
		$output->end_javascript();

	} else {

		$output->load_javascript(self::include_dir."pick_cla.js");
		$output->start_javascript();
		$output->addCode( sprintf("selection_init('%s');", $_REQUEST["field_id"] ));
		$output->end_javascript();

	}

	$output->layout_page_end();
	$output->exit_buffer();
?>
