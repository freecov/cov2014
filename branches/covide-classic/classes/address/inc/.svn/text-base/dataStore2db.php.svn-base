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
/* check if user uploaded a new photo for this contact */
if (is_array($_FILES["address"]) && $_FILES["address"]["error"]["binphoto"] == 0) {
	/* store the relphoto */
	$this->storeRelIMG($address["id"], "relations", $_FILES);
}
/* prepare the data to inject it into the db */
if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$address_letterinfo = $this->generate_letterinfo($address);
	$address["tav"] = $address_letterinfo["tav"];
	$address["contact_person"] = $address_letterinfo["contact_person"];
	$address["is_company"] = 1;
	unset($address_letterinfo);

	/* break data into 2 arrays. one for address, one for address_info */
	$address_info["warning"]        = $address["warning"];        unset($address["warning"]);
	$address_info["comment"]        = $address["comment"];        unset($address["comment"]);
	$address_info["photo"]          = $address["photo"];          unset($address["photo"]);
	$address_info["classification"] = $address["classification"]; unset($address["classification"]);
	$address_info["provision_perc"] = $address["provision_perc"]; unset($address["provision_perc"]);
} else {
	if ($address["type"] == "users" || $address["type"] == "private") {
		$address["contact_givenname"] = $address["givenname"];
		$address["contact_infix"]     = $address["infix"];
		$address["contact_surname"]   = $address["surname"];
		$address_letterinfo = $this->generate_letterinfo($address);
		$address["tav"] = $address_letterinfo["tav"];
		$address["contact_person"] = $address_letterinfo["contact_person"];
		unset($address_letterinfo);
		unset($address["contact_givenname"]);
		unset($address["contact_infix"]);
		unset($address["contact_surname"]);
	}
	$address["is_company"] = 0;
}
/* extract id cause we are not gonna inject that in the db */
/* {{{ fields for address table */
$id = $address["id"]; unset($address["id"]);
$fields = array();
$values = array();

if ($address["bday_year"]) {
	$timestamp_birthday = mktime(0, 0, 0, $address["bday_month"], $address["bday_day"], $address["bday_year"]);
	/* 1-1-1970 workaround */
	if (!$timestamp_birthday)
		$timestamp_birthday = 1;
} else {
	$timestamp_birthday = 0;
}

if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$fields[] = "contact_birthday";     $values[] = sprintf("%d",   $timestamp_birthday);
	$fields[] = "companyname";          $values[] = sprintf("'%s'", $address["companyname"]);
	$fields[] = "address2";             $values[] = sprintf("'%s'", $address["address2"]);
	$fields[] = "is_company";           $values[] = sprintf("%d",   $address["is_company"]);
	$fields[] = "account_manager";      $values[] = sprintf("%d",   $address["account_manager"]);
	$fields[] = "tav";                  $values[] = sprintf("'%s'", $address["tav"]);
	$fields[] = "contact_person";       $values[] = sprintf("'%s'", $address["contact_person"]);
	$fields[] = "contact_letterhead";   $values[] = sprintf("%d",   $address["contact_letterhead"]);
	$fields[] = "contact_commencement"; $values[] = sprintf("%d",   $address["contact_commencement"]);
	$fields[] = "contact_initials";     $values[] = sprintf("'%s'", $address["contact_initials"]);
	$fields[] = "contact_givenname";    $values[] = sprintf("'%s'", $address["contact_givenname"]);
	$fields[] = "contact_infix";        $values[] = sprintf("'%s'", $address["contact_infix"]);
	$fields[] = "contact_surname";      $values[] = sprintf("'%s'", $address["contact_surname"]);
	$fields[] = "title";                $values[] = sprintf("%d",   $address["title"]);
	$fields[] = "relname";              $values[] = sprintf("'%s'", $address["relname"]);
	$fields[] = "relpass";              $values[] = sprintf("'%s'", $address["relpass"]);
	$fields[] = "debtor_nr";            $values[] = sprintf("'%s'", $address["debtor_nr"]);
	$fields[] = "bankaccount";          $values[] = sprintf("'%s'", $address["bankaccount"]);
	$fields[] = "giro";                 $values[] = sprintf("'%s'", $address["giro"]);
	$fields[] = "bsn";                  $values[] = sprintf("'%s'", $address["bsn"]); // social security number
	$fields[] = "is_customer";          $values[] = sprintf("%d",   $address["is_customer"]);
	$fields[] = "is_supplier";          $values[] = sprintf("%d",   $address["is_supplier"]);
	$fields[] = "is_person";            $values[] = sprintf("%d",   $address["is_person"]);
	$fields[] = "jobtitle";             $values[] = sprintf("'%s'",   $address["jobtitle"]);

} elseif ($address["type"] == "overig") {
	/* can be companylocation or 'arbo' */
	if ($address["sub"] == "kantoor") {
		/* companylocation */
		$fields[] = "arbo_bedrijf";     $values[] = sprintf("%d", $address["arbo_bedrijf"]);
	} else {
		/* arbo */
		$fields[] = "arbo_team";        $values[] = sprintf("'%s'", $address["arbo_team"]);
	}
	$fields[] = "companyname";      $values[] = sprintf("'%s'", $address["companyname"]);
	$fields[] = "pobox";            $values[] = sprintf("'%s'", $address["pobox"]);
	$fields[] = "pobox_zipcode";    $values[] = sprintf("'%s'", $address["pobox_zipcode"]);
	$fields[] = "pobox_city";       $values[] = sprintf("'%s'", $address["pobox_city"]);
} else {
	$fields[] = "surname";              $values[] = sprintf("'%s'", $address["surname"]);
	$fields[] = "infix";                $values[] = sprintf("'%s'", $address["infix"]);
	$fields[] = "givenname";            $values[] = sprintf("'%s'", $address["givenname"]);

}
if (in_array($address["type"], array("relations", "nonactive", "private", "users"))) {
	$fields[] = "pobox";                $values[] = sprintf("'%s'", $address["pobox"]);
	$fields[] = "pobox_zipcode";        $values[] = sprintf("'%s'", $address["pobox_zipcode"]);
	$fields[] = "pobox_city";           $values[] = sprintf("'%s'", $address["pobox_city"]);
	$fields[] = "pobox_state";          $values[] = sprintf("'%s'", $address["pobox_state"]);
	$fields[] = "pobox_country";        $values[] = sprintf("'%s'", $address["pobox_country"]);
	$fields[] = "state";                $values[] = sprintf("'%s'", $address["state"]);
}
if (in_array($address["type"], array("private", "users"))) {

	if ($address["bday_year"]) {
		$timestamp_birthday = mktime(0, 0, 0, $address["bday_month"], $address["bday_day"], $address["bday_year"]);
		/* 1-1-1970 workaround */
		if (!$timestamp_birthday)
			$timestamp_birthday = 1;
	} else {
		$timestamp_birthday = 0;
	}

	$fields[] = "tav";                  $values[] = sprintf("'%s'", $address["tav"]);
	$fields[] = "contact_person";       $values[] = sprintf("'%s'", $address["contact_person"]);
	$fields[] = "contact_letterhead";   $values[] = sprintf("%d",   $address["contact_letterhead"]);
	$fields[] = "contact_commencement"; $values[] = sprintf("%d",   $address["contact_commencement"]);
	$fields[] = "contact_initials";     $values[] = sprintf("'%s'", $address["contact_initials"]);
	$fields[] = "title";                $values[] = sprintf("%d",   $address["title"]);

	/* new fields */
	$fields[] = "alternative_name";     $values[] = sprintf("'%s'", $address["alternative_name"]);
	$fields[] = "timestamp_birthday";   $values[] = sprintf("%d",   $timestamp_birthday);
	$fields[] = "suffix";               $values[] = sprintf("%d",   $address["suffix"]);

	$fields[] = "jobtitle";             $values[] = sprintf("'%s'", $address["jobtitle"]);
	$fields[] = "locationcode";         $values[] = sprintf("'%s'", $address["locationcode"]);
	$fields[] = "businessunit";         $values[] = sprintf("'%s'", $address["businessunit"]);
	$fields[] = "department";           $values[] = sprintf("'%s'", $address["department"]);
	$fields[] = "business_address";     $values[] = sprintf("'%s'", $address["business_address"]);
	$fields[] = "business_phone_nr";    $values[] = sprintf("'%s'", $address["business_phone_nr"]);
	$fields[] = "business_city";        $values[] = sprintf("'%s'", $address["business_city"]);
	$fields[] = "business_phone_nr_2";  $values[] = sprintf("'%s'", $address["business_phone_nr_2"]);
	$fields[] = "business_state";       $values[] = sprintf("'%s'", $address["business_state"]);
	$fields[] = "business_fax_nr";      $values[] = sprintf("'%s'", $address["business_fax_nr"]);
	$fields[] = "business_zipcode";     $values[] = sprintf("'%s'", $address["business_zipcode"]);
	$fields[] = "business_mobile_nr";   $values[] = sprintf("'%s'", $address["business_mobile_nr"]);
	$fields[] = "business_country";     $values[] = sprintf("'%s'", $address["business_country"]);
	$fields[] = "business_car_phone";   $values[] = sprintf("'%s'", $address["business_car_phone"]);
	$fields[] = "business_email";       $values[] = sprintf("'%s'", $address["business_email"]);
	$fields[] = "phone_nr_2";           $values[] = sprintf("'%s'", $address["phone_nr_2"]);
	$fields[] = "other_address";        $values[] = sprintf("'%s'", $address["other_address"]);
	$fields[] = "other_phone_nr";       $values[] = sprintf("'%s'", $address["other_phone_nr"]);
	$fields[] = "other_city";           $values[] = sprintf("'%s'", $address["other_city"]);
	$fields[] = "other_phone_nr_2";     $values[] = sprintf("'%s'", $address["other_phone_nr_2"]);
	$fields[] = "other_state";          $values[] = sprintf("'%s'", $address["other_state"]);
	$fields[] = "other_fax_nr";         $values[] = sprintf("'%s'", $address["other_fax_nr"]);
	$fields[] = "other_zipcode";        $values[] = sprintf("'%s'", $address["other_zipcode"]);
	$fields[] = "other_mobile_nr";      $values[] = sprintf("'%s'", $address["other_mobile_nr"]);
	$fields[] = "other_country";        $values[] = sprintf("'%s'", $address["other_country"]);
	$fields[] = "other_email";          $values[] = sprintf("'%s'", $address["other_email"]);

	//$fields[] = "pobox_state";          $values[] = sprintf("'%s'", $address["pobox_state"]);
	//$fields[] = "pobox_country";        $values[] = sprintf("'%s'", $address["pobox_country"]);

	/* opt fields */
	$fields[] = "opt_callback_phone_nr";$values[] = sprintf("'%s'", $address["opt_callback_phone_nr"]);
	$fields[] = "opt_company_phone_nr"; $values[] = sprintf("'%s'", $address["opt_company_phone_nr"]);
	$fields[] = "opt_company_name";     $values[] = sprintf("'%s'", $address["opt_company_name"]);
	$fields[] = "opt_manager_name";     $values[] = sprintf("'%s'", $address["opt_manager_name"]);
	$fields[] = "opt_pager_number";     $values[] = sprintf("'%s'", $address["opt_pager_number"]);
	$fields[] = "opt_profession";       $values[] = sprintf("'%s'", $address["opt_profession"]);
	$fields[] = "opt_radio_phone_nr";   $values[] = sprintf("'%s'", $address["opt_radio_phone_nr"]);
	$fields[] = "opt_telex_number";     $values[] = sprintf("'%s'", $address["opt_telex_number"]);

	$fields[] = "opt_assistant_name";     $values[] = sprintf("'%s'", $address["opt_assistant_name"]);
	$fields[] = "opt_assistant_phone_nr"; $values[] = sprintf("'%s'", $address["opt_assistant_phone_nr"]);

}

$fields[] = "address";              $values[] = sprintf("'%s'", $address["address"]);
$fields[] = "zipcode";              $values[] = sprintf("'%s'", $address["zipcode"]);
$fields[] = "city";                 $values[] = sprintf("'%s'", $address["city"]);
$fields[] = "phone_nr";             $values[] = sprintf("'%s'", $address["phone_nr"]);
$fields[] = "fax_nr";               $values[] = sprintf("'%s'", $address["fax_nr"]);
$fields[] = "email";                $values[] = sprintf("'%s'", $address["email"]);
$fields[] = "user_id";              $values[] = sprintf("%d",   $address["user_id"]);
$fields[] = "mobile_nr";            $values[] = sprintf("'%s'", $address["mobile_nr"]);
$fields[] = "website";              $values[] = sprintf("'%s'", $address["website"]);
if ($address["type"] != "overig") {
	$fields[] = "country";              $values[] = sprintf("'%s'", $address["country"]);
	$fields[] = "modified";             $values[] = sprintf("%d",   mktime());
	$fields[] = "modified_by";          $values[] = sprintf("%d",   $_SESSION["user_id"]);
	$fields[] = "is_active";            $values[] = sprintf("%d",   $address["is_active"]);
}
$fields[] = "is_public";            $values[] = sprintf("%d",   $address["is_public"]);
/* }}} */
/* {{{ address_info fields and values */
if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$fields_info[] = "warning";        $values_info[] = sprintf("'%s'", $address_info["warning"]);
	$fields_info[] = "comment";           $values_info[] = sprintf("'%s'", $address_info["comment"]);
	$fields_info[] = "classification"; $values_info[] = sprintf("'%s'", $address_info["classification"]);
	$fields_info[] = "provision_perc"; $values_info[] = sprintf("%d", $address_info["provision_perc"]);
} elseif ($address["type"] == "private") {
	$fields[] = "comment";           $values[] = sprintf("'%s'", $address["comment"]);
}

/* }}} */
if ($id) {
	// find current is_active status for crm forms
	$q = sprintf("SELECT is_active FROM address where id = %d", $id);
	$r = sql_query($q);
	$oldactive = sql_result($r, 0);
	if ($oldactive == -1 && $address["is_active"] == 1) {
		$sender = $GLOBALS["covide"]->license["email"];
		$subject = "Inloggegevens";
		$cms_username = $address["email"];
		$cms_password = chr(rand(69,90));
		for ($i=1; $i <= 6; $i++) {
			$cms_password .= chr(rand(69,90));
		}
		$body = sprintf("Uw inloggegevens voor de website van %s zijn:\n\nGebruikersnaam: %s\nWachtwoord: %s\n\n",$GLOBALS["covide"]->license["name"], $cms_username, $cms_password);
		$sql = sprintf("INSERT INTO cms_users (username, password, is_enabled, email, registration_date, is_active, address_id) VALUES ('%s', '%s', 1, '%s', %d, 1, %d)",
			$cms_username, $cms_password, $address["email"], mktime(), $id);
		$r = sql_query($sql);
		@mail($cms_username, $subject, $body, "From: $sender", "-f$sender");
	}
	/* update folder as well. fixes bug #1560558 */
	if ($address["type"] == "relations" || $address["type"] == "nonactive") {
		$filesys_data = new Filesys_data();
		$filesys_folder = $filesys_data->getRelFolder($id);
		$folderupdate = sprintf("UPDATE filesys_folders SET name='%s' WHERE id=%d", $address["companyname"], $filesys_folder);
	}
	if ($folderupdate)
		$res_folderupdate = sql_query($folderupdate);

	if (count($metafields)) {
		$meta_data = new Metafields_data();
		$meta_data->meta_save_field($metafields);
	}
	/* update for address */
	if ($address["type"] == "relations" || $address["type"] == "nonactive") {
		$sql = "UPDATE address SET ";
	} elseif($address["type"] == "overig") {
		$sql = "UPDATE address_other SET ";
	} else {
		$sql = "UPDATE address_private SET ";
	}
	foreach ($fields as $k=>$v) {
		$sql .= $v."=".$values[$k].", ";
	}
	$sql  = substr($sql, 0, strlen($sql)-2);
	$sql .= sprintf(" WHERE id=%d", $id);

	$res = sql_query($sql);

	if ($address["type"] == "relations" || $address["type"] == "nonactive") {
		$sql_info = "UPDATE address_info SET ";
		foreach ($fields_info as $k=>$v) {
			$sql_info .= $v."=".$values_info[$k].", ";
		}
		$sql_info  = substr($sql_info, 0, strlen($sql_info)-2);
		$sql_info .= sprintf(" WHERE address_id=%d", $id);
		$res_info = sql_query($sql_info);
	}
	if ($address["type"] == "relations") {
		//check rcbc
		$address["id"] = $id;
		$address["classification"] = $address_info["classification"];
		$address["ssn"] = $address["bsn"];
		$address["memo"] = $address_info["comment"];
		$this->checkrcbc($address);

	}
	$new_id = $id;

} else {
	/* new address record */
	if ($address["type"] != "relations" && $address["type"] != "nonactive") {
		$sql = "INSERT INTO address_private (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
		$new_id = sql_insert_id("address_private");
	} elseif($address["type"] == "overig") {
		$sql = "INSERT INTO address_other (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
		$new_id = sql_insert_id("address_other");
	} else {
		$sql = "INSERT INTO address (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
		$address_id = sql_insert_id("address", $GLOBALS["covide"]->db->connection);
		$new_id = $address_id;
		$fields_info[] = "address_id"; $values_info[] = $address_id;
		$sql_info = "INSERT INTO address_info (".implode(",", $fields_info).") VALUES(".implode(",", $values_info).");";
		$res_info = sql_query($sql_info);
		//create rcbc
		$address["id"] = $address_id;
		$address["classification"] = $address_info["classification"];
		$address["ssn"] = $address["bsn"];
		$address["memo"] = $address_info["comment"];
		$this->checkrcbc($address);
	}
}
/* check if we have twinfield */
if ($GLOBALS["covide"]->license["has_twinfield"] && ($address["type"] == "relations" || $address["type"] == "nonactive")) {
	//send data to twinfield server so we can save stuff there as well
	$tw_address = array(
		"code"           => sprintf("%d", $address["debtor_nr"]),
		"companyname"    => sprintf("%s", $address["companyname"]),
		"url"            => sprintf("%s", $address["website"]),
		"contact_person" => sprintf("%s", $address["contact_person"]),
		"address"        => sprintf("%s", $address["address"]),
		"zipcode"        => sprintf("%s", $address["zipcode"]),
		"city"           => sprintf("%s", $address["city"]),
		"country"        => sprintf("%s", $address["country"]),
		"phonenr"        => sprintf("%s", $address["phone_nr"]),
		"faxnr"          => sprintf("%s", $address["fax_nr"]),
		"email"          => sprintf("%s", $address["email"]),
		"address_id"     => sprintf("%d", $new_id),
		"administration" => sprintf("%d", $address["twinfield_administration"])
	);
	$twinfield_data = new twinfield_data();
	$twinfield_data->saveAddress($tw_address);
}

/* if this address is migrated from a private card */
if ($address["src_id"]) {
	/* delete the private address */
	$this->delete($address["src_id"], "private", 1);

	/* mark the new address for funambol sync */
	$funambol_data = new Funambol_data();
	$funambol_data->toggleAddressSync($_SESSION["user_id"], $new_id, "address");
}

if ($GLOBALS["covide"]->license["has_funambol"] && !$skip_funambol) {
	$funambol_data = new Funambol_data();
	if ($id)
		$funambol_data->updateAddressById($id, $address["type"]);
}

if (!$skip_funambol && !$src_id) {
	$output = new Layout_output();
	$output->start_javascript();
		$output->addCode("
			if (opener) {
				if (opener.location.href.match(/\?mod=address/gi)) {
					if (opener.location.href.match(/\&action=relcard/gi)) {
						var uri = opener.location.href;
						uri = uri.replace(/\&restore_point_steps=\d/gi, '');
						uri = uri.concat('&restore_point_steps=2');
						opener.location.href = uri;
					} else {
						opener.location.href = opener.location.href;
					}
				} else {
					if (opener.document.getElementById('classifications_frm')) {
						opener.document.getElementById('classifications_frm').submit();
					} else {
						opener.document.getElementById('deze').submit();
					}
				}
				var t = setTimeout('window.close();', 100);
			}
		");
	$output->end_javascript();
	$output->exit_buffer();
}
?>
