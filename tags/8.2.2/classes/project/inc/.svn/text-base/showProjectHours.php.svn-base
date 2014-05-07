<?php
		if (!class_exists("Project")) {
			exit("no class definition found");
		}
		$project_data = new Project_data();
		$project_id = $_REQUEST["id"];
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
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("date"), "%date");
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping(gettext("activity"), "%activity_name");
			$view->addMapping(gettext("hours"), "%project_hours");
			$view->addData($proj_info);
			$view->setHTMLField("description");

			//javascript: popup('index.php?mod=project&action=showprojecthours&id=233', 'hour_overview', 750, 600, 1);
			$venster->addCode($view->generate_output());
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
						setTimeout('window.close();', 500);
						"
					);
				$output->end_javascript();
			}
		}


		$output->layout_page_end();
		$output->exit_buffer();
?>
