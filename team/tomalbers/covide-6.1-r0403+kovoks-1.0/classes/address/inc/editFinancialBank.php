<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* init address data object */
$address_data = new Address_data();

if ($_REQUEST["bankid"] > 0 )
{
	$financialData = $address_data->getFinanceBankDetailsByBankID($_REQUEST["bankid"]);

	if ($_REQUEST["cardid"] != $financialData["address_id"]) 
		die("wicked, that bank account does not belong to this relation");
}

/* start output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id" => "editfinancialbank",
		"method" => "post",
		"action" => "index.php"
	));
        $output->addHiddenField("financialcard[id]", $_REQUEST["bankid"]);
        $output->addHiddenField("financialcard[address_id]", $_REQUEST["cardid"]);
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "save_financialbank");
	$venster = new Layout_venster(array(
		"title"    => gettext("Bank account"),
		"subtitle" => gettext("edit")
	));
	$venster->addVensterData();
		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		$table->addTableRow();
			$table->insertTableData(gettext("info"), array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Description"), "", "header");
			$table->addTabledata(array("colspan"=>3), "data");
				$table->addTextField("financialcard[desc]", $financialData["desc"], array("style"=> "width: 309px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("financialcard[name]", $financialData["name"]);
			$table->endTableData();
			$table->insertTableData(gettext("Address"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[address]", $financialData["address"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("ZIP"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[zip]", $financialData["zip"]);
			$table->endTableData();
			$table->insertTableData(gettext("City"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[place]", $financialData["place"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Province"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[province]", $financialData["province"]);
			$table->endTableData();
			$table->insertTableData(gettext("Country"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[country]", $financialData["country"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("IBAN"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[iban]", $financialData["iban"]);
			$table->endTableData();
			$table->insertTableData(gettext("BIC"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[bic]", $financialData["bic"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData("", "", "header");
			$table->addTableData(array("colspan"=>3));
			$table->insertAction("save", gettext("save"), "javascript: financialbank_save();");
 		$table->endTableRow();
		/* end table, attach to output buffer */
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."financialcard_actions.js");
/* end output buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
