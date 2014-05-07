<?php
	require('emoe.inc.php');
	html_header();
	pageNav();

	$aantalwerknemersOmschrijving = array("1-5", "5-25", "25-100", "100-500", "> 500");

	switch ($pagina){
		case "": klantenOverzicht(); break;
		case "nieuw": klantenInvoeren(); break;
		case "bewerk": klantenInvoeren(); break;
		case "nieuw_db": klantenInvoerenDb(); break;
		case "bewerk_db": klantenInvoerenDb(); break;
		case "verwijder": klantenVerwijder(); break;
	}

	//------------------------------------------------------------------------------------------
	function klantenOverzicht(){
		global $sort, $chr, $aantalwerknemersOmschrijving, $linkCvd, $menu; ?>

			<script language="Javascript">
				function toonInfo(id){
					div = eval("div"+id);
					div.style.visibility='visible';
				}
			</script>

			<?php
				$todayVorigJaar = date( "Ymd", time()-(3600*24*365));

				//$sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM akties ;");
				//23-09-02 steve
				$sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM finance_omzet_akties ;");
				$totaalFlow = sql_result($sumResult,0);

				//$sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM akties WHERE datum > ".$todayVorigJaar." ;");
				//23-09-02 steve
        $sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM finance_omzet_akties WHERE datum > ".$todayVorigJaar." ;");
				$totaalFlow12 = sql_result($sumResult,0);

			?>

			<?php //haal soorten bedrijven op
				$soortenbedrijven = array();
				$result = sql_query ("SELECT * FROM finance_soortbedrijf ORDER BY omschrijving;");
					while ($row = sql_fetch_array($result)){
						$soortenbedrijven[$row["id"]] = $row["omschrijving"];
					}


				$t=0;
				if ($sort == null) $sort = "companyname";
				if ($chr == null) $chr = "a";
				if($sort == "totaal_flow" || $sort == "totaal_flow_12") $desc = "DESC";

				if ($chr != "`"){
          $zoekopdr = "SELECT id FROM address WHERE debtor_nr > 0 and companyname LIKE '$chr%' ORDER BY $sort $desc ;";
					$result = sql_query ($zoekopdr , $linkCvd);
				}else{
          $zoekopdr = "SELECT id FROM address where 1=0 and debtor_nr > 0 ORDER BY $sort $desc ;";
					$result = sql_query ($zoekopdr , $linkCvd);
				}

        //print "<br>" . $zoekopdr;

				$address_data = new Address_data();
				$rows = array();
				while ($rowx = sql_fetch_array($result)){
					$row = $address_data->getAddressById($rowx["id"]);
					$rows[] = $row;
					?>
					<div id="div<?php echo $row["id"]; ?>" style="position:absolute;visibility:hidden;left:119px;top:<?php echo ($t*20)-57 ?>px">
						<table border="0" cellspacing="2" cellpadding="0" bgcolor="#FFFFFF"><tr><td>
							<table border="0" cellspacing="1" cellpadding="0" background="img_tabel_bg.gif" onMouseout="Javascript:div<?php echo $row["id"]; ?>.style.visibility='hidden';">
								<tr><td>
									<table border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF" width="302" onMouseover="Javascript:div<?php echo $row["id"]; ?>.style.visibility='visible';">
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;bedrijfsnaam&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><b><?php echo $row["companyname"]; ?></b></span></td></tr>
										<tr><td height="21" background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;debiteur-nummer&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><b><?php echo $row["debtor_nr"]; ?></b></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;aanhef&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["contact_person"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;t.a.v.&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["tav"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;adres&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["address"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;postcode&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["zipcode"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;plaats&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["city"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;land&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["country"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;telefoonnummer&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["phone_nr"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;faxnummer&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["fax_nr"]; ?></span></td></tr>
										<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;email&nbsp;</span></td><td background="img_td_bg.gif"><span class="record"><?php echo $row["email"]; ?></span></td></tr>
									</table></td></tr></table></tr></table>
					</div>
				<?php $t++; } ?>

				<?php venster_header("klanten", "", $menu, 0, -1); ?>
						<!--<tr><td background="img_td_titel_bg.gif" colspan="15"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><span class="titel">klanten</span></td><td width="1"><a href="Javascript:setWaarde('pagina', 'nieuw');verzend();"><img src="../img_knop_nieuw.gif" border="0"></a></td></tr></table></td></tr>-->

						<tr><td><?php tabel_header(0); ?>

						<tr><td <?php echo td(1) ?> colspan="15" align="center">
							<?php for ($i=95;$i<123;$i++){ ?>
									<a href="Javascript:setWaarde('chr', '<?php echo chr($i); ?>');verzend();"><span class='d'>
								<?php	if(chr($i)==$chr){echo("<b>");}
										if ($i!=96){
											echo("".str_replace("_","#&nbsp;&nbsp;",chr($i))."");
										}else{
											echo("#");
										}
									if(chr($i)==$chr){echo("</b>");}
									echo("</a>&nbsp;</span>");
								} ?>
						</span></td></tr>
						<tr>
							<td <?php echo td(0) ?> width="1"><span class="dT">&nbsp;</span></td>
							<td <?php echo td(0) ?> height="21"><span class="dT"><a href="Javascript:setWaarde('sort', 'bedrijfsnaam');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;naam&nbsp;</span></td>
							<td <?php echo td(0) ?>><span class="dT">&nbsp;telefoonnr.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
							<td <?php echo td(0) ?>><span class="dT"><nobr>&nbsp;t.flow&nbsp;(%tot)&nbsp;&nbsp;&nbsp;&nbsp;</nobr></span></td>
							<td <?php echo td(0) ?>><span class="dT"><nobr>&nbsp;12mnd&nbsp;(%tot)&nbsp;&nbsp;</nobr></span></td>
							<!--<td background="img_td_kolom_bg.gif" width="1"><span class="kolom">&nbsp;</span></td>-->
						</tr>
						<?php
							foreach ($rows as $row) {
							//while ($row = sql_fetch_array($result)) {
						?>
						<?php $row["debiteur_nr"] = $row["debtor_nr"]; ?>
						<tr>
							<td <?php echo td(1) ?> width="50"><span class="d"><nobr>&nbsp;<a href="Javascript:setFormAktie('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/producten.php');setWaarde('debiteur_nr', '<?php echo $row["debiteur_nr"]; ?>');verzend();"><img src="../img/knop_product.gif" border="0"></a></span></td>
							<td <?php echo td(1) ?>><span class="d"><nobr>&nbsp;<?php echo $row["companyname"]; ?>&nbsp;</nobr></span></td>
							<td <?php echo td(1) ?>><span class="d">&nbsp;<?php echo $row["phone_nr"]; ?>&nbsp;</span></td>
							<td <?php echo td(1) ?>><span class="d"><nobr>
									<?php	$sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM finance_akties WHERE address_id = ".(int)$row["debiteur_nr"]." ;");
										$totaal = sql_result($sumResult,0);
										echo($totaal); ?>
										<?php if($totaal!=null){ ?>
												&nbsp;(<?php echo(round((100/$totaalFlow)*$totaal,2)); ?>%)<?php
												$sqlQuery = "UPDATE finance_klanten set totaal_flow = $totaal WHERE id = ".$row["id"]." ;";
												sql_query ($sqlQuery);
											} ?>

								</nobr></span></td>
							<td <?php echo td(1) ?>><span class="d"><nobr>
									<?php	$sumResult = sql_query ("SELECT SUM(rekeningflow) as Total FROM finance_akties WHERE address_id = ".(int)$row["debiteur_nr"]." AND datum > ".$todayVorigJaar." ;");
										$totaal = sql_result($sumResult,0);
										echo($totaal); ?>
										<?php if($totaal!=null){ ?>
												&nbsp;(<?php echo(round((100/$totaalFlow12)*$totaal,2)); ?>%)<?php
												$sqlQuery = "UPDATE finance_klanten set totaal_flow_12 = $totaal WHERE id = ".$row["id"]." ;";
												sql_query ($sqlQuery);
											} ?>
								</nobr></span></td>
							<!--<td background="img_td_bg.gif" width="29"><span class="record"><a href="Javascript:setWaarde('pagina', 'bewerk');setWaarde('id', '<?php echo $row["id"]; ?>');verzend();"><img src="../img_knop_bewerk.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina', 'verwijder');setWaarde('id', '<?php echo $row["id"]; ?>');verzend();"><img src="../img_knop_verwijder.gif" border="0"></a></span></td>-->
						</tr>
						<?php	} ?>
				<?php tabel_footer(); ?></td></tr>
			<?php venster_footer(); ?>

<?php }

	//------------------------------------------------------------------------------------------
	function klantenInvoeren(){
			global $pagina, $id, $chr, $aantalwerknemersOmschrijving, $linkCvd;

			if ($pagina == "bewerk"){
				$titel = "bewerk klant";
				$sqlQuery = "SELECT * FROM finance_klanten WHERE id = $id ;";
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_array($result);
					$debiteur_nr = $row["debiteur_nr"];
					$naam = $row["naam"];
					$contactpersoon = $row["contactpersoon"];
					$contactpersoon_voorletters = $row["contactpersoon_voorletters"];
					$adres = $row["adres"];
					$postcode = $row["postcode"];
					$plaats = $row["plaats"];
					$land = $row["land"];
					$telefoonnummer = $row["telefoonnummer"];
					$faxnummer = $row["faxnummer"];
					$email = $row["email"];
					$soortbedrijf_id = $row["soortbedrijf_id"];
					$aantalwerknemers = $row["aantalwerknemers"];
			}else{
				$titel = "nieuwe klant";
			} ?>

				<table border="0" cellspacing="1" cellpadding="0" background="img_tabel_bg.gif">
					<tr><td>
						<table border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
							<tr><td background="img_td_titel_bg.gif" colspan="15"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><span class="titel"><?php echo $titel ?></span></td><td width="1"><a href="Javascript:setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="../img_knop_ok.gif" border="0"></a></td></tr></table></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;bedrijfsnaam&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="naam" value="<?php echo $naam; ?>" style="width:251px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;debiteur nummer&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="debiteur_nr" value="<?php echo $debiteur_nr; ?>" style="width:50px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;aanhef&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="contactpersoon" value="<?php echo $contactpersoon; ?>" style="width:251px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;t.a.v.&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="contactpersoon_voorletters" value="<?php echo $contactpersoon_voorletters; ?>" style="width:251px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;adres&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="adres" value="<?php echo $adres; ?>" style="width:251px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;postcode&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="postcode" value="<?php echo $postcode; ?>" style="width:60px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;plaats&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="plaats" value="<?php echo $plaats; ?>"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;land&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="land" value="<?php echo $land; ?>"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;telefoonnummer&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="telefoonnummer" value="<?php echo $telefoonnummer; ?>"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;faxnummer&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="faxnummer" value="<?php echo $faxnummer; ?>"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;email&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="email" value="<?php echo $email; ?>" style="width:251px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;soort bedrijf&nbsp;</span></td><td background="img_td_bg.gif">
								<select name="soortbedrijf_id" class="inputselect">
								<?php $result = sql_query ("SELECT * FROM soortbedrijf ORDER BY omschrijving;");
										while ($row = sql_fetch_array($result)){ ?>
											<option value="<?php echo $row["id"]; ?>" <?php if ($soortbedrijf_id == $row["id"]){ ?> SELECTED <?php } ?>><?php echo $row["omschrijving"]; ?>
									<?php } ?>
								</select>
							</td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="kolom">&nbsp;aantal werknemers&nbsp;</span></td><td background="img_td_bg.gif">
								<select name="aantalwerknemers" class="inputselect">
									<?php $t=0;
										foreach($aantalwerknemersOmschrijving as $omschr){ ?>
											<option value="<?php echo $t ?>" <?php if ($aantalwerknemers == $t){ ?> SELECTED <?php } ?>><?php echo $omschr ?>
										<?php	$t++;
										} ?>
								</select>
							</td></tr>
					</table></td></tr>
				</table>
<?php }

	//------------------------------------------------------------------------------------------
	function klantenInvoerenDb(){
		global $pagina, $id, $chr;
		global $naam, $debiteur_nr, $contactpersoon, $contactpersoon_voorletters, $adres, $postcode, $plaats, $land, $telefoonnummer, $faxnummer, $email, $soortbedrijf_id, $aantalwerknemers;

		if ($pagina == "nieuw_db") $sqlQuery = "INSERT INTO ";
		if ($pagina == "bewerk_db") $sqlQuery = "UPDATE ";
		$sqlQuery .= "finance_klanten SET ".
			"debiteur_nr = '$debiteur_nr', ".
			"naam = '$naam', ".
			"contactpersoon = '$contactpersoon', ".
			"contactpersoon_voorletters = '$contactpersoon_voorletters', ".
			"adres = '$adres', ".
			"postcode = '$postcode', ".
			"plaats = '$plaats', ".
			"land = '$land', ".
			"telefoonnummer = '$telefoonnummer', ".
			"faxnummer = '$faxnummer', ".
			"email = '$email', ".
			"soortbedrijf_id = $soortbedrijf_id, ".
			"aantalwerknemers = $aantalwerknemers ";
		if ($pagina == "bewerk_db") $sqlQuery .= " WHERE id = $id ";
		$sqlQuery .= ";";

		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	function klantenVerwijder(){
		global $id, $chr;

		$sqlQuery = "DELETE FROM finance_klanten WHERE id = $id ;";
		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}
?>

</form>

<?php html_footer(); ?>
