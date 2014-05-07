<?php

if (!class_exists("Customers_output")) 
	die("no class definition found");

/* define list_from */
$list_from = $_REQUEST["list_from"];
if ($list_from == "") $list_from = 0;

/* collect all the customers */
$customer_data = new Customers_data();
$customers = $customer_data->getCustomers(array(
	"id"        => 0,
	"sort"      => $_REQUEST["sort"],
	"search"    => $_REQUEST["search"],
	"list_from" => $list_from));

/* seperate data and extra info */
$total_count = $customers["total_count"];
$customers   = $customers["data"];

/* begin */
$output = new Layout_output();
$output->layout_page(gettext("Customers"));

/* create frame */
$frame = new Layout_venster(array(
	"title"    => gettext("Customers"),
	"subtitle" => gettext("List")
));

/* Left hand side menu */
$frame->addMenuItem(gettext("Add customer"), "index.php?mod=customers&action=add");
$frame->addMenuItem(gettext("List customers"), "index.php?mod=customers");
$frame->generateMenuItems();

$frame->addVensterData();

/* Let's list the customers! */
$view = new Layout_view();

/* {{{ customer search form */
$table = new Layout_table(array("cellspacing" => 3));
$table->addTag("form", array(
	"id"     => "search",
	"method" => "post",
	"action" => "index.php"
));

$table->addHiddenField("mod", "customers");
$table->addHiddenField("sort", $_REQUEST["sort"]);

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
		$table->addLabel(gettext("Company").":", "search[company_name]");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search[company_name]", $_REQUEST["search"]["company_name"], 
			array("onkeypress" => "return entsub(event,this.form)"), "", 1);
	$table->endTableData();
	$table->addTableData();
		$table->addCode($output->nbspace(3));
		$table->addLabel(gettext("Organisation nr").":", "search[organisation_nr]");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search[organisation_nr]", $_REQUEST["search"]["organisation_nr"], 
			array("onkeypress" => "return entsub(event,this.form)"), "", 1);
	$table->endTableData();
	$table->addTableData();
		$table->addCode($output->nbspace(3));
		$table->addLabel(gettext("Customer nr").":", "search[customer_nr]");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search[customer_nr]", $_REQUEST["search"]["customer_nr"], 
			array("onkeypress" => "return entsub(event,this.form)"), "", 1);
	$table->endTableData();
	$table->addTableData();
		$table->addCode($output->nbspace(3));
		$table->addLabel(gettext("City").":", "search[city]");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search[city]", $_REQUEST["search"]["city"], 
			array("onkeypress" => "return entsub(event,this.form)"), "", 1);
		$table->addSpace(2);
		$table->insertAction("forward", "search", "javascript: document.getElementById('search').submit();");
	$table->endTableData();
$table->endTableRow();
$table->endTable();

$table->endTag("form");

$frame->addCode($table->generate_output());
/* }}} */

/* {{{ mapping and sorting definitions */

/* add data we want in a list */
$view->addData($customers);

$view->defineSortForm("sort", "search");

/* define the columns with their data */

/* The company name is trickier than the other entries because you're supposed to 
 * be able to click it and be able to modify/view all of the customers saved info.
 * We need to use defineComplexMapping() for this.
 *
 * Update: We don't use a complex mapping now since we use the icons on the right
 * to view / modify information.
 */

/*
$view->addMapping(gettext("company name"), "%%company_name");

$view->defineComplexMapping("company_name", array(
	array(
		type => "link",
		link => array("?mod=customers&action=edit&id=", "%id"),
		text => "%company_name"
	)
));
*/

$view->addMapping(gettext("company name"), "%company_name");

$view->addMapping(gettext("organisation number"), "%organisation_nr");
$view->addMapping(gettext("customer number"), "%customer_nr");
$view->addMapping(gettext("city"), "%city");

$view->defineSort(gettext("company name"), "company_name");
$view->defineSort(gettext("organisation number"), "organisation_nr");
$view->defineSort(gettext("customer number"), "customer_nr");
$view->defineSort(gettext("city"), "city");

$view->addMapping("&nbsp;", "%%actions", "", "", "", 1);
$view->defineComplexMapping("actions", array(
	array(
		"type" => "action",
		"src"  => "info",
		"alt"  => gettext("more information"),
		"link" => array("javascript: showItem(", "%id", ");")
	),
	array(
		"type" => "action",
		"src"  => "edit",
		"alt"  => gettext("edit"),
		"link" => array("?mod=customers&action=edit&id=", "%id")
	)
), "nowrap");

/* }}} */

/* Add the generated list to the frame */
$frame->addCode($view->generate_output());
unset($view);

/* Page links ... */
//$url = "index.php?mod=customers&action=".$_REQUEST["action"]."&list_from=%%";
$url = "javascript: gopage(%%);";
$paging = new Layout_paging();
$paging->setOptions($list_from, $total_count, $url);

/* Add page links to $frame's output */
$frame->addCode($paging->generate_output());

$frame->endVensterData();

/* Add $frame's output to $output */
$output->addCode($frame->generate_output());

/* Clean up memory */
unset($frame);

$output->load_javascript("classes/customers/inc/show_item.js");

/* Checkpoint support */
$history = new Layout_history();
$output->addCode($history->generate_save_state());

/* End, print buffer */
$output->layout_page_end();
$output->exit_buffer();
?>
