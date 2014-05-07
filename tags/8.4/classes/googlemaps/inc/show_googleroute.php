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
$venster->addCode( $googlemaps->generate_route($from, $to, array("style"=>"width: 500px; height:500px")) );

$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
/* end of venster object */


$output->layout_page_end();
$output->exit_buffer();

?>