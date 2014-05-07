<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
/* prepare the data to inject it into the db */
$values = array();
if ($data["id"])
{
	$fields[]="id"; 
        $values[]=$data["id"];
}

$fields[] = "`customerid`";       $values[] = sprintf("%d",   $data["customerid"]);
$fields[] = "`productid`";        $values[] = sprintf("%d",   $data["productid"]);
$fields[] = "`productnr_cust`";   $values[] = sprintf("'%s'", $data["productnr_cust"]);
$fields[] = "`price`";            $values[] = sprintf("%01.2f", $data["price"]);
$fields[] = "`remark`";           $values[] = sprintf("'%s'", $data["remark"]);
$fields[] = "`commission_amount`";$values[] = sprintf("'%s'", $data["commission_amount"]);
$fields[] = "`commission_perc`";  $values[] = sprintf("'%s'", $data["commission_perc"]);
$fields[] = "`commission_calc`";  $values[] = sprintf("'%s'", $data["commission_calc"]);
$validity = $_REQUEST["valid_start"];
$d = mktime(0, 0, 0, $validity["timestamp_month"], $validity["timestamp_day"], $validity["timestamp_year"]);
$fields[] = "`start_date`";       $values[] = sprintf("%d", $d);
$validity = $_REQUEST["valid_end"];
$d = mktime(0, 0, 0, $validity["timestamp_month"], $validity["timestamp_day"], $validity["timestamp_year"]);
$fields[] = "`end_date`";         $values[] = sprintf("%d", $d);

$sql = "REPLACE INTO products_prices (".implode(",", $fields).") VALUES (".implode(",", $values).");";
$res = sql_query($sql);
echo mysql_error();
if ($data["id"])
    $id = $data["id"];
else
    $id = mysql_insert_id();

$output = new Layout_output();
$output->start_javascript();
/* if this is a new offer, redirect to the edit screen.... */
if ($_REQUEST["reedit"] == true)
     $output->addCode(" location.href = 'index.php?mod=product&action=editprice&reedit=true&supplierid=".$_REQUEST["proddata"]["supplierid"]."&id=".$id."' ");
else
    $output->addCode(" location.href = 'index.php?mod=product&action=showprices' ");
$output->end_javascript();
$output->exit_buffer();
?>
