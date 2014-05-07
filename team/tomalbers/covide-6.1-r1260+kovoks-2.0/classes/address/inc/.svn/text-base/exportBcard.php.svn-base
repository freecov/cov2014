<?php
	if (!class_exists("Address_output")) {
		die("no class definition found");
	}
	/* only address managers are allowed to export globally */
	$userdata = new User_data();
	$userperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
	if (!$userperms["xs_addressmanage"]) {
		die("no access");
	}

	if ($row["givenname"] || $row["surname"]) {
		/* include this record in the export */
		$addressinfo = $address_data->getAddressById($row["address_id"]);
		if (!(trim($row["business_address"]) || trim($row["personal_address"]))) {
			/* no address info attached, get addressinfo from company record */
			$row["address"]   = $addressinfo["address"];
			$row["address2"]  = $addressinfo["address2"];
			$row["zipcode"]   = $addressinfo["zipcode"];
			$row["city"]      = $addressinfo["city"];
			$row["country"]   = $addressinfo["country"];
			$row["phone_nr"]  = $addressinfo["phone_nr"];
			$row["mobile_nr"] = $addressinfo["mobile_nr"];
			$row["fax_nr"]    = $addressinfo["fax_nr"];
			$row["email"]     = $addressinfo["email"];
		} else {
			if (trim($row["business_address"])) {
				$row["address"] = $row["business_address"];
				$row["address2"] = "";
				$row["zipcode"] = $row["business_zipcode"];
				$row["city"] = $row["business_city"];
				$row["country"] = $row["business_country"];
			} else {
				/* put personal address there */
				$row["address"] = $row["personal_address"];
				$row["address2"] = $row["personal_address2"];
				$row["zipcode"] = $row["personal_zipcode"];
				$row["city"] = $row["personal_city"];
				$row["country"] = $row["personal_country"];
			}
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
		$csv[]= $titles[$row["title"]];
		$csv[]= $commencements[$row["commencement"]];
		$csv[]= $letterheads[$row["letterhead"]];
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

		if ($addressinfo["pobox"]) {
			$csv[]= gettext("Postbus")." ".$addressinfo["pobox"];
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
		$data.= $this->generateCSVRecord($csv);
	}

?>