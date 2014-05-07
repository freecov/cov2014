<?php
/**
 * Covide Groupware-CRM calendar module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
/* check if we are included */
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}

/* a list of modified ids */
$modified_ids = array();

/* prepare the data */
$appointment =& $data;
$insert_groupid = 0;
/* check if we want more then one user in the calendar item */
if ($appointment["user_id"]) {
	$user_arr   = explode(",", $appointment["user_id"]);
	$user_arr[] = $appointment["user"];
	$insert_groupid = 1;
}
if (is_array($user_arr)) {
	foreach ($user_arr as $k=>$v) {
		if ((int)$v <= 0) {
			unset($user_arr[$k]);
		}
	}
} else {
	$user_arr = array($appointment["user"]);
}
$user_arr = array_unique($user_arr);

/* if group, add group members */
if ($appointment["is_group"]) {
	/* get groupmembers */
	$user_data = new User_data();
	$groupinfo = $user_data->getGroupinfo($appointment["is_group"]);
	$members = explode(",", $groupinfo["members"]);
	foreach($members as $member) {
		if (in_array($member, $user_arr)) {
			unset($user_arr[array_search($member, $user_arr)]);
		}
	}
	$extra_users = $user_arr;
	$user_arr = array_merge($user_arr, $members);
	$insert_groupid = 1;
} else {
	$extra_users = $user_arr;
}

foreach ($user_arr as $k=>$v) {
	if (!$v)
		unset($user_arr[$k]);
}

if (is_array($extra_users)) {
	foreach ($extra_users as $k=>$v) {
		if (!$v)
			unset($extra_users[$v]);
	}
}

/* if singleuser appointment is moved to multi user appointment, group_id will be empty */
if ($insert_groupid) {
	if (!(int)$appointment["group_id"]) {
		$ok = false;
		while ($ok === false) {
			$appointment["group_id"] = rand(0, pow(2,30));
			$sql = sprintf("SELECT COUNT(*) FROM calendar WHERE group_id = %d", $appointment["group_id"]);
			$res = sql_query($sql);
			if (sql_result($res, 0) == 0)
				$ok = true;
		}
	}
}

/* get address_id's into an array */
$addresses = explode(",", $appointment["address_id"]);
/* strip empty items */
$addresses = array_unique($addresses);
foreach ($addresses as $k=>$v) {
	if (!$v) {
		unset($addresses[$k]);
	}
}
sort($addresses);
/* put first id we find in address_id. If more are found set multirel to the remaining values */
$appointment["address_id"] = $addresses[0];
if (count($addresses) > 1) {
	unset($addresses[0]);
	$appointment["multirel"] = implode(",", $addresses);
}
/* trim the data */
$appointment["description"] = trim($appointment["description"]);

/* convert newlines */
$conversion = new Layout_conversion();
$appointment["description"] = $conversion->html2txtLines($appointment["description"]);

/* if no subject, generate one */
if (strlen(trim($appointment["subject"])) == 0) {
	$appointment["subject"] = substr(preg_replace("/[^a-z0-9 _-]/si", "", str_replace("\n", " ", $appointment["description"])), 0, 40);
}
$appointment["kilometers"] = preg_replace("/,/", ".", $appointment["kilometers"]);
$appointment["kilometers"] = round($appointment["kilometers"]);
if (!$appointment["kilometers"])   { $appointment["kilometers"] = 0; }
if (!$appointment["multirel"])     { $appointment["multirel"] = 0; }
if (!$appointment["address_id"])   { $appointment["address_id"]=0; }
if ($appointment["app_type"] == 5) { $appointment["is_ill"] = 1; } else { $appointment["is_ill"] = 0; }
if ($appointment["app_type"] == 4) { $appointment["is_specialleave"] = 1; } else { $appointment["is_specialleave"] = 0; }
if ($appointment["app_type"] == 3) { $appointment["is_holiday"] = 1; } else { $appointment["is_holiday"] = 0; }
if ($appointment["app_type"] == 2) { $appointment["is_private"] = 1; } else { $appointment["is_private"] = 0; }
unset($appointment["app_type"]);


/* loop through each day this appointment is for */
foreach ($appointment["from_day"] as $day) {
	$appointment["timestamp_start"] = mktime($appointment["from_hour"], $appointment["from_min"], 0, $appointment["from_month"], $day, $appointment["from_year"]);
	$appointment["timestamp_end"]   = mktime($appointment["to_hour"], $appointment["to_minute"], 0, $appointment["from_month"], $day, $appointment["from_year"]);

	$fields = Array();
	$values = Array();
	$fields[] = "modified_by";      $values[] = sprintf("%d",   $_SESSION["user_id"]);
	$fields[] = "modified";         $values[] = sprintf("%d",   mktime());
	$fields[] = "is_important";     $values[] = sprintf("%d",   $appointment["is_important"]);
	$fields[] = "is_ill";           $values[] = sprintf("%d",   $appointment["is_ill"]);
	$fields[] = "is_specialleave";  $values[] = sprintf("%d",   $appointment["is_specialleave"]);
	$fields[] = "is_holiday";       $values[] = sprintf("%d",   $appointment["is_holiday"]);
	$fields[] = "timestamp_start";  $values[] = sprintf("%d",   $appointment["timestamp_start"]);
	$fields[] = "timestamp_end";    $values[] = sprintf("%d",   $appointment["timestamp_end"]);
	$fields[] = "subject";          $values[] = sprintf("'%s'", $appointment["subject"]);
	$fields[] = "description";      $values[] = sprintf("'%s'", $appointment["description"]);
	$fields[] = "project_id";       $values[] = sprintf("%d",   $appointment["project_id"]);
	$fields[] = "is_private";       $values[] = sprintf("%d",   $appointment["is_private"]);
	$fields[] = "address_id";       $values[] = sprintf("%d",   $appointment["address_id"]);
	$fields[] = "multirel";         $values[] = sprintf("'%s'", $appointment["multirel"]);
	$fields[] = "notifytime";       $values[] = sprintf("%d",   $appointment["notifytime"]);
	$fields[] = "kilometers";       $values[] = sprintf("%d",   $appointment["kilometers"]);
	$fields[] = "is_repeat";        $values[] = sprintf("%d",   $appointment["is_repeat"]);
	if ($appointment["is_repeat"] && !$appointment["repeat_type"]) {
		$appointment["repeat_type"] = "D";
	}
	$fields[] = "repeat_type";      $values[] = sprintf("'%s'", $appointment["repeat_type"]);
	$fields[] = "is_registered";    $values[] = sprintf("%d",   $appointment["is_registered"]);
	$fields[] = "location";         $values[] = sprintf("'%s'", $appointment["location"]);
	$fields[] = "deckm";            $values[] = sprintf("%d",   $appointment["deckm"]);
	$fields[] = "is_dnd";           $values[] = sprintf("%d",   $appointment["is_dnd"]);
	$fields[] = "is_group";         $values[] = sprintf("%d",   $appointment["is_group"]);
	if ($insert_groupid) {
		$fields[] = "group_id";         $values[] = sprintf("%d", $appointment["group_id"]);
	}
	$fields[] = "is_event";           $values[] = sprintf("%d",   $appointment["is_event"]);

	$queries = array();
	if ($appointment["id"]) {
		/* alter appointment {{{ */
		/* check if this user already has this appointment */
		foreach ($user_arr as $user) {

			/* unset previous set user_id value */
			if (in_array("user_id", $fields)) {
				$k = array_search("user_id", $fields);
				unset($fields[$k]);
				unset($values[$k]);
			}
			$fields[] = "user_id"; $values[] = $user;
			/* remove target user from $extra_user array */
			$extra_u = $extra_users;
			unset($extra_u[array_search($user, $extra_u)]);
			if (in_array("extra_users", $fields)) {
				$k = array_search("extra_users", $fields);
				unset($fields[$k]);
				unset($values[$k]);
			}
			$fields[] = "extra_users"; $values[] = "'".implode(",", $extra_u)."'";
			/* if $insert_groupid is set, there are multiple users in this calendar item */
			/* so find the items we need to alter */
			$insert = 0;
			if ($insert_groupid) {
				/* check if the user has this appointment as singleuser appointment */
				$sql = sprintf("SELECT COUNT(*) FROM calendar WHERE id = %d AND user_id = %d", $appointment["id"], $user);
				$res = sql_query($sql);
				$count_user = sql_result($res, 0);
				if (!$count_user) {
					$sql = sprintf("SELECT COUNT(*) FROM calendar WHERE group_id= %d AND user_id = %d", $appointment["group_id"], $user);
					$res = sql_query($sql);
					$count = sql_result($res, 0);
					if (!$count) {
						$insert = 1;
					}
				}
			}
			if ($insert) {
				/* insert this appointment */
				$q  = "INSERT INTO calendar ";
				$q .= "(".implode(",",$fields).") VALUES (".implode(",",$values).")";
			} else {
				/* update this appointment */
				$q = "UPDATE calendar SET ";
				foreach ($fields as $k=>$v) {
					$q .= $fields[$k]." = ".$values[$k].", ";
				}
				$q  = substr($q, 0, strlen($q)-2);
				if ($insert_groupid && !$count_user) {
					$q .= sprintf(" WHERE group_id = %d AND user_id = %d", $appointment["group_id"], $user);

					/* get target ids */
					$q2 = sprintf("select id from calendar where group_id = %d AND user_id = %d", $appointment["group_id"], $user);
					$res2 = sql_query($q2);
					while ($row2 = sql_fetch_assoc($res2))
						$modified_ids[] = $row2["id"];

				} else {
					$q .= sprintf(" WHERE id=%d", $appointment["id"]);
					$modified_ids[] = $appointment["id"];
				}
			}
			$queries[] = $q;
		}
		/* remove items from group members or extra_users that are deselected */
		if ($appointment["group_id"] > 100) {
			$sql = sprintf("SELECT user_id FROM calendar WHERE group_id = %d", $appointment["group_id"]);
			$res = sql_query($sql);
			$orig_users = array();
			while ($row = sql_fetch_assoc($res)) {
				if (!in_array($row["user_id"], $user_arr)) {
					$orig_users[] = $row["user_id"];
				}
			}
		}
		if (count($orig_users)) {
			foreach ($orig_users as $u) {
				$sql = sprintf("SELECT id FROM calendar WHERE user_id = %d AND group_id = %d", $u, $appointment["group_id"]);
				$res2 = sql_query($sql);
				while ($row2 = sql_fetch_assoc($res2)) {
					$modified_ids[] = $row2["id"];
				}
				$sql = sprintf("DELETE FROM calendar WHERE user_id = %d AND group_id = %d", $u, $appointment["group_id"]);
				$res = sql_query($sql);
			}
		}
		/* }}} */
	} else {
		/* making new appointment {{{ */
		foreach ($user_arr as $user) {
			/* remove target user from $extra_user array */
			$extra_u = $extra_users;
			unset($extra_u[array_search($user, $extra_u)]);
			if (in_array("extra_users", $fields)) {
				$k = array_search("extra_users", $fields);
				unset($fields[$k]);
				unset($values[$k]);
			}
			$fields[] = "extra_users"; $values[] = "'".implode(",", $extra_u)."'";
			/* unset previous set user_id value */
			if (in_array("user_id", $fields)) {
				$k = array_search("user_id", $fields);
				unset($fields[$k]);
				unset($values[$k]);
			}
			$fields[] = "user_id"; $values[] = $user;
			$q  = "INSERT INTO calendar ";
			$q .= "(".implode(",",$fields).") VALUES (".implode(",",$values).")";
			$queries[] = $q;
		}
		/* }}} */
	}
	/* apply all queries */
	foreach ($queries as $query) {
		$res = sql_query($query);
		if (preg_match("/^INSERT INTO calendar /si", $query))
			$modified_ids[] = sql_insert_id("calendar");
	}

	/* put todo on done when this was a todo */
	if ($appointment["todoid"]) {
		$sql = sprintf("UPDATE todo SET is_done=1 WHERE id=%d", $appointment["todoid"]);
		$res = sql_query($sql);
	}
}
/* prepare an email */
if (is_array($data["relmail"])) {
	$user_data = new User_data();
	$address_data = new Address_data();
	$address_id  = $user_data->getAddressIdByUserId($_SESSION["user_id"]);
	if($address_id>0)
		$address_info = $address_data->getAddressByID($address_id, "private");
	else
		$address_info["tav"] = $user_data->getUsernameById($_SESSION["user_id"]);
	$mail_subject = gettext("New appointment");
	$mail_body  = gettext("You have made an appointment with")." ".$address_info["tav"]."\n\n";
	$mail_body .= gettext("starttime").": ".date("d-m-Y H:i",$appointment["timestamp_start"])."\n";
	$mail_body .= gettext("endtime").": ".date("d-m-Y H:i", $appointment["timestamp_end"])."\n";
	$mail_body .= gettext("subject").": ".$appointment["subject"]."\n";
	$mail_body .= gettext("description").": ".$appointment["description"];
	$mail_data = new Email_data();
	foreach ($data["relmail"] as $mail_to) {
		$mail_data->sendMail($mail_to, $mail_subject, $mail_body, "", $appointment["user"]);
	}
}
/* do we need to send a note to the user? */
if ($appointment["notify_user"] == 1) {
	$user_data = new User_data();
	$loggedin_user = $user_data->getUsernameById($_SESSION["user_id"]);
	$subject = gettext("New calendar item for you");
	$body  = $loggedin_user." ".gettext("added calendar item").".\n\n";
	$body .= gettext("starttime").": ".date("d-m-Y H:i",$appointment["timestamp_start"])."\n";
	$body .= gettext("endtime").": ".date("d-m-Y H:i", $appointment["timestamp_end"])."\n";
	$body .= gettext("subject").": ".$appointment["subject"]."\n";
	$body .= gettext("description").": ".$appointment["description"]."\n\n";
	$body .= "<a href=\"index.php?mod=calendar&day=".date("d", $appointment["timestamp_start"])."&month=".date("m", $appointment["timestamp_start"])."&year=".date("Y", $appointment["timestamp_start"])."\">".gettext("to calendar")."</a>";
	$note["to"]         = implode(",", $user_arr);
	$note["from"]       = $_SESSION["user_id"];
	$note["body"]       = $body;
	$note["subject"]    = $subject;
	$note["address_id"] = $appointment["address_id"];
	$note["project_id"] = $appointment["project_id"];
	$note_data = new Note_data();
	if (!$note_data->store2db($note)) {
		die("ERROR ! Something went wrong with storing the note. Please file a bugreport.");
	}
}

if ($GLOBALS["covide"]->license["has_funambol"] && !$funambol_input) {
	/* list of modified/create calendar items. lets remove doubles and loop thru for funambol */
	$modified_ids = array_unique($modified_ids);
	foreach ($modified_ids as $mod_id) {
		// find owner of modified id for funambol
		$sql = sprintf("SELECT user_id FROM calendar WHERE id=%d", $mod_id);
		$res = sql_query($sql);
		$mod_user_id = sql_result($res, 0);
		$funambol_data = new Funambol_data((int)$mod_user_id);
		$funambol_data->syncRecord("calendar", "", $mod_id);
		unset($funambol_data);
	}
}
if (!$funambol_input) {
	?>
	<script language="javascript1.2" type="text/javascript">
		if (opener) {
			if (opener.document.getElementById('search')) {
				opener.document.getElementById('search').submit();
			} else {
				opener.location.reload();
			}
			window.close();
		}
	</script>
<? } ?>
