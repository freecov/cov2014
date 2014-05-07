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
			/* make the db object easier to access */
			$db = $GLOBALS["covide"]->db;
			/* make some URL params easier to access */
			$action     = $_REQUEST["action"];
			$top        = $_REQUEST["top"];
			$limit      = $this->pagesize;
			$zoekstring = $_REQUEST["zoekstring"];
			if ($_REQUEST["action"] == "searchsv") {
				$options["note_type"] = $_REQUEST["note_type"];
			}
			if (!is_array($options)) {
				$options["note_type"] = "current";
			}
			$options["top"]        = $top;
			$options["limit"]      = $limit;
			$options["zoekstring"] = $zoekstring;
			$options["sort"]       = $_REQUEST["sort"];

			/* get the permissions for the user */
			$user = new User_data();
			$user->getUserPermissionsById($_SESSION["user_id"]);
			$user_arr = $user->getUserList();
			/* get the notes data */
			$notedata  = new Note_data();
			$notes_arr = $notedata->getNotes($options);

			/* start output buffer routines */
			$output = new Layout_output();
			$output->layout_page( gettext("Notities") );

			$output->addTag("form", array(
				"id"     => "zelf",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("mod" ,"note");
			$output->addHiddenField("action", "");
			$output->addHiddenField("msg_id", "");
			$output->endTag("form");
			switch ($options["note_type"]) {
				case "sent" : $subtitle = gettext("verstuurde berichten");            break;
				case "old"  : $subtitle = gettext("afgehandelde berichten");          break;
				case "show" : $subtitle = gettext("verzonden, niet gelezen berichten"); break;
				default     : $subtitle = gettext("berichtenlijst");                  break;
			}
			$settings = array(
				"title"    => gettext("Notities"),
				"subtitle" => $subtitle
			);
			$venster = new Layout_venster($settings);
			unset($settings);
			$venster->addMenuItem(gettext("nieuw bericht"), "javascript: note_edit(0);");
			$venster->addMenuItem(gettext("afgehandeld"), "./?mod=note&action=old&short_view=".$_REQUEST["short_view"]);
			$venster->addMenuItem(gettext("verstuurd"), "./?mod=note&action=sent&short_view=".$_REQUEST["short_view"]);
			$venster->addMenuItem(gettext("verstuurd, ongelezen"), "./?mod=note&action=show&short_view=".$_REQUEST["short_view"]);
			$venster->addMenuItem(gettext("zoeken"), "./?mod=index&search[private]=1&search[notes]=1");
			if ($options["note_type"] != "current") {
				$venster->addMenuItem(gettext("berichtenlijst"), "./?mod=note&short_view=".$_REQUEST["short_view"]);
			}
			$venster->generateMenuItems();

			$venster->addVensterData();
				/* prepare grid */
				$settings = array();
				$data = $notes_arr["notes"];
				$view = new Layout_view();
				$view->addData($data);
				$view->addSettings($settings);

				/* add the mappings so we actually have something */
				if ($_REQUEST["short_view"]) {
					$view->addMapping(gettext("onderwerp"), "%%complex_subject");
					$view->addMapping(gettext("afzender"), "%from_name");
					if ($options["note_type"] == "sent" || $options["note_type"] == "show") {
						$view->addMapping(gettext("ontvanger"), "%to_name");
					}
					$view->addMapping(gettext("relatie"), "%%relation_icon");
					$view->addMapping(gettext("datum"), "%human_date");

				} else {
					$view->addMapping(gettext("onderwerp"), "%%complex_contactitem");
					$view->addSubMapping("%%complex_subject", "%nieuw");
					$view->addMapping(gettext("afzender"), "%from_name");
					if ($options["note_type"] == "sent" || $options["note_type"] == "show") {
						$view->addMapping(gettext("ontvanger"), "%to_name");
					}
					if ($GLOBALS["covide"]->license["project"]) {
						$view->addMapping(gettext("project"), "");
					}
					$view->addMapping(gettext("relatie"), "%%relation_name");
					$view->addMapping(gettext("datum"), "%human_date");
				}

				/* define the mappings */
				/* subject is link to complete note */
				$view->defineComplexMapping("complex_subject", array(
					array(
						"type" => "link",
						"link" => array("index.php?mod=note&action=message&msg_id=", "%id"),
						"text" => "%subject"
					)
				));
				/* contactitem is image that displays wether this is a contactmoment */
				$view->defineComplexMapping("complex_contactitem", array(
					array(
						"type" => "image",
						"src"  => "f_oud.gif",
						"check" => "%is_support"
					)
				));
				$view->defineComplexMapping("relation_name", array(
					array(
						"type" => "link",
						"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
						"text" => "%relation_name"
					)
				));
				$view->defineComplexMapping("relation_icon", array(
					array(
						"type" => "action",
						"src"  => "addressbook",
						"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
						"alt"   => "%relation_name",
						"check" => "%address_id"
					)
				));

				$view->defineSortParam("sort");
				$view->defineSort(gettext("onderwerp"), "subject");
				$view->defineSort(gettext("afzender"), "user_name");
				$view->defineSort(gettext("relatie"), "address_name");
				$view->defineSort(gettext("datum"), "timestamp");


				/* put the table in the $venster data buffer and destroy object */
				$venster->addCode( $view->generate_output() );
				unset($view);

				$paging = new Layout_paging();
				$url = "index.php?mod=note&action=$action&short_view=".$_REQUEST["short_view"]."&top=%%";
				$paging->setOptions($top, $notes_arr["total_count"], $url);


				$tbl = new Layout_table();
				$tbl->addTableRow();
					$tbl->addTableData();
						if ($_REQUEST["short_view"]) {
							$short_view = 0;
						} else {
							$short_view = 1;
						}
						$tbl->insertAction("view_all", gettext("lange/korte weergave"), "?mod=note&action=".$_REQUEST["action"]."&sort=".$_REQUEST["sort"]."&short_view=".$short_view);
					$tbl->endTableData();
					$tbl->addTableData();
						$tbl->addCode( $paging->generate_output() );
					$tbl->endTableData();
				$tbl->endTableRow();
				$tbl->endTable();

			$venster->addCode($tbl->generate_output());
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);

			$output->load_javascript(self::include_dir."note_actions.js");
			$history = new Layout_history();
			$output->addCode( $history->generate_save_state("action") );
			$output->layout_page_end();
			echo $output->generate_output();
		}
		/* }}} */

	    /* 	show_note {{{ */
	    /**
	     * 	Show note
	     *
	     * @return bool true
	     */
		public function show_note($id, $hidenav = 0) {
			if ($hidenav) {
				$showrel = 0;
			} else {
				$showrel = 1;
			}
			$note_data = new Note_data();
			$note      = $note_data->getNoteById($id);
			$note_data->flagRead($note["id"]);

			/* start output buffer routines */
			$output = new Layout_output();
			$output->layout_page( gettext("Notities"), $hidenav);
			/* small form for relation alteration */
			$output->addTag("form", array(
				"id" => "noteform",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("noteid", $note["id"]);
			$output->endTag("form");
			$settings = array(
				"title"    => gettext("Notities"),
				"subtitle" => gettext("berichtenlijst")
			);
			$venster = new Layout_venster($settings);
			unset($settings);
			if ($hidenav) {
				$venster->addMenuItem(gettext("sluiten"), "javascript: window.close();");
			} else {
				$venster->addMenuItem(gettext("terug"), "index.php?mod=note");
				$venster->addMenuItem(gettext("nieuw bericht"), "javascript: note_edit(0);");
				$venster->addMenuItem(gettext("antwoord"), "javascript: note_reply(".$note["id"].");");
				$venster->addMenuItem(gettext("doorsturen"), "javascript: note_forward(".$note["id"].");");
				$venster->addMenuItem(gettext("agenderen"), "javascript: note_plan(".$note["id"].");");
				$venster->addMenuItem(gettext("printen"), "javascript: note_print(".$note["id"].");");
				$venster->addMenuItem(gettext("afvinken"), "javascript: note_flagdone(".$note["id"].");");
			}
			$venster->generateMenuItems();
			$venster->addVensterData();


				$view = new Layout_view();
				$data[0] = $note;
				$view->addData($data);
				/* specify layout */
				$view->addMapping(gettext("datum"), "%human_date");
				$view->addMapping(gettext("afzender"), "%from_name");
				$view->addMapping(gettext("ontvanger"), "%to_name");
				$view->addMapping(gettext("extra ontvangers"), "%extra_names");
				$view->addMapping(gettext("onderwerp"), "%subject");
				$view->addmapping(gettext("bericht"), "%body");
				if ($hidenav) {
					$view->addMapping(gettext("relatie"), "%relation_name");
				} else {
					$view->addMapping(gettext("relatie"), "%%relation_name");
				}
				if ($GLOBALS["covide"]->license["has_project"]) {
					$view->addMapping(gettext("project"), "%project_name");
				}
				if ($note["is_done"]) {
					$image = "f_nieuw.gif"; $alt = gettext("ja");
				} else {
					$image = "f_oud.gif";   $alt = gettext("nee");
				}
				$view->addMapping(gettext("afgehandeld"), "%%complex_done");
				$view->addMapping(gettext("vervolgaktie"), "");
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
						if (!$hidenav) {
							$table_actions->insertAction("mail_new", gettext("nieuw bericht"), "javascript: note_edit(0);");
							$table_actions->insertAction("mail_reply", gettext("antwoord"), "javascript: note_reply('".$id."');");
							$table_actions->insertAction("mail_forward", gettext("doorsturen"), "javascript: note_forward('".$id."');");
							$table_actions->insertAction("calendar_today", gettext("agenderen"), "javascript: note_plan('".$id."');");
						}
						$table_actions->insertAction("print", gettext("printen"), "javascript: note_print('".$id."');");
						$table_actions->insertAction("ok", gettext("afvinken"), "javascript: note_flagdone('".$id."');");
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
				"title"    => gettext("notities"),
				"subtitle" => gettext("bericht verzonden")
			));
			#$venster->addMenuItem(gettext("nog een bericht"), "javascript: note_edit(0);");
			#$venster->addMenuItem(gettext("terug"), "javascript: close_window();");
			#$venster->generateMenuItems();
			$venster->addVensterData();
				$venster->addCode(gettext("uw bericht is verzonden"));
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
			$output->layout_page( gettext("Notities"), 1 );
			$settings = array(
				"title"    => gettext("Notitie")
			);
			$venster = new Layout_venster($settings);
			unset($settings);
			$venster->addVensterData();


				$view = new Layout_view();
				$data[0] = $note;
				$view->addData($data);
				/* specify layout */
				$view->addMapping(gettext("datum"), "%human_date");
				$view->addMapping(gettext("afzender"), "%from_name");
				$view->addMapping(gettext("ontvanger"), "%to_name");
				$view->addMapping(gettext("extra ontvangers"), "%extra_names");
				$view->addMapping(gettext("onderwerp"), "%subject");
				$view->addmapping(gettext("bericht"), "%body");
				$view->addMapping(gettext("relatie"), "%%relation_name");
				if ($GLOBALS["covide"]->license["has_project"]) {
					$view->addMapping(gettext("project"), "%project_name");
				}
				if ($note["is_done"]) {
					$image = "f_nieuw.gif"; $alt = gettext("ja");
				} else {
					$image = "f_oud.gif";   $alt = gettext("nee");
				}
				$view->addMapping(gettext("afgehandeld"), "%%complex_done");
				$view->addMapping(gettext("vervolgaktie"), "");

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
					window.close();
					"
				);
			$output->end_javascript();
			$output->layout_page_end();
			echo $output->generate_output();

		}
	}
?>
