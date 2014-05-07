<?php
Class Sync4j_calendar {
	public function calendar2covide($filename) {
		$sync4j_convert = new Sync4j_convert();
		$ary = $sync4j_convert->getXML($filename);
		$hash = md5_file($filename);

		$body 					= $ary["APPOINTMENT"]["BODY"];
		$subject       = $ary["APPOINTMENT"]["SUBJECT"];
		$start["date"] 	= $ary["APPOINTMENT"]["START"];
		$end["date"] 		= $ary["APPOINTMENT"]["END"];
		$location				= $ary["APPOINTMENT"]["LOCATION"];
		$reminderset		= $ary["APPOINTMENT"]["REMINDERSET"];
		$remindermin		= $ary["APPOINTMENT"]["REMINDERMINUTESBEFORESTART"];

		//format: 20050928T220000Z
		$start["year"]	= substr($start["date"],0,4);
		$start["month"]	= substr($start["date"],4,2);
		$start["day"]	= substr($start["date"],6,2);
		$start["hour"]	= substr($start["date"],9,2);
		$start["min"]	= substr($start["date"],11,2);

		$end["year"]	= substr($end["date"],0,4);
		$end["month"]	= substr($end["date"],4,2);
		$end["day"]		= substr($end["date"],6,2);
		$end["hour"]	= substr($end["date"],9,2);
		$end["min"]		= substr($end["date"],11,2);

		$start["ts"] = mktime($start["hour"],$start["min"],0,$start["month"],$start["day"],$start["year"]);
		$end["ts"] = mktime($end["hour"],$end["min"],0,$end["month"],$end["day"],$end["year"]);

		$ret = array(
			"subject"=>$subject,
			"body"=>$body,
			"start"=>$start["ts"],
			"end"=>$end["ts"],
			"location"=>$location,
			"reminderset"=>$reminderset,
			"remindermin"=>$remindermin,
			"hash"=>$hash
		);
		return $ret;
	}

	public function calendar2sync($subject, $body, $start, $end, $location="", $busystatus=2, $remindermin=15, $reminderset=0) {
		if ($busystatus<0) $busystatus = 0;

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$xml.= "<appointment>";
		$xml.= "<Companies></Companies>";
		$xml.= "<RecurrenceType>1</RecurrenceType>";
		$xml.= "<IsRecurring>0</IsRecurring>";
		$xml.= "<Occurrences>10</Occurrences>";
		$xml.= "<PatternEndDate></PatternEndDate>";
		$xml.= "<MeetingStatus>0</MeetingStatus>";
		$xml.= "<BillingInformation></BillingInformation>";

		//subject
		$xml.= "<Subject>$subject</Subject>";

		$xml.= "<DayOfMonth>0</DayOfMonth>";
		$xml.= "<ReminderSet>$reminderset</ReminderSet>";
		$xml.= "<MonthOfYear>0</MonthOfYear>";
		$xml.= "<DayOfWeekMask>0</DayOfWeekMask>";
		$xml.= "<Mileage></Mileage>";

		//start datetime recurring
		if ($start) {
			$date_start = strftime("%Y%m%dT%H%M%SZ", $start);
		} else {
			$date_start = "19990101T010000Z";
		}
		if ($end) {
			$date_end = strftime("%Y%m%dT%H%M%SZ", $end);
		} else {
			$date_end = "19990101T010000Z";
		}

		$xml.= "<PatternStartDate>$date_start</PatternStartDate>";
		$xml.= "<Instance>0</Instance>";
		$xml.= "<BusyStatus>$busystatus</BusyStatus>";
		$xml.= "<Sensitivity>0</Sensitivity>";
		$xml.= "<ReplyTime></ReplyTime>";
		$xml.= "<Importance>1</Importance>";

		//end time
		$xml.= "<End>$date_end</End>";

		//body
		$xml.= "<Body>$body</Body>";
		$xml.= "<ReminderMinutesBeforeStart>$remindermin</ReminderMinutesBeforeStart>";

		//start dateimte
		$xml.= "<Start>$date_start</Start>";

		$xml.= "<Interval>1</Interval>";
		$xml.= "<Categories></Categories>";
		$xml.= "<AllDayEvent>0</AllDayEvent>";
		$xml.= "<Location>$location</Location>";
		$xml.= "<NoEndDate>1</NoEndDate>";
		$xml.= "</appointment>";

		return $xml;
	}
}
?>
