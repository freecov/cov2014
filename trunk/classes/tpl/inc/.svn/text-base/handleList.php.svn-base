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

	$pagesize = $this->pagesize;
	$start    = $_REQUEST["start"];
	if (!$start)
		$start = 1;
	else
		$start++;

	//format: list_pageid_offset
	$datakey = sprintf("list_%d_%d", $id, $start);

	//prepare link
	$pl = ($this->page_less_rewrites) ? "/":"/page/";

	$fetch = $this->getApcCache($datakey);
	if ($fetch) {
		$data.= $fetch;

	} else {

		$key = sprintf("s:{%s} p:{%s}", session_id(), $id);

		$list = $this->cms->getListData($id);
		if (count($list["_query"]) == 0) {
			$buffer.= "<br><br>".gettext("list error: no filter specified")."<br><br>";
			return;
		}
		if (count($list["_fields"]) == 0) {
			$buffer.= "<br><br>".gettext("list error: no fields specified")."<br><br>";
			return;
		}
		if (count($list["_order"]) == 0) {
			$buffer.= "<br><br>".gettext("list error: no order specified")."<br><br>";
			return;
		}


		/* check for list entry */
		$q = sprintf("select ids from cms_temp where userkey = '%s'", $key);
		$res     = sql_query($q);
		$found   = sql_num_rows($res);
		$results = sql_result($res,0);

		/* if offset is the first page or if no results are found */
		if (!$_REQUEST["list"] || $found == 0) {
			$sql = " SELECT cms_data.id FROM cms_data ##metajoin## left join cms_date ON cms_date.pageid=cms_data.id WHERE isActive = 1 AND ";
			$sql.= sprintf(" isPublic = 1 AND ((cms_data.date_start = 0 OR cms_data.date_start IS NULL OR cms_data.date_start <= unix_timestamp())
					AND (cms_data.date_end = 0 OR cms_data.date_end IS NULL OR cms_data.date_end >= unix_timestamp())) AND apEnabled IN (%s) AND ( ", implode(",", $this->public_roots));

			$first = 1;
			$query = $list["_query"];

			/* always force 'OR' statement to first item */
			$query[0][4] = "of";

			$like = sql_syntax("like");

			$results = array();
			foreach ($query as $k=>$v) {

				if (preg_match("/^meta\d{1,}/si", $v[0])) {
					$meta = (int)preg_replace("/^meta/si", "", $v[0]);
					$v[0] = "cms_metadata.value";

					$find = "##metajoin##";
					$repl = "right join cms_metadata ON cms_metadata.pageid = cms_data.id";
					$sql_mod = str_replace($find, $repl, $sql);
				} else {
					$sql_mod = str_replace("##metajoin##", "", $sql);
				}

				switch ($v[0]) {
					case "paginaTitel":
						$v[0] = "pageTitle";
						break;
				}
				if (preg_match("/not/s",$v[1])) {
					$modifier = " NOT ";
				} else {
					$modifier = " ";
				}


				if ($v[0] != "datum") {
					if (preg_match("/like/s",$v[1])) {
						$condition = "%";
					} else {
						$condition = "";
					}

					$q = sprintf("%s %s%s%s '%s%s%s' ) GROUP BY cms_data.id", $sql_mod, $v[0], $modifier, $like,
						$condition, $v[2], $condition);
				} else {
					$t = explode("-", $v[2]);
					foreach ($t as $tk=>$tv)
						$t[$tk] = explode("/", $tv);

					$tt["start"] = mktime(0,0,0,$t[0][1],$t[0][0],$t[0][2]);
					$tt["end"]   = mktime(0,0,0,$t[1][1],$t[1][0],$t[1][2]);

					$q = sprintf("%s datePublication between %d and %d ) GROUP BY cms_data.id",
						$sql_mod, $tt["start"], $tt["end"]);
				}
				$res = sql_query($q);

				$extra_results = array();
				while ($row = sql_fetch_assoc($res)) {
					if ($v[4] == "of") {
						/* OR - add to array */
						$results[]= $row["id"];
					} else {
						$extra_results[]= $row["id"];
					}
				}
				if ($v[4] != "of") {
					foreach ($results as $k=>$v) {
						if (!in_array($v, $extra_results))
							unset($results[$k]);
					}
				}
			}

			$results = array_slice($results, 0, 1000, TRUE); //limit to 1000 pages
			$results = implode(",", $results);
			if (!$results) $results = "0";

			$q = sprintf("delete from cms_temp where userkey = '%s'", $key);
			sql_query($q);

			$q = sprintf("insert into cms_temp (ids, userkey, datetime) values ('%s', '%s', %d)",
				$results, $key, time());
			sql_query($q);
		}
		$meta_order = array();
		foreach ($list["_order"] as $k=>$v) {
			if (preg_match("/^meta\d{1,}/si", $v["sort"])) {
				$meta_order[]=preg_replace("/^meta/si", "", $v["sort"]);
				$list["_order"][$k]["sort"] = "cms_metadata.value";
			}
		}

		$q = "select cms_data.id from cms_data ";
		$q1 = "";
		if ($meta_order[0]) {
			$q1.= " LEFT JOIN cms_metadata ON cms_metadata.pageid = cms_data.id ";
			$q1.= " WHERE (cms_metadata.fieldid = ".$meta_order[0]." OR cms_metadata.fieldid IS NULL) AND cms_data.id IN (".$results.") ";
		} else {
			$q1.= " WHERE cms_data.id IN (".$results.") ";
		}

		$q1.= sprintf(" AND apEnabled IN (%s) ", implode(",", $this->public_roots));
		$q2 = "select count(cms_data.id) from cms_data ".$q1;
		$q.= $q1;
		$q.= " ORDER BY ";
		$i=0;
		$list["_order"][] = array(
			"sort" => "id"
		);
		foreach ($list["_order"] as $k=>$v) {
			if ($v["sort"]) {
				switch ($v["sort"]) {
					case "paginaTitel":
						$v["sort"] = "pageTitle";
						break;
					case "datumPublicatie":
						$v["sort"] = "datePublication";
						break;
				}
				$i++;
				if ($i>1)	$q.= ",";
					$q.= sprintf(" %s %s ", $v["sort"], $v["asc"]);
			}
		}
		$_data = array();
		$resc = sql_query($q2);
		$num  = sql_result($resc, 0);
		$next_results = "/list/".$id."&amp;mode=".$_REQUEST["mode"]."&amp;start=%%#pagelist";

		$res  = sql_query($q, "", $start-1, $pagesize);

		while ($row = sql_fetch_assoc($res)) {
			$_data[$row["id"]]["id"] = $this->checkAlias($row["id"]);

			foreach ($list["_fields"] as $k=>$v) {

				$t = $this->getVeldValue($k, $row["id"]);
				if ($k == "shopPrice" && $this->valuta && $this->cms_license["cms_shop"])
					$t = sprintf("%s&nbsp;%s\n%s", $this->valuta, str_replace(".", ",", $t),
						$this->addListShopLink($row["id"]));

				$_data[$row["id"]][$v] = ($t) ? ($t):" ";
			}
			ksort($_data[$row["id"]]);
		}
		$view = new Layout_view(1);

		$view->addData($_data);

		foreach ($list["_fields"] as $k=>$v) {
			switch ($k) {
				case "datumPublicatie":
					$name = gettext("date");
					break;
				case "paginaTitel":
					$name = gettext("title");
					$view->setHtmlField($v);
					break;
				case "trefwoorden":
					$name = gettext("keywords");
					break;
				case "pageLabel":
					$name = gettext("label");
					break;
				case "pageHeader":
					$name = gettext("description");
					break;
				case "thumb":
					$name = gettext("image");
					$view->setHtmlField($v);
					break;
				case "shopPrice":
					$name = gettext("price / order");
					$view->setHtmlField($v);
					break;
				case "dateweekday":
					$name = gettext("weekdays");
					$view->setHtmlField($v);
					break;
				case "datemonth":
					$name = gettext("monthdays");
					$view->setHtmlField($v);
					break;
				case "datefull":
					$name = gettext("dates");
					$view->setHtmlField($v);
					break;
				default:
					if (preg_match("/^meta\d{1,}$/s", $k)) {
						$name = (int)preg_replace("/^meta(\d{1,})$/s", "$1", $k);
						$q = sprintf("select field_name from cms_metadef where id = %d", $name);
						$res = sql_query($q);
						$name = sql_result($res,0);
					}
					break;
			}
			if ($v == 1) {
				$view->addMapping($name, "%%complex_".$v, "left");
				$view->defineComplexMapping("complex_".$v, array(
					array(
						"type" => "link",
						"link" => array(($this->textmode) ? "text":$pl, "%id"),
						"text" => "%1"
					)
				), array("class" => "listcolumn_".$v));
			} else {
				$view->addMapping($name, "%%complex_".$v, "left");
				$view->defineComplexMapping("complex_".$v, array(
					array(
						"type" => "text",
						"text" => "%".$v
					),
				), array("class" => "listcolumn_".$v));
			}
		}
		$output2 = new Layout_output;
		$output2->insertTag('a', '', array(
			'id' => 'pagelist',
			'class' => 'anchor'
		));
		$output2->addTag('form', array(
			'id' => 'listForm',
			'style' => 'display: inline',
			'action' => '#'
		));
		$output2->addCode($view->generate_output(1));
		if ($num > $pagesize) {
			$paging = new Layout_paging();
			$paging->setOptions($start-1, $num, $next_results, $pagesize, 1);
			$output2->addCode($paging->generate_output());
		}
		$output2->endTag('form');
		$output2->addTag('br');
		
		$buffer.= $output2->generate_output();
		$data .= $buffer; 
		$this->setApcCache($datakey, $buffer);
	}

?>
