<?php
  # 
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
if (!class_exists("Address_output")) {
        die("no class definition found");
}

if ($_REQUEST["relcardaction"] == "cardrem") {
        $sql = sprintf("DELETE FROM address_relations WHERE id=%d", $_REQUEST["id"]);
        $res = sql_query($sql);
}

/* get the data from db */
$address_data = new Address_data();
$customerOrSupplierData = $address_data->getCustomerOrSupplierByAdressID($cardid);
$relationsData = $address_data->getRelationsByAdressID($cardid, $customerOrSupplierData);

$output = new Layout_output();
$output->layout_page("", 1);
/* nice window widget */
$venster = new Layout_venster( array( "title"=>gettext("addressbook"), "subtitle"=>gettext("relationscard") ));
$venster->addMenuItem(gettext("close"), 
	"javascript:window.close();");

if ($customerOrSupplierData["is_supplier"])
	$venster->addMenuItem(gettext("add customer"), 
		"index.php?mod=address&action=relationsedit&cardid=$cardid&type=addcustomer");

if ($customerOrSupplierData["is_customer"])
	$venster->addMenuItem(gettext("add supplier"), 
		"index.php?mod=address&action=relationsedit&cardid=$cardid&type=addsupplier");

$venster->generateMenuItems();
$venster->addVensterData();

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

$history = new Layout_history();
$link = $history->generate_history_call();
$venster->addCode($link);

/* customer info */
if ($customerOrSupplierData["is_supplier"]) {
	$venster2 = new Layout_venster(array("title"=>gettext("Customers")));
	$venster2->addVensterData();
	$view = new Layout_view();
	$view->addData($relationsData["customer"]);
	$view->addMapping("", "%%complex_actions");
	$view->addMapping(gettext("Customer"), "%name");
	$view->addMapping(gettext("Payment Remark"), "%pay_remark");
	$view->addMapping(gettext("Bank Account"), "%bankname");
	$view->addMapping(gettext("Transporter"), "%transporter_name");
	$view->addMapping(gettext("Meta"), "%meta");
	$view->defineComplexMapping("complex_actions", array(
    		array(
                        "type"    => "action",
                        "src"     => "edit",
                        "alt"     => gettext("bewerken"),
                        "link"    => array("index.php?mod=address&action=relationsedit&type=editcustomer&cardid=","%id")),
                array(
                        "type"    => "action",
                        "src"     => "delete",
                        "alt"     => gettext("verwijderen"),
                        "link"    => array("javascript:if(confirm('This will delete this relationship. Are you sure you want to continue?')){document.location.href='index.php?mod=address&action=relationsshow&cardid=$cardid&history=".$_REQUEST["history"]."&relcardaction=cardrem&id=", "%id", "';}"))
                ));
	$venster2->addCode($view->generate_output());
	$venster2->endVensterData();
	$venster2->insertAction("new", gettext("add customer"), 
		"index.php?mod=address&action=relationsedit&cardid=$cardid&type=addcustomer");
	$venster->addCode($venster2->generate_output());
	unset($view);
	unset($venster2);

	$venster->addTag("br");
	$venster->addTag("br");
}

if ($customerOrSupplierData["is_customer"]) { 
	$venster2 = new Layout_venster(array("title"=>gettext("Suppliers")));
	$venster2->addVensterData();
	$view = new Layout_view();
	$view->addMapping("", "%%complex_actions");
	$view->addData($relationsData["supplier"]);
	$view->addMapping(gettext("Supplier"), "%name");
	$view->addMapping(gettext("Payment Remark"), "%pay_remark");
	$view->addMapping(gettext("Bank Account"), "%bankname");
	$view->addMapping(gettext("Transporter"), "%transporter_name");
	$view->addMapping(gettext("Meta"), "%meta");
	$view->defineComplexMapping("complex_actions", array(
    		array(
                        "type"    => "action",
                        "src"     => "edit",
                        "alt"     => gettext("bewerken"),
                        "link"    => array("index.php?mod=address&action=relationsedit&type=editsupplier&cardid=","%id")),
                array(
                        "type"    => "action",
                        "src"     => "delete",
                        "alt"     => gettext("verwijderen"),
                        "link"    => array("javascript:if(confirm('This will delete this relationship. Are you sure you want to continue?')){document.location.href='index.php?mod=address&action=relationsshow&cardid=$cardid&history=".$_REQUEST["history"]."&relcardaction=cardrem&id=", "%id", "';}"))
                ));
	$venster2->addCode($view->generate_output());
	$venster2->endVensterData();
	$venster2->insertAction("new", gettext("add supplier"), 
		"index.php?mod=address&action=relationsedit&cardid=$cardid&type=addsupplier");
	$venster->addCode($venster2->generate_output());
	unset($view);
	unset($venster2);
}
unset($relationsData);

$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
#	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();

$output->addCode($tbl->generate_output());

$history = new Layout_history();
$output->addCode( $history->generate_save_state() );

/* back and top links */
$output->addTag("br");
$output->addTag("center");

$url = $_SERVER["QUERY_STRING"];
$output->insertAction("up", gettext("go to top"), "?$url#top");
$output->insertAction("cancel", gettext("terug"), "javascript: window.close();");
$output->endTag("center");

$output->load_javascript(self::include_dir."financialcard_actions.js");

$output->layout_page_end();
echo $output->exit_buffer();
?>
