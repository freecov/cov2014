<small>
<?php
require("../inc_db.php");
db_init();

$q = "select * from boekingen where grootboek_id = 66";
$res = sql_query($q);
while ($row = sql_fetch_array($res)){
	$betaald = $row[betaald];
	$credit = $row[credit];

	if ($credit == 0){
		$credit = 1;
	}else{
		$credit = 0;
	}
	if ($betaald == 0){
		$betaald = 1;
	}else{
		$betaald = 0;
	}
	

	$q = "select bedrijfsnaam from adres where debiteur_nr = $row[deb_nr]";
	$resx = sql_query($q);
	$rowx = sql_fetch_array($resx);



	$q = "select * from boekingen where grootboek_id = 66 and factuur = $row[factuur] and betaald = $betaald and credit = $credit";
	$res3 = sql_query($q);

		
	$res2 = sql_query($q);
	$row2 = sql_fetch_array($res2);
	
	
	if(sql_num_rows($res3) == 0){
	echo "deb: $row[deb_nr]/$rowx[bedrijfsnaam] - factuur: $row[factuur] - bedrag: $row[bedrag] / ";
		echo "geen tegenboeking - factuur: $row[factuur]<br>";
	}
	elseif ($row[bedrag] != $row2[bedrag]){
	echo "deb: $row[deb_nr]/$rowx[bedrijfsnaam] - factuur: $row[factuur] - bedrag: $row[bedrag] / ";
		echo "bedrag: $row2[bedrag]- tegenbedrag klopt niet - factuur: $row2[factuur]<br>";
	}
	else
	{
		//echo "$row2[bedrag]($row2[credit])<br>";
	}

}


?>


