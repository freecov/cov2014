<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$q = "select mail_messages.*, mail_messages_data.mail_decoding, mail_messages_data.body as mail_body, mail_messages_data.header as mail_header ";
	$q.= "from mail_messages left join mail_messages_data ON mail_messages_data.mail_id = mail_messages.id ";
	$q.= sprintf("where id = %d", $id);
	$res = sql_query($q);
	$row = sql_fetch_assoc($res, "", array("mail_body"));

	$conversion = new Layout_conversion();

	if ($row["mail_decoding"]) {
		$this->handleWindows1251($row["mail_body"], $row["mail_decoding"]);
		$row["mail_body"] = @mb_convert_encoding($row["mail_body"], "UTF-8", $row["mail_decoding"]);
	} else {
		$enc = mb_detect_encoding($row["mail_body"], "UTF-8, ISO-8859-1");
		$this->handleWindows1251($row["mail_body"], $enc);
		if ($enc == "Windows-1252") {
			$row["mail_body"] = @mb_convert_encoding($row["mail_body"], "UTF-8", $enc);
		} else {
			$row["mail_body"] = $conversion->str2utf8($row["mail_body"]);
		}
	}

	$row["attachments"] = $this->attachments_list($id, 1);
	/* scan for vcards */
	foreach ($row["attachments"] as $att) {
		if ($att["subtype"] == "vcard") {
			$row["vcard"] = $att["id"];
		}
	}

	$row["attachments_count"] = count($row["attachments"]);
	$list = array();
	foreach ($row["attachments"] as $k=>$v) {
		$list[]=$v["id"];
	}
	$row["attachments_ids"] = implode(",", $list);

	$row["clean_emailaddress"]  = $this->cleanAddress($row["sender_emailaddress"]);
	if ($row["sender_emailaddress"]=="INVALID_ADDRESS@.SYNTAX-ERROR") {
		$row["sender_emailaddress"] = gettext("geen afzender");
	}

	$row["subject"]               = $this->decodeMimeString($row["subject"]);
	$row["sender_emailaddress"]   = $this->decodeMimeString($row["sender_emailaddress"]);
	$row["to"]                    = $this->decodeMimeString($row["to"]);

	if (!$row["user_id"]) {
		$row["user_id"] = $_SESSION["user_id"];
	}

	if (is_numeric($row["sender_emailaddress"])) {
		$row["sender_emailaddress_h"] = "--";
	} else {
		$row["sender_emailaddress_h"] = str_replace(array("<",">"), array("&lt;", "&gt;"), $row["sender_emailaddress"]);
	}

	$row["to"]  = $this->cleanAddress($row["to"]);
	$row["cc"]  = $this->cleanAddress($row["cc"]);
	$row["bcc"] = $this->cleanAddress($row["bcc"]);

	$row["h_to"] = str_replace(",", ",<br>", $row["to"]);
	$row["h_cc"] = str_replace(",", ",<br>", $row["cc"]);
	$row["h_bcc"] = str_replace(",", ",<br>", $row["bcc"]);

	$row["h_date"]          = strftime("%d-%m-%Y %H:%m", $row["date"]);
	if ($row["date_received"]) {
		$row["h_date_received"] = strftime("%d-%m-%Y %H:%m", $row["date_received"]);
	}
	$row["is_text"]         = (int)$row["is_text"];

	$row["body"]   =& $row["mail_body"];
	$row["header"] =& $row["mail_header"];

	if ($row["askwichrel"] && $this->validateEmail($row["sender_emailaddress"])) {
		$row["check_askwichrel"] = 1;
	}

	if (!$row["is_text"]) {

		/* try to fix buggy email clients like outlook */
		$row["body"] = preg_replace("/ src=(cid:[^( |>)])*?( |>)/si", "src=\"$1\"$2", $row["body"]);

		/* scan for inline images */
		preg_match_all("/ src=('|\")(cid:[^('|\")]*?)('|\")/si", $row["body"], $matches);
		foreach ($matches[2] as $k=>$v) {
			$v = preg_replace("/^cid:/si", "", $v);

			$att_id = $this->getAttachmentIdByCid($row["id"], $v);
			if ($att_id) {
				$repl = $GLOBALS["covide"]->webroot."?mod=email&action=download_attachment&id=".$att_id;
				$row["body"] = str_replace($matches[0][$k], " clsid=\"cid:$v\" src=".$matches[1][$k].$repl.$matches[3][$k], $row["body"]);
			}
		}

		/* save full blown html version */
		$row["body_html"] = $row["body"];

		/* save a downgraded version for plain text */
		$row["body"] = $this->html2Text($row["body"]);
		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"]))
			$row["body"]=wordwrap($row["body"], 120);

		$row["body_pre"] = preg_replace("/<br[^>]*?>/si", "\n", $row["body"]);
		$row["body_pre"] = str_replace("<", "&lt;", $row["body_pre"]);
		$row["body_pre"] = "<PRE WRAP>".$row["body_pre"]."</PRE>";
	} else {
		/* save text version for browser */
		$row["body"] = $row["body"];
		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"]))
			$row["body"]=wordwrap($row["body"], 120);
		$row["body_pre"] = "<PRE WRAP>".str_replace("\r", "", htmlspecialchars($row["body"])."</PRE>");
	}


	/* remove windows line feeds */
	$row["body"] = str_replace("\r", "", $row["body"]);

	if ($this->get_current_tracker_item($row["id"])) {
		$row["tracking"] = 1;
	}

	/* public or private */
	if ($row["is_public"]==2) {
		$row["is_public_i"] = "state_private";
		$row["is_public_h"] = gettext("deze email is prive");
	} else {
		$row["is_public_i"] = "state_public";
		$row["is_public_h"] = gettext("deze email is publiek");
	}


	/* append thedetailed header to the d ata array */
	$header = $this->parseEmailHeader($row["header"]);

	/* append mail tracking items to the data array */
	$q = sprintf("select count(*) from mail_tracking where mail_id = %d or mail_id_2 = %d", $id, $id);
	$res = sql_query($q);
	if (sql_result($res,0)>0) {
		$header["mail_tracking"] = 1;
	}

	$options = $this->decodeMailOptions($row["options"]);

	$data[0] = array_merge($row, $header, $options);

?>
