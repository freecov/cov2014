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

if ($cardid>0)
	$relationsData = $address_data->getRelationDataByRelationID($cardid);

/* start output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id" => "editrelationscard",
		"method" => "post",
		"action" => "index.php"
	));
        $output->addHiddenField("relationscard[type]", $_REQUEST["type"]);
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "save_relationscard");

	if ($_REQUEST["type"] == "editsupplier")
	{
        	$output->addHiddenField("relationscard[id]", $cardid);
		$subtitle = gettext("edit supplier");
	}

	if ($_REQUEST["type"] == "addsupplier")
	{
		$subtitle = gettext("edit supplier");
		$relationsData["customer_id"] = $cardid;
		$relationsData["supplier_id"] = 0;
	}

	if ($_REQUEST["type"] == "editcustomer")
	{
        	$output->addHiddenField("relationscard[id]", $cardid);
		$subtitle = gettext("edit customer");
	}

	if ($_REQUEST["type"] == "addcustomer")
	{
		$subtitle = gettext("add customer");
		$relationsData["supplier_id"] = $cardid;
		$relationsData["customer_id"] = 0;
	}

	$venster = new Layout_venster(array(
		"title"    => gettext("relationscard"),
		"subtitle" => $subtitle
	));

        $history = new Layout_history();
        $link = $history->generate_history_call();
        $venster->addCode($link);

	$venster->addVensterData();
		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		$table->addTableRow();
			$table->insertTableData(gettext("info"), array("colspan"=>4), "header");
		$table->endTableRow();
 		$table->addTableRow();
                	$table->insertTableData(gettext("Customer"), "", "header");
                	$table->addTableData("", "data");
			$relname = $address_data->getAddressNameById($relationsData["customer_id"]);
                	$table->addHiddenField("relationscard[customer_id]", $relationsData["customer_id"]);
				if ($_REQUEST["type"] == "addcustomer")
                	        	$table->insertTag("span", $relname, array( "id" => "humanrelationscardcustomer_id" ));
				else
                	        	$table->insertTag("span", $relname);
                	        $table->addSpace(1);
				if ($_REQUEST["type"] == "addcustomer")
 					$table->insertAction("edit", gettext("wijzigen"), 
						"javascript: popup('?mod=address&action=searchRel&sub=klanten', 'searchrel', 
                                                                   0, 0, 1,'relationscardcustomer_id');");
                	$table->endTableData();

                	$table->insertTableData(gettext("Supplier"), "", "header");
                	$table->addTableData("", "data");
			$relname = $address_data->getAddressNameById($relationsData["supplier_id"]);
                	$table->addHiddenField("relationscard[supplier_id]", $relationsData["supplier_id"]);
			if ($_REQUEST["type"] == "addsupplier")
           	        	$table->insertTag("span", $relname, array( "id" => "humanrelationscardsupplier_id" ));
			else
           	        	$table->insertTag("span", $relname);
                        $table->addSpace(1);
			if ($_REQUEST["type"] == "addsupplier")
               	         	$table->insertAction("edit", gettext("wijzigen"), 
					"javascript: popup('?mod=address&action=searchRel&sub=leveranciers', 'searchrel', 
                                                           0, 0, 1,'relationscardsupplier_id');");
                	$table->endTableData();
        	$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData(gettext("Payment condition"), "", "header");
			$table->addTabledata(array("colspan"=>3), "data");
				$table->addTextArea("relationscard[pay_remark]", $relationsData["pay_remark"], 
						array("style" => "width: 400px; height: 100px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Bank"), "", "header");
			$table->addTableData("", "data");
				$bankname = $address_data->getFinanceBankNameByBankID($relationsData["bank_pref"]);
				$table->addHiddenField("relationscard[bank_pref]", $relationsData["bank_pref"]);
				$table->insertTag("span", $bankname, array( "id" => "searchbank" ));
                                $table->addSpace(1);
                	        $table->insertAction("edit", gettext("wijzigen"), 
					"javascript: popup_picker()");
			$table->endTableData();
		$table->endTableRow();
 		$table->addTableRow();
                	$table->insertTableData(gettext("Transporter"), "", "header");
                	$table->addTableData("", "data");
				$transportername = $address_data->getAddressNameById($relationsData["transporter"]);
                		$table->addHiddenField("relationscard[transporter]", $relationsData["transporter"]);
                	        $table->insertTag("span", $transportername, array( "id" => "humanrelationscardtransporter" ));
                	        $table->addSpace(1); 
 				$table->insertAction("edit", gettext("wijzigen"), 
					"javascript: popup('?mod=address&action=searchRel&sub=transporteurs', 'searchrel', 
							   0, 0, 1, 'relationscardtransporter');");
                	$table->endTableData();
        	$table->endTableRow();

		// add extra's
                        $table->addTableRow();
                                $table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
                        $table->endTableRow();
                        $metadata   = new Metafields_data();
                        $metaoutput = new Metafields_output();
                        $metafields = $metadata->meta_list_fields("address_relations", $relationsData["id"]);
                        foreach ($metafields as $v) {
                                $table->addTableRow();
                                        $table->insertTableData($v["fieldname"], "", "header");
                                        $table->addTableData(array("colspan" => 3), "data");
                                                $table->addCode($metaoutput->meta_format_field($v));
                                                $table->insertAction("delete", gettext("verwijder"), "javascript: remove_meta(".$v["id"].");");
                                        $table->endTableData();
                                $table->endTableRow();
                        }
                        $table->addTableRow();
                                $table->insertTableData("", "", "header");
                                $table->addTableData(array("colspan" => 3), "data");
                                        $table->insertAction("new", gettext("toevoegen"), "javascript: add_meta('address_relations', ".$relationsData["id"].");");
                                $table->endTableData();
                        $table->endTableRow();
		// end extra's

		$table->addTableRow();
		$table->insertTableData("&nbsp;", "", "header");
			$table->addTableData(array("colspan"=>3));
			$table->insertAction("save", gettext("save"), "javascript: relationscard_save();");
 		$table->endTableRow();
		/* end table, attach to output buffer */
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	$venster->endVensterData();
        $venster->insertAction("back", gettext("terug"), "javascript: history_goback();");
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."relationscard_actions.js");
	$output->insertTag("script",$javascript);
/* ENd output buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
