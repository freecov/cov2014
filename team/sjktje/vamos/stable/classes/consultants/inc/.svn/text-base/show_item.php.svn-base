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

if (!class_exists("Consultants_output")) 
	die("no class definition found");

if (!$id) 
	die("no id");


$consultants_data = new Consultants_data();
$consultant = $consultants_data->getConsultants(array("id" => $id));
$logbook_data = new Logbook_data();
$logentry = $logbook_data->getLastLogEntry(array(
	"module" => "consultants",
	"record_id" => $id,
	"limit" => 200
));

$table = new Layout_table();

$table->addTableRow();
	$table->insertTableData(gettext("Firstname"), "", "header");
	$table->insertTableData($consultant["firstname"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Surname"), "", "header");
	$table->insertTableData($consultant["surname"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Social security number"), "", "header");
	$table->insertTableData($consultant["ssn"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Prescription code"), "", "header");
	$table->insertTableData($consultant["prescription_code"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Address"), "", "header");
	$table->insertTableData($consultant["address"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "header");
	$table->insertTableData($consultant["address2"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Zipcode"), "", "header");
	$table->insertTableData($consultant["zipcode"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("City"), "", "header");
	$table->insertTableData($consultant["city"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Email"), "", "header");
	$table->addTableData("", "data");
		$table->insertLink($consultant["email"], array("href" => "mailto:".$consultant["email"]));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Phone number"), "", "header");
	$table->insertTableData($consultant["phone_nr"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Mobile number"), "", "header");
	$table->insertTableData($consultant["mobile_nr"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Fax number"), "", "header");
	$table->insertTableData($consultant["fax_nr"], "", "data");
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Employee number"), "", "header");
	$table->insertTableData($consultant["employee_nr"], "", "data");
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
$table->endTable();
/* }}} */

$buf = addslashes(preg_replace("/(\r|\n)/si", "", $table->generate_output()));

print sprintf("infoLayer('%s');", $buf);
?>
