<?php
if (!class_exists("Address_data")) {
	die("no class definition found");
}
$titles        = $this->getTitles();
$commencements = $this->getCommencements();
$letterheads   = $this->getLetterheads();

/* generate contact_person */
if ($data["contact_letterhead"] == 2) {
	/* dutch only */
	/* part before names */
	if ($data["contact_commencement"] == 1) {
		$contact_person = "Geachte heer";
	} elseif ($data["contact_commencement"] == 2) {
		$contact_person = "Geachte mevrouw";
	} else {
		$contact_person = "Geachte ".$titles[$data["title"]];
	}
	/* name part */
	$contact_person .= " ".ucfirst(strtolower($data["contact_infix"]))." ".ucwords(strtolower($data["contact_surname"]));
	$return["contact_person"] = trim($contact_person);
} elseif ($data["contact_letterhead"] == 1) {
	/* dutch only */
	$contact_person = "Beste ".ucwords(strtolower($data["contact_givenname"]));
	$return["contact_person"] = trim($contact_person);
} elseif ($data["contact_letterhead"] == 3) {
	/* dear */
	switch ($data["contact_commencement"]) {
		case 4  :
			$contact_person  = "Dear Mr. ".ucfirst(strtolower($data["contact_infix"]));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		case 5  :
			$contact_person  = "Dear Mrs. ".ucfirst(strtolower($data["contact_infix"]));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		case 6  :
			$contact_person  = "Dear Ms. ".ucfirst(strtolower($data["contact_infix"]));
			$contact_person .= " ".ucwords($data["contact_surname"]);
			break;
		default :
			$contact_person  = "Dear ".ucwords(strtolower($data["contact_givenname"]));
			break;
	}
	$return["contact_person"] = preg_replace("/\W{2,}/si", " ", trim($contact_person));
}
/* generate tav field */
$tav  = $commencements[$data["contact_commencement"]]." ";
$tav .= $titles[$data["title"]];
$tav .= strtoupper($data["contact_initials"])." ";
$tav .= strtolower($data["contact_infix"])." ";
$tav .= ucwords(strtolower($data["contact_surname"]));
$return["tav"] = preg_replace("/\W{2,}/si", " ", trim($tav));
?>
