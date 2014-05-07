<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);
	$output->load_javascript(self::include_dir."script_cms.js");

	/* ------------ */
	/* tab switcher */
	/* ------------ */
	$output->addTag("span", array(
		"id"    => "span_template",
		"style" => "border: 1px solid #666; width: 150px; padding: 5px; margin: 1px; background-color: #ffebb0;"
	));
		$output->insertTag("a", gettext("site template"), array(
			"href" => "javascript: templateSelectTab('template');"
		));
	$output->endTag("span");
	$output->addTag("span", array(
		"id"    => "span_media",
		"style" => "border: 1px solid #666; width: 150px; padding: 5px; margin: 1px;"
	));
		$output->insertTag("a", gettext("media gallery"), array(
			"href" => "javascript: templateSelectTab('media');"
		));
	$output->endTag("span");
	$output->addTag("span", array(
		"id"    => "span_includes",
		"style" => "border: 1px solid #666; width: 150px; padding: 5px; margin: 1px;"
	));
		$output->insertTag("a", gettext("site includes"), array(
			"href" => "javascript: templateSelectTab('includes');"
		));
	$output->endTag("span");
	$output->addTag("span", array(
		"id"    => "span_help",
		"style" => "border: 1px solid #666; width: 150px; padding: 5px; margin: 1px;"
	));
		$output->insertTag("a", gettext("functie overzicht"), array(
			"href" => "javascript: templateSelectTab('help');"
		));
	$output->endTag("span");

	/* ------------- */
	/* site template */
	/* ------------- */

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("site template bewerken")
	));
	$pageid = $id;

	$cms_data = new Cms_data();
	$cms = $cms_data->getTemplateById($id);

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("nummer"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(($cms["id"]) ? $cms["id"]:"-");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("categorie"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("cms[category]", $cms_data->include_category, $cms["category"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("omschrijving"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[title]", $cms["title"], array("style" => "width: 200px"));
				$tbl->addTag("span");
					$tbl->addSpace(5);
					$tbl->insertTag("b", gettext("Gebruik NIET de highlighter en het scherm hieronder tegelijk. Dit kan ongewenste resultaten geven."));
				$tbl->endTag("span");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("template"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[data]", $cms["data"], array(
					"style"   => "width: 780px; height: 450px; font-family: courier new; font-size:12px;",
					"wysiwyg" => "true",
					"wrap"    => "off"
				));
				$tbl->insertAction("view_all", gettext("highlight source"), "javascript: popup('?mod=cms&action=highlight_init', 'highlighter', 900, 600, 1);");
				$tbl->addTag("br");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();


		$venster->addCode($tbl->generate_output());

		$venster->insertAction("back", gettext("terug"), "?mod=cms&action=siteTemplates");
		$venster->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveTemplate");
	$output->addHiddenField("id", $pageid);

	$output->insertTag("div", $venster->generate_output(), array(
	 	"id" => "tab_template",
	 	"style" => "display: block"
	));

	/* ------------- */
	/* media gallery */
	/* ------------- */

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Media gallery")
	));

	$venster->addVensterData();
		$venster->start_javascript();
			$venster->addCode("
				function onPreview() {
					document.getElementById('f_image').innerHTML = document.getElementById('f_url').value.replace(/^.*(\/cmsfile\/)/g, '$1');
				}
			");
		$venster->end_javascript();
		$venster->insertTag("b", gettext("link naar het geselecteerde bestand").": ");
		$venster->addHiddenField("f_url", "");
		$venster->insertTag("span", "", array(
			"id" => "f_image",
			"style" => "text-decoration: underline;"
		));
		$venster->addTag("br");
		$venster->addTag("br");
		$venster->addTag("iframe", array(
				"id"  => "mediaframe",
				"src" => "?mod=cms&action=media_gallery&ftype=cmsfile",
				"style" => "width: 880px; height: 515px; border: 1px solid #666;"
			));
		$venster->endTag("iframe");
	$venster->endVensterData();
	$output->insertTag("div", $venster->generate_output(), array(
	 	"id" => "tab_media",
	 	"style" => "display: none"
	));

	/* ------------- */
	/* site includes */
	/* ------------- */

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Site includes")
	));

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "data");
				$tbl->insertTag("b", gettext("code naar dit css bestand").": ");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertTag("span", "", array(
					"id" => "css_url",
					"style" => "text-decoration: underline;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "data");
				$tbl->insertTag("b", gettext("code naar dit js bestand").": ");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertTag("span", "", array(
					"id" => "js_url",
					"style" => "text-decoration: underline;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "data");
				$tbl->insertTag("b", gettext("code als inline text").": ");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertTag("span", "", array(
					"id" => "inl_url",
					"style" => "text-decoration: underline;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "data");
				$tbl->insertTag("b", gettext("code als inline uitvoerbare code").": ");
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertTag("span", "", array(
					"id" => "exec_url",
					"style" => "text-decoration: underline;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
	$venster->addCode($tbl->generate_output());
	$venster->addTag("br");
	$cms_data = new Cms_data();
	$cms = $cms_data->getSiteTemplates();

	$view = new Layout_view();
	$view->addData($cms);

	$view->addMapping( gettext("nummer"), "%id" );
	$view->addMapping( gettext("omschrijving"), "%title" );
	$view->addMapping( " ", "%%complex_actions" );

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "file_attach",
			"alt"  => gettext("selecteren"),
			"link" => array("javascript: document.getElementById('css_url').innerHTML = '\$template->load_css(", "%id", ");'; document.getElementById('js_url').innerHTML = '\$template->load_js(", "%id", ");'; document.getElementById('inl_url').innerHTML = '\$template->load_inline(", "%id", ");'; document.getElementById('exec_url').innerHTML = '\$template->exec_inline(", "%id", ");'; void(0);")
		)
	));
	$venster->addCode($view->generate_output());

	$venster->endVensterData();

	$output->insertTag("div", $venster->generate_output(), array(
	 	"id" => "tab_includes",
	 	"style" => "display: none"
	));

	/* ------------ */
	/*    cms help  */
	/* ------------ */
	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Help")
	));

	$functions = array(
		"\$template->start_html(enable_headers);" => gettext("begin een html pagina. enable_headers staat standaard uit."),
		"\$template->end_html();"                 => gettext("einde van de html pagina"),
		"\$template->load_css(include);"          => gettext("laadt een extern css bestand na, include staat hierbij voor het include nummer."),
		"\$template->load_js(include);"           => gettext("laadt een extern javascript bestand na"),
		"\$template->load_inline(include);"       => gettext("laadt een include inline in dit bestand op de aangegeven plek")
	);
	$venster->addVensterData();
		$tbl = new Layout_table();
		foreach ($functions as $func=>$desc) {
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addCode($func);
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->addCode($desc);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->endTable();
	$venster->addCode($tbl->generate_output());

	$venster->endVensterData();
	$output->insertTag("div", $venster->generate_output(), array(
	 	"id" => "tab_help",
	 	"style" => "display: none"
	));

	/* ------------ */
	/* some scripts */
	/* ------------ */


	$output->endTag("form");
	$output->load_javascript("classes/html/inc/tabs.js");
	$output->insertTag("a", "", array("name" => "end"));

	$output->layout_page_end();
	$output->exit_buffer();

?>