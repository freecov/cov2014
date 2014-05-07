<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
if (!class_exists("Address_output")) {
	die("no class definition found");
}
if (!$cardid) {
	die("cannot edit without id. How did you get here?");
}
/* init address data object */
$address_data = new Address_data();
$financialData = $address_data->getFinanceByRelationID($cardid);

/* start output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id" => "editfinancialcard",
		"method" => "post",
		"action" => "index.php"
	));
        $output->addHiddenField("financialcard[id]", $cardid);
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "save_financialcard");
	$venster = new Layout_venster(array(
		"title"    => gettext("financialcard"),
		"subtitle" => gettext("edit")
	));
	$venster->addVensterData();
		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		$table->addTableRow();
			$table->insertTableData(gettext("info"), array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Tax number"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("financialcard[tax_nr]", $financialData["tax_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("Accijnsnumber"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("financialcard[acc_nr]", $financialData["acc_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Ecotax"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[ecotax]", $financialData["ecotax"]);
			$table->endTableData();
			$table->insertTableData(gettext("Kingid"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("financialcard[kingid]", $financialData["kingid"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Payment condition"), "", "header");
			$table->addTabledata(array("colspan"=>3), "data");
				$table->addTextArea("financialcard[pay_remark]", $financialData["pay_remark"], array("style" => "width: 400px; height: 100px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData("", "", "header");
			$table->addTableData(array("colspan"=>3));
			$table->insertAction("save", gettext("save"), "javascript: financialcard_save();");
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
