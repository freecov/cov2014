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
					Covide Virtueel Kantoor kan naast een grote klanttevredenheid ook veel
					efficiencywinst opleveren, wanneer gebruikers er op de best mogelijke
					manier mee omgaan.<br>
					Daarom is een goede training een prima investering.<br><br>
					In Nederland biedt %1\$sCovide%3\$s zelf trainingen op maat, terwijl
					%2\$sAT Computing%3\$s standaard trainingen verzorgt.",
					"<a href='http://www.atcomputing.nl/Training/covide.html' target='_blank'>",
					"<a href='http://www.covide.nl/page/444.htm' target='_blank'>",
					"</a>"));

				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode("Er is (nog) geen goede online handleiding beschikbaar is.
					In plaats daarvan bieden we u als gebruiker de volgende help-mogelijkheden:");
				$venster->addTag("br");

				$venster->addTag("ol");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/ScreendumpsCovideCRM-Groupware.htm' target='_blank'>demo schermen</a> op Covide.nl");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/screencasting_demo.htm' target='_blank'>filmpjes</a> op Covide.nl (nog in bewerking)");
					$venster->insertTag("li", "de <a href='http://www.covide.nl/page/faq-pagina.htm' target='_blank'>FAQ</a> op Covide.nl");
					$venster->insertTag("li", "het <a href='http://www.covide.nl/page/supportformulier.htm' target='_blank'>supportformulier</a> op Covide.nl");
					$venster->insertTag("li", "het <a href='http://sourceforge.net/forum/forum.php?forum_id=590728' target='_blank'>forum</a> op SourceForge.net");

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