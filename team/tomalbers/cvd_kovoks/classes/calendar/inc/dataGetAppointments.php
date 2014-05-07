<?php
if (!class_exists("Calendar_data")) {
	die("no class definition found");
}
$user_id = $_SESSION["user_id"];
$licensie = $GLOBALS["covide"]->license;
$address_data = new Address_data();
$project_data = new Project_data();
$user_data = new User_data();
//$eigen = 1;
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
for ($i=0;$i<(24*60);$i+=15) {
	$arr[$i] = 0;
}
$events = array();

//start and end timestamps of day
$start=mktime(0,0,0,$_month,$_day,$_year);
$eind=mktime(0,0,0,$_month,$_day+1,$_year);
$c=1;

//normal appointments
$q = "select id,timestamp_start AS begin,timestamp_end AS eind,is_private AS prive,is_event from calendar where ((timestamp_start >= $start AND timestamp_end < $eind) AND (user_id = $_user)) AND (is_repeat=0 OR is_repeat IS NULL) ";
$res = sql_query($q);

while ($row = sql_fetch_array($res)) {
	$b_time = (date("H",$row["begin"])*60)+date("i",$row["begin"]);
	$e_time = (date("H",$row["eind"])*60)+date("i",$row["eind"]);
	
	// store the events.
	if ($row["is_event"]) {
		$events[] = $row["id"];
	}
	
	// for all other appoinments add it to the array...
	for ($i=$b_time;$i<$e_time;$i+=15) {
		$arr[$i] = $row["id"];
	}
}
//get time based on Jan 1 1970
#$start_day = date("z",$row["begin"]);
$start_day = date("z", 0);

//repeating items on daily interval.
//if weekend, don't show them
$weekdag = date("w",mktime(0,0,0,$_month,$_day,$_year));
if ($weekdag==0 || $weekdag == 6) {
	$qs = "AND is_repeat > 1";
} else {
	$qs = "AND is_repeat != 0";
}
$escape = sql_syntax("escape_char");
$q = "select subject,description,id,timestamp_start AS begin,timestamp_end AS eind,is_private AS prive,is_repeat AS ".$escape."repeat".$escape.",repeat_type, user_id, is_event from calendar where (user_id = $_user AND repeat_type = 'D' $qs)";
$res = sql_query($q);
while ($row = sql_fetch_array($res)) {
	/* by default ppl have permission */
	$row["permission"] = 1;
	/* wipe out subject and description for private appointments that are not for the logged in user */
	if (($_SESSION["user_id"] != $row["user_id"] && $row["prive"]) || $calperms == "") {
		$row["subject"]     = gettext("prive afspraak");
		$row["description"] = gettext("prive afspraak");
		$row["permission"]  = 0;
	}
	$flag=0;
	//see if we need this item today. Does not apply for daily items
	if ($row["repeat"]==1) {
		$flag=1;
	} else {
		//Is the item for today?
		$r["day"]		= date("z",$row["begin"]); //option base 0
		$r["val"]		= $row["repeat"];
		$r["diff"]	= 0;
		//get first and following places in raster
		$rt = mktime(0,0,0,1,$r["day"]+1,$_year);
		while ($rt < $start) {
			$r["diff"]+=$r["val"];
			$rt = mktime(0,0,0,1,$r["day"]+$r["diff"]+1,$_year);
		}
		//new day
		if ($rt>=$start && $rt<$eind) { //less than, cause day ends on 1 second before midnight
			$flag=1;
		}

	}
	if ($flag==1) {
		//calculate time in grid
		$b_time = (date("H",$row["begin"])*60)+date("i",$row["begin"]);
		$e_time =	(date("H",$row["eind"])*60)+date("i",$row["eind"]);
	
		// store the events.
		if ($row["is_event"]) {
			$events[] = $row["id"];
		}

		// Mark the timespan for all appointments other than events.
		for ($i=$b_time;$i<$e_time;$i+=15){
			//check if there's a normal item here.
			if ($arr[$i]==0)	{
				$arr[$i] = $row["id"];
			}
		}
	}//end flag
}
//repeating items on month/year interval (month=1,year=12)
$q = "select id,timestamp_start AS begin,timestamp_end AS eind,is_private AS prive,repeat_type from calendar where (user_id = $_user AND (repeat_type = 'M' OR repeat_type = 'Y'))";
$res = sql_query($q);
while ($row = sql_fetch_array($res)) {
	$r["day"]		= date("j",$row["begin"]);
	$r["month"] = (int)date("m",$row["begin"]);
	$r["year"]	= date("Y",$row["begin"]);
	if ($row["repeat_type"]=="M") {
		$r["val"]	= 1; //month
	} else {
		$r["val"]	= 12; //year
	}
	$r["diff"]	= 0;
	$r["prive"]	= $row["prive"];
	//calculate first and following items in grid
	$rt = mktime(0,0,0,$r["month"],$r["day"],$r["year"]);
	while ($rt < $start) {
		$r["diff"]+=$r["val"];
		$rt = mktime(0,0,0,$r["month"]+$r["diff"],$r["day"],$r["year"]);
	}
	if ($rt>=$start && $rt<$eind) {
		$flag = 1;
	} else {
		$flag = 0;
	}
	if ($flag==1) {
		//calculate time in raster
		$b_time = (date("H",$row["begin"])*60)+date("i",$row["begin"]);
		$e_time =	(date("H",$row["eind"])*60)+date("i",$row["eind"]);
		
		// store the events.
		if ($row["is_event"]) {
			$events[] = $row["id"];
		}

		// all other items...
		for ($i=$b_time;$i<$e_time;$i+=15) {
			if ($arr[$i]==0){
				$arr[$i] = $row["id"];
			}
		}
	}//end flag
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

if (count($this->calendar_items)==0){
	$counter = 1;
}else{
	$counter++;
}
if ($calperms == "") {
	$ids = 0;
}

//get the data from the database
$q = "select * from calendar where id IN ($ids) ORDER BY timestamp_start";
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
	if ($row["is_event"] == 1) {
		$this->calendar_items[$p]["human_span_short"]= "";   // weekview for example does not need any text 
		$this->calendar_items[$p]["human_span_long"] = gettext("Event")."\n";   // for dayview for example, its better to have this..
		$this->calendar_items[$p]["shuman"]          = "";
		$this->calendar_items[$p]["ehuman"]          = "";
	} else {
		$this->calendar_items[$p]["shuman"]          = strftime("%H:%M", $row["timestamp_start"]);
		$this->calendar_items[$p]["ehuman"]          = strftime("%H:%M", $row["timestamp_end"]);
		$this->calendar_items[$p]["human_span_short"] = $this->calendar_items[$p]["shuman"]."-".$this->calendar_items[$p]["ehuman"]."\n";
		$this->calendar_items[$p]["human_span_long"] = $this->calendar_items[$p]["shuman"]."-".$this->calendar_items[$p]["ehuman"]."\n";
	}
	$this->calendar_items[$p]["location"]      = $row["location"];
	$this->calendar_items[$p]["km"]            = $row["kilometers"];
	$this->calendar_items[$p]["relation"]      = (int)$row["address_id"];
	$this->calendar_items[$p]["relation_name"] = $address_data->getAddressNameById($row["address_id"]);
	$this->calendar_items[$p]["project_id"]    = (int)$row["project_id"];
	$this->calendar_items[$p]["project_name"]  = $project_data->getProjectNameById($row["project_id"]);
	$this->calendar_items[$p]["multirel"]      = $row["multirel"];
	$this->calendar_items[$p]["is_registered"] = $row["is_registered"];
	$this->calendar_items[$p]["show_actions"]  = ($row["is_registered"]==1?0:1);
	if ((int)$row["is_registered"] == 0 && (int)$row["project_id"])
		$this->calendar_items[$p]["show_reg"]  = 1;
	else
		$this->calendar_items[$p]["show_reg"]  = 0;
	$this->calendar_items[$p]["repeat_type"]   = $row["repeat_type"];
	$this->calendar_items[$p]["is_repeat"]     = $row["is_repeat"];
	if (!$row["is_repeat"]){
		unset($this->calendar_items[$p]["is_repeat"]);
	}
	$this->calendar_items[$p]["important"]       = $row["is_important"];
	/* wipe out contert for private appointments that do not belong to loggedin user */
	if (($_SESSION["user_id"] != $row["user_id"] && $row["is_private"]) || $calperms == "") {
		$this->calendar_items[$p]["subject"]         = gettext("prive afspraak");
		$this->calendar_items[$p]["body"]            = gettext("prive afspraak");
		$this->calendar_items[$p]["permissions"]     = 0;
		$this->calendar_items[$p]["show_actions"]    = 0;
		/* to get this item shown in weekview, fake registered item */
		$this->calendar_items[$p]["is_registered"]   = 1;
	} else {
		if ($calperms == "RW" || $row["user_id"] == $_SESSION["user_id"]) {
			$this->calendar_items[$p]["permissions"]     = 1;
		} else {
			$this->calendar_items[$p]["permissions"]     = 0;
		}
		if (!trim($row["subject"])) {
			$this->calendar_items[$p]["subject"]     = substr($row["description"], 0, 40);
		} else {
			$this->calendar_items[$p]["subject"]     = $row["subject"];
		}
		$this->calendar_items[$p]["body"]            = $row["description"];
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
	$this->calendar_items[$p]["group_id"]        = $row["group_id"];
	$this->calendar_items[$p]["user_id"]         = $row["user_id"];
	$this->calendar_items[$p]["is_ill"]          = $row["is_ill"];
	$this->calendar_items[$p]["is_specialleave"] = $row["is_specialleave"];
	$this->calendar_items[$p]["is_holiday"]      = $row["is_holiday"];
	$this->calendar_items[$p]["modified"]        = (int)$row["modified"];
	$this->calendar_items[$p]["modified_by"]     = (int)$row["modified_by"];
	$this->calendar_items[$p]["h_modified"]      = date("d-m-Y H:i", (int)$row["modified"]);
	$this->calendar_items[$p]["h_modified_by"]   = $user_data->getUsernameById((int)$row["modified_by"]);
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
	$this->calendar_items[$p]["is_event"]          = $row["is_event"];

	$counter++;
}
//last record not used - option base 0
$counter--;
//recreate the array.
for ($i=0;$i<24*60;$i+=15) {
	for ($p=1;$p<=$counter;$p++) {
		if ($arr[$i]==$this->calendar_items[$p]["id"]) {
			$arr[$i]=$p;
		}
	}
}
//compress the array
if ($compress==1) {
	$comp[] = $arr[0];
	for($i=0;$i<24*60;$i+=15) {
		if ($arr[$i]!=$comp[count($comp)-1]) {
			$comp[] = $arr[$i];
		}
	}
	$arr=$comp; //put the compressed array back in the old array
} else {
	//calculate rowspan based on ammount of grid places
	for($i=1;$i<=$counter;$i++) {
		// check if we are editing the current user
		if ($this->calendar_items[$i]["user_id"]==$_user) {
			//tellen
			$this->calendar_items[$i]["rowspan"]=0; //option base 0
			for($x=0;$x<=24*60;$x+=15) {
				if ($arr[$x]==$i) {	//new pointers
					$this->calendar_items[$i]["rowspan"] += 1;
				}
			}
		}
	}
}
if ($calperms == "") {
	unset($arr);
	for ($i=(8*60); $i<=17*60; $i+=15) {
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
		"subject"       => gettext("geen rechten op deze agenda"),
		"body"          => gettext("geen rechten op deze agenda"),
		"rowspan"       => 36
	);
}

/* sort based on start time */
$sortorder = array();
if (is_array($this->calendar_items)) {
	foreach ($this->calendar_items as $k=>$v) {
		$sortorder[$k] = $v["shuman"];
	}
	natcasesort($sortorder);
	foreach ($sortorder as $newkey=>$temp) {
		$sortorder[$newkey] = $this->calendar_items[$newkey];
	}
	$this->calendar_items = $sortorder;
	unset($sortorder);
}
?>
