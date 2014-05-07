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

	// get array with address_id's we should ignore
	// those are inactive and crmforms
	$ignore = array();
	$sql = "SELECT id FROM address where is_active != 1";
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		$ignore[] = $row["id"];
	}
	$filter = "%@%.%";
	$mails = array();
	$q = "
		SELECT
			email,
			business_email,
			personal_email,
			other_email,
			address_id as id,
			multirel
		FROM
			address_businesscards
		WHERE
			address_businesscards.user_id = 0
			AND (
				LOWER(email) LIKE '$filter'
				OR LOWER(business_email) LIKE '$filter'
				OR LOWER(personal_email) like '$filter'
			)";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$row["email"]          = trim(strtolower($row["email"]));
		$row["business_email"] = trim(strtolower($row["business_email"]));
		$row["personal_email"] = trim(strtolower($row["personal_email"]));
		$row["other_email"]    = trim(strtolower($row["other_email"]));
		$address_ids = preg_replace("/^,/", "", preg_replace("/,$/", "", $row["id"].",".$row["multirel"]));
		$address_ids = explode(",", $address_ids);
		$address_ids = array_unique($address_ids);
		foreach ($address_ids as $k=>$v) {
			if (in_array($v, $ignore)) {
				unset($address_ids[$k]);
			}
		}
		if (!count($address_ids)) {
			continue;
		}

		if (preg_match("/.*@.*\..*/s", $row["email"])) {
			if (count($address_ids) > 1) {
				$mails[$row["email"]] = -1;
			} else {
				if ($mails[$row["email"]] && !in_array($mails[$row["email"]], $address_ids)) {
					$mails[$row["email"]] = -1;
				} else {
					$mails[$row["email"]] = $row["id"];
				}
			}
		}
		if (preg_match("/.*@.*\..*/s", $row["business_email"])) {
			if (count($address_ids) >1 ) {
				$mails[$row["business_email"]] = -1;
			} else {
				if ($mails[$row["business_email"]] && !in_array($mails[$row["business_email"]], $address_ids)) {
					$mails[$row["business_email"]] = -1;
				} else {
					$mails[$row["business_email"]] = $row["id"];
				}
			}
		}
		if (preg_match("/.*@.*\..*/s", $row["personal_email"])) {
			if (count($address_ids)>1) {
				$mails[$row["personal_email"]] = -1;
			} else {
				if ($mails[$row["personal_email"]] && !in_array($mails[$row["personal_email"]], $address_ids)) {
					$mails[$row["personal_email"]] = -1;
				} else {
					$mails[$row["personal_email"]] = $row["id"];
				}
			}
		}
		if (preg_match("/.*@.*\..*/s", $row["other_email"])) {
			if (count($address_ids)>1) {
				$mails[$row["other_email"]] = -1;
			} else {
				if ($mails[$row["other_email"]] && !in_array($mails[$row["other_email"]], $address_ids)) {
					$mails[$row["other_email"]] = -1;
				} else {
					$mails[$row["other_email"]] = $row["id"];
				}
			}
		}
	}
	$q = "select email, id from address where is_active = 1 AND lower(email) like '$filter' ";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$row["email"] = trim(strtolower($row["email"]));
		if ($mails[$row["email"]] && $mails[$row["email"]]!=$row["id"]) {
			$mails[$row["email"]] = -1;
		} else {
			$mails[$row["email"]] = $row["id"];
		}
	}
?>
