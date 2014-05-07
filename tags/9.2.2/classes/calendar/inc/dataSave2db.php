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

/**
 * There are 4 tables involved in the calendar.
 * calendar, calendar_user, calendar_repeat, calendar_repeat_exception.
 *
 * calendar:
 * id => appointment id
 * timestamp_start => unix timestamp of start of the appointment
 * timestamp_end => unix timestamp of the end of the appointment
 * alldayevent => 1 if this is all-day event, 0 otherwise
 * subject => short summary of appointment
 * body => large description of appointment
 * location => where the appointment takes place
 * kilometers => how far is the appointment location
 * reminderset => 1 if we want to get a reminder
 * reminderminutesbeforestart => when do we want the reminder
 * busystatus
 * importance => 0 = low, 1 = normal, 2 = high
 * address_id => main contact linked to this appointment
 * multirel => additional contacts linked to this appointment
 * project_id => main project linked to this appointment
 * is_private => 1 if this is a private appointment
 * isrecurring => 1 if we have recurrence. see calendar_repeat
 * modified_by => userid of the person who last modified this item
 * modified => unix timestamp when this item was last modified
 *
 * calendar_user:
 * calendar_id => id from calendar table
 * user_id => id from user table
 * status => 1 for accepted, 2 for rejected, 3 for pending invite, 4 for registered
 */
/* check if we are included */
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}

/* a list of modified ids */
$modified_ids = array();
/* prepare the data */
$appointment =& $data;
//print_r($appointment); die();
/* check if we want more then one user in the calendar item */
if ($appointment["user_id"]) {
	$user_arr   = explode(",", $appointment["user_id"]);
	$user_arr[] = $appointment["user"];
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
if (is_array($addresses) && count($addresses) > 1) {
	unset($addresses[0]);
	$appointment["multirel"] = implode(",", $addresses);
}

/* get privates_id's into an array */
$privates = explode(",", $appointment["private_id"]);
/* strip empty items */
$privates = array_unique($privates);
foreach ($privates as $k=>$v) {
	if (!$v) {
		unset($privates[$k]);
	}
}
sort($privates);
/* put first id we find in address_id. If more are found set multirel to the remaining values */
$appointment["private_id"] = $privates[0];
if (is_array($privates) && count($privates) > 1) {
	unset($privates[0]);
	$appointment["multiprivate"] = implode(",", $privates);
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
if (!$appointment["multiprivate"]) { $appointment["multiprivate"] = 0; }
if (!$appointment["private_id"])   { $appointment["private_id"]=0; }
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
	$fields[] = "modified";         $values[] = sprintf("%d",   time());
	$fields[] = "importance";       $values[] = sprintf("%d",   $appointment["importance"]);

	$fields[] = "is_private";       $values[] = sprintf("%d",   $appointment["is_private"]);
	$fields[] = "is_ill";           $values[] = sprintf("%d",   $appointment["is_ill"]);
	$fields[] = "is_specialleave";  $values[] = sprintf("%d",   $appointment["is_specialleave"]);
	$fields[] = "is_holiday";       $values[] = sprintf("%d",   $appointment["is_holiday"]);

	$fields[] = "timestamp_start";  $values[] = sprintf("%d",   $appointment["timestamp_start"]);
	$fields[] = "timestamp_end";    $values[] = sprintf("%d",   $appointment["timestamp_end"]);
	$fields[] = "subject";          $values[] = sprintf("'%s'", $appointment["subject"]);
	$fields[] = "body";             $values[] = sprintf("'%s'", $appointment["description"]);
	$fields[] = "private_id";       $values[] = sprintf("%d",   $appointment["private_id"]);
	$fields[] = "multiprivate";     $values[] = sprintf("'%s'", $appointment["multiprivate"]);
	$fields[] = "address_id";       $values[] = sprintf("%d",   $appointment["address_id"]);
	$fields[] = "multirel";         $values[] = sprintf("'%s'", $appointment["multirel"]);
	$fields[] = "location";         $values[] = sprintf("'%s'", $appointment["location"]);
	$fields[] = "project_id";       $values[] = sprintf("%d",   $appointment["project_id"]);
	$fields[] = "kilometers";       $values[] = sprintf("%d",   $appointment["kilometers"]);
	$fields[] = "dimdim_meeting";   $values[] = sprintf("%d",   $appointment["dimdim_meeting_id"]);

	if ($appointment["notifytime"] > 0) {
		$fields[] = "reminderset";                      $values[] = 1;
		$fields[] = "reminderminutesbeforestart";       $values[] = sprintf("%d",   $appointment["notifytime"]/60);
	} else {
		$fields[] = "reminderset";                      $values[] = 0;
		$fields[] = "reminderminutesbeforestart";       $values[] = 0;
	}
	$fields[] = "alldayevent";      $values[] = sprintf("%d",   $appointment["is_event"]);
	$fields[] = "is_dnd";           $values[] = sprintf("%d",   $appointment["is_dnd"]);

	$fields[] = "deckm";            $values[] = sprintf("%d",   $appointment["deckm"]);
/*
	if ($appointment["is_group"]) {
		$fields[] = "is_registered";    $values[] = 0;
	} else {
		$fields[] = "is_registered";    $values[] = sprintf("%d",   $appointment["is_registered"]);
	}
	$fields[] = "is_group";         $values[] = sprintf("%d",   $appointment["is_group"]);
*/

	//repeat options
	if ($appointment["is_repeat"] && $appointment["repeat_type"]) {
		$fields[] = "isrecurring"; $values[] = sprintf("%d", $appointment["is_repeat"]);
		// check if endtime should be used
		if ($appointment["repeat_use_end_date"]) {
			//if something is left out, use current date
			if (!$appointment["repeat_end_day"])   { $repeat_end_day = date("d");   } else { $repeat_end_day = $appointment["repeat_end_day"]; }
			if (!$appointment["repeat_end_month"]) { $repeat_end_month = date("m"); } else { $repeat_end_month = $appointment["repeat_end_month"]; }
			if (!$appointment["repeat_end_year"])  { $repeat_end_year = date("Y");  } else { $repeat_end_year = $appointment["repeat_end_year"]; }
			$appointment_repeat["repeat_end"] = mktime(23, 59, 59, $repeat_end_month, $repeat_end_day, $repeat_end_year);
		}
		$appointment_repeat["repeat_type"] = $appointment["repeat_type"];
		if ($appointment_repeat["repeat_type"] == "2") {
			$dow = date("w", $appointment["timestamp_start"]);
			for ($d = 0; $d <= 6; $d++) {
				if ($d == $dow) {
					$daymask[$d] = "y";
				} else {
					$daymask[$d] = "n";
				}
			}
			$appointment_repeat["repeat_days"] = implode("", $daymask);
		} else if ($appointment_repeat["repeat_type"] == "7") {
			$appointment_repeat["repeat_days"] = "nyyyyyn";
		} else {
			$appointment_repeat["repeat_days"] = "yyyyyyy";
		}
	} else {
		$appointment["is_repeat"] = 0;
		$appointment["repeat_type"] = 0;
		$fields[] = "isrecurring";
		$values[] = 0;
	}
	$queries = array();
	if ($appointment["id"]) {
		/* alter appointment {{{ */
		/* check if this user already has this appointment */
		foreach ($user_arr as $user) {
			//check if this appointment is already linked to this user.
			$sql = sprintf("SELECT COUNT(*) FROM calendar_user WHERE calendar_id = %d AND user_id = %d", $appointment["id"], $user);
			$res = sql_query($sql);
			$count_user = sql_result($res, 0);
			//if not, add it
			if (!$count_user) {
				if ($appointment["is_registered"]) {
					$status = 4;
				} else {
					$status = 1;
				}
				//create in google if needed
				$_user_data = new User_data();
				$_user_info = $_user_data->getUserDetailsById($user);
				if ($_user_info["google_username"] && $_user_info["google_password"]) {
					$google_data = new Google_data();
					$google_id = $google_data->createGoogleCalendarItem($_user_info["google_username"], $_user_info["google_password"], $appointment);
				} else {
					$google_id = 0;
				}
				$sql = sprintf("INSERT INTO calendar_user (calendar_id, user_id, status, google_id) VALUES (%d, %d, %d, '%s')", $appointment["id"], $user, $status, $google_id);
				$res = sql_query($sql);
			}
		}
		//update calendar info
		$q = "UPDATE calendar SET ";
		foreach ($fields as $k=>$v)
			$q .= $fields[$k]." = ".$values[$k].", ";

		$q  = substr($q, 0, strlen($q)-2);
		$q .= sprintf(" WHERE id=%d", $appointment["id"]);

		$res = sql_query($q);

		$modified_ids[] = $appointment["id"];
		// grab the users that have this appointment so we can figure out if we have to remove them from google
		$sql = sprintf("SELECT user_id, google_id FROM calendar_user WHERE calendar_id = %d", $appointment["id"]);
		$res = sql_query($sql);
		$_user_data = new User_data();
		$google_data = new Google_data();
		while ($row = sql_fetch_assoc($res)) {
			if ($row["google_id"]) {
				$_user_info = $_user_data->getUserDetailsById($row["user_id"]);
				if ($_user_info["google_username"] && $_user_info["google_password"]) {
					if (in_array($row["user_id"], $user_arr)) {
						//die("update");
						//update in google
						$google_data->updateGoogleCalendarItem($_user_info["google_username"], $_user_info["google_password"], $row["google_id"], $appointment);
					} else {
						//remove from google
						$google_data->deleteGoogleCalendarItem($_user_info["google_username"], $_user_info["google_password"], $row["google_id"]);

					}
				}
			}
		}
		if (count($user_arr)) {
			//remove items from group members or extra_users that are deselected
			$sql = sprintf("DELETE FROM calendar_user WHERE calendar_id = %d AND user_id NOT IN (%s)", $appointment["id"], implode(",", $user_arr));
			$res = sql_query($sql);
		}

		// update or insert or delete repeats based on the is_repeat setting
		if ($appointment["is_repeat"]) {
			// check if we already have items in the calendar_repeats table
			$sql = sprintf("SELECT COUNT(*) FROM calendar_repeats WHERE calendar_id = %d", $appointment["id"]);
			$res = sql_query($sql);
			$count = sql_result($res, 0);
			if ($count) {
				//update
				$q = sprintf("UPDATE calendar_repeats SET repeat_type = %d, timestamp_end = %d, repeat_frequency = %d, repeat_days = '%s' WHERE calendar_id = %d",
					$appointment_repeat["repeat_type"], $appointment_repeat["repeat_end"], 1, $appointment_repeat["repeat_days"], $appointment["id"]);
			} else {
				//insert
				$q = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, timestamp_end, repeat_frequency, repeat_days) VALUES (%d, %d, %d, %d, '%s')",
					$appointment["id"], $appointment_repeat["repeat_type"], $appointment_repeat["repeat_end"], 1, $appointment_repeat["repeat_days"]);
			}
			$res = sql_query($q);
		} else {
			// remove items from calendar_repeats if there
			$sql = sprintf("DELETE FROM calendar_repeats WHERE calendar_id = %d", $appointment["id"]);
			$res = sql_query($sql);
		}
		/* }}} */
	} else {
		/* making new appointment {{{ */
		//insert calendar info int calendar table
		$q  = sprintf("INSERT INTO calendar (%s) VALUES (%s)", implode(",", $fields), implode(",", $values));
		$res = sql_query($q);
		//get the calendar id
		$appointmentid = sql_insert_id("calendar");
		$modified_ids[] = $appointmentid;
		//link the calendar appointment to all the users
		if ($appointment["is_registered"]) {
			$status = 4;
		} else {
			$status = 1;
		}
		foreach ($user_arr as $user) {
			//create in google if needed
			$_user_data = new User_data();
			$_user_info = $_user_data->getUserDetailsById($user);
			if ($_user_info["google_username"] && $_user_info["google_password"]) {
				$google_data = new Google_data();
				$google_id = $google_data->createGoogleCalendarItem($_user_info["google_username"], $_user_info["google_password"], $appointment);
			} else {
				$google_id = 0;
			}
			$q = sprintf("INSERT INTO calendar_user (calendar_id, user_id, status, google_id) VALUES (%d, %d, %d, '%s')", $appointmentid, $user, $status, $google_id);
			$res = sql_query($q);
		}
		// make it repeating if needed
		if ($appointment["is_repeat"]) {
				$q = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, timestamp_end, repeat_frequency, repeat_days) VALUES (%d, %d, %d, %d, '%s')",
					$appointmentid, $appointment_repeat["repeat_type"], $appointment_repeat["repeat_end"], 1, $appointment_repeat["repeat_days"]);
				$res = sql_query($q);
		}
		/* }}} */
	}
	/* if campaign actions are involved */
	if ($appointment["campaign_id"]) {
		$sql = sprintf("UPDATE campaign_records SET is_called=1, answer=3, appointment_id=%d, user_id=%d WHERE id=%d", $modified_ids[0], $_SESSION["user_id"], $appointment["campaign_id"]);
		$res = sql_query($sql);
		$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					if (parent) {
						if (parent.document.getElementById('options3')) {
							parent.document.getElementById('options3').checked = true;
							parent.document.getElementById('velden').submit();
						}
						closepopup();
					}
				");
			$output->end_javascript();
		$output->exit_buffer();
	}

	/* put todo on done when this was a todo */
	if ($appointment["todoid"]) {
		$sql = sprintf("UPDATE todo SET is_done=1 WHERE id=%d", $appointment["todoid"]);
		$res = sql_query($sql);
		if ($GLOBALS["covide"]->license["has_funambol"]) {
			$funambol_data = new Funambol_data();
			$funambol_data->syncRecord("todo", "", $appointment["todoid"]);
		}
	}
}
/* prepare an email */
if (is_array($data["relmail"])) {
	// see if the user has custom notification template
	$user_body = $this->getNotifyTemplate($_SESSION["user_id"]);
	// get users realname
	$user_data = new User_data();
	$user_info = $user_data->getEmployeedetailsById($_SESSION["user_id"]);

	$mail_subject = gettext("Confirmation of new appointment")." ".strftime("%A %d %B %Y %H:%M", $appointment["timestamp_start"]);

	if ($user_body) {
		// replace start time marker
		$mail_body = str_replace("{{starttime}}", strftime("%A %d %B %Y %H:%M", $appointment["timestamp_start"]), $user_body);
		// replace end time marker
		$mail_body = str_replace("{{endtime}}", strftime("%A %d %B %Y %H:%M", $appointment["timestamp_end"]), $mail_body);
		// replace subject marker
		$mail_body = str_replace("{{subject}}", $appointment["subject"], $mail_body);
		// replace description marker
		$mail_body = str_replace("{{description}}", html_entity_decode($appointment["description"], ENT_NOQUOTES, "UTF-8"), $mail_body);
		// replace the location marker
		$mail_body = str_replace("{{location}}", $appointment["location"], $mail_body);
	} else {
		$mail_body  = gettext("You have made an appointment with")." ".$user_info["realname"]." ".gettext("of company")." ".$GLOBALS["covide"]->license["name"]."\n\n";
		$mail_body .= "---\n";
		if (count(is_array($appointment["from_day"]) && $appointment["from_day"]) > 1) {
			$mail_body .= gettext("days").": ";
			$mail_body .= implode($appointment["from_day"], "/");
			$mail_body .= " ".strftime("%B", mktime(0,0,0,$appointment["from_month"],15,$appointment["from_year"])) ." ". $appointment["from_year"]."\n";
			$mail_body .= gettext("starttime").": ".strftime("%H:%M", $appointment["timestamp_start"])."\n";
			$mail_body .= gettext("endtime").": ".strftime("%H:%M", $appointment["timestamp_end"])."\n";
		} else {
			$mail_body .= gettext("starttime").": ".strftime("%d %B %Y %H:%M", $appointment["timestamp_start"])."\n";
			$mail_body .= gettext("endtime").": ".strftime("%d %B %Y %H:%M", $appointment["timestamp_end"])."\n";
		}
		$mail_body .= gettext("subject").": ".$appointment["subject"]."\n";
		$mail_body .= gettext("description").": ".html_entity_decode($appointment["description"], ENT_NOQUOTES, "UTF-8")."\n";
		$mail_body .= "---\n\n";
		$mail_body .= gettext("Without further notice I assume this appointment will take place at the time listed above.")."\n\n";
		$mail_body .= gettext("Regards")."\n\n".$user_info["realname"]."\n".$GLOBALS["covide"]->license["name"];
	}

	$from = $user_info["mail_email"];
	if (!trim($from)) {
		$from = "support@terrazur.nl";
	}

	$mail_data = new Email_data();
	$concept_id = $mail_data->save_concept();
	if (!is_array($data["relmail"])) {
		$data["relmail"] = array($data["relmail"]);
	}
	$mailreq = array(
		"view_mode" => "html",
		"mail" => array(
			"from" => $from,
			"subject" => $mail_subject,
			"to" => implode(",", $data["relmail"]),
			"rcpt" => implode(",", $data["relmail"]),
		),
		"contents" => nl2br($mail_body),
	);
	if ($data["address_id"]) {
		$mailreq["mail"]["address_id"] = $data["address_id"];
	}
	//create ics file
	$ics = "BEGIN:VCALENDAR
PRODID:-//Covide BV//Covide ".$GLOBALS["covide"]->version."//EN
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
ATTENDEE;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:".implode(",", $data["relmail"])."
ORGANIZER:MAILTO:".$from."
DTSTART:".date("Ymd", $appointment["timestamp_start"])."T".date("His", $appointment["timestamp_start"])."
DTEND:".date("Ymd", $appointment["timestamp_end"])."T".date("His", $appointment["timestamp_end"])."
LOCATION:".$appointment["location"]."
TRANSP:TRANSPARENT
SEQUENCE:0
UID:".date('Ymd').'T'.date('His')."-".rand()."
DTSTAMP:".date('Ymd').'T'.date('His')."
DESCRIPTION:".$appointment["description"]."
SUMMARY:".$appointment["subject"]."
PRIORITY:5
X-MICROSOFT-CDO-IMPORTANCE:1
CLASS:PUBLIC
END:VEVENT
END:VCALENDAR
";
	$icsdata = array(
		"name" => "meeting.ics",
		"type" => "text/calendar; method=REQUEST; charset=\"UTF-8\"",
		"bin"  => $ics,
		"id"   => $concept_id
	);
	$tmp_buf[1] = $mail_data->save_concept($concept_id, $mailreq);
	$tmp_buf[3] = $mail_data->addAttachmentFromString($icsdata);
	$tmp_buf[2] = $mail_data->sendMailComplex($concept_id, 1, 1, 1);
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
	$usertmp_arr = $user_arr;
	foreach ($usertmp_arr as $key => $value) {
		if ($value == $_SESSION["user_id"]) {
			unset($usertmp_arr[$key]);
		}
	}
	$note["to"]         = implode(",", $usertmp_arr);
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
	// list of modified/create calendar items. lets remove doubles and loop thru for funambol
	$modified_ids = array_unique($modified_ids);
	foreach ($modified_ids as $mod_id) {
		// find owner of modified id for funambol
		$sql = sprintf("SELECT user_id FROM calendar_user WHERE calendar_id=%d", $mod_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$funambol_data = new Funambol_data((int)$row["user_id"]);
			$funambol_data->syncRecord("calendar", "", $mod_id);
			unset($funambol_data);
		}
	}
}

/* do dimdim stuff */
if ($data["dimdim_meeting_id"]) {
	$user_data = new User_data();
	$dimdim_data = new Dimdim_data();
	$dimdim = $dimdim_data->getMeetingById($data["dimdim_meeting_id"]);

	$users = explode(",", $dimdim["attendees"]);
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
		if (!$v || $v == $_SESSION["user_id"]) {
			unset($users[$k]);
		}
	}
	if ($users) {
		$subject = gettext("New dimdim meeting for you");
		$body  = gettext("You are invited to a dimdim meeting").".\n\n";
		$body .= gettext("starttime").": ".date("d-m-Y H:i", strtotime($dimdim["startdate"]))."\n";
		$body .= gettext("endtime").": ".date("d-m-Y H:i", strtotime($dimdim["enddate"]))."\n";
		$body .= gettext("subject").": ".$dimdim["name"]."\n";
		$body .= gettext("description").": ".$dimdim["description"]."\n\n";
		$body .= gettext("When the meeting starts you can click the following link")."\n\n";
		$body .= "<a target=\"_blank\" href=\"http://webmeeting.dimdim.com:80/portal/JoinForm.action?confKey=".$dimdim["room"]."\">".$dimdim["name"]."</a>";
		$note["to"]         = implode(",", $users);
		$note["from"]       = $_SESSION["user_id"];
		$note["body"]       = addslashes($body);
		$note["subject"]    = $subject;
		$note_data = new Note_data();
		if (!$note_data->store2db($note)) {
			die("ERROR ! Something went wrong with storing the note. Please file a bugreport.");
		}
	}

	$emails = unserialize($dimdim["external_attendees"]);
	if (is_array($emails)) {
		$user_info = $user_data->getEmployeedetailsById($_SESSION["user_id"]);
		$from = $user_info["mail_email"];
		if (!trim($from)) {
			$from = "support@terrazur.nl";
		}
		$body  = gettext("You're invited to a dimdim meeting").".\n\n";
		$body .= gettext("starttime").": ".date("d-m-Y H:i", strtotime($dimdim["startdate"]))."\n";
		$body .= gettext("endtime").": ".date("d-m-Y H:i", strtotime($dimdim["enddate"]))."\n";
		$body .= gettext("subject").": ".$dimdim["name"]."\n";
		$body .= gettext("description").": ".$dimdim["description"]."\n\n";
		$body .= gettext("When the meeting starts you can click the following link")."\n\n";
		$body .= "<a target=\"_blank\" href=\"http://webmeeting.dimdim.com:80/portal/JoinForm.action?confKey=".$dimdim["room"]."\">".$dimdim["name"]."</a>";
		$mail_data = new Email_data();
		$concept_id = $mail_data->save_concept();
		$mailreq = array(
			"view_mode" => "html",
			"mail" => array(
				"from" => $from,
				"subject" => gettext("New dimdim meeting for you"),
				"to" => implode(",", $emails),
				"rcpt" => implode(",", $emails),
			),
			"contents" => nl2br($body)
		);
		$tmp_buf[1] = $mail_data->save_concept($concept_id, $mailreq);
		$tmp_buf[2] = $mail_data->sendMailComplex($concept_id, 1, 1);
	}
}

if (!$funambol_input) {
	$output = new Layout_output();
	$output->start_javascript();
	$output->addCode("
		if (parent) {
			if (parent.document.getElementById('search')) {
				parent.document.getElementById('search').submit();
			} else {
				parent.location.reload();
			}
		}
		closepopup();
	");
	$output->end_javascript();
	echo $output->generate_output();
}
?>
