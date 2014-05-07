<?php
/**
 * Covide Dimdim module
 *
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class Dimdim {

	/* variables */

	/* methods */

	/* constants */

	public function __construct() {
		switch ($_REQUEST["action"]) {
			case "edit_meeting" :
				$dimdim_output = new Dimdim_output();
				$dimdim_output->edit_meeting($_REQUEST);
				break;
			case "save_meeting" :
				$dimdim_data = new Dimdim_data();
				$dimdim_data->save_meeting($_REQUEST);
				break;
			case "delete_meeting" :
				$dimdim_data = new Dimdim_data();
				$dimdim_data->deleteMeeting($_REQUEST["id"]);
				break;
			default :
				break;
		}
	}
}
?>
