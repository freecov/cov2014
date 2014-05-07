<?php
/**
 * Covide Groupware-CRM user module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class User {
	public function __construct() {
		/* dual input for $action. we need to modify the form.action attribute on the login
		   page, but it is not accessible if there is a hidden field named 'action' in the form
		*/
		if ($_REQUEST["subaction"]) {
			$action = $_REQUEST["subaction"];
		} else {
			$action = $_REQUEST["action"];
		}

		if (!$_SESSION["user_id"] && !in_array($action,	array(
			"validate", "translate", "cron", "convert_db", "getUserinfoXML", "logout"))) {
			$GLOBALS["covide"]->trigger_login();
		}

		switch ($action) {
			case "searchUserInit":
				$user = new User_output();
				$user->searchUserInit();
				break;
			case "searchUser":
				$user = new User_output();
				$user->searchUser();
				break;
			case "validate" :
				$user = new User_data();
				if (is_array($_REQUEST["login2"])) {
					var_dump($_REQUEST["login2"]);
					$user->validate_login($_REQUEST["login2"]);
				} else {
					$user->validate_login($_REQUEST["login"]);
				}
				break;
			case "useredit" :
				$useroutput = new User_output();
				$useroutput->useredit();
				break;
			case "useredit_check" :
				$user_data = new User_data();
				$user_data->useredit_check($_REQUEST);
				break;
			case "usersave" :
				$userdata = new User_data();
				$userdata->saveUserSettings($_REQUEST);
				break;
			case "logout" :
				$user = new User_data();
				$user->logout();
				break;
			case "pick_user" :
				$useroutput = new user_output();
				$useroutput->pick_user();
				break;
			case "phpinfo" :
				$useroutput = new User_output();
				$useroutput->php_info();
				break;
			case "autocomplete" :
				$userdata = new User_data();
				$userdata->autocomplete();
				break;
			case "show_time" :
				$userdata = new User_output();
				$userdata->showTime();
				break;
			case "random_output" :
				$userdata = new User_output();
				$userdata->randomOutput();
				break;
			case "translate" :
				$userdata = new User_output();
				$userdata->translateString($_REQUEST["str"]);
				break;
			case "activate" :
				$user_data = new User_data();
				$user_data->activate_user($_REQUEST["user_id"]);
				break;
			case "deactivate" :
				$user_data = new User_data();
				$user_data->deactivate_user($_REQUEST["user_id"]);
				break;
			case "activate_xml" :
				$user_output = new User_output();
				$user_output->activate_user_xml($_REQUEST["user_id"]);
				break;
			case "deactivate_xml" :
				$user_output = new User_output();
				$user_output->deactivate_user_xml($_REQUEST["user_id"]);
				break;
			case "groupindex" :
				$user_output = new User_output();
				$user_output->show_grouplist();
				break;
			case "groupinfo" :
				$user_output = new User_output();
				$user_output->show_groupinfo($_REQUEST["group_id"]);
				break;
			case "save_group" :
				$user_data = new User_data();
				$user_data->save_group($_REQUEST);
				$user_output = new User_output();
				$user_output->show_grouplist();
				break;
			case "delete_group" :
				$user_data = new User_data();
				$user_data->delete_group($_REQUEST["group"]["id"]);
				$user_output = new User_output();
				$user_output->show_grouplist();
				break;
			case "updatePagesize":
				$user_data = new User_data();
				$user_data->updatePagesize((int)$_REQUEST["pagesize"]);
				break;
			case "usernamecheckxml" :
				$user_data = new User_data();
				$user_data->checkUserXML($_REQUEST["username"], $_REQUEST["_userid"]);
				break;
			case "moduleconf":
				$user_output = new User_output();
				$user_output->show_moduleconf();
				break;
			case "saveLicense":
				$user_data = new User_data();
				$user_data->saveLicense($_REQUEST);
				break;
			case "cleanup":
				$user_data = new User_data();
				$user_data->cleanUp();
				break;
			case "help":
				$user_data = new User_output();
				$user_data->showHelp();
				break;
			case "cron":
				$user_data = new User_data();
				$user_data->cron();
				break;
			case "convert_db":
				$convert = new Covide_convert();
				//$convert->convertDb();
				break;
			case "getUserinfoXML":
				$user_data = new User_data();
				$user_data->getUserinfoXML($_REQUEST["username"]);
				break;
			case "show_online" :
				$user_output = new User_output();
				$user_output->show_online();
				break;
			case "manageraccess" :
				$user_output = new User_output();
				$user_output->showManagerAccess();
				break;
			case "removelogo":
				$user_data = new User_data();
				$user_data->removelogo();
				break;
			default :
				$user = new User_output();
				$user->show_index();
				break;
		}
	}
}
?>
