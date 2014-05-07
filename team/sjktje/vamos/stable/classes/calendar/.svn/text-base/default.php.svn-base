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
	 * @copyright Copyright 2000-2006 Covide BV
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
			if (!$_SESSION["user_id"]) {
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
				case "delete" :
					$calendar_data = new Calendar_data();
					$calendar_data->delete($_REQUEST["id"]);
					$calendar_output = new Calendar_output();
					if ($_REQUEST["returnto"] == "monthview")
						$calendar_output->show_monthview();
					else
						$calendar_output->show_main();
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
				case "reg_save" :
					$calendar_data = new Calendar_data();
					$calendar_data->reg_save();
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
					$calendar_output->show_monthview_mu();
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
					} else {
						/* show monhtly view */
						$calendar_output->show_monthview();
					}
					break;
			}
		}
		/* }}} */
	}
?>
