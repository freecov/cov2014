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

	$cms_data = new Cms_data();

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("cms sitemap index validation")
	));
	$venster->addVensterData();
		$uri = sprintf("http://%s/sitemap.xml", $cms_data->getHostnameByPage($sitemap));
		$venster->insertTag("div",
			$cms_data->validateSitemap($uri, "siteindex"),
			array("style" => "background-color: white"));

	$venster->endVensterData();
	$output->addCode($venster->generate_output());

	$fc = file_get_contents($uri);
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $fc, $vals, $index);
	xml_parser_free($parser);
	foreach ($vals as $v) {
		if ($v["tag"] == "LOC") {
			$i++;
			$uri = sprintf("http://%s/sitemap.%d.xml.text",
				$cms_data->getHostnameByPage($sitemap), $i);

			$venster = new Layout_venster(array(
				"title" => gettext("CMS"),
				"subtitle" => gettext("cms sitemap validation").": ".gettext("file")." ".$i
			));
			$venster->addVensterData();
				$venster->insertTag("div",
					$cms_data->validateSitemap($uri, "sitemap"),
						array("style" => "background-color: white"));

			$venster->endVensterData();
			$output->addCode($venster->generate_output());

		}
	}



	$output->layout_page_end();
	$output->exit_buffer();

?>