<?php
/**
 * Conversion script to put mails from Covide into an IMAP server
 *
 * @version 0.1
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2009 Michiel van Baak
 */

/* include php files we need */
include("Mail/mime.php");

/* variables used */
$dbserver = "localhost";
$dbusername = "user";
$dbpassword = "pass";
$dbdatabase = "covide_trunk";

$imapserver = "mail.example.net";
$imapusername = "user@example.net";
$imappassword = "password";

$crlf = "\n";


/* logic */

$db = mysql_connect($dbserver, $dbusername, $dbpassword);
mysql_select_db($dbdatabase);

// get messages
$sql = "SELECT * FROM mail_messages WHERE id = 30";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$sql_data = sprintf("SELECT * FROM mail_messages_data WHERE mail_id = %d", $row["id"]);
	$res_data = mysql_query($sql_data);
	$row_data = mysql_fetch_assoc($res_data);
	$mime = new Mail_mime($crlf);
	if ($row["is_text"] == 1) {
		$mime->setTXTBody($row_data["body"]);
	} else {
		$mime->setHTMLBody($row_data["body"]);
	}
	$sql_attachments = sprintf("SELECT * FROM mail_attachments WHERE message_id = %d", $row["id"]);
	$res_attachments = mysql_query($sql_attachments);
	while ($row_attachments = mysql_fetch_assoc($res_attachments)) {
		$p = pathinfo($row_attachments["name"]);
		$ext = $p["extension"];
		if ($ext == "html") {
			$ext = "htm";
		}
		if (strlen($ext)!=3) {
			$ext = "dat";
		}
		$file = sprintf("/var/covide_files/covidetrunk/email/%s.%s", $row_attachments["id"], $ext);
		$mime->addAttachment($file, $row_attachments["type"], $row_attachments["name"]);
	}
	$body = $mime->get();
	$headers = $mime->headers(array("From" => $row["sender_emailaddress"], "Subject" => $row["subject"], "To" => $row["to"], "Date" => date("r", $row["date"]), "Message-Id" => $row["message_id"]));
	$mail = $mime->txtHeaders()."\n\n".$body;
	$mbox = imap_open("{".$imapserver."/novalidate-cert}INBOX", $imapusername, $imappassword);
	imap_append($mbox, "{".$imapserver."}INBOX", $mail, "\\Seen");
	//file_put_contents("/tmp/mail.msg", $mail);
	//var_dump($mail);
	//var_dump($row_data);
	echo "\n";
}
?>
