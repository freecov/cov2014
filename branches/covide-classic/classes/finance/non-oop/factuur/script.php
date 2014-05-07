<small>
<?php
require("../inc_db.php");
db_init();


$q = "select * from offertes where datum_2 between 20020101 and 20021231 and definitief_2 = 1";
$res = sql_query($q) or die(sql_error());
while ($row = sql_fetch_array($res)){
	
	$bedrag = 0;
	$q = "select * from producten_in_offertes where link_id = $row[producten_id_2]";
	$res2 = sql_query($q);
	while ($row2 = sql_fetch_array($res2)){
		$bedrag += round(($row2[prijs]*$row2[aantal]),2);
	}

	$q = "select * from omzet_akties where factuur_nr = $row[factuur_nr_2]";
	$res2 = sql_query($q);
	$row2 = sql_fetch_array($res2);

	if (round($row2[rekeningflow_ex],2) != round($bedrag*($row[prec_betaald_2]/100),2)){
		echo "factuur: $row2[factuur_nr], deb: $row2[debiteur_nr] - $row2[rekeningflow_ex] / $bedrag <br>";
	}

}

?>
</small>
