<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	/* use external elinks/links2 utility */
	$external = true;

	if ($external) {
		$conversion = new Layout_conversion();
		//$html = $conversion->str2utf8($html);

		$html = preg_replace("/<meta[^>]*?>/si", "", $html);

		$temp = tempnam($GLOBALS["covide"]->temppath, "MAIL_");
		$handle = fopen($temp, "w");
		fwrite($handle, $html);
		fclose($handle);

		$cmd = sprintf("export HOME=/tmp && links2 -dump -codepage 'ISO-8859-15' -force-html -html-assume-codepage 'UTF-8' -html-numbered-links 1 file:%s", $temp);
		exec ($cmd, $ret, $retcode);

		$return = @mb_convert_encoding(implode("<br>", $ret), "UTF-8", "ISO-8859-15");
		@unlink($temp);
	} else {
		$conversion = new Layout_conversion();
		$return = $conversion->html2text($html);
	}
?>
