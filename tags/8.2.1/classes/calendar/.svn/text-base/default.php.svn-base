<?php
	/**
	 * Covide Groupware-CRM calendar module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2008 Covide BV
	 * @package Covide
	 */
	Class Calendar {
		/* constants */
		/* variables */
		/* methods */

		/* {{{ __construct */
		/**
		 * controller to choose action based on url params
		 */
		public function __construct() {
			if ( !$_SESSION["user_id"] && $_REQUEST["action"] != "convert_to_new" &&
                             $_REQUEST["action"] != "externalcalendarsync" ) {
				$GLOBALS["covide"]->trigger_login();
			}
			switch ($_REQUEST["action"]) {
				case "km" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_km();
					break;
				case "xml_check" :
					$calendar_data = new Calendar_data();
					$calendar_data->xml_check();
					break;
				case "edit" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_edit((int)$_REQUEST["id"]);
					break;
				case "save" :
					$calendar_data = new Calendar_data();
					$calendar_data->save2db($_REQUEST["appointment"]);
					break;
				case "deleteallrep" :
					$calendar_data = new Calendar_data();
					$calendar_data->deleteallrep($_REQUEST["id"], $_REQUEST["user_id"]);
					$calendar_output = new Calendar_output();
					if ($_REQUEST["returnto"] == "monthview") {
						$calendar_output->show_monthview();
					} else {
						$calendar_output->show_main();
					}
					break;
				case "deleteonerep" :
					$calendar_data = new Calendar_data();
					$calendar_data->deleteonerep($_REQUEST["id"], $_REQUEST["user_id"], $_REQUEST["datestamp"]);
					$calendar_output = new Calendar_output();
					if ($_REQUEST["returnto"] == "monthview") {
						$calendar_output->show_monthview();
					} else {
						$calendar_output->show_main();
					}
					break;				
				case "delete" :
					$calendar_data = new Calendar_data();
					$calendar_data->delete($_REQUEST["id"]);
					$calendar_output = new Calendar_output();
					switch ($_REQUEST["returnto"]) {
						case "monthview":
							$calendar_output->show_monthview();
							break;
						case "monthview_mu":
							$user_data = new User_data();
							$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
							$mu_user = ($_REQUEST["user"]) ? $_REQUEST["user"] : $userinfo["calendarselection"];
							$calendar_output->show_monthview_mu($mu_user);
							break;
						default:
							$calendar_output->show_main();
							break;
					}
					break;
				case "show_info" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_info();
					break;
				/* hour registration cases */
				case "reg_input" :
					$calendar_output = new Calendar_output();
					$calendar_output->reg_input();
					break;
				case "batch_reg_input" :
					$calendar_output = new Calendar_output();
					$calendar_output->batch_reg_input($_REQUEST["id"], $_REQUEST["project_id"]);
					break;
				case "misc_reg_input" :
					$calendar_output = new Calendar_output();
					$calendar_output->misc_reg_input($_REQUEST["id"], $_REQUEST["project_id"]);
					break;
				case "batch_reg_save" :
				case "misc_reg_save" :
				case "reg_save" :
					$calendar_data = new Calendar_data();
					$calendar_data->reg_save();
					break;
				case "reg_delete_xml" :
					$calendar_data = new Calendar_data();
					$calendar_data->reg_delete_xml($_REQUEST["id"]);
					break;
				/* end hour reg */
				case "search" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_searchres();
					break;
				case "addressemails" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_emails();
					break;
				case "permissionintro" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_permissions();
					break;
				case "permissionsave" :
					$calendar_data = new Calendar_data();
					$calendar_data->save_permissions($_REQUEST["calperm"]);
					break;
				case "init_calendar" :
					$calendar_data = new Calendar_output();
					$calendar_data->init_calendar();
					break;
				case "monthview" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_monthview();
					break;
				case "monthview_mu" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_monthview_mu($_REQUEST["user"]);
					break;
				case "show_planning" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_planning();
					break;
				case "print_main" :
					$calendar_ouput = new calendar_output();
					$calendar_ouput->print_main();
					break;
				case "print_week" :
					$calendar_output = new Calendar_output();
					$calendar_output->print_week();
					break;
				case "print_month" :
					$calendar_output = new Calendar_output();
					$calendar_output->print_month();
					break;
				case "notificationtemplate" :
					$calendar_output = new Calendar_output();
					$calendar_output->show_notificationtemplate($_REQUEST["user_id"]);
					break;
				case "savenotification" :
					$calendar_data = new Calendar_data();
					$calendar_data->save_notification($_REQUEST);
					$calendar_output = new Calendar_output();
					$calendar_output->show_notificationtemplate($_REQUEST["user_id"]);
					break;
				case "ask_repeat" :
					$calendar_output = new Calendar_output();
					$calendar_output->ask_repeat($_REQUEST);
					break;
				case "convert_to_new" :
					$calendar_data = new Calendar_data();
					$calendar_data->migrate_calendar01();
					break;
				case "externalcalendarsync" :
					$calendar_data = new Calendar_data();
					$calendar_data->externalCalendarSync($_REQUEST["user_id"]);
				default :
					if ($GLOBALS["covide"]->license["has_funambol"]) {
						$funambol_data = new Funambol_data();
						$funambol_data->checkRecords("calendar");
						unset($funambol_data);
					}

					/* user can set their calendar to default month or day view. */
					$user_data = new User_data();
					$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
					$calendar_output = new Calendar_output();

					/* mode == 1: daily view. Can be forced by url param or session var */
					if ($userinfo["calendarmode"] == 1 || $_REQUEST["forceday"] == 1 || $_SESSION["calendar_forceday"]) {
						/* if forced and usersetting is monthview, store in session var */
						if ($_REQUEST["forceday"] == 1 && $userinfo["calendarmode"] == 2) {
							$_SESSION["calendar_forceday"] = 1;
						}
						/* show daily view */
						$calendar_output->show_main();
					} elseif ($userinfo["calendarmode"] == 2 && $userinfo["calendarselection"]) {
						/* show monhtly view + multi user */
						$calendar_output->show_monthview_mu($userinfo["calendarselection"]);
					
					} elseif ($userinfo["calendarmode"] == 2) {
						/* show monhtly view */
						$calendar_output->show_monthview();
					} else {
						/* show week view */
						$calendar_output->show_planning();
					}
					break;
			}
		}
		/* }}} */
	}
?>
