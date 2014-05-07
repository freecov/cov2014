<?php
/**
 * Covide ProjectDeclaration module
 *
 * This module has been build based on demands of a specific customer.
 * That's why there are some Dutch text strings in here, and it's based on
 * the Dutch low etc. I dont know how global it is and/or who can use it besides
 * this one customer.
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class ProjectDeclaration {
	/* function __construct {{{  */

	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_project"] || !$GLOBALS["covide"]->license["has_project_declaration"]) {
			die("no license for this module");
		}
		if ($GLOBALS["covide"]->license["has_project_ext"] || $GLOBALS["covide"]->license["has_project_ext_samba"]) {
			die("conflicting license settings found. Please do not use project_ext(_samba) and project_declaration at the same time.");
		}
		switch ($_REQUEST["action"]) {
			case "register_item":
				$project_output = new ProjectDeclaration_output();
				$project_output->registerItem($_REQUEST["project_id"]);
				break;
			case "edit_one":
				$project_output = new ProjectDeclaration_output();
				$project_output->editOne($_REQUEST);
				break;
			case "save_one":
				$project_data = new ProjectDeclaration_data();
				$project_data->saveOne($_REQUEST);
				break;
			case "edit_multi":
				$project_output = new ProjectDeclaration_output();
				$project_output->editMulti($_REQUEST);
				break;
			case "save_multi":
				$project_data = new ProjectDeclaration_data();
				$project_data->saveMulti($_REQUEST);
				break;
			case "save_registration":
				$project_data = new ProjectDeclaration_data();
				$project_data->saveRegistration($_REQUEST);
				break;
			case "generate_document":
				$project_data = new ProjectDeclaration_data();
				$project_data->generateDocument($_REQUEST);
				break;
			case "send_batch":
				$project_output = new ProjectDeclaration_output();
				$project_output->sendBatch($_REQUEST["project_id"]);
				break;
			case "delete_registration":
				$project_data = new ProjectDeclaration_data();
				$project_data->deleteRegistration($_REQUEST["id"]);
				break;
			case "start" :
				$project_output = new ProjectDeclaration_output();
				$project_output->manageOptions();
				break;
		}
	}
	/* }}} */
}
?>
