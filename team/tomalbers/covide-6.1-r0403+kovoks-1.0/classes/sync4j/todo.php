<?
Class Sync4j_todo {
	public function todo2covide($filename) {
		$sync4j_convert = new Sync4j_convert();
		$ary = $sync4j_convert->getXML($filename);
		$hash = md5_file($filename);

		$body 					= $ary["TASK"]["BODY"];
		$start["date"] 	= $ary["TASK"]["STARTDATE"];
		$end["date"] 		= $ary["TASK"]["DUEDATE"];
		$subject				= $ary["TASK"]["SUBJECT"];

		//format: 20050928T220000Z
		//tasks have a 2 hour + 1 day offset (?!)

		//format: 20050928T220000Z
		$start["year"]	= substr($start["date"],0,4);
		$start["month"]	= substr($start["date"],4,2);
		$start["day"]		= substr($start["date"],6,2);
		$start["hour"]	= substr($start["date"],9,2);
		$start["min"]		= substr($start["date"],11,2);

		$end["year"]	= substr($end["date"],0,4);
		$end["month"]	= substr($end["date"],4,2);
		$end["day"]		= substr($end["date"],6,2);
		$end["hour"]	= substr($end["date"],9,2);
		$end["min"]		= substr($end["date"],11,2);


		$start["ts"] = mktime($start["hour"],$start["min"],0,$start["month"],$start["day"],$start["year"]);
		$end["ts"] = mktime($end["hour"],$end["min"],0,$end["month"],$end["day"],$end["year"]);

		if ($start["ts"]<=mktime(0,0,0,1,1,1990)) {
			$start["ts"]=mktime(0,0,0,date("m"),date("d"),date("Y"));
		}
		if ($end["ts"]<=mktime(0,0,0,1,1,1990)) {
			$end["ts"] = $start["ts"];
		}
		$ret = array(
			"subject"=>$subject,
			"body"=>$body,
			"start"=>$start["ts"],
			"end"=>$end["ts"],
			"hash"=>$hash
		);

		return $ret;
	}

	public function todo2sync($subject, $body, $start, $end) {
		if ($busystatus<0) $busystatus = 0;

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$xml.= "<task>";
		$xml.= "<DateCompleted></DateCompleted>";
		$xml.= "<Companies></Companies>";
		$xml.= "<Status>0</Status>";
		$xml.= "<PercentComplete>0</PercentComplete>";
		$xml.= "<RecurrenceType>1</RecurrenceType>";
		$xml.= "<IsRecurring>0</IsRecurring>";
		$xml.= "<Occurrences>10</Occurrences>";
		$xml.= "<PatternEndDate></PatternEndDate>";
		$xml.= "<BillingInformation></BillingInformation>";

		$xml.= "<Subject>$subject</Subject>";
		$xml.= "<DayOfMonth>0</DayOfMonth>";
		$xml.= "<ReminderSet>0</ReminderSet>";
		$xml.= "<TotalWork>0</TotalWork>";
		$xml.= "<MonthOfYear>0</MonthOfYear>";
		$xml.= "<Complete>0</Complete>";
		$xml.= "<DayOfWeekMask>32</DayOfWeekMask>";
		$xml.= "<Mileage></Mileage>";

		//start datetime recurring
		if ($start) {
			$date_start = strftime("%Y%m%dT220000Z", $start);
		} else {
			$date_start = "19990101T010000Z";
		}
		if ($end) {
			$date_end = strftime("%Y%m%dT220000Z", $end);
		} else {
			$date_end = "19990101T010000Z";
		}

		$xml.= "<PatternStartDate>$date_start</PatternStartDate>";
		$xml.= "<Instance>0</Instance>";
		$xml.= "<Sensitivity>0</Sensitivity>";
		$xml.= "<Importance>1</Importance>";
		$xml.= "<StartDate>$date_start</StartDate>";
		$xml.= "<ActualWork>0</ActualWork>";

		$xml.= "<Body>$body</Body>";
		$xml.= "<TeamTask>0</TeamTask>";
		$xml.= "<ReminderTime></ReminderTime>";
		$xml.= "<Interval>1</Interval>";
		$xml.= "<Categories></Categories>";
		$xml.= "<DueDate>$date_end</DueDate>";
		$xml.= "<NoEndDate>0</NoEndDate>";
		$xml.= "</task>";

		return $xml;
	}
}
?>
