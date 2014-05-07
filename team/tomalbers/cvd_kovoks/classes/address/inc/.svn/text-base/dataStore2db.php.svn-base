<?php
if (!class_exists("Address_data")) {
	die("no class definition found");
}
/* check if user uploaded a new photo for this contact */
if (is_array($_FILES["address"]) && $_FILES["address"]["error"]["binphoto"] == 0) {
	/* store the relphoto */
	$this->storeRelIMG($address["id"], "relations", $_FILES);
}
/* prepare the data to inject it into the db */
if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$address_letterinfo = $this->generate_letterinfo($address);
	$address["tav"] = $address_letterinfo["tav"];
	$address["contact_person"] = $address_letterinfo["contact_person"];
	$address["is_company"] = 1;
	unset($address_letterinfo);

	/* break data into 2 arrays. one for address, one for address_info */
	$address_info["warning"]        = $address["warning"];        unset($address["warning"]);
	$address_info["comment"]        = $address["comment"];        unset($address["comment"]);
	$address_info["photo"]          = $address["photo"];          unset($address["photo"]);
	$address_info["classification"] = $address["classification"]; unset($address["classification"]);
	$address_info["provision_perc"] = $address["provision_perc"]; unset($address["provision_perc"]);
} else {
	$address["is_company"] = 0;
}
/* extract id cause we are not gonna inject that in the db */
/* {{{ fields for address table */
$id = $address["id"]; unset($address["id"]);
$fields = array();
$values = array();

if ($address["bday_year"]) {
	$timestamp_birthday = mktime(0, 0, 0, $address["bday_month"], $address["bday_day"], $address["bday_year"]);
	/* 1-1-1970 workaround */
	if (!$timestamp_birthday)
		$timestamp_birthday = 1;
} else {
	$timestamp_birthday = 0;
}

if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$fields[] = "contact_birthday";     $values[] = sprintf("%d",   $timestamp_birthday);
	$fields[] = "companyname";          $values[] = sprintf("'%s'", $address["companyname"]);
	$fields[] = "address2";             $values[] = sprintf("'%s'", $address["address2"]);
	$fields[] = "is_company";           $values[] = sprintf("%d",   $address["is_company"]);
	$fields[] = "tav";                  $values[] = sprintf("'%s'", $address["tav"]);
	$fields[] = "contact_person";       $values[] = sprintf("'%s'", $address["contact_person"]);
	$fields[] = "account_manager";      $values[] = sprintf("%d",   $address["account_manager"]);
	$fields[] = "contact_letterhead";   $values[] = sprintf("%d",   $address["contact_letterhead"]);
	$fields[] = "contact_commencement"; $values[] = sprintf("%d",   $address["contact_commencement"]);
	$fields[] = "contact_initials";     $values[] = sprintf("'%s'", $address["contact_initials"]);
	$fields[] = "contact_givenname";    $values[] = sprintf("'%s'", $address["contact_givenname"]);
	$fields[] = "contact_infix";        $values[] = sprintf("'%s'", $address["contact_infix"]);
	$fields[] = "contact_surname";      $values[] = sprintf("'%s'", $address["contact_surname"]);
	$fields[] = "title";                $values[] = sprintf("%d",   $address["title"]);
	$fields[] = "relname";              $values[] = sprintf("'%s'", $address["relname"]);
	$fields[] = "relpass";              $values[] = sprintf("'%s'", $address["relpass"]);
	$fields[] = "debtor_nr";            $values[] = sprintf("'%s'",   $address["debtor_nr"]);
	$fields[] = "is_customer";          $values[] = sprintf("%d",   $address["is_customer"]);
	$fields[] = "is_supplier";          $values[] = sprintf("%d",   $address["is_supplier"]);
	$fields[] = "is_transporter";       $values[] = sprintf("%d",   $address["is_transporter"]);
	$fields[] = "is_contact";           $values[] = sprintf("%d",   $address["is_contact"]);
} elseif ($address["type"] == "overig") {
	/* can be companylocation or 'arbo' */
	if ($address["sub"] == "kantoor") {
		/* companylocation */
		$fields[] = "arbo_bedrijf";     $values[] = sprintf("%d", $address["arbo_bedrijf"]);
	} else {
		/* arbo */
		$fields[] = "arbo_team";        $values[] = sprintf("'%s'", $address["arbo_team"]);
	}
	$fields[] = "companyname";      $values[] = sprintf("'%s'", $address["companyname"]);
	$fields[] = "pobox";            $values[] = sprintf("'%s'", $address["pobox"]);
	$fields[] = "pobox_zipcode";    $values[] = sprintf("'%s'", $address["pobox_zipcode"]);
	$fields[] = "pobox_city";       $values[] = sprintf("'%s'", $address["pobox_city"]);
} else {
	$fields[] = "surname";              $values[] = sprintf("'%s'", $address["surname"]);
	$fields[] = "infix";                $values[] = sprintf("'%s'", $address["infix"]);
	$fields[] = "givenname";            $values[] = sprintf("'%s'", $address["givenname"]);

}
if (in_array($address["type"], array("relations", "nonactive", "private"))) {
	$fields[] = "pobox";                $values[] = sprintf("'%s'", $address["pobox"]);
	$fields[] = "pobox_zipcode";        $values[] = sprintf("'%s'", $address["pobox_zipcode"]);
	$fields[] = "pobox_city";           $values[] = sprintf("'%s'", $address["pobox_city"]);

}

$fields[] = "address";              $values[] = sprintf("'%s'", $address["address"]);
$fields[] = "zipcode";              $values[] = sprintf("'%s'", $address["zipcode"]);
$fields[] = "city";                 $values[] = sprintf("'%s'", $address["city"]);
$fields[] = "phone_nr";             $values[] = sprintf("'%s'", $address["phone_nr"]);
$fields[] = "fax_nr";               $values[] = sprintf("'%s'", $address["fax_nr"]);
$fields[] = "email";                $values[] = sprintf("'%s'", $address["email"]);
$fields[] = "user_id";              $values[] = sprintf("%d",   $address["user_id"]);
$fields[] = "mobile_nr";            $values[] = sprintf("'%s'", $address["mobile_nr"]);
$fields[] = "website";              $values[] = sprintf("'%s'", $address["website"]);
$fields[] = "speeddial";            $values[] = sprintf("'%s'", $address["speeddial"]);
if ($address["type"] != "overig") {
	$fields[] = "country";              $values[] = sprintf("'%s'", $address["country"]);
	$fields[] = "modified";             $values[] = sprintf("%d",   mktime());
	$fields[] = "sync_modified";        $values[] = sprintf("%d",   mktime());
	$fields[] = "is_active";            $values[] = sprintf("%d",   $address["is_active"]);
}
$fields[] = "is_public";            $values[] = sprintf("%d",   $address["is_public"]);
/* }}} */
/* {{{ address_info fields and values */
if ($address["type"] == "relations" || $address["type"] == "nonactive") {
	$fields_info[] = "warning";        $values_info[] = sprintf("'%s'", $address_info["warning"]);
	$fields_info[] = "comment";           $values_info[] = sprintf("'%s'", $address_info["comment"]);
	$fields_info[] = "classification"; $values_info[] = sprintf("'%s'", $address_info["classification"]);
	$fields_info[] = "provision_perc"; $values_info[] = sprintf("%d", $address_info["provision_perc"]);
} elseif ($address["type"] == "private") {
	$fields[] = "comment";           $values[] = sprintf("'%s'", $address["comment"]);
}
/* }}} */
if ($id) {
	/* update folder as well. fixes bug #1560558 */
	if ($address["type"] == "relations" || $address["type"] == "nonactive") {
		$filesys_data = new Filesys_data();
		$filesys_folder = $filesys_data->getRelFolder($id);
		$folderupdate = sprintf("UPDATE filesys_folders SET name='%s' WHERE id=%d", $address["companyname"], $filesys_folder);
	}
	if ($folderupdate)
		$res_folderupdate = sql_query($folderupdate);

	if (count($metafields)) {
		$meta_data = new Metafields_data();
		$meta_data->meta_save_field($metafields);
	}
	/* update for address */
	if ($address["type"] == "relations" || $address["type"] == "nonactive") {
		$sql = "UPDATE address SET ";
	} elseif($address["type"] == "overig") {
		$sql = "UPDATE address_other SET ";
	} else {
		$sql = "UPDATE address_private SET ";
	}
	foreach ($fields as $k=>$v) {
		$sql .= $v."=".$values[$k].", ";
	}
	$sql  = substr($sql, 0, strlen($sql)-2);
	$sql .= sprintf(" WHERE id=%d", $id);
	$res = sql_query($sql);
	if ($address["type"] == "relations" || $address["tye"] == "nonactive") {
		$sql_info = "UPDATE address_info SET ";
		foreach ($fields_info as $k=>$v) {
			$sql_info .= $v."=".$values_info[$k].", ";
		}
		$sql_info  = substr($sql_info, 0, strlen($sql_info)-2);
		$sql_info .= sprintf(" WHERE address_id=%d", $id);
		$res_info = sql_query($sql_info);
	}

} else {
	/* new address record */
	if ($address["type"] != "relations" && $address["type"] != "nonactive") {
		$sql = "INSERT INTO address_private (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
	} elseif($address["type"] == "overig") {
		$sql = "INSERT INTO address_other (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
	} else {
		$sql = "INSERT INTO address (".implode(",", $fields).") VALUES (".implode(",", $values).");";
		$res = sql_query($sql);
		$address_id = sql_insert_id("address", $GLOBALS["covide"]->db);
		$fields_info[] = "address_id"; $values_info[] = $address_id;
		$sql_info = "INSERT INTO address_info (".implode(",", $fields_info).") VALUES(".implode(",", $values_info).");";
		$res_info = sql_query($sql_info);
	}
}

$output = new Layout_output();
$output->start_javascript();
	$output->addCode("
		if (opener) {
			if (opener.location.href.match(/\?mod=address/gi)) {
				if (opener.location.href.match(/\&action=relcard/gi)) {
					var uri = opener.location.href;
					uri = uri.replace(/\&restore_point_steps=\d/gi, '');
					uri = uri.concat('&restore_point_steps=2');
					opener.location.href = uri;
				} else {
					opener.location.href = opener.location.href;
				}
			} else {
				opener.document.getElementById('deze').submit();
			}
			var t = setTimeout('window.close();', 100);
		}
	");
$output->end_javascript();
$output->exit_buffer();
?>
