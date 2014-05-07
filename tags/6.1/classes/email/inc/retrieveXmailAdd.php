<?php
	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}
	/* pointer to global class var */
	$xmail =& $this->xmail;

	//We can find the name on 2 places. Make sure to check both before assuming no name
	if ($obj->dparameters[0]->value) {
		$fname = $obj->dparameters[0]->value;
	} elseif ($obj->parameters) {
		if ( (string)$obj->parameters == "Array") {
			$fname = $obj->parameters[0]->value;
		}
	}
	if (!$fname) {
		$fname = $defname;
	}

	if ($fname == "winmail.dat") {
		//winmail.dat is a mail structure. We need to unpack it first
		//After unpacking we process the content as attachments to the original mail

		//first retreive the data
		$fx = imap_fetchbody($stream, $mailnr, preg_replace("/.$/","",$part));
		//decode
		$data = $this->UUdecodeMailAtt($fx, $obj->type, $obj->encoding);
		if (strtolower($obj->enc) == "quoted-printable") {
			$data = quoted_printable_decode($data);
		}
		unset($fx);

		//then decode the data and retreive a file list
		$dir = $this->winmail_decode($data);
		$files = $this->listDir($dir);

		//now insert the files seperate into $xmail
		$subpart = 0;
		foreach ($files as $file) {

			$subpart++;
			$p = $xmail->p; //read current record

			$xmail->part[$p] 		= $part.$subpart;           //part nr
			$xmail->name[$p] 		= $file;                    //filename
			$xmail->type[$p] 		= 3;                        //binary file type
			$xmail->subtype[$p] = $this->detectMimeType($dir."/".$file);  //file "extension"
			$xmail->size[$p] 		= filesize($dir."/".$file); //size
			$xmail->enc[$p] 		= 5;                        //other type encoding
			$xmail->cid[$p] 		= "";                       //cid
			$xmail->disposition[$p]	= "attachment";         //att disposition

			$xmail->fetchData[$p] = 0; //fetch data now

			// get contents of a file into a string
			$filename = $dir."/".$file;
			$handle = fopen($filename, "r");
			$xmail->data[$p] = fread($handle, filesize($filename));
			fclose($handle);

			$xmail->p++; //next record
		}
		//remove temp space used for unpacking
		$this->delDir($dir);
	} else {

		$p = $xmail->p; //read current record

		$xmail->part[$p] 		= preg_replace("/.$/","",$part);	//part nr
		$xmail->name[$p] 		= $fname;												//filename
		$xmail->type[$p] 		= $obj->type;										//file type
		$xmail->subtype[$p] = $obj->subtype;								//file "extension"
		$xmail->size[$p] 		= $obj->bytes;									//size
		$xmail->enc[$p] 		= $obj->encoding;								//type encoding
		$xmail->cid[$p] 		= $obj->id;											//cid
		$xmail->disposition[$p]	= $obj->disposition;				//att disposition
		$xmail->fetchData[$p] = 1; //we still need to fetch the data. We do this as very last step
		$xmail->p++; //next record
	}
?>
