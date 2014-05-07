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

	if ($options["relation"]) {

		/* init user object */
		$user_data = new User_data();
		$userperms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
		$accmanager_arr = explode(",", $user_data->permissions["addressaccountmanage"]);

		/* get the address */
		$address_data   = new Address_data();
		$addressinfo[0] = $address_data->getAddressById($options["relation"]);

		if ($userperms["xs_addressmanage"])
			$astrict = 1;
		elseif ($GLOBALS["covide"]->license["address_strict_permissions"]) {
			$classification_data = new Classification_data();
			$cla_permission = $classification_data->getClassificationByAccess(1);
			$cla_address = explode("|", $addressinfo[0]["classifi"]);
			foreach ($cla_address as $k=>$v) {
				if ($v && in_array($v, $cla_permission))
					$astrict = 1;
			}
		}
		if (!$addressinfo[0]["addressacc"] && !$addressinfo[0]["addressmanage"] && !$astrict) {
			$output = new Layout_output();
			$output->layout_page("address");

			$venster = new Layout_venster(array(
				"title" => gettext("Relation Card"),
				"subtitle" => gettext("No permissions")
			));
			$venster->addVensterData();
				$venster->addCode(gettext("You have no permissions to access the following email archive").": ");
				$venster->insertTag("b", $addressinfo[0]["companyname"]);
				$venster->addTag("br");

				$history = new Layout_history();
				$link = $history->generate_history_call();
				$venster->addCode($link);

				$venster->insertAction("back", gettext("back"), "?mod=email");
			$venster->endVensterData();

			$table = new Layout_table();

			$output->addCode($table->createEmptyTable($venster->generate_output()));
			$output->exit_buffer();
		}
	}

	/* check for special folder sent-items or deleted-items or archive */
	$sent_items    = $this->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
	$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);
	$archive       = $this->getSpecialFolder("Archief", 0);

	if ($options["show_folders"]) {
		$_folders = $this->getFolders("", 1);
	}

	if ($options["folder"] == $sent_items["id"] || $options["folder"] == $deleted_items["id"]) {
		/* update all messages in this folder to read */
		$this->update_folder_readstatus($options["folder"]);
	}
	if ($options["folder"] == $archive["id"] && $options["relation"]) {
		$this->update_folder_readstatus($options["folder"], $options["address_id"]);
	}
	/* typecast the start parameter */
	$start = (int)$start;

	/* retrieve the sql like syntax */
	$like = sql_syntax("like");

	/* start query */
	$sq = "";
	$sq2 = " FROM mail_messages ";
	$data = array();

	if ($options["nolimit"] && $options["folder"] == $archive["id"] && !$options["relation"] && !$options["relation_inbox"]) {

		$use_subquery = 1;

		$sq2.= " WHERE 1=1 ";
		/* get user object */
		$user = new User_data();
		$user->getUserPermissionsById($_SESSION["user_id"]);

		if (!$user->checkPermission("xs_relationmanage")) {

			/* if this user is not a relation manager, this user has limited access */
			$accmanager_arr = $user->permissions["addressaccountmanage"];
			if ($accmanager_arr) {
				$accmanager_arr.= ",".$_SESSION["user_id"];
			} else {
				$accmanager_arr = $_SESSION["user_id"];
			}

			$sq2.= sprintf(" AND address_id IN (select id from address WHERE address.is_public = 1 AND user_id IN (%s)) ", $accmanager_arr);
		}

	} else {

		if ($options["search"]) {
			$sq2.= " LEFT JOIN mail_messages_data ON mail_messages_data.mail_id = mail_messages.id ";
			if ($options["search_archive"])
				$sq2.= " LEFT JOIN mail_messages_data_archive ON mail_messages_data_archive.mail_id = mail_messages.id ";
		} else {
			$sq2.= " LEFT JOIN mail_messages_data ON mail_messages_data.mail_id = mail_messages.id ";
		}
		$sq2.= " WHERE 1=1 ";
	}

	/* set start position */
	$options["start"] = $start;

	/* if request is done from the relation card (display all INBOX folders */
	if ($options["relation_inbox"] || $options["global_index"]) {
		$folders = $this->getFolders();

		$sent_items = $this->getSpecialFolder("Verzonden-Items", $_SESSION["user_id"]);
		$deleted_items = $this->getSpecialFolder("Verwijderde-Items", $_SESSION["user_id"]);

		if (count($folders) <5) {
			# user has less the all folders, force creation.
  			$this->getSpecialFolder("Postvak-IN", $_SESSION["user_id"]);
			$this->getSpecialFolder("Bounced berichten", $_SESSION["user_id"]);
			$this->getSpecialFolder("Concepten", $_SESSION["user_id"]);
			$folders = $this->getFolders();
		}

		/* exclude some special folders */
		$f = array();
		foreach ($folders as $k=>$v) {
			if ($k != $sent_items["id"] && $k != $deleted_items["id"]) {
				$f[]=$k;
			}
		}

		$f = implode(",", $f);
		if ($options["relation_inbox"]) {
			$sq2.= sprintf(" AND mail_messages.address_id = %d and folder_id IN (%s) ", $options["relation_inbox"], $f);
		} else {
			$sq2.= sprintf(" AND folder_id IN (%s) ", $f);
		}

		/* if we search for a keyword */
		if ($options["search"]) {
			$sq2 = $this->getEmailBySearchAddSearchQuery($sq2, $options["search"], $use_subquery, $options["search_archive"]);
		}

		/* if we search an relation */
		if ($options["relation"]) {
			$sq2 .= sprintf(" AND mail_messages.address_id = %d ", $options["relation"]);
		}

		/* if we search a bcard */
		if ($options["bcard"]) {
			$sql2 .= sprintf(" AND mail_messages.bcard_id = %d ", $options["bcard"]);
		}

	} else {
		/* if we are searching a project */
		if ($options["project"]) {
			//only available projects OR your own emails
			$sq .= sprintf(" AND (project_id = %d) ", $options["project"]);
		}
		/* if we search an relation */
		if ($options["relation"]) {
			$sq .= sprintf(" AND mail_messages.address_id = %d ", $options["relation"]);
		}
		/* if we search a bcard */
		if ($options["bcard"]) {
			$sq .= sprintf(" AND mail_messages.bcard_id = %d ", $options["bcard"]);
		}
		/* if we search a private contact */
		if ($options["private"]) {
			$sq .= sprintf(" AND private_id = %d ", $options["private"]);
		}
		/* if we search for a keyword */
		if ($options["search"]) {
			$sq2 = $this->getEmailBySearchAddSearchQuery($sq2, $options["search"], $use_subquery, $options["search_archive"]);
		}
		/* if we are searching a specific user */
		/* is_public = 0 means public, is_public = 2 means private at the moment */
		if (!$user_data->permissions["xs_usermanage"]) {
			$sq2.= sprintf("AND (folder_id = %d) %s ", $options["folder"], $sq);
		} else {
			$sq2.= sprintf(" AND ((folder_id = %d AND user_id = %d) OR ", $options["folder"], $options["user"]);
			$sq2.= sprintf("(folder_id = %d)) %s ", $options["folder"], $sq);
		}
	}

	$escape = sql_syntax("escape_char");
/*
	$flds[]= $escape."to".$escape;
	$flds[]= $escape."date".$escape;
	$flds[]= "user_id";
	$flds[]= "mail_messages.id";
	$flds[]= "sender_emailaddress";
	$flds[]= "subject";
	$flds[]= "askwichrel";
	$flds[]= "description";
	$flds[]= "is_public";
	$flds[]= "is_new";
	$flds[]= "folder_id";
	$flds[]= "address_id";
	$flds[]= "is_text";
	$flds[]= "mail_messages_data.body";
 */
	$flds[] = "id";
	if ($options["nolimit"] || ($options["search"] && $options["folder_id"]==$archive["id"])) {
		//$buf = sql_syntax("buffer");
	}
	$q = sprintf("select %s %s %s ", $buf, implode(", ", $flds), $sq2);
	if ($sort) {
		$ssq = sql_filter_col($sort);
	} else {
		$ssq = "is_new desc, date desc";
	}
	$q.= " order by ".$ssq;
	$q_count = "select count(*) ".$sq2;

	if ($options["nolimit"]) {
		if ($options["max_hits"]) {
			$res = sql_query($q, "", 0, $options["max_hits"]);
		} else {
			$res = sql_query($q);
		}
	} else {
		$res = sql_query($q, "", $start, $this->pagesize);
	}

	$conversion = new Layout_conversion();

	/* get users table */
	$users = array();
	$q = "select id, username from users";
	$res2 = sql_query($q);
	while ($row2 = sql_fetch_assoc($res2)) {
		$users[$row2["id"]] = $row2["username"];
	}

	while ($row = sql_fetch_assoc($res)) {
		//lets try this:
		$_tmp_data = $this->getEmailById($row["id"]);
		if (!$_tmp_data[0]["is_text"]) {
			$_tmp_data[0]["h_body"] = substr(trim($this->html2text($_tmp_data[0]["body"])), 0, 256);
		} else {
			$_tmp_data[0]["h_body"] = substr(trim($_tmp_data[0]["body"]), 0, 256);
		}
   		$_tmp_data[0]["short_date"]          = $_tmp_data[0]["date"] ? date("d-m-Y", $_tmp_data[0]["date"]) : "-";
		$_tmp_data[0]["short_time"]          = $_tmp_data[0]["date"] ? date("H:i", $_tmp_data[0]["date"]) : "-";
		$_tmp_data[0]["h_description"]   = trim( preg_replace("/((\r)|(\t)|(\n))/s", " ", strip_tags($_tmp_data[0]["description"]) ) );
		$_tmp_data[0]["h_description"]   = preg_replace("/ {1,}/s", " ", $_tmp_data[0]["h_description"]);
		if ($_tmp_data[0]["h_description"]) {
			$_tmp_data[0]["h_description"] = sprintf("%s: %s", gettext("description"), stripslashes($_tmp_data[0]["h_description"]));
		}
		$data[$row["id"]] = $_tmp_data[0];

	}

	$part["data"] =& $data;

	if ($options["nolimit"]) {
		$count = count($part["data"]);
	} else {
		$res = sql_query($q_count);
		$count = sql_result($res,0);
	}

	$part["count"] = $count;
?>
