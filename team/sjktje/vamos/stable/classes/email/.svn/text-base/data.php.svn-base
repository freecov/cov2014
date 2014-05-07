<?php
/**
 * Covide Groupware-CRM Email module
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

	/* methods */
	public function __construct() {
		$this->data = array();
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	/* createFolder {{{ */
	/**
	 * Create a mailfolder in users Inbox
	 */
	public function createFolder() {
		$parent_id   = $_REQUEST["folder_id"];
		$folder_name = $_REQUEST["action_value"];

		$q = sprintf("insert into mail_folders (name, user_id, parent_id) values ('%s', %d, %d)",
			$folder_name, $_SESSION["user_id"], $parent_id);
		sql_query($q);
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
			$zipfile->add_file(&$attachment["data_binary"], sprintf("covide/%s", $attachment["name"]));
			unset($attachment);
		}

		$data = $zipfile->file();
		unset($zipfile);

		$fname = "covide.zip";

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/zip');

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$fname.'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$fname.'"');
		}

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

		if ($folder["high_parent"] == $del_items["id"]) {
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

		} else {
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
		$folder_id = $_REQUEST["folder_id"];
		$target_id = $_REQUEST["target_id"];

		$q = sprintf("update mail_folders set parent_id = %d where id = %d and user_id = %d",
			$target_id, $folder_id, $_SESSION["user_id"]);
		sql_query($q);
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
				$ids[]=$k;
			}
			//first get state of the selection
			$q = sprintf("select sum(is_new) as cnt from mail_messages where id IN (%s)", implode(",",$ids));
			$res = sql_query($q);
			$count = sql_result($res,0);
			if ($count > 0) {
				//set all to state NOT new
				$state = 0;
			} else {
				//set all to state new
				$state = 1;
			}
			$q = sprintf("update mail_messages set is_new = %d where id IN (%s)", $state, implode(",",$ids));
			sql_query($q);
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
			}
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
	 * @return mixed the result of the smtp dialog
	 */
	public function sendMailComplex($id) {
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
		return $new_id;
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
	 * @param string $to Receipient of the mail
	 * @param string $subject Subject of the mail
	 * @param string $message Body of the mail
	 * @param string $additional_options See php mail() manual
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
		mail($to, $subject, $message, $additional_options, "-f".$from);
	}
	/* }}} */
	/* save_concept {{{ */
	/**
	 * Save a Draft to the database
	 *
	 * @param int $id The Draft id to save, or 0 to create a new Draft
	 * @return int The draftid that was saved
	 */
	public function save_concept($id=0) {
		require(self::include_dir."dataSaveConcept.php");
		return $id;
	}
	/* }}} */
	/* upload_files {{{ */
	public function upload_files() {
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
			if ($full) {
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
		$att = $this->attachments_list($id);

		//create code for attachments
		$output = new Layout_output();
		$conversion = new Layout_conversion();

		foreach ($att as $k=>$v) {
			$output->insertAction("delete", gettext("delete attachment"), "javascript: attachment_delete('".$v["id"]."');");
			$output->insertAction("file_download", gettext("download attachment"), "?mod=email&action=download_attachment&dl=1&id=".$v["id"]);
			$output->addCode(" ".$v["name"]." (".($v["h_size"]).")");
			$output->addTag("br");
		}
		$output->exit_buffer();
	}
	/* }}} */
	private function parseEmailHeader($header) {
		require(self::include_dir."dataParseEmailHeader.php");
		return $data;
	}

	public function getEmailById($id) {
		require(self::include_dir."dataGetEmailById.php");
		return $data;
	}

	public function getEmailBySearch($options, $start=0, $sort="") {
		require(self::include_dir."dataGetEmailBySearch.php");
		return $part;
	}
	/* getEmailBySearchAddSearchQuery {{{ */
	/**
	 * add search part to sql query to fetch emails
	 *
	 * @param string $sq The original sql query without search entries
	 * @param sting $search The search keywords
	 * @param int $use_subquery if set it will use MATCH fulltextsearch in a subquery
	 * @return string the new query we need to fire to the sql server
	 */
	private function getEmailBySearchAddSearchQuery($sq, $search, $use_subquery) {
		$like = sql_syntax("like");
		$esc  = sql_syntax("escape_char");

		$sq.= sprintf(" AND (subject %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR sender_emailaddress %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR ".$esc."to".$esc." %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR cc %s '%%%s%%' ", $like, $search);
		$sq.= sprintf(" OR bcc %s '%%%s%%' ", $like, $search);

		if ($use_subquery) {
			/* check if database is fulltext capable */
			if (sql_syntax("fulltext") == 1) {
				$sq.= sprintf(" OR id IN (select mail_id from mail_messages_data where MATCH (body,header) AGAINST ('%s')) ) ", $search);
			} else {
				$sq.= sprintf(" OR id IN (select mail_id from mail_messages_data where body %s '%%%s%%')) ", $like, $search);
			}
		} else {
			if (sql_syntax("fulltext") == 1) {
				$sq.= sprintf(" OR MATCH (mail_messages_data.body, mail_messages_data.header) AGAINST ('*%s*' IN BOOLEAN MODE) )", $search);
			} else {
				$sq.= sprintf(" OR mail_messages_data.body %s '%%%s%%')", $like, $search);
			}
		}
		return $sq;
	}
	/* }}} */
	public function html2filter($html) {
		require(self::include_dir."dataHtml2Filter.php");
		return $return;
	}

	public function html2text($html) {
		require(self::include_dir."dataHtml2Text.php");
		return $return;
	}
	public function autocomplete() {
		require(self::include_dir."autocomplete.php");
	}

	public function mail_delete_xml() {
		/* delete the mail */
		$item   = $_REQUEST["id"];
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);

		/* retreive the current folder */
		$q = sprintf("select folder_id from mail_messages where id = %d", $item);
		$res = sql_query($q);
		$item_folder = sql_result($res,0);
		$folderinfo = $this->getFolder($item_folder);

		if ($folder["high_parent"] == $deleted_items["id"]) {
			/* real delete the email */
			$this->mail_delete($item);
		} else {
			/* move the email to deleted items */
			$q = sprintf("update mail_messages set folder_id = %d where id = %d", $deleted_items["id"], $item);
			sql_query($q);
		}
		/* generate history call */
		echo "history_goback();";
	}

	/* only send email to deleted items */
	public function mail_delete_multi() {
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
		$current_folder = $_REQUEST["folder_id"];

		/* retrieve additional folder info */
		$folderinfo = $this->getFolder($_REQUEST["folder_id"]);

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
			}
		}
	}
	/* really delete mail items */
	public function mail_delete_multi_real() {
		$mails = $_REQUEST["checkbox_mail"];
		if (is_array($mails)) {
			foreach ($mails as $id=>$v) {
				$this->mail_delete($id);
			}
		}
	}

	public function mail_delete($id=0) {
		//retrieve all attachments of the message
		$q = sprintf("select id from mail_attachments where message_id = %d", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			//delete attachment
			$this->mail_delete_attachment($row["id"]);
		}

		//delete the mail
		$q = sprintf("delete from mail_messages where id = %d", $id);
		sql_query($q);

		$file = $GLOBALS["covide"]->filesyspath."/maildata/".$id;
		@unlink($file);

	}

	public function mail_delete_attachment($id=0) {
		//delete from filesys
		$q = sprintf("select name from mail_attachments where id = %d", $id);
		$res = sql_query($q);
		$name = sql_result($res,0);

		$fsdata = new Filesys_data();
		$ext = $fsdata->get_extension($name);

		$fspath = $GLOBALS["covide"]->filesyspath;
		$file = sprintf("%s/email/%s.%s", $fspath, $id, $ext);
		@unlink ($file);

		$q = sprintf("delete from mail_attachments where id = %d", $id);
		sql_query($q);
	}

	public function getSpecialFolder($name, $user_id=0) {
		$_user_id = (int)$user_id;
		if (!$user_id) {
			$user_id = "is null";
		} else {
			$user_id = " = ".(int)$user_id;
		}
		$q = sprintf("select * from mail_folders where parent_id is null and user_id %s and name = '%s'", $user_id, $name);
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

	public function checkSpecialFolder($id) {
		$q = sprintf("select parent_id from mail_folders where id = %d", $id);
		$res = sql_query($q);
		$parent = sql_result($res,0);
		if ($parent) {
			return 0;
		} else {
			return 1;
		}
	}

	public function change_description() {
		$id          = $_REQUEST["id"];
		$description = urldecode($_REQUEST["description"]);

		$q = sprintf("update mail_messages set description = '%s' where id = %d", $description, $id);
		sql_query($q);

		echo sprintf("document.getElementById('description_notify').innerHTML = '%s';", gettext("- saved -"));
	}

	public function change_folder_xml() {
		$id     = $_REQUEST["id"];
		$folder = $_REQUEST["folder"];
		$description = addslashes($_REQUEST["description"]);

		$q = sprintf("update mail_messages set folder_id = %d, description = '%s' where id = %d", $folder, $description, $id);
		#$q.= sprintf(" and (user_id = 0 or user_id is null or user_id = %d)", $_SESSION["user_id"]);
		sql_query($q);

		#$q = sprintf("select name from mail_folders where id = %d", $folder);
		#$res = sql_query($q);
		#$name = sql_result($res,0);
		//echo "alert('De email is verplaatst naar de gekozen map.');";
		echo "if (document.getElementById('mailnojump').checked == false) {	history_goback(); }";
	}

	public function toggle_private_state_xml() {
		$output = new Layout_output();

		$id = $_REQUEST["id"];
		$q = sprintf("select is_public from mail_messages where id = %d", $id);
		$res = sql_query($q);
		$state = sql_result($res,0);

		if ($state == 2) {
			/* state is private, set to public */
			$new_state = 0;
			$output->addCode( gettext("this email is public") );
			$output->insertAction("state_public", gettext("this email is public"), "");
		} else {
			/* state is public, set to private */
			$new_state = 2;
			$output->addCode( gettext("this email is private") );
			$output->insertAction("state_private", gettext("this email is private"), "");
		}

		$q = sprintf("update mail_messages set is_public = %d where id = %d", $new_state, $id);
		sql_query($q);

		echo sprintf("document.getElementById('private_state').innerHTML = '%s';",
			 str_replace("'", "\\'", $output->generate_output()) );

	}

	public function change_relation_list() {
		$this->change_relation();
		echo "document.getElementById('relation_".$_REQUEST["id"]."').style.display = 'none';";
		echo "hideInfoLayer();";
	}

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
		if (preg_match($pat, $v))
			return true;
		else
			return false;
	}


	public function change_project_xml() {
		$id       = $_REQUEST["id"];
		$project  = $_REQUEST["project"];
		if ($project < 0) {
			$project = 0;
		}

		$q = sprintf("update mail_messages set project_id = %d where id = %d", $project, $id);
		sql_query($q);

		if ($project) {
			$project_data = new Project_data();
			$project_info = $project_data->getProjectById($project);
			$name = $project_info[0]["name"];
		} else {
			$name = gettext("none");
		}
		echo("document.getElementById('project_name').innerHTML = '$name';");
		echo("document.getElementById('mailproject_id').value = '$project';");
	}


	public function remove_script_tags($html) {
		$conversion = new Layout_conversion();
		$html = $conversion->filterTags($html);
		return $html;
	}

	public function getEmailSignature($id) {
		if ($id>0) {
			$q = sprintf("select signature from mail_signatures where id = %d", $id);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		} else {
			$q = sprintf("select mail_signature from users where id = %d", $_SESSION["user_id"]);
			$res = sql_query($q);
			$sig = sql_result($res,0);
		}
		return $sig;
	}

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
			$email[ gettext("alternative signatures") ][$row["id"]] = "&lt;".$row["email"]."&gt; ".$row["subject"];
		}
		return $email;
	}
	public function getEmailAliasById($id) {
		$q = sprintf("select email, realname, companyname from mail_signatures where id = %d and user_id = %d", $id, $_SESSION["user_id"]);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}

	public function getEmailAliasesPlain() {
		$email = array();
		$user = new User_data();
		$userinfo = $user->getUserdetailsById($_SESSION["user_id"]);

		$email[-1] = $userinfo["mail_email"];
		$email[-2] = $userinfo["mail_email1"];

		$q = sprintf("select * from mail_signatures where user_id = %d", $_SESSION["user_id"]);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$email[$row["id"]] = $row["email"];
		}
		return $email;
	}

	//If for some reason mail comes in over and over again, these 2 functions will prevent it from showing to the user
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
					//now remove the mail
					$sql = sprintf("delete from mail_messages where id = %d", $row["id"]);
					sql_query($sql);
				}
			}
			$record++;
		}
		return $buf;
	}

	public function getAttachmentIdByCid($mail_id, $cid) {
		$q = sprintf("select id from mail_attachments where cid = '<%s>' and message_id = %d", $cid, $mail_id);
		$res = sql_query($q);
		if (sql_num_rows($res)==1) {
			return sql_result($res,0);
		} else {
			return false;
		}
	}
	public function getAttachment($id, $fetchdata=0) {
		$q = sprintf("select * from mail_attachments where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$row["name"] = $this->decodeMimeString($row["name"]);

		$fsdata = new Filesys_data();
		$row = $fsdata->detect_preview($row);

		$conversion = new Layout_conversion();
		$row["h_size"] = $conversion->convert_to_bytes($row["size"]);
		unset($conversion);

		if ($fetchdata == 1) {
			/* retrieve from filesys */
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fsdata = new Filesys_data();

			$ext = $fsdata->get_extension($row["name"]);
			$file = sprintf("%s/email/%s.%s", $fspath, $id, $ext);

			$row["data_file"] = $file;
			$datafile = fopen($file,"r");
			$row["data_binary"] = fread($datafile, filesize($file));
			fclose($datafile);

		}
		return $row;
	}

	private function showTemplateFile($id) {
		$q = sprintf("select * from mail_templates_files where id = %d", $id);
		$res = sql_query($q);
		$data = sql_fetch_assoc($res);

		switch (trim(strtolower($data["type"]))) {
			case "image/gif":
			case "image/jpeg":
			case "image/pjpeg":
				$data["subtype"] = "image";
				break;
		}

		$conversion = new Layout_conversion();
		$data["h_size"] = $conversion->convert_to_bytes($data["size"]);
		unset($conversion);

		/* retrieve from filesys */
		$fspath = $GLOBALS["covide"]->filesyspath;
		$file = sprintf("%s/templates/%s.dat", $fspath, $id);
		if (!file_exists($file)) {
			exit;
		}

		$data["data_file"] = $file;
		$datafile = fopen($file,"r");
		$data["data_binary"] = fread($datafile, filesize($file));
		fclose($datafile);

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: '.strtolower($data["type"]));

		echo $data["data_binary"];
		exit();

	}

	public function downloadAttachment($id) {
		$data = $this->getAttachment($id, 1);

		header("Content-Transfer-Encoding: binary");
		header("Content-Type: ".strtolower($data["type"]));
		#header("Content-Length: ".strlen($data["data_binary"]));

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

	public function _get_archive_id() {
		$q = "select id from mail_folders where parent_id is null and user_id is null and name = 'Archief'";
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$this->archive = $row["id"];
		return ($this->archive);
	}

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

	public function decodeEmailAddress($ow) {
		$ow = str_replace("<", "&lt;", $ow);
		$ow = str_replace(">", "&gt;", $ow);
		return $ow;
	}

	public function decodeMimeString($ow) {
		$conversion = new Layout_conversion();
		return $conversion->decodeMimeString($ow);
	}

	private function _getSubFolders($folder, $folders, $level) {
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
			$this->_getSubFolders($row["id"], &$folders, $level);
		}
	}

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

	private function checkSharePermissions($folder) {
		$q = "select count(*) from mail_permissions where folder_id = ".$folder;
		$res2 = sql_query($q);
		if (sql_result($res2,0)>0) {
			return 1;
		} else {
			return 0;
		}
	}


	public function getFolders($options="", $init_archive=0) {
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

		$q = "select id, name from mail_folders where (parent_id = 0 or parent_id is null) ";
		$q .= sprintf("and (user_id = %d %s) order by name", $options["user"], $sq);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if (!$folders[$row["id"]]) {
				$folders[$row["id"]] = array(
					"id"     => $row["id"],
					"name"   => $row["name"],
					"level"  => 0,
					"count"  => $count,
					"unread" => 0,
					"shared" => $this->checkSharePermissions($row["id"])

				);
			}
			$this->_getSubFolders($row["id"], &$folders, 0);
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
		$msg_id = ( strtoupper(dechex(mktime())).".".$this->generateRandInt(7)."@".$suffix );
		return $msg_id;
	}

	public function updateReadStatus($mail_id, $user_id) {
		$q = sprintf("update mail_messages set is_new = 0 where id = %d", $mail_id, $user_id);
		#echo $q;
		sql_query($q);
	}

	public function br2nl($str) {
		$str = preg_replace("/<br[^>]*?>/si", "\n", $str);
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

		set_time_limit(60*60*10); //10 hours
		ob_end_flush();


		$id = $_REQUEST["id"];
		$file = $_REQUEST["datafile"];
		$from = $_REQUEST["from"];
		$newsletter_target = $_REQUEST["newsletter_target"];

		$filename = $GLOBALS["covide"]->temppath.$file;
		$handle = fopen($filename, "rb");
		$data = fread($handle, filesize($filename));
		fclose($handle);

		$address_data = new Address_data();
		$blocksize = 50;

		session_write_close();

		ob_end_flush();
		ob_implicit_flush(1);

		ob_flush();
		flush();

		$list = $this->get_tracker_items($id);
		$i = 1;
		foreach ($list["list"] as $tracker) {

			if ($i % $blocksize == 0) {
				$num = $this->status_queue($id);

				/* using the mothod below, non-multipart aware browsers will ignore the
					-- boundaries and print them as text in the hidden iframe */
				$buffer = "";
				$buffer.= "<script>parent.updateCurrentStatus('$num');</script>\n";
				echo $buffer;

				flush();
				ob_flush();
				sleep(2); //relax the mail server
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
					$cmnt = $address_data->lookupRelationEmailCommencement($email, $newsletter_target);

					$this->insertTracking($tmp_data, $mailcode, $email, $cmnt);
					/*
					$tmp_data = str_replace("##trackerid##", $email, $tmp_data );
					$tmp_data = str_replace("##mailcode##", $mailcode, $tmp_data );
					$tmp_data = str_replace("##rcptcmnt##", $cmnt, $tmp_data );
					*/

					$smtp->set_data($tmp_data );

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

		/*
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("parent.updateCurrentStatus('".$this->status_queue($id)."');");
		$output->end_javascript();
		echo $output->generate_output()."\n";
		*/
		$buffer = "";
		$buffer.= "<script>parent.updateCurrentStatus('".$this->status_queue($id)."');</script>\n";
		echo $buffer;

		flush();
		ob_flush();

		@unlink($filename);
		exit();
	}

	public function insertTracking(&$data, $mailcode, $email, $cmnt) {
		preg_match_all("/#(=\r{0,1}\n){0,1}#[^#(=\r{0,1}\n){0,1}#]*?#(=\r{0,1}\n){0,1}#/s", $data, $matches);

		/* partial reverse quoted printable decoding */
		$matches = array_unique($matches[0]);
		foreach ($matches as $m) {
			$r = preg_replace("/=\r{0,1}\n/", "", $m);
			$data = str_replace($m, $r, $data);
		}

		/* insert tracker vars */
		/* =\n => ensure quoted printable wrap position */
		$data = str_replace("##trackerid##", $email."=\n", $data );
		$data = str_replace("##mailcode##", $mailcode."=\n", $data );
		$data = str_replace("##rcptcmnt##", $cmnt."<br><br>=\n", $data );
	}

	public function get_tracker_items($mail_id, $id="", $limit=0) {
		$emails = array();
		if ($mail_id) {
			$q = sprintf("select * from mail_tracking where mail_id = %d", $mail_id);
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
			$files[$row["position"]] = $row;
		}
		return $files;
	}

	public function templateSave($id) {

		$mail = $_REQUEST["mail"];
		if ($id) {
			$q = sprintf("update mail_templates set description = '%s', header = '%s', footer = '%s' where id = %d",
				$mail["description"], $mail["header"], $mail["footer"], $id);
			sql_query($q);
		} else {
			$q = sprintf("insert into mail_templates (description, header, footer) values ('%s', '%s', '%s')", $mail["description"], $mail["header"], $mail["footer"]);
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
						$timestamp_first = mktime();
					} else {
						$timestamp_first = $row["timestamp_first"];
					}

					$timestamp_last = mktime();
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
			if (preg_match("/^mailto:/si", $link)) {
				echo "<html><body><script>top.location.href='".$link."'; window.close();</script></body></html>";
			} else {
				$link = preg_replace("/(\r)|(\n)/s", "", $link);
				header("Location: ".$link);
			}
		}
		exit();
	}

	/* signatures */
	public function get_signature_list($id="", $user_id=0) {
		if (!$user_id) $user_id = $_SESSION["user_id"];
		$data = array();
		if ($id) {
			$q = sprintf("select * from mail_signatures where user_id = %d and id = %d", $user_id, $id);
		} else {
			$q = sprintf("select * from mail_signatures where user_id = %d order by email", $user_id);
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row;
		}
		return $data;
	}

	public function signatureSave($id) {

		$mail = $_REQUEST["mail"];
		if (!$id) {
			$q = sprintf("insert into mail_signatures (user_id, email, signature, subject, realname, companyname) values (%d, '%s', '%s', '%s', '%s', '%s')",
				$_SESSION["user_id"], $mail["email"], $mail["signature"], $mail["subject"], $mail["realname"], $mail["companyname"]);
			sql_query($q);

		} else {
			$q = sprintf("update mail_signatures set email = '%s', signature = '%s', subject = '%s', realname = '%s', companyname = '%s' where id = %d",
				$mail["email"], $mail["signature"], $mail["subject"], $mail["realname"], $mail["companyname"], $id);
			sql_query($q);
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
			$q = sprintf("insert into mail_filters (user_id, sender, receipient, subject, to_mapid, priority) values (%d, '%s', '%s', '%s', %d, %d)",
				$_SESSION["user_id"], $mail["sender"], $mail["receipient"], $mail["subject"], $mail["to_mapid"], $mail["priority"]);
			sql_query($q);

		} else {
			$q = sprintf("update mail_filters set sender = '%s', receipient = '%s', subject = '%s', to_mapid = %d, priority= %d where id = %d",
				$mail["sender"], $mail["receipient"], $mail["subject"], $mail["to_mapid"], $mail["priority"], $id);
			sql_query($q);
		}
	}

	public function filterDelete($id) {
		$q = sprintf("delete from mail_filters where id = %d", $id);
		sql_query($q);
	}


	public function getPrefix($no_html_tags=0) {
		if (!$no_html_tags) {
			$prefix = "<html>\n";
			$prefix.= "<head>\n";
			$prefix.= " <base target=\"_blank\">\n";
			$prefix.= " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
		}
		$prefix.= " <style type='text/css'>\n";
		$prefix.= "  body, td, span, div, A {\n";
		$prefix.= "   font-family: arial, serif;\n";
		$prefix.= "   font-size: 10pt;\n";
		$prefix.= "   color: black;\n";
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
		$prefix.= "  }\n";
		$prefix.= "  .cell2 {\n";
		$prefix.= "   padding-left: 10px;\n";
		$prefix.= "   padding-right: 10px;\n";
		$prefix.= "  }\n";



		$prefix.= " </style>\n";
		if (!$no_html_tags) {
			$prefix.= "</head>\n";
			$prefix.= "<body>\n";
		}

		return $prefix;
	}

	public function stripBodyTags($body) {
		$strip_tags = array("!doctype","html","head","title","meta","body");
		foreach ($strip_tags as $k=>$v) {
			$repl = "/<\/{0,1}".$v."[^>]*?>/si";
			$body = preg_replace($repl, "", $body);
		}
		return $body;
	}

	public function stylehtml($body) {
		$body = $this->remove_script_tags($body);
		if (preg_match("/<body[^>]*?>/si", $body)) {
			preg_match_all("/<body[^>]*?>/si", $body, $matches);
			$newbody = $matches[0][0];
		}
		$body = $this->getPrefix() . $this->stripBodyTags($body) . "</body></html>";
		if ($newbody) {
			$body = preg_replace("/<body[^>]*?>/si", $newbody, $body);
		}
		return $body;
	}

	public function addAttachmentCovide() {
		require(self::include_dir."dataAddAttachmentsCovide.php");
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

	public function checkFilePermissions($id, $mail_id=0, $address_id = 0) {
		$xs = 0;

		$fsdata = new Filesys_data();

		/* get email folders and archive id if not in cache already */
		if (!$this->_cache["folders"]) {
			$this->_cache["folders"]    = $this->getFolders();
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
			if ($mail_id && $row["folder_id"] != $this->_cache["archive_id"]["id"]) {
				$xs = 0;
			}
			if ($xs == 1) {
				if ($row["folder_id"] == $this->_cache["archive_id"]["id"]) {
					$folder["name"] = gettext("Archive");
					$folder["id"]   = $row["folder_id"];
				} else {
					$folder = $this->_cache["folders"][$row["folder_id"]];
				}

				$ftype = $fsdata->getFileType($row["attachmentname"]);
				$data = array(
					"date"       => date("d-m-Y", $row["date"]),
					"short_date" => date("d-m-Y", $row["date"]),
					"short_time" => date("H:m", $row["date"]),
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

	public function mime_encode($str) {
		if (imap_utf7_encode($str) != $str) {
			return sprintf("=?UTF-8?Q?%s?=", preg_replace("/(\t)|(\r)|(\n)/s", "", imap_8bit($str)));
		} else {
			return $str;
		}
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
	public function savePermissions($id, $folder_id, $users, $user_id) {
		/* check if folder id does exist */
		$q = sprintf("select id from mail_permissions where folder_id = %d and user_id = %d", $folder_id, $user_id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$id = sql_result($res,0);
		}
		if (!$id) {
			$q = sprintf("insert into mail_permissions (folder_id, user_id, users) values (%d, %d, '%s')",
				$folder_id, $user_id, $users);
		} else {
			$q = sprintf("update mail_permissions set users = '%s' where id = %d", $users, $id);
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

		if (!file_exists($file)) {
			$syntax = sql_syntax("escape_char");
			$q = sprintf("select sender_emailaddress, cc, %sto%s, subject, mail_messages_data.body, mail_messages_data.mail_decoding from mail_messages left join mail_messages_data on mail_messages_data.mail_id = mail_messages.id where mail_messages.id = %d", $syntax, $syntax, $id);
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

			$body = strip_tags($row["body"]);
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
			//echo $file."<br>";
		}
	}
	/* cleanUpMaildata */
	/**
	 * Remove leftover maildata that can be deleted
	 */
	public function cleanUpMaildata() {
		/* cleanup maildata items */
		$q = "select mail_id from mail_messages_data where mail_id NOT IN (select id from mail_messages)";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* check if mail does really not exists */
			$q = sprintf("select count(*) from mail_messages where id = %d", $row["mail_id"]);
			$res2 = sql_query($q);
			if (sql_result($res2, 0) == 0) {
				/* delete from mail_messages_data */
				$q = sprintf("delete from mail_messages_data where mail_id = %d", $row["mail_id"]);
				sql_query($q);

				/* delete from filesys */
				$path = $GLOBALS["covide"]->filesyspath."/maildata/";
				$file = $path.$row["mail_id"].".txt";
				@unlink($file);
			}
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
		$q = sprintf("select * from mail_permissions where users %s and user_id != %d", $regex, $user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$username = $user_data->getUserNameById($row["user_id"]);
			$row["username"] = $username;
			$data[]=$row;
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
						$str = str_replace($c, mb_convert_encoding($c, "Windows-1252", $encoding), $str);
					}
					$encoding = "Windows-1252";
				}
			}
		}
	}
}
?>
