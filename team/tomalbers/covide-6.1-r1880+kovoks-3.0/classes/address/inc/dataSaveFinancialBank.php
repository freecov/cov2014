<?php
  #
  # Copyright 2006 KovoKs VOF 2006 - GPL
  # based on similar code Copyright Covide BV 2000-2006
  #
$financialcardinfo = $_REQUEST["financialcard"];

/* create 2 arrays so we can easily build the queries */
$fields = array();
$values = array();

$fields[] = "id";         
if ($financialcardinfo["id"]>0) 
	$values[] = sprintf("%d",   $financialcardinfo["id"]);
else
	$values[]= '\'\'';
$fields[] = "address_id";         
$values[] = sprintf("%d",   $financialcardinfo["address_id"]);
$fields[] = "`desc`";   
$values[] = sprintf("'%s'", $financialcardinfo["desc"]);
$fields[] = "name";       
$values[] = sprintf("'%s'", $financialcardinfo["name"]);
$fields[] = "address";         
$values[] = sprintf("'%s'", $financialcardinfo["address"]);
$fields[] = "zip";       
$values[] = sprintf("'%s'", $financialcardinfo["zip"]);
$fields[] = "place";          
$values[] = sprintf("'%s'", $financialcardinfo["place"]);
$fields[] = "province";          
$values[] = sprintf("'%s'", $financialcardinfo["province"]);
$fields[] = "country";          
$values[] = sprintf("'%s'", $financialcardinfo["country"]);
$fields[] = "iban";          
$values[] = sprintf("'%s'", $financialcardinfo["iban"]);
$fields[] = "bic";          
$values[] = sprintf("'%s'", $financialcardinfo["bic"]);

if ($financialcardinfo["address_id"]) {
	$sql = "REPLACE INTO address_finance_bank (".implode(",", $fields).") VALUES (".implode(",", $values).")";
}
$res = sql_query($sql);
/* refresh parent and close this window */
$output = new Layout_output();
$output->start_javascript();
$output->addCode(
	"location.href = 'index.php?mod=address&action=financialshow&cardid=".$financialcardinfo["address_id"]."'"
);
$output->end_javascript();
$output->exit_buffer();
?>
