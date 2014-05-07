<?php
Class ProjectExt_output {
	/* constants */
	const include_dir = "classes/project/inc/";
	const include_dir_ext = "classes/projectext/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "project";

	public function manageExtended() {/*{{{*/
		$output = new Layout_output();
		$output->layout_page();
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("project instellingen"),
			"subtitle" => gettext("bedrijfsonderdelen")
		));
		$venster->addMenuItem(gettext("nieuw onderdeel"), "javascript: popup('index.php?mod=projectext&action=extDepartmentEdit', 'projects', 550, 400, 1);");
		$venster->addMenuItem(gettext("velden definities"), "?mod=projectext&action=defineMetaFields");
		$venster->addMenuItem(gettext("projectmodule"), "?mod=project");
		$venster->generateMenuItems();

		$project_data = new ProjectExt_data();
		$data = $project_data->extGetDepartments();

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($data);
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("bedrijfsonderdeel"), "%%complex_department");
			$view->addMapping(gettext("relatiekaart"), "%address_name");
			$view->addMapping("", "%%complex_actions");
			/* specify our complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("bedrijfsonderdeel wijzigen"),
					"link" => array("javascript: popup('index.php?mod=projectext&action=extDepartmentEdit&id=", "%id", "', 'projects', 550, 400, 1);")
				),
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("verwijderen"),
					"link"  => array("index.php?mod=projectext&action=extDepartmentsDelete&id=", "%id")
				)
			), "nowrap");
			$view->defineComplexMapping("complex_department", array(
				array(
					"type" => "action",
					"src"  => "state_special"
				),
				array(
					"type" => "link",
					"text" => array(" ", "%department"),
					"link" => array("index.php?mod=projectext&action=extShowActivities&department_id=", "%id")
				)
			), "nowrap");
			$venster->addCode($view->generate_output());


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}/*}}}*/
	public function extDepartmentEdit() {/*{{{*/
		$id = $_REQUEST["id"];
		$output = new Layout_output();
		$output->layout_page("projects", 1);
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("projecten"),
			"subtitle" => gettext("bedrijfsonderdelen beheren")
		));
		$project_data = new ProjectExt_data();

		if ($id) {
			$data = $project_data->extGetDepartments($id);
			$data = $data[$id];
		}

		$venster->addVensterData();

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("bedrijfsonderdeel") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("data[department]", $data["department"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("omschrijving") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextArea("data[description]", $data["description"], array("style"=>"width: 300px; height: 100px;"));
				$table->endTableData();
			$table->endTableRow();

			/* access */
			$table->addTableRow();
				$table->insertTableData(gettext("toegang"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("data[users]", $data["users"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("datausers", $data["users"], 1, 0, 1, 0, 1) );
					unset($useroutput);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("relatie") );
				$table->endTableData();
				$table->addTableData("", "data");

					$address = new Address_data();
					$address_info = $address->getAddressNameByID((int)$data["address_id"]);

					$table->addHiddenField("data[address_id]", $data["address_id"], "projectaddress_id");
					$table->insertTag("span", $address_info, array("id"=>"searchrel"));
					$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'search_address');", 700, 600, 1);

				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan"=>2), "header");

					$table->insertAction("close", gettext("sluiten"), "javascript: window.close();");
					$table->insertAction("save", gettext("opslaan"), "javascript: document.getElementById('velden').submit();");

				$table->endTableData();
			$table->endTable();

			$venster->addCode( $table->generate_output() );


		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "projectext");
		$output->addHiddenField("action", "extDepartmentSave");
		$output->addHiddenField("id", $_REQUEST["id"]);

		$output->addCode( $venster->generate_output() );

		$output->load_javascript(self::include_dir."edit_project.js");
		$output->endTag("form");

		$output->exit_buffer();
	}/*}}}*/
	public function defineMetaFields() {/*{{{*/
		$output = new Layout_output();
		$output->layout_page();
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("projecten"),
			"subtitle" => gettext("velden definitie")
		));
		$venster->addMenuItem(gettext("nieuw veld"), "javascript: popup('index.php?mod=projectext&action=extDefineFieldsEdit', 'project', 550, 300, 1);");
		$venster->addMenuItem(gettext("compleet overzicht"), "?mod=projectext&action=allmeta");
		$venster->addMenuItem(gettext("terug"), "?mod=projectext");
		$venster->generateMenuItems();

		$project_data = new ProjectExt_data();
		$data = $project_data->extGetMetaFields();

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($data);
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("volgorde"), "%field_order");
			$view->addMapping(gettext("veld naam"), "%field_name");
			$view->addMapping(gettext("veld type"), "%h_field_type");
			if (!$_REQUEST["activity_id"]) {
				$view->addMapping(gettext("toon in lijst"), "%show_list");
			}
			$view->addMapping(gettext("merge code"), "%%complex_merge");
			$view->addMapping("", "%%complex_actions");
			/* specify our complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"  => "action",
					"src"   => "edit",
					"alt"   => gettext("wijzigen"),
					"link"  => array("javascript: popup('index.php?mod=projectext&action=extDefineFieldsEdit&id=", "%id","', 'project', 550, 300, 1);")
				),
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("verwijderen"),
					"link"  => array("index.php?mod=projectext&action=extDefineFieldsDelete&id=", "%id")
				)
			), "nowrap");
			$view->defineComplexMapping("complex_merge", array(
				array(
					"text" => array("field_", "%id")
				)
			), "nowrap");


			$venster->addCode($view->generate_output());


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}/*}}}*/
	public function defineMetaFieldsEdit() {/*{{{*/
		$id = $_REQUEST["id"];
		$output = new Layout_output();
		$output->layout_page("projects", 1);

		$project_data = new ProjectExt_data();

		if ($_REQUEST["activity_id"]) {
			$activity = $project_data->extGetActivities(0, $_REQUEST["activity_id"]);
			$activity[$_REQUEST["activity_id"]]["activity"] = gettext("bedrijfsactiviteit")." ".$activity[$_REQUEST["activity_id"]]["activity"];
		}

		$venster = new Layout_venster(array(
			"title"    => gettext("projecten")." ".$activity[$_REQUEST["activity_id"]]["activity"],
			"subtitle" => gettext("veld toevoegen")
		));

		if ($id) {
			$data = $project_data->extGetMetaFields($id);
			$data = $data[$id];
		} else {
			$data["default_col"] = 1;
			$data["default_value"] = "waarde1\nwaarde2";
		}

		$venster->addVensterData();

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("veld volgorde") );
				$table->endTableData();
				$table->addTableData("", "data");
					for ($i=0;$i<=25;$i++) {
						$sel[$i] = $i;
					}
					$table->addSelectField("data[field_order]", $sel, $data["field_order"]);
				$table->endTableData();
			$table->endTableRow();
			/* name */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("veld naam") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("data[field_name]", $data["field_name"]);
				$table->endTableData();
			$table->endTableRow();
			/* default value */
			$show = 0;
			if ($_REQUEST["id"]) {
				if ($data["field_type"]==3 || $data["field_type"]==4 || $data["field_type"]==5) {
					$show = 1;
				}
			} else {
				$show = 1;
			}
			if ($show == 1) {
				$table->addTableRow(array("id"=>"selectcheck", "style" => "display: none;"));
					$table->addTableData("", "header");
						$table->addCode( gettext("checkbox/selectbox") );
						$table->addTag("br");
						$table->addCode( gettext("waarden") );
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addTextArea("data[default_value]", $data["default_value"], "", "defaultval");
						$table->addTag("br");
						$table->addCode(gettext("voer hier de waarden in voor de checkbox / selectbox ")."<br>".gettext("in gescheiden door een enter"));
					$table->endTableData();
				$table->endTableRow();

				$table->addTableRow(array("id"=>"fileupload", "style" => "display: none;"));
					$table->addTableData("", "header");
						$table->addCode( gettext("csv bestand") );
						$table->addTag("br");
						$table->addCode( gettext("uploaden") );
					$table->endTableData();
					$table->addTableData("", "data");
						$table2 = new Layout_table();
						$table2->addTableRow();
							$table2->addTableData(array("colspan"=>2));
								$table2->addUploadField("binFile[]");
							$table2->endTableData();
						$table2->endTableRow();
						$table2->addTableRow();
							$table2->addTableData(array("colspan"=>2));
								$table2->addCode(gettext("upload hier een csv bestand met de tabel voor dit veld"));
								$table2->addTag("br");
								$table2->addCode(gettext("De eerste rij bevat de kolomnamen."));
								$table2->addTag("br");
								$table2->addTag("br");
							$table2->endTableData();
						$table2->endTableRow();
						$table2->addTableRow();
							$table2->addTableData();
								$table2->addCode(gettext("kies het scheidingsteken").":" );
							$table2->endTableData();
							$table2->addTableData();
								$seps = array(
									"comma" => ", (".gettext("komma").")",
									"semicolon" => "; (".gettext("puntkomma").")"
								);
								$table2->addSelectField("data[seperator]", $seps);
							$table2->endTableData();
						$table2->endTableRow();
						$table2->addTableRow();
							$table2->addTableData();
								$table2->addCode(gettext("weer te geven kolom in de lijst").": ");
							$table2->endTableData();
							$table2->addTableData();
								$table2->addTextField("data[default_col]", $data["default_col"], array("style"=>"width: 30px"));
							$table2->endTableData();
						$table2->endTableRow();
						$table2->endTable();
						$table->addCode($table2->generate_output());

					$table->endTableData();
				$table->endTableRow();
			}
			/* type */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("veld type") );
				$table->endTableData();
				$table->addTableData("", "data");
					if ($_REQUEST["id"]) {
						$fields = $project_data->field_type;
						$table->addCode( $fields[$data["field_type"]] );
						$table->addHiddenField("data[field_type]", $data["field_type"]);
					} else {
						$table->addSelectField("data[field_type]", $project_data->field_type, $data["field_type"]);
					}
				$table->endTableData();
			$table->endTableRow();
			if ($_REQUEST["activity_id"]) {
				$table->addHiddenField("data[show_list]", 0);
			} else {
				$table->addTableRow();
					$table->addTableData("", "header");
						$table->addCode( gettext("toon in lijst") );
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addCheckBox("data[show_list]", 1, $data["show_list"]);
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->addTableData(array("colspan"=>2), "header");

					$table->insertAction("close", gettext("sluiten"), "javascript: window.close();");
					$table->insertAction("save", gettext("opslaan"), "javascript: document.getElementById('velden').submit();");

				$table->endTableData();
			$table->endTable();

			$venster->addCode( $table->generate_output() );


		$venster->endVensterData();

		$output->addTag("form", array(
			"id"      => "velden",
			"action"  => "index.php",
			"method"  => "post",
			"enctype" => "multipart/form-data"
		));
		$output->addHiddenField("mod", "projectext");
		$output->addHiddenField("department_id", $_REQUEST["department_id"]);
		$output->addHiddenField("activity_id", $_REQUEST["activity_id"]);
		$output->addHiddenField("action", "extDefineFieldsSave");
		$output->addHiddenField("id", $_REQUEST["id"]);

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->load_javascript(self::include_dir_ext."projectext.js");

		$output->exit_buffer();
	}/*}}}*/
	public function genDynamicProjectFields($project_id=0, $activity_id=0, $output_only=0) {/*{{{*/

		if ($_REQUEST["project_id"]) {
			$project_id = $_REQUEST["project_id"];
		}
		if ($_REQUEST["activity_id"]) {
			$activity_id = $_REQUEST["activity_id"];
		}
		$output_buffer = $_REQUEST["output_buffer"];

		$project_data = new ProjectExt_data();
		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1,
			"width"       => "100%"
		));

		if ($output_only) {
			$multi_activities = $project_data->extGetProjectActivities($project_id);
		} else {
			if ($activity_id) {
				$multi_activities = array($activity_id);
			} else {
				$multi_activities = array();
			}
		}

		foreach ($multi_activities as $activity_id) {

			$activity = $project_data->extGetActivities("", $activity_id);
			$activity_name = $activity[$activity_id]["activity"];
			$fields = $project_data->extGetMetaFields("", $project_id, $activity_id);

			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "header");
					$tbl->addCode( gettext("bedrijfsactiviteit")." ".$activity_name );
				$tbl->endTableData();
			$tbl->endTableRow();

			/* get department */
			$user_data = new User_data();
			$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);
			if ($user_perm["xs_projectmanage"]) {
				$xs = 1;
			} else {
				$ext_data = new ProjectExt_data();
				$dep = $ext_data->extGetActivityDepartment($v["activity"]);

				$arr = $user_data->getUserGroups($_SESSION["user_id"]);
				$users = explode(",", $dep["department"]["users"].",".$dep["activity"]["users"]);

				foreach ($arr as $v) {
					if (in_array("G".$v, $users)) {
						$xs = 1;
					} elseif (in_array($_SESSION["user_id"], $users)) {
						$xs = 1;
					}
				}
			}

			foreach ($fields as $k=>$v) {
				if ($xs) {
					$tbl->addTableRow();
						$tbl->addTableData("", "top nowrap");
							$tbl->insertTag("b", $v["field_name"].":" );
						$tbl->endTableData();
						$tbl->addTableData(array("width" => "95%"), "data");
							$this->switchFieldType($tbl, $output_only, $v);
						$tbl->endTableData();
					$tbl->endTableRow();
				} elseif (!$output_only) {
					$tbl->addTag("span", array("style" => "display: none;"));
						$this->switchFieldType($tbl, $output_only, $v);
					$tbl->endTag("span");
				}
			}
		}
		$tbl->endTable();

		$output = new Layout_output();
		$output->addCode($tbl->generate_output());
		if (!$output_buffer) {
			$output->load_javascript(self::include_dir_ext."projectext.js");
		}

		if ($output_buffer) {
			$buf = $output->generate_output();

			$delimeter = '/<script[^>]*>((?:[^<>"\']+(?:"[^"]*"|\'[^\']*\')*)+)<\/script>/i';

			preg_match_all($delimeter, $buf, $matches);
			foreach ($matches[0] as $m) {
				$buf = str_replace($m, "<!-- js filtered -->", $buf);
			}

			if (!$_REQUEST["exec_javascript"]) {
				echo $buf;
				exit();
			} else {
				foreach ($matches[1] as $m) {
					echo $m;
				}
				exit();
			}
		} else {
			return $output->generate_output();
		}
	}/*}}}*/


	private function switchFieldType(&$tbl, $output_only, &$v, $is_activity=0) {

		if ($is_activity) {
			$meta = "meta";
		} else {
			$meta = "dmeta";
		}

		switch ($v["field_type"]) {
			case 0:
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextField(sprintf($meta."[%s]", $v["field_name"]), $v["value"]);
				}
				break;
			case 1:
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextArea(sprintf($meta."[%s]", $v["field_name"]), $v["value"], array(
						"style" => "width: 300px; height: 80px;"
					));
				}
				break;
			case 2:
				$days[0] = "-";
				for ($i=1;$i<=31;$i++) {
					$days[$i] = $i;
				}
				$months[0] = "-";
				for ($i=1;$i<=12;$i++) {
					$months[$i] = $i;
				}
				$year[0] = "-";
				for ($i=1990;$i!=date("Y")+5;$i++) {
					$year[$i] = $i;
				}
				if ($output_only) {
					if ($v["value"] > 0) {
						$tbl->addCode(date("d-m-Y", $v["value"]));
					} else {
						$tbl->addCode("--");
					}
				} else {
					if ($v["value"] > 0) {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["field_name"]), $days, date("d", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["field_name"]), $months, date("m", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["field_name"]), $year, date("Y", $v["value"]));
					} else {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["field_name"]), $days, 0);
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["field_name"]), $months, 0);
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["field_name"]), $year, 0);
					}
				}
				break;
			case 3:
				$sel = array();
				$v["default_value"] = explode("\n", $v["default_value"]);
				foreach ($v["default_value"] as $z) {
					$z = trim($z);
					$sel[$z] = $z;
				}
				if ($output_only) {
					$tbl->addCode($sel[$v["value"]]);
				} else {
					$tbl->addSelectField(sprintf($meta."[%s]", $v["field_name"]), $sel, $v["value"]);
				}
				break;
			case 4:
				if ($output_only) {
					$tbl->addCode(nl2br($v["value"]));
				} else {

					$sel = array();
					$values = explode("\n", $v["value"]);
					$v["default_value"] = explode("\n", $v["default_value"]);
					foreach ($v["default_value"] as $z) {
						if (in_array(trim($z), $values)) {
							$checked = 1;
						} else {
							$checked = 0;
						}
						$tbl->addCheckBox(sprintf($meta."[%s][%s]", $v["field_name"], $z), $z, $checked);
						$tbl->addCode($z);
						$tbl->addTag("br");
					}
				}
				break;
			case 5:
				/*
				$sel = array();
				$conversion = new Layout_conversion();
				$data = $conversion->utf8_convert($v["large_data"]);
				$data = explode("#", $data);
				foreach ($data as $key=>$record) {
					$i++;
					$record = explode("|", $record);
					$key = $record[$v["default_value"]];
					if ($i==1) {
						$sel[0] = "--".$key."--";
					} else {
						$sel[$key] = $key;
					}
				}
				*/
				if ($output_only) {
					$tbl->addCode(nl2br($v["value"]));
					$tbl->addHiddenField("meta[".$v["field_name"]."]", $v["value"]);
					$tbl->insertAction("view_all", gettext("toon tabel"), "javascript: showProjectExtTable('meta".preg_replace("/(\[)|(\])|( )/s", "", $v["field_name"])."', '".$v["id"]."', 0);");
				} else {
					#$tbl->addSelectField(sprintf($meta."[%s]", $v["field_name"]), $sel, $v["value"]);
					$tbl->addCode($v["value"]);
					$tbl->addHiddenField("meta[".$v["field_name"]."]", $v["value"]);
					$tbl->insertAction("view_all", gettext("toon tabel"), "javascript: showProjectExtTable('meta".preg_replace("/(\[)|(\])|( )/s", "", $v["field_name"])."', '".$v["id"]."', 1);");
				}
				break;
			case 6:
				if ($output_only) {
					$userdata = new User_data();
					$tbl->addCode($userdata->getUsernameById($v["value"]));
				} else {
					$tbl->addHiddenField(sprintf($meta."[%s]", $v["field_name"]), $v["value"]);
					$useroutput = new User_output();
					$tbl->addCode( $useroutput->user_selection(sprintf($meta."%s", str_replace(" ", "", $v["field_name"])), $v["value"], 1, 0, 1, 0, 1) );
					unset($useroutput);
					$tbl->addTag("br");
				}
				break;
			case 7:
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextField(sprintf($meta."[%s]", $v["field_name"]), $v["value"], array(
						"onkeyup" => "this.value = parseInt(this.value.replace(/[^0-9\.\,]/, ''));"
					));
				}
				break;
		}

	}

	public function extShowMetaTable() {
		$project_data = new ProjectExt_data();
		$data = $project_data->getMetaTableData($_REQUEST["metafield"], $_REQUEST["metacurrent"], $_REQUEST["filter"]);

		$output = new Layout_output();
		$output->layout_page(gettext("projecten"), 1);

		$venster = new Layout_venster(array("title"=>"projecten"));
		$venster->addVensterData();

			$venster->addTag("form", array(
				"id"     => "velden",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "projectext");
			$venster->addHiddenField("action", "extShowMetaTable");
			$venster->addHiddenField("metaid", $_REQUEST["metaid"]);
			$venster->addHiddenField("metafield", $_REQUEST["metafield"]);
			$venster->addHiddenField("metacurrent", $_REQUEST["metacurrent"]);
			$venster->addHiddenField("allow_select", $_REQUEST["allow_select"]);
			$venster->addTextField("filter", $_REQUEST["filter"], "", "", 1);
			$venster->insertAction("forward", gettext("filter"), "javascript: document.getElementById('velden').submit();");
			$venster->endTag("form");
			$venster->addTag("br");
			$venster->addSpace(2);
			for ($i=0; $i!=26; $i++) {
				$venster->addSpace();
				$venster->insertLink(chr(65+$i), array(
					"href" => "javascript: setSearch('".chr(65+$i)."*');"
				) );
				$venster->addSpace(1);
			}


			$table = new Layout_table(array("border"=>1));
			foreach ($data as $k=>$v) {
				$i++;
				$table->addTableRow();
					if ($_REQUEST["allow_select"]) {
						if ($i==1) {
							$table->addTableData("", "header");
								$table->addSpace();
							$table->endTableData();
						} else {
							$table->addTableData("", "data");
								if ($k == $_REQUEST["metacurrent"]) {
									$table->insertAction("ok", gettext("kies deze waarde"), sprintf("javascript: opener.document.getElementById('%s').value = '%s'; window.close();", $_REQUEST["metaid"], $k));
								} else {
									$table->insertAction("view", gettext("kies deze waarde"), sprintf("javascript: opener.document.getElementById('%s').value = '%s'; window.close();", $_REQUEST["metaid"], $k));
								}
							$table->endTableData();
						}
					}
					foreach ($v as $fld) {
						if ($i==1) {
							$table->addTableData("", "header");
								$table->addCode($fld);
								$table->addSpace();
							$table->endTableData();
						} else {
							if ($k == $_REQUEST["metacurrent"]) {
								$class = "list_data_highlighted";
							}	else {
								$class = "list_data";
							}
							$table->addTableData(array("class"=>$class), "");
								$table->addCode($fld);
								$table->addSpace();
							$table->endTableData();
						}
					}
				$table->endTableData();
			}
			$table->endTable();
			$venster->addCode($table->generate_output());

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir_ext."projectext.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function genExtraProjectFields($project_id=0, $output_only=0) {/*{{{*/

		$project_data = new ProjectExt_data();
		$fields = $project_data->extGetMetaFields("", $project_id);

		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1,
			"width"       => "100%"
		));
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->addCode( gettext("huidige bedrijfsactiviteit") );
			$tbl->endTableData();
		$tbl->endTableRow();

		$departments = $project_data->extGetDepartments();
		$sel = array(
			"standaard" => array("0" => gettext("geen selectie"))
		);
		foreach ($departments as $d) {
			$activities = $project_data->extGetActivities($d["id"]);
			foreach ($activities as $a) {
				$sel[$d["department"]][$a["id"]] = $a["activity"];
			}
		}
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "data");
				if ($output_only) {
					$activity_id = $project_data->extGetProjectActivityType($project_id);
					$activity = $project_data->extGetActivities("", $activity_id);
					$activity_name = $activity[$activity_id]["activity"];
					if (!$activity_id) $activity_name = gettext("geen");

					$tbl->insertAction("activity", $activity_name, "");
					$tbl->addCode($activity_name);
				} else {
					$tbl->addSelectField("ext[activity]", $sel, $project_data->extGetProjectActivityType($project_id) );
				}
			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2));
				$tbl->addSpace();
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->addCode( gettext("extra project gegevens") );
			$tbl->endTableData();
		$tbl->endTableRow();
		foreach ($fields as $k=>$v) {
			$tbl->addTableRow();
				$tbl->addTableData("", "top nowrap");
					$tbl->insertTag("b", $v["field_name"].":" );
				$tbl->endTableData();
				$tbl->addTableData(array("width" => "95%"), "data");
					$this->switchFieldType($tbl, $output_only, $v, 1);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->endTable();

		$output = new Layout_output();
		$output->addCode( $tbl->generate_output() );
		$output->addTag("br");

		//debug
		#$activity_id = 2;

		$extra = $this->genDynamicProjectFields($project_id, $activity_id, $output_only);
		$output->insertTag("div", $extra, array(
			"id" => "project_extrafields"
		));
		return $output->generate_output();
	}/*}}}*/
	public function extShowActivities() {/*{{{*/

		$project_data = new ProjectExt_data();
		$department = $project_data->extGetDepartments($_REQUEST["department_id"]);

		$output = new Layout_output();
		$output->layout_page();
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("bedrijfsonderdeel")." ".$department[$_REQUEST["department_id"]]["department"],
			"subtitle" => gettext("bedrijfsactiviteiten")
		));
		$venster->addMenuItem(gettext("nieuwe activiteit"), "javascript: popup('index.php?mod=projectext&action=extActivityEdit&department_id=".$_REQUEST["department_id"]."', 'projects', 550, 400, 1);");
		$venster->addMenuItem(gettext("terug"), "?mod=projectext");
		$venster->generateMenuItems();

		$data = $project_data->extGetActivities($_REQUEST["department_id"]);

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($data);
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("bedrijfsactiviteit"), "%%complex_activity");
			$view->addMapping("", "%%complex_actions");
			/* specify our complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("bedrijfsactiviteut wijzigen"),
					"link" => array("javascript: popup('index.php?mod=projectext&action=extActivityEdit&id=", "%id", "', 'projects', 550, 400, 1);")
				),
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("verwijderen"),
					"link"  => array("index.php?mod=projectext&action=extActivityDelete&id=", "%id")
				)
			), "nowrap");
			$view->defineComplexMapping("complex_activity", array(
				array(
					"type" => "action",
					"src"  => "folder_open"
				),
				array(
					"type" => "link",
					"text" => array(" ", "%activity"),
					"link" => array("index.php?mod=projectext&action=extOpenActivity&department_id=".$_REQUEST["department_id"]."&activity_id=", "%id")
				)
			), "nowrap");
			$venster->addCode($view->generate_output());


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();

	}/*}}}*/
	public function extActivityEdit() {/*{{{*/
		$id = $_REQUEST["id"];
		$output = new Layout_output();
		$output->layout_page("projects", 1);
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("projecten"),
			"subtitle" => gettext("activiteiten beheren")
		));
		$project_data = new ProjectExt_data();

		if ($id) {
			$data = $project_data->extGetActivities("", $id);
			$data = $data[$id];
		}

		$venster->addVensterData();

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("bedrijfsactiviteit") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("data[activity]", $data["activity"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("omschrijving") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextArea("data[description]", $data["description"], array("style"=>"width: 300px; height: 100px;"));
				$table->endTableData();
			$table->endTableRow();

			/* access */
			$table->addTableRow();
				$table->insertTableData(gettext("toegang"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("data[users]", $data["users"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("datausers", $data["users"], 1, 0, 1, 0, 1) );
					unset($useroutput);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->addTableData(array("colspan"=>2), "header");

					$table->insertAction("close", gettext("sluiten"), "javascript: window.close();");
					$table->insertAction("save", gettext("opslaan"), "javascript: document.getElementById('velden').submit();");

				$table->endTableData();
			$table->endTable();

			$venster->addCode( $table->generate_output() );


		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "projectext");
		$output->addHiddenField("action", "extActivitySave");
		$output->addHiddenField("id", $_REQUEST["id"]);
		$output->addHiddenField("data[department_id]", $_REQUEST["department_id"]);

		$output->addCode( $venster->generate_output() );

		$output->load_javascript(self::include_dir."edit_project.js");
		$output->endTag("form");

		$output->exit_buffer();
	}/*}}}*/
	public function extOpenActivity() {/*{{{*/

		$project_data = new ProjectExt_data();
		$activity_id = $_REQUEST["activity_id"];

		$output = new Layout_output();
		$output->layout_page();

		$activity = $project_data->extGetActivities(0, $activity_id);

		/* create window widget for searchbox */
		$venster = new Layout_venster( array(
			"title"    => gettext("bedrijfsactiviteit")." ".$activity[$activity_id]["activity"],
			"subtitle" => gettext("velden beheren")
		));
		$dir = $activity[$activity_id]["department_name"]."/activiteiten/".$activity[$activity_id]["activity"];

		$venster->addMenuItem(gettext("nieuw veld"), "javascript: popup('index.php?mod=projectext&action=extDefineFieldsEdit&department_id=".$_REQUEST["department_id"]."&activity_id=".$activity_id."', 'projects', 550, 400, 1);");
		if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
			$venster->addMenuItem(gettext("open bestandsmap"), $project_data->openFileSys($dir), 1);
		}
		$venster->addMenuItem(gettext("terug"), "?mod=projectext&action=extShowActivities&department_id=".$_REQUEST["department_id"]);
		$venster->generateMenuItems();

		$data = $project_data->extGetMetaFields(0, 0, $activity_id);

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($data);
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("volgorde"), "%field_order");
			$view->addMapping(gettext("veld naam"), "%field_name");
			$view->addMapping(gettext("veld type"), "%h_field_type");
			$view->addMapping(gettext("merge code"), "%%complex_merge");
			$view->addMapping("", "%%complex_actions");
			/* specify our complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"  => "action",
					"src"   => "edit",
					"alt"   => gettext("wijzigen"),
					"link"  => array("javascript: popup('index.php?mod=projectext&action=extDefineFieldsEdit&department_id=".$_REQUEST["department_id"]."&activity_id=".$activity_id."&id=", "%id", "', 'project', 550, 300, 1);")
				),
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("verwijderen"),
					"link"  => array("index.php?mod=projectext&action=extMetaFieldsDelete&department_id=".$_REQUEST["department_id"]."&activity_id=".$activity_id."&id=", "%id")
				)
			), "nowrap");
			$view->defineComplexMapping("complex_merge", array(
				array(
					"text" => array("field_", "%id")
				)
			), "nowrap");

			$venster->addCode($view->generate_output());

		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();

	}/*}}}*/
	public function extGenerateDocumentTree() {/*{{{*/
		$id = $_REQUEST["id"];
		$address_id = $_REQUEST["address_id"];

		/* get address options */
		$project_data = new ProjectExt_data();
		$project_info = $project_data->getProjectById($id);
		$address_rcpt = $project_info[0]["address_id"];

		$address_data = new Address_data();

		if ($id) {
			$rcpt_address["name"] = $project_info[0]["relname"];
			$rcpt_address["id"]   = $project_info[0]["address_id"];
		} else {
			$rcpt_address["name"] = $address_data->getAddressNameByID($address_id, "relations");
			$rcpt_address["id"]   = $address_id;
		}

		/* get company departments options */
		$activity_id   = $project_data->extGetProjectActivityType($id);
		$activity_info = $project_data->extGetActivities("", $activity_id);
		$department_id = $activity_info[$activity_id]["department_id"];

		$department_info = $project_data->extGetDepartments($department_id);
		$sender_address["name"] = $department_info[$department_id]["address_name"];
		$sender_address["id"]   = $department_info[$department_id]["address_id"];

		$output = new Layout_output();
		$output->layout_page();

		$venster = new Layout_venster( array(
			"title"    => gettext("Projecten"),
			"subtitle" => gettext("Document genereren")
		));

		$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1);");
		$venster->generateMenuItems();
		$venster->addVensterData();

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			/* rcpt */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("adresgegevens ontvanger") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->insertTag("span", $rcpt_address["name"], array("id"=>"layer_mail_relation_rcpt"));
					$table->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');", 700, 600, 1);
				$table->endTableData();
			$table->addTableRow();
			/* sender */
			$table->endTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("eigen adresgegevens") );
				$table->endTableData();
				$table->addTableData("", "data");
					$table->insertTag("span", $sender_address["name"], array("id"=>"layer_mail_relation_sender"));
					$table->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');", 700, 600, 1);
				$table->endTableData();
			$table->endTableRow();
			/* tree */
			$table->endTableRow();
				$table->addTableData("", "header");
					$table->addCode( gettext("kies template") );
				$table->endTableData();
				$table->addTableData("", "data");

					$data = $project_data->extGetFileTemplates($id, $activity_id);
					$view = new Layout_view();
					$view->addData($data["project"]);
					/* define the fields and mappings for the view */
					$view->addMapping(gettext("Globale Project templates"), "%%complex_link");
					/* specify our complex mappings */
					$view->defineComplexMapping("complex_link", array(
						array(
							"type" => "link",
							"text" => "%short_name",
							"link" => array("javascript: mergeFile('", "%link_name", "');")
						)
					));
					$table->addCode( $view->generate_output() );
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode( $table->generate_output() );

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "get",
			"action" => "index.php"
		));
		$output->addHiddenField("sender_address", $sender_address["id"]);
		$output->addHiddenField("rcpt_address", $rcpt_address["id"]);
		$output->addHiddenField("file_name", "");
		$output->addHiddenField("dl", "1");
		$output->addHiddenField("file_type", "project");
		$output->addHiddenField("mod", "projectext");
		$output->addHiddenField("project_id", $id);
		$output->addHiddenField("action", "extMergeTemplate");

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->load_javascript(self::include_dir."edit_project.js");
		$output->exit_buffer();
	}/*}}}*/

	public function addMetaFieldsToList(&$view, &$projects) {/*{{{*/

		$meta_ids = array();
		$data = new projectExt_data();
		$user_data = new User_data();

		if (!is_array($projects))
			$projects = array();
		foreach ($projects as $k=>$v) {
			$w = $data->extGetMetaFields(0, $v["id"]);
			foreach ($w as $z) {
				if ($z["show_list"]) {
					if ($z["field_type"]==6) {
						$z["value"] = $user_data->getUsernameById((int)$z["value"]);
					} elseif ($z["field_type"]==2) {
						if ($z["value"] == 0) {
							$z["value"] = "--";
						} else {
							$z["value"] = date("d-m-Y", $z["value"]);
						}
					}
					$meta_ids[$z["field_name"]] = "meta_".$z["id"];
					$projects[$k]["meta_".$z["id"]] = $z["value"];
				}
			}
		}
		foreach ($meta_ids as $k=>$v) {
			$view->addMapping($k, "%".$v);
		}
	}/*}}}*/

	public function genOverviewSearch($formname="projectsearch") {
		$days[0] = "-";
		for ($i=1;$i<=31;$i++) {
			$days[$i] = $i;
		}
		$months[0] = "-";
		for ($i=1;$i<=12;$i++) {
			$months[$i] = $i;
		}
		$year[0] = "-";
		for ($i=date("Y")-1;$i!=date("Y")+5;$i++) {
			$year[$i] = $i;
		}
		$output = new Layout_output();

		$project_data = new ProjectExt_data();
		$data = $project_data->extGetMetaFields();

		$sel = array();
		foreach ($data as $k=>$v) {
			if ($v["field_type"] == 2) {
				$sel[$v["id"]] = $v["field_name"];
			}
		}
		if (count($sel)>0) {
			$output->addSpace(5);
			$output->addSelectField("projectext[meta_field]", $sel, $_REQUEST["projectext"]["meta_field"]);
			$output->addCode(": ");
			$output->addCode(gettext("van")." ");
			$output->addSelectField("projectext[start_day]", $days, $_REQUEST["projectext"]["start_day"]);
			$output->addSelectField("projectext[start_month]", $months, $_REQUEST["projectext"]["start_month"]);
			$output->addSelectField("projectext[start_year]", $year, $_REQUEST["projectext"]["start_year"]);

			$output->addSpace();
			$output->addCode(gettext("tot")." ");
			$output->addSelectField("projectext[end_day]", $days, $_REQUEST["projectext"]["end_day"]);
			$output->addSelectField("projectext[end_month]", $months, $_REQUEST["projectext"]["end_month"]);
			$output->addSelectField("projectext[end_year]", $year, $_REQUEST["projectext"]["end_year"]);
			$output->insertAction("forward", gettext("zoeken"), "javascript: document.getElementById('".$formname."').submit();");
		}

		return $output->generate_output();
	}
	public function allMetaOverview() {

		$projectext_data = new ProjectExt_data();
		$data = $projectext_data->getAllMetaFields();

		$output = new Layout_output();
		$output->layout_page("project", 1);

		/* create window widget for searchbox */
		$venster = new Layout_venster( array(
			"title"    => gettext("velden definities"),
			"subtitle" => gettext("compleet overzicht")
		));

		$venster->addMenuItem(gettext("print"), "javascript: window.print();");
		$venster->addMenuItem(gettext("terug"), "?mod=projectext&action=defineMetaFields");
		$venster->generateMenuItems();

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($data);
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("naam"), "%field_name");
			$view->addMapping(gettext("bedrijfsonderdeel"), "%department_name");
			$view->addMapping(gettext("bedrijfsactiviteit"), "%activity_name");
			$view->addMapping(gettext("merge code"), "%%complex_merge");
			/* specify our complex mappings */
			$view->defineComplexMapping("complex_merge", array(
				array(
					"text" => array("field_", "%id")
				)
			), "nowrap");

			$venster->addCode($view->generate_output());

		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();

	}
}
?>
