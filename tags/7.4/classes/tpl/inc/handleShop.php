<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}


	$pageid =& $id;
	if (!$id) $id = $this->pageid;

	$row = $this->page_cache[$id];

	$output = new Layout_output();
	$output->addTag("form", array(
		"name"   => "shopfrm",
		"id"     => "shopfrm",
		"method" => "get",
		"action" => "site.php"
	));

	$tbl = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1,
		"class"       => "view_header table_data"
	));
	$tbl->addTableRow();
		$tbl->addTableHeader(array(
			"colspan" => 2,
			"class" => "list_header_center",
			"style" => "text-align: left"
		), "header");
			$tbl->insertAction("shop", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("Add to shopping cart"));
		$tbl->endTableHeader();
	$tbl->endTableRow();
	$tbl->addTableRow(array("class" => "list_record"));
		$tbl->addTableData();
			$tbl->addHiddenField("shopid", $this->pageid);
			$tbl->addTextField("shopcount", 1, array("style" => "width: 30px;"));
			$tbl->addCode(sprintf(" x %s %s", $this->valuta, $row["shopPrice"]));
			$tbl->insertAction("forward", gettext("add to shopping cart"), sprintf("javascript: shopAdd('%d');", $this->pageid));
			$tbl->addTag("br");
			$tbl->addTag("br");
			$tbl->insertTag("a", gettext("go to my shopping cart"), array(
				"href" => "/mode/shopcontents"
			));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$output->addCode($tbl->generate_output());
	$output->endTag("form");
	$data.= $output->generate_output();
?>