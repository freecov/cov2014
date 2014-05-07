<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$str = $_REQUEST["str"];
	$mails = array();
	$related = $_REQUEST["rel"];

	/* array( email, name, company ) */
	$columns = array("personal_email", "business_email", "email");

	if ($related && $related != "|") {

		$rel = explode("|", $related);
		$original_id = $rel[0];
		$relation_id = $rel[1];

		/* get the original email */
		$mails = array();
		$data = $this->getEmailById($original_id);

		/* get the original email addresses */
		$d[gettext("sender")]  = explode(",", preg_replace("/(\t)|(\r)|(\n)/s", "", $this->cleanAddress($data[0]["sender_emailaddress"])));
		$d[gettext("cc")]        = explode(",", preg_replace("/(\t)|(\r)|(\n)/s", "", $this->cleanAddress($data[0]["cc"])));
		$d[gettext("bcc")]       = explode(",", preg_replace("/(\t)|(\r)|(\n)/s", "", $this->cleanAddress($data[0]["bcc"])));
		$d[gettext("recipient")] = explode(",", preg_replace("/(\t)|(\r)|(\n)/s", "", $this->cleanAddress($data[0]["to"])));

		foreach ($d as $k=>$a) {
			foreach ($a as $v) {
				if ($this->validateEmail($v)) {
					$mails[$v]= array(
						"email"   => $v,
						"name"    => "<i>".$k."</i>",
						"company" => ""
					);
				}
			}
		}

		if ($relation_id) {
			/* get relation email address */
			$address_data = new Address_data();
			$d = $address_data->getAddressById($relation_id);
			if ($this->validateEmail($d["email"])) {
				$mails[$d["email"]] = array(
					"email"  => $d["email"],
					"name"   => $d["tav"],
					"company" => $d["companyname"],
					"address_id" => $d["id"]
				);
			}

			/* get bcards email addresses */
			$bcards = $address_data->getBcardsByRelationID($relation_id);
			foreach ($bcards as $k=>$row) {
				foreach ($columns as $col) {
					if ($this->validateEmail($row[$col])) {
						$mails[$row[$col]]= array(
							$row[$col],
							substr($row["fullname"], 0, 50),
							substr($d["companyname"], 0, 30),
							$row["address_id"]
						);
					}
				}
			}
		}

	} else {

		//TODO: 'others/overig' does bug in mysql
		//$types = array("bcards","relations","private","users","overig");
		$types = array("bcards","relations","private","users");

		//in addressbook
		$address_data = new Address_data();
		foreach ($types as $t) {
			$address = $address_data->getRelationsList( array("addresstype"=>$t, "search"=>$str, "top"=>0) );
			if (is_array($address["address"])) {
				foreach ($address["address"] as $r=>$row) {
					foreach ($columns as $col) {
						if ($this->validateEmail($row[$col])) {
							if (in_array($t, array("bcards", "relations"))) {
								if ($row["address_id"]) {
									$address_id = $row["address_id"];
								} else {
									$address_id = $row["id"];
								}
							} else {
								$address_id = 0;
							}
							$mails[$row[$col]]= array(
								$row[$col],
								substr($row["fullname"], 0, 50),
								substr($row["companyname"], 0, 30),
								$address_id
							);
						}
					}
				}
			}
		}
	}
	natsort($mails);

	if (!$_REQUEST["expand"]) {
		$mails = array_slice($mails, 0, 20);
	}

	header("Content-type: text/plain; charset=ISO-8859-1");
	echo $_REQUEST["field"]."#";
	echo gettext("email")."|".gettext("name")."|".gettext("company name")."#";

	$conversion = new Layout_conversion();

	if (is_array($mails)) {
		foreach ($mails as $m) {
			echo $conversion->utf8_convert( str_replace("#", "", implode("|",$m))."#");
		}
	}
?>
