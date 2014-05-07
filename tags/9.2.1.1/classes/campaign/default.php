<?php
/**
 * Covide Groupware-CRM Campaign module
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
Class Campaign {

	/* constants */
	const include_dir = "classes/campaign/inc/";
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

		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "new":
				$campaign_output = new Campaign_output();
				$campaign_output->show_new();
				break;
			case "new2":
				$campaign_output = new Campaign_output();
				$campaign_output->show_new2();
				break;
			case "editcampaign" :
				$campaign_output = new Campaign_output();
				$campaign_output->edit_campaign($_REQUEST["id"]);
				break;
			case "save":
				$campaign_data = new Campaign_data();
				$campaign_data->saveCampaign($_REQUEST);
				break;
			case "delete":
				$campaign_data = new Campaign_data();
				$campaign_data->deleteCampaign($_REQUEST["id"]);
				$campaign_output = new Campaign_output();
				$campaign_output->show_list();
				break;
			case "open":
				$campaign_output = new Campaign_output();
				$campaign_output->show_contents($_REQUEST["id"]);
				break;
			case "open_specific":
				$campaign_output = new Campaign_output();
				$campaign_output->show_specific_contents($_REQUEST["id"], $_REQUEST["answer"]);
				break;
			case "edit_record":
				$campaign_output = new Campaign_output();
				$campaign_output->show_edit_record($_REQUEST["id"]);
				break;
			case "show_script":
				$campaign_output = new Campaign_output();
				$campaign_output->show_script($_REQUEST["id"]);
				break;
			case "save_edit_record":
				$campaign_data = new Campaign_data();
				$campaign_data->save_edit_record($_REQUEST["id"], $_REQUEST["options"], $_REQUEST["callscript"], $_REQUEST["calltime"]);
				break;
			case "callscript":
				$campaign_data = new Campaign_data();
				$campaign_data->callscript($_REQUEST["id"]);
				break;
			case "recallscript":
				$campaign_data = new Campaign_data();
				$campaign_data->recallscript($_REQUEST["id"]);
				break;
			case "refreshcla":
				$campaign_data = new Campaign_data();
				$campaign_data->refreshClassifications($_REQUEST["id"]);
				break;
			default:
				$campaign_output = new Campaign_output();
				$campaign_output->show_list();
				break;
		}
	}
	/* }}} */
}
?>
