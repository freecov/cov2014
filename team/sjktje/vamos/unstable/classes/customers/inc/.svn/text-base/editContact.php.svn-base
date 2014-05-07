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

/* 
 * $id == id of contact to edit
 * $customer_id == id of customer which contact is related to.
 *
 * If $id isn't sent we'll add a new contact. If this is the case, $customer_id
 * must be sent! 
 */
if (!class_exists("Customers_output")) 
	die("no class definition found");

$customers_data = new Customers_data();
$output = new Layout_output();
$contact = "";

/* If id is set and is above 0, we want to edit a contact. */
if ($id > 0) {
	$contact = $customers_data->getContacts(array("id" => $id)); 
	$title = gettext("alter");
} else {
	$title = gettext("add");
}

//if (!$id && !$customer_id)
//die("no id");

$output->layout_page($title, 1);

$frame = new Layout_venster(array(
	"title"    => gettext("contacts"),
	"subtitle" => $title
));

$frame->addVensterData();

$output->addTag("form", array(
	"id"      => "contactedit",
	"action"  => "index.php",
	"method"  => "post",
	"enctype" => "multipart/form-data"
));

$frame->addHiddenField("mod", "customers");
$frame->addHiddenField("action", "save_contact");
$frame->addHiddenField("contact[id]", $id);
$frame->addHiddenField("customers[id]", $customer_id);

/* {{{ Add/modify contact form */
$table = new Layout_table(array("cellspacing" =>1));
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Firstname"), "contact[firstname]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[firstname]", $contact["firstname"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Surname"), "contact[surname]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[surname]", $contact["surname"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Title"), "contact[title]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[title]", $contact["title"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Email"), "contact[email]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[email]", $contact["email"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Address"), "contact[address]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[address]", $contact["address"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "header"); // $table->addSpace(1) doesn't work? 
	$table->addTableData("", "data");
		$table->addTextField("contact[address2]", $contact["address2"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Zipcode"), "contact[zipcode]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[zipcode]", $contact["zipcode"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("City"), "contact[city]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[city]", $contact["city"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Country"), "contact[country]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[country]", $contact["country"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Phone number"), "contact[phone_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[phone_nr]", $contact["phone_nr"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Mobile number"), "contact[mobile_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[mobile_nr]", $contact["mobile_nr"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Other"), "contact[other]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[other]", $contact["other"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("PO box"), "contact[pobox]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[pobox]", $contact["pobox"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("PO box zipcode"), "contact[pobox_zipcode]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[pobox_zipcode]", $contact["pobox_zipcode"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("PO box city"), "contact[pobox_city]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("contact[pobox_city]", $contact["pobox_city"]);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Save"), "", "header");
	$table->addTableData("", "data");
		$table->insertAction("save", "save", 
			"javascript: document.getElementById('contactedit').submit(); ".
			"window.opener.location.reload(); var t = setTimeout('window.close();', 100)");
	$table->endTableData();
$table->endTableRow();
$table->endTable();

/* Focus the firstname field if we're adding a new user */
if (!isset($contact["firstname"])) {
	$table->start_javascript();
		$table->addCode("document.getElementById('contactfirstname').focus();");
	$table->end_javascript();
}
/* }}} */

$frame->addCode($table->generate_output());
unset($table);

$frame->endTag("form");

$frame->endVensterData();

$output->addCode($frame->generate_output());

$output->layout_page_end();

$output->exit_buffer();
?>
