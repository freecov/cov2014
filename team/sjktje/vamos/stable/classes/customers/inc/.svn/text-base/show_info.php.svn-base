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

if (!class_exists("Customers_output")) {
	die("no class definition found");
}

$customers_data = new Customers_data();
$contact = $customers_data->getContacts(array("id" => $id));

/* {{{ Contact info listing */
/* TODO: Reorder the rows */
$table = new Layout_table();

$table->addTableRow();
	$table->insertTableData(gettext("Firstname"), "", "header");
	$table->insertTableData($contact["firstname"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Surname"), "", "header");
	$table->insertTableData($contact["surname"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Title"), "", "header");
	$table->insertTableData($contact["title"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Email"), "", "header");
	$table->addTableData("", "data");
		$table->insertLink($contact["email"], array("href" => "mailto:".$contact["email"]));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Address"), "", "header");
	$table->insertTableData($contact["address"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "header"); // $table->addSpace(1) doesn't work? 
	$table->insertTableData($contact["address2"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Zipcode"), "", "header");
	$table->insertTableData($contact["zipcode"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("City"), "", "header");
	$table->insertTableData($contact["city"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Country"), "", "header");
	$table->insertTableData($contact["country"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Phone number"), "", "header");
	$table->insertTableData($contact["phone_nr"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Mobile number"), "", "header");
	$table->insertTableData($contact["mobile_nr"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Other"), "", "header");
	$table->insertTableData($contact["other"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("PO box"), "", "header");
	$table->insertTableData($contact["pobox"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("PO box zipcode"), "", "header");
	$table->insertTableData($contact["pobox_zipcode"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("PO box city"), "", "header");
	$table->insertTableData($contact["pobox_city"], "", "data");
$table->endTableRow();
$table->endTable();

/* }}} */

$buf = addslashes(preg_replace("/(\r|\n)/si", "", $table->generate_output()));

print sprintf("infoLayer('%s');", $buf);
?>
		
