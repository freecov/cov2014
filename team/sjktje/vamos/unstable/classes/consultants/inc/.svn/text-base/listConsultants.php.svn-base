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

if (!class_exists("Consultants_output")) {
        die("no class definition found");
}

/* define top */
$list_from = $_REQUEST["list_from"];
if ($list_from == "") $list_from = 0;

/* collect all the consultants */
$consultant_data = new Consultants_data();
$consultants = $consultant_data->getConsultants(array(
	"id"     => 0,
	"sort"   => $_REQUEST["sort"],
	"search" => $_REQUEST["search"],
	"top"    => $list_from));

/* seperate data and extra info */
$total_count = $consultants["total_count"];
$consultants = $consultants["data"];

//Begin 
$output = new Layout_output();
$output->layout_page(gettext("Consultants"));

//Create frame
$frame = new Layout_venster(Array(
	"title"    => gettext("Consultants"),
	"subtitle" => gettext("List"),
));

/* Left hand side menu  */
$frame->addMenuItem(gettext("Add consultant"), 'index.php?mod=consultants&action=add');
$frame->addMenuItem(gettext("List consultants"), 'index.php?mod=consultants');
$frame->generateMenuItems();

$frame->addVensterData();

/* Let's list the consultants! */
$view = new Layout_view();

/* {{{ consultant search form */
$table = new Layout_table(array("cellspacing" => 3));
$table->addTag("form", array(
	"id"     => "search",
	"method" => "post",
	"action" => "index.php"
));

$table->addHiddenField("mod", "consultants");
$table->addHiddenField("sort", $_REQUEST["sort"]);
$table->addHiddenField("list_from", $list_from);
$table->start_javascript();
$table->addCode("
	function entsub(event,ourform) {
		if (event && event.which == 13)
			ourform.submit();
		else
			return true;
	}");
$table->end_javascript();

	$table->addTableRow();
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("First name").":", "search[firstname]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[firstname]", $_REQUEST["search"]["firstname"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("Surname").":", "search[surname]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[surname]", $_REQUEST["search"]["surname"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("Email").":", "search[email]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[email]", $_REQUEST["search"]["email"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("Competence").":", "search[competence]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[competence]", $_REQUEST["search"]["competence"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();  
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("Prescription code").":", "search[prescription_code]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[prescription_code]", $_REQUEST["search"]["prescription_code"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
		$table->addTableData();
			$table->addCode($output->nbspace(3));
			$table->addLabel(gettext("City").":", "search[city]");
		$table->endTableData();
		$table->addTableData();
			$table->addTextField("search[city]", $_REQUEST["search"]["city"], 
				array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->endTableData();
		$table->insertTableData("&nbsp;", "data");
		$table->addTableData();
			// TODO: Make an extension of addCheckBox to make this hack unnecessary
			$name = "search[is_beingchecked]";
			$table->addTag("input", array("type"=>"checkbox", "id"=>preg_replace("/(\[)|(\])|( )/s", "", $name), 
				"name" => $name, "checked" => $_REQUEST["search"]["is_beingchecked"], "value" => "1",
				"onchange" => "return document.getElementById('search').submit()"));
			$table->addCode($output->nbspace(1));
			$table->addLabel(gettext("Is being checked"), "search[is_beingchecked]");
			//$table->addCheckBox("search[is_beingchecked]", 1, $_REQUEST["search"]["is_beingchecked"]);
		$table->endTableData();
		$table->addTableData();
			$table->insertAction("forward", "search", 
				"javascript: document.getElementById('search').submit();");
		$table->endTableData();
	$table->endTableRow();
$table->endTable();

$table->endTag("form");

$frame->addCode($table->generate_output());
/* }}} */

/* {{{ mapping and sorting definitions */
/* add data we want in a list */
$view->addData($consultants);

$view->defineSortForm("sort", "search");

/* define the columns with their data */

/* The firstname is trickier than the other entries because you're supposed to
 * be able to click it and be able to modify/view all of the consultants saved
 * info. We need to use defineComplexMapping().
 */
 /* 
  * Update: We don't use a complex mapping now since we use the icons on the right
  * to view / modify information. 
  */
/*
$view->addMapping(gettext("firstname"), "%%firstname");

$view->defineComplexMapping("firstname", array(
  array(
        type => "link",
        link => array("?mod=consultants&action=edit&id=", "%id"),
        text => "%firstname"
  )
));
*/

$view->addMapping(gettext("firstname"), "%firstname");
$view->addMapping(gettext("surname"), "%surname");
$view->addMapping(gettext("email"), "%%email");

$view->defineComplexMapping("email", array(
	array(
		type => "link",
		link => array("mailto:", "%email"),
		text => "%email"
	)
));

$view->addMapping(gettext("phone number"), "%phone_nr");
$view->addMapping(gettext("mobile number"), "%mobile_nr");
$view->addMapping(gettext("prescription code"), "%prescription_code");
$view->addMapping(gettext("city"), "%city");

$view->addMapping("&nbsp;", "%%actions", "", "", "", 1);
$view->defineComplexMapping("actions", array(
	array(
		"type" => "action",
		"src"  => "info",
		"alt"  => gettext("more information"),
		"link" => array("javascript: showItem(", "%id", ");"),
		//"link" => array("?mod=consultants&action=show_info&id=", "%id")
	),
	array(
		"type" => "action",
		"src"  => "edit",
		"alt"  => gettext("edit"),
		"link" => array("?mod=consultants&action=edit&id=", "%id")
	)
), "nowrap");

/* [michiel] If you have a form you must NOT use the defineSortParam because this will undo the form */
#$view->defineSortParam("sort");
$view->defineSort(gettext("firstname"), "firstname");
$view->defineSort(gettext("surname"), "surname");
$view->defineSort(gettext("email"), "email");
$view->defineSort(gettext("phone number"), "phone_nr");
$view->defineSort(gettext("mobile number"), "mobile_nr");
$view->defineSort(gettext("prescription code"), "prescription_code");
$view->defineSort(gettext("city"), "city");

/* sample of %%name. This is called a complex mapping.
        with this you can add complex data to the list.
        For example, I want the name and the skill surrounded by () in one field.
        Here's how to do that */
/* $view->addMapping("total", "%%complex_total"); */
/* now create the complex_total content. We do this using defineComplexMapping */
/*
$view->defineComplexMapping("complex_total", array(
        array(
                "type" => "text",
                "text" => array(
                        "%name",
                        " ( ",
                        "%skill",
                        " ) "
                )
        )
));
*/
/* }}} */

/* add the generated list to the frame */
$frame->addCode($view->generate_output());
unset($view);

/* Page links ... */
//$url = "index.php?mod=consultants&action=".$_REQUEST["action"]."&top=%%";
$url = "javascript: gopage(%%);";
$paging = new Layout_paging();
$paging->setOptions($list_from, $total_count, $url);

/* Add page links to $frame's output */
$frame->addCode($paging->generate_output());

$frame->endVensterData();

/* Add $frame's output to $output */
$output->addCode($frame->generate_output());

/* [michiel] clean up memory */
unset($frame);

$output->load_javascript("classes/consultants/inc/show_item.js");

$history = new Layout_history();
$output->addCode($history->generate_save_state());

//End, print buffer.
$output->layout_page_end();

/* Print $output */
$output->exit_buffer();
?>
