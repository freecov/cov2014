<?php
if (!class_exists("Address_data")) {
	die("no class definition found");
}
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
$accmanager_arr = explode(",", $user->permissions["addressaccountmanage"]);

if ($options["addresstype"] == "users" || $options["addresstype"] == "private") {
	$t = "_private";
} elseif ($options["addresstype"] == "overig") {
	$t = "_other";
} else {
	$t = "";
}

$addresstype = $options["addresstype"];
/* sync4j table identifier */
if ($GLOBALS["covide"]->license["has_sync4j"]) {
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
	/* build list with items the user is already syncing. We use this for the bullet */
	$sql = sprintf("SELECT * FROM address_sync_records WHERE user_id = %d AND address_table = '%s'", $_SESSION["user_id"], $sync_identifier);
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		$_sync[$row["address_id"]] = $row["id"];
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

if (strlen($_REQUEST["freeclasssearch"])) {
	$addr = new Address_data();
	$classes = $addr->checkcla_array($_REQUEST["freeclasssearch"]);
	if (strlen($classes)>0) {
		$options["cla"]["classifications"]["positive"] = $classes;	
		$options["cla"]["selectiontype"]               = "or";
	}
}

if (is_array($options["cla"]["classifications"])) {
	$sq = "";
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
	$sq.= " AND NOT (".$cla_negative_sq.")";

} else {
	/* do nothing, successfull */
	$sq = "1=1";
}
/* end classification part */

/* some basic query start */
if ($addresstype == "users" || $addresstype == "private" || $addresstype == "overig") {
	$query_count = "SELECT count(address$t.id) FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".$_SESSION["user_id"].") ";
	$query_zoek = "SELECT address$t.* FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".$_SESSION["user_id"].") ";
	$query_csv = "SELECT address$t.* FROM address$t ". ($addresstype == "users" ? ",users" : ""). " WHERE (address$t.is_public=1 OR address$t.user_id=".$_SESSION["user_id"].") ";
} elseif ($addresstype != "bcards") {
	/* main address list */
	$query_count = "SELECT count(address.id) FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".$_SESSION["user_id"].") AND ($sq) ";
	$query_zoek = "SELECT address.*, address_info.classification, address_info.provision_perc FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".$_SESSION["user_id"].") AND ($sq) ";
	$query_csv = "SELECT address.*, address_info.classification, address_info.provision_perc  FROM address INNER JOIN address_info ON address_info.address_id=address.id WHERE (address.is_public=1 OR address.user_id=".$_SESSION["user_id"].") AND ($sq) ";
} else {
	/* business cards */
	$bjoin = " LEFT JOIN address ON address.id = address_businesscards.address_id ";

	$query_count = "SELECT count(address_businesscards.id) FROM address_businesscards $bjoin WHERE ($sq) ";
	$query_zoek = "SELECT address_businesscards.id,address_businesscards.surname,address_businesscards.infix,address_businesscards.givenname,address_businesscards.surname,address_businesscards.business_mobile_nr AS mobile_nr,address_businesscards.business_phone_nr AS phone_nr,address_businesscards.business_email AS email, address_businesscards.personal_email AS personal_email, address_businesscards.address_id as address_id, address.companyname as companyname FROM address_businesscards $bjoin WHERE ($sq) ";
	$query_csv = "SELECT address_businesscards.* FROM address_businesscards $bjoin WHERE ($sq) ";
}

$like_syntax = sql_syntax("like");
/* letter selection */
if ($options["l"]) {
	if ($options["addresstype"] == "bcards") {
		$query .= "AND (address_businesscards.surname $like_syntax '".$options["l"]."%')";
	} elseif ($options["addresstype"] == "users") {
		$query .= "AND (address_private.surname $like_syntax '".$options["l"]."%' OR address_private.givenname $like_syntax '".$options["l"]."%')";
	} else {
		$query .= "AND (address.surname $like_syntax '".$options["l"]."%' OR address.companyname $like_syntax '".$options["l"]."%')";
	}
}
/* Search keys */
$zoekwoorden = split(" ",$options["search"]);
if ($zoekwoorden[0] == true) {
	if ($options["addresstype"] != "bcards") {
		foreach ($zoekwoorden as $zw) {
			$query .= "AND (";
			$query .= "(address$t.givenname $like_syntax '%$zw%') OR ";
			$query .= "(address$t.surname $like_syntax '%$zw%') OR ";
			$query .= "(address$t.address $like_syntax '%$zw%') OR ";
			$query .= "(address$t.address2 $like_syntax '%$zw%') OR ";
			$query .= "(address$t.zipcode $like_syntax '%$zw%') OR ";
			$query .= "(address$t.pobox $like_syntax '%$zw%') OR ";
			$query .= "(address$t.pobox_zipcode $like_syntax '%$zw%') OR ";
			$query .= "(address$t.city $like_syntax '%$zw%') OR ";
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
			$query .= ") ";
		}
	} else {
		foreach ($zoekwoorden as $zw) {
			$query .= "AND (";
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
			$query .= " AND (address$t.user_id=".$_SESSION["user_id"].") AND id NOT IN ($exclude) ";
		} else {
			$query .= " AND (address$t.user_id=".$_SESSION["user_id"].") ";
		}
	}
} else if ($options["addresstype"] != "bcards") {
	$query .= " AND (address$t.is_public=1 OR ((address$t.is_public=0) AND (address$t.user_id=".$_SESSION["user_id"]."))) ";
}

/* Company addresses only */
if ($options["addresstype"] == "relations" || $options["addresstype"] == "nonactive" || preg_match("/^zoekRel(.*)$/", $options["action"])) {
	$query .= " AND (address$t.is_company=1) ";
	/* subgroup ? */
	if ($options["sub"] == "klanten") {
		$query .= " AND (address$t.is_customer=1) ";
	} elseif ($options["sub"] == "leveranciers") {
		$query .= " AND (address$t.is_supplier=1) ";
	} elseif ($options["sub"] == "transporteurs") {
		$query .= " AND (address$t.is_transporter=1) ";
	} elseif ($options["sub"] == "contacts") {
		$query .= " AND (address$t.is_contact=1) ";
	}
}
/* Users only */
if ($options["addresstype"] == "users") {
	if (!$options["sub"]) {
		$options["sub"] = "actief";
	}
	if ($options["sub"] == "actief") {
		$query .= " AND (address_private.is_company!=1 AND address_private.is_public=1 AND users.address_id=address_private.id AND users.address_id IS NOT NULL AND users.is_active=1) ";
	} else {
		$query .= " AND (address_private.is_company !=1 AND address_private.is_public=1 AND users.address_id=address_private.id AND users.address_id IS NOT NULL AND users.is_active=0) ";
	}
}
if (($options["addresstype"] != "bcards") && ($options["addresstype"] != "private") && ($options["addresstype"] != "overig")) {
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
		$query_zoek .= "ORDER BY address$t.companyname,address$t.surname";
	} else {
		$query_zoek .= "ORDER BY ".sql_filter_col($options["sort"]);
	}
}
$addressinfo["query_zoek"] = $query_zoek;
$addressinfo["query_csv"]  = $query_csv;
if (!$options["nolimit"]) {
	$result = $GLOBALS["covide"]->db->limitQuery($query_zoek, $options["top"], $GLOBALS["covide"]->pagesize);
} else {
	if ($options["max_hits"]) {
		$result = $GLOBALS["covide"]->db->limitQuery($query_zoek, 0, $options["max_hits"]);
	} else {
		$result = $GLOBALS["covide"]->db->query($query_zoek);
	}
}
if (PEAR::isError($result)) {
	die($result->getUserInfo());
}

/* build the actual data array */
$i = 0;
while ($result->fetchInto($row)) {
	$i = $row["id"];
	$addressinfo["address"][$i] = $row;
	if ($GLOBALS["covide"]->license["has_sync4j"]) {
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
			if (in_array($row["accmanager"], $accmanager_arr)) {
				$addressinfo["address"][$i]["addressacc"] = 1;
			} else {
				$addressinfo["address"][$i]["addressacc"] = 0;
			}
		}
		$addressinfo["address"][$i]["fullname"] = $row["contact_givenname"]." ".$row["contact_infix"]." ".$row["contact_surname"];
		$addressinfo["address"][$i]["fullname"] = preg_replace("/\W{2,}/si", " ", $addressinfo["address"][$i]["fullname"]);
	} else {
		$addressinfo["address"][$i]["mail_id"] = 0;
		$addressinfo["address"][$i]["fullname"] = $row["givenname"]." ".$row["infix"]." ".$row["surname"];
		$addressinfo["address"][$i]["fullname"] = preg_replace("/\W{2,}/si", " ", $addressinfo["address"][$i]["fullname"]);
		if ($options["addresstype"] == "private") {
			if ($addressinfo["address"][$i]["user_id"] == $_SESSION["user_id"]) {
				$addressinfo["address"][$i]["addressacc"]    = 1;
				$addressinfo["address"][$i]["addressmanage"] = 1;
			}
		}
	}
	if (!$addressinfo["address"][$i]["companyname"]) { $addressinfo["address"][$i]["companyname"] = gettext("geen"); }
	if (!$addressinfo["address"][$i]["fullname"]) { $addressinfo["address"][$i]["fullname"] = gettext("geen"); }
	/* fix website */
	if (strpos($addressinfo["address"][$i]["website"], "http://") !== 0) {
		$addressinfo["address"][$i]["website"] = "http://".$addressinfo["address"][$i]["website"];
	}
	$addressinfo["address"][$i]["companyname_html"] = addslashes(htmlspecialchars($row["companyname"]));
	$addressinfo["address"][$i]["phone_nr_link"] = $this->show_phonenr($row["phone_nr"]);
	$addressinfo["address"][$i]["mobile_nr_link"] = $this->show_phonenr($row["mobile_nr"]);
	$i++;
}

/* make sure addressinfo[address] is always an array */
if (!is_array($addressinfo["address"])) {
	$addressinfo["address"] = array();
}

?>
