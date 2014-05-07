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
		set_time_limit(60*5);
	}
	/* }}} */

	public function selectTargetGroup() {
		$output = new Layout_output();
		$output->layout_page("newsletter", 1);

		$settings = array(
			"title"    => gettext("Newsletter"),
			"subtitle" => gettext("choose target")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "newsletter");
		$output->addHiddenField("action", "selectClassification");
		$output->addHiddenField("address_type", "");
		$output->addHiddenField("target_type", "");
		$output->addHiddenField("campaign", $_REQUEST["campaign"]);
		$output->addHiddenField("campaign_name", $_REQUEST["camp"]["name"]);
		$output->addHiddenField("campaign_desc", $_REQUEST["camp"]["description"]);

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("complete Covide addressbook").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("next"), "javascript: step_next('complete')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("customers").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("next"), "javascript: step_next('customers')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("suppliers").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("next"), "javascript: step_next('suppliers')");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData( gettext("employees").":" , "", "data");
			$tbl->addTableData("", "data");
				$tbl->insertAction("forward", gettext("next"), "javascript: step_next('employees')");
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
		$output->layout_page("newsletter", 1);

		$settings = array(
			"title"    => gettext("Newsletter"),
			"subtitle" => gettext("pick classification(s)")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "newsletter");
		$output->addHiddenField("action", "selectFormat");
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);
		$output->addHiddenField("campaign", $_REQUEST["campaign"]);
		$output->addHiddenField("campaign_name", $_REQUEST["campaign_name"]);
		$output->addHiddenField("campaign_desc", $_REQUEST["campaign_desc"]);

		$classification = new Classification_output();

		$output_alt = new Layout_output();
		$output_alt->insertAction("back", gettext("back"), "?mod=newsletter");
		$output_alt->insertAction("forward", gettext("next"), "javascript: step_next();");

		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();
			$venster->addCode( $classification->select_classification("", $output_alt->generate_output(), 1, 1) );
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
		$output->layout_page("newsletter", 1);

		$settings = array(
			"title"    => gettext("Newsletter"),
			"subtitle" => gettext("pick classification(s)")
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
		$output->addHiddenField("emailtype", $_REQUEST["emailtype"]);
		$output->addHiddenField("selectiontype", $_REQUEST["selectiontype"]);
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);

		$output->addHiddenField("campaign", $_REQUEST["campaign"]);
		$output->addHiddenField("campaign_name", $_REQUEST["campaign_name"]);
		$output->addHiddenField("campaign_desc", $_REQUEST["campaign_desc"]);


		$output->addHiddenField("classifications[positive]", $_REQUEST["classifications"]["positive"]);
		$output->addHiddenField("classifications[negative]", $_REQUEST["classifications"]["negative"]);

		$classification = new Classification_data();
		$emailData      = new Email_data();

		$sub = "";
		if ($_REQUEST["target_type"] == "customers")
			$sub = "klanten";
		if ($_REQUEST["target_type"] == "suppliers")
			$sub = "leveranciers";
		$classifications = $_REQUEST["classifications"];
		$classifications["selectiontype"] = $_REQUEST["selectiontype"];
		$address_data = new Address_data();
		$list = $address_data->getNewsletterData( array("addresstype"=>$addresstype, "nolimit"=>1, "sub"=>$sub, "newsletterselection" => 1, "classifications" => $classifications, "count_only" => 1));
		/*
		$emails = array();
		$errors = array();
		if (!is_array($list["address"])) {
			$list["address"] = array();
		}

		// define cols for bcards also check whether we want private or business email
		if($_REQUEST["emailtype"] == "private") {
			$cols = array("personal_email");
		} else {
			$cols = array("email");
		}

		foreach ($list["address"] as $k=>$record) {
			$email_address = "";
			if ($_REQUEST["addresstype"] == "bcards") {
				foreach ($cols as $cc) {
					if ($emailData->validateEmail($record[$cc])) {
						$email_address = $record[$cc];
					}
					elseif($emailData->validateEmail($record["email"])) {
						$email_address = $record["email"];
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
		 */
		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("total recipients"), "", "header");
				$tbl->insertTableData( $list["total_count"]." ".gettext("ontvanger(s)"), "", "data");
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("from"), "", "header");
				$tbl->addTableData();
				/* get Email Aliases */
				$aliases = $emailData->getEmailAliases();
				$tbl->addSelectField("mail[from]", $aliases, "" );
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData( gettext("choose layout"), "", "header");
				$tbl->addTableData();
					$tbl->insertAction("forward", gettext("text"), "javascript: step_next(1)");
					$tbl->addSpace();
					$tbl->addCode( gettext("proceed to the next step") );
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
		$output->layout_page("newsletter", 1);

                $sub = "";
                if ($_REQUEST["target_type"] == "customers")
                        $sub = "klanten";
                if ($_REQUEST["target_type"] == "suppliers")
                        $sub = "leveranciers";

		$classifications = $_REQUEST["classifications"];
		$classifications["selectiontype"] = $_REQUEST["selectiontype"];
		$address_data = new Address_data();
		$list = $address_data->getNewsletterData( array("addresstype"=>$_REQUEST["addresstype"], "emailtype"=>$_REQUEST["emailtype"], "nolimit"=>1, "sub"=>$sub, "newsletterselection" => 1, "classifications" => $classifications) );

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
		$cols = array("personal_email", "email");

		$ids = array();
		foreach ($list as $k=>$record) {
			$email_address = "";
			if ($_REQUEST["addresstype"] == "bcards" || $_REQUEST["addresstype"] == "users" || $_REQUEST["addresstype"] == "employees") {
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
				$data = array(
					"mail_id"  => $id,
					"email"    => $email_address,
					"mailcode" => $hash,
					"address_type" => $_REQUEST["addresstype"],
					"address_id"   => $record["id"],
				);
				$emailData->save_tracker_item($data);
			}
			if ($email_address)
				$ids[$email_address] = $record["id"];
			else
				$ids[$record["id"]] = $record["id"];
		}

		if ($_REQUEST["campaign"]) {
			$campaign_data = array(
				"name"           => $_REQUEST["campaign_name"],
				"description"    => $_REQUEST["campaign_desc"],
				"type"           => "newsletter",
				"ids"            => $ids,
				"mail_tracker"   => $id,
				"classification" => array(
					"positive" => $_REQUEST["classifications"]["positive"],
					"negative" => $_REQUEST["classifications"]["negative"],
					"target"   => $_REQUEST["target_type"],
					"operator" => $_REQUEST["selectiontype"],
					"type"     => $_REQUEST["addresstype"]
				)
			);
			$campaign = new Campaign_data();
			$campaign->addCampaign($campaign_data);
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
