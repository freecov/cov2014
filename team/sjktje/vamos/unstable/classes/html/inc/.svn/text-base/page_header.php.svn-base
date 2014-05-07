<?php
	if (!class_exists("Layout_output")) {
		die("no class definition found");
	}
	require("settings.php");
	$userdata = new User_data();

	if ($_SESSION["user_id"])
		$userdata->getUserPermissionsById($_SESSION["user_id"]);

	require($parent."common/functions_onderdelen.php");

	if (!$GLOBALS["covide"]->mobile) {
		header("Content-type: text/html; charset=UTF-8");
		$this->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
	}
	$this->addCode("<html>\n");

	if (!$GLOBALS["covide"]->mobile) {
		$this->addComment("Covide  : Cooperative Virtual Desktop copyright (c) 2000-2006 Covide BV");
		$this->addComment("License : Licensed under GPL");
		$this->addComment("Web     : http://www.covide.net, http://covide.sourceforge.net");
		$this->addComment("Info    : info@covide.nl");
	}
	$this->addCode("<head>\n");

	$this->addCode(sprintf("<base href=\"%s\">\n",
		$GLOBALS["covide"]->webroot
	));
	#$this->addCode("<base href='".$GLOBALS["covide"]->webroot."'>");
	$this->css(-1);
	$this->addCode("\n");
	$this->addCode("<title>Covide - Cooperative Virtual Desktop ".$title."</title>\n");

	if (!$GLOBALS["covide"]->mobile) {
		$this->addTag("meta", array("http-equiv"=>"Content-Type", "content"=>"text/html; charset=UTF-8") );
		$this->addTag("link", array("rel"=>"icon", "href"=>"img/favicon.png"));
		$this->addTag("link", array("rel"=>"shortcut icon", "href"=>"img/favicon.png"));
	}

	$this->start_javascript();
	$this->addCode("\nvar rendertime_start = new Date();\n");
	$this->addCode(sprintf("var covide_code = '%s';\n", $GLOBALS["covide"]->license["code"]));
	$this->addCode(sprintf("var disable_basics = %d;\n", $GLOBALS["covide"]->license["disable_basics"]));
	$this->end_javascript();

	$this->load_javascript("classes/html/inc/js_classes.js");
	$this->load_javascript("classes/html/inc/js_popups.js");
	$this->load_javascript("classes/html/inc/xmlhttp.js");
	$this->load_javascript("classes/html/inc/alerts.js");

	$this->addCode("</head>");
	$this->addCode("<body>");

	if ($GLOBALS["display_error"]) {
		$this->insertTag("b", "Fatal error occured: ");
		$this->addCode("one or more programs required by Covide are not installed on the server.");
		$this->addCode("Covide will run without these programs,");
		$this->addTag("br");
		$this->addCode(" but with reduced functionality and/or performance. ");
		$this->addCode("Please update your server installation as soon as possible!");
		$this->addTag("br");
		$this->insertTag("u", "Not installed programs");
		$this->addCode(": ");
		$this->insertTag("b", implode(", ", $GLOBALS["display_error"]));
	}

	$this->insertTag("div", "", array("id"=>"xmlhttp_status"));

	/* top page anchor to refer to */
	$this->insertTag("a", "", array("id"=>"top"));


	/* info layer scripts */
	$this->addTag("div", array("id"=>"infocontainer"));
		$this->insertTag("div", "", array("id"=>"inforight",  "class"=>"infolayer inforight"));
		$this->insertTag("div", "", array("id"=>"infovoip",   "class"=>"infolayer infovoip"));
		$this->addTag("div", array("id"=>"voip_image", "style"=>"display:none"));
			$this->insertAction("data_phone_rining", "", "");
		$this->endTag("div");

	$this->endTag("div");

	$this->addTag("div", array("id"=>"infoLayerMsg", "style"=>"display: none; position: absolute"));
		$this->insertAction("close", gettext("close"), "javascript: hideInfoLayer();");
		$this->addSpace();
		$this->addTag("b");
		$this->insertTag("a", gettext("close"), array("href"=>"javascript: hideInfoLayer();") );
		$this->endTag("b");
	$this->endTag("div");
	/* end infolayer scripts */

	$this->load_javascript(self::include_dir."infolayer.js");
	$this->load_javascript(self::include_dir."js_floatlayer_msie.js");

	$this->addTag("div", array("id"=>"covide_body"));

	if ($this->_hide_navigation) {
		$this->addTag("table", array("class"=>"fullheight list_data") );
	} else {

		$this->addTag("table", array("class"=>"fullheight") );
		$this->addTag("tr");
		$this->addTag("td", array("width"=>"100%", "class" => "valign_top") );

		if ($_SESSION["user_id"]) {

			$navigation = new Layout_navigation();
			if ($GLOBALS["covide"]->license["has_cms"]) {
				//$navigation->addNavigationItem(gettext("Website"), "module_website", "http://".$_SERVER["HTTP_HOST"]."/");
				$navigation->addNavigationItem(gettext("CMS"), "module_cms", "?mod=cms&cmd=collapseAll&options_state=none");
			}
			//$navigation->addNavigationItem(gettext("Address"),"module_addressbook","?mod=address");
			$navigation->addNavigationItem(gettext("Consultants"),"module_doctor","?mod=consultants");
			$navigation->addNavigationItem(gettext("Customers"),"module_hospital","?mod=customers");
			/*
			if (!$GLOBALS["covide"]->license["disable_basics"])
				$navigation->addNavigationItem(gettext("Calendar"),"module_calendar","?mod=calendar");
			$navigation->addNavigationItem(gettext("Notes"),"module_notes","?mod=note");
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				$navigation->addNavigationItem(gettext("Email"),"module_email","?mod=email");
				$navigation->addNavigationItem(gettext("File management"),"module_filesys","?mod=filesys");
				if ($GLOBALS["covide"]->license["has_project"])
					$navigation->addNavigationItem(gettext("Projects"), "module_project", "?mod=project");
				if ($GLOBALS["covide"]->license["has_finance"])
					$navigation->addNavigationItem(gettext("Finance"), "module_finance", "./non-oop/finance/");
				if ($GLOBALS["covide"]->license["has_sales"] && $userdata->checkPermission("xs_salesmanage"))
					$navigation->addNavigationItem(gettext("Sales"), "module_sales", "?mod=sales");
				if ($GLOBALS["covide"]->license["has_issues"])
					$navigation->addNavigationItem(gettext("Support"), "module_support", "?mod=support");
				$navigation->addNavigationItem(gettext("Search"), "module_search", "?mod=index");
				if (date("w")==5 && $GLOBALS["covide"]->license["has_snack"])
					$navigation->addNavigationItem(gettext("Snacks"), "module_snack", "?mod=snack");
			}
			$navigation->addNavigationItem(gettext("Help"), "module_help", "javascript: var help = window.open('?mod=user&action=help');");
			*/

			$navigation->addNavigationItem(gettext("Settings"), "module_settings", "?mod=user");
			$navigation->addNavigationItem(gettext("Logout"), "module_logout", "?mod=user&action=logout");

			$navigation->generateNavigationItems();

			$table = new Layout_table();
			$table->addTableRow();
				$table->addTableData();
				$table->addCode( $navigation->generate_output() );

				$table->endTableData();

				$table->addTableData();

					$table->addTag("div", array("id"=>"menuDate") );
						if ($GLOBALS["covide"]->mobile) {
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
								$table->addCode( strftime("%A, %e %B %Y ") );
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
								$table->endTag("span");
							$table->endTag("span");
							$table->load_javascript("js/jscript_clock.js");

							if (!$GLOBALS["covide"]->mobile && $GLOBALS["covide"]->license["has_voip"]) {
								/* check if user has show_voip_popup */
								$user_data = new User_data();
								$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
								if ($user_info["showvoip"])
									$table->load_javascript("classes/voip/inc/active_calls.js");

								$table->load_javascript("classes/voip/inc/newvoip_calls.js");
							}
						}

					$table->endTag("div");
					if ($GLOBALS["covide"]->mobile) {
						$table->addTag("span", array("class"=>"nowrap") );
							$table->addCode( strftime("%A, %e %B %Y ") );
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
