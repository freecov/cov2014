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

	$results = array();
	$like = sql_syntax("like");
	$regex_syntax = sql_syntax("regex");
	$repl = " replace(replace(value, '\\n', '|'), '\\r', '') ";
	foreach ($data as $k=>$v) {
		$results[$k] = array();
		if (is_array($v) && count($v) > 0) {
			$v = sprintf("((%s))", trim(implode(")|(", $v)));
			$reg = " ($repl $regex_syntax '(^|\\\\|)". $v ."(\\\\||$)') ";

			$q = sprintf("select cms_data.id from cms_data
				left join cms_metadata on cms_data.id = cms_metadata.pageid
				where %s and fieldid = %d and %s and apEnabled IN (%s) order by datePublication desc",
				$this->base_condition, $k, $reg, implode(",", $this->public_roots));
		} elseif ($v) {
			$q = sprintf("select cms_data.id from cms_data
				left join cms_metadata on cms_data.id = cms_metadata.pageid
				where %s and fieldid = %d and value $like '%%%s%%' and apEnabled IN (%s) order by datePublication desc",
				$this->base_condition, $k, $v, implode(",", $this->public_roots));
		}
		if ($v) {
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$results[$k][$row["id"]] = $row["id"];
			}
		}
	}
	$pages = array();
	foreach ($results as $k=>$data) {
		foreach ($data as $v) {
			$pages[$v]++;
		}
	}

	foreach ($pages as $k=>$v) {
		if ($v < count($results))
			unset($pages[$k]);
	}

	$target = array(-1);
	foreach ($pages as $k=>$v) {
		$target[] = $k;
	}
	$pages = $target;
?>