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

/* --- check for migration flag --- */
if (!$GLOBALS["covide"]->license["address_migrated"]) {
	$q = "select id, address_id, multirel from address_businesscards";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$this->updateBcardRelations($row["id"], $row["address_id"], $row["multirel"]);
	}
	$q = "update license set address_migrated = 1";
	sql_query($q);
}
/* --- end of address migration --- */

/* get address account manager info and add the current user id */
$accmanager_arr = explode(",", $user->permissions["addressaccountmanage"]);
if (!$accmanager_arr[0])
	unset($accmanager_arr);
$accmanager_arr[] = $_SESSION["user_id"];
if ($options["addresstype"] == "users" || $options["addresstype"] == "private") {
	$t = "_private";
} elseif ($options["addresstype"] == "overig") {
	$t = "_other";
} else {
	$t = "";
}

$addresstype = $options["addresstype"];
/* sync table identifier */
if ($GLOBALS["covide"]->license["has_funambol"]) {
	switch ($addresstype) {
		case "users" :
		case "private" :
			$sync_identifier = "address_private";
			break;
		case "bcards" :
			$sync_identifier = "address_businesscards";
			break;
		case "overig" :
			$sync_identifier = "address_other";
			break;
		default :
			$sync_identifier = "address";
			break;
	}

	/* re-check address sync manager permissions */
	if ($options["funambol_user"]) {
		if ($options["funambol_user"] != $_SESSION["user_id"]) {
			$user_data = new User_data();
			$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
			$accmanager = explode(",", $user_info["addresssyncmanage"]);
			if (!in_array($options["funambol_user"], $accmanager))
				unset($options["funambol_user"]);
		}
	}
	if (!$options["funambol_user"])
		$options["funambol_user"] = $_SESSION["user_id"];

	/* build list with items the user is already syncing. We use this for the bullet */
	if ($GLOBALS["covide"]->license["has_funambol"]) {
		$sql = sprintf("SELECT * FROM funambol_address_sync WHERE user_id = %d AND address_table = '%s'", $options["funambol_user"], $sync_identifier);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$_sync[$row["address_id"]] = $row["id"];
		}
	} else {
		$sql = sprintf("SELECT * FROM address_sync_records WHERE user_id = %d AND address_table = '%s'", $options["funambol_user"], $sync_identifier);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$_sync[$row["address_id"]] = $row["id"];
		}
	}
}

/* begin classification part */
if ($_REQUEST["classifications"]) {
	$options["cla"]["classifications"] = $_REQUEST["classifications"];
	$options["cla"]["selectiontype"]   = $_REQUEST["selectiontype"];
}
if ($options["classifications"]) {
	$options["cla"]["classifications"] = $options["classifications"];
	$options["cla"]["selectiontype"]   = $options["selectiontype"];
}
/* detect classification search */
if (is_array($options["cla"]["classifications"]))
	$_classification_search = 1;

/* detect strict adress mode */
if ($GLOBALS["covide"]->license["address_strict_permissions"]) {
	/* get classification the current user has permissions */
	$classification_data = new Classification_data();

	/* get r/w permissions for later use */
	$cla_rw = $classification_data->getClassificationByAccess(1);

	$cla = explode("|", $options["cla"]["classifications"]["positive"]);
	$cla_extra = $classification_data->getClassificationByAccess();
	foreach ($cla_extra as $c) {
		$cla[]=$c;
	}
	foreach ($cla as $k=>$v) {
		if (!$v)
			unset($cla[$k]);
	}
	$cla = array_unique($cla);
	if ($options["cla"]["selectiontype"] != "and") {
		$options["cla"]["classifications"]["positive"] = implode("|", $cla);
		if (!$options["cla"]["selectiontype"])
			$options["cla"]["selectiontype"] = "OR";
	}
}


if (is_array($options["cla"]["classifications"])) {
	$sq = " ((";

	$regex_syntax = sql_syntax("regex");
	if ($options["addresstype"]=="bcards") {
		$regex_field = "address_businesscards.classification";
	} else {
		$regex_field = "address_info.classification";
	}

	if ($options["cla"]["selectiontype"]=="and") {
		$op = "and";
		$cla_positive_sq = "1=1";
	} else {
		$op = "or";
		$cla_positive_sq = "1=0";
	}
	$cla_positive = explode("|", $options["cla"]["classifications"]["positive"]);
	$cla_negative = explode("|", $options["cla"]["classifications"]["negative"]);

	$cla_negative_sq = "1=0";

	foreach ($cla_positive as $k=>$v) {
		if ($v) {
			$cla_positive_sq.= " $op ($regex_field $regex_syntax '(^|\\\\|)". $v ."(\\\\||$)' $t) ";
		}
	}
	$sq.= "(".$cla_positive_sq.")";

	foreach ($cla_negative as $k=>$v) {
		if ($v) {
			$cla_negative_sq.= " OR ($regex_field $regex_syntax '(^|\\\\|)". $v ."(\\\\||$)' $t) ";
		}
	}
	$sq.= " AND NOT (".$cla_negative_sq."))";

	if ($GLOBALS["covide"]->license["address_strict_permissions"] && !$_classification_search) {
		$regex_field_ad = "address_info.classification";
		foreach ($cla_positive as $k=>$v) {
			if ($v) {
				$cla_positive_sq_ad.= " $op ($regex_field_ad $regex_syntax '(^|\\\\|)". $v ."(\\\\||$)' $t) ";
			}
		}
		$sq.= sprintf(" OR account_manager IN (%s) ", implode(",", $accmanager_arr));

		if ($options["addresstype"] == "bcards") {
			$sq.= " OR address_businesscards.id IN (
				SELECT bcard_id FROM address_businesscards_info WHERE address_id IN (
					SELECT address_id from address_info WHERE (1=0 ".$cla_positive_sq_ad."))
					OR account_manager IN (".implode(",", $accmanager_arr).")) ";
		}
	}
	$sq.= " ) ";

} else {
	/* do nothing, successfull */
	$sq = "1=1";
}
/* end classification part */

/* export of businesscards */
if ($options["bcard_export"])
	$sq .= " AND address.is_active = 1";

/* index module */
if ($options["address_id"]) {
	$sq .= sprintf(" AND address_id IN (%s)", $options["address_id"]);
}

/* some basic query start */
if ($addresstype == "users" || $addresstype == "private" || $addresstype == "overig") {
	/* funambol addon */
	if ($addresstype == "private" && $options["funambol_user"])
		$fb = " OR address_private.user_id = ".(int)$options["funambol_user"];
	if ($options["privuseredit"]) {
		$query_count = "SELECT count(address$t.id) FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.is_public != 1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
		$query_zoek = "SELECT address$t.*".($addresstype == "users" ? ",users.username" : "")." FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.is_public != 1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
		$query_csv = "SELECT address$t.*".($addresstype == "users" ? ",users.username" : "")." FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.is_public != 1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
	} else {
		$query_count = "SELECT count(address$t.id) FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
		$query_zoek = "SELECT address$t.*".($addresstype == "users" ? ",users.username" : "")." FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
		$query_csv = "SELECT address$t.*".($addresstype == "users" ? ",users.username" : "")." FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".(int)$_SESSION["user_id"]." $fb) ";
	}
} elseif ($addresstype != "bcards") {
	/* main address list */
	$query_count = "SELECT count(address.id) FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".(int)$_SESSION["user_id"].") AND ($sq) ";
	$query_zoek = "SELECT address.*, address_info.classification, address_info.provision_perc FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".(int)$_SESSION["user_id"].") AND ($sq) ";
	$query_csv = "SELECT address.*, address_info.classification, address_info.provision_perc  FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".(int)$_SESSION["user_id"].") AND ($sq) ";
} else {
	/* business cards */
	$bjoin = " LEFT JOIN address ON address.id = address_businesscards.address_id ";

	$query_count = "SELECT count(address_businesscards.id) FROM address_businesscards $bjoin WHERE ($sq) ";
	$query_zoek = "SELECT address_businesscards.multirel, address_businesscards.id,address_businesscards.alternative_name,address_businesscards.surname,address_businesscards.infix,address_businesscards.givenname,address_businesscards.surname,address_businesscards.business_mobile_nr AS mobile_nr,address_businesscards.business_phone_nr AS phone_nr,address_businesscards.business_email AS email, address_businesscards.personal_email AS personal_email, address_businesscards.address_id as address_id, address.companyname as companyname FROM address_businesscards $bjoin WHERE ($sq) ";
	$query_csv = "SELECT address_businesscards.multirel, address_businesscards.* FROM address_businesscards $bjoin WHERE ($sq) ";
}

$like_syntax = sql_syntax("like");
/* letter selection */
if ($options["l"]) {
	if ($options["addresstype"] == "bcards") {
		$query .= "AND (address_businesscards.surname $like_syntax '".$options["l"]."%')";
	} elseif ($options["addresstype"] == "users" || $options["addresstype"] == "private") {
		$query .= "AND (address_private.surname $like_syntax '".$options["l"]."%' OR address_private.givenname $like_syntax '".$options["l"]."%')";
	} else {
		$query .= "AND (address.surname $like_syntax '".$options["l"]."%' OR address.companyname $like_syntax '".$options["l"]."%')";
	}
}
/* Search keys */

// Split intelligent, keep single quoted keywords together, so people can search for 'tom a'
// Copied from http://us3.php.net/fgetcsv
$expr="/ (?=(?:[^']*'[^']*')*(?![^']*'))/";
$results=preg_split($expr,stripslashes($options["search"]));
$zoekwoorden = preg_replace("/^'(.*)'$/","$1",$results);
if ($zoekwoorden[0] == true) {
	if ($options["addresstype"] != "bcards") {
		foreach ($zoekwoorden as $zw) {

			if($options["specified"] == 0) {
				$zw = addslashes($zw);
				$query .= "AND (";
				$query .= "(address$t.givenname $like_syntax '%$zw%') OR ";
				$query .= "(address$t.surname $like_syntax '%$zw%') OR ";
				$query .= "(address$t.address $like_syntax '%$zw%') OR ";
				$query .= "(address$t.address2 $like_syntax '%$zw%') OR ";
				$query .= "(address$t.zipcode $like_syntax '%$zw%') OR ";
				$query .= "(address$t.pobox $like_syntax '%$zw%') OR ";
				$query .= "(address$t.pobox_zipcode $like_syntax '%$zw%') OR ";
				$query .= "(address$t.city $like_syntax '%$zw%') OR ";
				$query .= "(address$t.country $like_syntax '%$zw%') OR ";
				$query .= "(address$t.phone_nr $like_syntax '%$zw%') OR ";
				$query .= "(address$t.mobile_nr $like_syntax '%$zw%') OR ";
				$query .= "(address$t.fax_nr $like_syntax '%$zw%') OR ";
				if (!$t) {
					$query .= "(address$t.debtor_nr $like_syntax '%$zw%') OR ";
					$query .= "(address_info.comment $like_syntax '%$zw%') OR ";
					$query .= "(address_info.warning $like_syntax '%$zw%') OR ";
					$query .= "(address$t.companyname $like_syntax '%$zw%') OR ";
					$query .= "(address$t.contact_person $like_syntax '%$zw%') OR ";
					$query .= "(address$t.tav $like_syntax '%$zw%') OR ";
				}
				$query .= "(address$t.email $like_syntax '%$zw%') ";
			} else { 
				$query .= "AND (";
					if($options["specified"] == 1) { $query .= "(address$t.companyname $like_syntax '%$zw%')"; }
					if($options["specified"] == 2) { $query .= "(address$t.tav $like_syntax '%$zw%')"; }
					if($options["specified"] == 3) { $query .= "(address$t.zipcode $like_syntax '%$zw%')"; }
					if($options["specified"] == 4) { $query .= "(address$t.city $like_syntax '%$zw%')"; }
					if($options["specified"] == 5) { $query .= "(address$t.country $like_syntax '%$zw%')"; }
			}
			$query .= ") ";
		}
	} else {
		foreach ($zoekwoorden as $zw) {
			$zw = addslashes($zw);
			$query .= "AND (";
			$query .= "(address_businesscards.alternative_name $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.givenname $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.surname $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.personal_email $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.personal_phone_nr $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.personal_mobile_nr $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.business_phone_nr $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.business_mobile_nr $like_syntax '%$zw%') OR ";
			$query .= "(address_businesscards.business_email $like_syntax '%$zw%') ";
			$query .= ") ";
		}
	}
}
/* Private addresses only */
if ($options["addresstype"] == "private") { 
	if (!$options["privuseredit"]) {
		if ($user->checkPermission("xs_addressmanage")) {
			$q = "SELECT address_id FROM users";
			$r = sql_query($q);
			$exc = Array(0);
			while ($e = sql_fetch_assoc($r)) {
				if ($e["address_id"]) { $exc[] = $e["address_id"]; }
			}
			$exclude = implode(",",array_unique($exc));
			if ($options["funambol_user"] != $_SESSION["user_id"] && $GLOBALS["covide"]->license["has_funambol"])
				$query .= " AND (address$t.user_id=".(int)$options["funambol_user"].") AND (sync_added > 0 OR address_private.is_public = 1) AND id NOT IN ($exclude) ";
			else
				$query .= " AND (address$t.user_id=".(int)$_SESSION["user_id"].") AND id NOT IN ($exclude) ";
		} else {
			if ($options["funambol_user"] != $_SESSION["user_id"] && $GLOBALS["covide"]->license["has_funambol"])
				$query .= " AND (address$t.user_id=".(int)$options["funambol_user"].") AND (sync_added > 0 OR address_private.is_public = 1) ";
			else
				$query .= " AND (address$t.user_id=".(int)$_SESSION["user_id"].") ";
		}
	} else {
		$userinfo = $user->getUserDetailsById($options["privuseredituser"]);
			$q = "SELECT address_id FROM users";
			$r = sql_query($q);
			$exc = Array(0);
			while ($e = sql_fetch_assoc($r)) {
				if ($e["address_id"] != $userinfo["address_id"]) {
					if ($e["address_id"]) { $exc[] = $e["address_id"]; }
				}
			}
			$exclude = implode(",",array_unique($exc));

			$query .= " AND id NOT IN ($exclude) ";

	}
} else if ($options["addresstype"] != "bcards") {
	$query .= " AND (address$t.is_public=1 OR ((address$t.is_public=0) AND (address$t.user_id=".(int)$_SESSION["user_id"]."))) ";
}

/* Company addresses only */
if ($options["addresstype"] == "relations" || $options["addresstype"] == "nonactive" || preg_match("/^zoekRel(.*)$/", $options["action"])) {
	$query .= " AND (address$t.is_company=1) ";
	/* subgroup ? */
	if ($options["sub"] == "klanten") {
		$query .= " AND (address$t.is_customer=1) ";
	} elseif ($options["sub"] == "leveranciers") {
		$query .= " AND (address$t.is_supplier=1) ";
	}
}
/* Users only */
if ($options["addresstype"] == "users") {
	if (!$options["sub"]) {
		$options["sub"] = "actief";
	}
	if ($options["sub"] == "actief") {
		$query .= " AND (address_private.is_public=1 AND users.address_id=address_private.id AND users.address_id IS NOT NULL AND users.is_active=1) ";
	} else {
		$query .= " AND (address_private.is_public=1 AND users.address_id=address_private.id AND users.address_id IS NOT NULL AND users.is_active=0) ";
	}
}
if (($options["addresstype"] != "bcards") && ($options["addresstype"] != "private") && ($options["addresstype"] != "users") && ($options["addresstype"] != "overig")) {
	if ($options["addresstype"] == "nonactive") {
		$query .= " AND (address$t.is_active=0 OR address$t.is_active IS NULL) ";
	} else {
		$query .= " AND address$t.is_active=1 ";
	}
	if ($classificatie[0] == true || $classificatie_niet[0] == true)
		$query .= " AND address$t.is_company=1 ";
}
if ($options["addresstype"] == "overig") {
	if (!$options["sub"]) {
		$options["sub"] = "kantoor";
	}
	if ($options["sub"] == "kantoor") {
		$query .= " AND address_other.is_companylocation=1 ";
	} elseif ($options["sub"] == "arbo") {
		$query .= " AND arbo_kantoor=1 ";
	} else {
		$query .= " AND 1=0 ";
	}
}
$query_count.=$query;
$query_zoek.=$query;
$query_csv.=$query;
$result = sql_query($query_count);

$totaal = sql_fetch_row($result);
if (($options["top"]+$GLOBALS["covide"]->pagesize)>$totaal[0]) { $bottom=$totaal[0]; } else { $bottom = $options["top"]+$GLOBALS["covide"]->pagesize; }

$addressinfo["total_count"] = $totaal[0];
$addressinfo["top"]         = $options["top"];
$addressinfo["bottom"]      = $bottom;
$addressinfo["query_count"] = $query_count;


if ($options["addresstype"] != "bcards") {
	$print_query = $query_zoek . "ORDER BY address_businesscards.surname,address_businesscards.givenname";
} else {
	$print_query = $query_zoek . "ORDER BY address$t.companyname,address$t.surname";
}

$print_csv = $query_zoek . "ORDER BY address$t.companyname,address$t.surname";
if ($options["addresstype"] == "bcards") {
	if (!$options["sort"]) {
		$query_zoek .= "ORDER BY address_businesscards.surname,address_businesscards.givenname";
	} else {
		$query_zoek .= "ORDER BY ".sql_filter_col($options["sort"]);
	}
} elseif ($options["addresstype"] == "users" || $options["addresstype"] == "private") {
	if (!$options["sort"]) {
		$query_zoek .= "ORDER BY UPPER(surname),UPPER(givenname)";
	} else {
		$query_zoek .= "ORDER BY ".sql_filter_col($options["sort"]);
	}
} else {
	if (!$options["sort"]) {
		if ($options["addresstype"] == "private")
			$query_zoek .= "ORDER BY sync_added DESC address$t.companyname,address$t.surname";
		elseif ($options["addresstype"] == "relations")
			$query_zoek .= "ORDER BY concat(COALESCE(address.companyname, ''), COALESCE(address.contact_surname, ''))";
		else
			$query_zoek .= "ORDER BY address$t.companyname,address$t.surname";
	} else {
		$query_zoek .= "ORDER BY ".sql_filter_col($options["sort"]);
	}
}
$addressinfo["query_zoek"] = $query_zoek;
$addressinfo["query_csv"]  = $query_csv;
if (!$options["nolimit"]) {
	$result = sql_query($query_zoek, "", $options["top"], $GLOBALS["covide"]->pagesize);
} else {
	if ($options["max_hits"]) {
		$result = sql_query($query_zoek, "", 0, $options["max_hits"]);
	} else {
		$result = sql_query($query_zoek);
	}
}
if (PEAR::isError($result)) {
	die($result->getUserInfo());
}

/* build the actual data array */
$i = 0;
while ($row = sql_fetch_assoc($result)) {
	$i = $row["id"];
	$addressinfo["address"][$i] = $row;
	if ($GLOBALS["covide"]->license["has_funambol"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		if (isset($_sync[$row["id"]])) {
			$addressinfo["address"][$i]["sync_yes"] = 1;
			$addressinfo["address"][$i]["sync_no"]  = 0;
		} else {
			$addressinfo["address"][$i]["sync_yes"] = 0;
			$addressinfo["address"][$i]["sync_no"]  = 1;
		}
	}
	if ($options["addresstype"] == "relations" || $options["addresstype"] == "nonactive" || $options["addresstype"] == "overig") {
		if ($options["addresstype"] != "overig") {
			$addressinfo["address"][$i]["mail_id"] = $addressinfo["address"][$i]["id"];
		} else {
			$addressinfo["address"][$i]["mail_id"] = 0;
		}
		if ($user->checkPermission("xs_addressmanage")) {
			$addressinfo["address"][$i]["addressmanage"] = 1;
			$addressinfo["address"][$i]["addressacc"] = 1;
		} else {
			$addressinfo["address"][$i]["addressmanage"] = 0;
			$addressinfo["address"][$i]["addressacc"]    = 1;

			if ($row["account_manager"] && in_array($row["account_manager"], $accmanager_arr)) {
				$addressinfo["address"][$i]["addressmanage"] = 1;
			} elseif ($GLOBALS["covide"]->license["address_strict_permissions"]) {
				if (in_array($row["account_manager"], $accmanager_arr)) {
					$addressinfo["address"][$i]["addressmanage"] = 1;
				} else {
					$cla_record = explode("|", $row["classification"]);
					$int = array_intersect($cla_record, $cla_rw);
					if (count($int) > 0) {
						$addressinfo["address"][$i]["addressmanage"] = 1;
					}
				}
			} else {
				$addressinfo["address"][$i]["addressacc"] = 0;
				$addressinfo["address"][$i]["noaccess"] = 1;
			}
		}
		$addressinfo["address"][$i]["letterinfo"] = $this->generate_letterinfo(array(
			"contact_initials"     => $addressinfo["address"][$i]["contact_initials"],
			"contact_letterhead"   => $addressinfo["address"][$i]["contact_letterhead"],
			"contact_commencement" => $addressinfo["address"][$i]["contact_commencement"],
			"contact_givenname"    => $addressinfo["address"][$i]["contact_givenname"],
			"contact_infix"        => $addressinfo["address"][$i]["contact_infix"],
			"contact_surname"      => $addressinfo["address"][$i]["contact_surname"],
			"title"                => $addressinfo["address"][$i]["contact_title"]
		));
		$addressinfo["address"][$i]["tav"] = $addressinfo["address"][$i]["letterinfo"]["tav"];
		$addressinfo["address"][$i]["contact_person"] = $addressinfo["address"][$i]["letterinfo"]["contact_person"];

		$addressinfo["address"][$i]["fullname"] = $row["contact_givenname"]." ".$row["contact_infix"]." ".$row["contact_surname"];
		$addressinfo["address"][$i]["fullname"] = preg_replace("/\W{2,}/si", " ", $addressinfo["address"][$i]["fullname"]);
	} else {
		/* bcards or private or users */
		$addressinfo["address"][$i]["mail_id"] = sprintf("%d", $addressinfo["address"][$i]["address_id"]);
		$addressinfo["address"][$i]["fullname"] = $row["givenname"]." ".$row["infix"]." ".$row["surname"];
		$addressinfo["address"][$i]["fullname"] = preg_replace("/\W{2,}/si", " ", $addressinfo["address"][$i]["fullname"]);
		if ($options["addresstype"] == "private") {
			if ($row["sync_added"]) {
				$addressinfo["address"][$i]["sync_h"] = date("d-m-Y H:i", $row["sync_added"]);
				$addressinfo["address"][$i]["sync_action"] = 1;
				/* allow user to view this record */
				if ($_REQUEST["funambol_user"])
					$addressinfo["address"][$i]["addressacc"] = 1;
			}
			if ($addressinfo["address"][$i]["user_id"] == $_SESSION["user_id"]) {
				$addressinfo["address"][$i]["addressacc"]    = 1;
				$addressinfo["address"][$i]["addressmanage"] = 1;
			}
		} elseif ($options["addresstype"] == "users") {
			if ($user->checkPermission("xs_hrmmanage") || $user->checkPermission("xs_usermanage") || $user->checkPermission("xs_addressmanage")) {
				$addressinfo["address"][$i]["addressacc"]    = 1;
				$addressinfo["address"][$i]["addressmanage"] = 1;
			}
		} else {
			/* bcards */
			if ($user->checkPermission("xs_usermanage") || $user->checkPermission("xs_addressmanage")) {
				$addressinfo["address"][$i]["addressacc"]    = 1;
				$addressinfo["address"][$i]["addressmanage"] = 1;
			}
			$addressinfo["address"][$i]["companyname"] = array();
			$ids = array();
			$ids = explode(",", $row["multirel"]);
			$ids[] = $row["address_id"];
			$ids = array_unique($ids);
			foreach ($ids as $v) {
				if ($v) {
					$name = trim($this->getAddressNameByID($v));
					if ($name)
						$addressinfo["address"][$i]["companyname"][] = $name;
				}
			}
			$addressinfo["address"][$i]["companyname"] = implode(", ", $addressinfo["address"][$i]["companyname"]);
		}
	}
	#if ($GLOBALS["covide"]->license["address_strict_permissions"]) {
	#	$addressinfo["address"][$i]["addressacc"]    = 1;
	#	$addressinfo["address"][$i]["addressmanage"] = 1;
	#}
	if (!$addressinfo["address"][$i]["companyname"]) { $addressinfo["address"][$i]["companyname"] = gettext("none"); }
	if (!$addressinfo["address"][$i]["fullname"]) { $addressinfo["address"][$i]["fullname"] = gettext("none"); }
	/* fix website */
	if (strpos($addressinfo["address"][$i]["website"], "http://") !== 0) {
		$addressinfo["address"][$i]["website"] = "http://".$addressinfo["address"][$i]["website"];
	}

	if ($options["addresstype"] == "private") {
		/* try a conversion of some fields */
		if (!trim($row["phone_nr"]))
			$row["phone_nr"] = $row["business_phone_nr"];

		if (!trim($row["mobile_nr"]))
			$row["mobile_nr"] = $row["business_mobile_nr"];

		if (!trim($addressinfo["address"][$i]["zipcode"]))
			$addressinfo["address"][$i]["zipcode"] = $addressinfo["address"][$i]["business_zipcode"];

		if (!trim($addressinfo["address"][$i]["city"]))
			$addressinfo["address"][$i]["city"] = $addressinfo["address"][$i]["business_city"];

		if (!trim($addressinfo["address"][$i]["email"]))
			$addressinfo["address"][$i]["email"] = $addressinfo["address"][$i]["business_email"];
	}
	
	/* fix companyname for persons in relationlist */
	if ($options["addresstype"] == "relations" && $addressinfo["address"][$i]["is_person"]) {
		$companyname_temp = $row["contact_surname"].", ".$row["contact_givenname"]." ".$row["contact_infix"];
		$addressinfo["address"][$i]["companyname"] = preg_replace("/ {2,}/", " ", $companyname_temp);
	}

	$addressinfo["address"][$i]["companyname_html"] = addslashes(htmlspecialchars($addressinfo["address"][$i]["companyname"]));
	$addressinfo["address"][$i]["phone_nr_link"] = $this->show_phonenr($row["phone_nr"]);
	$addressinfo["address"][$i]["mobile_nr_link"] = $this->show_phonenr($row["mobile_nr"]);

	if ($addressinfo["address"][$i]["timestamp_birthday"] == -1)
		$addressinfo["address"][$i]["timestamp_birthday"] = 0;
	if ($addressinfo["address"][$i]["contact_birthday"] == -1)
		$addressinfo["address"][$i]["contact_birthday"] = 0;

	/* reverse copy the is_public flag to a is_private flag */
	$addressinfo["address"][$i]["is_private"] = ($row["is_public"]) ? 0:1;

	/* if added by sync, state should be public */
	if ($row["sync_added"]) {
		$addressinfo["address"][$i]["is_private"] = 0;
		$addressinfo["address"][$i]["is_public"] = 1;
	}
	/* Replace the country code with the human readable name */
	$data = new Address_data();
	$data_arr = $data->listCountries();
	$addressinfo["address"][$i]["country"] = $data_arr[$addressinfo["address"][$i]["country"]];

	$i++;
}

/* make sure addressinfo[address] is always an array */
if (!is_array($addressinfo["address"])) {
	$addressinfo["address"] = array();
}

?>
