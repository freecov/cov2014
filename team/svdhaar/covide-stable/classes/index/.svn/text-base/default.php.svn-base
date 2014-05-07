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
Class Index {

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
		} else {
			$sql = sprintf("select is_active from users where id = %d", $_SESSION["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_active"] != 1) {
				unset($_SESSION["user_id"]);
				$GLOBALS["covide"]->trigger_login();
			}
		}
		switch ($_REQUEST["action"]) {
			default :
				$sales_output = new Index_output();
				$sales_output->show_index();
				break;
		}
	}
	/* }}} */
}
?>
