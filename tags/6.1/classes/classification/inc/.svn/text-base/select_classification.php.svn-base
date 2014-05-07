<?php

	if (!class_exists("Classification_output")) {
		exit("no class definition found");
	}

	/* get classifications from db */
	$classification_data = new Classification_data();
	$classifications = $classification_data->getClassifications();

	$classi = array();
	foreach ($classifications as $c) {
		if ($c["is_active"]) {
			$classi[$c["id"]] = $c["description"];
		}
	}

	$output = new Layout_output();

	$output->addHiddenField("classifications[positive]", $_REQUEST["classifications"]["positive"]);
	$output->addHiddenField("classifications[negative]", $_REQUEST["classifications"]["negative"]);

	if ($_REQUEST["addresstype"]) {
		$addresstype = $_REQUEST["addresstype"];
	} else {
		$addresstype = "relations";
	}
	if ($_REQUEST["selectiontype"]) {
		$selectiontype = $_REQUEST["selectiontype"];
	} else {
		$selectiontype = "and";
	}

	$tbl = new Layout_table();
	if ($prefix) {
		$tbl->addTableRow();
			$tbl->addTableData( array("colspan"=>3), "data");
				$tbl->addCode($prefix);
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->insertTableData( gettext("soort adres"), "", "header" );
		$tbl->addTableData( array("colspan"=>2), "data");
			$tbl->addRadioField("addresstype", gettext("relaties"), "relations", $addresstype);
			$tbl->addRadioField("addresstype", gettext("businesscards"), "bcards", $addresstype);
			if ($allow_mixed)
				#$tbl->addRadioField("addresstype", gettext("gemengd"), "both", $addresstype);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->insertTableData( gettext("soort selectie"), "", "header" );
		$tbl->addTableData( array("colspan"=>2), "data");
			$tbl->addRadioField("selectiontype", gettext("unieke classificaties (en)"), "and", $selectiontype);
			$tbl->addRadioField("selectiontype", gettext("opgetelde classificaties (of)"), "or", $selectiontype);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->insertTableData( gettext("classificatie"), "", "header" );
		$tbl->addTableData(array("style"=>"vertical-align: top"), "data");
			$tbl->insertTag("b", gettext("positief"));
			$tbl->addTag("br");
			$tbl->addCode( $this->classification_selection("classificationspositive", $_REQUEST["classifications"]["positive"]) );
		$tbl->endTableData();
		$tbl->addTableData(array("style"=>"vertical-align: top"), "data");
			$tbl->insertTag("b", gettext("negatief"));
			$tbl->addTag("br");
			$tbl->addCode( $this->classification_selection("classificationsnegative", $_REQUEST["classifications"]["negative"], "disabled") );
		$tbl->endTableData();
	$tbl->endTableRow();
	if ($suffix) {
		$tbl->addTableRow();
			$tbl->addTableData( array("colspan"=>3, "align"=>"right"), "header");
				$tbl->addCode($suffix);
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->endTable();

	$output->addCode( $tbl->generate_output() );
?>
