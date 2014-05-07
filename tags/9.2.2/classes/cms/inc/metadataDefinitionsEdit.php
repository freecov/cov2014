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
		"subtitle" => gettext("add/alter metadata definition")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getMetadataDefinitionById($_REQUEST["id"]);
	$venster->addVensterData();

		$tbl = new Layout_table(array(
			"cellspacing" => 1
		));
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("Metadata definition"), array("colspan"=>2), "header");
		$tbl->endTableRow();
		/* field name */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[field_name]", $cms["field_name"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* group */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("group"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[group]", $cms["group"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* field type */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("type of field"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addSelectField("cms[field_type]", $cms_data->meta_field_types, $cms["field_type"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* order */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("order"), "", "header");
			$tbl->addTableData("", "data");
				$sel = array();
				for ($i=0;$i<=50;$i++) {
					$sel[$i] = $i;
				}
				$tbl->addSelectField("cms[order]", $sel, $cms["order"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* show frontpage */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("active on website"), "", "header");
			$tbl->addTableData("", "data");
				$sel = array(
					0 => "+ ".gettext("show this item in website"),
					1 => "- ".gettext("hide this item in website")
				);
				$tbl->addSelectField("cms[fphide]", $sel, $cms["fphide"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default value */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("default value")."*", "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[field_value]", $cms["field_value"], array(
					"style" => "width: 300px; height: 150px;",
					"wrap"  => "off"
				));
				$tbl->addTag("i");
				$tbl->addTag("br");
				$tbl->addCode("* ".gettext("This is the default value."));
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("For a dropdown menu enter the values seperated by a newline. One item per line."));
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext(" De eerste waarde hierbij zal de standaard waarde zijn. De waarde '--' (2 streepjes) zal niet worden getoond op de site."));
				$tbl->endTag("i");

			$tbl->endTableData();

		$tbl->endTableRow();


		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );


		$venster->insertAction("back", gettext("back"), "?mod=cms&action=metadataDefinitions");
		$venster->insertAction("save", gettext("new item"), "javascript: saveSettings();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveMetadataDefinition");
	$output->addHiddenField("id", $_REQUEST["id"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>