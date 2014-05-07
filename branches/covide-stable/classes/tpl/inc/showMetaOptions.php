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

	$output = new Layout_output();
	$output->addTag("form", array(
		"action" => "/metadata/",
		"method" => "post",
		"id"     => "metaform"
	));
	$output->addHiddenField("metainit", 1);

	$tbl = new Layout_table();

	$data = $this->cms->getMetadataDefinitions(1);
	$avail_formitems = array();
	foreach ($data as $groupname=>$group) {
		if (!$groupname)
			$groupname = gettext("global");

		$tbl->addTableRow();
			$tbl->addTableData(array(
				"colspan" => 2
			));
				$tbl->addCode(sprintf("<b id='meta_categories'>category: %s<br><hr></b>", $groupname));
			$tbl->endTableData();
		$tbl->endTableRow();

		#echo "<PRE>";
		#print_r($group);
		$tbl->start_javascript();
			$tbl->addCode(sprintf(" var meta_error_msg = '%s'; ",
				addslashes(gettext('Cannot select more than five criteria.'))
			));
		$tbl->end_javascript();

		foreach ($group as $formitem) {
			$avail_formitems[] = $formitem["id"];
			$tbl->addTableRow();
				$tbl->addTableData(array("style" => "vertical-align: top;"));
					$tbl->addTag("span", array(
						"id" => sprintf("holder_%d", $formitem["id"])
					));
						$tbl->insertCheckBox(sprintf("avail[%d]", $formitem["id"]), 1, 0, 0);
					$tbl->endTag("span");

					$tbl->start_javascript();
						$tbl->addCode(sprintf("
							document.getElementById('avail%1\$d').onchange = function() {
								changeMetaCounter(document.getElementById('avail%1\$d'), %1\$d);
							}\n
							", $formitem["id"]
						));
					$tbl->end_javascript();

					$tbl->addCode($formitem["field_name"]);
					$tbl->addCode(':');
					$tbl->addSpace(2);
				$tbl->endTableData();
				$tbl->addTableData(array(
					"id" => sprintf("metalayer_%d", $formitem["id"]),
					"style" => "display: none"
				));
				switch ($formitem["field_type"]) {
					case "text":
					case "textarea":
						if (trim($formitem["field_value"]) == "--")
							$formitem["field_value"] = "";

						$tbl->addTextField(sprintf("data[%d]", $formitem["id"]), $formitem["field_value"], array(
							"style" => "text-align: left; width: 200px;",
							"maxlength" => 100
						));
						break;
					case "numeric":
					case "financial":
						if (trim($formitem["field_value"]) == "--")
							$formitem["field_value"] = "";
						if ($formitem['field_type'] == 'financial') $tbl->addCode($this->valuta);
						$tbl->addTextField(sprintf("data[%d][]", $formitem["id"]), $formitem["field_value"], array(
							"style" => "text-align: left; width: 80px;",
							"maxlength" => 100
						));
						$tbl->addCode(' - ');
						if ($formitem['field_type'] == 'financial') $tbl->addCode($this->valuta);
						$tbl->addTextField(sprintf("data[%d][]", $formitem["id"]), $formitem["field_value"], array(
							"style" => "text-align: left; width: 80px;",
							"maxlength" => 100
						));
						break;

					case "select":
						$seltemp = explode("\n", $formitem["field_value"]);
						$sel     = array();
						foreach ($seltemp as $vv) {
							if (trim($vv) == "--")
								$vv = "";

							$sel[$vv] = $vv;
						}
						$tbl->addSelectField(sprintf("data[%d]", $formitem["id"]), $sel, array(
							"style" => "text-align: left;"
						));
						break;
					case "checkbox":
						$sel = explode("\n", $formitem["field_value"]);
						foreach ($sel as $k=>$v) {
							$tbl->addCheckBox(sprintf("data[%d][%s]", $formitem["id"], $k), $v, 0);
							$tbl->addSpace();
							$tbl->addCode($v);
							$tbl->addTag("br");
						}
						break;
				}
				$tbl->endTableData();
			$tbl->endTableRow();
		}
	}
	$tbl->endTable();
	$output->addCode($tbl->generate_output());
	$output->addTag("br");

	$output->addTag("input", array(
		"type" => "submit",
		"name" => "submit",
		"value" => gettext("search")." &gt&gt",
		"class" => "inputbutton inputtext",
		"style" => "display: inline; width: 150px;"
	));

	$output->endTag("form");
	#$output->addTag("br");
	#$output->insertTag("a", gettext("search")." &gt;&gt;", array(
	#	"href" => "javascript: submitMetaForm();"
	#));
	echo $output->generate_output();

?>
