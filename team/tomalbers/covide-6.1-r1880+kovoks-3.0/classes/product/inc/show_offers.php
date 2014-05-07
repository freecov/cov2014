<?php
if (!class_exists("Product_output")) {
	die("no class definition found");
}
/* make the db object easier to access */
$db = $GLOBALS["covide"]->db;
/* make some URL params easier to access */

$action      = $_REQUEST["action"];
$top         = $_REQUEST["top"];
$l           = $_REQUEST["l"];
$and_or      = $_REQUEST["and_or"];
$state       = $_REQUEST["state"];
$search      = $_REQUEST["search"];
$customerid  = $_REQUEST["customerid"];

if ($top=="") { $top=0; }

$options = Array(
        "top"             => $top,
        "action"          => $action,
        "l"               => $l,
        "and_or"          => $and_or,
        "state"           => $state,
        "search"          => $search,
        "customerid"      => $customerid,
        "sort"            => $_REQUEST["sort"]
);

/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);

$productdata = new Product_data();

if ($_REQUEST["prodcardaction"] == "rem" && $_REQUEST["offerid"]>0) {
    $productdata->delOffer($_REQUEST["offerid"]);
}

if ($_REQUEST["lock"] > 0)
{
    $productdata->lockOffer($_REQUEST["lock"]);
}

/* start output buffer routines */
$output = new Layout_output();
$output->layout_page(gettext("Products"));
$output->start_javascript();
$output->addCode("
	function selectRel(id, prodname, classname) {
		if (opener && opener.selectRel) {
			opener.selectRel(id, prodname, classname);
			setTimeout('window.close();',20);
		} else {
			document.location.href='index.php?mod=product&action=show&productid='+id;
		}
	}
	function zet(naam,waarde) {
		eval('document.getElementById(\'deze\').'+naam+'.value=\''+waarde+'\'');
	}
	function stuur() {
		document.getElementById('deze').submit();
	}
	function selectUser(id) {
		document.location.href='index.php?mod=product&action=usercard&id='+id;
	}
	function selectOther(id, type) {
		document.location.href='index.php?mod=product&action=showother&id='+id+'&type='+type;
	}
");
$output->end_javascript();

$output->addTag("form", array(
	"id"     => "deze",
	"method" => "post",
	"action" => "index.php"
));
$output->addHiddenField("id", "");
$output->addHiddenField("mod", "product");
$output->addHiddenField("customerid", $customerid);
$output->addHiddenField("sort", $_REQUEST["sort"]);
$output->addHiddenField("state", $_REQUEST["state"]);
$output->addHiddenField("action", "showoffers");
$output->addHiddenField("and_or", trim($and_or));

if ($customerid > 0)
{
    $addressdata = new Address_data();
    $subtitle = $addressdata->getAddressNameByID($customerid);
    unset($addressdata);
}
else
    $subtitle = gettext("list");

$venster = new Layout_venster(Array(
	"title"    => gettext("Offers"),
	"subtitle" => $subtitle
));
$venster->addMenuItem(gettext("products"), "index.php?mod=product");
$venster->addMenuItem(gettext("new offer"), "index.php?mod=product&action=editoffer&customerid=".$customerid);
$venster->generateMenuItems();
$venster->addVensterData();

$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addCode( $output->nbspace(3) );
		$table->addCode( gettext("search").": ");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search", stripslashes($_REQUEST["search"]), "", "", 1);
		$table->start_javascript();
			$table->addCode("
				document.getElementById('search').focus();
			");
		$table->end_javascript();
		$table->addSpace(2);
		$table->insertAction("forward", gettext("search"), "javascript: stuur();");
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
        $table->addTableData();
                $table->addCode( $output->nbspace(3) );
                $table->insertLink(gettext("valid"), array(
                                                   "href" => "index.php?mod=product&action=$action&state=valid&customerid=".$_REQUEST["customerid"]) );
                $table->addCode( $output->nbspace(3) );
                $table->insertLink(gettext("expired"), array(
                                                   "href" => "index.php?mod=product&action=$action&state=expired&customerid=".$_REQUEST["customerid"]) );
                $table->addCode( $output->nbspace(3) );
                $table->insertLink(gettext("all"), array(
                                                   "href" => "index.php?mod=product&action=$action&state=all&customerid=".$_REQUEST["customerid"]) );

$table->endTableRow();
$table->endTable();


$venster->addCode($table->generate_output());
unset($table);
$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addSpace(1);
	$table->endTableData();
$table->endTableRow();

$table->endTable();
$venster->addCode($table->generate_output());
unset($table);
$productinfo_arr = $productdata->getOfferList($options);
$table = new Layout_table();
	$table->addTableRow();
		$table->addTableData();
			$table->addSpace(1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData();
			$table->addSpace(1);
			if ($productinfo_arr["total_count"]) {
				$table->addCode(gettext("offers")." ".($productinfo_arr["top"]+1)." ".gettext("to")." ".$productinfo_arr["bottom"]." ".gettext("van de")." ".$productinfo_arr["total_count"]);
			}
		$table->endTableData();
	$table->endTableRow();
$table->endTable();
$venster->addCode($table->generate_output());

$data = $productinfo_arr["product"];
$settings = array(
	#"count"    => 500,
	"current"  => 30,
	"pagesize" => 10,
	"sort"     => $_REQUEST["sort"],
        "state"    => $_REQUEST["state"]
);

$view = new Layout_view();
$view->addData($data);
$view->addSettings($settings);
$view->defineSortForm("sort", "deze");
$view->addMapping("&nbsp;", "%%complex_actions");
$view->addMapping(gettext("id"), "%id");
if ($customerid == '')
    $view->addMapping(gettext("customer"), "%customer_name");
$view->addMapping(gettext("supplier"), "%supplier_name");
$view->addMapping(gettext("date"), "%date_human");
$view->addMapping(gettext("products"), "%products");

$view->defineSort(gettext("id"), "id");
$view->defineSort(gettext("date"), "date");
						 
$view->defineComplexMapping("complex_actions", array(
	array(
		"type"    => "action",
		"src"     => "info",
		"alt"     => gettext("more information"),
		"link"    => array("index.php?mod=product&action=showoffer&offerid=", "%id"),
	),
	array(
		"type"    => "action",
		"src"     => "unlocked",
		"alt"     => gettext("Click to lock this offer"),
                "link"    => array("javascript:if(confirm('This will lock offer ","%id",". Are you sure you want to continue?'))".
                                   "{document.location.href='index.php?mod=product&action=showoffers&lock=","%id","';}"),
                "check"   => "%unlocked"
	),
	array(
		"type"    => "action",
		"src"     => "locked",
		"alt"     => gettext("lock"),
                "check"   => "%locked"
	),
	array(
		"type"    => "action",
		"src"     => "edit",
		"alt"     => gettext("edit"),
		"link"    => array("index.php?mod=product&action=editoffer&id=", "%id"),
                "check"   => "%unlocked"
	),
        array(
                "type"    => "action",
                "src"     => "delete",
                "alt"     => gettext("verwijderen"),
                "link"    => array("javascript:if(confirm('This will delete offer ","%id",". Are you sure you want to continue?'))".
                                   "{document.location.href='index.php?mod=product&customerid=$customerid&action=showoffers&offerid=","%id","&history=".
                                   $_REQUEST["history"]."&prodcardaction=rem';}"),
                "check"   => "%unlocked"
             )
                        ));

$view->defineComplexMapping("complex_productname", array(
        array(
                "type"    => "link",
                "text"    => "%name",
                "link"    => array(     "javascript: selectRel(",
                                        "%id",
                                        ", '",
                                        "%name",
                                        "','",
                                        $_REQUEST["classname"],
                                        "')"
                                )
        )
));


/* if no records, show user a nice message about that */
if (!$productinfo_arr["total_count"]) {
	$venster->addCode(gettext("no offers found"));
	$venster->addCode("<br><br><br>");
} else {
	$venster->addCode( $view->generate_output() );
}
unset($view);

$table = new Layout_table();
$table->addTableRow();
        $table->addTableData(array("style"=>"text-align: right", "colspan"=>1));
                $url = "index.php?mod=product&action=$action&customerid=$customerid&state=$state&addresstype=$addresstype&top=%%&l=$l&search=$search";
                $paging = new Layout_paging();
                $paging->setOptions($top, $productinfo_arr["total_count"], $url);
                $table->addCode( $paging->generate_output() );
        $table->endTableData();
$table->endTableRow();
$venster->addCode($table->generate_output());

$output->addCode($venster->generate_output());

$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );
$output->endTag("form");

$output->layout_page_end();
$output->exit_buffer();
?>
