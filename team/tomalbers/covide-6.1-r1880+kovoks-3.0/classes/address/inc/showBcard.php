<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* get the data from db */
$address_data = new Address_data();
$bcardinfo[0] = $address_data->getAddressByID($cardid, "bcards");
if ($bcardinfo[0]["photo"]["size"]) {
	$url = "index.php?mod=address&action=showrelimg&addresstype=bcards";
	foreach ($bcardinfo[0]["photo"] as $k=>$v) {
		$url .= "&photo[$k]=$v";
	}
	$bcardinfo[0]["photourl"] = $url;
}
/* get the meta data for this address record */
$meta_data = new Metafields_data();
$meta_output = new Metafields_output();
$metafields = $meta_data->meta_list_fields("bcards", $cardid);

foreach ($metafields as $v) {
	$bcardinfo[0][$v["fieldname"]] = $meta_output->meta_print_field($v);
}

$output = new Layout_output();
$output->layout_page("", 1);
/* nice window widget */
$venster = new Layout_venster(array(
	"title"    => gettext("adresboek"),
	"subtitle" => gettext("businesscard")
));
$venster->addMenuItem(gettext("sluiten"), "javascript:window.close();");
$venster->generateMenuItems();
$venster->addVensterData();

	/* put a view here */
	$view = new Layout_view();
	$view->addData($bcardinfo);
	$view->addMapping("", "%alternative_name");
	$view->addMapping(gettext("businessunit"), "%businessunit");
	$view->addMapping(gettext("afdeling"), "%department");
	$view->addMapping(gettext("locatiecode"), "%locationcode");
	$view->addMapping(gettext("adres"), "%business_address");
	$view->addMapping(gettext("postcode").", ".gettext("plaats"), "%%complex_business_address");
	$view->addMapping(gettext("telefoon nummer"), "%business_phone_nr_link");
	$view->addMapping(gettext("fax nummer"), "%business_fax_nr");
	$view->addMapping(gettext("mobiel nummer"), "%business_mobile_nr_link");
	$view->addMapping(gettext("email adres"), "%business_email");

	$view->addMapping(gettext("prive")." ".gettext("adres"), "%personal_address");
	$view->addMapping(gettext("prive")." ".gettext("postcode").", ".gettext("plaats"), "%%complex_personal_address");
	$view->addMapping(gettext("prive")." ".gettext("telefoon nummer"), "%personal_phone_nr_link");
	$view->addMapping(gettext("prive")." ".gettext("fax nummer"), "%personal_fax_nr");
	$view->addMapping(gettext("prive")." ".gettext("mobiel nummer"), "%personal_mobile_nr_link");
	$view->addMapping(gettext("prive")." ".gettext("email adres"), "%personal_email");
	if (count($metafields)) {
		foreach ($metafields as $v) {
			$database_mapping = "%".$v["fieldname"];
			$view->addMapping($v["fieldname"], $database_mapping);
		}
	}
	$view->defineComplexMapping("complex_business_address", array(
		array(
			"text"  => array("%business_zipcode", ", ", "%business_city"),
			"check" => "%business_city"
		)
	));
	$view->defineComplexMapping("complex_personal_address", array(
		array(
			"text"  => array("%personal_zipcode", ", ", "%personal_city"),
			"check" => "%personal_city"
		)
	));
	$table = new Layout_table();
	$table->addTableRow();
		$table->addTableData(array("vertical-align" => "top"), "top");
			$table->addCode($view->generate_output_vertical());
		$table->endTableData();
		$table->addTableData(array("vertical-align" => "top"), "top");
			if ($bcardinfo[0]["photourl"]) {
				$table->addCode("<img src=\"".$bcardinfo[0]["photourl"]."\">");
			}
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster->addCode($table->generate_output());
	unset($view);
$venster->endVensterData();
/* attach to output */
$output->addCode($venster->generate_output());
unset($venster);
//$output->addCode("<pre>".print_r($bcardinfo, true)."</pre>");
$output->layout_page_end();
$output->exit_buffer();
?>
