<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
/* prepare the data to inject it into the db */
$values = array();
if ($data["id"]) {
	$fields[]="id"; $values[]=$data["id"];
}
$zip = implode("|",$data["zips"]);

$fields[] = "productid";           $values[] = sprintf("%d",   $data["productid"]);
$fields[] = "country";             $values[] = sprintf("'%s'", $data["country"]);
$fields[] = "zip";                 $values[] = sprintf("'%s'", $zip);

$sql = "REPLACE INTO products_nosell (".implode(",", $fields).") VALUES (".implode(",", $values).");";
$res = sql_query($sql);

$output = new Layout_output();
$output->start_javascript();
$output->addCode(" location.href = 'index.php?mod=product&action=show&productid=".$data["productid"]."'");
$output->end_javascript();
$output->exit_buffer();
?>
