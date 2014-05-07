<?php
/**
 * Covide Googlemaps module
 *
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class Googlemaps {

	/* variables */

	/* methods */

	/* constants */

	public function __construct() {

		switch ($_REQUEST["action"]) {
			case "show_map" :
				$googlemaps_output = new Googlemaps_output();
				$googlemaps_output->showMap($_REQUEST["location"], $_REQUEST["id"]);
				break;
			case "show_route" :
				$googlemaps_output = new Googlemaps_output();
				$googlemaps_output->showRoute($_REQUEST["from"], $_REQUEST["to"]);
				break;
			default :
				break;

		}
	}
}
?>
