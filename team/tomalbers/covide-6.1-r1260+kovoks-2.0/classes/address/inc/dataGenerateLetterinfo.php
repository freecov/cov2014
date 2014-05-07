<?php
if (!class_exists("Address_data")) {
	die("no class definition found");
}
$titles        = $this->getTitles();
$commencements = $this->getCommencements();
$letterheads   = $this->getLetterheads();


/* make sure initials are always ending with a dot (.) */
if (!preg_match("/\.$/s", $data["contact_initials"]) && preg_match("/[a-z]/si", $data["contact_initials"]))
	$data["contact_initials"].=".";

/* generate contact_person */
if ($data["contact_letterhead"] == 2) {
	/* dutch only */
	/* part before names */
	if ($data["contact_commencement"] == 1) {
		$contact_person = "Geachte heer";
	} elseif ($data["contact_commencement"] == 2) {
		$contact_person = "Geachte mevrouw";
	} else {
		$contact_person = "Geachte heer/mevrouw ".$titles[$data["title"]];
	}
	/* name part */
	$contact_person .= " ".ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"))." ".ucwords(mb_strtolower($data["contact_surname"]));
	$return["contact_person"] = trim($contact_person);
} elseif ($data["contact_letterhead"] == 1) {
	/* dutch only */
	$contact_person = "Beste ".ucwords(mb_strtolower($data["contact_givenname"], "UTF-8"));
	$return["contact_person"] = trim($contact_person);
} elseif ($data["contact_letterhead"] == 3) {
	/* dear */
	switch ($data["contact_commencement"]) {
		case 4  :
			$contact_person  = "Dear Mr. ".ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		case 5  :
			$contact_person  = "Dear Mrs. ".ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		case 6  :
			$contact_person  = "Dear Ms. ".ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		default :
			$contact_person  = "Dear ".ucwords(mb_strtolower($data["contact_givenname"], "UTF-8"));
			break;
	}
	$return["contact_person"] = preg_replace("/\s{2,}/si", " ", trim($contact_person));
}
/* generate tav field */
//$tav  = $commencements[$data["contact_commencement"]]." ";

if ($data["contact_commencement"] == 1) {
	$tav = "De heer ";
} elseif ($data["contact_commencement"] == 2) {
	$tav = "Mevrouw ";
}

$tav .= $titles[$data["title"]]." ";
$tav .= mb_strtoupper($data["contact_initials"], "UTF-8")." ";
$tav .= mb_strtolower($data["contact_infix"], "UTF-8")." ";
$tav .= ucwords(mb_strtolower($data["contact_surname"], "UTF-8"));
$return["tav"] = ucfirst( trim(preg_replace("/ {2,}/si", " ", $tav)) );
?>
