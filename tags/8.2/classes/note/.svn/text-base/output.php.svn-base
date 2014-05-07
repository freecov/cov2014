<?php
	/**
	 * Covide Groupware-CRM Notes module List
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 *
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2006 Covide BV
	 * @package Covide
	 */
	Class Note_output {
		/* constants */
		const include_dir = "classes/note/inc/";
		const include_dir_main = "classes/html/inc/";

		//TODO: somwhere there is a bug with other values than 20
		public $pagesize = 20;
		/* methods */


	  public function __construct() {
	  	$this->pagesize = $GLOBALS["covide"]->pagesize;
	  }
	    /* 	generate_list {{{ */
	    /**
	     * 	generate_list. Show notes
	     *
		 * Show noteslist. This includes search results.
	     *
		 * @param array options  for the list
	     * @return bool true
	     */

		public function generate_list($options = "") {
			require(self::include_dir."generate_list.php");
		}
		/* }}} */

	    /* 	show_note {{{ */
	    /**
	     * 	Show note
	     *
	     * @return bool true
	     */
		public function show_note($id, $hidenav = 0, $actions = 1) {
			if ($hidenav) {
				$showrel = 0;
				$editproject = 0;
			} else {
				$showrel = 1;
				$editproject = 1;
			}
			$note_data = new Note_data();
			$note      = $note_data->getNoteById($id);
			$note_data->flagRead($note["id"]);

			/* start output buffer routines */
			$output = new Layout_output();
			$output->layout_page( gettext("Notes"), $hidenav);
			/* small form for relation alteration */
			$output->addTag("form", array(
				"id" => "noteform",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("noteid", $note["id"]);
			$output->addHiddenField("addressid", $note["address_id"]);
			$output->addHiddenField("projectid", $note["project_id"]);
			$output->endTag("form");
			$settings = array(
				"title"    => gettext("Notes"),
				"subtitle" => gettext("list of messages")
			);
			$venster = new Layout_venster($settings);
			unset($settings);
			if ($hidenav) {
				$venster->addMenuItem(gettext("close"), "javascript: window.close();");
			} else {
				$venster->addMenuItem(gettext("back"), "index.php?mod=note");
				$venster->addMenuItem(gettext("new note"), "javascript: note_new();");
				$venster->addMenuItem(gettext("answer sender"), "javascript: note_reply_single(".$note["id"].");");
				$venster->addMenuItem(gettext("answer all"), "javascript: note_reply(".$note["id"].");");
				$venster->addMenuItem(gettext("forward"), "javascript: note_forward(".$note["id"].");");
				$venster->addMenuItem(gettext("plan in calendar"), "javascript: note_plan(".$note["id"].");");
				$venster->addMenuItem(gettext("print"), "javascript: note_print(".$note["id"].");");
				$venster->addMenuItem(gettext("done"), "javascript: note_flagdone(".$note["id"].");");
			}
			$venster->generateMenuItems();
			$venster->addVensterData();


				$view = new Layout_view();
				$data[0] = $note;

				$view->addData($data);
				/* specify layout */
				$view->addMapping(gettext("date"), "%human_date");
				$view->addMapping(gettext("sender"), "%from_name");
				$view->addMapping(gettext("recipient"), "%to_name");
				$view->addMapping(gettext("extra recipients"), "%extra_names");
				$view->addMapping(gettext("subject"), "%subject");
				$view->addmapping(gettext("message"), "%body", array(
					"allow_html" => 1
				));
				if ($hidenav) {
					$view->addMapping(gettext("contact"), "%relation_name");
				} else {
					$view->addMapping(gettext("contact"), "%%relation_name");
				}
				if ($GLOBALS["covide"]->license["has_project"]) {
					$view->addMapping(($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("dossier"):gettext("project"), "%%project_name");
				}
				if ($note["is_done"]) {
					$image = "f_nieuw.gif"; $alt = gettext("yes");
				} else {
					$image = "f_oud.gif";   $alt = gettext("no");
				}
				$view->addMapping(gettext("done"), "%%complex_done");
				$view->addMapping(gettext("followup"), "");
				/* define complex mappings */
				$view->defineComplexMapping("relation_name", array(
					array(
						"type"  => "link",
						"link"  => array("index.php?mod=address&action=relcard&id=", "%address_id"),
						"text"  => "%relation_name"
					),
					array(
						"type" => "action",
						"src"  => "edit",
						"link" => array("javascript: pick_rel(", "%id", ");")
					)
				));
				$view->defineComplexMapping("project_name", array(
					array(
						"type"  => "link",
						"link"  => array("?mod=project&action=showinfo&master=0&id=", "%project_id"),
						"text"  => "%project_name"
					),
					array(
						"type"  => "action",
						"src"   => "edit",
						"link"  => array("javascript: pickProject();"),
						"check" => $editproject
					),
				));
				$view->defineComplexMapping("complex_done", array(
					array(
						"type" => "image",
						"src"  => $image,
						"alt"  => $alt,
						"link" => array("javascript: note_flagdone(", "%id", ");"),
						"id"   => array("flag_done_", "%id")
					)
				));
				/* append the view to the page */

				$table_actions = new Layout_table( array("width"=>"100%") );
				$table_actions->addTableRow();
					$table_actions->insertTableData($view->generate_output_vertical());
				$table_actions->endTableRow();
				$table_actions->addTableRow();
					$table_actions->addTableData();
						if (!$hidenav || $actions) {
							$table_actions->insertAction("mail_new", gettext("new note"), "javascript: note_edit(0);");
							$table_actions->insertAction("mail_reply", gettext("answer sender"), "javascript: note_reply_single('".$id."');");
							$table_actions->insertAction("mail_reply_all", gettext("answer all"), "javascript: note_reply('".$id."');");
							$table_actions->insertAction("mail_forward", gettext("forward"), "javascript: note_forward('".$id."');");
							$table_actions->insertAction("calendar_today", gettext("plan in calendar"), "javascript: note_plan('".$id."');");
						}
						$table_actions->insertAction("print", gettext("print"), "javascript: note_print('".$id."');");
						$table_actions->insertAction("ok", gettext("done"), "javascript: note_flagdone('".$id."');");
					$table_actions->endTableData();
				$table_actions->endTableRow();
				$table_actions->endTable();
				$venster->addCode( $table_actions->generate_output() );
				$venster->load_javascript(self::include_dir_main."xmlhttp.js");
				$venster->load_javascript(self::include_dir."note_actions.js");
				unset($view);
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
			$history = new Layout_history();
	        $output->addCode( $history->generate_history_call() );
			$output->layout_page_end();
			echo $output->generate_output();
		}
		/* }}} */

	    /* 	edit_note {{{ */
	    /**
	     * 	Create/reply/forward note
	     *
	     * @return bool true
	     */
		public function edit_note() {
			require(self::include_dir."edit_note.php");
		}
		/* }}} */

		public function show_sent() {
			$output = new Layout_output();
			$output->layout_page("", 1);
			/* make a nice screen */
			$venster = new Layout_venster(array(
				"title"    => gettext("notes"),
				"subtitle" => gettext("message sent")
			));
			#$venster->addMenuItem(gettext("new message"), "javascript: note_edit(0);");
			#$venster->addMenuItem(gettext("back"), "javascript: close_window();");
			#$venster->generateMenuItems();
			$venster->addVensterData();
				if ($_REQUEST["note"]["is_draft"]) {
					$venster->addCode(gettext("your message has been saved"));
				} else {
					$venster->addCode(gettext("your message has been sent"));
				}
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			#$output->load_javascript(self::include_dir."show_sent.js");
			$output->start_javascript();
				$output->addCode(
					"
					if (opener) {
						opener.location.href = opener.location.href;
					}
					var wclose = setTimeout('window.close()', 1000);
					"
				);
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}

		public function printnote() {
			$note_data = new Note_data();
			$note      = $note_data->getNoteById($_REQUEST["id"]);

			/* start output buffer routines */
			$output = new Layout_output();
			$output->layout_page( gettext("Notes"), 1 );
			$settings = array(
				"title"    => gettext("Note")
			);
			$venster = new Layout_venster($settings);
			unset($settings);
			$venster->addVensterData();


				$view = new Layout_view();
				$data[0] = $note;
				$view->addData($data);
				/* specify layout */
				$view->addMapping(gettext("date"), "%human_date");
				$view->addMapping(gettext("sender"), "%from_name");
				$view->addMapping(gettext("recipient"), "%to_name");
				$view->addMapping(gettext("extra recipients"), "%extra_names");
				$view->addMapping(gettext("subject"), "%subject");
				$view->addmapping(gettext("message"), "%body", array(
					"allow_html" => 1
				));
				$view->addMapping(gettext("contact"), "%%relation_name");
				if ($GLOBALS["covide"]->license["has_project"]) {
					$view->addMapping(gettext("project"), "%project_name");
				}
				if ($note["is_done"]) {
					$image = "f_nieuw.gif"; $alt = gettext("yes");
				} else {
					$image = "f_oud.gif";   $alt = gettext("no");
				}
				$view->addMapping(gettext("done"), "%%complex_done");
				$view->addMapping(gettext("followup"), "");

				/* define complex mappings */
				$view->defineComplexMapping("relation_name", array(
					array(
						"type" => "link",
						"text" => "%relation_name"
					)
				));
				$view->defineComplexMapping("complex_done", array(
					array(
						"type" => "image",
						"src"  => $image,
						"alt"  => $alt,
						"id"   => array("flag_done_", "%id")
					)
				));

				$table_actions = new Layout_table( array("width"=>"100%") );
				$table_actions->addTableRow();
					$table_actions->insertTableData($view->generate_output_vertical());
				$table_actions->endTableRow();
				$table_actions->endTable();
				$venster->addCode( $table_actions->generate_output() );
				unset($view);
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
			$output->start_javascript();
				$output->addCode(
					"
					window.print();
					setTimeout('window.close();', 100);
					"
				);
			$output->end_javascript();
			$output->layout_page_end();
			echo $output->generate_output();

		}
	}
?>
