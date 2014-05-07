<?
	if (!class_exists("Sync4j")) {
		die("no class definition found");
	}
	//update php execution time naar 10 min!
	set_time_limit(600);

	require_once(self::include_dir."../sync_db.php");
	$sync4j_convert = new Sync4j_convert();
	$sync4j_todo    = new Sync4j_todo();

	/* ----------------------------------------------------------------------------------- */
	/* some settings   																																		 */
	/* ----------------------------------------------------------------------------------- */
	$user_id = (int)$_REQUEST["user_id"];

	$defpath = $GLOBALS["covide"]->filesyspath;

	$q = sprintf("select id, sync4j_source_todo, sync4j_path from users where id = %d", $user_id);
	$res = sql_query($q);
	if (sql_num_rows($res)>0) {
		$row = sql_fetch_array($res);

		$sync["covide_user"] = $row["id"];
		$sync["source"] = $row["sync4j_source_todo"];
		$sync["path"] = $defpath."/syncml/todos/".$row["sync4j_path"]."/";
	}

	print_r($sync);

	if (!$sync["covide_user"] || !$sync["source"] || !$sync["path"]) {
		die("error: not all user vars are set or user not found");
	}

	if (!file_exists($sync["path"])) {
		die("error: filesys path does not exist");
	}


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
	/* Process updated/deleted items in covide=>syncserver                                 */
	/* ----------------------------------------------------------------------------------- */
	$q = sprintf("select * from todo_sync where user_id = %d", $sync["covide_user"]);
	$res = sql_query($q);
	while ($row = sql_fetch_array($res)) {
		$guid    = $row["sync_guid"];
		$user_id = $row["user_id"];

		if ($row["action"]=="U") {
			$q = sprintf("select * from todo where user_id = %d and sync_guid = %d", $user_id, $guid);
			$res2 = sql_query($q);
			$row2 = sql_fetch_array($res2);

			//get all new items from Covide => SyncServer
			$subject = stripslashes(substr($row2["subject"],0,50));
			$body    = stripslashes($row2["body"]);
			$start   = $row2["timestamp"]-$sync["gmt_offset"];
			$end     = $row2["timestamp_end"]-$sync["gmt_offset"];

			//create a new ical xml item
			$xml = $sync4j_todo->todo2sync($subject, $body, $start, $end);
			$hash = md5($xml);

			//fwrite
			$out = fopen($filesys.$guid, "w");
			fwrite($out, $xml);
			fclose($out);

			$q = sprintf("update todo set sync_hash = '%s' where sync_guid = %d and user_id = %d", $hash, $guid, $user_id);
			sql_query($q);

			echo "updated item (covide=>syncserver) ...\n";

			$db_index = $sync4j_convert->insertSyncDb($guid, $db_index, "U");

		} elseif ($row["action"]=="D") {

			//insert delete statement into index
			$db_index = $sync4j_convert->insertSyncDb($guid, $db_index, "D");

			@unlink($filesys.$guid);

			$q = sprintf("update todo set sync_guid = 0, sync_hash = '' where sync_guid = %d and user_id = %d", $guid, $gebruiker);
			sql_query($q);

			echo "deleted item (covide=>syncserver) ...\n";
		}

	}
	$q = sprintf("delete from todo_sync where user_id = %d", $sync["covide_user"]);
	sql_query($q);


	/* ----------------------------------------------------------------------------------- */
	/* get all NEW items in Covide, but not in Syncserver (producte sync_guid + sync_hash) */
	/* ----------------------------------------------------------------------------------- */
	$min_date = mktime(0,0,0,date("m")-$sync["max_age"],date("d"),date("Y"));

	$q = sprintf("select * from todo where user_id = %d and (sync_guid = 0 or sync_guid is null) order by id", $sync["covide_user"]);
	$res = sql_query($q);
 	while ($row = sql_fetch_array($res)) {

		//get all new items from Covide => SyncServer
		$subject = stripslashes(substr($row["subject"],0,50));
		$body    = stripslashes($row["body"]);
		$start   = $row["timestamp"]-$sync["gmt_offset"];
		$end     = $row["timestamp_end"]-$sync["gmt_offset"];

		//create a new ical xml item
		$xml = $sync4j_todo->todo2sync($subject, $body, $start, $end);

		//retrieve max luid and principal for this user/source to get the current syncrange
		$q = sprintf("select count(*) from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
		$res2 = pg_query($db_sync, $q);
		if (pg_fetch_result($res2,0)==0)
			die ("please insert at least one task in the sync client first");

		$q = sprintf("select max(luid) as luid, max(principal) as principal from sync4j_client_mapping where sync_source = '%s'", $sync["source"]);
		$res2 = pg_query($db_sync, $q);
		$luid = pg_fetch_result($res2,0,"luid");
		$luid = substr($luid, 0, strlen($luid)-3).rand(100,999);

		$principal = (int)pg_fetch_result($res2,0,"principal");

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

		$q = sprintf("update todo set sync_hash = '%s', sync_guid = %d where id = %d", $hash, $guid, $row["id"]);
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
	$q = sprintf("select * from todo where user_id = %d and sync_guid > 0", $sync["covide_user"]);
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
			$q = sprintf("delete from todo where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
			sql_query($q);
			echo "record deleted (covide) ...\n";
		}
	}
	//search for new items in sync=>covide
	foreach ($items_server as $k=>$v) {
		if (!array_key_exists($k,$items_covide)) {
			$data = $sync4j_todo->todo2covide($filesys.$k, $sync["date_offset"]);

			//insert item
			$q = "insert into todo (subject, timestamp, timestamp_end, user_id, body, sync_guid, sync_hash) values (";
			$q.= sprintf("'%s', ", substr(addslashes($data["subject"]),0,250));
			$q.= sprintf("%d, %d, %d, ", ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]), $sync["covide_user"]);
			$q.= sprintf("'%s', %d, '%s')", addslashes($data["body"]), $k, $data["hash"]);
			sql_query($q);

			echo "record inserted (syncserver=>covide) ...\n";

		}
	}

	//detect changed items in syncserver (minus cache timeout)
	$diff = array_diff($items_covide, $items_server);
	foreach ($diff as $k=>$v) {

		if (!file_exists($filesys.$k)) {
			//update covide link
			$q = sprintf("update todo set sync_guid = 0, sync_hash = '' where user_id = %d and sync_guid = %d", $sync["covide_user"], $k);
			pg_query($q);

			echo "record status cleaned (covide) ...\n";
		} else {
			$data = $sync4j_todo->todo2covide($filesys.$k);

			$q = sprintf("update todo set subject = '%s', timestamp = %d, timestamp_end = %d, ", substr(addslashes($data["subject"]),0,250), ($data["start"]+$sync["gmt_offset"]), ($data["end"]+$sync["gmt_offset"]));
			$q.= sprintf("body = '%s', sync_hash = '%s' ", addslashes($data["body"]), $data["hash"]);
			$q.= sprintf("where sync_guid = %d and user_id = %d", $k, $sync["covide_user"]);
			sql_query($q);

			echo "record updated (syncserver=>covide) ...\n";
		}
	}

	$sync4j_convert->writeSyncDB($db_index, $filesys, $fp_array);
	exit();
?>
