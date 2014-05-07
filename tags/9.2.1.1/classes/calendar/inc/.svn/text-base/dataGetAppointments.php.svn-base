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
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
$user_id = $_SESSION["user_id"];
$licensie = $GLOBALS["covide"]->license;
$address_data = new Address_data();
$project_data = new Project_data();
$user_data = new User_data();
//$eigen = 1;

$intervalAmount = 5;

/* get the permissions from db */
if ($_user != $user_id) {
	$calperms = "";
	$sql = sprintf("SELECT * FROM calendar_permissions WHERE user_id = %d AND user_id_visitor = %d", $_user, $user_id);
	$res = sql_query($sql);
	if (sql_num_rows($res)) {
		$row = sql_fetch_assoc($res);
		$calperms = $row["permissions"];
	}
} else {
	$calperms = "RW";
}
//create array with timestamps in minutes from daystart
for ($i=0;$i<(24*60);$i+=$intervalAmount) {
	$arr[$i] = 0;
}
$events = array();

//start and end timestamps of day
$start=mktime(0,0,0,$_month,$_day,$_year);
$eind=mktime(0,0,0,$_month,$_day+1,$_year);
$c=1;

$repeating_events = $this->query_items($_user, true, $start, $eind);
$normal_events    = $this->query_items($_user, false, $start, $eind);

//get all repeating items for today
$rep = $this->getRepeatingItemsByDate($_user, $repeating_events, $start, true);

if (is_array($rep)) {
	foreach ($rep as $k=>$v) {
		$normal_events[] = $v;
	}
}
foreach ($normal_events as $row) {
	$b_time = (date("H",$row["timestamp_start"])*60)+date("i",$row["timestamp_start"]);
	$e_time = (date("H",$row["timestamp_end"])*60)+date("i",$row["timestamp_end"]);
	// store the events.
	if ($row["is_repeat"] != 1 && $row["alldayevent"]) {
		if ($_day != date("d", $row["timestamp_start"])) {
			continue;
		}
		$events[] = $row["id"];
	}
	// for all other appoinments add it to the array...
	for ($i=$b_time;$i<$e_time;$i+=$intervalAmount) {
		if (!$arr[$i])
			$arr[$i] = $row["id"];
		else
			$arr[$i].= ",".$row["id"]; //a very simple workaround to display multiple appo's at the same time
	}
}
//check for appointment
$i=0;
while ($i<60*24 || $c==0) {
	if ($arr[$i]>0) {
		$c=1;
	}
	$i+=15;
}

//copy of array with regular appointments.
$ids = implode(",",array_unique($arr));

//copy of array with events.
$eventids = implode(",",$events);

//combine those, so the details get fetched.
if (count($events) > 0) {
	$ids .= ",".$eventids;
}

if (count($this->calendar_items)==0) {
	$counter = 1;
} else {
	$counter++;
}
if ($calperms == "") {
	$ids = 0;
}
//get the data from the database
$q = sprintf("SELECT
	calendar.*, calendar_user.status
FROM
	calendar
INNER JOIN calendar_user ON calendar.id = calendar_user.calendar_id
WHERE
	calendar_user.user_id = %d
	AND id IN (%s)
ORDER BY
	timestamp_start",
	$_user, $ids);
$res = sql_query($q);
while ($row = sql_fetch_array($res)) {
	$p                                         = $counter;
	$this->calendar_items[$p]["id"]            = $row["id"];
	$this->calendar_items[$p]["start_time"]    = (date("H",$row["timestamp_start"])*60)+date("i",$row["timestamp_start"]);
	$this->calendar_items[$p]["end_time"]      = (date("H",$row["timestamp_end"])*60)+date("i",$row["timestamp_end"]);
	$this->calendar_items[$p]["ststamp"]       = $row["timestamp_start"];
	$this->calendar_items[$p]["etstamp"]       = $row["timestamp_end"];
	$this->calendar_items[$p]["shuman"]        = strftime("%H:%M", $row["timestamp_start"]);
	$this->calendar_items[$p]["ehuman"]        = strftime("%H:%M", $row["timestamp_end"]);
	if ($row["alldayevent"] == 1) {
		$this->calendar_items[$p]["human_span_short"]= "";   // weekview for example does not need any text
		$this->calendar_items[$p]["human_span_long"] = gettext("Event")."\n";   // for dayview for example, its better to have this..
		$this->calendar_items[$p]["shuman"]          = "";
		$this->calendar_items[$p]["ehuman"]          = "";
	} else {
		if (!array_key_exists($_SESSION["user_id"], $this->userdetails_cache)) {
			$this->userdetails_cache[$_SESSION["user_id"]] = $user_data->getUserdetailsById($_SESSION["user_id"]);
		}
		if ($this->userdetails_cache[$_SESSION["user_id"]]["hour_format"])
			$prefDate = "g:i A";
		else
			$prefDate = "H:i";
		$this->calendar_items[$p]["shuman"]          = date($prefDate, $row["timestamp_start"]);
		$this->calendar_items[$p]["ehuman"]          = date($prefDate, $row["timestamp_end"]);
		$this->calendar_items[$p]["human_span_short"] = $this->calendar_items[$p]["shuman"]." - ".$this->calendar_items[$p]["ehuman"]."\n";
		$this->calendar_items[$p]["human_span_long"] = $this->calendar_items[$p]["shuman"]." - ".$this->calendar_items[$p]["ehuman"]."\n";
	}
	$this->calendar_items[$p]["location"]      = $row["location"];
	$this->calendar_items[$p]["kilometers"]    = $row["kilometers"];
	$this->calendar_items[$p]["relation"]      = (int)$row["address_id"];
	$this->calendar_items[$p]["relation_name"] = $address_data->getAddressNameById($row["address_id"]);
	$this->calendar_items[$p]["project_id"]    = (int)$row["project_id"];
	$this->calendar_items[$p]["project_name"]  = $project_data->getProjectNameById($row["project_id"]);
	$this->calendar_items[$p]["multirel"]      = $row["multirel"];
	if (!$row["multirel"])
		$address_ids = $row["address_id"];
	else
		$address_ids = $row["address_id"].",".$row["multirel"];
	$address_ids_arr = explode(",", $address_ids);
	array_unique($address_ids_arr);
	$all_address_names_arr = array();
	foreach($address_ids_arr as $address)
		$all_address_names_arr[] = $address_data->getAddressNameById($address);
	$this->calendar_items[$p]["all_address_ids"] = implode(",", $address_ids_arr);
	$this->calendar_items[$p]["all_address_names"] = implode(", ", $all_address_names_arr);
	if ($row["status"] == 4) {
		$row["is_registered"] = 1;
	}
	$this->calendar_items[$p]["is_registered"] = $row["is_registered"];
	$this->calendar_items[$p]["show_actions"]  = ($row["is_registered"]==1?0:1);
	if ((int)$row["is_registered"] == 0 && (int)$row["project_id"] && !$GLOBALS["covide"]->license["has_project_declaration"] && ($row["timestamp_start"] < time())) {
		$this->calendar_items[$p]["show_reg"]  = 1;
	}
	$this->calendar_items[$p]["repeat_type"]   = $row["repeat_type"];
	//XXX updated field
	$this->calendar_items[$p]["is_repeat"]     = $row["is_repeat"];
	$this->calendar_items[$p]["is_repeat"]     = $row["isrecurring"];
	$this->calendar_items[$p]["importance"]      = $row["importance"];
	if ($this->calendar_items[$p]["importance"] == 2) {
		$this->calendar_items[$p]["important"] = 1;
	}
	$this->calendar_items[$p]["notifytime"]      = $row["notifytime"];
	/* wipe out contert for private appointments that do not belong to loggedin user */
	//XXX: get rid of _SESSION in data object
	if (($_SESSION["user_id"] != $_user && $row["is_private"]) || $calperms == "") {
		$this->calendar_items[$p]["subject"]         = gettext("private appointment");
		$this->calendar_items[$p]["body"]            = gettext("private appointment");
		$this->calendar_items[$p]["permissions"]     = 0;
		$this->calendar_items[$p]["show_actions"]    = 0;
		/* to get this item shown in weekview, fake registered item */
		$this->calendar_items[$p]["is_registered"]   = 1;
	} else {
		if ($calperms == "RW" || $_user == $_SESSION["user_id"]) {
			$this->calendar_items[$p]["permissions"]     = 1;
		} else {
			$this->calendar_items[$p]["permissions"]     = 0;
		}
		if (!trim($row["subject"])) {
			$this->calendar_items[$p]["subject"]     = substr($row["description"], 0, 40);
		} else {
			$this->calendar_items[$p]["subject"]     = $row["subject"];
		}
		$this->calendar_items[$p]["body"]            = $row["body"];
	}
	$this->calendar_items[$p]["is_private"]      = $row["is_private"];
	$this->calendar_items[$p]["note_id"]         = $row["note_id"];
	if ($row["note_id"]) {
		$this->calendar_items[$p]["no_note"]     = 0;
		$note_data = new Note_data();
		$noteinfo = $note_data->getNoteById($row["note_id"]);
		$this->calendar_items[$p]["note_title"]  = $noteinfo["subject"];
		unset($note_data);
		unset($noteinfo);
	} else {
		$this->calendar_items[$p]["no_note"]     = 1;
	}
	$this->calendar_items[$p]["user_id"]         = $_user;
	$this->calendar_items[$p]["is_ill"]          = $row["is_ill"];
	$this->calendar_items[$p]["is_specialleave"] = $row["is_specialleave"];
	$this->calendar_items[$p]["is_holiday"]      = $row["is_holiday"];
	$this->calendar_items[$p]["modified"]        = (int)$row["modified"];
	$this->calendar_items[$p]["modified_by"]     = (int)$row["modified_by"];
	$this->calendar_items[$p]["h_modified"]      = date("d-m-Y H:i", (int)$row["modified"]);
	$this->calendar_items[$p]["h_modified_by"]   = $user_data->getUsernameById((int)$row["modified_by"]);
	$this->calendar_items[$p]["dimdim_id"]       = $row["dimdim_meeting"];
	/* appointment type identifier */
	if ($row["is_ill"])
		$this->calendar_items[$p]["app_type"] = 5;
	elseif ($row["is_specialleave"])
		$this->calendar_items[$p]["app_type"] = 4;
	elseif ($row["is_holiday"])
		$this->calendar_items[$p]["app_type"] = 3;
	elseif ($row["is_private"])
		$this->calendar_items[$p]["app_type"] = 2;
	else
		$this->calendar_items[$p]["app_type"] = 1;
	$this->calendar_items[$p]["alldayevent"]          = $row["alldayevent"];
	$this->calendar_items[$p]["rowspan"] = 0;

	$counter++;
}
//last record not used - option base 0
$counter--;
//recreate the array.
for ($i=0;$i<24*60;$i+=$intervalAmount) {
	if (strpos($arr[$i], ",")!== false) {
		$_items = array();
		$_i = explode(",", $arr[$i]);
		foreach ($_i as $_k => $_v) {
			for ($j=1;$j<=$counter;$j++) {
				if ($_v == $this->calendar_items[$j]["id"]) {
					$_items[] = $j;
				}
			}
		}
		$arr[$i] = implode(",", $_items);
	} else {
		for ($j=1;$j<=$counter;$j++) {
			if ($arr[$i]==$this->calendar_items[$j]["id"]) {
				$arr[$i]=$j;
			}
		}
	}
}
//compress the array
if ($compress==1) {
	$comp[] = $arr[0];
	for($i=0;$i<24*60;$i+=$intervalAmount) {
		if ($arr[$i]!=$comp[count($comp)-1]) {
			$comp[] = $arr[$i];
		}
	}
	$arr=$comp; //put the compressed array back in the old array
} else {
	for ($x = 0; $x <= 24*60; $x += $intervalAmount) {
		if ($arr[$x]) {
			if (strpos($arr[$x], ",") !== false) {
				$_i = explode(",", $arr[$x]);
				foreach ($_i as $_v) {
					$this->calendar_items[$_v]["rowspan"] += 1;
				}
			} else {
				$this->calendar_items[$arr[$x]]["rowspan"] += 1;
			}
		}
	}
}
if ($calperms == "") {
	unset($arr);
	for ($i=(8*60); $i<=17*60; $i+=$intervalAmount) {
		$arr[$i] = 1;
	}
	unset($this->calendar_items);
	$row["timestamp_start"] = mktime(8, 0, 0, $month, $day, $year);
	$row["timestamp_end"] = mktime(17, 0, 0, $month, $day, $year);
	$this->calendar_items[1] = array(
		"id"            => 0,
		"start_time"    => (date("H",$row["timestamp_start"])*60)+date("i",$row["timestamp_start"]),
		"end_time"      => (date("H",$row["timestamp_end"])*60)+date("i",$row["timestamp_end"]),
		"ststamp"       => $row["timestamp_start"],
		"etstamp"       => $row["timestamp_end"],
		"shuman"        => strftime("%H:%M", $row["timestamp_start"]),
		"ehuman"        => strftime("%H:%M", $row["timestamp_end"]),
		"show_actions"  => 0,
		"important"     => 0,
		"subject"       => gettext("no permissions on this calendar"),
		"rowspan"       => 36
	);
}

/* sort based on start time */
$sortorder = array();
if (is_array($this->calendar_items)) {
	foreach ($this->calendar_items as $k=>$v) {
		$sortorder[$k] = $v["shuman"];
	}
	asort($sortorder);
	foreach ($sortorder as $newkey=>$temp) {
		$sortorder[$newkey] = $this->calendar_items[$newkey];
	}
	$this->calendar_items = $sortorder;
	unset($sortorder);
}
?>
