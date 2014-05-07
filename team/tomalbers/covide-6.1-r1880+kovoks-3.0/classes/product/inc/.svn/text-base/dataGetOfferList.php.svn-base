<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);

$address = new Address_data();

$zoekwoorden = split(" ",$options["search"]);
if ($zoekwoorden[0] == true) 
{
    $query_count = "SELECT count(products_offers.id) FROM products_offers,products_offers_prods,products 
                    WHERE products_offers.id = products_offers_prods.offerid and products_offers_prods.productid=products.id ";
    $query_zoek = "SELECT products_offers.* FROM products_offers,products_offers_prods,products
                   WHERE products_offers.id = products_offers_prods.offerid and products_offers_prods.productid=products.id ";
}
else
{
    $query_count = "SELECT count(products_offers.id) FROM products_offers WHERE 1=1 ";
    $query_zoek = "SELECT products_offers.* FROM products_offers WHERE 1=1 ";
}


if ($options["customerid"] > 0)
	$query .= "AND products_offers.customerid = " . $options["customerid"]. " ";

if ($options["state"] == "")
    $options["state"] = "valid";
if ($options["state"] == "valid")
       $query .= "AND validity > ".date("U")." ";
else if ($options["state"] == "expired")
       $query .= "AND validity < ".date("U")." ";


$like_syntax = sql_syntax("like");
if ($zoekwoorden[0] == true) 
{
	foreach ($zoekwoorden as $zw) 
        {
            $query .= "AND (";
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

$print_query = $query_zoek . "ORDER BY date desc, id desc";

if (!$options["sort"]) {
	$query_zoek .= "ORDER BY date desc, id desc";
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
	$productinfo["product"][$i]["customer_name"] = $address->getAddressNameByID($row[customerid]);
	$productinfo["product"][$i]["date_human"] = date("d-m-Y H:i", $row[date]);
        if ($productinfo["product"][$i]["locked"] == "1")
            $productinfo["product"][$i]["unlocked"] = "0";
        else
            $productinfo["product"][$i]["unlocked"] = "1";
        $data = $this->getProductsFromOffer($i);
        $p = array();
        foreach($data as $prod)
            $p[] = $prod["label"]." (".$prod["offerprice"].")";
        $productinfo["product"][$i]["products"] = implode(", ",$p);
	$i++;
}

/* make sure it is always an array */
if (!is_array($productinfo["product"])) {
	$productinfo["product"] = array();
}

?>
