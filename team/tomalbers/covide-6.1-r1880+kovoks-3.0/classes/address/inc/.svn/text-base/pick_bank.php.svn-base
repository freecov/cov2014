<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
if (!class_exists("Address_output")) {
	exit("no class definition found");
}
$output = new Layout_output();
$output->layout_page("", 1);
/* put form around the whole thing */
$output->addTag("form", array(
	"id" => "velden",
	"method" => "get",
	"action" => "index.php"
));
$output->addHiddenField("mod", "address");
$output->addHiddenField("action", $_REQUEST["action"]);
$output->addHiddenField("start", $_REQUEST["start"]);

$deb = $_REQUEST["deb"];
$bankid = $_REQUEST["bankid"];

$output->addHiddenField("deb", $deb);
$output->load_javascript(self::include_dir."pick_bank.js");
/* put a table in place to do some outlining */
$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addCode(gettext("zoeken"));
		$table->addSpace(2);
		$table->addTextField("searchinfo", $_REQUEST["searchinfo"], "", "", 1);
		$table->insertAction("forward", gettext("zoeken"), "javascript: search();");
		$table->addSpace(2);
		$table->insertAction("close", gettext("sluiten"), "javascript: window.close();");

		$table->addTag("br");

		$address_data = new Address_data();
		$address_name = $address_data->getAddressNameById($deb);
		$table->addCode( gettext("relatie").": ".$address_name);
		$table->addSpace(2);
		$table->insertAction("last", gettext("show all bankaccounts"), "javascript: drop_filter();");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData();
		$ids = 0;

		$bank_data = new Address_data();
		$data = $bank_data->getFinanceBankDetailsByRelationID($deb);

		$view = new Layout_view();
		$view->addData($data);
		$view->addMapping(gettext("Supplier"), "%client_name");
		$view->addMapping(gettext("Description"), "%%complex_desc");

 	        $view->defineComplexMapping("complex_desc", array(
                        array(
                                "type" => "link",
                                "link" => array("javascript: selectBank(", "%id", ", '", "%desc", "');"),
                                "text" => "%desc"
                        )
                ), "nowrap");


		$table->addCode($view->generate_output());

		$paging = new Layout_paging();
		$paging->setOptions($_REQUEST["start"], count($data), "javascript: blader('%%');");
		$table->addCode( $paging->generate_output() );
		$table->addTag("br");
		$table->insertAction("delete", gettext("no bankaccount"), sprintf("javascript: selectBank(0, '%s');", addslashes(gettext("geen"))));


		$table->addTag("br");
	$table->endTableData();
$table->endTableRow();
$table->endTable();

$venster = new Layout_venster(array(
	"title" => gettext("Bank accounts"),
	"subtitle" => gettext("pick relevant bankaccount")
));
$venster->addVensterData();
	$venster->addCode( $table->generate_output() );
$venster->endVensterData();

$output->addCode($venster->generate_output());
$output->endTag("form");
$output->layout_page_end();
$output->exit_buffer();
?>
