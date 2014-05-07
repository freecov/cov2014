<?php
/**
 * Covide Groupware-CRM Notes data class
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
Class Note_data {

	/* variables */

	/* methods */

	/* getNotes {{{ */
	/**
	 * Generate an array with note data based on options array
	 *
	 * @return Array note as it is in the db
	 */
	public function getNotes($options = "") {
		/* we gonna use regex here */
		$regex_syntax = sql_syntax("regex");
		$like_syntax  = sql_syntax("like");
		/* same with users */
		$user      = new User_data();
		$users     = $user->getUserList(1);
		if ($_SESSION["user_id"])
			$user->getUserPermissionsById($_SESSION["user_id"]);

		/* search */
		if ($options["zoekstring"]) {
			$q = "AND (subject $like_syntax '%".sprintf("%s", $options["zoekstring"])."%' OR body $like_syntax '%".sprintf("%s", $options["zoekstring"])."%')";
			if (!$options["user_id"])
				$options["user_id"] = "all";
		}


		if (!$options["user_id"] || !($user->checkPermission("xs_usermanage") || $user->checkPermission("xs_notemanage"))) {
			$options["user_id"] = $_SESSION["user_id"];
		} elseif ($options["user_id"] != "all") {
			/* some items may have G in their value. get rid of them and replace with current groupmembers */
			$userarr = explode(",", $options["user_id"]);
			foreach ($userarr as $k=>$v) {
				if (strpos($v, "G") !== false) {
					unset($userarr[$k]);
					$groupid = substr($v, 1);
					$groupinfo = $user->getGroupInfo($groupid);
					$members = explode(",", $groupinfo["members"]);
					$userarr = array_merge($userarr, $members);
				}
			}
			$userarr = array_unique($userarr);
			$options["user_id"] = implode(",", $userarr);
		}
		/* no user filter */
		if ($options["user_id"] == "all") {
			$q_userid = "1 = 1";
		} else {
			if(preg_replace("/^,/s", " ", $options["user_id"]))
				$options["user_id"] = str_replace(",","",$options["user_id"]);
			if ($options["search"])
				$q_userid = sprintf("(notes.user_id IN (%1\$s) or sender IN (%1\$s))", $options["user_id"]);
			else
				$q_userid = sprintf("notes.user_id IN (%s)", $options["user_id"]);
		}
		/* address filter */
		if ($options["address_id"]) {
			$q .= sprintf(" AND notes.address_id=%d", $options["address_id"]);
		}
		/* project filter */
		if ($options["project_id"]) {
			$q .= sprintf(" AND project_id = %d", $options["project_id"]);
		}
		/* customer contact filter */
		if ($options["custcont"])
			$q .= " AND is_support = 1";
		if ($options["nocustcont"])
			$q .= " AND (is_support = 0 OR is_support IS NULL)";

		/* date filter */
		if ($options["timestamp_start"])
			$q .= sprintf(" AND timestamp >= %d", $options["timestamp_start"]);
		if ($options["timestamp_end"])
			$q .= sprintf(" AND timestamp <= %d", $options["timestamp_end"]);

		/* sort module */
		if (!$options["sort"]) {
			$order = "is_read,timestamp DESC";
		} else {
			$order = sql_filter_col($options["sort"]);
		}

		$join = "LEFT JOIN address ON address.id = notes.address_id LEFT JOIN users ON users.id = notes.sender";
		$fields = "notes.*, address.companyname as address_name, users.username as user_name";
		switch ($options["note_type"]) {
			case "old"     :
				$query = "SELECT $fields FROM notes $join WHERE $q_userid AND is_done=1 $q ORDER BY $order";
				$query_count = "SELECT COUNT(*) FROM notes WHERE $q_userid AND is_done=1 $q";
				$showasold = 1;
				break;
			case "sent"    :
				$query = "SELECT $fields FROM notes $join where is_read = 1 AND sender=".(int)$options["user_id"]." AND delstatus!=1 $q ORDER BY $order";
				$query_count = "SELECT COUNT(*) FROM notes where is_read = 1 AND sender=".(int)$options["user_id"]." AND delstatus!=1 $q";
				$showasold = 1;
				break;
			case "show"    :
				$query = "SELECT $fields FROM notes $join where (is_read=0 OR is_read IS NULL) AND sender=".(int)$options["user_id"]." AND delstatus!=1 AND is_draft = 0 $q ORDER BY $order";
				$query_count = "SELECT COUNT(*) FROM notes where (is_read=0 OR is_read IS NULL) AND sender=".(int)$options["user_id"]." AND delstatus!=1 AND is_draft = 0 $q";
				$showasold = 1;
				break;
			case "all" :
				$query = "SELECT $fields FROM notes $join where $q_userid $q ORDER BY $order";
				$query_count = "SELECT COUNT(*) FROM notes where $q_userid $q";
				break;
			case "drafts" :
				$query = sprintf("SELECT $fields FROM notes $join where sender = %d AND is_draft = 1 $q ORDER BY $order", $_SESSION["user_id"]);
				$query_count = sprintf("SELECT COUNT(*) FROM notes where sender = %d AND is_draft = 1 $q", $_SESSION["user_id"]);
				break;
			case "current" :
			default        :
				$query = "SELECT $fields FROM notes $join WHERE $q_userid AND (is_done IS NULL OR is_done = 0) AND delstatus!=2 AND is_draft = 0 $q ORDER BY $order";
				$query_count = "SELECT COUNT(*) FROM notes WHERE $q_userid AND (is_done IS NULL OR is_done = 0) AND delstatus!=2 AND is_draft = 0 $q";
				break;
		}
		$res_count = sql_query($query_count);
		$total = sql_result($res_count, 0);
		$data = Array();
		$data["total_count"] = $total;
		if ($options["nolimit"] == 1) {
			$result = sql_query($query);
		} else {
			$result = sql_query($query, "", (int)$options["top"], $options["limit"]);
		}
		$new = 0;
		while ($row = sql_fetch_assoc($result)) {
			if (!trim($row["subject"])) { $row["subject"] = gettext("no subject"); }
			$row["relation_name"] = $row["address_name"];
			$row["from_name"]     = $users[$row["sender"]];
			$row["to_name"]       = $users[$row["user_id"]];
			$extra_ont = explode(",", $row["extra_recipients"]);
			$extra_names = array();
			foreach ($extra_ont as $k=>$v) {
				$extra_names[] = $users[$v];
			}
			$extra_names = implode(", ", $extra_names);
			$row["extra_names"] = $extra_names;
			$row["human_date"]  = date("d-m-Y H:i:s", $row["timestamp"]);
			if (!$showasold) {
				if ($row["is_read"]) {
					$nieuw = 0;
				} else {
					$nieuw = 1;
				}
			} else {
				$nieuw = 0;
			}
			/* to draft stuff */
			if ($row["is_draft"] == 0) {
				$row["no_draft"] = 1;
			}
			
			$row["nieuw"] = $nieuw;
			$data["notes"][]    = $row;
			if ($row["is_read"] == 0) {
				$new++;
			}
		}
		$data["new_count"] = $new;
		return $data;
	}
	/* }}} */
	/* flagRead {{{ */
	/**
	 * update the note so it's flagged as read
	 *
	 * @return boolean true
	 */
	public function flagRead($id) {
		$regex_syntax = sql_syntax("regex");
		if ($id) {
			/* only allow flagging of messages owned by logged in user */
			$q_userid = sprintf("user_id = %d", $_SESSION["user_id"]);
			$sql = sprintf("UPDATE notes SET is_read=1 WHERE id=%d AND $q_userid", $id);
			$res = sql_query($sql);
		}
		return true;
	}
	/* }}} */
	/* flagdone {{{ */
	/**
	 * update the note so it's flagged as done
	 */
	public function flagdone($id) {
		$regex_syntax = sql_syntax("regex");
		$q_userid = sprintf("user_id = %d", $_SESSION["user_id"]);
		$output = new Layout_output();
		if ($id) {
			/* get current state */
			$sql = sprintf("SELECT is_done FROM notes WHERE id=%d", $id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_done"]) {
				$is_done = 0;

				$image = $output->replaceImage("f_oud.gif");
				$alt = gettext("no");
			} else {
				$is_done = 1;
				$image = $output->replaceImage("f_nieuw.gif");
				$alt = gettext("yes");
			}
			unset ($output);
			$sql = sprintf("UPDATE notes SET is_done = %d WHERE id = %d AND $q_userid", $is_done, $id);
			$res = sql_query($sql);
		}
		echo "document.getElementById('flag_done_$id').src='$image';";
		echo "document.getElementById('flag_done_$id').alt='$alt';";
		echo "document.getElementById('flag_done_$id').title='$alt';";
		echo "history_goback();";
	}
	/* }}} */
	/* getNoteById {{{ */
	/**
	 * Generate an array with note data based on id
	 *
	 * @return Array note as it is in the db
	 */
	public function getNoteById($id) {
		if (!$id)
			return false;

		$address              = new Address_data();
		$user                 = new User_data();
		$users                = $user->getUserList();
		$project              = new Project_data();
		$query                = sprintf("SELECT * FROM notes WHERE id=%d", $id);
		$res                  = sql_query($query);
		$row                  = sql_fetch_assoc($res);
		$row["relation_name"] = $address->getAddressNameById($row["address_id"]);
		$row["from_name"]     = $users[$row["sender"]];
		$row["to_name"]       = $users[$row["user_id"]];
		$projectinfo          = $project->getProjectById($row["project_id"]);
		$row["project_name"]  = $projectinfo[0]["name"];
		$extra_ont = explode(",", $row["extra_recipients"]);
		$extra_names = array();
		foreach ($extra_ont as $k=>$v) {
			$extra_names[] = $users[$v];
		}
		$extra_names = implode(", ", $extra_names);
		$row["extra_names"] = $extra_names;
		$row["human_date"]  = date("d-m-Y H:i:s", $row["timestamp"]);
		if ($row["is_read"]) {
			$nieuw = 0;
		} else {
			$nieuw = 1;
		}
		$row["nieuw"] = $nieuw;

		return $row;
	}
	/* }}} */
	/* getNotesByContact {{{ */
	/**
	 * Generate an array with notes data based on contact id
	 *
	 * @return Array notes as they are in the db
	 */
	public function getNotesByContact($id, $active=1, $custcontact = 0) {
		$user                 = new User_data();
		$users_active         = $user->getUserList(1, "", 1);
		$users_nonactive      = $user->getUserList(0);

		if (is_array($users_active) && is_array($users_nonactive)) {
			$users = $users_active+$users_nonactive;
		} else {
			$users = array(0 => gettext("none"));
		}

		if (!$custcontact) {
			$is_support_neg = 1;
		} else {
			$is_support_neg = 0;
		}
		if ($active) {
			$sql = sprintf("SELECT * FROM notes WHERE (address_id=%d AND (is_done is null OR is_done = 0) and is_support!=%d) ORDER BY timestamp DESC", $id, $is_support_neg);
		} else {
			$sql = sprintf("SELECT * FROM notes WHERE (address_id=%d AND is_done=1 and is_support!=%d) ORDER BY timestamp DESC", $id, $is_support_neg);
		}
		$res = sql_query($sql);
		$notes = array();
		while ($row = sql_fetch_assoc($res)) {
			if (!trim($row["subject"])) { $row["subject"] = gettext("no subject"); }

			/* gen the hash */
			$hash = md5($row["sender"].$row["timestamp"].$row["message"].$row["subject"]);
			if ($hash != $tmphash) {
				$row["from_name"]     = $users[$row["sender"]];
				$row["to_name"]       = $users[$row["user_id"]];
				$extra_ont = explode(",", $row["extra_recipients"]);
				$extra_names = array();
				foreach ($extra_ont as $k=>$v) {
					$extra_names[] = $users[$v];
				}
				$extra_names = implode(", ", $extra_names);
				$row["extra_names"] = $extra_names;
				if ($extra_names != $row["to_name"]) {
					if (strlen($extra_names)) {
						$row["to_name"].= ",".$extra_names;
					}
				}
				$row["human_date"]  = date("d-m-Y H:i:s", $row["timestamp"]);
				if ($row["is_read"]) {
					$nieuw = 0;
				} else {
					$nieuw = 1;
				}
				$row["nieuw"] = $nieuw;
				$notes[] = $row;
			}
			/* hash some fields */
			$tmphash = $hash;
		}
		return $notes;
	}
	/* }}} */
	/* getNotecountByUserId {{{ */
	/**
	 * Generate an array with active and new note count
	 *
	 * @return Array "new"=>unread count, "active"=>read but not done, "total"=>total count
	 */
	public function getNotecountByUserId($user) {
		$regex_syntax = sql_syntax("regex");
		$q_userid = sprintf("user_id = %d", $user);
		$sql_new    = "SELECT COUNT(*) FROM notes WHERE $q_userid AND is_read=0 AND is_draft = 0";
		$sql_active = "SELECT COUNT(*) FROM notes WHERE $q_userid AND is_draft = 0 AND (is_done is null OR is_done=0)";
		$sql_total  = "SELECT COUNT(*) FROM notes WHERE $q_userid";
		$res = sql_query($sql_new);
		$return["new"] = sql_result($res,0);
		$res = sql_query($sql_active);
		$return["active"] = sql_result($res,0);
		$res = sql_query($sql_total);
		$return["total"] = sql_result($res,0);
		return $return;
	}
	/* }}} */
	/* store2db {{{ */
	/**
	 * put array data in database as note
	 *
	 * @return bool true on success, false on error
	 */
	public function store2db($note) {
		$user_data = new User_data();
		$users = explode(",",$note["to"]);
		$archiveuser = $user_data->getArchiveUserId();

		$user_rcpt = array();
		foreach ($users as $k=>$v) {
			if (preg_match("/^G\d{1,}/s", $v)) {
				$group = $user_data->getGroupInfo((int)preg_replace("/^G/s", "", $v));
				$members = explode(",", $group["members"]);
				foreach ($members as $z) {
					$user_rcpt[]=$z;
				}
			} else {
				$user_rcpt[]=$v;
			}
		}
		$users = array_unique($user_rcpt);
		unset($user_rcpt);

		foreach ($users as $k=>$v) {
			if (!$v) {
				unset($users[$k]);
			}
		}
		/* Recalculate index values */
		sort($users);
		/* trim the data */
		$note["body"] = trim($note["body"]);

		/* convert newlines */
		$conversion = new Layout_conversion();
		$note["body"] = $conversion->html2txtLines($note["body"]);

		$user_data = new User_data();
		if (!$note["id"]) {
			$users_copy = $users;
			/* It's a draft, so only save once */
			if ($note["is_draft"]) {
			/* If the AFP field is submitted, put some standard values in the note */
					$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
					if($note["afp"]) {
						if($note["subject"]) { $note["subject"] = ": ".$note["subject"]; }
						$note["subject"] = $user_info["username"]." ".gettext("asks permission from your agenda").$note["subject"];
						$note["body"] .= "<P><a href=\"index.php?mod=calendar&action=permissionintro\">".gettext("Go to permission control")."</a>";
					}
					$extra = $users;
					unset($extra[array_search($rcpt, $extra)]);
					/* notes to archiveuser should be posted read and done */
					if ($rcpt == $archiveuser) {
						$done = 1;
						$read = 1;
					} else {
						$done = 0;
						$read = 0;
					}
					$sql  = "INSERT INTO notes (is_support, timestamp, extra_recipients, subject, body, sender, is_read, is_done, address_id, project_id, campaign_id, user_id, is_draft) VALUES (";
					$sql .= sprintf("%d, %d, '%s', '%s', '%s'", $note["is_support"], mktime(), implode(",", $extra), $note["subject"], $note["body"]);
					$sql .= sprintf(",%d, %d, %d, %d, %d, %d, %d, %d", $note["from"], $read, $done, $note["address_id"], $note["project_id"], $note["campaign_id"], $users[0], $note["is_draft"]);
					$sql .= ")";
					$res = sql_query($sql);
					if ($rcpt == $_SESSION["user_id"])
						$newnote = sql_insert_id("notes");
					unset ($sql);
			} else {
			/* It's not a draft, it's being sent, do it for all users */
				foreach ($users as $rcpt) {
					if ($note["sms"] && !$note["is_draft"]) {
						$body = $note["subject"]."\n".$note["body"];
						$voip_data = new Voip_data();
						$voip_data->sendSMS($rcpt, $body);
					}

					/* If the AFP field is submitted, put some standard values in the note */
					$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
					if($note["afp"]) {
						if($note["subject"]) { $note["subject"] = ": ".$note["subject"]; }
						$note["subject"] = $user_info["username"]." ".gettext("asks permission from your agenda").$note["subject"];
						$note["body"] .= "<P><a href=\"index.php?mod=calendar&action=permissionintro\">".gettext("Go to permission control")."</a>";
					}

					if ($note["to_mobile"] && !$note["is_draft"]) {
						$user_info = $user_data->getUserDetailsById($rcpt);
						if ($user_info["xs_funambol"]) {
							/* prepare email */
							$body  = gettext("-- Covide note --")."\n\n";
							$body .= sprintf("%s: %s\n", gettext("subject"), $note["subject"]);
							$body .= sprintf("%s: %s\n", gettext("date and time"), date("d-m-Y H:i"));
							$body .= sprintf("%s: %s\n", gettext("from"), $user_data->getUserNameById($note["from"]));
							$rcpt = array();
							foreach ($users_copy as $u) {
								$rcpt[] = $user_data->getUserNameById($u);
							}
							$body .= sprintf("%s: %s\n", gettext("recipients"), implode(", ", $rcpt));
							if ($note["is_support"])
								$body .= sprintf("%s: %s\n", gettext("customer contact"), gettext("yes"));

							if ($note["address_id"]) {
								$address_data = new Address_data();
								$body .= sprintf("%s: %s\n", gettext("relation"),
									$address_data->getAddressNameById($note["address_id"]));
							}
							if ($note["project_id"]) {
								$project_data = new Project_data();
								$body .= sprintf("%s: %s\n", gettext("relation"),
									$project_data->getProjectNameById($note["project_id"]));
							}
							$body.= "\n\n";


							$body.= strip_tags(preg_replace("/<br[^>]*?>/s", "\n", $note["body"]));

							$rcpt_email = $user_info["mail_email"];
							$user_info2 = $user_data->getUserDetailsById($note["from"]);
							$from_email = $user_info2["mail_email"];

							$headers = "From: $from_email\n";
							$headers.= "MIME-Version: 1.0\n";
							$headers.= "Content-Type: text/plain; charset=utf-8\n";
							$headers.= "Content-Transfer-Encoding: 8bit";

							// Send
							mail($rcpt_email, $note["subject"], $body, $headers, "-f".$from_email);
						}
					}
					$extra = $users;
					unset($extra[array_search($rcpt, $extra)]);
					/* notes to archiveuser should be posted read and done */
					if ($rcpt == $archiveuser) {
						$done = 1;
						$read = 1;
					} else {
						$done = 0;
						$read = 0;
					}
					$sql  = "INSERT INTO notes (is_support, timestamp, extra_recipients, subject, body, sender, is_read, is_done, address_id, project_id, campaign_id, user_id, is_draft) VALUES (";
					$sql .= sprintf("%d, %d, '%s', '%s', '%s'", $note["is_support"], mktime(), implode(",", $extra), $note["subject"], $note["body"]);
					$sql .= sprintf(",%d, %d, %d, %d, %d, %d, %d, %d", $note["from"], $read, $done, $note["address_id"], $note["project_id"], $note["campaign_id"], $rcpt, $note["is_draft"]);
					$sql .= ")";
					$res = sql_query($sql);
					if ($rcpt == $_SESSION["user_id"])
						$newnote = sql_insert_id("notes");
					unset ($sql);
				}
			}
			if (!$newnote && ($note["is_todo"] || $note["is_sales"])) {
				$sql  = "INSERT INTO notes (is_support, timestamp, extra_recipients, subject, body, sender, is_read, address_id, project_id, campaign_id, user_id) VALUES (";
				$sql .= sprintf("%d, %d, '%s', '%s', '%s'", $note["is_support"], mktime(), implode(",", $users), $note["subject"], $note["body"]);
				$sql .= sprintf(",%d, %d, %d, %d, %d, %d", $note["from"], 1, $note["address_id"], $note["project_id"], $note["project_id"], $_SESSION["user_id"]);
				$sql .= ")";
				$res = sql_query($sql);
				$newnote = sql_insert_id("notes");
			}
		} else {
			$sql  = "UPDATE notes SET ";
			$sql .= sprintf("is_support=%d, timestamp=%d, extra_recipients='%s'", $note["is_support"], mktime(), implode(",", $users));
			$sql .= sprintf(", subject='%s', body='%s', sender=%d", $note["subject"], $note["body"], $note["from"]);
			$sql .= sprintf(", is_read=%d, address_id=%d, project_id=%d", $note["is_read"], $note["address_id"], $note["project_id"]);
			$sql .= sprintf(", user_id=%d, is_draft=%d WHERE id=%d", $users[0], $note["is_draft"], $note["id"]);
			$res = sql_query($sql);
			$newnote = $note["id"];
			unset($sql);
			
			/* If extra users, insert new rows, cause were sending and we want a single draft to become full individual rows */
			/* the n is for disabling the first user in the array to get an insert. This user already has a row (the one we updated) */
			$n = 0;
			foreach ($users as $user) {
				if ($n != 0) {
					$sql  = "INSERT INTO notes (is_support, timestamp, extra_recipients, subject, body, sender, is_read, is_done, address_id, project_id, campaign_id, user_id, is_draft) VALUES (";
					$sql .= sprintf("%d, %d, '%s', '%s', '%s'", $note["is_support"], mktime(), implode(",", $users), $note["subject"], $note["body"]);
					$sql .= sprintf(",%d, %d, %d, %d, %d, %d, %d, %d", $note["from"], $read, $done, $note["address_id"], $note["project_id"], $note["campaign_id"], $user, $note["is_draft"]);
					$sql .= ")";
					$res = sql_query($sql);
				}
				$n++;
				
			}
		}
		if ($note["oldid"]) {
			$sql = sprintf("UPDATE notes SET is_read=1, is_done=1 WHERE id=%d", $note["oldid"]);
			$res = sql_query($sql);
		}
		if ($note["calendar_id"]) {
			/* get id of just inserted note */
			$note_id = sql_insert_id("notes");
			$query = sprintf("UPDATE calendar SET note_id = %d WHERE id = %d", $note_id, $note["calendar_id"]);
			$result = sql_query($query);
		}
		if ($note["is_todo"] || $note["is_sales"]) {
			if ($note["is_todo"] && $note["is_sales"]) {
				$todo_output = new Todo_output();
				$todo_output->edit_todo($newnote, 2);
			} elseif ($note["is_todo"]) {
				$todo_output = new Todo_output();
				$todo_output->edit_todo($newnote, 1);
			} elseif ($note["is_sales"]) {
				$sales_output = new Sales_output();
				$sales_output->salesEdit(array("note_id" => $newnote, "noiface" => 1));
			}
			return true;
		}
		if ($note["support_id"]) {
			/* if support id is specified */
			$support_data = new Support_data(0);
			$support_data->remove_ext_item($note["support_id"], 0, 1);
		}
		/* if campaign_id set the data */
		if ($note["campaign_id"]) {
			$note_id = sql_insert_id("notes");
			$sql = sprintf("UPDATE campaign_records SET is_called=1, answer=8, note_id=%d, user_id=%d WHERE id=%d", $note_id, $_SESSION["user_id"], $note["campaign_id"]);
			$res = sql_query($sql);
			$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						opener.document.getElementById('options8').checked = true;
						opener.document.getElementById('velden').submit();
						window.close();
					");
				$output->end_javascript();
			$output->exit_buffer();
		}
		
		if ($note["calendar_id"] || $note["support_id"]) {
			$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						opener.location.href = opener.location.href;
						window.close();
					");
				$output->end_javascript();
			$output->exit_buffer();
			return false;
		} else {
			return true;
		}
	}
	/* }}} */
	/* storeaddressid {{{ */
	/**
	 * Update address_id field of a note
	 *
	 * This function is ment to be called by an AJAX call.
	 * It will echo 'reload_page();' so make sure that javascript
	 * function is defined on the page you call it.
	 *
	 * @param int $noteid The note to alter the address_id
	 * @param int $address_id The new address_id
	 */
	public function storeaddressid($noteid, $address_id) {
		$sql = sprintf("UPDATE notes SET address_id = %d WHERE id = %d", $address_id, $noteid);
		$res = sql_query($sql);
		echo "reload_page();";
	}
	/* }}} */
	/* storeprojectid {{{ */
	/**
	 * Update project_id field of a note
	 *
	 * This function is ment to be called by an AJAX call.
	 * It will echo 'reload_page();' so make sure that javascript
	 * function is defined on the page you call it.
	 *
	 * @param int $noteid The note to alter the project_id
	 * @param int $project_id The new project_id
	 */
	public function storeprojectid($noteid, $project_id) {
		$sql = sprintf("UPDATE notes SET project_id = %d WHERE id = %d", $project_id, $noteid);
		$res = sql_query($sql);
		echo "reload_page();";
	}
	/* }}} */

	/* searchAll {{{ */
	/**
	 * Search in notes according to options specified.
	 *
	 * The options array can hold:
	 * array(
	 *   "searchkey" => string to search for,
	 *   "private"   => if set, only show my own notes,
	 *   "address_id" => if set, filter by this address id,
	 * )
	 *
	 * @param array $options The search options(see function description for possible array elements)
	 * @return array The matching notes with all their info
	 */
	public function searchAll($options) {
		$search  = $options["searchkey"];
		if ($options["private"])
			$private = $options["private"];
		else
			$private = 0;
		if ($options["address_id"])
			$address_id = $options["address_id"];
		else
			$address_id = false;

		$uid = $_SESSION["user_id"];
		$like = sql_syntax("like");
		$buf  = sql_syntax("buffer");

		$user      = new User_data();
		$users     = $user->getUserList(1);

		$address   = new Address_data();
		$relations = $address->getRelationsArray();


		$q = "select $buf * from notes where (sender = $uid or user_id = $uid) ";
		if ($address_id)
			$q .= sprintf(" and address_id IN (%s)", $address_id);
		if ($options["date"]) {
				$q .= sprintf(" AND timestamp BETWEEN %d AND %d", $options["date"]["start"], $options["date"]["end"]);
		}
		$q.= sprintf(" and (subject %s '%%%s%%' ", $like, $search);
		$q.= sprintf(" or body %s '%%%s%%') order by timestamp desc", $like, $search);
		$res = sql_query($q);

		$data = array();
		while ($row = sql_fetch_assoc($res)) {
			if (!trim($row["subject"])) { $row["subject"] = gettext("no subject"); }
			$row["relation_name"] = $relations[$row["address_id"]];
			$row["from_name"]     = $users[$row["sender"]];
			$row["to_name"]       = $users[$row["user_id"]];
			$extra_ont = explode(",", $row["extra_recipients"]);
			$extra_names = array();
			foreach ($extra_ont as $k=>$v) {
				$extra_names[] = $users[$v];
			}
			$extra_names = implode(", ", $extra_names);
			$row["extra_names"] = $extra_names;
			$row["human_date"]  = date("d-m-Y H:i:s", $row["timestamp"]);
			if (!$showasold) {
				if ($row["is_read"]) {
					$nieuw = 0;
				} else {
					$nieuw = 1;
				}
			} else {
				$nieuw = 0;
			}
			$row["nieuw"] = $nieuw;
			$data[$row["id"]]    = $row;
		}
		return $data;
	}
}
?>
