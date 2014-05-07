<?php
/**
 * Covide Groupware-CRM Twitter module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2009 Covide BV
 * @package Covide
 */
Class Twitter {
	/* methods */
	/* __construct {{{ */
	/**
	 * Constructor to find out what part of the data class we need and what to feed it
	 */
	public function __construct() {
		switch($_REQUEST["action"]) {
		case "newtweet" :
			$user_data = new User_data();
			$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
			if ($userinfo["twitter_username"] && $userinfo["twitter_password"]) {
				$twitter_data = new Twitter_data($userinfo["twitter_username"], $userinfo["twitter_password"]);
				$twitter_data->sendMessage($_REQUEST["status"]);
			} else {
				echo "alert('no valid twitter username and/or password supplied');";
			}
			break;
		}
	}
	/* }}} */
}
?>
