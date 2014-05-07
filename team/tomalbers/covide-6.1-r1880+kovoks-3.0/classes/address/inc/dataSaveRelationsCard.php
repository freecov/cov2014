<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
$relationscardinfo = $_REQUEST["relationscard"];
$metafields = $_REQUEST["metafield"];

/* create 2 arrays so we can easily build the queries */
$fields = array();
$values = array();

$fields[] = "id";         
$values[] = sprintf("%d",   $relationscardinfo["id"]);
$fields[] = "customer_id";         
$values[] = sprintf("%d",   $relationscardinfo["customer_id"]);
$fields[] = "supplier_id";         
$values[] = sprintf("%d",   $relationscardinfo["supplier_id"]);
$fields[] = "pay_remark";   
$values[] = sprintf("'%s'", $relationscardinfo["pay_remark"]);
$fields[] = "bank_pref";         
$values[] = sprintf("%d",   $relationscardinfo["bank_pref"]);
$fields[] = "transporter";         
$values[] = sprintf("%d",   $relationscardinfo["transporter"]);

$sql = "REPLACE INTO address_relations (".implode(",", $fields).") VALUES (".implode(",", $values).")";
$res = sql_query($sql);
/* refresh parent and close this window */
$output = new Layout_output();
$output->start_javascript();

if ($relationscardinfo["type"] == "editsupplier" || $relationscardinfo["type"] == "addsupplier")
	$output->addCode( "location.href = 'index.php?mod=address&action=relationsshow&cardid=".$relationscardinfo["customer_id"]."'");
if ($relationscardinfo["type"] == "editcustomer" || $relationscardinfo["type"] == "addcustomer")
	$output->addCode( "location.href = 'index.php?mod=address&action=relationsshow&cardid=".$relationscardinfo["supplier_id"]."'");
if (count($metafields)) {
        $meta_data = new Metafields_data();
        $meta_data->meta_save_field($metafields);
}

$output->end_javascript();
$output->exit_buffer();
?>
