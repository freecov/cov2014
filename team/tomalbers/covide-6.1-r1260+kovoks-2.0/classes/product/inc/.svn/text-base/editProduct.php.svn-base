<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
if ($productid) {
	/* get the data from db */
	$product_data = new Product_data();
	$data = $product_data->getProductByID($productid);
} else {
	/* new record */
	$productid='';
}

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("edit product"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("products"),
	"subtitle" => ($productid == "")?gettext("new"):gettext("edit")
);

$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "productedit",
		"action" => "index.php",
		"method" => "post",
		"enctype" => "multipart/form-data"
	));
	$venster->addHiddenField("mod", "product");
	$venster->addHiddenField("action", "save");
	$venster->addHiddenField("data[id]", $productid);

	/* start building the form in a table object */
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("name"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[name]", $data["name"]);
		$table->addSpace(1);
		$table->addCode(gettext("label"));
		$table->addSpace(1);
		$table->addTextField("data[label]", $data["label"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
                $table->insertTableData(gettext("supplier"), "", "header");
                $table->addTableData("", "data");
		$address_data = new Address_data();
                $relname = $address_data->getAddressNameById($data["supplierid"]);
                $table->addHiddenField("data[supplierid]", $data["supplierid"]);
                $table->insertTag("span", $relname, array( "id" => "humandatasupplierid" ));
                $table->addSpace(1);
                $table->insertAction("edit", gettext("edit"),
                                     "javascript: popup('?mod=address&action=searchRel&sub=leveranciers', 'searchrel',
                                                                   0, 0, 1,'datasupplierid');");
                $table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("region"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[region]", $data["region"]);
		$table->addSpace(1);
		$table->addCode(gettext("type"));
		$table->addSpace(1);
		$table->addTextField("data[prod_type]", $data["prod_type"]);
		$table->addSpace(1);
		$table->addCode(gettext("quality"));
		$table->addSpace(1);
		$table->addTextField("data[quality]", $data["quality"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("year"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[prod_year]", $data["prod_year"]);
		$table->addSpace(1);
		$table->addCode(gettext("price"));
		$table->addSpace(1);
		$table->addTextField("data[price]", $data["price"]);
		$table->addSpace(1);
		$table->addCode(gettext("alcohol"));
		$table->addSpace(1);
		$table->addTextField("data[alcohol]", $data["alcohol"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("content"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[content]", $data["content"]);
		$table->addSpace(1);
		$table->addCode(gettext("box"));
		$table->addSpace(1);
		$table->addTextField("data[box]", $data["box"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("box/layer"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[boxlayer]", $data["boxlayer"]);
		$table->addSpace(1);
		$table->addCode(gettext("pallet"));
		$table->addSpace(1);
		$table->addTextField("data[pallet]", $data["pallet"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("EAN Product"), "", "header");
		$table->addTableData("", "data");
		$table->addTextField("data[EAN_prod]", $data["EAN_prod"]);
		$table->addSpace(1);
		$table->addCode(gettext("EAN Box"));
		$table->addSpace(1);
		$table->addTextField("data[EAN_box]", $data["EAN_box"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("replaced by"), "", "header");
		$table->addTableData("", "data");
                $prod = $product_data->getProductNameById($data["replacement"]);
		$table->addHiddenField("data[replacement]", $data["replacement"]);
                $table->insertTag("span", $prod, array( "id" => "humandatareplacement" ));
                $table->addSpace(1);
                $table->insertAction("edit", gettext("edit"),
                                     "javascript: popup('?mod=product&action=searchProd&sub=active', 'searchprod',
                                                                   0, 0, 1,'datareplacement');");
                $table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("private label"), "", "header");
		$table->addTableData("", "data");
		$table->addCheckBox("data[private]", 1, $data["private"]);
		$table->addSpace(3);
		$table->addCode(gettext("pricelist"));
		$table->addSpace(1);
		$table->addCheckBox("data[pricelist]", 1, $data["pricelist"]);
		$table->endTableData();
	$table->endTableRow();
        $table->addTableRow();
                $table->insertTableData(gettext("remark"), "", "header");
                $table->addTabledata(array("colspan"=>3), "data");
                $table->addTextArea("data[remark]", $data["remark"], array("style" => "width: 400px; height: 100px;"));
                $table->endTableData();
	$table->endTableRow();
        $table->addTableRow();
                $table->insertTableData("", "", "header");
                $table->addTableData(array("colspan"=>3), "data");
                $table->insertAction("save", gettext("save"), "javascript: product_save();");
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
