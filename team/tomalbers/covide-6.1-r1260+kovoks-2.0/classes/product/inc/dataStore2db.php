<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
/* prepare the data to inject it into the db */
$values = array();
if ($data["id"]) {
	$fields[]="id"; $values[]=$data["id"];
}
$fields[] = "quality";           $values[] = sprintf("'%s'", $data["quality"]);
$fields[] = "name";              $values[] = sprintf("'%s'", $data["name"]);
$fields[] = "label";             $values[] = sprintf("'%s'",   $data["label"]);
$fields[] = "supplierid";        $values[] = sprintf("%d", $data["supplierid"]);
$fields[] = "replacement";       $values[] = sprintf("%d", $data["replacement"]);
$fields[] = "prod_year";         $values[] = sprintf("%d", $data["prod_year"]);
$fields[] = "content";       	 $values[] = sprintf("%01.2f", $data["content"]);
$fields[] = "box";               $values[] = sprintf("%d", $data["box"]);
$fields[] = "pallet";        	 $values[] = sprintf("%d", $data["pallet"]);
$fields[] = "price";           	 $values[] = sprintf("%01.2f", $data["price"]);
$fields[] = "remark";      	 $values[] = sprintf("'%s'",   $data["remark"]);
$fields[] = "EAN_prod";          $values[] = sprintf("'%s'",   $data["EAN_prod"]);
$fields[] = "EAN_box";           $values[] = sprintf("'%s'",   $data["EAN_box"]);
$fields[] = "pricelist";         $values[] = sprintf("%d", $data["pricelist"]);
$fields[] = "boxlayer";    	 $values[] = sprintf("%d", $data["boxlayer"]);
$fields[] = "prod_type";      	 $values[] = sprintf("'%s'", $data["prod_type"]);
$fields[] = "alcohol";           $values[] = sprintf("%d", $data["alcohol"]);
$fields[] = "private";           $values[] = sprintf("%d", $data["private"]);
$fields[] = "region";            $values[] = sprintf("'%s'",   $data["region"]);

$sql = "REPLACE INTO products (".implode(",", $fields).") VALUES (".implode(",", $values).");";
$res = sql_query($sql);
echo mysql_error();

$output = new Layout_output();
$output->start_javascript();
$output->addCode(" location.href = 'index.php?mod=product&action=show&productid=".$data["id"]."' ");
$output->end_javascript();
$output->exit_buffer();
?>
