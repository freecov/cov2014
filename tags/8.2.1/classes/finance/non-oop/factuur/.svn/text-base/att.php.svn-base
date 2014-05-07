<?php

		$db_host     = "localhost";
		$db_user     = "cvd_assist";
		$db_passwd   = "cvd_assist_pw";
		$db_database = "cvd_assist";
		
		$db = sql_connect ($db_host, $db_user, $db_passwd);
		sql_select_db($db_database, $db);

?>

<?php $resultAtt = sql_query("SELECT * FROM mail_attachments ;");
	//toon de lijst
	while ($rowAtt = sql_fetch_array($resultAtt)){
			$id_1 = $rowAtt["id"];
			$dat_1 = $rowAtt["dat"];
			$q = "INSERT INTO mail_attachments_dat SET dat = \"".AddSlashes($dat_1)."\", att_id = ".$id_1." ;";
				sql_query($q);
				echo("1. ".sql_error()."<br>");
	} ?>		

