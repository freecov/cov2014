<?php
/**
  * Covide Includes
  *
  * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
  * @version %%VERSION%%
  * @license http://www.gnu.org/licenses/gpl.html GPL
  * @link http://www.covide.net Project home.
  * @copyright Copyright 2000-2008 Covide BV
  * @package Covide
  */

	/* set error reporting level */
	error_reporting(E_ALL & ~E_NOTICE);

	/* if apache config is at the default dir, check for loaded mods */
	$apache_dir = "/etc/apache2/mods-enabled";
	$apache_mods = array("headers", "rewrite");
	if (file_exists($apache_dir)) {
		$f = scandir($apache_dir);
		foreach ($apache_mods as $mod) {
			if (!in_array($mod.".load", $f))
				trigger_error(sprintf("Apache module [%s] is not loaded.", $mod), E_USER_ERROR);
		}
	}

	/* check php version */
	if (version_compare(phpversion(), "5.2.0", "<")) {
		$err = "PHP 5.2.0 or later is required to run Covide, you are running php: ".phpversion();
		trigger_error($err, E_USER_ERROR);
	}

	$ini = array(
		"allow_call_time_pass_reference" => 1,
		"safe_mode" => 0,
		"memory_limit" => 32,
		"post_max_size" => 16,
		"upload_max_filesize" => 32
	);
	foreach ($ini as $k=>$v) {
		if ($v <= 1) {
			if (ini_get($k) != $v)
				$display_error[] = sprintf("Please set '%s = %s' in your php.ini", $k, ($v == 1) ? "On":"Off");
		} else {
			if (ini_get($k) < $v)
				$display_error[] = sprintf("Please set '%s >= %s' in your php.ini", $k, $v);
		}
	}

		/* load required extensions */
	$extensions = array(
		"imap",
		"mbstring",
		"session",
		"gettext",
		"pcre",
		"zlib"
	);
	foreach ($extensions as $extension) {
		if (!extension_loaded($extension))
			$display_error[] = sprintf("PHP extension '%s' is not loaded", $extension);
	}

	/* check binary programs */
	$check = array(
		"beagle"       => "beagle-query --help",    // beagle desktop search
		"uudecode"     => "uudecode --help",        // uudecode (encoded attachments)
		"uudeview"     => "uudeview --help",        // uudeview (encoded attachments)
		"tnef"         => "tnef --help",            // ms tnef support
		"pdftohtml"    => "pdftohtml -help",        // pdf to html
		"wv"           => "wvHtml --help",          // ms word to html
		"xlhtml"       => "xlhtml --help",          // ms excel to html
		"unzip"        => "unzip --help",           // unzip support
		"zip"          => "zip -v",                 // zip support
		/*
		"o3view"       => "echo '' | o3tohtml",     // open office decoder
		"utf8tolatin1" => "echo '' | utf8tolatin1", // utf8 to latin conversion
		 */
		"links2"       => "links2 --help",          // utf8 to latin conversion
		"convert"      => "convert --help",         // utf8 to latin conversion
		"wmf2eps"      => "wmf2eps --help",         // utf8 to latin conversion
		"sfftobmp"     => "sfftobmp --help",        // sff conversion (voip)
		"tiff2pdf"     => "tiff2pdf --help",        // tiff to pdf (voip)
		"html2ps"      => "html2ps --help",         // html to postscript (templates)
		"ps2pdf"       => "ps2pdf --help",          // postscript to pdf (templates)
		"curl"         => "curl --help",            // curl (template images)
		"fortune-mod"  => "/usr/games/fortune",     // fortune-mod package
		"xmllint"      => "xmllint --help",         // xml syntax checking (cms and sync)
		"munpack"      => "munpack /dev/null",      // munpack routines for eml files
		"aspell"       => "aspell -v",              // spellchecker for editor
		"php5"         => "php5 -v"                 // php5 cli for cronjobs
	);
	$hash = md5(print_r($check, true));

	//check if check is already done
	$check_file = sprintf("%stmp/check.txt",
		$GLOBALS["autoloader_include_path"]);

	//check if check file is not aged (1 day)
	/*
	if (file_exists($check_file)) {
		$age = mktime() - filemtime($check_file);
		if ($age > 60*60*24)
			unlink($check_file);
	}
	*/

	if ($_SESSION["program_check"] != $hash) {
		if (!file_exists($check_file)) {
			$need_check = 1;
		} else {
			$fhash= file_get_contents($check_file);
			if ($fhash != $hash) $need_check = 1;
		}
		if ($need_check) {
			foreach ($check as $k=>$v) {
				$cmd = sprintf("%s > /dev/null", $v);
				exec($cmd, $ret, $retval);
				if ($retval == 127) {
					$t = sprintf("Program '%s' not found.", $k);
					$display_error[]= $t;
				}
			}
			if (!$display_error) {
				$out = fopen($check_file, "w");
				fwrite($out, $hash);
				fclose($out);
				$_SESSION["program_check"]=$hash;
			}
		}
	}
	if (is_array($display_error) && count($display_error) > 0) {
		foreach ($display_error as $error) {
			trigger_error($error, E_USER_WARNING);
		}
	}
?>
