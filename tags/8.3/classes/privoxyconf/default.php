<?php
/**
 * Covide Groupware-CRM privoxy config module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Privoxyconf {
	/* constants */
	/* variables */
	/* methods */
	public function __construct() {
		switch ($_REQUEST["action"]) {
			case "delete" :
				$proxy_data = new Privoxyconf_data();
				$proxy_data->deleteSite($_REQUEST["site"]);
				$proxy_output = new Privoxyconf_output();
				$proxy_output->showList();
				break;
			case "edit" :
				$proxy_data = new Privoxyconf_data();
				$proxy_data->editSite($_REQUEST["olddata"], $_REQUEST["newdata"]);
				$proxy_output = new Privoxyconf_output();
				$proxy_output->showList();
				break;
			case "add" :
				$proxy_data = new Privoxyconf_data();
				$proxy_data->addSite($_REQUEST["site"]);
				$proxy_output = new Privoxyconf_output();
				$proxy_output->showList();
				break;
			default :
				$proxy_output = new Privoxyconf_output();
				$proxy_output->showList();
				break;
		}
	}
}
?>
