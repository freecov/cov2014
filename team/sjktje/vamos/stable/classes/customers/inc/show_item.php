<?php
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

if (!class_exists("Customers_output")) 
	die("no class definition found");

if (!$id) 
	die("no id");


$customers_data = new Customers_data();
$customer = $customers_data->getCustomers(array("id" => $id));
$contacts = $customers_data->getContacts(array("customer_id" => $id));
$logbook_data = new Logbook_data();
$logentry = $logbook_data->getLastLogEntry(array(
	"module" => "customers",
	"record_id" => $id,
	"limit" => 50
));

$table = new Layout_table();

$table->addTableRow();
	$table->insertTableData(gettext("Company name"), "", "header");
	$table->insertTableData($customer["company_name"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Billing address"), "", "header");
	$table->insertTableData($customer["billing_address"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Telephone number"), "", "header");
	$table->insertTableData($customer["telephone_nr"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Fax number"), "", "header");
	$table->insertTableData($customer["fax_nr"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Website"), "", "header");
	$table->addTableData("", "data");
		$table->insertLink($customer["website"], array("href" => $customer["website"]));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Expense number"), "", "header");
	$table->insertTableData($customer["expense_nr"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Organisation number"), "", "header");
	$table->insertTableData($customer["organisation_nr"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Customer number"), "", "header");
	$table->insertTableData($customer["customer_nr"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Address"), "", "header");
	$table->insertTableData($customer["address"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "header");
	$table->insertTableData($customer["address2"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Zipcode"), "", "header");
	$table->insertTableData($customer["zipcode"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("City"), "", "header");
	$table->insertTableData($customer["city"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Country"), "", "header");
	$table->insertTableData($customer["country"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Last log entry"), "", "header");
	$table->addTableData("", "data");
		$table->addCode(gettext("added by"). " ".$logentry["username"]." ". 
			$logentry["timestamp"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "header");
	$table->insertTableData($logentry["message"], "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Contact persons"), "", "header");
	$ctable = new Layout_table(array("cellspacing" => 1));
	foreach ($contacts as $c) {
		$ctable->addTableRow();
			$ctable->addTableData("", "data");
				$ctable->insertLink($c["firstname"]." ".$c["surname"].", ".$c["title"], 
				array("href" => "mailto:".$c["email"]));
			$ctable->endTableData();
		$ctable->endTableRow();
	}
	$ctable->endTable();
	$table->insertTableData($ctable->generate_output(), "", "data");
$table->endTableRow();
$table->endTable();

$buf = addslashes(preg_replace("/(\r|\n)/si", "", $table->generate_output()));

print sprintf("infoLayer('%s');", $buf);
?>
