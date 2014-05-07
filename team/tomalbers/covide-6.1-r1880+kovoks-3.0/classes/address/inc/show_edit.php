<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* init address data class */
$address_data = new Address_data();
/* get array of possible letterheads */
$letterheads = $address_data->getLetterheads();
/* get array of possible commencements */
$commencements = $address_data->getCommencements();
/* get array of possible titles */
$titles = $address_data->getTitles();
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
if ($GLOBALS["covide"]->license["has_hrm"]) {
	/* get arbo addresses */
	$sql = "SELECT * FROM address_other WHERE arbo_kantoor = 1";
	$res = sql_query($sql);
	$arbos = array(0 => gettext("geen"));
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
$output->layout_page(gettext("wijzig adres"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("adressen"),
	"subtitle" => ($type == "relations" || $type == "nonactive")?gettext("bedrijfsgegevens wijzigen"):gettext("adres wijzigen")
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
	$venster->addHiddenField("address[type]", $type);
	if ($type == "overig") {
		if (!$sub) $sub = "kantoor";
		$venster->addHiddenField("address[sub]", $sub);
	}
	$venster->addHiddenField("address[user_id]", $address_info["user_id"]);

	/* start building the form in a table object */
	$table = new Layout_table(array("cellspacing"=>1));
	if ($type == "relations" || $type == "nonactive") {
		/*{{{ relation specific fields */
		$table->addTableRow();
			$table->insertTableData(gettext("relatie"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width:500px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("relatie login"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[relname]", $address_info["relname"]);
				$table->addSpace(1);
				$table->addCode(gettext("relatie wachtwoord"));
				$table->addSpace(1);
				$table->addTextField("address[relpass]", $address_info["relpass"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("aanroep"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("address[contact_letterhead]", $letterheads, $address_info["contact_letterhead"]);
				$table->addSpace(1);
				$table->addCode(gettext("aanhef"));
				$table->addSpace(1);
				$table->addSelectField("address[contact_commencement]", $commencements, $address_info["contact_commencement"]);
				$table->addSpace(1);
				$table->addCode(gettext("titel"));
				$table->addSpace(1);
				$table->addSelectField("address[title]", $titles, $address_info["title"]);
				$table->addSpace(1);
				$table->addCode(gettext("voorletter(s)"));
				$table->addSpace(1);
				$table->addTextField("address[contact_initials]", $address_info["contact_initials"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("voornaam"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[contact_givenname]", $address_info["contact_givenname"]);
				$table->addSpace(1);
				$table->addCode(gettext("tussenvoegsel"));
				$table->addSpace(1);
				$table->addTextField("address[contact_infix]", $address_info["contact_infix"]);
				$table->addSpace(1);
				$table->addCode(gettext("achternaam"));
				$table->addSpace(1);
				$table->addTextField("address[contact_surname]", $address_info["contact_surname"]);
			$table->endTableData();
		$table->endTableRow();

		$table->addTableRow();
			$table->insertTableData(gettext("geboortedatum"), "", "header");
			$table->addTableData("", "data");
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

		$table->addTableRow();
			$table->insertTableData(gettext("letop"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[warning]", $address_info["warning"], array("style"=>"width:500px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("account manager"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("address[account_manager]", $address_info["account_manager"]);
				$useroutput = new User_output();
				$table->addCode( $useroutput->user_selection("addressaccount_manager", $address_info["account_manager"], 0, 0, 1) );
				unset($useroutput);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("classificatie"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("address[classification]", $address_info["classifi"]);
				$table->endTag("span");
				$classification = new Classification_output();
				$table->addCode( $classification->classification_selection("addressclassification", $address_info["classifi"]) );

			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("relationtype"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("address[is_customer]", 1, $address_info["is_customer"]);
				$table->addCode(gettext("klant"));
				$table->addCheckBox("address[is_supplier]", 1, $address_info["is_supplier"]);
				$table->addCode(gettext("leverancier"));
				$table->addCheckBox("address[is_transporter]", 1, $address_info["is_transporter"]);
				$table->addCode(gettext("transporteur"));
				$table->addCheckBox("address[is_contact]", 1, $address_info["is_contact"]);
				$table->addCode(gettext("contact"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("actief"), "", "header");
			$table->addTableData("", "data");
				$table->insertCheckbox("address[is_active]", "1", $address_info["is_active"]);
			$table->endTableData();
		$table->endTableRow();
		/* end relation specific stuff }}}*/
	} elseif ($type == "overig") {
		if ($sub == "kantoor") {
			$table->addTableRow();
				$table->insertTableData("naam", "", "header");
				$table->addTableData("", "data");
					$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width: 500px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("arbo kantoor"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("address[arbo_bedrijf]", $arbos, $address_info["arbo_bedrijf"]);
				$table->endTableData();
			$table->endTableRow();
		} else {
			$table->addTableRow();
				$table->insertTableData(gettext("regiokantoor")." / ".gettext("team"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("address[companyname]", $address_info["companyname"], array("style"=>"width:248px;"));
					$table->addCode("/");
					$table->addTextField("address[arbo_team]", $address_info["arbo_team"], array("style"=>"width:247px;"));
				$table->endTableData();
			$table->endTableRow();
		}
	} else {
		$table->addTableRow();
			$table->insertTableData(gettext("voornaam"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[givenname]", $address_info["givenname"]);
				$table->addSpace(1);
				$table->addCode(gettext("tussenvoegsel"));
				$table->addSpace(1);
				$table->addTextField("address[infix]", $address_info["infix"]);
				$table->addSpace(1);
				$table->addCode(gettext("achternaam"));
				$table->addSpace(1);
				$table->addTextField("address[surname]", $address_info["surname"]);
			$table->endTableData();
		$table->endTableRow();
	}

	$table->addTableRow();
		$table->insertTableData(gettext("adres"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[address]", $address_info["address"], array("style"=>"width: 500px;"));
			if ($type == "relations") {
				$table->addTag("br");
				$table->addTextField("address[address2]", $address_info["address2"], array("style"=>"width: 500px;"));
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("postcode"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[zipcode]", $address_info["zipcode"]);
			$table->addSpace(1);
			$table->addCode(gettext("plaats"));
			$table->addSpace(1);
			$table->addTextField("address[city]", $address_info["city"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("staat"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[state]", $address_info["state"]);
			$table->addSpace(1);
			$table->addCode(gettext("land"));
			$table->addSpace(1);
			$table->addTextField("address[country]", $address_info["country"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("postbus"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[pobox]", $address_info["pobox"]);
			$table->addSpace(1);
			$table->addCode(gettext("postcode postbus"));
			$table->addSpace(1);
			$table->addTextField("address[pobox_zipcode]", $address_info["pobox_zipcode"]);
			$table->addSpace(1);
			$table->addCode(gettext("plaats postbus"));
			$table->addSpace(1);
			$table->addTextField("address[pobox_city]", $address_info["pobox_city"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("telefoon nr"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[phone_nr]", $address_info["phone_nr"]);
			$table->addSpace(1);
			$table->addCode(gettext("fax nr"));
			$table->addSpace(1);
			$table->addTextField("address[fax_nr]", $address_info["fax_nr"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("mobiel nr"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[mobile_nr]", $address_info["mobile_nr"]);
			$table->addSpace(1);
			$table->addCode(gettext("snelkies nr"));
			$table->addSpace(1);
			$table->addTextField("address[speeddial]", $address_info["speeddial"]);
			$table->addSpace(1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("email"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[email]", $address_info["email"], array("style" => "width: 500px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("website"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("address[website]", $address_info["website"], array("style" => "width: 500px;"));
		$table->endTableData();
	$table->endTableRow();
	if ($type == "relations") {
		$table->addTableRow();
			$table->insertTableData(gettext("debiteur nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("address[debtor_nr]", $address_info["debtor_nr"]);
				$table->insertAction("toggle", gettext("test"), "javascript: address_get_debtornr();");
			$table->endTableData();
		$table->endTableRow();
	}
	if ($type != "overig") {
		$table->addTableRow();
			$table->insertTableData(gettext("publiek toegankelijk"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("address[is_public]", 1, $address_info["is_public"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$venster->addHiddenField("address[is_public]", 1);
	}
	$table->addTableRow();
		$table->insertTableData(gettext("aantekeningen"), "", "header");
		$table->addTableData("", "data");
			if ($type == "private") {
				$table->addTextArea("address[comment]", $address_info["comment"], array("style"=>"width: 500px; height: 150px;"));
			} else {
				$table->addTextArea("address[comment]", $address_info["memo"], array("style"=>"width: 500px; height: 150px;"));
			}
		$table->endTableData();
	$table->endTableRow();
	if ($id) {
		/* photo field */
		if ($type == "relations") {

			$userdata = new User_data();
			$userdata->getUserPermissionsById($_SESSION["user_id"]);

			/* if mortgage module and mortgage manager */
			if ($GLOBALS["covide"]->license["has_hypo"] && $userdata->checkPermission("xs_hypo")) {
				$table->addTableRow();
					$table->insertTableData(gettext("provisie percentage (%)"), "", "header");
					$table->addTableData("", "data");
						$table->addTextField("address[provision_perc]", $address_info["provision_perc"]);
					$table->endTableData();
				$table->endTableRow();
			} else {
				$table->addHiddenField("address[provision_perc]", $address_info["provision_perc"]);
			}

			/* show photo with delete link, or field to upload new photo */
			$table->addTableRow();
				$table->insertTableData(gettext("foto"), "", "header");
				$table->addTableData("", "data");
					if ($address_info["photo"]["size"]) {
						$url = "index.php?mod=address&action=showrelimg&addresstype=relations";
						foreach ($address_info["photo"] as $k=>$v) {
							$url .= "&photo[$k]=$v";
						}
						$table->addTag("img", array(
							"src" => $url,
							"border" => 0,
							"alt" => $address_info["photo"]["name"]
						));
						$table->insertAction("delete", gettext("verwijderen"), "javascript: remove_img(".$address_info["id"].");");
					} else {
						$table->addUploadField("address[binphoto]");
					}
				$table->endTableData();
			$table->endTableRow();
		}
		if ($type != "overig") {
			$table->addTableRow();
				$table->insertTableData(gettext("extra"), array("colspan" => 2), "header");
			$table->endTableRow();
			$metadata   = new Metafields_data();
			$metaoutput = new Metafields_output();
			$metafields = $metadata->meta_list_fields("adres", $id);
			foreach ($metafields as $v) {
				$table->addTableRow();
					$table->insertTableData($v["fieldname"], "", "header");
					$table->addTableData("", "data");
						$table->addCode($metaoutput->meta_format_field($v));
						$table->insertAction("delete", gettext("verwijder"), "javascript: remove_meta(".$v["id"].");");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("new", gettext("toevoegen"), "javascript: add_meta('".$_REQUEST["addresstype"]."', ".$_REQUEST["id"].");");
				$table->endTableData();
			$table->endTableRow();
		}
	}
	if ($type != "overig") {
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addTag("div", array("id"=>"address_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addTag("span", array("id" => "action_force_double", "style" => "visibility: visable;"));
					$table->addCheckBox("address[force_double]", 1);
					$table->addSpace(1);
					$table->addCode(gettext("forceer dubbele invoer"));
				$table->endTag("span");
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			if($id) {
				$table->insertAction("delete", gettext("verwijder"), "javascript: address_remove_item($id, '$type');");
			}
			if ($type != "overig")  {
				$table->addTag("span", array("id"=>"action_save_span", "style"=>"visibility: hidden;"));
			}
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
if ($type == "overig") {
	$output->start_javascript();
		$output->addCode("var skip_checks = 1;");
	$output->end_javascript();
} else {
	$output->start_javascript();
		$output->addCode("var skip_checks = 0;");
	$output->end_javascript();
}
$output->load_javascript(self::include_dir."address_edit.js");
$output->layout_page_end();
$output->exit_buffer();
?>
