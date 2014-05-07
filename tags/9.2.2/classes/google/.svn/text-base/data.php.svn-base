<?php
/**
 * Covide Google Apps module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Google_data {
	/* constants */
	const include_dir      = "classes/google/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name       = "google";

	/* variables */
	/**
	 * @var string uri for single login
	 */
	private $google_login   = "https://www.google.com/accounts/ClientLogin";
	/**
	 * @var string uri for document listing
	 */
	private $google_docs    = "https://docs.google.com/feeds/documents/private/full";
	/**
	 * @var string uri for calendar items listing
	 */
	private $google_calendar = "http://www.google.com/calendar/feeds/default/private/full";
	/**
	 * @var string uris for document export in .doc
	 */
	private $google_export_doc = "https://docs.google.com/MiscCommands?command=saveasdoc&exportformat=%s&hl=en&docID=%s";
	/**
	 * @var string uri for document export in .zip
	 */
	private $google_export_zip = "https://docs.google.com/UserMiscCommands?command=saveaszip&hl=en&docID=%s";
	/**
	 * @var string uri for spreadsheet export in .xls
	 */
	private $google_export_xls = "https://spreadsheets.google.com/fm?fmcmd=%d&hl=en&key=%s";
	/**
	 * @var string uri for spreadsheet worksheet export in .xls
	 */
	private $google_export_xls_worksheet = "http://spreadsheets.google.com/feeds/worksheets/%s/private/full";
	/**
	 * @var string uri for spreadsheet cellbased export in .xls
	 */
	private $google_export_xls_cellbased = "http://spreadsheets.google.com/feeds/cells/%s/%s/private/full";
	/**
	 * @var string uri for spreadsheet rowbased export in .xls
	 */
	private $google_export_xls_rowbased  = "http://spreadsheets.google.com/feeds/list/%s/%s/private/full";
	/* internal vars */
	private $google = array();
	private $cache  = array();
	private $token;
	private $debug = 0;
	private $debug_file = "/tmp/google.xml";
	private $use_sessions = 0; //downloads not implemented
	private $user_data;

	/* methods */
	/* __construct {{{  */
	public function __construct() {
		$this->user_data = new User_data();
		/* debug handler */
		if ($this->debug && file_exists($this->debug_file) && !$GLOBALS["google"]) {
			unlink($this->debug_file);
		}
		/* fill the cache */
		$this->cache =& $GLOBALS["google"]["cache"];

		/* if google session token is set */
		if ($_SESSION["google_id"] && $this->use_sessions)
			$this->token =& $_SESSION["google_id"];
	}
	/* }}} */
	/* getGoogleFolders {{{ */
	/**
	 * Get a list of folders in Google Docs
	 *
	 * @param string $current_folder Current selected google folder, should be prepended with g_
	 * @param string $subaction if set to 'add_attachment' adds extra dropdown actions for attaching google docs to email
	 *
	 * @return array folders
	 */
	public function getGoogleFolders($current_folder, $subaction="") {
		/* retrieve google folders */
		$ret     = $this->getGoogleDocList("", "", $subaction);
		$folders = array();

		if (preg_match("/^g_/s", $current_folder)) {
			return array();
		} else {
			foreach ($ret as $r) {
				if (!$r["google_folder"]) {
					$k = 0;
					$folders[$k]["name"]        = gettext("items not in folders");
					$folders[$k]["foldericon"]  = "folder_my_docs";
					$folders[$k]["description"] = gettext("default folder");
				} else {
					$k = base64_encode($r["google_folder"]);
					$folders[$k]["name"]        = $r["google_folder"];
					$folders[$k]["foldericon"]  = "folder_lock";
					$folders[$k]["description"] = gettext("google folder");
				}
				$folders[$k]["id"]         = sprintf("g_%s", $k);
				$folders[$k]["h_name"]      = $folders[$k]["name"];
				$folders[$k]["foldercount"] = 0;
				$folders[$k]["parent_id"]   = $parent_id;
				$folders[$k]["allow"]       = 1;
				$folders[$k]["filecount"]++;
			}
			natcasesort($folders);
			return $folders;
		}
	}
	/* }}} */
	/* descr2Ftype {{{ */
	/**
	 * Returns the first 3 characters of given parameter
	 *
	 * @todo find out why we need this
	 *
	 * @param string $str input
	 *
	 * @return string the first 3 characters of the given string
	 */
	private function descr2Ftype($str) {
		return substr($str, 1, 3);
	}
	/* }}} */
	/* getGoogleDocList {{{ */
	/**
	 * Get list of google docs
	 *
	 * @param string $current_folder If specified, only look for items in this folder
	 * @param string $file_search If specified, limit list of files that match this string
	 * @param string $subaction If set to 'add_attachment' show extra options in file dropdown to attach file to an email
	 *
	 * @return array The files found
	 */
	public function getGoogleDocList($current_folder=0, $file_search="", $subaction="") {
		/* if no folder was requested */
		if (!preg_match("/^g_/s", $current_folder) && $current_folder)
			return array();

		if ($this->cache["data"]) {
			$data = $this->cache["data"];
			if (preg_match("/^g_/s", $current_folder))
				$gfolder = base64_decode(preg_replace("/^g_/s", "", $current_folder));

			foreach ($data as $k=>$v) {
				/* filter documents */
				if ($gfolder && $gfolder != $v["google_folder"])
					unset($data[$k]);
				elseif ($current_folder == "g_0" && $v["google_folder"])
					unset($data[$k]);
				elseif ($file_search) {
					if (preg_match("/^[a-z]\*$/si", $file_search)) {

						$regex = "/^".strtolower(substr($file_search, 0, 1))."/si";
						if (!preg_match($regex, $v["name"]))
							unset($data[$k]);

					} elseif (!stristr($v["name"], $file_search)) {
						unset($data[$k]);
					}
				}
			}
			return $data;
		}
		$user_data =& $this->user_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
		$fs_data   = new Filesys_data();
		$output    = new Layout_output();

		$service = Zend_Gdata_Docs::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($user_info["google_username"], $user_info["google_password"], $service);

		$docs = new Zend_Gdata_Docs($client);
		$feed = $docs->getDocumentListFeed();
		$data = array();
		foreach ($feed->entries as $entry) {
			$ext = $entry->getExtensionElements();
			foreach($ext as $v) {
				if ($v->rootElement == 'resourceId') {
					$id = $v->getText();
				}
			}
			$doc = array();
			$a = $entry->getAuthor();
			$c = $entry->getCategory();
			//$id = $entry->getId()->getText();
			$link = $entry->getEditLink()->getHref();
			$update = $entry->getUpdated()->getText();
			foreach ($c as $cat) {
				$term = $cat->getTerm();
				$label = $cat->getLabel();
				if ($term == $label) {
					$doc["google_folder"] = $label;
				} else {
					switch($label) {
					case "document":
						$doc["type"] = "application/msword";
						break;
					case "spreadsheet":
						$doc["type"] = "application/vnd.ms-excel";
						break;
					case "presentation":
						$doc["type"] = "application/mspowerpoint";
						break;
					}
				}
			}
			$doc["name"] = $entry->title->getText();
			$doc["description"] = $a[0]->getName()->getText()." - ".$a[0]->getEmail()->getText();
			$doc["edit_url"] = $link;
			$doc["google_id"]  = explode(":", urldecode($id));
			$doc["google_id"]  = $doc["google_id"][count($doc["google_id"])-1];
			$doc["google_md5"] = md5($doc["google_id"]);
			$doc["timestamp"] = strtotime($update);

			/* guess export formats, this is not documented at all! */
			switch ($doc["type"]) {
				case "application/msword":
					$doc["name"]         .= ".doc";
					$doc["fileicon"]      = "ftype_doc";
					$doc["export"][sprintf(".doc (%s)", gettext("microsoft word"))]     = sprintf($this->google_export_doc, "doc", $doc["google_id"]);
					$doc["export"][sprintf(".pdf (%s)", gettext("pdf document"))]       = sprintf($this->google_export_doc, "pdf", $doc["google_id"]);
					$doc["export"][sprintf(".odt (%s)", gettext("open office writer"))] = sprintf($this->google_export_doc, "oo",  $doc["google_id"]);
					$doc["export"][sprintf(".zip (%s)", gettext("zip file with html"))] = sprintf($this->google_export_zip, $doc["google_id"]);
					$doc["allow_attach"] = 1;
					break;
				case "application/vnd.ms-excel":
					$doc["name"]         .= ".xls";
					$doc["fileicon"]      = "ftype_calc";
					$doc["export"][sprintf(".xls (%s)", gettext("microsoft excel"))]  = sprintf($this->google_export_xls, "4",  $doc["google_id"]);
					$doc["export"][sprintf(".ods (%s)", gettext("open office calc"))] = sprintf($this->google_export_xls, "13", $doc["google_id"]);
					$doc["export"][sprintf(".pdf (%s)", gettext("pdf document"))]     = sprintf($this->google_export_xls, "12", $doc["google_id"]);
					$doc["allow_attach"] = 2;
					break;
				case "application/mspowerpoint":
					$doc["name"]         .= ".ppt";
					$doc["fileicon"]      = "ftype_ppt";
					$doc["export"][sprintf(".zip (%s)", gettext("zip file with html"))] = sprintf($this->google_export_zip, $doc["google_id"]);
					$doc["allow_attach"] = 1;
					unset($doc["data_url"]);
					break;
				default:
					$doc["name"] .= ".unknown";
					unset($doc["data_url"]);
					break;
			}
			if (is_array($doc["export"])) {
				$sel = array("0" => gettext("choose action"));
				foreach ($doc["export"] as $ek=>$ev) {
					$ev .= sprintf("|%s", $doc["name"]); //add name to string
					$sel[gettext("download as")][sprintf("dl:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("%s", $ek);
				}
				if ($subaction == "add_attachment" && $doc["allow_attach"] == 1) {
					foreach ($doc["export"] as $ek=>$ev) {
						$ev .= sprintf("|%s", $doc["name"]); //add name to string
						$sel[gettext("attach as")][sprintf("att:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("+ %s", $ek);
					}
				} elseif ($subaction == "add_attachment" && $doc["allow_attach"] == 2) {
					$ev = end($doc["export"]);
					$ek = sprintf(".csv (%s)", gettext("csv file(s)"));

					$ev .= sprintf("|%s", $doc["name"]); //add name to string
					$sel[gettext("attach as")][sprintf("att:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("+ %s", $ek);
				}
				$output->addSelectField(sprintf("g_%s", $doc["google_md5"]), $sel, "", "", array(
					"style" => "width: 170px;"
				));
				$output->start_javascript();
					$output->addCode(sprintf("document.getElementById('g_%1\$s').onchange = function() { googleAction(document.getElementById('g_%1\$s')); }",
						$doc["google_md5"]));
				$output->end_javascript();

				$doc["selectbox"] = $output->generate_output();
			}
			$doc["size"]         = 0;
			$doc["user_id"]      = $_SESSION["user_id"];
			$doc["username"]     = $user_data->getUsernameById($doc["user_id"]);
			$doc["user_name"]    = $doc["username"];
			$doc["date_human"]   = date("d-m-Y H:i", $doc["timestamp"]);
			$doc["show_google_actions"] = 1;
			if ($doc["google_folder"]) {
				$doc["folder_name"]  = $doc["google_folder"];
				$doc["folder_id"]    = sprintf("g_%s", urlencode(base64_encode($doc["folder_name"])));
			} else {
				$doc["folder_name"]  = gettext("items not in folders");
				$doc["folder_id"]    = $fs_data->getGoogleFolders();
			}
			$doc = $fs_data->detect_preview($doc);
			$doc["subview"] = 0;

			$data[$doc["google_id"]] = $doc;
		}
		ksort($data);

		$this->cache["data"] = $data;
		return $data;
	}
	/* }}} */
	/* _getGoogleId {{{ */
	/**
	 * Function to grab the resourceId of a google item
	 *
	 * @param Gdata_Entry $item The item to grab the ID for
	 *
	 * @return string The google ID
	 */
	private function _getGoogleId($item) {
			$ext = $item->getExtensionElements();
			//var_dump($ext);
			foreach($ext as $v) {
				if ($v->rootElement == "resourceId") {
					$id = $v->getText();
				}
				if ($v->rootElement == "uid") {
					$id = $v->extensionAttributes["value"]["value"];
				}
			}
			$id = str_replace("@google.com", "", $id);
			return $id;
	}
	/* }}} */
	/* getGoogleCalendar {{{ */
	/**
	 * Get google calendar items for the given user
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param array $options Optional array with options to limit the results
	 *
	 * @return array Google calendar items
	 */
	public function getGoogleCalendar($username, $password, $options = array()) {
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
		$service = new Zend_Gdata_Calendar($client);

		$query = $service->newEventQuery();
		$query->setUser("default");
		$query->setVisibility("private");
		$query->setProjection("full");
		$query->setOrderBy("starttime");
		$query->setFutureevents("true");

		try {
			$eventfeed = $service->getCalendarEventFeed($query);
		} catch (Zend_Gdata_App_HttpException $e) {
			$data = array();
			return $data;
		}
		$data = array();
		foreach ($eventfeed as $event) {
			$calitem = array();
			if ($event->recurrence && $event->recurrence->text) {
				//parse the recurrence data
				$recuritems = explode("\n", $event->recurrence->text);
				foreach($recuritems as $v) {
					if (strpos($v, "DTSTART") !== false) {
						//assume: DTSTART:YYYYmmddTHHmmiiZ
						$startdate = str_replace("DTSTART:", "", $v);
						$calitem["timestamp_start"] = strtotime($startdate);
					}
					if (strpos($v, "DTEND") !== false) {
						//assume: DTEND:YYYYmmddTHHmmiiZ
						$enddate = str_replace("DTEND:", "", $v);
						$calitem["timestamp_end"] = strtotime($enddate);
					}

				}
			} else {
				$calitem["timestamp_start"] = strtotime($event->when[0]->startTime);
				$calitem["timestamp_end"] = strtotime($event->when[0]->endTime);
			}
			$calitem["subject"] = $event->title->text;
			$calitem["google_id"] = $this->_getGoogleId($event);
			$calitem["google_url"] = $event->id->text;

			$calitem["description"] = $event->content->text;
			$calitem["location"] = $event->where[0]->valueString;
			$data[$calitem["google_id"]] = $calitem;
		}
		return $data;
	}
	/* }}} */
	/* createGoogleCalendarItem {{{ */
	/**
	 * Create an item on the Google calendar service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param array $data The calendar data
	 *
	 * @return string The google id of the new item
	 */
	public function createGoogleCalendarItem($username, $password, $data) {
		$recurrence = "";
		if ($data["is_repeat"] == 1) {
			if ($data["is_event"] == 1) {
				$dtstart = date("Ymd", $data["timestamp_start"]);
				$dtend   = date("Ymd", $data["timestamp_end"]);
			} else {
				$dtstart = date("Ymd\THis\Z", ($data["timestamp_start"]-date("Z")));
				$dtend   = date("Ymd\THis\Z", ($data["timestamp_end"]-date("Z")));
			}
			if ($data["repeat_use_end_date"] == 1) {
				$until = sprintf(";UNTIL=%s%02s%02s", $data["repeat_end_year"], $data["repeat_end_month"], $data["repeat_end_day"]);
			} else {
				$until = "";
			}
			$recurrence = sprintf("DTSTART:%s\r\nDTEND:%s\r\nRRULE:FREQ=%%s%s\r\n", $dtstart, $dtend, $until);
			/* repeat types:
			 * 1 = daily
			 * 2 = weekly
			 * 3 =
			 * 4 =
			 * 5 = monthly on date
			 * 6 = yearly
			 */
			switch($data["repeat_type"]) {
			case 1:
				$recurrence = sprintf($recurrence, "DAILY");
				break;
			case 2:
				$recurrence = sprintf($recurrence, "WEEKLY");
				break;
			case 3:
			case 4:
			case 5:
				$recurrence = sprintf($recurrence, "MONTHLY");
				break;
			case 6:
				$recurrence = sprintf($recurrence, "YEARLY");
				break;
			default:
				$recurrence = "";
				break;
			}
		}
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
		$service = new Zend_Gdata_Calendar($client);

		try {
			$event = $service->newEventEntry();
		} catch (Zend_Gdata_App_HttpException $e) {
			return 0;
		}
		$event->title = $service->newTitle($data["subject"]);
		$event->where = array($service->newWhere($data["location"]));
		$conversion = new Layout_conversion();
		$event->content = $service->newContent($conversion->html2text($data["description"]));
		if ($data["is_repeat"] == 1 && $recurrence != "") {
			$event->recurrence = $service->newRecurrence($recurrence);
		} else {
			$when = $service->newWhen();
			if ($data["is_event"] == 1) {
				$when->startTime = date("Y-m-d", $data["timestamp_start"]);
				$when->endTime = date("Y-m-d", $data["timestamp_start"]);
			} else {
				$when->startTime = date("c", $data["timestamp_start"]);
				$when->endTime = date("c", $data["timestamp_end"]);
			}
			$event->when = array($when);
		}
		try {
			$newEvent = $service->insertEvent($event);
		} catch (Zend_Gdata_App_HttpException $e) {
			return 0;
		}
		return $this->_getGoogleId($newEvent);
	}
	/* }}} */
	/* updateGoogleCalendarItem {{{ */
	/**
	 * Update a specific calendar item on the Google calendar service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param string $id Google calendar item id
	 * @param array $data The new calendar data
	 *
	 * @return bool true on success, false on failure
	 */
	public function updateGoogleCalendarItem($username, $password, $id, $data) {
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
		$service = new Zend_Gdata_Calendar($client);

		try {
			$event = $service->getCalendarEventEntry(sprintf("http://www.google.com/calendar/feeds/default/private/full/%s", $id));
		} catch (Zend_Gdata_App_HttpException $e) {
			return false;
		}
		$event->title = $service->newTitle($data["subject"]);
		$event->where = array($service->newWhere($data["location"]));
		$conversion = new Layout_conversion();
		$event->content = $service->newContent($conversion->html2text($data["description"]));
		$when = $service->newWhen();
		if ($data["is_event"] == 1) {
			$when->startTime = date("Y-m-d", $data["timestamp_start"]);
			$when->endTime = date("Y-m-d", $data["timestamp_start"]);
		} else {
			$when->startTime = date("c", $data["timestamp_start"]);
			$when->endTime = date("c", $data["timestamp_end"]);
		}
		$event->when = array($when);
		$event->save();
		return true;
	}
	/* }}} */
	/* deleteGoogleCalendarItem {{{ */
	/**
	 * Remove a specific calendar item from the Google calendar service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param string $id Google calendar item id
	 *
	 * @return bool true on success, false on failure
	 */
	public function deleteGoogleCalendarItem($username, $password, $id) {
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
		$service = new Zend_Gdata_Calendar($client);

		try {
			$event = $service->getCalendarEventEntry(sprintf("http://www.google.com/calendar/feeds/default/private/full/%s", $id));
		} catch (Zend_Gdata_App_HttpException $e) {
			return false;
		}
		$event->delete();
		return true;
	}
	/* }}} */
	/* syncGoogleCalendar {{{ */
	/**
	 * Grab items from google and insert/update covide database
	 *
	 * @param int $user_id The covide user_id
	 * @param string $username Google account username
	 * @param string $password Google account password
	 *
	 * @return void
	 */
	public function syncGoogleCalendar($user_id, $username, $password) {
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
		$service = new Zend_Gdata_Calendar($client);
		// grab all google_id's from users calendar
		$sql = sprintf("SELECT calendar_id, google_id FROM calendar_user right join calendar on calendar.id = calendar_user.calendar_id WHERE timestamp_start >= %d AND user_id = %d AND google_id != '0'", (time()-86400), $user_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			try {
				$event = $service->getCalendarEventEntry(sprintf("http://www.google.com/calendar/feeds/default/private/full/%s", $row["google_id"]));
			} catch (Zend_Gdata_App_HttpException $e) {
				$http_code = $e->getResponse()->getStatus();
				if ($http_code == 404) {
					//delete the appointment
					$calendar_data = new Calendar_data();
					$calendar_data->delete($row["calendar_id"], $user_id);
				} else {
					continue;
				}
			}
		}
		$cal = $this->getGoogleCalendar($username, $password);
		foreach ($cal as $v) {
			//check if we already have this one, update
			$sql = sprintf("SELECT calendar_id FROM calendar_user WHERE user_id = %d AND google_id = '%s'", $user_id, $v["google_id"]);
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				//yes we have, update
				$sql = sprintf("UPDATE calendar SET subject = '%s', body = '%s', location = '%s', timestamp_start = %d, timestamp_end = %d WHERE id = %d",
					$v["subject"], addslashes(nl2br($v["description"])), $v["location"], $v["timestamp_start"], $v["timestamp_end"], sql_result($res, 0));
				$res = sql_query($sql);
			} else {
				//no we haven't, insert
				$sql = sprintf("INSERT INTO calendar (subject, body, location, timestamp_start, timestamp_end) VALUES ('%s', '%s', '%s', %d, %d)",
					$v["subject"], nl2br($v["description"]), $v["location"], $v["timestamp_start"], $v["timestamp_end"]);
				$res = sql_query($sql);
				$calid = sql_insert_id("calendar");
				$sql = sprintf("INSERT INTO calendar_user (user_id, calendar_id, google_id, status) VALUES (%d, %d, '%s', 1)",
					$user_id, $calid, $v["google_id"]);
				$res = sql_query($sql);
			}
		}
		// now sync the items that are in covide but not yet in google
		$sql = sprintf("SELECT calendar.subject, calendar.body, calendar.location, calendar.timestamp_start, calendar.timestamp_end, calendar_user.calendar_id FROM calendar_user RIGHT JOIN calendar ON calendar.id = calendar_user.calendar_id WHERE user_id = %d AND timestamp_start >= %d AND (google_id = '0' OR google_id IS NULL)", $user_id, (time()-(86400*7)));
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$event_id = $this->createGoogleCalendarItem($username, $password, $row);
			$q = sprintf("UPDATE calendar_user SET google_id = '%s' WHERE user_id = %d AND calendar_id = %d", $event_id, $user_id, $row["calendar_id"]);
			$r = sql_query($q);
		}
	}
	/* }}} */
	/* getGoogleContactGroups {{{ */
	/**
	 * Get the google contact groups
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 *
	 * @return array key of array is groupid and value is groupname
	 */
	public function getGoogleContactGroups($username, $password) {
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, "cp");
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);
		$groupquery = new Zend_Gdata_Query("http://www.google.com/m8/feeds/groups/default/full");
		$groupfeed = $gdata->retrieveAllEntriesForFeed($gdata->getFeed($groupquery));
		$groups = array();
		foreach ($groupfeed as $groupentry) {
			$groups[$groupentry->id->text] = $groupentry->title->text;
		}
		return $groups;
	}
	/* }}} */
	/* getGoogleContacts {{{ */
	/**
	 * get contacts stored on google server
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param array $options Optional array with options to limit the results
	 *
	 * @return array Google contacts items
	 */
	public function getGoogleContacts($username, $password, $options = array()) {
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, "cp");
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);
		$query = new Zend_Gdata_Query("http://www.google.com/m8/feeds/contacts/default/full");
		$feed = $gdata->getFeed($query);

		$contacts = array();

		foreach ($feed as $entry) {
			$xml = simplexml_load_string($entry->getXML());
			$id = $entry->id->text;
			$id = $entry->getEditLink()->href;
			$name = (string) $entry->title;
			$companyname = (string) $xml->organization->orgName;
			$jobtitle = (string) $xml->organization->orgTitle;
			foreach ($xml->email as $v) {
				if (strpos($v["rel"], "work") !== false) {
					$business_email = (string) $v["address"];
				}
				if (strpos($v["rel"], "home") !== false) {
					$personal_email = (string) $v["address"];
				}
			}
			foreach ($xml->phoneNumber as $k => $v) {
				if (strpos($v["rel"], "work") !== false) {
					$business_phone_nr = (string) $v;
				}
				if (strpos($v["rel"], "work") !== false) {
					$personal_phone_nr = (string) $v;
				}
			}
			foreach ($xml->postalAddress as $k => $v) {
				//For now we only know how to parse this:
				//Street + number\nzipcode\ncity\noptional country
				$aparts = explode("\n", (string) $v);
				if (count($aparts) >= 3) {
					$address = $aparts[0];
					$zipcode = $aparts[1];
					$city = $aparts[2];
					$country = $aparts[3];
				} else {
					$address = (string) $v;
				}
				if (strpos($v["rel"], "work") !== false) {
					$business_address = $address;
					$business_zipcode = $zipcode;
					$business_city = $city;
					$business_country = $counry;
				}
				if (strpos($v["rel"], "home") !== false) {
					$personal_address = $address;
					$personal_zipcode = $zipcode;
					$personal_city = $city;
					$personal_country = $counry;
				}
			}
			$contacts[$id] = array(
				"google_id" => $id,
				"name" => $name,
				"companyname" => $companyname,
				"jobtitle" => $jobtitle,
				"business_address" => $business_address,
				"business_zipcode" => $business_zipcode,
				"business_city" => $business_city,
				"business_country" => $business_country,
				"business_email" => $business_email,
				"business_phone_nr" => $business_phone_nr,
				"personal_address" => $personal_address,
				"personal_zipcode" => $personal_zipcode,
				"personal_city" => $personal_city,
				"personal_country" => $personal_country,
				"personal_email" => $personal_email,
				"personal_phone_nr" => $personal_phone_nr,
			);
		}
		return $contacts;
	}
	/* }}} */
	/* createGoogleContact {{{ */
	/**
	 * Create an item on the Google contacts service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param array $data The contact data
	 *
	 * @return string The google id of the new item
	 */
	public function createGoogleContact($username, $password, $data) {
		//get contact groups
		$contactgroups = $this->getGoogleContactGroups($username, $password);
		$mycontacts = array_search("System Group: My Contacts", $contactgroups);

		// perform login and set protocol version to 3.0
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, 'cp');
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		// create new entry
		$doc  = new DOMDocument();
		$doc->formatOutput = true;
		$entry = $doc->createElement("atom:entry");
		$entry->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:atom", "http://www.w3.org/2005/Atom");
		$entry->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:gd", "http://schemas.google.com/g/2005");
		$entry->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:gContact", "http://schemas.google.com/contact/2008");
		$doc->appendChild($entry);

		$cat = $doc->createElement("category");
		$cat->setAttribute("scheme", "http://schemas.google.com/g/2005#kind");
		$cat->setAttribute("term", "http://schemas.google.com/contact/2008#contact");
		$entry->appendChild($cat);

		// add name element
		$name = $doc->createElement("gd:name");
		$entry->appendChild($name);
		$fullName = $doc->createElement("gd:fullName", $data["fullname"]);
		$name->appendChild($fullName);

		if ($data["business_email"]) {
			// add email element
			$email = $doc->createElement("gd:email");
			$email->setAttribute("address", $data["business_email"]);
			$email->setAttribute("rel", "http://schemas.google.com/g/2005#work");
			$entry->appendChild($email);
		}

		if ($data["personal_email"]) {
			// add email element
			$email = $doc->createElement("gd:email");
			$email->setAttribute("address", $data["personal_email"]);
			$email->setAttribute("rel", "http://schemas.google.com/g/2005#home");
			$entry->appendChild($email);
		}

		if ($data["business_phone_nr"]) {
			// add phonenumber element
			$business_phone = $doc->createElement("gd:phoneNumber", $data["business_phone_nr"]);
			$business_phone->setAttribute("rel", "http://schemas.google.com/g/2005#work");
			$entry->appendChild($business_phone);
		}

		if ($data["business_fax_nr"]) {
			// add phonenumber element
			$business_fax = $doc->createElement("gd:phoneNumber", $data["business_fax_nr"]);
			$business_fax->setAttribute("rel", "http://schemas.google.com/g/2005#work_fax");
			$entry->appendChild($business_fax);
		}

		if ($data["business_mobile_nr"]) {
			// add phonenumber element
			$business_mobile = $doc->createElement("gd:phoneNumber", $data["business_mobile_nr"]);
			$business_mobile->setAttribute("rel", "http://schemas.google.com/g/2005#mobile");
			$entry->appendChild($business_mobile);
		}

		if ($data["personal_phone_nr"]) {
			// add phonenumber element
			$personal_phone = $doc->createElement("gd:phoneNumber", $data["personal_phone_nr"]);
			$personal_phone->setAttribute("rel", "http://schemas.google.com/g/2005#home");
			$entry->appendChild($personal_phone);
		}

		if ($data["personal_fax_nr"]) {
			// add phonenumber element
			$personal_fax = $doc->createElement("gd:phoneNumber", $data["personal_fax_nr"]);
			$personal_fax->setAttribute("rel", "http://schemas.google.com/g/2005#home_fax");
			$entry->appendChild($personal_fax);
		}

		if ($data["personal_mobile_nr"]) {
			// add phonenumber element
			$personal_mobile = $doc->createElement("gd:phoneNumber", $data["personal_mobile_nr"]);
			$personal_mobile->setAttribute("rel", "http://schemas.google.com/g/2005#mobile");
			$entry->appendChild($personal_mobile);
		}

		if ($data["companyname"]) {
			// add org name element
			$org = $doc->createElement("gd:organization");
			$org->setAttribute("rel", "http://schemas.google.com/g/2005#work");
			$entry->appendChild($org);
			$orgName = $doc->createElement("gd:orgName", $data["companyname"]);
			$org->appendChild($orgName);
		}

		// add postal address element
		$business_address_data = sprintf("\n%s\n%s\n%s\n", $data["business_address"], $data["business_zipcode"], $data["business_city"]);
		$business_address = $doc->createElement("gd:structuredPostalAddress");
		$business_address->setAttribute("rel", "http://schemas.google.com/g/2005#work");
		$business_data = $doc->createElement("gd:formattedAddress", $business_address_data);
		$business_address->appendChild($business_data);
		$entry->appendChild($business_address);

		// add postal address element
		$personal_address_data = sprintf("\n%s\n%s\n%s\n", $data["personal_address"], $data["personal_zipcode"], $data["personal_city"]);
		$personal_address = $doc->createElement("gd:structuredPostalAddress");
		$personal_address->setAttribute("rel", "http://schemas.google.com/g/2005#home");
		$personal_data = $doc->createElement("gd:formattedAddress", $personal_address_data);
		$personal_address->appendChild($personal_data);
		$entry->appendChild($personal_address);

		// add contactgroup 'my contacts'
		$cg = $doc->createElement("gContact:groupMembershipInfo");
		$cg->setAttribute("href", $mycontacts);
		$cg->setAttribute("deleted", "false");
		$entry->appendChild($cg);
		// insert entry
		$entryResult = $gdata->insertEntry($doc->saveXML(), "http://www.google.com/m8/feeds/contacts/default/full");
		return $entryResult->id;
	}
	/* }}} */
	/* updateGoogleContact {{{ */
	/**
	 * Update a contact on the google contact service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param string $id Google contact item id
	 * @param array $data The new contact data
	 *
	 * @return bool true on success, false on failure
	 */
	public function updateGoogleContact($username, $password, $id, $data) {
		//get contact groups
		$contactgroups = $this->getGoogleContactGroups($username, $password);
		$mycontacts = array_search("System Group: My Contacts", $contactgroups);
		//login to google and set some properties
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, "cp");
		$client->setHeaders("If-Match: *");
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);
		$query = new Zend_Gdata_Query($id);
		$entry = $gdata->getEntry($query);
		$xml = simplexml_load_string($entry->getXML());
		$xml->name->fullName = $data["fullname"];
		$xml->organization->orgName = $data["companyname"];
		foreach ($xml->email as $k=>$v) {
			if (strpos($v["rel"], "work") !== false) {
				$v["address"] = $data["business_email"];
			}
			if (strpos($v["rel"], "home") !== false) {
				$v["address"] = $data["personal_email"];
			}
		}
		foreach ($xml->postalAddress as $v) {
			if (strpos($v["rel"], "work") !== false) {
				$v[0] = sprintf("\n%s\n%s\n%s\n", $data["business_address"], $data["business_zipcode"], $data["business_city"]);
			}
			if (strpos($v["rel"], "home") !== false) {
				$v[0] = sprintf("\n%s\n%s\n%s\n", $data["personal_address"], $data["personal_zipcode"], $data["personal_city"]);
			}
		}
		foreach ($xml->phoneNumber as $v) {
			if (strpos($v["rel"], "work") !== false) {
				$v[0] = $data["business_phone_nr"];
			}
			if (strpos($v["rel"], "home") !== false) {
				$v[0] = $data["personal_phone_nr"];
			}
		}
		// add contactgroup 'my contacts'
		/* This is not working, have to fix
		$cg = $xml->createElement("gContact:groupMembershipInfo");
		$cg->setAttribute("href", $mycontacts);
		$cg->setAttribute("deleted", "false");
		$xml->appendChild($cg);
		 */
		// insert entry
		$entryResult = $gdata->updateEntry($xml->saveXML(), $entry->getEditLink()->href);
		return true;
	}
	/* }}} */
	/* deleteGoogleContact {{{ */
	/**
	 * Remove a specific contact from the Google contacts service
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param string $id Google contact id
	 *
	 * @return bool true on success, false on failure
	 */
	public function deleteGoogleContact($username, $password, $id) {
		$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, "cp");
		$client->setHeaders("If-Match: *");
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);
		try {
			$gdata->delete($id);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
	/* }}} */
	/* obsolete ? */
	private function userSettings($service="writely") {
		$user_data =& $this->user_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if ($user_info["google_username"] && $user_info["google_password"]) {
			$this->google["Email"]  = $user_info["google_username"];
			$this->google["Passwd"] = $user_info["google_password"];
		} else {
			return false;
		}
		$this->google["accounttype"] = "HOSTED_OR_GOOGLE";
		$this->google["service"] = $service;
		$this->google["source"]  = "covide";
	}

	public function getGoogleUserLogin() {
		$uri = "https://docs.google.com";
		return $uri;
	}
	public function checkGoogleSession() {
		$user_data =& $this->user_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if ($this->use_sessions && !$_SESSION["google_id"])
			return false;
		elseif (!$user_info["google_username"] || !$user_info["google_password"])
			return false;
		else
			return true;
	}
	private function googleLoginAuth($sid=0) {
		if ($this->use_sessions == 1) {
			if (!$_SESSION["google_id"]) {
				/* require login! */
				return false;
			}
		} else {
			/* single server side login */
			$p = array();
			foreach ($this->google as $k=>$v) {
				$p[] = sprintf("%s=%s", $k, $v);
			}
			$param = implode("&", $p);
			$ret  = $this->googleQuery($this->google_login, $param);

			/* search for the auth key */
			$data = explode("\n", $ret["data"]);
			foreach ($data as $k=>$v) {
				if ($sid) {
					if (preg_match("/^SID=/s", $v))
						return trim($v);
				} else {
					if (preg_match("/^Auth=/s", $v))
						return trim(preg_replace("/^Auth=/s", "", $v));
				}
			}
		}
	}
	private function googleQuery($url, $param="", $post=1, $header="") {
		$s = $this->mtime();

		if (!function_exists('curl_init'))
			return false;

		$cl   = curl_init();
		$opts = array();

		if ($post) {
			$opts[CURLOPT_POST] = true;
			$opts[CURLOPT_POSTFIELDS] = $param;
		}
		if ($header)
			$opts[CURLOPT_HTTPHEADER] = array($header);

		#$fp = fopen("/tmp/curl.txt", "w");
		#curl_setopt($cl, CURLOPT_WRITEHEADER, $fp);

		$opts[CURLOPT_SSL_VERIFYPEER]       = true;
		$opts[CURLOPT_HEADER]               = 0;
		$opts[CURLOPT_RETURNTRANSFER]       = true;
		$opts[CURLOPT_FOLLOWLOCATION]       = true;
		$opts[CURLOPT_DNS_USE_GLOBAL_CACHE] = true;
		$opts[CURLOPT_URL]                 = $url;
		$opts[CURLOPT_ENCODING]             = "gzip";
		$opts[CURLOPT_HTTP_VERSION]         = CURL_HTTP_VERSION_1_1;
		/*
			$opts[CURLOPT_COOKIEJAR]            = "-"; /* sprintf("%sgcookie_%s.txt",
			$GLOBALS["covide"]->temppath, md5(session_id())); */
		//$opts[CURLOPT_MAXCONNECTS]          = 10;

		/* set options */
		curl_setopt_array($cl, $opts);

		$res  = curl_exec($cl);
		$info = curl_getinfo($cl);

		$param = preg_replace("/(\&Passwd=)[^\&]*?\&/si", "$1=***********&", $param);

		$return = array(
			"info"  => $info,
			"error" => ($res === false) ? curl_error($cl):false,
			"data"  => $res,
			"param" => $param,
			"code"  => $info["http_code"],
			"time"  => $s-$this->mtime(),
			"dns"   => number_format($info["namelookup_time"], 6)

		);
		#fclose($fp);
		#unset($fp);
		curl_close($cl);

		if (!in_array($return["code"], array(200,302))) {
			echo "<b>An error occured while connecting to Google:</b><br><br>";
			echo $return["code"].": ".print_r($info, true)."<BR><BR>".$return["data"];
		}

		if ($this->debug)
			file_put_contents($this->debug_file, print_r($return, true)."\n\n", FILE_APPEND);

		return $return;
	}
	private function compareDomains($domain1, $domain2) {
		$domain[1] = $domain1;
		$domain[2] = $domain2;

		foreach ($domain as $k=>$v) {
			$domain[$k] = preg_replace("/^http(s){0,1}/si", "", $v);
			$domain[$k] = preg_replace("/\/(.*)$/si", "", $domain[$k]);
		}
		if ($domain[1] == $domain[2])
			return true;
		else
			return false;
	}
	public function gdownload($f, $mail_id=0) {
		// strip first part
		$f = preg_replace("/^((att)|(dl)):/s", "", $f);

		// strip extension
		$ext = preg_replace("/^(.{3}):.*$/s", "$1", $f);
		$f = preg_replace("/^(.{3}:)/s", "", $f);

		// decode
		$f = base64_decode($f);

		// split by |
		$f = explode("|", $f);
		$f[1] = preg_replace("/\..{3}$/s", ".".$ext, $f[1]);

		if (!$mail_id) {
			/* download to client directly */
			header(sprintf("Location: %s", $f[0]));
			exit();
		} else {
			/* fetch within covide and attach to mail */

			if ($this->compareDomains($f[0], $this->google_export_xls) == true) {

				/* extract the key */
				$k = preg_replace("/^(.*)\&key=(.*)$/s", "$2", $f[0]);
				$uri = sprintf($this->google_export_xls_worksheet, $k);

				$this->userSettings("wise");
				$this->token = $this->googleLoginAuth();
				$ret = $this->googleQuery($uri, "", 0, $this->authToHeader($this->token));

				require_once(self::include_dir."atom.php");
				$objXML = new xml2Array();
				$arr = $objXML->parse($ret["data"]);

		 		foreach ($arr as $a) {
					if ($a["name"] == "FEED") {
						foreach ($a["children"] as $child) {
							if ($child["name"] == "TITLE") {
								$docname = $child["tagData"];
							} elseif ($child["name"] == "ENTRY") {
								$doc = array();
								$t = "";
								$n = "";
								foreach ($child["children"] as $attrib) {
									switch($attrib["name"]) {
										case "ID":
											$t = explode("/", $attrib["tagData"]);
											$t = end($t);
											$t = sprintf($this->google_export_xls_cellbased, $k, $t);
											break;
										case "TITLE":
											$n = $attrib["tagData"];
											break;
									}
								}
								$tags[$t] = sprintf("%s [%s].csv", $docname, $n);
							}
						}
					}
				}
				$j = 0;
				$ws = array();
				foreach ($tags as $t=>$n) {
					$j++;
					$ws[$j] = $n;

					$this->userSettings("wise");
					$this->token = $this->googleLoginAuth();

					unset($ret);
					unset($arr);
					unset($objXML);
					$objXML = new xml2Array();

					$ret = $this->googleQuery($t, "", 0, $this->authToHeader($this->token));
					$arr = $objXML->parse($ret["data"]);
					foreach ($arr as $a) {
						if ($a["name"] == "FEED") {
							foreach ($a["children"] as $child) {
								if ($child["name"] == "ENTRY") {
									foreach ($child["children"] as $scell) {
										if ($scell["name"] == "GS:CELL") {
											$attrib = $scell["attrs"];
											$cells[$j]["c"][$attrib["ROW"]][$attrib["COL"]] = $attrib["INPUTVALUE"];
											if ($attrib["ROW"] > $cells[$j]["rows"])
												$cells[$j]["rows"] = $attrib["ROW"];
											if ($attrib["COL"] > $cells[$j]["cols"])
												$cells[$j]["cols"] = $attrib["COL"];
										}
									}
								}
							}
						}
					}
				}
				$conversion = new Layout_conversion();
				foreach ($cells as $w=>$c) {
					$lines = array();
					for ($i=1;$i<=$c["rows"];$i++) {
						$cols = array();
						for ($j=1;$j<=$c["cols"];$j++) {
							$cols[] =& $c["c"][$i][$j];
						}
						$lines[] = $conversion->generateCSVRecord($cols);
					}
					$lines = implode("\n", $lines)."\n";

					/* add as attachment here */
					$files[$w] = array(
						"name" => $ws[$w],
						"type" => "text/comma-separated-values",
						"size" => strlen($lines),
						"data" => $lines
					);
				}
				unset($lines);
				unset($cols);

			} else {
				$this->userSettings();
				$this->token = $this->googleLoginAuth();
				// query google
				$ret = $this->googleQuery($f[0], "", 0, $this->authToHeader($this->token));

				$files[0] = array(
					"name" => $f[1],
					"type" => $ret["info"]["content_type"],
					"size" => strlen($file["data"]),
					"data" => $ret["data"]
				);
				unset($ret);
			}
			$fsdata = new Filesys_data();
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fsdir_target  = "email";

			foreach ($files as $file) {
				/* insert file into dbase */
				$q = "insert into mail_attachments (message_id, name, size, type) values ";
				$q.= sprintf("(%d, '%s', '%s', '%s')", $mail_id, addslashes($file["name"]), $file["size"], $file["type"]);
				sql_query($q);
				$new_id = sql_insert_id("mail_attachments");

				/* move data to the destination */
				$ext = $fsdata->get_extension($file["name"]);

				$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

				/* write file data */
				file_put_contents($destination, $file["data"]);
				$fsdata->FS_checkFile($destination);
			}

			echo "mail_upload_update_list();";
			exit();
		}
	}

	private function mtime() {
		list($usec, $sec) = explode(" ",microtime());
		$m = ((float)$usec + (float)$sec);
		return $m;
	}
	public function gtoken($token) {
		if ($token != -1) {
			$ret = $this->googleQuery($this->google_upgrade_session, "", "", $this->authToHeader($token, 1));
			if ($ret["code"] == 200) {
				$session_token = preg_replace("/^Token=/s", "", trim($ret["data"]));
				$_SESSION["google_id"] = $session_token;
			}
		}
		if ($session_token || $token == -1) {
			if ($token == -1)
				unset($_SESSION["google_id"]);

			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					opener.document.getElementById('velden').submit();
				");
			$output->end_javascript();
			$output->exit_buffer();
		} else {
			echo "Invalid or illegal request, please check your settings!";
		}
	}
	public function gsearch($search, $subaction="") {
		if ($this->userSettings() !== false) {
			$ret = $this->getGoogleDocList(0, $search, $subaction);
			return $ret;
		} else {
			return array();
		}
	}

	/* insertIntoSpreadsheet {{{ */
	/**
	 * Inserts data into a spreadsheet
	 *
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param string $spreadSheetId The Google id of the spreadsheet
	 * @param array $options Optional array with options
	 *
	 * @return bool true on success, false on failure
	 */
	public function insertIntoSpreadsheet($username, $password, $spreadSheetId, $data = array()) {
		$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
		$service = new Zend_Gdata_Spreadsheets($client);
		if (count($data) == 0) {
			die("No valid data");
		}
		try {
			$newEntry = $service->insertRow($data, $spreadSheetId);
			return true;
		} catch (Exception $e) {
			return false;
		}

	}
	/* }}} */
	/* getFromSpreadsheet {{{ */
	/**
	 * List entries in the spreadsheet
	 *
	 * @todo retrieve cell-information (using function Zend_Gdata_Spreadsheets->getSpreadsheetCellFeedContents)
	 * @param string $username Google account username
	 * @param string $password Google account password
	 * @param array $options Optional array with options
	 *
	 * @return array Array of elements
	 */
	public function getFromSpreadsheet($username, $password, $options = array()) {
		if (count($options) == 0) {
			die("no valid options");
		}
		$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
		$service = new Zend_Gdata_Spreadsheets($client);

		if ($options["workSheetId"] == 0) {
			$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
			$query->setSpreadsheetKey($options["spreadSheetId"]);
			$feed = $service->getWorksheetFeed($query);
			$worksheet = $feed->entries[0];
			$currWkshtId = explode('/', $worksheet->id->text);
			$workSheetId = $currWkshtId[8];
		}

		$query = new Zend_Gdata_Spreadsheets_ListQuery();
		$query->setSpreadsheetKey($options["spreadSheetId"]);
		$query->setWorksheetId($workSheetId);
		$res = $service->getListFeed($query);
	}
	/* }}} */
}
?>