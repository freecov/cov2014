<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);

$address = new Address_data();

$query_count = "SELECT count(products.id) FROM products,address WHERE address.id = supplierid ";
$query_zoek = "SELECT products.* FROM products,address WHERE address.id = supplierid ";

if ($options["supplierid"] > 0)
	$query .= "AND products.supplierid = " . $options["supplierid"]. " ";

if ($options["sub"] == "active")
	$query .= "AND replacement = 0 ";
else if ($options["sub"] == "inactive")
	$query .= "AND replacement > 0 ";
	
$like_syntax = sql_syntax("like");
/* letter selection */
if ($options["l"]) 
	$query .= "AND (name $like_syntax '".$options["l"]."%')";

/* Search keys */
$zoekwoorden = split(" ",$options["search"]);
if ($zoekwoorden[0] == true) {
	foreach ($zoekwoorden as $zw) {
	$query .= "AND (";
	$query .= "(companyname $like_syntax '%$zw%') OR ";
	$query .= "(name $like_syntax '%$zw%') OR ";
	$query .= "(label $like_syntax '%$zw%')";
	$query .= ")";
	}
}
$query_count.=$query;
$query_zoek.=$query;
$result = sql_query($query_count);

$totaal = sql_fetch_row($result);
if (($options["top"]+$GLOBALS["covide"]->pagesize)>$totaal[0]) { $bottom=$totaal[0]; } else { $bottom = $options["top"]+$GLOBALS["covide"]->pagesize; }
$productinfo["total_count"] = $totaal[0];
$productinfo["top"]         = $options["top"];
$productinfo["bottom"]      = $bottom;
$productinfo["query_count"] = $query_count;

$print_query = $query_zoek . "ORDER BY name asc, prod_year desc, content desc";

if (!$options["sort"]) {
	$query_zoek .= "ORDER BY name asc, prod_year desc, content desc";
} else {
	$query_zoek .= "ORDER BY ".sql_filter_col($options["sort"]);
}

$produktinfo["query_zoek"] = $query_zoek;
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
	$productinfo["product"][$i] = $row;
	$productinfo["product"][$i]["supplier_name"] = $address->getAddressNameByID($row[supplierid]);
	$i++;
}


/* make sure it is always an array */
if (!is_array($productinfo["product"])) {
	$productinfo["product"] = array();
}

?>
