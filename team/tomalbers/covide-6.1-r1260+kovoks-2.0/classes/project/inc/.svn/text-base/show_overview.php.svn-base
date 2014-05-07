<?php
		if (!class_exists("Project")) {
			exit("no class definition found");
		}
		/* show overview, included from output.php */
		$userdata = new User_data();
		$userperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
		$project_data = new Project_data();
		if ($_REQUEST["searchkey"]) {
			$options = array("searchkey" => $_REQUEST["searchkey"]);
		}
		$projects = $project_data->getProjectsBySearch($options);
		$output = new Layout_output();
		$output->layout_page();
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => gettext("projecten"),
			"subtitle" => gettext("zoeken")
		));
		$venster->addVensterData();

			$tbl = new Layout_table();
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addTextField("searchkey", $_REQUEST["searchkey"], "", "", 1);
						$tbl->insertAction("forward", gettext("zoeken"), "javascript: document.getElementById('projectsearch').submit();");
						$tbl->addTag("br");
						$tbl->insertLink(gettext("toon alle projecten"), array(
							"href" => "javascript: search_showall();"
						));
					$tbl->endTableData();
					if ($GLOBALS["covide"]->license["has_project_ext"]) {
						$projectext = new ProjectExt_output();
						$tbl->addTableData();
							$tbl->addCode($projectext->genOverViewSearch());
						$tbl->endTableData();
					}
				$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );
		$venster->endVensterData();
		/* form for various actions */
		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "get",
			"action" => "index.php"
		));
		foreach ($_REQUEST as $k=>$v) {
			$output->addHiddenField($k, $v);
		}
		$output->endTag("form");

		$output->addTag("form", array(
			"id"     => "projectsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "project");
		$output->addHiddenField("action", "projectsearch");
		$output->addCode($venster->generate_output());
		$output->endTag("form");
		unset($venster);
		/* create nice window widget */
		$venster = new Layout_venster(array(
			"title" => gettext("projecten"),
			"subtitle" => gettext("overzicht")
		));
		$venster->addMenuItem(gettext("nieuw project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=0');");
		if ($userperms["xs_projectmanage"]) {
			$venster->addMenuItem(gettext("nieuw hoofdproject"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=1');");
			$venster->addMenuItem(gettext("manage activiteiten"), "index.php?mod=project&action=show_activities");
			$venster->addMenuItem(gettext("uren overzicht"), "index.php?mod=project&action=hour_overview");

			/* if extended project management */
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$venster->addMenuItem(gettext("project instellingen"), "?mod=projectext&action=extend");
			}
			//$venster->addMenuItem(gettext("planning"), "poe");
		}
		$venster->generateMenuItems();
		/* menu items */
		$venster->addVensterData();
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping("", "%%complex_actions");
			$view->addMapping(gettext("project"), array(" ", "%name"));
			$view->addMapping(gettext("omschrijving"), "%description");

			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$projectext_data = new ProjectExt_data();
				$projectext_data->applyMetaSearch($projects);
				$projectext_output = new ProjectExt_output();
				$projectext_output->addMetaFieldsToList($view, $projects);
			}
			$view->addData($projects);

			/* specify our complex mappings */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "info",
					"alt"  => gettext("info"),
					"link" => array("index.php?mod=project&action=showinfo&id=", "%id", "&master=", "%master")
				),
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("project wijzigen"),
					"link" => array("javascript: popup('index.php?mod=project&action=edit&id=", "%id", "&master=", "%master", "');"),
					"check" => "%allow_edit"
				),
				array(
					"type"  => "action",
					"src"   => "calendar_reg_hour",
					"alt"   => gettext("uren overzicht"),
					"link"  => array("index.php?mod=project&action=showhours&id=", "%id", "&master=", "%master"),
					"check" => "%nonmaster"
				)
			), "nowrap");
			$venster->addCode($view->generate_output());
			/* end of view */
		$venster->endVensterData();
		/* end of window widget */
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."show_overview.js");

		$history = new Layout_history();
		$output->addCode( $history->generate_save_state("action") );

		$output->layout_page_end();
		$output->exit_buffer();
?>
