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

	$keywords = $_REQUEST["keywords"];
	$pagesize = $this->pagesize;

	if (!$keywords) {
		echo "<form method='get' id='searchfrm' action='/filesearch/' style='display:inline'>";
		echo gettext("enter your search keywords: ").sprintf("<input type='text' name='keywords' style='width: 200px;' value='%s'>", $_REQUEST["prev"]);
		echo $this->insertAction("forward", gettext("search"), "javascript: document.getElementById('searchfrm').submit();");
		echo "</form>";
		echo "<br><br>";

		$cms_license = $this->cms->getCmsSettings();
		if ($cms_license["cms_address"]) {
			echo $this->insertAction("addressbook", "", "");
			echo sprintf(" <a href='/addressdata/'><i>%s</i> %s</a><br><br>", gettext("or"), "search by address");
		}
		if ($cms_license["cms_meta"]) {
			echo $this->insertAction("view_all", "", "");
			echo sprintf(" <a href='/metadata/'><i>%s</i> %s</a><br><br>", gettext("or"), "search by category or metadata");
		}
		echo $this->insertAction("search", "", "");
		echo sprintf(" <a href='/search/'><i>%s</i> %s</a><br><br>", gettext("or"), "search inside pages");
		echo "<br>";

	} else {

		$key = sprintf("availfiles_%s", md5(implode(",", $this->public_roots)));
		$fetch = $this->getApcCache($key);
		if ($fetch) {
			$filelist = $fetch;
		} else {
			/* get list of public pages */
			$q = sprintf("select id, pageData from cms_data where %s and apEnabled IN (%s)",
				$this->base_condition, implode(",", $this->public_roots));
			$res = sql_query($q);
			$filelist = array();

			$combined_data = "";
			while ($row = sql_fetch_assoc($res)) {
				$combined_data .= $row["pageData"];
			}
			$files = $this->get_urls($combined_data);
			foreach ($files as $group) {
				foreach ($group as $f) {
					if (preg_match("/\/cmsfile\/\d{1,}$/si", $f))
						$filelist[] = (int)basename($f);
				}
			}
			$this->setApcCache($key, $filelist);
		}

		/* exec index query */
		$index_data = new Index_data();

		$param["search"]["phrase"]    = $keywords;
		$param["search"]["binfile"]   = 1;
		$param["search"]["websearch"] = 1;
		$param["search"]["and"]       = 1;

		$key = sprintf("searchfiles_%s", md5(implode(",", $this->public_roots).$keywords));
		$fetch = $this->getApcCache($key);
		if ($fetch) {
			$results = $fetch;
		} else {
			$results["binfile"] = $index_data->execSearchCommand($param);

			$fs_data = new Filesys_data();
			$results["filesys"] = $fs_data->searchWebFiles($keywords);

			/* combine results */
			foreach ($results["binfile"] as $v) {
				foreach ($v as $f)
					$results["filesys"][$f] = $f;
			}
			unset($results["binfile"]);

			foreach ($results["filesys"] as $k=>$v) {
				if (!in_array($k, $filelist))
					unset($results["filesys"][$k]);
				else
					$results["filesys"][$k] = $k;
			}
			$this->setApcCache($key, $results);
		}

		$count = count($results["filesys"]);
		$results["filesys"] = array_slice($results["filesys"], (int)$_REQUEST["start"], $pagesize, true);

		$output = new Layout_output();
		$output->insertAction("search", gettext("File results"), "");
		$output->addSpace();
		echo $output->generate_output();
		echo sprintf("Your file search for <a href='/filesearch/?prev=".urlencode(stripslashes($_REQUEST["keywords"]))."'><b>".stripslashes($_REQUEST["keywords"])."</b></a> has %d results:", $count);
		echo "<ul>";
		echo sprintf("<li>you can also browse by <a href='/addressdata/'>address</a></li>");
		echo sprintf("<li>or search for <a href='/search/?prev=".urlencode(stripslashes($_REQUEST["keywords"]))."'>pages</a></li>");
		echo sprintf("<li>or search by <a href='/metadata/'>category or metadata</a>");
		echo "</ul>";


		$data = array();
		foreach ($results["filesys"] as $k=>$v) {
			$row =& $data[$k];
			$row = $fs_data->getFileById($k, 1);
			if (!$row["name"]) $row["name"] = $k;
			$row["size"]      = $fs_data->parseSize($row["size"]);
			$row["timestamp"] = date("d-m-Y H:i", $row["timestamp"]);
			$row["fileicon"]  = $fs_data->getFileType($row["name"]);
		}

		$view = new Layout_view(1);
		$view->addData($data);

		$view->addMapping(gettext("filename"), "%%complex", "left");
		$view->addMapping(gettext("date"), "%timestamp", "left");
		$view->addMapping(gettext("size"), "%size", "left");
		$view->defineComplexMapping("complex", array(
			array(
				"type"  => "action",
				"src"   => "%fileicon",
				"link" => array("/cmsfile/", "%id"),
			),
			array(
				"type" => "link",
				"link" => array("/cmsfile/", "%id"),
				"text" => array(" ", "%name")
			)
		));
		echo "<a name='results'></a>";
		echo $view->generate_output(1);

		$next_results = stripslashes("/filesearch/?keywords=".urlencode($_REQUEST["keywords"])."&amp;start=%%#results");

		if ($count > $pagesize) {
			$paging = new Layout_paging();
			$paging->setOptions($_REQUEST["start"], $count, $next_results, 20, 1);
			echo $paging->generate_output();
			echo "<br>";
		}
	}

?>