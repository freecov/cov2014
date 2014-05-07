<?php
if (!class_exists("Project_output")) {
	exit("no class definition found");
}
/* get projectinfo from database */
$address_data = new Address_data();
$project_data = new Project_data();
$projectinfoarr = $project_data->getProjectById($projectid, $master);
$projectinfo = $projectinfoarr[0];
$masterprojects = $project_data->getMasterProjectArray();
unset($projectinfoarr);
$userdata = new User_data();
$users = $userdata->getUserlist();

/* get relation name */
$address = new Address_data();
if ((int)$projectinfo["address_id"] && !$projectinfo["multirel"]) {
	$relname = $address_data->getAddressNameById($projectinfo["address_id"]);
} else {
	$relname = "";
}

/* see if we need to do some magic on the selected addresses */
if ($projectinfo["multirel"] && !$master) {
	$address_ids = explode(",", $projectinfo["multirel"]);
	$address_ids[] = $projectinfo["address_id"];
	sort($address_ids);
	$multirel = array();
	foreach ($address_ids as $aid) {
		$multirel[$aid] = $address_data->getAddressNameById($aid);
	}
	unset($address_ids);
	unset($projectinfo["address_id"]);
	$relname = "";
} else {
	$multirel = array(
		$projectinfo["address_id"] => $address_data->getAddressNameById($projectinfo["address_id"])
	);
	unset($projectinfo["address_id"]);
	$relname = "";
}
unset($projectinfo["multirel"]);
$projectinfo["multirel"] = $multirel;

/* start output */
$output = new Layout_output();
$output->layout_page("", 1);
/* form arount the whole widget */
$output->addTag("form", array(
	"id"     => "projectedit",
	"method" => "get",
	"action" => "index.php"
));
$output->addHiddenField("mod", "project");
$output->addHiddenField("action", "save_project");
$output->addHiddenField("activity_switch", "-1");
$output->addHiddenField("project[master]", $master);
$output->addHiddenField("project[id]", $projectinfo["id"]);
/* window widget */
if ($master) {
	if ($projectid) {
		$subtitle = gettext("hoofdproject bewerken");
	} else {
		$subtitle = gettext("hoofdproject aanmaken");
	}
} else {
	if ($projectid) {
		$subtitle = gettext("project bewerken");
		$address_data = new Address_data();
		$bcardinfo = $address_data->getBcardsByRelationID($projectinfo["address_id"]);
		unset($address_data);
		$bcards = array(0 => gettext("geen"));
		foreach($bcardinfo as $v) {
			$bcards[$v["id"]] = $v["givenname"]." ".$v["infix"]." ".$v["surname"];
		}
	} else {
		$bcards = array(0 => gettext("geen"));
		$subtitle = gettext("project aanmaken");
	}
}
$venster_settings = array(
	"title"    => gettext("projecten"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$table = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1
	));
	$table->addTableRow();
		$table->insertTableData(gettext("nummer")."/".gettext("naam"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("project[name]", $projectinfo["name"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("omschrijving"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("project[description]", $projectinfo["description"], array("style"=>"width: 300px; height: 100px;"));
		$table->endTableData();
	$table->endTableRow();
	if (!$projectinfo["id"]) {
		$projectinfo["manager"] = $_SESSION["user_id"];
	}
	/* manager */
	$table->addTableRow();
		$table->insertTableData(gettext("projectmanager"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("project[manager]", $projectinfo["manager"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("projectmanager", $projectinfo["manager"], 0, 0, 1) );
			unset($useroutput);

		$table->endTableData();
	$table->endTableRow();

	/* access */
	$table->addTableRow();
		$table->insertTableData(gettext("toegang"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("project[users]", $projectinfo["users"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("projectusers", $projectinfo["users"], 1, 0, 1, 0, 1) );
			unset($useroutput);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("relatie"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("project[address_id]", $projectinfo["address_id"]);
			$table->insertTag("span", "", array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("actief"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("project[is_active]", "1", $projectinfo["is_active"]);
		$table->endTableData();
	$table->endTableRow();

	/* specific fields for a normal project */
	if (!$master) {
		$table->addTableRow();
			$table->insertTableData(gettext("businesscard"), "", "header");
			$table->addTableData("", "data");
				$table->addTag("div", array("id" => "project_bcard_layer"));
					$table->addHiddenField("project[bcard]", $projectinfo["address_businesscard_id"]);
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("deelproject van"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("project[group_id]", $masterprojects, $projectinfo["group_id"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("budget")." &euro;", "", "header");
			$table->addTableData("", "data");
				$table->addTextField("project[budget]", $projectinfo["budget"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("uren"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("project[hours]", $projectinfo["hours"]);
			$table->endTableData();
		$table->endTableRow();
	}
	/* end normal project specific fields */
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("save", gettext("opslaan"), "javascript: save_project();");
			$table->insertAction("close", gettext("sluiten"), "javascript: window.close();");

			/* XXX: we need to figure out a way to remove/disable projects */
			/*
			if($projectid) {
				$table->addSpace(2);
				$table->insertAction("delete", gettext("verwijderen"), "javascript: delete_project();");
			}
			*/
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$tbl = new Layout_table();
	$tbl->addTableRow();
		$tbl->addTableData("", "top");
			$tbl->addCode( $table->generate_output() );
		$tbl->endTableData();
		$tbl->addTableData("", "top");
			if ($GLOBALS["covide"]->license["has_project_ext"] && !$master) {
				$projectext = new ProjectExt_output();
				$tbl->addCode( $projectext->genExtraProjectFields($projectinfo["id"]) );
			}
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addCode($tbl->generate_output());
/* end window widget */
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
if ($master) {
	$output->load_javascript(self::include_dir."edit_master_project.js");
} else {
	$output->load_javascript(self::include_dir."edit_project.js");
	$output->start_javascript();
	$output->addCode("updateBcards();");
	$output->end_javascript();
}
$output->start_javascript();
foreach ($projectinfo["multirel"] as $aid=>$aname) {
	if (trim($aid)) {
		$output->addCode("selectRel($aid, '$aname');\n");
	}
}
$output->end_javascript();
/* end output */
$output->layout_page_end();
$output->exit_buffer();

?>
