<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
if (!$_REQUEST["address_id"]) {
	die("no address id given");
}

$output = new Layout_output();
$output->layout_page("", 1);
	$venster = new Layout_venster(array("title" => gettext("zoeken")));
	$venster->addVensterData();
		$venster->addTag("form", array(
			"id"     => "relcardsearch",
			"method" => "get",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "address");
		$venster->addHiddenField("action", "relcardsearch");
		$venster->addHiddenField("address_id", $_REQUEST["address_id"]);
		$venster->addCode(gettext("zoekwoord"));
		$venster->addSpace(1);
		$venster->addTextField("searchkey", "", "", "", 1);
		$venster->addSpace(1);
		$venster->insertAction("forward", gettext("zoeken"), "javascript: document.getElementById('relcardsearch').submit();");
		$venster->endTag("form");
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
$output->layout_page_end();
$output->exit_buffer();
?>
