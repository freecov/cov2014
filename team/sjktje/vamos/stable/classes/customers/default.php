<?php
/**
 * Covide/Vamos Customers module
 *
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Svante Kvarnstrom <sjk@ankeborg.nu>
 * @copyright Copyright 2006 Svante Kvarstrom
 * @package Vamos
 */

class Customers {

	/* {{{ __construct() */
	/**
	 * Class constructor for Customers.
	 *
	 * The constructor will check if the user is logged in (otherwise it'll
	 * redirect them to the login page.) If the user is logged in the constructor
	 * will figure out what object to create and what method to call.
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}

		switch($_REQUEST["action"]) {
			/* {{{ case "add": */
			case "add":
				$customers_output = new Customers_output();
				$customers_output->editCustomer(0,$_REQUEST["type"], $_REQUEST["consultant_id"]);
				break;
			/* }}} */
			/* {{{ case "edit": */
			case "edit":
				$customers_output = new Customers_output();
				$customers_output->editCustomer($_REQUEST["id"], $_REQUEST["type"], $_REQUEST["consultant_id"]);
				break;
			/* }}} */
			/* {{{ case "show_info": */
			case "show_info":
				$customers_output = new Customers_output();
				$customers_output->showContact($_REQUEST["id"]);
				break;
			/* }}} */
			/* {{{ case "show_item": */
			case "show_item":
				$customers_output = new Customers_output();
				$customers_output->showItem($_REQUEST["id"]);
				break;
			/* }}} */
			/* {{{ case "show_contact": */
			case "edit_contact":
				$customers_output = new Customers_output();
				$customers_output->editContact($_REQUEST);
				break; 
			/* }}} */
			/* {{{ case "save": */
			case "save":
				$customers_data = new Customers_data();
				if ($customers_data->saveCustomer($_POST)) {
					if ($_POST["customers"]["type"] == 2) {
						$consultants_output = new Consultants_output();
						$consultants_output->editConsultant($_POST["customers"]["consultant_id"]);
					} else {
						$customers_output = new Customers_output();
						$customers_output->editCustomer($_POST["customers"]["id"], $_POST["type"], $_POST["consultant_id"]);
					}
				} else {
					die("Need proper error screen here!");
				}
				break;
			/* }}} */
			/* {{{ case "save_contact": */
			case "save_contact":
				$customers_data = new Customers_data();
				
				/* 
				 * I was thinking that this'd reload the customer page so that
				 * the new (or updated) contact would appear in the list. Thing is,
				 * since we're calling this from a popup window, which has a timeout
				 * set to call .close(), it'll load the page in that window and then 
				 * just close it. So the main window will remain unchanged, and will
				 * not get updated! We'll have to solve this with some javascript magic.
				 */
				if ($customers_data->saveContact($_POST)) {
					$customers_output = new Customers_output();
					$customers_output->editCustomer($_POST["customer_id"]);
				} else {
					die("Need proper error screen here!");
				}
				break;
			/* }}} */
			/* {{{ case "delete_contact": */
			case "delete_contact":
				$customers_data = new Customers_data();
				if ($customers_data->deleteContact($_REQUEST["id"])) {
					$customers_output = new Customers_output();
					$customers_output->editCustomer($_REQUEST["customer_id"]);
				}
				break;
			/* }}} */
			/* {{{ default: */
			default:
				$customers_output = new Customers_output();
				$customers_output->listCustomers();
				break;
			/* }}} */
		}
	} /* }}} */

}
