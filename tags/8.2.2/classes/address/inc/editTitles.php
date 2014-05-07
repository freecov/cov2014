<?php
/**
 * Covide Groupware-CRM Addressbook module.
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

if (!class_exists("Address_output")) {
	die("no class definition found");
}

/* Get data */
$data = new Address_data();
$titles= $data->getTitles($_REQUEST["edit_id"]);


/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("Edit titles"), 1);
/* venster object */
$venster_settings = array(
	"title"    => gettext("Titles"),
	"subtitle" => gettext("Edit titles")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "titleedit",
		"action" => "index.php",
		"method" => "post",
	));
	$venster->addHiddenField("mod", "address");
	$venster->addHiddenField("action", "saveTitles");
	$venster->addHiddenField("titles[0][id]", $titles[0]["id"]);
	if($_REQUEST["edit_id"] != 0) {
		$venster->addHiddenField("method", "edit");
	} else {
		$venster->addHiddenField("method", "new");
	}

	$table = new Layout_table(array("cellpadding"=>10));
	$table->addTableRow();
        $table->insertTableData(gettext("Title"));
		$table->addTableData();
          $table->addTextField("titles[0][title]", $titles[0]["title"]);
		$table->endTableData();
		$table->addTableData();
          $table->insertAction("save", gettext("Save"), "javascript: save_title();");
		$table->endTableData();
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

$output->start_javascript();
	$output->addCode("var skip_checks = 0;");
$output->end_javascript();

$output->load_javascript(self::include_dir."title_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
