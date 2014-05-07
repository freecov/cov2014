<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	$row = $this->page_cache[$id];
	$cms = $this->cms->getMetadataData($id);

	if (!is_array($cms["data"]))
		$cms["data"] = array();

	foreach ($cms["data"] as $group=>$metadata) {
	if (!$group) $group = sprintf("[%s]", gettext("default"));
	if ($group == -1) continue;
		foreach ($metadata as $k=>$v) {
			if ($v["fphide"] == 1)
				unset($metadata[$k]);
		}
		if (count($metadata) > 0) {
			if (!$tbl)
				$tbl = new Layout_table(array(
					"cellspacing" => 1,
					"cellpadding" => 1,
					"class"       => "view_header table_data metadata_table"
				));

			$tbl->addTableRow();
				$tbl->addTableHeader(array(
					"colspan" => 2,
					"class" => "list_header_center metadata_header",
					"style" => "text-align: left"
				), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addCode(" ". $group );
				$tbl->endTableHeader();
			$tbl->endTableRow();
			foreach ($metadata as $v) {
				$tbl->addTableRow(array("class" => "list_record"));
					$tbl->addTableData("", "header");
						$tbl->insertTag("a", "", array("name" => urlencode($v["field_name"])));
						$tbl->addCode($v["field_name"].': ');
					$tbl->endTableData();
					$tbl->addTableData("", "data");
						if ($v["field_type"] == "shop") {
							$tbl->addCode(sprintf("%s %s", $this->valuta, number_format($row["shopPrice"],2)));
						} elseif ($v["field_type"] == "textarea") {
							if (!preg_match("/<((br)|(p))[^>]*?>/si", $v["value"]))
								$tbl->addCode(nl2br($v["value"]));
							else
								$tbl->addCode($v["value"]);
						} else {
							if ($v['field_type'] == 'financial') {
								$tbl->addCode($this->valuta);
								$tbl->addSpace();
							}
							$tbl->addCode(nl2br($v["value"]));
						}
					$tbl->endTableData();
				$tbl->endTableRow();
			}
		}
	}
	if ($tbl) {
		$tbl->endTable();
		$tbl->addTag("br");
		$data.= $tbl->generate_output();
	}

?>
