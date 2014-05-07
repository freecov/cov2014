<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* FIXME: quick and dirty way to solve relationcard for relation 'none' */
if ($id == 0) {
	echo "<script language=\"javascript\">history.go(-1);</script>";
}

/* init user object */
$user_data = new User_data();
$userperms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
$accmanager_arr = explode(",", $user_data->permissions["addressaccountmanage"]);

/* get the address */
$address_data   = new Address_data();
$addressinfo[0] = $address_data->getAddressById($id);

/* check if we have access */
if ($user_data->checkPermission("xs_addressmanage")) {
	$addressinfo[0]["addressmanage"] = 1;
	$addressinfo[0]["addressacc"] = 1;
} else {
	$addressinfo[0]["addressmanage"] = 0;
	if (in_array($row["accmanager"], $accmanager_arr)) {
		$addressinfo[0]["addressacc"] = 1;
	} else {
		$addressinfo[0]["addressacc"] = 0;
	}
}
if (!$addressinfo[0]["addressacc"] && !$addressinfo[0]["addressmanage"]) {
	die("no permissions");
}
/* end access check */

/* we can taggle some stuff, so lets do that before we enter the rest */
if ($_REQUEST["relcardaction"] == "toggle_custcont") {
	foreach ($_REQUEST["checkbox_custcont"] as $k=>$item) {
		$sql = sprintf("UPDATE notes SET is_done = 1, is_read = 1 WHERE (is_done !=1 OR is_done is null) AND id=%d", $k);
		$res = sql_query($sql);
	}
}
if ($_REQUEST["relcardaction"] == "cardrem") {
	$address_data->remove_bcard($_REQUEST["cardid"]);
}

if ($addressinfo[0]["photo"]["size"]) {
	$url = "index.php?mod=address&action=showrelimg&addresstype=relations";
	foreach ($addressinfo[0]["photo"] as $k=>$v) {
		$url .= "&photo[$k]=$v";
	}
	$addressinfo[0]["photourl"] = $url;
}
/* get the meta data for this address record */
$meta_data = new Metafields_data();
$meta_output = new Metafields_output();
$metafields = $meta_data->meta_list_fields("adres", $id);
/* get the bcards */
$businesscards  = $address_data->getBcardsByRelationID($id);
/* get the active notes for this relation */
$notes_data     = new Note_data();
if ($_REQUEST["history"] == "notes") {
	$noteinfo       = $notes_data->getNotesByContact($id, 0);
	$subtitle_notes = gettext("historie");
} else {
	$noteinfo       = $notes_data->getNotesByContact($id, 1);
	$subtitle_notes = gettext("huidig");
}
/* get the active customer contact items for this relation */
if ($_REQUEST["history"] == "customercontact") {
	$custcontinfo   = $notes_data->getNotesByContact($id, 0, 1);
} else {
	$custcontinfo   = $notes_data->getNotesByContact($id, 1, 1);
}
/* get the active todos */
$todo_data      = new Todo_data();
$todoinfo       = $todo_data->getTodosByAddressId($id);
/* get the appointments */
$calendar_data = new Calendar_data();
if ($_REQUEST["history"] == "calendar") {
	$calendarinfo  =$calendar_data->getAppointmentsByAddress($id, 1);
} else {
	$calendarinfo  =$calendar_data->getAppointmentsByAddress($id);
}
/* get the filesys data */
$filesys_data = new Filesys_data();
$filesysinfo = $filesys_data->getRelFolder($id);
/* get the email data */
$email_data   = new Email_data();
$emailinfo    = $email_data->getEmailBySearch( array("relation_inbox" => $id) );
$emailarchive = $email_data->getSpecialFolder("Archief", 0);
if ($GLOBALS["covide"]->license["has_sales"]) {
	/* get sales data */
	$sales_data   = new Sales_data();
	$salesinfo    = $sales_data->getSalesBySearch( array("address_id"=>$id) );
}
/* get mortgage data */
$mortgage_data   = new Mortgage_data();
$mortgageinfo    = $mortgage_data->getMortgageBySearch( array("address_id"=>$id) );

if ($GLOBALS["covide"]->license["has_project"]) {
	/* get the project data */
	$project_data = new Project_data();
	$projectoptions = array("address_id" => $id);
	$projectinfo = $project_data->getProjectsBySearch($projectoptions);
	unset($projectoptions);
}

/* get the support data */
$support_data = new Support_data();
$supportinfo = $support_data->getSupportItems(array("address_id" => $id, "active" => 1, "nolimit" => 1));

/* get the support data */
$templates_data = new Templates_data();
$templates_info = $templates_data->getTemplateBySearch($id, 0, 1);


/* init the output object */
$output         = new Layout_output();
$output->layout_page("relationcard");

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
	$history = new Layout_history();
	$link = $history->generate_history_call();
	$venster->addCode($link);

	$venster->insertAction("back", gettext("terug"), "javascript: history_goback();");
	if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
		$venster->insertAction("view_all", gettext("template merge"), "?mod=projectext&action=extGenerateDocumentTree&address_id=".$_REQUEST["id"]);
	}

	$view = new Layout_view();
	$view->addData($addressinfo);
	/* specify layout */
	$view->addMapping(gettext("bedrijfsnaam"), "%%companyname");
	$view->addMapping(gettext("letop"), array("<b>", "%warning", "</b>"));
	$view->addMapping(gettext("foto"), "%%photo");
	$view->addMapping(gettext("debiteur nr"), "%debtor_nr");
	$view->addMapping(gettext("contact persoon"), "%tav");
	$view->addMapping(gettext("adres"), array("%address","\n","%address2"));
	$view->addMapping(gettext("postcode"), "%zipcode");
	$view->addMapping(gettext("plaats"), "%city");
	$view->addMapping(gettext("land"), "%country");
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
	$view->addMapping(gettext("zoeken naar communicatie items"), "%%complex_search");
	$view->defineComplexMapping("companyname", array(
		array(
			"type" => "link",
			"link" => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=relations', 'addressedit', 0, 0, 0);"),
			"text" => "%companyname"
		)
	));
	$view->defineComplexMapping("photo", array(
		array(
			"type" => "text",
			"text" => array("<img src=\"", "%photourl", "\">"),
			"check" => "%photourl"
		)
	));
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
			"link"    => array("javascript: emailSelectFrom('", "%email", "','", "%id", "');")
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
	$view->defineComplexMapping("complex_search", array(
		array(
			"type" => "action",
			"src"  => "search",
			"link" => array("javascript: popup('index.php?mod=address&action=relcardsearchform&address_id=", "%id", "', 'searchall', 0, 0, 0);")
		),
		array(
			"text" => " "
		),
		array(
			"type" => "link",
			"text" => gettext("zoeken naar communicatie items"),
			"link" => array("javascript: popup('index.php?mod=address&action=relcardsearchform&address_id=", "%id", "', 'searchall', 0, 0, 0);")
		)
	));
	$venster->addCode($view->generate_output_vertical(1));
	unset($view);
$venster->endVensterData();


$buf1->addCode($venster->generate_output());
unset($venster);

$venster_settings = array(
	"title" => gettext("extra")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$table = new Layout_table(array("cellspacing" => 1));
	foreach ($metafields as $v) {
		$table->addTableRow();
			$table->insertTableData($v["fieldname"], "", "header");
			$table->addTableData("", "data");
				$table->addCode($meta_output->meta_print_field($v));
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

/* business cards */
$venster_settings = array(
	"title" => gettext("business cards")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($businesscards);
	$view->addMapping("", "%%complex_actions");
	$view->addMapping(gettext("naam"), "%%name");
	$view->addMapping(gettext("telefoon/mobiel"), "%%complex_phone");

	/* first column in list holds action buttons */
	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "image",
			"src"     => "f_oud.gif",
			"alt"     => gettext("toggle sync"),
			"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'activate');"),
			"id"      => array("toggle_sync_", "%id"),
			"check"   => "%sync_no"
		),
		array(
			"type"    => "image",
			"src"     => "f_nieuw.gif",
			"alt"     => gettext("toggle sync"),
			"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'deactivate');"),
			"id"      => array("toggle_sync_", "%id"),
			"check"   => "%sync_yes"
		),
		array(
			"type"    => "action",
			"src"     => "info",
			"alt"     => gettext("meer informatie"),
			"link"    => array("javascript: popup('index.php?mod=address&action=cardshow&cardid=", "%id", "', 'view', 0, 0, 1);")
		),
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("bewerken"),
			"link"    => array("javascript: bcard_edit(", "%id", ");")
		),
		array(
			"type"    => "action",
			"src"     => "delete",
			"alt"     => gettext("verwijderen"),
			"link"    => array("javascript:if(confirm(gettext('Dit verwijderd de businesscard en alle koppelingen. Weet u dit zeker?'))){document.location.href='index.php?mod=address&action=relcard&id=$id&history=".$_REQUEST["history"]."&relcardaction=cardrem&cardid=", "%id", "';}")
		)
	));
	$view->defineComplexMapping("name", array(
		array(
			"type"  => "action",
			"src"   => "data_name"
		),
		array(
			"text"  => array(
				" ",
				"%alternative_name",
				"\n"
				),
			"check" => "%alternative_name"
		),
		array(
			"text"  => array(
				" ",
				"%givenname",
				" ",
				"%infix",
				" ",
				"%surname",
				"\n"
			)
		),
		array(
			"type"    => "action",
			"src"     => "data_business_email",
			"alt"     => gettext("zakelijk email adres"),
			"check"   => "%business_email"
		),
		array(
			"type"    => "link",
			"text"    => array(" ","%business_email"),
			"link"    => array("javascript: emailSelectFrom('", "%business_email", "','", "%address_id", "');"),
			"check"   => "%business_email"
		),
		array("text" => "\n"),
		array(
			"type"    => "action",
			"src"     => "data_private_email",
			"alt"     => gettext("prive email adres"),
			"check"   => "%personal_email"
		),
		array(
			"type"    => "link",
			"text"    => array(" ", "%personal_email"),
			"link"    => array("javascript: emailSelectFrom('", "%personal_email", "','", "%address_id", "');"),
			"check"   => "%personal_email"
		)
	));
	$view->defineComplexMapping("complex_phone", array(
		array(
			"type"    => "action",
			"src"     => "data_business_telephone",
			"alt"     => gettext("zakelijk telefoon nummer"),
			"check"   => "%business_phone_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%business_phone_nr_link",
				"\n"
			),
			"check" => "%business_phone_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_private_telephone",
			"alt"     => gettext("prive telefoon nummer"),
			"check"   => "%personal_phone_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%personal_phone_nr_link",
				"\n"
			),
			"check" => "%personal_phone_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_business_cellphone",
			"alt"     => gettext("zakelijk mobiel nummer"),
			"check"   => "%business_mobile_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%business_mobile_nr_link",
				"\n"
			),
			"check" => "%business_mobile_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_private_cellphone",
			"alt"     => gettext("prive mobiel nummer"),
			"check"   => "%personal_mobile_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%personal_mobile_nr_link",
				"\n"
			),
			"check" => "%personal_mobile_nr_link"
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
	$venster->insertAction("new", gettext("nieuwe businesscard"), "javascript: bcard_edit(0, $id);");
$venster->endVensterData();
$buf1->addCode($venster->generate_output());
$buf1->load_javascript(self::include_dir."sync4j.js");
unset($venster);

/* memo field */
$venster_settings = array(
	"title" => gettext("memo")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
	$venster->addVensterData();
		if (trim($addressinfo[0]["memo"])) {
			$venster->addCode(nl2br($addressinfo[0]["memo"]));
		} else {
			$venster->addCode(gettext("geen memo aanwezig"));
		}
	$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

/* project info */
$venster_settings = array("title" => gettext("projecten"));
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$pr_view = new Layout_view();
	$pr_view->addData($projectinfo);
	$pr_view->addMapping(gettext("naam"), "%%complex_name");
	$pr_view->addMapping(gettext("omschrijving"), "%description");
	$pr_view->addMapping(gettext("actief"), "%%complex_active");
	$pr_view->defineComplexMapping("complex_name", array(
		array(
			"type" => "link",
			"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%id"),
			"text" => "%name"
		)
	));
	$pr_view->defineComplexMapping("complex_active", array(
		array(
			"type"  => "action",
			"src"   => "delete",
			"check" => "%is_active"
		),
		array(
			"type"  => "action",
			"src"   => "ok",
			"check" => "%is_nonactive"
		)
	));
	$venster->addCode($pr_view->generate_output());
	unset($pr_view);
$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

/* support info */
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

/* note info */
$venster_settings = array(
	"title"    => gettext("notities"),
	"subtitle" => $subtitle_notes
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
	if ($_REQUEST["history"] == "notes") {
		$venster->insertLink(gettext("huidig"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
		));
		$venster->addSpace(3);
	} else {
		$venster->insertLink(gettext("historie"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=notes"
		));
		$venster->addSpace(3);
	}
	$venster->insertAction("new", gettext("maak notitie"), "javascript: new_note(".$id.");");
	$venster->insertLink(gettext("maak notitie"), array(
		"href" => "javascript: new_note(".$id.");"
	));
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
	if ($_REQUEST["history"] != "customercontact") {
		$view->addMapping("%%actions_header", "%%actions");
	}
	$view->defineComplexMapping("subject", array(
		array(
			"type"  => "link",
			"link"  => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 0, 0, 1);"),
			"text"  => "%subject",
		)
	));
	$view->defineComplexMapping("actions_header", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"alt"  => gettext("de geselecteerde klantcontacten archiveren"),
			"link" => "javascript: custcont_togglestate();"
		)
	));
	$view->defineComplexMapping("actions", array(
		array(
			"text" => $output->insertCheckbox(array("checkbox_custcont[", "%id", "]"), "1", 0, 1)
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
	if ($_REQUEST["history"] == "customercontact") {
		$venster->insertLink(gettext("huidig"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
		));
		$venster->addSpace(3);
	} else {
		$venster->insertLink(gettext("historie"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=customercontact"
		));
		$venster->addSpace(3);
	}
	$venster->insertAction("new", gettext("maak klantcontact"), "javascript: new_note(".$id.", 1);");
	$venster->insertLink(gettext("maak klantcontact"), array(
		"href" => "javascript: new_note(".$id.", 1);"
	));
$venster->endVensterData();
$buf2->addTag("form", array(
	"id"     => "custcont",
	"method" => "get",
	"action" => "index.php"
));
$buf2->addHiddenField("mod", "address");
$buf2->addHiddenField("action", "relcard");
$buf2->addHiddenField("id", $_REQUEST["id"]);
$buf2->addHiddenField("history", $_REQUEST["history"]);
$buf2->addHiddenField("relcardaction", "toggle_custcont");
$buf2->addCode($venster->generate_output());
$buf2->endTag("form");
unset($venster);

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
	$view->addMapping(gettext("gebruiker"), "%user_name");
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
				"link" => array("javascript: showcalitem(", "%id", ");"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);

		if ($_REQUEST["history"] == "calendar") {
			$venster->insertLink(gettext("huidig"), array(
				"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
			));
			$venster->addSpace(3);
		} else {
			$venster->insertLink(gettext("historie"), array(
				"href" => "index.php?mod=address&action=relcard&id=$id&history=calendar"
			));
			$venster->addSpace(3);
		}
		$venster->insertAction("new", gettext("maak agendapunt"), "javascript: new_calitem(".$id.");");
		$venster->insertLink(gettext("maak agendapunt"), array(
			"href" => "javascript: new_calitem(".$id.");"
		));


	$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* filesys link */
$venster_settings = array(
	"title" => gettext("bestandsbeheer")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
	$venster->addVensterData();
		$venster->insertAction("open", gettext("open bestandsmap van deze relatie"),
			"index.php?mod=filesys&action=opendir&id=".$filesysinfo
		);
		$venster->addSpace();
		$venster->insertTag("a", gettext("open bestandsmap van deze relatie"), array(
			"href" => "index.php?mod=filesys&action=opendir&id=".$filesysinfo
		));
	$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* sales link */
if ($GLOBALS["covide"]->license["has_sales"] && $user_data->checkPermission("xs_salesmanage")) {
	$venster_settings = array(
		"title" => gettext("sales"),
		"subtitle" => gettext("active sales items")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($salesinfo["data"]);

			/* add the mappings (columns) we needed */
			$view->addMapping(gettext("prospect"), "%h_timestamp_prospect");
			$view->addMapping(gettext("titel"), "%subject");
			$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
			$view->addMapping(gettext("bedrag"), "%total_sum", "right");

			$venster->addCode( $view->generate_output() );

			$venster->insertAction("go_sales", gettext("open sales map van deze relatie"),
				"index.php?mod=sales&action=list&search[address_id]=".$id
			);
			$venster->addSpace();
			$venster->insertTag("a", gettext("open sales map van deze relatie"), array(
				"href"=>"index.php?mod=sales&action=list&search[address_id]=".$id
			));

		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}
/* mortgage link */
if ($GLOBALS["covide"]->license["has_hypo"]) {
	$venster_settings = array(
		"title" => gettext("hypotheek"),
		"subtitle" => gettext("active hypotheek items")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($salesinfo["data"]);

			/* add the mappings (columns) we needed */
			$view->addMapping(gettext("soort"), "%h_type");
			$view->addMapping(gettext("datum"), "%h_timestamp");
			$view->addMapping(gettext("titel"), "%subject");
			$view->addMapping(gettext("bedrag"), "%total_sum", "right");

			$venster->addCode( $view->generate_output() );

			$venster->insertAction("go_mortgage", gettext("open hypotheek map van deze relatie"),
				"index.php?mod=mortgage&action=list&search[address_id]=".$id
			);
			$venster->addSpace();
			$venster->insertTag("a", gettext("open hypotheek map van deze relatie"), array(
				"href"=>"index.php?mod=mortgage&action=list&search[address_id]=".$id
			));

		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}

/* template link */
$venster_settings = array(
	"title" => gettext("templates")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);

	$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($templates_info["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("omschrijving"), "%description");
		$view->addMapping(gettext("datum"), "%date");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_address", array(
			array(
				"type"  => "link",
				"text"  => "%address",
				"link"  => array("?mod=address&action=relcard&id=", "%address_id")
			)
		));

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("bewerken"),
				"link"    => array("javascript: popup('index.php?mod=templates&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
			)
		));
		$venster->addCode( $view->generate_output() );
		$venster->insertAction("view_all", gettext("open template module"),
			"index.php?mod=templates&address_id=".$id
		);
		$venster->addSpace();
		$venster->insertTag("a", gettext("open template module"), array(
			"href" => "index.php?mod=templates&address_id=".$id
		));
	$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);


/* email link */
$venster_settings = array(
	"title" => gettext("email"),
	"subtitle" => gettext("emails van deze relatie in mijn eigen mappen")
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
	$view->addData($emailinfo["data"]);


	$view->addMapping("%%header_fromto", "%%data_fromto");
	$view->addMapping(gettext("datum"), "%%data_datum", "right");
	$view->addMapping(gettext("map"), "%folder_name", "right");

	$view->addSubMapping("%%data_subject", "%is_new");
	$view->addSubMapping("%%data_description", "");

	/* define complex header fromto */
	$view->defineComplexMapping("header_fromto", array(
		array(
			"text" => array(
				gettext("onderwerp"),
				"\n",
				gettext("afzender"),
				"/",
				gettext("ontvanger")
			)
		)
	));
	/* define complex data fromto */
	$view->defineComplexMapping("data_fromto", array(
		array(
			"text" => array(
				gettext("van"), ": ",	"%sender_emailaddress", "\n",
				gettext("aan"), ": ",	"%to"
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

		$venster->insertAction("go_email", gettext("open email archief van deze relatie"),
			"index.php?mod=email&action=list&address_id=".$id."&folder_id=".$emailarchive["id"]
		);
		$venster->addSpace();
		$venster->insertTag("a", gettext("open email archief van deze relatie"), array(
			"href" => "index.php?mod=email&action=list&address_id=".$id."&folder_id=".$emailarchive["id"]
		));
	$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);



$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();

if ($userperms["xs_turnovermanage"]) {
	if ($addressinfo[0]["is_supplier"] || $addressinfo[0]["is_customer"]) {
		if (file_exists("non-oop/relcard.php")) {
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2));
					$tbl->insertAction("down", gettext("resize"), "javascript: mail_resize_frame();");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2));
					$tbl->addTag("iframe", array(
						"id" => "turnoverinfo",
						"src" => "non-oop/relcard.php?klant_id=".$_REQUEST["id"],
						"border" => "0",
						"frameborder" => "0",
						"width" => "100%",
						"height" => "200px",
						"scrolling" => "no"
					));
					$tbl->endTag("iframe");
				$tbl->endTableData();
			$tbl->endTableRow();
		}
	}
}
$tbl->endTable();

$output->addCode($tbl->generate_output());
$output->load_javascript(self::include_dir."address_actions.js");
$output->load_javascript(self::include_dir."relcard_actions.js");

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );


$history = new Layout_history();
$output->addCode( $history->generate_save_state() );

/* back and top links */
$output->addTag("br");
$output->addTag("center");
$output->insertAction("back", gettext("terug"), "javascript: history_goback();");

$url = $_SERVER["QUERY_STRING"];
$output->insertAction("up", gettext("naar boven"), "?$url#top");
$output->endTag("center");


$output->layout_page_end();
echo $output->exit_buffer();
?>
