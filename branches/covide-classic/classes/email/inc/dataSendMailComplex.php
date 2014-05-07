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

	$data = $this->getEmailById($id);
	$mdata =& $data[0];
	$part_body 	= array();

	//we don't do session changes anymore here, speed up the other windows
	session_write_close();

	//translate semi-colon to comma
	$mdata["to"]  = str_replace(";",",",$mdata["to"]);
	$mdata["cc"]  = str_replace(";",",",$mdata["cc"]);
	$mdata["bcc"] = str_replace(";",",",$mdata["bcc"]);

	// get users default bcc address
	$user_data = new User_data();
	$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
	if ($user_info["mail_default_bcc"]) {
		$mdata["bcc"] .= ",".$user_info["mail_default_bcc"];
		$mdata["bcc"] = preg_replace("/,{2,}/", ",", $mdata["bcc"]);
	}

	//html entities decode
	$mdata["body"] = preg_replace("/\&\#(\d{1,3});/e","chr($1)", $mdata["body"]);
	//$mdata["body"] = str_replace(chr(194),"&euro;", $mdata["body"]);

	/* if html */
	if (!$mdata["is_text"]) {
		/* ************************** */
		/* some html specific parsing */
		/* ************************** */

		/* parse inline images */
		//new cid
		$mdata["body_html"] = preg_replace("/src='([^']*?)'/si","src=\"$1\"", $mdata["body_html"]);
		preg_match_all("'src=\"[^(\")]*?newcid[^(\")]*?\"'si", $mdata["body_html"], $retarray);

		foreach ($retarray[0] as $rr) {
			$att = preg_replace("'^.*newcid=(\d{1,}).*$'si","$1",$rr);
			$q = "select cid from mail_attachments where id = ".$att;
			$res = sql_query($q);
			$curcid = sql_result($res,0);
			if (preg_match("/<.*>/s", $curcid)) {
				$newcid = $curcid;
			} else {
				$newcid = "<".strtoupper(md5(uniqid(time())))."@localhost>";
			}
			$q = "update mail_attachments set cid = '$newcid' where id = $att";
			sql_query($q);

			$newcid = str_replace("<","cid:",$newcid);
			$newcid = str_replace(">","",$newcid);
			$mdata["body_html"] = str_replace($rr,"src=\"".$newcid."\" ", $mdata["body_html"]);
		}
		unset($retarray[0]);

		//existing cid - img tag
		preg_match_all("'<img[^>]*?clsid[^>]*?>'si", $mdata["body_html"], $retarray);
		foreach ($retarray[0] as $rr) {
			$rnew = preg_replace("'src=\"[^(\")]*?\"'si"," ",$rr);
			$rnew = str_replace("clsid","src",$rnew);
			$rnew = str_replace("src= ","src=",$rnew);
			$mdata["body_html"] = str_replace($rr, $rnew, $mdata["body_html"]);
		}
		unset($retarray);

		//existing cid - td tag
		preg_match_all("'<td[^>]*?clsid[^>]*?>'si", $mdata["body_html"], $retarray);
		foreach ($retarray[0] as $rr) {
			$rnew = str_replace("background= ","background=",$rr);
			$rnew = preg_replace("'background=\"[^(\")]*?\"'si"," ",$rnew);
			$rnew = str_replace("clsid","background",$rnew);
			$mdata["body_html"] = str_replace($rr, $rnew, $mdata["body_html"]);
		}
		unset($retarray);

		/* if templates are enabled with tracking */
		if ($mdata["template"] && $mdata["template_type"]=="tracking") {
			//add tracking urls and stuff
			$trackid = md5(rand().session_id());

			$mdata["body_html"] = preg_replace("/ href='([^']*?)'/si"," href=\"$1\"", $mdata["body_html"]);
			preg_match_all("/ href=\"[^\"]*?\"/si", $mdata["body_html"], $matches);

			/* we only use non-encrypted http to use tracking */
			$server_url = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"])."/";

			foreach ($matches[0] as $k=>$v) {
				$new = preg_replace("/^href=\"/si","",trim($v));
				$new = preg_replace("/\"$/s","",$new);
				$new = str_replace("/",",,",$new);
				$new = str_replace(".", "%2E", $new);

				$repl = " href=\"".$server_url."showafb.php?contentlink=##mailcode##|".urlencode($new)."|##trackerid##\" ";
				$mdata["body_html"] = str_replace($v, $repl, $mdata["body_html"]);
			}
		}
		/* update the styles used in the email */
		$mdata["body_html"] = $this->stylehtml($mdata["body_html"], 0, $mdata["id"]);

		/* update the downgraded text version of the html part */
		$mdata["body"] = $this->html2text($mdata["body_html"]);
		
		/* convert &10# to a newline, as it should be */
		$mdata['body'] = str_replace('&#10;', "\n", $mdata['body']);

	}
	/* get sender information */
	if (!$skip_sender_rewrite) {
		if ($mdata["sender_emailaddress"] > 0) {
			$alias = $this->getEmailAliasById($mdata["sender_emailaddress"]);
		} else {
			$user = new User_data();
			$alias = $user->getEmployeedetailsById($_SESSION["user_id"]);
			if ($mdata["sender_emailaddress"]==-1) {
				$alias["email"] = $alias["mail_email"];
			} else {
				$alias["email"] = $alias["mail_email1"];
			}
		}
	} else {
		$alias["email"]    = $mdata["sender_emailaddress"];
		$alias["realname"] = $mdata["sender_emailaddress"];
	}

	//put all header parts in an array
	$part_body["From_name"]    = $this->mime_encode($alias["realname"]);
	$part_body["From_mail"]    = $alias["email"];
	$part_body["Organisation"] = $alias["companyname"];
	$part_body["Read_confirm"] = (int)$mdata["readconfirm"];
	$part_body["Priority"]     = (int)$mdata["priority"];
	$part_body["IsHtml"]       = ($mdata["is_text"]) ? 0 : 1;
	$part_body["template"]      = $mdata["template"];
	$part_body["template_type"] = $mdata["template_type"];
	$part_body["template_font"] = $mdata["template_font"];
	$part_body["template_size"] = $mdata["template_size"];
	$part_body["template_cmnt"] = $mdata["template_cmnt"];

	$part_body["newsletter_target"] = $mdata["classifications_target"];

	$senderemail = $alias["email"];

	//if this is a newsletter addresses will be read when sending.
	//if not a newsletter, we already have the to, cc and bcc values

	$mdata["to"]  = trim($mdata["to"]);
	$mdata["cc"]  = trim($mdata["cc"]);
	$mdata["bcc"] = trim($mdata["bcc"]);

	$mdata["to"]  = preg_replace("/,$/s", "", $mdata["to"]);
	$mdata["cc"]  = preg_replace("/,$/s", "", $mdata["cc"]);
	$mdata["bcc"] = preg_replace("/,$/s", "", $mdata["bcc"]);

	$part_body["To"]  = $mdata["to"];
	$part_body["Cc"]  = $mdata["cc"];
	$part_body["Bcc"] = $mdata["bcc"];

	$part_body["Subject"]   = $this->mime_encode($mdata["subject"]);
	$part_body["Body"]      = $mdata["body"];
	$part_body["Body_html"] = $mdata["body_html"];


	/* check for newsletter */
	$_newsletter = 0;
	// XXX: do not use REQUEST inside data objects!
	$newsletter = $this->get_tracker_items($_REQUEST["id"]);

	if ($newsletter["count"] > 0) {
		$_newsletter = 1;
		$part_body["To"] = "";

	} elseif ($mdata["template_type"]=="tracking") {
		/* if tracking and this is not a newsletter */
		/* insert an single tracker item into the database with state 2 */
		// XXX: do not use REQUEST inside data objects!
		$hash = $this->generate_message_id( sprintf("%s@localhost", $_REQUEST["id"]) );

		$q = sprintf("insert into mail_tracking (mail_id, email, is_sent, mailcode) values (%d, '%s', %d, '%s')",
			$_REQUEST["id"], $part_body["To"], 2, $hash);
		sql_query($q);
	}
	$generate = new Email_generate();

	/* retrieve attachment list */
	$attIds = array();
	foreach ( $this->attachments_list($id) as $k=>$v ) {
		$attIds[]=$v["id"];
	}

	if ($mdata["template"]) {
		$part_body["template-cid"] = $generate->parseTemplate(&$part_body, &$mdata);
	}

	$gen = "";
	$gen = $generate->generateMail(&$part_body, $attIds);

	/* send the mail */
	if ($_newsletter) {
		/* send to newsletter background queue */
		// XXX: do not use REQUEST inside data objects!
		$gen = $generate->sendMail2BackgroundSmtp(&$gen, &$part_body, &$newsletter, $_REQUEST["id"]);
	} else {
		$gen = $generate->sendMail2Smtp(&$gen, &$part_body, $hash);
	}

	/* move the mail to 'send items' */
	$folder  = $this->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
	$archive = $this->getSpecialFolder("Archief", 0);

	/* sync the mail body contents */
	if (!$mdata["is_text"]) {
		$q = sprintf("update mail_messages_data set body = '%s' where mail_id = %d", addslashes($mdata["body_html"]), $mdata["id"]);
		sql_query($q);
	}
	
	if ($mdata["address_id"]) {
		/* move email to relation archive */
		$q = sprintf("update mail_messages set sender_emailaddress = '%s', is_new = 0, folder_id = %d where id = %d", $senderemail, $archive["id"], $mdata["id"]);
		sql_query($q);

		/* save a copy in sent-items */
		$this->userCopy($mdata["id"], $_SESSION["user_id"], $folder["id"], 1);

	} else {
		/* no relation, just move to sent-items */
		$q = sprintf("update mail_messages set sender_emailaddress = '%s', is_new = 0, folder_id = %d where id = %d", $senderemail, $folder["id"], $mdata["id"]);
		sql_query($q);
	}
	$status = gettext("Your email has been sent.");

	/* if campaign actions are involved */
	if ($mdata["campaign_id"]) {

		$sql = sprintf("UPDATE campaign_records SET is_called=1, answer=4, email_id=%d, user_id=%d WHERE id=%d", $mdata["id"], $_SESSION["user_id"], $mdata["campaign_id"]);
		$res = sql_query($sql);
		$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					opener.document.getElementById('options4').checked = true;
					opener.document.getElementById('velden').submit();
					window.close();
				");
			$output->end_javascript();
		$output->exit_buffer();
	}
	
	if (!$return) {
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("
				try {
					if (opener.document.getElementById('velden').mod.value == 'email' && !opener.document.getElementById('velden').action.value) {
						opener.document.getElementById('velden').submit();
					}
				} catch(e) {
					var err = 1;
				}
				window.close();
			");
		$output->end_javascript();
		$output->exit_buffer();
	}
?>
