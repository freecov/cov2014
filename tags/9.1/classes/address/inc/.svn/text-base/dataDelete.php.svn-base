<?php
/**
 * Covide Groupware-CRM Addressbook module. delete address
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
if ($GLOBALS["covide"]->license["has_funambol"] && !$skip_funambol) {
	$funambol_data = new Funambol_data();
	$funambol_data->removeRecord("address", $address_id);
}

if ($addresstype == "private") {
	/* check if user record is linked */

	$sql = sprintf("SELECT COUNT(*) FROM users WHERE address_id=%d", $address_id);
	$res = sql_query($sql);
	$linkcount = sql_result($res, 0);
	if ($linkcount) {
		//die("user record, we cannot delete this entry !");
		echo "not deleting record with address id $address_id";
	}
	$sql = sprintf("DELETE FROM address_private WHERE id=%d", $address_id);
	$res = sql_query($sql);
}
if (!$skip_funambol) {
	$output = new Layout_output();
	$output->start_javascript();
		$output->addCode("
			if (parent) {
				parent.document.getElementById('deze').submit();
				var t = setTimeout('closepopup();', 100);
			}
		");
	$output->end_javascript();
	$output->exit_buffer();
}
?>
