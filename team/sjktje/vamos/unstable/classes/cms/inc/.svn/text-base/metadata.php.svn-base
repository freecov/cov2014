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
		"subtitle" => gettext("metadata definitions")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getMetadataData($id, $_REQUEST["subaction"]);

	$this->addMenuItems(&$venster);
	$venster->generateMenuItems();

	$venster->addVensterData();
		if ($cms["useMetaData"] == 0) {
			$venster->addCode( gettext("This page has no metadata.") );
			$venster->addCode( gettext("Click")." " );
			$venster->insertTag("a", gettext("here"), array(
				"href" => "?mod=cms&action=metadata&id=".$id."&subaction=enable"
			));
			$venster->addcode( " ".gettext("to enable metadata for this page.") );
			$venster->insertAction("ok", gettext("enable metadata"), "?mod=cms&action=metadata&id=".$id."&subaction=enable");
		} else {
			$venster->addCode( gettext("This page has metadata.") );
			$venster->addCode( gettext("Click")." " );
			$venster->insertTag("a", gettext("here"), array(
				"href" => "?mod=cms&action=metadata&id=".$id."&subaction=disable"
			));
			$venster->addcode( " ".gettext("to disable metadata for this page.") );
			$venster->insertAction("delete", gettext("disable metadata"), "?mod=cms&action=metadata&id=".$id."&subaction=disable");

			$venster->insertAction("close", gettext("close"), "javascript: window.close();");
			$venster->addTag("br");
			$venster->addTag("br");

			$tbl = new Layout_table(array("cellspacing"=>1));
			if (!is_array($cms["data"]))
				$cms["data"] = array();

			foreach ($cms["data"] as $group=>$data) {
				if (!$group) $group = sprintf("[%s]", gettext("default"));
				$tbl->addTableRow();
					$tbl->addTableData(array("colspan" => 2), "header");
						$tbl->insertAction("view_all", "", "");
						$tbl->addCode(" ". $group );
					$tbl->endTableData();
				$tbl->endTableRow();
				foreach ($data as $v) {
					$tbl->addTableRow();
						$tbl->addTableData("", "header");
							$tbl->addCode($v["field_name"]);
						$tbl->endTableData();
						$tbl->addTableData("", "data");
							$this->switchFieldType($tbl, $v);
						$tbl->endTableData();
					$tbl->endTableRow();
				}
			}
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );

			$venster->insertAction("save", gettext("save"), "javascript: saveSettings();");
			$venster->insertAction("close", gettext("close"), "javascript: window.close();");

	}

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveMetadata");
	$output->addHiddenField("id", $_REQUEST["id"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>