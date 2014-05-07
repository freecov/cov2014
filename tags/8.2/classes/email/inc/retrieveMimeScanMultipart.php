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

	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}

	$xmail =& $this->xmail;

	$n = 1;
	foreach ($parts as $obj) {

		/* part is part + subpart */
		$part = "$part_number$n.";

		if ($obj->type == 1) {
			//ignore subnumbering for combination rfc822 and multipart headers
			// > parse as 1 part
			if ($ign_part==1) {
				$part = $part_number;
			}
			if (strtolower($obj->subtype) == "alternative") {
				$is_alternative_part = 1;
			}

			/* nested scan of this part */
			$this->mime_scan_multipart($obj->parts, "$part", &$stream, &$mailnr, 0, $is_alternative_part);

		} elseif ($obj->type == 2 && strtolower($obj->subtype)=="rfc822") {

			//parse eml file in email
			//forward to multipart parser without increased subnumbering when nestet multipart stuff detected
			//parsen van inhoud van rfc(multipart) gedeelte
			//parse content of rfc (multipart) part

			$subpart = preg_replace("/.$/","",$part);
			$p = $xmail->p;
			/*
			$body_txt 	= $this->get_part($stream, $mailnr, "TEXT/PLAIN", false, $subpart);
			$body_html  = $this->get_part($stream, $mailnr, "TEXT/HTML", false, $subpart);
			*/

			//headers of message/rfc822 submail
			$eml      = imap_fetchbody($stream, $mailnr, preg_replace("/.$/","",$part));
			$head     = imap_rfc822_parse_headers($eml);
			$subject 	= $this->decodeMimeString($head->subject);
			$from			= $this->decodeMimeString($head->fromaddress);
			$rcpt			= $this->decodeMimeString($head->toaddress);
			$reply		= $this->decodeMimeString($head->reply_toaddress);
			$date	    = $head->date;
			unset($head);

			$from_email = preg_replace("/^[^<]*?<([^>]*?)>/i", "$1", $from);
			if (!$from_email) {
				$from_email = $from;
			}

			$rcpt_email = preg_replace("/^[^<]*?<([^>]*?)>/i", "$1", $rcpt);
			if (!$rcpt_email) {
				$rcpt_email = $rcpt;
			}

			$div  = "<html><body><font size=2><div style='font: 10pt arial'>----- ".gettext("Original message")." -----";
			$div .= "<div style='background: #e4e4e4; font-color: black'><b>".gettext("from").":</b> <a href='mailto:".$from_email."'>".str_replace("<", "&lt;", $from)."</a></div>";
			$div .= "<div><b>".gettext("to").":</b> <a href='mailto:$rcpt_email'>$rcpt</a></div>";
			$div .= "<div><b>".gettext("date").":</b> ".$date."</div>";
			$div .= "<div><b>".gettext("subject").":</b> ".$subject."</div>";
			$div .= "</div></body></html>";

			/* create temp folder */
			$tmp = sprintf("eml_%s", md5(rand().mktime().$div));
			$dir = sprintf("%s%s", $GLOBALS["covide"]->temppath, $tmp);
			mkdir($dir, 0777);

			/* move file to its destination */
			file_put_contents(sprintf("%s/%s.eml", $dir, $tmp), $eml);
			unset($eml);

			$cmd = sprintf("munpack -C %s -fqt %s.eml", escapeshellarg($dir), $tmp);
			exec($cmd, $ret, $retval);

			$fname = $subject;
			$fname = str_replace(" ","_",$fname);
			$fname = preg_replace("/\W/i","",$fname);

			file_put_contents(sprintf("%s/headers_%s.htm", $dir, $fname), $div);

			unlink(sprintf("%s/%s.eml", $dir, $tmp));

			$zipfile = new Covide_zipfile();
			$zipfile->add_dir("message/");

			/* remove dir contents */
			$s = scandir($dir);
			foreach ($s as $f) {
				$fn = sprintf("%s/%s", $dir, $f);
				$fn2 = $fn;

				if (!$this->filesys)
					$this->filesys = new Filesys_data();

				if (!is_dir($fn)) {
					$b = file_get_contents($fn);
					if (preg_match("/^part\d{1,}/s", basename($fn))) {
						$mime = $this->filesys->detectMimetype($fn);
						if ($mime == "TEXT/PLAIN") {
							$b = trim($b);
							if (preg_match("/^((<\!DOCTYPE[^>]*?>)|(<html[^>]*?>))/si", $b)
								&& preg_match("/<\/html>$/si", $b)) {
								$mime = "TEXT/HTML";
							} else {
								$fn = sprintf("message_%s.txt", basename($fn));
							}
						}
					}
					if ($mime == "TEXT/HTML") {
						$fn = sprintf("message_%s.htm", basename($fn));
					}

					$zipfile->add_file($b, sprintf("message/%s", basename($fn)));

					unlink($fn2);
					unset($b);
				}
			}
			$xmail->data[$p] = $zipfile->file();
			unset($zipfile);

			/* remove dir */
			rmdir($dir);

			//put everything in the object for later processing.
			$xmail->name[$p]      = "email[".$fname."].zip";
			$xmail->part[$p]      = preg_replace("/.$/","",$part);
			$xmail->type[$p]      = "APPLICATION/X-ZIP";
			$xmail->subtype[$p]   = "APPLICATION/X-ZIP";
			$xmail->size[$p]      = strlen($xmail->data[$p]); //*8 (?);
			$xmail->enc[$p]       = $obj->enc;
			//$xmail->htmlMail[$p]  = 1;
			$xmail->cid[$p]       = "<converted/eml>";
			$xmail->fetchData[$p] = 0;
			$xmail->p++;

			/* nested scan */
			//$this->mime_scan_multipart($obj->parts, "$part", &$stream, &$mailnr, 1);

		} else {
			/* if this part = binary file data */
			if($obj->type != 2) { //0 = text, 2 = attached email, 3-7 is binary
				/* only process non-text body parts */
				#print_r($obj);
				if (strtolower($obj->disposition) == "attachment" || (strtolower($obj->disposition) == "inline" && !$is_alternative_part) || $obj->type != 0) {
					$this->XmailAdd($obj, $stream, $mailnr, $part);
				}
			}
		}
		$n++;
	}
?>