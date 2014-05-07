<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$fspath = $GLOBALS["covide"]->filesyspath;

	$output = new layout_output();
	$output->layout_page();

	$venster = new Layout_venster( array(
		"title" => gettext("E-mail templates"),
		"subtitle" => gettext("edit")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_template_list($id);
	}
	$venster->addMenuItem(gettext("templates"), "?mod=email&action=templates");
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("template name"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[description]", $list[0]["description"], array("style"=>"width: 350px"));
		$tbl->endTableData();
	$tbl->endTableRow();
	
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("template"), "", "header");
		$tbl->addTableData("", "data");
		$tbl->addTextArea("mail[html_data]",  $list[0]["html_data"], array(
				"style" => "width: 850px; height: 600px;"
			), "contents");
		$editor = new Layout_editor();
		$tbl->addCode( $editor->generate_editor("", "", "false", "contents", 1) );
		$tbl->endTableData();
	$tbl->endTableRow();

			
	// Template creating help
	$tbl->addTableRow();
	$tbl->insertTableData("", "", "header");
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("Two minimal requirements are '{{tpl_cmnt}}' for the commencement in newsletters and '{{tpl_body}}' for the text you type in the Covide mail composer."));
		$tbl->endTableData();
	$tbl->endTableRow();
	
	
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("back"), "?mod=email&action=templates");
			$tbl->insertAction("save", gettext("save"), "javascript: template_save_db();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addVensterData();
		$venster->addCode( $tbl->generate_output() );
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "POST",
		"enctype" => "multipart/form-data"
	));
	$output->addHiddenField("mod", "email");
	$output->addHiddenField("action", "templateSave");
	$output->addHiddenField("id", $id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."templateEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
