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

	if ($id == "__search") {
		$keywords = $_REQUEST["keywords"];
	} else {
		$keywords = preg_replace("/\.htm$/si", "", $_REQUEST["page"]);
		$keywords = preg_replace("/[^a-z0-9]/si", " ", $keywords);
		$keywords = preg_replace("/ {1,}/s", " ", $keywords);
	}
	$pagesize = $this->pagesize;
	$start    = $_REQUEST["start"];
	if (!$start)
		$start = 1;
	else
		$start++;

	$fields = "pageData,pageTitle,pageLabel,pageAlias,search_title,search_fields,search_descr,keywords";

	$type  = "IN BOOLEAN MODE";      // for boolean operators
	$type2 = "WITH QUERY EXPANSION"; // for relevance

	/* replace all data var by pointers to new array */
	$keywords = stripslashes($keywords);
	preg_match_all("/\"[^\"]*?\"/si", $keywords, $matches);
	$matches = $matches[0];
	$matches = array_unique($matches);
	foreach ($matches as $k=>$v) {
		$keywords = str_replace($v, "##$k", $keywords);
		$matches[$k] = substr($v, 1, strlen($v)-2);
	}
	$keywords = explode(" ", $keywords);
	foreach ($keywords as $k=>$v) {
		if (preg_match("/^##\d{1,}/s", $v)) {
			$keywords[$k] = "\"*".$matches[str_replace("#", "", $v)]."*\"";
		} else {
			if (substr($v, 0, 1) != "-") {
				$keywords[$k] = preg_replace("/^(\+|\-){0,1}(.*)$/s", "$1*$2*", $v);
			} else {
				$keywords[$k] = $v;
			}
		}
	}
	$keywords = implode(" ", $keywords);

	$qc = sprintf("select count(*) from cms_data where apEnabled IN (%s) AND
		MATCH (pageData,pageTitle,pageLabel,pageAlias,search_title,search_fields,search_descr,keywords)
		AGAINST ('%s' $type) AND %s", implode(",", $this->public_roots), $keywords, $this->base_condition);
	$res = sql_query($qc);
	$count = sql_result($res,0);

	$q = sprintf("select id, pageTitle, datePublication,
		MATCH (%5\$s) AGAINST ('%1\$s' $type2) as relevance
		from cms_data where apEnabled IN (%6\$s) AND MATCH (%5\$s) AGAINST ('%1\$s' $type) AND %2\$s
		ORDER BY relevance DESC, datePublication DESC LIMIT %3\$d, %4\$d",
			$keywords, $this->base_condition, $start-1, $pagesize, $fields, implode(",", $this->public_roots));

	$qmax = sprintf("select MATCH (%3\$s)
		AGAINST ('%1\$s' $type2) as relevance
		from cms_data where apEnabled IN (%4\$s) AND MATCH (%3\$s) AGAINST ('%1\$s' $type) AND %2\$s
		ORDER BY relevance DESC, datePublication DESC LIMIT 1",
		$keywords, $this->base_condition, $fields, implode(",", $this->public_roots));
	$res = sql_query($qmax);
	if (sql_num_rows($res)==1)
		$max = (float)sql_result($res,0);
	else
		$max = 0;

	if (preg_match("/[^\*]/si", $keywords)) {

		if ($id == "__search") {
			$output = new Layout_output();
			$output->insertAction("search", gettext("Search results"), "");
			$output->addSpace();
			echo $output->generate_output();
			echo sprintf(gettext("Your search query for")." <a href='/search/?prev=".urlencode(stripslashes($_REQUEST["keywords"]))."'><b>".stripslashes($_REQUEST["keywords"])."</b></a> ".gettext("has")." %d ".gettext("results").":", $count);
			echo "<ul>";
			echo sprintf("<li>".gettext("you can also browse by <a href='/addressdata/'>address</a>")."</li>");
			echo sprintf("<li>".gettext("or search inside")." <a href='/filesearch/?prev=".urlencode(stripslashes($_REQUEST["keywords"]))."'>".gettext("public files")."</a></li>");
			echo sprintf("<li>".gettext("or search by <a href='/metadata/'>category or metadata</a>"));
			echo "</ul>";


		} else {
			$output = new Layout_output();
			$output->insertAction("delete", gettext("Not found"), "");
			$output->addSpace();
			echo $output->generate_output();
			echo sprintf(gettext("The requested page")." <b>".stripslashes($_REQUEST["page"])."</b> ".gettext("could not be found or is non-public").". ".gettext("Er zijn")." %d ".gettext("vergelijkbare pagina's gevonden").":<br><br>", $count);
		}

		$res = sql_query($q);
		echo "<a name='results'></a>";

		while ($row = sql_fetch_assoc($res)) {
			if ($max == 0) {
				$perc = 100;
			} else {
				$perc = $row["relevance"]/$max*100;
			}

			if ($perc == 100) {
				$perc = (int)$perc;
			} else {
				$perc = number_format($perc,1);
			}
			$row["perc"] = $perc."%";
			$row["alias"] = $this->checkAlias($row["id"]);
			$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
			$data[] = $row;

		}

		$view = new Layout_view(1);
		$view->setHtmlField("pageTitle");
		$view->addData($data);

		$view->addMapping(gettext("page name"), "%%complex", "left");
		$view->addMapping(gettext("date"), "%datePublication_h", "left", "nowrap");
		$view->addMapping(gettext("relevance"), "%perc", "right", "nowrap");
		$view->defineComplexMapping("complex", array(
			array(
				"type" => "link",
				"link" => array("/page/", "%alias"),
				"text" => "%pageTitle"
			)
		));
		echo $view->generate_output(1);

		$next_results = stripslashes(($id == "__search") ? "/search/?keywords=".urlencode($_REQUEST["keywords"]):"/page/".$_REQUEST["page"])."&start=%%#results";
		echo "<br>";

		if ($count > $pagesize) {
			$paging = new Layout_paging();
			$paging->setOptions($start-1, $count, $next_results, 20, 1);
			echo $paging->generate_output();
			echo "<br>";
		}
	} else {
		echo "<form method='get' id='searchfrm' action='/search/' style='display:inline'>";
		echo gettext("enter your search keywords: ").sprintf("<input type='text' name='keywords' style='width: 200px;' value='%s'>", $_REQUEST["prev"]);
		echo $this->insertAction("forward", gettext("search"), "javascript: document.getElementById('searchfrm').submit();");
		echo "</form>";
		echo "<br><br>";

		$cms_license = $this->cms->getCmsSettings();
		if ($cms_license["cms_address"]) {
			echo $this->insertAction("addressbook", "", "");
			echo sprintf(" <a href='/addressdata/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search by address"));
		}
		if ($cms_license["cms_meta"]) {
			echo $this->insertAction("view_all", "", "");
			echo sprintf(" <a href='/metadata/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search by category or metadata"));
		}
		echo $this->insertAction("ftype_text", "", "");
		echo sprintf(" <a href='/filesearch/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search inside files"));
		echo "<br>";
	}

	if ($id == "__err404") {
		echo "<br>".gettext("to homepage").": <a href=\"/\">";
		$this->getPageTitle($this->default_page, -1);
		echo "</a><br><br>";
	}

?>
