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
$sub         = $_REQUEST["sub"];
$and_or      = $_REQUEST["and_or"];
$search      = $_REQUEST["search"];
$supplierid  = $_REQUEST["supplierid"];
$classname   = $_REQUEST["classname"];

if (!$sub)
	$sub = "active";

if ($top=="") { $top=0; }

$options = Array(
        "top"             => $top,
        "action"          => $action,
        "l"               => $l,
        "sub"             => $sub,
        "and_or"          => $and_or,
        "search"          => $search,
        "supplierid"      => $supplierid,
        "sort"            => $_REQUEST["sort"],
	"classname"	  => $_REQUEST["classname"]
);

/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);

$productdata = new Product_data();

/* start output buffer routines */
$output = new Layout_output();

if ($_REQUEST["action"]=="searchProd") {
            $output->layout_page(gettext("Products"), 1);
} else {
            $output->layout_page(gettext("Products"));
}


$output->start_javascript();
$output->addCode("
        function selectProd(id, prodname, classname) {
                 if (opener && opener.selectProd) {
                        opener.selectProd(id, prodname, classname);
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
$output->addHiddenField("sort", $_REQUEST["sort"]);
$output->addHiddenField("action", "Zoek");
$output->addHiddenField("classname", $classname);
$output->addHiddenField("sub", $sub);
$output->addHiddenField("and_or", trim($and_or));

$subtitle = gettext("list");
if ($supplierid > 0)
{
    $addressdata = new Address_data();
    $subtitle = $addressdata->getAddressNameByID($supplierid);
}

$venster = new Layout_venster(Array(
	"title"    => gettext("Products"),
	"subtitle" => $subtitle
));


$venster->addMenuItem(gettext("new product"), "javascript: popup('index.php?mod=product&action=edit', 'productedit', 700, 600, 1);");
$venster->addMenuItem(gettext("offers"), "index.php?mod=product&action=showoffers");
$venster->addMenuItem(gettext("prices"), "index.php?mod=product&action=showprices");
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
$table->endTable();

$venster->addCode($table->generate_output());
unset($table);
$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addSpace(1);
	$table->endTableData();
$table->endTableRow();
$table->addTableRow();
	$table->addTableData();
	for ($i=0; $i!=26; $i++) {
			$table->addCode(" ");
                        if (preg_match("/^searchProd(.*)$/", $action)) 
                        {
                            $table->insertLink(chr(65+$i), array(
				"href" => "index.php?mod=product&action=$action&classname=".$_REQUEST["classname"]."&and_or=".trim($and_or)."&addresstype=".$addresstype."&l=".chr(65+$i)
                            ) );
                        }
                        else
                        {
                            $table->insertLink(chr(65+$i), array(
				"href" => "index.php?mod=product&action=lijst&and_or=".trim($and_or)."&addresstype=".$addresstype."&l=".chr(65+$i)
                            ) );
                        }
			$table->addSpace(1);
	}
	$table->endTableData();

$table->endTableRow();

$table->addTableRow();
       $table->addTableData(array("colspan"=>27));
               $table->addSpace(1);
               $table->insertLink(gettext("all"), array(
                       "href" => "index.php?mod=product&action=$action&sub=all&classname=".$_REQUEST["classname"]) );
               $table->addSpace(1);
               $table->insertLink(gettext("active"), array(
                       "href" => "index.php?mod=product&action=$action&sub=active&classname=".$_REQUEST["classname"]) );
               $table->addSpace(1);
               $table->insertLink(gettext("inactive"), array(
                       "href" => "index.php?mod=product&action=$action&sub=inactive&classname=".$_REQUEST["classname"]) );
       $table->endTableData();
$table->endTableRow();

$table->endTable();
$venster->addCode($table->generate_output());
unset($table);
$productinfo_arr = $productdata->getProductList($options);
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
				$table->addCode(gettext("products")." ".($productinfo_arr["top"]+1)." ".gettext("to")." ".$productinfo_arr["bottom"]." ".gettext("van de")." ".$productinfo_arr["total_count"]);
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
	"sort"     => $_REQUEST["sort"]
);

$view = new Layout_view();
$view->addData($data);
$view->addSettings($settings);
$view->defineSortForm("sort", "deze");
$view->addMapping("&nbsp;", "%%complex_actions");
$view->addMapping(gettext("id"), "%id");
$view->addMapping(gettext("wine name"), "%%complex_productname");
$view->addMapping(gettext("label"), "%label");
$view->addMapping(gettext("quality"), "%quality");
$view->addMapping(gettext("year"), "%prod_year");
$view->addMapping(gettext("content"), "%content");
$view->addMapping(gettext("box"), "%box");
$view->addMapping(gettext("price"), "%price");

$view->defineSort(gettext("id"), "id");
$view->defineSort(gettext("wine name"), "name");
$view->defineSort(gettext("label"), "label");
$view->defineSort(gettext("quality"), "quality");
$view->defineSort(gettext("year"), "prod_year");
$view->defineSort(gettext("content"), "content");
$view->defineSort(gettext("box"), "box");
$view->defineSort(gettext("price"), "price");
						 
/* first column in addresslist holds buttons to do actions on the record. These depend on permissions */
$view->defineComplexMapping("complex_actions", array(
	array(
		"type"    => "action",
		"src"     => "info",
		"alt"     => gettext("more information"),
		"link"    => array("javascript: popup('index.php?mod=product&action=show&productid=", "%id", "', 'bcardshow', 0, 0, 1);"),
		"check"   => $user->checkPermission("xs_addressmanage")
	),
	array(
		"type"    => "action",
		"src"     => "edit",
		"alt"     => gettext("edit"),
		"link"    => array("javascript: popup('index.php?mod=product&action=edit&productid=", "%id", "', 'productedit', 0, 0, 1);"),
		"check"   => $user->checkPermission("xs_addressmanage")
	)
), "nowrap");

$view->defineComplexMapping("complex_productname", array(
        array(
                "type"    => "link",
                "text"    => "%name",
                "link"    => array(     "javascript: selectProd(", " '", "%id", "',",
                                                                   " '", "%name_escaped", "',",
                                                                   " '", $_REQUEST["classname"], "')"
                                )
        )
));


/* if no records, show user a nice message about that */
if (!$productinfo_arr["total_count"]) {
	$venster->addCode(gettext("no products found"));
	$venster->addCode("<br><br><br>");
} else {
	$venster->addCode( $view->generate_output() );
}
unset($view);

$table = new Layout_table();
$table->addTableRow();
        $table->addTableData(array("style"=>"text-align: right", "colspan"=>1));
                $url = "index.php?mod=product&action=$action&classname=".$_REQUEST["classname"]."&supplierid=$supplierid&addresstype=$addresstype&top=%%&l=$l&sub=".$sub."&search=$search";
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
