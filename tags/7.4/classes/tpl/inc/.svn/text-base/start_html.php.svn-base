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
	/* set global textmode */
	$this->textmode = $textmode;

	$output = new Layout_output();
	if ($this->cms_license["cms_use_strict_mode"])
		$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n\t\"http://www.w3.org/TR/html4/loose.dtd\">\n");
	else
		$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");

	$output->addTag("html");
	$output->addCode(sprintf("\n<!-- page id [%d] -->\n", $this->pageid));
	$output->addTag("head");
	echo $output->generate_output();
	if ($header) {
		$this->html_header("", $textmode);
	}
	foreach ($this->file_loader as $file) {
		echo $file;
	}
	echo "</head>\n";
	echo "<body>\n";
	echo "<!-- start of document template -->\n\n";

	$this->output_started = 1;
?>