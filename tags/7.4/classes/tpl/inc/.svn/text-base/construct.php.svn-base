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
	/* create cache object */
	$this->apc_cache = new Tpl_cache();
	$this->setApcOptions();

	if ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")
		$this->protocol = "https://";
	else
		$this->protocol = "http://";

	/* detect supported browser versions */
	require_once("classes/covide/browser.php");
	$this->browser = browser_detection("full");

	$this->http_host = strtolower($_SERVER["HTTP_HOST"]);

	$this->cms = new Cms_data();
	$GLOBALS["covide"]->webroot = preg_replace("/page\/$/s", "", $GLOBALS["covide"]->webroot);

	$q = " isPublic=1 and isActive=1 ";
	$q.= " and ((date_start = 0 OR date_start IS NULL OR date_start <= ".mktime().") ";
	$q.= " and (date_end = 0 OR date_end IS NULL OR date_end >= ".mktime().")) ";
	$this->base_condition = $q;

	$q = "isMenuItem=1 and ".$q;
	$this->base_condition_menu = $q;

	$this->menu_cache_file = $GLOBALS["covide"]->temppath."menu";

	/* set some default images */
	$this->favicon = "/img/cms/favicon.png";
	$this->logo    = "/img/cms/logo.gif";

	/* get default page from db */
	$q = "select * from cms_license";
	$res = sql_query($q);
	$row = sql_fetch_assoc($res);

	/* save license for later use */
	$row["has_shop"] = $row["cms_shop"];
	$this->cms_license = $row;

	/* some vars */
	$this->default_page = $row["cms_defaultpage"];
	if ($row["cms_favicon"])     $this->favicon  = $row["cms_favicon"];
	if ($row["cms_logo"])        $this->logo     = $row["cms_logo"];
	if ($row["search_language"]) $this->language = $row["search_language"];
	if (trim($row["cms_manage_hostname"]))
		$this->manage_hostname = $row["cms_manage_hostname"];
	else
		$this->manage_hostname = $_SERVER["HTTP_HOST"]; //compatibility if not set

	$this->siteroot           = 0;
	$this->special_roots["R"] = $this->default_page;

	/* try to determine the default siteroot page */
	$q = sprintf("select cms_defaultpage, pageid, cms_favicon, cms_logo, search_language, cms_hostnames from cms_license_siteroots where
		cms_hostnames like '%%%s%%'", $this->http_host);
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$hostnames = explode("\n", $row["cms_hostnames"]);
		foreach ($hostnames as $h) {
			if (strtolower($this->http_host) == trim(strtolower($h))) {
				$this->siteroot     = $row["pageid"];
				$this->default_page = $row["cms_defaultpage"];

				if ($row["cms_favicon"])     $this->favicon  = $row["cms_favicon"];
				if ($row["cms_logo"])        $this->logo     = $row["cms_logo"];
				if ($row["search_language"]) $this->language = $row["search_language"];
			}
		}
	}

	/* get default public root (domains) */
	$q = "select id, isSpecial from cms_data where (parentPage = 0 or parentPage is null) and (isPublic = 1 or apEnabled = ".$this->siteroot.")";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res,2)) {
		if (!in_array($row["isSpecial"], array("X","D")))
			$this->addPublicRoot($row["id"]);
		else
			$this->special_roots[$row["isSpecial"]] = $row["id"];
	}

	/* for linkchecker, we make an expection */
	if ($_REQUEST["user"] && $_REQUEST["hash"] && $_REQUEST["page"]) {
		$q = "select pageid from cms_license_siteroots";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
			$this->addPublicRoot($row["pageid"]);
		}
	}

	if ($this->siteroot == 0)
		$this->public_roots[0] = 0;

	if (!$this->manage_hostname)
		$this->manage_hostname = strtolower($_SERVER["HTTP_HOST"]);

	/* set a custom language (if any) */
	if ($this->language)
		$GLOBALS["covide"]->override_language(strtoupper($this->language));


	$this->sendHeaders();

	/* handle current request or page */
	if (!$_REQUEST["include"])
		$this->handlePage();

	/* check for internal mode status */
	$this->switchCustomModes();

	/* delete old keys (> 1 hour old) */
	$q = sprintf("delete from cms_temp where datetime <= %d", mktime()-$this->keys_cache_timeout);
	sql_query($q);
?>