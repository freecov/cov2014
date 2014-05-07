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

Class Email_retrieve Extends Email_data {

	private $xmail;
	private $header;
	private $size_limit;

	public $data;

	private $mbox;
	private $user_id;
	private $folder;

	private $address;
	private $filters;
	private $filesys;

	/* needed for Covide 3.x to 4.x SR1 conversion */
	/* WARNING: will slow down processing time!    */
	private $message_conversion = 0;

	/* encodings */
	private $mime_enc;
	private $trans_enc;

	private $debug = 0;

	private $user_folders;
	private $user_folders_lookup;

	private $use_output = 0;


	/* constants */
	const include_dir = "classes/email/inc/";
	const class_name = "email_retrieve";

	public function __construct($user_id=0, $use_output=1) {

		$this->use_output = $use_output;

		// Pathetic mime-encoding table, cause imap functions return # instead of names.
		$mime_enc[0] = "text";
		$mime_enc[1] = "multipart";
		$mime_enc[2] = "message";
		$mime_enc[3] = "application";
		$mime_enc[4] = "audio";
		$mime_enc[5] = "image";
		$mime_enc[6] = "video";
		$mime_enc[7] = "other";

		// Transfer encoding, same deal as with mime-enc table
		$trans_enc[0] = "7BIT";
		$trans_enc[1] = "8BIT";
		$trans_enc[2] = "BINARY";
		$trans_enc[3] = "BASE64";
		$trans_enc[4] = "QUOTED-PRINTABLE";
		$trans_enc[5] = "OTHER";

		$this->mime_enc  =& $mime_enc;
		$this->trans_enc =& $trans_enc;

		/* read user id from arguments line */
		$this->user_id = $user_id;
		if (!$this->user_id)
			$this->user_id = (int)$_REQUEST["user_id"];

		$this->user_folders = $this->getFolders(array("user"=>$this->user_id));
		foreach ($this->user_folders as $v) {
			if ($v["level"]) {
				$this->user_folders_lookup[$v["name"]] = $v["id"];
			}
		}
	}

	public function retrieve() {
		$user_id = $this->user_id;

		//for nicer output in the browser
		if ($this->debug && $this->use_output)
			echo "<PRE>";

		$meminfo = memory_get_usage();
		$meminfo /= 1024;
		$meminfo = number_format($meminfo, 0)." KB";
		$this->markup("! memory statistics at start [$meminfo] ! ", "grey");

		if (!$user_id){
			echo gettext("no access");
			exit();
		}

		session_write_close();

		$userdata = new User_data();
		$userinfo = $userdata->getUserdetailsById($user_id);

		$user["mail_deltime"]  = $userinfo["mail_deltime"];
		$user["mail_server"]   = $userinfo["mail_server"];
		$user["mail_imap"]     = $userinfo["mail_imap"];
		$user["mail_username"] = $userinfo["mail_user_id"];
		$user["mail_password"] = $userinfo["mail_password"];

		/* set a time limit for scripts (60 * 10 sec default) */
		set_time_limit(600);

		$this->size_limit = 50*1024*1024; //50 MB

		$folder["inbox"]    = $this->getSpecialFolder("Postvak-IN", $user_id);
		$folder["bounced"]  = $this->getSpecialFolder("Bounced berichten", $user_id);
		$folder["deleted"]  = $this->getSpecialFolder("Verwijderde-Items", $user_id);
		$folder["send"]     = $this->getSpecialFolder("Verzonden-Items", $user_id);
		$this->folder = $folder;

		//TODO: remove old items from email after time

		$r = $this->mailConnect($user);
		switch ($r) {
			case 1:
				$this->refresh_opener();
				return "wrong username and/or password!";
			case 2:
				$this->refresh_opener();
				return "no changes found in mailbox";
		}
		$mbox =& $this->mbox;

		/* check for errors occured during the previous session */
		$this->purgeMailErrors();

		/* retrieve the emails */
		$this->mailProcess($user);

		//$this->cleanUpMaildata();

		$this->refresh_opener();
		return "mailfetch done";
	}

	private function refresh_opener() {
		if ($this->debug)
			die("eof");

		if ($this->use_output) {
			echo "
				<script>
					if (opener.document.getElementById('mod').value == 'email') {
						opener.document.getElementById('velden').submit();
					}
					var tx = setTimeout('window.close();', 200);
				</script>
			";
		}
	}
	/* function removes all mails with errors occured during the previous retrieve session */
	private function purgeMailErrors() {
		$q = sprintf("select id from mail_messages where status_pop = 1 and user_id = %d", $this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_array($res)) {
			$this->mail_delete($row["id"]);
			echo "! error fixed !\n";
		}
	}

	private function mailProcess($settings) {
		require(self::include_dir."retrieveMailProcess.php");
	}

	private function parseMessage($imap_id) {
		require(self::include_dir."retrieveParseMessage.php");
		return $data;
	}
	private function parseAttachments($imap_id, $mail_id) {
		require(self::include_dir."retrieveParseAttachments.php");
	}
	private function checkBouncer(&$header, &$data) {
		require(self::include_dir."retrieveCheckBouncer.php");
	}

	/* connect to a mail server */
	private function mailConnect($settings) {
		switch ($settings["mail_imap"]) {
			case 1:
				$protocol = "143/imap/novalidate-cert";
				break;
			case 2:
				$protocol = "993/imap/ssl/novalidate-cert";
				break;
			case 3:
				$protocol = "110/pop3/notls";
				break;
			default:
				$protocol = "110/pop3/notls";
				break;
		}
		$connstr = sprintf("{%s:%s}INBOX",  $settings["mail_server"], $protocol);
		$ok = (
			$mbox = imap_open(
				$connstr,
				$settings["mail_username"],
				$settings["mail_password"],
				OP_SHORTCACHE
			)
		) ? true : false;
		if (!$ok) {
			$imap_err = imap_errors();
			$this->markup("! An error occured - processing ended !", "red");
			foreach ($imap_err as $err) {
				echo sprintf("- error: %s\n", $err);
			}
			// user action
			if ($_REQUEST["user_id"] && $_REQUEST["action"] == "retrieve")
				exit();

			return 1;
		} else {
			if ($settings["mail_imap"]) {
				$stat = imap_status($mbox, $connstr, SA_ALL);

				$q = sprintf("select timestamp from status_conn where user_id = %d", $this->user_id);
				$res = sql_query($q);
				if (sql_num_rows($res) == 1) {
					/* if last modification date is the same as the previous */
					if (sql_result($res,0) == $stat->uidnext) {

						/* check if imap expunge flag is set */
						$q = sprintf("select count(*) from status_list where user_id = %d and mark_expunge = 1",
							$this->user_id);
						$res = sql_query($q);
						if (sql_result($res,0) == 0) {
							if (!$this->debug)
								return 2;
						}

					} else {
						$q = sprintf("update status_conn set timestamp = '%s' where user_id = %d",
							$stat->uidnext, $this->user_id);
						sql_query($q);
					}
				} else {
					$q = sprintf("delete from status_conn where user_id = %d", $this->user_id);
					sql_query($q);

					$q = sprintf("insert into status_conn (user_id) values (%d)", $this->user_id);
					sql_query($q);
				}
			}
		}
		/* set mbox stream */
		$this->mbox =& $mbox;
		echo $this->markup("- connection ok -", "green");
	}

	private function XmailAdd(&$obj, &$stream, &$mailnr, $part, $defname="headers.txt") {
		require(self::include_dir."retrieveXmailAdd.php");
	}

	//}}}-------------------------------------------------------------
	//{{{ mime_scan_multipart: recursivaly scan all subparts mimestuff
	//----------------------------------------------------------------
	private function mime_scan_multipart(&$parts, $part_number, &$stream, &$mailnr, $ign_part=0, $is_alternative_part=0) {
		require(self::include_dir."retrieveMimeScanMultipart.php");
	}

	//}}}-------------------------------------------
	//{{{ mime_scan: get mimetype info from only one
	//----------------------------------------------
	private function mime_scan(&$obj, &$stream, &$mailnr)	{

		$xmail =& $this->xmail;

		//if mail = multipart message
		if ($obj->type == 1) {
			$this->mime_scan_multipart($obj->parts, "", $stream, $mailnr);
		} elseif ($obj->type >= 3) {
			if (strtolower($obj->disposition) == "attachment" || $obj->type != 0) {
				$this->XmailAdd($obj, $stream, $mailnr, "1");
			}
		}
	}

	//}}}-------------------------------------------------
	//{{{ get_mime_type: figure out mime type of structure
	//----------------------------------------------------
	private function get_mime_type(&$structure) {
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
		if ($structure->subtype) {
			return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
		}
		return "TEXT/PLAIN";
	}

	//}}}------------------------------------------------
	//{{{ get_part: get a mailpart and fetch mimetype etc
	//---------------------------------------------------
	private function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
		require(self::include_dir."retrieveGetPart.php");
		return $return;
	}

	//}}}---------------------------------------------------------------------
	//{{{ UUdecodeMailAtt: uuencoded attachment, use *NIX uudecode sys command
	//------------------------------------------------------------------------
	private function UUdecodeMailAtt($data, $ct, $enc, $uuname=0){
		require(self::include_dir."retrieveUUdecodeMailAtt.php");
		return $data;
	}

	//}}}--------------------------------------------------------------------------
	//{{{ winmail_decode: actual decoding of winmail.dat with *NIX tnef sys command
	//-----------------------------------------------------------------------------
	private function winmail_decode($data) {
		$path_tmp = $GLOBALS["covide"]->temppath;
		$dirname = $uniq = "winmail_".strtolower(md5(uniqid(time())));
		$dir = $path_tmp.$dirname;
		//make sure the dir is empty and fresh
		$this->delDir($dir);
		mkdir($dir, 0777);

		$mijnFile = $dir."/winmail.dat";

		$fp = fopen($mijnFile,"w");
		//$data = gzcomp($data);
		fwrite( $fp, $data );
		unset ($fp);
		//do the tnef decoding
		$cmd = "tnef -f $mijnFile -C $dir";
		exec($cmd, $ret, $retval);
		@unlink($mijnFile);

		return $dir;
	}

	//}}}------------------------------------
	//{{{ delDir: delete a folder - recursive
	//---------------------------------------
	private function delDir($dirName) {
		if(empty($dirName)) {
			return;
		}
		if(file_exists($dirName)) {
			$dir = dir($dirName);
			while($file = $dir->read()) {
				if($file != '.' && $file != '..') {
					if(is_dir($dirName.'/'.$file)) {
						$this->delDir($dirName.'/'.$file);
					} else {
						@unlink($dirName.'/'.$file);
					}
				}
			}
			@rmdir($dirName.'/'.$file);
		} else {
			#echo 'Folder "<b>'.$dirName.'</b>" doesn\'t exist.';
		}
	}
	//}}}---------------------------------------
	//{{{ listDir: lists contents of a directory
	//------------------------------------------
	private function listDir($dirName) {
		$files = array();
		if ($handle = opendir($dirName)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$files[]=$file;
				}
			}
			closedir($handle);
		}
		return $files;
	}

	public function detectMimetype($file) {

		$file = escapeshellarg($file);
		$cmd = sprintf("file -i %s", $file);

		exec ($cmd, $ret, $status);
		$ret = explode(": ", $ret[0]);
		$ret = strtoupper(preg_replace("/\;.*$/s","",$ret[1]));
		return $ret;
	}

	private function markup($str, $color) {
		if ($this->debug && $this->use_output) {
			echo "<font color='$color'>".date("H:i:s")." => $str</font>\n";
		}
	}
}


?>
