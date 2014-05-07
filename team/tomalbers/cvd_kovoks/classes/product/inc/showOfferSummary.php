<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);

$address = new Address_data();

$query_zoek = "SELECT products_offers.*,products_offers_prods.* FROM products_offers,products_offers_prods
               WHERE products_offers.id = products_offers_prods.offerid and supplierid=$supplierid 
	             and customerid=$customerid and productid=$productid";

$result = sql_query($query_zoek);

/* build the actual data array */
$i = 0;
unset($productinfo);
while ($result->fetchInto($row)) {
	$productinfo .= gettext("Offer: " . $row[offerid] . " has a price of " . $row[offerprice] . "<br>");
	$i++;
}

if ($i == 0)
{
	echo "No offers found for this product";
}
else
{
	echo $productinfo;
}
?>
