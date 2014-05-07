<?php
	$output->start_javascript();
	$output->addCode("
		function formsubmit() {
			var stop = 0;
	");
	foreach ($forms as $formitem) {
		if ($formitem["field_type"] != "hidden" && $formitem["is_required"]) {
			if ($formitem["field_type"] != "checkbox") {
				/* textfield, textarea, dropdown */
				$output->addCode(sprintf("
					if (stop == 0 && !document.getElementById('%s').value) {
							var stop = 1;
							alert('%s: %s');
						}
					",
					preg_replace("/(\[)|(\])|( )/s", "", sprintf("data[%s]", $formitem["field_name"])),
					addslashes(gettext("no values for")),
					addslashes($formitem["field_name"])
				));
			} else {
				/* check box */
				$sel = explode("\n", $formitem["field_value"]);
				foreach ($sel as $k=>$v) {
					$sel[$k] = sprintf("document.getElementById('%s').checked",
						preg_replace("/(\[)|(\])|( )/s", "", sprintf("data[%s][%s]", $formitem["field_name"], $k)));
				}
				$output->addCode(sprintf("
					if (!(%s)) {
							var stop = 1;
							alert('%s: %s');
					}
					",
					preg_replace("/(\r)|(\t)|(\n)/s", "", implode(" || ", $sel)),
					addslashes(gettext("no values for")),
					addslashes($formitem["field_name"])
				));
			}
		}
	}

	$output->addCode("
			if (stop == 0) {
				document.getElementById('formident').submit();
			}
		}
	");
	$output->end_javascript();

	$tbl = new Layout_table();
	foreach ($forms as $formitem) {
		if ($formitem["field_type"] != "hidden") {
			if ($short_description && $formitem["description"]) {
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addSpace();
					$tbl->endTableData();
					$tbl->addTableData(array("style" => "vertical-align: top;"));
						$tbl->addTag("br");
						$tbl->insertTag("i", nl2br($formitem["description"]));
					$tbl->endTableData();
				$tbl->endTableRow();
			} elseif ($formitem["description"]) {
				$tbl->addTableRow();
					$tbl->addTableData(array("style" => "vertical-align: top;", "colspan" => 2));
						$tbl->addCode(nl2br($formitem["description"]));
						$tbl->addTag("br");
						$tbl->addTag("br");
					$tbl->endTableData();
				$tbl->endTableRow();
			}

			$tbl->addTableRow();
				$tbl->addTableData(array("style" => "vertical-align: top;"));
					$tbl->addCode($formitem["field_name"]);
					$i=0;
					if ($formitem["is_required"]) {
						$tbl->addCode("*");
						$i++;
					}
					$tbl->addSpace(2);
				$tbl->endTableData();
				$tbl->addTableData();
					switch ($formitem["field_type"]) {
						case "text":
							$tbl->addTextField(sprintf("data[%s]", $formitem["field_name"]), $formitem["field_value"], array(
								"style" => "text-align: left;"
							));
							break;
						case "textarea":
							$tbl->addTextArea(sprintf("data[%s]", $formitem["field_name"]), $formitem["field_value"], array(
								"style" => "text-align: left;"
							));
							break;
						case "select":
							$seltemp = explode("\n", $formitem["field_value"]);
							$sel     = array();
							foreach ($seltemp as $vv) {
								$sel[$vv] = $vv;
							}
							$tbl->addSelectField(sprintf("data[%s]", $formitem["field_name"]), $sel, array(
								"style" => "text-align: left;"
							));
							break;
						case "checkbox":
							$sel = explode("\n", $formitem["field_value"]);
							foreach ($sel as $k=>$v) {
								$tbl->addCheckBox(sprintf("data[%s][%s]", $formitem["field_name"], $k), $v, 0);
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
	$tbl->addTableRow();
		$tbl->addTableData(array(
			"style"   => "text-align: left; padding-top: 20px;"
		));
			if ($i>0) {
				$tbl->addCode("* = ".gettext("mandatory"));
				$tbl->addSpace(2);
			}
		$tbl->endTableData();
		$tbl->addTableData(array(
			"style"   => "text-align: right; padding-top: 20px;"
		));
			$tbl->addCode($custom_nav);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();


?>