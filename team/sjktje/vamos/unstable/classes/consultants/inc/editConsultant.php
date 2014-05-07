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
 * This page is included in the editConsultant($id=0); function. If $id is set
 * to an integer, an already filled in form will be displayed. The information
 * will be retrieved from the database -- SELECT * FROM nrd_consultants WHERE
 * id=$id. If no integer is passed to the function we'll display an empty form
 * which'll allow the user to add a new consultant.
 */

if (!class_exists("Consultants_output")) {
	die("no class definition found");
}

/*
 * We need a consultants_data object in order to fetch necessary info from the 
 * db, for example valid category types and so on.
 */
$consultants_data = new Consultants_data();

/* 
 * We need a customers_data object in order to fetch any companies linked to this
 * consultant. 
 */
$customers_data = new Customers_data();

/* Get list of available categories. */
$categories = $consultants_data->getCategories();

/* Get list of available competence areas */
$competence = $consultants_data->getCompetence();

/*
 * If an id has been passed to us, get information about the consultant with 
 * that id. Also get the id of the consultants filesys dir, as well as the 
 * selected categories + competence for the particular consultant (if any.)
 */
$consultant = Array();
if ($id>0) {
	$consultant = $consultants_data->getConsultants(array("id" => $id));
	$filesys_data = new Consultants_filesys();
	$consultant_dir = $filesys_data->getConsultantDir($id);
	$selected_categories = $consultants_data->getCategories($id);
	$selected_competence = $consultants_data->getCompetence($id);

	/*
	 * We need to count the log entries in order to know if we should display
	 * the "Log (view):" link or not.
	 */
	$logbook_data = new Logbook_data();
	$log_count = $logbook_data->getLogEntryCount("consultants", $id);
}

/*
 * Create layout. This includes the top menu listing modules. If we'd like to
 * skip displaying it we'd have to send "1" to $output->layout_page().
 */
$output = new Layout_output();
$output->layout_page(gettext("Consultants"));


/* Make new frame */
$frame = new Layout_venster(Array(
	"title"    => gettext("Consultants"),
	"subtitle" => $id >= 1 ? gettext("Modify consultant") : gettext("Add consultant") 
)); 
  
/* {{{ Left hand side menu */
$frame->addMenuItem(gettext("Add consultant"), 'index.php?mod=consultants&action=add');
$frame->addMenuItem(gettext("List consultants"), 'index.php?mod=consultants');
$frame->generateMenuItems();
/* }}} */

$frame->addVensterData();

/* {{{ Add/modify consultant form */
$frame->addTag("form", Array(
	"id"      => "consultantedit",
	"action"  => "index.php",
	"method"  => "post",
	"enctype" => "multipart/form-data"
));

$frame->addHiddenField("mod", "consultants");
$frame->addHiddenField("action", "save");
$frame->addHiddenField("consultants[id]", $id);

$table = new Layout_table(array("cellspacing" => 3));

if ($consultant["is_beingchecked"]) {
	$table->addTableRow();
		$table->insertTableData(gettext("Consultant is being checked!"), array("colspan" => "3", "style" => "text-align: center"), "data");
	$table->endTableRow();
}
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("First name"), "consultants[firstname]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[firstname]", $consultant["firstname"], 
				array("onchange" => "validatePresent(this, 'inf_firstname');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_firstname"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Surname"), "consultants[surname]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[surname]", $consultant["surname"],
				array("onchange" => "validatePresent(this, 'inf_surname');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_surname"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Social security number"), "consultants[ssn]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[ssn]", $consultant["ssn"],
				array("onchange" => "validateSSN(this, 'inf_ssn', 1);"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_ssn"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Prescription code"), "consultants[prescription_code]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[prescription_code]", $consultant["prescription_code"],
				array("onchange" => "validatePrescriptionCode(this, 'inf_prescription_code', 1);"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_prescription_code"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Address"), "consultants[address]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[address]", $consultant["address"],
				array("onchange" => "validatePresent(this, 'inf_address');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_address"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Address")." 2", "consultants[address2]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[address2]", $consultant["address2"]);
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_address2"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Zipcode"), "consultants[zipcode]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[zipcode]", $consultant["zipcode"],
				array("onchange" => "validatePresent(this, 'inf_zipcode');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_zipcode"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("City"), "consultants[city]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[city]", $consultant["city"],
				array("onchange" => "validatePresent(this, 'inf_city');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_city"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Email"), "consultants[email]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[email]", $consultant["email"],
				array("onchange" => "validateEmail(this, 'inf_email', 1);"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_email"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Phone number"), "consultants[phone_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[phone_nr]", $consultant["phone_nr"],
				array("onchange" => "validateTelnr(this, 'inf_phone_nr');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_phone_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Mobile number"), "consultants[mobile_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[mobile_nr]", $consultant["mobile_nr"],
				array("onchange" => "validateTelnr(this, 'inf_mobile_nr');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_mobile_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Fax number"), "consultants[fax_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[fax_nr]", $consultant["fax_nr"],
				array("onchange" => "validateTelnr(this, 'inf_fax_nr');"));
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_fax_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Clearing number"), "consultants[clearing_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[clearing_nr]", $consultant["clearing_nr"]);
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_clearing_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Account number"), "consultants[account_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[account_nr]", $consultant["account_nr"]);
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_account_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Tax"), "consultants[tax]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[tax]", $consultant["tax"]);
			$table->addSpace(2);
			$table->addTag("span", array("id" => "inf_tax"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Employee number"), "consultants[employee_nr]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextField("consultants[employee_nr]", $consultant["employee_nr"]);
			$table->addTag("span", array("id" => "inf_employee_nr"));
				$table->addSpace(1);
			$table->endTag("span");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Companies"), "consultants[companies]");
		$table->endTableData();
		$companies = $consultants_data->getConsultantCompanies($id);
	if (!is_array($companies))
		$companies = array();
		$ctable = new Layout_table(array("cellspacing" => 1));
		foreach ($companies as $comp) {
			$ctable->addTableRow();
				$ctable->addTableData("", "data");
					$link_href = "javascript: snsInfo(".$comp["id"].");";
					$ctable->insertLink($comp["company_name"], array("href" => $link_href));
				$ctable->endTableData();
				$ctable->addTableData("", "data");
				$link_href = "?mod=customers&action=edit&type=2&consultant_id=$id&id=".$comp["id"];
				$ctable->insertAction("edit", "", $link_href);
			$ctable->endTableRow();
		}
		$ctable->addTableRow();
			$ctable->addTableData("", "data");
				$ctable->insertLink(gettext("Add new company"), 
					array("href" => "?mod=customers&action=edit&type=2&consultant_id=$id"));
			$ctable->endTableData();
		$ctable->endTableRow();
		$ctable->addTableRow();
			$ctable->addTableData("", "data");
				$ctable->insertLink(gettext("Add existing company"),
					array("href" => "javascript: popup('?mod=consultants&action=search_company&consultant_id=$id','searchcompany',300,200,1);"));
			$ctable->endTableData();
		$ctable->endTableRow();
		$ctable->endTable();
		$table->insertTableData($ctable->generate_output(), "", "data");
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("Categories"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("consultants[categories][]", $categories, 
				$selected_categories == NULL ? "" : $selected_categories, 1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("Competence"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("consultants[competence][]", $competence, 
				$selected_competence == NULL ? "" : $selected_competence, 1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("Is"), "", "header");
		$table->addTableData("", "data");
			$table->addCheckBox("consultants[is_interested_in_oncall_duty]", 1, $consultant["is_interested_in_oncall_duty"]);
			$table->addSpace(1);
			$table->addLabel(gettext("interested in oncall duty"), "consultants[is_interested_in_oncall_duty]");
			$table->addSpace(1);
			$table->addCheckBox("consultants[is_beingchecked]", 1, $consultant["is_beingchecked"]);
			$table->addSpace(1);
			$table->addLabel(gettext("being checked"), "consultants[is_beingchecked]");
			$table->addSpace(1);
			$table->addCheckBox("consultants[is_blacklisted]", 1, $consultant["is_blacklisted"]);
			$table->addSpace(1);
			$table->addLabel(gettext("blacklisted"), "consultants[is_blacklisted]");
		$table->endTableData();
	$table->endTableRow();

	/* 
	 * The file view/upload stuff may only be shown when editing an already added 
	 * consultant. This is because we don't know the id of the consultants directory
	 * before the consultant's been added. One way to go around this would be to add
	 * a dummy user to the database when bringing the 'Add new consultant' page up, 
	 * get its id and create the directories needed, and then edit that dummy user.
	 *
	 * But I don't think that's necessary right now. The user will simply have to 
	 * save the new consultant information, and then view the consultant again to
	 * upload files. 
	 */
	if ($consultant_dir) {
		$table->addTableRow();
			$table->insertTableData(gettext("Files"), "", "header");
			$table->addTableData("", "data");
				/* TODO: Fix so that the menues etc aren't shown when using the filesystem in a popup window */
				//$link_href = "javascript: popup('?mod=filesys&action=opendir&id=$consultant_dir','showentries',600,500,0)";
				$link_href = "?mod=filesys&action=opendir&id=$consultant_dir";
				$table->insertLink(gettext("Click Here To View/Upload Files"), array(
					"href" => $link_href, 
					"target" => "_blank"
				));
			$table->endTableData();
		$table->endTableRow();
	}

	$table->addTableRow();
		if ($log_count > 0) {
			$table->addTableData("", "header");
			$link_href = "javascript: popup('?mod=logbook&regmod=consultants&id=$id','showentries',600,500,1)";
			$table->insertLink(gettext("Log (view)"), array("href" => $link_href));
		} else {
			$table->addTableData("", "header");
				$table->addLabel(gettext("Log"), "consultants[log]");
			$table->endTableData();
		}
		$table->addTableData("", "data");
			$table->addTextArea("consultants[log]", $log["log"], array("style" => "width: 500px; height: 100px;"));
		$table->endTableData(); 
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addLabel(gettext("Other"), "consultants[other]");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addTextArea("consultants[other]", $consultant["other"], array("style" => "width: 500px; height: 100px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("Save"), "", "header");
		$table->addTableData("", "data");
			$table->insertAction("save", "spara", "javascript: document.getElementById('consultantedit').submit();");
		$table->endTableData();
	$table->endTableRow();
$table->endTable();

/* Focus "firstname" if we're adding a new user */
if (!isset($consultant["firstname"])) {
	$table->start_javascript();
		$table->addCode("document.getElementById('consultantsfirstname').focus();");
	$table->end_javascript();
}

/* Add the $table code to $frame's output buffer */
$frame->addCode($table->generate_output());

/* Clean up memory. */
unset($table);

$frame->endTag("form");
/* }}} */

$frame->endVensterData();

/* Add $frame's output to $output's buffer */
$output->addCode($frame->generate_output());

/* [michiel] clean up memory */
unset($frame);

$history = new Layout_history();
$output->addCode($history->generate_save_state());

$output->load_javascript("classes/html/inc/js_form_validation.js");
$output->load_javascript("classes/consultants/inc/show_info.js");

/* Closing tags etc */
$output->layout_page_end();

/* print the output buffer */
$output->exit_buffer();
?>
