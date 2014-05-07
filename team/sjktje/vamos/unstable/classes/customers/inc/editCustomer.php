<?php
if (!class_exists("Customers_output")) 
	die("no class definition found");

$customers_data = new Customers_data();

$customer = Array();

if ($id>0) { 
	$customer = $customers_data->getCustomers(array("id" => $id));
	$filesys_data = new Customers_filesys();
	$customer_dir = $filesys_data->getCustomerDir($id);
	$logbook_data = new Logbook_data();
	$log_count = $logbook_data->getLogEntryCount("customers", $id);
}

/*
 * Create layout. This includes the top menu listing modules. If we'd like to 
 * skip displaying it we'd have to send "1" to $output->layout_page();
 */
$output = new Layout_output();
$output->layout_page(gettext("Customers"));

/* Make new frame */
$frame = new Layout_venster(Array(
	"title"    => gettext("Customers"),
	"subtitle" => $id>0 ? gettext("Modify customer") : gettext("Add customer")
));

/* {{{ Left hand side menu */
$frame->addMenuItem(gettext("Add customer"), "index.php?mod=customers&action=add");
$frame->addMenuItem(gettext("List customers"), "index.php?mod=customers");
$frame->generateMenuItems();
/* }}} */

$frame->addVensterData();

/* {{{ Add/modify customer form */
$frame->addTag("form", array(
	"id"      => "customeredit",
	"action"  => "index.php",
	"method"  => "post",
	"enctype" => "multipart/form-data"
));

//if ($consultant_id == 0)
//	unset($consultant_id);

if ($type == 0)
	$type = 1;

$frame->addHiddenField("mod", "customers");
$frame->addHiddenField("action", "save");
$frame->addHiddenField("customers[id]", $id);
$frame->addHiddenField("customers[type]", $type);
$frame->addHiddenField("customers[consultant_id]", $consultant_id);


$table = new Layout_table(array("cellspacing" => 3));

$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Company name"), "customers[company_name]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[company_name]", $customer["company_name"],
			array("onchange" => "validatePresent(this, 'inf_company_name');"));
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_company_name"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Billing address"), "customers[billing_address]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[billing_address]", $customer["billing_address"],
			array("onchange" => "validatePresent(this, 'inf_billing_address');"));
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_billing_address"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Telephone number"), "customers[telephone_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[telephone_nr]", $customer["telephone_nr"],
			array("onchange" => "validateTelnr(this, 'inf_telephone_nr');"));
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_telephone_nr"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Fax number"), "customers[fax_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[fax_nr]", $customer["fax_nr"],
			array("onchange" => "validateTelnr(this, 'inf_fax_nr');"));
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_fax_nr"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Website"), "customers[website]");
	$table->endTableData();
	$table->addTableData("", "data");
		/* TODO: Create javascript check which turns www.whatever.com
		 * into http://www.whatever.com before adding. Perhaps we could
		 * also add a "(visit)" link next to the input field which takes
		 * the user to the site in question.
		 */
		$table->addTextField("customers[website]", $customer["website"]); 
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Expense number"), "customers[expense_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[expense_nr]", $customer["expense_nr"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_expense_nr"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Organisation number"), "customers[organisation_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[organisation_nr]", $customer["organisation_nr"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_organisation_nr"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Customer number"), "customers[customer_nr]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[customer_nr]", $customer["customer_nr"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_customer_nr"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableData();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Address"), "customers[address]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[address]", $customer["address"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_address"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Address")." 2", "customers[address2]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[address2]", $customer["address2"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_address2"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Zipcode"), "customers[zipcode]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[zipcode]", $customer["zipcode"],
			array("onchange" => "validateZipcode(this, 'inf_zipcode');"));
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_zipcode"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("City"), "customers[city]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[city]", $customer["city"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_city"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Country"), "customers[country]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextField("customers[country]", $customer["country"]);
		$table->addSpace(2);
		$table->addTag("span", array("id" => "inf_country"));
			$table->addSpace(1);
		$table->endTag("span");
	$table->endTableData();
$table->endTableRow();
/*
 * Only show contact persons when updating/viewing an existing customer.
 * Don't show this when adding a new customer. 
 *
 * TODO: Prettify the contact listing!
 */
if ($id) {
	$table->addTableRow();
		$table->insertTableData(gettext("Contact persons"), "", "header");
		$table->addTableData("", "data");

			$ctable = new Layout_table(array("cellspacing" => 1));
				$contacts = $customers_data->getContacts(Array("customer_id" => $id));
				foreach ($contacts as $c) {
					$ctable->addTableRow();
						$ctable->addTableData("", "data");
							$link_href = "javascript: snsInfo(".$c["id"].");";
							$ctable->insertLink($c["firstname"]." ".$c["surname"].", ".$c["title"], array("href" => $link_href));
						$ctable->endTableData();
						$ctable->addTableData("", "data");
							$link_href = "javascript: popup('?mod=customers&action=edit_contact&customer_id=$id&id=".$c["id"]."','editcontact',600,500,1);";
							$ctable->insertAction("edit", "", $link_href);
							$link_href = "?mod=customers&action=delete_contact&id=".$c["id"]."&customer_id=$id";
							$ctable->insertAction("delete", "", $link_href);
						$ctable->endTableData();
					$ctable->endTableRow();
				}
				$ctable->addTableRow();
					$ctable->addTableData("", "data");
						$link_href = "javascript: popup('?mod=customers&action=edit_contact&customer_id=$id','addcontact',600,500,1);";
						$ctable->insertLink(gettext("Add contact person"), array("href" => $link_href));
					$ctable->endTableData();
				$ctable->endTableRow();
			$ctable->endTable();
			$table->addCode($ctable->generate_output());
		$table->endTableData();
	$table->endTableRow();
}
if ($customer_dir) {
	$table->addTableRow();
		$table->insertTableData(gettext("Files"), "", "header");
		$table->addTableData("", "data");
			$link_href = "?mod=filesys&action=opendir&id=$customer_dir";
			$table->insertLink(gettext("Click Here To View/Upload Files"), array(
				"href"   => $link_href,
				"target" => "_blank"
			));
		$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Quickfacts"), "customers[quickfacts]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextArea("customers[quickfacts]", $customer["quickfacts"], array("style" => "width: 500px; height: 100px;"));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData("", "header");
		$table->addLabel(gettext("Other"), "customers[other]");
	$table->endTableData();
	$table->addTableData("", "data");
		$table->addTextArea("customers[other]", $customer["other"], array("style" => "width: 500px; height: 100px;"));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	if ($log_count > 0) {
		$table->addTableData("", "header");
		$link_href = "javascript: popup('?mod=logbook&regmod=customers&id=$id','showentries',600,500,1)";
		$table->insertLink(gettext("Log (view)"), array("href" => $link_href));
	} else {
		$table->addTableData("", "header");
			$table->addLabel(gettext("Log"), "customers[log]");
		$table->endTableData();
	}
	$table->addTableData("", "data");
		$table->addTextArea("customers[log]", $customers["log"], array("style" => "width: 500px; height: 100px;"));
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->insertTableData(gettext("Save"), "", "header");
	$table->addTableData("", "data");
		$table->insertAction("save", "save", "javascript: document.getElementById('customeredit').submit();");
	$table->endTableData();
$table->endTableRow();
$table->endTable();

/* Focus "company name" if we're adding a new user */
if (!isset($customer["company_name"])) {
	$table->start_javascript();
		$table->addCode("document.getElementById('customerscompany_name').focus();");
	$table->end_javascript();
}

/* Add the $table code to $frame's output buffer */
$frame->addCode($table->generate_output());

/* Clean up memory. */
unset($table);

$frame->endTag("form");
/* }}} */

$frame->endVensterData();

$output->addCode($frame->generate_output());
unset($frame);

$history = new Layout_history();
$output->addCode($history->generate_save_state());

$output->load_javascript("classes/html/inc/js_form_validation.js");
$output->load_javascript("classes/customers/inc/show_info.js");

/* Closing tags etc */
$output->layout_page_end();

/* print the output buffer */
$output->exit_buffer();
?>
