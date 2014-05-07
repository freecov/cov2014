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

if (!class_exists("Address_data"))
	die("no class definition found");

if (!$GLOBALS["covide"]->license["has_multivers"])
	return false;

$timestamp = mktime(1, 1, 1, date("m"), date("d"), date("Y"));
if ($timestamp <= $GLOBALS["covide"]->license["multivers_update"])
	return false;

// update the license field
$sql = sprintf("UPDATE license SET multivers_update = %d", $timestamp);
$res = sql_query($sql);
$GLOBALS["covide"]->license["multivers_update"] = $timestamp;
echo "updated addressbook on ".date("d-m-Y", $timestamp);

$db_xml = $this->readMultivers($GLOBALS["covide"]->license["multivers_path"]);

for ($i=0; $i < count($db_xml); $i++) {
	if ($db_xml[$i]->Orgid) {
		$a = $db_xml[$i];
		$q = sprintf("SELECT companyname FROM address WHERE id = %d", $a->Orgid);
		$r = sql_query($q);
		$x = sql_fetch_assoc($r);
		if ($x["companyname"]) {
			$sql = sprintf("UPDATE address SET companyname = '%s', address = '%s', zipcode = '%s', city = '%s', phone_nr = '%s', fax_nr = '%s', email = '%s', debtor_nr = '%s', account_manager = %d, is_company = 1, is_active = 1, is_public = 1 WHERE id = %d;",
				addslashes($a->Orgnaam), addslashes($a->Orgadres), addslashes($a->Orgpostcode), addslashes($a->Orgplaats),
				addslashes($a->Orgtelnr), addslashes($a->Orgfaxnr), addslashes($a->Orgemail), $a->Orgdeb, $this->multiversAccManager($a->Orgvert), addslashes($a->Orgid));
		} else {
			$sql = sprintf("INSERT INTO address (id, companyname, address, zipcode, city, phone_nr, fax_nr, email, debtor_nr, account_manager, is_company, is_active, is_public) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, 1, 1, 1);",
				addslashes($a->Orgid), addslashes($a->Orgnaam), addslashes($a->Orgadres), addslashes($a->Orgpostcode), addslashes($a->Orgplaats),
				addslashes($a->Orgtelnr), addslashes($a->Orgfaxnr), addslashes($a->Orgemail), $a->Orgdeb, $this->multiversAccManager($a->Orgvert));
		}
		$res = sql_query($sql);

		$q = sprintf("SELECT COUNT(id) FROM address_info WHERE address_id = %d", $a->Orgid);
		$r = sql_query($q);
		if (!sql_result($r, 0)) {
			$q = sprintf("INSERT INTO address_info (address_id) VALUES (%d)", $a->Orgid);
			sql_query($q) or $error++;
		}
	}
}
unset($db_xml);
echo("...done!.\n");
?>
