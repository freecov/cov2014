<?php
		if (!class_exists("Project")) {
			exit("no class definition found");
		}
		//init user object for access checks
		$user_data = new User_data();
		$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);
		$project_data = new Project_data();
		$project_id = sprintf("%d", $_REQUEST["id"]);
		$projectinfo = $project_data->getProjectById($project_id);
		$hourslist = $project_data->getHoursList(array("projectid"=> $project_id, "lfact"=>$projectinfo[0]["lfact"]));

		if ($_REQUEST["user_id"]) {
			$proj_info = $project_data->getProjectHoursByProjectId($project_id, $_REQUEST["user_id"]);
		} else {
			$proj_info = $project_data->getProjectHoursByProjectId($project_id);
		}

		$output = new Layout_output();
		$output->layout_page(gettext("hour overview"),1);

		/* create nice window widget */
		$venster = new Layout_venster(array(
			"title" => gettext("projects"),
			"subtitle" => $proj_info[0]["project_name"]
		));

		$venster->addVensterData();
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("Additional information"), "", "header");
			$table->endTableRow();
			/* if extended project module */
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$table->addTableRow();
					$table->addTableData("", "data");
						$project_ext = new ProjectExt_output();
						$table->addCode( $project_ext->genExtraProjectFields($project_id, 1) );
						unset($project_ext);
					$table->endTableData();
				$table->endTableRow();
			}
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("date"), "%human_start_date");
			$view->addMapping(gettext("time"), "%hours_bill");
			$view->addMapping(gettext("service hours"), "%hours_service");
			$view->addMapping(gettext("user"), "%user_name");
			$view->addMapping(gettext("activity"), "%activityname");
			$view->addMapping(gettext("description"), "%description", array("allow_html" => 1));
			if ($userinfo["xs_projectmanage"]) {
				$view->addMapping(gettext("price"), "%costs");
			}
			$view->addData($hourslist["items"]);
			$table->addTableRow();
				$table->insertTableData(gettext("registered hours"), "", "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "data");
					$table->addCode($view->generate_output());
					unset($view);
				$table->endTableData();
			$table->endTableRow();

			// bulk added items
			$bulklist = $project_data->getHoursList(array("projectid" => $project_id, "bulk" => 1));
			$bulkviewdata = $bulklist["items"];
			$view = new Layout_view();
			$view->addData($bulkviewdata);
			$view->addMapping(gettext("time"), "%hours_bill");
			$view->addMapping(gettext("service hours"), "%hours_service");
			$view->addMapping(gettext("user"), "%user_name");
			$view->addMapping(gettext("activity"), "%activityname");
			$view->addMapping(gettext("description"), "%description", array("allow_html" => 1));
			if ($userinfo["xs_projectmanage"]) {
				$view->addMapping(gettext("price"), "%costs");
			}
			$table->addTableRow();
				$table->insertTableData(gettext("bulk added hours"), "", "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "data");
					$table->addCode($view->generate_output());
					unset($view);
				$table->endTableData();
			$table->endTableRow();

			// non hour items
			$misclist = $project_data->getHoursList(array("projectid" => $project_id, "misc" => 1));
			$miscviewdata = $misclist["items"];
			$view = new Layout_view();
			$view->addData($miscviewdata);
			$view->addMapping(gettext("user"), "%user_name");
			$view->addMapping(gettext("description"), "%description", array("allow_html" => 1));
			if ($userinfo["xs_projectmanage"]) {
				$view->addMapping(gettext("price"), "%price");
			}
			$table->addTableRow();
				$table->insertTableData(gettext("other project costs"), "", "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "data");
					$table->addCode($view->generate_output());
					unset($view);
				$table->endTableData();
			$table->endTableRow();

			$table->endTable();
			$venster->addCode($table->generate_output());

			if (!$_REQUEST["print"]) {
				$venster->insertAction("print", gettext("print"), "index.php?mod=project&action=showprojecthours&print=1&id=".$project_id);
				$venster->addSpace(2);
				//$venster->insertAction("ftype_pdf", gettext("export to pdf"), "index.php?mod=project&action=showprojecthours&print=1&pdf=1&id=".$project_id);
			}
			/* end of view */
		$venster->endVensterData();
		/* end of window widget */
		$output->addCode($venster->generate_output());
		if ($_REQUEST["print"]) {
			if ($_REQUEST["pdf"]) {

			} else {
				$output->start_javascript();
					$output->addCode(
						"
						window.print();
						"
					);
				$output->end_javascript();
			}
		}


		$output->layout_page_end();
		$output->exit_buffer();
?>
