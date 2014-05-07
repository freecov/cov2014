<?php
session_cache_limiter('private, must-revalidate');
session_start();

$user_id = $_SESSION["user_id"];
$start = $_REQUEST["start"];
$eind = $_REQUEST["eind"];
$type = $_REQUEST["type"];

if (!$_SESSION["user_id"])
	die("not allowed");

?>

<html>
<head>
<title>Grootboek overzicht</title>
</head>
<body>
<style type="text/css">
	BODY, TD {font-family: monospace; font-size: 12px;}
	BR.page { page-break-after: always }
</style>

<div name="loading" id="loading" style="position:absolute;top:50px;left:50px;" align="center">
	<font size="2"><b>Bezig met inlezen gegevens.... een moment geduld a.u.b....</b></font>
</div>

<div name="main" id="main" style="visibility:hidden">
<!--<div name="main" id="main" style="visibility:visible">-->
<?php
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
		$bedrag = str_replace(".", ",", $bedrag);
		return($bedrag);
	}

require("../inc_db.php");
db_init();
#error_reporting(64);

// Kijk of gebruiker toegang heeft
		$result = sql_query ("SELECT xs_omzetmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_array ($result);
		if ($row["xs_omzetmanage"]!=1) {
			echo "Geen toegang!!!";
			exit();
		}


//aantal records per pagina
$num_page = 48;
$num_page_kol = 55;

if ($type == "diff"){
	?>
	<table>
		<tr>
			<td colspan="15"><span class="dT"><nobr><b><i>Openstaande crediteuren op 31-12-<?php echo $jaar ?></b></i></td>
		</tr>
		<tr>
		<?php
			echo "<td><nobr>nummer</td>";
			echo "<td><nobr>relatie</td>";
			echo "<td><nobr>datum</td>";
			echo "<td><nobr>omschrijving</td>";
			echo "<td><nobr>bedrag te betalen</td>";
			echo "<td><nobr>bedrag betaald</td>";
		?>
		</tr>
		<?php
			$start = mktime(0,0,0,1,1,$jaar);
			$eind  = mktime(0,0,0,1,0,$jaar+1);

			#echo strftime("%a %d %b %Y  %H:%M",$eind);

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
				?>
					<tr>
						<td><span class="d"><?php echo $row[factuur] ?></td>
						<td><span class="d"><nobr><?php echo $row2[bedrijfsnaam] ?></td>
						<td><span class="d"><nobr><?php echo strftime("%d-%m-%Y",$row[datum]); ?></td>
						<?php
							$q = "select descr from inkopen where id = $row[koppel_id]";
							$res2 = sql_query($q);
							$descr = sql_result($res2,0);
						?>
						<td><span class="d"><nobr><?php echo $descr ?></td>
						<td align="right"><span class="d"><nobr><?php echo $row[bedrag] ?></td>
						<td align="right"><span class="d"><nobr><?php echo (float)$bedrag ?></td>
					</tr>
				<?php
				}
			}
		?>
		<tr>
			<td colspan="15" align="right"><span class="dT"><b>totaal: <?php echo $tot ?></b></td>
		</tr>

		<tr>
			<td colspan="15"><span class="dT"><nobr><b><i>Openstaande debiteuren op 31-12-<?php echo $jaar ?></i></b></td>
		</tr>
		<tr>
		<?php
			unset($tot);
			echo "<td><nobr>nummer</td>";
			echo "<td><nobr>relatie</td>";
			echo "<td><nobr>datum</td>";
			echo "<td><nobr>omschrijving</td>";
			echo "<td><nobr>bedrag te betalen</td>";
			echo "<td><nobr>bedrag betaald</td>";
		?>
		</tr>
		<?php
			$start = mktime(0,0,0,1,1,$jaar);
			$eind  = mktime(0,0,0,1,0,$jaar+1);

			$q = "select * from boekingen where datum between $start and $eind and grootboek_id = 66 and betaald=0 order by datum desc";
			$res = sql_query($q) or die($q.sql_error());
			while ($row = sql_fetch_array($res)){

				$q = "select sum(bedrag) from boekingen where datum between $start and $eind and grootboek_id = 66 and betaald=1 and factuur = $row[factuur] and status = $row[status]";
				#echo $q;

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
				?>
					<tr>
						<td><span class="d"><?php echo $row[factuur] ?></td>
						<td><span class="d"><nobr><?php echo $row2[bedrijfsnaam] ?></td>
						<td><span class="d"><nobr><?php echo strftime("%d-%m-%Y",$row[datum]); ?></td>
						<?php
							$q = "select omschrijving from omzet_akties where factuur_nr = $row[factuur]";
							#echo $q;
							$res2 = sql_query($q) or die($q.sql_error());
							$descr = sql_result($res2,0);
						?>
						<td><span class="d"><nobr><?php echo $descr ?></td>
						<td align="right"><span class="d"><nobr><?php echo $row[bedrag] ?></td>
						<td align="right"><span class="d"><nobr><?php echo (float)$bedrag ?></td>
					</tr>

				<?php
				}
			}
		?>
		<tr>
			<td colspan="15"  align="right"><span class="dT"><b>totaal: <?php echo $tot ?></b></td>
		</tr>
	</table>

<?php
}elseif ($type == "kolom"){
		$i = 0;
		$page=1;

		for ($z=1;$z<=2;$z++){

		?>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td><b><big><big>
					<?php
						$q = "select name from license";
						$res = sql_query($q);
						echo sql_result($res,0);
					?>	
					</big></big></b></td>
					<td align="right">datum afdruk:&nbsp;</td>
					<td><?php echo strftime("%d-%m-%Y %H:%M") ?></td>
				</tr>
				<tr>
					<td><b><big>Kolommenbalans</big></b></td>
					<td align="right">overzicht:&nbsp;</td>
						<td><nobr>
							<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>
							(pagina <?php echo $page ?>)
						</td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td colspan="10"><hr width="100%" size="1" color="black"></td>
			</tr>

			<tr>
				<td colspan="2"><b>grootboekrekening</b></td>
				<td colspan="3" align="left">&nbsp;&nbsp;<b>debet</b></td>
				<td colspan="3" align="left">&nbsp;&nbsp;<b>credit</b></td>
				<td colspan="3" align="left">&nbsp;&nbsp;<b>verschil</b></td>
			</tr>
		<?php

			if ($z == 1){
				$zq = "1 and 3999";
			}else{
				$zq = "4000 and 9999";
			}

			$xq = "select * from grootboeknummers where nr between $zq order by nr";
			$xres = sql_query($xq) or die(sql_error());
			while ($xrow = sql_fetch_array($xres)){

				$debet = 0;
				$credit = 0;

				//credit double (positive and negative)
				$q = "select sum(bedrag) as tot from boekingen where bedrag > 0 and grootboek_id = $xrow[id] and credit = 1 and datum >= $start and datum <= $eind ";
				$res = sql_query($q) or die(sql_error());
				$row = sql_fetch_array($res);
				$credit = $row["tot"];

				$q = "select sum(bedrag) as tot from boekingen where bedrag < 0 and grootboek_id = $xrow[id] and credit = 0 and datum >= $start and datum <= $eind ";
				$res = sql_query($q) or die(sql_error());
				$row = sql_fetch_array($res);
				$credit -= $row["tot"];

				//debet double (positive and negative)
				$q = "select sum(bedrag) as tot from boekingen where bedrag > 0 and grootboek_id = $xrow[id] and credit = 0 and datum >= $start and datum <= $eind ";
				$res = sql_query($q) or die(sql_error());
				$row = sql_fetch_array($res);
				$debet = $row["tot"];

				$q = "select sum(bedrag) as tot from boekingen where bedrag < 0 and grootboek_id = $xrow[id] and credit = 1 and datum >= $start and datum <= $eind ";
				$res = sql_query($q) or die(sql_error());
				$row = sql_fetch_array($res);
				$debet -= $row["tot"];


			$tot_deb += $debet+$debet2;
			$tot_cred += $credit;


			if ($i == $num_page_kol){
				$page += 1;

				$perc = round(($page/($page_totaal+1))*100);
				if ($perc > 100){ $perc = 100; }

				$i = 0;
				?>

				</table>
				<br class="page">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td><b><big><big>
							<?php
								$q = "select name from license";
								$res = sql_query($q);
								echo sql_result($res,0);
							?>	
						</big></big></b></td>
						<td align="right">datum afdruk:&nbsp;</td>
						<td><?php echo strftime("%d-%m-%Y %H:%m") ?></td>
					</tr>
					<tr>
						<td><b><big>Kolommenbalans</big></b></td>
						<td align="right">overzicht:&nbsp;</td>
						<td><nobr>
							<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>
							(pagina <?php echo $page ?>)
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td colspan="10"><hr width="100%" size="1" color="black"></td>
				</tr>
				<tr>
					<td colspan="2"><b>grootboekrekening</b></td>
					<td colspan="3" align="left">&nbsp;&nbsp;<b>debet</b></td>
					<td colspan="3" align="left">&nbsp;&nbsp;<b>credit</b></td>
					<td colspan="3" align="left">&nbsp;&nbsp;<b>verschil</b></td>
				</tr>

			<?php
			}
			?>


			<?php if ($debet != 0 || $credit != 0){ ?>
				<?php
					$i++;
				?>
			<tr>
				<td align="right"><?php echo $xrow[nr] ?></td>
				<td>&nbsp;-&nbsp;<?php echo $xrow[titel] ?>&nbsp;</td>
				<td align="left">&nbsp;&nbsp;&euro;&nbsp;</td>

				<td align="right"><?php echo fix($debet) ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="width:1px" bgcolor="black"></td>
				<td align="left">&nbsp;&nbsp;&euro;&nbsp;</td>

				<td align="right"><?php echo fix($credit) ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="width:1px" bgcolor="black"></td>
				<td align="left">&nbsp;&nbsp;&euro;&nbsp;</td>
				<td align="right"><?php echo fix($debet-$credit) ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<?php }

		} //grootboeken

	?>
		<tr>
			<td colspan="10"><hr width="100%" size="1" color="black"></td>
		</tr>
		<tr>
			<td colspan="2"><b>totaal</b></td>
			<td colspan="3" align="left">&nbsp;&nbsp;<b><?php echo fix($tot_deb) ?></b></td>
			<td colspan="3" align="left">&nbsp;&nbsp;<b><?php echo fix($tot_cred) ?></b></td>
			<td colspan="3" align="left">&nbsp;&nbsp;<b><?php echo fix($tot_deb-$tot_cred) ?></b></td>
		</tr>
		</table>
			<br class="page">
		<table cellspacing="0" cellpadding="0" border="0">
	<?php
	$tot_cred = 0;
	$tot_deb = 0;
	$i = 0;
	$page+=1;

	}
	echo "</table>";

}else{

	if ($id == 0){

		$gbmin = $_REQUEST["grootboek_start"];
		$gbmax = $_REQUEST["grootboek_eind"];
		$sq = " WHERE nr >= $gbmin and nr <= $gbmax ";

		$xq = "select id from grootboeknummers $sq order by nr";
		$xres = sql_query($xq) or die(sql_error());

		while ($xrow = sql_fetch_array($xres)){
				$cq = "select count(*) as aantal from boekingen where grootboek_id = $xrow[id] and datum >= $start and datum <= $eind ";
				$cres = sql_query($cq);
				$crow = sql_fetch_array($cres);
				$aantal = $crow[aantal];

				if ($aantal > 0){
					$page_totaal += round($aantal/$num_page);
				}
		}


		$xq = "select * from grootboeknummers $sq order by nr";
		$xres = sql_query($xq) or die(sql_error());
		while ($xrow = sql_fetch_array($xres)){

				$gb_nr = $xrow[nr];
				$gb_naam = $xrow[titel];

				$cq = "select count(*) as aantal from boekingen where grootboek_id = $xrow[id] and datum >= $start and datum <= $eind ";
				$cres = sql_query($cq);
				$crow = sql_fetch_array($cres);
				$aantal = $crow[aantal];
				$tot_deb = 0;
				$tot_cred = 0;

				if ($aantal > 0){

						?>
						<b><big>Grootboekrekening: <?php echo $gb_nr ?> - <?php echo $gb_naam ?></big> (<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>)</b><br>
						<table>
						<tr>
							<td colspan="10"><hr width="100%" size="1" color="black"></td>
						</tr>

						<tr>
							<td><b>datum</b></td>
							<td colspan="2" align="center"><b>debet</b></td>
							<td colspan="2" align="center"><b>credit</b></td>
							<td><b>omschrijving</b></td>
						</tr>
						<?php

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
							}

							$i++;

							$datum = $row[datum];

							?>
						<tr>
							<td valign="top"><nobr><?php echo date("d",$datum) ?>-<?php echo date("m",$datum) ?>-<?php echo date("Y",$datum) ?>&nbsp;&nbsp;</td>
							<?php
							if ($credit == "debet"){
								?>
								<td valign="top"><nobr>&euro;&nbsp;</td>
								<td valign="top" align="right"><nobr><?php echo fix($bedrag) ?>&nbsp;</td>
								<td colspan="2">&nbsp;</td>
								<?php
							}else{
								?>
								<td colspan="2">&nbsp;</td>
								<td valign="top"><nobr>&euro;&nbsp;</td>
								<td valign="top" align="right"><nobr><?php echo fix($bedrag) ?>&nbsp;</td>
								<?php
							}
							?>

							<td valign="top"><nobr><?php echo $omschrijving ?></td>
						</tr>
							<?php

							//page break after x records
							if ($i == $num_page){
								$page += 1;

								$perc = round(($page/($page_totaal+1))*100);
								if ($perc > 100){ $perc = 100; }

								$i = 0;
								?>
								<script language="Javascript">
								function updateLoad(){
									var loading = document.getElementById("loading");
									loading.innerHTML = '<font size="2"><b>Bezig met inlezen gegevens.... een moment geduld a.u.b.... (pagina <?php echo $page ?> - <?php echo $perc ?>%)</b></font>';
								}
								updateLoad();


								</script>

									<tr>
										<td colspan="10"><hr width="100%" size="1" color="black"></td>
									</tr>
									<tr>
										<td><b>subtotaal:</b></td>
										<td>&euro;</td>
										<td><?php echo fix($tot_deb) ?></td>
										<td>&euro;</td>
										<td><?php echo fix($tot_cred) ?></td>
									</tr>
									</table>
									<br class="page">

									<?php
									?>

									<b><big>Grootboekrekening: <?php echo $gb_nr ?> - <?php echo $gb_naam ?></big> (<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>)</b><br>
									<table>
									<tr>
										<td colspan="10"><hr width="100%" size="1" color="black"></td>
									</tr>
						<tr>
							<td><b>datum</b></td>
							<td colspan="2" align="center"><b>debet</b></td>
							<td colspan="2" align="center"><b>credit</b></td>
							<td><b>omschrijving</b></td>
						</tr>

								<?php
							}
						}
						?>
						<tr>
							<td colspan="10"><hr width="100%" size="1" color="black"></td>
						</tr>
						<tr>
							<td><b>totaal:</b></td>
							<td>&euro;</td>
							<td><?php echo fix($tot_deb) ?></td>
							<td>&euro;</td>
							<td><?php echo fix($tot_cred) ?></td>
						</tr>
						</table>
						<br class="page">
				<?php
				}
		}
	}else{

//--------------------------

				$cq = "select count(*) as aantal from boekingen where grootboek_id = $id and datum >= $start and datum <= $eind ";
				$cres = sql_query($cq);
				$crow = sql_fetch_array($cres);
				$aantal = $crow[aantal];

				if ($aantal > 0){
					$page_totaal += round($aantal/$num_page);
				}

				$q = "select * from grootboeknummers where id = $id";
				$res = sql_query($q) or die(sql_error());
				$row = sql_fetch_array($res);

				$gb_nr = $row[nr];
				$gb_naam = $row[titel];

				$tot_deb = 0;
				$tot_cred = 0;




				?>
				<b><big>Grootboekrekening: <?php echo $gb_nr ?> - <?php echo $gb_naam ?></big> (<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>)</b><br>
				<table>
				<tr>
					<td colspan="10"><hr width="100%" size="1" color="black"></td>
				</tr>

						<tr>
							<td><b>datum</b></td>
							<td colspan="2" align="center"><b>debet</b></td>
							<td colspan="2" align="center"><b>credit</b></td>
							<td><b>omschrijving</b></td>
						</tr>
				<?php

				//boekingen
				$q = "select * from boekingen where grootboek_id = $id and datum >= $start and datum <= $eind order by datum";
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
					}else{
						$q = "select * from overige_posten where id = $row[koppel_id]";
						$res2 = sql_query($q);
						$row2 = sql_fetch_array($res2);
						$omschrijving = $row2[omschrijving];
					}

				//echo $q;
					if ($omschrijving == ""){
						$omschrijving = "-";
					}

					$i++;

					$datum = $row[datum];

					?>
						<tr>
							<td valign="top"><nobr><?php echo date("d",$datum) ?>-<?php echo date("m",$datum) ?>-<?php echo date("Y",$datum) ?>&nbsp;&nbsp;</td>
							<?php
							if ($credit == "debet"){
								?>
								<td valign="top"><nobr>&euro;&nbsp;</td>
								<td valign="top" align="right"><nobr><?php echo fix($bedrag) ?>&nbsp;</td>
								<td colspan="2">&nbsp;</td>
								<?php
							}else{
								?>
								<td colspan="2">&nbsp;</td>
								<td valign="top"><nobr>&euro;&nbsp;</td>
								<td valign="top" align="right"><nobr><?php echo fix($bedrag) ?>&nbsp;</td>
								<?php
							}
							?>

							<td valign="top"><nobr><?php echo $omschrijving ?></td>
						</tr>
					<?php

					//page break after x records
					if ($i == $num_page){
								$page += 1;

								$perc = round(($page/($page_totaal+1))*100);
								if ($perc > 100){ $perc = 100; }

								$i = 0;
								?>
								<script language="Javascript">
								function updateLoad(){
									var loading = document.getElementById("loading");
									loading.innerHTML = '<font size="2"><b>Bezig met inlezen gegevens.... een moment geduld a.u.b.... (pagina <?php echo $page ?> - <?php echo $perc ?>%)</b></font>';
								}
								updateLoad();

								</script>

							<tr>
								<td colspan="10"><hr width="100%" size="1" color="black"></td>
							</tr>
							<tr>
								<td><b>subtotaal:</b></td>
								<td>&euro;</td>
								<td><?php echo fix($tot_deb) ?></td>
								<td>&euro;</td>
								<td><?php echo fix($tot_cred) ?></td>
							</tr>
							</table>
							<br class="page">

							<b><big>Grootboekrekening: <?php echo $gb_nr ?> - <?php echo $gb_naam ?></big> (<?php echo strftime("%d-%m-%Y",$start)." - ".strftime("%d-%m-%Y",$eind) ?>)</b><br>
							<table>
							<tr>
								<td colspan="10"><hr width="100%" size="1" color="black"></td>
							</tr>
						<tr>
							<td><b>datum</b></td>
							<td colspan="2" align="center"><b>debet</b></td>
							<td colspan="2" align="center"><b>credit</b></td>
							<td><b>omschrijving</b></td>
						</tr>

						<?php
					}
				}
				?>
				<tr>
					<td colspan="5"><hr width="100%" size="1" color="black"></td>
				</tr>
						<tr>
							<td><b>totaal:</b></td>
							<td>&euro;</td>
							<td><?php echo fix($tot_deb) ?></td>
							<td>&euro;</td>
							<td><?php echo fix($tot_cred) ?></td>
						</tr>
				</table>
	<?php
	}
}
//-------------------------------------------------------------
?>
</<div>

<script>
	function makePrint(){
		var loading = document.getElementById("loading");
		var main = document.getElementById("main");

		loading.style.visibility = 'hidden';
		main.style.visibility = 'visible';

		setTimeout('window.print()', 1000);
		setTimeout('window.close()', 2000);
	}

	makePrint();
</script>
</body>
</html>
<?php
//ob_end_flush();
//ob_end_clean();
?>
