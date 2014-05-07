<?php
/**
 * Covide Groupware-CRM Twinfield integration
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Twinfield {
	/* constants */
	const include_dir = "classes/twinfield/inc/";
	const class_name  = "twinfield";
	/* variables */
	/* methods */

	/* __construct {{{ */
	/**
	 * controller to choose action based on url params
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		} else {
			$sql = sprintf("select is_active from users where id = %d", $_SESSION["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_active"] != 1) {
				unset($_SESSION["user_id"]);
				$GLOBALS["covide"]->trigger_login();
			}
		}
		switch ($_REQUEST["action"]) {
			case "GetCluster" :
				$twinfield_data = new twinfield_data();
				$twinfield_data->GetCluster();
				break;
			case "getBrowseFields" :
				$twinfield_data = new twinfield_data();
				$twinfield_data->getBrowseFields();
				break;
			case "getOffices" :
				$twinfield_data = new twinfield_data();
				$twinfield_data->getOffices();
				break;
			case "getCustomers" :
				$twinfield_data = new twinfield_data();
				$twinfield_data->getCustomers();
				break;
			case "getFinancialsById" :
				$twinfield_data = new twinfield_data();
				$res = $twinfield_data->getFinancialsById($_REQUEST["address_id"]);
				echo $res;
				break;
			case "saveAddress" :
				$address = array(
					"code"           => 1313,
					"companyname"    => "Vanbaak Inc.",
					"url"            => "http://example.com",
					"contact_person" => "Dhr. M.C.C. van Baak",
					"address"        => "example street 1",
					"zipcode"        => "1111 ex",
					"city"           => "examplia",
					"country"        => "NL",
					"phonenr"        => "+31 123456789",
					"faxnr"          => "+31 123456781",
					"email"          => "mvanbaak@users.sourceforge.net",
					"administration" => "601"
				);
				$twinfield_data = new twinfield_data();
				if ($twinfield_data->saveAddress($address))
					echo "saved";
				else
					echo "not saved";
				break;
			default :
				die("no action specified");
				break;
		}
	}
	/* }}} */
}
