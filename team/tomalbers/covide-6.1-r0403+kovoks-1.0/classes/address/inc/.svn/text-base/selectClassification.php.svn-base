<?php
if (!class_exists("Address_output")) {
	die("no class definition found");
}
$output = new Layout_output();
$output->layout_page("classifications");

$settings = array(
	"title"    => gettext("Adresboek"),
	"subtitle" => gettext("kies de classificatie(s)")
);

$output->addTag("form", array(
	"id"     => "velden",
	"action" => "index.php",
	"method" => "post"
));
$output->addHiddenField("mod", "address");

$classification = new Classification_output();

$output_alt = new Layout_output();
$output_alt->insertAction("back", gettext("terug"), "?mod=address");
$output_alt->insertAction("forward", gettext("verder"), "javascript: step_next();");

$venster = new Layout_venster($settings, 1);
$venster->addVensterData();
	$venster->addCode( $classification->select_classification("", $output_alt->generate_output() ) );
$venster->endVensterData();

$placeholder = new Layout_table();
$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

$output->start_javascript();
$output->addCode("
	function step_next() {
		document.getElementById('velden').submit();
	}
");
$output->end_javascript();

$output->layout_page_end();
$output->exit_buffer();
?>
