<?php
	/**
	 * Covide Groupware-CRM Todo module output
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
	Class Todo_output {
		/* contants */
		const include_dir = "classes/todo/inc/";
		const include_dir_main = "classes/html/inc/";

		/* methods */

	    /* 	edit_todo {{{ */
	    /**
	     * Show screen to edit/create a todo
	     *
		 * @param int If set, reads the note from db and use the data as template for the todo
	     * @param int If set, dont show menu and footer. This is for popup version
	     */
		public function edit_todo($noteid = 0, $noiface = 0) {
			require(self::include_dir."edit_todo.php");
		}
		/* }}} */

		/* edit_multi_todo {{{ */
		/**
		 * Show screen with start and end time to alter multiple todos
		 */
		public function edit_multi_todo() {
			/* validate the input and build array for formfields */
			if (is_array($_REQUEST["checkbox_todo"])) {
				foreach ($_REQUEST["checkbox_todo"] as $k=>$v) {
					$todos[] = (int)$k;
				}
				$todo_ids = implode(",", $todos);
			}
			/* prepare data-array for dropdowns */
			$days = array();
			for ($i=1; $i<=31; $i++) {
				if ($i<10) {
					$days[$i] = "0".$i;
				} else {
					$days[$i] = $i;
				}
			}
			$months = array();
			for ($i=1; $i<=12; $i++) {
				if ($i<10) {
					$months[$i] = "0".$i;
				} else {
					$months[$i] = $i;
				}
			}
			$years = array();
			for ($i=date("Y")-2; $i<date("Y")+2; $i++) {
				$years[$i] = $i;
			}
			/* output buffer */
			$output = new Layout_output();
			$output->layout_page("", 1);
				/* form so we can actually submit the changes */
				$output->addTag("form", array(
					"id"     => "editmultitodo",
					"method" => "get",
					"action" => "index.php"
				));
				$output->addHiddenField("mod", "todo");
				$output->addHiddenField("action", "save_multi");
				$output->addHiddenField("todo[ids]", $todo_ids);
				/* nice window widget */
				$venster = new Layout_venster(array(
					"title"    => gettext("TODO today"),
					"subtitle" => gettext("wijzig groep")
				));
				$venster->addVensterData();
					/* table with input elements */
					$table = new Layout_table();
					$table->addTableRow();
						$table->insertTableData(gettext("dag"), "", "header");
						$table->addTableData("", "data");
							$table->addSelectField("todo[start_day]", $days, date("d"));
							$table->addSelectField("todo[start_month]", $months, date("m"));
							$table->addSelectField("todo[start_year]", $years, date("Y"));
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData(gettext("einddatum"), "", "header");
						$table->addTableData("", "data");
							$table->addSelectField("todo[end_day]", $days, date("d"));
							$table->addSelectField("todo[end_month]", $months, date("m"));
							$table->addSelectField("todo[end_year]", $years, date("Y"));
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->addTableData(array("colspan" => 2), "header");
							$table->insertAction("cancel", gettext("terug"), "javascript: ignore();");
							$table->insertAction("save", gettext("opslaan"), "javascript: save();");
						$table->endTableData();
					$table->endTableRow();
					$table->endTable();
					$venster->addCode($table->generate_output());
					unset($table);
				$venster->endVensterData();
				$output->addCode($venster->generate_output());
				unset($venster);
				$output->endTag("form");
				$output->load_javascript(self::include_dir."todo_multi_actions.js");
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */

	    /* 	show_todos {{{ */
	    /**
	     * Show all your todos with some actions.
	     */
		public function show_todos() {
			require(self::include_dir."show_todos.php");
		}
		/* }}} */

	    /* 	show_info {{{ */
	    /**
	     * Output javascript function for a todo 
	     *
		 * Show info will grab a todo from the database.
		 * It will parse this data into a javascriptcall.
		 * Use this with xmlhttprequest to get a javascriptcall with the todo as parameter.
	     */
		public function show_info() {
			require(self::include_dir."show_info.php");
		}
		/* }}} */
	}
?>
