<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* put classifications is array for the selector */
$clas = array(0 => gettext("geen"));
$classification_data = new Classification_data();
$classifications = $classification_data->getClassifications();
foreach ($classifications as $k=>$v) {
	$clas[$v["id"]] = $v["description"];
}
set_time_limit(60*60*2);

unset($classifications);
/* put possible targets in arry for the selectors */
$dbfields = array(
	gettext("geen actie") => array(
		"0"                 => gettext("overslaan")
	),
	gettext("globale velden") => array(
		"companyname"       => gettext("bedrijfsnaam"),
		"debtor_nr"         => gettext("debiteur nr (numeriek)"),
		"is_customer"       => gettext("is klant (1 of 0)"),
		"is_supplier"       => gettext("is leverancier (1 of 0)")
	),
	gettext("adres velden") => array(
		"address"           => gettext("adres"),
		"zipcode"           => gettext("postcode"),
		"city"              => gettext("stad"),
		"country"           => gettext("land")
	),
	gettext("communicatie velden") => array(
		"phone_nr"          => gettext("telefoon nummer"),
		"fax_nr"            => gettext("fax nummer"),
		"mobile_nr"         => gettext("mobiel nummer"),
		"email"             => gettext("email"),
		"website"           => gettext("website")
	),
	gettext("contact velden") => array(
		"contact_initials"  => gettext("initialen"),
		"contact_givenname" => gettext("voornaam"),
		"contact_infix"     => gettext("tussenvoegsel"),
		"contact_surname"   => gettext("achternaam")
	),
	gettext("postbus velden") => array(
		"pobox"             => gettext("postbus"),
		"pobox_zipcode"     => gettext("postcode postbus"),
		"pobox_city"        => gettext("plaats postbus")
	),
	gettext("overige velden") => array(
		"comment"           => gettext("memo"),
		"warning"           => gettext("letop"),
		"relname"           => gettext("relatie login"),
		"relpass"           => gettext("relatie password")
	)
);

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

		/* if the first line has to be skipped */
		if ($skip_first) {
			unset($data[0]);
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
		$output->addTag("form", array(
			"id"     => "import",
			"name"   => "import",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "address");
		$output->addHiddenField("action", "import_save");
		$output->addHiddenField("filename", $filename);
		$venster = new Layout_venster(array(
			"title"    => gettext("adressen"),
			"subtitle" => gettext("kies kolommen")
		));
		$venster->addVensterData();
			/* classification */
			$table = new Layout_table(array("cellspacing" => 1));
			$table->addTableRow();
				$table->insertTableData(gettext("Stap 2 van 2"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("classificatie"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("classi", $clas);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "data");
					$table->addCode(gettext("Below the first 10 records"));
					$table->addTag("br");
					$table->addCode(gettext("Kies de bijbehorende kolommen uit de lijst."));
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->addTag("br");
			/* preview and mapping */
			$table = new Layout_table(array("cellspacing" => 1));

			$cols = 0;
			foreach ($data as $k=>$v) {
				$t = count($v);
				if ($t > $cols) $cols = $t;
			}
			$table->addTableRow();
				for ($i=0;$i<$cols;$i++) {
					$table->addTableData();
						$table->addSelectField("col[$i]", $dbfields);
					$table->endTableData();
				}
			$table->endTableRow();
			for ($i=0;$i<10;$i++) {
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
			$venster->insertAction("save", gettext("importeren"), "javascript: import_to_step3();");
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
