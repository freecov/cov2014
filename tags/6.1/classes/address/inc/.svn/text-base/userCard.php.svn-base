<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* FIXME: quick and dirty way to solve usercard for user 'none' */
if ($id == 0) {
	echo "<script language=\"javascript\">history.go(-1);</script>";
}
$address_id = $id;
/* get the userinfo */
$userdata = new User_data();
$user_id = $userdata->getUserIdByAddressId($id);
$userinfo = $userdata->getUserDetailsById($user_id);
$visitor_perms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
/* get the address */
$address_data   = new Address_data();
$addressinfo[0] = $address_data->getAddressById($address_id, "user");
/* get the active notes for this user */
$notes_data     = new Note_data();
$notearr        = $notes_data->getNotes(array("user_id" => $user_id, "nocustcont" => 1));
$noteinfo       = $notearr["notes"];
/* get the active todos */
$todo_data = new Todo_data();
$todoinfo  = $todo_data->getTodosByUserId($user_id);
/* get the active customer contact items for this relation */
$custcontarr  = $notes_data->getNotes(array("user_id" => $user_id, "custcont" => 1));
$custcontinfo = $custcontarr["notes"];
/* get the appointments */
$calendar_data = new Calendar_data();
$calendarinfo  = $calendar_data->getAppointmentsByUser($user_id);
/* kilometers */
if ($GLOBALS["covide"]->license["has_project"]) {
	/* 1 month ago */
	$kmarr1 = $calendar_data->getKmItems(array(
		"users" => array($user_id),
		"start" => mktime(0, 0, 0, date("m")-1, 1, date("Y")),
		"end"   => mktime(0, 0, 0, date("m"), 1, date("Y"))
	));
	$kminfo["prev"] = $kmarr1[$user_id];
	unset($kmarr1);
	/* this month */
	$kmarr1 = $calendar_data->getKmItems(array(
		"users" => array($user_id),
		"start" => mktime(0, 0, 0, date("m"), 1, date("Y")),
		"end"   => mktime(0, 0, 0, date("m")+1, 1, date("Y"))
	));
	$kminfo["now"] = $kmarr1[$user_id];
}
/* get the filesys data */
if ($GLOBALS["covide"]->license["has_hrm"]) {
	$filesys_data = new Filesys_data();
	$filesysinfo = $filesys_data->getUserFolder($user_id);
}
/* get the project data */
if ($GLOBALS["covide"]->license["has_project"]) {
	$project_data = new Project_data();
	$projectinfo  = $project_data->getProjectsBySearch($id);
	$hoursinfo = $project_data->getOverviewData(mktime(0, 0, 0, date("m")-3, 1, date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
}
/* get the support data */
if ($GLOBALS["covide"]->license["has_issues"]) {
	$support_data = new Support_data();
	$supportinfo  = $support_data->getSupportItems(array("user_id" => $user_id, "active" => 1, "nolimit" => 1));
}
/* get the sales items */
if ($GLOBALS["covide"]->license["has_sales"]) {
	$sales_data = new Sales_data();
	$sales_info = $sales_data->getSalesBySearch(array("user_id" => $user_id));
}

/* init the output object */
$output = new Layout_output();
$output->layout_page("usercard");

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

/* address record */
$venster_settings = array(
	"title"    => gettext("gegevens")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($addressinfo);
	/* specify layout */
	//$venster->addCode("<pre>".print_r($addressinfo, true)."</pre>");
	$view->addMapping(gettext("naam"), array("%givenname", " ", "%surname"));
	$view->addMapping(gettext("foto"), "%photo");
	$view->addMapping(gettext("debiteur nr"), "%debtor_nr");
	$view->addMapping(gettext("contact persoon"), "%tav");
	$view->addMapping(gettext("adres"), array("%address","\n","%address2"));
	$view->addMapping(gettext("postcode"), "%zipcode");
	$view->addMapping(gettext("plaats"), "%city");
	$view->addMapping(gettext("postbus"), "%pobox");
	$view->addMapping(gettext("postcode postbus"), "%pobox_zipcode");
	$view->addMapping(gettext("plaats postbus"), "%pobox_city");
	$view->addMapping(gettext("telefoon nr"), "%%phone_nr");
	$view->addMapping(gettext("mobiel nr"), "%%mobile_nr");
	$view->addMapping(gettext("fax nr"), "%fax_nr");
	$view->addMapping(gettext("email"), "%%email");
	$view->addMapping(gettext("website"), "%%website");
	$view->addMapping(gettext("account manager"), "%account_manager_name");
	$view->addMapping(gettext("classificatie(s)"), "%classification_names");
	$view->addMapping(gettext("zoeken naar communicatie items"), "%companyname");
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

if ($visitor_perms["xs_hrmmanage"] || $user_id == $_SESSION["user_id"]) {
	/* hrm info */
	$hrminfo = $address_data->getHRMinfo($user_id);
	$venster_settings = array(
		"title"    => gettext("hrm"),
		"subtitle" => gettext("informatie")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($hrminfo);
		$view->addMapping(gettext("bestanden"), "%%complex_files");
		$view->addMapping(gettext("geslacht"), "%human_gender");
		$view->addMapping(gettext("verjaardag"), "%human_bday");
		$view->addMapping(gettext("sofi nummer"), "%social_security_nr");
		$view->addMapping(gettext("datum in dienst"), "%human_start");
		$view->addMapping(gettext("datum uit dienst"), "%human_end");
		$view->addMapping(gettext("dienstverband"), "%contract_type");
		$view->addMapping(gettext("aantal uren"), "%contract_hours");
		$view->addMapping(gettext("vakantie uren"), "%contract_holidayhours");
		$view->addMapping(gettext("evaluatie"), "%evaluation");
		$view->defineComplexMapping("complex_files", array(
			array(
				"type" => "link",
				"text" => gettext("open map"),
				"link" => array("index.php?mod=filesys&action=opendir&id=", $filesysinfo)
			)
		));
		$venster->addCode($view->generate_output_vertical());
		$venster->insertAction("edit", gettext("wijzig"), "javascript: popup('index.php?mod=address&action=edithrm&user_id=$user_id', 'hrmedit', 550, 600, 1);");
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
}
if ($GLOBALS["covide"]->license["has_project"]) {
	/* hours */
	$venster_settings = array(
		"title" => gettext("uren van de afgelopen 3 maanden")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData(array($hoursinfo["users"][$user_id]));
		$view->addMapping(gettext("wel factureerbare uren"), "%total_fac");
		$view->addMapping(gettext("niet factureerbare uren"), "%total_nofac");
		$view->addMapping(gettext("vakantie uren"), "%total_hol");
		$view->addMapping(gettext("ziek"), "%total_ill");
		$view->addMapping(gettext("bijzonder verlof"), "%total_sl");
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
	/* kilometers */
	$venster_settings = array(
		"title"    => gettext("kilometers"),
		"subtitle" => gettext("informatie")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$table = new Layout_table(array("width" => "100%"));
		$table->addTableRow();
			$table->insertTableData(gettext("vorige maand"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "data");
				$view = new Layout_view();
				$view->addData(array($kminfo["prev"]));
				$view->addMapping(gettext("declarabel"), "%total_dec");
				$view->addMapping(gettext("niet")." ".gettext("declarabel"), "%total_non_dec");
				$table->addCode($view->generate_output());
				unset($view);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("deze maand"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "data");
				$view = new Layout_view();
				$view->addData(array($kminfo["now"]));
				$view->addMapping(gettext("declarabel"), "%total_dec");
				$view->addMapping(gettext("niet")." ".gettext("declarabel"), "%total_non_dec");
				$table->addCode($view->generate_output());
				unset($view);
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);

		$venster->addTag("br");
		$venster->insertLink(gettext("zoeken"), array("href" => "index.php?mod=calendar&action=km"));
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
}

/* support info */
if ($GLOBALS["covide"]->license["has_issues"]) {
	$venster_settings = array(
		"title"    => gettext("klachten/support"),
		"subtitle" => gettext("huidig")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($supportinfo["items"]);
		$view->addMapping(gettext("datum"), "%human_date");
		$view->addMapping(gettext("support aanvraag"), "%short_desc");
		$view->addMapping(gettext("afhandeling"), "%short_sol");
		$view->addMapping(gettext("registrant"), "%sender_name");
		$view->addMapping(gettext("uitvoerder"), "%rcpt_name");
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}

/* note info */
$venster_settings = array(
	"title"    => gettext("notities"),
	"subtitle" => gettext("huidig")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($noteinfo);
	$view->addMapping(gettext("datum"), "%human_date");
	$view->addMapping(gettext("van"), "%from_name");
	$view->addMapping(gettext("naar"), "%to_name");
	$view->addMapping(gettext("onderwerp"), "%%subject");
	$view->defineComplexMapping("subject", array(
		array(
			"type" => "link",
			"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 0, 0, 1);"),
			"text" => "%subject"
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
	$venster->addTag("br");
	$venster->insertLink(gettext("zoeken")."/".gettext("historie"), array("href"=>"index.php?mod=index&search[private]=0&search[notes]=1&search[phrase]=".$userinfo["username"]));
$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* custcont info */
$venster_settings = array(
	"title"    => gettext("klantcontacten")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($custcontinfo);
	$view->addMapping(gettext("datum"), "%human_date");
	$view->addMapping(gettext("van"), "%from_name");
	$view->addMapping(gettext("naar"), "%to_name");
	$view->addMapping(gettext("onderwerp"), "%%subject");
	$view->defineComplexMapping("subject", array(
		array(
			"type" => "link",
			"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 0, 0, 1);"),
			"text" => "%subject"
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
	$venster->addTag("br");
	$venster->insertLink(gettext("zoeken")."/".gettext("historie"), array("href"=>"index.php?mod=index&search[private]=0&search[notes]=1&search[phrase]=".$userinfo["username"]));
$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* sales info */
if ($GLOBALS["covide"]->license["has_sales"] && ($user_id == $_SESSION["user_id"] || $visitor_perms["xs_salesmanage"])) {
	$venster_settings = array(
		"title"    => gettext("sales"),
		"subtitle" => gettext("actieve items")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($sales_info["data"]);
		$view->addMapping(gettext("onderwerp"), "%subject");
		$view->addMapping(gettext("relatie"), "%%complex_address");
		$view->addMapping(gettext("scoringspercentage"), "%%complex_score");
		$view->addMapping(gettext("bedrag"), "%total_sum");
		$view->defineComplexMapping("complex_address", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
				"text" => "%h_address"
			)
		));
		$view->defineComplexMapping("complex_score", array(
			array(
				"type" => "text",
				"text" => array("%expected_score", "&#37;") /* &#37; is a % */
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		$venster->addTag("br");
		$venster->insertLink(gettext("sales module"), array("href" => "index.php?mod=sales"));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}

/* todo info */
$venster_settings = array(
	"title"    => gettext("todo"),
	"subtitle" => gettext("huidig")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($todoinfo);
	$view->addMapping(gettext("datum"), "%desktop_time");
	$view->addMapping(gettext("onderwerp"), "%%subject");
	$view->defineComplexMapping("subject", array(
		array(
			"type" => "link",
			"link" => array("javascript: loadXML('index.php?mod=todo&action=show_info&id=", "%id", "');"),
			"text" => "%subject"
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* calendar info */
$venster_settings = array(
	"title" => gettext("agenda")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($calendarinfo);
		$view->addMapping(gettext("van"), "%%human_start");
		$view->addMapping(gettext("tot"), "%%human_end");
		$view->addMapping(gettext("onderwerp"), "%%subject");
		$view->addMapping(gettext("gebruiker"), "%user_name");
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
	$venster->insertLink(gettext("zoeken")."/".gettext("historie"), array("href"=>"index.php?mod=index&search[private]=0&search[calendar]=1&search[phrase]=".$userinfo["username"]));
	$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();

$output->addCode($tbl->generate_output());

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );

$output->layout_page_end();
echo $output->exit_buffer();
?>
