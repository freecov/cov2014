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
	if (preg_match("/\nX-Failed-Recipients: /si",$header)) {
		/* exim style or most rfc */
		$bounce = 1;
		$mailer = "exim/rfc";
	} elseif (preg_match("/\nX-MailerError: /si",$header) && preg_match("/X-MailerServer: XMail/si",$header)) {
		/* xmail style */
		$bounce = 1;
		$mailer = "xmail";
	} elseif (preg_match("/invoked for bounce/si",$header) && preg_match("/\Received: \(qmail/si",$header)) {
		/* qmail style */
		$bounce = 1;
		$mailer = "qmail";
	} elseif (stristr($header, "\nContent-Type: multipart/report; report-type=delivery-status;")) {
		/* postfix style */
		$bounce = 1;
		$mailer = "postfix";
	} else {
		/* misc, content, subject, sender style */
		// Match FROM
		if (stristr($data["sender_emailaddress"], "mailer-daemon@") || stristr($data["sender_emailaddress"], "postmaster@")) {
			$bounce = 1;
			$mailer = "SenderMatch";
		}
		// Account for non-bounce messages from the above senders
		if (stristr($data["subject"], "Delayed Mail (still being retried)")) {
			$bounce = 0;
		}
		// Match Subject
		if (stristr($data["subject"], "failure notice") || stristr($data["subject"], "Delivery Status Notification (Failure)") ||
			stristr($data["subject"], "Undelivered Mail Returned to Sender") || stristr($data["subject"], "Mail delivery failed: returning message to sender")) {
			$mailer = "SubjectMatch";
			$bounce = 1;
		}
	}
	if ($bounce) {
		$failed_address = "";
		$data["is_text"] = 1; //set to text mail, coz we gonna strip them
		switch ($mailer){
			case "qmail":
				$tmp = preg_replace("/--- Below this line is a copy of the message.(.*)$/si","",$data["body"]);
				preg_match_all("/<[^>]*?>/si",$tmp,$failed);
				//qmail only support
				if ($failed[0][0]) {
					$failed_address = trim(preg_replace("/(>)|(<)/si","",$failed[0][0]));
					$data["sender_emailaddress"] = $failed_address;
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
					$failed_address = trim ( preg_replace("/X-Failed-Recipients:/si","",$failed[0][0]) );

					//multiple users are possible - use the first one
					$failed_address = preg_replace("/,(.*)$/s","",$failed_address);
					$failed_address = trim(preg_replace("/(>)|(<)/si","",$failed_address));
					$data["sender_emailaddress"] = $failed_address;
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
					$failed_address = trim(preg_replace("/(>)|(<)/si","",$failed[0][0]));
					$data["sender_emailaddress"] = $failed_address;
					$data["sender"] = "Bounced by Xmail";
					$data["body"] = preg_replace("/>>$/s","",trim($tmp));
				}else{
					$data["sender"] = "Bounced by Xmail";
				}
				break;
			case "postfix" :
				$data["sender"] = "Bounced by Postfix";
				preg_match_all("/<[^>]*?>: /si", $data["body"], $failed);
				if ($failed[0][0]) {
					$failed_address = trim(preg_replace("/(>)|(<)|(:)/si","",$failed[0][0]));
					$data["sender_emailaddress"] = $failed_address;
				}
				break;

			default:
				$data["sender"] = "Bounced by ".$mailer;
				//try to grab the bounced address from the body
				preg_match("/^To: (.*)$/m", $data["body"], $failed);
				if (trim($failed[1])) {
					$failed_address = trim($email[1]);
					$data["sender_emailaddress"] = $failed_address;
				}
				break;
		}
		//mark all businesscards with this email address with extra classification
		if ($failed_address) {
			$classification_data = new Classification_data();
			$bcard_data = $classification_data->getSpecialClassification("Bounced-Email");
			$bounced_cla = $bcard_data[0]["id"];
			//ugly way to update the addressbook
			$sql = sprintf("UPDATE address_businesscards SET classification = concat(classification, '|', %d, '|') WHERE business_email like '%%%2\$s%%' OR personal_email like '%%%2\$s%%';", $bounced_cla, $failed_address);
			sql_query($sql);
		}
	}
?>
