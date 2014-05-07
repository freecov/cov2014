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
		/* get note data */
		$note_data = new Note_data();
		$note_count = $note_data->getNotecountByUserId($_SESSION["user_id"]);
		/* rss and email need desktop data object */
		$desktop_data = new Desktop_data();

		if ($GLOBALS["covide"]->license["has_issues"]) {
			$support_data = new Support_data();
			
			/* support issues */
			$supportinfo = $support_data->getSupportItems(array("user_id" => $_SESSION["user_id"], "active" => 1, "nolimit" => 1));
			
			/* support calls */
			$supportcalls["count"] = count($support_data->getExternalIssues());
		}
		
		if ($GLOBALS["covide"]->license["has_voip"]) {
			/* fax info */
			$voip_data = new Voip_data();
			$faxinfo   = $voip_data->getFaxes();
		}

		/* start defining the desktop */
		$output = new Layout_output();
		$output->Layout_page();
		
		$table = new Layout_table(array("width" => "100%", "cellspacing" => 4));
	
		/* testbox */
		$table->addTableRow();
			$table->addTableData(array("colspan" => 3));
				$venster = new Layout_venster();
				$venster->addVensterData();
					$venster->addTag("b");
						$venster->addCode("V&auml;lkommen");
					$venster->endTag("b");
					$venster->addTag("br");
					$venster->addTag("br");
			
					$venster->addTag("p");
						$venster->addCode("V&auml;lkommen till Covide+Vamos. Om du av n&aring;gon anledning beh&ouml;ver".
							" tala med utvecklarna, ring:");
					$venster->endTag("p");

					$venster->addCode("Svante Kvarnstr&ouml;m: 0702 38 34 00");
					$venster->addTag("br");
					$venster->addCode("Markus Liljergren: 08 50 00 48 22");
					$venster->addTag("br");
					$venster->addCode("Michiel van Baak: +31 (0)318 78 72 44");

				$venster->endVensterData();
				$table->addCode($venster->generate_output());
				unset($venster);
			$table->endTableData();
		$table->endTableRow();
		
		/* Personal notes */
		$table->addTableRow();
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
					$venster->insertAction("edit", gettext("change"), "javascript: popup('index.php?mod=desktop&action=editnotes', 'edit', 0, 0, 1);");
					if ($userinfo["alternative_note_view_desktop"]) {
						$venster->addSpace(2);
						$venster->insertAction("close", gettext("hide own notes"), "javascript: toggle_notes('nonactive');");
					}
				$venster->endVensterData();
				$table->addCode($venster->generate_output());
				unset($venster);
			$table->endTableData();
		$table->endTableRow();
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
