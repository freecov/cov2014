<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
$financialcardinfo = $_REQUEST["financialcard"];
/* create 2 arrays so we can easily build the queries */
$fields = array();
$values = array();

$fields[] = "address_id";         
$values[] = sprintf("%d",   $financialcardinfo["id"]);
$fields[] = "tax_nr";   
$values[] = sprintf("'%s'", $financialcardinfo["tax_nr"]);
$fields[] = "acc_nr";       
$values[] = sprintf("'%s'", $financialcardinfo["acc_nr"]);
$fields[] = "ecotax";         
$values[] = sprintf("'%s'", $financialcardinfo["ecotax"]);
$fields[] = "pay_remark";       
$values[] = sprintf("'%s'", $financialcardinfo["pay_remark"]);
$fields[] = "kingid";          
$values[] = sprintf("'%s'", $financialcardinfo["kingid"]);

if ($financialcardinfo["id"]) {
	$sql = "REPLACE INTO address_finance (".implode(",", $fields).") VALUES (".implode(",", $values).")";
}
$res = sql_query($sql);
/* refresh parent and close this window */
$output = new Layout_output();
$output->start_javascript();
$output->addCode(
	"location.href = 'index.php?mod=address&action=financialshow&cardid=".$financialcardinfo["id"]."'"
);
$output->end_javascript();
$output->exit_buffer();
?>
