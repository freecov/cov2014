<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
/* get the data from db */
$product_data = new Product_data();

if ($_REQUEST["prodcardaction"] == "rem") {
        $product_data->delProdFromOffer($_REQUEST["offerid"], $_REQUEST["remid"]);
}

if ($_REQUEST["add"] > 0) {
        $product_data->addProdToOffer($_REQUEST["offerid"], $_REQUEST["add"]);
}

if ($_REQUEST["subaction"] == "change")
{
    $product_data->changeOfferPrice($_REQUEST["offerprod"], $_REQUEST["price"]);
}

if ($_REQUEST["subaction"] == "changequantity")
{
    $product_data->changeOfferQuantity($_REQUEST["offerprod"], $_REQUEST["minorder"]);
}

$data[0] = $product_data->getOfferByID($_REQUEST["offerid"]);
$products_data = $product_data->getProductsFromOffer($_REQUEST["offerid"]);

$offerid = $_REQUEST["offerid"];
$address = new Address_data();
$data[0]["customer_name"] = $address->getAddressNameByID($data[0][customerid]);
$data[0]["supplier_name"] = $address->getAddressNameByID($data[0][supplierid]);

$output = new Layout_output();
$output->layout_page(gettext("Offer"));

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();

/* nice window widget */
$venster = new Layout_venster(array(
	"title"    => gettext("Offer"),
	"subtitle" => gettext("details")
));

/* Managers can always edit */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
if ($data[0]["unlocked"] || $user->permissions["xs_offermanage"])
    $venster->addMenuItem(gettext("edit"), "index.php?mod=product&action=editoffer&id=$offerid");
unset($user);

/* 
 * There can be more pdf outputs. Loop throught the contents of the template
 * and use the filename as a name.
 */
$cur_path = ereg_replace("index.php","classes/product/inc/templates",$_SERVER["SCRIPT_FILENAME"]);
if ($handle = opendir($cur_path)) 
{
   while (false !== ($file = readdir($handle))) 
   {
       if (!ereg(".php$",$file))
           continue;
       $file = ereg_replace(".php","",$file);
       $venster->addMenuItem( $file . gettext(" pdf"), "index.php?mod=product&action=pdfoffer&id=$offerid&template=$file",1);
   }
}
                  
                 


$venster->generateMenuItems();
$venster->addVensterData();

	/* put a view here */
	$view = new Layout_view();
	$view->addData($data);
	$view->addMapping(gettext("id"), "%id");
	$view->addMapping(gettext("customer"), "%customer_name");
	$view->addMapping(gettext("supplier"), "%supplier_name");
	$view->addMapping(gettext("header"), "%header");
	$view->addMapping(gettext("condition"), "%condition");
	$view->addMapping(gettext("terms"), "%terms");
	$view->addMapping(gettext("validity"), "%validity_human");
	$view->addMapping(gettext("samples"), "%samples");
	$view->addMapping(gettext("discount"), "%discount");
	$view->addMapping(gettext("remarks"), "%remarks");
	$view->addMapping(gettext("date"), "%date_human");

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


$venster2 = new Layout_venster(array("title"=>gettext("Products")));
$venster2->addVensterData();
        $view = new Layout_view();
        $view->addMapping("", "%%complex_actions");
        $view->addData($products_data);
        $view->addMapping(gettext("Name"), "%name");
        $view->addMapping(gettext("Label"), "%label");
        $view->addMapping(gettext("Year"), "%prod_year");
        $view->addMapping(gettext("Box"), "%box");
        $view->addMapping(gettext("Content"), "%content");
        $view->addMapping(gettext("Listprice"), "%price");
        $view->addMapping(gettext("Offerprice"), "%%editofferprice");
        $view->addMapping(gettext("Min.Quantity"), "%%editquant");
        $view->defineComplexMapping("complex_actions", array(
                array(
                        "type"    => "action",
                        "src"     => "delete",
                        "alt"     => gettext("delete"),
                        "link"    => array("javascript:if(confirm('This will delete this entry. Are you sure you want to continue?'))".
                                           "{document.location.href='index.php?mod=product&action=showoffer&offerid=$offerid&history=".
                                           $_REQUEST["history"]."&prodcardaction=rem&remid=", "%id", "';}"),
                        "check"   => "%unlocked"
                      )
                ));
        $view->defineComplexMapping("editofferprice", array(
                array(  "type"    => "text",
                        "text"    => "%offerprice"
                     ),
                array(
                        "type"    => "action",
                        "src"     => "edit",
                        "alt"     => gettext("edit offer price"),
                        "link"    => array("javascript:edit_offer_price(","%price",",","%offerprice",",","$offerid",",","%offerprodid",")"),
                        "check"   => "%unlocked"
                    )
                ));
        $view->defineComplexMapping("editquant", array(
                array(  "type"    => "text",
                        "text"    => "%minorder"
                     ),
                array(
                        "type"    => "action",
                        "src"     => "edit",
                        "alt"     => gettext("edit minimum quantity"),
                        "link"    => array("javascript:edit_quantity(","%minorder",",","$offerid",",","%offerprodid",")"),
                        "check"   => "%unlocked"
                    )
                ));
        $venster2->addCode($view->generate_output());
$venster2->endVensterData();
if ($data[0]["unlocked"])
    $venster2->insertAction("new", gettext("add_entry"),
        "javascript: popup('?mod=product&action=searchProd&supplierid=".($data[0][supplierid])."', 'searchprod', 0, 0, 1,'$offerid');");



// ------------------- Generate the output.

$buf1->addCode($venster->generate_output());
$buf2->addCode($venster2->generate_output());
unset($venster);
unset($venster2);

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();
$output->addCode($tbl->generate_output());
$output->load_javascript(self::include_dir."offer_actions.js");
$output->addTag("br");
$output->addTag("center");
$output->insertAction("back", gettext("terug"), "?mod=product&action=showoffers&customerid=".$data[0]["customerid"]);
$output->endTag("center");
$output->layout_page_end();
$output->exit_buffer();
?>
