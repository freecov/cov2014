<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups of people
 * that want the most efficient way to work together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (!class_exists("Address_output")) {
	die("no class definition found");
}

/* make the db object easier to access */
$db = $GLOBALS["covide"]->db;
if ($_REQUEST["addresstype"] == "overig" && !$_REQUEST["sub"]) {
	$_REQUEST["sub"] = "kantoor";
}
/* put all countries in an array */
$address_data = new Address_data();
$countryArray = $address_data->listCountries(1);

/* get the users address mode */
$user = new User_data();
$userinfo = $user->getUserDetailsById($_SESSION["user_id"]);
if (!$_REQUEST["addresstype"]) {
	if ($userinfo["addressmode"] == 0)
		$_REQUEST["addresstype"] = "relations";
	else
		$_REQUEST["addresstype"] = "bcards";
}
if (preg_match("/^searchRel/", $_REQUEST["action"]))
	$_REQUEST["addresstype"] = "relations";
if (preg_match("/^searchRelPrivate/", $_REQUEST["action"]))
	$_REQUEST["addresstype"] = "private";

/* make some URL params easier to access */
$action      = $_REQUEST["action"];
$top         = $_REQUEST["top"];
$addresstype = $_REQUEST["addresstype"];
if(!$_REQUEST["active_only"] && $_REQUEST["addresstype"] != 'nonactive')
	$_REQUEST["active_only"] = true;
$active_only = $_REQUEST["active_only"];
$l           = $_REQUEST["l"];
$sub         = $_REQUEST["sub"];
$and_or      = $_REQUEST["and_or"];
$search      = $_REQUEST["search"];
$specified   = $_REQUEST["specified"];
$landSelect  = $_REQUEST["landSelect"];
$cmsforms    = $_REQUEST["cmsforms"];

/* possibilities for specific searches */
if ($addresstype == "users" || $addresstype == "private") {
	$specified_arr = array(
		0 => "",
		7 => gettext("name"),
		3 => gettext("zipcode"),
		4 => gettext("city")
	);
} else {
	$specified_arr = array(
		0 => "",
		1 => gettext("name"),
		2 => gettext("relations"),
		3 => gettext("zipcode"),
		4 => gettext("city"),
		5 => gettext("country"),
		6 => gettext("global meta")
	);
}

/* get the permissions for the user */
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
	"bcard_export"    => $active_only,
	"top"             => $top,
	"action"          => $action,
	"l"               => $l,
	"sub"             => $sub,
	"and_or"          => $and_or,
	"search"          => $search,
	"specified"       => $specified,
	"landSelect"      => $landSelect,
	"classifications" => $_REQUEST["classifications"],
	"selectiontype"   => $_REQUEST["selectiontype"],
	"sort"            => $_REQUEST["sort"],
	"funambol_user"   => $_REQUEST["funambol_user"],
	"cmsforms"        => $cmsforms
);
/* insert into db */
$exportinfo = $addressdata->saveExportInfo($options);

$addressinfo_arr = $addressdata->getRelationsList($options);
/* do some housecleaning for sync and frame subtitle */
switch ($addresstype) {
	case "users" :
		$subtitle = gettext("employees");
		$sync_identifier = "address_private";
		break;
	case "private" :
		$sync_identifier = "address_private";
		$subtitle = gettext("private");
		break;
	case "bcards" :
		$sync_identifier = "address_businesscards";
		$subtitle = gettext("business cards");
		break;
	case "overig" :
		$sync_identifier = "address_other";
		$subtitle = gettext("remaining");
		break;
	default :
		$sync_identifier = "address";
		$subtitle = gettext("relations");
		break;
}
/* start output buffer routines */
$output = new Layout_output();

if (preg_match("/^searchRel/s", $_REQUEST["action"]) || $_REQUEST["campaign"]) {
	$output->layout_page(gettext("Addressbook"), 1);
} else {
	$output->layout_page(gettext("Addressbook"));
}


$output->start_javascript();
$output->addCode("
	function selectRel(id, relname) {
		if (opener && opener.selectRel) {
			opener.selectRel(id, relname);
			setTimeout('window.close();',20);
		} else {
			document.location.href='index.php?mod=address&action=relcard&funambol_user=".$_REQUEST["funambol_user"]."&id='+id;
		}
	}
	function selectPrivate(id, relname) {
		if (opener && opener.selectPrivate) {
			opener.selectPrivate(id, relname);
			setTimeout('window.close();',20);
		} else {
			document.location.href='index.php?mod=address&action=showPrivate&funambol_user=".$_REQUEST["funambol_user"]."&private_id='+id;
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
	"method" => "get",
	"action" => "index.php"
));
$output->addHiddenField("id", "");
$output->addHiddenField("mod", "address");
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
	"title"    => gettext("Addressbook"),
	"subtitle" => $subtitle
));
/* {{{ menu items */
if ($_REQUEST["campaign"]) {
	$venster->addMenuItem(gettext("search classifications"), "javascript: search_cla();");
	$venster->addMenuItem(gettext("select for campaign"), sprintf("?mod=campaign&action=new2&exportid=%s&type=%d", urlencode($exportinfo), $_REQUEST["campaign"]));
	$venster->generateMenuItems();

} elseif (!preg_match("/^searchRel/", $action)) {
	if ($GLOBALS["covide"]->license["multivers"] || $GLOBALS["covide"]->license["exact"]) {
		if ($GLOBALS["covide"]->license["hrm"]) {
			$venster->addMenuItem(gettext("new person"), "javascript: popup('index.php?mod=address&action=edit&addresstype=private', 'addressedit', 700, 600, 1);");
			$venster->addMenuItem(gettext("new other"), "javascript: popup('index.php?mod=address&action=edit_overig', 'addressedit', 600, 600, 1);");
		}
	} else {
		if ($user->checkPermission("xs_addressmanage") || $user->checkPermission("xs_relationmanage"))
			$venster->addMenuItem(gettext("new contact"), "javascript: popup('index.php?mod=address&action=edit&addresstype=relations', 'addressedit', 700, 600, 1);");

		$venster->addMenuItem(gettext("new person"), "javascript: popup('index.php?mod=address&action=edit&addresstype=private', 'addressedit', 700, 550, 1);");
		if ($GLOBALS["covide"]->license["hrm"])
			$venster->addMenuItem(gettext("new other"), "javascript: popup('index.php?mod=address&action=edit_overig', 'addressedit', 700, 600, 1);");
	}
	$venster->addMenuItem(gettext("search classifications"), "javascript: search_cla();");
	$venster->addMenuItem(gettext("extensive search"), "index.php?mod=index&search[private]=0&search[address]=1");
	if ($user->checkPermission("xs_addressmanage")) {
		$venster->addMenuItem(gettext("export addresses"), "javascript: popup('index.php?mod=address&action=export', 'addressexport', 500, 400, 1);");
		$venster->addMenuItem(gettext("import addresses"), "javascript: popup('index.php?mod=address&action=import', 'addressimport', 700, 600, 1);");
		$venster->addMenuItem(gettext("import vCard"), "javascript: popup('index.php?mod=address&action=importVcard', 'vcardimport', 700, 600, 1);");
		$venster->addMenuItem(gettext("letter templates"), "?mod=templates");
	if ($user->checkPermission("xs_classmanage")) {
		$venster->addMenuItem(gettext("classification mngt"), "index.php?mod=classification&action=show_classifications");
	}
		$venster->addMenuItem(gettext("header mngt"), "index.php?mod=address&action=showheaders");
		$venster->addMenuItem(gettext("select and sort"), "javascript: popup('index.php?mod=address&action=selectRows&type=".$_REQUEST["addresstype"]."', 'sortandselect', 700, 600, 1);");
		$venster->addMenuItem(gettext("inactive contacts"), "index.php?mod=address&addresstype=nonactive");
		$venster->addMenuItem(gettext("global metafields"), "index.php?mod=address&action=glob_metalist");
	}
	if ($GLOBALS["covide"]->license["has_hypo"] && $user->checkPermission("xs_hypo") && !$GLOBALS["covide"]->license["disable_basics"])
		$venster->addMenuItem(gettext("morgage module"), "index.php?mod=mortgage");

	if ($GLOBALS["covide"]->license["multivers"] || $GLOBALS["covide"]->license["exact"] && !$GLOBALS["covide"]->license["disable_basics"])
		$venster->addMenuItem(gettext("force import"), "javascript: popup('./fimport.php?e=1', 'forceimport', 0, 0, 1);");

	if ($GLOBALS["covide"]->license["has_voip"])
		$venster->addMenuItem(gettext("direct voip call"), "javascript: newVoipCall();");

	$venster->generateMenuItems();
}
/* end menu items }}} */
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
		$table->addSelectField("specified", $specified_arr, array($_REQUEST["specified"]));
		$table->addSpace(2);
		$table->addSelectField("landSelect", $countryArray, $_REQUEST["landSelect"]);
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
	if (is_array($_REQUEST["classifications"]))
		$href = "javascript: cla_letter('%s')";
	elseif (preg_match("/^searchRel(.*)$/", $action))
		$href = "index.php?mod=address&action=$action&and_or=".trim($and_or)."&funambol_user=".$_REQUEST["funambol_user"]."&addresstype=".$addresstype."&l=%s";
	else
		$href = "index.php?mod=address&action=lijst&and_or=".trim($and_or)."&funambol_user=".$_REQUEST["funambol_user"]."&addresstype=".$addresstype."&l=%s";
	for ($i=0; $i!=26; $i++) {
		$table->addSpace(1);
		if ($_REQUEST["l"] == chr(65+$i))
			$table->addTag("b");
		$table->insertLink(chr(65+$i), array(
			"href" => sprintf($href, chr(65+$i))
		));
		if ($_REQUEST["l"] == chr(65+$i))
			$table->endTag("b");
		$table->addSpace(1);
	}
	$table->endTableData();

if (!preg_match("/^searchRel/", $action)) {
	$table->addTableData();
		$table->addCode(" ");
		if ($_REQUEST["addresstype"] == "relations" || !$_REQUEST["addresstype"]) $table->addTag("b");
		$table->insertLink(gettext("contact"), array(
			"href" => "index.php?mod=address&addresstype=relations&funambol_user=".$_REQUEST["funambol_user"]
		) );
		if ($_REQUEST["addresstype"] == "relations" || !$_REQUEST["addresstype"]) $table->endTag("b");
		$table->addCode(" ");
		if ($_REQUEST["addresstype"] == "users") $table->addTag("b");
		$table->insertLink(gettext("employees"), array(
			"href" =>"index.php?mod=address&addresstype=users&funambol_user=".$_REQUEST["funambol_user"]
		) );
		if ($_REQUEST["addresstype"] == "users") $table->endTag("b");
		$table->addCode(" ");
		//if (!$_REQUEST["funambol_user"] || $_REQUEST["funambol_user"] == $_SESSION["user_id"]) {
		if ($_REQUEST["addresstype"] == "private") $table->addTag("b");
			$table->insertLink(gettext("private"), array(
				"href" => "index.php?mod=address&addresstype=private&funambol_user=".$_REQUEST["funambol_user"]
			) );
		if ($_REQUEST["addresstype"] == "private") $table->endTag("b");
		//} else {
		//	$table->addCode(gettext("private"));
		//}
		$table->addCode(" ");
		if ($_REQUEST["addresstype"] == "bcards") $table->addTag("b");
		$table->insertLink(gettext("business cards"), array(
			"href" => "index.php?mod=address&addresstype=bcards&funambol_user=".$_REQUEST["funambol_user"]
		) );
		if ($_REQUEST["addresstype"] == "bcards") $table->addTag("b");

		$table->addCode(" ");
		if ($GLOBALS["covide"]->license["has_hrm"]) {
			//TODO: fixme!
			/*
			$table->insertLink(gettext("remaining"), array(
				"href" => "index.php?mod=address&addresstype=overig"
			) );
			*/
		}
	$table->endTableData();
}

$table->endTableRow();

if ($addresstype == "overig") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));

			$table->addSpace(1);
			$table->insertLink(gettext("company addresses"), array(
				"href" => "index.php?mod=address&addresstype=overig&sub=kantoor"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("Occupational Health & Safety Services offices"), array(
				"href" => "index.php?mod=address&addresstype=overig&sub=arbo"
			) );
		$table->endTableData();
	$table->endTableRow();
}
if ($addresstype == "users") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));
			$table->addSpace(1);
			$table->insertLink(gettext("active"), array(
				"href" => "index.php?mod=address&addresstype=users&sub=actief"
			) );
			$table->addSpace(2);
			$table->insertLink(gettext("inactive"), array(
				"href" => "index.php?mod=address&addresstype=users&sub=nonactief"
			) );
		$table->endTableData();
	$table->endTableRow();
}
if ($addresstype == "relations") {
	$table->addTableRow();
		$table->addTableData(array("colspan"=>27));
			$table->addSpace(1);
			$table->insertLink(gettext("all"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=alle&funambol_user=".$_REQUEST["funambol_user"]."&action=".$_REQUEST["action"]
			) );
			$table->addSpace(2);
			if ($_REQUEST["sub"] == "klanten") $table->addTag("b");
			$table->insertLink(gettext("customers"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=klanten&funambol_user=".$_REQUEST["funambol_user"]."&action=".$_REQUEST["action"]
			) );
			if ($_REQUEST["sub"] == "klanten") $table->endTag("b");

			$table->addSpace(2);

			if ($_REQUEST["sub"] == "leveranciers") $table->addTag("b");
			$table->insertLink(gettext("suppliers"), array(
				"href" => "index.php?mod=address&addresstype=relations&sub=leveranciers&funambol_user=".$_REQUEST["funambol_user"]."&action=".$_REQUEST["action"]
			) );
			if ($_REQUEST["sub"] == "leveranciers") $table->endTag("b");

			$table->addSpace(2);

			if ($_REQUEST["cmsforms"] == "1") $table->addTag("b");
			$table->insertLink(gettext("cmsforms"), array(
				"href" => "index.php?mod=address&addresstype=relations&cmsforms=1&funambol_user=".$_REQUEST["funambol_user"]
			) );
			if ($_REQUEST["cmsforms"] == "1") $table->endTag("b");

			$table->addSpace(2);
			$table->addCode("(".gettext("Extented selections can be made based upon classifications").")");
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
			$user_data = new User_data();
			$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

			if ($GLOBALS["covide"]->license["has_funambol"] && !$GLOBALS["covide"]->license["disable_basics"]) {
				if ($GLOBALS["covide"]->license["has_funambol"]) {

					$table->addCode(gettext("select sync user").": ");

					$users = explode(",", $user_info["addresssyncmanage"]);
					$sel = array(
						gettext("my address list") => array(
							$_SESSION["user_id"] => $user_data->getUserNameById($_SESSION["user_id"])
						)
					);
					//if ($_REQUEST["addresstype"] == "private")
					//	$users = array();

					/* create funambol object */
					$funambol_data = new Funambol_data();
					foreach ($users as $k=>$v) {
						if ($funambol_data->checkUserSyncState($v) === true && $v != $_SESSION["user_id"])
							$sel[gettext("device of user")][$v] = $user_data->getUserNameById($v);
					}
					$table->addSelectField("funambol_user", $sel, $_REQUEST["funambol_user"]);
					$table->addSpace(2);

					if (!$GLOBALS["covide"]->license["disable_basics"] &&
						($user_info["xs_funambol"]
							|| ($_REQUEST["funambol_user"] != $_SESSION["user_id"])
								&& $_REQUEST["funambol_user"])) {


						$table->addCode(gettext("toggle selection").": ");
						$table->insertImage("f_nieuw.gif", gettext("toggle sync ON for selection"), array(
							"href" => sprintf("javascript: if (confirm(gettext('Are you sure you want to enable the selection?'))) { toggleSync('sel_on_%d', '%s', 'activate'); }", $exportinfo, $sync_identifier)
						));
						$table->addSpace(1);
						$table->insertImage("f_oud.gif", gettext("toggle sync OFF for selection"), array(
							"href" => sprintf("javascript: if (confirm(gettext('Are you sure you want to disable the selection?'))) { toggleSync('sel_off_%d', '%s', 'deactivate'); }", $exportinfo, $sync_identifier)
						));
						$table->addSpace(3);
					}

					$table->start_javascript();
						$table->addCode("
							document.getElementById('funambol_user').onchange = function() {
								document.getElementById('deze').submit();
							}
						");
					$table->end_javascript();
					$table->addSpace(3);
				}
			}
			$table->addSpace(1);
			if ($addressinfo_arr["total_count"]) {
				$table->addCode(gettext("addresses")." ".($addressinfo_arr["top"]+1)." ".gettext("to")." ".$addressinfo_arr["bottom"]." ".gettext("of")." ".$addressinfo_arr["total_count"]);
			}
		$table->endTableData();
	$table->endTableRow();
$table->endTable();
$venster->addCode($table->generate_output());

/* Do some stuff for the multilink/multirel relations */
foreach($addressinfo_arr["address"] as $index) {
	if($index["multirel"]) {
		$addressinfo_arr["address"][$index["id"]]["all_ids"] = trim($index["address_id"] ."," .$index["multirel"]);
	} else {
		$addressinfo_arr["address"][$index["id"]]["one_address"] = true;
	}
}
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
		$view->addMapping("&nbsp;", array("%%complex_sync", "%%complex_actions_bcards"), "", "", "", 1);
	} elseif ($_REQUEST["addresstype"] == "private" || $_REQUEST["addresstype"] == "users") {
		$view->addMapping("&nbsp;", array("%%complex_sync", "%%complex_actions_private"), "", "", "", 1);
	} else {
		$view->addMapping("&nbsp;", array("%%complex_sync", "%%complex_actions"), "", "", "", 1);
	}
} else {
	$view->addMapping("&nbsp;", array("%%complex_sync", "%%complex_actions_address"), "", "", "", 1);
}

$view->defineSortForm("sort", "deze");
$view->setHtmlField("phone_nr_link");
$view->setHtmlField("personal_phone_nr_link");
$view->setHtmlField("mobile_nr_link");

switch ($_REQUEST["addresstype"]) {
	case "users"   :
		$view->addMapping(gettext("name"), "%%complex_username");
		$view->addMapping(gettext("telephone nr"), "%%phone_nr");
		$view->addMapping(gettext("zip code"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("city"), "%city", "", "", "", 1);

		$view->defineSort(gettext("name"), "surname");
		$view->defineSort(gettext("telephone nr"), "phone_nr");
		$view->defineSort(gettext("zip code"), "zipcode");
		$view->defineSort(gettext("city"), "city");
		break;
	case "private" :
		$view->addMapping(gettext("name"), "%%complex_private_name");
		$view->addMapping(gettext("telephone nr"), "%%phone_nr");
		$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr");
		$view->addMapping(gettext("zip code"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("city"), "%city", "", "", "", 1);

		//if ($GLOBALS["covide"]->license["has_funambol"]) {
		$view->addMapping(gettext("convert"), "%%complex_sync_actions");
		$view->defineComplexMapping("complex_private_name", array(
			array (
				"type" => "link",
				"text" => array("%givenname", " ", "%infix", " ", "%surname") ,
				"link" => array("javascript: selectPrivate(", "%id", ", '", "%tav", "')")
			)
		));
		$view->defineComplexMapping("complex_sync_actions", array(
			array(
				"type"    => "action",
				"src"     => "state_public",
				"alt"     => gettext("this address is public"),
				"link"    => "javascript: void();",
				"no_mobile" => 1,
				"check"   => "%is_public"
			),
			array(
				"type"    => "action",
				"src"     => "state_private",
				"alt"     => gettext("this address is private"),
				"link"    => "javascript: void();",
				"no_mobile" => 1,
				"check"   => "%is_private"
			),
			array(
				"type"    => "action",
				"src"     => "forward",
				"alt"     => gettext("move to public address list"),
				"link"    => array("javascript: popup('?mod=address&action=move2public&id=", "%id", "', 'move', 800, 600, 1);"),
				"no_mobile" => 1
			),
			array(
				"text"    => array(" ", "%sync_h")
			)
		));
		//}

		$view->defineSort(gettext("name"), "surname");
		$view->defineSort(gettext("telephone nr"), "phone_nr");
		$view->defineSort(gettext("zip code"), "zipcode");
		$view->defineSort(gettext("city"), "city");
		break;
	case "bcards" :
		$field_sort = unserialize($user_info["default_address_fields_bcard"]);
		if (is_array($field_sort)) {
			foreach ($field_sort as $field=>$sort) {
				switch ($field) {
					case "name": $view->addMapping(gettext("name")." / ".gettext("free field"), "%%complex_bcardsname"); break;
					case "last name": $view->addMapping(gettext("last name"), "%surname"); break;
					case "given name": $view->addMapping(gettext("given name"), "%givenname"); break;
					case "mobile phone nr": $view->addMapping(gettext("mobile phone nr"), "%%mobile_nr"); break;
					case "relation": $view->addMapping(gettext("relation"), "%%complex_companyname"); break;
					case "classifications": $view->addMapping(gettext("classifications"), "%classification_html", "", "", "", 1); break;
					case "address": $view->addMapping(gettext("address"), "%business_address"); break;
					case "telephone nr": $view->addMapping(gettext("telephone nr"), "%%phone_nr"); break;
					case "zipcode": $view->addMapping(gettext("zip code"), "%business_zipcode", "", "", "", 1); break;
					case "city": $view->addMapping(gettext("city"), "%business_city", "", "", "", 1); break;
					case "country": $view->addMapping(gettext("country"), "%business_country", "", "", "", 1); break;
					case "email": $view->addMapping(gettext("email"), "%%complex_email", "", "", "", 1); break;
					case "jobtitle": $view->addMapping(gettext("jobtitle"), "%jobtitle", "", "", "", 1); break;
					case "warning": $view->addMapping(gettext("warning"), "%warning", "", "", "", 1); break;
					case "personal address": $view->addMapping(gettext("personal address"), "%personal_address"); break;
					case "personal telephone nr": $view->addMapping(gettext("personal telephone nr"), "%%personal_phone_nr"); break;
					case "personal zipcode": $view->addMapping(gettext("personal zipcode"), "%personal_zipcode", "", "", "", 1); break;
					case "personal city": $view->addMapping(gettext("personal city"), "%personal_city", "", "", "", 1); break;
					case "personal country": $view->addMapping(gettext("personal country"), "%personal_country", "", "", "", 1); break;
					case "personal email": $view->addMapping(gettext("personal email"), "%%complex_personal_email"); break;
					case "memo": $view->addMapping(gettext("memo"), "%memo", "", "", "", 1); break;
					case "website": $view->addMapping(gettext("website"), "%website", "", "", "", 1); break;
				}
			}
		} else {
			$view->addMapping(gettext("name")." / ".gettext("free field"), "%%complex_bcardsname");
			$view->addMapping(gettext("telephone nr"), "%%phone_nr");
			$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr");
			$view->addMapping(gettext("relation"), "%%complex_companyname");
			$view->addMapping(gettext("warning"), "%warning");
		}
		$view->defineSort(gettext("last name"), "surname");
		$view->defineSort(gettext("given name"), "givenname");
		$view->defineSort(gettext("jobtitle"), "jobtitle");
		$view->defineSort(gettext("warning"), "warning");
		$view->defineSort(gettext("memo"), "memo");
		$view->defineSort(gettext("address"), "business_address");
		$view->defineSort(gettext("zipcode"), "business_zipcode");
		$view->defineSort(gettext("city"), "business_city");
		$view->defineSort(gettext("country"), "business_country");
		$view->defineSort(gettext("email"), "email");
		$view->defineSort(gettext("website"), "website");
		$view->defineSort(gettext("telephone nr"), "phone_nr");
		$view->defineSort(gettext("mobile phone nr"), "mobile_nr");
		$view->defineSort(gettext("personal address"), "personal_address");
		$view->defineSort(gettext("personal zipcode"), "personal_zipcode");
		$view->defineSort(gettext("personal city"), "personal_city");
		$view->defineSort(gettext("personal country"), "personal_country");
		$view->defineSort(gettext("personal email"), "personal_email");
		$view->defineSort(gettext("personal telephone nr"), "personal_phone_nr");
		break;
	case "overig" :
		$view->addMapping(gettext("name"), "%%complex_othername");
		$view->addMapping(gettext("telephone nr"), "%%phone_nr");
		$view->addMapping(gettext("zip code"), "%zipcode", "", "", "", 1);
		$view->addMapping(gettext("city"), "%city", "", "", "", 1);

		$view->defineSort(gettext("name"), "surname");
		$view->defineSort(gettext("telephone nr"), "phone_nr");
		$view->defineSort(gettext("zip code"), "zipcode");
		$view->defineSort(gettext("city"), "city");
		break;
	default :
		$field_sort = unserialize($user_info["default_address_fields"]);
		if (is_array($field_sort)) {
			foreach ($field_sort as $field=>$sort) {
				switch ($field) {
					case "name": $view->addMapping(gettext("relation"), "%%complex_relationsname"); break;
					case "last name": $view->addMapping(gettext("last name"), "%contact_surname"); break;
					case "given name": $view->addMapping(gettext("given name"), "%contact_givenname"); break;
					case "birthday": $view->addMapping(gettext("birthday"), "%h_birthday"); break;
					case "contact": $view->addMapping(gettext("contact"), "%tav"); break;
					case "address": $view->addMapping(gettext("address"), "%address"); break;
					case "telephone nr": $view->addMapping(gettext("telephone nr"), "%%phone_nr"); break;
					case "zip code": $view->addMapping(gettext("zip code"), "%zipcode", "", "", "", 1); break;
					case "city": $view->addMapping(gettext("city"), "%city", "", "", "", 1); break;
					case "country": $view->addMapping(gettext("country"), "%country", "", "", "", 1); break;
					case "email": $view->addMapping(gettext("email"), "%%complex_email", "", "", "", 1); break;
					case "jobtitle": $view->addMapping(gettext("jobtitle"), "%jobtitle", "", "", "", 1); break;
					case "warning": $view->addMapping(gettext("warning"), "%warning", "", "", "", 1); break;
				}
			}
		} else {
			$view->addMapping(gettext("relation"), "%%complex_relationsname"); 
			$view->addMapping(gettext("address"), "%address");
			$view->addMapping(gettext("zip code"), "%zipcode", "", "", "", 1);
			$view->addMapping(gettext("city"), "%city", "", "", "", 1);
			$view->addMapping(gettext("telephone nr"), "%%phone_nr");
		}

		$view->defineSort(gettext("name"), "companyname");
		$view->defineSort(gettext("last name"), "contact_surname");
		$view->defineSort(gettext("given name"), "contact_givenname");
		$view->defineSort(gettext("birthday"), "contact_birthday");
		$view->defineSort(gettext("contact"), "tav");
		$view->defineSort(gettext("address"), "address");
		$view->defineSort(gettext("telephone nr"), "phone_nr");
		$view->defineSort(gettext("zip code"), "zipcode");
		$view->defineSort(gettext("city"), "city");
		$view->defineSort(gettext("country"), "country");
		$view->defineSort(gettext("email"), "email");
		$view->defineSort(gettext("jobtitle"), "jobtitle");
		$view->defineSort(gettext("warning"), "warning");

		break;
	/* end switch statement */
}

$view->defineComplexMapping("complex_companyname", array(
	array(
		"type" => "multilink",
		"text" => "%companyname" ,
		"link" => array("index.php?mod=address&action=relcard&funambol_user=&id=", "%all_ids"),
		"check" => "%all_ids"
		),
	array(
		"type" => "link",
		"text" => array("%companyname") ,
		"link" => array("index.php?mod=address&action=relcard&funambol_user=&id=", "%address_id"),
		"check" => "%one_address"
		)
));


$view->defineComplexMapping("complex_bcardsname", array(
	array(
		"text" => array(
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
$view->defineComplexMapping("personal_phone_nr", array(
	array(
		"type" => "text",
		"text" => "%personal_phone_nr_link"
	)
));
$view->defineComplexMapping("complex_personal_email", array(
	array(
		"type" => "link",
		"text" => array("%personal_email") ,
		"link" => array("javascript: emailSelectFrom('", "%personal_email", "');")
	)
));
$view->defineComplexMapping("phone_nr", array(
	array(
		"type" => "text",
		"text" => "%phone_nr_link"
	)
));
$view->defineComplexMapping("complex_email", array(
	array(
		"type" => "link",
		"text" => array("%email") ,
		"link" => array("javascript: emailSelectFrom('", "%email", "','", "%mail_id", "');")
	)
));
$view->defineComplexMapping("mobile_nr", array(
	array(
		"type" => "text",
		"text" => "%mobile_nr_link"
	)
));
	/* first column in addresslist holds buttons to do actions on the record. These depend on permissions */
	if ($GLOBALS["covide"]->license["has_funambol"]
		&& !$GLOBALS["covide"]->license["disable_basics"]
		&& ($user_info["xs_funambol"]
			|| ($_REQUEST["funambol_user"] && $_REQUEST["funambol_user"] != $_SESSION["user_id"]
		))) {

		$view->defineComplexMapping("complex_sync", array(
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
			)
		));
	} else {
		$view->defineComplexMapping("complex_sync", array());
	}

	/* when searchRel is activated we can not be sure if relation is there, so we need a special selectRel icon */
	$view->defineComplexMapping("complex_actions_address", array(
		array(
			"type"    => "action",
			"src"     => "addressbook",
			"alt"     => gettext("select relation"),
			"link"    => array("javascript: selectRel(", "%id", ", '", "%companyname_html", "')"),
		)
	));
	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"    => "action",
			"src"     => "addressbook",
			"alt"     => gettext("relation overview"),
			"link"    => array("javascript: selectRel(", "%id", ", '", "%companyname_html", "')"),
		),
		array(
			"type"    => "action",
			"src"     => "info",
			"alt"     => gettext("more information"),
			"link"    => array("javascript: popup('index.php?mod=address&action=show_item&id=", "%id", "&addresstype=$addresstype', 'showaddress', 700, 600, 1);"),
		),
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 700, 600, 1);"),
			"check"   => "%addressmanage"
		),
		array(
			"type"    => "action",
			"src"     => "view_all",
			"alt"     => gettext("add classification"),
			"link"    => array("javascript: popup('index.php?mod=address&action=addcla_inline&type=relation&id=", "%id", "', 'addcla_multi', 350, 150, 1);"),
			"check"   => "%addressmanage"
		),
		array(
			"type"    => "action",
			"src"     => "vcard",
			"alt"     => gettext("export as vCard"),
			"link"    => array("javascript: popup('index.php?mod=address&action=exportVcard&id=", "%id", "&type=".$_REQUEST["addresstype"]."');"),
		),
		array(
			"type"    => "action",
			"src"     => "mail_new",
			"alt"     => gettext("email"),
			"link"    => array("javascript: emailSelectFrom('", "%email", "','", "%mail_id", "');"),
			"check"   => "%email"
		)
	), "nowrap");

	$view->defineComplexMapping("complex_actions_private", array(
		array(
			"type"    => "action",
			"src"     => "info",
			"alt"     => gettext("more information"),
			"link"    => array("javascript: popup('index.php?mod=address&action=show_private&id=", "%id", "&addresstype=$addresstype', 'info', 700, 600, 1);"),
			"check"   => "%addressacc"
		),
		array(
			"type"    => "action",
			"src"     => "edit",
			"alt"     => gettext("edit"),
			"link"    => array("javascript: popup('index.php?mod=address&action=edit&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 700, 600, 1);"),
			"check"   => "%addressmanage"
		),
		array(
			"type"    => "action",
			"src"     => "mail_new",
			"alt"     => gettext("email"),
			"link"    => array("javascript: emailSelectFrom('", "%email", "','", "%mail_id", "');"),
			"check"   => "%email"
		)
	), "nowrap");

/* first column in addresslist holds buttons to do actions on the record. These depend on permissions */
$view->defineComplexMapping("complex_actions_bcards", array(
	array(
		"type"    => "action",
		"src"     => "info",
		"alt"     => gettext("more information"),
		"link"    => array("javascript: popup('index.php?mod=address&action=show_bcard&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 700, 600, 1);"),
	),
	array(
		"type"    => "action",
		"src"     => "edit",
		"alt"     => gettext("edit"),
		"link"    => array("javascript: popup('index.php?mod=address&action=edit_bcard&id=", "%id", "&addresstype=$addresstype&sub=$sub", "', 'addressedit', 700, 600, 1);"),
		"check"   => "%addressmanage"
	),
	array(
		"type"    => "action",
		"src"     => "view_all",
		"alt"     => gettext("add classification"),
		"link"    => array("javascript: popup('index.php?mod=address&action=addcla_inline&type=bcard&id=", "%id", "', 'addcla_multi', 350, 150, 1);"),
		"check"   => "%addressmanage"
	),
	array(
		"type"    => "action",
		"src"     => "vcard",
		"alt"     => gettext("export as vCard"),
		"link"    => array("javascript: popup('index.php?mod=address&action=exportVcard&id=", "%id", "&type=".$_REQUEST["addresstype"]."');"),
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

/* the name is a link to an overview */
$view->defineComplexMapping("complex_relationsname", array(
	array(
		"type"    => "link",
		"text"    => "%companyname",
		"link"    => array("javascript: selectRel(", "%id", ", '", "%companyname_html", "')"),
	)
));
/* if no records, show user a nice message about that */
if (!$addressinfo_arr["total_count"]) {
	$venster->addCode(gettext("no addresses with those parameters"));
} else {
	$venster->addCode( $view->generate_output() );
}
unset($view);
$table = new Layout_table();
$table->addTableRow();
	if ($_REQUEST["action"]=="searchRel") {
		$table->addTableData();
			/* delete selected relation if the action was selecting relation */
			$table->insertAction("delete", gettext("no contact"), "javascript: selectRel(-1, '".gettext("none")."')");
		$table->endTableData();
	}
	$table->addTableData(array("style"=>"text-align: right", "colspan"=>1));
		if (is_array($_REQUEST["classifications"])) {
			$url = "javascript: cla_page('%%');";
		} else {
			$url = "index.php?mod=address&action=$action&addresstype=$addresstype&top=%%&l=$l&sub=".$sub."&search=$search&landSelect=$landSelect&specified=$specified&funambol_user=".$_REQUEST["funambol_user"];
		}
		$paging = new Layout_paging();
		$paging->setOptions($top, $addressinfo_arr["total_count"], $url);
		$table->addCode( $paging->generate_output() );
	$table->endTableData();
$table->endTableRow();
if ($user->checkPermission("xs_addressmanage") || $user->checkPermission("xs_usermanage")) {
	$table->addTableRow(1);
		$table->addTableData(array("style"=>"text-align: right", "colspan"=>2));
			if ($addresstype == "nonactive")
				$table->insertAction("delete", gettext("delete non-active selection"), "javascript: popup('index.php?mod=address&action=deleteSelection&info=".urlencode($exportinfo)."', 'delete_selection', 450, 350, 1);");
			else
				$table->insertAction("toggle", gettext("put selection on non-active"), "javascript: popup('index.php?mod=address&action=removeSelection&info=".urlencode($exportinfo)."', 'remove_selection', 450, 350, 1);");
			$table->insertAction("view_all", gettext("manage selection classification"), "javascript: popup('index.php?mod=address&action=addcla_multi&info=".urlencode($exportinfo)."', 'addcla_multi', 350, 150, 1);");
			$table->insertAction("file_download", gettext("export selection"), "index.php?dl=1&mod=address&action=export&info=".urlencode($exportinfo));
			$table->insertAction("print", gettext("print"), "javascript: popup('index.php?mod=address&action=print&info=".urlencode($exportinfo)."', 'print', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
}

/* this always has to be a REQUEST, because we modify some data if address_strict_permissions is set */
if (is_array($_REQUEST["classifications"])) {
	$classification = new Classification_output();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace();
		$table->endTableData();
	$table->endTableRow();
		$table->addTableData(array("colspan"=>2), "header");
			$table->addCode(gettext("classifications").":");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("type").":", "", "bold top");
		if ($_REQUEST["addresstype"]=="bcards") {
			$table->insertTableData(gettext("business cards"));
		} else {
			$table->insertTableData(gettext("contacts"));
		}
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("selection").":", "", "bold top");
		if ($_REQUEST["selectiontype"]=="and") {
			$table->insertTableData(gettext("unique classifications (AND)"));
		} else {
			$table->insertTableData(gettext("added classifications (OR)"));
		}
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("positive").":", "", "bold top");
		$table->addTableData();
			$table->addCode( $classification->classification_selection("null", $_REQUEST["classifications"]["positive"], "enabled", 1) );
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("negative").":", "", "bold top");
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
	$output->addHiddenField("top", $top);
	$output->addHiddenField("campaign", $_REQUEST["campaign"]);
	$output->addHiddenField("l", $l);
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
		function cla_letter(letter) {
			document.getElementById('classifications_frm').l.value = letter;
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
/* attach javascript */
$output->load_javascript(self::include_dir."show_list.js");
$output->load_javascript(self::include_dir."sync.js");
$output->load_javascript("classes/voip/inc/newvoip_calls.js");

$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );


$output->layout_page_end();
$output->exit_buffer();
?>
