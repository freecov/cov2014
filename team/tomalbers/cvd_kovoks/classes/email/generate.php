<?php
/**
 * Covide Groupware-CRM Email Generation module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Email_generate Extends Email_data {

	/* constants */
	const include_dir = "classes/email/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name = "email_generate";

	/* variables */
	// -- none
	private $cr = "\n";

	/* methods */
	//}}}----------------------------------------------------------------------
	//{{{ moveTo2Cc: put multiple receipients in cc field and leave first as to
	//-------------------------------------------------------------------------
	private function moveTo2Cc($to, $cc) {
		$xto = explode(",",$to);
		if (strstr($cc,"@")) {
			$xcc = explode(",",$cc);
		} else {
			$xcc = array();
		}
		$to = $xto[0];
		unset	($xto[0]);
		foreach ($xto as $v) {
			$xcc[]=$v;
		}
		$cc = implode(", ",$xcc);
		return true;
	}



	//}}}--------------------------------------------------------
	//{{{ generateBoundary: generate boundary for multipart mails
	//-----------------------------------------------------------
	private function generateBoundary() {
		return "------------".$this->generateRandInt(24);
	}

	//}}}-----------------------------------------------------
	//{{{ generateMail: generate the mailstructure conform rfc
	//--------------------------------------------------------
	public function generateMail($body, $attIds=array()) {
		$vernr = $GLOBALS["covide"]->vernr;

		//pregenerate some boundaries for future use
		$body["boundary0"] = $this->generateBoundary();
		$body["boundary1"] = $this->generateBoundary();
		$body["boundary2"] = $this->generateBoundary();

		//timestamp = msg_id
		$body["timestamp"] = ( strtoupper(dechex(mktime())).".".$this->generateRandInt(7)."@".preg_replace("/^([^\@]*?\@)/i","",$body["From_mail"]) );
		//generate date field
		setlocale (LC_ALL, "en_US");
		if (strftime("%Z")=="CET") {
			$tz = "+0100";
		} else {
			$tz = "+0200";
		}
		$datum = strftime("%a, %d %b %Y %H:%M:%S")." ".$tz;

		setlocale(LC_ALL, $_SESSION["locale"]);
		setlocale(LC_NUMERIC, "en_US"); //always use . as decimal
		setlocale(LC_MONETARY, "C"); //C system locale

		$Xagent = getenv("HTTP_USER_AGENT");

		//set global headers
		$header = "";

		$header.= "Return-Path: <".$body["From_mail"].">\n";
		$header.= "Message-ID: <".$body["timestamp"].">\n";

		if ($body["Read_confirm"]==1) {
			//generate "please send read confirmation message" header
			$header.= "Disposition-Notification-To: <".$body["From_mail"].">\n";
		}
		$header.= "Date: ".$datum."\n";
		$header.= "From: \"".$body["From_name"]."\" <".$body["From_mail"].">\n";
		$header.= "Reply-To: ".$body["From_mail"]."\n";
		$header.= "Organisation: ".$body["Organisation"]."\n";

		$header.= "User-Agent: Covide Email v".$vernr."\n";
		$header.= "X-Mailer: Covide Email\n";
		if ($body["Priority"]) {
			$header.= "X-Priority: ".$body["Priority"]."\n";
		}

		$header.= "X-Accept-Language: ".str_replace(",",", ",preg_replace("/;(.*)$/si","",$_SERVER["HTTP_ACCEPT_LANGUAGE"]))."\n";
		$header.= "MIME-Version: 1.0\n";

		if ($body["To"]) { $header.= "To: ".$this->parseAddress($body["To"])."\n"; }
		if ($body["Cc"]) { $header.= "Cc: ".$this->parseAddress($body["Cc"])."\n"; }

		$header.= "Subject: ".$body["Subject"]."\n";
		$header.= "X-Client-Browser: ".wordwrap(str_replace(";",",",$Xagent), 50, "\n ")."\n";
		//end global headers

		$covide = array();
		$covide["Headers"] = $header;
		$covide["Body_html"] =& $body["Body_html"];
		$covide["Body"]      =& $body["Body"];

		if ($body["IsHtml"]) {
			$covide["Body"] = preg_replace("/<br[^>]*?>/si", "\n", $covide["Body"]);
		}

		//temlate stuff
		$template = $body["Template"];

		//method to choose mail encoding type
		if ($body["IsHtml"]==0) {
			//txt body
			if (!$attIds && !$template) {
				$method = "text";
				/*	plain text
				*  type: text/plain
				*	no parts
				*/
			} else {
				$method = "text/att";
				/*	plain text with attachment
				*  type: multipart/mixed
				*	single/multipart - 1 boundary
				*/
			}
		} else {
			//html body
			//count attachments, both attached as inline
			if ($attIds) {
				$attStr = "0,".implode(",",$attIds);
				$q = "select count(id) from mail_attachments where id IN ($attStr) and cid like '<%>'";
				$res = sql_query($q);
				$cidstat[1] = sql_result($res,0);         //inline
				$cidstat[0] = count($attIds)-$cidstat[1]; //attached
			} else {
				$cidstat[0] = 0;
				$cidstat[1] = 0;
				$attIds=array();
			}
			if ($template) {
				//put template images inline
				$q = "select count(id) from mail_templates_bestanden where template_id = $template";
				$res = sql_query($q);
				$cidstat[1] += sql_result($res,0);
			}

			if (!$attIds && !$template) {
				$method = "html";	//no attachments, so we can send it as plain html
			} else {
				//attachments, so we need to find out if only attached or also inline
				if ($cidstat[1]>0 && $cidstat[0]>0) {
					$method = "html/att/inl"; //both inline and attached
				} elseif ($cidstat[1]>0) {
					$method = "html/inl";     //inline only
				} else {
					$method = "html/att";     //attached only
				}
			}
		}

		//generate the mail
		switch ($method){
		case "text":
			$header.= "Content-Type: text/plain; charset=UTF-8\n";
			$header.= "Content-Transfer-Encoding: ".$this->header_enc($body["Body"])."\n\n";
			$header.= $this->text_enc($body["Body"], $max_wrap);
			$header.= "\n\n";
			break;
		case "text/att":
			$header.= "Content-Type: multipart/mixed;\n";
			$header.= "\tboundary=\"".$body["boundary0"]."\"\n";
			$header.= "\n";
			$header.= "This is a multi-part message in MIME format.\n\n";

			$header.= "--".$body["boundary0"]."\n";
			$header.= "Content-Type: text/plain; charset=UTF-8\n";
			$header.= "Content-Transfer-Encoding: ".$this->header_enc($body["Body"])."\n\n";
			$header.= $this->text_enc($body["Body"]);
			$header.= "\n\n";
			$header.= $this->insertAtt($attIds, $body["boundary0"]);
			$header.= "--".$body["boundary0"]."--\n\n";
			break;
		case "html":
			$header.= "Content-Type: multipart/alternative;\n";
			$header.= " boundary=\"".$body["boundary0"]."\"\n";
			$header.= "\n";
			$header.= "This is a multi-part message in MIME format.\n\n";

			//text part
			$header.= "--".$body["boundary0"]."\n";
			$header.= "Content-Type: text/plain; charset=UTF-8\n";
			$header.= "Content-Transfer-Encoding: ".$this->header_enc($body["Body"])."\n\n";
			$header.= $this->text_enc( $this->add_text_warning( $body["Body"]) );
			$header.= "\n\n";

			//html part
			$header.= "--".$body["boundary0"]."\n";
			$header.= "Content-Type: text/html; charset=UTF-8\n";
			$header.= "Content-Transfer-Encoding: ".$this->header_enc($body["Body_html"])."\n\n";
			$header.= $this->text_enc($body["Body_html"]);
			$header.= "\n\n";
			$header.= "--".$body["boundary0"]."--\n\n";
			break;
		case "html/inl":
			//html with inline attachments
			$header.= "Content-Type: multipart/related;\n";
			$header.= " type=\"multipart/alternative\";\n";
			$header.= " boundary=\"".$body["boundary0"]."\"\n";
			$header.= "\n";
			$header.= "This is a multi-part message in MIME format.\n";

			$header.= $this->insertAlternative($body["Body"], $body["Body_html"], $body["boundary0"], $body["boundary1"], $max_wrap);

			$header.= $this->insertTemplateAtt($template, $body["boundary0"], $body["Template-Cid"]);
			$header.= $this->insertAtt($attIds, $body["boundary0"],1);
			$header.= "--".$body["boundary0"]."--\n\n";
			break;
		case "html/att":
			//nested attachments *sigh*
			$header.= "Content-Type: multipart/mixed;\n";
			$header.= " boundary=\"".$body["boundary0"]."\"\n";
			$header.= "\n";
			$header.= "This is a multi-part message in MIME format.\n\n";

			$header.= $this->insertAlternative($body["Body"], $body["Body_html"], $body["boundary0"], $body["boundary1"], $max_wrap);

			$header.= $this->insertAtt($attIds, $body["boundary0"]);
			$header.= "--".$body["boundary0"]."--\n\n";
			break;
		case "html/att/inl":
			//most difficult and messy. needed 3 boundaries and mixed encodings.
			//headers with mixed conten - 1st boundary
			$header.= "Content-Type: multipart/mixed;\n";
			$header.= " boundary=\"".$body["boundary0"]."\"\n";
			$header.= "\n";
			$header.= "This is a multi-part message in MIME format.\n\n";

			//multipart/related - 2nd boundary
			$header.= "--".$body["boundary0"]."\n";
			$header.= "Content-Type: multipart/related;\n";
			$header.= " type=\"multipart/alternative\";\n";
			$header.= " boundary=\"".$body["boundary1"]."\"\n";
			$header.= "\n";

			//inline-inline attachment or mailbody. 3rd boundary
			$header.= $this->insertAlternative($body["Body"], $body["Body_html"], $body["boundary1"], $body["boundary2"], $max_wrap);

			$header.= $this->insertTemplateAtt($template, $body["boundary1"], $body["Template-Cid"]);
			$header.= $this->insertAtt($attIds, $body["boundary1"],1);
			$header.= "--".$body["boundary1"]."--\n\n";
			$header.= $this->insertAtt($attIds, $body["boundary0"]);
			$header.= "--".$body["boundary0"]."--\n\n";
			break;
		}

		$header = preg_replace("/\n/s", $this->cr ,$header);

		return $header;
	}

	//---------------------------------------------
	//{{{ insertAtt: add attachment to email struct
	//---------------------------------------------
	private function insertAtt($attIds, $boundary, $inline=0) {
		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdata = new Filesys_data();
		$dir = sprintf("%s/email", $fspath);

		$header = "";
		foreach ($attIds as $att) {
			$result2 = sql_query(sprintf("SELECT * FROM mail_attachments WHERE id = %d", $att));
			$row2 = sql_fetch_array($result2);

			$ext = $fsdata->get_extension($row2["name"]);

			$source_file = sprintf("%s/%d.%s", $dir, $row2["id"], $ext);
			$mime = $fsdata->detectMimetype($source_file);

			/* fallback for some undetectable documents */
			if (!$mime) {
				switch ($ext) {
					case "doc":
						$mime = "application/vnd.ms-word";
						break;
					case "xls":
						$mime = "application/vnd.ms-excel";
						break;
					case "odt":
					case "sxw":
						$mime = "application/vnd.oasis.opendocument.text";
						break;
					case "txt":
						$mime = "text/plain";
						break;
					default:
						$mime = "application/octet-stream";
						break;
				}
			}

			// now for the attachement
			$header .= "--" . $boundary . "\n";
			$header .= "Content-Type: ".strtolower($mime).";\n\tname=\"".$row2["name"]."\"\n";
			$header .= "Content-Transfer-Encoding: base64\n";
			if ($inline) {
				$header .= "Content-ID: ".$row2["cid"]."\n\n";
			} else {
				$header .= "Content-Disposition: attachment;\n\n";
			}

			//add the binary data.
			$ext = $fsdata->get_extension($row2["name"]);
			$myFile = ($fspath."/email/".$att.".".$ext);
			if (!file_exists($myFile)) {
				echo("<font color=red>Critical: error reading file from disk [id $att] - not found!</font><br>");
				$contents = "\n\n";
			} else {
				$datafile = fopen($myFile,"r");
				$contents = (fread($datafile, filesize($myFile)));
				fclose($datafile);
			}
			$encoded_attach = chunk_split(base64_encode($contents));
			$header .= $encoded_attach."\n\n";
			unset($contents); //free memory
		}
		return ($header);
	}

	//}}}------------------------------------------------------------------------------------------------
	//{{{ insertTemplateAtt: add attachments that belong to template as inline attachment to email struct
	//---------------------------------------------------------------------------------------------------
	function insertTemplateAtt($template_id, $boundary, $cid) {
		if ($template_id) {
			$fspath = $GLOBALS["covide"]->filesyspath;

			$header = "";
			$q = sprintf("select * from mail_templates_bestanden where template_id = %d", $template_id);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2)) {
				// now for the attachement
				$header .= "--" . $boundary . "\n";
				$header .= "Content-Type: ".strtolower($row2["type"]).";\n";
				$header .= "\tname=\"".$row2["naam"]."\"\n";
				$header .= "Content-Transfer-Encoding: base64\n";
				$header .= "Content-ID: ".str_replace("#posdata#",$row2["pos"],$cid)."\n\n";

				// add binary data
				$mijnFile = ($fspath."/templates/".$row2["id"].".dat");

				if (!file_exists($mijnFile)) {
					echo("<font color=red>Critical: error reading *template* file from disk [id $att] - not found!</font><br>");
					$contents = "\n\n";
				} else {
					$datafile = fopen($mijnFile,"r");
					$contents = (fread($datafile, filesize($mijnFile)));
					fclose($datafile);
				}
				$encoded_attach = chunk_split(base64_encode($contents));
				$header .= $encoded_attach."\n\n";

				unset($contents); //free memory
			}
		}
		return ($header);
	}

	//}}}--------------------------------------------------------------------------------------------------------
	//{{{ insertAlternative: imagine, we want all 3 listed above as 1 attachment AND html part AND txt part
	// Dont blame us, blame the ppl who invented inline attachments with html email and still want plain txt part
	//-----------------------------------------------------------------------------------------------------------
	private function insertAlternative($body, $body_html, $boundary0, $boundary1, $max_wrap) {
		//related part - split naar multipart/alternative
		$header = "--".$boundary0."\n";
		$header.= "Content-Type: multipart/alternative;\n";
		$header.= " boundary=\"".$boundary1."\"\n\n\n";

		//text part
		$header.= "--".$boundary1."\n";
		$header.= "Content-Type: text/plain; charset=UTF-8\n";
		$header.= "Content-Transfer-Encoding: ".$this->header_enc($body)."\n\n";
		$header.= $this->text_enc( $this->add_text_warning($body) );
		$header.= "\n\n";

		//html part
		$header.= "--".$boundary1."\n";
		$header.= "Content-Type: text/html; charset=UTF-8\n";
		$header.= "Content-Transfer-Encoding: ".$this->header_enc($body_html)."\n\n";
		$header.= $this->text_enc($body_html);
		$header.= "\n\n";

		$header.= "--".$boundary1."--\n\n";

		return $header;
	}

	//}}}--------------------------------------------------------------------------------------
	//{{{ text_enc: encode and enforce any text type to max 850 characters, word-break friendly
	//-----------------------------------------------------------------------------------------
	private function text_enc($str) {
		if ($this->qprint_enc_required($str)) {
			//$str = imap_8bit($str);
			$str = str_replace("\r", "", $str);     //strip CR
			$str = str_replace("\n", "\r\n", $str); //replace LF by CRLF
			$str = $this->quoted_printable_encode($str);
		}
		return $str;
	}
	private function header_enc($str) {
		if ($this->qprint_enc_required($str)) {
			return "quoted-printable";
		} else {
			return "8bit";
		}
	}

	private function qprint_enc_required($str) {
		$line_limit = 850; //max is 991, this value should be safe

		/* if any character above 127 is detected */
		if (preg_match("/[^\x01-\x7F]/s", $str)) {
			$enc_required = 1;
		} else {
			$enc_required = 1;
		}
		/* or if line lenght > limit */
		if (!$enc_required) {
			$str = explode("\n", $str);
			foreach ($str as $line) {
				if (strlen($line) > $line_limit)
					$enc_required = 1;
			}
		}
		return $enc_required;
	}

	//}}}---------------------------------------------------
	//{{{ add_text_warning: add txt body to html only mails.
	//------------------------------------------------------
	private function add_text_warning($str) {
		$tmp = "This message contains HTML, but your client doesn't support this format.\n";
		$tmp.= "A plain text copy of the message is included below this line.\n\n";
		$tmp.= "*** begin message ***\n\n";
		return ($tmp.$str."\n");
	}

	//}}}-------------------------------------------------------------------------------
	//{{{ sendMail2Q: send mail in background using smtp class (only 1 or very few addresses)
	//----------------------------------------------------------------------------------
	public function sendMail2BackgroundSmtp(&$data, $opts, $list, $mail_id) {

		$output = new Layout_output();
		$output->layout_page();

		/* write the data to disc */
		$path = $GLOBALS["covide"]->temppath;

		$file_plain = "newsletter_".md5(rand()*mktime()).".mail";
		$file = $path.$file_plain;

		$out = fopen($file, "w");
		fwrite($out, $data);
		fclose($out);

		$venster = new Layout_venster( array(
			"title"    => gettext("Nieuwsbrief"),
			"subtitle" => gettext("versturen")
		));
		$venster->addVensterData();

			$venster->addTag("span", array("id"=>"newsletter_progressbar"));
				$venster->addCode("(".gettext("bezig met initialiseren")."...)" );
			$venster->endTag("span");

		$venster->endVensterData();

		/* detect resume */
		$mailData = new Email_data();
		$current = $mailData->detectResume($mail_id);

		$tbl = new Layout_table();
		$output->addCode( $tbl->createEmptyTable( $venster->generate_output() ) );

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "get",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "email");
		$output->addHiddenField("total", $list["count"]);
		$output->addHiddenField("mail_id", $mail_id);
		$output->addHiddenField("datafile", $file_plain);
		$output->addHiddenField("from", $opts["From_mail"]);
		$output->addHiddenField("newsletter_target", $opts["newsletter_target"]);

		$output->addHiddenField("current", $current);
		$output->endTag("form");

		$output->insertTag("iframe", "", array(
			"width"  => "600",
			"height" => "100",
			"src"    => "blank.htm",
			"id"     => "newsletter_sender",
			"frameborder" => 0,
			"style"  => "visibility: hidden;"
		));

		$output->load_javascript(self::include_dir_main."xmlhttp.js");
		$output->load_javascript(self::include_dir."sendqueue.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	//}}}-------------------------------------------------------------------------------
	//{{{ sendMail2Smtp: send mail directly using smtp class (only 1 or very few addresses)
	//----------------------------------------------------------------------------------
	public function sendMail2Smtp(&$data, $opts, $hash) {

		$address_data = new Address_data();

		if ($opts["template_cmnt"]) {

			//get first address
			$to = explode(",", $opts['To']);
			$rcpt = "";
			foreach ($to as $t) {
				$cmnt = $address_data->lookupRelationEmailCommencement(trim($t), "");
				if ($cmnt) {
					$rcpt[] = $cmnt;
				}
			}
			$cmnt = implode(", ", $rcpt).",";
			//$data = str_replace("##rcptcmnt##", $cmnt, $data);
		}

		if ($hash) {
			$this->insertTracking($data, $hash, $opts["To"], $cmnt);
		}

		$smtp = new Email_smtp();
		$smtp->add_rcpt($opts["To"]);
		$smtp->add_rcpt($opts["Cc"]);
		$smtp->add_rcpt($opts["Bcc"]);

		$smtp->set_data($data);
		$smtp->set_from($opts["From_mail"]);

		$ret = $smtp->send();
		unset($smtp);

		return $ret;
	}

	//parseTemplate: get the mail, put it in a table, and render template around it
	public function parseTemplate($part_body, $data) {

		$emailData = new Email_data();
		$template = $emailData->get_template_list($data["template"]);
		$template_files = $emailData->get_template_filelist($data["template"]);
		$tf = array();
		foreach ($template_files as $v) {
			$tf[$v["position"]] = $v["id"];
		}
		$template[0]["header"] = htmlentities($template[0]["header"], ENT_QUOTES, "UTF-8");
		$template[0]["footer"] = htmlentities($template[0]["footer"], ENT_QUOTES, "UTF-8");

		/* header */
		$header = sprintf("<font face='%s' size='%s'><i>%s</i></font>",
			$part_body["template_font"], $part_body["template_size"], $template[0]["header"]);

		/* footer */
		$footer = sprintf("<font face='%s' size='%s'><i>%s</i></font>",
			$part_body["template_font"], $part_body["template_size"], $template[0]["footer"]);

		/* if personal cmnt */
		if ($part_body["template_cmnt"]) {
			$_personal_cmnt = sprintf("<font face='%s' size='%s'>%s</font>",
				$part_body["template_font"], $part_body["template_size"], "##rcptcmnt##");
		}

		/* generate content-id */
		$cid = $emailData->generate_message_id();
		$cid = str_replace("@", "#posdata#@", $cid);
		$cid = preg_replace("'<|>'si","",$cid);

		$server_url = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"])."/";

		if ($data["template_type"]=="tracking") {
			/* if tracking is enabled, send some extra info to the server */
			$server_url_alt = $server_url."/showafb.php?attachment_id=";
			$server_url.= "showafb.php?content=##mailcode##|##trackerid##|";
		} else {
			$server_url.= "showafb.php?attachment_id=";
			$server_url_alt = $server_url;
		}

		switch ($data["template_type"]) {
			/* determine how we want to send the template data */
			case "inline":
				if ($tf["t"]) { $tf["t"]= "\"cid:".str_replace("#posdata#","t",$cid)."\""; }
				if ($tf["b"]) { $tf["b"]= "\"cid:".str_replace("#posdata#","b",$cid)."\""; }
				if ($tf["l"]) { $tf["l"]= "\"cid:".str_replace("#posdata#","l",$cid)."\""; }
				if ($tf["r"]) { $tf["r"]= "\"cid:".str_replace("#posdata#","r",$cid)."\""; }
				break;
			case "external":
			case "tracking":
				if ($tf["t"]) { $tf["t"]= "\"".$server_url.$tf["t"]."\""; }
				if ($tf["b"]) { $tf["b"]= "\"".$server_url_alt.$tf["b"]."\""; }
				if ($tf["l"]) { $tf["l"]= "\"".$server_url_alt.$tf["l"]."\""; }
				if ($tf["r"]) { $tf["r"]= "\"".$server_url_alt.$tf["r"]."\""; }
				break;
		}

		//prepend to mail data
		$_prefix.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$_prefix.= "<tr>\n";
		if ($tf["t"]) {
			//top image
			$_prefix.= "<td colspan=\"3\"><img src=".$tf["t"]."></td>\n";
		} else {
			$_prefix.= "<td colspan=\"3\">&nbsp;</td>\n";
		}
		$_prefix.= "</tr>\n";
		$_prefix.= "<tr>\n";
		if ($tf["l"]) {
			//left image
			$_prefix.= "<td valign=\"top\" style=\"background-repeat: repeat-y;\" background=".$tf["l"]." rowspan=\"3\" valign=\"top\">\n";
			$_prefix.= "<img src=".$tf["l"].">\n";
		} else {
			$_prefix.= "<td valign=\"top\" rowspan=\"2\" valign=\"top\">\n";
			$_prefix.= "&nbsp;";
		}
		$_prefix.= "</td>\n";
		$_prefix.= "<td style=\"padding: 2px 2px 2px 2px;\">".$header."<br><br>$_personal_cmnt</td>\n";
		if ($tf["r"]) {
			//right image
			$_prefix.= "<td valign=\"top\" align=\"right\" style=\"background-repeat: repeat-y; background-position:top right;\" background=".$tf["r"]." rowspan=\"3\" align=\"right\" valign=\"top\">\n";
			$_prefix.= "<img src=".$tf["r"].">";
		} else {
			$_prefix.= "<td style=\"padding: 2px 2px 2px 2px;\" valign=\"top\" align=\"right\" rowspan=\"2\" align=\"right\" valign=\"top\">\n";
			$_prefix.= "&nbsp;";
		}
		$_prefix.= "</td>\n";
		$_prefix.= "</tr>\n";
		$_prefix.= "<tr>";
		$_prefix.= "<td>\n";

		//append to mail data
		$_suffix = "</td>\n";
		$_suffix.= "</tr>\n";
		$_suffix.= "<tr>\n";
		$_suffix.= "<td style=\"padding: 2px 2px 2px 2px;\"><br><br>".$footer."</td>\n";
		$_suffix.= "</tr>\n";
		$_suffix.= "<tr>\n";
		$_suffix.= "<td style=\"b\" colspan=\"3\">\n";
		if ($tf["b"]) {
			//bottom image
			$_suffix.= "<img src=".$tf["b"].">\n";
		} else {
			$_suffix.= "&nbsp;\n";
		}
		$_suffix.= "</td>\n";
		$_suffix.= "</tr>\n";
		$_suffix.= "</table>\n";

		$part_body["Body_html"] = preg_replace("/(<body[^>]*?>)/si", "\n\n$1\n\n".$_prefix."\n\n", $part_body["Body_html"]);
		$part_body["Body_html"] = preg_replace("/(<\/body>)/si", $_suffix."\n\n$1", $part_body["Body_html"]);
	}
}
?>
