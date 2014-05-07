<?php
/**
 * Covide Groupware-CRM Filesys module
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
Class Google {

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
		/* do login check */
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "gdownload":
				$google_data = new Google_data();
				$google_data->gdownload($_REQUEST["file"]);
				break;
			case "gattach":
				$google_data = new Google_data();
				$google_data->gdownload($_REQUEST["id"], $_REQUEST["mail_id"]);
				break;
			case "gtoken":
				$google_data = new Google_data();
				$google_data->gtoken($_REQUEST["token"]);
				break;
			case "chart":
				$google_output = new Google_output();
				$google_output->createGoogleCharts($_REQUEST["param"]);
				break;
			default:
				/* you've no business here, go away! */
				header("Location: index.php?mod=filesys");
				break;
		}
	}
}
?>
