<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
if (!$id && !$address_id) {
	die("cannot create businesscard without address id");
}
/* init address data object */
$address_data = new Address_data();
/* get array of possible letterheads */
$letterheads = $address_data->getLetterheads();
/* get array of possible commencements */
$commencements = $address_data->getCommencements();
/* get array of possible titles */
$titles = $address_data->getTitles();

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


if ($id) {
	/* fetch bcard we wanna edit */
	$bcardinfo = $address_data->getAddressById($id, "bcards");
	$subtitle  = gettext("change");
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
	$output->addHiddenField("bcard[id]", $bcardinfo["id"]);
	$venster = new Layout_venster(array(
		"title"    => gettext("businesscard"),
		"subtitle" => $subtitle
	));
	$venster->addVensterData();
		/* table for layout */
		$table = new Layout_table(array("cellspacing" => "1"));
		$table->addTableRow();
			$table->insertTableData(gettext("information"), array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("free field"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[alternative_name]", $bcardinfo["alternative_name"]);
			$table->endTableData();
			$table->insertTableData(gettext("locationcode"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[locationcode]", $bcardinfo["locationcode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("business unit"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("bcard[businessunit]", $bcardinfo["businessunit"]);
			$table->endTableData();
			$table->insertTableData(gettext("department"), "", "header");
			$table->addTabledata("", "data");
				$table->addTextField("bcard[department]", $bcardinfo["department"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("given name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[givenname]", $bcardinfo["givenname"]);
			$table->endTableData();
			$table->insertTableData(gettext("initials"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[initials]", $bcardinfo["initials"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("insertion"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[infix]", $bcardinfo["infix"]);
			$table->endTableData();
			$table->insertTableData(gettext("last name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[surname]", $bcardinfo["surname"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
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
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addSpace(1);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("letterhead"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[letterhead]", $letterheads, $bcardinfo["letterhead"]);
			$table->endTableData();
			$table->insertTableData(gettext("commencement"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[commencement]", $commencements, $bcardinfo["commencement"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("title"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("bcard[title]", $titles, $bcardinfo["title"]);
			$table->endTableData();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addSpace(1);
			$table->endTableData();
		$table->endTableRow();
		/* business address */
		$table->addTableRow();
			$table->insertTableData(gettext("busines address"), array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_address]", $bcardinfo["business_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_zipcode]", $bcardinfo["business_zipcode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_city]", $bcardinfo["business_city"]);
			$table->endTableData();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addSpace(1);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("telephone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_phone_nr]", $bcardinfo["business_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_fax_nr]", $bcardinfo["business_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_mobile_nr]", $bcardinfo["business_mobile_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[business_email]", $bcardinfo["business_email"]);
			$table->endTableData();
		$table->endTableRow();
		/* private address */
		$table->addTableRow();
			$table->insertTableData(gettext("private address"), array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_address]", $bcardinfo["personal_address"]);
			$table->endTableData();
			$table->insertTableData(gettext("zip code"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_zipcode]", $bcardinfo["personal_zipcode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("city"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_city]", $bcardinfo["personal_city"]);
			$table->endTableData();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->addSpace(1);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("telephone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_phone_nr]", $bcardinfo["personal_phone_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("fax nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_fax_nr]", $bcardinfo["personal_fax_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("mobile phone nr"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_mobile_nr]", $bcardinfo["personal_mobile_nr"]);
			$table->endTableData();
			$table->insertTableData(gettext("email address"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("bcard[personal_email]", $bcardinfo["personal_email"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", array("colspan"=>4), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("memo"), "", "header");
			$table->addTabledata(array("colspan"=>3), "data");
				$table->addTextArea("bcard[memo]", $bcardinfo["memo"], array("style" => "width: 400px; height: 100px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addHiddenField("bcard[address_id]", $bcardinfo["address_id"]);
				$table->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("classification"), "", "header");
			$table->addTableData(array("colspan" => 3), "data");
				$table->addHiddenField("bcard[classification]", $bcardinfo["classification"]);
				$table->endTag("span");
				$classification = new Classification_output();
				$table->addCode( $classification->classification_selection("bcardclassification", $bcardinfo["classification"]) );
			$table->endTableData();
		$table->endTableRow();
		if ($bcardinfo["id"]) {
			/* show photo with delete link, or field to upload new photo */
			$table->addTableRow();
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
			$table->addTableRow();
				$table->insertTableData(gettext("extra"), array("colspan" => 4), "header");
			$table->endTableRow();
			$metadata   = new Metafields_data();
			$metaoutput = new Metafields_output();
			$metafields = $metadata->meta_list_fields("bcards", $bcardinfo["id"]);
			foreach ($metafields as $v) {
				$table->addTableRow();
					$table->insertTableData($v["fieldname"], "", "header");
					$table->addTableData(array("colspan" => 3), "data");
						$table->addCode($metaoutput->meta_format_field($v));
						$table->insertAction("delete", gettext("remove"), "javascript: remove_meta(".$v["id"].");");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData(array("colspan" => 3), "data");
					$table->insertAction("new", gettext("add"), "javascript: add_meta('bcards', ".$bcardinfo["id"].");");
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData(array("colspan"=>3), "data");
				if ($bcardinfo["id"]) {
					$table->insertAction("delete", gettext("delete"), "javascript: bcard_delete(".$bcardinfo["id"].");");
				}
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
		$output->addCode("selectRel($aid, '".addslashes($aname)."');\n");
	}
	$output->end_javascript();
/* end output buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
