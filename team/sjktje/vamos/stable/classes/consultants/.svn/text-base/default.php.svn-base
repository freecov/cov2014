<?php
/**
 * Covide/Vamos Consultants module
 *  
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Svante Kvarnstrom <sjk@ankeborg.nu>
 * @copyright Copyright 2006 Svante Kvarnstrom
 * @package Vamos
 */
 
/*
 *  Copyright (C) 2006 Svante Kvarnstrom
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
 *  USA
 */

class Consultants {

/* __construct() {{{ */
	/**
	 * Class constructor for Consultants
	 *
	 * The constructor will check if the user is logged in (otherwise it'll
	 * redirect them to the login page.) If the user is logged in the constructor
	 * will figure out what object to create and what method to call. 
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}

		switch ($_REQUEST["action"]) {
			/* {{{ case "add": */
			case "add":
				$consultants_output = new Consultants_output();
				$consultants_output->editConsultant();
				break;
			/* }}} */
			/* {{{ case "edit": */
			case "edit":
				$consultants_output = new Consultants_output();
				$consultants_output->editConsultant($_REQUEST["id"]);
				break;
			/* }}} */
			/* {{{ case "save": */
			case "save":
				$consultants_data = new Consultants_data();
				if ($consultants_data->saveConsultant($_POST)) {
					$consultants_output = new Consultants_output();
					$consultants_output->editConsultant($_POST["consultants"]["id"]);
				} else {
					die("need proper error screen here!");
				}
				break;
			/* }}} */
			/* {{{ case "saveec": */
			case "saveec":
				$customers_data = new Customers_data();
				$customers_data->linkConsultantCompany($_REQUEST["consultant_id"],$_REQUEST["saveec"]["results"]);
				break;
			/* }}} */
			/* {{{ case "search_company": */
			case "search_company":
				$consultants_output = new Consultants_output();
				$consultants_output->searchCompany($_REQUEST["consultant_id"],$_REQUEST);
				break;
			/* }}} */
			/* {{{ case "show_item": */
			case "show_item":
				$consultants_output = new Consultants_output();
				$consultants_output->showItem($_REQUEST["id"]);
				break;
			/* }}} */
			/* {{{ default: */
			default:
		        $consultants_output = new Consultants_output();
				$consultants_output->listConsultants();
				break;
			/* }}} */
		}
	} /* }}} */

}
?>
