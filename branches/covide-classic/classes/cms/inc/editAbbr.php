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

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("cms abbreviation")
	));

	$cms_data = new Cms_data();
	if ($id) {
		$cms = $cms_data->getAbbreviations($id);
		$cms = $cms[0];
	}

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("abbreviation"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[abbreviation]", $cms["abbreviation"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("description"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[description]", $cms["description"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* languages */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("available in"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = $cms_data->lang;
				if (!is_array($cms["lang"]))
					$cms["lang"] = array();

				foreach ($sel as $k=>$v) {
					$tbl->insertCheckBox("cms[lang][$k]", $k, (in_array($k, $cms["lang"])) ? 1:0);
					$tbl->addSpace();
					$tbl->addCode($v." (".$k.")");
					$tbl->addTag("br");
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("back", gettext("back"), "?mod=cms&action=editAbbreviations");
		$venster->insertAction("save", gettext("save"), "javascript: saveSettings();");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveAbbreviation");
	$output->addHiddenField("id", $id);

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();

?>