<?
	if (!class_exists("Filesys_output")) {
		exit("no class definition found");
	}

	$fsdata = new Filesys_data();

	$output = new Layout_output();
	$output->layout_page("filesys", 1);

	$folder = $_REQUEST["folder"];


	$q = sprintf("select count(*) from filesys_permissions where folder_id = %d", $folder);
	$res = sql_query($q);
	if (sql_result($res,0)==1) {
		$useparent = 0;
	} else {
		$useparent = 1;
	}

	$users = explode(",", $_REQUEST["users"]);
	$read  = $_REQUEST["read"];
	$write = $_REQUEST["write"];

	#print_r($_REQUEST);

	/* if there is a subaction */
	switch ($_REQUEST["subaction"]){

		case "update_useparent":
			$useparent = $_REQUEST["useparent"];

			$q = "delete from filesys_permissions where folder_id = $folder";
			sql_query($q);

			if ($useparent==0){
				$r = $fsdata->retrieveFullPermissions($folder);
				$q = "insert into filesys_permissions (folder_id, user_id, permissions) values ($folder, '".$r[0]."', '".$r[1]."')";
				sql_query($q);
			}
			break;

		case "add_read":
			$r = $fsdata->retrieveFullPermissions($folder);
			foreach ($users as $uid) {
				$r = $fsdata->modifyPermissionArray($r, $uid, "R");
			}
			$fsdata->updatePermissionsDb($folder, $r);
			$prefetched=1;
			break;
		case "del_complete":
			$r = $fsdata->retrieveFullPermissions($folder);
			foreach ($users as $uid) {
				$r = $fsdata->modifyPermissionArray($r, $uid, "D");
			}
			$fsdata->updatePermissionsDb($folder, $r);
			$prefetched=1;
			break;
		case "add_write":
			$r = $fsdata->retrieveFullPermissions($folder);
			foreach ($users as $uid) {
				$r = $fsdata->modifyPermissionArray($r, $uid, "W");
			}
			$fsdata->updatePermissionsDb($folder, $r);
			$prefetched=1;
			break;

	}

	if (!$prefetched){
		$r = $fsdata->retrieveFullPermissions($folder);
	}

	$r_user        = explode("|",$r[0]);
	$r_permissions = explode("|",$r[1]);

	/* create arrays */
	$read  = array();
	$write = array();

	$userdata   = new User_data();
	$enabled_users  = $userdata->getUserList(1);
	$disabled_users = $userdata->getUserList(0);
	$groups         = $userdata->getGroupList(1);

	foreach ($r_user as $k=>$v){
		if ($r_permissions[$k]=="R"){
			if ($enabled_users[$v]) {
				$read[$v] = "enabled";
			} elseif ($disabled_users[$v]) {
				$read[$v] = "disabled";
			} elseif ($groups[$v]) {
				$read[$v] = "group";
			} else {
				$read[$v] = "special";
			}
		}elseif ($r_permissions[$k]=="W"){
			if ($enabled_users[$v]) {
				$write[$v] = "enabled";
			} elseif ($disabled_users[$v]) {
				$write[$v] = "disabled";
			} elseif ($groups[$v]) {
				$write[$v] = "group";
			} else {
				$write[$v] = "special";
			}
		}
	}
	/* end subaction */


	$venster = new Layout_venster(array(
		"title" => gettext("bestandsbeheer"),
		"subtitle" => gettext("aangepaste rechten uitdelen")
	));
	$venster->addVensterData();

		$table = new Layout_table( array(
			"width"   => "100%",
			"cellspacing" => 1

		));
		$table->addTableRow();
			$table->addTableData( array("colspan"=>2), "data");
				if ($useparent) {
					$table->insertAction("folder_up", "", "");
					$table->addSpace();
					$table->addCode( gettext("deze map gebruikt de rechten van de bovenliggende map") );
					$table->addTag("br");
					$table->addTag("br");
					$table->insertAction("folder_lock", gettext("mapspecifieke rechten inschakelen"), "?mod=filesys&action=set_permissions&folder=$folder&subaction=update_useparent&useparent=0");
					$table->addSpace();
					$table->addCode( gettext("pagina specifieke rechten inschakelen") );
				} else {
					$table->insertAction("folder_denied", gettext("mapspecifieke rechten uitschakelen"), "?mod=filesys&action=set_permissions&folder=$folder&subaction=update_useparent&useparent=1");
					$table->addSpace();
					$table->addCode( gettext("pagina specifieke rechten uitschakelen") );
					$table->addSpace();
				}
				$table->addTableData("","datatop");
					$table->addTag("div", array(
						"align" => "right"
					));
					$table->insertAction("close", gettext("sluiten"), "javascript: opener.document.getElementById('velden').submit(); window.close();");
					$table->endTag("div");

				$table->endTableData();

			$table->endTableData();
		$table->endTableRow();


		$table->addTableRow();
			$table->insertTableData(gettext("kies gebruikers"), "", "header");
			$table->insertTableData(gettext("lees rechten"), "", "header");
			$table->insertTableData(gettext("schrijf rechten"), "", "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "datatop");

			$table->addHiddenField("users", "");
			$table->addTag("div", array("style"=>"text-align: left"));

				if (!$useparent) {
					$useroutput = new User_output();

					$table->addCode( $useroutput->user_selection("users", "", 1, 0, 1, 0, 1) );
					$table->insertAction("delete",  gettext("ontneem de geselecteerde gebruikers alle rechten"), "javascript: permissions_del_complete()");
					$table->insertAction("permissions_add_read",  gettext("geef de geselecteerde gebruikers lees rechten"), "javascript: permissions_add_read()");
					$table->insertAction("permissions_add_write", gettext("geef de geselecteerde gebruikers schrijf rechten"), "javascript: permissions_add_write()");
				} else {
					$table->addCode( gettext("Zet eerst paginaspecifieke rechten aan voordat deze optie beschikbaar wordt") );
				}
			$table->endTag("div");


			$table->endTableData();
			$table->addTableData("", "datatop");
			foreach ($read as $k=>$v) {
				$table->addTag("li", array("class"=>$v));
				if (!$useparent) {
					$table->addTag("a", array(
						"href" => "?mod=filesys&action=set_permissions&folder=$folder&subaction=del_complete&users=$k"
					));
				}
				$table->addCode( $userdata->getUsernameById($k) );
				if (!$useparent) {
					$table->endTag("a");
				}
				$table->endTag("li");
			}

			$table->endTableData();
			$table->addTableData("", "datatop");
			foreach ($write as $k=>$v) {
				$table->addTag("li", array("class"=>$v));
				if (!$useparent) {
					$table->addTag("a", array(
						"href" => "?mod=filesys&action=set_permissions&folder=$folder&subaction=del_complete&users=$k"
					));
				}
				$table->addCode( $userdata->getUsernameById($k) );
				if (!$useparent) {
					$table->endTag("a");
				}
				$table->endTag("li");
			}

			$table->endTableData();

		$table->endTableRow();
		$table->endTable();

		$venster->addCode( $table->generate_output() );
	$venster->endVensterData();


	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "post"
	));


	$output->addHiddenField("mod", "filesys");
	$output->addHiddenField("action", "set_permissions");
	$output->addHiddenField("subaction", "");
	$output->addHiddenField("folder", $_REQUEST["folder"]);

	$output->addCode( $venster->generate_output() );
	$output->endTag("form");
	$output->load_javascript(self::include_dir."file_operations.js");

	$output->exit_buffer();
?>