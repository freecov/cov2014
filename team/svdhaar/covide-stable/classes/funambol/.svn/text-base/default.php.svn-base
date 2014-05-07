<?php
/**
 * Covide Syncronisation module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Funambol {

	/* variables */

	/* methods */

	/* constants */

	public function __construct() {

		switch ($_REQUEST["action"]) {
			#case "reset":
			#	$funambol = new Funambol_data();
			#	$funambol->reset_user($_REQUEST["user_id"]);
			#	break;
			case "rebuild":
				$funambol = new Funambol_data((int)$_REQUEST["user_id"]);

				$funambol->syncUser();
				$funambol->truncateUserStore();
				$funambol->syncUser();

				$funambol->upgradeFnbl();
				break;
			case "rebuild_import":
				$funambol = new Funambol_data((int)$_REQUEST["user_id"]);

				$funambol->truncateCovideStore();
				$funambol->syncUser();
				$funambol->upgradeFnbl();
				break;
			case "sync":
				$funambol = new Funambol_data((int)$_REQUEST["user_id"]);
				$funambol->syncUser();
				break;
			default :
				$funambol = new Funambol_data();
				break;

		}
	}
}
?>
