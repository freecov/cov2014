<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}

$product_data = new Product_data();
if ($nosellid) {
	/* get the data from db */
	$d = $product_data->getNoSellDataByID($nosellid);
	$data = $d[0];
} else {
	/* new record */
	$nosellid='';
}


/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("edit entry"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("No sell entry"),
	"subtitle" => ($nosellid == "")?gettext("new"):gettext("edit")
);

$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "noselledit",
		"action" => "index.php",
		"method" => "post",
		"enctype" => "multipart/form-data"
	));
	$venster->addHiddenField("mod", "product");
	$venster->addHiddenField("action", "nosellsave");
	$venster->addHiddenField("data[id]", $nosellid);
	$venster->addHiddenField("data[productid]", $productid);

	/* start building the form in a table object */
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("Country"), "", "header");
		$table->addTableData("", "data");
		$countries = $product_data->getCountryList();
		sort($countries);
		if (! in_array($data["country"], $countries))
			$countries = array_merge(array($data["country"]=>$data["country"]), $countries);
		$table->addSelectField("data[country]", $countries, $data["country"],"","","", true);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
                $table->insertTableData(gettext("Zipcode"), "", "header");
                $table->addTableData("", "data");
		$zips = array();
		$zips = explode("|",$data["zip"]);
		$i2=0;
		for ($i = 0 ; $i < 100 ; $i++) {
			if ($i < 10) $s = "0".$i;  else  $s = $i;
			$table->addCheckbox("data[zips][]", $s, in_array($s,$zips));
			$table->addCode($s."&nbsp;");
			$i2++;
			if ($i2 > 9) {
				$table->addCode("<br>");
				$i2 = 0;
			}
		}
                $table->endTableData();
	$table->endTableRow();
        $table->addTableRow();
                $table->insertTableData("", "", "header");
                $table->addTableData(array("colspan"=>3), "data");
                $table->insertAction("save", gettext("save"), "javascript: nosell_save();");
                $table->endTableData();
        $table->endTableRow();
	$table->endTable();
	/* end table object */

	$venster->addCode($table->generate_output());
	unset($table);
	$venster->endTag("form");
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
/* end of venster object */
$output->load_javascript(self::include_dir."product_edit.js");
$output->layout_page_end();
$output->exit_buffer();
?>
