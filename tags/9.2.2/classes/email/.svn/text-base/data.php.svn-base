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
 * @todo translate the special folders to english
 */

Class Email_data {

	/* constants */
	const include_dir = "classes/email/inc/";
	const class_name = "email_data";

	/* variables */
	public $data = array();
	public $pagesize;

	public $archive_id = "";
	public $_folder = array();

	private $_cache;
	private $_expunge = array();

	public $_archive_period = 6; //in months, 0 = disable feature

	/* methods */
	public function __construct() {
		$this->data = array();
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	/* createFolder {{{ */
	/**
	 * Create a mailfolder in users Inbox
	 */
	public function createFolder($parent_id = "", $folder_name = "") {
		if (!$parent_id)
			$parent_id   = $_REQUEST["folder_id"];

		if (!$folder_name)
			$folder_name = $_REQUEST["action_value"];

		$sql = sprintf("SELECT COUNT(*) as count FROM mail_folders WHERE name = '%s' AND user_id = '%d' AND parent_id='%d'",$folder_name, $_SESSION["user_id"], $parent_id);
		$res = sql_query($sql);
		$foldercount = sql_result($res, 0);
		if ($foldercount == 0) {
			$q = sprintf("insert into mail_folders (name, user_id, parent_id) values ('%s', %d, %d)",
			$folder_name, $_SESSION["user_id"], $parent_id);
			sql_query($q);
			return sql_insert_id("mail_folders");
		}
	}
	/* }}} */
	/* editFolder {{{ */
	/**
	 * Save modified folder data to db
	 */
	public function editFolder() {
		$folder_id   = $_REQUEST["folder_id"];
		$folder_name = $_REQUEST["action_value"];

		$q = sprintf("update mail_folders set name = '%s' where user_id = %d and id = %d",
			$folder_name, $_SESSION["user_id"], $folder_id);
		sql_query($q);
	}
	/* }}} */
	/* multi_download_zip {{{ */
	/**
	 * Add multiple files in a .zip archive and let the user download it
	 */
	public function multi_download_zip() {
		$mail_id = $_REQUEST["mail_id"];

		if ($mail_id) {
			$attachments = $this->attachments_list($mail_id);
			$attachment_ids = array();
			foreach ($attachments as $k=>$v) {
				$attachment_ids[]=$k;
			}
		} else {
			$attachment_ids = explode(",",$_REQUEST["attachment_ids"]);
		}

		$zipfile = new Covide_zipfile();
		// add the subdirectory ... important!
		$zipfile->add_dir("covide/");

		foreach ($attachment_ids as $k=>$v) {
			$attachment = $this->getAttachment($v, 1);
			$zipfile->add_file($attachment["data_binary"], sprintf("covide/%s", $attachment["name"]));
			unset($attachment);
		}

		$data = $zipfile->file();
		unset($zipfile);

		$fname = "covide.zip";

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/x-zip');

		#if (!$_SERVER["HTTPS"])
		#	header("Content-Length: ".strlen($data));

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$fname.'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$fname.'"');
		}
		file_put_contents("/tmp/zcovide.zip", $data);
		echo $data;
		exit();
	}
	/* }}} */
	/* folderDelete {{{ */
	/**
	 * Recursivaly remove folder from mailfolders
	 *
	 * @param int The folder id to remove
	 * @return int The id of the Inbox folder
	 */
	public function folderDelete($folder_id) {
		/* detect if the highest parent is deleted items */
		$folder    = $this->getFolder($folder_id);
		$del_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
		$inbox     = $this->getSpecialFolder("Postvak-IN", $_SESSION["user_id"]);
		$archive   = $this->_get_archive_id();

		if ($folder["id"] != $archive && $folder["id"] && $folder["high_parent"] == $del_items["id"]) {
			/* if the highest parent is deleted items */

			/* list all child folders */
			$q = sprintf("select id from mail_folders where parent_id = %d", $folder["id"]);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$this->folderDelete($row["id"]);
			}

			/* delete the emails */
			$q = sprintf("select id from mail_messages where folder_id = %d", $folder["id"]);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$this->mail_delete($row["id"]);
			}

			/* delete the folder */
			$q = sprintf("delete from mail_folders where id = %d", $folder["id"]);
			sql_query($q);

		} elseif ($folder["id"] != $archive && $folder["id"]) {
			/* just move the folder to deleted items */
			$q = sprintf("update mail_folders set parent_id = %d where id = %d and user_id = %d",
				$del_items["id"], $folder["id"], $_SESSION["user_id"]);
			sql_query($q);
		}

		//return inbox id
		return $inbox["id"];
	}
	/* }}} */
	/* folderMoveExec {{{ */
	/**
	 * Move a folder in the mailfolders structure
	 */
	public function folderMoveExec() {
		$archive = $this->_get_archive_id();
		$folder_id = $_REQUEST["folder_id"];
		$target_id = $_REQUEST["target_id"];

		if ($folder_id != $archive && $target_id != $archive) {
			$q = sprintf("update mail_folders set parent_id = %d where id = %d and user_id = %d",
				$target_id, $folder_id, $_SESSION["user_id"]);
			sql_query($q);
		}
	}
	/* }}} */
	/* multipleMove {{{ */
	/**
	 * Move multiple mail messages to a new folder
	 */
	public function multipleMove() {
		$mail = $_REQUEST["mail"];
		$ids  = $mail["ids"];

		$folder     = $_REQUEST["folder_id"];
		$address_id = $mail["address_id"];
		$project_id = $mail["project_id"];

		$q = sprintf("update mail_messages set folder_id = %d, project_id = %d, address_id = %d where id IN (%s)", $folder, $project_id, $address_id, $ids);
		sql_query($q);

		/* check if sync server expunge mailitems flag is active */
		if ($user_info["xs_funambol_expunge"])
			$this->expungeMailItem($ids);
	}
	/* }}} */
	/* getPriorityList {{{ */
	/**
	 * Get a list of predefined mail priorities
	 *
	 * @return array The priorities with numberic rep. as array key
	 */
	public function getPriorityList() {
		$prior = array(
			"0"  => gettext("no value"),
			"1" => gettext("1 - Highest"),
			"2" => gettext("2 - High"),
			"3" => gettext("3 - Normal"),
			"4" => gettext("4 - Low"),
			"5" => gettext("5 - Lowest")
		);
		return $prior;
	}
	/* }}} */
	/* toggleState {{{ */
	/**
	 * Switch is_new state on mail messages
	 */
	public function toggleState() {
		$mails = $_REQUEST["checkbox_mail"];
		if (count($mails)>0) {
			$ids = array();
			foreach ($mails as $k=>$v) {
				$this->toggleStateOne($k);
			}
		}
	}
	/* }}} */
	/* toggleStateOne {{{ */
	/**
	 * Toggle the state of one mail message
	 *
	 * @param int $mail_id The mail to toggle state for
	 * @param int $xml Defaults to 0, set to one if you want to echo the new state instead of returning it
	 * @return int the new status of the mail. 1 for new 0 for read
	 */
	public function toggleStateOne($mail_id, $xml=0) {
		//first get state of the selection
		$q = sprintf("select is_new from mail_messages where id = %d", $mail_id);
		$res = sql_query($q);
		$state = sql_result($res,0);
		if ($state > 0) {
			//set to state NOT new
			$state = 0;
		} else {
			//set to state new
			$state = 1;
		}
		$q = sprintf("update mail_messages set is_new = %d where id = %d", $state, $mail_id);
		sql_query($q);
		if ($xml) {
			echo $state;
		} else {
			return $state;
		}
	}
	/* }}} */
	/* updateMailOptions {{{ */
	public function updateMailOptions($id, $data) {
		$str = addslashes( $this->encodeMailOptions($data) );

		$q = sprintf("update mail_messages set options = '%s' where id = %d", $str, $id);
		sql_query($q);
	}
	/* }}} */
	/* encodeMailOptions {{{ */
	/**
	 * Encode array with mail options to textstring with values | seperated
	 *
	 * @param array mailoptions like priority, classifications etc
	 * @return string same options, but as | seperated textstring
	 */
	public function encodeMailOptions($data) {
		$opts = array();
		$opts["readconfirm"]              = $data["readconfirm"];
		$opts["priority"]                 = $data["priority"];
		$opts["template"]                 = $data["template"];
		$opts["template_type"]            = $data["template_type"];
		$opts["template_font"]            = $data["template_font"];
		$opts["template_size"]            = ($data["template_size"]) ? $data["template_size"] : "2";
		$opts["template_cmnt"]            = $data["template_cmnt"];
		$opts["related_id"]               = $data["related_id"];
		$opts["classifications_positive"] = $data["classifications_positive"];
		$opts["classifications_negative"] = $data["classifications_negative"];
		$opts["classifications_target"]   = $data["classifications_target"];
		$opts["classifications_type"]     = $data["classifications_type"];
		$opts["newsletter_target"]        = $data["newsletter_target"];
		$opts["campaign_id"]              = $data["campaign_id"];

		/* encode template options */
		if ($data["template_values"]) {
			foreach ($data["template_values"] as $tpl_key=>$tpl_value)
				$opts["tpl_".$tpl_key] = $tpl_value;
		}
		$d = array();
		foreach ($opts as $k=>$v) {
			$d[] = $k."=".$v;
		}
		$d = implode("|",$d);
		return $d;
	}
	/* }}} */
	/* decodeMailOptions {{{ */
	/**
	 * Reverse the operation done by encodeMailOptions
	 *
	 * @param string The mailoptions encoded as | seperated text string
	 * @return array The mailoptions in an array
	 */
	public function decodeMailOptions($str) {
		$opts = array();
		$data = explode("|", $str);
		foreach ($data as $setting) {
			$d = explode("=", $setting);
			switch ($d[0]) {
				case "readconfirm"             : $opts["readconfirm"]              = $d[1]; break;
				case "priority"                : $opts["priority"]                 = $d[1]; break;
				case "template"                : $opts["template"]                 = $d[1]; break;
				case "template_type"           : $opts["template_type"]            = $d[1]; break;
				case "template_font"           : $opts["template_font"]            = $d[1]; break;
				case "template_size"           : $opts["template_size"]            = $d[1]; break;
				case "template_cmnt"           : $opts["template_cmnt"]            = $d[1]; break;
				case "related_id"              : $opts["related_id"]               = $d[1]; break;
				case "classifications_positive": $opts["classifications_positive"] = $d[1]; break;
				case "classifications_negative": $opts["classifications_negative"] = $d[1]; break;
				case "classifications_target"  : $opts["classifications_target"]   = $d[1]; break;
				case "classifications_type"    : $opts["classifications_type"]     = $d[1]; break;
				case "newsletter_target"       : $opts["newsletter_target"]        = $d[1]; break;
				case "campaign_id"             : $opts["campaign_id"]              = $d[1]; break;
			}
			if (preg_match("/^tpl_/s", $d[0]))
				$opts[$d[0]] = $d[1];
		}
		if (!$opts["template_size"]) {
			$opts["template_size"] = 2;
		}
		return $opts;
	}
	/* }}} */
	/* getTemplates {{{ */
	public function getTemplates() {
		$templates = array();
		$q = "select id, description from mail_templates order by description";
		$res = sql_query($q);
		$templates = array(
			"0" => gettext("no template")
		);

		while ($row = sql_fetch_assoc($res)) {
			$templates[$row["id"]] = $row["description"];
		}
		return $templates;
	}
	/* }}} */
	/* sendMailComplex {{{ */
	/**
	 * Send a Draft by id that is more complex then just plain text part
	 *
	 * @param int $id The draft id to send
	 * @param int $return If set to 1 return the status code
	 * @param int $skip_sender_rewrite If set to 1, dont do something with the sender ?
	 * @param int $skip_gmail If set to 1, forces delivery over localhost instead of the google mambojambo we do in there.
	 *
	 * @return mixed the result of the smtp dialog
	 */
	public function sendMailComplex($id, $return=0, $skip_sender_rewrite=0, $skip_gmail = 0) {
		require(self::include_dir."dataSendMailComplex.php");
		return $status;
	}
	/* }}} */
	/* userCopy {{{ */
	/**
	 * Copy a mail to another user
	 *
	 * @param int $mail_id The mail to copy
	 * @param int $user_id The user to copy the mail to
	 * @param string $folder
	 * @param int $flag_read if 1 the mail will be marked as read, if 0 the mail will be flagged unread
	 * @return int The id of the new email messages
	 */
	public function userCopy($mail_id, $user_id, $folder="", $flag_read=0) {
		require(self::include_dir."dataUserCopy.php");
		return $new_msg_id;
	}
	/* }}} */
	/* userMove {{{ */
	/**
	 * Move a mail to another user
	 */
	public function userMove() {
		require(self::include_dir."dataUserMove.php");
	}
	/* }}} */
	/* sendMail {{{ */
	/**
	 * Extended version of php's mail().
	 * This function gets the from out of the covide database based on sender user_id
	 *
	 * @param string $to Recipient of the mail
	 * @param string $subject Subject of the mail
	 * @param string $message Body of the mail
	 * @param string $additional_options Name for the From field
	 * @param int $user_id The user id to use in sender address. if unset it will be support@terrazur.nl
	 */
	public function sendMail($to, $subject, $message, $additional_options="", $user_id="") {
		if ($user_id) {
			$user_data = new User_data();
			$userinfo = $user_data->getUserdetailsById($user_id);
			$from = $userinfo["mail_email"];
			unset($user_data);
		}
		if (!trim($from)) {
			$from = "support@terrazur.nl";
		}

		if (empty($additional_options))
			$headers = "From: \"Covide ".gettext("reminder")."\" <".$from.">";
		else
			$headers = "From: \"".$additional_options."\" <".$from.">";
		// always send utf8 email
		$headers .= "\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=utf-8\n";
		$headers .= "Content-Transfer-Encoding: quoted-printable\n";
		$headers .= "X-Mailer: Covide v".$GLOBALS["covide"]->version."\n";
		mail($to, $subject, imap_8bit($message), $headers, "-f".$from);
	}
	/* }}} */
	/* save_concept {{{ */
	/**
	 * Save a Draft to the database
	 *
	 * @param int $id The Draft id to save, or 0 to create a new Draft
	 * @return int The draftid that was saved
	 */
	public function save_concept($id=0, $req="") {
		if (is_array($req)) {
			$REQUEST = $req;
			$POST    = $req;
		}	else {
			$REQUEST = $_REQUEST;
			$POST    = $_POST;
		}

		require(self::include_dir."dataSaveConcept.php");
		return $id;
	}
	/* }}} */
	/* upload_files {{{ */
	public function upload_files($id="", $return=0) {
		require(self::include_dir."dataUploadFiles.php");
	}
	/* }}} */
	/* attachments_list {{{ */
	/**
	 * Fetch list of attachments for a mail message
	 *
	 * @param int $id The id of the message to lookup attachments for
	 * @param int $full If set return everything we have in the database, otherwise only basic data
	 * @return array The attachment info
	 */
	public function attachments_list($id, $full=0) {
		//fetch attachments
		$conversion = new Layout_conversion();
		$fsdata = new Filesys_data();

		$q = sprintf("select * from mail_attachments where message_id = %d order by name", $id);
		$res2 = sql_query($q);
		$att = array();
		while ($row2 = sql_fetch_assoc($res2)) {
			if ($full == 2) {
				$att[$row2["id"]] = $this->getAttachment($row2["id"], 2);
			} elseif ($full == 1) {
				$att[$row2["id"]] = $this->getAttachment($row2["id"]);
			} else {
				$att[$row2["id"]]["id"]   = $row2["id"];
				$att[$row2["id"]]["cid"]  = $row2["cid"];
				$att[$row2["id"]]["type"] = $row2["type"];
				$att[$row2["id"]]["name"] = $this->decodeMimeString($row2["name"]);
				$att[$row2["id"]]["short_name"] = $conversion->limit_string($this->decodeMimeString($row2["name"], 30));
				$att[$row2["id"]]["h_size"] = $conversion->convert_to_bytes($row2["size"]);
			}
			if (preg_match("/^image\/*/si", $att[$row2["id"]]["type"])) {
				$att[$row2["id"]]["is_image"] = 1;
			}
			if (!$att[$row2["id"]]["cid"]) {
				$att[$row2["id"]]["no_cid"] = 1;
			} elseif (!preg_match("/^image\/*/si", $att[$row2["id"]]["type"])) {
				$att[$row2["id"]]["no_cid"] = 1;
			}
			$att[$row2["id"]]["fileicon"] = $fsdata->getFileType($row2["name"]);

		}
		return $att;
	}
	/* }}} */
	/* upload_list {{{ */
	/**
	 * show list of attached files
	 */
	public function upload_list() {
		$id = $_REQUEST["id"];
		$att = $this->attachments_list($id, 2);

		//create code for attachments
		$output = new Layout_output();
		$conversion = new Layout_conversion();

		foreach ($att as $k=>$v) {
			$output->insertAction("delete", gettext("delete attachment"), "javascript: attachment_delete('".$v["id"]."');");
			$output->insertAction("file_download", gettext("download attachment"), "?mod=email&action=download_attachment&dl=1&id=".$v["id"]);
			$output->addCode(sprintf(" %s (%s)", $v["name"], $v["h_size"]));
			if (!$v["data_has_binary"]) {
				$output->addSpace();
				$output->insertTag("font", sprintf("[%s]", gettext("error - file not found")),
					array("color" => "red"));
			}
			$output->addTag("br");
		}
		$output->exit_buffer();
	}
	/* }}} */
	/* parseEmailHeader {{{ */
	/**
	 * Parse email header information into an array
	 *
	 * @param string $header The mailheader
	 * @return array header information in an array
	 */
	private function parseEmailHeader($header) {
		require(self::include_dir."dataParseEmailHeader.php");
		return $data;
	}
	/* }}} */
	/* archiveOldEmails {{{ */
	/**
	 * Archive emails older then X months from mail_messages_data to mail_messages_data_archive
	 */
	public function archiveOldEmails() {
		set_time_limit(60*60*4);
		session_write_close();

		if ($this->_archive_period > 0) {

			/* generate timestamp */
			$ts = mktime(0,0,0,date("m")-$this->_archive_period,date("d"),date("Y"));

			/* scan for emails in mail_messages_data */
			$q = sprintf("select id from mail_messages where (
					(date_received > 0 and date_received < %1\$d)
					or (date > 0 and date < %1\$d)
					or (
						(date = 0 or date is null)
						and (date_received = 0 or date_received is null)
					)
				)
				and folder_id = %2\$d
				and id IN (select mail_id from mail_messages_data)
				", $ts, $this->_get_archive_id()
			);
			//order by date_received, date

			/* max 200/5000 a time */
			$res = sql_query($q, "", 0, ($_REQUEST["fetchall"] ? 200:5000));
			while ($row = sql_fetch_assoc($res)) {
				$fetch = 1;
				$this->archiveEmail($row["id"]);
			}
			if ($_REQUEST["fetchall"] && $fetch) {
				$output = new Layout_output();
				$output->addCode("archiving mail ");
				$output->addCode("(");
				$output->addCode($_REQUEST["fetchall"] * 200);
				$output->addCode("): |");
				for ($i=0; $i <= $_REQUEST["fetchall"]; $i++) {
					$output->addCode(" -");
				}
				$output->addCode(">");
				$output->start_javascript();
				$output->addCode(sprintf(
					"setTimeout(\"location.href='?mod=email&action=archiveOldEmails&fetchall=%s';\", 500);",
					$_REQUEST["fetchall"]+1));
				$output->end_javascript();
				$output->exit_buffer();
			}

		}
		/* cleanup email folder */
		$this->cleanUpMaildata();
	}
	/* }}} */
	/* archiveEmail {{{ */
	/**
	 * Archive emails but only the ones given
	 *
	 * @param string $items comma seperated list of mailids to archive
	 */
	private function archiveEmail($items) {
		if ($this->_archive_period == 0)
			return true;

		$ids = explode(",", $items);
		foreach ($ids as $id) {
			$q = sprintf("select id, date, date_received, folder_id from mail_messages where id = %d", $id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			/* get mail received date */
			$date = $row["date_received"];

			/* check if field date_received is set */
			if (!$date) $date = $row["date"];

			#echo date("d-m-Y", $date);
			/* check if date is older than datetime */
			$ts = mktime(0,0,0,date("m")-$this->_archive_period,date("d"),date("Y"));

			/* get current table location for this email */
			$q = sprintf("select count(mail_id) from mail_messages_data where mail_id = %d",
				$id);
			$res = sql_query($q);
			if (sql_result($res,0) == 0)
				$in_archive = 1;
			else
				$in_archive = 0;

			if ($date < $ts && $row["folder_id"] == $this->_get_archive_id()) {
				/* move to archive */
				if (!$in_archive)
					$this->switchMail($id, "mail_messages_data", "mail_messages_data_archive");
			} else {
				/* move to current */
				if ($in_archive)
					$this->switchMail($id, "mail_messages_data_archive", "mail_messages_data");
			}
		}
	}
	/* }}} */
	/* switchMail {{{ */
	/**
	 * Switch mail between source and destination.
	 *
	 * This is used for email that is in the archive and a user needs it.
	 * It will be moved from the archive table to the current table. It
	 * is also used to store a mail in the archive table
	 *
	 * @param int $id The mail to move around
	 * @param string $src Sourcetable. Can be mail_messages_data or mail_messages_data_archive
	 * @param string $dest Destinationtable. Can be mail_messages_data or mail_messages_data_archive
	 */
	private function switchMail($id, $src, $dest) {
		$q = sprintf("select * from %s where mail_id = %d",
			$src, $id);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			$row = sql_fetch_assoc($res);

			$q = sprintf("delete from %s where mail_id = %d", $dest, $id);
			sql_query($q);

			$q = sprintf("insert into %s (mail_id,
				header, body, mail_decoding) values (%d, '%s', '%s', '%s')",
				$dest, $id,
				addslashes($row["header"]),
				addslashes($row["body"]),
				addslashes($row["mail_decoding"])
			);
			sql_query($q);

			$q = sprintf("delete from %s where mail_id = %d", $src, $id);
			sql_query($q);
		}
	}
	/* }}} */
	/* getEmailById {{{ */
	/**
	 * Fetch a specific mail
	 *
	 * @param int $id The mail message id
	 * @return array Mailinfo
	 */
	public function getEmailById($id) {
		/* check if the mail needs to be archived */
		$this->archiveEmail($id);

		/* get the email */
		require(self::include_dir."dataGetEmailById.php");

		/* return the data */
		return $data;
	}
	/* }}} */
	/* getEmailBySearch {{{ */
	public function getEmailBySearch($options, $start=0, $sort="") {
		require(self::include_dir."dataGetEmailBySearch.php");
		return $part;
	}
	/* }}} */
	/* getEmailBySearchAddSearchQuery {{{ */
	/**
	 * add search part to sql query to fetch emails
	 *
	 * @param string $sq The original sql query without search entries
	 * @param sting $search The search keywords
	 * @param int $use_subquery if set it will use MATCH fulltextsearch in a subquery
	 * @return string the new query we need to fire to the sql server
	 */
	private function getEmailBySearchAddSearchQuery($sq, $search, $use_subquery, $archive=0) {
		$like = sql_syntax("like");
		$esc  = sql_syntax("escape_char");

		if (!$archive) {
			/* generate timestamp */
			$ts = mktime(0,0,0,date("m")-$this->_archive_period,date("d"),date("Y"));
			$sq.= sprintf(" AND (date > %1\$d OR date_received > %1\$d) ", $ts);
		}

		$sq.= sprintf(" AND (subject %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR sender_emailaddress %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR ".$esc."to".$esc." %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR cc %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR bcc %s '%%%s%%' ", $like, $search);

		if ($use_subquery) {
			$sq.= sprintf(" OR id IN (select mail_id from mail_messages_data where body %1\$s '%%%2\$s%%')) ", $like, $search);
			if ($archive)
				$sq.= sprintf(" OR id IN (select mail_id from mail_messages_data_archive where body %1\$s '%%%2\$s%%')) ", $like, $search);

		} else {
			if (!$archive) {
				$sq.= sprintf(" OR mail_messages_data.body %1\$s '%%%2\$s%%')", $like, $search);
			} else {
				$sq.= sprintf(" OR mail_messages_data.body %1\$s '%%%2\$s%%'
					 OR mail_messages_data_archive.body %1\$s '%%%2\$s%%')", $like, $search);
			}

		}
		return $sq;
	}
	/* }}} */
	/* html2filter {{{ */
	public function html2filter($html) {
		require(self::include_dir."dataHtml2Filter.php");
		return $return;
	}
	/* }}} */
	/* html2text {{{ */
	public function html2text($html) {
		require(self::include_dir."dataHtml2Text.php");
		return $return;
	}
	/* }}} */
	/* autocomplete {{{ */
	public function autocomplete() {
		require(self::include_dir."autocomplete.php");
	}
	/* }}} */
	/* mail_delete_xml {{{ */
	public function mail_delete_xml() {
		/* delete the mail */
		$item   = $_REQUEST["id"];
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
		$archive = $this->_get_archive_id();

		/* retreive the current folder */
		$q = sprintf("select folder_id from mail_messages where id = %d", $item);
		$res = sql_query($q);
		$item_folder = sql_result($res,0);
		$folderinfo = $this->getFolder($item_folder);
		if ($folderinfo["high_parent"] == $archive || $folderinfo["id"] == $archive) {
			echo "history_goback();";
			return true;
		}

		if ($folderinfo["high_parent"] == $deleted_items["id"]) {
			/* real delete the email */
			$this->mail_delete($item);
		} else {
			/* move the email to deleted items */
			$q = sprintf("update mail_messages set folder_id = %d where id = %d", $deleted_items["id"], $item);
			sql_query($q);

			$this->archiveEmail($item);
		}
		/* generate history call */
		echo "history_goback();";
	}
	/* }}} */
	/* mail_delete_multi {{{ */
	/**
	 * Move multiple emails to 'Trash'
	 */
	public function mail_delete_multi() {
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
		$current_folder = $_REQUEST["folder_id"];

		$user_data = new User_data();
		$user_info = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		/* retrieve additional folder info */
		$folderinfo = $this->getFolder($_REQUEST["folder_id"]);
		$archive = $this->_get_archive_id();

		if ($folderinfo["high_parent"] == $archive || $current_folder == $archive) {
			return false;
		}

		/* if the highest parent is deleted items */
		if ($folderinfo["high_parent"] == $deleted_items["id"]) {
			$this->mail_delete_multi_real();
		} else {
			$mails = $_REQUEST["checkbox_mail"];
			if (is_array($mails)) {
				$ids = array();
				foreach ($mails as $k=>$v) {
					$ids[]=$k;
				}
				$ids = implode(",", $ids);

				$q = sprintf("update mail_messages set folder_id = %d where id IN (%s)", $deleted_items["id"], $ids);
				sql_query($q);

				$this->archiveEmail($ids);

				/* check if sync server expunge mailitems flag is active */
				if ($user_info["xs_funambol_expunge"])
					$this->expungeMailItem($ids);

			}
		}
	}
	/* }}} */
	/* expungeMailItem {{{ */
	public function expungeMailItem($ids) {
		/* prepare for mail expunge on the server */
		if (!is_array($ids))
			$ids = array($ids);

		if (count($ids) > 0) {
			$q = sprintf("update status_list set mark_expunge = 1 where user_id = %d AND
				mail_id IN (%s)", $_SESSION["user_id"], implode(",", $ids));
			sql_query($q);
		}
	}
	/* }}} */
	/* mail_delete_multi_real {{{ */
	/**
	 * really delete mail items
	 */
	public function mail_delete_multi_real() {

		$mails = $_REQUEST["checkbox_mail"];
		if (is_array($mails)) {
			foreach ($mails as $id=>$v) {
				/* check if sync server expunge mailitems flag is active */
				if ($user_info["xs_funambol_expunge"])
					$this->expungeMailItem($id);

				$this->mail_delete($id);
			}
		}
	}
	/* }}} */
	/* mail_delete {{{ */
	/**
	 * Delete a mail including all dependecies and attachments
	 *
	 * @param int $id The mail to trash
	 */
	public function mail_delete($id=0) {
		//check for archive and deleted-items
		$archive = $this->_get_archive_id();
		$q = sprintf("SELECT folder_id, user_id FROM mail_messages WHERE id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		if ($row["folder_id"] == $archive) {
			return false;
		}
		//check if this mail is in the deleted items of the user
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $row["user_id"]);
		if ($row["folder_id"] != $deleted_items["id"]) {
			//its not, but it can be in the folder of a user that had a folder shared with this user
			$sql = sprintf("SELECT name FROM mail_folders WHERE (parent_id = 0 OR parent_id IS NULL) AND id = %d", $row["folder_id"]);
			$r = sql_query($sql);
			$folderinfo = sql_fetch_assoc($r);
			if ($folderinfo["name"] != "Verwijderde-Items") {
				return false;
			}
		}
		//retrieve all attachments of the message
		$q = sprintf("select id from mail_attachments where message_id = %d", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			//delete attachment
			$this->mail_delete_attachment($row["id"]);
		}
		//delete the mail
		$q = sprintf("delete from mail_messages_data where mail_id = %d", $id);
		sql_query($q);

		$q = sprintf("delete from mail_messages_data_archive where mail_id = %d", $id);
		sql_query($q);

		$q = sprintf("delete from mail_projects where message_id = %d", $id);
		sql_query($q);

		$q = sprintf("delete from mail_messages where id = %d", $id);
		sql_query($q);

		//delete all tracking date
		$q = sprintf("delete from mail_tracking where mail_id = %d", $id);
		sql_query($q);

		/* check if sync server expunge mailitems flag is active */
		if ($user_info["xs_funambol_expunge"])
			$this->expungeMailItem($id);

		$file = $GLOBALS["covide"]->filesyspath."/maildata/".$id;
		@unlink($file);

	}
	/* }}} */
	/* mail_delete_attachment {{{ */
	/**
	 * Delete a mail attachment. Helper function for mail_delete
	 *
	 * @param int $id The attachment id to remove
	 */
	public function mail_delete_attachment($id=0) {
		//delete from filesys
		$q = sprintf("select name from mail_attachments where id = %d", $id);
		$res = sql_query($q);
		$name = sql_result($res,0);

		$fsdata = new Filesys_data();
		$ext = $fsdata->get_extension($name);

		$fspath = $GLOBALS["covide"]->filesyspath;
		$file = sprintf("%s/email/%s.%s", $fspath, $id, $ext);

		#@unlink ($file);
		$fsdata->FS_removeFile($file);

		$q = sprintf("delete from mail_attachments where id = %d", $id);
		sql_query($q);
	}
	/* }}} */
	/* getSpecialFolder {{{ */
	/**
	 * Function to grab a special folder (like Inbox or Archive)
	 *
	 * @param string $name The special folder's name
	 * @param int $user_id The userid of the folder
	 * @return array The folder info
	 */
	public function getSpecialFolder($name, $user_id=0) {
		$_user_id = (int)$user_id;
		if (!$user_id) {
			$user_id = "is null or user_id = 0";
		} else {
			$user_id = " = ".(int)$user_id;
		}
		$q = sprintf("select * from mail_folders where (parent_id is null OR parent_id = 0) and user_id %s and name = '%s'", $user_id, $name);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0) {
			/* folder is not there, CREATE ! */
			if ($_user_id) {
				$sql = sprintf("INSERT INTO mail_folders (parent_id, user_id, name) values (null, %d, '%s')",
					$_user_id, $name);
			} else {
				$sql = sprintf("INSERT INTO mail_folders (parent_id, user_id, name) VALUES (null, null, '%s')", $name);
			}
			sql_query($sql);
			$res = sql_query($q);
		}
		$folder = sql_fetch_assoc($res);
		return $folder;
	}
	/* }}} */
	/* checkSpecialFolder {{{ */
	/**
	 * Checks if given folder is a special one
	 *
	 * @param int $id The folder id to check
	 * @return int 1 if folder is special, 0 if folder is a user defined folder
	 */
	public function checkSpecialFolder($id) {
		$q = sprintf("select parent_id, name from mail_folders where id = %d", $id);
		$res = sql_query($q);
		$parent = sql_result($res, 0, "parent_id");
		$name   = sql_result($res, 0, "name");

		if ($parent) {
			$user_data = new User_data();
			$q = sprintf("select user_id from mail_folders where id = %d", $parent);
			$res = sql_query($q);
			$user = sql_result($res,0);

			if ($user_data->getUserNameById($user) == "archiefgebruiker") {
				/* check if form page does still exist */
				$q = sprintf("select count(*) from cms_data where isForm = 1 and id = %d", $name);
				$res = sql_query($q);
				if (sql_result($res,0) == 1)
					return 1;
			}
			return 0;
		} else {
			return 1;
		}
	}
	/* }}} */
	/* change_description {{{ */
	/**
	 * Change 'memo' field content for an email
	 */
	public function change_description() {
		$id          = $_REQUEST["id"];
		$description = urldecode($_REQUEST["description"]);

		$q = sprintf("update mail_messages set description = '%s' where id = %d", $description, $id);
		sql_query($q);

		echo sprintf("document.getElementById('description_notify').innerHTML = '%s';", gettext("- saved -"));
	}
	/* }}} */
	/* change_folder_xml {{{ */
	/**
	 * AJAX method to put a mail message in a folder
	 */
	public function change_folder_xml() {
		$id     = $_REQUEST["id"];
		$folder = $_REQUEST["folder"];
		$description = addslashes($_REQUEST["description"]);

		$q = sprintf("update mail_messages set folder_id = %d, description = '%s' where id = %d", $folder, $description, $id);
		sql_query($q);

		echo "if (document.getElementById('mailnojump').checked == false) {	history_goback(); }";
	}
	/* }}} */
	/* toggle_private_state {{{ */
	/**
	 * AJAX method to toggle the 'mail is private' state of an email.
	 *
	 * @param int $id Id of the email.
	 * @param $output variable to save xml output in case this function
	 *                is called from the toggle_private_state_xml function.
	 */
	public function toggle_private_state($id, $output = null) {
		$q = sprintf("select is_public from mail_messages where id = %d", $id);
		$res = sql_query($q);
		$state = sql_result($res,0);

		if ($state == 2) {
			/* state is private, set to public */
			$new_state = 0;
			if ($output != null) {
				$output->addCode( gettext("this email is public") );
				$output->insertAction("state_public", gettext("this email is public"), "");
			}
		} else {
			/* state is public, set to private */
			$new_state = 2;
			if ($output != null) {
				$output->addCode( gettext("this email is private") );
				$output->insertAction("state_private", gettext("this email is private"), "");
			}
		}
		$q = sprintf("update mail_messages set is_public = %d where id = %d", $new_state, $id);
		sql_query($q);

	}

	/* }}} */
	/* toggle_private_state_xml() {{{ */
	/**
	 * AJAX method to toggle the 'mail is private' state of an email. Returns response
	 * in XML format.
	 */
	public function toggle_private_state_xml() {
		$output = new Layout_output();
		$this->toggle_private_state( $_REQUEST['id'], $output );
		echo sprintf("document.getElementById('private_state').innerHTML = '%s';",
			str_replace("'", "\\'", $output->generate_output()) );
	}

	/* }}} */
	/* change_relation_list {{{ */
	/**
	 * AJAX method to alter the 'this mail is linked to a contact' status icon
	 */
	public function change_relation_list() {
		$this->change_relation();
		echo "document.getElementById('relation_".$_REQUEST["id"]."').style.display = 'none';";
		//generate icon for 'linked to relation'
		$output = new Layout_output();
		$output->insertAction("addressbook", gettext("this email is linked to a contact"), "javascript: popup('?mod=address&action=relcard&id=".$_REQUEST["new_relation"]."');");
		$html = $output->generate_output();
		echo sprintf("document.getElementById('addresslink_%d').innerHTML = '%s';", $_REQUEST["id"], addslashes($html));
		echo "hideInfoLayer();";
	}
	/* }}} */
	/* change_relation {{{ */
	/**
	 * AJAX method to alter the linked contact of an email
	 */
	public function change_relation() {
		$id       = $_REQUEST["id"];
		$relation = $_REQUEST["new_relation"];
		if ($relation < 0) {
			$relation = 0;
		}

		$q = sprintf("update mail_messages set askwichrel = 0, address_id = %d where id = %d", $relation, $id);
		//$q.= sprintf(" and (user_id = 0 or user_id is null or user_id = %d)", $_SESSION["user_id"]);
		sql_query($q);

		if ($_REQUEST["update"]) {
			// prevent double escaping, because we could get already escaped data from a js.
			$val = stripslashes($_REQUEST["update"]);
			echo sprintf("document.getElementById('layer_mail_relation').innerHTML = '%s';", addslashes($val));
			echo sprintf("document.getElementById('mailrelation').value = '%d';", addslashes($relation));
		}
	}
	/* }}} */
	/* validateEmail {{{ */
	/**
	 * Check if given email address is a syntactically correct email address
	 *
	 * @param string $v The email address to validate
	 * @return bool true on success and false on failure
	 */
	public function validateEmail($v) {
		$v = trim($v);
		/*
		next preg_match is taken from http://nl3.php.net/preg_match comment
		of Niklas Åkerlund posted on Sept 13 2006 05:23

		Fred Schenk came up with the idea based on the RFC. His description is:
		I've seen some regexp's here to validate email addresses. I just wanted to add my own version which accepts the following formats in front of the @-sign:
		1. A single character from the following range: a-z0-9!#$%&*+-=?^_`{|}~
		2. Two or more characters from the previous range including a dot but not starting or ending with a dot
		3. Any quoted string

		After the @-sign there is the possibility for any number of subdomains, then the domain name and finally a two letter TLD or one of the TLD's specified.

		And finally the check is case insensitive.
		*/
		$pat  = "/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*";
		$pat .= "@([-a-z0-9]+\.)+([a-z]{2,3}";
		$pat .= "|info|arpa|aero|coop|name|museum)$/ix";
		if (preg_match($pat, $v) && !strstr($v, ":"))
			return true;
		else
			return false;
	}
	/* }}} */
	/* change_project_xml {{{ */
	/**
	 * AJAX method to change the project linked to an email
	 */
	public function change_project_xml() {
		$id       = $_REQUEST["id"];
		$project  = $_REQUEST["project"];
		if ($project < 0) {
			$project = 0;
		}

		$q = sprintf("select * from mail_projects where message_id = %d and project_id = %d", $id, $project);
		$res = sql_query($q);
		if(sql_num_rows($res) > 0) {
			$name = "";
		} else {
			$q = sprintf("insert into mail_projects (message_id, project_id) values(%d, %d)", $id, $project);
			sql_query($q);
		}

		if ($project) {
			$project_data = new Project_data();
			$q = sprintf("select * from mail_projects where message_id = %d", $id);
			$res = sql_query($q);

			if(sql_num_rows($res)>0) {
				while($row= sql_fetch_assoc($res)) {
					$name[$row["project_id"]] = $project_data->getProjectNameById($row["project_id"]);
				}
			}
			echo("document.getElementById('project_name').innerHTML = '';");
			foreach($name as $key => $value) {
				echo("document.getElementById('project_name').innerHTML += '<a href=\"javascript:popup(\'?mod=project&action=showhours&id=$key\');\">$value</a>'+'\t\t'+'<a href=\"javascript:deleteProject(\'$key\');\" title=\"remove\">[X]</a>'+'\t\t,\t\t';");
			}
		} else {
			echo("document.getElementById('project_name').innerHTML = '';");
			$q = sprintf("delete from mail_projects where message_id = %d", $id);
			$res = sql_query($q);
			$name = gettext("none");
			echo("document.getElementById('project_name').innerHTML = '$name';");
		}
		echo("document.getElementById('mailproject_id').value = '$project';");
	}
	/* }}} */
	/* delete_project_xml {{{ */
	/**
	 * AJAX method to delete the project linked to an email
	 */
	public function delete_project_xml() {
		$id       = $_REQUEST["id"];
		$project  = $_REQUEST["project"];

		$q = sprintf("delete from mail_projects where message_id = %d and project_id = %d", $id, $project);
		sql_query($q);

		$q = sprintf("select * from mail_projects where message_id = %d", $id);
		$res = sql_query($q);

		if(sql_num_rows($res)>0) {
			$project_data = new Project_data();
			while($row= sql_fetch_assoc($res)) {
				$name[$row["project_id"]] = $project_data->getProjectNameById($row["project_id"]);
			}
			foreach($name as $key => $value) {
				echo("document.getElementById('project_name').innerHTML += '<a href=\"javascript:popup(\'?mod=project&action=showhours&id=$key\');\">$value</a>'+'\t\t'+'<a href=\"javascript:deleteProject(\'$key\');\" title=\"remove\">[X]</a>'+'\t\t,\t\t';");
			}
		} else {
			$name = gettext("none");
			echo("document.getElementById('project_name').innerHTML = '$name';");
		}
	}
	/* }}} */
	/* remove_script_tags {{{ */
	/**
	 * Remove javascript tags from given string
	 *
	 * @param string $html The raw html data
	 * @return string html string with script tags removed
	 */
	public function remove_script_tags($html) {
		$conversion = new Layout_conversion();
		$html = $conversion->filterTags($html);
		return $html;
	}
	/* }}} */
	/* getEmailSignature {{{ */
	public function getEmailSignature($id) {
		$this->get_signature_list(); #migration

		if ($id>0) {
			$q = sprintf("select signature from mail_signatures where id = %d", $id);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		} else {
			$q = sprintf("select mail_signature from users where id = %d order by id", $_SESSION["user_id"]);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		}
		return $sig;
	}
	/* }}} */
	/* getEmailSignatureHtml {{{ */
	public function getEmailSignatureHtml($id) {
		$this->get_signature_list(); #migration

		if ($id>0) {
			$q = sprintf("select signature_html from mail_signatures where id = %d", $id);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		} else {
			$q = sprintf("select mail_signature_html from users where id = %d order by id", $_SESSION["user_id"]);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		}
		return $sig;
	}
	/* }}} */
	/* getEmailAliases {{{ */
	public function getEmailAliases() {
		$email = array();
		$user = new User_data();
		$userinfo = $user->getUserdetailsById($_SESSION["user_id"]);

		$email[ gettext("default") ][-1] = "&lt;".$userinfo["mail_email"] ."&gt; ".gettext("default email address");
		if (trim($userinfo["mail_email1"])) {
			$email[ gettext("default") ][-2] = "&lt;".$userinfo["mail_email1"] ."&gt; ".gettext("alternate email address");
		}

		$q = sprintf("select * from mail_signatures where user_id = %d", $_SESSION["user_id"]);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* only added non-default alternatives */
			if ($row['default'] == 0) {
				$email[ gettext("alternative signatures") ][$row["id"]] = "&lt;".$row["email"]."&gt; ".$row["subject"];
			}
		}
		return $email;
	}
	/* }}} */
	/* getEmailAliasById {{{ */
	public function getEmailAliasById($id) {
		$q = sprintf("select email, realname, companyname from mail_signatures where id = %d and user_id = %d", $id, $_SESSION["user_id"]);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}
	/* }}} */
	/* getEmailAliasesPlain {{{ */
	public function getEmailAliasesPlain($user_id = 0) {
		if (!$user_id) {
			$user_id = $_SESSION["user_id"];
		}
		$email = array();
		$user = new User_data();
		$userinfo = $user->getUserdetailsById($user_id);

		$email[-1] = $userinfo["mail_email"];
		$email[-2] = $userinfo["mail_email1"];

		$q = sprintf("select * from mail_signatures where user_id = %d", $user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$email[$row["id"]] = $row["email"];
		}
		return $email;
	}
	/* }}} */
	/* checkMailRepair {{{ */
	/**
	 * If for some reason mail comes in over and over again, these 2 functions will prevent it from showing to the user
	 */
	public function checkMailRepair() {
		$archive = $this->_get_archive_id();

		//check for double email combi msg_id/user_id, this should be unique
		$q = sprintf("select message_id from mail_messages where user_id = %d", $_SESSION["user_id"]);
		$q.= sprintf(" and folder_id != %d group by message_id having count(message_id) > 1", $archive);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["message_id"]) {
				$this->repairMail($row["message_id"], $archive);
			}
		}
	}
	/* }}} */
	/* repairMail {{{ */
	public function repairMail($msgid, $archief) {
		$buf = "";
		$q = sprintf("select id, is_new from mail_messages where message_id = '%s'", addslashes($msgid));
		$q.= sprintf(" and user_id = %d", $_SESSION["user_id"]);
		$q.= sprintf(" and folder_id != %d order by id", $archief);
		$res = sql_query($q);
		$record=0;

		//get all ids from the current user/message_id
		while ($row = sql_fetch_assoc($res)) {
			if ($record > 0) {
				//only remove if mail is new/unread
				if ($row["is_new"]==1) {

					$buf .= "<span class='d'><center>".gettext("information").": ".gettext("folders/disk cleaned")."</center></span><br>";
					//get fspath
					$fspath = $GLOBALS["covide"]->filesyspath;

					//first get attachments
					$q = sprintf("select id from mail_attachments where message_id = %d", $row["id"]);
					$res2 = sql_query($q);
					while ($row2= sql_fetch_assoc($res2)) {
						$db_id=$row2["id"];
						$mijnFile = ($fspath."/email/".$db_id.".dat");
						@unlink($mijnFile);
					}

					$sql = sprintf("delete from mail_attachments where message_id = %d", $row["id"]);
					sql_query($sql);
					$sql = sprintf("delete from mail_projects where message_id = %d", $row["id"]);
					sql_query($sql);
					//now remove the mail
					$sql = sprintf("delete from mail_messages where id = %d", $row["id"]);
					sql_query($sql);
				}
			}
			$record++;
		}
		return $buf;
	}
	/* }}} */
	/* getAttachmentIdByCid {{{ */
	public function getAttachmentIdByCid($mail_id, $cid) {
		$q = sprintf("select id from mail_attachments where cid = '<%s>' and message_id = %d", $cid, $mail_id);
		$res = sql_query($q);
		if (sql_num_rows($res)==1) {
			return sql_result($res,0);
		} else {
			return false;
		}
	}
	/* }}} */
	/* getAttachment {{{ */
	public function getAttachment($id, $fetchdata=0) {
		$q = sprintf("select * from mail_attachments where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$fsdata = new Filesys_data();
		$row = $fsdata->detect_preview($row);

		$conversion = new Layout_conversion();
		$row["name"]   = $conversion->decodeMimeString($row["name"]);
		$row["h_size"] = $conversion->convert_to_bytes($row["size"]);
		unset($conversion);

		if ($fetchdata == 2) {
			/* retrieve from filesys */
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fsdata = new Filesys_data();

			$ext = $fsdata->get_extension($row["name"]);
			$file = sprintf("%s/email/%s.%s", $fspath, $id, $ext);

			$row["data_file"] = $file;
			$row["data_has_binary"] = $fsdata->FS_checkFile($file);

		} elseif ($fetchdata == 1) {
			/* retrieve from filesys */
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fsdata = new Filesys_data();

			$ext = $fsdata->get_extension($row["name"]);
			$file = sprintf("%s/email/%s.%s", $fspath, $id, $ext);

			$row["data_file"] = $file;
			$row["data_binary"] = $fsdata->FS_readFile($file);
		}
		return $row;
	}
	/* }}} */
	/* showTemplateFile {{{ */
	private function showTemplateFile($id) {
		$id = preg_replace("/[^0-9]/s", "", $id);
		if ($id) {
			$cmsData = new Cms_data();
			$cmsData->getCmsFile((int)$id, 1);
		}
	}
	/* }}} */
	/* downloadAttachment {{{ */
	public function downloadAttachment($id) {
		$data = $this->getAttachment($id, 1);

		$conversion = new Layout_conversion();
		$conversion->convertFilename($data["name"]);

		$conversion->filterNulls($data["name"]);
		$conversion->filterNulls($data["type"]);

		header("Content-Transfer-Encoding: binary");
		header("Content-Type: ".strtolower($data["type"]));

		#if (!$_SERVER["HTTPS"] && $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] != "https")
		#	header("Content-Length: ".strlen($data["data_binary"]));


		if (!$_REQUEST["view_only"]) {
			if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
				header('Content-Disposition: filename="'.$data["name"].'"'); //msie 5.5 header bug
			} else {
				header('Content-Disposition: attachment; filename="'.$data["name"].'"');
			}
		}
		echo $data["data_binary"];
		exit();
	}
	/* }}} */
	/* _get_archive_id {{{ */
	/**
	 * Get the folderid of the email archive
	 *
	 * @return int The folderid
	 */
	public function _get_archive_id() {
		$q = "select id from mail_folders where (parent_id is null or parent_id = 0) and (user_id is null or user_id = 0) and name = 'Archief'";
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$this->archive = $row["id"];
		return ($this->archive);
	}
	/* }}} */
	/* _init_mail_archive {{{ */
	/**
	 * Fill $this->_folders with all folders below $folder_id
	 *
	 * @param int $folder_id The id to base the folder info on
	 *
	 * @return void
	 */
	public function _init_mail_archive($folder_id) {
		$q = sprintf("select * from mail_folders where id = %d", $folder_id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$this->_folders[$row["id"]] = $row;

		$q = sprintf("select id from mail_folders where parent_id = %d order by name", $folder_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->_init_mail_archive($row["id"]);
		}
	}
	/* }}} */
	/* mapUsage {{{ */
	/**
	 * Return approx size of all mails in a folder
	 *
	 * @param int $folder_id The folder to calculate the size of mails for
	 * @param int $bid If set, limit the stuff on this relation id (address_id database field)
	 *
	 * @return int The total size in bytes
	 */
	public function mapUsage($folder_id, $bid=0) {
		//sum approx. size in this folder
		$size = 0;
		$q = sprintf("select sum(length(mail_messages_data.body)) as size from mail_messages left join mail_messages_data on mail_id = mail_messages.id where folder_id = %d", $folder_id);
		if ($bid) {
			$q.= sprintf(" and address_id = %d", $bid);
		}
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$size += (int)$row["size"];

		$casttype = sql_syntax("casttype");

		//now sum it on attachments
		$q = sprintf("select sum( cast( replace(replace(a.size,',00',''),'.','') as %s) ) as size from mail_attachments a, mail_messages b where a.message_id = b.id and a.size > 0 and folder_id = %d", $casttype, $folder_id);
		if ($bid) {
			$q.= sprintf(" and address_id = %d", $bid);
		}
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$size += (int)$row["size"];

		return $size;
	}
	/* }}} */
	/* _init_mail_mappen {{{ */
	/**
	 * Populate $this->_folders with all folders of loggedin user
	 *
	 * @todo Get rid of $_SESSION["user_id"] and make this a function parameter
	 *
	 * @param int $init_archive if set, add mail archive folders
	 *
	 * @return void
	 */
	public function _init_mail_mappen($init_archive) {
		$q = sprintf("select * from mail_folders where user_id = %d order by name", $_SESSION["user_id"]);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->_folders[$row["id"]] = $row;
		}

		if ($init_archive==1) {
			$archive = $this->_get_archive_id();
			$this->_init_mail_archive($archive);
		}
	}
	/* }}} */
	/* decodeEmailAddress {{{ */
	/**
	 * Replace < and > with their html entity
	 *
	 * @param string $ow Email address string
	 *
	 * @return string the string with < and > replaced with &lt; and &gt;
	 */
	public function decodeEmailAddress($ow) {
		$ow = str_replace("<", "&lt;", $ow);
		$ow = str_replace(">", "&gt;", $ow);
		return $ow;
	}
	/* }}} */
	/* decodeMimeString {{{ */
	/**
	 * Decode a mime-encoded string to plain UTF-8
	 *
	 * @param string $ow The mime-encoded string
	 *
	 * @return string UTF-8 version of the mime-encoded input
	 */
	public function decodeMimeString($ow) {
		$conversion = new Layout_conversion();
		return $conversion->decodeMimeString($ow);
	}
	/* }}} */
	/* _getSubFolders {{{ */
	/**
	 * Get folder structure based on starting folder
	 *
	 * @param int $folder The base folder to start from
	 * @param array $folders The folders structure array
	 * @param int $level The number of levels deep based from start folder
	 *
	 * @return void
	 */
	private function _getSubFolders($folder, &$folders, $level) {
		$level++;
		$q = sprintf("select id, name from mail_folders where parent_id = %d order by name", $folder);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$folders[$row["id"]] = array(
				"id"     => $row["id"],
				"name"   => $row["name"],
				"level"  => $level,
				"count"  => 0,
				"unread" => 0,
				"shared" => $this->checkSharePermissions($row["id"])
			);
			$this->_getSubFolders($row["id"], $folders, $level);
		}
	}
	/* }}} */
	/* getParentFolder {{{ */
	/**
	 * Get highest parent folder the given folder is in
	 *
	 * @param int $id The folder to check the base for
	 *
	 * @return int The highest parent folder
	 */
	public function getParentFolder($id) {
		$q = sprintf("select parent_id from mail_folders where id = %d", $id);
		$res = sql_query($q);
		$parent = sql_result($res,0);
		if ($parent > 0) {
			return $this->getParentFolder($parent);
		} else {
			return $id;
		}
	}
	/* }}} */
	/* getFolder {{{ */
	/**
	 * Get all the info of a mailfolder
	 *
	 * @param int $id The folder in question
	 * @param int $show_count If set, grab number of total and unread mails in this folder
	 *
	 * @return array The folder information with optionally the total count and unread count
	 */
	public function getFolder($id, $show_count=0) {
		$q = sprintf("select * from mail_folders where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$row["high_parent"] = $this->getParentFolder($id);
		if ($show_count) {
			$q = sprintf("select sum(is_new) as unread, count(*) as nr from mail_messages where folder_id = %d", $id);
			$res2 = sql_query($q);
			$row2 = sql_fetch_assoc($res2);
			$row["count"] = $row2["nr"];
			$row["unread"] = $row2["unread"];
		}
		return $row;
	}
	/* }}} */
	/* checkSharePermissions {{{ */
	/**
	 * Check if given folder is shared
	 *
	 * @param int $folder The folder id to check
	 *
	 * @return int 1 if the folder is shared, 0 otherwise
	 */
	private function checkSharePermissions($folder) {
		$q = "select count(*) from mail_permissions where folder_id = ".$folder;
		$res2 = sql_query($q);
		if (sql_result($res2,0)>0) {
			return 1;
		} else {
			return 0;
		}
	}
	/* }}} */

	public function getFolders($options="", $init_archive=0) {
		/* special names for toplevel folders */
		$special_names = array(
			"Postvak-IN" => gettext("Received messages"),
			"Concepten" => gettext("Draft messages"),
			"Bounced berichten" => gettext("Bounced messages"),
			"Verwijderde-Items" => gettext("Deleted messages"),
			"Verzonden-Items" => gettext("Sent messages"),
		);
		$folders = array();
		$archive = $this->getSpecialFolder("Archief", 0);

		if (!is_array($options)) {
			$options = array($options);
		}
		if (!$options["user"]) {
			$options["user"] = $_SESSION["user_id"];
		}

		if ($options["relation"] || $options["project_id"]) {
			$sq = " or ((user_id is null or user_id is null) and name = 'Archief') ";
		}

		if ($init_archive > 0 || $options["relation"]) {
			$q = sprintf("select * from mail_folders where id = %d", $archive["id"]);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			if ($options["relation"]) {
				$q = sprintf("select count(*) from mail_messages where folder_id = %d and address_id = %d and (is_public IN (0,1) OR is_public IS NULL OR user_id = %d) ", $row["id"], $options["relation"], $options["user"]);
				$res2 = sql_query($q);
				$count = sql_result($res2,0);
			}
			$folders[$row["id"]] = array(
				"id"     => $row["id"],
				"name"   => gettext("Archive"),
				"level"  => 0,
				"count"  => $count,
				"unread" => 0,
				"archive"=> 1,
				"shared" => $this->checkSharePermissions($row["id"])
			);
		}
		if(!$options["parent"]["id"]) {
			$parentQuery = "(parent_id = 0 or parent_id is null)";
		} else {
			$parentQuery = "parent_id = ".$options["parent"]["id"];
		}
		$q = sprintf("select id, name from mail_folders where %s ", $parentQuery);
		$q .= sprintf("and (user_id = %d %s) order by name", $options["user"], $sq);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if (!$folders[$row["id"]]) {
				$folders[$row["id"]] = array(
					"id"     => $row["id"],
					"name"   => (array_key_exists($row["name"], $special_names)?$special_names[$row["name"]]:$row["name"]),
					"level"  => 0,
					"count"  => $count,
					"unread" => 0,
					"shared" => $this->checkSharePermissions($row["id"])

				);
			}
			$this->_getSubFolders($row["id"], $folders, 0);
		}

		if ($options["count"]) {

			$sent_items    = $this->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
			$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);

			$_ids = array();
			foreach ($folders as $v) {
				if ($v["id"] != $archive["id"]) {
					$_ids[] = $v["id"];
				}
			}
			if (count($_ids)>0) {
				$_ids = implode(",", $_ids);
				$q = sprintf("select sum(is_new) as unread, count(*) as nr, folder_id from mail_messages where folder_id IN (%s) group by folder_id order by folder_id", $_ids);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					if ($row["folder_id"] == $sent_items["id"]
						|| $row["folder_id"] == $deleted_items["id"]
						|| $row["folder_id"] == $archive["id"]) {

						/* force no unread items on folder */
						$folders[$row["folder_id"]]["unread"] = 0;
					} else {
						$folders[$row["folder_id"]]["unread"] = $row["unread"];
					}
					if ($row["folder_id"] != $archive["id"]) {
						$folders[$row["folder_id"]]["count"] = $row["nr"];
					}
				}
			}
		}

		return $folders;
	}


	public function update_folder_readstatus($folder_id, $address_id=0) {
		$qc = sprintf("select count(*) from mail_messages where is_new = 1 and folder_id = %d ", $folder_id);
		$qu = sprintf("update mail_messages set is_new = 0 where folder_id = %d ", $folder_id);

		if ($address_id) {
			$qc.= sprintf(" and address_id = %d ", $address_id);
			$qu.= sprintf(" and address_id = %d ", $address_id);
		}
		$res = sql_query($qc);
		if (sql_result($res,0) > 0) {
			sql_query($qu);
		}
	}

	public function parseAddressStrict($to){
		return ( trim( preg_replace("/, {0,}/si",",\n ",$to) ) );
	}

	public function parseAddress($str) {
		$arr = explode(",",$str);
		for ($i=0;$i<count($arr);$i++) {
			//remove spaces
			$arr[$i] = trim($arr[$i]);
			//remove realnames and comments and anti-spam hacks
			if (preg_match("/<.*>/",$arr[$i])) {
				$arr[$i] = preg_replace("/<([^>]*?)>/","$1",$arr[$i]);
			}
			$arr[$i] =("\"".$arr[$i]."\" <".($arr[$i]).">");
		}
		$str = implode(",",$arr);
		$str = preg_replace("/,/si",",\n\t",$str);
		return ($str);
	}
	public function cleanAddress($str) {
		$str = preg_replace("/(\r)|(\t)|(\n)/s","",$str);

		$str = preg_replace("/\"[^\"]*?\"/s", "", $str);

		$arr = explode(",",$str);
		for ($i=0;$i<count($arr);$i++) {
			//remove spaces
			$arr[$i] = trim($arr[$i]);
			//remove realnames and comments and anti-spam hacks
			if (preg_match("/<.*>/",$arr[$i])) {
				$arr[$i] = preg_replace("/^.*<([^>]*?)>.*$/s","$1",$arr[$i]);
			}
		}
		$str = implode(",",$arr);
		$str = preg_replace("/,/si",",\n\t",$str);

		/* extra filter */
		$str = preg_replace("/\"[^\"]*?\"/s", "", $str);
		$str = trim(strtolower($str));

		$str = preg_replace("/( |\n|\r|\t)/s", "", $str);
		$str = trim( preg_replace("/\,{1,}/s", ", ", $str) );
		#replace comma at end of string with nothing
		$str = preg_replace("/\,$/s", "", $str);
		#replace single quote with nothing
		$str = str_replace("'", "", $str);

		return ($str);
	}

	public function generateRandInt($pos) {
		$bit=0;
		for ($i=0;$i<$pos-1;$i++) {
			$bit.=rand(0,9);
		}
		return ($bit);
	}

	public function generate_message_id($email="covide@covide.local") {
		$suffix = preg_replace("/^([^\@]*?\@)/i","",$email);
		if (!$suffix) {
			$suffix = "localhost";
		}
		$msg_id = ( strtoupper(dechex(time())).".".$this->generateRandInt(7)."@".$suffix );
		return $msg_id;
	}

	public function updateReadStatus($mail_id, $user_id) {
		// grab old status so we can return it
		$q = sprintf("SELECT is_new FROM mail_messages WHERE id = %d", $mail_id);
		$res = sql_query($q);
		$oldstatus = sql_result($res, 0);
		$q = sprintf("update mail_messages set is_new = 0 where id = %d", $mail_id);
		sql_query($q);

		/* check if sync server expunge mailitems flag is active */
		$user_data = new User_data();
		$user_info = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		if ($user_info["xs_funambol_expunge"]) {
			$this->expungeMailItem($mail_id);
		}

		return $oldstatus;
	}

	public function br2nl($str) {
		$str = preg_replace("/<br[^>]*?>/sxi", "\n", $str);
		$str = strip_tags($str);
		return $str;
	}

	public function save_tracker_item($data="") {

		/* prepare the data */
		$fields["mail_id"]             = array("d", $data["mail_id"]);

		$fields["mail_id_2"]           = array("d", $data["mail_id_2"]);
		$fields["timestamp_first"]     = array("d", $data["timestamp_first"]);
		$fields["timestamp_last"]      = array("d", $data["timestamp_last"]);
		$fields["count"]               = array("d", $data["count"]);
		$fields["clients"]             = array("s", $data["clients"]);
		$fields["agents"]              = array("s", $data["agents"]);
		$fields["count"]               = array("d", $data["count"]);
		$fields["hyperlinks"]          = array("s", $data["hyperlinks"]);

		$fields["mailcode"]            = array("s", $data["mailcode"]);
		$fields["email"]               = array("s", $data["email"]);
		$fields["address_type"]        = array("s", $data["address_type"]);
		$fields["address_id"]          = array("d", $data["address_id"]);


		if (is_array($data)) {
			if ($data["id"]) {

				unset($fields["mail_id"]);

				/* record does already exists */
				$vals = array();
				foreach ($fields as $k=>$v) {
					if ($v[0]=="s") {
						$vals[$k]="'".addslashes( $v[1] )."'";
					} else {
						$vals[$k]=(int)$v[1];
					}
				}
				$q = sprintf("update mail_messages set mail_id = %d ", (int)$data["mail_id"]);
				foreach ($vals as $k=>$v) {
					$q.= sprintf(", %s = %s ", $k, $v);
				}
				$q.= sprintf(" where id = %d", $id);
				sql_query($q);

			} else {
				/* record does not exists */
				$keys = array();
				$vals = array();
				foreach ($fields as $k=>$v) {
					$keys[] = $k;
					if ($v[0]=="s") {
						$vals[]="'".addslashes( $v[1] )."'";
					} else {
						$vals[]=(int)$v[1];
					}
				}
				$keys = implode(",",$keys);
				$vals = implode(",",$vals);

				$q = sprintf("insert into mail_tracking (%s) values (%s)", $keys, $vals);
				sql_query($q);
			}
		}
	}

	public function get_current_tracker_item($mail_id) {
		$q = sprintf("select * from mail_tracking where mail_id = %d and (is_sent = 0 OR is_sent is null) order by id limit 1", $mail_id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_assoc($res);
			$email    = $row["email"];
			$id       = $row["id"];
			$mailcode = $row["mailcode"];
			return array($id, $email, $mailcode);
		} else {
			return 0;
		}
	}

	public function update_tracker_item_sent($id, $return_code=true) {
		if ($return_code == false) {
			$q = "update mail_tracking set is_sent = 2 where id = ".$id;
		} else {
			$q = "update mail_tracking set is_sent = 1 where id = ".$id;
		}
		sql_query($q);
	}

	public function status_queue($id) {
		$q = sprintf("select count(*) from mail_tracking where mail_id = %d and is_sent > 0", $id);
		$res = sql_query($q);
		$num = sql_result($res,0);
		/*
		echo $num;
		exit();
		*/
		return $num;
	}

	public function send_queue() {

		set_time_limit(60*60*1); //1 hours

		$id = $_REQUEST["id"];
		$file = $_REQUEST["datafile"];
		$from = $_REQUEST["from"];
		$newsletter_target = $_REQUEST["newsletter_target"];

		$filename = $GLOBALS["covide"]->temppath.$file;
		$handle = fopen($filename, "rb");
		$data = fread($handle, filesize($filename));
		fclose($handle);

		$address_data = new Address_data();
		$blocksize = 25;

		session_write_close();

		$list = $this->get_tracker_items($id, "", $blocksize+1, 1); //not sure about the +1, but it cannot harm
		$i = 1;
		foreach ($list["list"] as $tracker) {

			if ($i % ($blocksize + 1) == 0) {
				$num = $this->status_queue($id);

				/* using the mothod below, non-multipart aware browsers will ignore the
					-- boundaries and print them as text in the hidden iframe */
				$buffer = "";
				$buffer.= "<script>parent.updateCurrentStatus('$num');</script>\n";
				echo $buffer;
				exit();
			}

			if ($tracker["is_sent"] < 1) {
				$record   = $tracker["id"];
				$email    = $tracker["email"];
				$mailcode = $tracker["mailcode"];

				if (strlen(trim($email)) && $this->validateEmail($email)) {
					/* create smtp object */
					$smtp = new Email_smtp();

					/* make a copy */
					$tmp_data = $data;
					$tmp_data = str_replace("%%EMAILADDRLINK%%", "&email=3D".trim($email), $tmp_data);
					// replace the maillink code
					$cmnt = $address_data->lookupRelationEmailCommencement($email, $newsletter_target);

					$this->insertTracking($tmp_data, $mailcode, $email, $cmnt);

					$smtp->set_data($tmp_data);

					$smtp->clear_rcpt();
					$smtp->set_extraheader("To: ".$email);
					$smtp->add_rcpt($email);
					$smtp->set_from($from);

					$ret = $smtp->send();
					unset($smtp);
				} else {
					$ret = 2;
				}
				$this->update_tracker_item_sent($record, $ret);
				unset($tracker);
				$i++;
			}
		}
		$folder  = $this->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
		$q = sprintf("update mail_messages set sender_emailaddress = '%s', is_new = 0, folder_id = %d where id = %d", $from, $folder["id"], $id);
		sql_query($q);

		$buffer = "";
		$buffer.= "<script>parent.updateCurrentStatus('".$this->status_queue($id)."');</script>\n";

		ob_end_clean();
		ob_start();

		echo $buffer;

		unlink($filename);
		exit();
	}

	public function insertTracking(&$data, $mailcode, $email, $cmnt) {
		$tracking_data = preg_split("/(\#(=(\r){0,1}\n){0,1}\#)/s", $data, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		$i = 0;
		$matches = array();
		$crlf = (strstr($data, "\r\n")) ? "\r\n":"\n";

		foreach ($tracking_data as $k=>$v) {
			$v = preg_replace("/\#=(\r){0,1}\n\#/s", "##", $v);
			if (trim($v) == "##") {
				$i++;
				if ($i % 2 == 0) {
					$m = sprintf("%s%s%s", $tracking_data[$k-2], $tracking_data[$k-1], $tracking_data[$k]);

					$matches[0][] = $m;
					$matches[1][] = trim(preg_replace("/=(\r){0,1}\n/s", "", $m));
					$matches[2][] = $crlf;
				}
			}
		}

		/* insert tracker vars */
		/* =\n => ensure quoted printable wrap position */
		foreach ($matches[0] as $k=>$v) {
			switch ($matches[1][$k]) {
				case "##trackerid##":
					$repl = $email;
					break;
				case "##mailcode##":
					$repl = $mailcode;
					break;
				case "##rcptcmnt##":
					$repl = $cmnt;
					break;
				default:
					$repl = "";
			}
			if ($repl) {
				$repl = sprintf("%2\$s%1\$s%2\$s", $repl, $matches[2][$k]);
				$repl = wordwrap($repl, 70, "=".$matches[2][$k]);
				$data = str_replace($v, $repl, $data);
			}
		}
	}

	public function get_tracker_items($mail_id, $id="", $limit=0, $only_queue=0, $count_only=0) {
		$emails = array();
		if ($count_only) {
			if ($only_queue) {
				$q = sprintf("select count(*) from mail_tracking where mail_id = %d and (is_sent = 0 or is_sent is null)", $mail_id);
			} else {
				$q = sprintf("select count(*) from mail_tracking where mail_id = %d", $mail_id);
			}
			$res = sql_query($q);
			$total = sql_result($res, 0);
			$return["count"] = $total;
			return $return;
		}
		if ($mail_id) {
			if ($only_queue)
				$q = sprintf("select * from mail_tracking where mail_id = %d and (is_sent = 0 or is_sent is null)", $mail_id);
			else
				$q = sprintf("select * from mail_tracking where mail_id = %d", $mail_id);

			if ($limit)
				$res = sql_query($q, "", 0, $limit);
			else
				$res = sql_query($q);

			while ($row = sql_fetch_assoc($res)) {
				if ($row["timestamp_first"]) {
					$row["read_first_h"] = date("d-m-Y H:i", $row["timestamp_first"]);
				} else {
					$row["read_first_h"] = gettext("not read");
				}
				if ($row["timestamp_last"]) {
					$row["read_last_h"]  = date("d-m-Y H:i", $row["timestamp_last"]);
				} else {
					$row["read_first_h"] = gettext("not read");
				}
				if (!$row["is_sent"]) {
					$row["not_sent"] = 1;
				}
				$row["agents_h"]     = str_replace("|", "<br>", $row["agents"]);
				$row["hyperlinks_h"] = str_replace("|", "<br>", $row["hyperlinks"]);


				$emails[] = $row;
			}
			$data["count"] = count($emails);
			$data["list"] = $emails;
			return $data;
		}
	}

	public function get_template_list($id="") {
		$data = array();
		if ($id) {
			$q = sprintf("select * from mail_templates where id = %d", $id);
		} else {
			$q = sprintf("select * from mail_templates order by description");
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row;
		}
		return $data;
	}

	public function get_template_filelist($template_id) {
		$files = array();

		$q = sprintf("select * from mail_templates_files where template_id = %d", $template_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["position"])
				$files[$row["position"]] = $row;
			else
				$files[$row["id"]] = $row;
		}
		return $files;
	}

	public function templateSave($id) {

		$mail = $_REQUEST["mail"];
		if ($id) {
			$q = sprintf("update mail_templates set description = '%s', header = '%s', footer = '%s', html_data = '%s', use_complex_mode = %d where id = %d",
				$mail["description"], $mail["header"], $mail["footer"], $mail["html_data"], $mail["use_complex"], $id);
			sql_query($q);
		} else {
			$q = sprintf("insert into mail_templates (description, header, footer, html_data, use_complex_mode) values ('%s', '%s', '%s', '%s', %d)", $mail["description"], $mail["header"], $mail["footer"], $mail["html_data"], $mail["use_complex_mode"]);
			sql_query($q);
			$id = sql_insert_id("mail_templates");
		}

		//check for new files
		$files =& $_FILES["image"];
		$filesys = new Filesys_data();

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "templates";

		if (is_array($files["tmp_name"])) {
			foreach ($files["tmp_name"] as $pos=>$tmp_name) {
				/* if file position is filled with a tmp_name */
				if ($files["error"][$pos] == UPLOAD_ERR_OK && $tmp_name) {

					/* gather some file info */
					$name = $files["name"][$pos];
					$type = $filesys->detectMimetype($tmp_name);
					$size = $files["size"][$pos];

					/* insert file into dbase */
					$q = "insert into mail_templates_files (template_id, name, size, type, position) values ";
					$q.= sprintf("(%d, '%s', '%s', '%s', '%s')", $id, $name, $size, $type, $pos);
					sql_query($q);
					$new_id = sql_insert_id("mail_templates_files");

					/* move data to the destination */
					$destination = sprintf("%s/%s/%s.dat", $fspath, $fsdir, $new_id);
					move_uploaded_file($tmp_name, $destination);
				}
			}
		}
		return $id;
	}

	public function templateDeleteFile($id) {
		/* important: never delete files here, maybe they are used in tracking items */
		$q = sprintf("update mail_templates_files set template_id = 0 where id = %d", $id);
		sql_query($q);

	}
	public function templateDelete($id) {
		$q = sprintf("select id from mail_templates_files where template_id = %d", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->templateDeleteFile($row["id"]);
		}

		$q = sprintf("delete from mail_templates where id = %d", $id);
		sql_query($q);
	}

	public function detectResume($mail_id) {
		$q = sprintf("select count(*) from mail_tracking where mail_id = %d and is_sent = 1", $mail_id);
		$res = sql_query($q);
		$current = sql_result($res,0);
		return $current;
	}
	public function detectResumeEmail($mail_id) {
		$mails = array();
		$q = sprintf("select email, is_sent from mail_tracking where mail_id = %d", $mail_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$mails[$row["email"]] = $row["is_sent"];
		}
		return $mails;
	}

	public function getTrackerLink($data) {
		$this->getTrackerImage($data);
	}

	public function getTrackerImage($data) {
		if ($data["email"] && $data["mailcode"]) {
			$q = sprintf("select * from mail_tracking where email = '%s' and mailcode = '%s'", addslashes($data["email"]), addslashes($data["mailcode"]));
			$res = sql_query($q);
			if (sql_num_rows($res)>0) {
				$row = sql_fetch_assoc($res);

				$codeid = $row["id"];
				if ($codeid) {
					if (!$row["timestamp_first"]) {
						$timestamp_first = time();
					} else {
						$timestamp_first = $row["timestamp_first"];
					}

					$timestamp_last = time();
					$count = $row["count"]+1;

					$clients = $row["clients"]."|".$_SERVER["REMOTE_ADDR"];
					$clients = addslashes(preg_replace("/^\|/s","",$clients));
					$clients = explode("|",$clients);
					$clients = array_unique($clients);
					$clients = implode("|",$clients);

					$agents = $row["agents"]."|".$_SERVER["HTTP_USER_AGENT"];
					$agents = addslashes(preg_replace("/^\|/s","",$agents));
					$agents = explode("|",$agents);
					$agents = array_unique($agents);
					$agents = implode("|",$agents);

					if ($data["link"]) {
						$link = preg_replace("/,{2}/si", "/", urldecode($data["link"]));
						$links = $row["hyperlinks"]."|".$link;
						$links = addslashes(preg_replace("/^\|/s","",$links));
						$links = explode("|",$links );
						$links = array_unique($links);
						$links = implode("|",$links );
					} else {
						$links = $row["hyperlinks"];
					}

					/* update tracker stats */
					$q = sprintf("update mail_tracking set timestamp_first = %d, timestamp_last = %d, count = %d, clients = '%s', agents = '%s', hyperlinks = '%s' where id = %d",
						$timestamp_first, $timestamp_last, $count, $clients, $agents, $links, $codeid);
					sql_query($q);
				}
			}
		}
		if ($data["attachment_id"]) {
			$this->showTemplateFile($data["attachment_id"]);
		} else {
			$link = preg_replace("/,{2}/si", "/", urldecode($data["link"]));
			if (!preg_match("/^mailto:/si", $link)) {
				$link = preg_replace("/(\r)|(\n)/s", "", $link);
				header("Location: ".$link);
			}
		}
		exit();
	}

	/* signatures */
	public function get_signature_list($id="", $user_id=0) {
		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		$esc = sql_syntax("escape_char");
		$q = sprintf("select count(*) from mail_signatures where ".$esc."default".$esc." = 1 and user_id = %d",
			$_SESSION["user_id"]);
		$res = sql_query($q);
		if (sql_result($res,0) == 0) {
			$user_data = new User_data();
			$user_info = $user_data->getUserDetailsById($user_id);

			$q = sprintf("insert into mail_signatures (user_id, ".$esc."default".$esc.",
				signature, signature_html) values (%d, 1, '%s', '%s')",
				$_SESSION["user_id"],
				addslashes($user_info["mail_signature"]),
				addslashes(nl2br($user_info["mail_signature"])));
			sql_query($q);
		}

		$data = array();
		if ($id) {
			$q = sprintf("select * from mail_signatures where user_id = %d and id = %d", $user_id, $id);
		} else {
			$q = sprintf("select * from mail_signatures where user_id = %d order by email", $user_id);
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["default"]) {
				$row["email"] = sprintf("&lt;%s&gt;", gettext("default email address"));
			}
			$data[] = $row;
		}
		return $data;
	}

	public function signatureSave($id) {

		$mail = $_REQUEST["mail"];

		$mail["signature_html"] = str_replace("\"/cmsfile", $GLOBALS["covide"]->webroot."cmsfile", $mail["signature_html"]);
		if (!$id) {
			$q = sprintf("insert into mail_signatures (user_id, email, signature, signature_html, subject, realname, companyname) values (%d, '%s', '%s', '%s', '%s', '%s', '%s')",
				$_SESSION["user_id"], $mail["email"], $mail["signature"], $mail["signature_html"], $mail["subject"], $mail["realname"], $mail["companyname"]);
			sql_query($q);

		} else {
			$q = sprintf("update mail_signatures set email = '%s', signature = '%s', signature_html = '%s', subject = '%s', realname = '%s', companyname = '%s' where id = %d",
				$mail["email"], $mail["signature"], $mail["signature_html"], $mail["subject"], $mail["realname"], $mail["companyname"], $id);
			sql_query($q);

			$q = sprintf("select * from mail_signatures where id = %d", $id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			if ($row["default"]) {
				$q = sprintf("update users set mail_signature = '%s',
					mail_signature_html = '%s' where id = %d",
					$mail["signature"], $mail["signature_html"], $row["user_id"]);
				sql_query($q);
			}
		}
	}

	public function signatureDelete($id) {
		$q = sprintf("delete from mail_signatures where id = %d", $id);
		sql_query($q);
	}



		/* filters */
	public function get_filter_list($id="", $user_id = 0) {
		if (!$user_id)
			$user_id = $_SESSION["user_id"];
		$data = array();

		if ($id) {
			$q = sprintf("select * from mail_filters where user_id = %d and id = %d", $user_id, $id);
		} else {
			$q = sprintf("select * from mail_filters where user_id = %d order by priority", $user_id);
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$folder = $this->getFolder($row["to_mapid"]);
			$row["folder_name"] = $folder["name"];
			$data[] = $row;
		}
		return $data;
	}

	public function filterSave($id) {
		$mail = $_REQUEST["mail"];
		if (!$id) {
			$q = sprintf("insert into mail_filters (user_id, sender, recipient, subject, to_mapid, priority) values (%d, '%s', '%s', '%s', %d, %d)",
				$_SESSION["user_id"], $mail["sender"], $mail["recipient"], $mail["subject"], $mail["to_mapid"], $mail["priority"]);
			sql_query($q);

		} else {
			$q = sprintf("update mail_filters set sender = '%s', recipient = '%s', subject = '%s', to_mapid = %d, priority= %d where id = %d",
				$mail["sender"], $mail["recipient"], $mail["subject"], $mail["to_mapid"], $mail["priority"], $id);
			sql_query($q);
		}
	}

	public function filterDelete($id) {
		$q = sprintf("delete from mail_filters where id = %d", $id);
		sql_query($q);
	}


	public function getPrefixCss($no_important_css=0) {
		$prefix = "";
		$prefix.= "  body, td, span, div, a, p {\n";
		$prefix.= sprintf("   font-family: arial, serif %s;\n", ($no_important_css) ? "":"!important");
		$prefix.= sprintf("   font-size: 12px %s;\n", ($no_important_css) ? "":"!important");
		$prefix.= sprintf("   color: black %s;\n", ($no_important_css) ? "":"!important");
		$prefix.= "  }\n";
		$prefix.= "  .head1 {\n";
		$prefix.= "   text-align: center;\n";
		$prefix.= "   background-color: #ddd;\n";
		$prefix.= "   font-weight: bold;\n";
		$prefix.= "  }\n";
		$prefix.= "  .head2 {\n";
		$prefix.= "   background-color: #eee;\n";
		$prefix.= "  }\n";
		$prefix.= "  .table1 {\n";
		$prefix.= "   border: 2px outset #999;\n";
		$prefix.= "  }\n";
		$prefix.= "  .cell1 {\n";
		$prefix.= "   font-weight: bold;\n";
		$prefix.= "   padding-left: 10px;\n";
		$prefix.= "   padding-right: 10px;\n";
		$prefix.= "   font-family: arial, serif;\n";
		$prefix.= "   font-size: 12px;\n";
		$prefix.= "  }\n";
		$prefix.= "  .cell2 {\n";
		$prefix.= "   padding-left: 10px;\n";
		$prefix.= "   padding-right: 10px;\n";
		$prefix.= "   font-family: arial, serif;\n";
		$prefix.= "   font-size: 12px;\n";
		$prefix.= "  }\n";
		return $prefix;
	}
	public function getPrefix($no_html_tags=0, $view_mode=0) {
		if (!$no_html_tags) {
			$prefix = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
			$prefix.= "<html>\n";
			$prefix.= "<head>\n";
			if ($no_html_tags != 2) {
				$prefix.= " <base target=\"_blank\">\n";
				$prefix.= " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
			}
		}
		if ($no_html_tags != 2) {
			$prefix.= " <style title='covide_email_css' type='text/css'>\n";
			$prefix.= "  <!-- \n";
			$prefix.= $this->getPrefixCss($view_mode);
			$prefix.= "  -->\n";
			$prefix.= " </style>\n";
		}
		if (!$no_html_tags) {
			$prefix.= "</head>\n";
			$prefix.= "<body>\n";
		}

		return $prefix;
	}

	public function stripBodyTags($body) {
		$strip_tags = array("!doctype","html","head","meta","body","base","link");

		//$body = preg_replace('/(<title>)(.*)(<title>)/imxsU', "", $body);
		$body = preg_replace("/<\/{0,1}title[^>]*?>/sxi", "", $body);

		foreach ($strip_tags as $k=>$v) {
			$repl = "/<\/{0,1}".$v."[^>]*?>/sxi";
			$body = preg_replace($repl, "", $body);
		}
		#$conversion = new Layout_conversion();
		#$body = $conversion->removeTags($body, $strip_tags);
		#$body = $this->getPrefix()."\n\n\n".$body;

		preg_match_all('/(<style\Wtitle=.covide_email_css.\W.*>)(.*)(<\/style>)/imxsU', $body, $matches);
		if (preg_match("/font-size\: 12px \!important\;/s", $matches[2][0]))
			$important = 1;

		foreach ($matches[0] as $v) {
			$body = str_replace($v, "", $body);
		}
		/* newline compression */
		/*
		$body = explode("\n", $body);
		foreach ($body as $k=>$v) {
			if (!trim($v))
				unset($body[$k]);
		}
		$body = implode("\n", $body);
		*/
		$body = preg_replace("/(\\n){3,}/sx", "\n<!-- crlf removed -->\n", $body);

		if ($important) {
			$body = str_replace("\t", "", "
				<style type='text/css'>
					<!--
						body, td, span, div, a, p {
							font-family: arial, serif !important;
							font-size: 12px !important;
							color: black !important;
						}
					-->
				</style>
			").$body;
		}
		return $body;
	}

	public function stylehtml($body, $view_mode=0, $mailId=0) {
		$body = $this->remove_script_tags($body);
		if (preg_match("/<body[^>]*?>/sxi", $body)) {
			preg_match_all("/<body[^>]*?>/sxi", $body, $matches);
			$newbody = $matches[0][0];
		}
		$body = $this->getPrefix(0, $view_mode).$this->stripBodyTags($body)."</body></html>";
		if ($newbody) {
			$body = preg_replace("/<body[^>]*?>/sxi", $newbody, $body);
		}
		return trim($body);
	}

	public function addAttachmentCovide($req="") {
		if (is_array($req))
			$REQUEST = $req;
		else
			$REQUEST = $_REQUEST;

		require(self::include_dir."dataAddAttachmentsCovide.php");
		return $new_ids;
	}

	public function getAttachmentsInfo($ids) {
		$projects  = array();
		$relations = array();

		$ids = explode(",", $ids);
		$ids[]="0";
		foreach ($ids as $k=>$v) {
			if (!$v) {
				unset($ids[$k]);
			}
		}
		$ids = implode(",", $ids);

		$q = sprintf("select mail_messages.project_id, mail_messages.address_id from mail_attachments LEFT JOIN mail_messages ON mail_messages.id = mail_attachments.message_id where mail_attachments.id IN (%s)", $ids);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$projects[]  = (int)$row["project_id"];
			$relations[] = (int)$row["address_id"];
		}
		$projects  = array_unique($projects);
		$relations = array_unique($relations);

		return array(
			"projects"  => $projects,
			"relations" => $relations
		);
	}

	public function checkFilePermissions($id, $mail_id=0, $address_id=0, $private=0) {
		$xs = 0;

		$fsdata = new Filesys_data();

		/* get email folders and archive id if not in cache already */
		if (!$this->_cache["folders"]) {
			$this->_cache["folders"] = $this->getFolders();
			foreach ($this->_cache["folders"] as $k=>$v) {
				$this->_cache["folderlist"][]=$k;
			}
			$this->_cache["archive_id"] = $this->getSpecialFolder("Archief", 0);
		}

		/* get mail id */
		if (!$mail_id) {
			if ($address_id)
				$q = sprintf("select mail_messages.*, mail_attachments.message_id, mail_attachments.name as attachmentname, mail_attachments.size as attachmentsize from mail_attachments left join mail_messages on mail_messages.id = mail_attachments.message_id where mail_attachments.id = %d AND mail_messages.address_id IN (%s)", $id, $address_id);
			else
				$q = sprintf("select mail_messages.*, mail_attachments.message_id, mail_attachments.name as attachmentname, mail_attachments.size as attachmentsize from mail_attachments left join mail_messages on mail_messages.id = mail_attachments.message_id where mail_attachments.id = %d", $id);
		} else {
			if ($address_id)
				$q = sprintf("select * from mail_messages where id = %d AND address_id IN (%s)", $mail_id, $address_id);
			else
				$q = sprintf("select * from mail_messages where id = %d", $mail_id);
		}

		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_assoc($res);

			/* if mail is private */
			if ($row["public"] == 2 && $row["user_id"] == $_SESSION["user_id"]) {
				$xs = 1;
			} elseif (in_array($row["folder_id"], $this->_cache["folderlist"])) {
				$xs = 1;
			} elseif ($row["folder_id"] == $this->_cache["archive_id"]["id"]) {
				/* check relation */
				if (!$this->_cache["obj_user_data"]) {
					$this->_cache["obj_user_data"] = new User_data();
					$this->_cache["address_user_permissions"] = $this->_cache["obj_user_data"]->getUserPermissionsById($_SESSION["user_id"]);
				}
				$user_data =& $this->_cache["obj_user_data"];

				/* get the address */
				if (!$this->_cache["obj_address_data"]) {
					$this->_cache["obj_address_data"] = new Address_data();
				}
				$addressinfo[0] = $this->_cache["obj_address_data"]->getAddressById($row["address_id"]);
				$user_data->getUserPermissionsById($_SESSION["user_id"]);

				if ($user_data->checkPermission("xs_relationmanage")) {
					$xs = 1;
				} else {
					if ($addressinfo[0]["account_manager"] == $_SESSION["user_id"]) {
						$xs = 1;
					} elseif ($addressinfo[0]["account_manager"] > 0) {
						/* if this user is not a relation manager, this user has limited access */
						$accmanager_arr = explode(",", $user_data->permissions["addressaccountmanage"]);

						if (in_array($addressinfo[0]["account_manager"], $accmanager_arr)) {
							$xs = 1;
						}
					}
				}
			}
			if (!$private) {
				/* filter my own folders from this search */
				if ($mail_id && $row["folder_id"] != $this->_cache["archive_id"]["id"])
					$xs = 0;
			} else {
				/* filter archive searches */
				if ($mail_id && $row["folder_id"] == $this->_cache["archive_id"]["id"])
					$xs = 0;
			}
			if ($xs == 1) {
				if (is_numeric($row["sender_emailaddress"]))
					$row["sender_emailaddress"] = "--";

				if ($row["folder_id"] == $this->_cache["archive_id"]["id"]) {
					$folder["name"] = gettext("Archive");
					$folder["id"]   = $row["folder_id"];
				} else {
					$folder = $this->_cache["folders"][$row["folder_id"]];
				}

				$ftype = $fsdata->getFileType($row["attachmentname"]);
				$timestamp = ($row["date"]) ? $row["date"] : $row["date_received"];
				$data = array(
					"timestamp"  => $timestamp,
					"date"       => date("d-m-Y", $timestamp),
					"short_date" => date("d-m-Y", $timestamp),
					"short_time" => date("H:i", $timestamp),
					"sender_emailaddress_h" => $this->cleanAddress($row["sender_emailaddress"]),
					"subject"  => $row["subject"],
					"name"     => $row["attachmentname"],
					"size"     => $row["attachmentsize"],
					"attid"    => $id,
					"id"       => ($id) ? $id:$mail_id,
					"icon"     => $ftype,
					"folder"   => $folder["name"],
					"folder_name" => $folder["name"]." ".$addressinfo[0]["companyname"],
					"address_id"  => $row["address_id"],
					"folderid"    => $folder["id"],
					"folder_id"=> $folder["id"],
					"relation" => $row["relation_id"],
					"mailid"   => $row["id"],
				);
				$xs = $data;
			}
		}
		return $xs;
	}

	private function header_quoted_printable_encode($string, $encoding="UTF-8") {
    $string = str_replace(" ", "_", trim($string)) ;

    // We need to delete "=\r\n" produced by imap_8bit() and replace '?'
    $string = str_replace("?", "=3F", str_replace("=\r\n", "", imap_8bit($string))) ;
    // Now we split by \r\n - i'm not sure about how many chars (header name counts or not?)
    $string = chunk_split($string, 65);
    // We also have to remove last unneeded \r\n :
    $string = substr($string, 0, strlen($string)-2);
    // replace newlines with encoding text "=?UTF ..."
    $string = str_replace("\r\n", "?=\n\t=?".$encoding."?Q?", $string) ;

    return sprintf("=?%s?Q?%s?=", $encoding, $string);
	}
	public function mime_encode($str) {
		$str = str_replace(array("\r", "\t", "\n"), array("","",""), $str);

		if (imap_utf7_encode($str) != $str)
			return $this->header_quoted_printable_encode($str);
		else
			return $str;
	}

	public function quoted_printable_encode($str) {
		$str = imap_8bit($str);
		$str = explode("\n", $str);
		$i=0;
		foreach ($str as $k=>$v) {
			if (trim($v)==".") {
				/* protect 'end of data' sequence */
				$str[$k] = str_replace(".", "=2E", $v);
			} elseif (preg_match("/^\./s", $v)) {
				/* protect dots at the beginning of a line */
				$str[$k] = preg_replace("/^\./s", "=2E", $v);
			}
		}
		$str = implode("\n", $str);
		return $str;
	}


	/* }}} */

	/* savePermissions {{{ */
	/**
	 * Save specified mailfolder permisions to the db
	 *
	 * @param array user_id, folder_id, and users as | seperated string
	 * @return bool true on access and false on failure
	 */
	public function savePermissions($id, $folder_id, $users, $user_id, $name="") {
		/* check if folder id does exist */
		$q = sprintf("select id from mail_permissions where folder_id = %d and user_id = %d", $folder_id, $user_id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$id = sql_result($res,0);
		}
		if (!$id) {
			$q = sprintf("insert into mail_permissions (folder_id, name, user_id, users) values (%d, '%s', %d, '%s')",
				$folder_id, $name, $user_id, $users);
		} else {
			$q = sprintf("update mail_permissions set folder_id = %d, users = '%s', name = '%s' where id = %d", $folder_id, $users, $name, $id);
		}
		sql_query($q);
		echo "<script>location.href='index.php?mod=email&action=show_permissions&user_id=".$user_id."';</script>";
	}
	/* }}} */
	public function deletePermissions($id, $user_id) {
		$q = sprintf("delete from mail_permissions where user_id = %d and id = %d", $user_id, $id);
		sql_query($q);
		echo "<script>location.href='index.php?mod=email&action=show_permissions&user_id=".$user_id."';</script>";
	}

	public function dropMailBodyToFilesys($id) {

		$path = $GLOBALS["covide"]->filesyspath."/maildata/";
		$file = $path.$id.".txt";

		$filesys_data = new Filesys_data();
		$file_new = $filesys_data->FS_calculatePath($file);

		if (
			!file_exists($file) &&
			!file_exists($file.".gz") &&
			!file_exists($file_new) &&
			!file_exists($file_new.".gz")) {

			$q = sprintf("select count(mail_id) from mail_messages_data where mail_id = %d",
				$id);
			$res = sql_query($q);
			if (sql_result($res,0) == 0)
				$tbl = "mail_messages_data_archive";
			else
				$tbl = "mail_messages_data";

			$syntax = sql_syntax("escape_char");
			$q = sprintf("select sender_emailaddress, cc, %1\$sto%1\$s, subject,
				%3\$s.body,
				%3\$s.mail_decoding
				from mail_messages
				left join %3\$s
				on %3\$s.mail_id = mail_messages.id
				where mail_messages.id = %2\$d", $syntax, $id, $tbl);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			if ($row["mail_decoding"]) {
				@$row["body"] = mb_convert_encoding($row["body"], "UTF-8", $row["mail_decoding"]);
				$row["to"]    = $this->decodeMimeString($row["to"]);
				$row["cc"]    = $this->decodeMimeString($row["cc"]);
				$row["bcc"]   = $this->decodeMimeString($row["bcc"]);
				$row["from"]  = $this->decodeMimeString($row["from"]);
				$row["subject"] = $this->decodeMimeString($row["subject"]);
			}

			/* first some normal fields */
			$fields = array($row["sender_emailaddress"], $row["cc"], $row["to"], $row["subject"]);
			foreach ($fields as $b=>$v) {
				$fields[$b] = preg_replace("/[^a-z0-9@\. ]/si", "", $v);
			}
			$fields = implode("\n", $fields);

			if (is_array($row)) {
				$row = implode(" ", $row);
			}
			$body = strip_tags($row);

			$body = preg_replace("/[^a-z0-9]/si", " ", $body);

			$body = str_replace("\n", " ", $fields)." ".$body;
			$body = preg_replace("/ {1,}/s", " ", $body);
			$body = explode(" ", $body);
			foreach ($body as $b=>$v) {
				if (strlen($v)<3) {
					unset($body[$b]);
				}
			}
			$body = implode(" ", $body);


			$out = fopen($file, "w");
			fwrite($out, $body);
			fclose($out);

			$fsdata = new Filesys_data();
			$fsdata->FS_compressFile($file);
		}
	}
	/* cleanUpMaildata */
	/**
	 * Remove leftover maildata that can be deleted
	 */
	public function cleanUpMaildata() {
		//return true;
		#TODO: debug this
		/* cleanup maildata items */
		/*
		$q = "select mail_id from mail_messages_data where mail_id NOT IN (select id from mail_messages)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			// check if mail does really not exists
			$q = sprintf("select count(*) from mail_messages where id = %d", $row["mail_id"]);
			$res2 = sql_query($q);
			if (sql_result($res2, 0) == 0) {
				//delete from mail_messages_data
				$q = sprintf("delete from mail_messages_data where mail_id = %d", $row["mail_id"]);
				sql_query($q);

				//delete from filesys
				$path = $GLOBALS["covide"]->filesyspath."/maildata/";
				$file = $path.$row["mail_id"].".txt";
				@unlink($file);
			}
		}
		*/
		$fsdata = new Filesys_data();

		$ary = array("mail_messages_data", "mail_messages_data_archive");
		foreach ($ary as $a) {
			$q = sprintf("select mail_id from %s where mail_id NOT IN (
				select id from mail_messages)", $a);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				/* delete from filesys */
				$path = $GLOBALS["covide"]->filesyspath."/maildata/";
				$file = $path.$row["mail_id"].".txt";
				//@unlink($file);
				$fsdata->FS_removeFile($file);

				/* cleanup from data table */
				$q = sprintf("delete from %s where mail_id = %d",
					$a, $row["mail_id"]);
				sql_query($q);

				$stats++;
			}
		}
		if ($stats) {
			echo "Removed orphaned items from mail datastore [$stats items]\n";
		}
	}
	/* }}} */

	public function get_permissions_list($user_id="", $id="") {
		$user_data = new User_data();
		$user_output = new User_output();

		$data = array();
		if (!$user_id) {
			$user_id = $_SESSION["user_id"];
		}
		$q = sprintf("select * from mail_permissions where user_id = %d", $user_id);
		if ($id) {
			$q.= sprintf(" and id = %d", $id);
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$folderinfo = $this->getFolder($row["folder_id"]);
			$row["h_folder"] = $folderinfo["name"];
			$row["h_users"]  = $user_output->user_selection_output($row["users"]);

			$data[$row["id"]]=$row;
		}
		return $data;
	}
	public function getSharedFolderAccess($user_id) {

		/* check for cms form folder access */
		$this->checkSharedFolders();

		//get groups
		$user_data = new User_data();
		$groups = $user_data->getUserGroups($user_id);

		$data = array();
		$regex_syntax = sql_syntax("regex");
		$str = "((".$user_id.")";
		foreach ($groups as $g) {
			$str.= "|(G".$g.")";
		}
		$str.= ")";
		$regex = $regex_syntax." '(^|\\\\,)". $str ."(\\\\,|$)' ";
		$q = sprintf("select mail_permissions.*, mail_folders.id as mailfolderid from mail_permissions
			left join mail_folders on mail_folders.id = mail_permissions.folder_id
			where mail_permissions.users %s and mail_permissions.user_id != %d ORDER BY name", $regex, $user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["mailfolderid"]) {
				$username = $user_data->getUserNameById($row["user_id"]);
				$row["username"] = $username;
				$data[]=$row;
			} else {
				$q = sprintf("delete from mail_permissions where id = %d", $row["id"]);
				sql_query($q);
			}
		}
		return $data;
	}

	private function handleWindows1251(&$str, &$encoding) {
		/* if specified encoding is ISO-8859-xx */
		if (preg_match("/^iso\-8859\-\d{1,2}/si", $encoding)) {
			/* check for range 'C1 control code'. Html should not use this. */
			/* is this range is used, a wrong encoding is specified in the mail */
			/* mostly the encoding is not ISO-8859-xx as specified but Windows-1251 */
			if (preg_match("/[\x80-\x9F]/s", $str)) {
				/* if the reserved range is used, we try to upgrade all characters */
				/* in range xA0-xFF to Windows-1251 and set the encoding itself to 1251 */
				preg_match_all("/[\xA0-\xFF]/s", $str, $conversion_range);
				if (is_array($conversion_range[0])) {
					$conversion_range = $conversion_range[0];
					$conversion_range = array_unique($conversion_range);

					$conv_inp = array();
					$conv_out = array();
					foreach ($conversion_range as $c) {
						/* replace character position ISO-8859-xx by Windows-1252 position */
						$str = str_replace($c, @mb_convert_encoding($c, "Windows-1252", $encoding), $str);
					}
					$encoding = "Windows-1252";
				}
			}
		}
	}

	public function cleanOrphanedItems() {
		/* email data */
		$fspath = $GLOBALS["covide"]->filesyspath."/email/";
		echo "\nfilesystem: email_attachments\n";
		$dir = scandir($fspath);
		foreach ($dir as $k=>$v) {
			$f = (int)$v;
			if ($f > 0) {
				$q = sprintf("select count(*) from mail_attachments where id = %d", $f);
				$res = sql_query($q);
				if (sql_result($res,0) == 0) {
					echo sprintf("[%d] could not find attachment id: %d\n", $f, $f);
				}
			}
		}
		echo "\ntable: mail_attachments: message_id\n";
		$q = "select * from mail_attachments where message_id NOT IN (
			select id from mail_messages)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("[%d] could not find message_id: %d\n", $row["id"], $row["message_id"]);
		}
		echo "\ntable: mail_messages_data: mail_id\n";
		$q = "select * from mail_messages_data where mail_id NOT IN (
			select id from mail_messages)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("[%d] could not find mail_id: %d\n", $row["id"], $row["mail_id"]);
		}
		echo "\ntable: mail_tracking: mail_id(2)\n";
		$q = "select * from mail_tracking where ((
				mail_id > 0 AND mail_id NOT IN (select id from mail_messages)
			) AND (
				mail_id_2 > 0 AND mail_id_2 NOT IN (select id from mail_messages)
			)) OR (mail_id = 0 AND mail_id_2 = 0)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("[%d] could not find mail_id %d or %d: %d\n", $row["id"], $row["mail_id"], $row["mail_id_2"]);
		}
		echo "\ntable: mail_filters: folder_id\n";
		$q = "select * from mail_filters where to_mapid NOT IN (
			select id from mail_folders)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("[%d] could not find folder_id %d: %d\n", $row["id"], $row["to_mapid"]);
		}
	}

	/* Deletes the entire content of the trashbin including subfolders */
	public function deleteAllTrash() {
		$mailData = new Email_data();
		$id = $mailData->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
		$options = array("parent"=>$id);
		$mails = $this->getFolders($options);
		$mailsCurrent = $this->getFolder($id);
		$mails[$id["id"]] = $mailsCurrent["high_parent"];
		if (is_array($mails)) {
			foreach ($mails as $id=>$v) {
				$this->folderDelete($id);
			}
		}
		// We're in a XMLload so output javascript to return to the mail section
		echo "document.location.href = '?mod=email'";

	}

	public function checkCmsMailFolder() {
			$user_data = new User_data();
			$user_info = $user_data->getUserList($active=1, $search="archiefgebruiker", 1);
			$user_key  = array_search("archiefgebruiker", $user_info);
			$sent_items = $this->getSpecialFolder("Sent-Items", $user_key);

			/* delete old concepts and sent items older > 1 hour from archiveuser */
			$concepts = $this->getSpecialFolder("Concepten", $user_key);
			$date = time()-3600;
			$q = sprintf("select id from mail_messages where date < %d and folder_id IN (%d, %d)",
				$date, $concepts["id"], $sent_items["id"]);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$this->mail_delete($row["id"]);
			}

			return $sent_items;
	}

	public function checkSharedFolders($pageid=0) {
		$data = $this->checkCmsMailFolder();
		$cms  = new Cms_data();

		/* fetch all shared folders */
		$folders = array();
		$q = sprintf("select id,name from mail_folders where parent_id = %d", $data["id"]);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$folders[$row["id"]] = $row["name"];
		}
		/* prefetch all admins / managers get access */
		$admins = array();
		$q = "select id from users where xs_cms_level >= 2";
		$res2 = sql_query($q);
		while ($row2 = sql_fetch_assoc($res2)) {
			$admins[] = $row2["id"];
		}

		/* get all form pages */
		if ($pageid)
			$q = sprintf("select id, pageAlias from cms_data where isForm = 1 and id = %d", $pageid);
		else
			$q = sprintf("select id, pageAlias from cms_data where isForm = 1");

		$res = sql_query($q);

		while ($row = sql_fetch_assoc($res)) {
			/* check current share permissions */
			$xs = 1;
			if ($xs) {
				/* calculate new share permissions */
				$share = $admins;
				$pp = $cms->getPermissions($row["id"]);
				foreach ($pp as $user=>$p) {
					if (is_numeric($user) && $p["editRight"])
						$share[] = (int)$user;
				}
				$share = implode(",", $share);

				/* now check against db */
				if ($row["pageAlias"])
					$alias = sprintf("/page/%s.htm", $row["pageAlias"]);
				else
					$alias = sprintf("/page/%s.htm", $row["id"]);

				$page = array_search($row["id"], $folders);
				$name = sprintf("page %d (%s)", $row["id"], $alias);
				if (!$page) {
					/* insert new folder */
					$page = $this->createFolder($data["id"], $row["id"]);
				}
				$q = sprintf("select * from mail_permissions where folder_id = %d",
					$page);
				$res2 = sql_query($q);
				if (sql_num_rows($res2) == 0) {
					$q = sprintf("insert into mail_permissions (folder_id, users, user_id, name)
						values (%d, '%s', %d, '%s')",
						$page, $share, $data["user_id"], addslashes($name));
					sql_query($q);
				}  else {
					$row2 = sql_fetch_assoc($res2);
					$diff = 0;
					if ($row2["users"] != $share || $row2["name"] != $name) {
						/* update */
						$q = sprintf("update mail_permissions set users = '%s', name = '%s' where id = %d",
							$share, addslashes($name), $row2["id"]);
						sql_query($q);
					}
				}
			}
		}
		return $page;
	}

	public function messageToFolder($mail_id, $folder_id) {
		$q = sprintf("update mail_messages set is_new = 1, folder_id = %d where id = %d",
			$folder_id, $mail_id);
		sql_query($q);
	}

	public function getTemplateFields($id) {
		$q = sprintf("SELECT * FROM mail_templates WHERE id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}

	public function addAttachmentFromString($data) {
		$filesys = new Filesys_data();

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "email";

		/* gather some file info */
		$name = $data["name"];
		$type = $data["type"];
		$size = strlen($data["bin"]);
		$id   = $data["id"];

		/* insert file into dbase */
		$q = "insert into mail_attachments (message_id, name, size, type) values ";
		$q.= sprintf("(%d, '%s', '%s', '%s')", $id, $name, $size, $type);
		sql_query($q);
		$new_id = sql_insert_id("mail_attachments");

		$ext = $filesys->get_extension($name);

		/* move data to the destination */
		$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir, $new_id, $ext);
		file_put_contents($destination, $data["bin"]);

		/* compress file */
		$filesys->FS_compressFile($destination);
	}

	/* pa_getAutoreplyDB {{{ */
	/**
	 * Get the DB object for the PostfixAdmin postfix virtual vacation database
	 *
	 * @return MDB2 database object
	 */
	public function pa_getAutoreplyDB() {
		if ($GLOBALS["covide"]->license["has_postfixadmin"] && $GLOBALS["covide"]->license["postfixdsn"]) {
			$options = array(
				"persistent"  => TRUE,
				'portability' => MDB2_PORTABILITY_NONE
			);
			$dsn = $GLOBALS["covide"]->license["postfixdsn"];
			return MDB2::connect($dsn, $options);
		} else {
			die("No postfixadmin license or no valid postfix database DSN");
		}
	}
	/* }}} */
	/* pa_getAutoreplyByUserID {{{ */
	/**
	 * Get the autoreply (vacation) status from postfixadmin database for a user
	 *
	 * @param int $user_id The userid to grab the status for
	 * @return array Current autoreply record
	 */
	public function pa_getAutoreplyByUserID($user_id) {
		$db_postfix = $this->pa_getAutoreplyDB();

		$user_data = new User_data();
		$userinfo = $user_data->getUserdetailsById($user_id);
		if (!$userinfo["mail_user_id"])
			return false;

		// split mail_user_id in username and domain part
		$email = $userinfo["mail_user_id"];
		$tmp = preg_split("/@/", $email);
		$domain = $tmp[1];

		$sql = sprintf("SELECT * FROM vacation WHERE email = '%s' AND domain = '%s'", $email, $domain);
		$res = sql_query($sql, $db_postfix);
		if (sql_num_rows($res)) {
			$row = sql_fetch_assoc($res);
			$return = array(
				"autoreply" => true,
				"email"     => $email,
				"domain"    => $domain,
				"created"   => $row["created"],
				"subject"   => $row["subject"],
				"body"      => $row["body"]
			);
		} else {
			$return = array(
				"autoreply" => false,
				"email"     => $email,
				"domain"    => $domain,
			);
		}
		return $return;
	}
	/* }}} */
	/* pa_autoreplySave {{{ */
	/**
	 * Save a users postfixadmin autoreply to the database
	 *
	 * @param array $autoreply Form fields
	 * @return true on success, false on failure
	 */
	public function pa_autoreplySave($autoreply, $return = 1) {
		$db_postfix = $this->pa_getAutoreplyDB();
		//first look if we already have an autoreply for this email address
		$sql = sprintf("SELECT COUNT(*) FROM vacation WHERE email = '%s' AND domain = '%s'", $autoreply["email"], $autoreply["domain"]);
		$res = sql_query($sql, $db_postfix);
		$count = sql_result($res, 0);
		if ($count > 0) {
			//if not active remove this from the database, else update
			if (!$autoreply["active"])
				$sql = sprintf("DELETE FROM vacation WHERE email='%s' AND domain='%s'", $autoreply["email"], $autoreply["domain"]);
			else
				$sql = sprintf("UPDATE vacation SET subject='%s', body='%s' WHERE email = '%s' AND domain='%s'", $autoreply["subject"], $autoreply["body"], $autoreply["email"], $autoreply["domain"]);
		} else {
			//insert if active
			if ($autoreply["active"])
				$sql = sprintf("INSERT INTO vacation (subject, body, email, domain) VALUES ('%s', '%s', '%s', '%s')", $autoreply["subject"], $autoreply["body"], $autoreply["email"], $autoreply["domain"]);
		}
		sql_query($sql, $db_postfix);
		if ($return) {
			return true;
		} else {
			$output = new Layout_output();
			$output->layout_page();
			$output->start_javascript();
			$output->end_javascript();
			$output->exit_buffer();
		}
	}
	/* }}} */
	/* copy2concepts {{{ */
	/**
	 * Copy an email into concepts
	 *
	 * @param int $id The mail id to copy
	 * @return the new email id after copy
	 */
	public function copy2concepts($id) {
		$folder = $this->getSpecialFolder("Concepten", $_SESSION["user_id"]);
		$new_id = $this->userCopy($id, $_SESSION["user_id"], $folder["id"]);

		return $new_id;
	}
	/* }}} */
	/* getEmailByPrivateId {{{ */
	/**
	 * Get all email where a specific private contact is set
	 *
	 * @param int $id The mail id to copy
	 * @return array ID's of emails
	 */
	public function getEmailByPrivateId($id) {
		$q = sprintf("SELECT id FROM mail_messages WHERE private_id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}
	/* }}} */
	/* change_private_xml {{{ */
	/**
	 * AJAX method to change the private contact linked to an email
	 */
	public function change_private_xml() {
		$id       = $_REQUEST["id"];
		$private  = $_REQUEST["private"];
		if ($private < 0) {
			$private = 0;
		}

		$q = sprintf("update mail_messages set private_id = %d where id = %d", $private, $id);
		sql_query($q);

		if ($private) {
			$address = new Address_data();
			$address_info = $address->getRecord(array("id"=>$private, "type"=>"user"));
			$name = $address_info["tav"];
		} else {
			$name = gettext("none");
		}
		echo("document.getElementById('layer_mail_private').innerHTML = '$name';");
		echo("document.getElementById('mailprivate_id').value = '$private';");
	}
	/* }}} */
	/* cleanTrackingData {{{ */
	/**
	 * SQL query to delete tracking data which email_ID does not exist anymore
	 */
	public function cleanTrackingData() {
		$sql = "SELECT mail_id FROM mail_tracking t INNER JOIN mail_messages m WHERE t.mail_id = m.id";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["mail_id"]] = $row["mail_id"];
		}
		if (is_array($data))
			$ids = implode(",", $data);
		if ($ids) {
			$sql = sprintf("DELETE FROM mail_tracking WHERE mail_id NOT IN (%s)", $ids);
			$result = sql_query($sql);
		}
	}
	/* }}} */
	/* save_bcard_xml {{{ */
	/**
	 * Link a mail to a bcard. Function is called by an AJAX call.
	 *
	 * @param int $mail_id The mail in question
	 * @param int $bcard_id The bcard to link the mail to
	 * @return bool false on error true on success
	 */
	public function save_bcard_xml($mail_id, $bcard_id) {
		$mail_id = sprintf("%d", $mail_id);
		$bcard_id = sprintf("%d", $bcard_id);
		if ($mail_id && $bcard_id) {
			$sql = sprintf("UPDATE mail_messages SET bcard_id = %d WHERE id = %d", $bcard_id, $mail_id);
			$res = sql_query($sql);
			return true;
		} else {
			return false;
		}
	}
	/* }}} */
	/* getAutoreplyByUserId {{{ */
	/**
	 * Get autoreply info for a user_id
	 *
	 * @param int $user_id The user to fetch the autoreply stuff for
	 *
	 * @return array The autoreply settings or an empty array if none in database
	 */
	public function getAutoreplyByUserId($user_id) {
		$autoreply = array();
		$sql = sprintf("SELECT * FROM mail_autoreply WHERE user_id = %d", $user_id);
		$res = sql_query($sql);
		if (sql_num_rows($res) == 1) {
			$autoreply = sql_fetch_array($res);
		}
		return $autoreply;
	}
	/* }}} */
	/* saveAutoreply {{{ */
	/**
	 * Store autoreply data in datbase
	 *
	 * @param array $autoreply The autoreply data
	 */
	public function saveAutoreply($autoreply) {
		if (!$autoreply["id"]) {
			$sql = sprintf("INSERT INTO mail_autoreply (user_id, subject, body, is_active, timestamp_start, timestamp_end) VALUES (%d, '%s', '%s', %d, %d, %d)",
				$autoreply["user_id"], $autoreply["subject"], $autoreply["body"], $autoreply["active"],
				mktime(0, 0, 0, $autoreply["start_month"], $autoreply["start_day"], $autoreply["start_year"]),
				mktime(0, 0, 0, $autoreply["end_month"], $autoreply["end_day"], $autoreply["end_year"]));
		} else {
			$sql = sprintf("UPDATE mail_autoreply SET subject = '%s', body = '%s', is_active = %d, timestamp_start = %d, timestamp_end = %d WHERE id = %d",
				$autoreply["subject"], $autoreply["body"], $autoreply["active"],
				mktime(0, 0, 0, $autoreply["start_month"], $autoreply["start_day"], $autoreply["start_year"]),
				mktime(0, 0, 0, $autoreply["end_month"], $autoreply["end_day"], $autoreply["end_year"]), $autoreply["id"]);
		}
		$res = sql_query($sql);
		if ($autoreply["return"]) {
			return true;
		} else {
			$output = new Layout_output();
			$output->layout_page("saved", 1);
			$output->start_javascript();
			$output->addCode("closepopup();");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
	}
	/* }}} */
	/* linkRelSelection {{{ */
	/**
	 * Try to link emailselection to relation
	 */
	public function linkRelSelection() {
		$mails = $_REQUEST["checkbox_mail"];
		if (count($mails)>0) {
			/* grab addresses */
			$address_data = new Address_data();
			$this->address = $address_data->getRelationsEmailArray();
			foreach ($mails as $k=>$v) {
				// quick-n-dirty way to grab email address
				$q = sprintf("SELECT sender_emailaddress FROM mail_messages WHERE id = %d", $k);
				$res = sql_query($q);
				$data = sql_fetch_assoc($res);

				$email_clean = trim(strtolower($this->cleanAddress($data["sender_emailaddress"])));
				$sql = "";
				if ($this->address[$email_clean] > 0) {
					$sql = sprintf("UPDATE mail_messages SET address_id = %d WHERE id = %d", $this->address[$email_clean], $k);
				} elseif ($this->address[$email_clean] == -1) {
					$data["askwichrel"] = 1;
					$sql = sprintf("UPDATE mail_messages SET askwichrel = 1 WHERE id = %d", $k);
				}
				if ($sql) {
					sql_query($sql);
				}
			}
		}
	}
	/* }}} */
	/* sendReadNotification {{{ */
	/**
	 * Send a 'mail is read' notification for a specific mail.
	 * It will also update the database so that it will not be asked again when sent.
	 *
	 * @param int $mail_id The mail to send the notification for
	 */
	public function sendReadNotification($mail_id) {
		$mail_data = $this->getEmailById($mail_id);
		$hx = explode("\n",str_replace("\r","",$mail_data[0]["header"]));
		foreach ($hx as $v) {
			if (preg_match("/^Disposition-Notification-To:(.*)/si",$v, $matches)) {
				if ($matches[1]) {
					//$mailto = str_replace("<", "", str_replace(">", "", $matches[1]));
					$mailto = $this->cleanAddress($matches[1]);
					if ($mailto) {
						//send mail
						$subject = sprintf("%s: \"%s\"", gettext("Delivery notification for"), $mail_data[0]["subject"]);
						$body = sprintf("%s %s %s \"%s\" %s %s %s",
							gettext("Your message to"),
							$mail_data[0]["to"],
							gettext("about"),
							$mail_data[0]["subject"],
							gettext("on"),
							$mail_data[0]["h_date"],
							gettext("has been read.")
						);
						mail($mailto, $subject, $body, "From: ".$mail_data[0]["to"], "-f".$mail_data[0]["to"]);
					}
				}
			}
		}
	}
	/* }}} */
}
?>
