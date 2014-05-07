<?php
/**
 * Covide Syncronisation module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Funambol_data {
	/*
		===================================================================
		supported sync server software is the latest stable linux version 6
		===================================================================
	*/
	/* constants */
	const include_dir = "classes/funambol/inc/";
	const class_name  = "funambol_data";
	const plugin_dir  = "classes/funambol/plug-ins/cl/config/spds/";

	/* variables */
	private $filesyspath = "";
	private $funabol_db = "";
	private $offset = 0;
	private $conversion;
	private $sync_users = array();
	private $stats = array();
	private $debug = 0;
	private $timezone;
	private $batch = 0;

	public $realtime = 0;
	private $recover = 0;

	/* methods */

	public function __construct($user_id=0, $batch=0) {
		$this->conversion = new Layout_conversion();
		$this->batch = $batch;

		$this->debug = ($_REQUEST["debug"] == 1) ? 1:0;

		$user_data     = new User_data();
		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		$this->user_id = $user_id;

		/* offset to sync server winter / summer time */
		if (strftime("%Z")=="CET")
			$this->offset = 0;
		else
			$this->offset = 3600;

		$this->filesyspath = sprintf("%s/%s/", $GLOBALS["covide"]->filesyspath, "../fnbl_client");

		/* check office filesyspath */
		/*
		$array = array("", "contacts", "calendar", "todo", "files");
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
		*/
		/*
		if (!$GLOBALS["funambol_db"]) {
			require_once("conf/sync.php");
			require_once("MDB2.php");

			$options = array(
				"persistent"  => TRUE,
				'portability' => MDB2_PORTABILITY_NONE
			);

			$GLOBALS["funambol_db"] =& MDB2::connect($dsn_sync, $options);
			if (PEAR::isError($GLOBALS["funambol_db"])) {
				echo ("Warning: no Covide office configured at this address or no valid database specified. ");
				echo ($GLOBALS["funambol_db"]->getMessage());
				die();
			}
			/* include our own db lib. This should be removed asap. *
			require_once("common/functions_pear.php");
			$GLOBALS["funambol_db"]->setFetchMode(DB_FETCHMODE_ASSOC);
			$GLOBALS["funambol_db"]->setOption("autofree", 1);
		}
		$this->funabol_db =& $GLOBALS["funambol_db"];
		*/

		/* get server timezone */
		$this->timezone = trim(file_get_contents("/etc/timezone"));
	}

	private function setDbStats($mode, $hash = "") {
		/*
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
		*/
	}
	private function checkDbStats($mode) {
		/* nothing yet */
	}

	public function checkPrivateAddressSyncState($id) {
		$q = sprintf("select count(*) from funambol_address_sync where
			address_table = 'address_private' and address_id = %d", $id);
		$res = sql_query($q);
		return sql_result($res,0);
	}

	public function upgradeFnbl() {
		$q = sprintf("update users set xs_funambol_version = 6 where id = %d",
			$this->user_id);
		sql_query($q);
	}

	private function getAddressIdent($id, $table) {
		switch ($table) {
			case "relations":
				$type_a = 0;
				break;
			case "bcards":
				$type_a = 1;
				break;
			case "private":
				$type_a = 2;
				break;
		}
		return sprintf("%d-%d", $id, $type_a);
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
						$ident_a = $this->getAddressIdent($record["id"], $address_table);
						$next = $this->getNextFreeGuid("address", 0, $ident_a);

						/* insert the identifier record */
						$q = sprintf("insert into funambol_address_sync (guid, address_table, address_id, user_id) values ('%s', '%s', %d, %d)",
							$next, $address_table, $record["id"], ($user) ? $user:$this->user_id);
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
					else if (opener.document.stuur)
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
				$ident_a = $this->getAddressIdent($record["id"], $address_table);
				$next = $this->getNextFreeGuid("", 0, $ident_a);

				/* delete previous versions of this record */
				$q = sprintf("delete from funambol_address_sync where guid = '%s'",
					$ident_a);
				sql_query($q);

				/* insert the identifier record */
				$q = sprintf("insert into funambol_address_sync (guid, address_table, address_id, user_id) values ('%s', '%s', %d, %d)",
					$next, $address_table, $record["id"], ($user) ? $user:$this->user_id);
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
	private function getUserByGuid($table, $guid) {
		/* get user_id corresponding to guid */
		if ($guid > 0) {
			$q = sprintf("select user_id from %s where guid = '%s'", $table, $guid);
			$res = sql_query($q);
			return sql_result($res,0);
		} else {
			return $this->user_id;
		}
	}

	public function syncUser($reset_db=0) {

		#$pidfile = $GLOBALS["covide"]->temppath."funambol.pid";
		$pidfile = "/tmp/funambol.pid";

		if (file_exists($pidfile) && filemtime($pidfile) >= mktime()-60)
			die("funambol lockfile found, skipping request");

		$fp = fopen($pidfile, "w+");
		if (flock($fp, LOCK_EX)) {

			fwrite($fp, mktime());

			$user_id = $this->user_id;
			$user_data = new User_data();
			$data = $user_data->getUserDetailsById($user_id);

			$settings["username"] = $data["mail_user_id"];
			$settings["password"] = $data["mail_password"];

			$xml_account = file_get_contents(self::include_dir."syncml.properties");
			$xml_store   = file_get_contents(self::include_dir."syncml.source");

			$path = $this->getFunambolPath("config", 0, $user_id);
			$path_2 = $this->filesyspath.dirname($path);

			foreach ($settings as $k=>$v) {
				$xml_account = str_replace(sprintf("<%s>", $k), $v, $xml_account);
			}
			file_put_contents($path_2."/syncml.properties", $xml_account);
			file_put_contents(self::plugin_dir."syncml.properties", $xml_account);

			$array = array("address", "calendar", "todo");
			unset($settings);

			$changed_items = array();
			$current_items = array();

			foreach ($array as $a) {
				$path = $this->getFunambolPath($a, 0, $user_id);
				$path = $this->filesyspath.dirname($path);

				$xml_store_t = $xml_store;
				$settings["sourcedir"] = $path;
				$settings["store"] = $this->getFunambolStore($a);
				$settings["fulldate"] = date("r");

				switch ($a) {
					case "address":
						$settings["mimetype"] = "text/x-s4j-sifc";
						break;
					case "calendar":
						$settings["mimetype"] = "text/x-s4j-sife";
						break;
					case "todo":
						$settings["mimetype"] = "text/x-s4j-sift";
						break;
					default:
						$settings["mimetype"] = "text/xml";
				}
				$ds = $this->getSourceHash($path);
				$settings["timestamp"] = ($ds["synchash"]) ? $ds["synchash"]:1;

				foreach ($settings as $k=>$v) {
					$xml_store_t = str_replace(sprintf("<%s>", $k), $v, $xml_store_t);
				}
				file_put_contents($path_2."/".$a.".properties", $xml_store_t);
				file_put_contents(self::plugin_dir."sources/$a.properties", $xml_store_t);

				if ($reset_db) {
					unset($ret);
					$cmd = sprintf("rm -f %s", escapeshellarg($path."/*"));
					exec($cmd, $ret, $retval);
				}

				unset($ret);
				$cmd = sprintf("ls -lt %s", escapeshellarg($path));
				exec($cmd, $ret, $reval);


				$current_items[$a] = md5(implode("\n", $ret));
			}

			/* exec sync call */
			$cmd = sprintf("cd classes/funambol/plug-ins/cl && sh run.sh");
			exec($cmd, $ret, $retval);

			if ($retval != 0) {
				echo "Unknown error occured:<br><br>";
				echo file_get_contents("tmp/funambol.log");
				die();
			}

			foreach ($array as $a) {
				$path = $this->getFunambolPath($a, 0, $user_id);
				$path = $this->filesyspath.dirname($path);

				$data = explode("\n", file_get_contents(self::plugin_dir."sources/$a.properties"));
				foreach ($data as $k=>$v) {
					if (preg_match("/^last=\d{1,}/s", trim($v))) {
						$v = explode("=", $v);
						$v = $v[1];
						$this->checkSourceHash($path, 1, $v);
					}
				}
				unset($ret);
				$cmd = sprintf("ls -lt %s", escapeshellarg($path));
				exec($cmd, $ret, $reval);
				$hash = md5(implode("\n", $ret));

				if ($current_items[$a] != $hash)
					$changed_items[$a] = 1;

			}
			flock($fp, LOCK_UN);
			fclose($fp);
			unlink($pidfile);

		} else {
			die("Could not create exclusive lock on funambol pid file");
		}

		return $changed_items;
	}

	public function getFunambolStore($type) {
		switch ($type) {
			case "address":
				$dir = "scard";
				break;
			case "calendar":
				$dir = "scal";
				break;
			case "todo":
				$dir = "stask";
				break;
			case "files":
				$dir = "briefcase";
				break;
		}
		return $dir;

	}

	public function truncateCovideStore() {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($this->user_id);

		/* get current user data */
		$user_info_current = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if (!$user_info_current["xs_usermanage"])
			die("not an admin");

		if ($user_info["xs_funambol_version"] == 6 && !$_REQUEST["force"])
			die("funambol already version 6 for this user");

		/* delete calendar items */
		$q = sprintf("delete from calendar where user_id = %d", $this->user_id);
		sql_query($q);

		/* delete todo items */
		$q = sprintf("delete from todo where user_id = %d", $this->user_id);
		sql_query($q);

		/* delete address private */
		$q = sprintf("delete from address_private where user_id = %d", $this->user_id);
		sql_query($q);

		/* delete sync tables */
		$q = sprintf("delete from funambol_address_sync where user_id = %d", $this->user_id);
		sql_query($q);
		$q = sprintf("delete from funambol_address_sync_v3 where user_id = %d", $this->user_id);
		sql_query($q);
		$q = sprintf("delete from funambol_calendar_sync where user_id = %d", $this->user_id);
		sql_query($q);
		$q = sprintf("delete from funambol_todo_sync where user_id = %d", $this->user_id);
		sql_query($q);
	}

	public function truncateUserStore() {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($this->user_id);

		/* get current user data */
		$user_info_current = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if (!$user_info_current["xs_usermanage"])
			die("not an admin");

		if ($user_info["xs_funambol_version"] == 6 && !$_REQUEST["force"])
			die("funambol already version 6 for this user");

		$path["address"]  = preg_replace("/0$/s", "", $this->filesyspath.$this->getFunambolPath("address", 0));
		$path["calendar"] = preg_replace("/0$/s", "", $this->filesyspath.$this->getFunambolPath("calendar", 0));
		$path["todo"]     = preg_replace("/0$/s", "", $this->filesyspath.$this->getFunambolPath("todo", 0));

		foreach ($path as $p) {
			$dir = scandir($p);
			foreach ($dir as $f) {
				$file = sprintf("%s/%s", $p, $f);
				if (is_file($file))
					unlink($file);
			}
		}
	}

	public function getFunambolPath($type, $guid, $override_user_id=0) {
		switch ($type) {
			case "address":
				$dir = "contacts";
				$user_id = $this->getUserByGuid("funambol_address_sync", $guid);
				break;
			case "calendar":
				$dir = "calendar";
				$user_id = $this->getUserByGuid("funambol_calendar_sync", $guid);
				break;
			case "todo":
				$dir = "todo";
				$user_id = $this->getUserByGuid("funambol_todo_sync", $guid);
				break;
			case "files":
				$dir = "files";
				$user_id = $this->getUserByGuid("funambol_file_sync", $guid);
				break;
			case "config":
				$dir = "config";
				$user_id = $this->user_id;
				break;
		}

		if ($override_user_id)
			$user_id = $override_user_id;

		$user_data = new User_data();
		$data = $user_data->getUserDetailsById($user_id);

		$file = sprintf("%s/%s/%s",
			$data["mail_user_id"], $dir, $guid);

		$dir = $this->filesyspath.dirname($file);
		if (!file_exists($dir))
			mkdir($dir, 0777, 1);

		return $file;
	}


	/* some example code

	// device to code
	$tz   = timezone_open("Europe/Amsterdam");
	$date = date_create("20070327T090000Z", $tz);
	date_timezone_set($date, $tz);

	echo date_format($date, "d-m-Y H:i")."<BR>";

	// covide to device
	$ts = mktime(11,0,0,3,27,2007);

	$date = date_create("@".$ts, $tz);
	date_timezone_set($date, $tz);
	echo preg_replace(array(
		"/\+(.*)$/s",
		"/\:/s"),
		array("", ""),
		date_format($date, "c")
	)."Z";
	*/

	private function timestamp2iso8601($ts) {
		#echo date("d-m-Y H:i", $ts)."<BR>";

		#print_r(timezone_identifiers_list());
		/* timezone conversion */
		$tz = timezone_open("Etc/UTC");
		$tz2 = timezone_open($this->timezone);

		$tdate = date_create(sprintf("@%d", $ts), $tz);
		date_timezone_set($tdate, $tz2);

		$offset = date_offset_get($tdate);

		/* convert timezone datetime back to unix epoch time */
		$unixt = strtotime(date_format($tdate, "c"))-$offset;

		/* server and client = winter time, make summer time appointment fails */
		#if (date("I"))
		#	$unixt += 3600;

		/* convert the format to the RFC format we need, with DST */
		$iso = preg_replace(array(
			"/\+(.*)$/s",
			"/:|-/s"),
			array("", ""),
			date("c", $unixt)
		)."Z";

		#echo $iso;
		#die();

		return $iso;
	}

	private function iso86012timestamp($iso) {
		$tz = timezone_open($this->timezone);
		$date = date_create($iso, $tz);
		date_timezone_set($date, $tz);

		$ts = date_format($date, "U");

		return $ts;
	}

	public function checkRecords($type) {
		#print_r($GLOBALS["covide"]->license);
		if (!$this->batch)
			return false;

		/* check if the requested user has sync */
		$q = sprintf("select xs_funambol from users where id = %d", $this->user_id);
		$res = sql_query($q);
		if (sql_result($res,0) == 0)
			return false;


		/*
		if ($GLOBALS["covide"]->license["funambol_server_version"] < 600) {
			/* upgrade needed
			echo "<b>Funambol interface and/or server is not upgraded.</b><br>
				Please install Funambol server version 6 and execute patch <br>
				'sql/mysq/patches_runonce/2007050700.sql' to drop all Funambol v3 support.<br>";
			return false;
		}
		*/

		if ($GLOBALS["covide"]->license["funambol_server_version"] < 600)
			$this->recover = 1;

		if ($this->recover == 1) {
			sql_query("delete from funambol_stats");
			sql_query("delete from funambol_calendar_sync");
			sql_query("delete from funambol_address_sync");
			sql_query("delete from funambol_todo_sync");
			sql_query("delete from funambol_file_sync");
			sql_query("update license set funambol_server_version = 600");
		}

		$stats = array();
		switch ($type) {
			case "calendar":
				$ret = $this->checkRecordsCalendar($type, $stats);
				break;
			case "address":
				$ret = $this->checkRecordsAddress($type, $stats);
				break;
			case "todo":
				$ret = $this->checkRecordsTodo($type, $stats);
				break;
			case "files":
				#$ret = $this->checkRecordsFiles($type, $stats);
				break;
		}
		if ($ret === false)
			return $ret;
		else
			return true; //$stats;
	}

	private function checkRecordsCalendar($type, &$stats) {
		$calendar_data = new Calendar_data();

		/* check for missing items */
		$ts = mktime(0,0,0, date("m")-1, date("d"), date("Y")); //max 1 month back

		/* check for synable items, that do not yet exist in the sync server */
		$q = sprintf("select id from calendar where (is_registered = 0 OR is_registered IS NULL) AND timestamp_start > %2\$d AND user_id = %1\$d and id NOT IN (
			select calendar_id from funambol_calendar_sync where user_id = %1\$d)", $this->user_id, $ts);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->syncRecord("calendar", "", $row["id"]);
		}

		$db = $this->getFunambolPath($type, 0);
		$items = $this->checkDB($db);

		if ($items === false)
			return false;

		$items_db = array();

		/* cleanup sync state list for items that do no longer exist */
		#$q = sprintf("delete from funambol_calendar_sync where user_id = %1\$d
		#	and calendar_id NOT IN (select id from calendar where user_id = %1\$d)", $this->user_id);
		#sql_query($q);

		/* check for aged sync items in the sync server, or items already accorded */
		$q = sprintf("select guid from funambol_calendar_sync where calendar_id IN (
			select id from calendar where user_id = %1\$d and (timestamp_start < %2\$d OR
				is_registered = 1))",
				$this->user_id, $ts);
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

					$db_data = $calendar_data->getCalendarItemById($v["calendar_id"]);

					$file = $this->getFunambolPath("calendar", $v["guid"]);
					$data = $this->ical2array($file);
					//$data["id"] = $v["calendar_id"];

					foreach ($data as $kk=>$vv) {
						$db_data[$kk] = $vv;
					}

					/* update db, if item is not registered */
					if (!$calendar_data->checkRegistrationState($v["calendar_id"])) {
						$data["id"] = $v["calendar_id"];
						$db_data["id"] = $v["calendar_id"];
						$calendar_data->save2db($db_data, 1);

						$q = sprintf("update funambol_calendar_sync set datetime = %d where guid = '%s'",
							mktime(), $v["guid"]);
						sql_query($q);
						$stats["U"]++;
					}
					break;
				case "I":
					$file = $this->getFunambolPath("calendar", $v["guid"], $this->user_id);
					$data = $this->ical2array($file);
					$data["user_id"] = $this->user_id;

					/* insert into db, only if item is not aged */
					if ($data["timestamp_start"] > $ts) {
						$ids = $calendar_data->save2db($data, 1);

						$q = sprintf("insert into funambol_calendar_sync (user_id, guid, calendar_id, datetime) values (%d, '%s', %d, %d)",
							$this->user_id, $v["guid"], $ids[0], mktime());
						sql_query($q);
						$stats["I"]++;
					}
					break;
			}
		}
		$stats["C"] = 1;
		$GLOBALS["covide"]->sync_stats["calendar"] = $stats;
	}

	private function checkRecordsTodo($type, &$stats) {
		$todo_data = new Todo_data();

		/* check for missing records */
		$ts = mktime(0,0,0, date("m")-1, date("d"), date("Y")); //max 1 month back

		/* check for synable items, that do not yet exist in the sync server */
		$q = sprintf("select id from todo where user_id = %1\$d and id NOT IN (
			select todo_id from funambol_todo_sync where user_id = %1\$d)", $this->user_id, $ts);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->syncRecord("todo", "", $row["id"]);
		}

		$db = $this->getFunambolPath($type, 0);
		$items = $this->checkDB($db);
		$items_db = array();

		if ($items === false)
			return false;

		/* cleanup sync state list for items that do no longer exist */
		#$q = sprintf("delete from funambol_todo_sync where user_id = %1\$d
		#	and todo_id NOT IN (select id from todo where user_id = %1\$d)", $this->user_id);
		#sql_query($q);

		/* get current covide db records */
		$q = sprintf("select guid, todo_id, datetime from funambol_todo_sync where user_id = %d", $this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$items_db[$row["guid"]] = $row;
		}
		$this->setActions($items, $items_db, "todo");

		foreach ($items as $k=>$v) {
			switch ($v["action"]) {
				case "D":
					/* delete the appointment */
					/* delete from db */
					$todo_data->delete_todo($v["todo_id"],1);

					$stats["D"]++;
					break;
				case "U":

					$db_data = $todo_data->getTodoById($v["todo_id"]);

					$file = $this->getFunambolPath("todo", $v["guid"]);
					$data = $this->sift2array($file);
					//$data["id"] = $v["calendar_id"];

					foreach ($data as $kk=>$vv) {
						$db_data[$kk] = $vv;
					}
					#print_r($data);

					/* update db, if item is not registered */
					$data["id"] = $v["todo_id"];
					$data["user_id"] = $this->user_id;
					$db_data["id"] = $v["todo_id"];
					$db_data["user_id"] = $this->user_id;

					$todo_data->save_todo($db_data, 1);

					$q = sprintf("update funambol_todo_sync set datetime = %d where guid = '%s'",
						mktime(), $v["guid"]);
					sql_query($q);
					$stats["U"]++;

					break;

				case "I":
					$file = $this->getFunambolPath("todo", $v["guid"], $this->user_id);
					$data = $this->sift2array($file);
					$data["user_id"] = $this->user_id;

					/* insert into db, only if item is not aged */
					$ids = $todo_data->save_todo($data, 1);

					$q = sprintf("insert into funambol_todo_sync (user_id, guid, todo_id, datetime) values (%d, '%s', %d, %d)",
						$this->user_id, $v["guid"], $ids[0], mktime());
					sql_query($q);
					$stats["I"]++;

					break;
			}
		}
		$stats["C"] = 1;
		$GLOBALS["covide"]->sync_stats["todo"] = $stats;
	}

	private function checkRecordsFiles($type, &$stats) {
		$fs_data = new Filesys_data();

		/* file sync could take some time */
		set_time_limit(60*60);

		/* get sync folder id */
		$folder = $fs_data->getSyncFolder();

		$db = $this->getFunambolPath($type, 0);
		$items = $this->checkDB($db);
		$items_db = array();

		/* cleanup sync state list for items that do no longer exist */
		#$q = sprintf("delete from funambol_file_sync where user_id = %1\$d
		#	and file_id NOT IN (select id from filesys_files where folder_id = %2\$d and user_id = %1\$d)",
		#	$this->user_id, $folder);
		#sql_query($q);

		$ts = mktime(0,0,0, date("m")-1, date("d"), date("Y")); //max 1 month back

		/* check for synable items, that do not yet exist in the sync server */
		$q = sprintf("select id from filesys_files where folder_id = %2\$d and user_id = %1\$d and id NOT IN (
			select file_id from funambol_file_sync where user_id = %1\$d)",
			$this->user_id, $folder);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$this->syncRecord("files", "", $row["id"]);
		}

		/* if no items changed in syncserver, quit */
		if ($items === false)
			return false;

		/* get current covide db records */
		$q = sprintf("select guid, file_id, datetime from funambol_file_sync where user_id = %d", $this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$items_db[$row["guid"]] = $row;
		}
		$this->setActions($items, $items_db, "files");

		foreach ($items as $k=>$v) {
			switch ($v["action"]) {
				case "D":
					/* delete the appointment */
					/* delete from db */
					$fs_data->file_remove($v["file_id"], $folder, 1);

					$stats["D"]++;
					break;
				case "U":
					$file = $this->getFunambolPath("files", $v["guid"]);
					$data = $this->filecdata2array($file);
					//$data["id"] = $v["calendar_id"];

					foreach ($data as $kk=>$vv) {
						$db_data[$kk] = $vv;
					}

					/* update db, if item is not registered */
					$data["id"] = $v["file_id"];
					$data["folder_id"] = $folder;
					$db_data["id"] = $v["file_id"];
					$db_data["folder_id"] = $folder;

					$fs_data->file_upload_bindata($db_data);

					$q = sprintf("update funambol_file_sync set datetime = %d where guid = %d",
						mktime(), $v["guid"]);
					sql_query($q);
					$stats["U"]++;

					break;

				case "I":
					$file = $this->getFunambolPath("files", $v["guid"], $this->user_id);
					$data = $this->filecdata2array($file);
					$data["folder_id"] = $folder;

					/* insert into db, only if item is not aged */
					$id = $fs_data->file_upload_alt($data);

					//$ids = $todo_data->save_todo($data, 1);
					$q = sprintf("insert into funambol_file_sync (user_id, guid, file_id, datetime) values (%d, %d, %d, %d)",
						$this->user_id, $v["guid"], $id, mktime());
					sql_query($q);
					$stats["I"]++;

					break;
			}
		}
		$stats["C"] = 1;
		$GLOBALS["covide"]->sync_stats["files"] = $stats;
	}

	private function checkRecordsAddress($type, &$stats) {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($this->user_id);
		if (!$user_info["xs_funambol"])
			return false;

		/* get db store */
		$db = $this->getFunambolPath("address", 0);

		/* get records to be migrated from v3 to v6 */
		$q = sprintf("select * from funambol_address_sync_v3 where user_id = %d",
			$this->user_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* re-insert the record */
			$foo = $this->toggleAddressSync($this->user, $row["address_id"], $row["address_table"]);

			/* remove reference */
			$q = sprintf("delete from funambol_address_sync_v3 where id = %d", $row["id"]);
			sql_query($q);
		}

		/* retrieve items which are still present in the mobile device */
		#$current_items = $this->checkFS($db);
		$current_items = $this->checkDB($db);
		if ($current_items === false)
			return false;

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
		$address_data = new Address_data();

		/* check for modifications in the device */
		$this->setActions($current_items, $items_db, "address");

		/* diff all the items, there should be no differences */
		foreach ($current_items as $k=>$v) {
			switch ($v["action"]) {
				case "U":
					if ($v["address_table"] == "address_private") {
						$file = preg_replace("/0$/s", $v["guid"], $db);
						/* get old data from database */
						$data = $address_data->getAddressById($v["address_id"], "private");

						/* get new data from sync and merge it */
						$data_sync = $this->vcard2array($file);
						foreach ($data_sync as $k=>$x) {
							$data[$k] = $x;
						}
						$address_data->store2db($data, array(), 1);
						$q = sprintf("update funambol_address_sync set datetime = %d where guid = '%s'",
							mktime(), $v["guid"]);

						sql_query($q);
						$stats["U"]++;
					} else {
						if ($v["address_table"] == "address_businesscards")
							$vt = "bcards";
						else
							$vt = "relations";

						$this->updateAddressById($v["address_id"], $vt);
						$stats["R"]++;
					}
					break;
				case "I":
					/* insert into private store */
					$file = preg_replace("/0$/s", $v["guid"], $db);
					$data = $this->vcard2array($file);
					$id = $address_data->store2db($data, array(), 1);

					/* update sync added flag */
					$q = sprintf("update address_private set sync_added = %d where id = %d",
						mktime(), $id);
					sql_query($q);

					$q = sprintf("insert into funambol_address_sync (user_id, guid, address_id, address_table, datetime) values (%d, '%s', %d, 'address_private', %d)",
						$this->user_id, $v["guid"], $id, mktime());
					sql_query($q);
					$stats["I"]++;
					break;
				case "D":
					if ($v["address_table"] == "address_private") {
						/* allow delete */
						$address_data->delete($v["address_id"], "private", 1);
						$stats["D"]++;

						#$q = sprintf("delete from funambol_address_sync where guid = %d",
						#	$v["guid"]);
						$q = sprintf("update funambol_address_sync set address_id = 0, datetime = %d where guid = '%s'",
							mktime(), $v["guid"]);
						sql_query($q);

					} else {
						/* just toggle off */
						//$this->updateAddressById($v["address_id"], $v["address_table"], $this->user_id);
						#$q = sprintf("delete from funambol_address_sync where guid = %d and user_id = %d",
						#	$v["guid"], $this->user_id);
						$q = sprintf("update funambol_address_sync set address_id = 0, datetime = %d where guid = '%s' and user_id = %d",
							mktime(), $v["guid"], $this->user_id);
						sql_query($q);
						#$stats["X"]++;
					}
				}
		}
		/* address should never check for updates from device */
		$stats["C"] = 1;
		$GLOBALS["covide"]->sync_stats["address"] = $stats;
	}

	private function parseXML($file) {
		$data = file_get_contents($file, "r");
		$data = str_replace(array("<![CDATA[", "]]>"), array('', ''), $data);

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $data, $vals, $index);
		xml_parser_free($parser);

		return array(
			"index" => $index,
			"vals"  => $vals
		);
	}

	private function ical2array($file) {
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
						$data["from_day"]    = array(date("d", $data["timestamp_start"]));
						$data["from_month"]  = date("m", $data["timestamp_start"]);
						$data["from_year"]   = date("Y", $data["timestamp_start"]);
						$data["from_hour"]   = date("H", $data["timestamp_start"]);
						$data["from_min"]    = date("i", $data["timestamp_start"]);
						$data["from_minute"] = date("i", $data["timestamp_start"]);
						$data["timestamp_start_h"] = date("d-m-Y H:i", $data["timestamp_start"]);
						break;
					case "END":
						$data["timestamp_end"] = $this->iso86012timestamp($val, $this->offset);
						$data["to_day"]     = array(date("d", $data["timestamp_end"]));
						$data["to_month"]   = date("m", $data["timestamp_end"]);;
						$data["to_year"]    = date("Y", $data["timestamp_end"]);;
						$data["to_hour"]    = date("H", $data["timestamp_end"]);;
						$data["to_min"]     = date("i", $data["timestamp_end"]);;
						$data["to_minute"]  = date("i", $data["timestamp_end"]);;
						$data["timestamp_end_h"] = date("d-m-Y H:i", $data["timestamp_end"]);
						break;
					case "BODY":
						$data["description"] = addslashes(trim($val));
						break;
					case "SENSITIVITY":
						if ($val == "PRIVATE")
							$data["is_private"] = 1;
							break;
					case "SUBJECT":
						$data["subject"] = addslashes($val);
						break;
					case "LOCATION":
						$data["location"] = addslashes($val);
						break;
					case "REMINDERSET":
						#if ($val)
						#	$data["is_important"] = 1;
						#$data["is_important"] = 0; //always not set, PocketPC has a serious bug.
						break;
					case "REMINDERMINUTESBEFORESTART":
						#if ($val)
						#	$data["notifytime"] = $val * 60;
						break;
				}
			}
		}
		/* ical2array needs to support SIF-E as well, do some conversions */
		if ($data["from_hour"] == 0
			&& $data["from_min"] == 0
			&& $data["to_hour"] == 23
			&& $data["to_min"] == 59) {
			/* we deal with an all day event here */
			$data["is_event"] = 1;
		}

		return $data;
	}

	private function vcard2array($file) {
		/* notice: this function is only for inserting private addresses */
		$fn = $this->filesyspath.$file;

		$data = array(
			"user_id" => $this->user_id,
			"type"    => "private"
		);

		if (file_exists($fn)) {
			$xml = $this->parseXML($fn);
			foreach ($xml["vals"] as $v) {

				$key =& $v["tag"];
				$val =& $v["value"];

				switch ($key) {
					case "BIRTHDAY":
						$ts = explode("-", $val);
						if ($ts[0] && $ts[1] && $ts[2])
							$ts = mktime(0,0,0,$ts[1],$ts[2],$ts[0]);
							if ($ts == 0)
								$ts = 1; //work around for 1-1-1970
						else
							$ts = 0;

						$data["timestamp_birthday"] = $ts;
						break;
					case "BODY":
						$data["comment"] = addslashes($val);
						break;
					case "BUSINESS2TELEPHONENUMBER":
						$data["business_phone_nr_2"] = addslashes($val);
						break;
					case "BUSINESSADDRESSCITY":
						$data["business_city"] = addslashes($val);
						break;
					case "BUSINESSADDRESSCOUNTRY":
						$data["business_country"] = addslashes($val);
						break;
					case "BUSINESSADDRESSPOSTALCODE":
						$data["business_zipcode"] = addslashes($val);
						break;
					case "BUSINESSADDRESSSTATE":
						$data["business_state"] = addslashes($val);
						break;
					case "BUSINESSADDRESSSTREET":
						$data["business_address"] = preg_replace("/(\r)|(\n)/s", "", addslashes($val));
						break;
					case "BUSINESSFAXNUMBER":
						$data["business_fax_nr"] = addslashes($val);
						break;
					case "BUSINESSTELEPHONENUMBER":
						$data["business_phone_nr"] = addslashes($val);
						break;
					case "BUSINESSWEBPAGE":
						$data["website"] = addslashes($val);
						break;
					case "DEPARTMENT":
						$data["department"] = addslashes($val);
						break;
					case "EMAIL1ADDRESS":
						$data["business_email"] = addslashes($val);
						break;
					case "EMAIL2ADDRESS":
						$data["email"] = addslashes($val);
						break;
					case "EMAIL3ADDRESS":
						$data["other_email"] = addslashes($val);
						break;
					case "NICKNAME":
						$data["alternative_name"] = addslashes($val);
						break;
					case "FIRSTNAME":
						$data["givenname"] = addslashes($val);
						break;
					case "HOME2TELEPHONENUMBER":
						$data["phone_nr_2"] = addslashes($val);
						break;
					case "HOMEADDRESSCITY":
						$data["city"] = addslashes($val);
						break;
					case "HOMEADDRESSCOUNTRY":
						$data["country"] = addslashes($val);
						break;
					case "HOMEADDRESSPOSTALCODE":
						$data["zipcode"] = addslashes($val);
						break;
					case "HOMEADDRESSSTATE":
						$data["state"] = addslashes($val);
						break;
					case "HOMEADDRESSSTREET":
						$data["address"] = preg_replace("/(\n|\r)/s", "", addslashes($val));
						break;
					case "HOMEFAXNUMBER":
						$data["fax_nr"] = addslashes($val);
						break;
					case "HOMETELEPHONENUMBER":
						$data["phone_nr"] = addslashes($val);
						break;
					case "INITIALS":
						$data["contact_initials"] = addslashes($val);
						break;
					case "JOBTITLE":
						$data["jobtitle"] = addslashes($val);
						break;
					case "LASTNAME":
						$data["surname"] = addslashes($val);
						break;
					case "MIDDLENAME":
						$data["infix"] = addslashes($val);
						break;
					case "MOBILETELEPHONENUMBER":
						$data["mobile_nr"] = addslashes($val);
						break;
					case "OFFICELOCATION":
						$data["locationcode"] = addslashes($val);
						break;
					case "OTHERADDRESSCITY":
						$data["other_city"] = addslashes($val);
						break;
					case "OTHERADDRESSCOUNTRY":
						$data["other_country"] = addslashes($val);
						break;
					case "OTHERADDRESSPOSTALCODE":
						$data["other_zipcode"] = addslashes($val);
						break;
					case "OTHERADDRESSSTATE":
						$data["other_state"] = addslashes($val);
						break;
					case "OTHERADDRESSSTREET":
						$data["other_address"] = preg_replace("/(\r|\n)/s", "", addslashes($val));
						break;
					case "OTHERFAXNUMBER":
						$data["other_fax_nr"] = addslashes($val);
						break;
					case "OTHERTELEPHONENUMBER":
						$data["other_phone_nr"] = addslashes($val);
						break;
					case "CARTELEPHONENUMBER":
						$data["business_car_phone_nr"] = addslashes($val);
						break;
					case "COMPANYMAINTELEPHONENUMBER":
						$data["opt_company_phone_nr"] = addslashes($val);
						break;
					case "COMPANYNAME":
						$data["opt_company_name"] = addslashes($val);
						break;
					case "MANAGERNAME":
						$data["opt_manager_name"] = addslashes($val);
						break;
					case "PAGERNUMBER":
						$data["opt_pager_number"] = addslashes($val);
						break;
					case "PROFESSION":
						$data["opt_profession"] = addslashes($val);
						break;
					case "RADIOTELEPHONENUMBER":
						$data["opt_radio_phone_nr"] = addslashes($val);
						break;
					case "SPOUSE":
						$data["opt_spouse"] = addslashes($val);
						break;
					case "TELEXNUMBER":
						$data["opt_telex_number"] = addslashes($val);
						break;
					case "ASSISTANTNAME":
						$data["opt_assistant_name"] = addslashes($val);
						break;
					case "ASSISTANTTELEPHONENUMBER":
						$data["opt_assistant_phone_nr"] = addslashes($val);
						break;
				}
			}
		}
		return $data;
	}

	private function filecdata2array($file) {
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
					case "NAME":
						$data["name"] = $val;
						break;
					case "BODY":
						$data["base64"] = $val;
						break;
					case "SIZE":
						$data["size"] = $val;
						break;
				}
			}
		}

		return $data;
	}

	private function sift2array($file) {
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
					case "STARTDATE":
						if ($val)
							$data["timestamp"]  = $this->iso86012timestamp($val);
						else
							$data["timestamp"]  = mktime(0,0,0,1,1,1970);

						$data["timestamp_day"]   = date("d", $data["timestamp"]);
						$data["timestamp_month"] = date("m", $data["timestamp"]);
						$data["timestamp_year"]  = date("Y", $data["timestamp"]);
						break;
					case "DUEDATE":
						if ($val)
							$data["timestamp_end"]  = $this->iso86012timestamp($val);
						else
							$data["timestamp_end"]  = mktime(0,0,0,1,1,2030);

						$data["timestamp_end_day"]   = date("d", $data["timestamp_end"]);
						$data["timestamp_end_month"] = date("m", $data["timestamp_end"]);
						$data["timestamp_end_year"]  = date("Y", $data["timestamp_end"]);
						break;
					case "SENSITIVITY":
						if ($val == 2)
							$data["is_private"] = 1;
						else
							$data["is_private"] = 0;
						break;
					case "BODY":
						$data["body"] = addslashes(trim($val));
						break;
					case "SUBJECT":
						$data["subject"] = addslashes($val);
						break;
					case "IMPORTANCE":
						if ($val == 2)
							$data["is_alert"] = 1;
						else
							$data["is_alert"] = 0;
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
			case "todo":
				$v["todo_id"]       = $items_db[$v["guid"]]["todo_id"];
				break;
			case "files":
				$v["file_id"]       = $items_db[$v["guid"]]["file_id"];
		}
	}

	private function setActions(&$items, &$items_db, $mode="calendar") {
		if (!is_array($items))
			$items = array();

		/* (D)elete, (I)nsert, (U)pdate or empty */
		foreach ($items as $k=>$v) {
			/*
			if ($v["state"] == "D") {
				if ($items_db[$v["guid"]]) {
					$v["action"] = "D";
					$this->setItemVal(&$v, &$items_db, $mode);
				}
			} else
			*/
			if ($items_db[$v["guid"]]) {
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
				$items_db[$v["guid"]]["seen"] = 1;

			} else {
				/* need insert */
					$v["action"] = "I";
			}
			$items[$k] = $v;

			/*if (!$v["action"]) {
				#$v["action"] = "S";
				unset($items[$k]);
			} else {
			}
			*/
		}

		foreach ($items_db as $k=>$v) {
			if (!$v["seen"] && (
				$v["address_id"] > 0 || $v["calendar_id"] > 0 ||
				$v["todo_id"] > 0 || $v["file_id"] > 0)) {

				if ($items_db[$v["guid"]]) {
					$items[$v["guid"]] = array(
						"guid"   => $v["guid"],
						"action" => "D"
					);
					$this->setItemVal(&$items[$v["guid"]], &$items_db, $mode);
				}
			}
		}
	}

	private function getSourceHash($db) {
		$db = preg_replace("/\/$/s", "", $db);
		$db = explode("/", $db);

		$db = sprintf("%s/%s", $db[count($db)-2], $db[count($db)-1]);
		$source = $db;
		/* check hash against db */
		$q = sprintf("select * from funambol_stats where source = '%s'",
			$source);

		$res = sql_query($q);
		if (sql_num_rows($res) == 1) {
			$row = sql_fetch_assoc($res);
		} else {
			$q = sprintf("insert into funambol_stats (source, lasthash) values ('%s', '%s')",
				$source, $hash);
			sql_query($q);
			$row = array();
		}
		return $row;
	}

	private function checkSourceHash($db_fs, $update_db=0, $sync_ts=0) {

		$db = $db_fs;
		$db = preg_replace("/\/$/s", "", $db);
		$db = explode("/", $db);

		$db = sprintf("%s/%s", $db[count($db)-2], $db[count($db)-1]);
		$source = $db;

		$q = sprintf("select lasthash from funambol_stats where source = '%s'", $source);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0) {
			/* add the source */
			$q = sprintf("insert into funambol_stats (source) values ('%s')", $source);
			sql_query($q);
		} else {
			$lasthash = sql_result($res,0);
			if ($lasthash > 1000000000000) {
				$q = sprintf("update funambol_stats set lasthash = '0' where source = '%s'",
					$source);
				sql_query($q);
				$lasthash = 0;
			}
		}

		if ($sync_ts) {
			/* funambol client */
			$q = sprintf("update funambol_stats set synchash = '%s' where source = '%s'",
				$sync_ts, $source);
			sql_query($q);
			return true;
		} else {
			/* covide */
			$cmd = sprintf("ls -t %s", escapeshellarg($db_fs));
			exec($cmd, $ret, $retval);
			$filesys_ts = filemtime(sprintf("%s/%s", $db_fs, $ret[0]));

			switch ($update_db) {
				case 0:
					$db_ts = $lasthash;
					break;
				case 1:
					$q = sprintf("update funambol_stats set lasthash = '%s' where source = '%s'", $filesys_ts, $source);
					sql_query($q);
					$db_ts = $filesys_ts;
					break;
			}
			if ($filesys_ts > $db_ts) {
				#echo 1;
				return true;
			} else {
				#echo 0;
				return true; //false;
			}
		}
	}

	private function checkDB($db) {
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;

		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($this->user_id);

		/*
		$db = preg_replace("/\/$/s", "", $db);
		$db_t = explode("/", $db);
		$type = $db_t[count($db_t)-1];
		$db_file = sprintf("%s/../%s.sync.db", $db, $type);
		*/

		if (!$user_info["xs_funambol"])
			return false;

		/* check source hash against database */
		$need_sync = $this->checkSourceHash($db);
		#$need_sync = 1;

		if ($need_sync) {
			$dir = scandir($db);
			foreach ($dir as $file) {
				$v = sprintf("%s%s", $db, $file);
				if (is_file($v)) {
					$items[$file] = array(
						"guid"     => $file,
						"state"    => "S",
						"datetime" => filemtime($v)
					);
				}
			}

			/* update to db */
			$this->checkSourceHash($db, 1);

			//echo "sync! ";
			return $items;
		} else {
			return false;
		}
	}
	private function checkFS($db) {
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;

		if (!file_exists($db))
			die("fs error!");

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
				$q = sprintf("select guid, user_id from funambol_calendar_sync where calendar_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res)>0) {

					$guid = sql_result($res, 0, "guid");
					$user = sql_result($res, 0, "user_id");
					$this->deleteRecord($type, $guid, $user);
				}
				break;
			case "todo":
				$q = sprintf("select guid from funambol_todo_sync where todo_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res)>0) {
					$guid = sql_result($res,0);
					$this->deleteRecord($type, $guid);
				}
				break;
			case "files":
				$q = sprintf("select guid from funambol_file_sync where file_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res)>0) {
					$guid = sql_result($res,0);
					$this->deleteRecord($type, $guid);
				}
				break;
		}
	}
	public function syncRecord($type, $guid, $id=0) {

		/* check if the requested user has sync */
		$q = sprintf("select xs_funambol from users where id = %d", $this->user_id);
		$res = sql_query($q);
		if (sql_result($res,0) == 0)
			return false;


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
					$data["contact_initials"] = $data["initials"];
				}

				if ($subtype == "bcards") {
					/* not sure if this does still apply, but it can't harm */
					$data["phone_nr"]  = $data["business_phone_nr"];
					$data["fax_nr"]    = $data["business_fax_nr"];
					$data["mobile_nr"] = $data["business_mobile_nr"];
					$data["email"]     = $data["business_email"];
					$data["address"]   = $data["business_address"];
					$data["country"]   = $data["business_country"];
					$data["zipcode"]   = $data["business_zipcode"];
					$data["city"]      = $data["business_city"];
					$data["state"]     = $data["business_state"];
					/* end obsolete(?) code */

					if ($data["opt_company_name"])
						$data["companyname"] = $data["opt_company_name"];
					else
						$data["companyname"] = $address_data->getAddressNameByID($data["address_id"]);
				} elseif ($subtype == "private") {
					$data["companyname"]    = $data["opt_company_name"];
					$data["memo"]           = $data["comment"];

					$data["personal_email"]      = $data["email"];
					$data["personal_city"]       = $data["city"];
					$data["personal_country"]    = $data["country"];
					$data["personal_zipcode"]    = $data["zipcode"];
					$data["personal_state"]      = $data["state"];
					$data["personal_address"]    = $data["address"];
					$data["personal_fax_nr"]     = $data["fax_nr"];
					$data["personal_phone_nr"]   = $data["phone_nr"];
					$data["personal_phone_nr_2"] = $data["phone_nr_2"];
					$data["initials"]            = $data["contact_initials"];
				} else {
					/* relations */
					$data["business_address"]  = $data["address"];
					$data["business_city"]     = $data["city"];
					$data["business_country"]  = $data["country"];
					$data["business_zipcode"]  = $data["zipcode"];
					$data["business_state"]    = $data["state"];
					$data["business_fax_nr"]   = $data["fax_nr"];
					$data["business_phone_nr"] = $data["phone_nr"];
					$data["business_email"]    = $data["email"];

					$data["givenname"]         = $data["contact_givenname"];
					$data["surname"]           = $data["contact_surname"];
					$data["initials"]          = $data["contact_initials"];
					$data["infix"]             = $data["contact_infix"];
				}

				/* translate title, letterhead, commencement and suffix */
				$letterheads   = $address_data->getLetterheads();
				$commencements = $address_data->getCommencements();
				$titles        = $address_data->getTitles();
				$suffix        = $address_data->getSuffix();

				if (is_numeric($data["contact_commencement"]))
					$data["contact_commencement"] = $commencements[$data["contact_commencement"]];

				if (is_numeric($data["contact_letterhead"]))
					$data["contact_letterhead"] = $letterheads[$data["contact_letterhead"]];

				if (is_numeric($data["title"]))
					$data["title"] = $titles[$data["title"]];

				if (is_numeric($data["suffix"]))
					$data["suffix"] = $suffix[$data["suffix"]];

				/* end of translations */

				if (!$data["mobile_nr_business"])
					$data["mobile_nr_business"] = "";

				$data["categories"] = explode("\n", $data["classification_names"]);
				$data["categories"] = preg_replace("/(,|\t|\r)/s", " ", $data["categories"]);
				$data["categories"] = implode(",",$data["categories"]);

				if ($data["contact_birthday"]) {
					$data["birthday_date"] = date("Y-m-d", $data["contact_birthday"]);
				} elseif ($data["timestamp_birthday"]) {
					$data["birthday_date"] = date("Y-m-d", $data["timestamp_birthday"]);
				} else {
					$data["birthday_date"] = "";
				}

				$xml = file_get_contents(self::include_dir."address.xml");

				$data["fileas"] = sprintf("%s, %s %s",
					$data["contact_surname"],
					$data["contact_givenname"],
					$data["contact_infix"]
				);
				$data["fileas"] = preg_replace("/ {1,}/s", " ", trim($data["fileas"]));
				$data["fileas"] = preg_replace("/,$/s", "", $data["fileas"]);

				switch ($subtype) {
					case "relations":
						$data["fileas"].= sprintf(" (%s)", gettext("relation"));
						break;
					case "bcards":
						$data["fileas"].= sprintf(" (%s)", gettext("bcard"));
						break;
				}
				$data["fileas"] = trim($data["fileas"]);

				$this->mergeXmlData($data, $xml);
				$md5 = md5($xml);

				/* debug info, please leave it intact */
				#header("Content-Type: text/xml");
				#echof $xml;
				#die();

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
				$data = $calendar_data->getCalendarItemById($id, 1);

				/* fill some variables */
				if ($data["is_event"]) {
					$data["datetime_start"] = date("Y-m-d", $data["timestamp_start"]);
					$data["datetime_end"]   = date("Y-m-d", $data["timestamp_end"]);
				} else {
					$data["datetime_start"] = $this->timestamp2iso8601($data["timestamp_start"]); //-$this->offset);
					$data["datetime_end"]   = $this->timestamp2iso8601($data["timestamp_end"]); //- $this->offset);
				}

				if (in_array($data["repeat_type"], array("D","M","Y")))
					$data["recurring"] = 1;
				else
					$data["recurring"] = 0;


				$data["category"] = "";
				if ($data["is_private"])
					$data["sensitivity"]    = 2; //= "PRIVATE";
				else
					$data["sensitivity"]    = 0; //= "PUBLIC";

				$data["reminder"]           = ($data["is_important"]) ? 1:0;
				$data["reminder_min"]       = (int)($data["notifytime"]/60);

				$data["reminder_options"]   = ($data["is_important"]) ? 8:0;
				$data["reminder_interval"]  = 0;

				if ($data["is_registered"])
					$data["description"].= " (registered)";

				/* parse xml file */
				$xml = file_get_contents(self::include_dir."calendar.xml");
				$this->mergeXmlData($data, $xml);

				/* all day events need a custom format */

				if ($data["repeat_type"] == "D" && $data["is_repeat"] == 1) {
					/* daily repeating */
					$rpt = file_get_contents(self::include_dir."calendar_repeat_daily.xml");
					$rpt = str_replace("{repeat_start_date}", $data["datetime_start"], $rpt);
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				}	 elseif ($data["repeat_type"] == "D" && $data["is_repeat"] == 7) {
					/* weekly */
					$rpt = file_get_contents(self::include_dir."calendar_repeat_weekly.xml");
					$rpt = str_replace("{repeat_start_date}", $data["datetime_start"], $rpt);
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				} elseif ($data["repeat_type"] == "D") {
					/* 2 weekly */
					$rpt = file_get_contents(self::include_dir."calendar_repeat_2weekly.xml");
					$rpt = str_replace("{repeat_start_date}", $data["datetime_start"], $rpt);
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				} elseif ($data["repeat_type"] == "M") {
					/* monthly */
					$rpt = file_get_contents(self::include_dir."calendar_repeat_monthly.xml");
					$rpt = str_replace("{repeat_day}", (int)date("d", $data["timestamp_start"]), $rpt);
					$rpt = str_replace("{repeat_month}", (int)date("m", $data["timestamp_start"]), $rpt);
					$rpt = str_replace("{repeat_start_date}", $data["datetime_start"], $rpt);
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				} elseif ($data["repeat_type"] == "Y") {
					/* yearly */
					$rpt = file_get_contents(self::include_dir."calendar_repeat_yearly.xml");
					$rpt = str_replace("{repeat_day}", (int)date("d", $data["timestamp_start"]), $rpt);
					$rpt = str_replace("{repeat_month}", (int)date("m", $data["timestamp_start"]), $rpt);
					$rpt = str_replace("{repeat_start_date}", $data["datetime_start"], $rpt);
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				} else {
					$xml = str_replace("<repeatmarker />", $rpt, $xml);
				}

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
					if ($old_guid)
						$guid = $old_guid;
					else
						$guid = $this->getNextFreeGuid("", 0, $data["id"]);

					$file = $this->getFunambolPath($type, $guid, $data["user_id"]);
					$ts = $this->writeFile($file, $xml);

					if ($old_guid)
						$q = sprintf("update funambol_calendar_sync set file_hash = '%s', datetime = %d where guid = %d",
							$md5, $ts, $guid);
					else
						$q = sprintf("insert into funambol_calendar_sync (guid, calendar_id, user_id, file_hash, datetime) values (%d, %d, %d, '%s', %d)",
							$guid, $id, $data["user_id"], $md5, $ts);

					sql_query($q);

					/* notify the store for the old record */
					#if ($old_guid)
					#	$this->deleteRecord($type, $old_guid);
				}
				break;

			case "todo":
				/* get appointment info */
				$todo_data = new Todo_data();
				$data = $todo_data->getTodoById($id);

				$data["start_date"] = date("Y-m-d", $data["timestamp"]);
				$data["end_date"]   = date("Y-m-d", $data["timestamp_end"]);

				if ($data["is_alert"])
					$data["importance"] = 2;
				else
					$data["importance"] = 1;

				/* parse xml file */
				$xml = file_get_contents(self::include_dir."todo.xml");
				$this->mergeXmlData($data, $xml);

				/* calculate a hash */
				$md5 = md5($xml);

				/* check if current appointment does exist */
				$q = sprintf("select * from funambol_todo_sync where todo_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res) == 1) {
					/* found! */
					$row = sql_fetch_assoc($res);
					$file_hash = $row["file_hash"];
					$old_guid = $row["guid"];
				}

				if ($file_hash != $md5) {
					/* if record has been changed */
					if ($old_guid)
						$guid = $old_guid;
					else
						$guid = $this->getNextFreeGuid("todo", $this->user_id, $data["id"]);

					$file = $this->getFunambolPath($type, $guid, $this->user_id);
					$ts = $this->writeFile($file, $xml);


					if ($old_guid)
						$q = sprintf("update funambol_todo_sync set file_hash = '%s', datetime = %d where guid = %d",
							$md5, $ts, $guid);
					else
						$q = sprintf("insert into funambol_todo_sync (guid, todo_id, user_id, file_hash, datetime) values (%d, %d, %d, '%s', %d)",
							$guid, $id, $data["user_id"], $md5, $ts);

					sql_query($q);

					/* notify the store for the old record */
					#if ($old_guid)
						#$this->deleteRecord($type, $old_guid);
				}
				break;
			case "files":
				/* get file info */
				$fs_data = new Filesys_data();
				$data = $fs_data->getFileById($id);

				#if (strlen($data["binary"]) > 8192)
				#	return false;

				$data["base64"] = base64_encode($data["binary"]);
				unset($data["binary"]);

				/* parse xml file */
				$xml = file_get_contents(self::include_dir."file_cdata.xml");
				$this->mergeXmlData($data, $xml);

				/* calculate a hash */
				$md5 = md5($xml);

				/* check if current appointment does exist */
				$q = sprintf("select * from funambol_file_sync where file_id = %d", $id);
				$res = sql_query($q);
				if (sql_num_rows($res) == 1) {
					/* found! */
					$row = sql_fetch_assoc($res);
					$file_hash = $row["file_hash"];
				}

				if ($file_hash != $md5) {
					//old guid is impossible for files!

					/* if record has been changed */
					$guid = $this->getNextFreeGuid("files", 0, $data["id"]);

					$file = $this->getFunambolPath($type, $guid, $this->user_id);
					$ts = $this->writeFile($file, $xml, 1);

					$q = sprintf("insert into funambol_file_sync (guid, file_id, user_id, file_hash, datetime) values (%d, %d, %d, '%s', %d)",
						$guid, $id, $data["user_id"], $md5, $ts);

					sql_query($q);
				}
				break;

		}
	}

	private function mergeXmlData(&$data, &$xml) {
		foreach ($data as $k=>$v) {
			if (!is_array($v)) {
				/* no html tags please */
				$v = preg_replace("/<br[^>]*?>/si", "\n", $v);
				$v = preg_replace("/<li[^>]*?>/si", "- ", $v);
				$v = preg_replace("/<\/li[^>]*?>/si", "\n", $v);
				$v = preg_replace("/<\/p[^>]*?>/si", "\n\n", $v);
				$v = strip_tags($v);

				/* only utf-8 */
				$v = $this->convertEncoding($v);

				/* some custom euro stuff */
				$v = str_replace("&", "&amp;", $v);

				$v = str_replace("&euro;", chr(hexdec("0xE2")).chr(hexdec("0x82")).chr(hexdec("0xAC")), $v);

				/* parse it */
				$xml = str_replace(sprintf("{%s}", $k), $v, $xml);
			}
		}
		$xml = preg_replace("/\{[^\}]*?\}/s", "", $xml);
	}
	private function convertEncoding($str) {
		$str = preg_replace("/(\r|\t)/s", "", $str);
		$str = $this->conversion->str2utf8($str);
		#$str = htmlentities($str, ENT_NOQUOTES, "UTF-8");
		//$str = str_replace("\n", "\t\r\n", $str); //bugfix for devices
		return $str;
	}

	public function getNextFreeGuid($type="", $user=0, $current_id=0) {
		if ($current_id)
			return $current_id;
		else
			die("no id specified:". $type);


		$next = -1;

		while ($next != $next_expected) {
			/* get next counter */
			$q = sprintf("select counter+1 from fnbl_id where idspace = 'guid'");
			$res = sql_query($q, $this->funabol_db);
			$next_expected = sql_result($res,0);

			/* update counter */
			//$q = sprintf("update fnbl_id set counter = counter+1 where idspace = 'guid'");
			//sql_query($q, $this->funabol_db);

			/* re-read the counter */
			//$q = sprintf("select counter from fnbl_id where idspace = 'guid'");
			//$res = sql_query($q, $this->funabol_db);
			//$next = sql_result($res,0);

			$next = $next_expected;
			//$next = $next_expected+1;
		}

		/*---------------------------------------------------------------------------------
		/* The following part is to prevent caching of inserted id's in the Funambol server
		/* We don't want Covide to go throught the whole SyncML call, so we only use SyncML
		/* to reserve the id's we need
		/*---------------------------------------------------------------------------------
		 */

		/* reserve a number */
		$file = sprintf("%s/%s", $db, $next);

		$contents = "\n";
		file_put_contents($file, $contents);

		$this->execFunambolCl();

		return $next;
	}

	public function execFunambolCl() {
		/* exec reserve call */
		$cmd = sprintf("cd classes/funambol/plug-ins/cl && sh run.sh");
		exec($cmd, $ret, $retval);

		if ($retval != 0) {
			echo "<PRE>";
			if ($retval == 127)
				die("Java not found in /bin/java");
			if (file_exists("tmp/funambol.log"))
				echo file_get_contents("tmp/funambol.log");

			echo "\n\n";
			die("An unknown sync error occured.");
		} else {
			#print_r($ret);
			#die();
		}
	}

	public function getCurrentGuidCounter() {
		$q = sprintf("select counter from fnbl_id where idspace = 'guid'");
		$res = sql_query($q, $this->funabol_db);
		$curr = sql_result($res,0);
		return $curr;
	}

	private function truncateFile($file) {
		$file = $this->filesyspath.$file;
		if (file_exists($file)) {
			if (is_writable($file)) {
				unlink($file);
			} else {
				#echo "alert('cannot unlink file');";
			}
		}
	}
	private function writeFile($file, $data, $binary_mode=0) {
		/* strip cr */
		$data = str_replace("\r", "", $data);

		if (!$binary_mode) {
			/* convert lf to crlf */
			$data = str_replace("\n", "\r\n", $data);

		} else {
			/* use strict binary xml format, no cr and end with newline */
			$data = trim($data);
			$data = $data."\n";
		}

		/* get full file path and write file to disk */
		$file = $this->filesyspath.$file;
		if (!preg_match("/\/0$/s", $file) && file_exists(dirname($file)))
			file_put_contents($file, $data);

		if (file_exists($file))
			$mtime = filemtime($file);
		else
			$mtime = false;

		return $mtime;
	}

	private function updateDB($db, $guid, $state) {
		return true;

		/*
		$db = preg_replace("/0$/s", "", $db);
		$db = $this->filesyspath.$db;
		if (file_exists($db))
			$dir = scandir($db);
		else
			$dir = array();

		foreach ($dir as $file) {
			if (preg_match("/\.db$/s", $file)) {
				$fn = sprintf("%s/%s", $db, $file);
				$data = sprintf("%d=%s%d000\n", $guid, $state, mktime());
				file_put_contents($fn, $data, FILE_APPEND);
			}
		}
		*/
	}

	public function deleteRecord($type, $guid, $user_id = 0) {

		if (!$user_id)
			$user_id = $this->user_id;

		switch ($type) {
			case "address":
				$db = $this->getFunambolPath("address", 0);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("address", $guid);
				$this->truncateFile($file);

				#$q = sprintf("delete from funambol_address_sync where guid = %d", $guid);
				$q = sprintf("update funambol_address_sync set address_id = 0, datetime = %d where guid = '%s'", mktime(), $guid);
				sql_query($q);
				break;
			case "calendar":
				$db = $this->getFunambolPath("calendar", 0, $user_id);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("calendar", $guid, $user_id);
				$this->truncateFile($file);

				#$q = sprintf("delete from funambol_calendar_sync where guid = %d", $guid);
				$q = sprintf("update funambol_calendar_sync set calendar_id = 0, datetime = %d where guid = '%s'", mktime(), $guid);
				sql_query($q);
				break;
			case "todo":
				$db = $this->getFunambolPath("todo", 0);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("todo", $guid);
				$this->truncateFile($file);
				#$q = sprintf("delete from funambol_todo_sync where guid = %d", $guid);
				$q = sprintf("update funambol_todo_sync set todo_id = 0, datetime = %d where guid = '%s'", mktime(), $guid);
				sql_query($q);
				break;
			case "files":
				$db = $this->getFunambolPath("files", 0);
				$this->updateDB($db, $guid, "D");

				$file = $this->getFunambolPath("files", $guid);
				$this->truncateFile($file);
				#$q = sprintf("delete from funambol_file_sync where guid = %d", $guid);
				$q = sprintf("update funambol_file_sync set file_id = 0, datetime = %d where guid = '%s'", mktime(), $guid);
				sql_query($q);
				break;
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
	public function updateAddressById($id, $type, $limit_user=0) {
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
			if (!$limit_user || $limit_user == $row["user_id"]) {
				/* sync the record to disk */
				$this->syncRecord("address", $row["guid"]);

				#$this->toggleAddressSync($row["user_id"], $row["address_id"], $row["address_table"]);
				#$this->toggleAddressSync($row["user_id"], $row["address_id"], $row["address_table"]);
			}
		}
	}

	public function reset_user($user_id=0) {
		$this->user_id =& $user_id;

		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		/* get filesys paths */
		$types = array(
			"todo"     => "funambol_todo_sync",
			"address"  => "funambol_address_sync",
			"calendar" => "funambol_calendar_sync",
			"files"    => "funambol_file_sync"
		);

		/* get last used sync id */
		$curr = $this->getCurrentGuidCounter();

		/* now do one insane thing, mark *all* appointments ever created as deleted in the device */
		$template = "#FileSystemSyncSource file database\n";
		$template.= sprintf("#%s\n", date("r"));
		for ($i=1;$i<=$curr;$i++) {
			$template.= sprintf("%d=D%d000\n", $i, mktime());
		}
		foreach ($types as $t=>$table) {
			/* get filesys dir */
			$db = $this->getFunambolPath($t, "", $user_id);
			$dir = preg_replace("/0$/s", "", $GLOBALS["covide"]->filesyspath."/../funambol/".$db);

			/* now truncate all funambol user records */
			$q = sprintf("delete from %s where user_id = %d", $table, $user_id);
			#sql_query($q);

			/* now truncate the file store (except db files) */
			$folder = scandir($dir);
			foreach ($folder as $file) {
				if (!is_dir($file)) {
					if (preg_match("/\.db$/s", $file)) {
						/* file is a db file, overwrite with generated template */
						file_put_contents($dir.$file, $template);
					} elseif (is_numeric(basename($file))) {
						/* file is an appointment, unlink it */
						unlink($dir.$file);
					}
				}
			}
			$this->checkSourceHash($db, 2);
		}
		$output = new Layout_output();
		$output->layout_page("sync", 1);
			$venster = new Layout_venster(array("title"=>gettext("users")));
			$venster->addMenuItem(gettext("close"), "javascript: window.close();");
			$venster->generateMenuItems();
			$venster->addVensterData();
				$venster->addCode(gettext("The device state has been reset."));
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("Please do sync with your mobile device(s) now as soon as possible to reset your device. This initial sync could take some time. If a timeout occurs during this sync, please retry the operation until it's complete."));
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function checkUserSyncState($user_id, $mode="contacts") {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($user_id);
		if ($user_info["xs_funambol"]) {
			/* get base path */
			$dir = sprintf("%s%s/%s/*.db", $this->filesyspath, $mode, $user_info["mail_user_id"]);

			$cmd = sprintf("ls %s", $dir);
			if ($retval == 0)
				return true;
		}
		return false;
	}
}
?>
