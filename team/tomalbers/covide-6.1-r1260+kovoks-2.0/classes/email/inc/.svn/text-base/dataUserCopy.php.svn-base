<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}
	$escape = sql_syntax("escape_char");

	//$mail_id = $_REQUEST["id"];
	//$user_id = $_REQUEST["user_id"];

	//retrieve inbox for this user
	if (!$folder) {
		$inbox = $this->getSpecialFolder("Postvak-IN", $user_id);
	} else {
		$inbox["id"] = $folder;
		$inbox["user_id"] = $user_id;
	}

	if ($flag_read) {
		$is_new = 0;
	} else {
		$is_new = 1;
	}
	$fsdata = new Filesys_data();

	//retrieve data in database
	$data = $this->getEmailById($_REQUEST["id"]);
	$mdata =& $data[0];

	$user = new User_data();
	$username = $user->getUsernameById($_SESSION["user_id"]);

	if ($_REQUEST["description"]) {
		$mdata["description"]  = addslashes($_REQUEST["description"]);
		$mdata["description"] .= sprintf("\n[%s %s]\n", gettext("kopie van"), $username);
	}

	//prepare the data
	$fields["message_id"]          = $this->generate_message_id();
	$fields["folder_id"]           = array("d",$inbox["id"]);
	$fields["user_id"]             = array("d",$inbox["user_id"]);
	$fields["address_id"]          = array("d",$mdata["address_id"]);
	$fields["project_id"]          = array("d",$mdata["project_id"]);
	$fields["sender"]              = array("s",$mdata["sender"]);
	$fields["subject"]             = array("s",$mdata["subject"]);
	$fields["date"]                = array("s",$mdata["date"]);
	$fields["is_text"]             = array("d",$mdata["is_text"]);
	$fields["is_public"]           = array("d",$mdata["is_public"]);
	$fields["sender_emailaddress"] = array("s",$mdata["sender_emailaddress"]);
	$fields[$escape."to".$escape]  = array("s",$mdata["to"]);
	$fields["cc"]                  = array("s",$mdata["cc"]);
	$fields["description"]         = array("s",$mdata["description"]);
	$fields["is_new"]              = array("d",$is_new);
	$fields["replyto"]             = array("s",$mdata["replyto"]);
	$fields["bcc"]                 = array("s",$mdata["bcc"]);
	$fields["date_received"]       = array("s",$mdata["date_received"]);
	$fields["template_id"]         = array("d",$mdata["template_id"]);
	$fields["indexed"]             = array("d",$mdata["indexed"]);
	$fields["options"]             = array("s",$mdata["options"]);

	$keys = array();
	$vals = array();
	foreach ($fields as $k=>$v) {
		$keys[] = $k;
		if ($v[0]=="s") {
			$vals[]="'".addslashes($v[1])."'";
		} else {
			$vals[]=(int)$v[1];
		}
	}
	$keys = implode(",",$keys);
	$vals = implode(",",$vals);

	$q = sprintf("insert into mail_messages (%s) values (%s)", $keys, $vals);
	sql_query($q);

	$new_msg_id = sql_insert_id("mail_messages");

	if ($mdata["is_text"]) {
		$body =& $mdata["body"];
	} else {
		$body =& $mdata["body_html"];
	}

	$fspath = $GLOBALS["covide"]->filesyspath;
	$fsdir_source  = "email";
	$fsdir_target  = "email";

	$mail_id = $new_msg_id;

	//copy the attachments

	foreach ($mdata["attachments"] as $k=>$v) {
		$file = $this->getAttachment($k, 1);

		/* gather some file info */
		$name = addslashes($file["name"]);
		$type = addslashes($file["type"]);
		$size = addslashes($file["size"]);

		/* insert file into dbase */
		$q = "insert into mail_attachments (message_id, name, size, type) values ";
		$q.= sprintf("(%d, '%s', '%s', '%s')", $mail_id, $name, $size, $type);
		sql_query($q);
		$new_id = sql_insert_id("mail_attachments");

		/* move data to the destination */
		$ext = $fsdata->get_extension($name);

		$source = sprintf("%s/%s/%s.%s", $fspath, $fsdir_source, $k, $ext);
		$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

		@copy($source, $destination);

	}

	//delete old messages data (if any)
	$q = sprintf("delete from mail_messages_data where mail_id = %d", $mail_id);
	sql_query($q);

	//copy message data
	$q = sprintf("insert into mail_messages_data (mail_id, body, header) values (%d, '%s', '%s')", $new_msg_id, addslashes($body), addslashes($mdata["header"]));
	sql_query($q);

?>
