<?php
		if (!class_exists("Project")) {
			exit("no class definition found");
		}
		if ($this->has_declaration) {
			/* let the declaration module check a few conditions */
			$declaration = new ProjectDeclaration();
			$has_declaration = $this->has_declaration;
		}


		/* show overview, included from output.php */
		$userdata = new User_data();
		$userperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
		$project_data = new Project_data();
		if ($_REQUEST["searchkey"]) {
			$options = array("searchkey" => $_REQUEST["searchkey"]);
		}
		if ($_REQUEST["inactive"] == 1) {
			$options["inactive"] = 1;
		}
		$top = $_REQUEST["top"];
		if (is_array($options)) {
			$url = sprintf("?mod=project&searchkey=%s&top=%%%%", $_REQUEST["searchkey"]);
		} else {
			$url = "?mod=project&top=%%";
		}
		$projects = $project_data->getProjectsBySearch($options, $top, $GLOBALS["covide"]->pagesize);
		$total_count = $projects["total_count"];
		unset($projects["total_count"]);
		$output = new Layout_output();
		$output->layout_page();
		/* create window widget for searchbox */
		$venster = new Layout_venster(array(
			"title"    => ($has_declaration) ? gettext("dossier") : gettext("projects"),
			"subtitle" => gettext("search")
		));
		$venster->addVensterData();

			$tbl = new Layout_table();
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addTextField("searchkey", $_REQUEST["searchkey"], "", "", 1);
						$tbl->insertAction("forward", gettext("search"), "javascript: document.getElementById('projectsearch').submit();");
						$tbl->addTag("br");
						if ($this->has_declaration) {
							$tbl->insertLink(gettext("show all dossiers"), array(
								"href" => "javascript: search_showall();"
							));
						} else {
							$tbl->insertLink(gettext("show all projects"), array(
								"href" => "javascript: search_showall();"
							));
						}
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
			"method" => "post",
			"action" => "index.php"
		));
		foreach ($_REQUEST as $k=>$v) {
			$output->addHiddenField($k, $v);
		}
		$output->endTag("form");

		$output->addTag("form", array(
			"id"     => "projectsearch",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "project");
		$output->addHiddenField("action", "projectsearch");
		$output->addCode($venster->generate_output());
		$output->endTag("form");
		unset($venster);
		/* create nice window widget */
		$venster = new Layout_venster(array(
			"title" => ($has_declaration) ? gettext("dossier") : gettext("projects"),
			"subtitle" => gettext("overview")
		));
		//active/inactive toggle menuitem
		if ($options["inactive"] == 1) {
			$venster->addMenuItem(gettext("show active"), "index.php?mod=project&inactive=0");
		} else {
			$venster->addMenuItem(gettext("show inactive"), "index.php?mod=project&inactive=1");
		}
		if ($userperms["xs_projectmanage"]) {
			$venster->addMenuItem(($has_declaration) ? gettext("new dossier") : gettext("new project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=0');");
			$venster->addMenuItem(($has_declaration) ? gettext("new group") : gettext("new main project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=1');");
			$venster->addMenuItem(gettext("manage activities"), "index.php?mod=project&action=show_activities");
			if (!$declaration) {
				$venster->addMenuItem(gettext("hour overview"), "index.php?mod=project&action=hour_overview");
			} else {
				$declaration_data = new ProjectDeclaration_data();
				$venster->addMenuItem(gettext("manage options"), "index.php?mod=projectdeclaration&action=start");
				$venster->addMenuItem(gettext("document templates"), "index.php?mod=filesys&action=opendir&id=".$declaration_data->getFolderId());
			}
			/* if extended project management */
			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$venster->addMenuItem(gettext("project settings"), "?mod=projectext&action=extend");
			}
		}
		$venster->generateMenuItems();
		/* menu items */
		$venster->addVensterData();
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping("", "%%complex_actions");
			$view->addMapping(($has_declaration) ? gettext("dossier") : gettext("project"), array(" ", "%%complex_name"));
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping(gettext("executor"), "%executor");

			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$projectext_data = new ProjectExt_data();
				$projectext_data->applyMetaSearch($projects);
				$projectext_output = new ProjectExt_output();
				$projectext_output->addMetaFieldsToList($view, $projects);
			}
			if ($GLOBALS["covide"]->license["has_project_declaration"]) {
				$projectdeclaration_output = new ProjectDeclaration_output();
				$projectdeclaration_output->addFieldsToList($view, $projects);
			}
			$view->addData($projects);

			/* specify our complex mappings */
			$view->defineComplexMapping("complex_name", array(
				array(
					"type" => "link",
					"text"  => "%name",
					"link"  => array("index.php?mod=project&action=showhours&id=", "%id", "&master=", "%master"),
					"check" => "%nonmaster"
				),
				array(
					"type" => "text",
					"text"  => "%name",
					"check" => "%master"
				)
			));
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "info",
					"alt"  => gettext("information"),
					"link" => array("index.php?mod=project&action=showinfo&id=", "%id", "&master=", "%master"),
					"check" => "%master"
				),
				array(
					"type" => "action",
					"src"  => "go_todo",
					"alt"  => gettext("information"),
					"link"  => array("index.php?mod=project&action=showhours&id=", "%id", "&master=", "%master"),
					"check" => "%nonmaster"
				),
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("alter project"),
					"link" => array("javascript: popup('index.php?mod=project&action=edit&id=", "%id", "&master=", "%master", "');"),
					"check" => "%allow_edit"
				),
				array(
					"type"  => "action",
					"src"   => "important",
					"alt"   => gettext("has hours"),
					"check" => "%has_hours",
				),
			), "nowrap");
			$venster->addCode($view->generate_output());
			/* end of view */
			/* add paging to the whole grid */
			$paging = new Layout_paging();
			$paging->setOptions($top, $total_count, $url);
			$venster->addCode( $paging->generate_output() );
			/* end paging */

		$venster->endVensterData();
		/* end of window widget */
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."show_overview.js");

		$history = new Layout_history();
		$output->addCode( $history->generate_save_state("action") );

		$output->layout_page_end();
		$output->exit_buffer();
?>
