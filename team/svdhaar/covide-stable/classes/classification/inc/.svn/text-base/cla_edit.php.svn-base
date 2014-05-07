<?php
/**
 * Covide Groupware-CRM Classification
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
if (!class_exists("Classification_output")) {
	die("no class definition found");
}
/* get the data if id is given, otherwise init empty array */
$classification_data = new Classification_data();
if ($id) {
	$classification_info = $classification_data->getClassificationById($id);
	
	$address_data = new Address_data();
	$multirel = $address_data->getAddressIdByClassification($id);
	$subtitle = gettext("alter classification");
} else {
	$classification_info = array(
		"id"          => 0,
		"is_active"   => 1,
		"description" => ""
	);
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$classification_info["is_cms"] = 0;
	}
	$subtitle = gettext("new classification");
}

$cla_groups_info = $classification_data->getGroups();
$cla_groups = array(0 => "--");
foreach ($cla_groups_info as $cla_gr) {
	$cla_groups[$cla_gr["id"]] = $cla_gr["name"];
}
/* start output buffer */
$output = new Layout_output();
$output->layout_page(gettext("alter classification"), 1);
/* make nice window */
$venster = new Layout_venster(array(
	"title"    => gettext("classifications"),
	"subtitle" => $subtitle
));
$venster->addVensterData();
	/* create form */
	$venster->addTag("form", array(
		"method" => "post",
		"id"     => "claedit",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "classification");
	$venster->addHiddenField("action", "cla_save");
	$venster->addHiddenField("cla[id]", $id);
	/* put a table here for the layout */
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("classification"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("cla[description]", $classification_info["description"], array("style"=>"width: 300px;"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("description"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("cla[description_full]", $classification_info["description_full"], array("rows"=>"4", "cols"=> "50"));
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("group"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("cla[group_id]", $cla_groups, $classification_info["group_id"]);
		$table->endTableData();
	$table->endTableRow();

	$user = new User_data();
	$user->getUserPermissionsById($_SESSION["user_id"]);
	if ($GLOBALS["covide"]->license["has_hypo"] && $user->checkPermission("xs_hypo")) {
		$table->addTableRow();
			$table->insertTableData(gettext("type"), "", "header");
			$table->addTableData("", "data");
				$available_subtypes = array(
					0 => "--",
					1 => gettext("loan"),
					2 => gettext("insurance company")
				);
				$table->addSelectField("cla[subtype]", $available_subtypes, $classification_info["subtype"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$table->addHiddenField("cla[subtype]", $classification_info["subtype"]);
	}

	/* access r/w */
	$table->addTableRow();
		$table->insertTableData(gettext("read/write access"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("cla[access]", $classification_info["access"]);
			$useroutput = new User_output();
			$table->addCode($useroutput->user_selection("claaccess", $classification_info["access"], array(
				"multiple" => 1,
				"inactive" => 1,
				"groups"   => 1,
				"confirm"  => 1
			)));
			unset($useroutput);
		$table->endTableData();
	$table->endTableRow();

	/* access readonly/limited */
	$table->addTableRow();
		$table->insertTableData(gettext("limited readonly access"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("cla[access_read]", $classification_info["access_read"]);
			$useroutput = new User_output();
			$table->addCode($useroutput->user_selection("claaccess_read", $classification_info["access_read"], array(
				"multiple" => 1,
				"inactive" => 1,
				"groups"   => 1,
				"confirm"  => 1
			)));
			unset($useroutput);
		$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(gettext("active"), "", "header");
		$table->addTableData("", "data");
			$table->insertCheckBox("cla[is_active]", "1", $classification_info["is_active"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("available in cms"), "", "header");
			$table->addTableData("", "data");
				$table->insertCheckBox("cla[is_cms]", "1", $classification_info["is_cms"]);
			$table->endTableData();
		$table->endTableRow();
	}
	/* relation (multiple) */
	if ($_REQUEST["show_relations"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData("", "data");
			$table->addHiddenField("cla[address_id]", $classification_info["address_id"]);
				$table->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$table->addSpace(1);
				$table->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
	} else {
		$table->addTableRow();
			$table->insertTableData(gettext("contact"), "", "header");
			$table->addTableData("", "data");
				$table->insertLink(gettext("Show all").' '.count($multirel).' '.gettext("relations"), array("href" => "?mod=classification&action=cla_edit&show_relations=1&id=$id"));
				$table->addSpace();
				$table->insertLink(gettext("Show in addresbook"), array("href" => "javascript:popup('?mod=address&classifications[positive]=|$id|&addresstype=relations&hide=1')"));
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->insertAction("save", gettext("save"), "javascript: cla_save();");
			if ($id) {
				$table->insertAction("delete", gettext("delete"), "javascript: cla_remove($id)");
			}
		$table->endTableData();
	$table->endTableRow();


	$table->endTable();
	$venster->addCode($table->generate_output());
	$venster->endTag("form");
	/* end form */
$venster->endVensterData();
/* include window in output buffer */
$output->addCode($venster->generate_output());
$output->load_javascript(self::include_dir."classification_actions.js");
/* do some more magic with the rel field if necessary */
if (is_array($multirel) && $_REQUEST["show_relations"]) {
	$output->start_javascript();
	$output->addCode("addLoadEvent( update_relsearch() );\n");
	$output->addCode("function update_relsearch() { \n");
	foreach ($multirel as $i=>$n) {
		if ($i) {
			$output->addCode("\n");
			$output->addCode("selectRel($i, \"$n\");");
		}
	}
	$output->addCode("\n}\n");
	$output->end_javascript();
}
$output->layout_page_end();
/* flush the buffer to the browser */
$output->exit_buffer();
?>
