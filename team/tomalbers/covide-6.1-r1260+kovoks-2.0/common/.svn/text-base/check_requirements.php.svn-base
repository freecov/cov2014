<?php

	/* load required extensions */
	$extensions = array(
		"mysql",
		"imap",
		"mbstring",
		"session",
		"gettext",
		"pcre",
		"zlib"
	);
	foreach ($extensions as $extension) {
		if (!extension_loaded($extension)) {
			if (dl( sprintf("%s.so", $extension)) == false) {
				$enable_gzip = 0;
			};
		}
	}

	/* check binary programs */
	$check = array(
		"uudecode"     => "uudecode --help",        // uudecode (encoded attachments)
		"uudeview"     => "uudeview --help",        // uudeview (encoded attachments)
		"tnef"         => "tnef --help",            // ms tnef support
		"pdftohtml"    =>"pdftohtml -help",         // pdf to html
		"wv"           => "wvHtml --help",          // ms word to html
		"xlhtml"       => "xlhtml --help",          // ms excel to html
		"unzip"        => "unzip --help",           // unzip support
		"zip"          => "zip -v",                 // zip support
		"o3view"       => "echo '' | o3tohtml",     // open office decoder
		"utf8tolatin1" => "echo '' | utf8tolatin1", // utf8 to latin conversion
		"elinks"       => "elinks --help",          // utf8 to latin conversion
		"convert"      => "convert --help",         // utf8 to latin conversion
		"wmf2eps"      => "wmf2eps --help",         // utf8 to latin conversion
		"sfftobmp"     => "sfftobmp --help",        // sff conversion (voip)
		"tiff2pdf"     => "tiff2pdf --help",        // tiff to pdf (voip)
		"html2ps"      => "html2ps --help",         // html to postscript (templates)
		"ps2pdf"       => "ps2pdf --help",          // postscript to pdf (templates)
		"curl"         => "curl --help"             // curl (template images)
	);
	$hash = md5(print_r($check, true));

	//check if check is already done
	$check_file = "tmp/check.txt";

	if ($_SESSION["program_check"] != $hash) {
		if (!file_exists($check_file)) {
			$need_check = 1;
		} else {
			$fhash= file_get_contents($check_file);
			if ($fhash != $hash) $need_check = 1;
		}
		if ($need_check) {
			foreach ($check as $k=>$v) {
				$cmd = $v;
				exec($cmd, $ret, $retval);
				if ($retval == 127) {
					$display_error[]= $k;
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
?>
