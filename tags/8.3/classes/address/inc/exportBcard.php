<?php
	/**
	 * Covide Groupware-CRM Addressbook module.
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

	if (!class_exists("Address_output")) {
		die("no class definition found");
	}
	
	$conversion = new Layout_conversion();
	$commencements = $address_data->getCommencements();
	$titles = $address_data->getTitles();
	$letterheads = $address_data->getLetterheads();
	if ($row["givenname"] || $row["surname"] || 1 == 1) {
		/* include this record in the export */
		if (trim($row["business_address"])) {
			$row["address"] = $row["business_address"];
			$row["address2"] = "";
			$row["zipcode"] = $row["business_zipcode"];
			$row["city"] = $row["business_city"];
			$row["country"] = $row["business_country"];
		} elseif (trim($row["personal_address"])) {
			/* put personal address there */
			$row["address"] = $row["personal_address"];
			$row["address2"] = $row["personal_address2"];
			$row["zipcode"] = $row["personal_zipcode"];
			$row["city"] = $row["personal_city"];
			$row["country"] = $row["personal_country"];
		} else {
			/* no address info attached, get addressinfo from company record */
			$q = sprintf("SELECT address, address2, zipcode, city, country, phone_nr, mobile_nr, fax_nr, email FROM address WHERE id = %d", $row["address_id"]);
			$r = sql_query($q);
			$addressinfo = sql_fetch_assoc($r);
			$row["address"]   = $addressinfo["address"];
			$row["address2"]  = $addressinfo["address2"];
			$row["zipcode"]   = $addressinfo["zipcode"];
			$row["city"]      = $addressinfo["city"];
			$row["country"]   = $addressinfo["country"];
			$row["phone_nr"]  = $addressinfo["phone_nr"];
			$row["mobile_nr"] = $addressinfo["mobile_nr"];
			$row["fax_nr"]    = $addressinfo["fax_nr"];
			$row["email"]     = $addressinfo["email"];
			unset($addressinfo);
		}
		if ($row["business_phone_nr"]) {
			$row["phone_nr"] = $row["business_phone_nr"];
		} elseif ($row["personal_phone_nr"]) {
			$row["phone_nr"] = $row["personal_phone_nr"];
		}
		if ($row["business_fax_nr"]) {
			$row["fax_nr"] = $row["business_fax_nr"];
		} elseif($row["personal_fax_nr"]) {
			$row["fax_nr"] = $row["personal_fax_nr"];
		}
		if ($row["business_mobile_nr"]) {
			$row["mobile_nr"] = $row["business_mobile_nr"];
		} elseif($row["personal_mobile_nr"]) {
			$row["mobile_nr"] = $row["personal_mobile_nr"];
		}
		if ($row["business_email"]) {
			$row["email"] = $row["business_email"];
		} elseif($row["personal_email"]) {
			$row["email"] = $row["personal_email"];
		}
		$row["letterinfo"] = $address_data->generate_letterinfo(array(
			"contact_initials"     => $row["initials"],
			"contact_letterhead"   => $row["letterhead"],
			"contact_commencement" => $row["commencement"],
			"contact_givenname"    => $row["givenname"],
			"contact_infix"        => $row["infix"],
			"contact_surname"      => $row["surname"],
			"title"                => $row["title"]
		));
		$row["tav"] = $row["letterinfo"]["tav"];
		$row["contact_person"] = $row["letterinfo"]["contact_person"];

		$csv = array();
		$csv[]= $titles[$row["title"]]["title"];
		$csv[]= $commencements[$row["commencement"]]["title"];
		$csv[]= $letterheads[$row["letterhead"]]["title"];
		$csv[]= $row["tav"];
		$csv[]= $row["contact_person"];
		$csv[]= $address_data->getAddressNameById($row["address_id"]);
		$csv[]= $row["initials"];
		$csv[]= $row["givenname"];
		$csv[]= $row["infix"];
		$csv[]= $row["surname"];
		$csv[]= $row["phone_nr"];
		$csv[]= $row["fax_nr"];
		$csv[]= $row["mobile_nr"];
		$csv[]= $row["email"];
		$csv[]= $row["website"];

		if ($addressinfo["pobox"]) {
			$csv[]= gettext("PO box")." ".$addressinfo["pobox"];
			$csv[]= "";
		} else {
			$csv[]= $row["address"];
			$csv[]= $row["address2"];
		}
		$csv[]= ($addressinfo["pobox_zipcode"]) ? $addressinfo["pobox_zipcode"]:$row["zipcode"];
		$csv[]= ($addressinfo["pobox_city"])    ? $addressinfo["pobox_city"]:$row["city"];

		$csv[]= $row["address"];
		$csv[]= $row["address2"];
		$csv[]= $row["zipcode"];
		$csv[]= $row["city"];

		$csv[]= $row["country"];
		$csv[]= preg_replace("/(\r)|(\t)|(\n)/s", " ", $row["memo"]);
		$csv[]= $row["businessunit"];
		$csv[]= $row["department"];
		$csv[]= $row["locationcode"];
		$csv[]= $row["alternative_name"];
		$csv[]= $row["jobtitle"];
		$csv[]= $row["opt_assistant_name"];
		$csv[]= $row["opt_assistant_phone_nr"];
		$csv[]= $row["opt_profession"];
		$csv[]= $row["opt_manager_name"];
		$csv[]= date("d-m-Y", sprintf("%d", $row["timestamp_birthday"]));
		$data.= $conversion->generateCSVRecord($csv);
	}
	unset($conversion);
?>
