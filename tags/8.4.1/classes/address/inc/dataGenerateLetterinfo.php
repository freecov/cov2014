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

/* modified to improve speed and memory usage even more */
class Address_data_letterinfo {

	static private $_titles;
	static private $_commencements;
	static private $_letterheads;
	static private $_visitor;
	static private $_inited = false;
	
	static public function add($visitor = null) {
		self::$_visitor = $visitor;
	}
	static public function generateLetterInfo($data) {
		if (!self::$_inited) {
			self::$_inited = true;
			self::$_titles        = self::$_visitor->getTitles();
			self::$_commencements = self::$_visitor->getCommencements();
			self::$_letterheads   = self::$_visitor->getLetterheads();
		}
		
		$titles =& self::$_titles;
		$commencements =& self::$_commencements;
		$letterheads =& self::$_letterheads;

		/* we need the conversion class to do multibyte ucfirst */
		$conversion = new Layout_conversion();

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
			$contact_person .= " ".$conversion->mb_ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"), "UTF-8")." ".$conversion->mb_ucfirst(mb_strtolower($data["contact_surname"], "UTF-8"), "UTF-8");
			$return["contact_person"] = trim($contact_person);
		} elseif ($data["contact_letterhead"] == 1) {
			/* dutch only */
			$contact_person = "Beste ".mb_convert_case(mb_strtolower($data["contact_givenname"], "UTF-8"), MB_CASE_TITLE, "UTF-8");
			$return["contact_person"] = trim($contact_person);
		} elseif ($data["contact_letterhead"] == 3) {
			/* dear */
			switch ($data["contact_commencement"]) {
				case 4  :
					$contact_person  = "Dear Mr. ".$conversion->mb_ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"), "UTF-8");
					$contact_person .= " ".$conversion->mb_ucfirst(mb_strtolower($data["contact_surname"]), "UTF-8");
					break;
				case 5  :
					$contact_person  = "Dear Mrs. ".$conversion->mb_ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"), "UTF-8");
					$contact_person .= " ".$conversion->mb_ucfirst(mb_strtolower($data["contact_surname"]), "UTF-8");
					break;
				case 6  :
					$contact_person  = "Dear Ms. ".$conversion->mb_ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"), "UTF-8");
					$contact_person .= " ".$conversion->mb_ucfirst(mb_strtolower($data["contact_surname"]), "UTF-8");
					break;
				default :
					$contact_person  = "Dear ".mb_convert_case(mb_strtolower($data["contact_givenname"], "UTF-8"), MB_CASE_TITLE, "UTF-8");
					break;
			}
			$return["contact_person"] = preg_replace("/\s{2,}/si", " ", trim($contact_person));
		}
		/* generate tav field */
		//$tav  = $commencements[$data["contact_commencement"]]." ";

		//XXX: this is ugly, needs to be fixed.
		//$contact_person = $letterheads[$data["contact_letterhead"]]["title"]." ".$commencements[$data["contact_commencement"]]["title"];
		//$contact_person .= " ".$conversion->mb_ucfirst(mb_strtolower($data["contact_infix"], "UTF-8"), "UTF-8")." ".$conversion->mb_ucfirst(mb_strtolower($data["contact_surname"], "UTF-8"), "UTF-8");
		$return["contact_person"] = trim($contact_person);

		if ($data["contact_commencement"] == 1) {
			$tav = "De heer ";
		} elseif ($data["contact_commencement"] == 2) {
			$tav = "Mevrouw ";
		}

		$surname = mb_strtolower($data["contact_surname"], "UTF-8");

		$surname_parts = preg_split("/( |\-)/s", $surname, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($surname_parts as $k=>$v) {
			$surname_parts[$k] = $conversion->mb_ucfirst($v, "UTF-8");
		}
		$surname = implode("", $surname_parts);


		$tav .= $titles[$data["title"]]["title"]." ";
		$tav .= mb_strtoupper($data["contact_initials"], "UTF-8")." ";
		$tav .= mb_strtolower($data["contact_infix"], "UTF-8")." ";
		$tav .= $surname;
		$tav = trim(preg_replace("/ {2,}/si", " ", $tav));
		$return["tav"] = $tav;
		
		return $return;
	}
}
?>
