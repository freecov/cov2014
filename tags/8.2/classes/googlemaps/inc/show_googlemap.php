<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* if a business ID was given, get the possible locations. */
if ($id) {
	$address_data = new Address_data();
	$data = $address_data->getBcardsByRelationId($id);
	foreach ($data as $bc) {
		if ($bc["business_address"]) {
			$location_string = $bc["business_address"].', '.$bc["business_city"].', '.$bc["business_country"];
			$addresses[$bc["fullname"]][$location_string] = $location_string . ' - '.gettext("business");
		}
		if ($bc["personal_address"]) {
			$location_string = $bc["personal_address"].', '.$bc["personal_city"].', '.$bc["personal_country"];
			$addresses[$bc["fullname"]][$location_string] = $location_string . ' - '.gettext("personal");
		}
		if ($bc["other_address"]) {
			$location_string = $bc["other_address"].', '.$bc["other_city"].', '.$bc["other_country"];
			$addresses[$bc["fullname"]][$location_string] = $location_string . ' - '.gettext("other"); 
		}
	}
}

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("show map"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("show map"),
	"subtitle" => gettext("location")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();

	$googlemaps = new Googlemaps_output;
	/* If we wanna create new route, give bogus location (a.k.a. timestamp) */
	$loc = (!$location) ? $loc = time() : $loc = $location;
	$venster->addCode( $googlemaps->generate_map($loc, array("style"=>"width: 500px; height:500px")) );
	$venster->addTag("form", array(
		"id"     => "form",
		"method" => "post",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "googlemaps");
	$venster->addHiddenField("action", "show_route");
	$venster->addTag("br");
	$venster->addCode(gettext("from").": ");
	if (!$id) {
		$venster->addTextfield("from", $location, array("style" => "width: 400px;"));
	} else {
		$venster->addTextfield("from", '', array("style" => "width: 400px;"));
	}
	$venster->addTag("br");
	$venster->addCode(gettext("to").": ");
	if (!$id) {
		$venster->addTextfield("to", '', array("style" => "width: 400px;"));
	} else {
		$venster->addSelectField("to", $addresses, 1);
	}
	$venster->addSpace(5);
	$venster->insertAction("forward", gettext("generate route"), "javascript:getRoute()");
	$venster->endTag("form");
	$venster->start_javascript();
		$venster->addCode("
			function getRoute() {
				document.getElementById('form').submit();
			}
		");
	$venster->end_javascript();
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
/* end of venster object */


$output->layout_page_end();
$output->exit_buffer();

?>