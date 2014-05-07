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
    $query_count = "SELECT count(products_prices.id) FROM products_prices,products 
                    WHERE products_prices.productid=products.id ";
    $query_zoek = "SELECT products_prices.* FROM products_prices,products
                   WHERE products_prices.productid=products.id ";
}
else
{
    $query_count = "SELECT count(products_prices.id) FROM products_prices WHERE 1=1 ";
    $query_zoek = "SELECT products_prices.* FROM products_prices WHERE 1=1 ";
}

if ($options["customerid"] > 0)
	$query .= "AND products_prices.customerid = " . $options["customerid"]. " ";

if ($options["state"] == "")
    $options["state"] = "valid";
if ($options["state"] == "valid")
       $query .= "AND start_date < ".date("U")." AND end_date > ".date("U")." ";
else if ($options["state"] == "expired")
       $query .= "AND end_date < ".date("U")." ";


$like_syntax = sql_syntax("like");
if ($zoekwoorden[0] == true) 
{
	foreach ($zoekwoorden as $zw) 
        {
            $query .= "AND (";
            $query .= "(remark $like_syntax '%$zw%')";
            $query .= ")";
	}
}

$query_count.=$query;
$query_zoek.=$query;
$result = sql_query($query_count);

$totaal = sql_fetch_row($result);
if (($options["top"]+$GLOBALS["covide"]->pagesize)>$totaal[0]) { $bottom=$totaal[0]; } else { $bottom = $options["top"]+$GLOBALS["covide"]->pagesize; }
$priceinfo["total_count"] = $totaal[0];
$priceinfo["top"]         = $options["top"];
$priceinfo["bottom"]      = $bottom;
$priceinfo["query_count"] = $query_count;

$print_query = $query_zoek . "ORDER BY end_date desc, id desc";

if (!$options["sort"]) {
	$query_zoek .= "ORDER BY end_date desc, id desc";
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
$product_data = new Product_data();
while ($result->fetchInto($row)) {
	$i = $row["id"];
	$priceinfo["price"][$i] = $row;
	$priceinfo["price"][$i]["human_customer"] = $address->getAddressNameByID($row[customerid]);
	if ($row[start_date] == 0)
		$priceinfo["price"][$i]["human_start_date"] = "";
	else
	        $priceinfo["price"][$i]["human_start_date"] = date("d-m-Y", $row[start_date]);
	$priceinfo["price"][$i]["human_end_date"] = date("d-m-Y", $row[end_date]);
	$temp_ar = $product_data->getProductByID($row["productid"]);
	foreach ($temp_ar as $k=>$v)
        	$priceinfo["price"][$i]["productinfo_".$k] = $v;
	$priceinfo["price"][$i]["producer"] = $address->getAddressNameByID($product_data->getProducerFromProduct($row["productid"]));
}

/* make sure it is always an array */
if (!is_array($priceinfo["productid"])) {
	$priceinfo["productid"] = array();
}

?>
