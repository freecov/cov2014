<?php
if (!class_exists("Address_data")) {
	die("no class definition found.");
}
$filename       = $data["filename"];
$classification = (int)$data["classi"];
$cols           = $data["col"];
unset($data);
/* first get the data to import from the file */
$ok = strpos($filename, $GLOBALS["covide"]->temppath);
if ($ok === false) {
	die("Gheh, got you. You're hack attempt will be reported to the cops.");
}
$fp = fopen($filename, "r");
$data = fread($fp, filesize($filename));
fclose($fp);
/* replace all data var by pointers to new array */
preg_match_all("/\"[^\"]*?\"/si", $data, $matches);
$matches = $matches[0];
$matches = array_unique($matches);
foreach ($matches as $k=>$v) {
	$data = str_replace($v, "##$k", $data);
	$matches[$k] = substr($v, 1, strlen($v)-2);
}
$data = explode("\n", $data);
/* cleanup empty stuff */
foreach ($data as $k=>$v) {
	if (trim($v) == "") {
		unset($data[$k]);
	}
}
/* remove some columns */
foreach ($cols as $k=>$v) {
	if (!$v) {
		unset($cols[$k]);
	} else {
		/* some columns should go in a secord table */
		switch ($v) {
			case "warning" :
			case "comment" :
				$cols_info[$k] = $v;
				unset($cols[$k]);
				break;
			/* done */
		}
	}
}
/* reset array keys */
reset($cols);
/* default query for every csv record */
$prequery = "INSERT INTO address (".implode(",", $cols).", tav, contact_person, is_public, is_active, is_company, modified) VALUES(";
$postquery = "1, 1, 1, ".mktime().")";
foreach ($data as $l) {
	$nameinfo = array();
	$sql = "";
	$fld = explode(",", $l);
	/* revert ## replacements to actual data */
	foreach ($fld as $x=>$z) {
		if (preg_match("/^##\d{1,}$/si", $z)) {
			$fld[$x] = (int)preg_replace("/^##/si", "", $z);
			$fld[$x] = $matches[$fld[$x]];
		}
	}
	unset($vals);
	$vals = array();

	/* foreach chosen column */
	foreach ($cols as $k=>$colname) {
		/* key position = field name data from array */
		$v = $fld[$k];
		/* do some sanity checks */
		$v = $this->limit_import_field($v, 255);
		$vals[] = $v;
		switch ($colname) {
			case "contact_initials" :
				$nameinfo["contact_initials"] = $v;
				break;
			case "contact_givenname" :
				$nameinfo["contact_givenname"] = $v;
				break;
			case "contact_infix" :
				$nameinfo["contact_infix"] = $v;
				break;
			case "contact_surname" :
				$nameinfo["contact_surname"] = $v;
				break;
		}
	}
	$address_letterinfo = $this->generate_letterinfo($nameinfo);
	$vals[] = $address_letterinfo["tav"];
	$vals[] = $address_letterinfo["contact_person"];
	unset($address_letterinfo);
	/* build insert statement */
	$q = $prequery;
	foreach ($vals as $x) {
		$q .= "'".$x."', ";
	}
	$q .= $postquery;
	sql_query($q);
	#echo $q."<br><br>\n";
	/* prepare stuff for addressinfo table */
	$inserted_id = sql_insert_id("address");
	$q_info  = "INSERT INTO address_info (address_id, classification, warning, comment) VALUES ";
	$warning = "";
	$comment = "";
	if (is_array($cols_info)) {
		foreach ($cols_info as $a=>$b) {
			if ($b == "warning") {
				$warning = $fld[$a];
			}
			if ($b == "comment") {
				$comment = $fld[$a];
			}
		}
	}
	$warning = $this->limit_import_field($warning, 255);
	$comment = addslashes($comment);
	$q_info .= "($inserted_id, $classification, '$warning', '$comment')";
	#echo $q_info."<br><br>\n";
	sql_query($q_info);
}
/* remove the file */
@unlink($filename);
?>
