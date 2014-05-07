<?php
	if (!class_exists("Sync4j")) {
		die("no class defenition found");
	};
	//update php execution time naar 10 min!
	set_time_limit(600);
	$sync4j_convert = new Sync4j_convert();
	$sync4j_address = new Sync4j_address();

	require_once(self::include_dir."../sync_db.php");

	/* ----------------------------------------------------------------------------------- */
	/* some settings   																																		 */
	/* ----------------------------------------------------------------------------------- */
	$user_id = (int)$_REQUEST["user_id"];

	$defpath = $GLOBALS["covide"]->filesyspath;

	$q = sprintf("select id, sync4j_source_adres, sync4j_path from users where id = %d", $user_id);
	$res = sql_query($q);
	if (sql_num_rows($res)>0) {
		$row = sql_fetch_assoc($res);

		$sync["covide_user"] = $row["id"];
		$sync["source"]      = $row["sync4j_source_adres"];
		$sync["path"]        = $defpath."/syncml/contacts/".$row["sync4j_path"]."/";
	}

	if (!$sync["covide_user"] || !$sync["source"] || !$sync["path"]) {
		die("error: not all user vars are set or user not found");
	}

	if (!file_exists($sync["path"])) {
		die("error: filesys path does not exist");
	}
	$filesys = $sync["path"];

	$fp_array = array();
	$db_index = $sync4j_convert->getSyncDb($filesys, $fp_array); //retrieve sync db index file(s)

	//build syncml list
	$_sync = array();
	$q = sprintf("select * from address_sync_records where user_id = %d", $sync["covide_user"]);
	$sres = sql_query($q);
	while ($srow = sql_fetch_assoc($sres)) {
		$_sync[$srow["address_table"]][$srow["address_id"]]=1;
	}
	print_r($_sync);
	/* ----------------------------------------------------------------------------------- */
	/* get all adresses from the global store with the personal global identifier (guid)	 */
	/* ----------------------------------------------------------------------------------- */
	$q = "select address.*, sync.sync_guid from address_sync address ";
	$q.= " left join address_sync_guid sync on address.id = sync.sync_id ";
	$q.= sprintf(" where (address.is_private = 0) or (address.is_private = %d)", $sync["covide_user"]);
	$res = sql_query_direct($q);
	while ($row = sql_fetch_array($res)) {
		if (!$row["sync_guid"]) {
			$table = $row["address_table"];
			$id    = $row["address_id"];

			if ($_sync[$row["address_table"]][$row["address_id"]]==1) {
				//insert new record into syncsource

				//get all new items from Covide => SyncServer
				$data = $sync4j_address->getAdresData($table, $id);

				//create a new vcard xml item
				$xml = $sync4j_address->adress2sync($data);

				//retrieve max luid and principal for this user/source to get the current syncrange
				$q = "select count(*) from sync4j_client_mapping where sync_source = '".$sync["source"]."'";
				$res2 = pg_query($db_sync, $q);
				if (pg_fetch_result($res2,0)==0)
					die ("please insert at least one adress in the sync client first.<br>\nthis record will be replaced with te covide adress book.");

				$q = "select max(luid) as luid, max(principal) as principal from sync4j_client_mapping where sync_source = '".$sync["source"]."'";
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

				//insert the covide record
				$hash = md5($xml);

				$q = "insert into address_sync_guid (sync_id, user_id, sync_guid) values (";
				$q.= $row["id"].",".$sync["covide_user"].",".$guid.")";
				sql_query($q);

				$db_index = $sync4j_convert->insertSyncDb($guid, $db_index,"N");

				//update db to a higher guid value
				$q = "update sync4j_id set counter = $guid where idspace = 'guid'";
				pg_query($db_sync, $q);

				echo "record prepared (covide)...";
			}
		} else {
			if ($_sync[$row["address_table"]][$row["address_id"]]==0) {
				$guid = $row["sync_guid"];
				$k = $guid;
				$sync4j_convert->insertSyncDb($guid, $db_index, "D");
				@unlink($filesys.$k);
				echo "deleted item (syncserver)...\n";
			}
		}
	}

	/* ----------------------------------------------------------------------------------- */
	/* get all items in Covide and the SyncServer with sync hash and guid									 */
	/* ----------------------------------------------------------------------------------- */
	$items_covide = array();
	$items_server = array();

	//query all items from mapping table in the sync server
	$cmd = "md5sum ".$filesys."*";
	exec ($cmd, $ret, $retval);

	//syntax: [guid] = hash
	foreach ($ret as $line) {
		$line = preg_replace("/ {1,}/s"," ",$line);
		$v = explode(" ",$line);
		if (is_numeric(basename($v[1])) && !preg_match("/\.db$/si",basename($v[1])))
			$items_server[basename($v[1])]=$v[0];
	}
	//get all covide items with [guid]=hash
	$q = "select address.sync_hash, sync.sync_guid from address_sync address ";
	$q.= " inner join address_sync_guid sync on address.id = sync.sync_id ";
	$q.= sprintf(" where (address.is_private = 0) or (address.is_private = %d)", $sync["covide_user"]);
	#echo $q;
	$res = sql_query_direct($q);
	while ($row = sql_fetch_array($res)) {
		$items_covide[$row["sync_guid"]] = $row["sync_hash"];
	}
	$diff = array_diff($items_server, $items_covide);
	foreach ($diff as $k=>$v) {
		if (array_key_exists($k, $items_covide)) {
			//update record from covide=>syncserver
			//get all new items from Covide => SyncServer
			$q = "select address.* from address_sync address, address_sync_guid sync where sync.sync_id = address.id and ";
			$q.= sprintf("sync.sync_guid = %d and sync.user_id = %d", $k, $sync["covide_user"]);
			$res = sql_query($q);
			$row = sql_fetch_array($res);

			$data = $sync4j_address->getAdresData($row["address_table"], $row["address_id"]);

			//create a new vcard xml item
			$xml = $sync4j_address->adress2sync($data);
			$hash = md5($xml);

			//fwrite
			$out = fopen($filesys.$k, "w");
			fwrite($out, $xml);
			fclose($out);

			//update the adress hash
			$q = sprintf("update address_sync set sync_hash = '%s' where id = %d", $hash, $row["id"]);
			sql_query($q);
			echo "record updated (covide=>syncserver)...<br>";
		}
	}

	//detect items not in Covide, but in syncserver
	foreach ($items_server as $k=>$v) {
		if (!array_key_exists($k, $items_covide)) {

			$sync4j_convert->insertSyncDb($guid, $db_index, "D");
			@unlink($filesys.$k);
			echo "deleted item (syncserver)...";
		}
	}

	foreach ($items_covide as $k=>$v) {
		if (!array_key_exists($k, $items_server)) {

			$q = "select address.* from address_sync address, address_sync_guid sync where sync.sync_id = address.id and ";
			$q.= sprintf("sync.sync_guid = %d and sync.user_id = %d", $k, $sync["covide_user"]);
			$res = sql_query($q);
			$row = sql_fetch_array($res);

			$id    = $row["address_id"];
			$table = $row["address_table"];

			//delete the old record
			$q = sprintf("delete from address_sync_guid where sync_guid = %d and user_id = %d", $k, $sync["covide_user"]);
			sql_query($q);

			if ($_sync[$row["address_table"]][$row["address_id"]]==1) {
				//insert new record into syncsource

				//get all new items from Covide => SyncServer
				$data = $sync4j_address->getAdresData($table, $id);

				//create a new vcard xml item
				$xml = $sync4j_address->adress2sync($data);

				//retrieve max luid and principal for this user/source to get the current syncrange
				$q = "select count(*) from sync4j_client_mapping where sync_source = '".$sync["source"]."'";
				$res2 = pg_query($db_sync, $q);
				if (pg_fetch_result($res2,0)==0)
					die ("please insert at least one adress in the sync client first.<br>\nthis record will be replaced with te covide adress book.");

				$q = "select max(luid) as luid, max(principal) as principal from sync4j_client_mapping where sync_source = '".$sync["source"]."'";
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

				//insert the covide record
				$hash = md5($xml);

				$q = "insert into address_sync_guid (sync_id, user_id, sync_guid) values (";
				$q.= $row["id"].",".$sync["covide_user"].",".$guid.")";
				sql_query($q);

				$db_index = $sync4j_convert->insertSyncDb($guid, $db_index,"N");

				//update db to a higher guid value
				$q = "update sync4j_id set counter = $guid where idspace = 'guid'";
				pg_query($db_sync, $q);

				echo "record inserted (covide=>syncserver) ...\n";


			}

		}
	}
	$sync4j_convert->writeSyncDB($db_index, $filesys, $fp_array);
	exit();
?>
