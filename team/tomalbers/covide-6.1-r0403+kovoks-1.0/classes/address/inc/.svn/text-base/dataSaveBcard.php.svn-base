<?php
if (!class_exists("Address_data")) {
	die("no class definition found");
}
$bcardinfo  = $_REQUEST["bcard"];
$metafields = $_REQUEST["metafield"];
/* create 2 arrays so we can easily build the queries */

$addresses = explode(",", $bcardinfo["address_id"]);
/* strip empty items */
$addresses = array_unique($addresses);
foreach ($addresses as $k=>$v) {
	if (!$v) {
		unset($addresses[$k]);
	}
}
/* save photo */
if (is_array($_FILES["bcard"]) && $_FILES["bcard"]["error"]["binphoto"] == 0) {
	/* store the relphoto */
	$this->storeRelIMG($bcardinfo["id"], "bcards", $_FILES);
}
sort($addresses);
/* put first id we find in address_id. If more are found set multirel to the remaining values */
$bcardinfo["address_id"] = $addresses[0];
if (count($addresses) > 1) {
	unset($addresses[0]);
	$bcardinfo["multirel"] = implode(",", $addresses);
}

$fields = array();
$values = array();

$fields[] = "address_id";         $values[] = sprintf("%d",   $bcardinfo["address_id"]);
$fields[] = "alternative_name";   $values[] = sprintf("'%s'", $bcardinfo["alternative_name"]);
$fields[] = "businessunit";       $values[] = sprintf("'%s'", $bcardinfo["businessunit"]);
$fields[] = "department";         $values[] = sprintf("'%s'", $bcardinfo["department"]);
$fields[] = "locationcode";       $values[] = sprintf("'%s'", $bcardinfo["locationcode"]);
$fields[] = "givenname";          $values[] = sprintf("'%s'", $bcardinfo["givenname"]);
$fields[] = "initials";           $values[] = sprintf("'%s'", $bcardinfo["initials"]);
$fields[] = "infix";              $values[] = sprintf("'%s'", $bcardinfo["infix"]);
$fields[] = "surname";            $values[] = sprintf("'%s'", $bcardinfo["surname"]);
$fields[] = "timestamp_birthday"; $values[] = sprintf("%d",   mktime(0, 0, 0, $bcardinfo["bday_month"], $bcardinfo["bday_day"], $bcardinfo["bday_year"]));
$fields[] = "letterhead";         $values[] = sprintf("%d",   $bcardinfo["letterhead"]);
$fields[] = "commencement";       $values[] = sprintf("%d",   $bcardinfo["commencement"]);
$fields[] = "title";              $values[] = sprintf("%d",   $bcardinfo["title"]);
$fields[] = "business_address";   $values[] = sprintf("'%s'", $bcardinfo["business_address"]);
$fields[] = "business_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["business_zipcode"]);
$fields[] = "business_city";      $values[] = sprintf("'%s'", $bcardinfo["business_city"]);
$fields[] = "business_phone_nr";  $values[] = sprintf("'%s'", $bcardinfo["business_phone_nr"]);
$fields[] = "business_fax_nr";    $values[] = sprintf("'%s'", $bcardinfo["business_fax_nr"]);
$fields[] = "business_mobile_nr"; $values[] = sprintf("'%s'", $bcardinfo["business_mobile_nr"]);
$fields[] = "business_email";     $values[] = sprintf("'%s'", $bcardinfo["business_email"]);
$fields[] = "personal_address";   $values[] = sprintf("'%s'", $bcardinfo["personal_address"]);
$fields[] = "personal_zipcode";   $values[] = sprintf("'%s'", $bcardinfo["personal_zipcode"]);
$fields[] = "personal_city";      $values[] = sprintf("'%s'", $bcardinfo["personal_city"]);
$fields[] = "personal_phone_nr";  $values[] = sprintf("'%s'", $bcardinfo["personal_phone_nr"]);
$fields[] = "personal_fax_nr";    $values[] = sprintf("'%s'", $bcardinfo["personal_fax_nr"]);
$fields[] = "personal_mobile_nr"; $values[] = sprintf("'%s'", $bcardinfo["personal_mobile_nr"]);
$fields[] = "personal_email";     $values[] = sprintf("'%s'", $bcardinfo["personal_email"]);
$fields[] = "memo";               $values[] = sprintf("'%s'", $bcardinfo["memo"]);
$fields[] = "classification";     $values[] = sprintf("'%s'", $bcardinfo["classification"]);
$fields[] = "sync_modified";      $values[] = sprintf("%d",   mktime());
$fields[] = "multirel";           $values[] = sprintf("'%s'", $bcardinfo["multirel"]);

if ($bcardinfo["id"]) {
	if (count($metafields)) {
		$meta_data = new Metafields_data();
		$meta_data->meta_save_field($metafields);
	}
	/* save modified bcard */
	$sql = "UPDATE address_businesscards SET ";
	foreach ($fields as $k=>$v) {
		$sql .= $v." = ".$values[$k].", ";
	}
	$sql = substr($sql, 0, strlen($sql)-2);
	$sql .= sprintf(" WHERE id=%d", $bcardinfo["id"]);
} else {
	/* inject new bcard */
	$sql = "INSERT INTO address_businesscards (".implode(",", $fields).") VALUES (".implode(",", $values).")";
}
$res = sql_query($sql);
/* refresh parent and close this window */
$output = new Layout_output();
$output->start_javascript();
$output->addCode(
	"
	opener.document.location.href = opener.document.location.href;
	window.close();
	"
);
$output->end_javascript();
$output->exit_buffer();
?>
