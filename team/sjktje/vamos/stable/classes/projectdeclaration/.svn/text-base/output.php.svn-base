<?
Class ProjectDeclaration_output {
	/* constants */
	const include_dir = "classes/projectdeclaration/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "projectdeclaration";


	public function addFieldsToList(&$view, &$projects) {
		$projects_declaration_data = new ProjectDeclaration_data();

		foreach ($projects as $k=>$v) {
			if (!$v["master"]) {
				$to_declare = $projects_declaration_data->addToDeclare($v["id"]);
				if ($to_declare)
					$projects[$k]["to_declare"] = number_format($to_declare,2);
				else
					$projects[$k]["to_declare"] = "--";
			}
		}
		$view->addMapping(gettext("to declare"), "%to_declare");
	}

	public function registerItem($project_id) {
		$output = new Layout_output();
		$output->layout_page();

		$declaration_data = new ProjectDeclaration_data();
		$calendar         = new Calendar_output();

		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y")-1; $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}

		$venster = new Layout_venster(array(
			"title" => gettext("register new item")
		));
		$venster->addVensterData();
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1,
				"width" => "600px"
			));
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("type of declaration"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addSelectField("declaration[declaration_type]", $declaration_data->declaration_types, "");
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData(gettext("date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("date[timestamp_day]", $days, date("d"));
					$table->addSelectField("date[timestamp_month]", $months, date("m"));
					$table->addSelectField("date[timestamp_year]", $years, date("Y"));
					$table->addCode( $calendar->show_calendar("document.getElementById('datetimestamp_day')", "document.getElementById('datetimestamp_month')", "document.getElementById('datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();

			/* if hour registration */
			$table->addTableRow(array("id" => "layer_hourtarif1"));
				$table->addTableData("", "header");
					$table->addCode(gettext("hour tariff"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addSelectField("declaration[hour_tarif]", $declaration_data->getTarifs(), "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow(array("id" => "layer_hourtarif2"));
				$table->addTableData("", "header");
					$table->addCode(gettext("time in minutes"));
				$table->endTableData();
				$table->addTableData("", "data");
					$timeunits = $declaration_data->getFieldContent("timeunits");
					$sel = array();
					for ($i=$timeunits;$i<=24*60;$i+=$timeunits) {
						if ($i >= 60) {
							$j1 = floor($i/60);
							$j2 = $i%60;
							if ($j2 == 0) {
								$sel[$i] = sprintf("%d %s", $j1, gettext("hour(s)"));
							} else {
								$sel[$i] = sprintf("%d %s, %d %s", $j1, gettext("hour(s)"), $j2, gettext("minutes"));
							}
						} else {
							$sel[$i] = sprintf("%d %s", $i, gettext("minutes"));
						}
					}
					$table->addSelectField("declaration[time_units]", $sel, "");
				$table->endTableData();
			$table->endTableRow();

			/* if kilometers */
			$table->addTableRow(array("id" => "layer_kilometers", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("number of kilometers"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("declaration[kilometers]", "");
				$table->endTableData();
			$table->endTableRow();

			/* if btw */
			$table->addTableRow(array("id" => "layer_btw"));
				$table->addTableData("", "header");
					$table->addCode(gettext("BTW in %"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = $declaration_data->getFieldContent("BTW", 1);
					$data = array();
					foreach ($sel as $k=>$v) {
						$data[$v] = $v;
					}
					$table->addSelectField("declaration[btw]", $data, "");
				$table->endTableData();
			$table->endTableRow();

			/* NCNP and verschotten */
			$table->addTableRow(array("id" => "layer_ncnp_verschotten", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("price in EUR"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addHiddenField("declaration[default_nora]", $declaration_data->getFieldContent("NORA"));
					$table->addTextField("declaration[price]", "");
				$table->endTableData();
			$table->endTableRow();
			/* NCNP and verschotten */
			$table->addTableRow(array("id" => "layer_ncnp", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("% NCNP"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("declaration[perc_NCNP]", "");
				$table->endTableData();
			$table->endTableRow();

			/* extra description */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("description"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextArea("declaration[description]", "", array("style" => "width: 250px; height: 100px;"));
				$table->endTableData();
			$table->endTableRow();

			/* user */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("user"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addHiddenField("declaration[user_id]", $_SESSION["user_id"]);
					$useroutput = new User_output();
					//$table->addCode( $useroutput->user_selection("declarationuser_id", $_SESSION["user_id"], 0, 0, 0, 1, 1) );
					$table->addCode( $useroutput->user_selection("declarationuser_id", $_SESSION["user_id"], array(
						"groups"   => 1,
						"inactive" => 1,
						"confirm"  => 1
					)));
				$table->endTableData();
			$table->endTableRow();

			/* actions */
			$table->addTableRow();
				$table->addTableData();
					$table->insertAction("back", gettext("back"), sprintf("?mod=project&action=showhours&id=%d&master=0", $project_id));
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
				$table->endTableData();
			$table->endTableRow();


			$table->endTable();
		$venster->addCode($table->generate_output());
		$venster->endVensterData();

		$output->addTag("form", array(
			"action" => "index.php",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "projectdeclaration");
		$output->addHiddenField("action", "save_registration");
		$output->addHiddenField("project_id", $project_id);

		$table2 = new Layout_table();
		$output->addCode($table2->createEmptyTable($venster->generate_output()));

		$output->endTag("form");
		$output->load_javascript(self::include_dir."editProject.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function sendBatch($project_id) {
		$output = new Layout_output();
		$output->layout_page();

		$declaration_data = new ProjectDeclaration_data();

		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y")-1; $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}

		/* some objects */
		$useroutput = new User_output();
		$calendar   = new Calendar_output();

		$venster = new Layout_venster(array(
			"title" => gettext("send declaration")
		));
		$venster->addVensterData();

			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			/* history (?) */
			if ($_REQUEST["history"]) {
				$table->addTableRow();
					$table->addTableData(array("colspan"=>2), "header");
						$table->addCode(gettext("Resend declaration")." - ".sprintf("%03d", $_REQUEST["history"]));
					$table->endTableData();
				$table->endTableRow();
			}
			/* target */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("declaration target"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = array(
						"client"      => gettext("client"),
						"constituent" => gettext("constituent"),
						"adversary"   => gettext("adversary"),
						"expertise"   => gettext("expertise")
					);
					$table->addSelectField("declaration[address]", $sel, "");
				$table->endTableData();
			$table->endTableRow();
			/* filter */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("declaration filter"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = array(
						#"text"   => gettext("only send reminder"),
						#"nora"   => gettext("only send NORA"),
						"calc"   => gettext("send calculation")
					);
					$table->addSelectField("declaration[filter]", $sel, "");
				$table->endTableData();
			$table->endTableRow();
			/* secretary */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("secretary"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addHiddenField("declaration[secretary]", $_SESSION["user_id"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("declarationsecretary", $_SESSION["user_id"], 0, 0, 0, 1, 1) );
				$table->endTableData();
			$table->endTableRow();
			/* manager */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("manager"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addHiddenField("declaration[manager]", $_REQUEST["manager"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("declarationmanager", $_REQUEST["manager"], 0, 0, 0, 1, 1) );
				$table->endTableData();
			$table->endTableRow();
			/* timestamp */
			$days = array();
			for ($i=1; $i<=31; $i++) {
				$days[$i] = $i;
			}
			$months = array();
			for ($i=1; $i<=12; $i++) {
				$months[$i] = $i;
			}
			$years = array();
			for ($i=date("Y")-1; $i<=date("Y")+5; $i++) {
				$years[$i] = $i;
			}

			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("date"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addSelectField("date[timestamp_day]", $days, date("d"));
					$table->addSelectField("date[timestamp_month]", $months, date("m"));
					$table->addSelectField("date[timestamp_year]", $years, date("Y"));
					$table->addCode( $calendar->show_calendar("document.getElementById('datetimestamp_day')", "document.getElementById('datetimestamp_month')", "document.getElementById('datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());

			$data = $declaration_data->getFolderDocuments();
			$view = new Layout_view();
			$view->addData($data);
			#$view->addMapping(gettext("name"), "%name");
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping("", "%%complex_action");

			$view->defineComplexMapping("complex_action", array(
				array(
					"type" => "action",
					"src"  => "forward",
					"alt"  => gettext("generate document"),
					"link" => array("javascript: generateDocument('", "%id", "');")
				)
			));

			$venster->addCode($view->generate_output());
		$venster->endVensterData();

		$output->addTag("form", array(
			"action" => "index.php",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "projectdeclaration");
		$output->addHiddenField("action", "generate_document");
		$output->addHiddenField("project_id", $project_id);
		$output->addHiddenField("file_id", 0);

		$table2 = new Layout_table();
		$output->addCode($table2->createEmptyTable($venster->generate_output()));

		$output->endTag("form");
		$output->load_javascript(self::include_dir."editProject.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editOne($req) {
		$output = new Layout_output();
		$output->layout_page();

		$declaration_data = new ProjectDeclaration_data();

		$declaration_data->checkOption($req["type"]);
		$data = $declaration_data->getOptionsByType($req["type"], 1);

		$venster = new Layout_venster(array(
			"title" => gettext("declaration options")
		));
		$venster->addVensterData();
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode($req["name"]);
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("value", $data[0]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->insertAction("back", gettext("back"), "?mod=projectdeclaration&action=start");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
		$venster->addCode($table->generate_output());
		$venster->endVensterData();


		$output->addTag("form", array(
			"action" => "index.php",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "projectdeclaration");
		$output->addHiddenField("action", "save_one");
		$output->addHiddenField("type", $req["type"]);

		$table2 = new Layout_table();
		$output->addCode($table2->createEmptyTable($venster->generate_output()));

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function editMulti($req) {
		$output = new Layout_output();
		$output->layout_page();

		$declaration_data = new ProjectDeclaration_data();

		$data = $declaration_data->getOptionsByType($req["type"], 0, 1);

		$venster = new Layout_venster(array(
			"title" => gettext("declaration options")
		));
		$venster->addVensterData();
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			/* prev entries */
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->addCode($req["name"]);
				$table->endTableData();
			$table->endTableRow();
			foreach ($data as $k=>$v) {
				$i++;
				$table->addTableRow();
					$table->addTableData("", "header");
						$table->addCode(gettext("current entries"));
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addCode($i.". ");
						$table->addTextField(sprintf("value[%d]", $k), $v);
						$table->insertAction("delete", gettext("delete value"), sprintf("javascript: document.getElementById('value%d').value = ''; void(0);", $k));
					$table->endTableData();
				$table->endTableRow();
			}
			/* new entry */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("new entry"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("value[-1]", $data[0]);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->addTableData("", "header");
					$table->insertAction("back", gettext("back"), "?mod=projectdeclaration&action=start");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
		$venster->addCode($table->generate_output());
		$venster->endVensterData();


		$output->addTag("form", array(
			"action" => "index.php",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "projectdeclaration");
		$output->addHiddenField("action", "save_multi");
		$output->addHiddenField("type", $req["type"]);

		$table2 = new Layout_table();
		$output->addCode($table2->createEmptyTable($venster->generate_output()));

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();

	}


	public function manageOptions() {
		$output = new Layout_output();
		$output->layout_page();

		$projectdeclaration = new ProjectDeclaration_data();

		$venster = new Layout_venster(array(
			"title" => gettext("declaration options")
		));
		$venster->addVensterData();
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			/* time units */
			$table->addTableRow();
				$table->insertTableData(gettext("manage time units"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("timeunits"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_one&type=timeunits&name=".gettext("time units in minutes"));
				$table->endTableData();
			$table->endTableRow();
			/* kilometers */
			$table->addTableRow();
				$table->insertTableData(gettext("kilometers tariff"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("kilometerstarif"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_one&type=kilometerstarif&name=".gettext("kilometers tariff in eurocents"));
				$table->endTableData();
			$table->endTableRow();
			/* perc office costs */
			$table->addTableRow();
				$table->insertTableData(gettext("% office costs"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("officecosts"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_one&type=officecosts&name=".gettext("% office costs"));
				$table->endTableData();
			$table->endTableRow();
			/* NORA tarif */
			$table->addTableRow();
				$table->insertTableData(gettext("NORA tariff"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("NORA"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_one&type=NORA&name=".gettext("NORA tariff"));
				$table->endTableData();
			$table->endTableRow();
			/* hour tarif */
			/*
			$table->addTableRow();
				$table->insertTableData(gettext("hour tarif"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("hourtarifs"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=hourtarifs&name=".gettext("hour tarif"));
				$table->endTableData();
			$table->endTableRow();
			*/
			/* accident types */
			$table->addTableRow();
				$table->insertTableData(gettext("accident types"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("accident_type"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=accident_type&name=".gettext("accident types"));
				$table->endTableData();
			$table->endTableRow();
			/* normal tarifs */
			/*
			$table->addTableRow();
				$table->insertTableData(gettext("normal tarifs"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("tarifs"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=tarifs&name=".gettext("normal tarifs"));
				$table->endTableData();
			$table->endTableRow();
			*/
			/* lesions */
			$table->addTableRow();
				$table->insertTableData(gettext("lesions"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("lesion"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=lesion&name=".gettext("lesions"));
				$table->endTableData();
			$table->endTableRow();
			/* btw */
			$table->addTableRow();
				$table->insertTableData(gettext("% BTW"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("BTW"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=BTW&name=".gettext("% BTW"));
				$table->endTableData();
			$table->endTableRow();
			/* back link */
			$table->addTableRow();
				$table->addTableData("", "");
					$table->insertAction("back", gettext("back"), "?mod=project");
				$table->endTableData();
			$table->endTableRow();

			$table->endTable();
		$venster->addCode($table->generate_output());
		$venster->endVensterData();

		$table2 = new Layout_table();
		$output->addCode($table2->createEmptyTable($venster->generate_output()));

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function genExtraFields($project_id) {
		$output = new Layout_output();

		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y")-1; $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}
		/* some modules */
		$declaration_data = new ProjectDeclaration_data();
		$useroutput       = new User_output();
		$calendar         = new Calendar_output();
		$address          = new Address_data();

		$declaration = $declaration_data->getDeclarationByProjectId($project_id);

		$table = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("view_all", "", "");
					$table->addSpace();
					$table->addCode(gettext("general information"));
				$table->endTableData();
			$table->endTableRow();
			/* task date */
			$table->addTableRow();
				$table->insertTableData(gettext("task date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("task_date[timestamp_day]", $days, date("d", $declaration["task_date"]));
					$table->addSelectField("task_date[timestamp_month]", $months, date("m", $declaration["task_date"]));
					$table->addSelectField("task_date[timestamp_year]", $years, date("Y", $declaration["task_date"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('task_datetimestamp_day')", "document.getElementById('task_datetimestamp_month')", "document.getElementById('task_datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			/* accident date */
			$table->addTableRow();
				$table->insertTableData(gettext("accident date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("damage_date[timestamp_day]", $days, date("d", $declaration["damage_date"]));
					$table->addSelectField("damage_date[timestamp_month]", $months, date("m", $declaration["damage_date"]));
					$table->addSelectField("damage_date[timestamp_year]", $years, date("Y", $declaration["damage_date"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('damage_datetimestamp_day')", "document.getElementById('damage_datetimestamp_month')", "document.getElementById('damage_datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			/* type of accident */
			$table->addTableRow();
				$table->insertTableData(gettext("type of accident"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[accident_type]", $declaration_data->getAccidents(), $declaration["accident_type"]);
				$table->endTableData();
			$table->endTableRow();
			/* wanted */
			$table->addTableRow();
				$table->insertTableData(gettext("% liabilities wished"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[perc_liabilities_wished]", $declaration["perc_liabilities_wished"]);
				$table->endTableData();
			$table->endTableRow();
			/* given */
			$table->addTableRow();
				$table->insertTableData(gettext("% liabilities recognised"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[perc_liabilities_recognised]", $declaration["perc_liabilities_recognised"]);
				$table->endTableData();
			$table->endTableRow();

			/* constituent */
			$basic = array(
				0 => sprintf("- %s -", gettext("choose"))
			);
			$ary = array("constituent", "client", "adversary", "expertise");
			foreach ($ary as $v) {
				if ($declaration[$v])
					$basic[$declaration[$v]] = $address->getAddressNameById($declaration[$v]);
			}
			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("state_special", "", "");
					$table->addSpace();
					$table->addCode( gettext("constituent") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("constituent"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[constituent]", $basic, $declaration["constituent"]);
				$table->endTableData();
			$table->endTableRow();
			/* identifier */
			$table->addTableRow();
				$table->insertTableData(gettext("constituent identifier"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[identifier]", $declaration["identifier"]);
				$table->endTableData();
			$table->endTableRow();
			/* tarif */
			$table->addTableRow();
				$table->insertTableData(gettext("default tariff"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[tarif]", $declaration_data->getTarifs(), $declaration["tarif"]);
				$table->endTableData();
			$table->endTableRow();

			/* client */
			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("addressbook", "", "");
					$table->addSpace();
					$table->addCode( gettext("other relations") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("client"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[client]", $basic, $declaration["client"]);
				$table->endTableData();
			$table->endTableRow();
			/* adversary */
			$table->addTableRow();
				$table->insertTableData(gettext("adversary"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[adversary]", $basic, $declaration["adversary"]);
				$table->endTableData();
			$table->endTableRow();
			/* expertise */
			$table->addTableRow();
				$table->insertTableData(gettext("expertise"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[expertise]", $basic, $declaration["expertise"]);
				$table->endTableData();
			$table->endTableRow();

			/* type of lesion */
			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("state_private", "", "");
					$table->addSpace();
					$table->addCode( gettext("lesion") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("lesion"), "", "header");
				$table->addTableData("", "data");
						$table->addSelectField("declaration[lesion]", $declaration_data->getLesions(), $declaration["lesion"]);
				$table->endTableData();
			$table->endTableRow();
			/* lesion description */
			$table->addTableRow();
				$table->insertTableData(gettext("description"), "", "header");
				$table->addTableData("", "data");
					$table->addTextArea("declaration[lesion_description]", $declaration["lesion_description"], array("style" => "width: 200px; height: 80px;"));
				$table->endTableData();
			$table->endTableRow();
			/* hospitalisation */
			$table->addTableRow();
				$table->insertTableData(gettext("hospitalisation"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[hospitalisation]", array(
						"0" => gettext("No"),
						"1" => gettext("Yes")
					), $declaration["hospitalisation"]);
				$table->endTableData();
			$table->endTableRow();

			/* employment */
			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("info", "", "");
					$table->addSpace();
					$table->addCode( gettext("incapacity for work") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("employment"), "", "header");
				$table->addTableData();
					$table->addTextArea("declaration[employment]", $declaration["employment"], array("style" => "width: 200px; height: 80px;"));
				$table->endTableData();
			$table->endTableRow();
			/* hospitalisation */
			$table->addTableRow();
				$table->insertTableData(gettext("profession"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[profession]", array(
						"0" => gettext("paid employment"),
						"1" => gettext("independent")
					), $declaration["profession"]);
				$table->endTableData();
			$table->endTableRow();
			/* hospitalisation */
			$table->addTableRow();
				$table->insertTableData(gettext("incapacity for work"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[incapacity_for_work]", array(
						"0" => gettext("No"),
						"1" => gettext("Yes")
					), $declaration["incapacity_for_work"]);
				$table->endTableData();
			$table->endTableRow();


		$table->endTable();
		$output->addCode( $table->generate_output() );
		$output->load_javascript(self::include_dir."editProject.js");
		return $output->generate_output();

	}
}
?>
