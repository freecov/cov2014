<?php
	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}

	$user_id =& $this->user_id;

	//fetch the status list
	$_statuslist = array();
	$_statuslist_msg = array();

	//create a lookup table
	$q = sprintf("select * from status_list where user_id = %d", $this->user_id);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {
		$_statuslist[$row["id"]]=$row;
		$_statuslist[$row["id"]]["mark_delete"]=1;
		$_statuslist_msg[$row["msg_id"]] = $row["id"];
	}

	$user = new User_data();
	$userSettings = $user->getUserdetailsById($this->user_id);

	$deltime_server = $userSettings["mail_server_deltime"];
	$deltime_folder = $userSettings["mail_deltime"];


	$num_messages = imap_num_msg($this->mbox);
	$mboxSort = ( imap_sort($this->mbox, SORTARRIVAL, 0, SE_NOPREFETCH) );

	$meminfo = memory_get_usage();
	$meminfo /= 1024;
	$meminfo = number_format($meminfo, 0)." KB";
	$this->markup("! memory statistics after list fetch [$meminfo] ! ", "grey");

	if ($num_messages > 0) {
		/* pre-process the address book */
		/* try to link the email to a relationcard */

		//in addressbook + bcards
		$address_data = new Address_data();
		$this->address = $address_data->getRelationsEmailArray();

		$this->markup("- loaded addressbook email addresses - ", "green");
		$meminfo = memory_get_usage();
		$meminfo /= 1024;
		$meminfo = number_format($meminfo, 0)." KB";
		$this->markup("! memory statistics with addressbook loaded [$meminfo] ! ", "grey");

		/* pre-process all mail filters */
		$mailData = new Email_data();
		$filters = $mailData->get_filter_list("", $this->user_id);
		$this->markup("- loaded mailfilters - ", "green");
		$meminfo = memory_get_usage();
		$meminfo /= 1024;
		$meminfo = number_format($meminfo, 0)." KB";
		$this->markup("! memory statistics with mailfilters loaded [$meminfo] ! ", "grey");
	}

	foreach ($mboxSort as $i=>$imap_id) {

		/* alias some variabeles */
		$data   =& $this->data;
		$xmail  =& $this->xmail;
		$header =& $this->header;

		/* clear some variabeles */
		$header = array();
		$data   = array();
		$xmail  = 0;
		$xmail  = new Email_container();


		/* retrieve the email header */
		$this->header = imap_headerinfo($this->mbox, $imap_id);

		//new format style msg_id (md5sum style)
		$msg_id = "<".md5($header->message_id.$header->udate.$header->subject).">";

		//lookup message position in statuslist array
		$_msg_record = $_statuslist_msg[$msg_id];

		/* msg_id old to new format conversion */
		if ($this->message_conversion) {
			$msg_old_id = preg_replace("/>$/s","",substr(addslashes($header->message_id),0,250)).">";
			$msg_old_alt = "<".addslashes(substr($header->udate."---".$header->subject,0,250)).">";
			if ($msg_old_id=="") {
				$msg_old_id = $msg_old_alt;
			}
			//update to memory array
			$_statuslist[$_msg_record]["msg_id"] = $msg_id;
		}
		/* end of conversion */


		/* mark the email as seen on the server - state: in_sync */
		$_statuslist[$_msg_record]["mark_delete"]=0;

		//if this message is still present on the server
		if (array_key_exists($msg_id, $_statuslist_msg)) {
			//check if we can remove the mail from the server
			if ($deltime_server > 0) {
				$deldate = $deltime_server+$_statuslist[$_msg_record]["timestamp"];
				$now = mktime();

				/* delete item after del_time */
				if ($now >= $deldate){
					imap_delete($this->mbox, $imap_id);
					$_statuslist[$_msg_record]["mark_delete"] = 1;
					$this->markup("- item marked for delete -", "red");
				}
			}
		} else {

			/* check if stream is alive */
			if (!imap_ping($this->mbox))
				die("mailserver: user is logged out");


			/* this email is new - process it */
			$num_messages_done++;
			$this->markup("+ retrieving message [".$imap_id."] +", "navy");

			$this->parseMessage($imap_id);

			$this->markup(" - message body ok -", "green");

			$escape = sql_syntax("escape_char");

			$email_clean = trim(strtolower($this->cleanAddress($data["sender_emailaddress"])));

			if ($this->address[$email_clean] > 0) {
				$data["address_id"] = $this->address[$email_clean];
				$this->markup(" - address found [".$data["address_id"]."] -", "orange");
			} elseif ($this->address[$email_clean] == -1) {
				$data["askwichrel"] = 1;
				$this->markup(" - multiple addresses found [ask for relation] -", "orange");
			}

			/* matching private filters */
			$from = $mailData->cleanAddress($data["sender_emailaddress"]);
			$rcpt = $mailData->cleanAddress($data["to"]);
			$subject = $data["subject"];

			$filter_done    = 0;
			$matches_needed = 0;
			$matches_found  = 0;

			foreach ($filters as $f) {
				if (!$filter_done) {
					if (!$data["new_folder_id"]) {
						$matches_needed = 0;
						$matches_found = 0;

						if ($f["sender"]) {
							$matches_needed++;
							if (strtolower($from) == strtolower($f["sender"])) {
								$matches_found++;
								$d["sender"] = 1;
								$this->markup(" - Filter based on sender matched [".$f["sender"]."] -", "green");
							}
						}
						if ($f["receipient"]) {
							$matches_needed++;
							if (strtolower($rcpt) == strtolower($f["receipient"])) {
								$matches_found++;
								$d["receipient"] = 1;
								$this->markup(" - Filter based on receipient matched [".$f["receipient"]."] -", "green");
							}
						}
						if ($f["subject"]) {
							$matches_needed++;
							if (strpos(strtolower($subject), strtolower($f["subject"])) !== false) {
								$matches_found++;
								$d["subject"] = 1;
								$this->markup(" - Filter based on subject matched [".$f["subject"]."] -", "green");
							}
						}
						if ($matches_needed > 0 && $matches_needed == $matches_found) {
							$data["new_folder_id"] = $f["to_mapid"];
							$filter_done = 1;
						}
					}
				}
			}

			if ($data["new_folder_id"]) {
				$data["folder_id"] = $data["new_folder_id"];
			}

			//prepare the data
			$fields["message_id"]          = array("s",$msg_id);
			$fields["sender"]              = array("s",$data["from"]);
			$fields["sender_emailaddress"] = array("s",$data["sender_emailaddress"]);
			$fields[$escape."to".$escape]  = array("s",$data["to"]);
			$fields["cc"]                  = array("s",$data["cc"]);
			$fields["subject"]             = array("s",$data["subject"]);
			$fields["address_id"]          = array("d",$data["address_id"]);
			$fields["project_id"]          = array("d",$data["project_id"]);
			$fields["folder_id"]           = array("d",$data["folder_id"]);
			$fields["user_id"]             = array("d",$this->user_id);
			$fields["date"]                = array("s",$data["date"]);
			$fields["date_received"]       = array("d",$data["date_received"]);
			$fields["askwichrel"]          = array("d",$data["askwichrel"]);
			$fields["indexed"]             = array("d",2);
			$fields["is_text"]             = array("d",$data["is_text"]);
			if ($data["mark_old"])
				$fields["is_new"]          = array("d",0);
			else
				$fields["is_new"]          = array("d",1);


			if (!$fields["subject"][1]) {
				$fields["subject"][1] = gettext("geen onderwerp");
			}
			$this->markup(" - subject [".substr($data["subject"],0,80)."] -", "black");

			/* convert field syntax to value syntax */
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

			$id = sql_insert_id("mail_messages");

			$q = sprintf("insert into mail_messages_data (mail_id, body, header, mail_decoding) values (%d, '%s', '%s', '%s')",
			$id, addslashes($data["body"]), addslashes($data["header"]), addslashes($data["mail_decoding"]));
			sql_query($q);

			$mailData->dropMailBodyToFilesys($id);
			#$id = 0;

			$this->markup(" - dbase query ok -", "green");

			//insert into statuslist db
			$q = sprintf("insert into status_list (msg_id, mail_id, timestamp, user_id) values ('%s', %d, %d, %d)", $msg_id, $id, mktime(), $user_id);
			sql_query($q);

			$status_id = sql_insert_id("status_list");
			#$status_id = 0;

			//insert status id into memory
			$_statuslist[$status_id]["msg_id"] = $msg_id;
			$_statuslist[$status_id]["mail_id"] = $dbMailID;
			$_statuslist[$status_id]["timestamp"] = mktime();
			$_statuslist[$status_id]["user_id"] = $user_id;
			$_statuslist[$status_id]["id"] = $status_id;
			$_statuslist[$status_id]["mark_delete"] = 0;

			$this->markup(" - status list ok -", "green");

			$this->parseAttachments($imap_id, $id);
			$this->markup(" - attachments ok -", "green");

			$meminfo = memory_get_usage();
			$meminfo /= 1024;
			$meminfo = number_format($meminfo, 0)." KB";
			$this->markup(" ! memory statistics [$meminfo] ! ", "grey");

			#die("eof");

		}
	}

	$sql = "update mail_messages set status_pop = 0 where status_pop = 1 and user_id = ".$this->user_id;
	sql_query($sql);

	//remove mail marked for delete on the server
	#imap_expunge($this->mbox); //delete

	//cleanup status_list in memory
	$_items_del = array(0);

	foreach ($_statuslist as $k=>$v) {
		if ($v["mark_delete"]==1) {
			if ($k) $_items_del[]=$k;
		}
	}
	unset ($_statuslist);
	$_items_del = implode(",",$_items_del);

	//resync status_list with the servers db
	$q = "delete from status_list where id IN (".$_items_del.") and user_id = ".$this->user_id;
	sql_query($q);

	//close the connection and expunge
	imap_expunge($this->mbox);
	$this->markup("- mailbox expunged - ", "grey");

	imap_close($this->mbox);
	$this->markup("- mailbox closed - ", "grey");


	/* ******************************************************* */
	/* second routine - delete all old emails in deleted-items */
	/* ******************************************************* */
	$mailData = new Email_data();
	$deleted_items = $mailData->getSpecialFolder("verwijderde-items", $this->user_id);
	$sent_items    = $mailData->getSpecialFolder("verzonden-items", $this->user_id);

	$this->markup("- deleting from sent and deleted items - ", "grey");
	$q = sprintf("select id, date, subject from mail_messages where folder_id IN (%d, %d)", $deleted_items["id"], $sent_items["id"]);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {
		if (($deltime_folder+(int)$row["date"]) <= mktime()) {
			$this->markup(" - deleted item [".substr($row["subject"],0,80)."] -", "red");
			$mailData->mail_delete($row["id"]);
		}
	}

	$meminfo = memory_get_usage();
	$meminfo /= 1024;
	$meminfo = number_format($meminfo, 0)." KB";
	$this->markup("! memory statistics at script end [$meminfo] ! ", "grey");
	$this->markup("- all done -", "green");
?>
