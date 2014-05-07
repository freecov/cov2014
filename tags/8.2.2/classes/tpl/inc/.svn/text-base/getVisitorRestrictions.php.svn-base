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

	$pages = array();
	if (!$path[0])
		$path[0] = $this->default_page;

	$q = sprintf("select id, isProtected from cms_data where id IN (%s)", implode(",", $path));
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res,2)) {
		$pages[$row["id"]] = $row["isProtected"];
	}
	$pages = array_reverse($pages, 1);
	$locked = 0;
	if ($pages[$page] == 1) {
		$locked = 1;
		$key = $page;
	} elseif (in_array(2, $pages)) {
		/* get keys */
		$keys = array_search(2, $pages);
		$locked = 1;
		if (is_array($keys))
			$key = $keys[0];
		else
			$key = $keys;
	}
	/* if page is locked, prevent filesys caching (see common/headers.php) */
	if ($locked)
		no_cache_headers();

	/* check if the user has no permissions and the page is locked */
	if ($locked && is_numeric($key)) {
		/* get page permissions */
		$perms = $this->cms->getAuthorisations($page);
		$xs = 0;
		if ($_SESSION["user_id"]) {
			if ($perms[$_SESSION["user_id"]] != "D")
				$xs = 1;

			if (!$xs) {
				$user_data = new User_data();
				$groups = $user_data->getUserGroups($_SESSION["user_id"]);
				foreach ($groups as $g) {
					if ($perms["G".$g] != "D")
						$xs = 1;
				}
			}
		} elseif ($_SESSION["visitor_id"]) {
			if ($perms["U".$_SESSION["visitor_id"]] != "D")
				$xs = 1;
		}
		$this->apc_disable = 1;
		if (!$xs) {
			$this->need_authorisation = $this->pageid;
			$this->pageid = "__err401";
		}
	}
?>