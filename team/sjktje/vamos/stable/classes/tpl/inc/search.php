<?php
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


	if ($id == "__search") {
		echo sprintf("Uw zoekopdracht naar <b>".stripslashes($_REQUEST["keywords"])."</b> leverde %d resultaten op:<br><br>", $count);
	} else {
		echo sprintf("De opgevraagde pagina <b>".stripslashes($_REQUEST["page"])."</b> kon niet worden gevonden of is niet publiek toegankelijk. Er zijn %d vergelijkbare pagina's gevonden:<br><br>", $count);
	}

	$res = sql_query($q);
	echo "<a name='results'></a>";
	echo "<table>";
	echo "<tr>";
		echo "<td><b>titel</b></td>";
		echo "<td><b>datum</b></td>";
		echo "<td><b>relevantie</b></td>";
	echo "</tr>";
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

		echo "<tr>";
			echo "<td><a href='/page/".$this->checkAlias($row["id"])."'>".$row["pageTitle"]."</td>";
			echo "<td>".date("d-m-Y", $row["datePublication"])."</td>";
			echo "<td style='text-align: right;'>".$perc."%</td>";
		echo "</tr>";
	}
	echo "</table>";

	$next_results = stripslashes(($id == "__search") ? "/search/?keywords=".$_REQUEST["keywords"]:"/page/".$_REQUEST["page"])."&start=%%#results";
	echo "<br>";

	if ($count > $pagesize) {
		$paging = new Layout_paging();
		$paging->setOptions($start-1, $count, $next_results, 20, 1);
		echo $paging->generate_output();
		echo "<br>";
	}

	if ($id == "__err404") {
		echo "<br>naar de startpagina: <a href=\"/\">";
		$this->getPageTitle($this->default_page, -1);
		echo "</a><br><br>";
	}

?>