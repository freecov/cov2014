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

		$table = new Layout_table(array("width" => "100%", "cellspacing" => 1));
		$table->addTableRow();
		$table->addTableData(array("width" => "33%", "style" => "vertical-align: top;"));

		$venster = new Layout_venster();
		$venster->addVensterData(array("width"=>"100%") );
			/* alerts */
			$venster->addCode("<b>".gettext("alerts")."</b><br><br>");
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
			if ($todoalertcount) {
				if ($todoalertcount == 1) {
					$venster->addCode($todoalertcount." ".gettext("important to do"));
				} else {
					$venster->addCode($todoalertcount." ".gettext("important to do's"));
				}
				$venster->addCode("<br>");
				$venster->insertAction("go_alert", gettext("To to dos"), "index.php?mod=todo");
				$venster->insertLink(gettext("To to dos"), array(
					"href" => "index.php?mod=todo"
				));
				$venster->addCode("<br>");
				$venster->addCode("<br>");
			}
			if ($supportinfo["count"]) {
				if ($supportinfo["count"] == 1) {
					$venster->addCode($supportinfo["count"]." ".gettext("current issue"));
				} else {
					$venster->addCode($supportinfo["count"]." ".gettext("current issues"));
				}
				$venster->addCode("<br>");
				$venster->insertAction("go_support", gettext("to issues/support"), "index.php?mod=support");
				$venster->insertLink(gettext("to issues/support"), array(
					"href" => "index.php?mod=support"
				));
				$venster->addCode("<br>");
				$venster->addCode("<br>");
				$alertcount++;
			}
			if ($projectinfo) {
				foreach($projectinfo AS $project) {
							$venster->addCode(gettext("Project")." ");
							$venster->insertLink($project["name"], array(
								"href" => "index.php?mod=project&action=showhours&id=".$project["id"]
							));
							$venster->addCode(" ".gettext("has exceeded limitations"));
							$venster->addCode("<br>");
							$alertcount++;
				}
			}
			if ($supportcalls["count"] && $userinfo["xs_issuemanage"]) {
				if ($supportcalls["count"] == 1) {
					$venster->addCode($supportcalls["count"]." ".gettext("current support call"));
				} else {
					$venster->addCode($supportcalls["count"]." ".gettext("current support calls"));
				}
				$venster->addCode("<br>");
				$venster->insertAction("important", gettext("to support calls"), "index.php?mod=support&action=list_external");
				$venster->insertLink(gettext("to support calls"), array(
					"href" => "index.php?mod=support&action=list_external"
				));
				$venster->addCode("<br>");
				$venster->addCode("<br>");
				$alertcount++;
			}
			if ($alertcount == 0) {
				$venster->addCode(gettext("there are no alerts at the moment"));
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
			if (!$GLOBALS["covide"]->license["disable_basics"]) {
				$venster->addTag("br");
				$venster->addTag("br");
				/* calendar items */
				$venster->addCode("<b>".gettext("calendar")."</b><br><br>");
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
					$venster->addCode(gettext("no active calendar items.")."<br>");
				}
				$venster->addCode("<br>");
				$venster->insertAction("go_calendar", gettext("to calendar"), "index.php?mod=calendar");
				$venster->insertLink(gettext("To calendar"), array(
					"href" => "index.php?mod=calendar"
				));
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode("<b>".gettext("email")."</b><br><br>");
				if (!$email_info["count"]) {
					$venster->addCode(gettext("No unread email")."<br><br>");
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
		$venster->addCode("<b>".gettext("notes")."</b>");
		if ($note_count["active"]) {
			$venster->addCode("<br><br>".gettext("there are")." ".$note_count["active"]." ".gettext("active notes."));
			if ($note_count["new"]) {
				$venster->addCode("<br>".gettext("of which")." ".$note_count["new"]." ".gettext("unread notes."));
			}
		} else {
			$venster->addCode("<br><br>".gettext("no active notes."));
		}
		$venster->addCode("<br><br>");
		$venster->insertAction("go_note", gettext("to notes"), "index.php?mod=note");
		$venster->insertLink(gettext("To notes"), array(
			"href" => "index.php?mod=note"
		));

		if (!$GLOBALS["covide"]->license["disable_basics"]) {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("<b>".gettext("to do's")."</b>");

			if (count($todo_arr)) {
				$venster->addTag("br");
				$venster->addTag("br");
				foreach($todo_arr as $v) {
					if ($v["is_alert"]) {
						$venster->addTag("b");
					}
					$item  = $v["desktop_time"]." - ".($v["is_active"]?"[A]":"[P]")." (".$v["priority"].") ".$v["subject"]." ";
					$venster->addCode($item);
					if ($v["is_alert"]) {
						$venster->endTag("b");
					}
					$venster->addTag("br");
				}
			} else {
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("no actual to-dos")."<br>");
			}

			$venster->addCode("<br>");
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

		if ($userinfo["rssnews"] || $userinfo["dayquote"] || $GLOBALS["covide"]->license["has_cms"]) {
			$table->addTableData(array("width" => "33%", "style" => "vertical-align: top;"));
			$venster = new Layout_venster();
			$venster->addVensterData( array("width"=>"100%") );
		} else {
			$table->addTableData(array("width" => "1%", "style" => "vertical-align: top;"));
			$table->addSpace(2);
		}
		if ($userinfo["rssnews"]) {
			$rss_data = new Rss_data();
			if($_REQUEST["refreshRSS"]) {
				$rss_data->updateFeeds();
			}
			$venster->addCode("<b>".gettext("news")."</b>");
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
			//$venster->insertAction("toggle", gettext("refresh"), "index.php?mod=desktop&refreshRSS=true");
			$venster->insertAction("edit", gettext("edit"), "index.php?mod=rss&action=listFeeds");
		}
		if ($userinfo["dayquote"]) {
			if ($userinfo["rssnews"]) {
				$venster->addTag("br");
				$venster->addTag("br");
			}
			$venster->addCode("<b>".gettext("quote of the day")."</b>");
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
		if ($userinfo["rssnews"] || $userinfo["dayquote"] || $GLOBALS["covide"]->license["has_cms"]) {
			$venster->endVensterData();
			$table->addCode ($venster->generate_output(1) );
			unset($venster);
		}

		$table->endTableData();
		$table->endTableRow();

		/* birthdays */
		$address = new Address_data();
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
							if ($v["days"] > 0)
								$venster->addCode(sprintf(" %s %d %s", gettext("in"), $v["days"], gettext("days")));

							$venster->addTag("br");
						}
					$venster->endVensterData();
					$table->addCode($venster->generate_output());
					unset($venster);
				$table->endTableData();
			$table->endTableRow();
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
		$output->addCode($table->generate_output());

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
		$output = new Layout_output();
		$output->layout_page("", 1);
		$table = new Layout_table(array("width"=>"100%"));
		foreach ($alertinfo as $k=>$v) {
			switch ($k) {
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
							$table->insertTableData($mailcount." ".gettext("new mail in folder")." ".$mailbox."<br>", "", "data");
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
}
?>
