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
	if ($this->textmode) {
		$this->setDocType(true, 'mobile');
	}

	$output = new Layout_output();
	if ($this->cms_license['cms_use_strict_mode']) {
		$output->addCode("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
		$output->addCode("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
			\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
		$output->addCode("<html xmlns=\"http://www.w3.org/1999/xhtml\">");
	} else {
		$output->addCode($this->parseDocType());
		if (!$this->doctype['xhtml']) {
			$output->addTag("html");
		} else {
			$output->addTag('html', array('xmlns' => 'http://www.w3.org/1999/xhtml'));
		}
	}
	$output->addCode("\n");

	//$output->addCode(sprintf("\n<!-- page id [%d] -->\n", $this->pageid));
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
	echo "<!-- [start of document template] -->\n\n";

	$this->output_started = 1;
?>
