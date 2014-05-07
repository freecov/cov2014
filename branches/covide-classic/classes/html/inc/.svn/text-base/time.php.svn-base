<?php
	session_start();
	$user = $_SESSION["user_id"];
	$sessid = session_id();
	session_write_close();

	/* include paths */
	ini_set('include_path',ini_get('include_path').':./PEAR:');

	/* open database connection and set some defaults. */
	require_once("MDB2.php");
	require_once("../../../conf/offices.php");

	$options = array(
		"persistent"  => TRUE,
		'portability' => MDB2_PORTABILITY_NONE
	);

	$db =& MDB2::connect($dsn, $options);
	if (PEAR::isError($db)) {
			echo ("Warning: no Covide office configured at this address or no valid database specified. ");
			echo ($db->getMessage());
			die();
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	$db->setOption("autofree", 1);

	/* Update the current timestamp */
	$sql = sprintf("UPDATE login_current SET time = %1\$d WHERE user_id = %2\$d and time < %1\$d AND session_id = '%3\$s'", time(), $user, $sessid);
	$db->query($sql);

	/* cleanup login_current */
	$q_cleanup = sprintf("DELETE FROM login_current WHERE time < %d;", time()-600);
	$db->query($q_cleanup);

	/* Call xml_recalls (which will output an info layer) if there's callable recalls for this session's userID */
	$sql = sprintf("SELECT count(*) AS amount FROM campaign_records WHERE user_id = %d AND (call_again < %d AND call_again > 0)", $_SESSION["user_id"], time());
	$resultset = $db->queryRow($sql);
	if ($resultset["amount"]) 
		echo "loadXML('index.php?mod=campaign&action=xml_recalls');";
	
	if ($_REQUEST["t"]) {
		$s = "
			document.getElementById('clock_seconds').innerHTML = '00';
			clock_start = 0;
		";
	} else {
		$s = "";
	}
	echo sprintf("
		document.getElementById('clock_time').innerHTML = '%s'; %s ",
			date("H:i:"), $s);
?>
