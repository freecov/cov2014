<?php
	/**
	 * Covide Groupware-CRM Addressbook module.
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Gerben Jacobs <ghjacobsk@users.sourceforge.net>
	 * @copyright Copyright 2000-2008 Covide BV
	 * @package Covide
	 */

	if (!class_exists("Address_output")) {
		die("no class definition found");
	}
	
	$conversion = new Layout_conversion();
	
	$address_data = new Address_data;
	/* Titles etc */
	$commencements = $address_data->getCommencements();
	$titles = $address_data->getTitles();
	$letterheads = $address_data->getLetterheads();
	$suffix = $address_data->getSuffix();
	
	foreach ($titles as $title) {
		$data_title[$title["id"]] = $title["title"];
	}
	
	/* Get relation/bcards */
	if ($_REQUEST["type"] != "bcards") {
		$address = $address_data->getRCBCByAddressId($_REQUEST["id"]);
	} else {
		$address = $address_data->getAddressById($_REQUEST["id"], "bcards");
	}
	//print_r($address); die();
	/* Fill the data with the addrress info */
	$data = array(
		"vcard_file"		=> $address["givenname"].$address["infix"].$address["surname"],
		"givenname" 		=> $address["givenname"],
		"surname" 			=> $address["surname"],
		"infix" 			=> $address["infix"],
		"title" 			=> $data_title[$address["title"]],
		"alternative_name" 	=> $address["alternative_name"],
		"company" 			=> $address["companyname"],
		"department" 		=> $address["department"],
		"jobtitle" 			=> $address["jobtitle"],
		"memo" 				=> $address["memo"],
		"phone_work" 		=> $address["business_phone_nr"],
		"phone_personal" 	=> $address["personal_phone_nr"],
		"phone_cell" 		=> $address["personal_mobile_nr"],
		"phone_pager" 		=> $address["opt_pager_number"],
		"fax_home" 			=> $address["personal_fax_nr"],
		"fax_work" 			=> $address["business_fax_nr"],
		"work_office" 		=> $address["businessunit"],
		"work_address" 		=> $address["business_address"],
		"work_zipcode" 		=> $address["business_zipcode"],
		"work_city" 		=> $address["business_city"],
		"work_state" 		=> $address["business_state"],
		"work_country" 		=> $address["business_country"],
		"work_website" 		=> $address["website"],
		"work_email" 		=> $address["business_email"],
		"personal_address" 	=> $address["personal_address"],
		"personal_zipcode"	=> $address["personal_zipcode"],
		"personal_city" 	=> $address["personal_city"],
		"personal_state" 	=> $address["personal_state"],
		"personal_country" 	=> $address["personal_country"],
		"personal_website" 	=> "",
		"personal_email"	=> $address["personal_email"],
		"birthday" 			=> date("Ymd", $address["timestamp_birthday"]),
	);

	$vcard .= "BEGIN:VCARD"."\n";
	$vcard .= "VERSION:2.1"."\n";
	$vcard .= "N:".$data["surname"].";".$data["givenname"].";".$data["infix"].";".$data["title"].""."\n";
	$vcard .= "FN:".$data["givenname"]." ".$data["infix"]." ".$data["surname"]."\n";
	$vcard .= "NICKNAME:".$data["alternative_name"]."\n";
	$vcard .= "ORG:".$data["company"].";".$data["department"]."\n";
	$vcard .= "TITLE:".$data["jobtitle"]."\n";
	$vcard .= "NOTE:".$data["memo"]."\n";
	$vcard .= "TEL;WORK;VOICE:".$data["phone_work"]."\n";
	$vcard .= "TEL;HOME;VOICE:".$data["phone_personal"]."\n";
	$vcard .= "TEL;CELL;VOICE:".$data["phone_cell"]."\n";
	$vcard .= "TEL;PAGER;VOICE:".$data["phone_pager"]."\n";
	$vcard .= "TEL;WORK;FAX:".$data["fax_work"]."\n";
	$vcard .= "TEL;HOME;FAX:".$data["fax_home"]."\n";
	$vcard .= "ADR;WORK:;".$data["work_office"].";".$data["work_address"].";".$data["work_city"].";".$data["work_state"].";".$data["work_zipcode"].";".$data["work_country"]."\n";
	/* Requires Mail MIME encoding - vCard will work without this line though!
	$vcard .= "LABEL;WORK;ENCODING=QUOTED-PRINTABLE:Hoofdkantoor=0D=0ABouwheerstraat 1a=0D=0ABarneveld, Gelderland 3771BL=0D=0AN=ederland";
	*/
	$vcard .= "ADR;HOME:;;".$data["personal_address"].";".$data["personal_city"].";".$data["personal_state"].";".$data["personal_zipcode"].";".$data["personal_country"]."\n";
	/* Requires Mail MIME encoding - vCard will work without this line though!
	$vcard .= "LABEL;HOME;ENCODING=QUOTED-PRINTABLE:Swammerdamlaan 28=0D=0ABennekom, Gelderland 6721 BK=0D=0ANederland";
	*/
	$vcard .= "URL;HOME:".$data["personal_website"]."\n";
	$vcard .= "URL;WORK:".$data["work_website"]."\n";
	$vcard .= "BDAY:".$data["birthday"]."\n";
	$vcard .= "EMAIL;PREF;INTERNET:".$data["work_email"]."\n";
	$vcard .= "EMAIL;INTERNET:".$data["personal_email"]."\n";
	/* TODO: Not too sure what to do with the last number */
	$vcard .= "REV:".date("Ymd", time())."T073813Z"."\n";
	$vcard .= "END:VCARD"."\n";

	header("Content-Transfer-Encoding: binary");
	header("Content-Type: text/x-vcard; charset=UTF-8");

	if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
		header("Content-Disposition: filename=".$data["vcard_file"].".vcf"); //msie 5.5 header bug
	}else{
		header("Content-Disposition: attachment; filename=".$data["vcard_file"].".vcf");
	}
	echo $vcard;
	exit();
?>
