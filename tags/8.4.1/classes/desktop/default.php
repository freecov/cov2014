<?php
/**
 * Covide Groupware-CRM Desktop module
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
Class Desktop {
	/* methods */

	/* __construct() {{{ */
	/**
	 * Class constructor for Desktop
	 *
	 * The constructor will check if a user
	 * is logged in. If so, it will figure out
	 * what object to create and what metod to call
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			/* edit a users personal notes on the desktop */
			case "editnotes":
				$desktop_output = new Desktop_output();
				$desktop_output->edit_notes();
				break;
			/* save a users notes to database */
			case "savenotes":
				$desktop_data = new Desktop_data();
				$desktop_data->save_notes($_REQUEST);
				break;
			/* prefetcher for files */
			case "prefetch":
				$desktop_output = new Desktop_output();
				$desktop_output->show_prefetch();
				break;
			/* alert popup screen */
			case "show_alerts" :
				$desktop_output = new Desktop_output();
				$desktop_output->show_alerts();
				break;
			/* by default, show the desktop */
			default :

				if ($GLOBALS["covide"]->license["has_funambol"]) {
					$funambol_data = new Funambol_data();
					$funambol_data->checkRecords("calendar");
					$funambol_data->checkRecords("todo");
					unset($funambol_data);
				}

				$desktop_output = new Desktop_output();
				$desktop_output->show_desktop();
				break;
			/* end switch statement */
		}
	}
	/* }}} */
}
?>
