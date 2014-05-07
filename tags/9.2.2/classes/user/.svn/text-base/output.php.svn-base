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
	 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
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
		 * Possible error codes:
		 * 1  - incorrect username and/or password
		 * 97 - Max number of concurrent users reached
		 * 98 - User is already logged in and single_login is activated
		 * 99 - Incorrect/Corrupt Radius answer received from Radius Server
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
			$output->layout_page(gettext("User settings - groups"));
				/* window object */
				$venster = new Layout_venster(array("title" => gettext("Groups")));
				$venster->addMenuItem(gettext("add"), "index.php?mod=user&action=groupinfo&group_id=-1");
				$venster->generateMenuItems();
				$venster->addVensterData();
					/* view for groups */
					$view = new Layout_view();
					$view->addData($groups);
					//$view->addMapping(gettext("id"), "%id");
					$view->addMapping(gettext("group name"), "%%groupname");
					$view->addMapping(gettext("description"), "%description");
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
		/* show_moduleconf {{{ */
		/**
		 * Show all license fields. This is only for the administrator
		 *
		 * This function shows all info, or just the has_<module> info based on
		 * the variable $access.
		 * if $access is 0, the script will die
		 * if $access is 1, the script will show has_<module> stuff
		 * if $access is 2, the script will show everything
		 */
		public function show_moduleconf() {
			$description_array = array(
				"has_project"                => gettext("Enable project module")."?",
				"has_issues"                 => gettext("Enable support module")."?",
				"has_snack"                  => gettext("On which day should the snack module be active"),
				"email"                      => gettext("Default e-mailaddress"),
				"has_hrm"                    => gettext("Enable extra HRM options")."?",
				"has_voip"                   => gettext("Enable VoIP module")."?",
				"has_sales"                  => gettext("Enable sales module")."?",
				"has_project_ext"            => gettext("Enable extended project module")."?",
				"has_cms"                    => gettext("Enable CMS module")."?",
				"default_lang"               => gettext("Default language"),
				"has_funambol"               => gettext("Enable funambol options")."?",
				"address_strict_permissions" => gettext("Strict address permissions")."?",
				"use_project_global_reghour" => gettext("Enable global registration of projecthours")."?",
				"has_factuur"                => gettext("Enable invoice module")."?",
				"has_campaign"               => gettext("Enable campaign module")."?",
				"single_login"               => gettext("Enable single login")."?",
				"has_onlineusers"            => gettext("Enable online users")."?",
				"alt_logo"                   => gettext("Alternative logo"),
			);

			$limited_fields = array(
				"has_project",
				"has_issues",
				"has_snack",
				"email",
				"has_hrm",
				"has_voip",
				"has_sales",
				"has_project_ext",
				"has_cms",
				"default_lang",
				"has_funambol",
				"address_strict_permissions",
				"use_project_global_reghour",
				"has_factuur",
				"has_campaign",
				"single_login",
				"has_onlineusers",
				"alt_logo",
			);

			/* language array */
			$languages = array(
				"NL" => "NL",
				"EN" => "EN",
				"DE" => "DE",
				"ES" => "ES",
				"DA" => "DA",
				"NO" => "NO"
			);
			/* Days of the week */
			$days = array(
				gettext("sunday"),
				gettext("monday"),
				gettext("tuesday"),
				gettext("wednesday"),
				gettext("thursday"),
				gettext("friday"),
				gettext("saturday"),
				gettext("never")
			);
			/* lookup language names */
			$conversion = new Layout_conversion();
			foreach ($languages as $k=>$v) {
				$languages[$k] = $conversion->getLangName($v);
			}

			/* create options array */
			$options = array(0 => gettext("no"), 1 => gettext("yes"));

			$access = 0;
			$user_data = new User_data();
			/* check permissions */
			$currname = $user_data->getUserNameById($_SESSION["user_id"]);
			if ($currname != "administrator") {
				// not admin. check if user has admin access in this office
				$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);
				if ($userinfo["xs_usermanage"] == 1) {
					$access = 1;
				}
			} else {
				$access = 2;
			}
			if ($access == 0) {
				die("access is denied!");
			}
			/* start output */
			$output = new Layout_output();
			$output->layout_page(gettext("Module config"));
				/* window object */
				$venster = new Layout_venster(array("title" => gettext("Module config")));
				$venster->addMenuItem(gettext("back"), "index.php?mod=user");
				$venster->generateMenuItems();
				$venster->addVensterData();
					$data = $user_data->getLicenseTable();
					$table = new Layout_table();
					foreach ($data as $k=>$v) {
						if ($access == 1) {
							if (!in_array($v["name"], $limited_fields)) {
								continue;
							}
						}
						$table->addTableRow();
							$table->addTableData(array("colspan" => 2), "header");
								$table->insertTag("b", $v["name"]);
							$table->endTableData();

						$table->endTableRow();
						$table->addTableRow();
							$table->addTableData("", "data");
								$table->addCode($description_array[$v["name"]]);
							$table->endTableData();
							$table->addTableData();
								if ($v["name"] == "default_lang") {
									$table->addSelectField(sprintf("lic[%s]", $v["name"]), $languages, $v["value"]);
								} elseif ($v["name"] == "has_snack") {
									$table->addSelectField(sprintf("lic[%s]", $v["name"]), $days, $v["value"]);
								} elseif ($v["name"] == "alt_logo") {
									if ($v["value"]) {
										$url = "index.php?mod=desktop&action=showlogo";
										$table->addTag("img", array(
											"src" => $url,
											"border" => 0,
											"alt" => "logo"
										));
										$table->insertAction("delete", gettext("delete"), "javascript: remove_logo();");
									} else {
										$table->addUploadField("alt_logo");
									}
								} else {
									if ($v["type"] == "d" && $access == 1) {
										$table->addSelectField(sprintf("lic[%s]", $v["name"]), $options, $v["value"]);
									} else {
										$table->addTextField(sprintf("lic[%s]", $v["name"]), $v["value"], array("style" => "width: 250px;"));
									}
								}
							$table->endTableData();
						$table->endTableRow();
					}
					$table->endTable();

					$venster->addCode($table->generate_output());

					$venster->start_javascript();
						$venster->addCode("
							function saveLicense() {
								var cf = confirm(gettext('This will save the new license settings. Are you sure you want to continue?'));
								if (cf == true) {
									document.getElementById('licensefrm').submit();
								}
							}
							function remove_logo() {
								if (confirm(gettext('Are you sure you want to delete the loo?'))) {
									url = 'index.php?mod=user&action=removelogo';
									loadXML(url);
									document.location.href=document.location.href;
								}
							}
						");
					$venster->end_javascript();
					$venster->insertAction("back", gettext("back"), "?mod=user");
					$venster->insertAction("save", gettext("save"), "javascript: saveLicense();");

				$venster->endVensterData();

				$output->addTag("form", array(
					"action" => "index.php",
					"method" => "post",
					"id"     => "licensefrm",
					"enctype" => "multipart/form-data"
				));
				$output->addHiddenField("mod", "user");
				$output->addHiddenField("action", "saveLicense");

				$output->addCode($venster->generate_output());
				$output->endTag("form");

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
			$output->layout_page(gettext("User settings - groups"));
				$output->addTag("form", array(
					"id"     => "groupedit",
					"method" => "post",
					"action" => "index.php"
				));
				$output->addHiddenField("mod", "user");
				$output->addHiddenField("action", "save_group");
				$output->addHiddenField("group[id]", $groupinfo["id"]);
				$venster = new Layout_venster(array("title" => gettext("groups"), "subtitle" => $groupinfo["name"]));
				$venster->addMenuItem(gettext("back"), "?mod=user&action=groupindex");
				$venster->generateMenuItems();
				$venster->addVensterData();
					/* put the whole thing in a nice table */
					$table = new Layout_table(array("cellspacing" => 1));
					$table->addTableRow();
						$table->insertTableData(gettext("name"), "", "header");
						$table->addTableData("", "data");
							$table->addTextField("group[name]", $groupinfo["name"]);
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData(gettext("description"), "", "header");
						$table->addTableData("", "data");
							$table->addTextArea("group[description]", $groupinfo["description"]);
						$table->endTableData();
					$table->endTableRow();
					$table->addTableRow();
						$table->insertTableData(gettext("members"), "", "header");
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
							$table->insertAction("delete", gettext("remove"), "javascript: delete_group();");
							$table->addSpace(2);
							$table->insertAction("save", gettext("save"), "javascript: save_group();");
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
				"title"    => gettext("Settings"),
				"subtitle" => gettext("search")
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
					$table->addCode( gettext("search").": ");
					$table->addTextField("search", "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData( array("style"=>"width: 360px") );
					$table->addTag("br");
					$table->addCode( gettext("available users").": ");
					$table->addTag("br");
					$table->addSelectField("users_available", $s_users_avail, "", 1, array("style"=>"width: 350px;", "size"=>12));
				$table->endTableData();
				$table->addTableData( array("style"=>"width: 20px") );
					$table->insertAction("add", gettext("add"), "");
					$table->addTag("br");
					$table->insertAction("remove", gettext("remove"), "");
				$table->endTableData();
				$table->addTableData();
					$table->addTag("br");
					$table->addCode( gettext("choosen users").": ");
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
		 * @param int $id The form identifier
		 * @param string $current , or | seperated list of users that are already selected
		 * @param mixed $opts_or_multiple if array all further params are in this array, otherwise allow multiple users selection if 1 and dont if 0
		 * @param int $showArchiveUser include the archive user in possible users if 1 otherwise not (defaults to not)
		 * @param int $showInActive include inactive users in possible users if 1 otherwise not (defaults to not)
		 * @param int $no_empty if 1 a user must be selected if 0 selection can be empty (defaults to 0)
		 * @param int $showGroups if 1 include groups in the selection list otherwise dont (defaults to 0)
		 * @param int $confirmation if 1 show confirmation dialog when removing user from selectionlist otherwise dont (defaults to 0)
		 * @param int $showCalendar if 1 only show users that have shared their calendar with the current logged in user (defaults to 0)
		 *
		 * @return string html code to show the user + search options
		 */
		public function user_selection($id, $current="", $opts_or_multiple=0, $showArchiveUser=0, $showInActive=0, $no_empty=0, $showGroups=0, $confirmation=0, $showCalendar=0) {
			if (!is_array($opts_or_multiple)) {
				/* use old style parameters */
				$multiple = $opts_or_multiple;
			} else {
				/* use new (array) style parameters */
				$opts = $opts_or_multiple;
				if ($opts["multiple"])     $multiple = 1;
				if ($opts["archiveuser"])  $showArchiveUser = 1;
				if ($opts["inactive"])     $showInActive = 1;
				if ($opts["noempty"])      $no_empty = 1;
				if ($opts["groups"])       $showGroups = 1;
				if ($opts["confirm"])      $confirmation = 1;
				if ($opts["calendar"])     $showCalendar = 1;

			}


			$current = str_replace(",", "|", $current);
			$current = explode("|", $current);
			foreach ($current as $k=>$v) {
				if (!$v) {
					unset($current[$k]);
				}
			}
			/* single user mode conflicts with groups */
			if ($multiple == 0) {
				$showGroups = 0;
			}
			$user_data = new User_data();

			$output = new Layout_output();
			if (preg_match("/MSIE/s", $_SERVER["HTTP_USER_AGENT"]))
				$style = "display: list-item; text-align: left; position: relative; left: -20px;";
			else
				$style = "display: inline; text-align: left;";

			$output->addTag("ul", array(
				"id"   => "user_name_".$id,
				"style" => $style
			));

			$enabled_users  = $user_data->getUserList(1);
			$disabled_users = $user_data->getUserList(0);
			$calendar_users = $user_data->getUserList(1, "0", "0", $_SESSION["user_id"]);
			$groups         = $user_data->getGroupList(1);
			$archive_uid    = $user_data->getArchiveUserId();
			foreach ($current as $k=>$v) {
				if ($enabled_users[$v]) {
					$output->addTag("li", array("class"=>"enabled"));
				} elseif ($disabled_users[$v]) {
					$output->addTag("li", array("class"=>"disabled"));
				} elseif ($calendar_users[$v]) {
					$output->addTag("li", array("class"=>"calendar"));
				} elseif ($groups[$v]) {
					$output->addTag("li", array("class"=>"group"));
				} else {
					$output->addTag("li", array("class"=>"special"));
				}
				$output->addCode( $user_data->getUserNameById($v) );
				$output->addSpace();
				if ($confirmation) {
					$output->addTag("a", array(
						"onclick" => "return confirm(gettext('Are you sure you want to remove this user / group?'));",
						"href" => "javascript: remove_user('$v', '$id', 'user_name_$id', '$no_empty');"
					));
				} else {
					$output->addTag("a", array(
						"href" => "javascript: remove_user('$v', '$id', 'user_name_$id', '$no_empty');"
					));
				}
				$output->addCode("[X]");
				$output->endTag("a");
				$output->endTag("li");
			}

			$output->endTag("ul");
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

			$output->addTextField("user_autocomplete_$id", gettext("search"), array(
				"style"     => "width: 100px;",
				"onkeydown" => "return scanKeyCode();"
			));
			$output->insertAction("edit", gettext("choose user(s)"),
				sprintf("javascript: user_select_popup('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
					$id, $multiple, $showInActive, $showEmpty, $showArchiveUser, $no_empty, $showGroups, $confirmation, $showCalendar
			));

			$output->load_javascript(self::include_dir."autocomplete.js");
			$output->start_javascript();
			$output->addCode("
				//in opera arrow down doesn't work with onkeyup, so fixed with keypress, other onkeyup works okay with opera
				if (jQuery.browser.opera) {
					document.getElementById('user_autocomplete_$id').onkeypress  = function(e) {
					autouser_complete_field('user_autocomplete_$id', 'user_name_$id', '$id',
					'$showArchiveUser', '$multiple', '$no_empty', '$showGroups', '$archive_uid', '$confirmation',
					'$showCalendar', e);
					}
				}
				//other browsers works okay with onkeyup
				document.getElementById('user_autocomplete_$id').onkeyup = function(e) { autouser_complete_field('user_autocomplete_$id', 'user_name_$id', '$id', '$showArchiveUser', '$multiple', '$no_empty', '$showGroups', '$archive_uid', '$confirmation', '$showCalendar', e);
				}
				//function to empty the field
				document.getElementById('user_autocomplete_$id').onfocus = function() {
					document.getElementById('user_autocomplete_$id').value = '';
				}
			");
			$output->end_javascript();

			return $output->generate_output();
		}
		/* }}} */
		/* user_selection_output {{{ */
		/**
		 * Show ul with users and their state
		 *
		 * @param string $current comma seperated list of users to show
		 *
		 * @return string the html code for the <ul>
		 */
		public function user_selection_output($current) {
			$current = str_replace(",", "|", $current);
			$current = explode("|", $current);
			foreach ($current as $k=>$v) {
				if (!$v) {
					unset($current[$k]);
				}
			}
			$user_data = new User_data();
			$output = new Layout_output();
			$output->addTag("ul", array(
				"display" => "inline"
			));

			$enabled_users  = $user_data->getUserList(1);
			$disabled_users = $user_data->getUserList(0);
			$calendar_users = $user_data->getUserList(1,'0', '0', $_SESSION["user_id"]);
			$groups         = $user_data->getGroupList(1);
			$archive_uid    = $user_data->getArchiveUserId();

			foreach ($current as $k=>$v) {
				if ($enabled_users[$v]) {
					$output->addTag("li", array("class"=>"enabled"));
				} elseif ($disabled_users[$v]) {
					$output->addTag("li", array("class"=>"disabled"));
				} elseif ($calendar_users[$v]) {
					$output->addTag("li", array("class"=>"calendar"));
				} elseif ($groups[$v]) {
					$output->addTag("li", array("class"=>"group"));
				} else {
					$output->addTag("li", array("class"=>"special"));
				}
				$output->addCode( $user_data->getUserNameById($v) );
				$output->endTag("li");
			}

			$output->endTag("ul");
			return $output->generate_output();

		}
		/* }}} */
		/* usersaved {{{ */
		/**
		 * Show the user all user settings have been changed
		 */
		public function usersaved() {
			$output = new Layout_output();
			$output->layout_page(gettext("User settings - saved"));
				$venster = new Layout_venster(array("title"=>gettext("users")));
				$venster->addMenuItem(gettext("back"), "index.php?mod=user");
				$venster->generateMenuItems();
				$venster->addVensterData();
					$venster->addCode(gettext("settings have been saved."));
					$venster->addTag("br");
					$venster->addCode(gettext("Some settings won't be visible till you logout and login again."));
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
		/* addUserField obsolete(?) {{{ */
		public function addUserField($name, $showArchiveUser=0, $showInActive=0, $selected_values="", $multiple = 0, $settings="", $id="") {
			$output = new Layout_output();

			$userData = new User_data();
			if ($showArchiveUser) {
				$archive = $userData->getArchiveUserId();
				$list[gettext("special choice")] = array($archive=>gettext("archiveuser"));
			}
			if ($showEmpty) {
				$leeg = array("0"=>gettext("no user"));
				$list[gettext("special choice")] = array_merge($leeg, $list[gettext("special choice")]);
			}
			$list[gettext("active users")] = $userData->getUserList();

			if ($showInActive) {
				$list[gettext("inactive")] = $userData->getUserList(0);
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
			$user_data = new User_data();
			$data = $user_data->getUserDetailsById($_SESSION["user_id"]);

			if ($data["xs_usermanage"])
				phpinfo();
			else
				echo "You are not allowed here!";

			exit();
		}
		/* }}} */
		/* php_apc {{{ */
		/**
		 * Show the output of apc.php, if exists, only when a user is logged in
		 */
		public function php_apc() {
			$user_data = new User_data();
			$data = $user_data->getUserDetailsById($_SESSION["user_id"]);

			if ($data["xs_usermanage"])
				if (file_exists("/usr/share/php/apc.php"))
					require("/usr/share/php/apc.php");
				else
					echo "APC.php not found, cannot read stats!";
			else
				echo "You are not allowed here!";

			exit();
		}
		/* }}} */
		/* showTime {{{ */
		/**
		 * Put current time in clock_time div on top of the page (for use in AJAX).
		 */
		public function showTime() {
			/* get the userinfo */
			/* XXX Not needed anymore I guess
			$user_data = new User_data();
			$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);
			//echo sprintf("document.getElementById('clock_time').innerHTML = '%s';", date("H:i:"));
			if ($userinfo["showpopup"]) {
				$desktop_data = new Desktop_data();
				if (is_array($desktop_data->getAlertInfo()))
					echo "show_alerts();";
				unset($desktop_data);
			}
			//close this connection
			//header("Connection: close");
			//exit();
			 */
		}
		/* }}} */
		/* randomOutput {{{ */
		/**
		 * Output some random data so we can prevent caching in javascript (for use in AJAX).
		 */
		public function randomOutput() {
			echo md5(time()*rand());
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
		/* showHelp {{{ */
		/**
		 * Show the help window (navigation panel)
		 */
		public function showHelp() {
			$user_data = new User_data();
			/* check permissions */

			/* start output */
			$output = new Layout_output();
			$output->layout_page("help", 1);
				$output->start_javascript();
					$output->addCode("
						location.href='http://sourceforge.net/forum/forum.php?forum_id=590728';
					");
				$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* show_online {{{ */
		/**
		* include the show_online.php page
		*/
		public function show_online() {
			require(self::include_dir."show_online.php");
		}
		/* }}} */
		/* showManagerAccess {{{ */
		/**
		 * Show all manager access levels, with overview of the users that have that access
		 */
		public function showManagerAccess() {
			require(self::include_dir."showManagerAccess.php");
		}
		/* }}} */
	}
?>
