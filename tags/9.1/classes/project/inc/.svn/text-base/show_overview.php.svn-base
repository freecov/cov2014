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
		$output->layout_page(gettext("Projects"));
		// form for various actions
		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		foreach ($_REQUEST as $k=>$v) {
			$output->addHiddenField($k, $v);
		}
		$output->endTag("form");

		/* create nice window widget */
		$venster = new Layout_venster(array(
			"title" => ($has_declaration) ? gettext("dossier") : gettext("Projects"),
			"subtitle" => gettext("overview")
		));
		if ($userperms["xs_projectmanage"]) {
			$venster->addMenuItem(($has_declaration) ? gettext("new dossier") : gettext("new project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=0');");
			$venster->addMenuItem(($has_declaration) ? gettext("new group") : gettext("new main project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=1');");
			$venster->generateMenuItems();
		}
		/* menu items */
		$op = new Layout_output();
		$op->addSpace(12);
		$spacer = $op->generate_output();
		unset($op);
		$venster->addVensterData();
			$tbl = new Layout_table(array("width" => "100%"));
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addCode(gettext("search").": ");
						$tbl->addTextField("searchkey", $_REQUEST["searchkey"], "", "", 1);
						$tbl->insertAction("forward", gettext("search"), "javascript: document.getElementById('projectsearch').submit();");
						if ($GLOBALS["covide"]->license["has_project_ext"]) {
							$projectext = new ProjectExt_output();
							$tbl->addCode($projectext->genOverViewSearch());
						}
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
					$tbl->addTableData(array("align" => "right"));
						//more actions select box
						$sel = array("void(0);" => "- ".gettext("more actions")." -");
						//active/inactive toggle menuitem
						if ($options["inactive"] == 1) {
							$sel["document.location.href='index.php?mod=project&inactive=0';"] = gettext("show active");
						} else {
							$sel["document.location.href='index.php?mod=project&inactive=1';"] = gettext("show inactive");
						}
						$sel["document.location.href='index.php?mod=project&action=show_activities';"] = gettext("manage activities");
						if (!$declaration) {
							$sel["document.location.href='index.php?mod=project&action=hour_overview';"] = gettext("hour overview");
						} else {
							$declaration_data = new ProjectDeclaration_data();
							$sel["document.location.href='index.php?mod=projectdeclaration&action=start';"] = gettext("manage options");
							$sel["document.location.href='index.php?mod=filesys&action=opendir&id=".$declaration_data->getFolderId()."';"] = gettext("document templates");
						}
						/* if extended project management */
						if ($GLOBALS["covide"]->license["has_project_ext"]) {
							$sel["document.location.href='?mod=projectext&action=extend';"] = gettext("project settings");
						}
						$tbl->addSelectField("project[actions]", $sel);
					$tbl->endTableData();
				$tbl->endTableRow();
			$tbl->endTable();
			$venster->addTag("form", array(
				"id"     => "projectsearch",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "project");
			$venster->addHiddenField("action", "projectsearch");
			$venster->addCode( $tbl->generate_output() );
			$venster->endTag("form");
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("actions").$spacer, "%%complex_actions", "", "nowrap");
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
