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

	if (!$_SESSION["user_id"]) {
		$user_data = new User_data();
		$session_user_id = $user_data->getArchiveUserId();
	} else {
		$session_user_id = $_SESSION["user_id"];
	}

	$this->dropMailBodyToFilesys($id);

	/* check if this mail resides inside current mails */
	$q = sprintf("select count(mail_id) from mail_messages_data where mail_id = %d",
		$id);
	$res = sql_query($q);
	if (sql_result($res,0) == 0)
		$tbl = "mail_messages_data_archive";
	else
		$tbl = "mail_messages_data";

	$q = sprintf("select mail_messages.*,
		%1\$s.mail_decoding,
		%1\$s.body as mail_body,
		%1\$s.header as mail_header
		from mail_messages left join %1\$s ON %1\$s.mail_id = mail_messages.id ",
		$tbl);

	$q.= sprintf("where id = %d", $id);
	$res = sql_query($q);
	$row = sql_fetch_assoc($res, "", array("mail_body"));

	/* ================= start permission check =================== */
	/* check permissions */
	$xs = 0;

	/* first check if the mail is inside one of my folders */
	$my_folders = $this->getFolders(array("user" => $session_user_id));
	$sh_folders = $this->getSharedFolderAccess($session_user_id);
	$combined_folders = array();

	foreach ($my_folders as $f) {
		$combined_folders[$f["id"]] = $f["id"];
	}
	foreach ($sh_folders as $f) {
		$combined_folders[$f["folder_id"]] = $f["folder_id"];
	}
	$concepten = $this->getSpecialFolder("Concepten", $session_user_id);
	if ($concepten["id"] > 0)
		$combined_folders[$concepten["id"]] = $concepten["id"];

	unset($my_folders);
	unset($sh_folders);

	if ($combined_folders[$row["folder_id"]] == $row["folder_id"]) {
		$xs = 1;
	}

	/* second check if i have access to the address */
	$archive_mail = $this->getSpecialFolder("Archief", 0);
	$archive_mail = $archive_mail["id"];

	/* ispublic 0 = public, 2 = private */
	if (!$xs && $row["address_id"] && $row["folder_id"] == $archive_mail && $row["is_public"]!=2) {
		/* init user object */
		$user_data = new User_data();
		$userperms = $user_data->getUserPermissionsById($session_user_id);
		$accmanager_arr = explode(",", $user_data->permissions["addressaccountmanage"]);

		/* get the address */
		$address_data   = new Address_data();
		$addressinfo[0] = $address_data->getAddressById($row["address_id"]);

		if ($userperms["xs_addressmanage"]) {
			$xs = 1;
		} elseif ($GLOBALS["covide"]->license["address_strict_permissions"]) {

			$classification_data = new Classification_data();
			$cla_permission = $classification_data->getClassificationByAccess();

			/* get rw permissions for later use */
			$cla_address = explode("|", $addressinfo[0]["classifi"]);
			$cla_permission_rw = $classification_data->getClassificationByAccess(1);
			$cla_xs = array_intersect($cla_address, $cla_permission_rw);
			if (count($cla_xs) > 0)
				$xs = 1;

			$cla_xs = array_intersect($cla_address, $cla_permission);
			if (count($cla_xs) > 0)
				$xs = 1;
		} elseif ($addressinfo[0]["addressacc"] || $addressinfo[0]["addressmanage"])
			$xs = 1;
	}
	/* third check if i have access to the project */
	if (!$xs && $row["project_id"] && $row["folder_id"] == $archive_mail && !$row["is_public"]) {
		$projectdata = new Project_data();
		$projectinfo = $projectdata->getProjectById($row["project_id"]);

		if ($projectinfo[0]["group_id"])
			$projectmaster = $projectdata->getProjectById($projectinfo[0]["group_id"], 1);

		if ($projectdata->dataCheckPermissions($projectinfo[0]) || !$projectdata->dataCheckPermissions($projectmaster[0]))
			$xs = 1;
	}
	/* fourth check if the user owns the mail when it is set to private */
	if (!$xs && $row["is_public"] == 2 && $session_user_id == $row["user_id"])
		$xs = 1;

	/* fifth check if the user doesnt own the mail when it is set to private but is admin */
	$user_data = new User_data();
	$user_data->getUserPermissionsById($session_user_id);
	if (!$xs && $row["is_public"] == 2 && $user_data->permissions["xs_usermanage"] == 1)
		$xs = 1;

	/* final - if no access */
	if (!$xs) {
		$row = array();
		$row["subject"] = gettext("Access to this mail is denied!");
	}
	/* ================= end permission check =================== */


	$conversion = new Layout_conversion();

	if ($row["mail_decoding"]) {
		$this->handleWindows1251($row["mail_body"], $row["mail_decoding"]);
		// for some strange reason, mail stored as iso-8859-1 is already UTF-8 here
		// XXX: This is not true. We just had a couple of mails like this, but the majority is correct.
		// That's why this check is commented out again.
		//if (mb_detect_encoding($row["mail_body"], $row["mail_decoding"].", UTF-8, ISO-8859-1") != "UTF-8" && mb_detect_encoding($row["mail_body"], $row["mail_decoding"].", UTF-8, ISO-8859-1") != "ISO-8859-1") {
			$row["mail_body"] = @mb_convert_encoding($row["mail_body"], "UTF-8", $row["mail_decoding"]);
		//}
	} else {
		$enc = mb_detect_encoding($row["mail_body"], "UTF-8, ISO-8859-1");
		$this->handleWindows1251($row["mail_body"], $enc);
		if ($enc != "UTF-8") {
			$row["mail_body"] = @mb_convert_encoding($row["mail_body"], "UTF-8");
		}
	}

	/* only put attachments in this mail if we have access */
	if ($xs) {
		$row["attachments"] = $this->attachments_list($id, 1);
		/* scan for vcards */
		foreach ($row["attachments"] as $att) {
			if ($att["subtype"] == "vcard") {
				$row["vcard"] = $att["id"];
			}
		}

		$row["attachments_count"] = count($row["attachments"]);
		$list = array();
		foreach ($row["attachments"] as $k=>$v) {
			$list[]=$v["id"];
		}
		$row["attachments_ids"] = implode(",", $list);
	}

	$row["clean_emailaddress"]  = $this->cleanAddress($row["sender_emailaddress"]);
	if ($row["sender_emailaddress"]=="INVALID_ADDRESS@.SYNTAX-ERROR") {
		$row["sender_emailaddress"] = gettext("no sender");
	}

	$row["subject"]               = $this->decodeMimeString($row["subject"]);
	$row["sender_emailaddress"]   = $this->decodeMimeString($row["sender_emailaddress"]);
	$row["to"]                    = $this->decodeMimeString($row["to"]);

	if (!$row["user_id"]) {
		$row["user_id"] = $session_user_id;
	}

	if (is_numeric($row["sender_emailaddress"])) {
		$row["sender_emailaddress_h"] = "--";
	} else {
		$row["sender_emailaddress_h"] = str_replace(array("<",">"), array("&lt;", "&gt;"), $row["sender_emailaddress"]);
	}

	$row["to"]  = $this->cleanAddress($row["to"]);
	$row["cc"]  = $this->cleanAddress($row["cc"]);
	$row["bcc"] = $this->cleanAddress($row["bcc"]);

	$output = new Layout_output();
	$output->addTag("br");
	$br = $output->generate_output();
	$row["h_to"] = str_replace(",", ",".$br, $row["to"]);
	$row["h_cc"] = str_replace(",", ",".$br, $row["cc"]);
	$row["h_bcc"] = str_replace(",", ",".$br, $row["bcc"]);

	$row["h_date"]          = $row["date"] ? strftime("%d-%m-%Y %H:%M", $row["date"]) : "-";
	if ($row["date_received"]) {
		$row["h_date_received"] = strftime("%d-%m-%Y %H:%M", $row["date_received"]);
	}
	if ($row["is_new"]) {
		$row["h_isnew"] = gettext("new");
	} else {
		$row["h_isnew"] = gettext("read");
	}
	$row["is_text"]         = (int)$row["is_text"];

	$row["body"]   =& $row["mail_body"];
	$row["header"] =& $row["mail_header"];

	if ($row["askwichrel"] && $this->validateEmail($row["sender_emailaddress"])) {
		$row["check_askwichrel"] = 1;
	}

	if (!$row["is_text"]) {
		/* scan for inline images */
		preg_match_all("/ src=('|\")(cid:[^('|\")]*?)('|\")/sxi", $row["body"], $matches);
		foreach ($matches[2] as $k=>$v) {
			$v = preg_replace("/^cid:/sxi", "", $v);

			$att_id = $this->getAttachmentIdByCid($row["id"], $v);
			if ($att_id) {
				$repl = $GLOBALS["covide"]->webroot."?mod=email&action=download_attachment&id=".$att_id;
				$row["body"] = str_replace(trim($matches[0][$k]), " clsid=\"cid:$v\" src=".$matches[1][$k].$repl.$matches[3][$k], $row["body"]);
			}
		}
		/* convert outlook empty paras */
		$row["body"] = str_replace("<![if !supportEmptyParas]>&nbsp;<![endif]>", "&nbsp;", $row["body"]);
		$row["body"] = str_replace("<o:p></o:p>", "", $row["body"]);

		preg_match_all('/(<p\Wclass=Mso[^>]*?>)(.*)(<\/p>)/imxsU', $row["body"], $matches);
		$matches = array_unique($matches[0]);
		foreach ($matches as $k=>$v) {
			$r = preg_replace("/^<p([^>]*?)>/si", "<div $1>", $v);
			$r = preg_replace("/<\/p>$/si", "<br></div>", $r);
			$row["body"] = str_replace($v, $r, $row["body"]);
		}

		/* save full blown html version */
		$row["body_html"] = $row["body"];

		/* save a downgraded version for plain text */
		$row["body"] = $this->html2Text($row["body"]);
		if (preg_match("/(MSIE)|(Opera)/s", $_SERVER["HTTP_USER_AGENT"]))
			$row["body"]=wordwrap($row["body"], 100);

		$row["body_pre"] = preg_replace("/<br[^>]*?>/sxi", "\n", $row["body"]);
		$row["body_pre"] = str_replace("<", "&lt;", $row["body_pre"]);
		$row["body_pre"] = "<PRE wrap='1' style='word-wrap: break-word; size: 12pt;'>\n".$row["body_pre"]."\n</PRE>";

		$row["body_hl"] = $row["body_pre"];

	} else {
		/* save text version for browser */
		$row["body"] = $row["body"];
		if (preg_match("/(MSIE)|(Opera)/s", $_SERVER["HTTP_USER_AGENT"]))
			$row["body"]=wordwrap($row["body"], 100);

		$row["body_pre"] = "<PRE wrap='1' style='word-wrap: break-word; size: 12pt;'>".str_replace("\r", "", htmlspecialchars($row["body"])."</PRE>");

		$row["body_hl"] = $row["body_pre"];
		/* if a diff file is found, try highlighting */
		if (preg_match("/\n\-{3} /s", $row["body_hl"])
			&& preg_match("/\n\+{3} /s", $row["body_hl"])
			&& preg_match("/\n\@\@ /s", $row["body_hl"])) {

			$hl =& $row["body_hl"];
			$hl = explode("\n", $hl);
			foreach ($hl as $k=>$v) {
				if (preg_match("/^\+/s", $v))
					$hl[$k] = sprintf("<font color='green'>%s</font>", $v);
				elseif (preg_match("/^\-/s", $v))
					$hl[$k] = sprintf("<font color='red'>%s</font>", $v);
				elseif (preg_match("/^\@\@ /s", $v) || preg_match("/^Modified\: /s", $v))
					$hl[$k] = sprintf("<font color='blue'>%s</font>", $v);
			}
			$hl = implode("\n", $hl);
		}
		$row["body_hl"] = preg_replace("/(\shttp(s){0,1}:\/\/[^\s]*?\s)/sxi",
			"<a target='_blank' href='$1'>$1</a>", $row["body_hl"]);
	}

	/* remove windows line feeds */
	$row["body"] = str_replace("\r", "", $row["body"]);

	if ($this->get_current_tracker_item($row["id"])) {
		$row["tracking"] = 1;
	}

	/* public or private */
	if ($row["is_public"]==2) {
		$row["is_public_i"] = "state_private";
		$row["is_public_h"] = gettext("this email is private");
	} else {
		$row["is_public_i"] = "state_public";
		$row["is_public_h"] = gettext("this email is public");
	}


	/* append thedetailed header to the d ata array */
	$header = $this->parseEmailHeader($row["header"]);

	/* append mail tracking items to the data array */
	$q = sprintf("select count(*) from mail_tracking where mail_id = %d or mail_id_2 = %d", $id, $id);
	$res = sql_query($q);
	if (sql_result($res,0)>0) {
		$header["mail_tracking"] = 1;
	}

	$options = $this->decodeMailOptions($row["options"]);

	$data[0] = array_merge($row, $header, $options);

?>
