<?php

	/* delete old keys (> 1 hour old) */
	$q = sprintf("delete from cms_temp where datetime <= %d", mktime()-(60*60));

	$key = sprintf("s:{%s} p:{%s}", session_id(), $pageid);
	$list = $this->cms->getListData($id);

	/* check for list entry */
	$q = sprintf("select count(*) from cms_temp where userkey = '%s'", $key);
	$res = sql_query($q);
	$found = sql_result($res,0);

	if (!$_REQUEST["list"] || $found == 0) {

		$sql = " SELECT cms_data.id FROM cms_data left join cms_date ON cms_date.pageid=cms_data.id WHERE isActive = 1 AND ";
		$sql.= " isPublic = 1 AND ((cms_data.date_start = 0 OR cms_data.date_start IS NULL OR cms_data.date_start <= unix_timestamp())
				AND (cms_data.date_end = 0 OR cms_data.date_end IS NULL OR cms_data.date_end >= unix_timestamp())) AND ( ";

		$first = 1;
		$query = $list["_query"];

		$like = sql_syntax("like");

		$results = array();

		foreach ($query as $k=>$v) {

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

			if (preg_match("/like/s",$v[1])) {
				$condition = "%";
			} else {
				$condition = "";
			}

			$q = sprintf("%s %s%s%s '%s%s%s' )", $sql, $v[0], $modifier, $like,
				$condition, $v[2], $condition);

			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				if ($v[4] == "of") {
					/* OR - add to array */
					$results[]= $row["id"];
				} else {
					$old_results = $results;
					$results = array();
					/* AND, check for existance */
					if (in_array($row["id"], $old_results))
						$results[]= $row["id"];

					unset ($old_results);
				}
			}
		}
		$meta_order = array();
		foreach ($list["_order"] as $k=>$v) {
			if (preg_match("/^meta\d{1,}/si", $v["sort"])) {
				$meta_order[]=preg_replace("/^meta/si", "", $v["sort"]);
				$list["_order"][$k]["sort"] = "cms_metadata.value";
			}
		}
		array_slice($results, 0, 1000); //limit to 1000 pages
		$results = implode(",", $results);
		if (!$results) $results = "0";

		$q = sprintf("delete from cms_temp where userkey = '%s'", $key);
		sql_query($q);

		$q = sprintf("insert into cms_temp (ids, userkey, datetime) values ('%s', '%s', %d)",
			$results, $key, mktime());
		sql_query($q);

	} else {

		$q = sprintf("select ids from cms_temp where userkey = '%s'", $key);
		$res = sql_query($q);
		$results = sql_result($res,0);

		$meta_order = array();
		foreach ($list["_order"] as $k=>$v) {
			if (preg_match("/^meta\d{1,}/si", $v["sort"])) {
				$meta_order[]=preg_replace("/^meta/si", "", $v["sort"]);
				$list["_order"][$k]["sort"] = "cms_metadata.value";
			}
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
	$q2 = "select count(cms_data.id) from cms_data ".$q1;
	$q.= $q1;
	$q.= " ORDER BY ";

	$i=0;
	foreach ($list["_order"] as $k=>$v) {
		if ($v["sort"]) {
			$i++;
			if ($i>1)	$q.= ",";
				$q.= sprintf(" %s %s ", $v["sort"], $v["asc"]);
		}
	}
	$pagesize = $this->pagesize;
	$start    = $_REQUEST["start"];
	if (!$start)
		$start = 1;
	else
		$start++;

	$_data = array();
	$resc = sql_query($q2);
	$num  = sql_result($resc, 0);
	$next_results = "/list/".$id."&amp;start=%%#pagelist";

	$res  = sql_query($q, "", $start, $pagesize);

	while ($row = sql_fetch_assoc($res)) {
		$_data[$row["id"]]["id"] = $this->checkAlias($row["id"]);
		foreach ($list["_fields"] as $k=>$v) {
			$t = $this->getVeldValue($k, $row["id"]);
			$_data[$row["id"]][$v] = ($t) ? ($t):"--";
		}

		ksort($_data[$row["id"]]);
	}

	$view = new Layout_view();
	$view->addData($_data);

	foreach ($list["_fields"] as $k=>$v) {
		switch ($k) {
			case "datumPublicatie":
				$name = gettext("datum");
				break;
			case "paginaTitel":
				$name = gettext("pagina titel");
				break;
			case "trefwoorden":
				$name = gettext("trefwoorden");
				break;
			case "pageLabel":
				$name = gettext("pagina label");
				break;
		}
		if ($v == 1) {
			$view->addMapping($name, "%%complex_".$v, "left");
			$view->defineComplexMapping("complex_".$v, array(
				array(
					"type" => "link",
					"link" => array("/page/", "%id"),
					"text" => "%1"
				)
			));
		} else {
			$view->addMapping($name, "%".$v, "left");
		}
	}

	$data.= "<a name='pagelist'></a>";
	$data.= $view->generate_output();
	$data.= "<br>";

	if ($num > $pagesize) {
		$paging = new Layout_paging();
		$paging->setOptions($start-1, $num, $next_results, $pagesize, 1);
		$data.= $paging->generate_output();
		$data.= "<br>";
	}

?>
