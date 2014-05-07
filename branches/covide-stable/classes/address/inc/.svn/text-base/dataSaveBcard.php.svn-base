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

$complete_from_rcbc = array(
	"givenname",
	"initials",
	"infix",
	"surname",
	"mobile_nr",
	"phone_nr",
	"email",
	"commencement",
	"classification",
	"letterhead",
	"business_address",
	"business_zipcode",
	"business_city",
	"business_mobile_nr",
	"business_phone_nr",
	"business_email",
	"business_fax_nr",
	"business_skype",
	"personal_address",
	"personal_zipcode",
	"personal_city",
	"personal_mobile_nr",
	"personal_phone_nr",
	"personal_email",
	"personal_fax_nr",
	"personal_skype",
	"title",
	"alternative_name",
	"businessunit",
	"department",
	"locationcode",
	"multirel",
	"business_state",
	"personal_state",
	"suffix",
	"jobtitle",
	"website",
	"business_phone_nr_2",
	"business_country",
	"business_car_phone",
	"personal_phone_nr_2",
	"personal_country",
	"other_address",
	"other_zipcode",
	"other_city",
	"other_state",
	"other_phone_nr",
	"other_phone_nr_2",
	"other_fax_nr",
	"other_mobile_nr",
	"other_email",
	"pobox",
	"pobox_country",
	"pobox_state",
	"pobox_zipcode",
	"pobox_city",
	"other_country",
	"opt_assistant_name",
	"opt_assistant_phone_nr",
	"opt_callback_phone_nr",
	"opt_company_phone_nr",
	"opt_company_name",
	"opt_manager_name",
	"opt_pager_number",
	"opt_profession",
	"opt_radio_phone_nr",
	"opt_telex_number",
	"ssn"
);

/*
$bcardinfo  = $_REQUEST["bcard"];
$metafields = $_REQUEST["metafield"];
*/
/* create 2 arrays so we can easily build the queries */

/* get the address info in variable */
$relation_info = $_REQUEST["address"];

$addresses = explode(",", $bcardinfo["address_id"]);
/* strip empty items */
$addresses = array_unique($addresses);
foreach ($addresses as $k=>$v) {
	if (!$v) {
		unset($addresses[$k]);
	}
}
// if this is an existing bcard, and it has the rcbc flag
// we need to unset rcbc, id and remove the rcbc relation from the address array
// This means we are going to create a new bc and leave the old one alone
if ($bcardinfo["id"] && count($addresses) > 1) {
	// check if this is/was an rcbc
	$sql = sprintf("SELECT address_id FROM address_businesscards WHERE rcbc = 1 AND id = %d", $bcardinfo["id"]);
	$res = sql_query($sql);
	if (sql_num_rows($res)) {
		$affected_rel = sql_result($res, 0);
		if ($affected_rel) {
			//find the item in the array and remove it
			unset($addresses[array_search($affected_rel, $addresses)]);
			$bcardinfo["rcbc"] = 0;
		}
	}
}
/* save photo */
if (is_array($_FILES["bcard"]) && $_FILES["bcard"]["error"]["binphoto"] == 0) {
	/* store the relphoto */
	$this->storeRelIMG($bcardinfo["id"], "bcards", $_FILES);
}
sort($addresses);
/* put first id we find in address_id. If more are found set multirel to the remaining values */
$bcardinfo["address_id"] = $addresses[0];

if (count($addresses) > 1) {
	unset($addresses[0]);
	$bcardinfo["multirel"] = implode(",", $addresses);
}

$fields = array();
$values = array();

if ($bcardinfo["bday_year"]) {
	$timestamp_birthday = mktime(0, 0, 0, $bcardinfo["bday_month"], $bcardinfo["bday_day"], $bcardinfo["bday_year"]);

	/* 1-1-1970 workaround */
	if (!$timestamp_birthday)
		$timestamp_birthday = 1;
} else {
	$timestamp_birthday = 0;
}

// Try to complete the data from the rcbc
if ($bcardinfo["completercbc"] && $bcardinfo["address_id"]) {
	// find rcbc
	$sql = sprintf("SELECT * FROM address_businesscards WHERE rcbc = 1 AND address_id = %d", $bcardinfo["address_id"]);
	$res = sql_query($sql);
	if (sql_num_rows($res)) {
		$rcbc = sql_fetch_assoc($res);
		foreach ($bcardinfo as $k=>$v) {
			// do some cleaning
			// classification field empty on an existing bcard means ||
			if ($k == "classification" && $v == "||") {
				$v = "";
			}
			//phone numbers can be stored as 0 which we should not count
			if (($k == "business_phone_nr" || $k == "business_phone_nr_2" || $k == "business_fax_nr"
				|| $k == "business_mobile_nr" || $k == "business_car_phone") && $v == "0") {
				$v = "";
			}
			//country field can hold 'XX' which means: non selected
			if (strstr($k, "_country") !== false && $v == "XX") {
				$v = "";
			}
			if (!trim($v) && in_array($k, $complete_from_rcbc)) {
				$bcardinfo[$k] = $rcbc[$k];
			}
		}
	}
}
$fields[] = "address_id";         $values[] = sprintf("%d",   $bcardinfo["address_id"]);
$fields[] = "alternative_name";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["alternative_name"]));
$fields[] = "businessunit";       $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["businessunit"]));
$fields[] = "department";         $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["department"]));
$fields[] = "locationcode";       $values[] = sprintf("'%s'", $bcardinfo["locationcode"]);
$fields[] = "jobtitle";           $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["jobtitle"]));
$fields[] = "website";            $values[] = sprintf("'%s'", $bcardinfo["website"]);

$fields[] = "givenname";          $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["givenname"]));
$fields[] = "initials";           $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["initials"]));
$fields[] = "infix";              $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["infix"]));
$fields[] = "surname";            $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["surname"]));
$fields[] = "rcbc";               $values[] = sprintf("%d",   $bcardinfo["rcbc"]);
$fields[] = "timestamp_birthday"; $values[] = sprintf("%d",   $timestamp_birthday);
$fields[] = "letterhead";         $values[] = sprintf("%d",   $bcardinfo["letterhead"]);
$fields[] = "commencement";       $values[] = sprintf("%d",   $bcardinfo["commencement"]);
$fields[] = "title";              $values[] = sprintf("%d",   $bcardinfo["title"]);
$fields[] = "suffix";             $values[] = sprintf("%d",   $bcardinfo["suffix"]);
$fields[] = "ssn";                $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["ssn"]));

$fields[] = "business_address";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_address"]));
$fields[] = "business_zipcode";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_zipcode"]));
$fields[] = "business_city";      $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_city"]));
$fields[] = "business_state";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_state"]));
$fields[] = "business_phone_nr";  $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_phone_nr"]));
$fields[] = "business_phone_nr_2";$values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_phone_nr_2"]));
$fields[] = "business_fax_nr";    $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_fax_nr"]));
$fields[] = "business_mobile_nr"; $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_mobile_nr"]));
$fields[] = "business_email";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_email"]));
$fields[] = "business_country";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_country"]));
$fields[] = "business_car_phone"; $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_car_phone"]));
$fields[] = "business_skype"; 	  $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["business_skype"]));

$fields[] = "personal_address";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_address"]));
$fields[] = "personal_zipcode";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_zipcode"]));
$fields[] = "personal_city";      $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_city"]));
$fields[] = "personal_state";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_state"]));
$fields[] = "personal_phone_nr";  $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_phone_nr"]));
$fields[] = "personal_phone_nr_2";$values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_phone_nr_2"]));
$fields[] = "personal_fax_nr";    $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_fax_nr"]));
$fields[] = "personal_mobile_nr"; $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_mobile_nr"]));
$fields[] = "personal_email";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_email"]));
$fields[] = "personal_country";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_country"]));
$fields[] = "personal_skype";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["personal_skype"]));

$fields[] = "other_address";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_address"]));
$fields[] = "other_zipcode";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_zipcode"]));
$fields[] = "other_city";      $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_city"]));
$fields[] = "other_state";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_state"]));
$fields[] = "other_phone_nr";  $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_phone_nr"]));
$fields[] = "other_phone_nr_2";$values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_phone_nr_2"]));
$fields[] = "other_fax_nr";    $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_fax_nr"]));
$fields[] = "other_mobile_nr"; $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_mobile_nr"]));
$fields[] = "other_email";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_email"]));
$fields[] = "other_country";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["other_country"]));

$fields[] = "opt_assistant_name";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["opt_assistant_name"]));
$fields[] = "opt_assistant_phone_nr"; $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["opt_assistant_phone_nr"]));

/* new pobox fields */
$fields[] = "pobox";           $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["pobox"]));
$fields[] = "pobox_zipcode";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["pobox_zipcode"]));
$fields[] = "pobox_city";      $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["pobox_city"]));
$fields[] = "pobox_state";     $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["pobox_state"]));
$fields[] = "pobox_country";   $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["pobox_country"]));

/* opt fields */
$fields[] = "opt_callback_phone_nr";$values[] = sprintf("'%s'", $bcardinfo["opt_callback_phone_nr"]);
$fields[] = "opt_company_phone_nr"; $values[] = sprintf("'%s'", $bcardinfo["opt_company_phone_nr"]);
$fields[] = "opt_company_name";     $values[] = sprintf("'%s'", $bcardinfo["opt_company_name"]);
$fields[] = "opt_manager_name";     $values[] = sprintf("'%s'", $bcardinfo["opt_manager_name"]);
$fields[] = "opt_pager_number";     $values[] = sprintf("'%s'", $bcardinfo["opt_pager_number"]);
$fields[] = "opt_profession";       $values[] = sprintf("'%s'", $bcardinfo["opt_profession"]);
$fields[] = "opt_radio_phone_nr";   $values[] = sprintf("'%s'", $bcardinfo["opt_radio_phone_nr"]);
$fields[] = "opt_telex_number";     $values[] = sprintf("'%s'", $bcardinfo["opt_telex_number"]);

$fields[] = "memo";            $values[] = sprintf("'%s'", sql_escape_string($bcardinfo["memo"]));
$fields[] = "classification";  $values[] = sprintf("'%s'", $bcardinfo["classification"]);
$fields[] = "multirel";        $values[] = sprintf("'%s'", $bcardinfo["multirel"]);

if ($update_modified_field) {
	$fields[] = "modified";        $values[] = sprintf("%d",   time());
	$fields[] = "modified_by";     $values[] = sprintf("%d",   $_SESSION["user_id"]);
}

/* if duplcate record comes, it comes from import */
if ($bcardinfo["duplicate_with"]) {
	$fields[] = "duplicate_with";        $values[] = sprintf("'%s'", $bcardinfo["duplicate_with"]);
}

/* save in relation */
if (is_array($relation_info) && $bcardinfo['type'] == "relations") {
	if ($relation_info['id']) {
		// find current is_active status for crm forms
		$q = sprintf("SELECT is_active FROM address where id = %d", $relation_info["id"]);
		$r = sql_query($q);
		$oldactive = sql_result($r, 0);
		if ($oldactive == -1 && $relation_info["is_active"] == 1) {
			$sender = $GLOBALS["covide"]->license["email"];
			$subject = "Inloggegevens";
			$cms_username = $bcardinfo["business_email"];
			$cms_password = chr(rand(69,90));
			for ($i=1; $i <= 6; $i++) {
				$cms_password .= chr(rand(69,90));
			}
			$body = sprintf("Uw inloggegevens voor de website van %s zijn:\n\nGebruikersnaam: %s\nWachtwoord: %s\n\n",$GLOBALS["covide"]->license["name"], $cms_username, $cms_password);
			$sql = sprintf("INSERT INTO cms_users (username, password, is_enabled, email, registration_date, is_active, address_id) VALUES ('%s', '%s', 1, '%s', %d, 1, %d)",
				$cms_username, $cms_password, $bcardinfo["business_email"], time(), $relation_info["id"]);
			$r = sql_query($sql);
			@mail($cms_username, $subject, $body, "From: $sender", "-f$sender");
		}
		$pre_sql = "UPDATE address SET ";
		$post_sql = " WHERE id = " . $relation_info['id'];
	} else {
		$pre_sql = "INSERT INTO address SET ";
		$post_sql = ", is_company = 1, is_public = 1";
		$post_sql .= "";
	}

	$sql = $pre_sql;
	$sql .= "companyname = '" . $relation_info['companyname'] . "', ";
	$sql .= "debtor_nr = '" . $relation_info['debtor_nr'] . "', ";
	$sql .= "is_customer = '" . $relation_info['is_customer'] . "', ";
	$sql .= "is_supplier = '" . $relation_info['is_supplier'] . "', ";
	$sql .= "is_person = '" . $relation_info['is_person'] . "', ";
	$sql .= "warning = '" . $relation_info['warning'] . "', ";
	$sql .= "account_manager = '" . $relation_info['account_manager'] . "', ";
	$sql .= "is_active = '" . $relation_info['is_active'] . "', ";
	$sql .= "bankaccount = '" . $relation_info['bankaccount'] . "', ";
	$sql .= "branche = '" . $relation_info['branche'] . "' ";
	$sql .= $post_sql;

	$res_rel = sql_query($sql);

	if ($relation_info['id']) {
		$relation_id = $relation_info['id'];

		$pre_info_sql = "UPDATE address_info SET ";
		$post_info_sql = " WHERE address_id = " . $relation_info['id'];
	} else {
		$relation_id = sql_insert_id('address');
		$pre_info_sql = "INSERT INTO address_info SET ";
		$post_info_sql = ", address_id = '" . $relation_id . "'";

		/*add set additional value for RCBC */
		$address_idx = array_search("address_id", $fields);
		$values[$address_idx] = sprintf("%d", $relation_id);

		$rcbc_idx = array_search("rcbc", $fields);
		$values[$rcbc_idx] = 1;
	}

	/*update address info */
	$info_sql = $pre_info_sql;
	$info_sql .= "classification = '" . $bcardinfo["classification"] . "',";
	$info_sql .= "warning = '" . $relation_info['warning'] . "'";
	$info_sql .= $post_info_sql;

	$info_res = mysql_query($info_sql);
}

if ($bcardinfo["id"]) {

	$q = sprintf("delete from address_birthdays where bcard_id = %d", $bcardinfo["id"]);
	sql_query($q);

	/* save modified bcard */
	$sql = "UPDATE address_businesscards SET ";
	foreach ($fields as $k=>$v) {
		$sql .= $v." = ".$values[$k].", ";
	}
	$sql = substr($sql, 0, strlen($sql)-2);
	$sql .= sprintf(" WHERE id=%d", $bcardinfo["id"]);

} else {
	/* inject new bcard */
	$sql = "INSERT INTO address_businesscards (".implode(",", $fields).") VALUES (".implode(",", $values).")";
}

$res = sql_query($sql);

/* update bcard <> address relation table */
if ($bcardinfo["id"]) {
	$bcard_id = $bcardinfo["id"];
	$this->updateBcardRelations($bcardinfo["id"], $bcardinfo["address_id"], $bcardinfo["multirel"]);
} else {
	$bcard_id = sql_insert_id("address_businesscards");
	$this->updateBcardRelations($bcard_id, $bcardinfo["address_id"], $bcardinfo["multirel"]);
}

/* save meta fields */
if (count($metafields)) {
	$meta_data = new Metafields_data();
	$meta_data->meta_save_field($metafields);
}
/* if this bcard is migrated from a private card */
if ($bcardinfo["src_id"]) {
	/* delete the private address */
	$this->delete($bcardinfo["src_id"], "private", 1);

	/* mark the new address for funambol sync */
	if ($bcardinfo["src_sync"]) {
		$funambol_data = new Funambol_data();
		$funambol_data->toggleAddressSync($bcardinfo["src_user"], $bcard_id, "address_businesscards");
	}
}

if ($GLOBALS["covide"]->license["has_funambol"] && !$skip_funambol) {
	$funambol_data = new Funambol_data();
	if ($bcardinfo["id"])
		$funambol_data->updateAddressById($bcardinfo["id"], "bcards");
}

/* if this bcard is set as rcbc, check if we already have one for this relation
   If so, remove the rcbc bit from the other one */
if ($bcardinfo["rcbc"]) {
	$sql = sprintf("SELECT id FROM address_businesscards WHERE rcbc = 1 AND address_id = %d AND id != %d", $bcardinfo["address_id"], $bcard_id);
	$res = sql_query($sql);
	if (sql_num_rows($res)) {
		// we got one, grab the id, and unset the rcbc field
		$row = sql_fetch_assoc($res);
		$q = sprintf("UPDATE address_businesscards SET rcbc = 0 WHERE id = %d", $row["id"]);
		$r = sql_query($q);
	}
}
if ($returnid) {
	return $bcard_id;
} else {
	if ($bcardinfo["address_id"]) {
		$extrajs = "if (parent.selectRel) parent.selectRel(".$bcardinfo["address_id"].");";
	} else {
		$extrajs = "";
	}
	/* refresh parent and close this window */
	$output = new Layout_output();
	$output->layout_page("save", 1);
	$output->start_javascript();
	$output->addCode("
		if (parent) {
			if (parent.location.href.match(/\?mod=address/gi)) {
				if (parent.location.href.match(/\&action=relcard/gi)) {
					var uri = parent.location.href;
					uri = uri.replace(/\&restore_point_steps=\d/gi, '');
					uri = uri.concat('&restore_point_steps=2');
					parent.location.href = uri;
				} else {
					parent.location.href = parent.location.href;
				}
			} else {
				if (parent.document.getElementById('deze')) {
					parent.document.getElementById('deze').submit();
				}
			}
			$extrajs
			var t = setTimeout('closepopup();', 100);
		}
	");

	$output->end_javascript();
	$output->layout_page_end(1);
	$output->exit_buffer();
}
?>
