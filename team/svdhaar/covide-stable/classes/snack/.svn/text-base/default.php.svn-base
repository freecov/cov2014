<?php
/**
 * Covide Groupware-CRM Snack module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Snack {

	/* variables */

	/* methods */

	/* __construct {{{ */
	/**
	 * __construct. TODO Single line description
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
			case "emptylist" :
				$snack_output = new Snack_output();
				$snack_output->empty_list();
				break;
			case "addsnacks" :
				$snack_output = new Snack_output();
				$snack_output->edit_snacks();
				break;
			case "savesnacks" :
				$snack_output = new Snack_output();
				$snack_output->save_snacks();
				break;
			case "additems" :
				$snack_output = new Snack_output();
				$snack_output->add_items();
				if(!$_REQUEST["id"]) { $id = 0; }
				break;
			case "itemlist" :
				$snack_output = new Snack_output();
				$snack_output->item_list();
				break;
			case "who_has" :
				$snack_output = new Snack_output();
				$snack_output->who_has($_REQUEST["snack_id"]);
				break;	
			default :
				$snack_output = new Snack_output();
				$snack_output->generate_list();
				break;
		}
	}
	/* }}} */
}
?>
