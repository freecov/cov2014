<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

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
	"title"    => gettext("address book"),
	"subtitle" => gettext("businesscard")
));
$venster->addMenuItem(gettext("close"), "javascript:window.close();");
$venster->generateMenuItems();
$venster->addVensterData();

	/* put a view here */
	$view = new Layout_view();
	$view->addData($bcardinfo);
	$view->addMapping("", "%alternative_name");
	$view->addMapping(gettext("businessunit"), "%businessunit");
	$view->addMapping(gettext("department"), "%department");
	$view->addMapping(gettext("locationcode"), "%locationcode");
	$view->addMapping(gettext("address"), "%business_address");
	$view->addMapping(gettext("state/province"), "%business_state");
	$view->addMapping(gettext("zip code").", ".gettext("city"), "%%complex_business_address");
	$view->addMapping(gettext("telephone number"), "%business_phone_nr_link");
	$view->addMapping(gettext("fax number"), "%business_fax_nr");
	$view->addMapping(gettext("mobile phone number"), "%business_mobile_nr_link");
	$view->addMapping(gettext("email address"), "%business_email");

	$view->addMapping(gettext("private")." ".gettext("address"), "%personal_address");
	$view->addMapping(gettext("private")." ".gettext("state/province"), "%personal_state");
	$view->addMapping(gettext("private")." ".gettext("zip code").", ".gettext("city"), "%%complex_personal_address");
	$view->addMapping(gettext("private")." ".gettext("telephone number"), "%personal_phone_nr_link");
	$view->addMapping(gettext("private")." ".gettext("fax number"), "%personal_fax_nr");
	$view->addMapping(gettext("private")." ".gettext("mobile phone number"), "%personal_mobile_nr_link");
	$view->addMapping(gettext("private")." ".gettext("email address"), "%personal_email");
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
