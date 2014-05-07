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
	die("no class definition found.");
}

set_time_limit(60*60*2);

$filename             = $data["filename"];
$company_id = $data["vcard"]["address_id"];
unset($data);

/* first get the data to import from the file */
$ok = strpos($filename, $GLOBALS["covide"]->temppath);
if ($ok === false) {
	die("Access is denied.");
}
$fp = fopen($filename, "r");
$data = unserialize(file_get_contents($filename));
fclose($fp);

if ($company_id) {
	$bcardinfo["address_id"] = $company_id;
	$bcardinfo["alternative_name"] = $data["alternative_name"];
	$bcardinfo["businessunit"] = $data["work_office"];
	$bcardinfo["department"] = $data["department"];
	$bcardinfo["jobtitle"] = $data["jobtitle"];
	$bcardinfo["website"] = $data["website"];
	$bcardinfo["givenname"] = $data["givenname"];
	$bcardinfo["infix"] = $data["infix"];
	$bcardinfo["surname"] = $data["surname"];
	$bcardinfo["title"] = $data["title"];
	if (!empty($data["birthday"])) {
		$bcardinfo["bday_year"] = date("Y", $data["birthday"]);
		$bcardinfo["bday_month"] = date("m", $data["birthday"]);
		$bcardinfo["bday_day"] = date("d", $data["birthday"]);
	}
	$bcardinfo["business_address"] = $data["work_address"];
	$bcardinfo["business_zipcode"] = $data["work_zipcode"];
	$bcardinfo["business_city"] = $data["work_city"];
	$bcardinfo["business_state"] = $data["work_state"];
	$bcardinfo["business_country"] = $data["work_country"];
	$bcardinfo["business_fax_nr"] = $data["fax_work"];
	$bcardinfo["business_phone_nr"] = $data["phone_work"];
	$bcardinfo["business_email"] = $data["work_email"];
	$bcardinfo["personal_address"] = $data["personal_address"];
	$bcardinfo["personal_zipcode"] = $data["personal_zipcode"];
	$bcardinfo["personal_city"] = $data["personal_city"];
	$bcardinfo["personal_state"] = $data["personal_state"];
	$bcardinfo["personal_country"] = $data["personal_country"];
	$bcardinfo["personal_fax_nr"] = $data["fax_home"];
	$bcardinfo["personal_phone_nr"] = $data["phone_home"];
	$bcardinfo["personal_email"] = $data["home_email"];
	$bcardinfo["personal_mobile_nr"] = $data["phone_cell"];
	$bcardinfo["opt_pager_nr"] = $data["phone_pager"];
	$bcardinfo["memo"] = $data["memo"];
	$this->save_bcard($bcardinfo);
	unlink($filename);
}

?>
