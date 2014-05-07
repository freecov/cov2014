<?php
	/**
	 * Covide Groupware-CRM Classification
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
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

	if ($newsletter == 1 && !$_REQUEST["classifications"]["negative"]) {
		$do_not_contact = $classification_data->getSpecialClassification("do not contact");
		$no_newsletter = $classification_data->getSpecialClassification("no newsletter");
		$_REQUEST["classifications"]["negative"] = "|".$do_not_contact[0]["id"]."|".$no_newsletter[0]["id"]."|";
	}

	$output = new Layout_output();

	$output->addHiddenField("classifications[positive]", $_REQUEST["classifications"]["positive"]);
	$output->addHiddenField("classifications[negative]", $_REQUEST["classifications"]["negative"]);

	if ($_REQUEST["addresstype"]) {
		$addresstype = $_REQUEST["addresstype"];
	} else {
		$addresstype = "relations";
	}
	$addresstype = "bcards";
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
		$tbl->insertTableData( gettext("address type"), "", "header" );
		$tbl->addTableData( array("colspan"=>2), "data");
			$tbl->addRadioField("addresstype", gettext("relations"), "relations", $addresstype);
			$tbl->addRadioField("addresstype", gettext("business cards"), "bcards", $addresstype);
			if ($allow_mixed)
				$tbl->addRadioField("addresstype", gettext("mixed"), "both", $addresstype);
		$tbl->endTableData();
	$tbl->endTableRow();

	if($newsletter == 1) {
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("email type"), "", "header" );
			$tbl->addTableData( array("colspan"=>2), "data");
				$tbl->addRadioField("emailtype", gettext("business"), "business", 0);
				$tbl->addRadioField("emailtype", gettext("private"), "private",'');
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->insertTableData( gettext("selection type"), "", "header" );
		$tbl->addTableData( array("colspan"=>2), "data");
			$tbl->addRadioField("selectiontype", gettext("unique classifications (AND)"), "and", $selectiontype);
			$tbl->addRadioField("selectiontype", gettext("added classifications (OR)"), "or", $selectiontype);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->insertTableData( gettext("classification"), "", "header" );
		$tbl->addTableData(array("style"=>"vertical-align: top"), "data");
			$tbl->insertTag("b", gettext("positive"));
			$tbl->addTag("br");
			$tbl->addCode( $this->classification_selection("classificationspositive", $_REQUEST["classifications"]["positive"]) );
		$tbl->endTableData();
		$tbl->addTableData(array("style"=>"vertical-align: top"), "data");
			$tbl->insertTag("b", gettext("negative"));
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
