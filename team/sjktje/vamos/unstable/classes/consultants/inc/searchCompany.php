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

$customers_data = new Customers_data();

if ($data["search_company"]["search"]) 
	$result = $customers_data->searchCustomer($data["search_company"]["search"], 2);

if (!is_array($result)) 
	$result = array();

$output = new Layout_output();
$output->layout_page("Add existing company", 1);

if ($data["done"]) {
	$output->start_javascript();
		$output->addCode("var t = setTimeout('window.close();', 100);");
	$output->end_javascript();
}

$frame = new Layout_venster(array(
	"title"    => gettext("consultants"),
	"subtitle" => gettext("Add existing company")
));

$frame->addVensterData();

/* {{{ Search form */
$table = new Layout_table();

$table->addTag("form", array(
	"id"      => "searchcompany",
	"action"  => "index.php",
	"method"  => "post",
	"enctype" => "multipart/form-data"
));

$table->addHiddenField("mod", "consultants");
$table->addHiddenField("action", "search_company");
$table->addHiddenField("consultant_id", $id);

$table->addTableRow();
	$table->addTableData("", "data");
		$table->addLabel(gettext("Search")."", "search_company[search]");
	$table->endTableData();
	$table->addTableData("", "data");
	$table->addTextField("search_company[search]", "");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData("&nbsp;", "", "data");
$table->endTableRow();
$table->endTag("form");
$frame->addCode($table->generate_output());
/* }}} */
/* {{{ Add company form */
$table = new Layout_table();
$table->addTag("form", array(
	"id"      => "saveec",
	"action"  => "index.php",
	"method"  => "post",
	"enctype" => "multipart/form-data"
));

$table->addHiddenField("mod", "consultants");
$table->addHiddenField("action", "saveec");
$table->addHiddenField("consultant_id", $id);
$table->addHiddenField("done", 1);

$table->addTableRow();
	$table->insertTableData(gettext("Results"), "", "header");
	$table->addTableData("", "data");
		$table->addSelectField("saveec[results]", $result);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Save"), "", "header");
	$table->addTableData("", "data");
		$table->insertAction("save", "save", 
		"javascript: document.getElementById('saveec').submit(); ".
		"window.opener.location.reload(); var t = setTimeout('window.close();', 100)");
	$table->endTableData();
$table->endTableRow();
$table->endTable();
$table->endTag("form");
$frame->addCode($table->generate_output());
/* }}} */ 

$frame->endVensterData();
$output->addCode($frame->generate_output());

$output->layout_page_end();

$output->exit_buffer();
?>
