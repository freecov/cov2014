<?php
/**
 * Covide Groupware-CRM Dimdim output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
Class Dimdim_output {
	/* constants */
	const include_dir = "classes/dimdim/inc/";

	/* methods */
	/* edit_meeting {{{ */
	/**
	 * Shows a form to edit/create a Dim dim web meeting
	 *
	 * @param array $data - Contains all $_REQUEST data
	 */
	public function edit_meeting($data) {
		require(self::include_dir."edit_meeting.php");

	}
	/* }}} */
}
?>