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
	"business_address",
	"business_phone_nr",
	"business_city",
	"business_phone_nr_2",
	"business_state",
	"business_fax_nr",
	"business_zipcode",
	"business_mobile_nr",
	"business_country",
	"business_car_phone",
	"opt_callback_phone_nr",
	"opt_pager_number",
	"opt_radio_phone_nr",
	"opt_telex_number",
	"pobox",
	"pobox_zipcode",
	"pobox_city",
	"pobox_country",
	"pobox_state",
	"other_address",
	"other_phone_nr",
	"other_city",
	"other_phone_nr_2",
	"other_state",
	"other_fax_nr",
	"other_zipcode",
	"other_mobile_nr",
	"other_country",
	"other_email",
	"classification",
);

/*
$bcardinfo  = $_REQUEST["bcard"];
$metafields = $_REQUEST["metafield"];
*/
/* create 2 arrays so we can easily build the queries */

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
if ($bcardinfo["completercbc"]) {
	// find rcbc
	$sql = sprintf("SELECT * FROM address_businesscards WHERE rcbc = 1 AND address_id = %d", $bcardinfo["address_id"]);
	$res = sql_query($sql);
	if ($res->numRows()) {
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
$fields[] = "alternative_name";   $values[] = sprintf("'%s'", $bcardinfo["alternative_name"]);
$fields[] = "businessunit";       $values[] = sprintf("'%s'", $bcardinfo["businessunit"]);
$fields[] = "department";         $values[] = sprintf("'%s'", addslashes($bcardinfo["department"]));
$fields[] = "locationcode";       $values[] = sprintf("'%s'", $bcardinfo["locationcode"]);
$fields[] = "jobtitle";           $values[] = sprintf("'%s'", $bcardinfo["jobtitle"]);
$fields[] = "website";            $values[] = sprintf("'%s'", $bcardinfo["website"]);

$fields[] = "givenname";          $values[] = sprintf("'%s'", $bcardinfo["givenname"]);
$fields[] = "initials";           $values[] = sprintf("'%s'", $bcardinfo["initials"]);
$fields[] = "infix";              $values[] = sprintf("'%s'", $bcardinfo["infix"]);
$fields[] = "surname";            $values[] = sprintf("'%s'", $bcardinfo["surname"]);
$fields[] = "rcbc";               $values[] = sprintf("%d",   $bcardinfo["rcbc"]);
$fields[] = "timestamp_birthday"; $values[] = sprintf("%d",   $timestamp_birthday);
$fields[] = "letterhead";         $values[] = sprintf("%d",   $bcardinfo["letterhead"]);
$fields[] = "commencement";       $values[] = sprintf("%d",   $bcardinfo["commencement"]);
$fields[] = "title";              $values[] = sprintf("%d",   $bcardinfo["title"]);
$fields[] = "suffix";             $values[] = sprintf("%d",   $bcardinfo["suffix"]);
$fields[] = "ssn";                $values[] = sprintf("'%s'", $bcardinfo["ssn"]);

$fields[] = "business_address";   $values[] = sprintf("'%s'", $bcardinfo["business_address"]);
$fields[] = "business_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["business_zipcode"]);
$fields[] = "business_city";      $values[] = sprintf("'%s'", $bcardinfo["business_city"]);
$fields[] = "business_state";     $values[] = sprintf("'%s'", $bcardinfo["business_state"]);
$fields[] = "business_phone_nr";  $values[] = sprintf("'%s'", $bcardinfo["business_phone_nr"]);
$fields[] = "business_phone_nr_2";$values[] = sprintf("'%s'", $bcardinfo["business_phone_nr_2"]);
$fields[] = "business_fax_nr";    $values[] = sprintf("'%s'", $bcardinfo["business_fax_nr"]);
$fields[] = "business_mobile_nr"; $values[] = sprintf("'%s'", $bcardinfo["business_mobile_nr"]);
$fields[] = "business_email";     $values[] = sprintf("'%s'", $bcardinfo["business_email"]);
$fields[] = "business_country";   $values[] = sprintf("'%s'", $bcardinfo["business_country"]);
$fields[] = "business_car_phone"; $values[] = sprintf("'%s'", $bcardinfo["business_car_phone"]);

$fields[] = "personal_address";   $values[] = sprintf("'%s'", $bcardinfo["personal_address"]);
$fields[] = "personal_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["personal_zipcode"]);
$fields[] = "personal_city";      $values[] = sprintf("'%s'", $bcardinfo["personal_city"]);
$fields[] = "personal_state";     $values[] = sprintf("'%s'", $bcardinfo["personal_state"]);
$fields[] = "personal_phone_nr";  $values[] = sprintf("'%s'", $bcardinfo["personal_phone_nr"]);
$fields[] = "personal_phone_nr_2";$values[] = sprintf("'%s'", $bcardinfo["personal_phone_nr_2"]);
$fields[] = "personal_fax_nr";    $values[] = sprintf("'%s'", $bcardinfo["personal_fax_nr"]);
$fields[] = "personal_mobile_nr"; $values[] = sprintf("'%s'", $bcardinfo["personal_mobile_nr"]);
$fields[] = "personal_email";     $values[] = sprintf("'%s'", $bcardinfo["personal_email"]);
$fields[] = "personal_country";   $values[] = sprintf("'%s'", $bcardinfo["personal_country"]);

$fields[] = "other_address";   $values[] = sprintf("'%s'", $bcardinfo["other_address"]);
$fields[] = "other_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["other_zipcode"]);
$fields[] = "other_city";      $values[] = sprintf("'%s'", $bcardinfo["other_city"]);
$fields[] = "other_state";     $values[] = sprintf("'%s'", $bcardinfo["other_state"]);
$fields[] = "other_phone_nr";  $values[] = sprintf("'%s'", $bcardinfo["other_phone_nr"]);
$fields[] = "other_phone_nr_2";$values[] = sprintf("'%s'", $bcardinfo["other_phone_nr_2"]);
$fields[] = "other_fax_nr";    $values[] = sprintf("'%s'", $bcardinfo["other_fax_nr"]);
$fields[] = "other_mobile_nr"; $values[] = sprintf("'%s'", $bcardinfo["other_mobile_nr"]);
$fields[] = "other_email";     $values[] = sprintf("'%s'", $bcardinfo["other_email"]);
$fields[] = "other_country";   $values[] = sprintf("'%s'", $bcardinfo["other_country"]);

$fields[] = "opt_assistant_name";     $values[] = sprintf("'%s'", $bcardinfo["opt_assistant_name"]);
$fields[] = "opt_assistant_phone_nr"; $values[] = sprintf("'%s'", $bcardinfo["opt_assistant_phone_nr"]);

/* new pobox fields */
$fields[] = "pobox";           $values[] = sprintf("'%s'", $bcardinfo["pobox"]);
$fields[] = "pobox_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["pobox_zipcode"]);
$fields[] = "pobox_city";      $values[] = sprintf("'%s'", $bcardinfo["pobox_city"]);
$fields[] = "pobox_state";     $values[] = sprintf("'%s'", $bcardinfo["pobox_state"]);
$fields[] = "pobox_country";   $values[] = sprintf("'%s'", $bcardinfo["pobox_country"]);

/* opt fields */
$fields[] = "opt_callback_phone_nr";$values[] = sprintf("'%s'", $bcardinfo["opt_callback_phone_nr"]);
$fields[] = "opt_company_phone_nr"; $values[] = sprintf("'%s'", $bcardinfo["opt_company_phone_nr"]);
$fields[] = "opt_company_name";     $values[] = sprintf("'%s'", $bcardinfo["opt_company_name"]);
$fields[] = "opt_manager_name";     $values[] = sprintf("'%s'", $bcardinfo["opt_manager_name"]);
$fields[] = "opt_pager_number";     $values[] = sprintf("'%s'", $bcardinfo["opt_pager_number"]);
$fields[] = "opt_profession";       $values[] = sprintf("'%s'", $bcardinfo["opt_profession"]);
$fields[] = "opt_radio_phone_nr";   $values[] = sprintf("'%s'", $bcardinfo["opt_radio_phone_nr"]);
$fields[] = "opt_telex_number";     $values[] = sprintf("'%s'", $bcardinfo["opt_telex_number"]);

$fields[] = "memo";            $values[] = sprintf("'%s'", $bcardinfo["memo"]);
$fields[] = "classification";  $values[] = sprintf("'%s'", $bcardinfo["classification"]);
$fields[] = "multirel";        $values[] = sprintf("'%s'", $bcardinfo["multirel"]);
if ($update_modified_field) {
	$fields[] = "modified";        $values[] = sprintf("%d",   mktime());
	$fields[] = "modified_by";     $values[] = sprintf("%d",   $_SESSION["user_id"]);
}

if ($bcardinfo["id"]) {

	$q = sprintf("delete from address_birthdays where bcard_id = %d", $bcardinfo["id"]);
	sql_query($q);

	if (count($metafields)) {
		$meta_data = new Metafields_data();
		$meta_data->meta_save_field($metafields);
	}
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
