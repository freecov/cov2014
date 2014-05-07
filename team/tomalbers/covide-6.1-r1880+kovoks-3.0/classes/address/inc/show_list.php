<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* make the db object easier to access */
$db = $GLOBALS["covide"]->db;
if ($_REQUEST["addresstype"] == "overig" && !$_REQUEST["sub"]) {
	$_REQUEST["sub"] = "kantoor";
}
/* make some URL params easier to access */
$action      = $_REQUEST["action"];
$top         = $_REQUEST["top"];
$addresstype = $_REQUEST["addresstype"];
$l           = $_REQUEST["l"];
$sub         = $_REQUEST["sub"];
$and_or      = $_REQUEST["and_or"];
$search      = $_REQUEST["search"];
$classname   = $_REQUEST["classname"];

if (!$addresstype) { $addresstype = "relations"; }
/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
/* init address_data object */
$addressdata = new Address_data();
/* put all company id's in array when we are looking at bcards */
if ($addresstype == "bcards") {
	$companies = $addressdata->getRelationsArray();
}
/* quick hack for backwards compatibility */
if ($addresstype == "relations" || $addresstype == "nonactive") { $bedrijven = 1; }

if ($top=="") { $top=0; }

/* build $options array to pass to getRelationsList method */
$options = Array(
	"addresstype"     => $addresstype,
	"top"             => $top,
	"action"          => $action,
	"l"               => $l,
	"sub"             => $sub,
	"and_or"          => $and_or,
	"search"          => $search,
	"classname"	  => $classname,
	"classifications" => $_REQUEST["classifications"],
	"selectiontype"   => $_REQUEST["selectiontype"],
	"sort"            => $_REQUEST["sort"]
);
/* insert into db */
$exportinfo = $addressdata->saveExportInfo($options);

$addressinfo_arr = $addressdata->getRelationsList($options);
/* do some housecleaning for sync4j and frame subtitle */
switch ($addresstype) {
	case "users" :
		$subtitle = gettext("medewerkers");
		$sync_identifier = "address_private";
		break;
	case "private" :
		$sync_identifier = "address_private";
		$subtitle = gettext("prive");
		break;
	case "bcards" :
		$sync_identifier = "address_businesscards";
		$subtitle = gettext("business cards");
		break;
	case "overig" :
		$sync_identifier = "address_other";
		$subtitle = gettext("overig");
		break;
	default :
		$sync_identifier = "address";
		$subtitle = gettext("relatie");
		break;
}
/* start output buffer routines */
$output = new Layout_output();

if ($_REQUEST["action"]=="searchRel") {
	$output->layout_page(gettext("Adresboek"), 1);
} else {
	$output->layout_page(gettext("Adresboek"));
}


$output->start_javascript();
$output->addCode("
	function selectRel(id, relname, classname) {
		if (opener && opener.selectRel) {
			opener.selectRel(id, relname, classname);
			setTimeout('window.close();',20);
		} else {
			document.location.href='index.php?mod=address&action=relcard&id='+id;
		}
	}
	function zet(naam,waarde) {
		eval('document.getElementById(\'deze\').'+naam+'.value=\''+waarde+'\'');
	}
	function stuur() {
		document.getElementById('deze').submit();
	}
	function selectUser(id) {
		document.location.href='index.php?mod=address&action=usercard&id='+id;
	}
	function selectOther(id, type) {
		document.location.href='index.php?mod=address&action=showother&id='+id+'&type='+type;
	}
");
$output->end_javascript();

$output->addTag("form", array(
	"id"     => "deze",
	"method" => "post",
	"action" => "index.php"
));
$output->addHiddenField("id", "");
#$output->addHiddenField("bedfijf", ""); //TODO: what is this?
$output->addHiddenField("mod", "address");
$output->addHiddenField("classname", $classname);
$output->addHiddenField("sort", $_REQUEST["sort"]);

if ($action=="searchRelFax") {
	$output->addHiddenField("faxid", $_REQUEST["faxid"]);
}
$output->addHiddenField("altemail", $_REQUEST["altemail"]);
if (preg_match("/^searchRel(.*)$/", $action)) {
	$output->addHiddenField("action", $action);
	$output->addHiddenField("extra", $action);
	$output->addHiddenField("bcards_sel", "1");
	$output->addHiddenField("verbergIface", $verbergIface);
} else {
	$output->addHiddenField("action", "Zoek");
	$output->addHiddenField("addresstype", $addresstype);
	$output->addHiddenField("sub", $sub);
	$output->addHiddenField("and_or", trim($and_or));
}

$venster = new Layout_venster(Array(
	"title"    => gettext("Adresboek"),
	"subtitle" => $subtitle
));
/* {{{ menu items */
if (!preg_match("/^searchRel/", $action)) {
	if ($GLOBALS["covide"]->license["multivers"] || $GLOBALS["covide"]->license["exact"]) {
		if ($GLOBALS["covide"]->license["hrm"]) {
			$venster->addMenuItem(gettext("nieuw persoon"), "javascript: popup('index.php?mod=address&action=edit&addresstype=private', 'addressedit', 700, 600, 1);");
			$venster->addMenuItem(gettext("nieuw overig"), "javascript: popup('index.php?mod=address&action=edit_overig', 'addressedit', 600, 600, 1);");
		}
	} else {
		// TODO: fix this please!
		if ($GLOBALS["covide"]->license["hrm"]) {
			$venster->addMenuItem(gettext("nieuwe relatie"), "javascript: popup('index.php?mod=address&action=edit&addresstype=relations', 'addressedit', 700, 600, 1);");
			$venster->addMenuItem(gettext("nieuw persoon"), "javascript: popup('index.php?mod=address&action=edit&addresstype=private', 'addressedit', 700, 550, 1);");
			$venster->addMenuItem(gettext("nieuw overig"), "javascript: popup('index.php?mod=address&action=edit_overig', 'addressedit', 700, 600, 1);");
		} else {
			$venster->addMenuItem(gettext("nieuwe relatie"), "javascript: popup('index.php?mod=address&action=edit&addresstype=relations', 'addressedit', 700, 600, 1);");
			$venster->addMenuItem(gettext("nieuw persoon"), "javascript: popup('index.php?mod=address&action=edit&addresstype=private', 'addressedit', 700, 550, 1);");
		}
	}
	$venster->addMenuItem(gettext("zoek classificaties"), "javascript: search_cla();");
	$venster->addMenuItem(gettext("zoek uitgebreid"), "index.php?mod=index&search[private]=0&search[address]=1");
	if ($user->checkPermission("xs_addressmanage") || $user->checkPermission("xs_usermanage")) {
		$venster->addMenuItem(gettext("export adressen"), "javascript: popup('index.php?mod=address&action=export', 'addressexport', 500, 400, 1);");
		$venster->addMenuItem(gettext("import adressen"), "javascript: popup('index.php?mod=address&action=import', 'addressimport', 700, 600, 1);");
		#$venster->addMenuItem(gettext("globale metavelden"), "index.php?mod=address&action=glob_metalist");
	}
	if ($GLOBALS["covide"]->license["has_sales"] && $user->checkPermission("xs_salesmanage")) {
		$venster->addMenuItem(gettext("sales module"), "index.php?mod=sales");
	}
	if ($GLOBALS["covide"]->license["has_hypo"] && $user->checkPermission("xs_hypo")) {
		$venster->addMenuItem(gettext("hypotheek module"), "index.php?mod=mortgage");
	}

	$venster->addMenuItem(gettext("brieven en templates"), "?mod=templates");
	$venster->addMenuItem(gettext("classificaties beheer"), "index.php?mod=classification&action=show_classifications");
	$venster->addMenuItem(gettext("niet actieve adressen"), "index.php?mod=address&addresstype=nonactive");

	if ($GLOBALS["covide"]->license["multivers"] || $GLOBALS["covide"]->license["exact"]) {
		$venster->addMenuItem(gettext("forceer import"), "javascript: popup('./fimport.php?e=1', 'forceimport', 0, 0, 1);");
	}

	$venster->generateMenuItems();
}
/* end menu items }}} */
$venster->addVensterData();

$table = new Layout_table();
$table->addTableRow();
	$table->addTableData();
		$table->addCode( $output->nbspace(3) );
		$table->addCode( gettext("zoeken").": ");
	$table->endTableData();
	$table->addTableData();
		$table->addTextField("search", stripslashes($_REQUEST["search"]), "", "", 1);
		$table->start_javascript();
			$table->addCode("
				document.getElementById('search').focus();
			");
		$table->end_javascript();
		$table->addSpace(2);
		//$table->insertImage("knop_rechts.gif", gettext("zoeken"), "javascript: stuur();");
		$table->insertAction("forward", gettext("zoeken"), "javascript: stuur();");
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
			if (preg_match("/^searchRel(.*)$/", $action)) {
				$table->insertLink(chr(65+$i), array(
					"href" => "index.php?mod=address&action=$action&classname=$classname&and_or=".trim($and_or)."&addresstype=".$addresstype."&l=".chr(65+$i)
				) );
				$table->addSpace(1);
			} else {
				$table->insertLink(chr(65+$i), array(
					"href" => "index.php?mod=address&action=lijst&and_or=".trim($and_or)."&addresstype=".$addresstype."&l=".chr(65+$i)
				) );
				$table->addSpace(1);
			}
	}
	$table->endTableData();

if (!preg_match("/^searchRel/", $action)) {
	$table->addTableData();
		$table->addCode(" ");
		$table->insertLink(gettext("relatie"), array(
			"href" => "index.php?mod=address&addresstype=relations"
		) );
		$table->addCode(" ");
		$table->insertLink(gettext("medewerkers"), array(
			"href" =>"index.php?mod=address&addresstype=users"
		) );
		$table->addCode(" ");
		$table->insertLink(gettext("prive"), array(
			"href" => "index.php?mod=address&addresstype=private"
		) );
		$table->addCode(" ");
		$table->insertLink(gettext("businesscards"), array(
			"href" => "index.php?mod=address&addresstype=bcards"
		) );
		$table->addCode(" ");
		if ($GLOBALS["covide"]->license["has_hrm"]) {
			$table->insertLink(gettext("overig"), array(
				"href" => "index.php?mod=address&addresstype=overig"
			) );
		}
	$table->endTableData();
}

$table->endTableRow();

if ($addresstype == "overig") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));

			$table->addSpace(1);
			$table->insertLink(gettext("bedrijfsadressen"), array(
				"href" => "index.php?mod=address&addresstype=overig&sub=kantoor"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("arbokantoren"), array(
				"href" => "index.php?mod=address&addresstype=overig&sub=arbo"
			) );
		$table->endTableData();
	$table->endTableRow();
}
if ($addresstype == "users") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));
			$table->addSpace(1);
			$table->insertLink(gettext("actief"), array(
				"href" => "index.php?mod=address&addresstype=users&sub=actief"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("niet actief"), array(
				"href" => "index.php?mod=address&addresstype=users&sub=nonactief"
			) );
		$table->endTableData();
	$table->endTableRow();
}
if ($addresstype == "relations") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));
			$table->addSpace(1);
			$table->insertLink(gettext("alle"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=alle"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("klanten"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=klanten"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("leveranciers"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=leveranciers"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("transporteurs"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=transporteurs"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("contacts"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=contacts"
			) );
			$table->addSpace(2);
			$table->addCode("(".gettext("Uitgebreid selecteren doet u via de classificaties").")");
		$table->endTableData();
	$table->endTableRow();
}

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
			$table->addSpace(1);
			if ($addressinfo_arr["total_count"]) {
				$table->addCode(gettext("adressen")." ".($addressinfo_arr["top"]+1)." ".gettext("t/m")." ".$addressinfo_arr["bottom"]." ".gettext("van de")." ".$addressinfo_arr["total_count"]);
			}
		$table->endTableData();
	$table->endTableRow();
$table->endTable();
$venster->addCode($table->generate_output());

$data = $addressinfo_arr["address"];
$settings = array(
	#"count"    => 500,
	"current"  => 30,
	"pagesize" => 10,
	"sort"     => $_REQUEST["sort"]
);

$view = new Layout_view();
$view->addData($data);
$view->addSettings($settings);
if (!preg_match("/^searchRel/", $action)) {
	if ($_REQUEST["addresstype"] == "bcards") {
		$view->addMapping("&nbsp;", "%%complex_actions_bcards", "", "", "", 1);
	} else {
		$view->addMapping("&nbsp;", "%%complex_actions", "", "", "", 1);
	}
}

$view->defineSortForm("sort", "deze");
switch ($_REQUEST["addresstype"]) {
	case "users"   :
		$view->addMapping(gettext("naam"), "%%complex_username");
		$view->addMapping(gettext("telnr"), "%%phone_nr");
		$view->addMapping(gettext("postcode"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("plaats"), "%city", "", "", "", 1);

		$view->defineSort(gettext("naam"), "surname");
		$view->defineSort(gettext("telnr"), "phone_nr");
		$view->defineSort(gettext("postcode"), "zipcode");
		$view->defineSort(gettext("plaats"), "city");
		break;
	case "private" :
		$view->addMapping(gettext("naam"), array(
			"%givenname",
			" ",
			"%infix",
			" ",
			"%surname"
		));
		$view->addMapping(gettext("telnr"), "%%phone_nr");
		$view->addMapping(gettext("mobielnr"), "%%mobile_nr");
		$view->addMapping(gettext("postcode"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("plaats"), "%city", "", "", "", 1);

		$view->defineSort(gettext("naam"), "surname");
		$view->defineSort(gettext("telnr"), "phone_nr");
		$view->defineSort(gettext("postcode"), "zipcode");
		$view->defineSort(gettext("plaats"), "city");
		break;
	case "bcards" :
		$view->addMapping(gettext("naam"), "%%complex_bcardsname");
		$view->addMapping(gettext("telnr"), "%%phone_nr");
		$view->addMapping(gettext("mobielnr"), "%%mobile_nr");

		$view->defineSort(gettext("naam"), "surname");
		$view->defineSort(gettext("telnr"), "phone_nr");
		$view->defineSort(gettext("plaats"), "mobile_nr");
		break;
	case "overig" :
		$view->addMapping(gettext("naam"), "%%complex_othername");
		$view->addMapping(gettext("telnr"), "%%phone_nr");
		$view->addMapping(gettext("postcode"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("plaats"), "%city", "", "", "", 1);

		$view->defineSort(gettext("naam"), "surname");
		$view->defineSort(gettext("telnr"), "phone_nr");
		$view->defineSort(gettext("postcode"), "zipcode");
		$view->defineSort(gettext("plaats"), "city");
		break;
	default :
		$view->addMapping(gettext("naam"), "%%complex_relationsname");
		$view->addMapping(gettext("contactpersoon"), "%tav");
		$view->addMapping(gettext("telnr"), "%%phone_nr");
		$view->addMapping(gettext("postcode"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("plaats"), "%city", "", "", "", 1);

		$view->defineSort(gettext("naam"), "companyname");
		$view->defineSort(gettext("contactpersoon"), "tav");
		$view->defineSort(gettext("postcode"), "zipcode");
		$view->defineSort(gettext("plaats"), "city");
		break;
	/* end switch statement */
}


$view->defineComplexMapping("complex_bcardsname", array(
	array(
		"text" => array(
			"%companyname",
			", ",
			"%givenname",
			" ",
			"%infix",
			" ",
			"%surname"
		)
	),
	array(
		"text"  => array(" (", "%alternative_name", ")"),
		"check" => "%alternative_name"
	)
));
$view->defineComplexMapping("phone_nr", array(
	array(
		"type" => "text",
		"text" => "%phone_nr_link"
	)
));
$view->defineComplexMapping("mobile_nr", array(
	array(
		"type" => "text",
		"text" => "%mobile_nr_link"
	)
));
/* first column in addresslist holds buttons to do actions on the record. These depend on permissions */
$view->defineComplexMapping("complex_actions", array(
	array(
		"type"    => "image",
		"src"     => "f_oud.gif",
		"alt"     => gettext("toggle sync"),
		"link"    => array("javascript: toggleSync('", "%id", "', '$sync_identifier', 'activate');"),
		"id"      => array("toggle_sync_", "%id"),
		"check"   => "%sync_no",
		"no_mobile" => 1
	),
	array(
		"type"    => "image",
		"src"     => "f_nieuw.gif",
		"alt"     => gettext("toggle sync"),
		"link"    => array("javascript: toggleSync('", "%id", "', '$sync_identifier', 'deactivate');"),
		"id"      => array("toggle_sync_", "%id"),
		"check"   => "%sync_yes",
		"no_mobile" => 1
	),
	array(
		"type"    => "action",
		"src"     => "info",
		"alt"     => gettext("meer informatie"),
		"link"    => array("index.php?mod=address&action=show_item&id=", "%id", "&addresstype=$addresstype"),
		"check"   => "%addressacc"
	),
	array(
		"type"    => "action",
		"src"     => "edit",
		"alt"     => gettext("bewerken"),
		"link"    => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 0, 0, 1);"),
		"check"   => "%addressmanage"
	),
	array(
		"type"    => "action",
		"src"     => "mail_new",
		"alt"     => gettext("email"),
		"link"    => array("javascript: emailSelectFrom('", "%email", "','", "%mail_id", "');"),
		"check"   => "%email"
	/* temp disabled.
	TODO: make this a user option
	),
	array(
		"type"    => "action",
		"src"     => "mail_external",
		"alt"     => gettext("mailen met extern pakket"),
		"link"    => array("mailto:", "%email"),
		"check"   => "%email"
	*/
	)
), "nowrap");
/* first column in addresslist holds buttons to do actions on the record. These depend on permissions */
$view->defineComplexMapping("complex_actions_bcards", array(
	array(
		"type"    => "image",
		"src"     => "f_oud.gif",
		"alt"     => gettext("toggle sync"),
		"link"    => array("javascript: toggleSync('", "%id", "', '$sync_identifier', 'activate');"),
		"id"      => array("toggle_sync_", "%id"),
		"check"   => "%sync_no",
		"no_mobile" => 1
	),
	array(
		"type"    => "image",
		"src"     => "f_nieuw.gif",
		"alt"     => gettext("toggle sync"),
		"link"    => array("javascript: toggleSync('", "%id", "', '$sync_identifier', 'deactivate');"),
		"id"      => array("toggle_sync_", "%id"),
		"check"   => "%sync_yes",
		"no_mobile" => 1
	),
	array(
		"type"    => "action",
		"src"     => "info",
		"alt"     => gettext("meer informatie"),
		"link"    => array("javascript: popup('index.php?mod=address&action=cardshow&cardid=", "%id", "', 'bcardshow', 0, 0, 1);"),
		"check"   => "%addressacc"
	),
	array(
		"type"    => "action",
		"src"     => "edit",
		"alt"     => gettext("bewerken"),
		"link"    => array("javascript: popup('index.php?mod=address&action=edit_bcard&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 0, 0, 1);"),
		"check"   => "%addressmanage"
	),
	array(
		"type"    => "action",
		"src"     => "mail_new",
		"alt"     => gettext("email"),
		"link"    => array("javascript: emailSelectFrom('", "%email", "','", "%mail_id", "');"),
		"check"   => "%email"
	/* temp disabled.
	TODO: make this a user option
	),
	array(
		"type"    => "action",
		"src"     => "mail_external",
		"alt"     => gettext("mailen met extern pakket"),
		"link"    => array("mailto:", "%email"),
		"check"   => "%email"
	*/
	)
), "nowrap");


/* the name of a user is a link to users overview */
$view->defineComplexMapping("complex_username", array(
	array(
		"type" => "link",
		"text" => array("%givenname", " ", "%infix", " ", "%surname", " - ", "%username"),
		"link" => array("javascript: selectUser(", "%id", ");")
	)
));
/* the name is a link to an overview */
$view->defineComplexMapping("complex_othername", array(
	array(
		"type"    => "link",
		"text"    => "%companyname",
		"link"    => array("javascript: selectOther(", "%id", ", '", $_REQUEST["sub"], "')"),
	)
));

/* the name is a link to an overview (if enough permissions) */
$view->defineComplexMapping("complex_relationsname", array(
	array(
		"type"    => "link",
		"text"    => "%companyname",
		"link"    => array(	"javascript: selectRel(", 
					"%id", 
					", '", 
					"%companyname_html", 
					"','",
					$classname,
					"')"
				),
		"check"   => "%addressacc"
	),
	array(
		"type"    => "text",
		"text"    => "%companyname",
		"check"   => "%noaccess"
	)
));
/* if no records, show user a nice message about that */
if (!$addressinfo_arr["total_count"]) {
	$venster->addCode(gettext("geen adressen met die parameters"));
} else {
	$venster->addCode( $view->generate_output() );
}
unset($view);
$table = new Layout_table();
$table->addTableRow();
	if ($_REQUEST["action"]=="searchRel") {
		$table->addTableData();
			/* delete selected relation if the action was selecting relation */
			$table->insertAction("delete", gettext("geen relatie"), "javascript: selectRel(-1, '".gettext("geen")."')");
		$table->endTableData();
	}
	$table->addTableData(array("style"=>"text-align: right", "colspan"=>1));
		if (is_array($_REQUEST["classifications"])) {
			$url = "javascript: cla_page('%%');";
		} else {
			$url = "index.php?mod=address&action=$action&addresstype=$addresstype&top=%%&l=$l&sub=".$sub."&search=$search";
		}
		$paging = new Layout_paging();
		$paging->setOptions($top, $addressinfo_arr["total_count"], $url);
		$table->addCode( $paging->generate_output() );
	$table->endTableData();
$table->endTableRow();
if ($user->checkPermission("xs_addressmanage") || $user->checkPermission("xs_usermanage")) {
	$table->addTableRow(1);
		$table->addTableData(array("style"=>"text-align: right", "colspan"=>2));
			$table->insertAction("toggle", gettext("beheer classificatie voor selectie"), "javascript: popup('index.php?mod=address&action=addcla_multi&info=".urlencode($exportinfo)."', 'addcla_multi', 0, 0, 1);");
			$table->insertAction("save", gettext("opslaan"), "index.php?mod=address&action=export&info=".urlencode($exportinfo));
			$table->insertAction("print", gettext("printen"), "javascript: popup('index.php?mod=address&action=print&info=".urlencode($exportinfo)."', 'print', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
}

if (is_array($_REQUEST["classifications"])) {
	$classification = new Classification_output();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace();
		$table->endTableData();
	$table->endTableRow();
		$table->addTableData(array("colspan"=>2), "header");
			$table->addCode(gettext("classificaties").":");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("soort").":", "", "bold top");
		if ($_REQUEST["addresstype"]=="bcards") {
			$table->insertTableData(gettext("business cards"));
		} else {
			$table->insertTableData(gettext("relaties"));
		}
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("selectie").":", "", "bold top");
		if ($_REQUEST["selectiontype"]=="and") {
			$table->insertTableData(gettext("unieke classificaties (en)"));
		} else {
			$table->insertTableData(gettext("opgetelde classificaties (of)"));
		}
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("positief").":", "", "bold top");
		$table->addTableData();
			$table->addCode( $classification->classification_selection("null", $_REQUEST["classifications"]["positive"], "enabled", 1) );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("negatief").":", "", "bold top");
		$table->addTableData();
			$table->addCode( $classification->classification_selection("null", $_REQUEST["classifications"]["negative"], "disabled", 1) );
		$table->endTableData();
	$table->endTableRow();
}
$table->endTable();
$venster->addCode($table->generate_output());
unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
$output->endTag("form");

if (is_array($_REQUEST["classifications"])) {
	/* add a custom form */
	$output->addTag("form", array(
		"action" => "index.php",
		"method" => "post",
		"id"     => "classifications_frm"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "", "cla_action");
	$output->addHiddenField("top", "");
	$output->addHiddenField("classifications[positive]", $_REQUEST["classifications"]["positive"]);
	$output->addHiddenField("classifications[negative]", $_REQUEST["classifications"]["negative"]);
	$output->addHiddenField("addresstype", $_REQUEST["addresstype"]);
	$output->addHiddenField("selectiontype", $_REQUEST["selectiontype"]);
	$output->endTag("form");
	$output->start_javascript();
	$output->addCode("
		function search_cla() {
			document.getElementById('classifications_frm').cla_action.value = 'zoekcla';
			document.getElementById('classifications_frm').submit();
		}
		function cla_page(top) {
			document.getElementById('classifications_frm').top.value = top;
			document.getElementById('classifications_frm').submit();
		}
	");
	$output->end_javascript();
} else {
	$output->start_javascript();
	$output->addCode("
		function search_cla() {
			document.location.href='?mod=address&action=zoekcla';
		}
	");
	$output->end_javascript();
}

unset($venster);

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );

$output->load_javascript(self::include_dir."sync4j.js");


$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );


$output->layout_page_end();
$output->exit_buffer();
?>
