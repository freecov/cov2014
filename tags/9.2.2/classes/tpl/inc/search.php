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

	/* do we have a search request or an 404 request */
	if ($id == "__search") {
		/* search */
		$keywords = $_REQUEST["keywords"];
	} else {
		/* 404 not found */
		$keywords = preg_replace("/\.htm$/si", "", $_REQUEST["page"]);
		$keywords = preg_replace("/[^a-z0-9]/si", " ", $keywords);
		$keywords = preg_replace("/ {1,}/s", " ", $keywords);
	}

	/* if keywords are shorten than 4 characters, fulltext search is useless */
	if (mb_strlen($keywords) < 4)
		$this->enable_ft_search = 0;

	/* get the pagesize and start offset */
	$pagesize = $this->pagesize;
	$start    = $_REQUEST["start"];
	if (!$start)
		$start = 1;
	else
		$start++;

	/* define the search fields inside cms_data */
	$fields = array(
		"pageData",
		"pageTitle",
		"pageLabel",
		"pageAlias",
		"pageHeader",
		"search_title",
		"search_fields",
		"search_descr",
		"keywords"
	);

	/* do we need a reindex? */
	$reindex  = 0;
	$ft_cols  = array();
	$ft_index = array();

	$this->enable_ft_search = 0;

	/* set each field to 0 */
	foreach ($fields as $f) {
		$ft_cols[$f] = 0;
	}
	/* now check against indexes */
	$q = "show index from cms_data";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		if ($row["Index_type"] == "FULLTEXT") {
			$ft_cols[$row["Column_name"]] = 1;
			$ft_index[$row["Key_name"]] = 1;
		}
	}
	/* do we need to drop indexes */
	if (count($ft_index) != 1 || in_array(0, $ft_cols)) {
		$reindex = 1;
		foreach ($ft_index as $fti=>$v) {
			$q = sprintf("alter table `cms_data` drop index `%s`", $fti);
			sql_query($q);
		}
	}
	/* do we need to create a new index? */
	if ($reindex) {
		$q = sprintf("alter table `cms_data` add fulltext `ft_cms_data` (`%s`)",
			implode("`,`", $fields));
		#sql_query($q);
	}

	/* now do the same for the metadata table */
	$reindex = 1;
	$q = "show index from cms_metadata";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		if ($row["Index_type"] == "FULLTEXT" && $row["Column_name"] = "value")
			$reindex = 0;
	}
	/* do we need to create a new index on metadata? */
	if ($reindex) {
		$q = "alter table `cms_metadata` add fulltext `ft_meta_data` (`value`)";
		#sql_query($q);
	}

	/* if fulltext search is enabled (default) */

	/* combine to search types */
	$type  = "IN BOOLEAN MODE";      // for boolean operators
	$type2 = "WITH QUERY EXPANSION"; // for relevance

	/* replace all data var by pointers to new array */
	$keywords = trim(stripslashes($keywords));
	$keywords_plain = $keywords;

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
			$keywords[$k] = "\"".$matches[str_replace("#", "", $v)]."*\"";
		} else {
			#if (!preg_match("/^(\+|\-|\~)/s", $v))
			#	$v = sprintf("+%s", $v);

			if (substr($v, 0, 1) != "-") {
				$keywords[$k] = preg_replace("/^(\+|\-){0,1}(.*)$/s", "$1$2*", $v);
			} else {
				$keywords[$k] = $v;
			}
		}
	}
	/* we have a keyword list! */
	$keywords = implode(" ", $keywords);

	$cms["match"] = sprintf(" MATCH (`%s`) AGAINST ", implode("`,`", $fields));
	$cms["base"]  = sprintf(" %s AND apEnabled IN (%s) ", $this->base_condition,
		implode(",", $this->public_roots));

	/* include meta fields in ft mode */
	$cms["meta"]  = sprintf(" OR id IN (select pageid from cms_metadata
		where MATCH (`value`) AGAINST ('%s' %s) group by pageid)",
		$keywords, $type);

	/* include meta fields in non-ft mode */
	// but only if we have results
	$c_metacount = 0;
	$q_metacount = sprintf("SELECT COUNT(*) FROM cms_metadata WHERE `value` LIKE '%%%s%%'", sql_escape_string($keywords_plain));
	$r_metacount = sql_query($q_metacount);
	$c_metacount = sql_result($r_metacount, 0);
	if ($c_metacount > 0) {
		$cms["meta_plain"]  = sprintf(" OR id IN (select pageid from cms_metadata
			where `value` like '%%%s%%' group by pageid)",
			sql_escape_string($keywords_plain));
	} else {
		$cms["meta_plain"] = "";
	}

	/* the count query */
	if ($this->enable_ft_search) {
		$qc = sprintf("select count(*) from cms_data where %s and (%s ('%s' %s) %s)",
			$cms["base"], $cms["match"], sql_escape_string($keywords), $type, $cms["meta"]);
	} else {
		$qc = sprintf("select count(*) from cms_data where %s and (%s %s)",
			$cms["base"], $this->alternativeSearch($fields, sql_escape_string($keywords_plain)), $cms["meta_plain"]);
	}

	/* the count query */
	$res = sql_query($qc);
	$count = sql_result($res,0);

	/* the boolean search query (case insensitive) */
	if ($this->enable_ft_search) {
		$q = sprintf("select id, pageTitle, pageHeader, datePublication,
			%1\$s ('%2\$s' %3\$s)	as relevance
			from cms_data where %4\$s and (%1\$s ('%2\$s' %7\$s) %8\$s)
			order by relevance desc, datePublication desc limit %5\$d, %6\$d",
			$cms["match"],
			$keywords,
			$type2,
			$cms["base"],
			$start-1,
			$pagesize,
			$type,
			$cms["meta"]
		);
	} else {
		$q = sprintf("select id, pageTitle, pageHeader, datePublication
			from cms_data where %s and (%s %s)
			order by pageTitle, datePublication desc limit %d, %d",
			$cms["base"],
			$this->alternativeSearch($fields, sql_escape_string($keywords_plain)),
			$cms["meta_plain"],
			$start-1,
			$pagesize
		);
	}

	/* the relevance query with query expansion */
	if ($this->enable_ft_search) {
		$qmax = sprintf("select %1\$s ('%2\$s' %3\$s) as relevance
			from cms_data where %4\$s and %1\$s ('%2\$s' %5\$s)
			order by relevance desc, datePublication desc limit 1",
			$cms["match"],
			$keywords,
			$type2,
			$cms["base"],
			$type
		);
		$res = sql_query($qmax);
		if (sql_num_rows($res)==1)
			$max = (float)sql_result($res,0);
		else
			$max = 0;
	} else {
		$max = 0;
	}

	if (preg_match("/[^\*]/si", $keywords)) {
		if ($id == "__search") {
			$output = new Layout_output();
			$output->insertAction("search", gettext("Search results"), "");
			$output->addSpace();
			echo $output->generate_output();
			echo sprintf(gettext("Your search query for")." <a href='/search/?prev=".str_replace("%", "%%", urlencode(stripslashes($_REQUEST["keywords"])))."'><b>".stripslashes($_REQUEST["keywords"])."</b></a> ".gettext("has")." %d ".gettext("results").":", $count);

			echo "<ul id=\"extra_search_options\">";
			$cms_license = $this->cms_license;
			if ($cms_license["cms_address"])
				echo sprintf("<li>".gettext("browse by <a href='/addressdata/'>address</a>")."</li>");

			if (!$this->disableFileSearch)
				echo sprintf("<li>".gettext("search inside files")." <a href='/filesearch/?prev=".str_replace("%", "%%", urlencode(stripslashes($_REQUEST["keywords"])))."'>".gettext("public files")."</a></li>");

			if ($cms_license["cms_meta"])
				echo sprintf("<li>".gettext("search by <a href='/metadata/'>category or metadata</a>"));

			echo "</ul>";


		} else {
			if ($custom) {
				$this->getPageData($custom);
			} else {
				$output = new Layout_output();
				$output->insertAction("delete", gettext("Not found"), "");
				$output->addSpace();
				echo $output->generate_output();
				echo sprintf(gettext("The requested page")." <b>".stripslashes($_REQUEST["page"])."</b> ".gettext("could not be found or is non-public").". ".gettext("Er zijn")." %d ".gettext("vergelijkbare pagina's gevonden").":<br><br>", $count);
			}
		}

		$res = sql_query($q);
		echo "<a name=\"results\" class=\"anchor\"></a>";

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
			$row["pageHeader"] = $this->limit_string(
				trim(preg_replace("/((\t)|(\n)|(\r))/s", " ", $row["pageHeader"]))
			, 150, 0);
			$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
			$row["thumb"] = trim($this->getPageThumb($row["id"], 100, 50));
			$data[] = $row;

		}

		$view = new Layout_view(1);
		$view->setHtmlField("pageTitle");
		$view->addData($data);

		$view->addMapping("", "%%complex_image", "left");
		$view->addMapping(gettext("page name"), "%%complex", "left");
		$view->addMapping(gettext("date"), "%datePublication_h", "left", "nowrap");
		if ($this->enable_ft_search)
			$view->addMapping(gettext("relevance"), "%perc", "right", "nowrap");

		$view->defineComplexMapping("complex_image", array(
			array(
				"type" => "text",
				"text" => "%thumb"
			)
		));
		if ($this->page_less_rewrites)
			$p = "/";
		else
			$p = "/page/";
		
		
		$view->setHtmlField("thumb");
		$view->defineComplexMapping("complex", array(
			array(
				"type" => "link",
				"link" => array($p, "%alias"),
				"text" => "%pageTitle"
			),
			array(
				"text" => array("\n", "%pageHeader")
			)
		));
		echo $view->generate_output(1);

		$next_results = stripslashes(($id == "__search") ? "/search/?keywords=".urlencode($_REQUEST["keywords"]):$p.$_REQUEST["page"])."&start=%%#results";
		echo "<br>";

		if ($count > $pagesize) {
			$paging = new Layout_paging();
			$paging->setOptions($start-1, $count, $next_results, 20, 1);
			echo $paging->generate_output();
			echo "<br>";
		}
	} else {
		echo "<form method='get' id='searchfrm' action='/search/' style='display:inline'>";
		echo gettext("enter your search keywords: ").sprintf("<input type='text' class='inputtext' name='keywords' style='width: 200px;' value='%s'>", $_REQUEST["prev"]);
		echo $this->insertAction("forward", gettext("search"), "javascript: document.getElementById('searchfrm').submit();");
		echo "</form>";
		echo "<br><br>";

		$cms_license = $this->cms_license;
		if ($cms_license["cms_address"]) {
			echo $this->insertAction("addressbook", "", "");
			echo sprintf(" <a href='/addressdata/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search by address"));
		}
		if ($cms_license["cms_meta"]) {
			echo $this->insertAction("view_all", "", "");
			echo sprintf(" <a href='/metadata/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search by category or metadata"));
		}
		if (!$this->disableFileSearch) {
			echo $this->insertAction("ftype_text", "", "");
			echo sprintf(" <a href='/filesearch/'><i>%s</i> %s</a><br><br>", gettext("or"), gettext("search inside files"));
		}
		echo "<br>";
	}

	if ($id == "__err404" || $id == '_err410') {
		echo "<br>".gettext("to homepage").": <a href=\"/\">";
		$this->getPageTitle($this->default_page, -1);
		echo "</a><br><br>";
	}

?>
