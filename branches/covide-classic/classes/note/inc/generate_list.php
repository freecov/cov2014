<?php
/**
 * Covide Groupware-CRM Notes generate_list
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
/* make some URL params easier to access */
$action     = $_REQUEST["action"];
$top        = $_REQUEST["top"];
$limit      = $this->pagesize;
$zoekstring = $_REQUEST["zoekstring"];
if ($_REQUEST["action"] == "search")
	$options["search"] = 1;

/* get the permissions for the user */
$user = new User_data();
$user->getUserPermissionsById($_SESSION["user_id"]);
$user_arr = $user->getUserList();

/* find out what options to set for our notes data collector */
if (array_key_exists("search", $_REQUEST)) {
	$options["note_type"] = "all";
	if (array_key_exists("searchkey", $_REQUEST["search"]))
		$zoekstring = $_REQUEST["search"]["searchkey"];
	if (array_key_exists("user_id", $_REQUEST["search"])) {
		/* safety check. only note managers and admins are allowed to specify other user_ids */
		if ($user->checkPermission("xs_usermanage") || $user->checkPermission("xs_notemanage"))
			$options["user_id"] = $_REQUEST["search"]["user_id"];
		else
			$options["user_id"] = $_SESSION["user_id"];
	}
	if (array_key_exists("address_id", $_REQUEST["search"])) {
		$options["address_id"] = $_REQUEST["search"]["address_id"];
		$address_data = new Address_data();
		$relname = $address_data->getAddressNameById($_REQUEST["search"]["address_id"]);
		unset($address_data);
	}
	$options["timestamp_start"] = mktime(0, 0, 0, $_REQUEST["search"]["from_month"], $_REQUEST["search"]["from_day"], $_REQUEST["search"]["from_year"]);
	$options["timestamp_end"]   = mktime(0, 0, 0, $_REQUEST["search"]["to_month"], $_REQUEST["search"]["to_day"]+1, $_REQUEST["search"]["to_year"]);
}

if ($_REQUEST["action"] == "searchsv")
	$options["note_type"] = $_REQUEST["note_type"];

if (!is_array($options))
	$options["note_type"] = "current";

$options["top"]        = $top;
$options["limit"]      = $limit;
$options["zoekstring"] = $zoekstring;
$options["sort"]       = $_REQUEST["sort"];

/* generate some date arrays */
$days = array();
for ($i=1; $i<=31; $i++)
	$days[$i] = $i;
$months = array();
for ($i=1; $i<=12; $i++)
	$months[$i] = $i;
$years = array();
for ($i=2000; $i<=date("Y"); $i++)
	$years[$i] = $i;
	
/* get the notes data */
$notedata  = new Note_data();
$notes_arr = $notedata->getNotes($options);

/* start output buffer routines */
$output = new Layout_output();
$output->layout_page( gettext("Notes") );

if (!$_REQUEST["short_view"]) {
	$user_data = new User_data();
	$user_details = $user_data->getUserDetailsById($_SESSION["user_id"]);
	$_short_view = (int)$user_details["mail_shortview"];
} else {
	$_short_view = (int)$_REQUEST["short_view"];
}

$output->addTag("form", array(
	"id"     => "zelf",
	"method" => "get",
	"action" => "index.php"
));
$output->addHiddenField("mod" ,"note");
$output->addHiddenField("action", "");
$output->addHiddenField("msg_id", "");
$output->endTag("form");
switch ($options["note_type"]) {
	case "sent"   : $subtitle = gettext("sent notes");         break;
	case "old"    : $subtitle = gettext("read notes");         break;
	case "show"   : $subtitle = gettext("sent, unread notes"); break;
	case "drafts" : $subtitle = gettext("draft notes");        break;
	default       : $subtitle = gettext("list of messages");   break;
}
$settings = array(
	"title"    => gettext("Notes"),
	"subtitle" => $subtitle
);
$venster = new Layout_venster($settings);
unset($settings);
$venster->addMenuItem(gettext("new note"), "javascript: note_new();");
$venster->addMenuItem(gettext("current notes"), "./?mod=note&short_view=".$_short_view );
$venster->addMenuItem(gettext("read notes"), "./?mod=note&action=old&short_view=".$_short_view );
$venster->addMenuItem(gettext("drafts"), "./?mod=note&action=drafts&short_view=".$_short_view );
$venster->addMenuItem(gettext("sent"), "./?mod=note&action=sent&short_view=".$_short_view );
$venster->addMenuItem(gettext("sent, unread"), "./?mod=note&action=show&short_view=".$_short_view );
$venster->addMenuItem(gettext("search"), "./?mod=index&search[private]=1&search[notes]=1");
if ($options["note_type"] != "current")
	$venster->addMenuItem(gettext("list of messages"), "./?mod=note&short_view=".$_short_view );

$venster->generateMenuItems();

$venster->addVensterData();
	/* prepare grid */
	$settings = array();
	$data = $notes_arr["notes"];
	$view = new Layout_view();
	$view->addData($data);
	$view->addSettings($settings);

	/* add the mappings so we actually have something */
	if ($_short_view == 1) {
		$view->addMapping("&nbsp;", "%%is_new");
		$view->addMapping(gettext("subject"), "%%complex_subject");
		$view->addMapping(gettext("sender"), "%from_name");
		if ($options["note_type"] == "sent" || $options["note_type"] == "show")
			$view->addMapping(gettext("recipient"), "%to_name");

		$view->addMapping(gettext("contact"), "%%relation_icon");
		$view->addMapping(gettext("date"), "%human_date");
		$view->defineComplexMapping("is_new", array(
			array(
				"type"  => "action",
				"src"   => "note",
				"check" => "%nieuw",
				"alt"   => gettext("this note is unread"),
			)
		));
	} else {
		$view->addMapping(gettext("subject"), "%%complex_contactitem");
		$view->addSubMapping("%%complex_subject", "%nieuw");
		$view->addMapping(gettext("sender"), "%from_name");
		if ($options["note_type"] == "sent" || $options["note_type"] == "show")
			$view->addMapping(gettext("recipient"), "%to_name");

		if ($GLOBALS["covide"]->license["project"])
			$view->addMapping(gettext("project"), "");

		$view->addMapping(gettext("contact"), "%%relation_name");
		$view->addMapping(gettext("date"), "%human_date");
	}

	/* define the mappings */
	/* subject is link to complete note */
	$view->defineComplexMapping("complex_subject", array(
		array(
			"type" => "link",
			"link" => array("index.php?mod=note&action=message&msg_id=", "%id"),
			"text" => "%subject",
			"check" => "%no_draft"
		),
		array(
			"type" => "link",
			"link" => array("javascript:note_edit('", "%id", "');"),
			"text" => "%subject",
			"check" => "%is_draft"
		)
	));
	/* contactitem is image that displays wether this is a contactmoment */
	$view->defineComplexMapping("complex_contactitem", array(
		array(
			"type" => "image",
			"src"  => "f_oud.gif",
			"check" => "%is_support"
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"link" => array("javascript:delete_draft('", "%id", "');"),
			"alt"   => gettext("delete"),
			"check" => "%is_draft"
		)
	));
	$view->defineComplexMapping("relation_name", array(
		array(
			"type" => "link",
			"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
			"text" => "%relation_name"
		)
	));
	$view->defineComplexMapping("relation_icon", array(
		array(
			"type" => "action",
			"src"  => "addressbook",
			"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
			"alt"   => "%relation_name",
			"check" => "%address_id"
		)
	));

	$view->defineSortParam("sort");
	$view->defineSort(gettext("subject"), "subject");
	$view->defineSort(gettext("sender"), "user_name");
	$view->defineSort(gettext("contact"), "address_name");
	$view->defineSort(gettext("date"), "timestamp");

	/* put the table in the $venster data buffer and destroy object */
	$venster->addCode( $view->generate_output() );
	unset($view);

	$paging = new Layout_paging();
	if ($action == "search") {
		$url = "index.php?mod=note&action=$action&short_view=".$_REQUEST["short_view"];
		foreach ($_REQUEST["search"] as $k=>$v)
			$url .= "&search[$k]=$v";
		$url .= "&top=%%";
	} else {
		$url = "index.php?mod=note&action=$action&short_view=".$_REQUEST["short_view"]."&top=%%";
	}
	$paging->setOptions($top, $notes_arr["total_count"], $url);

	$tbl = new Layout_table();
	$tbl->addTableRow();
	$tbl->addTableData();
	if ($_short_view == 1)
		$short_view = -1;
	else
		$short_view = 1;

	$tbl->insertAction("view_all", gettext("short/long view"), "?mod=note&action=".$_REQUEST["action"]."&sort=".$_REQUEST["sort"]."&short_view=".$short_view);
	$tbl->endTableData();
	$tbl->addTableData();
	$tbl->addCode( $paging->generate_output() );
	$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addCode($tbl->generate_output());
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
$output->addTag("br");

$venster_settings = array(
	"title"    => gettext("Notes"),
	"subtitle" => gettext("search")
);
$venster = new Layout_venster($venster_settings);
$venster->addVensterData();
	/* form */
	$venster->addTag("form", array(
		"id"     => "searchform",
		"action" => "index.php",
		"method" => "post"
	));
	$venster->addHiddenField("mod", "note");
	$venster->addHiddenField("action", "search");
	$table = new Layout_table();
	$table->addTableRow();
		$table->insertTableData(gettext("searchkey"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("search[searchkey]", $_REQUEST["search"]["searchkey"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("username"), "", "header");
		$table->addTableData("", "data");
			if ($user->checkPermission("xs_usermanage") || $user->checkPermission("xs_notemanage")) {
			$table->addHiddenField("search[user_id]", $_REQUEST["search"]["user_id"]);
			$useroutput = new User_output();
			$table->addCode( $useroutput->user_selection("searchuser_id", ($_REQUEST["search"]["user_id"]) ? $_REQUEST["search"]["user_id"] : $_SESSION["user_id"], 1, 1, 0, 0, 1) );
		} else {
			$table->addHiddenField("search[user_id]", $_SESSION["user_id"]);
			$table->addCode($user->getUsernameById($_SESSION["user_id"]));
		}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("relation"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("search[address_id]", $_REQUEST["search"]["address_id"]);
			$table->insertTag("span", $relname, array(
				"id" => "searchrel"
			));
			$table->addSpace(1);
			$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("date"), "", "header");
		$table->addTableData(array("align" => "right"), "data");
			$table->addCode(gettext("from"));
			$table->addSpace(1);
			$table->addSelectField("search[from_day]",   $days,   ($_REQUEST["search"]["from_day"]?$_REQUEST["search"]["from_day"]:date("d")));
			$table->addSelectField("search[from_month]", $months, ($_REQUEST["search"]["from_month"]?$_REQUEST["search"]["from_month"]:date("m")));
			$table->addSelectField("search[from_year]",  $years,  ($_REQUEST["search"]["from_year"]?$_REQUEST["search"]["from_year"]:date("Y")-1));
			$table->addTag("br");
			$table->addCode(gettext("to"));
			$table->addSpace(1);
			$table->addSelectField("search[to_day]",   $days,   ($_REQUEST["search"]["to_day"]?$_REQUEST["search"]["to_day"]:date("d")));
			$table->addSelectField("search[to_month]", $months, ($_REQUEST["search"]["to_month"]?$_REQUEST["search"]["to_month"]:date("m")));
			$table->addSelectField("search[to_year]",  $years,  ($_REQUEST["search"]["to_year"]?$_REQUEST["search"]["to_year"]:date("Y")));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData(array("colspan" => 2), "data");
			$table->insertAction("forward", gettext("search"), "javascript: document.getElementById('searchform').submit();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
	$venster->endTag("form");
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->load_javascript(self::include_dir."note_actions.js");
$history = new Layout_history();
$output->addCode( $history->generate_save_state("action") );
$output->layout_page_end();
echo $output->generate_output();
?>
