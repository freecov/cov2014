<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
$addressdata = new Address_data();
$options = $addressdata->getExportInfo($_REQUEST["info"]);
if (!is_array($options)) {
	die("something went wrong, options is not an array");
}

/* start building output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	/* use a form */
	$output->addTag("form", array(
		"id"     => "addclamulti",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "savecla_multi");
	foreach ($options as $k=>$v) {
		$output->addHiddenField("options[$k]", $v);
	}
	/* window widget */
	$venster = new Layout_venster(array("title" => gettext("classifications")));
	$venster->addVensterData();
		/* use a table for layout */
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("controle"), "", "header");
			$table->addTableData("", "data");
				$table->addTag("div", array("id"=>"cla_check_layer", "style"=>"padding: 3px; font-weight: bold;"));
				$table->endTag("div");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("name"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("addcla[name]", $_REQUEST["addcla"]["name"]);
				$table->insertAction("ok", gettext("check"), "javascript: checkClaName();");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("", "", "header");
			$table->addTableData("", "data");
				$table->insertAction("cancel", gettext("back"), "javascript: window.close();");
				$table->addTag("span", array("id"=>"action_save", "style"=>"visibility: hidden;"));
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('addclamulti').submit();");
				$table->endTag("span");
			$table->endTableData();
		$table->endTableRow();
		/* end table and attach to window */
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	/* add window to output */
	$output->addCode($venster->generate_output());
	unset($venster);
	/* end form */
	$output->endTag("form");
	$output->load_javascript(self::include_dir."addclaMulti.js");
/* end buffer and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
