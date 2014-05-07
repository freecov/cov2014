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
/* init address data class */
$address_data = new Address_data();
/* get array of possible letterheads */
$letterheads = $address_data->getLetterheads(-1);
foreach($letterheads AS $keys => $values) {
	$newLetterheads[$keys] = $values["title"];
}
/* get array of possible commencements */
$commencements = $address_data->getCommencements(-1);
foreach($commencements AS $keys => $values) {
	$newCommencements[$keys] = $values["title"];
}
/* get array of possible titles */
$titles = $address_data->getTitles(-1,1);
foreach($titles AS $keys => $values) {
	$newTitles[$keys] = $values["title"];
}
/* get array of possible suffix options */
$suffix = $address_data->getSuffix(-1);
foreach($suffix AS $keys => $values) {
	$newSuffix[$keys] = $values["title"];
}
/* put all countries in an array */
$countryArray = $address_data->listCountries();

if ($id) {
	/* get the address data if we get an id. */
	$address_info = $address_data->getAddressById($id, $type, $sub);
} elseif ($src_id) {
	/* get the address data if we want to convert */
	$address_info = $address_data->getAddressById($src_id, "private");

	/* some conversions */
	$address_info["contact_infix"]     =& $address_info["infix"];
	$address_info["contact_surname"]   =& $address_info["surname"];
	$address_info["contact_givenname"] =& $address_info["givenname"];

	$address_info["companyname"] = preg_replace("/ {1,}/s", " ", trim(sprintf("%s %s %s",
		$address_info["givenname"], $address_info["infix"], $address_info["surname"])));

	$address_info["id"] = "";

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

/* twinfield */
if ($GLOBALS["covide"]->license["has_twinfield"]) {
	$twinfield_data = new twinfield_data();
	$address_info["twinfield_administration"] = $twinfield_data->getRelAdm($address_info["id"]);
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
	"subtitle" => ($type == "relations" || $type == "nonactive")?gettext("change contact"):gettext("alter address")
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
	$venster->addHiddenField("address[campaign_id]", $_REQUEST["campaign_id"]);
	if ($type == "overig") {
		if (!$sub) $sub = "kantoor";
		$venster->addHiddenField("address[sub]", $sub);
	}
	$venster->addHiddenField("address[user_id]", $address_info["user_id"]);

	/* start building the form in a table object */
	$table = new Layout_table(array("cellspacing"=>1));
	if ($view_only)
		$table->setInputReadOnly(1);

	if ($type == "relations" || $type == "nonactive") {
		/*{{{ relation specific fields */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_name", 1);
				$table->insertAction("state_special", "", "");
				$table->addSpace();
				$table->addCode(gettext("company name"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_name"));
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData(array("colspan" => 4), "data");
				$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width: 482px;"));
			$table->endTableData();
		$table->endTableRow();

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();
		
		//twinfield
		if ($GLOBALS["covide"]->license["has_twinfield"]) {
			$table->addTableRow();
				$table->addTableData(array("colspan" => 4), "header");
					$table->expandCollapse("expand_twinfield", 1);
					$table->insertAction("state_special", "", "");
					$table->addSpace();
					$table->addCode(gettext("Twinfield"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow(array("class" => "expand_twinfield"));
				$table->insertTableData(gettext("administration"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addSelectField("address[twinfield_administration]", $twinfield_data->getOffices(), $address_info["twinfield_administration"]);
				$table->endTableData();
			$table->endTableRow();
			/* empty */
			$table->addTableRow();
				$table->addTableData(array("colspan"=>4), "data");
					$table->addSpace();
				$table->endTableData();
			$table->endTableRow();
		}
		
		//login
		/*
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_login", 0);
				$table->insertAction("logout", "", "");
				$table->addSpace();
				$table->addCode(gettext("relation login"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_login"));
			$table->insertTableData(gettext("contact login"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[relname]", $address_info["relname"]);
			$table->endTableData("", "data");
			$table->insertTableData(gettext("contact password"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[relpass]", $address_info["relpass"]);
				if (!$view_only)
					$table->insertAction("toggle", gettext("generate password"), "javascript: document.getElementById('addressrelpass').value = randomPassword(8); void(0);");
			$table->endTableData("", "data");
		$table->endTableRow();
		*/

		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_contact", 1);
				$table->insertAction("state_public", "", "");
				$table->addSpace();
				$table->addCode(gettext("contact information"));
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("letterhead"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[contact_letterhead]", $newLetterheads, $address_info["contact_letterhead"]);
			$table->endTableData();
			$table->insertTableData(gettext("commencement"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[contact_commencement]", $newCommencements, $address_info["contact_commencement"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("title"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[title]", $newTitles, $address_info["title"]);
			$table->endTableData();
			$table->insertTableData(gettext("suffix"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[suffix]", $newSuffix, $address_info["suffix"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("initials"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_initials]", $address_info["contact_initials"]);
			$table->endTableData();
			$table->insertTableData(gettext("given name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_givenname]", $address_info["contact_givenname"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("insertion"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_infix]", $address_info["contact_infix"]);
			$table->endTableData();
			$table->insertTableData(gettext("last name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_surname]", $address_info["contact_surname"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("birth date"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				if ($address_info["contact_birthday"]) {
					$table->addSelectField("address[bday_day]", $days, date("d", $address_info["contact_birthday"]));
					$table->addSelectField("address[bday_month]", $months, date("m", $address_info["contact_birthday"]));
					$table->addSelectField("address[bday_year]", $years, date("Y", $address_info["contact_birthday"]));
				} else {
					$table->addSelectField("address[bday_day]", $days, 0);
					$table->addSelectField("address[bday_month]", $months, 0);
					$table->addSelectField("address[bday_year]", $years, 0);
				}
			$table->endTableData();
		$table->endTableRow();
		
		$table->addTableRow(array("class" => "expand_contact"));
			$table->insertTableData(gettext("jobtitle"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[jobtitle]", $address_info["jobtitle"]);
			$table->endTableData();
			$table->insertTableData(gettext("SSN"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[bsn]", $address_info["bsn"]);
			$table->endTableData();
		$table->endTableRow();
		
		
		/* empty */
		$table->addTableRow();
			$table->addTableData(array("colspan"=>4), "data");
				$table->addSpace();
			$table->endTableData();
		$table->endTableRow();

		if (!$view_only) {
			$table->addTableRow();
				$table->addTableData(array("colspan"=>4), "header");
					$table->expandCollapse("expand_account", 1);
					$table->insertAction("view_all", "", "");
					$table->addSpace();
					$table->addCode(gettext("account information"));
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData(gettext("account manager"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addHiddenField("address[account_manager]", $address_info["account_manager"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("addressaccount_manager", $address_info["account_manager"], 0, 0, 1) );
					unset($useroutput);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData(gettext("classification"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addHiddenField("address[classification]", $address_info["classifi"]);
					$table->endTag("span");
					$classification = new Classification_output();
					$table->addCode( $classification->classification_selection("addressclassification", $address_info["classifi"]) );

				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData(gettext("address type"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addCheckBox("address[is_customer]", 1, $address_info["is_customer"]);
					$table->addCode(gettext("customer"));
					$table->addCheckBox("address[is_supplier]", 1, $address_info["is_supplier"]);
					$table->addCode(gettext("supplier"));
					$table->addCheckBox("address[is_person]", 1, $address_info["is_person"]);
					$table->addCode(gettext("private"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData(gettext("active"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->insertCheckbox("address[is_active]", "1", $address_info["is_active"]);
				$table->endTableData();
			$table->endTableRow();

		}
		/* end relation specific stuff }}}*/
	} elseif ($type == "overig") {
		if ($sub == "kantoor") {
			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData("naam", "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width: 500px;"));
				$table->endTableData();
			$table->addTableRow(array("class" => "expand_account"));
			$table->addTableRow();
				$table->insertTableData(gettext("arbo office"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addSelectField("address[arbo_bedrijf]", $arbos, $address_info["arbo_bedrijf"]);
				$table->endTableData();
			$table->endTableRow();
		} else {
			$table->addTableRow(array("class" => "expand_account"));
				$table->insertTableData(gettext("region office")." / ".gettext("team"), "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width:248px;"));
					$table->addCode("/");
					$table->addTextField("address[arbo_team]", $address_info["arbo_team"], array("style"=>"width:247px;"));
				$table->endTableData();
			$table->endTableRow();
		}
	} else {
		$table->addTableRow(array("class" => "expand_account"));
			$table->insertTableData(gettext("given name"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addTextField("address[givenname]", $address_info["givenname"]);
				$table->addSpace(1);
				$table->addCode(gettext("insertion"));
				$table->addSpace(1);
				$table->addTextField("address[infix]", $address_info["infix"]);
				$table->addSpace(1);
				$table->addCode(gettext("last name"));
				$table->addSpace(1);
				$table->addTextField("address[surname]", $address_info["surname"]);
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
			$table->addTableData(array("colspan"=>4), "header");
				$table->expandCollapse("expand_address", 1);
				$table->insertAction("addressbook", "", "");
				$table->addSpace();
				$table->addCode(gettext("company address information"));
			$table->endTableData();
		$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("invoice address"), "", "header");
		$table->addTableData(array("colspan" => 3), "data");
			$table->addTextField("address[address]", $address_info["address"], array("style"=>"width: 500px;"));
			if ($type == "relations") {
				$table->addTag("br");
				$table->addTextField("address[address2]", $address_info["address2"], array("style"=>"width: 500px;"));
			}
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("zip code"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[zipcode]", $address_info["zipcode"]);
		$table->endTableData();
		$table->insertTableData(gettext("city"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[city]", $address_info["city"]);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("state/province"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[state]", $address_info["state"]);
		$table->endTableData();
		$table->insertTableData(gettext("country"), "", "header");
		$table->addTableData("", "data");
			//$table->addTextField("address[country]", $address_info["country"]);
			/* If no country is specified, we want to choose The Netherlands because most relations are Dutch and we are lazy :) */
			if (!$address_info["country"]) { $address_info["country"] = "NL"; }
			if ($view_only) 
				$table->addCode($countryArray[$address_info["country"]]);
			else
				$table->addSelectField("address[country]", $countryArray, $address_info["country"]);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("phone_nr"), "", "header");
		$table->addTableData("", "data");
			if ($view_only)
				$address_info["phone_nr"] = $address_data->show_phonenr($address_info["phone_nr"]);
			$table->addTextField("address[phone_nr]", $address_info["phone_nr"]);
		$table->endTableData();
		$table->insertTableData(gettext("mobile phone nr"), "", "header");
		$table->addTableData("", "data");
			if ($view_only)
				$address_info["mobile_nr"] = $address_data->show_phonenr($address_info["mobile_nr"]);
			$table->addTextField("address[mobile_nr]", $address_info["mobile_nr"]);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("fax nr"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[fax_nr]", $address_info["fax_nr"]);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("email"), "", "header");
		$table->addTableData(array("colspan" => 3), "data");
			$table->addTextField("address[email]", $address_info["email"], array("style" => "width: 500px;"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_address"));
		$table->insertTableData(gettext("website"), "", "header");
		$table->addTableData(array("colspan" => 3), "data");
			$table->addTextField("address[website]", $address_info["website"], array("style" => "width: 500px;"));
		$table->endTableData();
	$table->endTableRow();

	if ($type == "relations") {
		$table->addTableRow(array("class" => "expand_address"));
			$table->insertTableData(gettext("debtor nr"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addTextField("address[debtor_nr]", $address_info["debtor_nr"]);
				if (!$view_only)
					$table->insertAction("toggle", gettext("test"), "javascript: address_get_debtornr();");
			$table->endTableData();
		$table->endTableRow();
	/* TODO: Perhaps we could sometime add an Ajax checker for the 'elf proef' to verify the right length of a bankaccount */
		$table->addTableRow(array("class" => "expand_address"));
			$table->insertTableData(gettext("bankaccount"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[bankaccount]", $address_info["bankaccount"]);
			$table->endTableData();
			$table->insertTableData(gettext("giroaccount"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[giro]", $address_info["giro"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($type != "overig") {
		$table->addTableRow(array("class" => "expand_address"));
			$table->insertTableData(gettext("publicly accessible"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addCheckBox("address[is_public]", 1, $address_info["is_public"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$venster->addHiddenField("address[is_public]", 1);
	}


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
				if (!$address_info["pobox_country"]) { $address_info["pobox_country"] = "NL"; }
				if ($view_only) 
					$table->addCode($countryArray[$address_info["country"]]);
				else
					$table->addSelectField("address[pobox_country]", $countryArray, $address_info["pobox_country"]);
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

	$table->addTableRow();
		$table->addTableData(array("colspan"=>4), "header");
			$table->expandCollapse("expand_other", 1);
			$table->insertAction("toggle", "", "");
			$table->addSpace();
			$table->addCode(gettext("other information"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_other"));
		$table->insertTableData(gettext("notes"), "", "header");
		$table->addTableData(array("colspan" => 3), "data");
			$table->addTextArea("address[comment]", $address_info["memo"], array("style"=>"width: 500px; height: 150px;"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow(array("class" => "expand_other"));
		$table->insertTableData(gettext("warning"), "", "header");
		$table->addTableData(array("colspan" => 3), "data");
			$table->addTextField("address[warning]", $address_info["warning"], array("style"=>"width:500px;"));
		$table->endTableData();
	$table->endTableRow();

	/* Show meta data for viewing only */
	if ($id && $view_only) {
		$table->addTableRow(array("class" => "expand_other"));
		$table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
		$table->endTableRow();
		$metadata   = new Metafields_data();
		$metaoutput = new Metafields_output();
		$metafields = $metadata->meta_list_fields("adres", $id);
		foreach ($metafields as $v) {
			$table->addTableRow(array("class" => "expand_other"));
				$table->insertTableData($v["fieldname"], "", "header");
				$table->addTableData(array("colspan" => 3), "data");
				$table->addCode($metaoutput->meta_print_field($v));
				$table->endTableData();
			$table->endTableRow();
		}
	}
	if ($id && !$view_only) {
		/* photo field */
		if ($type == "relations") {

			$userdata = new User_data();
			$userdata->getUserPermissionsById($_SESSION["user_id"]);

			/* if mortgage module and mortgage manager */
			if ($GLOBALS["covide"]->license["has_hypo"] && $userdata->checkPermission("xs_hypo")) {
				$table->addTableRow(array("class" => "expand_other"));
					$table->insertTableData(gettext("provisie percentage (%)"), "", "header");
					$table->addTableData(array("colspan" => 3), "data");
						$table->addTextField("address[provision_perc]", $address_info["provision_perc"]);
					$table->endTableData();
				$table->endTableRow();
			} else {
				$table->addHiddenField("address[provision_perc]", $address_info["provision_perc"]);
			}
		}
		if ($type != "overig") {
			$table->addTableRow(array("class" => "expand_other"));
				$table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
			$table->endTableRow();
			$metadata   = new Metafields_data();
			$metaoutput = new Metafields_output();
			$metafields = $metadata->meta_list_fields("adres", $id);
			foreach ($metafields as $v) {
				$table->addTableRow(array("class" => "expand_other"));
					$table->insertTableData($v["fieldname"], "", "header");
					$table->addTableData(array("colspan" => 3), "data");
						$table->addCode($metaoutput->meta_format_field($v));

				/* Check if global or local meta */
				if ($v["record_id"])
					$table->insertAction("delete", gettext("remove"), "javascript: remove_meta(".$v["id"].");");

				$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow(array("class" => "expand_other"));
				$table->insertTableData("", "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->insertAction("new", gettext("add"), "javascript: add_meta('".$_REQUEST["addresstype"]."', ".$_REQUEST["id"].");");
				$table->endTableData();
			$table->endTableRow();
		}
	}
	if ($type != "overig" && !$view_only) {
		$table->addTableRow(array("class" => "expand_other"));
			$table->insertTableData("&nbsp;", array("rowspan" => 2), "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addTag("div", array("id"=>"address_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array("class" => "expand_other"));
			$table->addTableData(array("colspan" => 3), "data");
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
		$table->addTableData(array("colspan" => 3), "data");
			if (!$view_only) {
				if ($src_id)
					$table->insertAction("back", gettext("back"), "javascript: history.go(-1);");

				if($id) {
					$table->insertAction("delete", gettext("remove"), "javascript: address_remove_item($id, '$type');");
				}
				if ($type != "overig")  {
					$table->addTag("span", array("id"=>"action_save_span", "style"=>"visibility: hidden;"));
				}
				$table->insertAction("save", gettext("save"), "javascript: address_save();");
				if ($type != "overig") {
					$table->endTag("span");
				}
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

$output->load_javascript(self::include_dir."address_edit.js");
$output->load_javascript(self::include_dir."address_meta.js");
$output->layout_page_end();
$output->exit_buffer();
?>
