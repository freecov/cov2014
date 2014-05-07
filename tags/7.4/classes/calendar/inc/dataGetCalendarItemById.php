<?php
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
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
} else {
	$sql = sprintf("SELECT * FROM calendar WHERE id=%d", $id);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	/* check for access permissions */
	$access = 0;
	if ($row["extra_users"]) {
		$extra = explode(",", $row["extra_users"]);
		foreach($extra as $userid) {
			if ($userid == $_SESSION["user_id"] || $this->checkPermission($userid, $_SESSION["user_id"]))
				$access = 1;
		}
	}
	if ($this->checkPermission($row["user_id"], $_SESSION["user_id"]))
		$access = 1;
	if ($row["user_id"] == $_SESSION["user_id"])
		$access = 1;

	if (!$access) {
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
		if ($_SESSION["user_id"] != $row["user_id"] && $row["is_private"] && !$funambol) {
			$appointment["subject"]       = gettext("private appointment");
			$row["description"]           = gettext("private appointment");
		} else {
			if ($row["subject"]) {
				$appointment["subject"]   = $row["subject"];
			} else {
				$appointment["subject"]   = substr($row["description"], 0, 80);
			}
		}
		$appointment["is_important"]      = $row["is_important"];
		$appointment["description"]       = $row["description"];
		$appointment["project_id"]        = $row["project_id"];
		$projectinfo = $project_data->getProjectById($row["project_id"],0);
		$appointment["project_name"]      = $projectinfo[0]["name"];
		$appointment["is_private"]        = $row["is_private"];
		$appointment["location"]          = $row["location"];
		$appointment["user_id"]           = $row["user_id"];
		$appointment["user_name"]         = $user_data->getUsernameById($row["user_id"]);
		$appointment["notifytime"]        = $row["notifytime"];
		$appointment["is_group"]          = $row["is_group"];
		$appointment["group_id"]          = $row["group_id"];
		$appointment["is_repeat"]         = $row["is_repeat"];
		$appointment["repeat_type"]       = $row["repeat_type"];
		$appointment["deckm"]             = $row["deckm"];
		$appointment["modified_by"]       = $row["modified_by"];
		$appointment["modified_name"]     = $user_data->getUsernameById($row["modified_by"]);
		if ($GLOBALS["covide"]->license["voip"])
			$appointment["is_dnd"]        = $row["is_dnd"];
		if (!$row["multirel"])
			$address_ids = $row["address_id"];
		else
			$address_ids = $row["address_id"].",".$row["multirel"];
		$address_ids_arr = explode(",", $address_ids);
		array_unique($address_ids_arr);
		$all_address_names_arr = array();
		foreach($address_ids_arr as $address)
			$all_address_names_arr[] = $address_data->getAddressNameById($address);
		$appointment["all_address_ids"] = implode(",", $address_ids_arr);
		$appointment["all_address_names"] = implode(", ", $all_address_names_arr);
		
			
		if ($row["multirel"])
			$appointment["multirel"]      = $row["multirel"];
		if (!$appointment["address_id"]) {
			$appointment["address_id"]    = $row["address_id"];
			$appointment["address_name"]  = $address_data->getAddressNameByID($row["address_id"]);
		}
		$appointment["kilometers"]        = $row["kilometers"];
		if ($appointment["kilometers"]==0) {
			$appointment["kilometers"]    = "";
		}

		$appointment["is_holiday"]       = $row["is_holiday"];
		$appointment["is_specialleave"]  = $row["is_specialleave"];
		$appointment["is_ill"]           = $row["is_ill"];
		$appointment["modified"]         = (int)$row["modified"];
		$appointment["modified_by"]      = (int)$row["modified_by"];
		if($userdetails["hour_format"]) 
			$prefDate = "F j, Y g:i A";
		else
			$prefDate = "d-m-Y H:i"; 
		$appointment["h_modified"]       = date($prefDate, (int)$row["modified"]);
		$appointment["h_modified_by"]    = $user_data->getUsernameById((int)$row["modified_by"]);
		$appointment["extra_users"]      = $row["extra_users"];
		$appointment["is_event"]         = $row["is_event"];
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
		$appointment["is_registered"] = $row["is_registered"];
	}
}
?>
