<?php
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
unset($classifications);
/* put possible targets in arry for the selectors */
$dbfields = array(
	"0"                 => gettext("skip"),
	"companyname"       => gettext("company name"),
	"address"           => gettext("address"),
	"zipcode"           => gettext("zip code"),
	"city"              => gettext("city"),
	"country"           => gettext("country"),
	"phone_nr"          => gettext("telephone number"),
	"fax_nr"            => gettext("fax number"),
	"mobile_nr"         => gettext("mobile phone number"),
	"email"             => gettext("email"),
	"website"           => gettext("website"),
	"comment"           => gettext("memo"),
	"warning"           => gettext("warning"),
	"contact_initials"  => gettext("initials"),
	"contact_givenname" => gettext("given name"),
	"contact_infix"     => gettext("insertion"),
	"contact_surname"   => gettext("last name"),
	"pobox"             => gettext("po box"),
	"pobox_zipcode"     => gettext("zip code po box"),
	"pobox_city"        => gettext("city po box")
);

$seperator = $_REQUEST["import"]["seperator"];
/* check if we have a file */
if (array_key_exists("size", $_FILES["import_file"])) {
	if ($_FILES["import_file"]["size"]>0) {
		/* ok, we have a file bigger then 0, process */
		/* do some cleaning on the file */
		$data = fread(fopen($_FILES["import_file"]["tmp_name"], "r"), $_FILES["import_file"]["size"]);
		if ($seperator == "semicolon") {
			/* replace all data vars by pointers */
			preg_match_all("/\"[^\"]*?\"/si", $data, $matches);
			$matches = $matches[0];
			$matches = array_unique($matches);
			foreach ($matches as $k=>$v) {
				$data = str_replace($v, "##$k", $data);
				$matches[$k] = substr($v, 1, strlen($v)-2);
				$matches[$k] = str_replace(",",".",$matches[$k]);
			}
			/* convert semicolon to comma */
			$data = str_replace(";", ",", $data);
			/* replace all pointers with the data */
			foreach ($matches as $k=>$v) {
				$data = str_replace("##$k", $v, $data);
			}
		}
		/* we need to write this file to a temp store so we can later access it */
		$uniq = strtolower(md5(uniqid(time())));
		$name = "addressimport_".$uniq.".csv";
		$filename = $GLOBALS["covide"]->temppath.$name;
		$fp = fopen($filename, "w");
		fwrite($fp, $data);
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
				$table->insertTableData(gettext("classification"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("classi", $clas);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "data");
					$table->addCode(gettext("Hieronder ziet u (ingekort) de eerste 10 adressen."));
					$table->addTag("br");
					$table->addCode(gettext("Pick matching columns"));
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->addTag("br");
			/* preview and mapping */
			$table = new Layout_table(array("cellspacing" => 1));
			/* for easier processing, replace data by pointers to temp store */
			preg_match_all("/\"[^\"]*?\"/si", $data, $matches);
			$matches = $matches[0];
			$matches = array_unique($matches);
			foreach ($matches as $k=>$v) {
				$data = str_replace($v, "##$k",$data);
				$matches[$k] = substr($v, 1, strlen($v)-2);
			}
			$data = explode("\n",$data);
			$cols = 0;
			foreach ($data as $k=>$v) {
				$t = substr_count($v,",")+1;
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
				$t = explode(",",$data[$i]);
				if (count($t)>0) {
					$table->addTableRow();
					foreach ($t as $k=>$v) {
						$table->addTableData("", "data");
							if (preg_match("/^##\d{1,}$/si",$v)) {
								$val = number_format( preg_replace("/^##/si","",$v) );
								$val = $matches[$val];
							} else {
								$val = $v;
							}
							$table->addCode($val);
						$table->endTableData();
					}
					$table->endTableRow();
				}
			}
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->addTag("br");
			$venster->insertAction("save", gettext("import"), "javascript: import_to_step3();");
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
