<?php
	if (!class_exists("Layout_output")) {
		die("no class definition found");
	}
	require("settings.php");

	// load offices.php to check for some extra config directives
	require("conf/offices.php");


	if ($_SESSION["user_id"]) {
		$userdata = new User_data();
		$userdata->getUserPermissionsById($_SESSION["user_id"]);
		$user_info = $userdata->getUserdetailsById($_SESSION["user_id"]);
		$username = $userdata->getUsernameById($_SESSION["user_id"]);
	}

	if (!$GLOBALS["covide"]->mobile) {
		header("Content-type: text/html; charset=UTF-8");
		if ($GLOBALS["covide"]->output_xhtml) {
			$this->addCode("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
			$this->addCode("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
			$this->addCode("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
		} else {
			$this->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
			$this->addCode("<html>\n");
		}
	}

	if (!$GLOBALS["covide"]->mobile) {
		$this->addComment("Covide  : Cooperative Virtual Desktop copyright (c) 2000-2008 Covide BV");
		$this->addComment("License : Licensed under GPL");
		$this->addComment("Version : ".$GLOBALS["covide"]->vernr);
		$this->addComment("Web     : http://www.covide.net, http://covide.sourceforge.net");
		$this->addComment("Info    : info@covide.nl");
	}
	$this->addTag("head");
	$this->addCode("\n");

	/* prevent tearing for IE */
	if (preg_match("/MSIE (6|7)/s", $_SERVER["HTTP_USER_AGENT"])) {
		$this->addComment("MSIE tearing prevention");
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

	$this->insertTag("title", ($title ? $title : "Dashboard")." - Cooperative Virtual Desktop");
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
	$this->load_javascript(self::include_dir."jquery.js");
	$this->load_javascript(self::include_dir."jqueryplugins/jquery-ui-1.7.custom.min.js");
	$this->load_javascript(self::include_dir."js_popups.js");
	$this->load_javascript(self::include_dir."js_classes.js");
	$this->addCode("<!-- Prompt.js will be redone to use jquery -->");
	$this->load_javascript(self::include_dir."Prompt.js");
	if ($GLOBALS["covide"]->license["has_onlineusers"]) {
		$this->load_javascript(self::include_dir."js_online.js");
	}
	$this->addCode("<!-- xmlhttp.js will be redone to use jquery -->");
	$this->load_javascript(self::include_dir."xmlhttp.js");
	$this->addCode("<!-- alerts.js will be redone to use jquery -->");
	$this->load_javascript(self::include_dir."alerts.js");
	if (!$_SESSION["user_id"]) {
		// we only need the md5 plugin on the login screen.
		// since it's not that small we exclude it from the rest of the interface
		$this->load_javascript(self::include_dir."jqueryplugins/md5.js");
	} else {
		$this->start_javascript();
		if ($user_info["showvoip"] == 1) {
			$this->addCode("var voippopup = 1;");
		} else {
			$this->addCode("var voippopup = 0;");
		}
		$this->end_javascript();

		if ($GLOBALS["covide"]->license["has_voip"]) {
			$this->load_javascript("classes/voip/inc/active_calls.js");
		}
	}
	$this->addCode("\n");

	if ($extra_header_code) {
		$this->addCode($extra_header_code);
	}

	$this->endTag("head");
	$this->addCode("\n");
	$this->addTag("body", array(
		"id" => "covidedashboard",
	));
	$this->addCode("\n");
	$this->insertTag("div", "", array("id"=>"xmlhttp_status"));
	$this->addCode("\n");

	/* top page anchor to refer to */
	$this->insertTag("a", "", array("id"=>"top"));
	$this->addCode("\n");

	/* info layer scripts */
	$this->addTag("div", array(
		"id" => "infocontainer",
		"style" => "position: absolute"
	));
		$this->insertTag("div", "", array("id"=>"inforight",  "class"=>"infolayer inforight"));
		$this->insertTag("div", "", array("id"=>"infovoip",   "class"=>"infolayer infovoip"));
	$this->endTag("div");
	$this->addCode("\n");

	$this->addTag("div", array("id"=>"infoLayerMsg", "style"=>"display: none; position: absolute"));
		$this->addCode("\n");
		$this->insertAction("close", gettext("close"), "javascript: hideInfoLayer();");
		$this->addSpace();
		$this->addTag("b");
		$this->insertTag("a", gettext("close"), array("href"=>"javascript: hideInfoLayer();") );
		$this->endTag("b");
		$this->addCode("\n");
	$this->endTag("div");
	$this->addCode("\n");

	/* end infolayer scripts */
	$this->addTag("div", array("id"=>"pagecontainer"));
	$this->addCode("\n");

	if ($this->_hide_navigation) {
		//$this->addTag("table", array("class"=>"fullheight list_data") );
	} else {
		$this->addTag("div", array("id" => "headerwrap"));
			$this->addCode("\n");
			$this->addTag("div", array("id" => "header"));
				$this->addCode("\n");
				$this->addTag("div", array("id" => "headerleft"));
				if ($_SESSION["user_id"]) {
					$this->insertImage("modules/covide.png", "Covide", "?mod=desktop", 1);
				}
				$this->endTag("div");
				$this->addCode("\n");
				$this->addTag("div", array("id" => "headercenter"));
				if ($_SESSION["user_id"]) {
					$this->addCode("\n");
					$this->addCode("Loading ...");
					$this->endTag("div");
					if ($user_info["showpopup"]) {
						$this->addTag("object", array("id" => "alertsound", "width" => 0, "height" => 0));
						$this->addTag("param", array("name" => "movie", "vaule" => "themes/default/reminder.swf"));
						$this->addTag("param", array("name" => "loop", "value" => "false"));
						$this->addTag("param", array("name" => "autostart", "value" => "false"));
						$this->insertTag("embed", "", array("src" => "themes/default/reminder.swf", "autostart" => "false", "loop" => "false", "width" => 0, "height" => 0, "name" => "alertsound"));
						$this->endTag("object");
					}

					$this->start_javascript();
					$this->addCode("
						$(document).ready(function() {
							loadAlerts();
							setInterval('loadAlerts();', 300000);
						});
						function loadAlerts() {
							$('#headercenter').load('index.php?mod=desktop&action=header_alerts');
						}

					");
					$this->end_javascript();
				} else {
					$this->endTag("div");
				}
				$this->addCode("\n");
				$this->addTag("div", array("id" => "headerright"));
				if ($_SESSION["user_id"]) {
					$this->addTag("p");
						$this->addCode( utf8_encode(strftime("%A, %e %B %Y ")) );
							$this->addTag("span", array("id"=>"clock_container", "style" => "width: 150px;"));
								$this->addTag("span", array( "class"=>"clock nowrap", "id"=>"clock_time" ));
									$this->addCode( date("H:i:") );
								$this->endTag("span");
								$this->addTag("span", array( "class"=>"clock nowrap", "id"=>"clock_seconds" ));
									$this->addCode(date("s"));
								$this->endTag("span");
							$this->endTag("span");
							$this->load_javascript(self::include_dir."jscript_clock.js");
						$this->addTag("br");
						$this->addCode( gettext("logged in as:") );
						$this->addSpace();
						$this->addTag("b");
							$this->addCode($username);
						$this->endTag("b");
						$this->addSpace();
						$this->addCode("|");
						$this->addSpace();
						$this->insertLink(gettext("user settings"), array("href" => "?mod=user"));
						if ($GLOBALS["covide"]->license["has_onlineusers"]) {
							$this->addSpace();
							$this->addCode("|");
							$this->addSpace();
							$this->insertLink(gettext("online users"), array("href" => "javascript:showOnlineUsers();"));
						}
						$this->addSpace();
						$this->addCode("|");
						$this->addSpace();
						$this->insertLink(gettext("logout"), array("href" => "?mod=user&action=logout"));
						if ($_SESSION["locale"] == "nl_NL") {
							$this->addSpace();
							$this->addCode("|");
							$this->addSpace();
							$this->insertLink(gettext("help"), array("href" => "http://wiki.covide.nl", "target" => "_blank"));
						}
					$this->endTag("p");
						$this->addTag("form", array(
							"method" => "get",
							"id" => "header_search",
							"action" => "index.php"
						));
					$this->addCode("
							<form action=\"zoek\" method=\"get\">
								<input type=\"hidden\" name=\"fromdashboard\" value=\"1\" />
								<input type=\"text\" id='headersearchkey' value=\"\" class=\"inputtext\" />
								<select name=\"search_in\" id='search_in' class=\"inputselect\">
									<option value='1'>Covide</option>
									<option value='2'>Google</option>
									</select>
								<input type=\"button\" value=\"zoeken\" onclick='headersearch();' class=\"\" />
								");
						$this->endTag("form");
				}
				$this->endTag("div");
		if ($_SESSION["user_id"]) {
			$navigation = new Layout_navigation();
			/* module cms */
			if ($GLOBALS["covide"]->license["has_cms"] && $userdata->checkPermission("xs_cms_level")) {
				$navigation->addNavigationItem(gettext("CMS"), "cms", "?mod=cms");
			}

			/* module addressbook */
			$navigation->addNavigationItem(gettext("Address"), "address", "?mod=address");

			/* module calendar */
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				$navigation->addNavigationItem(gettext("Calendar"), "calendar", "?mod=calendar");
			}

			/* module notes */
			$navigation->addNavigationItem(gettext("Notes"), "notes", "?mod=note");

			/* module notes */
			$navigation->addNavigationItem(gettext("Todos"), "todo", "?mod=todo");

			/* email and filesystem */
			if (!$GLOBALS["covide"]->license["disable_basics"] || $GLOBALS["covide"]->license["has_cms"]) {
				$navigation->addNavigationItem(gettext("Email"), "email", "?mod=email");
				$navigation->addNavigationItem(gettext("File management"), "filemanagement", "?mod=filesys");
			}
			/* module project, finance/invoice */
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				if ($GLOBALS["covide"]->license["has_project"]) {
					$navigation->addNavigationItem(gettext("Projects"), "projects", "?mod=project");
				}

				if ($userdata->checkPermission("xs_turnovermanage")) {
					if ($GLOBALS["covide"]->license["has_finance"] && !$GLOBALS["covide"]->license["has_factuur"]) {
						$navigation->addNavigationItem(gettext("Finance Old"), "finance", "./non-oop/finance/");
					} elseif ($GLOBALS["covide"]->license["has_factuur"]) {
						$navigation->addNavigationItem(gettext("Finance"), "finance", "?mod=finance");
					}
				}
				/* module sales */

				if ($GLOBALS["covide"]->license["has_sales"]) {
					$navigation->addNavigationItem(gettext("Sales"), "sales", "?mod=sales");
				}

				/* module issues */
				if ($GLOBALS["covide"]->license["has_issues"]) {
					$navigation->addNavigationItem(gettext("Support"), "support", "?mod=support");
				}

				/* module campaigns */
				if ($GLOBALS["covide"]->license["has_campaign"]) {
					$navigation->addNavigationItem(gettext("Campaign"), "campaigns", "?mod=campaign");
				}

				/* module snack */
				if (date("w") == $GLOBALS["covide"]->license["has_snack"]) {
					$navigation->addNavigationItem(gettext("Snacks"), "module_snack", "?mod=snack");
				}
			}

			$navigation->generateNavigationItems();
			$this->addCode($navigation->generate_output());

			$table = new Layout_table();
			$table->addTableRow();
				$table->addTableData();

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
									$table->addCode($username);
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

							if (!$GLOBALS["covide"]->mobile) {
								$table->load_javascript("classes/voip/inc/active_calls.js");
							}
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
								$table->addCode($username);
							$table->endTag("b");
						$table->endTag("span");
						$table->addTag("br");
					}
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();

			//$this->addCode( $table->generate_output() );
		}
		$this->endTag("div");
		$this->endTag("div");
		unset($table);
	}

	$this->addComment("begin code");
	$this->addTag("div", array("id" => "contentcontainer"));
?>
