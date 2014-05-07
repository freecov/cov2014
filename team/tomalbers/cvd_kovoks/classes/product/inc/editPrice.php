<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
if ($id) {
	/* get the data from db */
	$product_data = new Product_data();
	$data = $product_data->getPriceByID($id);

	/* retrieve the productdetails as well. */
	if ($data["productid"] > 0)
		$proddata = $product_data->getProductByID($data["productid"]);
} else {
	/* new record */
	$id='';

        if ($_REQUEST["customerid"] > 0)
        {
            /* new price, but with a default customer */
            $data["customerid"] = $_REQUEST["customerid"];
            $data["start_date"] = date("U");
            $data["end_date"] = mktime(0, 0, 0, 1, 1, date("Y")+1);
        }
}

/* generate arrays for date selectbox */
$days = array();
for ($i=1;$i<=31;$i++) {
            $days[$i] = $i;
}
$months = array();
for ($i=1;$i<=12;$i++) {
            $months[$i] = $i;
}
$years = array();
for ($i=date("Y")-6; $i!=date("Y")+5; $i++) {
            $years[$i] = $i;
}

if ($_REQUEST["reedit"] == "true")
{
        $data["start_date"] = date("U");
        $data["end_date"] = mktime(0, 0, 0, 1, 1, date("Y")+1);
	$proddata["supplierid"] = $_REQUEST["supplierid"];
	print_r($_REQUEST);
}

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("edit price"));
/* venster object */
$venster_settings = array(
	"title"    => gettext("Price agreement"),
	"subtitle" => ($id == "")?gettext("new"):gettext("edit")
);

$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "priceedit",
		"action" => "index.php",
		"method" => "post",
		"enctype" => "multipart/form-data"
	));
	$venster->addHiddenField("mod", "product");
	$venster->addHiddenField("action", "saveprice");
	$venster->addHiddenField("data[id]", $id);

	/* start building the form in a table object */
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
                $table->insertTableData(gettext("customer"), "", "header");
                $table->addTableData("", "data");
		$address_data = new Address_data();
                $relname = $address_data->getAddressNameById($data["customerid"]);
                $table->addHiddenField("data[customerid]", $data["customerid"]);
                $table->insertTag("span", $relname, array( "id" => "humandatacustomerid" ));
                $table->addSpace(1);
                if ($_REQUEST["customerid"] == '' && $data["customerid"] == '')
                {
                    /* Only editable if this is not a new price.*/
                    $table->insertAction("edit", gettext("edit"),
                                     "javascript: popup('?mod=address&action=searchRel&sub=klanten', 'searchrel',
                                                                   0, 0, 1,'datacustomerid');");
                }
                $table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
                $table->insertTableData(gettext("supplier"), "", "header");
                $table->addTableData("", "data");
		$address_data = new Address_data();
                $relname = $address_data->getAddressNameById($proddata["supplierid"]);
                $table->addHiddenField("proddata[supplierid]", $proddata["supplierid"]);
                $table->insertTag("span", $relname, array( "id" => "humanproddatasupplierid" ));
                $table->addSpace(1);
                if ($_REQUEST["supplierid"] == '' && $proddata["supplierid"] == '')
                {
                    /* Only editable if this is not a new price.*/
                    $table->insertAction("edit", gettext("edit"),
                                     "javascript: popup('?mod=address&action=searchRel&sub=leveranciers', 'searchrel',
                                                                   0, 0, 1,'proddatasupplierid');");
                }
                $table->endTableData();
	$table->endTableRow();


$calendar = new Calendar_output();

if ($data["customerid"] > 0 && $proddata["supplierid"] > 0 )
{
        $table->addTableRow();
                $table->insertTableData(gettext("product"), "", "header");
                $table->addTableData("", "data");
                if ($data["productid"])
                      $prod = $product_data->getProductNameById($data["productid"]);
                $table->addHiddenField("data[productid]", $data["productid"]);
                $table->insertTag("span", $prod, array( "id" => "humandataproductid" ));
                $table->addSpace(1);
		$sup = $data["supplierid"];
                $table->insertAction("edit", gettext("edit"),
                    "javascript: popup('?mod=product&action=searchProd&sub=active&supplierid=$sup', 'searchprod',
                               0, 0, 1,'dataproductid');");
                $table->endTableData();
        $table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("product info:"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->insertTag("span", "", array( "id" => "product_info" ));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("offer info:"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->insertTag("span", "", array( "id" => "offer_info" ));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("productnr cust"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->addTextField("data[productnr_cust]", $data["productnr_cust"]);
		$table->endTableData();
	$table->endTableRow();


	$table->addTableRow();
		$table->insertTableData(gettext("price"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->addTextField("data[price]", $data["price"]);
		$table->endTableData();
	$table->endTableRow();


	$table->addTableRow();
		$table->insertTableData(gettext("remarks"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->addTextArea("data[remark]", $data["remark"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("Commission amount/perc/calc"), "", "header");
		$table->addTableData("", "data");
                $table->addTextField("data[commission_amount]", $data["commission_amount"]);
		$table->addTableData("percentage", "data");
                $table->addTextField("data[commission_perc]", $data["commission_perc"]);
		$table->addTableData("calculated", "data");
                $table->addTextField("data[commission_calc]", $data["commission_calc"]);
		$table->endTableData();
	$table->endTableRow();



	$table->addTableRow();
		$table->insertTableData(gettext("valid from"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->addSelectField("valid_start[timestamp_day]", $days, date("d", $data["start_date"]));
                $table->addSelectField("valid_start[timestamp_month]", $months, date("m", $data["start_date"]));
                $table->addSelectField("valid_start[timestamp_year]", $years, date("Y", $data["start_date"]));
                $table->addCode( $calendar->show_calendar("document.getElementById('valid_starttimestamp_day')", "document.getElementById('valid_starttimestamp_month')", "document.getElementById('valid_starttimestamp_year')" ));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("valid to"), "", "header");
		$table->addTableData(array("colspan"=>3), "data");
                $table->addSelectField("valid_end[timestamp_day]", $days, date("d", $data["end_date"]));
                $table->addSelectField("valid_end[timestamp_month]", $months, date("m", $data["end_date"]));
                $table->addSelectField("valid_end[timestamp_year]", $years, date("Y", $data["end_date"]));
                $table->addCode( $calendar->show_calendar("document.getElementById('valid_endtimestamp_day')", "document.getElementById('valid_endtimestamp_month')", "document.getElementById('valid_endtimestamp_year')" ));
		$table->endTableData();
	$table->endTableRow();
}
else
        $venster->addHiddenField("reedit", "true");

        $table->addTableRow();
        $table->insertTableData("", "", "header");
        $table->addTableData(array("colspan"=>3));
            $table->insertAction("save", gettext("save"), "javascript: price_save();");
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
$output->load_javascript(self::include_dir."price_actions.js");
if ($data["productid"] > 0)
{
       $output->addCode("<script>set_product_info(".$data[productid].")</script>");
       $output->addCode("<script>set_offer_info(".$data[customerid].",".$proddata[supplierid].",".$data[productid].")</script>");
}
$output->layout_page_end();
$output->exit_buffer();
?>
