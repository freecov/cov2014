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

	$pageid =& $this->pageid;
	$this->init_aliaslist();

	if ($pageid == "__def")
		$pageid = "__sitemap";

	if ($pageid == "__sitemap")
		$alias = "/sitemap.htm";
	else
		$alias = "/page/".$this->checkAlias($pageid);

	$this->file_loader[]= "<link rel='stylesheet' type='text/css' href='/classes/tpl/inc/style_print.css'>";
	$this->start_html(1, 1);

	$this->displayPath();
	echo sprintf(" (<a href='%s'>%s</a>)", $alias, gettext("full version"));

	$this->getPageTitle($pageid);
	$this->getPageData($pageid, "text");

	if ($_REQUEST["print"]) {
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(" setTimeout('window.print();', 200); ");
			if ($_REQUEST["close"])
				$output->addCode(" setTimeout('window.close();', 1000); ");

		$output->end_javascript();
		echo $output->generate_output();
	}
	echo "<span class='noprint'>\n<br><br>\n";
	echo "<a href='javascript: history.go(-1);'>".gettext("back")."</a>";
	echo " | <a href='javascript: window.print();'>".gettext("print")."</a>";
	echo " | <a href='".$alias."'>".gettext("full version")."</a>";
	if ($pageid != "__sitemap")
		echo " | <a href='/sitemap_plain.htm'>".gettext("sitemap")."</a>";
	echo "</span>\n";

	$this->end_html();
?>