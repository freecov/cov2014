<?php
/**
 * Covide Groupware-CRM support module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
Class Support {
	/* constants */
	/* variables */
	/* methods */
	/* __construct {{{ */
	/**
	 * controller to choose action based on url params
	 *
	 * @return void
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_issues"]) {
			die("no license for this module.");
		}
		switch ($_REQUEST["action"]) {
		case "remove_external_item":
			$support_data = new Support_data();
			$support_data->remove_ext_item($_REQUEST["id"], $_REQUEST["xml"]);
			break;
		case "list_external":
			$support_output = new Support_output();
			$support_output->show_list_external();
			break;
		case "edit":
			$support_output = new Support_output();
			$support_output->show_edit();
			break;
		case "save":
			$support_data = new Support_data();
			$support_data->save2db();
			break;
		case "showitem":
			$support_output = new Support_output();
			$support_output->show_issue();
			break;
		case "export":
			$support_output = new Support_output();
			$support_output->export($_REQUEST);
			break;
		case "show_info":
			$support_output = new Support_output();
			$support_output->show_info($_REQUEST["id"]);
			break;
		default:
			$support_output = new Support_output();
			$support_output->show_list();
			break;
		}
	}
	/* }}} */
}
?>
