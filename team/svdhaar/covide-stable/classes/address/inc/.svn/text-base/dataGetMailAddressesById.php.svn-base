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
	$return["emails"] = array();
	/* fetch email addresses from relationcard */
	$sql = sprintf("SELECT email FROM address WHERE id = %d", $address_id);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	if (trim($row["email"])) { $return["emails"][] = $row["email"]; }
	/* now fetch all emails on businesscards */
	$user = new User_data();
	$userinfo = $user->getUserDetailsById($_SESSION["user_id"]);
	$user_classification = $userinfo["classificationrestriction"];
	$regex_syntax = sql_syntax("regex");
	$sql = sprintf("SELECT * FROM address_businesscards WHERE address_id = %1\$d OR multirel $regex_syntax '(^|\\\\,)%1\$d(\\\\,|$)'", $address_id);
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		$classification_flag = 0;
		$bc_classification = explode("|",$row["classification"]);
		foreach ($bc_classification as $key => $value ) {
			if (($value == $user_classification || $userinfo["xs_usermanage"] || $userinfo["xs_addressmanage"]) && $classification_flag == 0) {
				$classification_flag = 1;
			}
		}
		if ($classification_flag == 1) {
			if (trim($row["business_email"])) {
				if (!in_array($row["business_email"], $return["emails"])) {
					$return["emails"][] = $row["business_email"];
				}
			}
			if (trim($row["personal_email"])) {
				if (!in_array($row["personal_email"], $return["emails"])) {
					$return["emails"][] = $row["personal_email"];
				}
			}
		}
	}
?>
