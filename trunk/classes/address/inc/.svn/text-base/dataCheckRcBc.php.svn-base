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
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

if (!class_exists("Address_data")) {
	die("no class definition found");
}
/* fill bcardinfo array with the stuff we want to save on the rcbc */
$bcardinfo = array(
	"address_id" => $addressdata["id"],
	"givenname" => addslashes($addressdata["contact_givenname"]),
	"initials" => addslashes($addressdata["contact_initials"]),
	"infix" => addslashes($addressdata["contact_infix"]),
	"surname" => addslashes($addressdata["contact_surname"]),
	"rcbc" => 1,
	"letterhead" => addslashes($addressdata["contact_letterhead"]),
	"commencement" => addslashes($addressdata["contact_commencement"]),
	"business_address" => addslashes($addressdata["address"]),
	"business_zipcode" => addslashes($addressdata["zipcode"]),
	"business_city" => addslashes($addressdata["city"]),
	"business_country" => addslashes($addressdata["country"]),
	"business_phone_nr" => addslashes($addressdata["phone_nr"]),
	"business_fax_nr" => addslashes($addressdata["fax_nr"]),
	"business_mobile_nr" => addslashes($addressdata["mobile_nr"]),
	"business_email" => addslashes($addressdata["email"]),
	"website" => addslashes($addressdata["website"]),
	"pobox" => addslashes($addressdata["pobox"]),
	"pobox_zipcode" => addslashes($addressdata["pobox_zipcode"]),
	"pobox_city" => addslashes($addressdata["pobox_city"]),
	"memo" => addslashes($addressdata["memo"]),
	"classification" => addslashes($addressdata["classification"]),
	"ssn" => addslashes($addressdata["ssn"]),
	"jobtitle" => addslashes($addressdata["jobtitle"]),
	"duplicate_with" => $addressdata["duplicate_with"],
);

/* check if we already have an rcbc */
$sql = sprintf("SELECT id FROM address_businesscards WHERE rcbc = 1 AND address_id = %d", $addressdata["id"]);
$res = sql_query($sql);
if (sql_num_rows($res) == 0) {
	//does not exist, so create it
	$rcbc_id = $this->save_bcard($bcardinfo, NULL, 1);
}
?>
