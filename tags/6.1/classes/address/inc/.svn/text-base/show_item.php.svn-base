<?php
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
$view->addMapping(gettext("relatie"), "%companyname");
$view->addMapping(gettext("contactpersoon"), "%contact_person");
$view->addMapping(gettext("t.a.v."), "%tav");
$view->addMapping(gettext("relatiekaart"), "%%complex_relcardlink");
$view->defineComplexMapping("complex_relcardlink", array(
	array(
		"type" => "link",
		"link" => array("index.php?mod=address&action=relcard&id=", "%id"),
		"text" => gettext("klik hier")
	)
));
$view->addMapping(gettext("account manager"), "%account_manager_name");
$view->addMapping(gettext("adres"), array("%address", "\n", "%address2"));
$view->addMapping(gettext("postcode"), "%zipcode");
$view->addMapping(gettext("plaats"), "%city");
$view->addMapping(gettext("land"), "%country");
$view->addMapping(gettext("postbus"), "%pobox");
$view->addMapping(gettext("postcode postbus"), "%pobox_zipcode");
$view->addMapping(gettext("plaats postbus"), "%pobox_city");
$view->addMapping(gettext("telefoon nr"), "%phone_nr");
$view->addMapping(gettext("mobiel nr"), "%mobile_nr");
$view->addMapping(gettext("fax nr"), "%fax_nr");
$view->addMapping(gettext("email"), "%email");
$view->addMapping(gettext("website"), "%website");
$view->addMapping(gettext("projecten"), "%project_names");
$view->addMapping(gettext("aantekeningen"), "%memo");
/* end view definition */

/* init output object */
$output = new Layout_output();

/* start building the page */
$output->layout_page(gettext("adresboek"));
/* window widget */
$venster = new Layout_venster(array(
	"title"    => gettext("adressen"),
	"subtitle" => gettext("gegevens bekijken")
));
/* menu items */
$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1);");
$venster->addMenuItem(gettext("wijzigen"), "javascript: alert('to be implemented');");
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
