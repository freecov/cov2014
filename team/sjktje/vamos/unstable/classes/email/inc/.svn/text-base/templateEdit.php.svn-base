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
		$tbl->insertTableData(gettext("header"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextArea("mail[header]", $list[0]["header"], array("style"=>"width: 350px; height: 100px"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("footer"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextArea("mail[footer]", $list[0]["footer"], array("style"=>"width: 350px; height: 100px"));
		$tbl->endTableData();
	$tbl->endTableRow();

	/* retrieve template files */
	$files = $mailData->get_template_filelist($id);

	for ($i=1;$i<=4;$i++) {
		switch ($i) {
			case "1":
				$pos = "t";
				$name = gettext("top");
				break;
			case "2":
				$pos = "l";
				$name = gettext("left");
				break;
			case "3":
				$pos = "r";
				$name = gettext("right");
				break;
			case "4":
				$pos = "b";
				$name = gettext("bottom");
				break;
		}
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("image")." ".$name, "", "header");
			$tbl->addTableData("", "data");
				if (!$id) {
					if ($pos == "t") {
						$tbl->addCode( gettext("save template first") );
					}
				} else {
					if ($files[$pos]) {
						//$tbl->insertAction("view", gettext("show"), sprintf("javascript: template_view_file('%d');", $files[$pos]["id"]));
						$tbl->insertAction("delete", gettext("delete"), sprintf("javascript: template_delete_file('%d', '%d');", $files[$pos]["id"], $id));
						$tbl->addSpace();
						$tbl->addCode($files[$pos]["name"]);
					} else {
						$tbl->addBinaryField("image[$pos]");
					}
				}
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("back"), "?mod=email&action=templates");
			$tbl->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
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