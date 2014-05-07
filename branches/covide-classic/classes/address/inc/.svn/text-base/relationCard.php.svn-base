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
$countryArray = $address_data->listCountries();

/* create rcbc if it's not there yet */
$address_data->checkrcbc($addressinfo[0]);

if ($userperms["xs_addressmanage"]) {
	$astrict = 1;
	$astrict_rw = 1;
} elseif ($GLOBALS["covide"]->license["address_strict_permissions"]) {

	$classification_data = new Classification_data();
	$cla_permission = $classification_data->getClassificationByAccess();

	/* get rw permissions for later use */
	$cla_address = explode("|", $addressinfo[0]["classifi"]);
	$cla_permission_rw = $classification_data->getClassificationByAccess(1);
	$cla_xs = array_intersect($cla_address, $cla_permission_rw);
	if (count($cla_xs) > 0)
		$astrict_rw = 1;

	$cla_xs = array_intersect($cla_address, $cla_permission);
	if (count($cla_xs) > 0)
		$astrict = 1;
} elseif ($addressinfo[0]["addressacc"] || $addressinfo[0]["addressmanage"]) {
	$astrict_rw = 1;
	$astrict = 1;
} else {
	$astrict_rw = 0;
	$astrict = 1;
}
$achange =& $astrict_rw;

if (!$astrict) {
	$output = new Layout_output();
	$output->layout_page("address");

	$venster = new Layout_venster(array(
		"title" => gettext("Relation Card"),
		"subtitle" => gettext("No permissions")
	));
	$venster->addVensterData();
		$venster->addCode(gettext("You have no permissions to access the following relation").": ");
		$venster->insertTag("b", $addressinfo[0]["companyname"]);
		$venster->addTag("br");

		$history = new Layout_history();
		$link = $history->generate_history_call();
		$venster->addCode($link);

		$venster->insertAction("back", gettext("back"), "javascript: history_goback();");
	$venster->endVensterData();

	$table = new Layout_table();

	$output->addCode($table->createEmptyTable($venster->generate_output()));
	$output->exit_buffer();
}
/* end access check */

/* we can taggle some stuff, so lets do that before we enter the rest */
if ($_REQUEST["relcardaction"] == "toggle_custcont" && is_array($_REQUEST["checkbox_custcont"])) {
	foreach ($_REQUEST["checkbox_custcont"] as $k=>$item) {
		$sql = sprintf("UPDATE notes SET is_done = 1, is_read = 1 WHERE (is_done !=1 OR is_done is null) AND id=%d", $k);
		$res = sql_query($sql);
	}
}
if ($_REQUEST["relcardaction"] == "cardrem") {
	$address_data->remove_bcard($_REQUEST["cardid"]);
}
if (is_array($addressinfo[0]["photo"]) && $addressinfo[0]["photo"]["size"]) {
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
//moved to getBcardsXML
//$businesscards  = $address_data->getBcardsByRelationID($id);


/* get the active notes for this relation */
$notes_data     = new Note_data();
if ($_REQUEST["history"] == "notes") {
	$noteinfo       = $notes_data->getNotesByContact($id, 0);
	$subtitle_notes = gettext("history");
} else {
	$noteinfo       = $notes_data->getNotesByContact($id, 1);
	$subtitle_notes = gettext("current");
}
/* get the active customer contact items for this relation */
if (!$GLOBALS["covide"]->license["disable_basics"]) {
	if ($_REQUEST["history"] == "customercontact") {
		$custcontinfo   = $notes_data->getNotesByContact($id, 0, 1);
	} else {
		$custcontinfo   = $notes_data->getNotesByContact($id, 1, 1);
	}
	/* get the appointments */
	/* TODO: Get all appointments and after that get the right users 
	attending them so we don't have to un-double */
	$calendar_data = new Calendar_data();
	if ($_REQUEST["history"] == "calendar") {
		$calendarinfo  =$calendar_data->getAppointmentsByAddress($id, 1);
	} else {
		$calendarinfo  =$calendar_data->getAppointmentsByAddress($id);
	}
	/* un-doubling (if there are appointments) */
	if (count($calendarinfo)) {
		$app_ids = array();
		foreach ($calendarinfo as $old_id => $cal) {
			if (!in_array($cal["id"], $app_ids)) {
				$app_ids[] = $cal["id"];
			} else {
				unset($calendarinfo[$old_id]);
			}
		}
	}
	/* get the filesys data */
	$filesys_data = new Filesys_data();
	$filesysinfo = $filesys_data->getRelFolder($id);
	$filesysdata = $filesys_data->getFolders(array("ids"=>$filesysinfo));
	/* get the email data */
	$email_data   = new Email_data();
	$emailinfo    = $email_data->getEmailBySearch( array("relation_inbox" => $id) );
	$emailarchive = $email_data->getSpecialFolder("Archief", 0);
	if ($GLOBALS["covide"]->license["has_sales"]) {
		if ($_REQUEST["history"] == "sales") {
			/* get sales data */
			$sales_data   = new Sales_data();
			$salesinfo    = $sales_data->getSalesBySearch( array("address_id"=>$id, "in_active"=>1) );
		} else {
			/* get sales data */
			$sales_data   = new Sales_data();
			$salesinfo    = $sales_data->getSalesBySearch( array("address_id"=>$id) );
		}
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
		foreach($projectinfo as $k=>$v) {
			if (!$v["allow_edit"]) {
				unset($projectinfo[$k]);
			}
		}
	}

	/* get the support data */
	$support_data = new Support_data();
	if ($_REQUEST["history"] == "support") {
		$supportinfo = $support_data->getSupportItems(array("address_id" => $id, "active" => 0, "nolimit" => 1));
		$subtitle_support = gettext("history");
	} else {
		$supportinfo = $support_data->getSupportItems(array("address_id" => $id, "active" => 1, "nolimit" => 1));
		$subtitle_support = gettext("current");
	}

	/* get the support data */
	$templates_data = new Templates_data();
	$templates_info = $templates_data->getTemplateBySearch($id, 0, 1);
}
/* get the active todos */
$todo_data      = new Todo_data();
$todoinfo       = $todo_data->getTodosByAddressId($id);


/* init the output object */
$output         = new Layout_output();
$output->layout_page("relationcard", $_REQUEST["hide"]);

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

/* address record */
$venster_settings = array(
	"title"    => gettext("information")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$history = new Layout_history();
	$link = $history->generate_history_call();
	$venster->addCode($link);

	$venster->insertAction("back", gettext("back"), sprintf(
		"javascript: history_goback('%d');" , $_REQUEST["restore_point_steps"]));

	if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
		$venster->insertAction(
			"view_all",
			gettext("template merge"),
			sprintf("javascript:popup('?mod=projectext&action=extGenerateDocumentTree&address_id=%d', 'samba', 700, 600, 1);", $_REQUEST["id"])
			);
	}

	$view = new Layout_view();
	$view->setHtmlField("phone_nr_link");
	$view->setHtmlField("mobile_nr_link");

	$view->addData($addressinfo);
	/* specify layout */
	if (!$addressinfo[0]["is_active"])
		$view->addMapping("&nbsp", "<span style=\"background-color: #d34545; color: white;\">&nbsp;".gettext("inactive")."&nbsp;</b>");
	$view->addMapping(gettext("company name"), "%%companyname");
	$view->addMapping(gettext("SSN"), "%bsn");
	$view->addMapping(gettext("birth date"), "%h_birthday");
	$view->addMapping(gettext("warning"), array("<b>", "%warning", "</b>"));
	$view->addMapping(gettext("picture"), "%%photo");
	$view->addMapping(gettext("debtor nr"), "%debtor_nr");
	$view->addMapping(gettext("addresstype"), "%%addresstype");
	$view->addMapping(gettext("bankaccount"), "%bankaccount");
	$view->addMapping(gettext("giroaccount"), "%giro");
	$view->addMapping(gettext("contact"), "%%tav");
	$view->addMapping(gettext("jobtitle"), "%jobtitle");
	$view->addMapping(gettext("address"), array("%address","\n","%address2"));
	$view->addMapping(gettext("zip code"), "%zipcode");
	$view->addMapping(gettext("city"), "%city");
	$view->addMapping(gettext("state/province"), "%state");
	$view->addMapping(gettext("country"), $countryArray[$addressinfo[0]["country"]]);
	$view->addMapping(gettext("streetmap"), "%%map");
	$view->addMapping(gettext("po box"), "%pobox");
	$view->addMapping(gettext("zip code po box"), "%pobox_zipcode");
	$view->addMapping(gettext("city po box"), "%pobox_city");
	$view->addMapping(gettext("telephone nr"), "%%phone_nr");
	$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr");
	$view->addMapping(gettext("fax nr"), "%fax_nr");
	$view->addMapping(gettext("email"), "%%email");
	$view->addMapping(gettext("website"), "%%website");
	$view->addMapping(gettext("classification(s)"), "%classification_names");
	$view->addMapping(gettext("account manager"), "%account_manager_name");
	$view->addMapping(gettext("last changed"), "%%last_changed");

	if ($achange) {
		$view->addMapping(gettext("search for communication items"), "%%complex_search");

		$view->defineComplexMapping("companyname", array(
			array(
				"type" => "link",
				"link" => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=relations', 'addressedit', 0, 0, 0);"),
				"text" => "%companyname"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"link" => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=relations', 'addressedit', 0, 0, 0);"),
				"alt" => gettext("alter")
			)
		));
	} else {
		$view->defineComplexMapping("companyname", array(
			array(
				"type" => "text",
				"text" => "%companyname"
			)
		));
	}
	$view->defineComplexMapping("addresstype", array(
		array(
			"type"  => "text",
			"text"  => array(" ", gettext("supplier")),
			"check" => "%is_supplier"
		),
		array(
			"type"  => "text",
			"text"  => array(" ", gettext("customer")),
			"check" => "%is_customer"
		),
		array(
			"type"  => "text",
			"text"  => array(" ", gettext("private")),
			"check" => "%is_person"
		)
	));
	$view->defineComplexMapping("last_changed", array(
		array(
			"type" => "text",
			"text" => array(gettext("last changed")," ", gettext("by")," ", "%changed_by_name"," ", gettext("on")," ", "%changed_human_date" ),
			"check" => "%changed_by_name"
		)
	));
	$view->defineComplexMapping("photo", array(
		array(
			"type" => "text",
			"text" => array("<img src=\"", "%photourl", "\">"),
			"check" => "%photourl"
		)
	));
	$view->defineComplexMapping("tav", array(
		array(
			"type" => "text",
			"text" => "%tav"
		),
		array(
			"type" => "text",
			"text" => array(" (", "%contact_givenname", ")"),
			"check" => "%contact_givenname"
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
			"link" => array("index.php?mod=index&search[address_id]=", "%id")
		),
		array(
			"text" => " "
		),
		array(
			"type" => "link",
			"text" => gettext("search for communication items"),
			"link" => array("index.php?mod=index&search[address_id]=", "%id")
		)
	));
	$view->defineComplexMapping("map", array(
		array(
			"type"   => "link",
			"text"   => gettext("show map"),
			"link"   => array("javascript: popup('index.php?mod=googlemaps&action=show_map&id=", "%id", "&location=", "%address", ", ", "%city", ", ", "%country", "', 'googlemap', 580, 650, 1);")
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
	if (!count($metafields)) {
		$table->addTableRow();
			$table->insertTableData(gettext("no items found"), "", "data");
		$table->endTableRow();
	}
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

if ($GLOBALS["covide"]->license["has_funambol"]) {
	$user_info_fb = $user_data->getUserDetailsById($_SESSION["user_id"]);
	$users = explode(",", $user_info_fb["addresssyncmanage"]);
	$sel = array(
		$_SESSION["user_id"] => $user_data->getUserNameById($_SESSION["user_id"])
	);

	/* create funambol object */
	$funambol_data = new Funambol_data();
	foreach ($users as $k=>$v) {
		if ($funambol_data->checkUserSyncState($v) === true)
			$sel[$v] = $user_data->getUserNameById($v);
	}

	$venster->addTag("form", array(
		"id"     => "deze",
		"method" => "get",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "address");
	$venster->addHiddenField("action", "relcard");
	$venster->addHiddenField("id", $_REQUEST["id"]);
	$venster->addHiddenField("history", $_REQUEST["history"]);
	$venster->addCode(gettext("sync user").": ");
	$venster->addSelectField("funambol_user", $sel, $_REQUEST["funambol_user"]);

	$venster->addSpace(5);
	$venster->addCode(gettext("search").": ");
	$venster->addTextField("bcard_search", "");
	$venster->insertAction("forward", gettext("search"), sprintf(
		"javascript: load_bcards('%d', document.getElementById('bcard_search').value, '%d');",
			$id, $_REQUEST["funambol_user"]
	));
	$venster->insertAction("toggle", gettext("show all"), sprintf(
		"javascript: document.getElementById('bcard_search').value = ''; load_bcards('%d', '', '%d');",
			$id, $_REQUEST["funambol_user"]
	));

	$venster->endTag("form");
	$venster->start_javascript();
		$venster->addCode("
			document.getElementById('funambol_user').onchange = function() {
				document.getElementById('deze').submit();
			}
		");
	$venster->end_javascript();
	$venster->addSpace(3);
}

	/* view here */
	$venster->insertTag("div", $this->getBcardsXML($id), array(
		"id" => "bcards_layer"
	));
	if ($achange)
		$venster->insertAction("new", gettext("new businesscard"), "javascript: bcard_edit(0, $id);");
$venster->endVensterData();
$buf1->addCode($venster->generate_output());
$buf1->load_javascript(self::include_dir."sync.js");
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
			$venster->addCode(gettext("no memo found"));
		}
	$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

/* project info */
if ($GLOBALS["covide"]->license["has_project"] && !$GLOBALS["covide"]->license["disable_basics"]) {
	if ($GLOBALS["covide"]->license["has_project_declaration"])
		$venster_settings = array("title" => gettext("declarations"));
	else
		$venster_settings = array("title" => gettext("projects"));
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$pr_view = new Layout_view();
		$pr_view->addData($projectinfo);
		$pr_view->addMapping(gettext("name"), "%%complex_name");
		$pr_view->addMapping(gettext("description"), "%description");
		$pr_view->addMapping(gettext("active"), "%%complex_active");
		$pr_view->defineComplexMapping("complex_name", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showhours&id=", "%id"),
				"text" => "%name"
			)
		));
		$pr_view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "enabled",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "disabled",
				"check" => "%is_nonactive"
			)
		));
		$venster->addCode($pr_view->generate_output());
		unset($pr_view);
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
}

/* sales overview for non-account managers */
$venster_settings = array(
	"title" => gettext("sales")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($salesinfo["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("prospect"), "%h_timestamp_prospect");
		$view->addMapping(gettext("title"), "%subject");
		$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
		$view->addMapping(gettext("price"), "%total_sum", "right");

		$venster->addCode( $view->generate_output() );
		$venster->endVensterData();
$buf1->addCode($venster->generate_output());
unset($venster);

/* support info */
if ($GLOBALS["covide"]->license["has_issues"] && !$GLOBALS["covide"]->license["disable_basics"]) {
	$venster_settings = array(
		"title"    => gettext("issues/support"),
		"subtitle" => $subtitle_support
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($supportinfo["items"]);

		$view->addMapping("&nbsp;", "%%complex_actions");
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("support request"), "%short_desc");
		$view->addMapping(gettext("dispatching"), "%short_sol");
		$view->addMapping(gettext("issuer"), "%sender_name");
		$view->addMapping(gettext("executor"), "%rcpt_name");


		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "info",
				"link" => array("javascript: popup('?mod=support&action=showitem&id=", "%id", "', 'support', 640, 500, 1);")
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		if ($_REQUEST["history"] == "support") {
			$venster->insertLink(gettext("current"), array(
				"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
			));
			$venster->addSpace(3);
		} else {
			$venster->insertLink(gettext("history"), array(
				"href" => "index.php?mod=address&action=relcard&id=$id&history=support"
			));
			$venster->addSpace(3);
		}
	$venster->insertAction("new", gettext("make support"), "javascript: new_support(".$id.");");
	$venster->insertLink(gettext("make support"), array(
		"href" => "javascript: new_support(".$id.");"
	));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}

/* note info */
$venster_settings = array(
	"title"    => gettext("notes"),
	"subtitle" => $subtitle_notes
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);

$actions = 0;
if ($user_data->checkPermission("xs_notemanage"))
	$actions = 1;

$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($noteinfo);
	$view->addMapping(gettext("date"), "%human_date");
	$view->addMapping(gettext("from"), "%from_name");
	$view->addMapping(gettext("to"), "%to_name");
	$view->addMapping(gettext("subject"), "%%subject");
	$view->defineComplexMapping("subject", array(
		array(
			"type" => "link",
			"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&actions=",$actions,"&msg_id=", "%id", "', 'shownote', 820, 400, 1);"),
			"text" => "%subject"
		)
	));
	$venster->addCode($view->generate_output());
	unset($view);
	if ($_REQUEST["history"] == "notes") {
		$venster->insertLink(gettext("current"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
		));
		$venster->addSpace(3);
	} else {
		$venster->insertLink(gettext("history"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=notes"
		));
		$venster->addSpace(3);
	}
	$venster->insertAction("new", gettext("make note"), "javascript: new_note(".$id.");");
	$venster->insertLink(gettext("make note"), array(
		"href" => "javascript: new_note(".$id.");"
	));
$venster->endVensterData();
$buf2->addCode($venster->generate_output());
unset($venster);

/* custcont info */
$venster_settings = array(
	"title"    => gettext("customer contact items")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($custcontinfo);
	$view->addMapping(gettext("date"), "%human_date");
	$view->addMapping(gettext("from"), "%from_name");
	$view->addMapping(gettext("to"), "%to_name");
	$view->addMapping(gettext("subject"), "%%subject");
	if ($_REQUEST["history"] != "customercontact") {
		$view->addMapping("%%actions_header", "%%actions");
	}
	$view->defineComplexMapping("subject", array(
		array(
			"type"  => "link",
			"link"  => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 820, 500, 1);"),
			"text"  => "%subject",
		)
	));
	$view->defineComplexMapping("actions_header", array(
		array(
			"type" => "action",
			"src"  => "toggle",
			"alt"  => gettext("archive selected customer contacts"),
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
		$venster->insertLink(gettext("current"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
		));
		$venster->addSpace(3);
	} else {
		$venster->insertLink(gettext("history"), array(
			"href" => "index.php?mod=address&action=relcard&id=$id&history=customercontact"
		));
		$venster->addSpace(3);
	}
	$venster->insertAction("new", gettext("create customer contact item"), "javascript: new_note(".$id.", 1);");
	$venster->insertLink(gettext("create customer contact item"), array(
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

if (!$GLOBALS["covide"]->license["disable_basics"]) {
	/* todo info */
	$venster_settings = array(
		"title"    => gettext("to do's"),
		"subtitle" => gettext("current")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($todoinfo);
		$view->addMapping(gettext("date"), "%desktop_time");
		$view->addMapping(gettext("user"), "%user_name");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->addMapping("", "%%complex_actions");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "link",
				"link" => array("javascript: loadXML('index.php?mod=todo&action=show_info&id=", "%id", "');"),
				"text" => "%subject"
			)
		));
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "info",
				"alt"  => gettext("information"),
				"link" => array("javascript: toonInfo(", "%id", ");")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: todo_delete(", "%id", ", 1);")
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("change:"),
				"link" => array("javascript: todo_edit(", "%id", ", 1);")
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"alt"  => gettext("plan in calendar"),
				"link" => array("javascript: todo_to_cal(", "%id", ");")
			),
		));

		$venster->addCode($view->generate_output());
		unset($view);
		$venster->insertAction("new", gettext("create todo"), "javascript: popup('?mod=todo&action=edit_todo&address_id=".$id."&hide=1');");
		$venster->insertLink(gettext("create todo"), array(
			"href" => "javascript: popup('?mod=todo&action=edit_todo&address_id=".$id."&hide=1');"
		));
	$venster->endVensterData();
	$venster->load_javascript(self::todo_include_dir."todo_actions.js");
	$buf2->addCode($venster->generate_output());
	unset($venster);

	/* calendar info */
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
					"type"  => "action",
					"src"   => "state_private",
					"alt"   => gettext("private appointment"),
					"check" => "%is_private"
				),
				array(
					"type" => "link",
					"link" => array("javascript: showcalitem(", "%id", ", ", "%user_id", ");"),
					"text" => "%subject"
				)
			));
			$venster->addCode($view->generate_output());
			unset($view);

			if ($_REQUEST["history"] == "calendar") {
				$venster->insertLink(gettext("current"), array(
					"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
				));
				$venster->addSpace(3);
			} else {
				$venster->insertLink(gettext("history"), array(
					"href" => "index.php?mod=address&action=relcard&id=$id&history=calendar"
				));
				$venster->addSpace(3);
			}
			$venster->insertAction("new", gettext("create calendar item"), "javascript: new_calitem(".$id.");");
			$venster->insertLink(gettext("create calendar item"), array(
				"href" => "javascript: new_calitem(".$id.");"
			));


		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);

	/* filesys link */
	$venster_settings = array(
		"title" => gettext("file management")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();
			$venster->insertAction("open", gettext("open filesystem folder of this contact"),
				"index.php?mod=filesys&action=opendir&id=".$filesysinfo
			);
			$venster->addSpace();
			$venster->insertTag("a", gettext("open filesystem folder of this contact"), array(
				"href" => "index.php?mod=filesys&action=opendir&id=".$filesysinfo
			));
			$venster->addSpace();
			$venster->insertTag("span","(". $filesysdata["data"]["0"]["filecount"]."/".$filesysdata["data"]["0"]["foldercount"].")");
		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);

	/* sales link */
	if ($GLOBALS["covide"]->license["has_sales"]) {
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
				$view->addMapping(gettext("title"), "%subject");
				$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
				$view->addMapping(gettext("price"), "%total_sum", "right");

				$venster->addCode( $view->generate_output() );
				if ($_REQUEST["history"] == "sales") {
					$venster->insertLink(gettext("current"), array(
						"href" => "index.php?mod=address&action=relcard&id=$id&history=nothing"
					));
					$venster->addSpace(3);
				} else {
					$venster->insertLink(gettext("history"), array(
						"href" => "index.php?mod=address&action=relcard&id=$id&history=sales"
					));
					$venster->addSpace(3);
				}
				$venster->insertAction("go_sales", gettext("open sales folder of this contact"),
					"index.php?mod=sales&action=list&search[address_id]=".$id
				);
				$venster->addSpace();
				$venster->insertTag("a", gettext("open sales folder of this contact"), array(
					"href"=>"index.php?mod=sales&action=list&search[address_id]=".$id
				));

			$venster->endVensterData();
		$buf2->addCode($venster->generate_output());
		unset($venster);
	}
	/* mortgage link */
	if ($GLOBALS["covide"]->license["has_hypo"]) {
		$venster_settings = array(
			"title" => gettext("mortgage"),
			"subtitle" => gettext("active morgage items")
		);
		$venster = new Layout_venster($venster_settings);
		unset($venster_settings);
			$venster->addVensterData();

				$view = new Layout_view();
				$view->addData($salesinfo["data"]);

				/* add the mappings (columns) we needed */
				$view->addMapping(gettext("type"), "%h_type");
				$view->addMapping(gettext("date"), "%h_timestamp");
				$view->addMapping(gettext("title"), "%subject");
				$view->addMapping(gettext("price"), "%total_sum", "right");

				$venster->addCode( $view->generate_output() );

				$venster->insertAction("go_mortgage", gettext("open mortgage folder of this contact"),
					"index.php?mod=mortgage&action=list&search[address_id]=".$id
				);
				$venster->addSpace();
				$venster->insertTag("a", gettext("open mortgage folder of this contact"), array(
					"href"=>"index.php?mod=mortgage&action=list&search[address_id]=".$id
				));

			$venster->endVensterData();
		$buf2->addCode($venster->generate_output());
		unset($venster);
	}

	/* template link */
	$venster_settings = array(
		"title" => gettext("letters by templates")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);

		$venster->addVensterData();

			$view = new Layout_view();
			$view->addData($templates_info["data"]);

			/* add the mappings (columns) we needed */
			$view->addMapping(gettext("type"), "%%complex_type");
			$view->addMapping(gettext("description"), "%%complex_description");
			$view->addMapping(gettext("date and city"), "%datecity");
			$view->addMapping("", "%%complex_actions");

			$view->defineComplexMapping("complex_description", array(
				array(
					"type"    => "link",
					"text"    => "%description",
					"link"    => array("javascript: popup('index.php?mod=templates&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
				)
			));

			$view->defineComplexMapping("complex_type", array(
				array(
					"type"  => "action",
					"src"   => "addressbook",
					"alt"   => gettext("single address"),
					"check" => "%icon_single"
				),
				array(
					"type"  => "action",
					"src"   => "state_public",
					"alt"   => gettext("businesscard"),
					"check" => "%icon_bcard"
				),
				array(
					"type"  => "action",
					"src"   => "state_multiple",
					"alt"   => gettext("multiple addresses"),
					"check" => "%icon_multi"
				)
			));

			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"    => "action",
					"src"     => "edit",
					"alt"     => gettext("edit"),
					"link"    => array("javascript: popup('index.php?mod=templates&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
				),
				array(
					"type"    => "action",
					"src"     => "delete",
					"alt"     => gettext("delete"),
					"link"    => array("?mod=templates&action=delete&back_address_id=", $id, "&id=", "%id"),
					"confirm" => gettext("Are you sure you want to remove this template?")
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
			$venster->addSpace();
			$venster->insertAction("new", gettext("new letter for this relation"),
				"javascript: popup('index.php?mod=templates&action=edit&address_id=$id', 'template', 960, 600, 1);"
			);
			$venster->addSpace();
			$venster->insertTag("a", gettext("new letter for this relation"), array(
				"href" => "javascript: popup('index.php?mod=templates&action=edit&address_id=$id', 'template', 960, 600, 1);"
			));

		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);

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
		$view->addData($emailinfo["data"]);


		$view->addMapping("%%header_fromto", "%%data_fromto");
		$view->addMapping(gettext("date"), "%%data_datum", "right");
		$view->addMapping(gettext("folder"), "%folder_name", "right");

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
					gettext("from"), ": ", "%sender_emailaddress", "\n",
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

			$venster->insertAction("go_email", gettext("open email archive of this contact"),
				"index.php?mod=email&action=list&address_id=".$id."&folder_id=".$emailarchive["id"]
			);
			$venster->addSpace();
			$venster->insertTag("a", gettext("open email archive of this contact"), array(
				"href" => "index.php?mod=email&action=list&address_id=".$id."&folder_id=".$emailarchive["id"]
			));
		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
}

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	if ($achange)
		$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();

/* finance */
if ($userperms["xs_turnovermanage"] && !$GLOBALS["covide"]->license["disable_basics"] && $addressinfo[0]["debtor_nr"]) {
	$finance_output = new Finance_output();
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => 2));
			$tbl->addCode( $finance_output->relationCard($_REQUEST["id"], $_REQUEST["finance_toggle"], $_REQUEST["finance_history"]) );
		$tbl->endTableData();
	$tbl->endTableRow();
}

if (($GLOBALS["covide"]->license["has_finance"] && $userperms["xs_turnovermanage"]) && !$GLOBALS["covide"]->license["disable_basics"]) {

	if ($addressinfo[0]["is_supplier"] || $addressinfo[0]["is_customer"]) {
		/*
		$finance_output = new Finance_output();

		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2));
				$tbl->addCode( $finance_output->relationCard($_REQUEST["id"]) );
			$tbl->endTableData();
		$tbl->endTableRow();
		*/

		if (file_exists("non-oop/relcard.php")) {
			/*
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2));
					$tbl->insertAction("down", gettext("resize"), "javascript: mail_resize_frame();");
				$tbl->endTableData();
			$tbl->endTableRow();
			*/
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2));
					$tbl->addTag("iframe", array(
						"id" => "turnoverinfo",
						"src" => "non-oop/relcard.php?klant_id=".$_REQUEST["id"],
						"border" => "0",
						"frameborder" => "0",
						"width" => "100%",
						"height" => "20px",
						"scrolling" => "no"
					));
					$tbl->endTag("iframe");
				$tbl->endTableData();
			$tbl->endTableRow();
		}
	}
}
if ($GLOBALS["covide"]->license["has_twinfield"]) {
	for ($i=date("Y"); $i>=date("Y")-2; $i--) {
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => "2"));
			$tw_frame = new Layout_venster(array("title" => "Twinfield", "subtitle" => $i));
			$tw_frame->addVensterData();
			$twinfield_data = new twinfield_data();
			$twinfield_data->office = $addressinfo[0]["twinfield_office"];
			$findata = $twinfield_data->getFinancialsById($addressinfo[0]["debtor_nr"], "$i", 1);
			$twtable = new Layout_table(array("width"=>"100%"));
			$twtable->addTableRow();
				$twtable->insertTableData(gettext("Dagboek"), "", "header");
				$twtable->insertTableData(gettext("Boekstuk Nummer"), "", "header");
				$twtable->insertTableData(gettext("Factuur Nummer"), "", "header");
				$twtable->insertTableData(gettext("Totaal bedrag"), "", "header");
				$twtable->insertTableData(gettext("Nog te betalen"), "", "header");
			$twtable->endTableRow();
			$twtable->addCode($findata);
			$twtable->endTable();
			$tw_frame->addCode($twtable->generate_output());
			$tw_frame->endVensterData();
			$tbl->addCode($tw_frame->generate_output());
		$tbl->endTableData();
	$tbl->endTableRow();
	}
}
$tbl->endTable();
$output->addCode($tbl->generate_output());
$output->load_javascript(self::include_dir."address_actions.js");
$output->load_javascript(self::include_dir."relcard_actions.js");

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );


$history = new Layout_history();
if (!$_REQUEST["restore_point_steps"])
	$output->addCode( $history->generate_save_state() );

/* back and top links */
$output->addTag("br");
$output->addTag("center");

$output->insertAction("back", gettext("back"), sprintf(
	"javascript: history_goback('%d');" , $_REQUEST["restore_point_steps"]));

$url = $_SERVER["QUERY_STRING"];
$output->insertAction("up", gettext("up"), "?$url#top");
$output->endTag("center");


$output->layout_page_end();
echo $output->exit_buffer();
?>
