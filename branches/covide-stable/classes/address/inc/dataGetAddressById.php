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
	if (!class_exists("Address_data")) {
		die("no class definition found");
	}
	$user = new User_data();
	$user->getUserPermissionsById($_SESSION["user_id"]);
	$accmanager_arr = explode(",", $user->permissions["addressaccountmanage"]);
	if (!$accmanager_arr[0])
		unset($accmanager_arr[0]);
	$accmanager_arr[] = $_SESSION["user_id"];
	if ($type == "bcards") {
		$sql = sprintf("SELECT address_businesscards.*, address.companyname FROM address_businesscards left join address on address.id = address_businesscards.address_id WHERE address_businesscards.id=%d", $addressid);
		$res = sql_query($sql);
		$adresinfo = sql_fetch_assoc($res);
		if (!is_array($adresinfo))
			$adresinfo = array();
		/* generate classification names */
		$classifications = explode("|", $adresinfo["classification"]);
		foreach ($classifications as $k=>$v) {
			if (!$v) {
				unset($classifications[$k]);
			}
		}
		$classifications[] = 0;
		$classifications = implode(",", $classifications);
		$query = sprintf("SELECT * FROM address_classifications WHERE id IN (%s) ORDER BY upper(description)", $classifications);
		$result = sql_query($query);
		$classification_names = "";
		while ($row = sql_fetch_assoc($result)) {
			$classification_names .= $row["description"]."\n";
		}

		/* make photo an array with the seperate parts of info */
		$photo = explode("|", $adresinfo["photo"]);
		unset($adresinfo["photo"]);
		if (count($photo) == 3) {
			/* valid photo */
			$adresinfo["photo"]["id"]   = $adresinfo["id"];
			$adresinfo["photo"]["size"] = $photo[0];
			$adresinfo["photo"]["type"] = $photo[1];
			$adresinfo["photo"]["name"] = $photo[2];
		} else {
			$adresinfo["photo"] = array(
				"id"   => 0,
				"size" => 0,
				"type" => "unknown",
				"name" => "unknown"
			);
		}
		$adresinfo["business_phone_nr_link"]  = $this->show_phonenr($adresinfo["business_phone_nr"], $adresinfo["address_id"], $_SESSION["user_id"]);
		$adresinfo["business_mobile_nr_link"] = $this->show_phonenr($adresinfo["business_mobile_nr"], $adresinfo["address_id"], $_SESSION["user_id"]);
		$adresinfo["personal_phone_nr_link"]  = $this->show_phonenr($adresinfo["personal_phone_nr"], $adresinfo["address_id"], $_SESSION["user_id"]);
		$adresinfo["personal_mobile_nr_link"] = $this->show_phonenr($adresinfo["personal_mobile_nr"], $adresinfo["address_id"], $_SESSION["user_id"]);
		$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
		$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
		$adresinfo["classification_names"] = $classification_names;
		$adresinfo["letterinfo"] = $this->generate_letterinfo(array(
			"contact_initials"     => $adresinfo["initials"],
			"contact_letterhead"   => $adresinfo["letterhead"],
			"contact_commencement" => $adresinfo["commencement"],
			"contact_givenname"    => $adresinfo["givenname"],
			"contact_infix"        => $adresinfo["infix"],
			"contact_surname"      => $adresinfo["surname"],
			"title"                => $adresinfo["title"]
		));
		$adresinfo["tav"] = $adresinfo["letterinfo"]["tav"];
		$adresinfo["contact_person"] = $adresinfo["letterinfo"]["contact_person"];

	} elseif($type == "overig") {
		$sql = sprintf("SELECT * FROM address_other WHERE id = %d", $addressid);
		$res = sql_query($sql);
		$adresinfo = sql_fetch_assoc($res);
		$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
		$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
	} elseif ($type != "relations" && $type != "nonactive" && $type != "address") {
		$sql = sprintf("SELECT * FROM address_private WHERE id=%d", $addressid);
		$res = sql_query($sql);
		$adresinfo = sql_fetch_assoc($res);
		if ($adresinfo["contact_initials"] == 0)
			$adresinfo["contact_initials"] = "";
		$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"]);
		$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"]);
		$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
		$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
	} else {
		$sql = sprintf("
			SELECT
				address.*,
				address_info.classification as classifi,
				address_info.provision_perc,
				address_info.warning as letop,
				address_info.comment as memo,
				address_info.classification as classifi,
				address_info.photo as photo
			FROM address
			LEFT JOIN address_info ON address.id = address_info.address_id
			WHERE
				address.id=%d",
				$addressid);
		$res = sql_query($sql);
		$adresinfo = sql_fetch_assoc($res);
		$adresinfo["classification"] = $adresinfo["classifi"];
		$adresinfo["warning"] = $adresinfo["letop"];
		/* make photo an array with the seperate parts of info */
		$photo = explode("|", $adresinfo["photo"]);
		unset($adresinfo["photo"]);
		if (count($photo) == 3) {
			/* valid photo */
			$adresinfo["photo"]["id"]   = $adresinfo["id"];
			$adresinfo["photo"]["size"] = $photo[0];
			$adresinfo["photo"]["type"] = $photo[1];
			$adresinfo["photo"]["name"] = $photo[2];
		} else {
			$adresinfo["photo"] = array(
				"id"   => 0,
				"size" => 0,
				"type" => "unknown",
				"name" => "unknown"
			);
		}
		if ($migrate == 0) {
			//grab rcbc for address info
			$bcard_info = $this->getRCBCByAddressId($addressid);
			if (is_array($bcard_info)) {
				$bcard_info["bcard_id"] = $bcard_info["id"];
				unset($bcard_info["id"]);
				$bcard_info["email"] = $bcard_info["business_email"];
				$bcard_info["classifi"] = $bcard_info["classification"];
				$bcard_info["address"] = $bcard_info["business_address"];
				$bcard_info["zipcode"] = $bcard_info["business_zipcode"];
				$bcard_info["city"] = $bcard_info["business_city"];
				$bcard_info["country"] = $bcard_info["business_country"];
				$bcard_info["mobile_nr"] = $bcard_info["business_mobile_nr"];
				$bcard_info["phone_nr"] = $bcard_info["business_phone_nr"];
				$bcard_info["fax_nr"] = $bcard_info["business_fax_nr"];

				$bcard_info["contact_initials"]     = $bcard_info["initials"];
				$bcard_info["contact_letterhead"]   = $bcard_info["letterhead"];
				$bcard_info["contact_commencement"] = $bcard_info["commencement"];
				$bcard_info["contact_givenname"]    = $bcard_info["givenname"];
				$bcard_info["contact_infix"]        = $bcard_info["infix"];
				$bcard_info["contact_surname"]      = $bcard_info["surname"];
				$bcard_info["contact_title"]        = $bcard_info["title"];
				if (!$bcard_info["modified"]) {
					$bcard_info["modified"] = $adresinfo["modified"];
				}
				if (!$bcard_info["modified_by"]) {
					$bcard_info["modified_by"] = $adresinfo["modified_by"];
				}
				$adresinfo = array_merge($adresinfo, $bcard_info);
			}
		}
		if ($adresinfo["is_person"]) {
			$adresinfo["companyname"] = preg_replace("/ {1,}/", " ", sprintf("%s, %s %s", $adresinfo["contact_surname"], $adresinfo["contact_givenname"], $adresinfo["contact_infix"]));
		}
		$adresinfo["letterinfo"] = $this->generate_letterinfo(array(
			"contact_initials"     => $adresinfo["initials"],
			"contact_letterhead"   => $adresinfo["letterhead"],
			"contact_commencement" => $adresinfo["commencement"],
			"contact_givenname"    => $adresinfo["givenname"],
			"contact_infix"        => $adresinfo["infix"],
			"contact_surname"      => $adresinfo["surname"],
			"title"                => $adresinfo["title"]
		));
		$adresinfo["tav"] = $adresinfo["letterinfo"]["tav"];
		$adresinfo["contact_person"] = $adresinfo["letterinfo"]["contact_person"];
		/* generate classification names */
		$classifications = explode("|", $adresinfo["classifi"]);
		foreach ($classifications as $k=>$v) {
			if (!$v) {
				unset($classifications[$k]);
			}
		}
		$classifications[] = 0;
		$classifications = implode(",", $classifications);
		$query = sprintf("SELECT description FROM address_classifications WHERE id IN (%s) ORDER BY upper(description)", $classifications);
		$result = sql_query($query);
		$classification_names = "";
		while ($row = sql_fetch_assoc($result)) {
			$classification_names .= $row["description"]."\n";
		}
		/* if address is a relation, we can have an account manager. init user object and get name */
		if ($type == "relations" || $type == "nonactive") {
			$adresinfo["account_manager_name"] = $user->getUsernameById($adresinfo["account_manager"]);
			if ($GLOBALS["covide"]->license["has_twinfield"]) {
				/* get the twinfield office this address is in */
				$q = sprintf("SELECT office_id FROM address_info_twinfield WHERE address_id = %d", $addressid);
				$r = sql_query($q);
				$adresinfo["twinfield_office"] = sql_result($r, 0);
			}
			/* Get the last modified by info*/
			if ($adresinfo["modified_by"] && $adresinfo["modified"]) {
				$adresinfo["changed_by_name"] = $user->getUsernameById($adresinfo["modified_by"]);
				$adresinfo["changed_human_date"] = date("d-m-Y H:i", $adresinfo["modified"]);
			}
		}
		/* fix website */
		if (!preg_match("/^http(s){0,1}:\/\//si", $adresinfo["website"]))
			$adresinfo["website"] = "http://".$adresinfo["website"];

		$adresinfo["classification_names"] = $classification_names;
		$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"], $adresinfo["id"], $_SESSION["user_id"]);
		$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"], $adresinfo["id"], $_SESSION["user_id"]);
		$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
		$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
	}
	if ($user->checkPermission("xs_addressmanage")) {
		$adresinfo["addressmanage"] = 1;
		$adresinfo["addressacc"]    = 1;
	} else {
		$adresinfo["addressmanage"] = 0;
		if ($adresinfo["account_manager"] && in_array($adresinfo["account_manager"], $accmanager_arr)) {
			$adresinfo["addressacc"] = 1;
		} else {
			$adresinfo["addressacc"] = 0;
			$adresinfo["noaccess"]   = 1;
		}
	}
	if ($adresinfo["timestamp_birthday"]) {
		$adresinfo["h_birthday"] = date("d-m-Y", $adresinfo["timestamp_birthday"]);
	}
	if (!trim($adresinfo["fullname"])) $adresinfo["fullname"] = "--";

	// run stripslashes on fields we send back and forth to the RCBC
	$slashfields = array("givenname", "contact_givenname",
		"initials", "contact_initials",
		"infix", "contact_infix",
		"surname", "contact_surname",
		"letterhead", "contact_letterhead",
		"commencement", "contact_commencement",
		"business_address", "address",
		"business_zipcode", "zipcode",
		"business_city", "city",
		"business_phone_nr", "phone_nr",
		"business_fax_nr", "fax_nr",
		"business_mobile_nr", "mobile_nr",
		"business_email", "email",
		"website", "website",
		"pobox", "pobox",
		"pobox_zipcode", "pobox_zipcode",
		"pobox_city", "pobox_city",
		"memo", "memo",
		"classification", "classification",
		"ssn", "ssn",
		"jobtitle", "jobtitle",
	);
	foreach ($slashfields as $f) {
		$adresinfo[$f] = stripslashes($adresinfo[$f]);
	}

?>
