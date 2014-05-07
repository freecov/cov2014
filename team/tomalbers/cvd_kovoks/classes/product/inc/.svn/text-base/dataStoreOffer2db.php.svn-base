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

if ($data["date"] > 0)
{
    $fields[]="`date`"; 
    $values[]=$data["date"];
}
else
{
    $fields[]="`date`"; 
    $values[]=date("U");        
}

$fields[] = "`supplierid`";  $values[] = sprintf("%d",   $data["supplierid"]);
$fields[] = "`customerid`";  $values[] = sprintf("%d",   $data["customerid"]);
$fields[] = "`header`";      $values[] = sprintf("'%s'", $data["header"]);
$fields[] = "`condition`";   $values[] = sprintf("'%s'", $data["condition"]);
$fields[] = "`terms`";       $values[] = sprintf("'%s'", $data["terms"]);
$validity = $_REQUEST["validity"];
$d = mktime(0, 0, 0, $validity["timestamp_month"], $validity["timestamp_day"], $validity["timestamp_year"]);
$fields[] = "`validity`";    $values[] = sprintf("%d", $d);
$fields[] = "`samples`";     $values[] = sprintf("'%s'", $data["samples"]);
$fields[] = "`discount`";    $values[] = sprintf("'%s'", $data["discount"]);
$fields[] = "`remarks`";     $values[] = sprintf("'%s'", $data["remarks"]);
$fields[] = "locked";        $values[] = "0";

$sql = "REPLACE INTO products_offers (".implode(",", $fields).") VALUES (".implode(",", $values).");";
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
    $output->addCode(" location.href = 'index.php?mod=product&action=editoffer&reedit=true&id=".$id."' ");
else
    $output->addCode(" location.href = 'index.php?mod=product&action=showoffer&offerid=".$id."' ");
$output->end_javascript();
$output->exit_buffer();
?>
