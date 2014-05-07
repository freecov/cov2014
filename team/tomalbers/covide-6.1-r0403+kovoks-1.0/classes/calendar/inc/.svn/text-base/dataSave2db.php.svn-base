<?php
/* check if we are included */
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
/* prepare the data */
$appointment =& $data;
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
	$fields[] = "modified_by";      $values[] = (int)$_SESSION["user_id"];
	$fields[] = "modified";         $values[] = (int)mktime();
	$fields[] = "is_important";     $values[] = (int)$appointment["is_important"];
	$fields[] = "is_ill";           $values[] = (int)$appointment["is_ill"];
	$fields[] = "is_specialleave";  $values[] = (int)$appointment["is_specialleave"];
	$fields[] = "is_holiday";       $values[] = (int)$appointment["is_holiday"];
	$fields[] = "timestamp_start";  $values[] = (int)$appointment["timestamp_start"];
	$fields[] = "timestamp_end";    $values[] = (int)$appointment["timestamp_end"];
	$fields[] = "subject";          $values[] = "'".$appointment["subject"]."'";
	$fields[] = "description";      $values[] = "'".$appointment["description"]."'";
	$fields[] = "project_id";       $values[] = (int)$appointment["project_id"];
	$fields[] = "is_private";       $values[] = sprintf("%d", $appointment["is_private"]);
	$fields[] = "address_id";       $values[] = (int)$appointment["address_id"];
	$fields[] = "multirel";         $values[] = "'".$appointment["multirel"]."'";
	$fields[] = "notifytime";       $values[] = (int)$appointment["notifytime"];
	$fields[] = "kilometers";       $values[] = (int)$appointment["kilometers"];
	$fields[] = "is_repeat";        $values[] = (int)$appointment["is_repeat"];
	if ($appointment["is_repeat"] && !$appointment["repeat_type"]) {
		$appointment["repeat_type"] = "D";
	}
	$fields[] = "repeat_type";      $values[] = "'".$appointment["repeat_type"]."'";
	$fields[] = "is_registered";    $values[] = sprintf("%d", $appointment["is_registered"]);
	$fields[] = "location";         $values[] = "'".$appointment["location"]."'";
	$fields[] = "deckm";            $values[] = (int)$appointment["deckm"];
	$fields[] = "is_dnd";           $values[] = (int)$appointment["is_dnd"];
	$fields[] = "is_group";         $values[] = (int)$appointment["is_group"];
	$fields[] = "group_id";         $values[] = (int)$appointment["group_id"];
	/*
	$fields[] = "gpid";             $values[] = $gpid;
	$fields[] = "groep";            $values[] = $appointment["groep"];
	*/
	$queries = array();
	if ($appointment["id"]) {
		/* sync4j snippet. Put a new record in the temp store. */
		$q = sprintf("select user_id, sync_guid from calendar where id = %d", $appointment["id"]);
		$resx = sql_query($q);
		$rowx = sql_fetch_assoc($resx);
		$q = sprintf("select sync4j_source from users where id = %d", $appointment["user"]);
		$resx2 = sql_query($q);
		if (sql_result($resx2,0)) {
			if ($rowx["sync_guid"]) {
				$q = "insert into agenda_sync (user_id, sync_guid, action) values (".$rowx["user_id"].",".$rowx["sync_guid"].",'U')";
				$trash = sql_query($q);
			}
		}
		/* check if this user already has this appointment */
		foreach ($user_arr as $user) {
			/* unset previous set user_id value */
			if (in_array("user_id", $fields)) {
				$k = array_search("user_id", $fields);
				unset($fields[$k]);
				unset($values[$k]);
			}
			$fields[] = "user_id"; $values[] = $user;

			$sql = sprintf("SELECT * FROM calendar WHERE id= %d AND user_id = %d", $appointment["id"], $user);
			$res = sql_query($sql);
			if (sql_num_rows($res) > 0) {
				/* update this appointment */
				$q = "UPDATE calendar SET ";
				foreach ($fields as $k=>$v) {
					$q .= $fields[$k]." = ".$values[$k].", ";
				}
				$q  = substr($q, 0, strlen($q)-2);
				#if ($appointment["group_id"]) {
				#	$q .= sprintf(" WHERE group_id=%d and user_id=%d", $appointment["group_id"], $user);
				#} else {
					$q .= sprintf(" WHERE id=%d", $appointment["id"]);
				#}
			} else {
				/* insert this appointment */
				$q  = "INSERT INTO calendar ";
				$q .= "(".implode(",",$fields).") VALUES (".implode(",",$values).")";
			}
			$queries[] = $q;
			
		}
	} else {
		/* making new appointment */
		foreach ($user_arr as $user) {
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
	}
	/* apply all queries */
	foreach ($queries as $query) {
		$res = sql_query($query);
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
	$mail_subject = gettext("Bevestiging agenda afspraak");
	$mail_body  = $user_data->getUsernameById($appointment["user"]);
	$mail_body .= " ".gettext("heeft een agenda-afspraak met u gemaakt").".\n\n";
	$mail_body .= gettext("begintijd").": ".date("d-m-Y H:i",$appointment["timestamp_start"])."\n";
	$mail_body .= gettext("eindtijd").": ".date("d-m-Y H:i", $appointment["timestamp_end"])."\n";
	$mail_body .= gettext("onderwerp").": ".$appointment["subject"]."\n";
	$mail_body .= gettext("omschrijving").": ".$appointment["description"];
	$mail_data = new Email_data();
	foreach ($data["relmail"] as $mail_to) {
		$mail_data->sendMail($mail_to, $mail_subject, $mail_body, "", $appointment["user"]);
	}
}
/* do we need to send a note to the user? */
if ($appointment["notify_user"] == 1) {
	$user_data = new User_data();
	$loggedin_user = $user_data->getUsernameById($_SESSION["user_id"]);
	$subject = gettext("Nieuwe agenda afspraak voor u");
	$body  = $loggedin_user." ".gettext("heeft een afspraak in de agenda gezet").".\n\n";
	$body .= gettext("begintijd").": ".date("d-m-Y H:i",$appointment["timestamp_start"])."\n";
	$body .= gettext("eindtijd").": ".date("d-m-Y H:i", $appointment["timestamp_end"])."\n";
	$body .= gettext("onderwerp").": ".$appointment["subject"]."\n";
	$body .= gettext("omschrijving").": ".$appointment["description"]."\n\n";
	$body .= "<a href=\"index.php?mod=calendar&day=".date("d", $appointment["timestamp_start"])."&month=".date("m", $appointment["timestamp_start"])."&year=".date("Y", $appointment["timestamp_start"])."\">".gettext("naar agenda")."</a>";
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
?>
<script language="javascript1.2" type="text/javascript">
	if (opener) {
		opener.location.reload();
		window.close();
	}
</script>
