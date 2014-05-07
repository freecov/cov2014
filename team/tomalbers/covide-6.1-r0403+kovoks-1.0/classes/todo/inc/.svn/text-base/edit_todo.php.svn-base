<?php
		if (!class_exists("Todo_output")) {
			die("no class definition found");
		}
		if ($_REQUEST["noteid"] || $noteid) {
			if ($noteid) { $_REQUEST["noteid"] = $noteid; }
			$note_data = new Note_data();
			$noteinfo  = $note_data->getNoteById($_REQUEST["noteid"]);
			$noteinfo["timestamp_end"] = $noteinfo["timestamp"];
			$noteinfo["is_customercontact"] = $noteinfo["is_support"];
			$noteinfo["id"] = 0;
		}
		if ($_REQUEST["todoid"]) {
			$note_data = new Todo_data();
			$noteinfo = $note_data->getTodoById($_REQUEST["todoid"]);
		}
		$address_data = new Address_data();
		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y"); $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}
		$output = new Layout_output();
		$output->layout_page("", $noiface);
		/* put a form around the page */
		$output->addTag("form", array(
			"id"     => "todoedit",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "todo");
		$output->addHiddenField("action", "save_todo");
		$output->addHiddenField("todo[id]", $noteinfo["id"]);
		$output->addHiddenField("todo[noteid]", $_REQUEST["noteid"]);
		$output->addHiddenField("todo[project_id]", $noteinfo["project_id"]);
		$output->addHiddenField("todo[is_customercontact]", $noteinfo["is_customercontact"]);
		$output->addHiddenField("todo[noiface]", $noiface);
		/* window widget */
		$venster = new Layout_venster(array(
			"title"    => gettext("notitie"),
			"subtitle" => gettext("agenderen")
		));
		if ($noiface) {
			$venster->addMenuItem(gettext("terug"), "javascript: window.close();");
		} else {
			$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1);");
		}

		$calendar = new Calendar_output();

		$venster->generateMenuItems();
		$venster->addVensterData();
			$table = new Layout_table();
			$table->addTableRow();
				/* dag */
				$table->insertTableData(gettext("dag"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("todo[timestamp_day]", $days, date("d", $noteinfo["timestamp"]));
					$table->addSelectField("todo[timestamp_month]", $months, date("m", $noteinfo["timestamp"]));
					$table->addSelectField("todo[timestamp_year]", $years, date("Y", $noteinfo["timestamp"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('todotimestamp_day')", "document.getElementById('todotimestamp_month')", "document.getElementById('todotimestamp_year')" ));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				/* einddatum */
				$table->insertTableData(gettext("eind datum"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("todo[timestamp_end_day]", $days, date("d", $noteinfo["timestamp_end"]));
					$table->addSelectField("todo[timestamp_end_month]", $months, date("m", $noteinfo["timestamp_end"]));
					$table->addSelectField("todo[timestamp_end_year]", $years, date("Y", $noteinfo["timestamp_end"]));
					$table->addCode( $calendar->show_calendar("document.getElementById('todotimestamp_end_day')", "document.getElementById('todotimestamp_end_month')", "document.getElementById('todotimestamp_end_year')" ));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("controle"), "", "header");
				$table->addTableData("", "data");
					$table->addTag("div", array("id"=>"todo_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
					$table->endTag("div");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				/* inhoud */
				$table->insertTableData(gettext("onderwerp"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("todo[subject]", $noteinfo["subject"], array("style"=>"width: 300px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				/* alert */
				$table->insertTableData(gettext("omschrijving"), "", "header");
				$table->addTableData("", "data");
					$table->addTextArea("todo[body]", $noteinfo["body"], array("style"=>"width: 300px; height: 200px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("relatie"), "", "header");
				$table->addTableData("", "data");
				$table->addHiddenField("todo[address_id]", $noteinfo["address_id"]);
					$table->insertTag("span", $address_data->getAddressNameById($noteinfo["address_id"]), array(
						"id" => "searchrel"
					));
					$table->addSpace(1);
					$table->insertAction("edit", gettext("wijzigen"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("alert"), "", "header");
				$table->addTableData("", "data");
					$table->addCheckBox("todo[is_alert]", 1, $noteinfo["is_alert"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					if ($_REQUEST["todoid"] && $noteinfo["id"]) {
						$table->insertAction("delete", gettext("verwijderen"), "javascript: todo_delete(".$noteinfo["id"].");");
					}
					$table->addTag("span", array("id"=>"action_save", "style"=>"visibility: hidden;"));
						$table->insertAction("save", gettext("opslaan"), "javascript: todo_save();");
					$table->endTag("span");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		/* close the form */
		$output->endTag("form");
		$output->load_javascript(self::include_dir."todo_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();


?>
