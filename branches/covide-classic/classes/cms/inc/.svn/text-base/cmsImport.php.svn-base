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

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Import non-oop Covide CMS 6.1")
	));

	$cms_data = new Cms_data();

	$venster->addVensterData();

		$tbl = new Layout_table(array(
			"cellspacing" => 1
		));
		/* siteroot */
		$roots[gettext("full migration")] = array(
			"O" => gettext("delete current data and use id insertion")
		);
		#$roots[gettext("import to default root")] = array(
		#	"R" => gettext("add to default site root")
		#);
		#$roots[gettext("import to user root")] = $cms_data->getUserSitemapRoots();

		$tbl->addTableRow();
			$tbl->insertTableData(gettext("use siteroot"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addSelectField("cms[siteroot]", $roots, array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* database server */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("database server"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[server]", "localhost", array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("database name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[database]", "", array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("mysql username"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[username]", "", array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("mysql password"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addPasswordField("cms[password]", "", array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("cmsfiles local path"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[filestore]", "/var/webapps/cmsfiles", array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );

		$venster->insertAction("back", gettext("back"), "javascript: window.close();");
		$venster->insertAction("last", gettext("new item"), "javascript: saveSettings();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "cmsImportExec");

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>