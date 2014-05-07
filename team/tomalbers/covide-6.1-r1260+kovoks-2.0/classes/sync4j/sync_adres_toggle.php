<?
	$verbergIfaceHelemaal = true;
	require("../inc_common.php");

	$q = "select count(*) from adres_sync_records where gebruiker_id = $user_id and adres_table = '$table' and adres_id = $id";
	$res = sql_query($q);
	if (sql_result($res,0)==0) {
		$q = "insert into adres_sync_records (gebruiker_id, adres_table, adres_id) values ($user_id, '$table', $id)";
		sql_query($q);
		$stat = 1;
	} else {
		$q = "delete from adres_sync_records where gebruiker_id = $user_id and adres_table = '$table' and adres_id = $id";
		sql_query($q);
		$stat = 0;
	}
	echo $stat."|".$id;
?>