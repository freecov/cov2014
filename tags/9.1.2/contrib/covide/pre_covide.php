<?php
	if ($_REQUEST["mod"] == "user" && $_REQUEST["action"] == "help") {
		$output = new Layout_output();
		$output->layout_page("help", 1);
			/* window object */
			$venster = new Layout_venster(array("title" => gettext("help information")));
			$venster->addMenuItem(gettext("close"), "javascript: window.close();");
			$venster->generateMenuItems();
			$venster->addVensterData();
				$venster->addCode(sprintf("
					Covide helpt u uw organisatie te organiseren, daardoor een hogere productiviteit te bereiken onder andere door betere samenwerking.
					Om u te helpen om ook zo efficient mogelijk gebruik te maken van Covide bieden we u:"));

				$venster->addTag("br");

				$venster->addTag("ol");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/Support.htm' target='_blank'>support pagina</a> op Covide.nl");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/Demofilmstartpagina.htm' target='_blank'>filmpjes</a> op Covide.nl");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/FAQ.htm' target='_blank'>FAQ</a> op Covide.nl");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/savefile/1533/PDFmap%20nieuwe%20site/Covide-Handleiding.pdf' target='_blank'>handleiding</a> op Covide.nl");
				$venster->endTag("ol");
				#$venster->start_javascript();
				#	$venster->addCode("window.resizeTo(900, 600);");
				#$venster->end_javascript();

			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}
?>
