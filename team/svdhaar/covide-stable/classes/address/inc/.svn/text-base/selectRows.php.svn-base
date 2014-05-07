<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @author Michiel van Baak <michiel@covide.nl>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */

if (!class_exists("Address_output")) {
	die("no class definition found");
}

/* define the fields */
$selectfields = array(
	"relations" => array(
		"name",
		"given name",
		"last name",
		"birthday",
		"address",
		"zip code",
		"city",
		"country",
		"telephone nr",
		"email",
		"jobtitle",
		"warning"
	),
	"bcards" => array(
		array("personal"),
		"name",
		"given name",
		"last name",
		"personal address",
		"personal zipcode",
		"personal city",
		"personal country",
		"personal email",
		"personal telephone nr",
		array("business"),
		"relation",
		"jobtitle",
		"address",
		"zipcode",
		"city",
		"country",
		"email",
		"telephone nr",
		"mobile phone nr",
		"website",
		array("other"),
		"memo",
		"classifications"
	)
);

$user_data = new User_data();
$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
switch ($type) {
case "users" :
	$default = unserialize($user_info["default_address_fields_users"]);
	$fields = $selectfields["bcards"];
	break;
case "private" :
	$default = unserialize($user_info["default_address_fields_private"]);
	$fields = $selectfields["bcards"];
	break;
case "bcards":
	$default = unserialize($user_info["default_address_fields_bcard"]);
	$fields = $selectfields["bcards"];
	break;
default:
case "relations":
	// In case we hit the 'default' above, make sure we set the $type variable for the form below.
	$type = "relations";
	$default = unserialize($user_info["default_address_fields"]);
	$fields = $selectfields["relations"];
	break;

}

/* define the options */
/* get the same amount as fields we have */
$options = array(0 => gettext("none"));
$optioncount = 1;
foreach ($fields as $field) {
	if (!is_array($field)) {
		$options[$optioncount] = $optioncount;
		$optioncount++;
	}
}

$output = new Layout_output();
$output->layout_page("", 1);
$venster = new Layout_venster(array(
	"title"    => gettext("addresses"),
	"subtitle" => gettext("select and sort")
));
$output->addTag("form", array(
	"id"     => "deze",
	"method" => "post",
	"action" => "index.php"
));
$output->addHiddenField("action", "saveSortAndSelect");
$output->addHiddenField("type", $type);
$output->addHiddenField("mod", "address");

$venster->addVensterData();

	$table = new Layout_table();
	$table->addTableRow(array("cellspacing" => 1));
		$table->insertTableData(gettext("Select and sort the rows you would like to see"), array("colspan" => 2), "data");
	$table->endTableRow();
	/* make a nice header */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode(gettext("field"));
		$table->endTableData();
		$table->addTableData("", "header");
			$table->addCode(gettext("sort"));
		$table->endTableData();
	$table->endTableRow();
	foreach ($fields as $field) {
		if (!is_array($field)) {
			$table->addTableRow();
				$table->addTableData("", "data");
					$table->addCode(gettext($field));
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addSelectField(sprintf("sort[%s]", $field), $options, $default[$field]);
				$table->endTableData();
			$table->endTableRow();
		} else {
			$table->addTableRow();
				$table->insertTableData(gettext($field[0]), array("colspan" => 2), "header");
			$table->endTableRow();
		}
	}
	$table->addTableRow();
		$table->addTableData(array("colspan" => 2), "data");
			$table->insertAction("reset", gettext("reset"), "javascript: resetAll();");
			$table->insertAction("save", gettext("save"), "javascript: saveSort();");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
$venster->addCode($table->generate_output());
$venster->endVensterData();

$output->addCode($venster->generate_output());
unset($venster);
$output->endTag("form");
	$output->start_javascript();
	$output->addCode("
		function resetAll() {
			var frm = document.getElementById('deze');
			for (i=0;i<frm.elements.length;i++) {
				if (frm.elements[i].name.match(/^sort\[/gi)) {
					frm.elements[i].selectedIndex = 0;
				}
			}
		}
		function saveSort() {
			document.getElementById('deze').submit();
		}
	");
	$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();

?>
