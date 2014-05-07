<?php
  # 
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
if (!class_exists("Address_output")) {
        die("no class definition found");
}
if ($_REQUEST["bankaction"] == "remove") {
        $sql = sprintf("DELETE FROM address_finance_bank WHERE id=%d AND address_id=%d", $_REQUEST["bankid"], $_REQUEST["cardid"]);
        $res = sql_query($sql);
}
/* get the data from db */
$address_data = new Address_data();
$financialData = $address_data->getFinanceByRelationID($cardid);
$financialBankData = $address_data->getFinanceBankDetailsByRelationID($cardid);
$output = new Layout_output();
$output->layout_page("", 1);
/* nice window widget */
$venster = new Layout_venster( array( "title"=>gettext("addressbook"), "subtitle"=>gettext("financialcard") ));
$venster->addMenuItem(gettext("close"), 
	"javascript:window.close();");
$venster->addMenuItem(gettext("edit"), 
	"index.php?mod=address&action=financialedit&cardid=$cardid");
$venster->addMenuItem(gettext("add bank account"), 
	"index.php?mod=address&action=financialbankedit&cardid=$cardid");
$venster->generateMenuItems();
$venster->addVensterData();

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

/* address record */
	$history = new Layout_history();
	$link = $history->generate_history_call();
	$venster->addCode($link);

	$view = new Layout_view();
	$view->addData(array($financialData));
	$view->addMapping(gettext("Tax number"), "%tax_nr");
	$view->addMapping(gettext("Accijnsnumber"), "%acc_nr");
	$view->addMapping(gettext("Ecotax"), "%ecotax");
	$view->addMapping(gettext("Payment condition"), "%pay_remark");
	$view->addMapping(gettext("Kingid"), "%kingid");
	$venster->addCode($view->generate_output_vertical(1));
	unset($view);
$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

foreach ($financialBankData as $bankData)
{
	$venster = new Layout_venster( array("title"=>$bankData["desc"]) );
	$venster->addVensterData();
	$view = new Layout_view();
	$view->addData(array($bankData));
	$view->addMapping(gettext("Name"), "%name");
	$view->addMapping(gettext("Address"), "%address");
	$view->addMapping(gettext("Zip and City"), "%%address_complete");
	$view->defineComplexMapping("address_complete", array(
		array("text"=>array("%zip"," ","%place") ) ));
	$view->addMapping(gettext("Province and Country"), "%%location_complete");
	$view->defineComplexMapping("location_complete", array(
		array("text"=>array("%province"," - ","%country"), 
                      "check"=>"%country" ) ));
	$view->addMapping(gettext("IBAN"), "%iban");
	$view->addMapping(gettext("BIC"), "%bic");
	$venster->addCode($view->generate_output_vertical(1));
	$venster->insertAction("edit",   gettext("edit"),    
			"index.php?mod=address&action=financialbankedit&cardid=$cardid&bankid=".$bankData["id"]);
	$venster->insertAction("delete", gettext("delete"), 
			"javascript: remove_item( $cardid, ".$bankData["id"].")");
	unset($bankData);
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	unset($view);
}
unset($financialBankData);
$buf2->insertAction("new", gettext("add bank account"), 
	"index.php?mod=address&action=financialbankedit&cardid=$cardid");

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
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
$output->endTag("center");

$output->load_javascript(self::include_dir."financialcard_actions.js");

$output->layout_page_end();
echo $output->exit_buffer();
?>
