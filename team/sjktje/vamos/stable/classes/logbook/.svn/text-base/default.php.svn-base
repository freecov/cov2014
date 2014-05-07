<?php
/**
 * Covide Groupware-CRM Logbook module
 * 
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Svante Kvarnstrom <sjktje@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Svante Kvarnstrom
 * @package Covide
 */
Class Logbook {
	
	/* __construct() {{{ */
	/**
	 * Class constructor for Logbook.
	 * 
	 * The constructor will check if the user is logged in (otherwise it'll 
	 * redirect them to the login page.) If the user is logged in the constructor 
	 * will figure out what object to create and what method to call. 
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		
		switch($_REQUEST["action"]) {
			default:
				$logbook_output = new Logbook_output();
				$logbook_output->showLogEntries($_REQUEST);
				break;
		}

	} /* }}} */

}
