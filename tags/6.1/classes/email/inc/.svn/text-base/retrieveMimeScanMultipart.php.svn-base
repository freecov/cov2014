<?php
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
			$body_txt 	= $this->get_part($stream, $mailnr, "TEXT/PLAIN", false, $subpart);
			$body_html  = $this->get_part($stream, $mailnr, "TEXT/HTML", false, $subpart);
			$p = $xmail->p;

			//headers of message/rfc822 submail
			$head     = imap_rfc822_parse_headers(imap_fetchbody($stream, $mailnr, preg_replace("/.$/","",$part)));
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

			$from        = str_replace("<","< ",$van);
			$from_email  = str_replace(">","> ",$from_email);
			$rcpt        = str_replace("<","< ",$rcpt);
			$rcpt_email  = str_replace(">","> ",$rcpt_email);
			$reply       = str_replace("<","< ",$reply);
			$reply_email = str_replace(">","> ",$reply_email);

			$div  = "<font size=2><div style='font: 10pt arial'>----- ".gettext("Oorspronkelijk Bericht")." -----";
			$div .= "<div style='background: #e4e4e4; font-color: black'><b>".gettext("van").":</b> <a href='mailto:".$from_email."'>".$from."</a></div>";
			$div .= "<div><b>".gettext("naar").":</b> <a href='mailto:$rcpt_email'>$rcpt</a></div>";
			$div .= "<div><b>".gettext("datum").":</b> ".$date."</div>";
			$div .= "<div><b>".gettext("onderwerp").":</b> ".$subject."</div>";
			$div .= "</div>";

			if ($body_html) {
				//if the content is HTML, replace body tag
				$xmail->data[$p] = $body_html;
				$xmail->data[$p] = preg_replace("/<body[^>]*?>/i","<body>$div<br>",$xmail->data[$p]);
			} else {
				//if the content is not HTML, make HTML so we can render it
				$xmail->data[$p] = preg_replace("/<br[^>]*?>/i","<br>",nl2br($body_txt));
				$xmail->data[$p] = "<html><body><style type=\"text/css\">BODY { font-family: Courier New; font-size: 10pt;}</style>$div<br>".$xmail->data[$p]."</font></body></html>";
				$xmail->data[$p] = preg_replace("/<font[^>]*?>/i","",$xmail->data[$p]);
			}

			/* free some memory */
			unset($body_html);
			unset($body_text);

			//put everything in the object for later processing.
			$fname = $subject;
			$fname = str_replace(" ","_",$fname);
			$fname = preg_replace("/\W/i","",$fname);
			$xmail->name[$p]      = "[".$fname."].html";
			$xmail->part[$p]      = preg_replace("/.$/","",$part);
			$xmail->type[$p]      = "TEXT/HTML";
			$xmail->subtype[$p]   = "TEXT/HTML";
			$xmail->size[$p]      = strlen($xmail->data[$p]); //*8 (?);
			$xmail->enc[$p]       = $obj->enc;
			$xmail->htmlMail[$p]  = 1;
			$xmail->cid[$p]       = "<converted/eml>";
			$xmail->fetchData[$p] = 0;
			$xmail->p++;

			/* nested scan */
			$this->mime_scan_multipart($obj->parts, "$part", &$stream, &$mailnr, 1);

		} else {

			/* if this part = binary file data */
			if($obj->type != 1) { //0 = text, 2 = attached email, 3-7 is binary
				/* only process non-text body parts */
				if (strtolower($obj->disposition) == "attachment" || (strtolower($obj->disposition) == "inline" && !$is_alternative_part) || $obj->type != 0) {
					$this->XmailAdd($obj, $stream, $mailnr, $part);
				}
			}
		}
		$n++;
	}
?>