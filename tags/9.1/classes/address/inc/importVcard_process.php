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
set_time_limit(60*60*2);

/* check if we have a file */
if (array_key_exists("size", $_FILES["import_file"])) {
	if ($_FILES["import_file"]["size"]>0) {

	/* Get file content */
	$vcf_file = file_get_contents($_FILES["import_file"]["tmp_name"]);
	
	/* Function to get lines */
	function getLineFrom($start, $data) {
		$r = explode($start, $data);
		if (isset($r[1])){
			$r = explode("\n", $r[1]);
			return $r[0];
		}
	}
	
	/* Lines of vCards */
	$lines = explode("\n", trim($vcf_file));
	foreach ($lines AS $line) {
		$explode = explode(":", $line);
		$property[] = trim($explode[0]);
		if ($explode[1] == "http") {
			$values[] = trim($explode[1].":".$explode[2]);
		} else {
			$values[] = trim($explode[1]);
		}
	}

	/* Flip properties so we can extract IDs */
	$property = array_flip($property);
	$value["name"] = explode(";", $values[$property["N"]]);
	$value["fullname"] = $values[$property["FN"]];
	$value["nickname"] = $values[$property["NICKNAME"]];
	$value["org"] = explode(";", $values[$property["ORG"]]);
	$value["title"] = $values[$property["TITLE"]];
	$value["note"] = $values[$property["NOTE"]];
	$value["email_work"] = $values[$property["EMAIL;PREF;INTERNET"]];
	$value["email_home"] = $values[$property["EMAIL;INTERNET"]];
	$value["bday"] = $values[$property["BDAY"]];
	$value["website"] = $values[$property["URL"]];
	if ($values[$property["URL;WORK"]]) {
		$value["website"] = $values[$property["URL;WORK"]];
	}
	
	/* If specific keywords are in the property name, overwrite values */
	/* flip it again (It must get dizzy!) */
	$property = array_flip($property);
	foreach ($property as $id=>$prop) {
		if (strstr($prop, "TEL") && strstr($prop, "WORK") && strstr($prop, "VOICE")) {
			$value["phone_work"] = $values[$id];
		}
			/* Nothing found? Try simpling it down.. */
			if (!$value["phone_work"] && (strstr($prop, "TEL") && strstr($prop, "WORK"))) {
				$value["phone_work"] = $values[$id];
			}
		if (strstr($prop, "TEL") && strstr($prop, "WORK") && strstr($prop, "FAX")) {
			$value["fax_work"] = $values[$id];
		}
		if (strstr($prop, "TEL") && strstr($prop, "HOME") && strstr($prop, "VOICE")) {
			$value["phone_home"] = $values[$id];
		}
			/* Nothing found? Try simpling it down.. */
			if (!$value["phone_home"] && (strstr($prop, "TEL") && strstr($prop, "HOME"))) {
				$value["phone_home"] = $values[$id];
			}
		if (strstr($prop, "TEL") && strstr($prop, "HOME") && strstr($prop, "FAX")) {
			$value["fax_home"] = $values[$id];
		}
		if (strstr($prop, "TEL") && strstr($prop, "CELL") && strstr($prop, "VOICE")) {
			$value["phone_cell"] = $values[$id];
		}
		if (strstr($prop, "TEL") && strstr($prop, "PAGER") && strstr($prop, "VOICE")) {
			$value["phone_pager"] = $values[$id];
		}
		if (strstr($prop, "ADR") && strstr($prop, "HOME")) {
			$value["home"] = explode(";", $values[$id]);
		}
		if (strstr($prop, "ADR") && strstr($prop, "WORK")) {
			$value["work"] = explode(";", $values[$id]);
		}
		if (!$value["email_home"] && (strstr($prop, "EMAIL") && strstr($prop, "HOME"))) {
			$value["email_home"] = $values[$id];
		}
		if (!$value["email_work"] && (strstr($prop, "EMAIL") && strstr($prop, "WORK"))) {
			$value["email_work"] = $values[$id];
		}
			/* Nothing found? Try simpling it down.. */
			if (!$value["email_work"] && strstr($prop, "EMAIL")) {
				$value["email_work"] = $values[$id];
			}
	}
	
	$data = array(
		"givenname" 		=> $value["name"][1],
		"surname" 			=> $value["name"][0],
		"infix" 			=> $value["name"][2],
		"title" 			=> $value["name"][3],
		"alternative_name" 	=> $value["fullname"],
		"company" 			=> $value["org"][0],
		"department" 		=> $value["org"][1],
		"jobtitle" 			=> $value["title"],
		"memo" 				=> $value["note"],
		"phone_work" 		=> $value["phone_work"],
		"phone_home" 		=> $value["phone_home"],
		"phone_cell" 		=> $value["phone_cell"],
		"phone_pager" 		=> $value["phone_pager"],
		"fax_home" 			=> $value["fax_home"],
		"fax_work" 			=> $value["fax_work"],
		"work_office" 		=> $value["work"][1],
		"work_address" 		=> $value["work"][2],
		"work_zipcode" 		=> $value["work"][5],
		"work_city" 		=> $value["work"][3],
		"work_state" 		=> $value["work"][4],
		"work_country" 		=> $value["work"][6],
		"work_website" 		=> $value["website"],
		"work_email" 		=> $value["email_work"],
		"personal_address" 	=> $value["home"][2],
		"personal_zipcode"	=> $value["home"][5],
		"personal_city" 	=> $value["home"][3],
		"personal_state" 	=> $value["home"][4],
		"personal_country" 	=> $value["home"][6],
		"home_email"		=> $value["email_home"],
		"birthday" 			=> strtotime($value["bday"]),
		"website"		=> $value["website"],
	);

	/* we need to write this file to a temp store so we can later access it */
	$uniq = strtolower(md5(uniqid(time())));
	$name = "vcardimport_".$uniq.".vcf";
	$filename = $GLOBALS["covide"]->temppath.$name;
	$fp = fopen($filename, "w");
	fwrite($fp, serialize($data));
	fclose($fp);
		
	/* start drawing some output */
	$output = new Layout_output();
	$output->layout_page("", 1);
	$output->addTag("form", array(
		"id"     => "importVcard_process",
		"name"   => "import",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "importVcard_save");
	$output->addHiddenField("filename", $filename);
	$venster = new Layout_venster(array(
		"title"    => gettext("addresses"),
		"subtitle" => gettext("pick relation")
	));
	$venster->addVensterData();
		/* classification */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("Step 2 of 2"), array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Is this vCard related to")."..", array("colspan" => 2), "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData("", "data");
			/* company name checks */
			$address_data = new Address_data;
			$company_id = $address_data->getRelationIdByName($data["company"]);
			$company_name = ($company_id) ? $data["company"] : gettext("none");
				
			$table->addHiddenField("vcard[address_id]", $company_id);
				$table->insertTag("span", $company_name, array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
		$venster->addTag("br");
		$venster->insertAction("forward", gettext("import"), "javascript: vCardSave();");
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."import_actions.js");
	$output->layout_page_end();
	$output->exit_buffer();
	}
}
?>
