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
$titles = $address_data->getTitles(0,1);
foreach($titles AS $keys => $values) {
	$newTitles[] = $values["title"];
}

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

/* this file is always private */
$type = "private";

if ($id) {
	/* get the address data if we get an id. */
	$address_info = $address_data->getAddressById($id, $type, $sub);

} else {
	/* otherwise set default values */
	$address_info = array();
	$address_info["id"] = 0;
	$address_info["user_id"] = $_SESSION["user_id"];
	$address_info["is_active"] = 1;
	$address_info["is_public"] = 1;
}

/* if address is new, set account manager to the current user */
if (!$id) {
	/* set permissions to me if script permissions are active */
	if ($GLOBALS["covide"]->license["address_strict_permissions"])
		$address_info["account_manager"] = $_SESSION["user_id"];

	$address_info["is_active"] = 1;
	$address_info["is_public"] = 1;
}


if ($GLOBALS["covide"]->license["has_hrm"]) {
	/* get arbo addresses */
	$sql = "SELECT * FROM address_other WHERE arbo_kantoor = 1";
	$res = sql_query($sql);
	$arbos = array(0 => gettext("none"));
	while ($row = sql_fetch_assoc($res)) {
		$arbos[$row["id"]] = $row["companyname"];
	}
}

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

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("alter address"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("addresses"),
	"subtitle" => ($view_only) ? gettext("view address"):gettext("alter address")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "addressedit",
		"action" => "index.php",
		"method" => "post",
		"enctype" => "multipart/form-data"
	));
	$venster->addHiddenField("mod", "address");
	$venster->addHiddenField("action", "save");
	$venster->addHiddenField("address[id]", $address_info["id"]);
	$venster->addHiddenField("address[src_id]", $src_id);
	$venster->addHiddenField("address[type]", $type);
	if ($type == "overig") {
		if (!$sub) $sub = "kantoor";
		$venster->addHiddenField("address[sub]", $sub);
	}
	$venster->addHiddenField("address[user_id]", $address_info["user_id"]);

		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		if ($view_only)
			$table->setInputReadOnly(1);

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
				$table->addTextField("address[alternative_name]", $address_info["alternative_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("birth date"), "", "header");
			$table->addTableData("", "data");
				if ($address_info["timestamp_birthday"]) {
					$table->addSelectField("address[bday_day]", $days, date("d", $address_info["timestamp_birthday"]));
					$table->addSelectField("address[bday_month]", $months, date("m", $address_info["timestamp_birthday"]));
					$table->addSelectField("address[bday_year]", $years, date("Y", $address_info["timestamp_birthday"]));
				} else {
					$table->addSelectField("address[bday_day]", $days, 0);
					$table->addSelectField("address[bday_month]", $months, 0);
					$table->addSelectField("address[bday_year]", $years, 0);
				}
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("letterhead"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[contact_letterhead]", $letterheads, $address_info["contact_letterhead"]);
			$table->endTableData();
			$table->insertTableData(gettext("commencement"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[contact_commencement]", $commencements, $address_info["contact_commencement"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("title"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[title]", $newTitles, $address_info["title"]);
			$table->endTableData();
			$table->insertTableData(gettext("suffix"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[suffix]", $suffix, $address_info["suffix"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("initials"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_initials]", $address_info["contact_initials"]);
			$table->endTableData();
			$table->insertTableData(gettext("given name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[givenname]", $address_info["givenname"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_information"));
			$table->insertTableData(gettext("insertion"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[infix]", $address_info["infix"]);
			$table->endTableData();
			$table->insertTableData(gettext("last name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[surname]", $address_info["surname"]);
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
				$table->expandCollapse("expand_private", 1);
				$table->insertAction("state_private", "", "");
				$table->addSpace();
				$table->addCode(gettext("private address"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[address]", $address_info["address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["phone_nr"] = $address_data->show_phonenr($address_info["phone_nr"]);
				$table->addTextField("address[phone_nr]", $address_info["phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[city]", $address_info["city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["phone_nr_2"] = $address_data->show_phonenr($address_info["phone_nr_2"]);
				$table->addTextField("address[phone_nr_2]", $address_info["phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[state]", $address_info["state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[fax_nr]", $address_info["fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[zipcode]", $address_info["zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["mobile_nr"] = $address_data->show_phonenr($address_info["mobile_nr"]);
				$table->addTextField("address[mobile_nr]", $address_info["mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_private"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[country]", $address_info["country"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $address_info["email"]));
				else
					$table->addTextField("address[email]", $address_info["email"]);
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
				$table->expandCollapse("expand_company_1", 0);
				$table->insertAction("addressbook", "", "");
				$table->addSpace();
				$table->addCode(gettext("company information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("job title"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[jobtitle]", $address_info["jobtitle"]);
			$table->endTableData();
			$table->insertTableData(gettext("locationcode"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[locationcode]", $address_info["locationcode"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("business unit"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("address[businessunit]", $address_info["businessunit"]);
			$table->endTableData();
			$table->insertTableData(gettext("department"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("address[department]", $address_info["department"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("manager name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[opt_manager_name]", $address_info["opt_manager_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("profession"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[opt_profession]", $address_info["opt_profession"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("assisant name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[opt_assistant_name]", $address_info["opt_assistant_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("assistant phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["opt_assistant_phone_nr"] = $address_data->show_phonenr($address_info["opt_assistant_phone_nr"]);
				$table->addTextField("address[opt_assistant_phone_nr]", $address_info["opt_assistant_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_1"));
			$table->insertTableData(gettext("website"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				if ($view_only) {
					if (!preg_match("/^http(s){0,1}/si", $address_info["website"]))
						$address_info["website"] = sprintf("http://%s", $address_info["website"]);

					$table->addCode(sprintf("<a href='%1\$s' target='_blank'>%1\$s</a>", $address_info["website"]));
				} else {
					$table->addTextField("address[website]", $address_info["website"], array(
						"style" => "width: 400px;"
					));
				}
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
				$table->addCode(gettext("relation information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_company_2"));
			$table->insertTableData(gettext("company name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[opt_company_name]", $address_info["opt_company_name"]);
			$table->endTableData();
			$table->addTableData(array("rowspan" => 2, "colspan" => 2), "data");
				//$table->addCode(gettext("only if information differs from relation card"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_company_2"));
			$table->insertTableData(gettext("company phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["opt_company_phone_nr"] = $address_data->show_phonenr($address_info["opt_company_phone_nr"]);
				$table->addTextField("address[opt_company_phone_nr]", $address_info["opt_company_phone_nr"]);
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
				$table->addTextField("address[business_address]", $address_info["business_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["business_phone_nr"] = $address_data->show_phonenr($address_info["business_phone_nr"]);
				$table->addTextField("address[business_phone_nr]", $address_info["business_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[business_city]", $address_info["business_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["business_phone_nr_2"] = $address_data->show_phonenr($address_info["business_phone_nr_2"]);
				$table->addTextField("address[business_phone_nr_2]", $address_info["business_phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[business_state]", $address_info["business_state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[business_fax_nr]", $address_info["business_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[business_zipcode]", $address_info["business_zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["business_mobile_nr"] = $address_data->show_phonenr($address_info["business_mobile_nr"]);
				$table->addTextField("address[business_mobile_nr]", $address_info["business_mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[business_country]", $address_info["business_country"]);
			$table->endTableData();
			$table->insertTableData(gettext("car phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["business_car_phone"] = $address_data->show_phonenr($address_info["business_car_phone"]);
				$table->addTextField("address[business_car_phone]", $address_info["business_car_phone"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_business"));
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $address_info["business_email"]));
				else
					$table->addTextField("address[business_email]", $address_info["business_email"], array(
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
					$address_info["opt_callback_phone_nr"] = $address_data->show_phonenr($address_info["opt_callback_phone_nr"]);
				$table->addTextField("address[opt_callback_phone_nr]", $address_info["opt_callback_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("pager number"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["opt_pager_number"] = $address_data->show_phonenr($address_info["opt_pager_number"]);
				$table->addTextField("address[opt_pager_number]", $address_info["opt_pager_number"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_custom_nr"));
			$table->insertTableData(gettext("radio phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["opt_radio_phone_nr"] = $address_data->show_phonenr($address_info["opt_radio_phone_nr"]);
				$table->addTextField("address[opt_radio_phone_nr]", $address_info["opt_radio_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("telex nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[opt_telex_number]", $address_info["opt_telex_number"]);
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
				$table->addTextField("address[pobox]", $address_info["pobox"]);
			$table->endTableData();
			$table->insertTableData(gettext("pobox zipcode"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[pobox_zipcode]", $address_info["pobox_zipcode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_pobox"));
			$table->insertTableData(gettext("pobox city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[pobox_city]", $address_info["pobox_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("pobox country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[pobox_country]", $address_info["pobox_country"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_pobox"));
			$table->insertTableData(gettext("pobox state"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[pobox_state]", $address_info["pobox_state"]);
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
				$table->addTextField("address[other_address]", $address_info["other_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 1"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["other_phone_nr"] = $address_data->show_phonenr($address_info["other_phone_nr"]);
				$table->addTextField("address[other_phone_nr]", $address_info["other_phone_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[other_city]", $address_info["other_city"]);
			$table->endTableData();
			$table->insertTableData(gettext("telephone nr 2"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["other_phone_nr_2"] = $address_data->show_phonenr($address_info["other_phone_nr_2"]);
				$table->addTextField("address[other_phone_nr_2]", $address_info["other_phone_nr_2"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("state/province"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[other_state]", $address_info["other_state"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[other_fax_nr]", $address_info["other_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[other_zipcode]", $address_info["other_zipcode"]);
			$table->endTableData();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$address_info["other_mobile_nr"] = $address_data->show_phonenr($address_info["other_mobile_nr"]);
				$table->addTextField("address[other_mobile_nr]", $address_info["other_mobile_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData(gettext("country"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[other_country]", $address_info["other_country"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				if ($view_only)
					$table->addCode(sprintf("<a href=\"javascript: emailSelectFrom('%1\$s','0');\">%1\$s</a>", $address_info["other_email"]));
				else
					$table->addTextField("address[other_email]", $address_info["other_email"]);
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->addTableData(array("colspan" => 4), "header");
				$table->expandCollapse("expand_options", 0);
				$table->insertAction("toggle", "", "");
				$table->addSpace();
				$table->addCode(gettext("other options"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_options"));
			$table->insertTableData(gettext("public accessible"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("address[is_public]", 1, $address_info["is_public"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_options"));
			$table->insertTableData(gettext("notes"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addTextArea("address[comment]", $address_info["comment"], array("style"=>"width: 400px; height: 150px;"));
			$table->endTableData();
		$table->endTableRow();
		if ($type != "overig" && !$view_only) {
			$table->addTableRow(array("class" => "expand_options"));
				$table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
			$table->endTableRow();
			$metadata   = new Metafields_data();
			$metaoutput = new Metafields_output();
			$metafields = $metadata->meta_list_fields("adres", $id);
			foreach ($metafields as $v) {
				$table->addTableRow(array("class" => "expand_options"));
					$table->insertTableData($v["fieldname"], "", "header");
					$table->addTableData("", "data");
						$table->addCode($metaoutput->meta_format_field($v));
						$table->insertAction("delete", gettext("remove"), "javascript: remove_meta(".$v["id"].");");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow(array("class" => "expand_options"));
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("new", gettext("add"), "javascript: add_meta('".$_REQUEST["addresstype"]."', ".$_REQUEST["id"].");");
				$table->endTableData();
			$table->endTableRow();
		}
	if ($type != "overig" && !$view_only) {
		$table->addTableRow(array("class" => "expand_options"));
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addTag("div", array("id"=>"address_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_options"));
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addTag("span", array("id" => "action_force_double", "style" => "visibility: visable;"));
					$table->addCheckBox("address[force_double]", 1);
					$table->addSpace(1);
					$table->addCode(gettext("force double entry"));
				$table->endTag("span");
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
		$table->addTableData("", "data");

			if ($src_id)
				$table->insertAction("back", gettext("back"), "javascript: history.go(-1);");
			else
				$table->insertAction("close", gettext("close"), "javascript: window.close();");

			if($id && !$view_only) {
				$table->insertAction("delete", gettext("remove"), "javascript: address_remove_item($id, '$type');");
			}
			if ($type != "overig" && !$view_only)  {
				$table->addTag("span", array("id"=>"action_save_span", "style"=>"visibility: hidden;"));
			}
			if (!$view_only)
				$table->insertAction("save", gettext("save"), "javascript: address_save();");

			if ($type != "overig") {
				$table->endTag("span");
			}
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	/* end table object */

	$venster->addCode($table->generate_output());
	unset($table);
	$venster->endTag("form");
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
/* end of venster object */
$output->start_javascript();
	$output->addCode("var skip_checks = 0;");
$output->end_javascript();

if ($view_only) {
	$email = new Email_output();
	$output->addCode( $email->emailSelectFromPrepare(1) );
}


$output->load_javascript(self::include_dir."address_edit.js");
$output->layout_page_end();
$output->exit_buffer();
?>
