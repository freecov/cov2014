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
/* FIXME: quick and dirty way to solve usercard for user 'none' */
if ($id == 0) {
	echo "<script language=\"javascript\">history.go(-1);</script>";
}
$address_id = $id;

/* get the address */
$address_data   = new Address_data();
$addressinfo[0] = $address_data->getAddressByID($address_id, "user");

/* get email data */
$email_data   = new Email_data();
$emailids    = $email_data->getEmailByPrivateID($address_id);
if (is_array($emailids)) {
	foreach ($emailids as $email_id) {
		$emailsdata = $email_data->getEmailById($email_id);
		//print_r($emailsdata);
		$id = $emailsdata[0]["id"];
		$emailinfo[$id]["id"] = $emailsdata[0]["id"];
		$emailinfo[$id]["folder_id"] = $emailsdata[0]["folder_id"];
		$emailinfo[$id]["sender"] = $emailsdata[0]["sender"];
		$emailinfo[$id]["to"] = $emailsdata[0]["to"];
		$emailinfo[$id]["subject"] = $emailsdata[0]["subject"];
		$emailinfo[$id]["h_description"] = trim( preg_replace("/((\r)|(\t)|(\n))/s", " ", strip_tags($emailsdata[0]["description"]) ) );
		$emailinfo[$id]["short_date"] = date("d-m-Y", $emailsdata[0]["date"]);
		$emailinfo[$id]["short_time"] = date("H:i", $emailsdata[0]["date"]);
		unset($emailsdata);
	}
}
/* get calendar data */
$calendar_data = new Calendar_data();
$calendarinfo  = $calendar_data->getAppointmentsByPrivate($address_id);


/* init the output object */
$output = new Layout_output();
$output->layout_page("usercard");

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();
$buf2counter = 0;
/* address record */
$venster_settings = array(
	"title"    => gettext("information")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($addressinfo);
	/* specify layout */
	$view->addMapping(gettext("name"), array("%givenname", " ", "%surname"));
	$view->addMapping(gettext("picture"), "%photo");
	$view->addMapping(gettext("debtor nr"), "%debtor_nr");
	$view->addMapping(gettext("contact"), "%tav");
	$view->addMapping(gettext("address"), array("%address","\n","%address2"));
	$view->addMapping(gettext("zip code"), "%zipcode");
	$view->addMapping(gettext("city"), "%city");
	$view->addMapping(gettext("po box"), "%pobox");
	$view->addMapping(gettext("zip code po box"), "%pobox_zipcode");
	$view->addMapping(gettext("city po box"), "%pobox_city");
	$view->addMapping(gettext("telephone nr"), "%%phone_nr", array(
		"allow_html" => 1
	));
	$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr", array(
		"allow_html" => 1
	));
	$view->addMapping(gettext("fax nr"), "%fax_nr");
	$view->addMapping(gettext("email"), "%%email");
	$view->addMapping(gettext("website"), "%%website");
	$view->addMapping(gettext("account manager"), "%account_manager_name");
	$view->addMapping(gettext("classification(s)"), "%classification_names");
	$view->addMapping(gettext("search for communication items"), "%companyname");
	$view->defineComplexMapping("phone_nr", array(
		array(
			"type" => "text",
			"text" => "%phone_nr_link"
		)
	));
	$view->defineComplexMapping("mobile_nr", array(
		array(
			"type" => "text",
			"text" => "%mobile_nr_link"
		)
	));
	$view->defineComplexMapping("email", array(
		array(
			"type"    => "link",
			"text"    => "%email",
			"link"    => array("javascript: emailSelectFrom('", "%email", "','", "$address_id", "');")
		)
	));
	$view->defineComplexMapping("website", array(
		array(
			"type"   => "link",
			"text"   => "%website",
			"link"   => "%website",
			"target" => "_blank"
		)
	));
	$venster->addCode($view->generate_output_vertical());
	unset($view);
$venster->endVensterData();


$buf1->addCode($venster->generate_output());
unset($venster);




/* calendar info */
//FIXME: add calendar delegation access

	$venster_settings = array(
		"title" => gettext("calendar")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($calendarinfo);
			$view->addMapping(gettext("from"), "%%human_start");
			$view->addMapping(gettext("till"), "%%human_end");
			$view->addMapping(gettext("subject"), "%%subject");
			$view->addMapping(gettext("user"), "%user_name");
			$view->defineComplexMapping("human_start", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=calendar&extra_user=", "%user_id", "&timestamp=", "%timestamp_start"),
					"text" => "%human_start"
				)
			));
			$view->defineComplexMapping("human_end", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=calendar&extra_user=", "%user_id", "&timestamp=", "%timestamp_end"),
					"text" => "%human_end"
				)
			));
			$view->defineComplexMapping("subject", array(
				array(
					"type" => "link",
					"link" => array("javascript: popup('index.php?mod=calendar&action=edit&id=", "%id", "', 'calendaredit', 0, 0, 1);"),
					"text" => "%subject"
				)
			));
			$venster->addCode($view->generate_output());
			unset($view);
		$venster->addTag("br");
		$venster->insertLink(gettext("search")."/".gettext("history"), array("href"=>"index.php?mod=index&search[private]=0&search[calendar]=1&search[phrase]=".$userinfo["username"]));
		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;


/* email link */
	$venster_settings = array(
		"title" => gettext("email"),
		"subtitle" => gettext("emails of this conctact in my own folders")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();

		$count = count($emailinfo["data"]);
		if ($count >= 6) {
			$limit_height = "height: 300px; overflow:auto;";
		} else {
			$limit_height = "";
		}
		$venster->addTag("div", array(
			"class"  => "limit_height",
			"style" => $limit_height
		));

		/* create a new view and add the data */
		$view = new Layout_view();
		$view->addData($emailinfo);


		$view->addMapping("%%header_fromto", "%%data_fromto");
		$view->addMapping(gettext("date"), "%%data_datum", "right");

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
					gettext("from"), ": ", "%sender", "\n",
					gettext("to"), ": ", "%to"
				)
			)
		));
		/* define complex mapping for subject  */

		$_action = "open";
		$view->defineComplexMapping("data_subject", array(
			array(
				"type" => "link",
				"text" => "%subject",
				"link" => array("?mod=email&action=$_action&id=", "%id")
			)
		));
		$view->defineComplexMapping("data_description", array(
			array(
				"type" => "text",
				"text" => "%h_description"
			)
		));

		/* define complex mapping for datum  */
		$view->defineComplexMapping("data_datum", array(
			array(
				"text" => array( "%short_date", "\n", "%short_time" )
			)
		));

		$venster->addCode( $view->generate_output() );
		unset($view);

		$venster->endTag("div");

		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);


$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	if ($buf2counter)
		$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();

$output->addCode($tbl->generate_output());

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );

$output->layout_page_end();
echo $output->exit_buffer();
?>
