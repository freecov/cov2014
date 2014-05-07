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
/* Process requested parameters */
$header_id = $_REQUEST["edit_id"];
$header_cid = $_REQUEST["cid"];

$header_cat = array(
	1=>gettext("commencement"),
	2=>gettext("title"),
	3=>gettext("letterhead"),
	4=>gettext("suffix")
);

/* Get data */
$data = new Address_data();
switch($header_cid) {
	case 1: $titles = $data->getCommencements($header_id); 		break;
	case 2: $titles = $data->getTitles($header_id); 			break;
	case 3: $titles = $data->getLetterheads($header_id); 		break;
	case 4: $titles = $data->getSuffix($header_id); 			break;
}

/* start building output */
$output = new Layout_output();
$output->layout_page(gettext("edit")." ".$header_cat[$header_cid], 1);
/* venster object */
$venster_settings = array(
	"title"    => $header_cat[$header_cid],
	"subtitle" => gettext("edit")." ".$header_cat[$header_cid]
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
	$venster->addHiddenField("cid", $header_cid);
	$venster->addHiddenField("titles[0][id]", $titles[$header_id]["id"]);
	if($_REQUEST["edit_id"]) {
		$venster->addHiddenField("method", "edit");
	} else {
		$venster->addHiddenField("method", "new");
	}

	$table = new Layout_table(array("cellpadding"=>10));
	$table->addTableRow();
        $table->insertTableData($header_cat[$header_cid]);
		$table->addTableData();
          $table->addTextField("titles[0][title]", $titles[$header_id]["title"]);
		$table->endTableData();
		$table->addTableData();
          $table->insertAction("save", gettext("Save"), "javascript: save_it();");
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
