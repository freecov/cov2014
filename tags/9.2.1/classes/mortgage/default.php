<?php
/**
 * Covide Groupware-CRM Mortgage module
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
Class Mortgage {

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
				$Mortgage_output = new Mortgage_output();
				$Mortgage_output->MortgageEdit();
				break;
			case "save":
				$Mortgage_output = new Mortgage_output();
				$Mortgage_output->MortgageSave();
				break;
			case "delete":
				$Mortgage_data = new Mortgage_data();
				$Mortgage_data->MortgageDelete($_REQUEST["id"]);

				$Mortgage_output = new Mortgage_output();
				$Mortgage_output->generate_list();
				break;

			default :
				$Mortgage_output = new Mortgage_output();
				$Mortgage_output->generate_list();
				break;
		}
	}
	/* }}} */
}
?>
