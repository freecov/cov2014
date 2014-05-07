<?php
Class Voip_data {

    /* 	getSMSSettings {{{ */
    /**
     * 	Retreive Bayham sms settings from db
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

    /* 	alterFax {{{ */
    /**
     * 	alterFax. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function alterFax($faxid, $addressid, $redirparent = 0) {
		$sql = sprintf("UPDATE faxes SET relation_id = %d WHERE id = %d", $addressid, $faxid);
		$res = sql_query($sql);
		if ($redirparent) {
			echo "reload_page();";
		}
	}
    /* }}} */

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

	public function getFaxRelationById($id) {
		$q = sprintf("select relation_id from faxes where id = %d", $id);
		$res = sql_query($q);
		return (int)sql_result($res,0);
	}

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

	public function getActiveCalls() {
		header("Content-type: text/plain; charset=ISO-8859-1");

		/* garbage collector */
		/* cleanup old records (old = > 30 sec) */
		$q = sprintf("delete from active_calls where timestamp < %d", mktime()-30);
		sql_query($q);

		$q = "select * from active_calls";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("%d|%s#", $row["address_id"], $row["name"]);
		}
		exit();
	}

    /* 	sendSMS {{{ */
    /**
     * 	send an SMS with bayham systems
     *
     * @param int the userid to sms
	 * @param text the sms body
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
}
?>
