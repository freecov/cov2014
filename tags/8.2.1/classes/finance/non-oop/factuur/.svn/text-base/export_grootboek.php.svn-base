<?php 	
session_cache_limiter('private, must-revalidate');
session_start();

function callback($buffer){
	return ($buffer);
}

ob_start('ob_gzhandler(\'callback\')');

function fix($bedrag){
	$bedrag = round($bedrag,2);

	//helemaal geen punt in het getal
	if(!strstr($bedrag,".")){
		$bedrag.=".00";
	}
	//wel een punt, maar maar 1 getal achter de punt
	if(!!strstr($bedrag,".")){
		if( (strlen($bedrag)-strpos($bedrag,".")) == 2){
			$bedrag.="0";
		}
	}
	//punt -> komma
	//$bedrag = str_replace(".", ",", $bedrag);
	return($bedrag);
}	


require("../inc_db.php");
db_init();



// Kijk of gebruiker toegang heeft
$result = sql_query ("SELECT xs_omzetmanage FROM gebruikers WHERE id=$user_id");
$row = sql_fetch_array ($result);
if ($row["xs_omzetmanage"]!=1) {
	echo "Geen toegang!!!";
	exit();
}


header('Content-Transfer-Encoding: binary');
header('Content-Type: text/plain');

if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
	header('Content-Disposition: filename="grootboek.txt"'); //msie 5.5 header bug
}else{
	header('Content-Disposition: attachment; filename="grootboek.txt"');
}

if ($type == "diff"){

	$line = "Openstaande crediteuren op 31-12-$jaar\n\n";
	$line .= "\"nummer\",\"relatie\",\"datum\",\"omschrijving\",\"bedrag te betalen\",\"bedrag betaald\"\n";

	$start = mktime(0,0,0,1,1,$jaar);
	$eind  = mktime(0,0,0,1,0,$jaar+1);
						
	$q = "select * from boekingen where datum between $start and $eind and grootboek_id = 89 and betaald=0 order by datum desc";
	$res = sql_query($q) or die($q.sql_error());
	while ($row = sql_fetch_array($res)){

		$q = "select sum(bedrag) from boekingen where datum between $start and $eind and grootboek_id = 89 and betaald=1 and koppel_id = $row[koppel_id] and status = $row[status]";
									
		$res2 = sql_query($q) or die($q.sql_error());
		$bedrag = sql_result($res2,0);

		if ((float)$bedrag!=(float)$row[bedrag]){
										
		$tot+=((float)$row[bedrag]-(float)$bedrag);

		if ($row['deb_nr']==0){
			$row2[bedrijfsnaam] = "geen relatie";
		}else{
			$q = "select * from adres where id = ".$row['deb_nr'];
			$res2 = sql_query($q) or die($q.sql_error());
			$row2 = sql_fetch_array($res2);
		}
		$line.= "\"$row[factuur]\",\"$row2[bedrijfsnaam]\",\"".strftime("%d-%m-%Y",$row[datum])."\",";
		
		$q = "select descr from inkopen where id = $row[koppel_id]";
		$res2 = sql_query($q);
		$descr = sql_result($res2,0);
		
		$line.= "\"$descr\",\"$row[bedrag]\",\"".(float)$bedrag."\"\n";
		}
	}
	$line.= "\"totaal:\",\"$tot\"\n \n \n";

	/**************************/

	$line .= "Openstaande debiteuren op 31-12-$jaar\n\n";
	$line .= "\"nummer\",\"relatie\",\"datum\",\"omschrijving\",\"bedrag te betalen\",\"bedrag betaald\"\n";

	$start = mktime(0,0,0,1,1,$jaar);
	$eind  = mktime(0,0,0,1,0,$jaar+1);
						
	$q = "select * from boekingen where datum between $start and $eind and grootboek_id = 66 and betaald=0 order by datum desc";
	$res = sql_query($q) or die($q.sql_error());
	while ($row = sql_fetch_array($res)){

		$q = "select sum(bedrag) from boekingen where datum between $start and $eind and grootboek_id = 66 and betaald=1 and factuur = $row[factuur] and status = $row[status]";
									
		$res2 = sql_query($q) or die($q.sql_error());
		$bedrag = sql_result($res2,0);

		if ((float)$bedrag!=(float)$row[bedrag]){
										
		$tot+=((float)$row[bedrag]-(float)$bedrag);

		if ($row['deb_nr']==0){
			$row2[bedrijfsnaam] = "geen relatie";
		}else{
			$q = "select * from adres where debiteur_nr = ".$row['deb_nr'];
			$res2 = sql_query($q) or die($q.sql_error());
			$row2 = sql_fetch_array($res2);
		}
		$line.= "\"$row[factuur]\",\"$row2[bedrijfsnaam]\",\"".strftime("%d-%m-%Y",$row[datum])."\",";

		$q = "select omschrijving from omzet_akties where factuur_nr = $row[factuur]";
		#echo $q;
		$res2 = sql_query($q) or die($q.sql_error());
		$descr = sql_result($res2,0);

		
		$line.= "\"$descr\",\"$row[bedrag]\",\"".(float)$bedrag."\"\n";
		}
	}
	$line.= "\"totaal:\",\"$tot\"\n\n";


}elseif ($type == "kolom"){
	$line = "\"grootboeknummer\",\"debet\",\"credit\",\"verschil\"\n";

	$xq = "select * from grootboeknummers order by nr";
	$xres = sql_query($xq) or die(sql_error());
	while ($xrow = sql_fetch_array($xres)){

		$debet = 0;
		$credit = 0;


		$q = "select sum(bedrag) as tot from boekingen where grootboek_id = $xrow[id] and credit = 1 and datum >= $start and datum <= $eind ";
		$res = sql_query($q) or die(sql_error());
		$row = sql_fetch_array($res);
		$credit = (double) $row[tot];

		$q = "select sum(bedrag) as tot from boekingen where grootboek_id = $xrow[id] and credit = 0 and datum >= $start and datum <= $eind ";
		$res = sql_query($q) or die(sql_error());
		$row = sql_fetch_array($res);
		$debet = (double) $row[tot];

		if ($debet!=0 || $credit!=0){
			$line.= "\"".$xrow[nr]." - ".$xrow[titel]."\",\"".fix($debet)."\",\"".fix($credit)."\",\"".fix($debet-$credit)."\"\n";
		}
	}

}else{

	if ($id == 0){

		$line = "\"grootboeknummer\",\"datum\",\"debet\",\"credit\",\"omschrijving\"\n";

		$xq = "select * from grootboeknummers order by nr";
		$xres = sql_query($xq) or die(sql_error());
		while ($xrow = sql_fetch_array($xres)){

			$gb_nr = $xrow[nr];
			$gb_naam = $xrow[titel];

			//boekingen
			$q = "select * from boekingen where grootboek_id = $xrow[id] and datum >= $start and datum <= $eind order by datum";
			//$q = "select * from boekingen where grootboek_id = $id order by datum";
			$res = sql_query($q) or die(sql_error());

			while ($row = sql_fetch_array($res)){
				
				$bedrag = $row[bedrag];
				$credit = $row[credit];
				$status = $row[status];

				if ($status != 5){
					$nummer = $row[factuur];
				}else{
					$nummer = "-";
				}

				if ($bedrag < 0){
					if ($credit == 0){
						$credit = 1;
					}else{
						$credit = 0;
					}
					$bedrag = 0-$bedrag;
				}


				if ($credit == 0){
					$credit = "debet";
					$tot_deb += $bedrag;
				}else{
					$credit = "credit";
					$tot_cred += $bedrag;
				}

				$omschrijving = "-";
				if ($status == 2 || $status == 3){
					if ($row[product] > 0){
						$q = "select * from producten where id = $row[product]";
						$res2 = sql_query($q);
						$row2 = sql_fetch_array($res2);
						$omschrijving = $row2[titel];
					}else{									
						$q = "select * from omzet_akties where factuur_nr = $row[factuur]";
						$res2 = sql_query($q);
						$row2 = sql_fetch_array($res2);
						$omschrijving = $row2[omschrijving];
					}
				}elseif ($status == 4){
					$q = "select * from inkopen where id = $row[koppel_id]";
					$res2 = sql_query($q);
					$row2 = sql_fetch_array($res2);
					$omschrijving = $row2[descr];
				}elseif ($status == 5){
					$q = "select * from overige_posten where id = $row[koppel_id]";
					$res2 = sql_query($q);
					$row2 = sql_fetch_array($res2);
					$omschrijving = $row2[omschrijving];
				}
				
				//echo $q;
				if ($omschrijving == ""){
					$omschrijving = "-";
				}else{
					$omschrijving = preg_replace("'\n|\r|,'si"," ",$omschrijving);
				}

				$i++;

				$datum = $row[datum];

				$line.= "\"".$gb_nr." - ".$gb_naam."\",\"".date("d",$datum)."-".date("m",$datum)."-".date("Y",$datum)."\",\"";

				if ($credit == "debet"){
					$line.=$bedrag."\",\"\",\"".$omschrijving."\"\n";
				}else{
					$line.="\",\"".$bedrag."\",\"".$omschrijving."\"\n";
				}
			}
		}
	}else{

	//--------------------------

		$q = "select * from boekingen where grootboek_id = $id and datum >= $start and datum <= $eind order by datum";
		//$q = "select * from boekingen where grootboek_id = $id order by datum";
		$res = sql_query($q) or die(sql_error());


		$line = "\"grootboeknummer\",\"datum\",\"debet\",\"credit\",\"omschrijving\"\n";
		
		while ($row = sql_fetch_array($res)){
			
			$bedrag = $row[bedrag];
			$credit = $row[credit];
			$status = $row[status];

			if ($status != 5){
				$nummer = $row[factuur];
			}else{
				$nummer = "-";
			}

			if ($bedrag < 0){
				if ($credit == 0){
					$credit = 1;
				}else{
					$credit = 0;
				}
				$bedrag = 0-$bedrag;
			}


			if ($credit == 0){
				$credit = "debet";
			}else{
				$credit = "credit";
			}

			$omschrijving = "-";
			if ($status == 2 || $status == 3){
				if ($row[product] > 0){
					$q = "select * from producten where id = $row[product]";
					$res2 = sql_query($q);
					$row2 = sql_fetch_array($res2);
					$omschrijving = $row2[titel];
				}else{									
					$q = "select * from omzet_akties where factuur_nr = $row[factuur]";
					$res2 = sql_query($q);
					$row2 = sql_fetch_array($res2);
					$omschrijving = $row2[omschrijving];
				}
			}elseif ($status == 4){
				$q = "select * from inkopen where id = $row[koppel_id]";
				$res2 = sql_query($q);
				$row2 = sql_fetch_array($res2);
				$omschrijving = $row2[descr];
			}elseif ($status == 5){
				$q = "select * from overige_posten where id = $row[koppel_id]";
				$res2 = sql_query($q);
				$row2 = sql_fetch_array($res2);
				$omschrijving = $row2[omschrijving];
			}
			
			//echo $q;
			if ($omschrijving == ""){
				$omschrijving = "-";
			}else{
				$omschrijving = preg_replace("'\n|\r|,'si"," ",$omschrijving);
			}

			$i++;

			$datum = $row[datum];

			$line.= "\"".$gb_nr." - ".$gb_naam."\",\"".date("d",$datum)."-".date("m",$datum)."-".date("Y",$datum)."\",\"";

			if ($credit == "debet"){
				$line.=$bedrag."\",\"\",\"".$omschrijving."\"\n";
			}else{
				$line.="\",\"".$bedrag."\",\"".$omschrijving."\"\n";
			}
		}
	}
}
//-------------------------------------------------------------


echo($line);
//ob_end_flush();
//ob_end_clean();
exit();
?>
