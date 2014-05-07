<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups of people
 * that want the most efficient way to work together.
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

// find out what type (for new menu button)
switch ($_REQUEST["sub"]) {
case "commencements":
	$type = 1;
	break;
case "letterheads":
	$type = 3;
	break;
case "suffix":
	$type = 4;
	break;
default :
	$type = 2;
	break;
}

/* start output buffer routines */
$output = new Layout_output();
$output->layout_page( gettext("Headers") );


$settings = array(
	"title"    => gettext("Headers"),
	"subtitle" => gettext("Overview of headers"),
);

$venster = new Layout_venster($settings);
$venster->addMenuItem(gettext("new"), "javascript: edit_title(0, $type);", 0, 0);
$venster->addMenuItem(gettext("title"), "?mod=address&action=showheaders&sub=titles");
$venster->addMenuItem(gettext("commencement"), "?mod=address&action=showheaders&sub=commencements");
$venster->addMenuItem(gettext("letterhead"), "?mod=address&action=showheaders&sub=letterheads");
$venster->addMenuItem(gettext("suffix"), "?mod=address&action=showheaders&sub=suffix");
$venster->addMenuItem(gettext("back"), "?mod=address");
$venster->generateMenuItems();


unset($settings);

switch($_REQUEST["sub"]) {
	case "titles":
	default:
/* Get data for titles */
$data = new Address_data();
$data = $data->getTitles();

unset($data[0]);
$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
   		 //$view->addMapping(gettext("id"), "%id");
   		 $view->addMapping(gettext("title"), "%title");
		 $view->addMapping("", "%%complex_actions");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: edit_title(", "%id", ",", "2", ");"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_title(", "%id", ",", "2", ");"),
		)
	), "nowrap");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	$paging = new Layout_paging();
$venster->endVensterData();
$venster->addTag("BR");
$output->addCode($venster->generate_output());
unset($venster);
	break;
	case "commencements":

/* Get data for blabla */
$data = new Address_data();
$data = $data->getCommencements();


unset($data[0]);
$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
   		 //$view->addMapping(gettext("id"), "%id");
   		 $view->addMapping(gettext("commencement"), "%title");
		 $view->addMapping("", "%%complex_actions");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: edit_title(", "%id", ",", "1", ");"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_title(", "%id", ",", "1", ");"),
		)
	), "nowrap");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	
	$paging = new Layout_paging();
$venster->endVensterData();
$venster->addTag("BR");
$output->addCode($venster->generate_output());
unset($venster);
	break;

	case "letterheads":

/* Get data for blabla */
$data = new Address_data();
$data = $data->getLetterheads();


unset($data[0]);
$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
   		 //$view->addMapping(gettext("id"), "%id");
   		 $view->addMapping(gettext("letterhead"), "%title");
		 $view->addMapping("", "%%complex_actions");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: edit_title(", "%id", ",", "3", ");"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_title(", "%id", ",", "3", ");"),
		)
	), "nowrap");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	
	$paging = new Layout_paging();

$venster->endVensterData();
$venster->addTag("BR");
$output->addCode($venster->generate_output());
unset($venster);

	break;
	case "suffix":

/* Get data for blabla */
$data = new Address_data();
$data = $data->getSuffix();


unset($data[0]);
$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
   		 //$view->addMapping(gettext("id"), "%id");
   		 $view->addMapping(gettext("suffix"), "%title");
		 $view->addMapping("", "%%complex_actions");

	/* define the mappings */

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: edit_title(", "%id", ",", "4", ");"),
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("delete"),
			"link"    => array("javascript: del_title(", "%id", ",", "4", ");"),
		)
	), "nowrap");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	
	$paging = new Layout_paging();

$venster->endVensterData();
$venster->addTag("BR");
$output->addCode($venster->generate_output());
unset($venster);

break;
} // End of switch


$history = new Layout_history();
$output->load_javascript(self::include_dir."title_actions.js");
$output->addCode( $history->generate_save_state("action") );
$output->layout_page_end();
echo $output->generate_output();
?>
