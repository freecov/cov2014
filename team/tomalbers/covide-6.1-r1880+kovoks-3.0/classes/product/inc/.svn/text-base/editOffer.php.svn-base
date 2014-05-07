<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
if ($id) {
	/* get the data from db */
	$product_data = new Product_data();
	$data = $product_data->getOfferByID($id);
} else {
	/* new record */
	$id='';
        $data["condition"] = "FCA (incoterms 2000)";


        if ($_REQUEST["customerid"] > 0)
        {
            /* new offer, but with a default customer */
            $data["customerid"] = $_REQUEST["customerid"];
        }
}

if ($_REQUEST["reedit"] == "true")
{
      /* init address data object */
       $address_data = new Address_data();
       $data["terms"] =  $address_data->getPaymentRemark($data["customerid"],$data["supplierid"]);
       $data["validity"] = date("U")+3628800 /* 6 weeks */;
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
for ($i=date("Y")-1; $i!=date("Y")+5; $i++) {
            $years[$i] = $i;
}

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("edit offer"));
/* venster object */
$venster_settings = array(
	"title"    => gettext("Offers"),
	"subtitle" => ($id == "")?gettext("new"):gettext("edit")
);

$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "offeredit",
		"action" => "index.php",
		"method" => "post",
		"enctype" => "multipart/form-data"
	));
	$venster->addHiddenField("mod", "product");
	$venster->addHiddenField("action", "saveoffer");
	$venster->addHiddenField("data[id]", $id);
	$venster->addHiddenField("data[date]", $data["date"]);

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
                    /* Only editable if this is not a new offer.*/
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
                $relname = $address_data->getAddressNameById($data["supplierid"]);
                $table->addHiddenField("data[supplierid]", $data["supplierid"]);
                $table->insertTag("span", $relname, array( "id" => "humandatasupplierid" ));
                $table->addSpace(1);
                if ($data["supplierid"] == '')
                {
                    $table->insertAction("edit", gettext("edit"),
                                     "javascript: popup('?mod=address&action=searchRel&sub=leveranciers', 'searchrel',
                                                                   0, 0, 1,'datasupplierid');");
                }
                $table->endTableData();
	$table->endTableRow();

$calendar = new Calendar_output();

if ($data["supplierid"] > 0)
{
        $table->addTableRow();
                $table->insertTableData(gettext("header"), "", "header");
                $table->addTableData("","data");
                $table->addTextField("data[header]", $data["header"], array("style"=> "width: 400px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("condition"), "", "header");
		$table->addTableData("", "data");
                $table->addSelectField("quicklist",$product_data->getConditions(),"","",
                        array("onchange"=>"javascript:datacondition.value=quicklist.value"),"",true);
                $table->addTag("br");
                $table->addTextArea("data[condition]", $data["condition"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("terms of payment"), "", "header");
		$table->addTableData("", "data");
                $table->addTextArea("data[terms]", $data["terms"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("validity offer"), "", "header");
		$table->addTableData("", "data");
                $table->addSelectField("validity[timestamp_day]", $days, date("d", $data["validity"]));
                $table->addSelectField("validity[timestamp_month]", $months, date("m", $data["validity"]));
                $table->addSelectField("validity[timestamp_year]", $years, date("Y", $data["validity"]));
                $table->addCode( $calendar->show_calendar("document.getElementById('validitytimestamp_day')", "document.getElementById('validitytimestamp_month')", "document.getElementById('validitytimestamp_year')" ));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("samples"), "", "header");
		$table->addTableData("", "data");
                $table->addTextArea("data[samples]", $data["samples"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("special discount"), "", "header");
		$table->addTableData("", "data");
                $table->addTextArea("data[discount]", $data["discount"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("remarks"), "", "header");
		$table->addTableData("", "data");
                $table->addTextArea("data[remarks]", $data["remarks"], array("style" => "width: 400px; height: 60px;"));
		$table->endTableData();
	$table->endTableRow();
}
else
        $venster->addHiddenField("reedit", "true");

        $table->addTableRow();
        $table->insertTableData("", "", "header");
        $table->addTableData(array("colspan"=>3));
            $table->insertAction("save", gettext("save"), "javascript: offer_save();");
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
$output->load_javascript(self::include_dir."offer_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
