<?php
/**
 * Covide Groupware-CRM RSS module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Rss {
	/* variables */

	/* methods */
    /* 	__construct {{{ */
    /**
	 * run this class
     */
	public function __construct() {
		switch ($_REQUEST["action"]) {
			case "listFeeds" :
				$rss_output = new Rss_output();
				$rss_output->listFeeds();
				break;
			case "editFeed" :
				$rss_output = new Rss_output();
				$rss_output->editFeed($_REQUEST["feedid"]);
				break;
			case "saveFeed" :
				$rss_data = new Rss_data();
				$rss_data->saveFeed($_REQUEST);
				$rss_output = new Rss_output();
				$rss_output->closepopup();
				break;
			case "removeFeed" :
				$rss_data = new Rss_data();
				$rss_data->removeFeed($_REQUEST["id"]);
				$rss_output = new Rss_output();
				$rss_output->listFeeds();
				break;
			default:
				$rss_data = new Rss_data();
				$rss_data->updateFeeds();
				break;
		}
	}
    /* }}} */
}
?>
