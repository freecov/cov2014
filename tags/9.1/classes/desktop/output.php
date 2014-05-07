<?php
/**
 * Covide Groupware-CRM Desktop output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Desktop_output {
	/* constants */
	const include_dir = "classes/desktop/inc/";

	/* methods */

	/* edit_notes() {{{ */
	/**
	 * Generate screen to edit personal notes on the desktop
	 */
	public function edit_notes() {
		/* init the data object */
		$desktop_data = new Desktop_data();
		$notes = $desktop_data->getOwnNotes($_SESSION["user_id"]);
		/* output */
		$output = new Layout_output();
		$output->layout_page("", 1);
			$output->addTag("form", array(
				"id"     => "editownnotes",
				"method" => "post",
				"action" => "index.php"
			));
			$output->addHiddenField("mod", "desktop");
			$output->addHiddenField("action", "savenotes");
			$output->addHiddenField("user_id", $_SESSION["user_id"]);
			/* window widget */
			$venster = new Layout_venster(array(
				"title" => gettext("personal notes")
			));
			$venster->addVensterData();

				$venster->addTextArea("contents", $notes, array(
					"style" => "width: 700px; height: 400px;"
				));
				$editor = new Layout_editor();
				$venster->addCode( $editor->generate_editor("", $notes) );
				$venster->addTag("br");
				$venster->insertAction("save", gettext("save"), "javascript: save_notes();");

			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
			$output->endTag("form");
			$output->load_javascript(self::include_dir."ownnotes.js");
		/* end page and flush */
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* show_desktop() {{{ */
	/**
	 * Generate desktop
	 */
	public function show_desktop() {
		/* get user settings */
		$user_data = new User_data();
		$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
		/* get note data */
		$note_data = new Note_data();
		$note_count = $note_data->getNotecountByUserId($_SESSION["user_id"]);
		/* get calendar data */
		if (!$GLOBALS["covide"]->license["disable_basics"]) {
			$calendar_data = new Calendar_data();
			$items_arr = $calendar_data->_get_appointments($_SESSION["user_id"], date("m"), date("d"), date("Y"));
			/* todo data */
			$todo_data = new Todo_data();
			$todo_arr = $todo_data->getTodosByUserId($_SESSION["user_id"], 0, 1);
		}
		/* rss and email need desktop data object */
		$desktop_data = new Desktop_data();
		$email_info = $desktop_data->getMailInfo($_SESSION["user_id"]);
		/* get alertinfo */
		$alertinfo = $desktop_data->getAlertInfo(1);

		if ($GLOBALS["covide"]->license["has_issues"] && !$GLOBALS["covide"]->license["disable_basics"]) {
			$support_data = new Support_data();

			/* support issues */
			$supportinfo = $support_data->getSupportItems(array("user_id" => $_SESSION["user_id"], "active" => 1, "nolimit" => 1));

			/* support calls */
			$supportcalls["count"] = count($support_data->getExternalIssues());
		}
			$project_data = new Project_data();

			/* project info */
			$projectinfo = $project_data->getExceededProjects($_SESSION["user_id"]);

		if ($GLOBALS["covide"]->license["has_voip"]) {
			/* fax info */
			$voip_data = new Voip_data();
			$faxinfo   = $voip_data->getFaxes();
		}

		/* start defining the desktop */
		$output = new Layout_output();
		$output->Layout_page();

		if (1 == 0) {
		$table = new Layout_table(array("width" => "100%", "cellspacing" => 1));
		$table->addTableRow();
		$table->addTableData(array("width" => "33%", "style" => "vertical-align: top;"));

		$venster = new Layout_venster();
		$venster->addVensterData(array("width"=>"100%") );
			/* alerts */
			$venster->insertTag("b", gettext("alerts"));
			$venster->addTag("br");
			$alertcount = 0;
			/* search for todo's with alert flag */
			$todoalertcount = 0;
			if (!is_array($todo_arr))
				$todo_arr = array();
			foreach ($todo_arr as $v) {
				if ($v["is_alert"]) {
					$alertcount++;
					$todoalertcount++;
				}
			}
			// crm forms
			if (is_array($alertinfo["crmforms"])) {
				$alertcount++;
				if ($alertinfo["crmforms"]["count"] == 1) {
					$venster->addCode($alertinfo["crmforms"]["count"]." ".gettext("new cms/crm form submission"));
				} else {
					$venster->addCode($alertinfo["crmforms"]["count"]." ".gettext("new cms/crm form submissions"));
				}
				$venster->addTag("br");
				$venster->insertAction("go_addressbook", gettext("To crm forms"), "index.php?mod=address&addresstype=relations&cmsforms=1");
				$venster->insertLink(gettext("To crm forms"), array(
					"href" => "index.php?mod=address&addresstype=relations&cmsforms=1"
				));
				$venster->addTag("br");
				$venster->addTag("br");			
			}
			if ($todoalertcount) {
				if ($todoalertcount == 1) {
					$venster->addCode($todoalertcount." ".gettext("important to do"));
				} else {
					$venster->addCode($todoalertcount." ".gettext("important to do's"));
				}
				$venster->addTag("br");
				$venster->insertAction("go_alert", gettext("To to dos"), "index.php?mod=todo");
				$venster->insertLink(gettext("To to dos"), array(
					"href" => "index.php?mod=todo"
				));
				$venster->addTag("br");
				$venster->addTag("br");
			}
			if ($supportinfo["count"]) {
				if ($supportinfo["count"] == 1) {
					$venster->addCode($supportinfo["count"]." ".gettext("current issue"));
				} else {
					$venster->addCode($supportinfo["count"]." ".gettext("current issues"));
				}
				$venster->addTag("br");
				$venster->insertAction("go_support", gettext("to issues/support"), "index.php?mod=support");
				$venster->insertLink(gettext("to issues/support"), array(
					"href" => "index.php?mod=support"
				));
				$venster->addTag("br");
				$venster->addTag("br");
				$alertcount++;
			}
			if ($projectinfo) {
				foreach($projectinfo AS $project) {
							$venster->addCode(gettext("Project")." ");
							$venster->insertLink($project["name"], array(
								"href" => "index.php?mod=project&action=showhours&id=".$project["id"]
							));
							$venster->addCode(" ".gettext("has exceeded limitations"));
							$venster->addTag("br");
							$alertcount++;
				}
			}
			if ($supportcalls["count"] && $userinfo["xs_issuemanage"]) {
				if ($supportcalls["count"] == 1) {
					$venster->addCode($supportcalls["count"]." ".gettext("current external support call"));
				} else {
					$venster->addCode($supportcalls["count"]." ".gettext("current external support calls"));
				}
				$venster->addTag("br");
				$venster->insertAction("important", gettext("to external support calls"), "index.php?mod=support&action=list_external");
				$venster->insertLink(gettext("to external support calls"), array(
					"href" => "index.php?mod=support&action=list_external"
				));
				$venster->addTag("br");
				$venster->addTag("br");
				$alertcount++;
			}
			if ($GLOBALS["covide"]->license["has_voip"] && $faxinfo["count"]) {
				$venster->addTag("br");
				$venster->addTag("br");
				if ($faxinfo["count"] == 1) {
					$venster->addCode(gettext("there is 1 fax"));
				} else {
					$venster->addCode(gettext("there are")." ".$faxinfo["count"]." ".gettext("faxes"));
				}
				$venster->addTag("br");
				$venster->insertAction("go_fax", gettext("To faxes"), "index.php?mod=voip&action=faxlist");
				$venster->insertLink(gettext("To faxes"), array(
					"href" => "index.php?mod=voip&action=faxlist"
				));
			}
			/* Campaign recall alerts */
			$campaign_data = new Campaign_data();
			$recalls = $campaign_data->campaignHasRecalls();
			if ($recalls && $userinfo["xs_campaignmanage"]) {
				$venster->addTag("br");
				if ($recalls == 1) {
					$venster->addCode(gettext("There is 1 recall possibility").".");
				} else {
					$venster->addCode(gettext("There are")." ".$recalls." ".gettext("recall possibilities").".");
				}
				$venster->addTag("br");
				$venster->insertAction("data_private_telephone", gettext("To campaigns"), "index.php?mod=campaign");
				$venster->insertLink(gettext("To campaigns"), array(
					"href" => "index.php?mod=campaign"
				));
				$alertcount++;
			}
			
			if ($alertcount == 0) {
				$venster->addTag("br");
				$venster->addCode(gettext("there are no alerts at the moment"));
			}
			
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				$venster->addTag("br");
				$venster->addTag("br");
				/* calendar items */
				$venster->insertTag("b", gettext("calendar"));
				$venster->addTag("br");
				$venster->addTag("br");
				if (count($calendar_data->calendar_items)) {
					foreach($calendar_data->calendar_items as $v) {
						if (!$v["subject"]) {
							$v["subject"] = substr(strip_tags($v["body"]), 0, 25);
						}
						$item  = $v["human_span_short"]." ";
						$venster->addCode($item);
						if ($v["important"]) {
							$venster->insertAction("important", gettext("important meeting"), "", "");
							$venster->addTag("b");
						}
						if ($v["is_private"]) {
							$venster->insertAction("state_private", gettext("private appointment"), "", "");
						}
						$venster->addSpace("2");
						$item = $v["subject"];
						$venster->addCode($item);
						if ($v["important"])
							$venster->endTag("b");
						$venster->addTag("br");
					}
				} else {
					$venster->addCode(gettext("no active calendar items."));
					$venster->addTag("br");
				}
				$venster->addTag("br");
				$venster->insertAction("go_calendar", gettext("to calendar"), "index.php?mod=calendar");
				$venster->insertLink(gettext("To calendar"), array(
					"href" => "index.php?mod=calendar"
				));
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertTag("b", gettext("email"));
				$venster->addTag("br");
				$venster->addTag("br");
				if (!$email_info["count"]) {
					$venster->addCode(gettext("No unread email"));
					$venster->addTag("br");
					$venster->addTag("br");
				} else {
					$venster->addCode(gettext("Unread email in the following folders")." (".$email_info["count"]." ".gettext("total").")");
					$venster->addTag("br");
					foreach ($email_info["folders"] as $v) {
						$venster->addSpace(2);
						$venster->insertTag("a", gettext($v["name"]), array(
							"href" => "?mod=email&amp;folder_id=".$v["id"]
						));

						$venster->addCode(" (".$v["unread"].")");
						$venster->addTag("br");
					}
					$venster->addTag("br");
				}
				$venster->insertAction("go_email", gettext("to email"), "index.php?mod=email");
				$venster->insertLink(gettext("To email"), array(
					"href" => "index.php?mod=email"
				));
			}
		$venster->endVensterData();
		$table->addCode ($venster->generate_output() );
		unset($venster);
		$table->endTableData();
		$table->addTableData(array("width" => "33%", "style" => "vertical-align: top"));
		/* note items */
		$venster = new Layout_venster();
		$venster->addVensterData( array("width"=>"100%") );
		
		$venster->insertTag("b", gettext("notes"));
		if ($note_count["active"]) {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("there are")." ".$note_count["active"]." ");
			$venster->insertLink(gettext("active notes."), array(
				"href" => "index.php?mod=note"
			));
			if ($note_count["new"]) {
				$venster->addTag("br");
				$venster->addCode(gettext("of which")." ".$note_count["new"]." ".gettext("unread notes."));
			}
			if ($note_count["draft"]) {
				$venster->addTag("br");
				$venster->addCode(gettext("and there are")." ".$note_count["draft"]." ");
				$venster->insertLink(gettext("draft notes"), array(
					"href" => "index.php?mod=note&action=drafts"
				));
			}
		} else {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("no active notes."));
		}
		$venster->addTag("br");
		$venster->addTag("br");
		$venster->insertAction("go_note", gettext("to notes"), "index.php?mod=note");
		$venster->insertLink(gettext("To notes"), array(
			"href" => "index.php?mod=note"
		));

		if (!$GLOBALS["covide"]->license["disable_basics"]) {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertTag("b", gettext("to do's"));

			if (count($todo_arr)) {
				$venster->addTag("br");
				$venster->addTag("br");
				foreach($todo_arr as $v) {
					if ($v["overdue"])
						$venster->addTag("font", array("color" => "red"));

					if ($v["is_alert"])
						$venster->addTag("b");

					$item  = $v["desktop_time"]." - ".($v["is_active"]?"[A]":"[P]")." (".$v["priority"].") ".$v["subject"]." ";
					$venster->addCode($item);

					if ($v["is_alert"])
						$venster->endTag("b");

					if ($v["overdue"])
						$venster->endTag("font");
					$venster->addTag("br");
				}
			} else {
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("no actual to-dos"));
				$venster->addTag("br");
			}

			$venster->addTag("br");
			$venster->insertAction("go_todo", gettext("to to-dos"), "index.php?mod=todo");
			$venster->insertLink(gettext("To to dos"), array(
				"href" => "index.php?mod=todo"
			));
			if ($userinfo["alternative_note_view_desktop"]) {
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertAction("edit", gettext("To personal notes"), "javascript: toggle_notes('active');");
				$venster->insertLink(gettext("To personal notes"), array(
					"href" => "javascript: toggle_notes('active');"
				));
			}
		}
		$venster->endVensterData();
		$table->addCode ($venster->generate_output() );
		unset($venster);
		$table->endTableData();

		if ($userinfo["rssnews"] || $userinfo["dayquote"]
			|| $GLOBALS["covide"]->license["has_cms"]) {

			$table->addTableData(array("width" => "33%", "style" => "vertical-align: top;"));
			$venster = new Layout_venster();
			$venster->addVensterData( array("width"=>"100%") );
		} else {
			$table->addTableData(array("width" => "1%", "style" => "vertical-align: top;"));
			$table->addSpace(2);
		}
		if ($userinfo["rssnews"]) {
			$rss_data = new Rss_data();
			if ($_REQUEST["refreshRSS"]) {
				$rss_data->updateFeeds();
			}
			$venster->insertTag("b", gettext("news"));
			$venster->addTag("br");
			$rssfeeds = $rss_data->getFeeds($_SESSION["user_id"],0,1);
			$venster->addTag("br");
			$conversion = new Layout_conversion();
			if (!is_array($rssfeeds)) $rssfeeds = array();
			foreach ($rssfeeds as $v) {
				$venster->addTag("b");
				$venster->addCode($v["name"]);
				$venster->endTag("b");
				$venster->addTag("br");
				$rssitems = $desktop_data->getRSSitems($v["id"], $v["count"]);
				if (is_array($rssitems)) {
					foreach ($rssitems as $item) {
						$venster->addTag("i");
						$title = $item["subject"]." (".date("d-m-Y", $item["date"]).")";
						$venster->insertLink($title, array(
							"href"   => $item["link"],
							"target" => "_blank"
						));
						$venster->endTag("i");
						$venster->addTag("br");
					}
				}
			}
			$venster->addTag("br");
			$venster->insertAction("edit", gettext("edit"), "index.php?mod=rss&action=listFeeds");
		}
		if ($userinfo["dayquote"]) {
			if ($userinfo["rssnews"]) {
				$venster->addTag("br");
				$venster->addTag("br");
			}
			$venster->insertTag("b", gettext("quote of the day"));
			$venster->addTag("br");
			$venster->addCode(nl2br( $GLOBALS["covide"]->license["dayquote_nr"] ));
		}

		if ($GLOBALS["covide"]->license["has_cms"]) {
			$cms_data = new Cms_data();
			$cms_info = $cms_data->getInternalStartPoints();
			foreach ($cms_info as $k=>$v) {
				$cmsitems++;
				if ($cmsitems == 1) {
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->insertTag("b", gettext("CMS document roots"));
					$venster->addTag("br");
					$venster->addTag("br");
				}
				$venster->addCode($v["date"]." - ");
				$venster->insertTag("a", $v["name"], array(
					"target" => "_blank",
					"href"   => $v["link"]
				));
				$venster->addTag("br");
			}
		}
		if ($userinfo["rssnews"] || $userinfo["dayquote"]
			|| $GLOBALS["covide"]->license["has_cms"]
			|| (is_array($finance_items) && count($finance_items) > 0)) {

			$venster->endVensterData();
			$table->addCode ($venster->generate_output(1) );
			unset($venster);
		}

		$table->endTableData();
		$table->endTableRow();

		/* birthdays */
		if ($userinfo["showbdays"]) {
			$address = new Address_data();
			$ebd = $address->getEmployeeBirthdays();
			if (count($ebd)>0) {
				$table->addTableRow();
					$table->addTableData(array("colspan" => 3));
						$venster = new Layout_venster();
						$venster->addVensterData();
							$venster->insertTag("b", gettext("employees")." ".gettext("birthday"));
							$venster->addTag("br");
							$venster->addTag("br");
							foreach ($ebd as $k=>$v) {
								$venster->insertAction("data_birthday", gettext("go to contact card"), array("href"=>"?mod=address&action=usercard&id=".$v["id"]));
								$venster->addCode( " <a href='?mod=address&action=usercard&id=".$v["id"]."'>".$v["givenname"]." ".$v["surname"]."</a>" );

								$venster->addCode( " ".gettext("turns")." ".$v["age"] );
								if ($v["days"] > 0) {
									$day = ($v["days"] == 1) ? gettext("day") : gettext("days");
									$venster->addCode(sprintf(" %s %d %s", gettext("in"), $v["days"], $day));
								}

								$venster->addTag("br");
							}
						$venster->endVensterData();
						$table->addCode($venster->generate_output());
						unset($venster);
					$table->endTableData();
				$table->endTableRow();
			}
			$bd = $address->getBirthdays();
			if (count($bd)>0) {
				$table->addTableRow();
					$table->addTableData(array("colspan" => 3));
						$venster = new Layout_venster();
						$venster->addVensterData();
							$venster->insertTag("b", gettext("birthdays"));
							$venster->addTag("br");
							$venster->addTag("br");
							foreach ($bd as $k=>$v) {
								$venster->insertAction("data_birthday", gettext("go to contact card"), array("href"=>"?mod=address&action=relcard&id=".$v["company_id"]));
								$venster->addCode( " ".$v["name"] );
								if ($v["company_name"]) {
									$venster->addCode(" (".gettext("from")." <a href='?mod=address&action=relcard&id=".$v["company_id"]."'>".$v["company_name"]."</a>)");
								}
								$venster->addCode( " ".gettext("turns")." ".$v["age"] );
								if ($v["days"] > 0) {
									$day = ($v["days"] == 1) ? gettext("day") : gettext("days");
									$venster->addCode(sprintf(" %s %d %s", gettext("in"), $v["days"], $day));
								}
								$venster->addTag("br");
							}
						$venster->endVensterData();
						$table->addCode($venster->generate_output());
						unset($venster);
					$table->endTableData();
				$table->endTableRow();
			}
		}

		if (!$GLOBALS["covide"]->license["disable_basics"]) {
			if (!$userinfo["alternative_note_view_desktop"]) {
				$table->addTableRow();
			} else {
				$table->addTableRow(array("style" => "visibility: hidden;", "id" => "ownnotes"));
			}
				$table->addTableData(array("colspan" => 3));
					$venster = new Layout_venster();
					$venster->addVensterData();
						$venster->addTag("b");
							$venster->addCode(gettext("personal notes"));
						$venster->endTag("b");
						$venster->addTag("br");
						$venster->addTag("br");
						$venster->addCode($desktop_data->getOwnNotes($_SESSION["user_id"]));
						$venster->addTag("br");
						$venster->insertAction("edit", gettext("change"), "javascript: popup('index.php?mod=desktop&action=editnotes', 'edit', 900, 600, 1);");
						if ($userinfo["alternative_note_view_desktop"]) {
							$venster->addSpace(2);
							$venster->insertAction("close", gettext("hide own notes"), "javascript: toggle_notes('nonactive');");
						}
					$venster->endVensterData();
					$table->addCode($venster->generate_output());
					unset($venster);
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
		//$output->addCode($table->generate_output());
		}
		

		/* Create alert block content */
		$alert_output = new Layout_output();
		$alertcount = 0;
		/* search for todo's with alert flag */
		$todoalertcount = 0;
		if (!is_array($todo_arr))
			$todo_arr = array();
		foreach ($todo_arr as $v) {
			if ($v["is_alert"]) {
				$alertcount++;
				$todoalertcount++;
			}
		}
		// crm forms
		if (is_array($alertinfo["crmforms"])) {
			$alertcount++;
			if ($alertinfo["crmforms"]["count"] == 1) {
				$alert_output->addCode($alertinfo["crmforms"]["count"]." ".gettext("new cms/crm form submission"));
			} else {
				$alert_output->addCode($alertinfo["crmforms"]["count"]." ".gettext("new cms/crm form submissions"));
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("go_addressbook", gettext("To crm forms"), "index.php?mod=address&addresstype=relations&cmsforms=1");
			$alert_output->insertLink(gettext("To crm forms"), array(
				"href" => "index.php?mod=address&addresstype=relations&cmsforms=1"
			));
			$alert_output->addTag("br");
			$alert_output->addTag("br");			
		}
		if ($todoalertcount) {
			if ($todoalertcount == 1) {
				$alert_output->addCode($todoalertcount." ".gettext("important to do"));
			} else {
				$alert_output->addCode($todoalertcount." ".gettext("important to do's"));
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("go_alert", gettext("To to dos"), "index.php?mod=todo");
			$alert_output->insertLink(gettext("To to dos"), array(
				"href" => "index.php?mod=todo"
			));
			$alert_output->addTag("br");
			$alert_output->addTag("br");
		}
		if ($supportinfo["count"]) {
			if ($supportinfo["count"] == 1) {
				$alert_output->addCode($supportinfo["count"]." ".gettext("current issue"));
			} else {
				$alert_output->addCode($supportinfo["count"]." ".gettext("current issues"));
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("go_support", gettext("to issues/support"), "index.php?mod=support");
			$alert_output->insertLink(gettext("to issues/support"), array(
				"href" => "index.php?mod=support"
			));
			$alert_output->addTag("br");
			$alert_output->addTag("br");
			$alertcount++;
		}
		if ($projectinfo) {
			foreach($projectinfo AS $project) {
						$alert_output->addCode(gettext("Project")." ");
						$alert_output->insertLink($project["name"], array(
							"href" => "index.php?mod=project&action=showhours&id=".$project["id"]
						));
						$alert_output->addCode(" ".gettext("has exceeded limitations"));
						$alert_output->addTag("br");
						$alertcount++;
			}
		}
		if ($supportcalls["count"] && $userinfo["xs_issuemanage"]) {
			if ($supportcalls["count"] == 1) {
				$alert_output->addCode($supportcalls["count"]." ".gettext("current external support call"));
			} else {
				$alert_output->addCode($supportcalls["count"]." ".gettext("current external support calls"));
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("important", gettext("to external support calls"), "index.php?mod=support&action=list_external");
			$alert_output->insertLink(gettext("to external support calls"), array(
				"href" => "index.php?mod=support&action=list_external"
			));
			$alert_output->addTag("br");
			$alert_output->addTag("br");
			$alertcount++;
		}
		if ($GLOBALS["covide"]->license["has_voip"] && $faxinfo["count"]) {
			$alert_output->addTag("br");
			$alert_output->addTag("br");
			if ($faxinfo["count"] == 1) {
				$alert_output->addCode(gettext("there is 1 fax"));
			} else {
				$alert_output->addCode(gettext("there are")." ".$faxinfo["count"]." ".gettext("faxes"));
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("go_fax", gettext("To faxes"), "index.php?mod=voip&action=faxlist");
			$alert_output->insertLink(gettext("To faxes"), array(
				"href" => "index.php?mod=voip&action=faxlist"
			));
		}
		/* Campaign recall alerts */
		$campaign_data = new Campaign_data();
		$recalls = $campaign_data->campaignHasRecalls();
		if ($recalls && $userinfo["xs_campaignmanage"]) {
			$alert_output->addTag("br");
			if ($recalls == 1) {
				$alert_output->addCode(gettext("There is 1 recall possibility").".");
			} else {
				$alert_output->addCode(gettext("There are")." ".$recalls." ".gettext("recall possibilities").".");
			}
			$alert_output->addTag("br");
			$alert_output->insertAction("data_private_telephone", gettext("To campaigns"), "index.php?mod=campaign");
			$alert_output->insertLink(gettext("To campaigns"), array(
				"href" => "index.php?mod=campaign"
			));
			$alertcount++;
		}
		
		if ($alertcount == 0) {
			$alert_output->addTag("br");
			$alert_output->addCode(gettext("there are no alerts at the moment"));
		}
		/* end alert block content */

		/* create calendar block content */
		$calendar_output = new Layout_output();
		if (count($calendar_data->calendar_items)) {
			foreach($calendar_data->calendar_items as $v) {
				if (!$v["subject"]) {
					$v["subject"] = substr(strip_tags($v["body"]), 0, 25);
				}
				$item  = $v["human_span_short"]." ";
				$calendar_output->addCode($item);
				if ($v["important"]) {
					$calendar_output->insertAction("important", gettext("important meeting"), "", "");
					$calendar_output->addTag("b");
				}
				if ($v["is_private"]) {
					$calendar_output->insertAction("state_private", gettext("private appointment"), "", "");
				}
				$calendar_output->addSpace("2");
				$item = $v["subject"];
				$calendar_output->addCode($item);
				if ($v["important"])
					$calendar_output->endTag("b");
				$calendar_output->addTag("br");
			}
		} else {
			$calendar_output->addCode(gettext("no active calendar items."));
			$calendar_output->addTag("br");
		}
		$calendar_output->addTag("br");
		$calendar_output->insertAction("go_calendar", gettext("to calendar"), "index.php?mod=calendar");
		$calendar_output->insertLink(gettext("To calendar"), array(
			"href" => "index.php?mod=calendar"
		));
		/* end calendar block content */

		/* create email block content */
		$email_output = new Layout_output();
		if (!$email_info["count"]) {
			$email_output->addCode(gettext("No unread email"));
			$email_output->addTag("br");
			$email_output->addTag("br");
		} else {
			$email_output->addCode(gettext("Unread email in the following folders")." (".$email_info["count"]." ".gettext("total").")");
			$email_output->addTag("br");
			foreach ($email_info["folders"] as $v) {
				$email_output->addSpace(2);
				$email_output->insertTag("a", gettext($v["name"]), array(
					"href" => "?mod=email&amp;folder_id=".$v["id"]
				));

				$email_output->addCode(" (".$v["unread"].")");
				$email_output->addTag("br");
			}
			$email_output->addTag("br");
		}
		$email_output->insertAction("go_email", gettext("to email"), "index.php?mod=email");
		$email_output->insertLink(gettext("To email"), array(
			"href" => "index.php?mod=email"
		));
		/* end email block content */

		/* create note block content */
		$note_output = new Layout_output();
		if ($note_count["active"]) {
			$note_output->addCode(gettext("there are")." ".$note_count["active"]." ");
			$note_output->insertLink(gettext("active notes."), array(
				"href" => "index.php?mod=note"
			));
			if ($note_count["new"]) {
				$note_output->addTag("br");
				$note_output->addCode(gettext("of which")." ".$note_count["new"]." ".gettext("unread notes."));
			}
			if ($note_count["draft"]) {
				$note_output->addTag("br");
				$note_output->addCode(gettext("and there are")." ".$note_count["draft"]." ");
				$note_output->insertLink(gettext("draft notes"), array(
					"href" => "index.php?mod=note&action=drafts"
				));
			}
		} else {
			$note_output->addCode(gettext("no active notes."));
		}
		$note_output->addTag("br");
		$note_output->addTag("br");
		$note_output->insertAction("go_note", gettext("to notes"), "index.php?mod=note");
		$note_output->insertLink(gettext("To notes"), array(
			"href" => "index.php?mod=note"
		));
		/* end note block content */

		/* create todo block content */
		$todo_output = new Layout_output();
		if (count($todo_arr)) {
			foreach($todo_arr as $v) {
				if ($v["overdue"])
					$todo_output->addTag("font", array("color" => "red"));

				if ($v["is_alert"])
					$todo_output->addTag("b");

				$item  = $v["desktop_time"]." - ".($v["is_active"]?"[A]":"[P]")." (".$v["priority"].") ".$v["subject"]." ";
				$todo_output->addCode($item);

				if ($v["is_alert"])
					$todo_output->endTag("b");

				if ($v["overdue"])
					$todo_output->endTag("font");
				$todo_output->addTag("br");
			}
		} else {
			$todo_output->addCode(gettext("no actual to-dos"));
			$todo_output->addTag("br");
		}

		$todo_output->addTag("br");
		$todo_output->insertAction("go_todo", gettext("to to-dos"), "index.php?mod=todo");
		$todo_output->insertLink(gettext("To to dos"), array(
			"href" => "index.php?mod=todo"
		));
		/* end todo block content */

		/* create rss block content */
		$rss_output = new Layout_output();
		$rss_data = new Rss_data();
		if ($_REQUEST["refreshRSS"]) {
			$rss_data->updateFeeds();
		}
		$rssfeeds = $rss_data->getFeeds($_SESSION["user_id"],0,1);
		$conversion = new Layout_conversion();
		if (!is_array($rssfeeds)) $rssfeeds = array();
		foreach ($rssfeeds as $v) {
			$rss_output->addTag("b");
			$rss_output->addCode($v["name"]);
			$rss_output->endTag("b");
			$rss_output->addTag("br");
			$rssitems = $desktop_data->getRSSitems($v["id"], $v["count"]);
			if (is_array($rssitems)) {
				foreach ($rssitems as $item) {
					$rss_output->addTag("i");
					$title = $item["subject"]." (".date("d-m-Y", $item["date"]).")";
					$rss_output->insertLink($title, array(
						"href"   => $item["link"],
						"target" => "_blank"
					));
					$rss_output->endTag("i");
					$rss_output->addTag("br");
				}
			}
		}
		$rss_output->addTag("br");
		$rss_output->insertAction("edit", gettext("edit"), "index.php?mod=rss&action=listFeeds");
		/* end rss block content */

		/* create qotd block content */
		$qotd_output = new Layout_output();
		$qotd_output->insertTag("b", gettext("quote of the day"));
		$qotd_output->addTag("br");
		$qotd_output->addCode(nl2br( $GLOBALS["covide"]->license["dayquote_nr"] ));
		/* end qotd block content */



		$output->addTag("ul",  array("id" => "add_dashboard"));
		$output->addTag("li");
			$output->insertLink("Dashboard", array("href" => "#dashboard1", "class" => "selected"));
		$output->endTag("li");
		$output->addTag("li");
			$output->insertLink("+ Add block", array("href" => "javascript:popup('?mod=desktop&action=showaddblock', 'addblock', 350, 100)", "class" => "add"));
		$output->endTag("li");
		$output->endTag("ul");

		$blocks = array(
			"alerts"        => $this->showBlock("block_alerts", gettext("alerts"), $alert_output->generate_output()),
			"calendar"      => $this->showBlock("block_calendar", gettext("calendar"), $calendar_output->generate_output()),
			"email"         => $this->showBlock("block_email", gettext("email"), $email_output->generate_output()),
			"notes"         => $this->showBlock("block_notes", gettext("notes"), $note_output->generate_output()),
			"todos"         => $this->showBlock("block_todos", gettext("to do's"), $todo_output->generate_output()),
			"news"          => $this->showBlock("block_news", gettext("news"), $rss_output->generate_output()),
			"quoteoftheday" => $this->showBlock("block_quoteoftheday", gettext("quote of the day"), $qotd_output->generate_output()),
			"own_notes"     => $this->showBlock("block_ownnotes", gettext("personal notes"), $desktop_data->getOwnNotes($_SESSION["user_id"])),
		);
		$dashboard_layout = unserialize($userinfo["dashboardlayout"]);
		if (!is_array($dashboard_layout)) {
			$dashboard_layout = array(
				"column1" => array(
					"alerts",
					"calendar",
					"email",
				),
				"column2" => array(
					"notes",
					"todos",
				),
				"column3" => array(
					"news",
					"quoteoftheday",
				),
			);
		}
		// start first dashboard
		$output->addCode("\n");
		$output->addCode("\n");
		$output->addComment("Start of dashboard 1");
		$output->addTag("div", array("id" => "dashboard1", "class" => "dashboard selected"));
		$output->addCode("\n");
			//content of dashboard goes here
			//wrapper for sortable stuff
			$output->addTag("div", array("class" => "sortable-container"));
				$output->addTag("div", array("id" => "column1", "class" => "col"));
					if (!is_array($dashboard_layout["column1"])) {
						$output->addCode($blocks["alerts"]);
						$output->addCode($blocks["calendar"]);
						$output->addCode($blocks["email"]);
					} else {
						foreach ($dashboard_layout["column1"] as $b) {
							if ($b == "spacer") continue;
							$output->addCode($blocks[$b]);
						}
					}
				$output->endTag("div");
				$output->addTag("div", array("id" => "column2", "class" => "col"));
					if (!is_array($dashboard_layout["column2"])) {
						$output->addCode($blocks["notes"]);
						$output->addCode($blocks["todos"]);
					} else {
						foreach ($dashboard_layout["column2"] as $b) {
							if ($b == "spacer") continue;
							$output->addCode($blocks[$b]);
						}
					}
				$output->endTag("div");
				$output->addTag("div", array("id" => "column3", "class" => "col"));
					if (!is_array($dashboard_layout["column3"])) {
						$output->addCode($blocks["news"]);
						$output->addCode($blocks["quoteoftheday"]);
					} else {
						foreach ($dashboard_layout["column3"] as $b) {
							if ($b == "spacer") continue;
							$output->addCode($blocks[$b]);
						}
					}
				$output->endTag("div");
				$output->addTag("div", array("id" => "column_ownnotes", "class" => "col"));
				$output->addCode($blocks["own_notes"]);
				$output->endTag("div");
			$output->endTag("div");
		$output->start_javascript();
		$output->addCode('
			function update_columns_by_id(el) {
				var order = $("#"+el).sortable("serialize");
				order = "block[]=spacer&"+order
				var col = el;
				loadXML("?mod=desktop&action=saveLayout&col="+col+"&"+order);
			}

			$(document).ready(function() {
				$(".block_options").click(function() {
					var store_col = $(this).parent("div").parent("div").parent("div").attr("id");
					$(this).parent("div").parent("div").remove();
					update_columns_by_id(store_col);
				});
				$("#column1").sortable({
					connectWith: ["#column2, #column3"],
					update: function () {
						update_columns_by_id(this.id);
					}
				});
				$("#column2").sortable({
					connectWith: ["#column1, #column3"],
					update: function () {
						update_columns_by_id(this.id);
					}
				});
				$("#column3").sortable({
					connectWith: ["#column1, #column2"],
					update: function () {
						update_columns_by_id(this.id);
					}
				});
			});
		');
		$output->end_javascript();
		// end and close first dashboard
		$output->addCode("\n");
		$output->endTag("div");
		$output->addCode("\n");
		$output->addComment("End of dashboard 1");
		$output->addCode("\n");

		$output->start_javascript();
		$output->addCode("var desktop_autorefresh = setTimeout('location.href=\'./?mod=desktop\';', 5*60*1000);");
		$output->end_javascript();
		if ($userinfo["alternative_note_view_desktop"]) {
			$output->load_javascript(self::include_dir."desktop.js");
		}
		$output->Layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* showBlock {{{ */
	/**
	 * Create a sortable/draggable block on the dashboard
	 *
	 * @param string $name Name/id of the block
	 * @param string $subject Title of the block
	 * @param string $content Content of the block
	 *
	 * @return string The html for a block
	 */
	public function showBlock($name, $subject = "", $content = "") {
		$output = new Layout_output();
		$output->addTag("div", array("id" => $name, "class" => "block"));
			$output->addTag("div", array("class" => "block_header"));
				$output->addTag("div", array("class" => "block_title"));
					$output->insertTag("h4", $subject);
				$output->endTag("div");
				$output->addTag("div", array("class" => "block_options", "id" => $name."_options"));
					//$output->insertImage("icons/ui/hide.png", gettext("toggle"), "#", 1);
					$output->insertImage("icons/ui/remove.png", gettext("delete"), "", 1);
					$output->addSpace(2);
				$output->endTag("div");
			$output->endTag("div");
			$output->addTag("div", array("class" => "block_content"));
				$output->addCode($content);
			$output->endTag("div");
		$output->endTag("div");
		return $output->generate_output();
	}
	/* }}} */
	/* show_alerts {{{ */
	/**
	 * Show screen with alert info and links to the modules
	 */
	public function show_alerts() {
		/* get alert info */
		$desktop_data = new Desktop_data();
		$alertinfo = $desktop_data->getAlertInfo();
		if (!is_array($alertinfo)) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("window.close();");
			$output->end_javascript();
			$output->exit_buffer();
			return 0;
		}
		$_SESSION["alertmd5"] = md5(serialize($alertinfo));
		$_SESSION["alert_sent"] = true;
		$output = new Layout_output();
		$output->layout_page("", 1);
		$table = new Layout_table(array("width"=>"100%"));
		foreach ($alertinfo as $k=>$v) {
			switch ($k) {
				case "crmforms" :
					$table->addTableRow();
						$table->insertTableData(gettext("cms/crm forms"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData($v["count"]." ".gettext("cms/crm forms"), "", "data");
					$table->endTableRow();
					break;
				case "supportcalls" :
					$table->addTableRow();
						$table->insertTableData(gettext("support calls"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData($v." ".gettext("current support calls"), "", "data");
					$table->endTableRow();
					break;
				case "email" :
					/* email has some extra info */
					$table->addTableRow();
						$table->insertTableData(gettext("email"), "", "header");
					$table->endTableRow();
					foreach ($v as $mailbox=>$mailcount) {
						$table->addTableRow();
							$table->insertTableData($mailcount." ".gettext("new mail in folder")." ".$mailbox, "", "data");
						$table->endTableRow();
					}
					break;
				case "notes" :
					$table->addTableRow();
						$table->insertTableData(gettext("notes"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData($v." ".gettext("new notes"), "", "data");
					$table->endTableRow();
					break;
				case "todo" :
					$table->addTableRow();
						$table->insertTableData(gettext("to dos"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData($v." ".gettext("important todos"), "", "data");
					$table->endTableRow();
					break;
				case "calendar" :
					$table->addTableRow();
						$table->insertTableData(gettext("calendar"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData($v." ".gettext("important calendar items"), "", "data");
					$table->endTableRow();
					break;
			}
		}
		$table->endTable();
		$frame = new Layout_venster(array("title"=>gettext("alerts")));
		$frame->addVensterData();
		$frame->addCode($table->generate_output());
		unset($table);
		$frame->endVensterData();
		$output->addCode($frame->generate_output());
		unset($frame);
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* showAddBlock {{{ */
	/**
	 * Code to show popup where a user can choose what block to add
	 * For now, we only support our own blocks, but in future we will
	 * support that a user can create their own blocks
	 */
	public function showAddBlock() {
		/* what blocks we support in the core */
		$blocks = array(
			"alerts"        => gettext("alerts"),
			"calendar"      => gettext("calendar"),
			"email"         => gettext("email"),
			"notes"         => gettext("notes"),
			"todos"         => gettext("to do's"),
			"news"          => gettext("news"),
			"quoteoftheday" => gettext("quote of the day"),
		);

		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array("title" => gettext("Dashboard"), "subtitle" => gettext("Add block")));
		$venster->addVensterData();
			$venster->insertTag("p", gettext("Select a block you want to add to the dashboard"));
			$venster->addTag("p");
			$venster->addTag("form", array(
				"id" => "addblockform",
				"method" => "post",
				"action" => "index.php",
			));
			$venster->addHiddenField("mod", "desktop");
			$venster->addHiddenField("action", "saveaddblock");
			$venster->addHiddenField("dashboard[user_id]", $_SESSION["user_id"]);
			$venster->addSelectField("dashboard[addblock]", $blocks);
			$venster->insertAction("save", gettext("save"), "javascript: saveBlocks();");
			$venster->endTag("form");
			$venster->endTag("p");
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->start_javascript();
		$output->addCode("
			function saveBlocks() {
				document.getElementById('addblockform').submit();
			}
		");
		$output->end_javascript();
		$output->layout_page_end(1);
		$output->exit_buffer();
	}
	/* }}} */
}
?>
