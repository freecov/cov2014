<?php
	/**
	 * Covide Groupware-CRM User output module
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
	Class User_output {

		/* constants */
		const include_dir = "classes/user/inc/";
		const include_dir_main = "classes/html/inc/";
		const class_name  = "User_output";

		/* methods */
		/* show_login($error = 0) {{{ */
		/**
		 * show a login screen, with optional error message
		 *
		 * @param int Error number
		 */
		public function show_login($error = 0) {
			require(self::include_dir."show_login.php");
		}
		/* }}} */
		/* show_index() {{{ */
		/**
		 * Show a screen where we can choose which user to edit.
		 * people without admin permissions will be redirected to useredit()
		 */
		public function show_index() {
			require(self::include_dir."show_index.php");
		}
		/* }}} */
		/* show_grouplist {{{ */
		/**
		 * Show a list of groups
		 */
		public function show_grouplist() {
			$user_data = new User_data();
			$groups = $user_data->getGroupList();
			/* start output */
			$output = new Layout_output();
			$output->layout_page();
				/* window object */
				$venster = new Layout_venster(array("title" => gettext("groepen")));
				$venster->addMenuItem(gettext("toevoegen"), "index.php?mod=user&action=groupinfo&group_id=-1");
				$venster->generateMenuItems();
				$venster->addVensterData();
					/* view for groups */
					$view = new Layout_view();
					$view->addData($groups);
					$view->addMapping(gettext("id"), "%id");
					$view->addMapping(gettext("groepnaam"), "%%groupname");
					$view->addMapping(gettext("omschrijving"), "%description");
					$view->defineComplexMapping("groupname", array(
						array(
							"type" => "link",
							"text" => "%name",
							"link" => array("index.php?mod=user&action=groupinfo&group_id=", "%id")
						)
					));
					$venster->addCode($view->generate_output());
					unset($view);
				$venster->endVensterData();
				$output->addCode($venster->generate_output());
				unset($venster);
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* show_groupinfo {{{ */
		/**
		 * Show all the info about a group like members and manager
		 *
		 * @param int The groupid to show
		 */
		public function show_groupinfo($group_id) {
			$user_data = new User_data();
			$groupinfo = $user_data->getGroupInfo($group_id);
			$output = new Layout_output();
			$output->layout_page();
				$output->addTag("form", array(
					"id"     => "groupedit",
					"method" => "post",
					"action" => "index.php"
				));
				$output->addHiddenField("mod", "user");
				$output->addHiddenField("action", "save_group");
				$output->addHiddenField("group[id]", $groupinfo["id"]);
				$venster = new Layout_venster(array("title" => gettext("groepen"), "subtitle" => $groupinfo["name"]));
				$venster->addMenuItem(gettext("terug"), "?mod=user&action=groupindex");
				$venster->generateMenuItems();
				$venster->addVensterData();
					/* put the whole thing in a nice table */
					$table = new Layout_table(array("cellspacing" => 1));
					$table->addTableRow();
						$table->insertTableData(gettext("naam"), "", "header");
						$table->addTableData("", "data");
							$table->addTextField("group[name]", $groupinfo["name"]);
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData(gettext("omschrijving"), "", "header");
						$table->addTableData("", "data");
							$table->addTextArea("group[description]", $groupinfo["description"]);
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData(gettext("leden"), "", "header");
						$table->insertTableData(gettext("manager"), "", "header");
					$table->endTableRow();
					$table->addTableRow();
						$table->addTableData(array("style" => "vertical-align: bottom;"), "data");
							$table->addHiddenField("group[members]", $groupinfo["members"]);
							$useroutput = new User_output();
							$table->addCode( $useroutput->user_selection("groupmembers", $groupinfo["members"], 1, 0, 1, 0) );
						$table->endTableData();
						$table->addTableData(array("style" => "vertical-align: bottom;"), "data bottom");
							$table->addHiddenField("group[manager]", $groupinfo["manager"]);
							$useroutput = new User_output();
							$table->addCode( $useroutput->user_selection("groupmanager", $groupinfo["manager"], 0, 0, 1, 0) );
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->addTableData(array("colspan" => 2), "data");
							$table->insertAction("delete", gettext("verwijder"), "javascript: delete_group();");
							$table->addSpace(2);
							$table->insertAction("save", gettext("opslaan"), "javascript: save_group();");
						$table->endTableData();
					$table->endTableRow();
					$table->endTable();
					$venster->addCode($table->generate_output());
					unset($table);
				$venster->endVensterData();
				$output->addCode($venster->generate_output());
				$output->endTag("form");
				$output->load_javascript(self::include_dir."show_groupinfo.js");
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* useredit() {{{ */
		/**
		 * show screen with options for a user so we can edit them
		 */
		public function useredit() {
			require(self::include_dir."useredit.php");
		}
		/* }}} */
		/* theme_preview($themeid) {{{ */
		/**
		 * show a little preview for a theme
		 *
		 * @param int The theme to preview
		 * @return string The output to include in a page
		 */
		public function theme_preview($themeid) {
			$table = new Layout_table(array("border"=>0));
			$table->addTableRow();
				$table->addTableData(array("background" => "themes/".$themeid."/tabel_bg_2.gif"));
						$table->addTag("image", array(
							"id"     => "themepreview",
							"src"    => "themes/previews/thumb_theme".$themeid.".png",
							"style"  => "border: 1px solid #666",
							"border" => 0
						));
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			return $table;
		}
		/* }}} */
		/* addSelectField() {{{ */
		/**
		 * What is this doing here ??? Obsolete ????
		 */
		public function addSelectField($name, $values="", $selected_values="", $multiple=0, $settings="", $usenewline=0) {
			$id = preg_replace("/(\[)|(\])/s", "", $name);
			$output = new Layout_output();
			$output->addSelectField($name, $values, $selected_values, $multiple, $settings);
			if ($usenewline) {
				$output->addTag("br");
			}
			$output->insertAction("users", "meer opties",
				sprintf("javascript: popup('?mod=user&action=searchUserInit&obj=%s')", $id));
			return $output->generate_output();
		}
		/* }}} */
		/* searchUserInit() {{{ */
		/**
		 * Output html to allow usersearch
		 */
		public function searchUserInit() {
			$output = new Layout_output();
			$output->layout_page("usersearch", 1);
			$output->addTag("form", array(
				"id" => "frm",
				"action" => "index.php"
			));

			$output->addHiddenField("mod", "user");
			$output->addHiddenField("action", "searchUser");
			$output->addHiddenField("users", "");
			$output->addHiddenField("users_selected", "");
			$output->addHiddenField("multiple", "");
			$output->addHiddenField("object", $_REQUEST["obj"]);
			$output->endTag("form");
			$output->load_javascript(self::include_dir."js_userlist.js");
			$output->start_javascript();
				$output->addCode("retrieve_userlist(); ");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* searchUser() {{{ */
		/**
		 * Show screen to select users from a list of available users
		 */
		public function searchUser() {
			$output = new Layout_output();
			$output->layout_page("usersearch", 1);

			$output->addTag("form", array(
				"id" => "frm",
				"action" => "index.php"
			));
			$output->addHiddenField("users", $_REQUEST["users"]);
			$output->addHiddenField("multiple", $_REQUEST["multiple"]);
			$users = explode(",", $_REQUEST["users"]);

			$venster = new Layout_venster(Array(
				"title"    => gettext("Gebruikers"),
				"subtitle" => gettext("zoeken")
			));
			$venster->addVensterData();

			/* retrieve all the selected users */
			$userData = new User_data();
			$db_users = $userData->getUserList();

			$s_users_avail = array();
			$s_users_selected = array();

			foreach ($db_users as $k=>$v) {
				if (in_array($k, $users)) {
					$s_users_avail[$k]=$v;
				}
				if (in_array($k, explode(",",$_REQUEST["users_selected"]) )) {
					$s_users_selected[$k]=$v;
				}
			}

			$table = new Layout_table(array("cellspacing"=>3, "width"=>"100%"), 1);
			$table->addTableRow();
				$table->addTableData( array("colspan"=>3) );
					$table->addCode( gettext("zoeken").": ");
					$table->addTextField("search", "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData( array("style"=>"width: 360px") );
					$table->addTag("br");
					$table->addCode( gettext("beschikbare gebruikers").": ");
					$table->addTag("br");
					$table->addSelectField("users_available", $s_users_avail, "", 1, array("style"=>"width: 350px;", "size"=>12));
				$table->endTableData();
				$table->addTableData( array("style"=>"width: 20px") );
					$table->insertAction("add", gettext("toevoegen"), "");
					$table->addTag("br");
					$table->insertAction("remove", gettext("weghalen"), "");
				$table->endTableData();
				$table->addTableData();
					$table->addTag("br");
					$table->addCode( gettext("gekozen gebruikers").": ");
					$table->addTag("br");
					$table->addSelectField("users_selected", $s_users_selected, "", 1, array("style"=>"width: 350px;", "size"=>12));
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();

			$venster->addCode( $table->generate_output() );

			$venster->endVensterData();

			$output->addCode( $venster->generate_output() );

			$output->endTag("frm");
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* user_selection {{{ */
		/**
		 * Display list of selected users. Including link to remove them.
		 *
		 * @param int The form identifier
		 */
		public function user_selection($id, $current="", $multiple=0, $showArchiveUser=0, $showInActive=0, $no_empty=0) {

			$current = str_replace(",", "|", $current);
			$current = explode("|", $current);
			foreach ($current as $k=>$v) {
				if (!$v) {
					unset($current[$k]);
				}
			}
			$user_data = new User_data();

			$output = new Layout_output();
			$output->addTag("div", array(
				"id"   => "user_name_".$id,
				"name" => "user_name_".$id,
				"display" => "inline"
			));

			$enabled_users  = $user_data->getUserList(1);
			$disabled_users = $user_data->getUserList(0);

			foreach ($current as $k=>$v) {
				if ($enabled_users[$v]) {
					$output->addTag("li", array("class"=>"enabled"));
				} elseif ($disabled_users[$v]) {
					$output->addTag("li", array("class"=>"disabled"));
				} else {
					$output->addTag("li", array("class"=>"special"));
				}
				$output->addTag("a", array(
					"href" => "javascript: remove_user('$v', '$id', 'user_name_$id', '$no_empty');"
				));
				$output->addCode( $user_data->getUserNameById($v) );
				$output->endTag("a");
				$output->endTag("li");
			}

			$output->endTag("div");
			$output->addTag("br");

			$output->insertTag("div", "&nbsp;", array(
				"id"    => "user_layer_autocomplete",
				"style" => "visibility:hidden; position:absolute; top:0px; left:0px; z-index: 10;"
			));
			$output->insertTag("iframe", "", array(
				"id"    => "user_layer_iframe",
				"style" => "z-index: 6; display: none; left: 0px; position: absolute; top: 0px;",
				"src"   => "blank.htm",
				"frameborder" => 0,
				"scrolling"   => "no"
			));

			$output->addTextField("user_autocomplete_$id", gettext("zoeken"), array(
				"style"     => "width: 100px;",
				"onkeydown" => "return scanKeyCode();"
			));
			$output->insertAction("edit", gettext("kies gebruiker(s)"), "javascript: user_select_$id();");

			$output->load_javascript(self::include_dir."autocomplete.js");
			$output->start_javascript();
			$output->addCode("
				function user_select_$id() {
					user_complete_initial = 0;
					popup('index.php?mod=user&action=pick_user&sub_action=init&field_id=$id&multiple=$multiple&inactive=$showInActive&empty=$showEmpty&archive=$showArchiveUser&no_empty=$no_empty', 'user_select', 700, 410, 1);
				}
				document.getElementById('user_autocomplete_$id').onkeyup = function() {
					 autouser_complete_field('user_autocomplete_$id', 'user_name_$id', '$id', '$showArchiveUser', '$multiple', '$no_empty');
				}
				document.getElementById('user_autocomplete_$id').onfocus = function() {
					document.getElementById('user_autocomplete_$id').value = '';
				}
			");
			$output->end_javascript();

			return $output->generate_output();
		}
		/* }}} */
		/* usersaved {{{ */
		/**
		 * Show the user all user settings have been changed
		 */
		public function usersaved() {
			$output = new Layout_output();
			$output->layout_page();
				$venster = new Layout_venster(array("title"=>gettext("gebruikers")));
				$venster->addMenuItem(gettext("terug"), "index.php?mod=user");
				$venster->generateMenuItems();
				$venster->addVensterData();
					$venster->addCode(gettext("de gebruikersgegevens zijn opgeslagen."));
					$venster->addTag("br");
					$venster->addCode(gettext("Sommige instellingen worden pas actief als u opnieuw inlogt."));
				$venster->endVensterData();
				$output->addCode($venster->generate_output());
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* pick_user {{{ */
		/**
		 * Output user picker select box
		 */
		public function pick_user() {
			require(self::include_dir."pick_user.php");
		}
		/* }}} */
		/* obsolete(?) {{{ */
		public function addUserField($name, $showArchiveUser=0, $showInActive=0, $selected_values="", $multiple = 0, $settings="", $id="") {
			$output = new Layout_output();

			$userData = new User_data();
			if ($showArchiveUser) {
				$archive = $userData->getArchiveUserId();
				$list[gettext("speciale keuzes")] = array($archive=>gettext("archiefgebruiker"));
			}
			if ($showEmpty) {
				$leeg = array("0"=>gettext("geen gebruiker"));
				$list[gettext("speciale keuzes")] = array_merge($leeg, $list[gettext("speciale keuzes")]);
			}
			$list[gettext("actieve gebruikers")] = $userData->getUserList();

			if ($showInActive) {
				$list[gettext("niet actief")] = $userData->getUserList(0);
			}
			foreach ($list as $k=>$v) {
				natcasesort($list[$k]);
			}

			$output->addSelectField($name, $list, $selected_values, $multiple, $settings, $id);
			return $output->generate_output();
		}
		/* }}} */
		/* php_info {{{ */
		/**
		 * Show the output of phpinfo, only when a user is logged in
		 */
		public function php_info() {
			if ($_SESSION["user_id"]) {
				phpinfo();
			}
			exit();
		}
		/* }}} */
		/* showTime {{{ */
		/**
		 * Put current time in clock_time div on top of the page (for use in AJAX).
		 */
		public function showTime() {
			echo sprintf("document.getElementById('clock_time').innerHTML = '%s';", date("H:i:"));
			exit();
		}
		/* }}} */
		/* randomOutput {{{ */
		/**
		 * Output some random data so we can prevent caching in javascript (for use in AJAX).
		 */
		public function randomOutput() {
			echo md5(mktime()*rand());
		}
		/* }}} */
		/* translateString {{{ */
		/**
		 * Translate a string and echo it escaped for in javascript (for use in AJAX).
		 *
		 * @param string The text string to translate with gettext()
		 */
		public function translateString($str) {
			echo str_replace("'", "\'", gettext($str));
		}
		/* }}} */
		/* activate_user_xml {{{ */
		/**
		 * Activate a user and echo javascript call (for use in AJAX).
		 *
		 * @param int The user_id to activate
		 */
		public function activate_user_xml($user_id) {
			/* activate user */
			$user_data = new User_data();
			$user_data->activate_user($user_id);
			/* output refresh function */
			echo "refresh_page();";
		}
		/* }}} */
		/* deactivate_user_xml {{{ */
		/**
		 * Deactivate a user and echo javascript call (for use in AJAX).
		 *
		 * @param int The user_id to deactivate
		 */
		public function deactivate_user_xml($user_id) {
			/* deactivate user */
			$user_data = new User_data();
			$user_data->deactivate_user($user_id);
			/* output refresh function */
			echo "refresh_page();";
		}
		/* }}} */
	}
?>
