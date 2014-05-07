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
/* put classifications is array for the selector */
$clas = array(0 => gettext("none"));
$classification_data = new Classification_data();
$classifications = $classification_data->getClassifications();
foreach ($classifications as $k=>$v) {
	$clas[$v["id"]] = $v["description"];
}
set_time_limit(60*60*2);

unset($classifications);
/* put possible targets in arry for the selectors */

if ($_REQUEST["import"]["target"] == "bcard") {
	/* start bcard fields */
	$dbfields = array(
		gettext("no action") => array(
			"0"                 => gettext("skip")
		),
		gettext("business fields") => array(
			"business_phone_nr"   => "b: ".gettext("telephone number"),
			"business_phone_nr_2" => "b: ".gettext("telephone number")." 2",
			"business_car_phone"  => "b: ".gettext("telephone number")." (".gettext("car").")",
			"business_fax_nr"     => "b: ".gettext("fax number"),
			"business_mobile_nr"  => "b: ".gettext("mobile phone number"),
			"business_email"      => "b: ".gettext("email"),
			"business_address"    => "b: ".gettext("address"),
			"business_zipcode"    => "b: ".gettext("zipcode"),
			"business_city"       => "b: ".gettext("city"),
			"business_state"      => "b: ".gettext("state"),
			"business_country"    => "b: ".gettext("country"),
			"jobtitle"            => "b: ".gettext("jobtitle"),
			"businessunit"        => "b: ".gettext("business unit"),
			"department"          => "b: ".gettext("department"),
			"locationcode"        => "b: ".gettext("locationcode"),
			"website"             => "b: ".gettext("website")
		),
		gettext("optional business fields") => array(
			"opt_company_name"      => gettext("companyname"),
			"opt_company_phone_nr"  => gettext("optional telephone number"),
			"opt_callback_phone_nr" => gettext("callback telephone number"),
			"opt_pager_number"      => gettext("pager number"),
			"opt_radio_phone_nr"    => gettext("radio phone number"),
			"opt_telex_number"      => gettext("telex number"),
			"opt_manager_name"      => gettext("manager name"),
			"opt_profession"        => gettext("profession"),
			"opt_assistant_name"    => gettext("assistant name"),
			"opt_assistant_phone_nr"=> gettext("assistant telephone number")
		),
		gettext("personal fields") => array(
			"personal_phone_nr"   => "p: ".gettext("telephone number"),
			"personal_fax_nr"     => "p: ".gettext("fax number"),
			"personal_mobile_nr"  => "p: ".gettext("mobile phone number"),
			"personal_email"      => "p: ".gettext("email"),
			"personal_address"    => "p: ".gettext("address"),
			"personal_zipcode"    => "p: ".gettext("zipcode"),
			"personal_city"       => "p: ".gettext("city"),
			"personal_state"      => "p: ".gettext("state"),
			"personal_country"    => "p: ".gettext("country"),
		),
		gettext("other address fields") => array(
			"other_phone_nr"      => "o: ".gettext("telephone number"),
			"other_phone_nr_2"    => "o: ".gettext("telephone number")." 2",
			"other_fax_nr"        => "o: ".gettext("fax number"),
			"other_mobile_nr"     => "o: ".gettext("mobile phone number"),
			"other_email"         => "o: ".gettext("email"),
			"other_address"       => "o: ".gettext("address"),
			"other_zipcode"       => "o: ".gettext("zipcode"),
			"other_city"          => "o: ".gettext("city"),
			"other_state"         => "o: ".gettext("state"),
			"other_country"       => "o: ".gettext("country")
		),
		gettext("pobox fields") => array(
			"pobox"               => gettext("pobox"),
			"pobox_zipcode"       => gettext("zipcode"),
			"pobox_city"          => gettext("city"),
			"pobox_state"         => gettext("state"),
			"pobox_country"       => gettext("country")
		),
		gettext("contact fields") => array(
			"alternative_name"  => gettext("alternative name"),
			"letterhead"        => gettext("letterhead"),
			"commencement"      => gettext("commencement"),
			"title"             => gettext("title"),
			"initials"          => gettext("initials"),
			"givenname"         => gettext("given name"),
			"infix"             => gettext("infix"),
			"surname"           => gettext("last name"),
			"suffix"            => gettext("suffix"),
			"ssn"               => gettext("bsn"),
			"timestamp_birthday"=> gettext("birthday")
		),
		gettext("misc fields") => array(
			"memo"              => gettext("memo")
		),
		gettext("special fields") => array(
			"classification"    => gettext("add classification"),
			"companyname"       => gettext("try link company by name")
		)
	);
	/* end bcard fields */
} else {
	/* start relation fields */
	$dbfields = array(
		gettext("no action") => array(
			"0"                 => gettext("skip")
		),
		gettext("global fields") => array(
			"companyname"       => gettext("company name"),
			"debtor_nr"         => gettext("debtor nr (numeric)"),
			"is_customer"       => gettext("is customer (1 or 0)"),
			"is_supplier"       => gettext("is supplier (1 or 0)"),
			"branche"           => gettext("branche"),
		),
		gettext("address fields") => array(
			"address"           => gettext("address"),
			"zipcode"           => gettext("zip code"),
			"city"              => gettext("city"),
			"country"           => gettext("country")
		),
		gettext("communication fields") => array(
			"phone_nr"          => gettext("telephone number"),
			"fax_nr"            => gettext("fax number"),
			"mobile_nr"         => gettext("mobile phone number"),
			"email"             => gettext("email"),
			"website"           => gettext("website")
		),
		gettext("contact fields") => array(
			"contact_initials"  => gettext("initials"),
			"contact_givenname" => gettext("given name"),
			"contact_infix"     => gettext("infix"),
			"contact_surname"   => gettext("last name"),
			"contact_letterhead" => gettext("letterhead"),
			"contact_commencement" => gettext("commencement"),
			"title"              => gettext("title"),
			"suffix"             => gettext("suffix"),
			"jobtitle"           => gettext("jobtitle"),
		),
		gettext("pobox fields") => array(
			"pobox"             => gettext("po box"),
			"pobox_zipcode"     => gettext("zip code po box"),
			"pobox_city"        => gettext("city po box"),
			"pobox_country"     => gettext("country po box")
		),
		gettext("misc fields") => array(
			"comment"           => gettext("memo"),
			"warning"           => gettext("warning"),
			"relname"           => gettext("relation login"),
			"relpass"           => gettext("relation password")
		),
		gettext("special fields") => array(
			"classification"    => gettext("add classification")
		)
	);
	/* end of relation fields */
}

$seperator = $_REQUEST["import"]["seperator"];
$skip_first = (int)$_REQUEST["import"]["skip_first"];

/* check if we have a file */
if (array_key_exists("size", $_FILES["import_file"])) {
	if ($_FILES["import_file"]["size"]>0) {


		/* ok, we have a file bigger then 0, process */
		/* do some cleaning on the file */
		//$data = fread(fopen($_FILES["import_file"]["tmp_name"], "r"), $_FILES["import_file"]["size"]);

		$csv_file = fopen($_FILES["import_file"]["tmp_name"], "r");
		$delimiter = ($seperator == "semicolon") ? ";":",";

		/* use fgetcsv */
		while (($line = fgetcsv($csv_file, "", $delimiter)) !== FALSE) {
			$data[] = $line;
		}

		$cols = 0;
		foreach ($data as $k=>$v) {
			$t = count($v);
			if ($t > $cols) $cols = $t;
		}

		/* if the first line has to be skipped */
		$names = array();
		switch ($skip_first) {
			case 3:
				$names[] = $data[2];
				unset($data[2]);
			case 2:
				$names[] = $data[1];
				unset($data[1]);
			case 1:
				$names[] = $data[0];
				unset($data[0]);
			default:
				reset($data);
		}

		/* load the conversion object */
		$conversion = new Layout_conversion();

		/* we need to write this file to a temp store so we can later access it */
		$uniq = strtolower(md5(uniqid(time())));
		$name = "addressimport_".$uniq.".csv";
		$filename = $GLOBALS["covide"]->temppath.$name;
		$fp = fopen($filename, "w");
		fwrite($fp, serialize($data));
		fclose($fp);
		/* start drawing some output */
		$output = new Layout_output();
		$output->layout_page("", 1);
		$output->addTag("div", array("style" => "width: 670px; overflow-x: auto;"));
		$output->addTag("form", array(
			"id"     => "import",
			"name"   => "import",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "address");
		$output->addHiddenField("target", $_REQUEST["import"]["target"]);
		$output->addHiddenField("action", "import_save");
		$output->addHiddenField("filename", $filename);
		$venster = new Layout_venster(array(
			"title"    => gettext("addresses"),
			"subtitle" => gettext("pick columns")
		));
		$venster->addVensterData();
			/* classification */
			$table = new Layout_table(array("cellspacing" => 1));
			$table->addTableRow();
				$table->insertTableData(gettext("Step 2 of 2"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("import target").": ".(($_REQUEST["import"]["target"]=="bcard") ? gettext("business cards"):gettext("relations")), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("classification"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("classi", $clas);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "data");
					$table->addCode(gettext("Below the first 5 records"));
					$table->addTag("br");
					$table->addCode(gettext("Pick matching columns"));
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->addTag("br");
			/* preview and mapping */
			$table = new Layout_table(array("cellspacing" => 1));

			foreach ($names as $k=>$v) {
				$table->addTableRow();
					for ($i=0;$i<$cols;$i++) {
						$table->addTableData();
							$table->insertTag("b", $v[$i]);
						$table->endTableData();
					}
				$table->endTableRow();
			}

			$table->addTableRow();
				for ($i=0;$i<$cols;$i++) {
					$table->addTableData();
						$table->addSelectField("col[$i]", $dbfields);
					$table->endTableData();
				}
			$table->endTableRow();
			for ($i=0;$i<=5;$i++) {
				$t =& $data[$i];
				if (count($t)>0) {
					$table->addTableRow();
					foreach ($t as $k=>$v) {
						$table->addTableData("", "data");
							$table->addCode($conversion->str2utf8($v));
						$table->endTableData();
					}
					$table->endTableRow();
				}
			}
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->addTag("br");
			$venster->insertAction("forward", gettext("import"), "javascript: import_to_step3();");
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->endTag("form");
		$output->load_javascript(self::include_dir."import_actions.js");
		$output->start_javascript();
			$output->addCode("
				addLoadEvent(
					window.resizeTo(screen.width-40, 600)
				);
			");
		$output->end_javascript();
		$output->endTag("div");
		$output->layout_page_end();
		$output->exit_buffer();
	}
}
?>
