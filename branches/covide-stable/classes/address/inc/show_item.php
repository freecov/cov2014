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
/* get the addressdata */
$address_data = new Address_data();
$address_info = $address_data->getAddressById($id, $addresstype);
/* create view */
$view = new Layout_view();
$view->addData(array($address_info));
/* specify mappings */
$view->addMapping(gettext("contact"), "%companyname");
$view->addMapping(gettext("contact"), "%contact_person");
$view->addMapping(gettext("addressed to"), "%tav");
if ($addresstype == "relations")
	$view->addMapping(gettext("contact profile"), "%%complex_relcardlink");
$view->defineComplexMapping("complex_relcardlink", array(
	array(
		"type" => "link",
		"link" => array("index.php?mod=address&action=relcard&id=", "%id"),
		"text" => gettext("click here")
	)
));
$view->addMapping(gettext("account manager"), "%account_manager_name");
$view->addMapping(gettext("address"), array("%address", "\n", "%address2"));
$view->addMapping(gettext("zip code"), "%zipcode");
$view->addMapping(gettext("city"), "%city");
$view->addMapping(gettext("state/province"), "%state");
$view->addMapping(gettext("country"), "%country");
$view->addMapping(gettext("po box"), "%pobox");
$view->addMapping(gettext("zip code po box"), "%pobox_zipcode");
$view->addMapping(gettext("city po box"), "%pobox_city");
$view->addMapping(gettext("telephone nr"), "%phone_nr");
$view->addMapping(gettext("mobile phone nr"), "%mobile_nr");
$view->addMapping(gettext("fax nr"), "%fax_nr");
$view->addMapping(gettext("email"), "%email");
$view->addMapping(gettext("website"), "%website");
$view->addMapping(gettext("projects"), "%project_names");
$view->addMapping(gettext("notes"), "%memo");
/* end view definition */

/* init output object */
$output = new Layout_output();

/* start building the page */
$output->layout_page(gettext("address book"));
/* window widget */
$venster = new Layout_venster(array(
	"title"    => gettext("addresses"),
	"subtitle" => gettext("view data")
));
/* menu items */
$venster->addMenuItem(gettext("back"), "javascript: history.go(-1);");
#$venster->addMenuItem(gettext("change:"), "javascript: alert('to be implemented');");
$venster->generateMenuItems();
/* end menu items */
$venster->addVensterData();
	$venster->addCode($view->generate_output_vertical());
$venster->endVensterData();
/* end window widget */
$output->addCode($venster->generate_output());
unset($venster);
$output->layout_page_end();
$output->exit_buffer();
?>
