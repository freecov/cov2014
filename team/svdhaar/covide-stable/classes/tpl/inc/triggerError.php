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

	$this->http_error = $code;
	$this->apc_disable = 1;

	$s = $this->cms->getCmsSettings($this->siteroot);
	$ary = array(401,403,404,602);
	foreach ($ary as $a) {
		if (!$s["custom_".$a])
			$s["custom_".$a] = $this->cms_license["custom_".$a];
	}

	switch ($code) {
		case 404:
		case 410:
			if (!$this->no_status_header)
				header(sprintf("Status: %s Not Found", $code), true, 404);

			header(sprintf("%s %s Not Found", $_SERVER['SERVER_PROTOCOL'], $code), true, 404);

			if ($s["custom_404"])
				$this->getPageTitle($s["custom_404"]);
			elseif ($code == 404) 
				echo sprintf("<h1>404 Not Found</h1>");
			else 
				echo sprintf('<h1>410 Gone</h1>');
			break;
		case 403:
			if (!$this->no_status_header)
				header("Status: 403 Forbidden", true, 403);

			header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
			if ($s["custom_403"])
				$this->getPageTitle($s["custom_403"]);
			else
				echo sprintf("<h1>403 Forbidden</h1>");
			break;
		case 401:
			if (!$this->no_status_header)
				header("Status: 401 Unauthorized", true, 401);

			header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized", true, 401);
			if ($s["custom_401"])
				$this->getPageTitle($s["custom_401"]);
			else
				echo sprintf("<h1>401 Unauthorized</h1>");
			break;
		case 307:
			if (!$this->no_status_header)
				header("Status: 307 Temporary Redirect", true, 307);
			header($_SERVER["SERVER_PROTOCOL"]." 307 Temporary Redirect", true, 307);
			break;
		case 301:
			if (!$this->no_status_header)
				header("Status: 301 Moved Permanently", true, 301);

			header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently", true, 301);
			break;
		case 602:
			if (!$this->no_status_header)
				header("Status: 602 Unknown Error", true, 602);

			header($_SERVER["SERVER_PROTOCOL"]." 602 Unknown Error", true, 602);
			if ($s["custom_602"])
				$this->getPageTitle($s["custom_602"]);
			else
				echo sprintf("<h1>602 Unknown Error</h1>");
			break;
	}
	$this->custom_status = $s["custom_".$code];
?>
