<?php
if (!class_exists("Classification_output")) {
	die("no class definition found");
}
/* get classifications from db */
$classification_data = new Classification_data();
$classifications = $classification_data->getClassifications();

/* init the output */
$output = new Layout_output();
$output->layout_page(gettext("classificaties")." ".gettext("overzicht"));
/*
$output->addCode(
	gettext("U beheert hier classificatiemogelijkheden voor relaties in het adresboek en tevens die van de BusinessCards (onderdeel van de relatiekaart)")
);
*/
/* make array with possible vars, for form generation */
$formitems = array(
	"mod"    => "classification",
	"action" => "show_classifications",
	"id"     => ""
);



	/* generate nice window */
	$venster = new Layout_venster(array(
		"title"    => gettext("classificaties"),
		"subtitle" => gettext("overzicht")
	));
	/* menu items */
	$venster->addMenuItem(gettext("nieuw"), "javascript: cla_edit(0);");
	$venster->generateMenuItems();
	$venster->addVensterData();
		/* view object for the data */
		$view = new Layout_view();
		$view->addData($classifications);
		$view->addMapping(gettext("classificatie"), "%description");
		$view->addMapping(gettext("actief"), "%%complex_active");
		$view->addMapping(gettext("soort"), "%%complex_subtype");
		$view->addMapping(gettext("edit"), "%%complex_actions");
		/* define the complex mappings */
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "image",
				"src"   => "f_oud.gif",
				"alt"   => gettext("actief")
			)
		));
		$view->defineComplexMapping("complex_subtype", array(
			array(
				"type"  => "action",
				"src"   => "state_special",
				"check" => "%h_subtype"

			),
			array(
				"type"  => "text",
				"text"  => "%h_subtype"
			)
		));
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"  => "action",
				"src"   => "edit",
				"alt"   => gettext("wijzigen"),
				"link"  => array("javascript: cla_edit(", "%id", ")")
			),
			array(
				"type"  => "action",
				"src"   => "delete",
				"alt"   => gettext("verwijderen"),
				"link"  => array("javascript: cla_remove(", "%id", ")")
			)
		));

		/* output the view */
		$venster->addCode($view->generate_output());
	$venster->endVensterData();
	/* end window */
$output->addTag("form", array(
	"id"     => "claform",
	"method" => "get",
	"action" => "index.php"
));
foreach ($formitems as $item=>$value) {
	$output->addHiddenField($item, $value);
}
$output->addCode( $venster->generate_output() );
$output->endTag("form");

$output->load_javascript(self::include_dir."classification_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
