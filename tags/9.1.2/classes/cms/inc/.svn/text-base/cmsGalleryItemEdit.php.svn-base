<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$cms_data = new Cms_data();
	$cms = $cms_data->getGalleryItem($id);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => ($cms["pageid"] == -1) ? gettext("alter banner"):gettext("alter gallery item")
	));

	$venster->addVensterData();

		$tbl = new Layout_table(array(
			"cellspacing" => 1
		));
		/* order */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("order"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[order]", $cms["order"], array("style" => "width: 50px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		if ($cms["pageid"] == -1) {
			/* website */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("website"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextField("cms[url]", $cms["url"], array("style" => "width: 250px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* rating */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("rating"), "", "header");
				$tbl->addTableData("", "data");
					$sel = array(
						0 => gettext("inactive")
					);
					for ($i=1;$i<=10;$i++) {
						$sel[$i] = $i."x";
					}
					for ($i=15;$i<=30;$i+=5) {
						$sel[$i] = $i."x";
					}
					for ($i=40;$i<=100;$i+=10) {
						$sel[$i] = $i."x";
					}
					$tbl->addSelectField("cms[rating]", $sel, $cms["rating"]);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		/* field name */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[file]", $cms["file"], array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default value */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("description"), "", "header");
			$tbl->addTableData("", "data");
				/*
				$tbl->addTextArea("cms[description]", $cms["description"], array(
					"style" => "width: 300px; height: 150px;"
				));
				*/
				$editor = new Layout_editor();
				$ret = $editor->generate_editor(1);
				$tbl->addTextArea("cms[description]", trim($cms["description"]), array(
					"style" => "width: 570px; height: 270px;"), "contents"
				);
				if ($ret !== false)
					$tbl->addCode($ret);

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );
		$venster->start_javascript();
			$venster->addCode("
				function syncDescription() {
					if (window.sync_editor_mini)
						sync_editor_mini();
				}
			");
		$venster->end_javascript();
		$venster->insertAction("back", gettext("back"), "javascript: window.close();");
		$venster->insertAction("save", gettext("new item"), "javascript: syncDescription(); saveSettings();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveGalleryItem");
	$output->addHiddenField("id", $id);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>