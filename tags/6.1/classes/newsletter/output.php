<?php
/**
 * Covide Groupware-CRM Newsletter module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Newsletter_output {

	/* constants */
	const include_dir = "classes/newsletter/inc/";
	const include_dir_main = "classes/html/inc/";

	const class_name = "newsletter";

	private $output;
	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		$this->output="";
	}
	/* }}} */

	public function selectTargetGroup() {
		$output = new Layout_output();
		$output->layout_page("newsletter");

		$settings = array(
			"title"    => gettext("Nieuwsbrief"),
			"subtitle" => gettext("kies doelgroep")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "get"
		));
		$output->addHiddenField("mod", "newsletter");
		$output->addHiddenField("action", "selectClassification");
		$output->addHiddenField("address_type", "");
		$output->addHiddenField("target_type", "");

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("complete Covide adresboek").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("volgende stap"), "javascript: step_next('complete')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("klanten").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("volgende stap"), "javascript: step_next('customers')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("leveranciers").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("volgende stap"), "javascript: step_next('suppliers')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("medewerkers").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("volgende stap"), "javascript: step_next('employees')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();


		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();
			$venster->addCode( $tbl->generate_output() );
		$venster->endVensterData();

		$placeholder = new Layout_table();
		$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );


		$output->start_javascript();
		$output->addCode("
			function step_next(str) {
				if (str == 'employees') {
					document.getElementById('action').value = 'selectFormat';
				}
				document.getElementById('target_type').value = str;
				document.getElementById('velden').submit();
			}
		");
		$output->end_javascript();

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function selectClassification() {
		$output = new Layout_output();
		$output->layout_page("newsletter");

		$settings = array(
			"title"    => gettext("Nieuwsbrief"),
			"subtitle" => gettext("kies de classificatie(s)")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "newsletter");
		$output->addHiddenField("action", "selectFormat");
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);

		$classification = new Classification_output();

		$output_alt = new Layout_output();
		$output_alt->insertAction("back", gettext("terug"), "?mod=newsletter");
		$output_alt->insertAction("forward", gettext("verder"), "javascript: step_next();");

		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();
			$venster->addCode( $classification->select_classification("", $output_alt->generate_output(), 1 ) );
		$venster->endVensterData();

		$placeholder = new Layout_table();
		$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

		$output->start_javascript();
		$output->addCode("
			function step_next() {
				document.getElementById('velden').submit();
			}
		");
		$output->end_javascript();

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function selectFormat() {
		$output = new Layout_output();
		$output->layout_page("newsletter");

		$settings = array(
			"title"    => gettext("Nieuwsbrief"),
			"subtitle" => gettext("kies de classificatie(s)")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$addresstype = $_REQUEST["addresstype"];
		if ($_REQUEST["target_type"] == "employees") {
			$addresstype = "users";
		}

		$output->addHiddenField("mod", "newsletter");
		$output->addHiddenField("action", "getAddresses");
		$output->addHiddenField("format", "");
		$output->addHiddenField("addresstype", $addresstype);
		$output->addHiddenField("selectiontype", $_REQUEST["selectiontype"]);
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);
		$output->addHiddenField("classifications[positive]", $_REQUEST["classifications"]["positive"]);
		$output->addHiddenField("classifications[negative]", $_REQUEST["classifications"]["negative"]);

		$classification = new Classification_data();
		$emailData      = new Email_data();

		$address_data = new Address_data();
		$list = $address_data->getRelationsList( array("addresstype"=>$addresstype, "nolimit"=>1 ));

		$emails = array();
		$errors = array();
		if (!is_array($list["address"])) {
			$list["address"] = array();
		}

		/* define cols for bcards */
		$cols = array("business_email", "email", "personal_email");

		foreach ($list["address"] as $k=>$record) {
			$email_address = "";
			if ($_REQUEST["addresstype"] == "bcards") {
				foreach ($cols as $cc) {
					if ($emailData->validateEmail($record[$cc]) && !$tmp) {
						$email_address = $record[$cc];
					}
				}
			} else {
				if ($emailData->validateEmail($record["email"])) {
					$email_address = $record["email"];
				}
			}
			if ($email_address) {
				$emails[$email_address] = trim( $record["fullname"]." - ".$record["companyname_html"] );
			} else {
				$errors[$record["email"]] = $record["fullname"];
			}
		}
		$emails = array_unique($emails);
		natcasesort($emails);

		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("aantal ontvangers"), "", "header");
				$tbl->insertTableData( count($emails)." ".gettext("ontvanger(s)"), "", "data");
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("ontvangers"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTag("div", array(
						"style" => "width: 100%; height: 300px; overflow: auto;"
					));
					foreach ($errors as $k=>$v) {
						$tbl->addTag("font", array(
							"color" => "red"
						));
						$tbl->addCode( sprintf("\"%s\" ", $v) );
						$tbl->addTag("b");
							$tbl->addCode( sprintf("&lt;%s&gt;", $k) );
						$tbl->endTag("b");
						$tbl->endTag("font");
						$tbl->addTag("br");
					}

					foreach ($emails as $k=>$v) {
						$tbl->addCode( sprintf("\"%s\" ", $v) );
						$tbl->addTag("b");
							$tbl->addCode( sprintf("&lt;%s&gt;", $k) );
						$tbl->endTag("b");
						$tbl->addTag("br");
					}
					$tbl->endTag("div");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("kies de opmaak"), "", "header");
				$tbl->addTableData();
					$tbl->insertAction("forward", gettext("text"), "javascript: step_next(1)");
					$tbl->addSpace();
					$tbl->addCode( gettext("ga door naar de volgende stap") );
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData( "", array("colspan"=>2), "header");
			$tbl->endTableRow();
			$tbl->endTable();

			$venster->addCode( $tbl->generate_output() );
		$venster->endVensterData();

		$placeholder = new Layout_table();
		$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

		$output->start_javascript();
		$output->addCode("
			function step_next(format) {
				document.getElementById('format').value = format;
				document.getElementById('velden').submit();
			}
		");
		$output->end_javascript();

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function getAddresses() {
		$output = new Layout_output();
		$output->layout_page();

		$address_data = new Address_data();
		$list = $address_data->getRelationsList( array("addresstype"=>$_REQUEST["addresstype"], "nolimit"=>1 ));

		/* create an empty concept for this user */
		$emailData = new Email_data();
		$id = $emailData->save_concept();

		$data = array(
			"classifications_positive" => str_replace("|",",",$_REQUEST["classifications"]["positive"]),
			"classifications_negative" => str_replace("|",",",$_REQUEST["classifications"]["negative"]),
			"classifications_target"   => $_REQUEST["addresstype"],
			"classifications_type"     => $_REQUEST["selectiontype"]
		);
		$emailData->updateMailOptions($id, $data);

		/* generate an extra hash to be used in the newsletter module */
		$hash = $emailData->generate_message_id( sprintf("%s@localhost", $id) );

		/* define cols for bcards */
		$cols = array("business_email", "email", "personal_email");

		foreach ($list["address"] as $k=>$record) {
			$email_address = "";
			if ($_REQUEST["addresstype"] == "bcards") {
				foreach ($cols as $cc) {
					if ($emailData->validateEmail($record[$cc]) && !$tmp) {
						$email_address = $record[$cc];
					}
				}
			} else {
				if ($emailData->validateEmail($record["email"])) {
					$email_address = $record["email"];
				}
			}
			$data = array(
				"mail_id"  => $id,
				"email"    => $email_address,
				"mailcode" => $hash
			);
			$emailData->save_tracker_item($data);
		}


		$output->start_javascript();
		$output->addCode("
			location.href='index.php?mod=email&action=compose&id=$id';
		");
		$output->end_javascript();

		$output->layout_page_end();
		$output->exit_buffer();
	}
}
?>
