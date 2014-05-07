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

	$data = $this->queryDecodeMetadata($query);

	$tbl = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1
	));
	$tbl->addTableRow();
		$tbl->addTableData();
			$tbl->insertTag("b", gettext("field"), array(
				"class" => "meta_field"
			));
		$tbl->endTableData();
		$tbl->addTableData();
			$tbl->insertTag("b", gettext("contains value"), array(
				"class" => "meta_value"
			));
		$tbl->endTableData();
	$tbl->endTableRow();

	foreach ($data as $k=>$v) {
		if (is_array($v))
			$v = implode(" ".gettext("and")." ", $v);

		$field = $this->cms->getMetadataDefinitionById($k);

		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addCode($field["field_name"]);
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode($v);
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->insertTableData("&nbsp;", array("colspan" => "2"));
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => "2"));
			$tbl->insertAction("rss", gettext("rss feed"), "/rss/meta/$query");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
	echo $tbl->generate_output();
	echo "<br>";

	require(self::include_dir."showMetaResultsBase.php");

	$pages = array_slice($pages, 0, 1000, TRUE);

	/* debug - add 100 random pages */
	#$q = "select id from cms_data order by id limit 100";
	#$res = sql_query($q);
	#while ($row = sql_fetch_assoc($res)) {
	#	$pages[]=$row["id"];
	#}
	$num = count($pages);

	$pages = implode(",", $pages);

	if (!$pages)
		$pages = -1;

	/* get offset */
	$start = (int)$_REQUEST["start"]+1;

	$_data = array();
	$q = sprintf("select * from cms_data where id IN (%s) order by datePublication desc",
		$pages);
	$res = sql_query($q, "", $start-1, $this->pagesize);
	while ($row = sql_fetch_assoc($res)) {
		$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
		if ($row["pageAlias"])
			$row["pageAlias"].= ".htm";
		else
			$row["pageAlias"] = $row["id"].".htm";

		$_data[] = $row;
	}
	$view = new Layout_view(1);
	$view->addData($_data);

	$view->addMapping(gettext("page name"), "%%complex", "left");
	$view->addMapping(gettext("date"), "%datePublication_h", "left");
	$view->defineComplexMapping("complex", array(
		array(
			"type" => "link",
			"link" => array(($this->page_less_rewrites) ? "/":"/page/", "%pageAlias"),
			"text" => "%pageTitle"
		)
	));
	echo $view->generate_output(1);

	if ($num-1 > $this->pagesize) {
		echo "<br>";
		echo "<br>";
		$next_results = "javascript: document.getElementById('start').value = '%%'; document.getElementById('metaresults').submit();";
		$paging = new Layout_paging();
		$paging->setOptions($start-1, $num-1, $next_results, $this->pagesize, 1);
		echo $paging->generate_output();
		echo "<br>";
	}

	$output = new Layout_output();
	$output->addTag("form", array(
		"action" => "/metadata/",
		"method" => "get",
		"id"     => "metaresults"
	));
	$output->addHiddenField("mode", "metadata");
	$output->addHiddenField("start", $start);
	$output->addHiddenField("query", $query);

	$output->endTag("form");
	echo $output->generate_output();
?>
