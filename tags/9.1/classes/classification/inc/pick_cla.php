<?php
	/**
	 * Covide Groupware-CRM Classification
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
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

	if ($GLOBALS["covide"]->license["address_strict_permissions"]) {
		$cla_perms = $classification_data->getClassificationByAccess();
		foreach ($classifications as $k=>$v) {
			if (!in_array($v["id"], $cla_perms))
				unset($classifications[$k]);
		}
		foreach ($classifications_all as $k=>$v) {
			if (!in_array($v["id"], $cla_perms))
				unset($classifications_all[$k]);
		}
	}

	$list = array();
	foreach ($classifications as $k=>$v) {
		if (!$search || stristr($v["description"], $search)) {
			if (strlen(trim($v["groupname"]))) {
				$list[$v["groupname"]][$v["id"]] = $v["description"];
			} else {
				$list[$v["id"]] = $v["description"];
			}
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
		if ($id && array_key_exists($id, $list_all)) {
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

	if ($_REQUEST["newcla"]["action"] == "save") {
		$new_id = $classification_data->store2db($_REQUEST["newcla"]);
		$classifications_selected[$new_id] = $_REQUEST["newcla"]["description"];
	}

	natcasesort($classifications);
	natcasesort($classifications_selected);

	/* second output handler for parent */
	$output_alt = new Layout_output();

	foreach ($classifications_selected as $k=>$v) {
		$output_alt->addTag("li", array("class"=>$_REQUEST["type"]));
		$output_alt->addCode($v);
		$output_alt->endTag("li");
	}

	$output_alt->output = preg_replace("/((\n)|(\r)|(\t))/s","",$output_alt->output);

	$search = $_REQUEST["search"];
	$sub_action = $_REQUEST["sub_action"];

	if ($sub_action != "init") {

		$venster_settings = array(
			"title"    => gettext("classifications"),
			"subtitle" => gettext("choose")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addVensterData();
			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "data");
					$tbl->addCode( gettext("search classification").": " );
					$tbl->addTextField("search", $search);
					$tbl->insertAction("ok", gettext("search"), "javascript: search_classification();");
					if ($search) {
						$tbl->insertAction("toggle", gettext("show all"), "javascript: search_show_all();");
					}
				$tbl->endTableData();
				$tbl->addTableData( array("align"=>"right"), "data" );
					$tbl->insertAction("close", gettext("close"), "javascript: closepopup();");
				$tbl->endTableData();

			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("available classifications").": " );
					$tbl->addTag("br");
					$tbl->addSelectField("classifications_available[]", $list, "", 1, array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->insertAction("forward", gettext("add selection"), "javascript: selection_add();");
					$tbl->addTag("br");
					$tbl->addTag("br");
					$tbl->insertAction("back", gettext("delete selection"), "javascript: selection_remove();");
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("applied classifications").": " );
					$tbl->addTag("br");
					$tbl->addSelectField("classifications_selected[]", $classifications_selected, "", 1, array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addCode( gettext("new classification").": " );
					$tbl->addTextField("newcla[description]", "");
					$tbl->addHiddenField("newcla[is_active]", 1);
					$tbl->addHiddenField("newcla[action]", "");
					$tbl->insertAction("save", gettext("save"), "javascript: create_classification();");
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
		$venster->endVensterData();


		$output->addCode( $venster->generate_output() );

		$output->endTag("form");

		$keys = array();
		foreach ($classifications_selected as $k=>$v) {
			$keys[]=$k;
		}
		$js_call = sprintf("selection_update_parent('%s','%s', '%s');", $_REQUEST["field_id"], "|".implode("|",$keys)."|", $output_alt->generate_output());
		$output->load_javascript(self::include_dir."pick_cla.js");
		$output->start_javascript();
		$output->addCode($js_call);
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
