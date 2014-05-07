<?php
/**
 * Covide Groupware-CRM Templates module
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
Class Templates {

	/* variables */

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		if (!$_SESSION["user_id"] && $_REQUEST["action"] != "view_file") {
			$GLOBALS["covide"]->trigger_login();
		} else if ($_REQUEST["action"] != "view_file") {
			$sql = sprintf("select is_active from users where id = %d", $_SESSION["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_active"] != 1) {
				unset($_SESSION["user_id"]);
				$GLOBALS["covide"]->trigger_login();
			}
		}

		switch ($_REQUEST["action"]) {
			case "delete_finance":
				$data = new Templates_data();
				$data->templateDeleteFinance($_REQUEST["finance"]);
				break;
			case "delete":
				$data = new Templates_data();
				$data->templateDelete($_REQUEST["id"]);
				if ($_REQUEST["back_address_id"]) {
					header("Location: index.php?mod=address&action=relcard&id=".$_REQUEST["back_address_id"]);
					exit();
				} else {
					$output = new Templates_output();
					$output->show_list();
				}
				break;
			case "edit":
				$output = new Templates_output();
				$output->templateEdit();
				break;
			case "save":
				$data = new Templates_data();
				$id = $data->templateSave();
				$output = new Templates_output();
				$output->templateEdit($id);
				break;
			case "selectCla":
				$output = new Templates_output();
				$output->selectCla();
				break;
			case "selectAddress":
				$output = new Templates_output();
				$output->selectAddress();
				break;
			case "settings":
				$output = new Templates_output();
				$output->settingsList();
				break;
			case "edit_settings":
				$output = new Templates_output();
				$output->settingsEdit();
				break;
			case "save_settings":
			case "del_file":
				$data = new Templates_data();
				$data->settingsSave();
				if ($_REQUEST["action"] == "del_file") {
					$data->delTemplateFile();
				}

				$output = new Templates_output();
				$output->settingsEdit();
				break;
			case "settingsdelete":
			 	$data = new Templates_data();
			 	$data->settingsDelete($_REQUEST["id"]);
				$output = new Templates_output();
				$output->settingsList();
			case "view_file":
			 	$data = new Templates_data();
			 	$data->showTemplateFile($_REQUEST["id"]);
			 	break;
			case "print":
				$data = new Templates_data();
				$data->templateSave();
				$output = new Templates_output();
				$output->templatePrint();
				break;
			case "calibrate":
				$output = new Templates_output();
				$output->calibratePrinter();
				break;
			default :
				$output = new Templates_output();
				$output->settingsList();
				break;
				/*
				$output = new Templates_output();
				$output->show_list();
				break;
				 */
		}
	}
	/* }}} */
}
?>
