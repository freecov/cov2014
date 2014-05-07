<?
Class Sync4j_reset {
	public function sync_reset() {
		//update php execution time naar 10 min!
		set_time_limit(600);
		require_once("sync_db.php");
		$syncconv = new Sync4j_convert();
		/* ----------------------------------------------------------------------------------- */
		/* some settings   																																		 */
		/* ----------------------------------------------------------------------------------- */
		$user_id = (int)$_REQUEST["user_id"];

		$defpath = $GLOBALS["covide"]->filesyspath;

		$q = sprintf("select id, sync4j_source_adres, sync4j_source, sync4j_source_todo, sync4j_path from users where id = %d", $user_id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_array($res);

			$sync["covide_user"]     = $row["id"];
			$sync["source_todo"]     = $row["sync4j_source_todo"];
			$sync["source_calendar"] = $row["sync4j_source"];
			$sync["source_address"]  = $row["sync4j_source_adres"];

			$sync["path_address"]    = $defpath."/syncml/contacts/".$row["sync4j_path"]."/";
			$sync["path_todo"]       = $defpath."/syncml/todos/".$row["sync4j_path"]."/";
			$sync["path_calendar"]   = $defpath."/syncml/calendar/".$row["sync4j_path"]."/";
		}
		//print_r($sync);

		if (!$sync["covide_user"]) {
			die("error: not all user vars are set or user not found");
		}

		//remove ALL agenda related sync information

		if ($sync["source_calendar"] && file_exists($sync["path_calendar"])) {
			$q = sprintf("update calendar set sync_guid = 0, sync_hash = '' where user_id = %d", $sync["covide_user"]);
			sql_query($q);

			$q = sprintf("delete from agenda_sync where user_id = %d", $sync["covide_user"]);
			sql_query($q);

			$q = sprintf("delete from sync4j_client_mapping where sync_source = '%s'",$sync["source_calendar"]);
			pg_query($db_sync, $q);

			$cmd = "rm -f ".$sync["path_calendar"]."*";
			echo $cmd;
			exec ($cmd, $ret, $retval);

			echo "Calendar Syncstatus truncated...<br>\n";
		}

		//remove all adress syncstatus related items
		if ($sync["source_address"] && file_exists($sync["path_address"])) {
			$q = sprintf("delete from address_sync_guid where user_id = %d", $sync["covide_user"]);
			sql_query($q);

			$q = sprintf("delete from sync4j_client_mapping where sync_source = '%s'", $sync["source_adres"]);
			pg_query($db_sync, $q);

			$cmd = "rm -f ".$sync["path_address"]."*";
			echo $cmd;
			exec ($cmd, $ret, $retval);

			echo "Address Syncstatus truncated...<br>\n";
		}

		//remove all todo syncstatus related items
		if ($sync["source_todo"] && file_exists($sync["path_todo"])) {
			$q = sprintf("delete from todo_sync where user_id = %d", $sync["covide_user"]);
			sql_query($q);

			$q = sprintf("delete from sync4j_client_mapping where sync_source = '%s'", $sync["source_todo"]);
			pg_query($db_sync, $q);

			$cmd = "rm -f ".$sync["path_todo"]."*";
			echo $cmd;
			exec ($cmd, $ret, $retval);

			echo "Todo Syncstatus truncated...<br>\n";
		}

		echo "<br><br>\n";
		echo "Please make also sure the client Sync Device has no items!";

		$filesys = $defpath."/syncml/";

		$fp_array = array();
		$db_index = $syncconv->getSyncDb($filesys, $fp_array); //retrieve sync db index file(s)
	}
}
?>
