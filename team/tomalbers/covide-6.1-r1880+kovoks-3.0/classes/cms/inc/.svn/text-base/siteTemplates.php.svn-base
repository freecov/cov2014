<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("site templates")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getSiteTemplates();

	$venster->addVensterData();

	$view = new Layout_view();
	$view->addData($cms);

	$view->addMapping( gettext("nummer"), "%id" );
	$view->addMapping( gettext("categorie"), "%%complex_category" );
	$view->addMapping( gettext("omschrijving"), "%title" );
	$view->addMapping( " ", "%%complex_actions" );

	$view->defineComplexMapping("complex_category", array(
		array(
			"type" => "action",
			"src"  => "%category_h",
			"alt"  => "%category"
		),
		array(
			"text" => array(
				" [", "%category", "]"
			)
		)
	));
	$view->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"alt"  => gettext("bewerken"),
			"link" => array("?mod=cms&action=editTemplate&id=", "%id")
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("verwijderen"),
			"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteTemplate&id=", "%id';")
		)
	));
	$venster->addCode($view->generate_output());

	$venster->insertAction("new", gettext("nieuwe template"), "?mod=cms&action=editTemplate");
	$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addCode($venster->generate_output());

	$output->layout_page_end();
	$output->exit_buffer();

?>