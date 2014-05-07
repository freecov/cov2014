<?php
/**
 * Covide Groupware-CRM Funambol (former Sync4J) module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version 6.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Funambol_data {

	/* constants */
	const include_dir = "classes/funambol/inc/";
	const class_name = "funambol_data";

	/* variables */
	private $filesyspath = "";
	private $funabol_db = "";
	private $offset = 0;
	private $conversion;
	/* methods */


	public function __construct($user_id=0) {
		$this->conversion = new Layout_conversion();

		$user_data     = new User_data();
		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		$this->user_id = $user_id;

		/* offset to sync server winter / summer time */
		if (strftime("%Z")=="CET")
			$this->offset = 0;
		else
			$this->offset = 3600;

		$this->filesyspath = sprintf("%s/%s/", $GLOBALS["covide"]->filesyspath, "../funambol");

		/* check office filesyspath */
		$array = array("", "contacts", "calendar", "todos");
		foreach ($array as $a) {
			if (!is_dir($this->filesyspath.$a)) {
				if (!mkdir($this->filesyspath.$a)) {
					die("Could not create needed directory: ".$this->filesyspath.$a);
				}
			}
			if (!is_writable($this->filesyspath)) {
				die("Could not write to directory: ".$this->filesyspath.$a);
			}
		}

		if (!$GLOBALS["funambol_db"]) {
			require_once("conf/sync.php");
			$GLOBALS["funambol_db"] =& DB::connect($dsn_sync, $options);
			if (PEAR::isError($GLOBALS["funambol_db"])) {
				echo ("Warning: no Covide office configured at this address or no valid database specified. ");
				echo ($GLOBALS["funambol_db"]->getMessage());
				die();
			}
			/* include our own db lib. This should be removed asap. */
			require_once("common/functions_pear.php");
			$GLOBALS["funambol_db"]->setFetchMode(DB_FETCHMODE_ASSOC);
			$GLOBALS["funambol_db"]->setOption("autofree", 1);
		}
		$this->funabol_db =& $GLOBALS["funambol_db"];

		/* check some default vals */
	}

	private function setDbStats($mode, $hash = "") {
		$q = sprintf("select count(*) from funambol_stats where source = '%s'", $mode);
		$res = sql_query($q);
		if (sql_result($res,0) == 0) {
			$q = sprintf("insert into funambol_stats (source) values ('%s')", $mode);
			sql_query($q);
		}
		if ($hash) {
			$q = sprintf("update funambol_stats set lasthash = '%s' where source = '%s'", $mode);
			sql_query($q);
		}
	}
	private function checkDbStats($mode) {

	}

	public function toggleAddressSync($user, $address_id, $address_table) {
		/* create output object */
		$output = new Layout_output();

		/* check if request affects multiple records */
		if (!is_numeric($address_id)) {
			/* multiple records */
			$address_data = new Address_data();
			if (preg_match("/^sel_on_/s", $address_id))
				$switch = "on";
			else
				$switch = "off";

			$selection          = (int)preg_replace("/^sel_((on)|(off))_/s", "", $address_id);
			$options            = $address_data->getExportInfo($selection);
			$options["nolimit"] = 1;
			$list               = $address_data->getRelationsList($options);

			/* get covide address items */
			$db_items = array();
			foreach ($list["address"] as $v) {
				$db_items[] = $v["id"];
			}

			/* get current sync items */
			$items = array();
			$q = sprintf("SELECT guid, address_id FROM funambol_address_sync WHERE user_id = %d AND address_table = '%s'", $user, $address_table);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$items[$row["guid"]] = $row["address_id"];
			}

			/* release session lock */
			session_write_close();

			foreach ($db_items as $id) {
				$record["id"] = $id;
				if (!in_array($record["id"], $items)) {

					if ($switch == "on") {
						/* lookup next free guid record */
						$next = $this->getNextFreeGuid();

						/* insert the identifier record */
						$q = sprintf("insert into funambol_address_sync (guid, address_table, address_id, user_id) values (%d, '%s', %d, %d)",
							$next, $address_table, $record["id"], $this->user_id);
						sql_query($q);
					}

					/* sync the record to disk */
					$this->syncRecord("address", $next);

				} else {
					if ($switch == "off") {
						$row["guid"] = array_search($record["id"], $items);
						#while ($row = sql_fetch_assoc($res)) {
						$this->deleteRecord("address", $row["guid"]);
						#}
					}
				}
			}
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					if (opener.location.href.match(/mod\=address/g))
						opener.location.href = opener.location.href;
					else
						opener.stuur();

					window.close();");
			$output->end_javascript();
			$output->exit_buffer();

		} else {
			$record["id"] = $address_id;
			$sql = sprintf("SELECT guid FROM funambol_address_sync WHERE user_id = %d AND address_id = %d AND address_table = '%s'", $user, $record["id"], $address_table);
			$res = sql_query($sql);
			if (sql_num_rows($res) == 0) {
				/* lookup next free guid record */
				$next = $this->getNextFreeGuid();

				/* insert the identifier record */
				$q = sprintf("insert into funambol_address_sync (guid, address_table, address_id, user_id) values (%d, '%s', %d, %d)",
					$next, $address_table, $record["id"], $this->user_id);
				sql_query($q);

				/* sync the record to disk */
				$this->syncRecord("address", $next);

				/* set images */
				$image = $output->replaceImage("f_nieuw.gif");
				$alt   = gettext("sync");
			} else {
				while ($row = sql_fetch_assoc($res)) {
					$this->deleteRecord("address", $row["guid"]);
				}
				/* set images */
				$image = $output->replaceImage("f_oud.gif");
				$alt   = gettext("sync");
			}
			return $image;
		}
	}

	public function getFunambolPath($type, $guid) {
		$user_data = new User_data();
		$data = $user_data->getUserDetailsById($this->user_id);

		switch ($type) {
			case "address":
				$dir = "contacts";
				break;
			case "calendar":
				$dir = "calendar";
				break;
		}

		$file = sprintf("%s/%s/%d",
			$dir, $data["mail_user_id"], $guid);

		return $file;
	}

	private function timestamp2iso8601($ts) {
		$ts = preg_replace("/[^0-9T]/s", "", date("c", $ts));
		$ts = preg_replace("/\d{4}$/s", "", $ts);
		return $ts;
	}
	private function iso86012timestamp($iso, $offset=0) {
		$ts = strtotime($iso)+$offset;
		return $ts;
	}

	public function checkRecords($type) {
		$stats = array();
		switch ($type) {
			case "calendar":
				$this->checkRecordsCalendar($type, $stats);
				break;
			case "address":
				$this->checkRecordsAddress($type, $stats);
				break;
		}
		return $stats;
	}

	private function checkRecordsCalendar($type, &$stats) {
		$calendar_data = new Calendar_data();

		$db = $this->getFunambolPath($type, 0);
		$items = $this->checkDB($db);

		$items_db = array();

		/* cleanup sync state list for items that do no longer exist */
		$q = sprintf("delete from funambol_calendar_sync where user_id = %1\$d
			and calendar_id NOT IN (select id from calendar where user_id = %1\$d)", $_SESSION["user_id"]);
		sql_query($q);

		$ts = mktime(0,0,0, date("m")-1, date("d"), date("Y")); //max 1 month back

		/* check for synable items, that do not yet exist in the sync server */
		$q = sprintf("select id from calendar where timestamp_start > %2\$d AND user_id = %1\$d and id NOT IN (
			select calendar_id from funambol_calendar_sync where user_id = %1\$d)", $_SESSION["user_id"], $ts);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->syncRecord("calendar", "", $row["id"]);
		}

		/* check for aged sync items in the sync server, or items already accorded */
		$q = sprintf("select guid from funambol_calendar_sync where calendar_id IN (
			select id from calendar where user_id = %1\$d and (timestamp_start < %2\$d OR
				is_registered = 1))",
				$_SESSION["user_id"], $ts);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->deleteRecord("calendar", $row["guid"]);
		}

		/* get current covide db records */
		$q = sprintf("select guid, calendar_id, datetime from funambol_calendar_sync where user_id = %d", $this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$items_db[$row["guid"]] = $row;
		}
		$this->setActions($items, $items_db);

		foreach ($items as $k=>$v) {
			switch ($v["action"]) {
				case "D":
					/* delete the appointment */
					#$q = sprintf("delete from calendar where id = %d", $items_db["calendar_id"]);
					#sql_query($q);
					/* delete from db */
					$calendar_data->delete($v["calendar_id"]);

					$stats["D"]++;
					break;
				case "U":
					$file = $this->getFunambolPath("calendar", $v["guid"]);
					$data = $this->vcard2array($file);
					$data["id"] = $v["calendar_id"];

					/* update db, if item is not registered */
					if (!$calendar_data->checkRegistrationState($v["calendar_id"])) {
						$calendar_data->save2db($data, 1);

						$q = sprintf("update funambol_calendar_sync set datetime = %d where guid = %d",
							mktime(), $v["guid"]);
						sql_query($q);
						$stats["U"]++;
					}
					break;
				case "I":
					$file = $this->getFunambolPath("calendar", $v["guid"]);
					$data = $this->vcard2array($file);

					/* insert into db, only if item is not aged */
					if ($data["timestamp_start"] > $ts) {
						$ids = $calendar_data->save2db($data, 1);

						$q = sprintf("insert into funambol_calendar_sync (user_id, guid, calendar_id, datetime) values (%d, %d, %d, %d)",
							$_SESSION["user_id"], $v["guid"], $ids[0], mktime());
						sql_query($q);
						$stats["I"]++;
					}

					break;
			}
		}
	}


	private function checkRecordsAddress($type, &$stats) {
		$calendar_data = new Address_data();

		function key_compare_func($a, $b) {
			if ($a != $b)
				return 0;
			else
				return 1;
		}

		$db = $this->getFunambolPath("address", 0);

		/* retrieve items which are still present in the mobile device */
		$current_items = $this->checkFS($db);

		/* get current covide db records */
		$items_db = array();
		$q = sprintf("select guid, address_id, address_table, datetime from funambol_address_sync where user_id = %d", $this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$items_db[$row["guid"]] = array(
				"datetime"   => $row["datetime"],
				"guid"       => $row["guid"],
				"address_id" => $row["address_id"],
				"address_table" => $row["address_table"]
			);
		}
		foreach ($items_db as $k=>$v) {
			if (!in_array($k, $current_items))
				$current_items[$k] = array(
					"datetime" => 0,
					"guid"     => $k
				);
		}

		/* check for items deleted in the device, which are still present inside Covide */
		$this->setActions($current_items, $items_db, "address");

		/* diff all the items, there should be no differences */
		foreach ($current_items as $k=>$v) {
			switch ($v["action"]) {
				case "U":
					$this->updateAddressById($v["address_id"], $v["address_table"]);
					break;
				case "I":
					/* skip */
					break;
			}
		}

	}

	private function parseXML($file) {
		$data = file_get_contents($file, "r");
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $data, $vals, $index);
		xml_parser_free($parser);
		return array(
			"index" => $index,
			"vals"  => $vals
		);
	}

	private function vcard2array($file) {
		$fn = $this->filesyspath.$file;

		$data = array(
			"user_id"     => $this->user_id,
			"modified_by" => $this->user_id
		);

		if (file_exists($fn)) {
			$xml = $this->parseXML($fn);
			foreach ($xml["vals"] as $v) {
				$key =& $v["tag"];
				$val =& $v["value"];

				switch ($key) {
					case "ALLDAYEVENT":
						if ($val == 1)
							$data["is_event"] = 1;
						break;
					case "START":
						$data["timestamp_start"] = $this->iso86012timestamp($val, $this->offset);
						$data["from_day"]   = array(date("d", $data["timestamp_start"]));
						$data["from_month"] = date("m", $data["timestamp_start"]);
						$data["from_year"]  = date("Y", $data["timestamp_start"]);
						$data["from_hour"]  = date("H", $data["timestamp_start"]);
						$data["from_min"]   = date("i", $data["timestamp_start"]);
						$data["timestamp_start_h"] = date("d-m-Y H:i", $data["timestamp_start"]);
						break;
					case "END":
						$data["timestamp_end"] = $this->iso86012timestamp($val, $this->offset);
						$data["to_day"]     = array(date("d", $data["timestamp_end"]));
						$data["to_month"]   = date("m", $data["timestamp_end"]);;
						$data["to_year"]    = date("Y", $data["timestamp_end"]);;
						$data["to_hour"]    = date("H", $data["timestamp_end"]);;
						$data["to_minute"]  = date("i", $data["timestamp_end"]);;
						$data["timestamp_end_h"] = date("d-m-Y H:i", $data["timestamp_end"]);
						break;
					case "BODY":
						$data["description"] = trim(quoted_printable_decode($val));
						break;
					case "SENSITIVITY":
						if ($val == "PRIVATE")
							$data["is_private"] = 1;
							break;
					case "SUBJECT":
						$data["subject"] = $val;
						break;
					case "REMINDERSET":
						if ($val)
							$data["is_important"] = 1;
						break;
					case "REMINDERMINUTESBEFORESTART":
						if ($val)
							$data["notifytime"] = $val * 60;
						break;
				}
			}
		}
		return $data;
	}

	private function setItemVal(&$v, &$items_db, $mode) {
		switch ($mode) {
			case "calendar":
				$v["calendar_id"]   = $items_db[$v["guid"]]["calendar_id"];
				break;
			case "address":
				$v["address_id"]    = $items_db[$v["guid"]]["address_id"];
				$v["address_table"] = $items_db[$v["guid"]]["address_table"];
				break;
		}
	}

	private function setActions(&$items, &$items_db, $mode="calendar") {
		/* (D)elete, (I)nsert, (U)pdate or empty */
		foreach ($items as $k=>$v) {
			if ($v["state"] == "D") {
				if ($items_db[$v["guid"]]) {
					$v["action"] = "D";
					$this->setItemVal(&$v, &$items_db, $mode);
				}
			} elseif ($items_db[$v["guid"]]) {
				/* record does exists, need update (?) */
				if ($v["datetime"] > $items_db[$v["guid"]]["datetime"]) {
					$this->setItemVal(&$v, &$items_db, $mode);
					$v["action"] = "U";
				} else {
					/* check file mtime, we cannot rely on just the sync db in this case */
					$db = $this->getFunambolPath($mode, 0);
					$file = $this->filesyspath.preg_replace("/0$/s", $v["guid"], $db);
					if (file_exists($file))
						$mtime = filemtime($file);
					else
						$mktime = -1;

					if ($mtime > $items_db[$v["guid"]]["datetime"]) {
						$this->setItemVal(&$v, &$items_db, $mode);
						$v["action"] = "U";
					}
				}
			} else {
				/* need insert */
					$v["action"] = "I";
			}
			if (!$v["action"]) {
				#$v["action"] = "S";
				/* remove from list */
				unset($items[$k]);
			} else {
				$items[$k] = $v;
			}
		}
	}

	private function checkDB($db) {
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;
		$dir = scandir($db);

		$items = array();
		foreach ($dir as $file) {
			if (preg_match("/\.db$/s", $file)) {
				$fn = sprintf("%s%s", $db, $file);
				$data = file_get_contents($fn);
				$data = explode("\n", $data);
				foreach ($data as $v) {
					if (preg_match("/^\d/s", $v)) {
						$v = trim($v);
						$v = preg_split("/=(N|U|D)/s", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
						$v[2] = substr($v[2], 0, 10);
						$items[$v[0]] = array(
							"guid"     => $v[0],
							"state"    => $v[1],
							"datetime" => $v[2]
						);
					}
				}
			}
		}
		return $items;
	}
	private function checkFS($db) {
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;
		$dir = scandir($db);
		$items = array();

		foreach ($dir as $file) {
			if (is_numeric($file)) {
				$items[(int)$file] = array(
					"datetime" => filemtime($db.$file),
					"guid"     => (int)$file
				);
			}
		}
		return $items;
	}


	public function removeRecord($type, $id) {
		switch ($type) {
			case "address":
				break;
			case "calendar":
				$q = sprintf("select guid from funambol_calendar_sync where calendar_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res)>0) {
					$guid = sql_result($res,0);
					$this->deleteRecord($type, $guid);
				}
				break;
		}
	}
	public function syncRecord($type, $guid, $id=0) {
		switch ($type) {
			case "address":
				$q = sprintf("select * from funambol_address_sync where guid = %d", $guid);
				$res = sql_query($q);
				$row = sql_fetch_assoc($res);

				switch ($row["address_table"]) {
					case "address_businesscards":
						$subtype = "bcards";
						break;
					case "address_private":
						$subtype = "private";
						break;
					default:
						$subtype = "relations";
						break;
				}

				$address_data = new Address_data();
				$data = $address_data->getAddressById($row["address_id"], $subtype);
				if ($subtype == "private" || $subtype == "bcards") {
					$data["contact_givenname"] = $data["givenname"];
					$data["contact_infix"] = $data["infix"];
					$data["contact_surname"] = $data["surname"];
				}
				if (!$data["mobile_nr_business"])
					$data["mobile_nr_business"] = "";

				$data["categories"] = explode("\n", $data["classification_names"]);
				$data["categories"] = preg_replace("/(,|\t|\r)/s", " ", $data["categories"]);
				$data["categories"] = implode(",",$data["categories"]);

				$xml = file_get_contents(self::include_dir."address.xml");

				$this->mergeXmlData($data, $xml);
				$md5 = md5($xml);

				//$file = sprintf("../funambol/contacts/%s/%s", $guid);
				$file = $this->getFunambolPath($type, $guid);
				$ts = $this->writeFile($file, $xml);

				/* update database with hash */
				$q = sprintf("update funambol_address_sync set file_hash = '%s', datetime = %d where guid = %d",
					$md5, $ts, $guid);
				sql_query($q);
				break;
			case "calendar":

				/* get appointment info */
				$calendar_data = new Calendar_data();
				$data = $calendar_data->getCalendarItemById($id);

				/* fill some variables */
				$data["datetime_start"] = $this->timestamp2iso8601($data["timestamp_start"]-$this->offset);
				$data["datetime_end"]   = $this->timestamp2iso8601($data["timestamp_end"]- $this->offset);
				$data["recurring"]      = 0;
				$data["category"]       = "";
				if ($data["is_private"])
					$data["sensitivity"]    = "PRIVATE";
				else
					$data["sensitivity"]    = "PUBLIC";

				$data["reminder"]           = ($data["is_important"]) ? 1:0;
				$data["reminder_min"]       = (int)($data["notifytime"]/60);

				$data["reminder_options"]   = ($data["is_important"]) ? 8:0;
				$data["reminder_interval"]  = 0;

				if ($data["is_registered"])
					$data["description"].= " (registered)";

				/* parse xml file */
				$xml = file_get_contents(self::include_dir."calendar.xml");
				$this->mergeXmlData($data, $xml);

				/* calculate a hash */
				$md5 = md5($xml);

				/* check if current appointment does exist */
				$q = sprintf("select * from funambol_calendar_sync where calendar_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res) == 1) {
					/* found! */
					$row = sql_fetch_assoc($res);
					$file_hash = $row["file_hash"];
					$old_guid = $row["guid"];
				}

				if ($file_hash != $md5) {
					/* if record has been changed */
					$guid = $this->getNextFreeGuid();

					$file = $this->getFunambolPath($type, $guid);
					$ts = $this->writeFile($file, $xml);

					$q = sprintf("insert into funambol_calendar_sync (guid, calendar_id, user_id, file_hash, datetime) values (%d, %d, %d, '%s')",
						$guid, $id, $data["user_id"], $md5, $ts);
					sql_query($q);

					/* notify the store for the old record */
					if ($old_guid)
						$this->deleteRecord($type, $old_guid);
				}
				break;
		}
	}

	private function mergeXmlData(&$data, &$xml) {
		foreach ($data as $k=>$v) {
			if (!is_array($v)) {
				$v = $this->convertEncoding($v);
				$v = str_replace("&", "&amp;", $v);
				$xml = str_replace(sprintf("{%s}", $k), $v, $xml);
			}
		}
		$xml = preg_replace("/\{[^\}]*?\}/s", "", $xml);
	}
	private function convertEncoding($str) {
		$str = preg_replace("/(\r|\t)/s", "", $str);
		$str = $this->conversion->str2utf8($str);
		$str = str_replace("&", "&amp;", $str);
		$str = str_replace("\n", "\t\r\n", $str); //bugfix for devices
		return $str;

	}

	public function getNextFreeGuid() {
		$next = -1;
		while ($next != $next_expected) {
			/* get next counter */
			$q = sprintf("select counter+1 from fnbl_id where idspace = 'guid'");
			$res = sql_query($q, $this->funabol_db);
			$next_expected = sql_result($res,0);

			/* update counter */
			$q = sprintf("update fnbl_id set counter = counter+1 where idspace = 'guid'");
			sql_query($q, $this->funabol_db);

			/* re-read the counter */
			$q = sprintf("select counter from fnbl_id where idspace = 'guid'");
			$res = sql_query($q, $this->funabol_db);
			$next = sql_result($res,0);
		}
		return $next;
	}

	private function truncateFile($file) {
		$file = $this->filesyspath.$file;
		if (file_exists($file)) {
			if (is_writable($file)) {
				unlink($file);
			} else {
				echo "alert('cannot unlink file');";
			}
		}
	}
	private function writeFile($file, $data) {
		$file = $this->filesyspath.$file;
		if (!preg_match("/\/0$/s", $file))
			file_put_contents($file, $data);
		return filemtime($file);
	}

	private function updateDB($db, $guid, $state) {
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;
		$dir = scandir($db);
		foreach ($dir as $file) {
			if (preg_match("/\.db$/s", $file)) {
				$fn = sprintf("%s/%s", $db, $file);
				$data = sprintf("%d=%s%d000\n", $guid, $state, mktime());
				file_put_contents($fn, $data, FILE_APPEND);
			}
		}
	}

	public function deleteRecord($type, $guid) {
		$q = sprintf("delete from fnbl_client_mapping where guid = '%d'", $guid);
		#sql_query($q, $this->funabol_db);

		switch ($type) {
			case "address":
				$db = $this->getFunambolPath("address", 0);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("address", $guid);
				$this->truncateFile($file);
				$q = sprintf("delete from funambol_address_sync where guid = %d", $guid);
				sql_query($q);
				break;
			case "calendar":
				$db = $this->getFunambolPath("calendar", 0);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("calendar", $guid);
				$this->truncateFile($file);
				$q = sprintf("delete from funambol_calendar_sync where guid = %d", $guid);
				sql_query($q);
				break;
			case "todo":
		}
	}
	public function deleteAddressById($id, $type) {
		/* get all synced versions of this record */
		$q = sprintf("select guid from funambol_address_sync where address_id = %d and address_table = '%s'",
			$id, $type);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->deleteRecord("address", $row["guid"]);
		}
	}
	public function updateAddressById($id, $type) {
		switch ($type) {
			case "private":
				$ident = "address_private";
				break;
			case "bcards":
				$ident = "address_businesscards";
				break;
			default:
				$ident = "address";
				break;
		}
		$q = sprintf("select * from funambol_address_sync where address_id = %d and address_table = '%s'",
			$id, $ident);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* re-enable the record */
			$this->toggleAddressSync($row["user_id"], $row["address_id"], $row["address_table"]);
			$this->toggleAddressSync($row["user_id"], $row["address_id"], $row["address_table"]);
		}
	}
}
?>
