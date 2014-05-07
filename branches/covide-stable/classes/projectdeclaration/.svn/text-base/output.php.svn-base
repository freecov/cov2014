<?php
/**
 * Covide ProjectDeclaration module
 *
 * This module has been build based on demands of a specific customer.
 * That's why there are some Dutch text strings in here, and it's based on
 * the Dutch low etc. I dont know how global it is and/or who can use it besides
 * this one customer.
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class ProjectDeclaration_output {
	/* constants */
	const include_dir = "classes/projectdeclaration/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "projectdeclaration";

	/* methods */
	public function addFieldsToList(&$view, &$projects) {
		$projects_declaration_data = new ProjectDeclaration_data();
		if (!is_array($projects))
			$projects = array();
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

		if ($_REQUEST["id"]) {
			$decitem = $declaration_data->getRegistrationItemById($_REQUEST["id"]);
			//var_dump($decitem);
			$timestamp_day = date("d", $decitem["timestamp"]);
			$timestamp_month = date("m", $decitem["timestamp"]);
			$timestamp_year = date("Y", $decitem["timestamp"]);
		} else {
			$timestamp_day = date("d");
			$timestamp_month = date("m");
			$timestamp_year = date("Y");
		}
		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y")-5; $i<=date("Y")+5; $i++) {
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
					$table->addSelectField("declaration[declaration_type]", $declaration_data->declaration_types, $decitem["declaration_type"]);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData(gettext("date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("date[timestamp_day]", $days, $timestamp_day);
					$table->addSelectField("date[timestamp_month]", $months, $timestamp_month);
					$table->addSelectField("date[timestamp_year]", $years, $timestamp_year);
					$table->addCode( $calendar->show_calendar("document.getElementById('datetimestamp_day')", "document.getElementById('datetimestamp_month')", "document.getElementById('datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();

			/* if hour registration */
			$table->addTableRow(array("id" => "layer_hourtarif1"));
				$table->addTableData("", "header");
					$table->addCode(gettext("hour tariff"));
				$table->endTableData();
				$table->addTableData("", "data");
					$tarifs = $declaration_data->getTarifs();
					if ($decitem["hour_tarif"]) {
						foreach ($tarifs as $key=>$val) {
							if (strpos($val, $decitem["hour_tarif"]) !== false) {
								$tarif_selected = $key;
							}
						}
					} else {
						$tarif_selected = "";
					}
					$table->addSelectField("declaration[hour_tarif]", $tarifs, $tarif_selected);
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
					$table->addSelectField("declaration[time_units]", $sel, $decitem["time_units"]);
				$table->endTableData();
			$table->endTableRow();

			/* if kilometers */
			$table->addTableRow(array("id" => "layer_kilometers", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("number of kilometers"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addTextField("declaration[kilometers]", $decitem["kilometers"]);
				$table->endTableData();
			$table->endTableRow();

			/* if btw */
			$table->addTableRow(array("id" => "layer_btw"));
				$table->addTableData("", "header");
					$table->addCode(gettext("VAT in %"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = $declaration_data->getFieldContent("BTW", 1);
					$data = array();
					foreach ($sel as $k=>$v) {
						$data[$v] = $v;
					}
					if ($decitem["perc_btw"]) {
						$btw_selected = $decitem["perc_btw"];
					} else {
						$btw_selected = end($data);
					}
					$table->addSelectField("declaration[btw]", $data, $btw_selected);
				$table->endTableData();
			$table->endTableRow();

			/* NCNP and verschotten */
			$table->addTableRow(array("id" => "layer_ncnp_verschotten", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("price in EUR"));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addHiddenField("declaration[default_nora]", $declaration_data->getFieldContent("NORA"));
					$table->addTextField("declaration[price]", $decitem["price"]);
				$table->endTableData();
			$table->endTableRow();

			/* NCNP and verschotten */
			$table->addTableRow(array("id" => "layer_ncnp_perc", "style" => "display: none"));
				$table->addTableData("", "header");
					$table->addCode(gettext("% NCNP"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = $declaration_data->getFieldContent("NCNP_tarif", 1);
					$data = array();
					foreach ($sel as $k=>$v) {
						$data[$v] = $v;
					}
					$table->addSelectField("declaration[perc_NCNP]", $data, $decitem["perc_NCNP"]);

				$table->endTableData();
			$table->endTableRow();
			$table->insertTag("span", "", array("id" => "layer_ncnp", "style" => "display: none"));
			
			/* extra description */
			$table->addTableRow(array("id" => "layer_description"));
				$table->addTableData("", "header");
					$table->addCode(gettext("description"));
				$table->endTableData();
				$table->addTableData("", "data");
					$sel = $declaration_data->getFieldContent("description", 1);
					$data = array();
					foreach ($sel as $k=>$v) {
						$data[$v] = $v;
					}
					if ($decitem["description"]) {
						$desc_selected = $decitem["description"];
					} else {
						$desc_selected = end($data);
					}
					$table->addSelectField("declaration[description]", $data, $desc_selected);
				$table->endTableData();
			$table->endTableRow();
			
			/* user */
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("user"));
				$table->endTableData();
				$table->addTableData("", "data");
					if ($decitem["user_id"]) {
						$user_seleted = $decitem["user_id"];
					} else {
						$user_seleted = $_SESSION["user_id"];
					}
					$table->addHiddenField("declaration[user_id]", $user_seleted);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("declarationuser_id", $user_seleted, array(
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
		$output->addHiddenField("id", $_REQUEST["id"]);

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
						"constituent" => gettext("principal"),
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
					$projects_declaration_data = new Projectdeclaration_data();
					$data = $projects_declaration_data->getRegistrationItems($project_id);
					foreach ($data as $k=>$v) {
						if ($v["declaration_type_plain"] == 5) {
							$ncnp++;
						}
					}
					if ($ncnp) {
						$sel = array(
							"calc"   => gettext("create NCNP calculation")
						);
					} else {
						$sel = array(
							"nocalc"   => gettext("create test calculation"),
							"calc"   => gettext("create normal calculation"),
							"mincalc" => gettext("create credit calculation")
						);
					}

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
			for ($i=1980; $i<=date("Y")+5; $i++) {
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
			$view->addMapping(gettext("description"), array("%name", " - ", "%description"));
			$view->addMapping("", "%%complex_action");

			$view->defineComplexMapping("complex_action", array(
				array(
					"type" => "action",
					"src"  => "forward",
					"alt"  => gettext("generate document"),
					"link" => array("javascript: generateDocument('", "%id", "');")
				)
			));

			$venster->insertTag("div", $view->generate_output(), array(
				"id" => "documentlist"
			));

			$venster->insertAction("back", gettext("back"), sprintf("?mod=project&action=showhours&id=%d&master=0", $project_id));
		$venster->endVensterData();

		$output->addTag("form", array(
			"action" => "index.php",
			"id"     => "velden",
			"method" => "post"
		));
		$output->addHiddenField("mod", "projectdeclaration");
		$output->addHiddenField("action", "generate_document");
		$output->addHiddenField("dl", "1");
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
			/* accident types */
			$table->addTableRow();
				$table->insertTableData(gettext("accident types"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("accident_type"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=accident_type&name=".gettext("accident types"));
				$table->endTableData();
			$table->endTableRow();
			/* normal tarifs */

			$table->addTableRow();
				$table->insertTableData(gettext("NCNP tarifs"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("NCNP_tarif"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=NCNP_tarif&name=".gettext("NCNP tarifs"));
				$table->endTableData();
			$table->endTableRow();

			/* lesions */
			$table->addTableRow();
				$table->insertTableData(gettext("injuries"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("lesion"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=lesion&name=".gettext("lesions"));
				$table->endTableData();
			$table->endTableRow();
			/* btw */
			$table->addTableRow();
				$table->insertTableData(gettext("% VAT"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("BTW"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=BTW&name=".gettext("% BTW"));
				$table->endTableData();
			$table->endTableRow();
			/* description */
			$table->addTableRow();
				$table->insertTableData(gettext("description"), "", "header");
				$table->insertTableData($projectdeclaration->getFieldContent("description"), "data");
				$table->addTableData("", "data");
					$table->insertAction("edit", gettext("edit"), "?mod=projectdeclaration&action=edit_multi&type=description&name=".gettext("description"));
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
		for ($i=date("Y")-20; $i<=date("Y")+5; $i++) {
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
			if (!$project_id) {
				$declaration["task_date"]   = time();
				$declaration["damage_date"] = time();
			}
			$table->addTableRow();
				$table->insertTableData(gettext("date of assignment"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("task_date[timestamp_day]", $days, date("d", $declaration["task_date"]));
					$table->addSelectField("task_date[timestamp_month]", $months, date("m", $declaration["task_date"]));
					$table->addSelectField("task_date[timestamp_year]", $years, date("Y", $declaration["task_date"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('task_datetimestamp_day')", "document.getElementById('task_datetimestamp_month')", "document.getElementById('task_datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			/* accident date */
			$table->addTableRow();
				$table->insertTableData(gettext("date of accident"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("damage_date[timestamp_day]", $days, date("d", $declaration["damage_date"]));
					$table->addSelectField("damage_date[timestamp_month]", $months, date("m", $declaration["damage_date"]));
					$table->addSelectField("damage_date[timestamp_year]", $years, date("Y", $declaration["damage_date"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('damage_datetimestamp_day')", "document.getElementById('damage_datetimestamp_month')", "document.getElementById('damage_datetimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			/* type of accident */
			$table->addTableRow();
				$table->insertTableData(gettext("kind of accident"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[accident_type]", $declaration_data->getAccidents(), $declaration["accident_type"]);
				$table->endTableData();
			$table->endTableRow();
			/* wanted */
			$table->addTableRow();
				$table->insertTableData(gettext("estimated percentage of liability"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[perc_liabilities_wished]", $declaration["perc_liabilities_wished"]);
				$table->endTableData();
			$table->endTableRow();
			/* given */
			$table->addTableRow();
				$table->insertTableData(gettext("liability accepted"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[perc_liabilities_recognised]", $declaration["perc_liabilities_recognised"]);
				$table->endTableData();
			$table->endTableRow();
			/* tarif */
			$table->addTableRow();
				$table->insertTableData(gettext("rating guide"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[tarif]", $declaration_data->getTarifs(), $declaration["tarif"]);
				$table->endTableData();
			$table->endTableRow();
			/* agreements */
			$table->addTableRow();
				$table->insertTableData(gettext("agreements"), "", "header");
				$table->addTableData("", "data");
					$table->addTextArea("declaration[agreements]", $declaration["agreements"],  array("style" => "width: 200px; height: 40px;"));
				$table->endTableData();
			$table->endTableRow();

			/* constituent */
			$basic = array(
				0 => sprintf("- %s -", gettext("choose relation"))
			);
			$basic2 = array();
			$ary = array("constituent", "client", "adversary", "expertise");
			foreach ($ary as $v) {
				if ($declaration[$v]) {
					$basic[$declaration[$v]] = $address->getAddressNameById($declaration[$v]);

					$tmp = $address->getBcardsByRelationID($declaration[$v]);
					foreach ($tmp as $k=>$x) {
						$basic2[$v][0] = sprintf("- %s -", gettext("choose businesscard"));
						$basic2[$v][$x["id"]] = $x["fullname"];
					}
				} else {
					$basic2[$v][0] = sprintf("- %s -", gettext("choose businesscard"));
				}
			}

			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("state_special", "", "");
					$table->addSpace();
					$table->addCode( gettext("principal") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("principal"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[constituent]", $basic, $declaration["constituent"]);
				$table->endTableData();
			$table->endTableRow();
			/* bcard */
			$table->addTableRow();
				$table->insertTableData(gettext("principal businesscard"), "", "header");
				$table->addTableData(array("id" => "layer_constituent"), "data");
					$table->addSelectField("declaration[bcard_constituent]", $basic2["constituent"], $declaration["bcard_constituent"]);
				$table->endTableData();
			$table->endTableRow();
			/* identifier */
			$table->addTableRow();
				$table->insertTableData(gettext("reference number"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[identifier]", $declaration["identifier"]);
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
				$table->insertTableData(gettext("client relation"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[client]", $basic, $declaration["client"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("client businesscard"), "", "header");
				$table->addTableData(array("id" => "layer_client"), "data");
					$table->addSelectField("declaration[bcard_client]", $basic2["client"], $declaration["bcard_client"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(" ", array("colspan" => 2), "data");
			$table->endTableRow();

			/* adversary */
			$table->addTableRow();
				$table->insertTableData(gettext("third party insurer"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[adversary]", $basic, $declaration["adversary"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("insurer businesscard"), "", "header");
				$table->addTableData(array("id" => "layer_adversary"), "data");
					$table->addSelectField("declaration[bcard_adversary]", $basic2["adversary"], $declaration["bcard_adversary"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("insurer identifier"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[identifier_adversary]", $declaration["identifier_adversary"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(" ", array("colspan" => 2), "data");
			$table->endTableRow();

			/* expertise */
			$table->addTableRow();
				$table->insertTableData(gettext("expertise relation"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[expertise]", $basic, $declaration["expertise"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("expertise businesscard"), "", "header");
				$table->addTableData(array("id" => "layer_expertise"), "data");
					$table->addSelectField("declaration[bcard_expertise]", $basic2["expertise"], $declaration["bcard_expertise"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("expertise identifier"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("declaration[identifier_expertise]", $declaration["identifier_expertise"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(" ", array("colspan" => 2), "data");
			$table->endTableRow();

			/* type of lesion */
			$table->addTableRow();
				$table->insertTableData("&nbsp;");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "header");
					$table->insertAction("state_private", "", "");
					$table->addSpace();
					$table->addCode( gettext("injuries") );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("injuries"), "", "header");
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
						"1" => gettext("Yes"),
						"2" => gettext("unknown")
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
					$table->addCode( gettext("incapacity to work") );
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
						"1" => gettext("independent"),
						"2" => gettext("unknown"),
						"3" => gettext("student")
					), $declaration["profession"]);
				$table->endTableData();
			$table->endTableRow();
			/* hospitalisation */
			$table->addTableRow();
				$table->insertTableData(gettext("incapacity to work"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("declaration[incapacity_for_work]", array(
						"0" => gettext("No"),
						"1" => gettext("Yes"),
						"2" => gettext("unknown")
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
