<?
	if (!class_exists("Sync4j")) {
		die("no class defenition found");
	};
	//update php execution time naar 10 min!
	set_time_limit(600);
	$sync4j_convert = new Sync4j_convert();
	$sync4j_calendar = new Sync4j_calendar();

	require_once(self::include_dir."../sync_db.php");

	/* ----------------------------------------------------------------------------------- */
	/* some settings   																																		 */
	/* ----------------------------------------------------------------------------------- */
	$user_id = (int)$_REQUEST["user_id"];

	$defpath = $GLOBALS["covide"]->filesyspath;

	$q = sprintf("select id, sync4j_source, sync4j_path from users where id = %d", $user_id);
	$res = sql_query($q);
	if (sql_num_rows($res)>0) {
		$row = sql_fetch_array($res);

		$sync["covide_user"] = $row["id"];
		$sync["source"]      = $row["sync4j_source"];
		$sync["path"]        = $defpath."/syncml/calendar/".$row["sync4j_path"]."/";
	}
	print_r($sync);

	if (!$sync["covide_user"] || !$sync["source"] || !$sync["path"]) {
		die("error: not all user vars are set or user not found");
	}

	if (!file_exists($sync["path"])) {
		die("error: filesys path does not exist");
	}


	$sync["max_age"] = 1; //in months

	$filesys = $sync["path"];

	/* determine offset from gmt_zone
	/* example:
	/* sync_server = 0700-0900
	/* covide server = 0900-1100 */

	//hour x 60 min x 60 sec
	if (strftime("%Z")=="CET") {
		$sync["gmt_offset"] = 1*3600;
	} else {
		$sync["gmt_offset"] = 2*3600;
	}


	$fp_array = array();
	$db_index = $sync4j_convert->getSyncDb($filesys, $fp_array); //retrieve sync db index file(s)

	/* ----------------------------------------------------------------------------------- */
	/* Process updated/deleted items in covide=>syncserver																 */
	/* ----------------------------------------------------------------------------------- */
	$q = sprintf("select * from agenda_sync where user_id = %d", $sync["covide_user"]);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {
		$guid = $row["sync_guid"];
		$user_id = $row["user_id"];

		if ($row["action"]=="U") {
			$q = sprintf("select * from calendar where user_id = %d and sync_guid = %d", $user_id, $guid);
			$res2 = sql_query($q);
			$row2 = sql_fetch_assoc($res2);

			//get all new items from Covide => SyncServer
			$subject = stripslashes($row2["subject"]);
			$body = stripslashes($row2["description"]);
			$start = $row2["timestamp_start"]-$sync["gmt_offset"];
			$end = $row2["timestamp_end"]-$sync["gmt_offset"];
			$location = $row2["location"];

			//create a new ical xml item
			$xml = $sync4j_calendar->calendar2sync($subject, $body, $start, $end, $location);
			$hash = md5($xml);

			//fwrite
			$out = fopen($filesys.$guid, "w");
			fwrite($out, $xml);
			fclose($out);

			$q = sprintf("update calendar set sync_hash = '%s' where sync_guid = %d and user_id = %d", $hash, $guid, $user_id);
			sql_query($q);

			echo "updated item (covide=>syncserver) ...\n";

			$db_index = $sync4j_convert->insertSyncDb($guid, $db_index, "U");

		} elseif ($row["action"]=="D") {

			//insert delete statement into index
			$db_index = $sync4j_convert->insertSyncDb($guid, $db_index, "D");

			@unlink($filesys.$guid);

			$q = sprintf("update calendar set sync_guid = 0, sync_hash = '' where sync_guid = %d and user_id = %d", $guid, $user_id);
			sql_query($q);

			echo "deleted item (covide=>syncserver) ...\n";
		}
	}
	$q = sprintf("delete from agenda_sync where user_id = %d", $sync["covide_user"]);
	sql_query($q);


	/* ----------------------------------------------------------------------------------- */
	/* get all NEW items in Covide, but not in Syncserver (producte sync_guid + sync_hash) */
	/* ----------------------------------------------------------------------------------- */
	$min_date = mktime(0,0,0,date("m")-$sync["max_age"],date("d"),date("Y"));

	$q = sprintf("select * from calendar where timestamp_start > %d and user_id = %d and (sync_guid = 0 or sync_guid is null) order by id", $min_date, $sync["covide_user"]);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {

		$subject = stripslashes($row["subject"]);
		$body = stripslashes($row["description"]);
		if ($row["is_registered"]==1) {
			$busystatus = 3;
			$body.= "(geaccordeerd)";
		}
		$start = $row["timestamp_start"]-$sync["gmt_offset"];
		$end = $row["timestamp_end"]-$sync["gmt_offset"];
		$location = $row["location"];

		//create a new ical xml item
		$xml = $sync4j_calendar->calendar2sync($subject, $body, $start, $end, $location, $busystatus);

		//retrieve max luid and principal for this user/source to get the current syncrange
		$q = sprintf("select count(*) from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
		$res2 = pg_query($db_sync, $q);
		if (pg_fetch_result($res2,0)==0)
			die ("please insert at least one appointment in the sync client first");

		$q = sprintf("select max(luid) as luid, max(principal) as principal from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
		$res2 = pg_query($db_sync, $q);
		$luid = pg_fetch_result($res2,0,"luid");
		$luid = substr($luid, 0, strlen($luid)-3).rand(100,999);

		$principal 	= (int)pg_fetch_result($res2,0,"principal");

		$q = "select counter as guid from sync4j_id where idspace = 'guid'";
		$res2 = pg_query($db_sync, $q);
		$guid = (int)pg_fetch_result($res2,0,"guid")+1;

		//fwrite
		$out = fopen($filesys.$guid, "w");
		fwrite($out, $xml);
		fclose($out);

		//insert a new free record into syncmappings
		$q = "insert into sync4j_client_mapping (principal, sync_source, luid, guid) values ($principal, '".$sync["source"]."', '$luid',$guid)";
		pg_query($db_sync,$q);

		//update the covide record
		$hash = md5($xml);

		$q = sprintf("update calendar set sync_hash = '%s', sync_guid = %d where id = %d", $hash, $guid, $row["id"]);
		sql_query($q);

		$db_index = $sync4j_convert->insertSyncDb($guid, $db_index,"N");

		//update db to a higher guid value
		$q = "update sync4j_id set counter = $guid where idspace = 'guid'";
		pg_query($db_sync, $q);

		echo "record inserted (covide=>syncserver) ...\n";
	}


	/* ----------------------------------------------------------------------------------- */
	/* get all items in Covide and the SyncServer with sync hash and guid									 */
	/* ----------------------------------------------------------------------------------- */
	$items_covide = array();
	$items_server = array();


	//query items from covide table
	$q = sprintf("select * from calendar where user_id = %d and sync_guid > 0", $sync["covide_user"]);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {
		$items_covide[$row["sync_guid"]] = $row["sync_hash"];
	}

	//query all items from mapping table in the sync server
	$cmd = "md5sum ".$filesys."*";
	exec ($cmd, $ret, $retval);

	foreach ($ret as $line) {
		$line = preg_replace("/ {1,}/s"," ",$line);
		$v = explode(" ",$line);
		if (is_numeric(basename($v[1])) && !preg_match("/\.db$/si",basename($v[1])))
			$items_server[basename($v[1])]=$v[0];
	}

	//search for deleted items sync=>covide
	foreach ($items_covide as $k=>$v) {
		//check if covide item is still in sync server
		if (!array_key_exists($k,$items_server)) {
			//delete covide appointment
			$q = sprintf("delete from calendar where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
			sql_query($q);
			echo "record deleted (covide) ...\n";
		}
	}
	//search for new items in sync=>covide
	foreach ($items_server as $k=>$v) {
		if (!array_key_exists($k,$items_covide)) {
			$data = $sync4j_calendar->calendar2covide($filesys.$k);
			/* check for conflict */
			$sql_chk  = sprintf("SELECT COUNT(*) as counter FROM calendar WHERE user_id = %d AND (((timestamp_start BETWEEN %d AND %d) OR (timestamp_end BETWEEN %d AND %d))", $sync["covide_user"], ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]-1), ($data["start"]+1+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]));
			$sql_chk .= sprintf(" OR (timestamp_start<=%d AND timestamp_end>=%d))", ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]));
			$res_chk  = sql_query($sql_chk);
			$row_chk  = sql_fetch_assoc($res_chk);
			/* XXX: debug info below, you can enable this if needed
			print_r($row_chk);
			echo "<br>query: $sql_chk";
			echo "<br><br>";
			*/
			if ($row_chk["counter"] > 0) {
				/* send note about it */
				$note_subject = gettext("calendar conflict");
				$note_body    = gettext("There's a conflict on your PDA.")."\n".gettext("Please correct as soon as possible.")."\n\n";
				$note_body   .= gettext("Calendar item that causes this conflict").":\n";
				$note_body   .= gettext("start").": ".date("d-m-Y H:i", ($data["start"]+$sync["gmt_offset"]))."\n";
				$note_body   .= gettext("end"). ": ".date("d-m-Y H:i", ($data["end"]+$sync["gmt_offset"]))."\n";
				$note_body   .= gettext("subject").": ".addslashes($data["subject"]);
				$note_rcpt    = $sync["covide_user"];
				$note = array(
					"to"      => $note_rcpt,
					"body"    => $note_body,
					"subject" => $note_subject
				);
				$note_data = new Note_data();
				$note_data->store2db($note);
				unset($note, $note_data);
			}
			//insert item
			$q = "insert into calendar (location, timestamp_start, timestamp_end, user_id, subject, description, is_private, sync_guid, sync_hash) values (";
			$q.= sprintf("'%s', ", addslashes($data["location"]));
			$q.= sprintf("%d, %d, %d, ", ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]), $sync["covide_user"]);
			$q.= sprintf("'%s', '%s', %d, %d, '%s')", addslashes($data["subject"]), addslashes($data["body"]), 0, $k, $data["hash"]);
			sql_query($q);

			echo "record inserted (syncserver=>covide) ...\n";

		}
	}

	//detect changed items in syncserver (minus cache timeout)
	$diff = array_diff($items_covide, $items_server);
	foreach ($diff as $k=>$v) {

		if (!file_exists($filesys.$k)) {
			//update covide link
			$q = sprintf("update calendar set sync_guid = 0, sync_hash = '' where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
			sql_query($q);

			echo "record status cleaned (covide) ...\n";

		} else {
			$data = $sync4j_calendar->calendar2covide($filesys.$k);

			//check if record is registered
			$q = sprintf("select is_registered from calendar where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
			#echo $q;
			$resx = sql_query($q);
			if (sql_result($resx,0)==1 && $k>0) {

				$sync4j_convert->insertSyncDb($k, $db_index, "D");
				@unlink($filesys.$k);
				echo "flushed accorded item in syncserver (scheduled)...\n";

				//flush sync status in agenda table
				$q = sprintf("update calendar set sync_guid = 0, sync_hash = '' where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
				sql_query($q);

				//retrieve covide agenda item
				$q = sprintf("select * from calendar where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
				$res = sql_query($q);
				$row = sql_fetch_array($res);

				$subject = stripslashes($row["subject"]);
				$body = stripslashes($row["description"]);
				if ($row["is_registered"]==1) {
					$busystatus = 3;
					$body.= "(geaccordeerd)";
				}
				$start = $row["timestamp_start"]-$sync["gmt_offset"];
				$end = $row["timstamp_end"]-$sync["gmt_offset"];
				$location = $row["location"];

				//create a new ical xml item
				$xml = $sync4j_calendar->calendar2sync($subject, $body, $start, $end, $location, $busystatus);

				//retrieve max luid and principal for this user/source to get the current syncrange
				$q = sprintf("select count(*) from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
				$res2 = pg_query($db_sync, $q);
				if (pg_fetch_result($res2,0)==0)
					die ("please insert at least one appointment in the sync client first");

				$q = sprintf("select max(luid) as luid, max(principal) as principal from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
				$res2 = pg_query($db_sync, $q);
				$luid = pg_fetch_result($res2,0,"luid");
				$luid = substr($luid, 0, strlen($luid)-3).rand(100,999);

				$principal 	= (int)pg_fetch_result($res2,0,"principal");

				$q = "select counter as guid from sync4j_id where idspace = 'guid'";
				$res2 = pg_query($db_sync, $q);
				$guid = (int)pg_fetch_result($res2,0,"guid")+1;

				//fwrite
				$out = fopen($filesys.$guid, "w");
				fwrite($out, $xml);
				fclose($out);

				//insert a new free record into syncmappings
				$q = "insert into sync4j_client_mapping (principal, sync_source, luid, guid) values ($principal, '".$sync["source"]."', '$luid',$guid)";
				pg_query($db_sync,$q);

				//update the covide record
				$hash = md5($xml);

				$q = sprintf("update calendar set sync_hash = '%s', sync_guid = %d where id = %d", $hash, $guid, $row["id"]);
				sql_query($q);

				$db_index = $sync4j_convert->insertSyncDb($guid, $db_index,"N");

				//update db to a higher guid value
				$q = "update sync4j_id set counter = $guid where idspace = 'guid'";
				pg_query($db_sync, $q);

				echo "record inserted (covide=>syncserver) ...\n";

			} else {
				$q = sprintf("update calendar set location = '%s', timestamp_start = %d, timestamp_end = %d, ", addslashes($data["location"]), ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]));
				$q.= sprintf("subject = '%s', description = '%s', sync_hash = '%s' ", addslashes($data["subject"]), addslashes($data["body"]), $data["hash"]);
				$q.= sprintf("where sync_guid = %d and user_id = %d", $k, $sync["covide_user"]);
				sql_query($q);

				echo "record updated (syncserver=>covide) ...\n";
			}
		}
	}

	//retrieve old items in covide and in syncserver for truncate cmd
	$q = sprintf("select sync_guid from calendar where timestamp_start < %d and sync_guid >0 and user_id = %d", $min_date, $sync["covide_user"]);
	$res = sql_query($q);

	while ($row = sql_fetch_array($res)) {

		$guid = $row["sync_guid"];

		$sync4j_convert->insertSyncDb($guid, $db_index, "D");
		@unlink($filesys.$guid);

		echo "deleted old item in syncserver ...\n";
	}

	//update sync status in covide db for old items
	$q = sprintf("update calendar set sync_guid = 0, sync_hash = '' where timestamp_start < %d and sync_guid >0 and user_id = %d", $min_date, $sync["covide_user"]);
	sql_query($q);


	$sync4j_convert->writeSyncDB($db_index, $filesys, $fp_array);
	exit();
?>
