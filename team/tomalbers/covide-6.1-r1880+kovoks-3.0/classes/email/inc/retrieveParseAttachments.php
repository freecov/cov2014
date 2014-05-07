<?php
	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}
	/* get the attachment store */
	$xmail    =& $this->xmail;

	/* mailbox */
	$mbox     =& $this->mbox;

	/* message body */
	$body     =& $this->data["body"];


	/* inline attachments */
	$inl_data =& $this->data["inline_data"];
	$inl_name =& $this->data["inline_name"];

	/* fetch the complete message structure */
	$obj = imap_fetchstructure($mbox, $imap_id);

	/* start procedure for retrieving attachments */
	$this->mime_scan($obj, $mbox, $imap_id);

	/* last record is not used, option base zero */
	$xmail->p = (int)($xmail->p-1);

	//now a customized part
	//inline attachments or attachments with only one part are set into the object as already fetched data
	for ($z=0; $z < count($inl_data); $z++) {
		$p = $xmail->p + 1;
		$xmail->data[$p]      = $inl_data[$z];
		$xmail->type[$p]      = "application";	#default
		$xmail->subtype[$p]   = "octet-stream";	#default
		$xmail->fetchdata[$p] = 0;	#mark attachments as data already fetched into object
		$xmail->name[$p]      = trim($inl_name[$z]);
		$xmail->uuenc[$p]     = 1;
		$xmail->p++;
	}

	unset($inl_name);
	unset($inl_data);

	//prepare binary data attachments in main attachment
	for ($z=0; $z <= $xmail->p; $z++){

		//if mail is html email
		if ($xmail->htmlMail[$z]==1){

				$body = str_replace("\n|\r|\r\n","",$xmail->data[$z]);
				//set back the data
				$xmail->data[$z] = addslashes($body);//addslashes for database
				unset($body);

		}elseif ($xmail->uuenc[$z]==1){
			$xmail->type[$z] = strtoupper($xmail->type[$z]."/".$xmail->subtype[$z]);

		}else{
			$xmail->type[$z] = strtoupper($this->mime_enc[$xmail->type[$z]])."/".$xmail->subtype[$z];
			$xmail->enc[$z] = $this->trans_enc[$xmail->enc[$z]];
			//decoding moved to be done later, see mail attachments insertion into database
		}
	}

	//for each attachment
	for ($x=0;$x<=$xmail->p;$x++){
		//write into db
		if (!$xmail->name[$x]){
			$xmail->name[$x] = sprintf("[%s].dat", gettext("geen naam"));
		}

		//if attachment is over-sized ($size_limit)
		if ($xmail->size[$x]>$this->size_limit) {
			//$xmail->data[$x] = sprintf("<html><body><BR><BR>bestandsnaam: ".$xmail->name[$x]."<BR>bestandsgrootte: ".($xmail->size[$x])." bytes<BR>limiet: ".($size_limit)." bytes</BODY></HTML>";
			$buf = "<html><body>";
			$buf.= sprintf("%s<br>", gettext("Dit bestand was groter dan het limiet en is daarom verwijderd."));
			$buf.= sprintf("%s: %s<br>", gettext("bestandsnaam"), $xmail->name[$x]);
			$buf.= sprintf("%s: %s %s<br>", gettext("bestandsgrootte"), $xmail->size[$x], gettext("bytes"));
			$buf.= sprintf("%s: %s %s<br>", gettext("limiet"), $this->size_limit, gettext("bytes"));
			$buf.= "</body></html>";
			$xmail->data[$x]      =& $buf;
			$xmail->name[$x]      =  sprintf("%s: [%s].htm", gettext("verwijderd"), $xmail->name[$x]);
			$xmail->fetchData[$x] =  0;
			$xmail->size[$x]      =  strlen($xmail->data[$x])*8;
			$xmail->type[$x]      =  "TEXT/HTML";
		}

		/* now fetch the data (if not already done) */
		if ($xmail->fetchData[$x]==1) {

			/* fetch the complete binary data part */
			$fx = imap_fetchbody($mbox, $imap_id, $xmail->part[$x]);

			/* use UUdecode to decode any encoded data */
			$xmail->data[$x] = $this->UUdecodeMailAtt( $fx , $xmail->type[$x], $xmail->enc[$x]);

			/* calculate the approx. size */
			$xmail->size[$x] = strlen($xmail->data[$x]);

			/* if the encoding is quoted printable after decoding */
			if (strtolower($xmail->enc[$x]) == "quoted-printable") {
				$xmail->data[$x] = str_replace("\r", "", $xmail->data[$x]); //soft-line-break

				/* dot stuffing is not supported in linux smtp */
				$xmail->data[$x] = preg_replace("/(^\.{2})|(\.{2}$)/s", ".", $xmail->data[$x]);
				$xmail->data[$x] = preg_replace("/\n\.{2}/s", "\n.", $xmail->data[$x]);

				/* now decode */
				$xmail->data[$x] = quoted_printable_decode($xmail->data[$x]);
			}

			/* unset the temp var */
			unset($fx);

		}	elseif ($xmail->uuenc[$x]==1) {

			/* if data is already fetched */
			/* for uu-encoded files */
			$xmail->data[$x] = $this->UUdecodeMailAtt( $xmail->data[$x] , $xmail->type[$x], $xmail->enc[$x], $xmail->uuenc[$x] );

			/* calculate the size */
			$xmail->size[$x] = strlen($xmail->data[$x]);
		}

		if (preg_match("/^\?UTF-8\?/si", $xmail->name[$x])) {
			if (!$conversion) $conversion = new Layout_conversion();
			$xmail->data[$x] = $conversion->utf8_convert($xmail->data[$x]);
		}

		/* dangerous extension filter, pack em away into a zip (for windows users) ;) */
		if (preg_match("'\.((exe)|(scr)|(bat)|(vbs)|(pif)|(com)|(dll)|(js))$'si", $xmail->name[$x])) {

			$zipfile = new Covide_zipfile();

			// add the subdirectory ... important!
			$zipfile->add_dir("blocked/");

			$name = urlencode( $this->decodeMimeString( $xmail->name[$x]) );
			$orig_name = $name;

			$zipfile->add_file(&$xmail->data[$x], sprintf("blocked/%s", $name));

			$xmail->name[$x] = sprintf("[blocked]-[%s].zip", $orig_name);
			$xmail->data[$x] = $zipfile->file();

			/* free memory */
			unset($zipfile);
			$xmail->size[$x] = strlen($xmail->data[$x]);
			$xmail->type[$x] = "application/octec-stream";
		}

		/* now insert the attachment into the database */
		$q = "INSERT INTO mail_attachments (message_id, name, type, size, cid) values ";
		$q.= sprintf("(%d, '%s', '%s', '%s', '%s')",
			$mail_id,
			addslashes($xmail->name[$x]),
			$xmail->type[$x],
			$xmail->size[$x],
			$xmail->cid[$x]
		);
		sql_query($q);
		$attachment_id = sql_insert_id("mail_attachments");

		/* and write data on the disc */
		$fspath = $GLOBALS["covide"]->filesyspath;

		if (!$fsdata) $fsdata = new Filesys_data();
		$ext = $fsdata->get_extension($xmail->name[$x]);
		$mijnFile = ($fspath."/email/".$attachment_id.".".$ext);

		$fp = fopen($mijnFile,"w");
		fwrite($fp, $xmail->data[$x]);
		unset ($fp);

		/* free the used memory, do not forget! :) */
		unset($xmail->data[$x]);

		$this->markup(" - attachment done -", "navy");

	}

?>
