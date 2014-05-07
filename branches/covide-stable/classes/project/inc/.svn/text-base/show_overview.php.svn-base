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
			$subtitle = gettext("inactive");
		} else {
			$options["inactive"] = 0;
			$subtitle = gettext("overview");
		}
		$top = $_REQUEST["top"];
		if (is_array($options)) {
			$url = sprintf("?mod=project&inactive=%d&searchkey=%s&top=%%%%", $options["inactive"], $_REQUEST["searchkey"]);
		} else {
			$url = "?mod=project&sort=".$_REQUEST["sort"]."&top=%%";
		}
		$options["sort"] = $_REQUEST["sort"];
		$projects = $project_data->getProjectsBySearch($options, $top, $GLOBALS["covide"]->pagesize);
		$total_count = $projects["total_count"];
		unset($projects["total_count"]);
		$output = new Layout_output();
		$output->layout_page(gettext("Projects")." - ".$subtitle);
		// form for various actions
		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		foreach ($_REQUEST as $k=>$v) {
			if ($k != "searchkey" && $k != "top") {
				$output->addHiddenField($k, $v);
			}
		}
		$output->endTag("form");

		/* create nice window widget */
		$venster = new Layout_venster(array(
			"title" => ($has_declaration) ? gettext("dossier") : gettext("Projects"),
			"subtitle" => $subtitle
		));
		if ($userperms["xs_projectmanage"] || $userperms["xs_limited_projectmanage"]) {
			$venster->addMenuItem(($has_declaration) ? gettext("new dossier") : gettext("new project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=0', 'projectedit', 800, 600, 1);", "", 0);
			$venster->addMenuItem(($has_declaration) ? gettext("new group") : gettext("new main project"), "javascript: popup('index.php?mod=project&action=edit&id=0&master=1', 'projectmainedit', 800, 600, 1);", "", 0);
		}
		if ($_SESSION["locale"] == "nl_NL") {
			$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Projecten", array("target" => "_blank"), 0);
		}
		//active/inactive toggle menuitem
		if ($options["inactive"] == 1) {
			$venster->addMenuItem(gettext("show active"), "javascript: document.location.href='index.php?mod=project&inactive=0';");
		} else {
			$venster->addMenuItem(gettext("show inactive"), "javascript: document.location.href='index.php?mod=project&inactive=1';");
		}
		if ($userperms["xs_projectmanage"] || $userperms["xs_limited_projectmanage"]) {
			$venster->addMenuItem(gettext("manage activities"), "javascript: document.location.href='index.php?mod=project&action=show_activities';");
			$venster->addMenuItem(gettext("manage other projectcosts"), "javascript: document.location.href='index.php?mod=project&action=show_costs';");
		}
		if (!$declaration) {
			$venster->addMenuItem(gettext("hour overview"), "javascript: document.location.href='index.php?mod=project&action=hour_overview';");
		} else {
			$declaration_data = new ProjectDeclaration_data();
			$venster->addMenuItem(gettext("manage options"), "javascript: document.location.href='index.php?mod=projectdeclaration&action=start';");
			$venster->addMenuItem(gettext("document templates"), "javascript: document.location.href='index.php?mod=filesys&action=opendir&id=".$declaration_data->getFolderId()."';");
		}
		/* if extended project management */
		if ($GLOBALS["covide"]->license["has_project_ext"]) {
			$venster->addMenuItem(gettext("project settings"), "javascript: document.location.href='?mod=projectext&action=extend';");
		}

		$venster->generateMenuItems();
		$op = new Layout_output();
		$op->addSpace(12);
		$spacer = $op->generate_output();
		unset($op);
		$venster->addVensterData();
			$tbl = new Layout_table(array("width" => "100%"));
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addHiddenField("top", $_REQUEST["top"]);
						$tbl->addCode(gettext("search").": ");
						$tbl->addHiddenField("top", $_REQUEST["top"]);
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
				$tbl->endTableRow();
			$tbl->endTable();
			$venster->addTag("form", array(
				"id"     => "projectsearch",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "project");
			$venster->addHiddenField("action", "projectsearch");
			$venster->addHiddenField("inactive", $_REQUEST["inactive"]);
			$venster->addCode( $tbl->generate_output() );
			$venster->endTag("form");
			/* create view */
			$view = new Layout_view();
			/* define the fields and mappings for the view */
			$view->addMapping(gettext("actions").$spacer, "%%complex_actions", "", "nowrap");
			$view->addMapping(($has_declaration) ? gettext("dossier") : gettext("project"), array(" ", "%%complex_name"));
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping(gettext("executor"), "%executor");
			$view->addMapping(gettext("relations"), "%%complex_relnames");

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
					"alt"  => gettext("list of subprojects"),
					"link" => array("index.php?mod=project&action=showinfo&id=", "%id", "&master=", "%master"),
					"check" => "%master"
				),
				array(
					"type" => "action",
					"src"  => "info3",
					"alt"  => gettext("overview card"),
					"link"  => array("index.php?mod=project&action=showhours&id=", "%id", "&master=", "%master"),
					"check" => "%nonmaster"
				),
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("alter project"),
					"link" => array("javascript: popup('index.php?mod=project&action=edit&id=", "%id", "&master=", "%master", "', 'projectedit', 800, 600, 1);"),
					"check" => "%allow_edit"
				),
				array(
					"type"  => "action",
					"src"   => "important",
					"alt"   => gettext("has hours"),
					"check" => "%has_hours",
				),
			), "nowrap");
			$view->defineComplexMapping("complex_relnames", array(
				array(
					"type" => "multilink",
					"link" => array("index.php?mod=address&action=relcard&id=", "%relations"),
					"text" => "%relation_names"
				)
			));
			$view->defineSortParam("sort");
			$view->defineSort(($has_declaration) ? gettext("dossier") : gettext("project"), "name");
			$view->defineSort(gettext("description"), "description");
			$view->defineSort(gettext("executor"), "executor");
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
