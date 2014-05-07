<?php
/**
 * Covide Groupware-CRM Newsletter module
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
Class Newsletter {

	/* constants */
	const include_dir = "classes/newsletter/inc/";
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
			case "getAddresses":
				$news = new Newsletter_output();
				$news->getAddresses();
				break;
			case "selectClassification":
				$news = new Newsletter_output();
				$news->selectClassification();
				break;
			case "selectFormat":
				$news = new Newsletter_output();
				$news->selectFormat();
				break;
			default:
				$news = new Newsletter_output();
				$news->selectTargetGroup();
				break;

		}

	}
	/* }}} */
}
?>
