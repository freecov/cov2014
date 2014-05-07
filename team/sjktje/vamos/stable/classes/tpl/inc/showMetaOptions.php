<?php
	$output = new Layout_output();
	$output->addTag("form", array(
		"action" => "/metadata/",
		"method" => "post",
		"id"     => "metaform"
	));
	$tbl = new Layout_table();

	$data = $this->cms->getMetadataDefinitions(1);
	foreach ($data as $groupname=>$group) {
		if (!$groupname)
			$groupname = gettext("global");

		$tbl->addTableRow();
			$tbl->addTableData(array(
				"colspan" => 2
			));
				$tbl->addCode(sprintf("<b>category: %s</b><br><hr>", $groupname));
			$tbl->endTableData();
		$tbl->endTableRow();

		#echo "<PRE>";
		#print_r($group);

		foreach ($group as $formitem) {
			$tbl->addTableRow();
				$tbl->addTableData(array("style" => "vertical-align: top;"));
					$tbl->addCode($formitem["field_name"]);
					$tbl->addSpace(2);
				$tbl->endTableData();
				$tbl->addTableData();
				switch ($formitem["field_type"]) {
					case "text":
					case "textarea":
						$tbl->addTextField(sprintf("data[%d]", $formitem["id"]), $formitem["field_value"], array(
							"style" => "text-align: left; width: 200px;"
						));
						break;
					case "select":
						$seltemp = explode("\n", $formitem["field_value"]);
						$sel     = array();
						foreach ($seltemp as $vv) {
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

	$output->endTag("form");
	$output->addTag("br");
	$output->insertTag("a", gettext("reset"), array(
		"href" => "javascript: document.getElementById('metaform').reset();"
	));
	$output->addCode(" | ");
	$output->insertTag("a", gettext("send")." &gt;&gt;", array(
		"href" => "javascript: document.getElementById('metaform').submit();"
	));
	echo $output->generate_output();

?>