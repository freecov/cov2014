<?php
/**
 * Covide Groupware-CRM Sales module
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
Class Sales {

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
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "edit":
				$sales_output = new Sales_output();
				$sales_output->salesEdit();
				break;
			case "save":
				$sales_output = new Sales_output();
				$sales_output->salesSave();
				break;
			case "delete":
				$sales_data = new Sales_data();
				$sales_data->salesDelete($_REQUEST["id"]);

				$sales_output = new Sales_output();
				$sales_output->generate_list();
				break;

			default :
				$sales_output = new Sales_output();
				$sales_output->generate_list();
				break;
		}
	}
	/* }}} */
}
?>
