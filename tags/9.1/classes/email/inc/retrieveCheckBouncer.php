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

	$bounce = 0;
		/* exim style or most rfc */
	if (preg_match("/\nX-Failed-Recipients: /si",$header)) {
		$bounce = 1;
		$mailer = "exim/rfc";
	} elseif (preg_match("/\nX-MailerError: /si",$header) && preg_match("/X-MailerServer: XMail/si",$header)) {
		/* xmail style */
		$bounce = 1;
		$mailer = "xmail";
	} elseif (preg_match("/invoked for bounce/si",$hedder) && preg_match("/\Received: \(qmail/si",$hedder)) {
		/* qmail style */
		$bounce = 1;
		$mailer = "qmail";
	}

	if ($bounce){
		$data["is_text"] = 0; //set to text mail, coz we gonna strip them
		switch ($mailer){
			case "qmail":
				$tmp = preg_replace("/--- Below this line is a copy of the message.(.*)$/si","",$data["body"]);
				preg_match_all("/<[^>]*?>/si",$tmp,$failed);
				//qmail only support
				if ($failed[0][0]){
					$data["sender_emailaddress"] = preg_replace("/(>)|(<)/si","",$failed[0][0]);
					$data["sender"] = "Bounced by Qmail";
					$data["body"] = $tmp;
				}else{
					$data["sender"] = "Bounced by Qmail";
				}
				break;

			case "exim/rfc":
				$tmp = preg_replace("/------ This is a copy of the message, including all the headers. ------(.*)$/si","",$data["body"]);
				preg_match_all("/<[^>]*?>/si",$tmp,$failed);
				//exim and rfc with failed recipients
				//get the headers
				$tmphd = str_replace("\r","",$header);
				$tmphd = preg_replace("/(\n\t)|(\n )/si","",$tmphd);
				preg_match_all("/\nX-Failed-Recipients:([^\n]*?\n)/si",$tmphd, $failed);
				//exim only support
				if ($failed[0][0]){
					$failed[0][0] = trim ( preg_replace("/X-Failed-Recipients:/si","",$failed[0][0]) );

					//multiple users are possible - use the first one
					$failed[0][0] = preg_replace("/,(.*)$/s","",$failed[0][0]);

					$data["sender_emailaddress"] = preg_replace("/(>)|(<)/si","",$failed[0][0]);
					$data["sender"] = "Bounced by Exim";
					$data["body"] = $tmp;
				}else{
					$data["sender"] = "Bounced by Exim";
				}
				break;

			case "xmail":
				$tmp = preg_replace("/\[<05>\] Here is listed the initial part of the message:(.*)$/si","",$data["body"]);
				$tmp2 = preg_replace("/\[<01>\] Error sending message(.*)$/si","",$tmp);
				$tmp2 = str_replace("<00>","",$tmp2);

				preg_match_all("/<[^>]*?>/si",$tmp2,$failed);
				if ($failed[0][0]){
					$data["sender_emailaddress"] = preg_replace("/(>)|(<)/si","",$failed[0][0]);
					$data["sender"] = "Bounced by Xmail";
					$data["body"] = preg_replace("/>>$/s","",trim($tmp));
				}else{
					$data["sender"] = "Bounced by Xmail";
				}
				break;
		}
	}
?>