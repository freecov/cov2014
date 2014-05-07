<?php
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}

/* Helper classes we need */
$user_data = new User_data();
$address_data = new Address_data();
$project_data = new Project_data();

/* our data holder */
$appointment = array();

if ($id==0) {
	/* return emty item, used for creating new item */
	/* we set the time to right now, rounded by hours */
	/* identifier for group appointment */
	$gpid = rand(0, pow(2,30));

	$appointment["id"]              = 0;
	$appointment["begin_month"]     = date("m");
	$appointment["begin_year"]      = date("Y");
	$appointment["begin_day"]       = date("j");
	$appointment["begin_hour"]      = date("H");
	$appointment["begin_min"]       = 0;
	$appointment["end_hour"]        = (date("H")+1);
	$appointment["end_min"]         = 0;
	$appointment["timestamp_start"] = mktime(date("H"),0,0,date("m"),date("d"),date("Y"));
	$appointment["timestamp_end"]   = mktime((date("H")+1),0,0,date("m"),date("d"),date("Y"));
	$appointment["group_id"]        = $gpid;
	$appointment["is_event"]        = 0;
	$appointment["importance"]      = 1; //normal
} else {
	$sql = sprintf("SELECT * FROM calendar, calendar_user WHERE calendar.id = calendar_user.calendar_id AND calendar.id = %d AND calendar_user.user_id", $id, $user_id);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	// do the permission check
	if ($this->checkPermission($user_id, $_SESSION["user_id"])) {
		$access = 1;
	}
	if ($user_id == $_SESSION["user_id"]) {
		$access = 1;
	}
	if (!$access && !$funambol) {
		$gpid = rand(0, pow(2,30));

		$appointment["id"]              = 0;
		$appointment["begin_month"]     = date("m");
		$appointment["begin_year"]      = date("Y");
		$appointment["begin_day"]       = date("j");
		$appointment["begin_hour"]      = date("H");
		$appointment["begin_min"]       = 0;
		$appointment["end_hour"]        = (date("H")+1);
		$appointment["end_min"]         = 0;
		$appointment["timestamp_start"] = mktime(date("H"),0,0,date("m"),date("d"),date("Y"));
		$appointment["timestamp_end"]   = mktime((date("H")+1),0,0,date("m"),date("d"),date("Y"));
		$appointment["group_id"]        = $gpid;
		$appointment["is_event"]        = 0;
	} else {
		/* return data fetched from database */
		$appointment["id"]                = $row["id"];
		$appointment["begin_month"]       = date("m",$row["timestamp_start"]);
		$appointment["begin_year"]        = date("Y",$row["timestamp_start"]);
		$appointment["begin_day"]         = date("j",$row["timestamp_start"]);
		// find out preferred date output format
		$userdetails = $user_data->getUserdetailsById($_SESSION["user_id"]);
		if ($userdetails["hour_format"]) { 
			$prefDate = "g"; 
			$prefAM = "i A"; 
		} else {
			$prefDate = "H"; 
			$prefAM = "i"; 
		}
		
		$appointment["begin_hour"]        = date($prefDate, $row["timestamp_start"]);
		$appointment["begin_min"]         = date($prefAM, $row["timestamp_start"]);
		$appointment["end_hour"]          = date($prefDate, $row["timestamp_end"]);
		$appointment["end_min"]           = date($prefAM, $row["timestamp_end"]);
		$appointment["timestamp_start"]   = $row["timestamp_start"];
		$appointment["timestamp_end"]     = $row["timestamp_end"];
		/* wipe out info on private appointments that are not for logged in user */
		if ($_SESSION["user_id"] != $user_id && $row["is_private"] && !$funambol) {
			$appointment["subject"]     = gettext("private appointment");
			$appointment["body"]        = gettext("private appointment");
			$appointment["description"] = gettext("private appointment");
		} else {
			// if no subject, generate one
			if ($row["subject"]) {
				$appointment["subject"] = $row["subject"];
			} else {
				$appointment["subject"] = substr(strip_tags(html_entity_decode($row["body"], ENT_NOQUOTES, "UTF-8")), 0, 80);
			}
			$appointment["body"]        = $row["body"];
			$appointment["description"] = $row["body"];
		}
		$appointment["project_id"]        = $row["project_id"];
		// attach project name
		$projectinfo = $project_data->getProjectById($row["project_id"],0);
		$appointment["project_name"]      = $projectinfo[0]["name"];
		$appointment["private_id"]        = $row["private_id"];
		$appointment["is_private"]        = $row["is_private"];
		$appointment["location"]          = $row["location"];
		$appointment["user_id"]           = $user_id;
		$appointment["user_name"]         = $user_data->getUsernameById($user_id);
		if ($row["reminderminutesbeforestart"] > 0) {
			$appointment["is_important"] = 1;
		} else {
			$appointment["is_important"] = 0;
		}
		$appointment["importance"]        = $row["importance"];
		if ($appointment["importance"] == 2) {
			$appointment["is_important"] = 1;
		}
		$appointment["notifytime"]        = ($row["reminderminutesbeforestart"]*60);
		$appointment["is_group"]          = $row["is_group"];
		$appointment["group_id"]          = $row["group_id"];
		$appointment["is_repeat"]         = $row["is_repeat"];
		$appointment["repeat_type"]       = $row["repeat_type"];
		$appointment["deckm"]             = $row["deckm"];
		$appointment["modified_by"]       = $row["modified_by"];
		$appointment["modified_name"]     = $user_data->getUsernameById($row["modified_by"]);
		if ($GLOBALS["covide"]->license["has_voip"]) {
			$appointment["is_dnd"]        = $row["is_dnd"];
		}
		if (!$row["multiprivate"]) {
			$address_ids = $row["private_id"];
		} else {
			$address_ids = $row["private_id"].",".$row["multiprivate"];
		}
		$address_ids_arr = explode(",", $address_ids);
		$private_ids = array_unique($address_ids_arr);
		$appointment["multiprivate"] = implode(",", $private_ids);
		
		if (!$row["multirel"]) {
			$address_ids = $row["address_id"];
		} else {
			$address_ids = $row["address_id"].",".$row["multirel"];
		}
		$address_ids_arr = explode(",", $address_ids);
		array_unique($address_ids_arr);
		
		$all_address_names_arr = array();
		foreach($address_ids_arr as $address) {
			$all_address_names_arr[] = $address_data->getAddressNameById($address);
		}
		$appointment["all_address_ids"] = implode(",", $address_ids_arr);
		$appointment["all_address_names"] = implode(", ", $all_address_names_arr);
		
			
		if ($row["multirel"]) {
			$appointment["multirel"]      = $row["multirel"];
		}
		if (!$appointment["address_id"]) {
			$appointment["address_id"]    = $row["address_id"];
			$appointment["address_name"]  = $address_data->getAddressNameByID($row["address_id"]);
		}
		$appointment["kilometers"]        = $row["kilometers"];
		if ($appointment["kilometers"]==0) {
			$appointment["kilometers"]    = "";
		}
		$appointment["dimdim_id"]        = $row["dimdim_meeting"];
		$appointment["is_holiday"]       = $row["is_holiday"];
		$appointment["is_specialleave"]  = $row["is_specialleave"];
		$appointment["is_ill"]           = $row["is_ill"];
		$appointment["modified"]         = (int)$row["modified"];
		$appointment["modified_by"]      = (int)$row["modified_by"];
		if($userdetails["hour_format"]) {
			$prefDate = "F j, Y g:i A";
		} else {
			$prefDate = "d-m-Y H:i";
		}
		$appointment["h_modified"]       = date($prefDate, (int)$row["modified"]);
		$appointment["h_modified_by"]    = $user_data->getUsernameById((int)$row["modified_by"]);
		$extrausers = $this->getCalendarUsersByCalendarId($id, $user_id);
		$appointment["extra_users"]      = implode(",", $extrausers);
		$appointment["is_event"]         = $row["is_event"];
		$appointment["alldayevent"]      = $row["alldayevent"];
		if ($row["note_id"]) {
			$appointment["no_note"]     = 0;
			$appointment["note_id"]     = $row["note_id"];
			$note_data = new Note_data();
			$noteinfo = $note_data->getNoteById($row["note_id"]);
			$appointment["note_title"]  = $noteinfo["subject"];
			unset($note_data);
			unset($noteinfo);
		} else {
			$appointment["no_note"]     = 1;
		}
		$appointment["is_registered"] = ($row["status"] == 4) ? 1 : 0;
		// attach repeating information
		if ($row["isrecurring"] == 1) {
			$sql = sprintf("SELECT * FROM calendar_repeats WHERE calendar_id = %d", $id);
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				$row = sql_fetch_assoc($res);
				$appointment["is_repeat"] = 1;
				$appointment["repeat_type"] = $row["repeat_type"];
				$appointment["repeat_end"]  = $row["timestamp_end"];
				$appointment["repeat_freq"] = $row["repeat_frequency"];
				$appointment["repeat_days"] = $row["repeat_days"];
			}
		}
	}
}
?>
