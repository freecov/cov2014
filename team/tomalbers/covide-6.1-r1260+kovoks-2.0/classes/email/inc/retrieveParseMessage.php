<?php
	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}
	$data   =& $this->data;
	$header =& $this->header;

	//get the sender
	$data["from"] = $header->from[0]->personal;
	if (!$data["from"]) {
		$data["from"] = $header->senderaddress;
	}
	//rcpt-to/cc field
	$data["to"] = $header->toaddress;
	$data["cc"] = $header->ccaddress;

	//sender email
	$data["sender_emailaddress"] = $header->fromaddress;

	//reply to
	$data["reply_to"] = $header->reply_toaddress;

	//reply-to can only have one email adress.
	$data["reply_to"] = preg_replace("/,(.*)$/si","",$data["reply_to"]);

	//subject
	$data["subject"] = $header->subject;
	if(!$data["subject"]) {
		$data["subject"] = "[".gettext("geen onderwerp")."]";
	}


	//content text and/or html
	$tmp_text = $this->get_part($this->mbox, $imap_id, "TEXT/PLAIN");
	$tmp_html = $this->get_part($this->mbox, $imap_id, "TEXT/HTML");

	$data["body"]      = $tmp_text["text"];
	$data["body_html"] = $tmp_html["text"];

	if (preg_match("/Content-Type: text\/html;/si", $data["body"]) && preg_match("/--$/s", trim($data["body"]))) {
		/* possible mismatch detected, we have a html mail (?) */
		$tmp = preg_replace("/^.*[^<html]*?<html/si", "<html", $data["body"]);
		$tmp = preg_replace("/<\/html>.*$/si", "", $tmp);

		if ($tmp) {
			$tmp = preg_replace("/(<body[^>]*?>)/si", "$1<center>An error was detected inside this email, the message may not display correctly.</center>", $tmp);
			$data["body"] = "";
			$data["body_html"] = $tmp;
			unset($tmp);
		}
	}

	if ($data["body"] && !$data["body_html"] && strpos(strtolower(imap_fetchheader($this->mbox, $imap_id)), "content-type: text/html")) {
		$data["body_html"] = $data["body"];
		$data["body"] = "";
	}

	if ($data["body_html"]) {
		$data["is_text"] = 0;
		$data["body"] = $data["body_html"];
		$data["mail_decoding"] = $tmp_html["enc"];
		$found_content = 1;
	} elseif ($data["body"]) {
		$data["is_text"] = 1;
		$data["mail_decoding"] = $tmp_text["enc"];
		$found_content = 1;
	} else {
		$found_content = 0;
	}
	//free some memory
	unset($data["body_html"]);

	$data["date"] = (int)$header->udate;
	$data["date_received"] = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));

	//fetch the complete header
	$header = imap_fetchheader($this->mbox, $imap_id);
	$data["header"] =& $header;

	/* some bounce checks */
	$bounce = $this->checkBouncer($header, $data);

	if ($bounce) {
		$data["folder_id"] = $this->folder["bounce"]["id"];
	} else {

		preg_match_all("/(^|\n)X-Covide-Folder: ([^\n]*?)\n/si", $header, $hmatches);
		$wanted_folder = trim( preg_replace("/[^a-z0-9 \-_]/si", "", $hmatches[2][0]) );
		unset($hmatches);

	 	/* lookup folder */
		if ($wanted_folder) {
	 		$data["folder_id"] = $this->user_folders_lookup[$wanted_folder];
			$data["mark_old"] = 1;
		}

		if (!$data["folder_id"])
			$data["folder_id"] = $this->folder["inbox"]["id"];
	}

	//now filter begin 666 code (apple?)
	preg_match_all("'begin 666.*[^end]*?end'si", $data["body"], $matches);
	$inl_data = $matches[0];
	unset($matches);

	for ($z=0;$z<count($inl_data);$z++) {

		$this->markup(" - inline attachment -", "blue");
		//extra \n on the end for uudecode (NIT uudeview)
		$inl_data[$z]=str_replace("\r","",$inl_data[$z]);
		$inl_data[$z].="\n";

		preg_match_all("'^begin 666 ([^\n]*?)\n's", $inl_data[$z], $submatches);
		$name = $submatches[0][0];
		$name = preg_replace("'^begin 666 's","",$name);
		$name = preg_replace("'\n$'s","",$name);
		unset($submatches);

		$inl_name[$z] = $name;
		unset($name);

		//do a stripslashes and remove all \r
		$inl_data[$z] = str_replace("\r","",stripslashes($inl_data[$z]) );
	}

	$data["inline_data"] =& $inl_data;
	$data["inline_name"] =& $inl_name;
	$data["is_bounce"] = $bounce;

	//erase parsed lines now, here we go ...
	$data["body"] = preg_replace("'begin 666.*[^end]*?end'si", "", $data["body"]);
	$data["status_pop"] = 1;
?>
