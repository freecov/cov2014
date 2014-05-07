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
} else {
	$sql = sprintf("SELECT * FROM calendar WHERE id=%d", $id);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	/* return data fetched from database */
	$appointment["id"]                = $row["id"];
	$appointment["begin_month"]       = date("m",$row["timestamp_start"]);
	$appointment["begin_year"]        = date("Y",$row["timestamp_start"]);
	$appointment["begin_day"]         = date("j",$row["timestamp_start"]);
	$appointment["begin_hour"]        = date("H",$row["timestamp_start"]);
	$appointment["begin_min"]         = date("i",$row["timestamp_start"]);
	$appointment["end_hour"]          = date("H",$row["timestamp_end"]);
	$appointment["end_min"]           = date("i",$row["timestamp_end"]);
	$appointment["timestamp_start"]   = $row["timestamp_start"];
	$appointment["timestamp_end"]     = $row["timestamp_end"];
	/* wipe out info on private appointments that are not for logged in user */
	if ($_SESSION["user_id"] != $row["user_id"] && $row["is_private"]) {
		$appointment["subject"]       = gettext("prive afspraak");
		$row["description"]           = gettext("prive afspraak");
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

	if ($appointment["group_id"]) {
		$res = sql_query(sprintf("SELECT user_id FROM calendar WHERE group_id=%d", $appointment["group_id"]));
		for ($i = 0; $arr = sql_fetch_row($res); $i++)
			$extra[$i] = $arr[0];
	}
	$appointment["is_holiday"]       = $row["is_holiday"];
	$appointment["is_specialleave"]  = $row["is_specialleave"];
	$appointment["is_ill"]           = $row["is_ill"];
}
?>
