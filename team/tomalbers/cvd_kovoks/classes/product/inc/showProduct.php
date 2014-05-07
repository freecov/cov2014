<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
/* get the data from db */
$product_data = new Product_data();

if ($_REQUEST["prodcardaction"] == "rem") {
        $product_data->remove_nosell($_REQUEST["nosellid"]);
}

$data[0] = $product_data->getProductByID($productid);
$no_sell_data = $product_data->getNoSellDataByProductId($productid);

/* get the meta data for this address record */
$meta_data = new Metafields_data();
$meta_output = new Metafields_output();
$metafields = $meta_data->meta_list_fields("products", $productid);

foreach ($metafields as $v) {
	$data[0][$v["fieldname"]] = $meta_output->meta_print_field($v);
}


$address = new Address_data();
$data[0]["supplier_name"] = $address->getAddressNameByID($data[0][supplierid]);

$data[0]["private"] ? $data[0]["private"]=gettext("Yes") : $data[0]["private"]=gettext("No");
$data[0]["pricelist"] ? $data[0]["pricelist"]=gettext("Yes") : $data[0]["pricelist"]=gettext("No");

$output = new Layout_output();
$summary ?  $output->layout_page(gettext("Products"),1) : $output->layout_page(gettext("Products"));

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

/* nice window widget */
$venster = new Layout_venster(array(
	"title"    => gettext("Products"),
	"subtitle" => gettext("details")
));

if (!$summary)
{
	$venster->addMenuItem(gettext("close"), "javascript: window.close();");
	$venster->addMenuItem(gettext("edit"), "index.php?mod=product&action=edit&productid=$productid");
	$venster->generateMenuItems();
}
$venster->addVensterData();

	/* put a view here */
	$view = new Layout_view();
	$view->addData($data);
	$view->addMapping(gettext("id"), "%id");
	$view->addMapping(gettext("name"), "%name");
	$view->addMapping(gettext("label"), "%label");
	$view->addMapping(gettext("supplier"), "%supplier_name");
	$view->addMapping(gettext("year"), "%prod_year");
	$view->addMapping(gettext("content"), "%content");
	$view->addMapping(gettext("amount in box"), "%box");
	$view->addMapping(gettext("box / layer"), "%boxlayer");
	$view->addMapping(gettext("amount on pallet"), "%pallet");
	$view->addMapping(gettext("region"), "%region");
	$view->addMapping(gettext("replacement"), "%replacement_human");
	$view->addMapping(gettext("remark"), "%remark");
	$view->addMapping(gettext("type"), "%prod_type");
	$view->addMapping(gettext("quality"), "%quality");
	$view->addMapping(gettext("price"), "%price");
	$view->addMapping(gettext("alcohol"), "%alcohol");
	$view->addMapping(gettext("EAN product"), "%EAN_prod");
	$view->addMapping(gettext("EAN box"), "%EAN_box");
	$view->addMapping(gettext("private label"), "%private");
	$view->addMapping(gettext("pricelist"), "%pricelist");

	if (count($metafields)) {
		foreach ($metafields as $v) {
			$database_mapping = "%".$v["fieldname"];
			$view->addMapping($v["fieldname"], $database_mapping);
		}
	}
	$table = new Layout_table();
	$table->addTableRow();
		$table->addTableData(array("vertical-align" => "top"), "top");
			$table->addCode($view->generate_output_vertical());
		$table->endTableData();
	#	$table->addTableData(arraydd("vertical-align" => "top"), "top");
	#		if ($bcardinfo[0]["photourl"]) {
	#			$table->addCode("<img src=\"".$bcardinfo[0]["photourl"]."\">");
	#		}
	#	$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster->addCode($table->generate_output());
	unset($view);

$venster->endVensterData();

// ----------------- Second column...

if (!$summary)
{
	$venster2 = new Layout_venster(array("title"=>gettext("Do not sell in")));
	$venster2->addVensterData();
        $view = new Layout_view();
        $view->addMapping("", "%%complex_actions");
        $view->addData($no_sell_data);
        $view->addMapping(gettext("Country"), "%country");
        $view->addMapping(gettext("Zipcode"), "%zipcode");
        $view->defineComplexMapping("complex_actions", array(
                array(
                        "type"    => "action",
                        "src"     => "edit",
                        "alt"     => gettext("bewerken"),
                        "link"    => array("index.php?mod=product&action=noselledit&nosellid=","%id","&productid=",$productid)),
                array(
                        "type"    => "action",
                        "src"     => "delete",
                        "alt"     => gettext("verwijderen"),
                        "link"    => array("javascript:if(confirm('This will delete this entry. Are you sure you want to continue?')){document.location.href='index.php?mod=product&action=show&productid=$productid&history=".$_REQUEST["history"]."&prodcardaction=rem&nosellid=", "%id", "';}"))
                ));
        $venster2->addCode($view->generate_output());
$venster2->endVensterData();
$venster2->insertAction("new", gettext("add entry"),
               "index.php?mod=product&action=noselledit&productid=$productid");
}

// ------------------- Generate the output.

$buf1->addCode($venster->generate_output());
if (!$summary)
	$buf2->addCode($venster2->generate_output());
unset($venster);
unset($venster2);

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
if (!$summary)
	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();

$output->addCode($tbl->generate_output());
$output->layout_page_end();
$output->exit_buffer();
?>
