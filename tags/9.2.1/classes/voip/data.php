<?php
/**
 * Covide Groupware-CRM Voip module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Voip_data {

		/* settings */
		private $cleanup_voip    = 30; //seconds
		private $cleanup_invites = 3600; // = 1 hour

    /* getSMSSettings {{{ */
    /**
     * Retreive sms settings from db
     * This is now accepting other SMS providers than Bayham as well, as long as they provide a http(s) post api
     *
     * @return array the sms settings we need to send sms
     */
	public function getSMSSettings() {
		$sql = "SELECT * FROM sms_settings";
		$res = sql_query($sql);
		if (sql_num_rows($res)) {
			$row = sql_fetch_assoc($res);
			/* if there's content in the translation field, convert it to an array
			   Format of the field: key1->value1:key2->value2:key3->value3 */
			if(trim($row["trans"]) != "") {
				$trans=explode(":", $row["trans"]);
				unset($row["trans"]);
				foreach ($trans as $t) {
					$tt=explode("->", $t, 2);
					$row["trans"][trim($tt[0])] = trim($tt[1]);
				}
			}
			return $row;
		} else {
			return false;
		}
	}
    /* }}} */
    /* rewriteSMSPostField {{{ */
    /**
     * For other SMS providers the fields containing companyID, user, password, message, MSISDN and originator ID
     * may be named differently. This translates the field name for other providers if this information is
     * contained in the sms config db, else it returns the same string untranslated
     *
     * @param string $string The name of the post field to translate
     * @param array $settings the sms settings array from the db
     * @return string the transtated post field name
     */
	public function rewriteSMSPostField($string,$settings) {
		if (isset($settings["trans"][$string])) {
			return $settings["trans"][$string];
		} else {
			return $string;
		}
	}
    /* }}} */
    
    /* normalizeMsisdn {{{ */
    /**
     * This 'canonifies' the called number with regard to country prefixes and
     * intl. number format and will strip all non-numbers in the end
     *
     * @param string $string The number to 'normalize'
     * @param array $settings the sms settings array from the db
     * @return string the normalized Msisdn
     */
        public function normalizeMsisdn($string,$settings) {
		/* strip all non digits */
		$string = preg_replace("/\D/si", "", $string);
		if (substr($string, 0, 2) == "00") { // 003145 -> 3145
			return(substr($string, 2));
		}elseif (substr($string, 0, 1) == "0") { // 045 -> 3145
			return($settings["default_prefix"].substr($string, 1));
		}else{ // 3145, maybe was +3145 as well, but stripped by the preg
			return($string);
		}
	}
    /* }}} */
	/* deleteFax {{{ */
	/**
	 * Delete a fax from the list and filesystem
	 *
	 * @param int $faxid The fax to remove
	 * @param int $redirect if set user will be redirected to the faxlist, if not nothing happens here
	 */
	public function deleteFax($faxid = 0, $redirect = 1) {
		if ($_REQUEST["faxid"]) {
			$faxid = $_REQUEST["faxid"];
		}
		if (!$faxid) { return false; }
		$sql = sprintf("DELETE FROM faxes WHERE id=%d", $faxid);
		$res = sql_query($sql);
		$fileid = sprintf("%d", $faxid);
		$faxfile = $GLOBALS["covide"]->filesyspath."/faxes/".$fileid.".dat";
		if (file_exists($faxfile)) {
			@unlink($faxfile);
		}
		if ($redirect) {
			header("Location: index.php?mod=voip&action=faxlist");
		}
	}
	/* }}} */
    /* 	alterFax {{{ */
    /**
     * Update faxinfo in faxlist
     *
     * @param int $faxid The fax to save
	 * @param int $addressid The addressid to link this fax to.
	 * @param int $redirparent if set reload the parentpage using reload_page() js call
     */
	public function alterFax($faxid, $addressid, $redirparent = 0) {
		$sql = sprintf("UPDATE faxes SET relation_id = %d WHERE id = %d", $addressid, $faxid);
		$res = sql_query($sql);
		if ($redirparent) {
			echo "reload_page();";
		}
	}
    /* }}} */
	/* getFaxes {{{ */
	/**
	 * Get all the info about faxes in the database
	 *
	 * @return array The database info for faxes
	 */
	public function getFaxes() {
		$address_data = new Address_data();
		$sql_count = "SELECT COUNT(*) as count FROM faxes";
		$res_count = sql_query($sql_count);
		$row = sql_fetch_assoc($res_count);
		$faxinfo["count"] = $row["count"];
		unset($row);
		$sql = "SELECT * FROM faxes ORDER BY date";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$row["human_date"] = date("d-m-Y H:i", $row["date"]);
			$row["relation_name"] = $address_data->getAddressNameById($row["relation_id"]);
			$faxinfo["items"][] = $row;
		}
		return $faxinfo;
	}
	/* }}} */
	/* getFaxRelationById {{{ */
	/**
	 * Get the address_id for a fax
	 *
	 * @param int $id The faxid to use
	 * @return int The address_id
	 */
	public function getFaxRelationById($id) {
		$q = sprintf("select relation_id from faxes where id = %d", $id);
		$res = sql_query($q);
		return (int)sql_result($res,0);
	}
	/* }}} */
	/* getFaxFromFS {{{ */
	/**
	 * Get the fax as stored on the filesystem and optional also the binary content
	 *
	 * @param int $faxid The fax to retreive
	 * @param int $getbindata if set also attach the binary content to the array with info
	 * @return array name,type,size and optional the binary content of the fax
	 */
	public function getFaxFromFS($faxid, $getbindata=1) {
		$conversion = new Layout_conversion();
		$faxid = (int)$faxid; /* simple statement to disallow strings here */
		$faxinfo["name"] = "fax".$faxid.".pdf";

		/* file names */
		$faxfile = $GLOBALS["covide"]->filesyspath."/faxes/".$faxid.".dat";
		$tiffile = $GLOBALS["covide"]->temppath.$faxid.".tif";
		$pdffile = $GLOBALS["covide"]->temppath.$faxid.".pdf";
		$giffile = $GLOBALS["covide"]->temppath.$faxid.".gif";

		/* conversion */
		$bindata = exec("sfftobmp -t $faxfile -o $tiffile", $ret, $retval);
		$bindata = exec("tiff2pdf -o $pdffile $tiffile", $ret, $retval);
		$jpgdata = exec("convert -append -sample 50% $tiffile $giffile", $ret, $retval);

		/* read the pdf in a var */
		if ($getbindata) {
			$fp = fopen($pdffile, "r");
			$faxinfo["bindata"] = fread($fp, filesize($pdffile));
			fclose($fp);
			if (file_exists($giffile)) {
				$fp = fopen($giffile, "r");
				$faxinfo["gifdata"] = fread($fp, filesize($giffile));
				fclose($fp);
			}
		}

		$faxinfo["size"]    = filesize($pdffile);
		$faxinfo["h_size"]  = $conversion->convert_to_bytes($faxinfo["size"]);
		/* remove temp files */
		@unlink($tiffile);
		@unlink($pdffile);
		@unlink($giffile);

		return $faxinfo;
	}
	/* }}} */
	/* getActiveCallsDB {{{ */
	/**
	 * Get the active calls that are stored in the database
	 *
	 * @param int $cleanup_limit The time a record should stay in the database
	 * @return string address_id|companyname
	 */
	private function getActiveCallsDB() {
		$q = "select * from active_calls order by IF(user_id, 0, 1), timestamp";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if (!$row["user_id"] && $row["timestamp"] < mktime()-$this->cleanup_voip) {
				/* if voip call and call is too old */
				$q = sprintf("delete from active_calls where address_id = %d and timestamp = %d",
					$row["address_id"], $row["timestamp"]);
				sql_query($q);
			} elseif ($row["user_id"] && $row["timestamp"] < mktime()-$this->cleanup_invites) {
				/* if invite and invite is too old */
				$q = sprintf("delete from active_calls where user_id = %d and timestamp = %d",
					$row["user_id"], $row["timestamp"]);
				sql_query($q);
			} else {
				$d = mktime()-$row["timestamp"];
				if ($d < 0)
					$d = sprintf("%d %s", (int)(0-($d/60)), gettext("min in the future")."!");
				elseif ($d < 60)
					$d = sprintf("< 1 ".gettext("minute ago"));
				else
					$d = sprintf("%d %s", (int)$d/60, gettext("minutes ago"));

				if ($_SESSION["user_id"] == $row["user_id"] && !$row["alert_done"]) {
					$alert = 1;
					$q = sprintf("update active_calls set alert_done = 1 where user_id = %d", $_SESSION["user_id"]);
					sql_query($q);
				} else {
					$alert = 0;
				}
				$buffer.= sprintf("%d|%s|%d|%s|%d|%d|%d|%d|%s#\n",
					$row["address_id"],
					str_replace(" ", "&nbsp;", $row["name"]),
					$row["user_id"],
					str_replace("", "&nbsp;", $d),
					$row["invite"],
					$row["timestamp"],
					$_SESSION["user_id"],
					$alert,
					$row["ident"]
				);
			}
		}
		return $buffer;
	}
	/* }}} */
	/* getActiveCalls {{{ */
	/**
	 * Get a list of active calls and echo them to the calling xmlhttp socket
	 *
	 * This function will use the APC extension if it's installed.
	 * That way not every call will query the database for this info.
	 * Every 2 seconds this APC variable will be cleared and a database request is
	 * done to repopulate it.
	 */
	public function getActiveCalls() {
		if (!strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) 
			header("Content-type: text/plain; charset=UTF8", true);

		echo $this->getActiveCallsDB($cleanup_limit);
		exit();
	}
	/* }}} */
    /* sendSMS {{{ */
    /**
     * send an SMS via HTTP(S) POST
     *
     * @param int $user_id the userid to sms
     * @param text $body the sms body
     * @return bool true on succes, false on failure
     */
	public function sendSMS($userid, $body) {
		if ($userid) {
			/* retreive our settings
			   We need them back up here because we
			   need the default country ID for the
			   installation */
			$settings = $this->getSMSSettings();
			if (!$settings) { return false; }
			/* retreive users cellphone number */
			$userdata = new User_data();
			$userinfo = $userdata->getUserdetailsById($userid);
			if ($userinfo["address_id"]) {
				/* get mobile number */
				$sql = sprintf("SELECT mobile_nr FROM address_private WHERE id = %d", $userinfo["address_id"]);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				if ($row["mobile_nr"]) {
					/* normalize the number to send */
					$msisdn = $this->normalizeMsisdn($row["mobile_nr"], $settings);
				} else {
					return false;
				}
			} else {
				return false;
			}
			/* sanitize body */
			$body = sprintf("%s", $body);
			$body = substr($body, 0, 160);
			require_once("HTTP/Request.php");
			$req = new HTTP_Request($settings["request_uri"]);
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
			$req->addPostData($this->rewriteSMSPostField("CompanyId", $settings), $settings["companyid"]);
			$req->addPostData($this->rewriteSMSPostField("UserId", $settings), $settings["userid"]);
			$req->addPostData($this->rewriteSMSPostField("Password", $settings), $settings["password"]);
			$req->addPostData($this->rewriteSMSPostField("Msisdn", $settings), $msisdn);
			$req->addPostData($this->rewriteSMSPostField("MessageText", $settings), $body);
			$req->addPostData($this->rewriteSMSPostField("OasText", $settings), $settings["sender"]);
			if (!PEAR::isError($req->sendRequest())) {
				unset($req);
				return true;
			} else {
				unset($req);
				return false;
			}
		}
	}
    /* }}} */
	/* updateCallTS {{{ */
	/**
	 * Put current timestamp in a txt file so we know wether to check db or not
	 *
	 * @return true on success and false on failure
	 */
	public function updateCallTS($req, $login, $pass) {
		/* variables */
		/*
			login: manager login
			password: manager password
			phonenr: the requested phone number
			example: ?mod=voip&action=updatecallts&login=mylogin&password=test&phonenr=1234567890
		*/

		/* if user is authorized */
		if ($req["login"] == $login && $req["password"] == $pass) {

			/* prevent session lock */
			header("Content-type: text/plain");
			session_write_close();
			$like = sql_syntax("like");

			/* define fields */
			$fields = array(
				"address_businesscards.business_phone_nr",
				"address_businesscards.business_mobile_nr",
				"address_businesscards.business_fax_nr",
				"address_businesscards.personal_phone_nr",
				"address_businesscards.personal_mobile_nr",
				"address_businesscards.personal_fax_nr"
			);

			/* prepare basic query */
			if ($req["phonenr"] && !is_numeric($req["phonenr"])) {
				$address = array(
					"id" => 0,
					"companyname" => $req["phonenr"]
				);
			} elseif ((int)$req["phonenr"] == 0) {
				$address = array(
					"id" => 0,
					"companyname" => gettext("unknown")
				);
			} else {
				$strippedzeros = 0;
				// strip first '0' character on national calls so it will actually match e.164 numbers in the database
				if (substr($req["phonenr"], 0, 1) == "0" && substr($req["phonenr"], 1, 1) > 0) {
					$req["phonenr"] = substr($req["phonenr"], 1);
					$strippedzeros = 1;
				}
				// strip first 2 '0' characters on international calls for the same reason
				if (substr($req["phonenr"], 0, 2) == "00") {
					$req["phonenr"] = substr($req["phonenr"], 2);
					$strippedzeros = 2;
				}
				$sql = "SELECT address.id, address.companyname, address_businesscards.id AS bcardid FROM address_businesscards
					LEFT JOIN address ON address.id = address_businesscards.address_id
					WHERE 1=0 \n ";

				foreach ($fields as $fld) {
					$sql.= sprintf(" OR replace(replace(%1\$s,'-',''), ' ','') %2\$s '%%%3\$s' \n ", $fld, $like, $req["phonenr"]);
				}
				#echo $sql ;

				$res = sql_query($sql);
				if (sql_num_rows($res) > 0) {
					$address = sql_fetch_assoc($res);
					$address_data = new Address_data();
					$bcard = $address_data->getAddressById($address["bcardid"], "bcards");
					$address["companyname"].="<br>".$bcard["tav"];
					unset($address_data);
				} else {
					for ($i = $strippedzeros; $i > 0; $i--) {
						$req["phonenr"] = "0".$req["phonenr"];
					}
					$address = array(
						"id" => 0,
						"companyname" => $req["phonenr"]
					);
				}
			}

			/* get dnd info */
			$sql = sprintf("SELECT voip_device from users where voip_device != '' AND id IN (
				SELECT calendar_user.user_id FROM calendar, calendar_user WHERE calendar.id = calendar_user.calendar_id AND
					(calendar.timestamp_start <= %1\$d AND calendar.timestamp_end >= %1\$d AND calendar.is_dnd = 1)
				) group by voip_device", mktime());
			$res = sql_query($sql);
			$dnd = array();
			while ($row = sql_fetch_assoc($res)) {
				$dnd[] = trim( $row["voip_device"] );
			}
			$dnd = implode(",",$dnd);

			if ($_REQUEST["prefix"])
				$address["companyname"] = sprintf("%s: %s", $_REQUEST["prefix"], $address["companyname"]);

			$now = $this->updateCallFile();

			$q = sprintf("insert into active_calls (name, address_id, timestamp, ident) values ('%s', %d, %d, '%s')",
				$address["companyname"], $address["id"], $now, md5(mktime().$address["id"].$address["companyname"]));
			sql_query($q);

			echo preg_replace("/(\r|\n|\t)/s", "", sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<item>
					<companyname>%s</companyname>
					<dnd>%s</dnd>
				</item>", strip_tags(str_replace("<br>", " ", $address["companyname"])), $dnd));

		} else {
			echo "You are not allowed here, go away!";
		}
		exit();
	}
	/* }}} */
	/* updateCallFile {{{ */
	/**
	 * Put current timestamp in a txt file so we know wether to check db or not
	 *
	 * @return current timestamp
	 */
	public function updateCallFile() {
		$now = mktime();
		$timestampfile = sprintf("%s/lastcall_%s.txt",
			$GLOBALS["covide"]->temppath,
			$GLOBALS["covide"]->license["code"]
		);
		if ($GLOBALS["covide"]->license) {
			file_put_contents($timestampfile, $now);
		}
		return $now;
	}
	/* }}} */
	/* mkIdent {{{ */
	/**
	 * Create a unique identifier for use in active_calls table
	 *
	 * @return current the ident string
	 */
	public function mkIdent($p = array()) {
		$str = sprintf("%d|%s", mktime(), implode("|", $p));
		$str = substr(md5($str), 0, 20);
		return $str;
	}
	/* }}} */
}
?>
