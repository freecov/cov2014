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
	$output = new Layout_output();
	$output->addCode($this->redir);
	$output->addCode("\n");
	if ($this->has_abbr && !$this->textmode) {
		$this->loadAbbreviations();
		$output->load_javascript("classes/tpl/inc/wz_tooltip.js");
	}

	if ($this->alternative_footer) {
		$output->addCode($this->alternative_text);
	}

	if ($this->siteroot > 0) {
		$q = sprintf("select letsstat_analytics, google_analytics, piwik_analytics from cms_license_siteroots
			where pageid = %d", $this->siteroot);
		$res = sql_query($q);
		$cms_siteroot = sql_fetch_assoc($res);
	} else {
		$cms_siteroot = $this->cms_license;
	}
	$output->addCode("\n\n<!-- [end of page] -->\n");

	$starttime = $this->_rendertime;
	$endtime   = microtime(1);
	$totaltime = number_format(($endtime - $starttime), 2);

	$web = ($totaltime - $GLOBALS["dbstat"]["time"]);
	if ($web < 0)
		$web = 0;

	if ($totaltime < $web)
	$web = $totaltime;

	if ($_SESSION["user_id"]) {
		$status = sprintf("page loaded - page id [%d] - web [%ss], db: [%ss], total [%ss]",
			$this->pageid,
			number_format($web,2),
			number_format($GLOBALS["dbstat"]["time"],2),
			number_format($totaltime,2)
		);
	}
	if ($_REQUEST["mode"] != "text") {
		$output->start_javascript(false);
			$output->addCode(sprintf("if (typeof(page_loaded) != 'undefined') page_loaded('%s');", $status));
		$output->end_javascript(false);
		$output->addCode("\n");
	}
	if ($cms_siteroot["letsstat_analytics"]) {
		$output->addCode("\n<!-- begin letsstat code -->\n");
		$output->addCode($this->letsstatAnalytics($cms_siteroot));
		$output->addCode("\n<!-- end letsstat code -->\n");
	}

	if ($cms_siteroot["piwik_analytics"]) {
		$output->addCode($this->piwikAnalytics($cms_siteroot));
	}

	$output->endTag("body");
	$output->endTag("html");
	echo $output->generate_output();
?>
