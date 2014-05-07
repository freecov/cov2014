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
$address_data = new Address_data();
/* search in notes */
$notes_options = array(
	"note_type" => "all",
	"user_id"   => "all",
	"address_id" => $_REQUEST["address_id"],
	"nolimit"   => 1,
	"zoekstring" => array(
		"ondinh" => $_REQUEST["searchkey"]
	)
);
$notes_data = new Note_data();
$notes_info = $notes_data->getNotes($notes_options);
unset($notes_options);

/* search support issues */

/* search calendar */
$calendar_options = array(
	"address_id"  => $_REQUEST["address_id"],
	"all"         => 1,
	"searchkey"   => $_REQUEST["searchkey"],
	"sortorder"   => "DESC"
);
$calendar_data = new Calendar_data();
$calendar_info = $calendar_data->getAppointmentsBySearch($calendar_options);
unset($calendar_options);

/* search email */
$email_data = new Email_data();
/* get archive folder info */
$archive_info = $email_data->getSpecialFolder("Archief", 0);
$email_options = array(
	"relation" => (int)$_REQUEST["address_id"],
	"folder"   => $archive_info["id"],
	"search"   => $_REQUEST["searchkey"],
	"nolimit"  => 1
);
$email_info = $email_data->getEmailBySearch($email_options);
unset($email_options);

/* search folders */

/* search files */

/* start the output */
$subtitle = gettext("search results");
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addCode(gettext("Searched for")." '".$_REQUEST["searchkey"]."'");
	$output->addTag("br");
	$output->addCode(gettext("contact")." ".$address_data->getAddressNameById($_REQUEST["address_id"]));

	/* note results */
	$venster = new Layout_venster(array("title" => gettext("notes"), "subtitle" => $subtitle));
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($notes_info["notes"]);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->addMapping(gettext("from"), "%from_name");
		$view->addMapping(gettext("recipient"), "%to_name");
		$view->addMapping(gettext("read"), "%%is_read");
		$view->addMapping(gettext("done"), "%%is_done");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "link",
				"link" => array("javascript: to_note(", "%id", ");"),
				"text" => "%subject"
			)
		));
		$view->defineComplexMapping("is_read", array(
			array(
				"type"  => "action",
				"src"   => "ok",
				"check" => "%is_read"
			)
		));
		$view->defineComplexMapping("is_done", array(
			array(
				"type"  => "action",
				"src"   => "ok",
				"check" => "%is_done"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	/* calendar results */
	$venster_settings = array(
		"title"    => gettext("calendar"),
		"subtitle" => $subtitle
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($calendar_info);
		$view->addMapping(gettext("start"), "%human_start");
		$view->addMapping(gettext("end"), "%human_end");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->addMapping(gettext("user"), "%user_name");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "link",
				"link" => array("javascript: to_calendar(", "%id", ");"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	/* email results */
	$venster_settings = array(
		"title"    => gettext("email"),
		"subtitle" => $subtitle
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		/* create a new view and add the data */
		$view = new Layout_view();
		$view->addData($email_info["data"]);
		/* add the mappings (columns) we needed */
		$view->addMapping("%%header_fromto", "%%data_fromto");
		$view->addMapping(gettext("date"), "%%data_datum", "right");
		$view->addMapping(gettext("user"), "%h_user", "right");
		$view->addSubMapping("%%data_subject", "%is_new");
		$view->addSubMapping("%%data_description", "");
		/* define complex header fromto */
		$view->defineComplexMapping("header_fromto", array(
			array(
				"text" => array(
					gettext("subject"),
					"\n",
					gettext("sender"),
					"/",
					gettext("recipient")
				)
			)
		));
		/* define complex data fromto */
		$view->defineComplexMapping("data_fromto", array(
			array(
				"text" => array(
					gettext("from"), ": ",	"%sender_emailaddress", "\n",
					gettext("to"), ": ",	"%to"
				)
			)
		));
		/* define complex mapping for subject */
		$view->defineComplexMapping("data_subject", array(
			array(
				"type" => "link",
				"text" => "%subject",
				"link" => array("javascript: to_email(", "%id", ");")
			)
		));
		$view->defineComplexMapping("data_description", array(
			array(
				"type" => "text",
				"text" => "%h_description"
			)
		));

		/* define complex mapping for datum */
		$view->defineComplexMapping("data_datum", array(
			array(
				"text" => array( "%short_date", "\n", "%short_time" )
			)
		));

		$venster->addCode( $view->generate_output() );
		unset($view);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	$output->load_javascript(self::include_dir."relcardsearch_actions.js");
/* we have everything, flush it to the client */
$output->layout_page_end();
$output->exit_buffer();
?>
