<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

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
		$output->insertTag("a", gettext("function overview"), array(
			"href" => "javascript: templateSelectTab('help');"
		));
	$output->endTag("span");

	/* ------------- */
	/* site template */
	/* ------------- */

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("modify site template")
	));
	$pageid = $id;

	$cms_data = new Cms_data();
	$cms = $cms_data->getTemplateById($id);

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("number"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCode(($cms["id"]) ? $cms["id"]:"-");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("category"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				if (!$id) {
					$tbl->addSelectField("cms[category]", $cms_data->include_category, $cms["category"]);
				} else {
					if (in_array($cms["category"], array("js", "css"))) {
						$inc = array($cms["category"] => $cms_data->include_category[$cms["category"]]);
					} else {
						$inc = $cms_data->include_category;
						unset($inc["js"]);
						unset($inc["css"]);
					}
					$tbl->addSelectField("cms[category]", $inc, $cms["category"]);
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("description"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[title]", $cms["title"], array("style" => "width: 200px"));
				$tbl->addTag("span");
					$tbl->addSpace(5);
					$tbl->insertTag("b", gettext("Do NOT use the highlighter and the input screen at the same time. This will result in undefined results."));
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

		$venster->insertAction("back", gettext("back"), "?mod=cms&action=siteTemplates");
		$venster->insertAction("save", gettext("save"), "javascript: saveSettings();");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
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
		$venster->insertTag("b", gettext("hyperlink to selected file").": ");
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
				$tbl->insertTag("b", gettext("code to this css file").": ");
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
				$tbl->insertTag("b", gettext("code to this js file").": ");
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
				$tbl->insertTag("b", gettext("code as inline text").": ");
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
				$tbl->insertTag("b", gettext("code as inline executable code").": ");
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

	$view->addMapping( gettext("number"), "%id" );
	$view->addMapping( gettext("description"), "%title" );
	$view->addMapping( " ", "%%complex_actions" );

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "file_attach",
			"alt"  => gettext("select"),
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

	$functions = $cms_data->load_function_overview();
	$venster->addVensterData();
		$tbl = new Layout_table();
		if (!is_array($functions))
			$functions = array();

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