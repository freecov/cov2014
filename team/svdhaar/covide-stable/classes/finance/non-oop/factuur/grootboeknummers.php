<?php require('emoe.inc.php'); ?>
<?php html_header(); ?>
<?php pageNav(); ?>

<?php
// Kijk of gebruiker toegang heeft
		$result = sql_query ("SELECT xs_omzetmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_array ($result);
		if ($row["xs_omzetmanage"]!=1) {
			echo "Geen toegang!!!";
			exit();
		}


	switch ($pagina){
		case ""						: grootboekOverzicht(); 	break;
		case "nieuw"			: grootboekInvoeren(); 		break;
		case "bewerk"			: grootboekInvoeren(); 		break;
		case "nieuw_db"		: grootboekInvoerenDb(); 	break;
		case "bewerk_db"	: grootboekInvoerenDb(); 	break;
		case "verwijder"	: grootboekVerwijder(); 	break;
		case "transact"		: boekingenOverzicht(); 	break;
		case "zoeken"			: boekingenOverzicht(); 	break;
		case "zoekengb"		: grootboekOverzicht(); 	break;

		case "datumwijzig"	: datumwijzig(); 						break;
		case "datumopslaan"	: datumopslaan(); 					break;
		case "print"				: print_boekingen();				break;
		case "print_popup"	: print_boekingen();				break;

		case "standen"			: standen_overzicht();			break;
		case "wijzig_stand"	: wijzig_stand();						break;
		case "opslaan_stand": opslaan_stand();					break;
		case "verwijder_stand" : verwijder_stand();			break;
		case "invoer_stand"		 : wijzig_stand();				break;

		case "afsluiten":		afsluiten();	break;
		case "opslaan_afsluiten":	opslaan_afsluiten(); break;
		case "afgesloten_overzicht": afgesloten_overzicht(); break;
	}



	function afgesloten_overzicht(){
			global $pagina,$jaar;

				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php?pagina=afsluiten'";
				$menu[count($menu)] = "export";
				$menu[count($menu)] = "javascript:document.location.href='./export_grootboek.php?type=diff&jaar=$jaar'";
				$menu[count($menu)] = "print";
				$menu[count($menu)] = "javascript:popprint();";
			?>
			<?php venster_header("grootboeknummers", "afgesloten overzicht", $menu, 0, -1); ?>

						<script>
						function popprint(){
							window.open('<?php echo $str_inc ?>print_grootboek.php?type=diff&jaar=<?php echo $jaar ?>','print','location=no, menubar=no, width=900, height=500, top=20, left=20, status=no ');
						}
						</script>

					<form name="standform" action="./grootboeknummers.php" method="post">
							<tr>
								<td colspan="15" <?php echo td(0) ?>><span class="dT"><nobr>Openstaande crediteuren op 31-12-<?php echo $jaar ?></td>
							</tr>
							<tr>
							<?php
								echo "<td ".td(0)."><nobr>nummer</td>";
								echo "<td ".td(0)."><nobr>relatie</td>";
								echo "<td ".td(0)."><nobr>datum</td>";
								echo "<td ".td(0)."><nobr>omschrijving</td>";
								echo "<td ".td(0)."><nobr>bedrag te betalen</td>";
								echo "<td ".td(0)."><nobr>bedrag betaald</td>";
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
											<td <?php echo td(1) ?>><span class="d"><?php echo $row[factuur] ?></td>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo $row2[bedrijfsnaam] ?></td>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo strftime("%d-%m-%Y",$row[datum]); ?></td>
											<?php
												$q = "select descr from inkopen where id = $row[koppel_id]";
												$res2 = sql_query($q);
												$descr = sql_result($res2,0);
											?>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo $descr ?></td>
											<td <?php echo td(1) ?> align="right"><span class="d"><nobr><?php echo $row[bedrag] ?></td>
											<td <?php echo td(1) ?> align="right"><span class="d"><nobr><?php echo (float)$bedrag ?></td>
										</tr>
									<?php
									}
								}
							?>
							<tr>
								<td colspan="15" <?php echo td(1) ?> align="right"><span class="dT"><b>totaal: <?php echo $tot ?></b></td>
							</tr>

							<tr>
								<td colspan="15" <?php echo td(0) ?>><span class="dT"><nobr>Openstaande debiteuren op 31-12-<?php echo $jaar ?></td>
							</tr>
							<tr>
							<?php
								unset($tot);
								echo "<td ".td(0)."><nobr>nummer</td>";
								echo "<td ".td(0)."><nobr>relatie</td>";
								echo "<td ".td(0)."><nobr>datum</td>";
								echo "<td ".td(0)."><nobr>omschrijving</td>";
								echo "<td ".td(0)."><nobr>bedrag te betalen</td>";
								echo "<td ".td(0)."><nobr>bedrag betaald</td>";
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
											<td <?php echo td(1) ?>><span class="d"><?php echo $row[factuur] ?></td>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo $row2[bedrijfsnaam] ?></td>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo strftime("%d-%m-%Y",$row[datum]); ?></td>
											<?php
												$q = "select omschrijving from omzet_akties where factuur_nr = $row[factuur]";
												#echo $q;
												$res2 = sql_query($q) or die($q.sql_error());
												$descr = sql_result($res2,0);
											?>
											<td <?php echo td(1) ?>><span class="d"><nobr><?php echo $descr ?></td>
											<td <?php echo td(1) ?> align="right"><span class="d"><nobr><?php echo $row[bedrag] ?></td>
											<td <?php echo td(1) ?> align="right"><span class="d"><nobr><?php echo (float)$bedrag ?></td>
										</tr>

									<?php
									}
								}
							?>
							<tr>
								<td colspan="15" <?php echo td(1) ?> align="right"><span class="dT"><b>totaal: <?php echo $tot ?></b></td>
							</tr>


			</form>
			<?php venster_footer(); ?>
			<?php

	}


	function opslaan_afsluiten(){
		global $jaar;

			$start = mktime(0,0,0,1,1,$jaar);
			$eind = mktime(0,0,0,1,1,$jaar+1);

			$q = "update boekingen set locked = 1 where datum between $start and $eind and locked = 0";
			sql_query($q) or die($q.sql_error());

			$now = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
			$q = "insert into jaar_afsluitingen (jaar, datum_afgesloten) values ($jaar, $now); ";
			sql_query($q) or die($q.sql_error());
			?>

			<?php venster_header("grootboeknummers", "jaar afgesloten", $menu, 0, -1); ?>
					<tr>
						<td <?php echo td(1) ?>><nobr>
							Het jaar <?php echo $jaar ?> is nu definitief afgesloten.<br><br>
							<a href="./grootboeknummers.php">terug naar grootboek</a>
						</td>
					</tr>
			<?php venster_footer(); ?>
	<?php

}

	function afsluiten(){
			global $id, $pagina;

				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php'";
			?>
			<?php venster_header("grootboeknummers", "jaar afsluiten", $menu, 0, -1); ?>
					<form name="standform" action="./grootboeknummers.php" method="post">
					<input type="hidden" name="pagina" value="opslaan_afsluiten">
					<input type="hidden" name="id" value="<?php echo $id ?>">
					<input type="hidden" name="type" value="<?php echo $pagina ?>">
							<tr>
								<td <?php echo td(0) ?>><span class="dT"><nobr>Jaar</td>
								<td <?php echo td(0) ?>><span class="dT"><nobr>Datum afsluiting</td>
							</tr>
					<?php
						$q = "select * from jaar_afsluitingen order by jaar";
						$res = sql_query($q);
						while ($row = sql_fetch_array($res)){

							$start = mktime(0,0,0,1,1,$row[jaar]);
							$eind = mktime(0,0,0,1,1,$row[jaar]+1);

							$q = "select count(*) as aantal from boekingen where datum between $start and $eind";
							$res2 = sql_query($q) or die(sql_error());
							$row2 = sql_fetch_array($res2);

							if ($row2[aantal]>0){
							?>
							<tr>
								<td <?php echo td(1) ?>><nobr>
									<a href="grootboeknummers.php?pagina=afgesloten_overzicht&jaar=<?php echo $row[jaar] ?>">
									<img src="../img/knop_rechts.gif" border="0">
									<?php echo $row[jaar] ?></a></td>
								<td <?php echo td(1) ?>><nobr><?php echo strftime("%a %d %b %Y - %H:%M",$row[datum_afgesloten]); ?>
								</td>
							</tr>
							<?php
							}

						}
					?>
					<tr>
						<td <?php echo td(1) ?> colspan=2><hr noshade size=1></td>
					</tr>
					<tr>
						<td <?php echo td(1) ?>><nobr>datum van vandaag:&nbsp;</td>
						<td <?php echo td(1) ?>><nobr>
							<?php echo strftime("%a %d %b %Y"); ?>
						</td>
					</tr>
					<tr>
						<td <?php echo td(1) ?>><nobr>boekhouding afsluiten van jaar:&nbsp;</td>
						<td <?php echo td(1) ?>>
							<select name="jaar" class="inputselect">
								<option value="">--</option>
								<?php
									#oudste record
									#$q = "select min(datum) as datum from boekingen where locked = 0";
									#$res = sql_query($q) or die(sql_error());
									#$row = sql_fetch_array($res);

									#$min = date("Y",$row['datum']);
									$min = "1999";


									$arr[]=0;
									#array van reeds afgesloten jaren opbouwen
									$q = "select * from jaar_afsluitingen";
									$res = sql_query($q) or die(sql_error());
									while ($row = sql_fetch_array($res)){
										$arr[]= $row[jaar];
									}

								?>
								<?php for($i=$min;$i<=date("Y");$i++){ ?>
									<?php if (!in_array($i,$arr)){ ?>
										<option value="<?php echo $i ?>"><?php echo $i ?></option>
									<?php } ?>
								<?php } ?>
								</select>
						</td>
					</tr>
						<script>
							function gaverder(){
								if (!document.velden.jaar.value){
									alert('Geen jaargekozen!');
								}else{
									document.velden.submit();
								}
							}
						</script>
					<tr>
						<td <?php echo td(1) ?> colspan="2">
							<a onclick="return confirm('Hiermee wordt de boekhouding van dit jaar definitief afgesloten.\n Er kunnen hierna geen wijzigingen meer worden gedaan in dit jaar! \n&nbsp;\nWeet u zeker dat u door wilt gaan?');" href="Javascript:setWaarde('pagina','opslaan_afsluiten'); gaverder();"><img border="0" src="../img/knop_rechts.gif"></a>
						</td>
					</tr>
					</form>
			<?php venster_footer(); ?>
			<?php

	}


	function verwijder_stand(){
		global $id;
		$q = "delete from begin_standen_finance where id = $id";
		sql_query($q) or die(sql_error());
		?><script>document.location.href="./grootboeknummers.php?pagina=standen";</script><?php
	}

	function opslaan_stand(){
		global $id, $stand, $grootboek, $type;
		if ($type == "invoer_stand"){
			$q = "insert into begin_standen_finance (stand, grootboek_id) values ('$stand',$grootboek)";
		}else{
			$q = "update begin_standen_finance set stand = '$stand', grootboek_id = $grootboek where id = $id";
		}
		sql_query($q) or die(sql_error());
		?><script>document.location.href="./grootboeknummers.php?pagina=standen";</script><?php
	}

	function wijzig_stand(){
			global $id, $pagina;

				if ($pagina != "invoer_stand"){
					$q = "select * from begin_standen_finance where id = $id";
					$res = sql_query($q) or die(sql_error());
					$row = sql_fetch_array($res);
					$stand = $row[stand];
				}else{
					$stand = "";
				}

				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php?pagina=standen'";
			?>
			<?php venster_header("grootboeknummers", "begin standen", $menu, 0, -1); ?>
					<form name="standform" action="./grootboeknummers.php" method="post">
					<input type="hidden" name="pagina" value="opslaan_stand">
					<input type="hidden" name="id" value="<?php echo $id ?>">
					<input type="hidden" name="type" value="<?php echo $pagina ?>">
					<tr>
						<td <?php echo td(1) ?>>Grootboek:&nbsp;</td>
						<td <?php echo td(1) ?>>
							<select name="grootboek" class="inputselect">
								<?php
									$q = "select * from grootboeknummers order by nr";
									$res2 = sql_query($q) or die(sql_error());
									while ($row2 = sql_fetch_array($res2)){
										if ($pagina == "invoer_stand"){
											?>
												<option value="<?php echo $row2[nr] ?>"><?php echo $row2[nr] ?> - <?php echo $row2[titel] ?></option>
											<?php
										}else{
											?>
												<option <?php if ($row2[nr] == $row[grootboek_id]){ echo "SELECTED";} ?> value="<?php echo $row2[nr] ?>"><?php echo $row2[nr] ?> - <?php echo $row2[titel] ?></option>
											<?php
										}
									}
								?>
							</select>

						</td>
					</tr>
					<tr>
						<td <?php echo td(1) ?>>Stand:&nbsp;</td>
						<td <?php echo td(1) ?>><input type="text" class="inputtext" name="stand" value="<?php echo $stand ?>"></td>
					</tr>
					<tr>
						<td <?php echo td(1) ?>>
							<a href="Javascript:setWaarde('pagina','opslaan_stand');verzend();"><img border="0" src="../img/knop_rechts.gif"></a>
						</td>
					</tr>
					</form>
			<?php venster_footer(); ?>
			<?php

	}

	function standen_overzicht(){

				$q = "select * from begin_standen_finance order by grootboek_id";
				$res = sql_query($q) or die(sql_error());



				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php'";
				$menu[count($menu)] = "nieuw";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php?pagina=invoer_stand'";
			?>
			<?php venster_header("grootboeknummers", "begin standen", $menu, 0, -1); ?>

				<tr>
					<td <?php echo td(0) ?>>Grootboek</td>
					<td <?php echo td(0) ?>><nobr>Begin bedrag</td>
					<td <?php echo td(0) ?>>&nbsp;</td>
				</tr>
				<?php while ($row = sql_fetch_array($res)){ ?>
					<?php
						$q = "select * from grootboeknummers where nr = $row[grootboek_id]";
						$res2 = sql_query($q);
						$row2 = sql_fetch_array($res2);

					?>
					<tr>
						<td <?php echo td(1) ?>><nobr><?php echo $row2[nr] ?> - <?php echo $row2[titel] ?></td>
						<td <?php echo td(1) ?>><nobr>&euro;&nbsp;<?php echo $row[stand] ?></td>
						<td <?php echo td(1) ?>><nobr>
							<a href="./grootboeknummers.php?pagina=wijzig_stand&id=<?php echo $row[id] ?>"><img border="0" src="../img/knop_bewerk.gif"></a>
							<a onclick="return confirm('Weet u zeker dat u dit item wilt verwijderen?');" href="./grootboeknummers.php?pagina=verwijder_stand&id=<?php echo $row[id] ?>"><img border="0" src="../img/knop_verwijder.gif"></a>
						</td>
					</tr>
				<?php } ?>

			<?php venster_footer(); ?>
			<?php

	}


//------------------------------------------------------------------------------------------
function print_boekingen(){
		global $gbid, $id, $pagina, $start, $end, $begin, $eind, $type, $export;
		
		?>
			<?php
				$start =& $begin;
			
				if ($start[dag] == "")		{ $start[dag] = 1; };
				if ($start[maand] == "")	{ $start[maand] = 1; };
				if ($start[jaar] == "")		{ $start[jaar] = date("Y"); };

				if ($end[dag] == "")			{ $end[dag] = 31; };
				if ($end[maand] == "")		{ $end[maand] = 12; };
				if ($end[jaar] == "")		  { $end[jaar] = date("Y"); };

				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "./grootboeknummers.php";
			?>
			<?php venster_header("grootboeknummers", "overzicht afdrukken", $menu, 0, -1); ?>


				<input type="hidden" name="gbid" value="<?php echo $id ?>">
				<?php if ($id == 0){ ?>
				<tr>
					<td <?php echo td(0) ?>>
						<input type="radio" name="type" value="mutaties" <?php if ($_REQUEST["type"] == "mutaties" || !$_REQUEST["type"]) { echo "checked"; } ?>>&nbsp;Mutaties Grootboek<br>
						<input type="radio" name="type" value="kolom" <?php if ($_REQUEST["type"] == "kolom") { echo "checked"; } ?>>&nbsp;Kolommenbalans<br>
						<!--<input type="radio" name="type" value="debcred">&nbsp;Openstaande posten-->
					</td>
				</tr>
					<tr>
					<td <?php echo td(0) ?>>
						<span class="dT">
						<?php
							$q = "select min(nr) as gbmin, max(nr) as gbmax from grootboeknummers";
							$resx = sql_query($q);
							$rowx = sql_fetch_array($resx);

							if (!$_REQUEST["grootboek_start"]) {
								$gbmin = $rowx["gbmin"];
							} else {
								$gbmin = $_REQUEST["grootboek_start"];
							}
							if (!$_REQUEST["grootboek_eind"]) {
								$gbmax = $rowx["gbmax"];
							} else {
								$gbmax = $_REQUEST["grootboek_eind"];
							}
						?>
						van: <br><input class="inputtext" type="text" name="grootboek_start" value="<?php echo $gbmin ?>"><br>
						t/m: <br><input class="inputtext" type="text" name="grootboek_eind" value="<?php echo $gbmax ?>"><br>
						<i>* van/tot alleen beschikbaar bij de mutaties grootboek optie</i>

					</td>
				</tr>
			<?php } ?>
				<tr>
					<td <?php echo td(0) ?>><nobr>
						<select name="begin[dag]" class="inputselect">
							<?php for($i=1;$i<=31;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($start[dag] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
						&nbsp;
						<select name="begin[maand]" class="inputselect">
							<?php for($i=1;$i<=12;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($start[maand] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
						&nbsp;
						<select name="begin[jaar]" class="inputselect">
							<?php for($i=2002;$i<=2009;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($start[jaar] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td <?php echo td(0) ?>><nobr>
						<select name="end[dag]" class="inputselect">
							<?php for($i=1;$i<=31;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($end[dag] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
						&nbsp;
						<select name="end[maand]" class="inputselect">
							<?php for($i=1;$i<=12;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($end[maand] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
						&nbsp;
						<select name="end[jaar]" class="inputselect">
							<?php for($i=2002;$i<=2009;$i++){ ?>
								<option value="<?php echo $i ?>" <?php if ($end[jaar] == $i){ echo "SELECTED"; } ?>><?php echo $i ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td <?php echo td(0) ?>><nobr>
						<input type="checkbox" name="export" value="1">&nbsp;export to file
					</td>
				</tr>
				<?php if ($pagina == "print_popup"){ ?>
					<?php
						$start = mktime(0,0,0,$begin[maand],$begin[dag],$begin[jaar]);
						$eind  = mktime(0,0,0,$end[maand],$end[dag],$end[jaar]);
						$str_inc = strstr(getenv('HTTP_USER_AGENT'), 'MSIE') ? './' : './';
					?>

					<?php if ($export != 1){ ?>
						<script>
							//alert('<?php echo $str_inc ?>print_grootboek.php?id=<?php echo $id ?>&start=<?php echo $start ?>&eind=<?php echo $eind ?>&type=<?php echo $type ?>&grootboek_start=<?php echo $_REQUEST["grootboek_start"] ?>&grootboek_eind=<?php echo $_REQUEST["grootboek_eind"] ?>');
							window.open('<?php echo $str_inc ?>print_grootboek.php?id=<?php echo $id ?>&start=<?php echo $start ?>&eind=<?php echo $eind ?>&type=<?php echo $type ?>&grootboek_start=<?php echo $_REQUEST["grootboek_start"] ?>&grootboek_eind=<?php echo $_REQUEST["grootboek_eind"] ?>','print','location=no, menubar=no, width=900, height=500, top=20, left=20, status=no ');
						</script>
					<?php }else{ ?>
						<div style="position:absolute;top:0px;left:0px;width:500px;height:400px;visibility:visible">
							<iframe width="500" height="400" src="./export_grootboek.php?id=<?php echo $id ?>&start=<?php echo $start ?>&eind=<?php echo $eind ?>&type=<?php echo $type ?>"></iframe>
						</div>
					<?php } ?>
					</script>
				<?php } ?>
				<tr>
					<td <?php echo td(0) ?> align="right"><nobr>
						<a href="Javascript:setWaarde('pagina','print_popup');verzend();"><img src="../img/knop_ok.gif" border="0"></a>
					</td>
				</tr>


			<?php venster_footer(); ?>
			</form>
			<?php

	}



	function datumopslaan(){
		global $id, $dag, $maand, $jaar, $gb_id;

		$datum = mktime(0,0,0,$maand,$dag,$jaar);
		$q = "update boekingen set datum = $datum where id = $id";
		sql_query($q) or die (sql_error());
		?>
			<script>document.location.href="./grootboeknummers.php?pagina=zoeken&maand=<?php echo $maand ?>&jaar=<?php echo $jaar ?>&id=<?php echo $gb_id ?>";</script>
		<?php
	}


	function datumwijzig(){
		global $id, $maand, $jaar, $gb_id;
		?>
			<input type="hidden" name="id" value="<?php echo $id ?>">
			<input type="hidden" name="pagina" value="datumopslaan">
			<input type="hidden" name="maand" value="<?php echo $maand ?>">
			<input type="hidden" name="jaar" value="<?php echo $jaar ?>">
			<input type="hidden" name="gb_id" value="<?php echo $gb_id ?>">

			<?php
				$q = "select * from boekingen where id = $id";
				$res = sql_query($q);
				$row = sql_fetch_array($res);



				$menu = array();

				$menu[count($menu)] = "terug";
				$menu[count($menu)] = "javascript:document.location.href='./grootboeknummers.php?pagina=zoeken&maand=$maand&jaar=$jaar&id=$gb_id'";
			?>
			<?php venster_header("grootboeknummers", "datum bewerken", $menu, 0, -1); ?>
				<tr>
					<td colspan="2" <?php echo td(0) ?>>bedrag: <b><?php echo $row[bedrag] ?></b></td>
				</tr>
				<tr>
					<td colspan="2" <?php echo td(0) ?>>boekstuknr: <b><?php echo $row[factuur] ?></b></td>
				</tr>
				<tr>
					<td <?php echo td(0) ?>><nobr>
							<?php

								$dag 		= date("d",$row[datum]);
								$maand 	= date("m",$row[datum]);
								$jaar 	= date("Y",$row[datum]);

							?>
							<select name="dag" class="inputselect">
								<?php
								for($i=1;$i<=31;$i++){
									?>
										<option value="<?php echo $i ?>" <?php if ($dag == $i){ echo "SELECTED";}?>><?php echo $i ?></option>
									<?php
								}
								?>
							</select>&nbsp;
							<select name="maand" class="inputselect">
								<?php
								for($i=1;$i<=12;$i++){
									?>
										<option value="<?php echo $i ?>" <?php if ($maand == $i){ echo "SELECTED";}?>><?php echo $i ?></option>
									<?php
								}
								?>
							</select>&nbsp;
							<select name="jaar" class="inputselect">
								<?php
								for($i=2002;$i<=2006;$i++){
								?>
										<option value="<?php echo $i ?>" <?php if ($jaar == $i){ echo "SELECTED";}?>><?php echo $i ?></option>
								<?php
								}
								?>
							</select>


					</td>
					<td <?php echo td(0) ?>><nobr>
					<a href="Javascript:setWaarde('pagina','datumopslaan');verzend();"><img src="../img/knop_ok.gif" border="0"></a>
					</td>
				</tr>


			<?php venster_footer(); ?>
			</form>
			<?php

	}


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



	function boekingenOverzicht(){
		global $id, $menu, $maand, $jaar, $pagina;

		if ($pagina != "zoeken"){
			$jaar = date("Y");
			$maand = date("m");
		}

		//bepaald datumrange
		if ($maand == 0){
			$start = mktime(0,0,0,1,1,$jaar);
			$eind = mktime(0,0,0,12,31,$jaar);
		}else{
			$start = mktime(0,0,0,$maand,1,$jaar);
			$eind = mktime(0,0,0,$maand+1,0,$jaar);
		}

			$q = "select * from grootboeknummers where id = $id ;";
			$res = sql_query($q);
			$row = sql_fetch_array($res);
			$gb_nr = $row["nr"];
			$gb_naam = $row["titel"];

			$totaal[0] = 0;
			$totaal[1] = 0;
			?>

			<?php venster_header("$gb_nr - $gb_naam", "", Array("grootboek","./grootboeknummers.php"), 0, -1); ?>
						<tr>
							<td <?php echo td(1) ?>>
							<nobr>
							toon datum:&nbsp;
								<select name="maand" class="inputselect">
								<option value=0>alle</option>
									<?php for ($i=1;$i<=12;$i++){ ?>
										<option <?php if ($i == $maand){ echo "SELECTED"; } ?> value=<?php echo $i ?>><?php echo $i ?></option>
									<?php } ?>
								</select>
								<select name="jaar" class="inputselect">
									<?php for ($i=2000;$i<=2010;$i++){ ?>
										<option <?php if ($i == $jaar){ echo "SELECTED"; } ?> value=<?php echo $i ?>><?php echo $i ?></option>
									<?php } ?>
								</select>
								<a href="Javascript:setWaarde('pagina','zoeken');verzend();"><img src="../img/knop_ok.gif" border="0"></a>
							</td>
						</tr>
						<tr>
							<td>

						<?php tabel_header(0); ?>



						<tr>
							<td width="250" height="21" <?php echo td(0) ?> ><span class="dT">Credit</span></td>
							<td width="250" height="21" <?php echo td(0) ?> ><span class="dT">Debet</span></td>
						</tr>
						<tr>
						<?php for($x=1;$x>=0;$x--){ ?>
							<td <?php echo td(1) ?>>
								<?php tabel_header(250); ?>
								<?php
									if ($x == 0){
										$q = "SELECT * FROM boekingen WHERE ((credit = 0 AND bedrag > 0) OR (credit = 1 AND bedrag < 0)) AND grootboek_id = $id AND datum BETWEEN $start AND $eind ORDER BY datum;";
									}else{
										$q = "SELECT * FROM boekingen WHERE ((credit = 1 AND bedrag > 0) OR (credit = 0 AND bedrag < 0)) AND grootboek_id = $id AND datum BETWEEN $start AND $eind ORDER BY datum;";
									}
									$result = sql_query ($q) or
										die (sql_error());
								?>
									<?php if (sql_num_rows($result) == 0){ ?>
											<td <?php echo td(1) ?>><span class="d">geen<span></td>
									<?php } ?>


									<?php while ($row = sql_fetch_array($result)){ ?>
										<?php
											if ($row["inkoop"] == 1){
												$target = "../finance/bankboek.php?action=detail&type=inkoop&id=".$row["koppel_id"];
											}elseif ($row["inkoop"] == 0){
													if ($row["betaald"] == 0){
														$q = "select * from omzet_akties where factuur_nr = $row[factuur]";
														$res4 = sql_query($q);
														$row4 = sql_fetch_array($res4);
														$koppelid = $row4[id];
														$target = "../finance/bankboek.php?action=detail&type=verkoop&id=".$koppelid;

													}else{
														$target = "../finance/bankboek.php?action=detail&type=verkoop&id=".$row["koppel_id"];
													}
											}else{
												$target = "?";
											}
											//bedrag omrekenen
											$bedrag = $row[bedrag];
											if ($bedrag < 0){
												$bedrag = 0-$bedrag;
												$bedrag = $bedrag;
												$bedrag_aft = "*";
											}else{
												$bedrag_aft = "";
											}

											$datum = strftime("%d-%m-%Y",$row["datum"]);
											$totaal[$x] += $bedrag;
										?>
										<tr>
											<td <?php echo td(1) ?>><span class="d">
												<table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tr>
														<td <?php echo td(1) ?> width="10"><span class="d">&euro;&nbsp;</td>
														<td <?php echo td(1) ?> align="right"><span class="d"><?php echo fix($bedrag) ?></td>
														<td <?php echo td(1) ?> width="5"><span class="d"><?php echo $bedrag_aft ?></td>
													</tr>
												</table>
											</td>
											<td <?php echo td(1) ?>><span class="d"><?php echo $datum ?><span></td>
											<td <?php echo td(1) ?>><span class="d"><?php echo $row[factuur] ?><span></td>
											<td <?php echo td(1) ?>><span class="d">

												<?php if ($row[inkoop] != 2){ ?>
													<a href="<?php echo $target ?>&gb_id=<?php echo $id ?>&maand=<?php echo $maand ?>&jaar=<?php echo $jaar ?>"><img src="../img/knop_info.gif" border="0"></a>&nbsp;
												<?php }else{ ?>
													<?php
														$q = "select * from overige_posten where id = $row[koppel_id]";
														$res3 = sql_query($q);
														$row3 = sql_fetch_array($res3);
														$descr = $row3[id]." - ".$row3[omschrijving];
													?>
													<a href="javascript:alert('<?php echo $descr ?>');"><img src="../img/knop_bewerk.gif" border="0"></a>&nbsp;
												<?php } ?>
												<?php
													global $xjaar;
													if (!in_array($jaar,$xjaar)){ ?>
												<a href="./grootboeknummers.php?pagina=datumwijzig&id=<?php echo $row[id] ?>&gb_id=<?php echo $id ?>&maand=<?php echo $maand ?>&jaar=<?php echo $jaar ?>"><img src="../img/knop_transact.gif" border="0"></a>
												<?php }else{ ?>
													<?php dicht(); ?>
												<?php } ?>
												<span></td>
										</tr>
									<?php	} ?>
									<?php tabel_footer(); ?>
							</td>
							<?php } ?>

										</td>
									</tr>
									<tr>
										<td <?php echo td(1) ?>>Totaal: &euro;&nbsp;&nbsp;<?php echo fix($totaal[1]) ?></td>
										<td <?php echo td(1) ?>>Totaal: &euro;&nbsp;&nbsp;<?php echo fix($totaal[0]) ?></td>
									</tr>
									<?php
										$q = "select count(*) as aantal from begin_standen_finance where grootboek_id = $gb_nr";
										$res = sql_query($q) or die(sql_error());
										$row = sql_fetch_array($res);
										$aantal = $row[aantal];
										if ($aantal != 0){
											$q = "select * from begin_standen_finance where grootboek_id = $gb_nr";
											$res = sql_query($q);
											$row = sql_fetch_array($res);
											$fbegin = $row[stand];
										}else{
											$fbegin = 0;
										}
											$q = "select sum(bedrag) as totaal from boekingen where grootboek_id = $id and datum <= $eind and credit = 0";
											$res = sql_query($q) or die(sql_error());
											$row = sql_fetch_array($res);
											$fdebet = $row[totaal];

											$q = "select sum(bedrag) as totaal from boekingen where grootboek_id = $id and datum <= $eind and credit = 1";
											$res = sql_query($q) or die(sql_error());
											$row = sql_fetch_array($res);
											$fcredit = $row[totaal];


											$ff = ($fbegin + ($fdebet - $fcredit));

									?>

									<tr>
										<td colspan="2" <?php echo td(1) ?>>Stand op dit moment:&nbsp;&nbsp;<?php echo fix($ff) ?></td>
									</tr>
								<?php tabel_footer(); ?>
							</td>


						</tr>
			<?php venster_footer(); ?>
<?php }



	//------------------------------------------------------------------------------------------
	function grootboekOverzicht(){
		global $sort, $menu, $pagina, $zoekwaarde;

			if ($sort == null) $sort = "nr";

			if ($pagina == "zoekengb" && is_numeric($zoekwaarde) == 1){
				$q = "SELECT * FROM grootboeknummers WHERE titel LIKE '%$zoekwaarde%' OR nr = '$zoekwaarde' ORDER BY $sort";
			}elseif ($pagina == "zoekengb"){
				$q = "SELECT * FROM grootboeknummers WHERE titel LIKE '%$zoekwaarde%' ORDER BY $sort";
			}else{
				$q = "SELECT * FROM grootboeknummers ORDER BY $sort";
			}
			$result = sql_query ($q); ?>

			<?php venster_header("grootboeknummers", "", Array("terug","../finance/bankboek.php","print boekingen","./grootboeknummers.php?pagina=print&id=0","begin standen","./grootboeknummers.php?pagina=standen","afsluiten jaar","./grootboeknummers.php?pagina=afsluiten"), 0, -1); ?>
						<tr>
							<td align="left" <?php echo td(1) ?>>
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
									<tr>
										<td <?php echo td(1) ?>><nobr>Zoeken:
											<input class="inputtext" type="text" name="zoekwaarde" value="<?php echo $zoekwaarde ?>">
											<a href="Javascript:setWaarde('pagina','zoekengb');verzend();"><img src="../img/knop_rechts.gif" border="0"></a>
										</td>
										<td <?php echo td(1) ?> align="right">
											<a href="./grootboeknummers.php?pagina=print&id=0"><img src="../img/knop_print.gif" border="0"></a>
											<a href="Javascript:setWaarde('pagina','nieuw');verzend();"><img src="../img/knop_nieuw.gif" border="0"></a>
											</form>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td <?php echo td(1) ?>>
						<?php tabel_header(0); ?>
						<tr><td height="21" <?php echo td(0) ?> ><span class="dT"><a href="Javascript:setWaarde('sort','nr');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;nr&nbsp;</span></td><td height="21" <?php echo td(0) ?> ><span class="dT"><a href="Javascript:setWaarde('sort','titel');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;titel&nbsp;</span></td><td height="21" <?php echo td(0) ?> ><span class="dT"><a href="Javascript:setWaarde('sort','debiteur');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;deb/cred&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td height="21" <?php echo td(0) ?> ><span class="dT">&nbsp;</span></td></tr>
						<?php	while ($row = sql_fetch_array($result)){ ?>
						<tr>
							<td width="10%" <?php echo td(1) ?>><span class="d">&nbsp;<?php echo $row["nr"]; ?>&nbsp;</span></td>
							<td width="90%" <?php echo td(1) ?>><span class="d"><nobr>&nbsp;<?php echo $row["titel"]; ?>&nbsp;</nobr></span></td>
							<td width="10%" <?php echo td(1) ?>><span class="d">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr><td>&nbsp;&nbsp;</td><td><input class="inputcheckbox" type="checkbox" disabled <?php if ($row["debiteur"]==1){ ?> CHECKED <?php } ?> ></td>
									<td><input type="checkbox" class="inputcheckbox" disabled <?php if ($row["debiteur"]==0){ ?> CHECKED <?php } ?> ></td><td>&nbsp;&nbsp;</td></tr>
								</table></span></td>
							<td width="100" <?php echo td(1) ?>><nobr><span class="d"><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina','verwijder');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_verwijder.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina','transact');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_transact.gif" border="0"></a>

							<a href="./grootboeknummers.php?pagina=print&id=<?php echo $row[id] ?>"><img src="../img/knop_print.gif" border="0"></a>
							<!--<a href="javascript:popUp('print_grootboek.php?id=<?php echo $row[id] ?>', 900, 500, 20, 20)"><img src="../img/knop_print.gif" border="0"></a>-->
							</span></td>
						</tr>
						<?php	} ?>
						<?php tabel_footer(); ?>
					</td></tr>
			<?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function grootboekInvoeren(){
			global $pagina, $id, $menu;

			if ($pagina == "bewerk"){
				$titelp = "bewerk grootboeknummer";
				$sqlQuery = "SELECT * FROM grootboeknummers WHERE id = $id ;";
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_array($result);
					$nr = $row["nr"];
					$titel = $row["titel"];
					$debiteur = $row["debiteur"];
			}else{
				$titelp = "nieuw grootboeknummer";
			} ?>

			<form name="eenform" method="post" action="?">
			<?php venster_header("grootboeknummers", "bewerk", $menu, 0, -1); ?>
							<tr><td align="right"><span class="d">&nbsp;nr&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="nr" value="<?php echo $nr; ?>" style="width:51px;"></td></tr>
							<tr><td align="right"><span class="d">&nbsp;titel&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="titel" value="<?php echo $titel; ?>" style="width:201px;"></td></tr>
							<tr><td align="right"><span class="d">&nbsp;debiteur&nbsp;</span></td><td background="img_td_bg.gif"><input type="checkbox" value="1" name="debiteur" <?php if ($debiteur){ ?> CHECKED <?php } ?> ></td></tr>
							<tr><td colspan="15" align="right"><a href="Javascript:setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>
			<?php venster_footer(); ?>
			</form>
<?php }

	//------------------------------------------------------------------------------------------
	function grootboekInvoerenDb(){
		global $pagina, $id;
		global $nr, $titel, $debiteur;

		if ($debiteur == "1"){$debiteur = 1;}else{$debiteur = 0;}

		if ($pagina == "nieuw_db") $sqlQuery = "INSERT INTO ";
		if ($pagina == "bewerk_db") $sqlQuery = "UPDATE ";
		$sqlQuery .= "grootboeknummers SET ".
			"nr = '$nr', ".
			"titel = '$titel', ".
			"debiteur = $debiteur ";
		if ($pagina == "bewerk_db") $sqlQuery .= " WHERE id = $id ";
		$sqlQuery .= ";";

		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	function grootboekVerwijder(){
		global $id;

		$sqlQuery = "DELETE FROM grootboeknummers WHERE id = $id ;";
		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}
?>

<?php html_footer(); ?>
