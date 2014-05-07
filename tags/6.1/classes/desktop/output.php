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
 * @copyright Copyright 2000-2006 Covide BV
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
				"title" => gettext("eigen aantekeningen")
			));
			$venster->addVensterData();

				$venster->addTextArea("contents", $notes, array(
					"style" => "width: 700px; height: 400px;"
				));
				$editor = new Layout_editor();
				$venster->addCode( $editor->generate_editor("", $notes) );
				$venster->addTag("br");
				$venster->insertAction("save", gettext("opslaan"), "javascript: save_notes();");

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
		$calendar_data = new Calendar_data();
		$items_arr = $calendar_data->_get_appointments($_SESSION["user_id"], date("m"), date("d"), date("Y"));
		/* todo data */
		$todo_data = new Todo_data();
		$todo_arr = $todo_data->getTodosByUserId($_SESSION["user_id"]);
		/* rss and email need desktop data object */
		$desktop_data = new Desktop_data();
		$email_info = $desktop_data->getMailInfo($_SESSION["user_id"]);
		if ($GLOBALS["covide"]->license["has_support"]) {
			/* support issues */
			$support_data = new Support_data();
			$supportinfo = $support_data->getSupportItems(array("user_id" => $_SESSION["user_id"], "active" => 1, "nolimit" => 1));
		}
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

		/* calendar items */
		$venster = new Layout_venster();
		$venster->addVensterData(array("width"=>"100%") );
			$venster->addCode("<b>".gettext("alerts")."</b><br><br>");
			$alertcount = 0;
			/* search for todo's with alert flag */
			$todoalertcount = 0;
			foreach ($todo_arr as $v) {
				if ($v["is_alert"]) {
					$alertcount++;
					$todoalertcount++;
				}
			}
			if ($todoalertcount) {
				if ($todoalertcount == 1) {
					$venster->addCode($todoalertcount." ".gettext("belangrijke todo"));
				} else {
					$venster->addCode($todoalertcount." ".gettext("belangrijke todo's"));
				}
				$venster->addCode("<br><br>");
				$venster->insertAction("go_alert", gettext("naar todo's"), "index.php?mod=todo");
				$venster->insertLink(gettext("naar todo's"), array(
					"href" => "index.php?mod=todo",
					"alt"  => gettext("naar todo's")
				));
			}
			if ($supportinfo["count"]) {
				if ($supportinfo["count"] == 1) {
					$venster->addCode($supportinfo["count"]." ".gettext("actuele klachten"));
				} else {
					$venster->addCode($supportinfo["count"]." ".gettext("actuele klacht"));
				}
				$venster->addCode("<br><br>");
				$venster->insertAction("go_support", gettext("naar klachten/support"), "index.php?mod=support");
				$venster->insertLink(gettext("naar klachten/support"), array(
					"href" => "index.php?mod=support",
					"alt"  => gettext("naar klachten/support")
				));
				$alertcount++;
			}
			if ($alertcount == 0) {
				$venster->addCode(gettext("er zijn momenteel geen alerts"));
			}
			if ($GLOBALS["covide"]->license["has_voip"] && $faxinfo["count"]) {
				$venster->addTag("br");
				$venster->addTag("br");
				if ($faxinfo["count"] == 1) {
					$venster->addCode(gettext("er is 1 fax"));
				} else {
					$venster->addCode(gettext("er zijn")." ".$faxinfo["count"]." ".gettext("faxen"));
				}
				$venster->addTag("br");
				$venster->insertAction("go_fax", gettext("naar faxen"), "index.php?mod=voip&action=faxlist");
				$venster->insertLink(gettext("naar faxen"), array(
					"href" => "index.php?mod=voip&action=faxlist",
					"alt"  => gettext("naar faxen")
				));
			}
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("<b>".gettext("agenda")."</b><br><br>");
			if (count($calendar_data->calendar_items)) {
				foreach($calendar_data->calendar_items as $v) {
					if (!$v["subject"]) {
						$v["subject"] = substr(strip_tags($v["body"]), 0, 25);
					}
					$item  = $v["shuman"]." - ".$v["ehuman"]." ";
					$item .= $v["subject"];
					$venster->addCode($item."<br>");
				}
			} else {
				$venster->addCode(gettext("geen aktuele agenda punten.")."<br>");
			}
			$venster->addCode("<br>");
			$venster->insertAction("go_calendar", gettext("naar agenda"), "index.php?mod=calendar");
			$venster->insertLink(gettext("Naar agenda"), array(
				"href" => "index.php?mod=calendar",
				"alt"  => gettext("naar agenda")
			));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode("<b>".gettext("email")."</b><br><br>");
			if (!$email_info["count"]) {
				$venster->addCode(gettext("Geen ongelezen email.")."<br><br>");
			} else {
				$venster->addCode(gettext("Ongelezen email in de volgende mappen")." (".$email_info["count"]." ".gettext("totaal").")");
				$venster->addTag("br");
				foreach ($email_info["folders"] as $v) {
					$venster->addCode(gettext($v["name"])." (".$v["unread"].")");
					$venster->addTag("br");
				}
				$venster->addTag("br");
			}
			$venster->insertAction("go_email", gettext("naar email"), "index.php?mod=email");
			$venster->insertLink(gettext("Naar email"), array(
				"href" => "index.php?mod=email",
				"alt"  => gettext("naar email")
			));
		$venster->endVensterData();
		$table->addCode ($venster->generate_output() );
		unset($venster);
		$table->endTableData();
		$table->addTableData(array("width" => "33%", "style" => "vertical-align: top"));
		/* note items */
		$venster = new Layout_venster();
		$venster->addVensterData( array("width"=>"100%") );
		$venster->addCode("<b>".gettext("notities")."</b>");
		if ($note_count["active"]) {
			$venster->addCode("<br><br>".gettext("er zijn")." ".$note_count["active"]." ".gettext("aktuele notities."));
			if ($note_count["new"]) {
				$venster->addCode("<br>".gettext("waarvan")." ".$note_count["new"]." ".gettext("ongelezen notities."));
			}
		} else {
			$venster->addCode("<br><br>".gettext("geen aktuele notities."));
		}
		$venster->addCode("<br><br>");
		$venster->insertAction("go_note", gettext("naar notities"), "index.php?mod=note");
		$venster->insertLink(gettext("Naar notities"), array(
			"href" => "index.php?mod=note",
			"alt"  => gettext("naar notities")
		));
		$venster->addTag("br");
		$venster->addTag("br");
		$venster->addCode("<b>".gettext("todo's")."</b>");

		if (count($todo_arr)) {
			$venster->addTag("br");
			$venster->addTag("br");
			foreach($todo_arr as $v) {
				if ($v["is_alert"]) {
					$venster->addTag("b");
				}
				$item  = $v["desktop_time"]." - ".$v["subject"]." ";
				$venster->addCode($item);
				if ($v["is_alert"]) {
					$venster->endTag("b");
				}
				$venster->addTag("br");
			}
		} else {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("geen actuele todo's")."<br>");
		}

		$venster->addCode("<br>");
		$venster->insertAction("go_todo", gettext("naar todos"), "index.php?mod=todo");
		$venster->insertLink(gettext("naar todo's"), array(
			"href" => "index.php?mod=todo",
			"alt"  => gettext("naar todos")
		));
		if ($userinfo["alternative_note_view_desktop"]) {
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertAction("edit", gettext("toon eigen aantekeningen"), "javascript: toggle_notes('active');");
			$venster->insertLink(gettext("toon eigen aantekeningen"), array(
				"href" => "javascript: toggle_notes('active');",
				"alt"  => gettext("toon eigen aantekeningen")
			));
		}
		$venster->endVensterData();
		$table->addCode ($venster->generate_output() );
		unset($venster);
		$table->endTableData();
		if ($userinfo["rssnews"]) {
			$table->addTableData(array("width" => "33%", "style" => "vertical-align: top;"));
			$venster = new Layout_venster();
			$venster->addVensterData( array("width"=>"100%") );
			$venster->addCode("<b>".gettext("nieuws")."</b><br>");
			$rssfeeds = $desktop_data->getRSSfeeds();
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
						$title = $conversion->utf8_convert($item["subject"])." (".date("d-m-Y", $item["date"]).")";
						$venster->insertLink($title, array(
							"href"   => $item["link"],
							"alt"    => gettext("bezoek artikel"),
							"target" => "_blank"
						));
						$venster->endTag("i");
						$venster->addTag("br");
					}
				}
			}
			$venster->endVensterData();
			$table->addCode ($venster->generate_output(1) );
			unset($venster);
		} else {
			$table->addTableData(array("width" => "1%", "style" => "vertical-align: top;"));
			$table->addSpace(2);
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
						$venster->insertTag("b", gettext("jarigen"));
						$venster->addTag("br");
						$venster->addTag("br");
						foreach ($bd as $k=>$v) {
							$venster->insertAction("data_birthday", gettext("ga naar de relatiekaart"), array("href"=>"?mod=address&action=relcard&id=".$v["company_id"]));
							$venster->addCode( " ".$v["name"] );
							if ($v["company_name"]) {
								$venster->addCode(" (".gettext("van")." ".$v["company_name"].")");
							}
							$venster->addCode( " ".gettext("is vandaag jarig en wordt")." ".$v["age"] );
							$venster->addTag("br");
						}
					$venster->endVensterData();
					$table->addCode($venster->generate_output());
					unset($venster);
				$table->endTableData();
			$table->endTableRow();
		}
		if (!$userinfo["alternative_note_view_desktop"]) {
			$table->addTableRow();
		} else {
			$table->addTableRow(array("style" => "visibility: hidden;", "id" => "ownnotes"));
		}
			$table->addTableData(array("colspan" => 3));
				$venster = new Layout_venster();
				$venster->addVensterData();
					$venster->addTag("b");
						$venster->addCode(gettext("eigen aantekeningen"));
					$venster->endTag("b");
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->addCode($desktop_data->getOwnNotes($_SESSION["user_id"]));
					$venster->addTag("br");
					$venster->insertAction("edit", gettext("aanpassen"), "javascript: popup('index.php?mod=desktop&action=editnotes', 'edit', 0, 0, 1);");
					if ($userinfo["alternative_note_view_desktop"]) {
						$venster->addSpace(2);
						$venster->insertAction("close", gettext("verberg eigen aantekeningen"), "javascript: toggle_notes('nonactive');");
					}
				$venster->endVensterData();
				$table->addCode($venster->generate_output());
				unset($venster);
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$output->addCode($table->generate_output());

		$output->start_javascript();
		$output->addCode("var desktop_autorefresh = setTimeout('location.href=\'./\';', 5*60*1000);");
		$output->end_javascript();
		if ($userinfo["alternative_note_view_desktop"]) {
			$output->load_javascript(self::include_dir."desktop.js");
		}
		$output->Layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
}
?>
