<?php
/**
 * Covide Groupware-CRM Classification module
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
Class Classification {

	/* variables */

	/* methods */

	/* __construct {{{ */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "cla_edit" :
			case "edit" :
				$classification_output = new Classification_output();
				$classification_output->show_edit($_REQUEST["id"], $_REQUEST["addresstype"]);
				break;
			case "cla_save" :
			case "save" :
				$classification_data = new Classification_data();
				$classification_data->store2db($_REQUEST["cla"]);
				break;
			case "remove"  :
			case "delete"  :
				$classification_data = new Classification_data();
				$classification_data->removecla($_REQUEST["id"]);
				break;
			case "pick_cla" :
				$classification_output = new Classification_output();
				$classification_output->pick_cla();
				break;
			case "select_classification":
				$classification_output = new Classification_output();
				$classification_output->select_classification();
				break;
			default :
				$classification_output = new Classification_output();
				$classification_output->show_classifications();
				break;
		}
	}
	/* }}} */
}
?>
