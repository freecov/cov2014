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

	/* show page number if logged in */
	if ($_SESSION["user_id"] && !$this->textmode) {
		$output->addCode("\n<!-- add page load handler -->\n");
		$output->start_javascript();
			$output->addCode(sprintf("addLoadEvent(init_status(%d));", $this->pageid));
		$output->end_javascript();
	}
	if ($this->alternative_footer) {
		$output->addCode("\n<!-- begin cms footer -->\n");
		$output->addCode($this->alternative_text);
		$output->addCode("\n<!-- end cms footer -->\n");
	}

	if ($this->siteroot > 0) {
		$q = sprintf("select letsstat_analytics, google_analytics from cms_license_siteroots
			where pageid = %d", $this->siteroot);
		$res = sql_query($q);
		$cms_siteroot = sql_fetch_assoc($res);
	} else {
		$cms_siteroot = $this->cms_license;
	}
	if ($cms_siteroot["letsstat_analytics"]) {
		$output->addCode("\n\n<!-- begin letsstat code -->\n");
		$output->addCode($this->letsstatAnalytics($cms_siteroot));
		$output->addCode("\n<!-- end letsstat code -->\n\n");
	}

	if ($cms_siteroot["google_analytics"]) {
		$output->addCode("\n\n<!-- begin google analytics code -->\n");
		$output->addCode($this->googleAnalytics($cms_siteroot));
		$output->addCode("\n<!-- end google analytics code -->\n\n");
	}

	$output->addCode("\n");
	$output->start_javascript();
		$output->addCode("page_loaded();");
	$output->end_javascript();
	$output->addCode("\n<!-- end of page -->\n");
	$output->endTag("body");
	$output->endTag("html");
	echo $output->generate_output();
?>