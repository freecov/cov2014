<?php
	if (!class_exists("Layout_output")) {
		die("no class definition found");
	}
	require("settings.php");

	// load offices.php to check for some extra config directives
	require("conf/offices.php");

	$userdata = new User_data();

	if ($_SESSION["user_id"])
		$userdata->getUserPermissionsById($_SESSION["user_id"]);

	if (!$GLOBALS["covide"]->mobile) {
		header("Content-type: text/html; charset=UTF-8");
		if ($GLOBALS["covide"]->output_xhtml)
			$this->addCode("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
				\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
		else
			$this->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
	}
	$this->addCode("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");

	if (!$GLOBALS["covide"]->mobile) {
		$this->addComment("Covide  : Cooperative Virtual Desktop copyright (c) 2000-2008 Covide BV");
		$this->addComment("License : Licensed under GPL");
		$this->addComment("Web     : http://www.covide.net, http://covide.sourceforge.net");
		$this->addComment("Info    : info@covide.nl");
	}
	$this->addTag("head");
	$this->addCode("\n");

	/* prevent tearing for IE */
	if (preg_match("/MSIE (6|7)/s", $_SERVER["HTTP_USER_AGENT"])) {
		$this->addTag("meta", array(
			"http-equiv" => "Page-Enter",
			"content" => "blendTrans(Duration=0.0)"
		));
		$this->addCode("\n");
	}
	
	$this->addTag("base", array(
		"href" => $GLOBALS["covide"]->webroot
	));
	$this->addCode("\n");

	$this->insertTag("title", "Covide - Cooperative Virtual Desktop ".$title);
	$this->addCode("\n");

	if (!$GLOBALS["covide"]->mobile) {

		$this->addTag("meta", array("http-equiv"=>"Content-Type", "content"=>"text/html; charset=UTF-8") );
		$this->addCode("\n");
		$this->addTag("link", array("rel"=>"icon", "href"=>"img/favicon.png"));
		$this->addCode("\n");
		$this->addTag("link", array("rel"=>"shortcut icon", "href"=>"img/favicon.png"));
		$this->addCode("\n");
	}

	$this->start_javascript();
	$this->addCode("\nvar rendertime_start = new Date();\n");
	$this->addCode(sprintf("var clock_start = %d;\n", date("s")));
	$this->addCode(sprintf("var covide_code = '%s';\n", $GLOBALS["covide"]->license["code"]));
	$this->addCode(sprintf("var dev_version = '%d';\n", ($GLOBALS["covide"]->devmode) ? 1 : 0));
	$this->addCode(sprintf("var disable_basics = %d;\n", $GLOBALS["covide"]->license["disable_basics"]));
	$this->addCode(sprintf("var webroot = '%s';\n", $GLOBALS["covide"]->webroot));
	$this->addCode(sprintf("var voip_poll_interval = %d;\n", $voip["polling_interval"]));

	$this->end_javascript();
	$this->addCode("\n");

	$this->css(-1);
	$this->load_javascript(self::include_dir."js_classes.js");
	$this->load_javascript(self::include_dir."js_popups.js");
	$this->load_javascript(self::include_dir."Prompt.js");
	if ($GLOBALS["covide"]->license["has_onlineusers"]) {
		$this->load_javascript(self::include_dir."js_online.js");
	}
	$this->load_javascript(self::include_dir."xmlhttp.js");
	$this->load_javascript(self::include_dir."alerts.js");
	$this->addCode("\n");

	$this->endTag("head");
	$this->addCode("\n");
	$this->addTag("body");

	/*
	if ($GLOBALS["display_error"]) {
		$this->addTag("div", array(
			"style" => "position: absolute; left: 80; top: 50; background-color: #fff; border: 2px solid red; z-index: 50; height: 50px; overflow-y: auto;"
		));
			$this->insertTag("b", "Fatal error occured: ");
			$this->addCode("one or more programs required by Covide are not installed on the server.");
			$this->addCode("Covide will run without these programs,");
			$this->addTag("br");
			$this->addCode(" but with reduced functionality and/or performance. ");
			$this->addCode("Please update your server installation as soon as possible!");
			$this->addTag("br");
			$this->addTag("ul");
			foreach ($GLOBALS["display_error"] as $error) {
				$this->insertTag("li", $error);
				trigger_error($error, E_USER_WARNING);
			}
			$this->endTag("ul");
		$this->endTag("div");
	}
	*/

	$this->insertTag("div", "", array("id"=>"xmlhttp_status"));

	/* top page anchor to refer to */
	$this->insertTag("a", "", array("id"=>"top"));


	/* info layer scripts */
	$this->addTag("div", array(
		"id" => "infocontainer",
		"style" => "position: absolute"
	));
		$this->insertTag("div", "", array("id"=>"inforight",  "class"=>"infolayer inforight"));
		$this->insertTag("div", "", array("id"=>"infovoip",   "class"=>"infolayer infovoip"));
	$this->endTag("div");

	$this->addTag("div", array("id"=>"infoLayerMsg", "style"=>"display: none; position: absolute"));
		$this->insertAction("close", gettext("close"), "javascript: hideInfoLayer();");
		$this->addSpace();
		$this->addTag("b");
		$this->insertTag("a", gettext("close"), array("href"=>"javascript: hideInfoLayer();") );
		$this->endTag("b");
	$this->endTag("div");
	/* end infolayer scripts */

	$this->addTag("div", array("id"=>"covide_body"));

	if ($this->_hide_navigation) {
		$this->addTag("table", array("class"=>"fullheight list_data") );
	} else {

		$this->addTag("table", array("class"=>"fullheight") );
		$this->addTag("tr");
		$this->addTag("td", array("width"=>"100%", "class" => "valign_top") );

		if ($_SESSION["user_id"]) {

			$navigation = new Layout_navigation();
			/* module cms */
			if ($GLOBALS["covide"]->license["has_cms"] && $userdata->checkPermission("xs_cms_level"))
				$navigation->addNavigationItem(gettext("CMS"), "module_cms", "?mod=cms");

			/* module addressbook */
			$navigation->addNavigationItem(gettext("Address"),"module_addressbook","?mod=address");

			/* module calendar */
			if (!$GLOBALS["covide"]->license["disable_basics"])
				$navigation->addNavigationItem(gettext("Calendar"),"module_calendar","?mod=calendar");

			/* module notes */
			$navigation->addNavigationItem(gettext("Notes"),"module_notes","?mod=note");
			
			/* module notes */
			$navigation->addNavigationItem(gettext("Todos"),"module_todo","?mod=todo");

			/* email and filesystem */
			if (!$GLOBALS["covide"]->license["disable_basics"] || $GLOBALS["covide"]->license["has_cms"]) {
				$navigation->addNavigationItem(gettext("Email"),"module_email","?mod=email");
				$navigation->addNavigationItem(gettext("File management"),"module_filesys","?mod=filesys");
			}
			/* module project, finance/invoice */
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				if ($GLOBALS["covide"]->license["has_project"])
					$navigation->addNavigationItem(gettext("Projects"), "module_project", "?mod=project");

				if ($userdata->checkPermission("xs_turnovermanage")) {
					if ($GLOBALS["covide"]->license["has_finance"] && !$GLOBALS["covide"]->license["has_factuur"]) {
						$navigation->addNavigationItem(gettext("Finance Old"), "module_finance_old", "./non-oop/finance/");
					} elseif ($GLOBALS["covide"]->license["has_factuur"]) {
						$navigation->addNavigationItem(gettext("Finance"), "module_finance", "?mod=finance");
					}
				}
				/* module sales */

				if ($GLOBALS["covide"]->license["has_sales"])
					$navigation->addNavigationItem(gettext("Sales"), "module_sales", "?mod=sales");

				/* module issues */
				if ($GLOBALS["covide"]->license["has_issues"])
					$navigation->addNavigationItem(gettext("Support"), "module_support", "?mod=support");

				/* module campaigns */
				if ($GLOBALS["covide"]->license["has_campaign"])
					$navigation->addNavigationItem(gettext("Campaign"), "module_campaign", "?mod=campaign");

				/* module search */
				$navigation->addNavigationItem(gettext("Search"), "module_search", "?mod=index");

				/* module snack */
				if (date("w") == $GLOBALS["covide"]->license["has_snack"]) {
					$navigation->addNavigationItem(gettext("Snacks"), "module_snack", "?mod=snack");
				}
			}
			$navigation->addNavigationItem(gettext("Help"), "module_help", "javascript: var help = window.open('?mod=user&action=help');");
			if ($GLOBALS["covide"]->license["has_onlineusers"]) {
				$navigation->addNavigationItem(gettext("Online users"), "module_chat", "javascript: showOnlineUsers();");
			}

			$navigation->addNavigationItem(gettext("Settings"), "module_settings", "?mod=user");
			$navigation->addNavigationItem(gettext("Logout"), "module_logout", "?mod=user&action=logout");

			$navigation->generateNavigationItems();

			$table = new Layout_table();
			$table->addTableRow();
				$table->addTableData();
				$table->addCode( $navigation->generate_output() );

				$table->endTableData();

				$table->addTableData();

					$table->addTag("div", array("id"=>"menuDate", "style" => "right: 2px; overflow-x: hidden;") );
						if ($GLOBALS["covide"]->mobile || $GLOBALS["autoloader_include_path"]) {
							$history = new Layout_history();
							$table->addCode( $history->get_history_scope_list() );
							$table->load_javascript(self::include_dir."js_history.js");
						} else {
							$history = new Layout_history();
							$table->addTag("form", array(
								"id"     => "history_frm",
								"action" => "index.php",
								"method" => "get"
							));
							$table->addCode( $history->get_history_scope_list() );
							$table->addSelectField("history", $history->get_history_data(), $_REQUEST["selected_restorepoint"], 0, array("style"=>"width:280px"));
							$table->load_javascript(self::include_dir."js_history.js");
							$table->endTag("form");
							$table->addTag("br");
						}


						if (!$GLOBALS["covide"]->mobile) {
							$table->addTag("span", array("class"=>"date nowrap") );
								$table->addCode( utf8_encode(strftime("%A, %e %B %Y ")) );
							$table->endTag("span");
							$table->addTag("br");
							$table->addTag("span", array("class"=>"user nowrap"));
								$table->addCode( gettext("logged in as:") );
								$table->addCode( "&nbsp;" );
								$table->addTag("b");
									$table->addCode($userdata->getUsernameById($_SESSION["user_id"]));
									unset($userdata);
								$table->endTag("b");
							$table->endTag("span");
							$table->addTag("br");
							$table->addTag("span", array("id"=>"clock_container", "style" => "width: 150px;"));
								$table->addTag("span", array( "class"=>"clock nowrap", "id"=>"clock_time" ));
									$table->addCode( date("H:i:") );
								$table->endTag("span");
								$table->addTag("span", array( "class"=>"clock nowrap", "id"=>"clock_seconds" ));
									$table->addCode(date("s"));
								$table->endTag("span");
							$table->endTag("span");
							$table->load_javascript(self::include_dir."jscript_clock.js");

							if (!$GLOBALS["covide"]->mobile) {
								$table->load_javascript("classes/voip/inc/active_calls.js");
							}
						}
						if (!$user_data)
							$user_data = new User_data();

						$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);
						if ($userinfo["showpopup"]) {
							$desktop_data = new Desktop_data();
							if (is_array($desktop_data->getAlertInfo())) {
								$table->start_javascript();
								$table->addCode("show_alerts();");
								$table->end_javascript();
							}
							unset($desktop_data);
						}

					$table->endTag("div");
					if ($GLOBALS["covide"]->mobile) {
						$table->addTag("span", array("class"=>"nowrap") );
							$table->addCode( utf8_encode(strftime("%A, %e %B %Y ")) );
						$table->endTag("span");
						$table->addTag("br");
						$table->addTag("span", array("class"=>"nowrap"));
							$table->addCode( gettext("logged in as:") );
							$table->addCode( "&nbsp;" );
							$table->addTag("b");
								$table->addCode($userdata->getUsernameById($_SESSION["user_id"]));
								unset($userdata);
							$table->endTag("b");
						$table->endTag("span");
						$table->addTag("br");
					}
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();

			$this->addCode( $table->generate_output() );
		}
		$this->endTag("td");
		$this->endTag("tr");

		unset($table);
	}

	$this->addTag("tr");
	$this->addTag("td", array(
		"class"=>"fullheight pagecontent",
		"align"=>"center"
	));
	$this->addComment("begin code");
?>
