<?php
	if (!class_exists("User_output")) {
		exit("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("users", 1);

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));

	$output->addHiddenField("mod", "user");
	$output->addHiddenField("action", "pick_user");
	$output->addHiddenField("sub_action", "pick_user");

	$output->addHiddenField("users", "pick_user");
	$output->addHiddenField("field_id", $_REQUEST["field_id"]);
	$output->addHiddenField("init_ids", "");

	$output->addHiddenField("inactive", $_REQUEST["inactive"]);
	$output->addHiddenField("archive" , $_REQUEST["archive"]);
	$output->addHiddenField("multiple", $_REQUEST["multiple"]);
	$output->addHiddenField("no_empty", $_REQUEST["no_empty"]);
	$output->addHiddenField("showGroups", $_REQUEST["showGroups"]);
	$output->addHiddenField("confirm", $_REQUEST["confirm"]);

	/* get users from db */
	$user_data = new User_data();
	$list = $user_data->getNestedUserList($_REQUEST["archive"],$_REQUEST["inactive"], $_REQUEST["showGroups"]);
	if ($_REQUEST["search"]) {
		$list_search = array();
		foreach ($list as $t=>$type) {
			foreach ($type as $k=>$v) {
				if (stristr($v, $_REQUEST["search"])) {
					$list_search[$t][$k] = $v;
				}
			}
		}
	}

	$users_selected = array();

	if ($_REQUEST["init_ids"]) {
		/* if mode is init */
		$users_selected_ids = explode(",", $_REQUEST["init_ids"]);
	} else {
		/* read previous selection */
		$users_selected_ids = $_REQUEST["users_selected"];
	}

	if (is_array($users_selected_ids)) {
		foreach ($users_selected_ids as $k=>$v) {
			if (!$v) {
				unset($users_selected_ids[$k]);
			}
		}
	}

	/* if no selection create an empty array */
	if (!is_array($users_selected_ids)) {
		if ($_REQUEST["no_empty"]) {
			/* use the current user as id */
			$users_selected_ids = array($_SESSION["user_id"]);
		} else {
			$users_selected_ids = array();
		}
	}


	if ($_REQUEST["sub_action"] == "add") {
		/* if not multiple */
		if (!$_REQUEST["multiple"]) {
			$users_selected_ids = array();
		}
		$usr_available = $_REQUEST["users_available"];
		if (is_array($usr_available)) {
			foreach ($usr_available as $id) {
				$users_selected[$id] = $user_data->findUserInList($list, $id);
			}
		}
	}

	foreach ($users_selected_ids as $k=>$v) {
		$users_selected[$v] = $user_data->findUserInList($list, $v);
	}

	/* second output handler for parent */
	$output_alt = new Layout_output();

	$enabled_users  = $user_data->getUserList(1);
	$disabled_users = $user_data->getUserList(0);
	$groups         = $user_data->getGroupList(1);


foreach ($users_selected as $k=>$v) {
		if ($enabled_users[$k]) {
			$output_alt->addTag("li", array("class"=>"enabled"));
		} elseif ($disabled_users[$k]) {
			$output_alt->addTag("li", array("class"=>"disabled"));
		} elseif ($groups[$k]) {
			$output_alt->addTag("li", array("class"=>"group"));
		} else {
			$output_alt->addTag("li", array("class"=>"special"));
		}
		$id = $_REQUEST["field_id"];
		$noempty = $_REQUEST["noempty"];

		if ($_REQUEST["confirm"]) {
			$output_alt->addTag("a", array(
				"onclick" => "return confirm(gettext(\'Are you sure you want to remove this user / group?\'));",
				"href" => "javascript: remove_user(\'$k\', \'$id\', \'user_name_$id\', \'$noempty\');"
			));
		} else {
			$output_alt->addTag("a", array(
				"href" => "javascript: remove_user(\'$k\', \'$id\', \'user_name_$id\', \'$noempty\');"
			));
		}
		$output_alt->addCode("[X]");
		$output_alt->endTag("a");
		$output_alt->addSpace();
		$output_alt->addCode($v);
		$output_alt->endTag("li");
	}
	$output_alt->output = preg_replace("/((\n)|(\r)|(\t))/s","",$output_alt->output);
	$search = $_REQUEST["search"];
	$sub_action = $_REQUEST["sub_action"];

	if ($sub_action != "init") {

		$venster_settings = array(
			"title"    => gettext("users"),
			"subtitle" => gettext("choose")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addVensterData();
			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "data");
					$tbl->addCode( gettext("search for user").": " );
					$tbl->addTextField("search", $search, "", "", 1);
					$tbl->insertAction("ok", gettext("search"), "javascript: search_user();");
					if ($search) {
						$tbl->insertAction("toggle", gettext("show all"), "javascript: search_show_all();");
					}
				$tbl->endTableData();
				$tbl->addTableData( array("align"=>"right"), "data" );
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();

			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("available users").": " );
					$tbl->addTag("br");
					$tbl->addSelectField("users_available[]", ($search) ? $list_search:$list, "", $_REQUEST["multiple"], array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->insertAction("forward", gettext("add selection"), "javascript: selection_add();");
					$tbl->addTag("br");
					$tbl->addTag("br");
					$tbl->insertAction("back", gettext("delete selection"), "javascript: selection_remove();");
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->addCode( gettext("choosen users").": " );
					$tbl->addTag("br");

					$users_sorted = $users_selected;
					natcasesort($users_sorted);

					$tbl->addSelectField("users_selected[]", $users_selected, "", 1, array("size"=>20, "style"=>"width:300px"));
				$tbl->endTableData();
			$tbl->endTableRow();

			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
		$venster->endVensterData();


		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->load_javascript(self::include_dir."pick_user.js");
		$output->start_javascript();

		$keys = array();
		foreach ($users_selected as $k=>$v) {
			$keys[]=$k;
		}
		$output->addCode( sprintf("selection_update_parent('%s','%s','%s');",
			$_REQUEST["field_id"], implode(",",$keys), $output_alt->generate_output() )
		);
		$output->end_javascript();

	} else {

		$output->load_javascript(self::include_dir."pick_user.js");
		$output->start_javascript();
		$output->addCode( sprintf("selection_init('%s');", $_REQUEST["field_id"] ));
		$output->end_javascript();

	}

	$output->layout_page_end();
	$output->exit_buffer();
?>
