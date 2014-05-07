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

/* init address data object */
$address_data = new Address_data();
/* get array of possible letterheads */
$letterheads = $address_data->getLetterheads();
/* get array of possible commencements */
$commencements = $address_data->getCommencements();
/* get array of possible titles */
$titles = $address_data->getTitles();
/* get array of possible suffix options */
$suffix = $address_data->getSuffix();


//TODO: use string padding
$days[0] = "---";
for ($a=1; $a<=31; $a++) {
	if ($a<10) {
		$days[$a] = "0".$a;
	} else {
		$days[$a] = $a;
	}
}
$months[0] = "---";
for ($a=1; $a<=12; $a++) {
	if ($a<10) {
		$months[$a] = "0".$a;
	} else {
		$months[$a] = $a;
	}
}
$years[0] = "---";
for ($a=date("Y")-100; $a<=date("Y"); $a++) {
	$years[$a] = $a;
}


/* split src_id */
if ($src_id) {
	if (preg_match("/^b/s", $src_id)) {
		$src_type = "business";
		$src_id = (int)preg_replace("/^b/s", "", $src_id);
	} elseif (preg_match("/^p/s", $src_id)) {
		$src_type = "private";
		$src_id = (int)preg_replace("/^p/s", "", $src_id);
	} else {
		unset($src_id);
	}
}

if ($id) {
	/* fetch bcard we wanna edit */
	$bcardinfo = $address_data->getAddressById($id, "bcards");
	$subtitle  = ($view_only) ? gettext("view"):gettext("change");
} elseif ($src_id) {

	/* get the address data if we want to convert */
	$bcardinfo = $address_data->getAddressById($src_id, "private");
	$subtitle = gettext("convert");

	unset($bcardinfo["id"]);

	/* use private fields */
	$bcardinfo["personal_address"]   =& $bcardinfo["address"];
	$bcardinfo["personal_zipcode"]   =& $bcardinfo["zipcode"];
	$bcardinfo["personal_city"]      =& $bcardinfo["city"];
	$bcardinfo["personal_state"]     =& $bcardinfo["state"];
	$bcardinfo["personal_mobile_nr"] =& $bcardinfo["mobile_nr"];
	$bcardinfo["personal_phone_nr"]  =& $bcardinfo["phone_nr"];
	$bcardinfo["personal_phone_nr_2"]=& $bcardinfo["phone_nr_2"];
	$bcardinfo["personal_email"]     =& $bcardinfo["email"];
	$bcardinfo["personal_fax_nr"]    =& $bcardinfo["fax_nr"];
	$bcardinfo["personal_state"]     =& $bcardinfo["state"];
	$bcardinfo["personal_country"]   =& $bcardinfo["country"];
	$bcardinfo["initials"]           =& $bcardinfo["contact_initials"];
	$bcardinfo["letterhead"]         =& $bcardinfo["contact_letterhead"];
	$bcardinfo["commencement"]       =& $bcardinfo["contact_commencement"];

	/* get sync state */
	$funambol_data = new Funambol_data();
	$sync_state = $funambol_data->checkPrivateAddressSyncState($src_id);

} else {
	/* new bcard */
	$bcardinfo["id"]         = 0;
	$bcardinfo["address_id"] = $address_id;
	$subtitle                = gettext("create");
}
if (strlen(trim($bcardinfo["multirel"]))) {
	$addressids = explode(",", $bcardinfo["multirel"]);
	$bcardinfo["multirel"] = array(
		$bcardinfo["address_id"] => $address_data->getAddressNameById($bcardinfo["address_id"])
	);
	foreach ($addressids as $aid) {
		$bcardinfo["multirel"][$aid] = $address_data->getAddressNameById($aid);
	}
} else {
	$bcardinfo["multirel"] = array(
		$bcardinfo["address_id"] => $address_data->getAddressNameById($bcardinfo["address_id"])
	);
}
unset($bcardinfo["address_id"]);

/* start output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id" => "editbcard",
		"method" => "post",
		"action" => "index.php",
		"enctype" => "multipart/form-data"
	));

	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "save_bcard");
	$output->addHiddenField("bcard[src_id]", $src_id);
	$output->addHiddenField("bcard[src_user]", $bcardinfo["user_id"]);
	$output->addHiddenField("bcard[src_sync]", $sync_state);
	$output->addHiddenField("bcard[id]", $bcardinfo["id"]);
	$venster = new Layout_venster(array(
		"title"    => gettext("businesscard"),
		"subtitle" => $subtitle
	));
	$venster->addVensterData();

		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		if ($view_only)
			$table->setInputReadonly(1);

		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_information", 1);
				$table->insertAction("state_special", "", "");
				$table->addSpace();
				$table->addCode(gettext("information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("free field"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[alternative_name]", $bcardinfo["alternative_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("birth date"), "", "header");
			$table->addTableData("", "data");
				if ($bcardinfo["timestamp_birthday"]) {
					$table->addSelectField("bcard[bday_day]", $days, date("d", $bcardinfo["timestamp_birthday"]));
					$table->addSelectField("bcard[bday_month]", $months, date("m", $bcardinfo["timestamp_birthday"]));
					$table->addSelectField("bcard[bday_year]", $years, date("Y", $bcardinfo["timestamp_birthday"]));
				} else {
					$table->addSelectField("bcard[bday_day]", $days, 0);
					$table->addSelectField("bcard[bday_month]", $months, 0);
					$table->addSelectField("bcard[bday_year]", $years, 0);
				}
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("letterhead"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[letterhead]", $letterheads, $bcardinfo["letterhead"]);
			$table->endTableData();
			$table->insertTableData(gettext("commencement"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[commencement]", $commencements, $bcardinfo["commencement"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("title"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[title]", $titles, $bcardinfo["title"]);
			$table->endTableData();
			$table->insertTableData(gettext("suffix"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[suffix]", $suffix, $bcardinfo["suffix"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("initials"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[initials]", $bcardinfo["initials"]);
			$table->endTableData();
			$table->insertTableData(gettext("given name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[givenname]", $bcardinfo["givenname"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("insertion"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[infix]", $bcardinfo["infix"]);
			$table->endTableData();
			$table->insertTableData(gettext("last name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[surname]", $bcardinfo["surname"]);
			$table->endTableData();
		$table->endTableRow();


		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_company_1", 1);
				$table->insertAction("addressbook", "", "");
				$table->addSpace();
				$table->addCode(gettext("company information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("job title"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[jobtitle]", $bcardinfo["jobtitle"]);
			$table->endTableData();
			$table->insertTableData(gettext("locationcode"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[locationcode]", $bcardinfo["locationcode"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("business unit"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("bcard[businessunit]", $bcardinfo["businessunit"]);
			$table->endTableData();
			$table->insertTableData(gettext("department"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("bcard[department]", $bcardinfo["department"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("manager name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_manager_name]", $bcardinfo["opt_manager_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("profession"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_profession]", $bcardinfo["opt_profession"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("assisant name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_assistant_name]", $bcardinfo["opt_assistant_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("assistant phone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_assistant_phone_nr]", $bcardinfo["opt_assistant_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("website"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				if ($view_only) {
					if (!preg_match("/^http(s){0,1}/si", $bcardinfo["website"]))
						$bcardinfo["website"] = sprintf("http://%s", $bcardinfo["website"]);

					$table->addCode(sprintf("<a href='%1\$s' target='_blank'>%1\$s</a>", $bcardinfo["website"]));
				} else {
					$table->addTextField("bcard[website]", $bcardinfo["website"], array(
						"style" => "width: 400px;"
					));
				}
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("linked relations"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addHiddenField("bcard[address_id]", $bcardinfo["address_id"]);
				$table->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				if (!$view_only)
					$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();


		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_company_2", 0);
				$table->insertAction("addressbook", "", "");
				$table->addSpace();
				$table->addCode(gettext("extra company information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_2"));
			$table->insertTableData(gettext("company name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_company_name]", $bcardinfo["opt_company_name"]);
			$table->endTableData();
			$table->addTableData(array("rowspan" => 2, "colspan" => 2), "data");
				$table->addCode(gettext("only if information differs from relation card"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_company_2"));
			$table->insertTableData(gettext("company phone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_company_phone_nr]", $bcardinfo["opt_company_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();



		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();


		/* business address */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_business", 1);
				$table->insertAction("state_public", "", "");
				$table->addSpace();
				$table->addCode(gettext("business address"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_address]", $bcardinfo["business_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["business_phone_nr"] = $address_data->show_phonenr($bcardinfo["business_phone_nr"]);
				$table->addTextField("bcard[business_phone_nr]", $bcardinfo["business_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_city]", $bcardinfo["business_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["business_phone_nr_2"] = $address_data->show_phonenr($bcardinfo["business_phone_nr_2"]);
				$table->addTextField("bcard[business_phone_nr_2]", $bcardinfo["business_phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_state]", $bcardinfo["business_state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_fax_nr]", $bcardinfo["business_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_zipcode]", $bcardinfo["business_zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["business_mobile_nr"] = $address_data->show_phonenr($bcardinfo["business_mobile_nr"]);
				$table->addTextField("bcard[business_mobile_nr]", $bcardinfo["business_mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_country]", $bcardinfo["business_country"]);
			$table->endTableData();
			$table->insertTableData(gettext("car phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["business_car_phone"] = $address_data->show_phonenr($bcardinfo["business_car_phone"]);
				$table->addTextField("bcard[business_car_phone]", $bcardinfo["business_car_phone"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $bcardinfo["business_email"]));
				else
					$table->addTextField("bcard[business_email]", $bcardinfo["business_email"], array(
						"style" => "width: 400px;"
					));
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* pobox info */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_custom_nr", 0);
				$table->insertAction("help", "", "");
				$table->addSpace();
				$table->addCode(gettext("custom business phone numbers"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_custom_nr"));
			$table->insertTableData(gettext("callback phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["opt_callback_phone_nr"] = $address_data->show_phonenr($bcardinfo["opt_callback_phone_nr"]);
				$table->addTextField("bcard[opt_callback_phone_nr]", $bcardinfo["opt_callback_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("pager number"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["opt_pager_number"] = $address_data->show_phonenr($bcardinfo["opt_pager_number"]);
				$table->addTextField("bcard[opt_pager_number]", $bcardinfo["opt_pager_number"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_custom_nr"));
			$table->insertTableData(gettext("radio phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["opt_radio_phone_nr"] = $address_data->show_phonenr($bcardinfo["opt_radio_phone_nr"]);
				$table->addTextField("bcard[opt_radio_phone_nr]", $bcardinfo["opt_radio_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("telex nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[opt_telex_number]", $bcardinfo["opt_telex_number"]);
			$table->endTableData();
		$table->endTableRow();


		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* pobox info */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_pobox", 0);
				$table->insertAction("view_all", "", "");
				$table->addSpace();
				$table->addCode(gettext("pobox information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_pobox"));
			$table->insertTableData(gettext("pobox"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[pobox]", $bcardinfo["pobox"]);
			$table->endTableData();
			$table->insertTableData(gettext("pobox zipcode"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[pobox_zipcode]", $bcardinfo["pobox_zipcode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_pobox"));
			$table->insertTableData(gettext("pobox city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[pobox_city]", $bcardinfo["pobox_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("pobox country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[pobox_country]", $bcardinfo["pobox_country"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_pobox"));
			$table->insertTableData(gettext("pobox state"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[pobox_state]", $bcardinfo["pobox_state"]);
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* private address */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_private", 0);
				$table->insertAction("state_private", "", "");
				$table->addSpace();
				$table->addCode(gettext("private address"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_address]", $bcardinfo["personal_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["personal_phone_nr"] = $address_data->show_phonenr($bcardinfo["personal_phone_nr"]);
				$table->addTextField("bcard[personal_phone_nr]", $bcardinfo["personal_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_city]", $bcardinfo["personal_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["personal_phone_nr_2"] = $address_data->show_phonenr($bcardinfo["personal_phone_nr_2"]);
				$table->addTextField("bcard[personal_phone_nr_2]", $bcardinfo["personal_phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_state]", $bcardinfo["personal_state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_fax_nr]", $bcardinfo["personal_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_zipcode]", $bcardinfo["personal_zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["personal_mobile_nr"] = $address_data->show_phonenr($bcardinfo["personal_mobile_nr"]);
				$table->addTextField("bcard[personal_mobile_nr]", $bcardinfo["personal_mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_country]", $bcardinfo["personal_country"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $bcardinfo["personal_email"]));
				else
					$table->addTextField("bcard[personal_email]", $bcardinfo["personal_email"]);
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* other address */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_other", 0);
				$table->insertAction("state_special", "", "");
				$table->addSpace();
				$table->addCode(gettext("other address"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_address]", $bcardinfo["other_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["other_phone_nr"] = $address_data->show_phonenr($bcardinfo["other_phone_nr"]);
				$table->addTextField("bcard[other_phone_nr]", $bcardinfo["other_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_city]", $bcardinfo["other_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["other_phone_nr_2"] = $address_data->show_phonenr($bcardinfo["other_phone_nr_2"]);
				$table->addTextField("bcard[other_phone_nr_2]", $bcardinfo["other_phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_state]", $bcardinfo["other_state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_fax_nr]", $bcardinfo["other_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_zipcode]", $bcardinfo["other_zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$bcardinfo["other_mobile_nr"] = $address_data->show_phonenr($bcardinfo["other_mobile_nr"]);
				$table->addTextField("bcard[other_mobile_nr]", $bcardinfo["other_mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[other_country]", $bcardinfo["other_country"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $bcardinfo["other_email"]));
				else
					$table->addTextField("bcard[other_email]", $bcardinfo["other_email"]);
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* other address */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_classifications", 1);
				$table->insertAction("toggle", "", "");
				$table->addSpace();
				$table->addCode(gettext("classfication information"));
			$table->endTableData();
		$table->endTableRow();


		$table->addTableRow(array("class" => "expand_classifications"));
			$table->insertTableData(gettext("classification"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addHiddenField("bcard[classification]", $bcardinfo["classification"]);
				$table->endTag("span");
				$classification = new Classification_output();
				if (!$view_only)
					$table->addCode( $classification->classification_selection("bcardclassification", $bcardinfo["classification"]) );

			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		/* other address */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_options", 0);
				$table->insertAction("toggle", "", "");
				$table->addSpace();
				$table->addCode(gettext("other information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_options"));
			$table->insertTableData(gettext("memo"), "", "header");
			$table->addTabledata(array("colspan"=>3), "data");
				$table->addTextArea("bcard[memo]", $bcardinfo["memo"], array("style" => "width: 400px; height: 100px;"));
			$table->endTableData();
		$table->endTableRow();

		if ($bcardinfo["id"] && !$view_only) {
			/* show photo with delete link, or field to upload new photo */
			$table->addTableRow(array("class" => "expand_options"));
				$table->insertTableData(gettext("picture"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					if ($bcardinfo["photo"]["size"]) {
						$url = "index.php?mod=address&action=showrelimg&addresstype=bcards";
						foreach ($bcardinfo["photo"] as $k=>$v) {
							$url .= "&photo[$k]=$v";
						}
						$table->addTag("img", array(
							"src" => $url,
							"border" => 0,
							"alt" => $bcardinfo["photo"]["name"]
						));
						$table->insertAction("delete", gettext("delete"), "javascript: remove_img(".$bcardinfo["id"].");");
					} else {
						$table->addUploadField("bcard[binphoto]");
					}
				$table->endTableData();
			$table->endTableRow();
			/* meta fields */
			$table->addTableRow(array("class" => "expand_options"));
				$table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
			$table->endTableRow();
			$metadata   = new Metafields_data();
			$metaoutput = new Metafields_output();
			$metafields = $metadata->meta_list_fields("bcards", $bcardinfo["id"]);
			foreach ($metafields as $v) {
				$table->addTableRow(array("class" => "expand_options"));
					$table->insertTableData($v["fieldname"], "", "header");
					$table->addTableData(array("colspan" => 3), "data");
						$table->addCode($metaoutput->meta_format_field($v));
						$table->insertAction("delete", gettext("remove"), "javascript: remove_meta(".$v["id"].");");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow(array("class" => "expand_options"));
				$table->insertTableData("", "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->insertAction("new", gettext("add"), "javascript: add_meta('bcards', ".$bcardinfo["id"].");");
				$table->endTableData();
			$table->endTableRow();
		}
		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData(array("colspan"=>3), "data");
				if ($src_id)
					$table->insertAction("back", gettext("back"), "javascript: history.go(-1);");
				else
					$table->insertAction("close", gettext("close"), "javascript: window.close();");

				if ($bcardinfo["id"] && !$view_only)
					$table->insertAction("delete", gettext("delete"), "javascript: bcard_delete(".$bcardinfo["id"].");");

				if (!$view_only)
					$table->insertAction("save", gettext("save"), "javascript: bcard_save();");

			$table->endTableData();
		$table->endTableRow();
		/* end table, attach to output buffer */
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");

	$output->load_javascript(self::include_dir."bcard_actions.js");
	$output->start_javascript();
	foreach ($bcardinfo["multirel"] as $aid=>$aname) {
		if ($aid)
			$output->addCode("addLoadEvent(selectRel($aid, '".addslashes($aname)."'));\n");
	}
	$output->end_javascript();
/* end output buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
