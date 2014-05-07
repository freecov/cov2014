<?php
Class Voip_data {

    /* getSMSSettings {{{ */
    /**
     * Retreive Bayham sms settings from db
     *
     * @return array the sms settings we need to send sms
     */
	public function getSMSSettings() {
		$sql = "SELECT * FROM bayham_settings";
		$res = sql_query($sql);
		if (sql_num_rows($res)) {
			$row = sql_fetch_assoc($res);
			return $row;
		} else {
			return false;
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
	private function getActiveCallsDB($cleanup_limit) {
		$q = "select * from active_calls";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["timestamp"] < mktime()-$cleanup_limit) {
				$q = sprintf("delete from active_calls where address_id = %d and timestamp = %d",
					$row["address_id"], $row["timestamp"]);
				sql_query($q);
			} else {
				$buffer.= sprintf("%d|%s#\n", $row["address_id"], $row["name"]);
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
		$cleanup_limit = 30; //cleanup calls in seconds

		header("Content-type: text/plain");
		session_write_close();

		/* limit some db connection if apc functions are available */
		if (function_exists('apc_fetch'))
			$apc = 1;

		/* traditional GET request */
		if ($apc) {
			$fetch = apc_fetch("voipcall");
			if ($fetch) {
				$fetch = unserialize($fetch);
				$from_apc = 1;
			}
		}
		if (!$fetch)
			$fetch = $this->getActiveCallsDB($cleanup_limit);

		echo $fetch;
		if ($apc && !$from_apc)
			apc_store("voipcall", serialize($fetch), 2); //buffer is 2 seconds valid

		exit();
	}
	/* }}} */
    /* sendSMS {{{ */
    /**
     * send an SMS with bayham systems
     *
     * @param int $user_id the userid to sms
	 * @param text $body the sms body
     * @return bool true on succes, false on failure
     */
	public function sendSMS($userid, $body) {
		if ($userid) {
			/* retreive users cellphone number */
			$userdata = new User_data();
			$userinfo = $userdata->getUserdetailsById($userid);
			if ($userinfo["address_id"]) {
				/* get mobile number */
				$sql = sprintf("SELECT mobile_nr FROM address_private WHERE id = %d", $userinfo["address_id"]);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				if ($row["mobile_nr"]) {
					/* strip all non digits */
					$msisdn = preg_replace("/\D/si", "", $row["mobile_nr"]);
					/* strip leading 0 */
					$msisdn = preg_replace("/^0/si", "", $msisdn);
					/* remove this line if you are not in the netherlands */
					$msisdn = "31".$msisdn;
				} else {
					return false;
				}
			} else {
				return false;
			}
			/* sanitize body */
			$body = sprintf("%s", $body);
			$body = substr($body, 0, 160);
			/* retreive our settings */
			$settings = $this->getSMSSettings();
			if (!$settings) { return false; }
			require("HTTP/Request.php");
			$req = new HTTP_Request("https://secure.bayhamsystems.com/mfi/sendMessage");
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
			$req->addPostData("CompanyId", $settings["companyid"]);
			$req->addPostData("UserId", $settings["userid"]);
			$req->addPostData("Password", $settings["password"]);
			$req->addPostData("Msisdn", $msisdn);
			$req->addPostData("MessageText", $body);
			$req->addPostData("OasText", $settings["sender"]);
			if (!PEAR::isError($req->sendRequest())) {
				return true;
			} else {
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
				"address.mobile_nr",
				"address_businesscards.business_phone_nr",
				"address_businesscards.business_mobile_nr",
				"address_businesscards.personal_phone_nr",
				"address_businesscards.personal_mobile_nr"
			);

			/* prepare basic query */
			if ((int)$req["phonenr"] == 0) {
				$address = array(
					"id" => 0,
					"companyname" => gettext("unknown")
				);
			} else {
				$sql = "SELECT address.id, address.companyname FROM address
					LEFT JOIN address_businesscards ON address.id = address_businesscards.address_id
					WHERE 1=0 \n ";

				foreach ($fields as $fld) {
					$sql.= sprintf(" OR replace(replace(%1\$s,'-',''), ' ','') %2\$s '%%%3\$s' \n ", $fld, $like, $req["phonenr"]);
				}
				#echo $sql ;

				$res = sql_query($sql);
				if (sql_num_rows($res) > 0) {
					$address = sql_fetch_assoc($res);
				} else {
					$address = array(
						"id" => 0,
						"companyname" => gettext("unknown")
					);
				}
			}

			/* get dnd info */
			$sql = sprintf("SELECT voip_device from users where voip_device != '' AND id IN (
				select user_id FROM calendar WHERE
					(timestamp_start <= %1\$d AND timestamp_end >= %1\$d AND is_dnd = 1)
				) group by voip_device", mktime());
			$res = sql_query($sql);
			$dnd = array();
			while ($row = sql_fetch_assoc($res)) {
				$dnd[] = trim( $row["voip_device"] );
			}
			$dnd = implode(",",$dnd);

			$now = mktime();
			$q = sprintf("insert into active_calls (name, address_id, timestamp) values ('%s', %d, %d)",
				$address["companyname"], $address["id"], $now);
			sql_query($q);

			$timestampfile = $GLOBALS["covide"]->temppath.sprintf("/lastcall_%s.txt", $GLOBALS["covide"]->license["code"]);
			file_put_contents($timestampfile, $now);

			echo preg_replace("/(\r|\n|\t)/s", "", sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<item>
					<companyname>%s</companyname>
					<dnd>%s</dnd>
				</item>", $address["companyname"], $dnd));

		} else {
			echo "You are not allowed here, go away!";
		}
		exit();

	}
	/* }}} */
}
?>
