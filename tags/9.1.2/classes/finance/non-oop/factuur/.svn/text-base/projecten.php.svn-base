<?php
	require('emoe.inc.php');
	html_header();
	pageNav();

	switch ($pagina) {
		case "nieuw": offertesInvoeren(); break;
		case "bewerk": offertesInvoeren(); break;
		case "betaald": offertesBetaald(); break;
		case "nieuw_db": offertesInvoerenDb(); break;
		case "bewerk_db": offertesInvoerenDb(); break;
		case "verwijder": offertesVerwijder(); break;
		case "verwijder_db": offertesVerwijderDb(); break;
		case "status": offertesStatus(); break;
		case "setdefinitief": offerteDefinitief(); break;
		case "changePrijs": changePrijs(); break;
		case "changePrijs2": changePrijs2(); break;
		case "changePrijs3": changePrijs3(); break;
		case "deletePrijs3": changePrijs3(); break;
		default: offertesOverzicht(); break;
	}

	//----------------------------------------------------------------------------------------
	// 25-09-02 By Steve
	function boekingenInvoerenDb($id, $statusid) {
		//Boekingen maken gedeelte
		//Gegevens van de factuur uit de dbase ophalen
		if ($statusid != 0 && $statusid != 1) {
			$q = "SELECT * FROM finance_offertes WHERE id = $id ;";

			$result = sql_query($q)
				or die($q."/".sql_error());

			while ($row = sql_fetch_assoc($result)) {
				$factuurnr = $row["factuur_nr"];
				$link_id = $row["producten_id_$statusid"];
				$btw_prec = $row["btw_prec"];
				$prec_betaald = $row["prec_betaald_$statusid"];
				$debiteur_nr = $row["debiteur_nr"];
				if ($row["address_id"]) {
					$debiteur_nr = $row["address_id"];
				}
				$datumx = $row["datum_".$statusid];
			}

			if ($link_id >= 0) {
				//Haal gegevens per product op en zet dit in de array
				$q = "SELECT * from finance_producten_in_offertes WHERE link_id = $link_id ;";
				$result = sql_query($q)
					or die($q."/".sql_error());

				$i = 0;
				while ($row = sql_fetch_assoc($result)){

					$product_id[$i] = $row["producten_id"];
					$product_aantal[$i] = $row["aantal"];
					$product_btwx[$i] = ($row["btw"]);

					$gb = "SELECT * from finance_producten WHERE id = $product_id[$i] ;";
					$gbresult = sql_query($gb);

					if (sql_num_rows($gbresult) == 0){
						echo $gb;
						echo "critical error: product not found !!!";
						exit();
					}

					$r = sql_fetch_assoc($gbresult);

					$product_grootnr[$i] = $r["grootboeknummer_id"];
					//Prijs ex btw * aantal producten * percentage factuur
					$product_ex[$i] = ($row["prijs"] * $product_aantal[$i] * $prec_betaald / 100);

					$i++;
				}

				//Bereken bedragen per product en zet in array
				for($x=0;$x<$i;$x++){
					$product_btw[$x] = $product_ex[$x] * $product_btwx[$x] / 100;
					$product_inc[$x] = $product_ex[$x] + $product_btw[$x];
				}

				//Bedragen zijn nu bekend, bereken nu de totalen
				for($x=0;$x<$i;$x++){
					$totaal_inc += $product_inc[$x];
					$totaal_btw += $product_btw[$x];
					$totaal_ex += $product_ex[$x];
				}


				//$sqlQuery = "DELETE FROM boekingen WHERE koppel_id = $id AND status = $statusid ;";
				//sql_query ($sqlQuery);


				// Functie Insert Boekingen
				function boek($datumx, $status, $credit, $grootboeknr, $bedrag, $factuur, $product, $debiteur){
					global $id;
					$tyear = substr($datumx, 0, 4);
					$tmonth = substr($datumx, 4, 2);
					$tday = substr($datumx, 6, 2);

					$timest = mktime(0,0,0,$tmonth, $tday, $tyear);

					$sqlQuery = "INSERT INTO finance_boekingen SET ".
						"credit = ".sprintf("%d", $credit).", ".
						"factuur = ".sprintf("%d", $factuur).", ".
						"status = ".sprintf("%d", $status).", ".
						"grootboek_id = ".sprintf("%d", $grootboeknr).", ".
						"bedrag = $bedrag, ".
						"koppel_id = $id,".
						"product = $product,".
						"deb_nr = $debiteur,".
						"datum = ". $timest . ";";
					sql_query ($sqlQuery)
						or die($sqlQuery."</p>".sql_error());
				}


				//Voer alle boekingen in per product
				for($x=0;$x<$i;$x++){
					//op omzet grootboekrekening van het product boeken
					boek($datumx, $statusid, 1, $product_grootnr[$x], $product_ex[$x], $factuurnr, $product_id[$x], $debiteur_nr);
				}

				function grootboeknr($nummer){
					$q = "SELECT * FROM finance_grootboeknummers WHERE nr = $nummer ;";
					$result = sql_query($q)
						or die($q."/".sql_error());

					while ($row = sql_fetch_assoc($result)){
						$gb_id = $row["id"];
					}

					return $gb_id;
				}

				//totaal btw boeken
				boek($datumx, $statusid, 1, grootboeknr(1511), $totaal_btw, $factuurnr, 0, $debiteur_nr);
				//totaal debiteur boeken
				boek($datumx, $statusid, 0, grootboeknr(1300), $totaal_inc, $factuurnr, 0, $debiteur_nr);


			} //end if linkid

		}
		// End of boekgedeelte
	}


	//------------------------------------------------------------------------------------------
	function changePrijs3(){
		global $offerte, $status, $id, $prijs, $pagina;


		if ($pagina == "changePrijs3"){
			$sql = "update producten_in_offertes set prijs = '$prijs' where id = $id";
		}else{
			$sql = "delete from producten_in_offertes where id = $id";
		}

			sql_query($sql) or die($sql);

			$q = "select * from offertes where id = $offerte";
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			$factuur = $row["factuur_nr_".$status];
			$datum = $row["datum_".$status];


		//$sql = "delete from boekingen where status = $status and betaald = 0 and factuur = $factuur";
		//sql_query($sql) or die($sql);

		//boekingenInvoerenDb($offerte, $status)

		?>
			<form action="projecten.php" method="post" name="prijsform">
			<input type="hidden" name="pagina" value="changePrijs">
			<input type="hidden" name="id" value="<?php echo $offerte ?>">
			<input type="hidden" name="status" value="<?php echo $status ?>">
			<script>verzend();</script>
		<?php

	}

	//------------------------------------------------------//
	function changePrijs2(){ ?>

			<?php global $id, $status, $offerte ?>

					<?php
						$q = "select * from offertes where id = $offerte";
						$res = sql_query($q);
						$row = sql_fetch_assoc($res);
						$factuur = $row["factuur_nr_".$status];

					?>

			<?php venster_header("offertes", "bedrag wijzigen - product", array(), 0, -1); ?>
					<tr><td background="img_td_titel_bg.gif" colspan="15" height="1">

					<?php
						$q = "select * from producten_in_offertes where id = $id";
						$res = sql_query($q) or die($q);
						$row = sql_fetch_assoc($res);

							$q = "select * from producten where id = $row[producten_id]";
							$res2 = sql_query($q);
							$row2 = sql_fetch_assoc($res2);
							$titel = $row2[titel];

						?>

						<form action="projecten.php" method="post" name="prijsform">
						<input type="hidden" name="offerte" value="<?php echo $offerte ?>">
						<input type="hidden" name="id" value="<?php echo $id ?>">
						<input type="hidden" name="status" value="<?php echo $status ?>">
						factuur: <?php echo $factuur ?>
						<table>
							<tr>
								<td><?php echo $titel ?></td>
								<td><input type="text" name="prijs" value="<?php echo $row[prijs] ?>"></td>
								<td>
									<a href="Javascript:setWaarde('pagina','changePrijs3');verzend();"><img src="../img/knop_ok.gif" border="0"></a>
									<a href="Javascript:setWaarde('pagina','deletePrijs3');verzend();" onclick="return confirm('Weet u dit zeker??');"><img src="../img/knop_verwijder.gif" border="0"></a>
								</td>
							</tr>
						</table>

						<?php
					?>
					</table>
					</td></tr>
			<?php venster_footer(); ?>
<?php	}


	//------------------------------------------------------------------------------------------
	function changePrijs(){ ?>

			<?php global $id, $status ?>
			<?php venster_header("offertes", "bedrag wijzigen", array(), 0, -1); ?>
					<tr><td background="img_td_titel_bg.gif" colspan="15" height="1">

					<?php
						$q = "select * from offertes where id = $id";
						$res = sql_query($q);
						$row = sql_fetch_assoc($res);
						$factuur = $row["factuur_nr_".$status];
						$link = $row["producten_id_".$status];
						$btw = $row["btw_prec"];
						$perc = $row["prec_betaald_".$status];


						$q = "select * from omzet_akties where factuur_nr = $factuur";
						$res = sql_query($q);
						$row = sql_fetch_assoc($res);
						$omzet = $row[rekeningflow];
					?>

					factuur: <?php echo $factuur ?>

						<script language="Javascript1.2">
							function fix(bedrag){
								document.write (bedrag.toFixed(2));
							}
						</script>

						<table>
						<tr>
							<td <?php echo td(0) ?>>product</td>
							<td <?php echo td(0) ?>>aantal</td>
							<td <?php echo td(0) ?>>bedrag</td>
							<td <?php echo td(0) ?>>totaal</td>
							<td <?php echo td(0) ?>></td>

				<?php
						$q = "select * from producten_in_offertes where link_id = $link";
						$res = sql_query($q) or die($q);
						while ($row = sql_fetch_assoc($res)){

							$q = "select * from producten where id = $row[producten_id]";
							$res2 = sql_query($q);
							$row2 = sql_fetch_assoc($res2);
							$titel = $row2[titel];

						?>
							<tr>
								<td <?php echo td(1) ?>><?php echo $titel ?></td>
								<td <?php echo td(1) ?>><?php echo $row[aantal] ?></td>
								<td <?php echo td(1) ?>><?php echo $row[prijs] ?></td>
								<td <?php echo td(1) ?>><script>fix(<?php echo $row[prijs]*$row[aantal] ?>);</script></td>
								<td <?php echo td(1) ?>><a href="factuur/projecten.php?pagina=changePrijs2&id=<?php echo $row[id] ?>&status=<?php echo $status ?>&offerte=<?php echo $id ?>"><img src="../img/knop_bewerk.gif" border="0"></a></td>
							</tr>


						<?php

							$tot += ($row[prijs]*$row[aantal]);


						}
						$tot = (($tot*(($btw/100)+1))*$perc/100);
					?>


							<tr>
								<td>totaal omzet: <?php echo $omzet ?> / berekend: <script>fix(<?php echo $tot ?>);</script></td>
							</tr>

					</table>
					</td></tr>
			<?php venster_footer(); ?>
<?php	}



	//------------------------------------------------------------------------------------------

	function offertesOverzicht() {
		global $sort, $chr, $zoeken, $overzicht, $menu, $theme, $zoeken, $linkCvd, $maand, $jaar;


		if ($_REQUEST["buf_klanten"] && !$klanten) $klanten = $_REQUEST["buf_klanten"];
		if ($_REQUEST["buf_zoeken"]  && !$zoeken)  $zoeken  = $_REQUEST["buf_zoeken"];
		if ($_REQUEST["buf_maand"]   && !$maand)   $maand   = $_REQUEST["buf_maand"];
		if ($_REQUEST["buf_jaar"]    && !$jaar)    $jaar    = $_REQUEST["buf_jaar"];

		$statusOmschrijving = array("offerte", "opdr.", "fact. 1", "fact. 2");
		?>
			<script language="Javascript">
				function toonInfo(id){
					div = eval("div"+id);
					div.style.visibility='visible';
				}
			</script>
			<?php
				$t=0;
				/* input validation */
				if (!in_array($sort, array(
					"factuur_nr", "bedrijfsnaam", "omschrijving", "datum"
				))) {
					unset($sort);
				}
				if (!$sort)
					$sort = "bedrijfsnaam";

				if ($overzicht) {
					if ($overzicht == "offertes")   $ov = " AND status = 0 ";
					if ($overzicht == "opdrachten") $ov = " AND status = 1 ";
					if ($overzicht == "facturen1")  $ov = " AND status = 2 ";
					if ($overzicht == "facturen2")  $ov = " AND status = 3 ";
				}
				if ($chr)
					unset($_REQUEST["klanten"]);

				if ($_REQUEST["klanten"]) {
					$q = sprintf("select * from finance_offertes where address_id > 0 and address_id IN
						(select debtor_nr from address where id = %d) order by %s %s", $_REQUEST["klanten"],
						$sort, $desc);
				} else {
					if (!$zoeken) {
						if (!$chr) $chr = "a";

						if ($chr == "`") {
							// no selection
							$q = "select * from finance_offertes where 1 = 0";
						} else {
							$q = sprintf("select * from finance_offertes where bedrijfsnaam like '%s%%' %s order by %s %s",
								$chr, $ov, $sort, $desc);
						}
					} else {
						//zoek de producten
						$lids = array(-99);
						$q = sprintf("select link_id from finance_producten_in_offertes where omschrijving like '%%%s%%' group by link_id",
							$zoeken);
						$resx = sql_query($q);
						while ($rowx = sql_fetch_assoc($resx)) {
							$lids[] = $rowx["link_id"];
						}
						$lids = implode(",",$lids);

						$fields_single = array("titel", "address_id", "datum", "bedrijfsnaam");
						$fields_multi  = array("html", "factuur_nr");
						foreach ($fields_multi as $m) {
							for ($i=0;$i<=3;$i++)
								$fields_single[] = sprintf("%s_%d", $m, $i);
						}
						foreach ($fields_single as $k=>$v) {
							$fields_single[$k] = sprintf(" %s like '%%%s%%' ", $v, $zoeken);
						}
						$q = sprintf("select * from finance_offertes where (%s) order by %s %s",
							implode(" OR ", $fields_single), $sort, $desc);
					}
				}
				$res = sql_query($q);
				$total = sql_num_rows($res);

				$result = sql_query($q, "", (int)$_REQUEST["paging"], $GLOBALS["finance"]->pagesize);

				 ?>

			<?php venster_header("offertes/facturen", "", $menu, 0, -1); ?>
						<tr><td align="right"><span class="d"><a href="Javascript:setWaarde('statusnieuw', '0');setWaarde('pagina','nieuw');verzend();"><img src="../img/knop_nieuw.gif" border="0"></a>&nbsp;offerte</span></td></tr>
						<tr><td align="right"><span class="d"><a href="Javascript:setWaarde('statusnieuw', '2');setWaarde('pagina','nieuw');verzend();"><img src="../img/knop_nieuw.gif" border="0"></a>&nbsp;factuur</span></td></tr>
						<tr><td>
							<?php tabel_header(0); ?>
								<tr>
									<td colspan="2" align="center" <?php echo td(1) ?> >
										<?php for ($i = 95; $i < 123; $i++) { ?>
											<a href="Javascript:setWaarde('zoeken', ''); setWaarde('klanten', ''); setWaarde('maand', ''); setWaarde('jaar', ''); setWaarde('pagina', '');setWaarde('chr', '<?php echo chr($i); ?>');verzend();"><span class='d'>
											<?php
											if (chr($i)==$chr) {
												echo("<b>");
											}
											if ($i != 96){
												echo(str_replace("_","",chr($i)) );
											} else {
												echo("#");
											}
											if (chr($i) == $chr) {
												echo("</b>");
											}
											?>
											</span></a>
										<?php } ?>
									</td>
									<td colspan="7" <?php echo td(1) ?>>
										<table border="0" cellspacing="0" cellpadding="0">
											<tr><td align="right"><span class="d">zoeken op tekst:&nbsp;</span></td><td><input type="text" class="inputtext" name="zoeken" value="<?php echo ($zoeken) ?>" style="width:200px;">&nbsp;<a href="Javascript: setWaarde('klanten', ''); setWaarde('maand', ''); setWaarde('jaar', ''); verzend();"><img src="../img/knop_rechts.gif" border="0"></a></td></tr>
											<tr><td height="4"></td></tr>
											<tr><td align="right"><span class="d">of zoeken op debiteur:&nbsp;</span></td><td>
												<input type="hidden" id="klanten" name="klanten" value="<?php echo $_REQUEST["klanten"] ?>">

											<?php if (!$address_data) $address_data = new Address_data(); ?>
											<span id="klanten_layer"><?php echo $address_data->getAddressNameById($_REQUEST["klanten"]) ?></span>
											<a href="javascript: popup('/?mod=address&action=searchRel', 'search_address');">kies relatie</a>
											&nbsp;<a href="Javascript: setWaarde('zoeken', ''); setWaarde('maand', ''); setWaarde('jaar', ''); setWaarde('chr', ''); verzend();"><img src="../img/knop_rechts.gif" border="0" align="absmiddle"></a>
											<script type="text/javascript">
												function selectRel(id, str) {
													document.getElementById('klanten').value = id;
													document.getElementById('klanten_layer').innerHTML = str;
												}
											</script>
										</td></tr>
										<tr><td height="4"></td></tr>
										<tr><td align="right"><span class="d">of zoeken op datum:&nbsp;</span></td><td>
											<select name="maand" class="inputselect">
												<?php
													if ($maand == ""){ $maand = date("m");}
													if ($jaar == ""){ $jaar = date("Y");}

												?>
												<?php for($i=1;$i<=12;$i++){
														$m = ($i<10)?"0".$i:$i; ?>
														<option value="<?php echo $m ?>" <?php if($m==$maand){ ?> SELECTED <?php } ?> ><?php echo $m ?>
												<?php } ?>
											</select>

											<select name="jaar" class="inputselect">
												<?php for($i=$GLOBALS["finance"]->data->getFirstRecordDate();$i<date("Y")+1;$i++){ ?>
														<option value="<?php echo $i ?>" <?php if($jaar==$i){ ?> SELECTED <?php } ?> ><?php echo $i ?>
												<?php } ?>
											</select>&nbsp;<a href="Javascript:document.velden.zoeken.value=document.velden.jaar.value+document.velden.maand.value;verzend(); setWaarde('zoeken', ''); setWaarde('klanten', ''); verzend();"><img src="../img/knop_rechts.gif" border="0" align="absmiddle"></a>
										</td></tr>
									</table></td></tr>
						<tr>
							<td <?php echo td(0) ?> height="21"><?php echo $db_database ?><a href="Javascript:setWaarde('sort','factuur_nr');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp; factuur nr</td>
							<td <?php echo td(0) ?> height="21"><a href="Javascript:setWaarde('sort','bedrijfsnaam');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;bedrijfsnaam</td>
							<td <?php echo td(0) ?> height="21"><a href="Javascript:setWaarde('sort','titel');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;titel</td>
							<td <?php echo td(0) ?> height="21"><a href="Javascript:setWaarde('sort','datum');verzend();"><img src="../img/knop_sort.gif" align="right" border="0"></a>&nbsp;datum</td>
								<?php if ($overzicht != ""){ ?>
									<td <?php echo td(0) ?> colspan="4">&nbsp;kosten&nbsp;</td>
								<?php } ?>
								<?php for ($j=0;$j<count($statusOmschrijving);$j++){ ?>
									<td <?php echo td(0) ?> >&nbsp;<?php echo $statusOmschrijving[$j]; ?>&nbsp;</td>
								<?php } ?>
								<td <?php echo td(0) ?>  width="80">&nbsp;</td>
						</tr>
						<?php	while ($row = sql_fetch_assoc($result)){

								// aangezien er meerdere velden zijn voor het factuurnummer (vanwege de meerdere stati)
								// moet het 'geldige' factuurnummer en datum in een apart veld factuur en datum worden
								// opgeslagen zodat je er op kunt sorteren

								/*
								wtf?
								if ($row["factuur_nr".$row["status"]]){
									$q = sprintf("update offertes set datum = %d, factuur_nr = %d, where id = %d",
										$row[sprintf("datum_%d", $row["status"])],
										$row[sprintf("factuur_nr_%d", $row["status"])],
										$id
									);
									sql_query($q);
								}
								*/

								if (!$address_data)
									$address_data = new Address_data();

								$row["debiteur_nr"] =& $row["address_id"];
								if ($row["debiteur_nr"])
									$klantid = $address_data->getAddressIdByDebtor($row["debiteur_nr"]);
								else
									$klantid = 0;


								$_omzet_akties = array();
								$q = sprintf("select id from finance_omzet_akties where factuur_nr = %d", $row["factuur_nr"]);
								$resx = sql_query($q);
								$rowx = sql_fetch_assoc($resx);
								$off_id = $rowx["id"];
						 ?>
						<tr>
							<?php if ($GLOBALS["covide"]->license["has_finance"]) { ?>
								<td <?php echo td(1) ?>>&nbsp;<a href="<?php echo $GLOBALS["finance"]->include_dir_finance ?>/bankboek.php?action=detail&type=verkoop&id=<?php echo $off_id ?>"><?php echo ($row["factuur_nr"]!="0" && $row["factuur_nr"]!="")?$row["factuur_nr"]:"-"; ?></a>&nbsp;</td>
							<?php } else { ?>
								<td <?php echo td(1) ?>>&nbsp;<?php echo ($row["factuur_nr"]!="0" && $row["factuur_nr"]!="")?$row["factuur_nr"]:"-"; ?>&nbsp;</td>
							<?php } ?>
							<td <?php echo td(1) ?>><a href="?mod=address&action=relcard&funambol_user=<?php echo $_SESSION["user_id"] ?>&id=<?php echo $klantid ?>"><?php echo $row["bedrijfsnaam"] ?></a></td>
							<td <?php echo td(1) ?>><?php echo $row["titel"] ?></td>
							<td <?php echo td(1) ?>>&nbsp;<?php echo convertDatum($row["datum_".$row["status"]]); ?>&nbsp;</td>

						<?php if ($overzicht != ""){
								if($row["producten_id_".$row["status"]] !=""){
									$kosten = berekenProdTotaal($row["producten_id_".$row["status"]], $row["prec_betaald_".$row["status"]]);
									$totaal+=$kosten[0];
									$totaalBtw+=$kosten[1];
								}
							?>

							<td <?php echo td(1) ?> align="left" colspan="4">&nbsp;&euro; <?php echo ($kosten[0]!="" && $row["producten_id_".$row["status"]] !="")?$kosten[0]:"0"; ?></td>
						<?php } ?>

							<?php for ($j=0;$j<count($statusOmschrijving);$j++){ ?>
								<td <?php echo td(1) ?> align="left">&nbsp;
								<?php
									/*
									als offerte/opdracht aanwezig
									factuur2 vakje
									factuur3 vak & factuur 2 < 100%
									factuur3 als factuur2 aanwezig
									- dan pas vakje tonen
									*/
								?>
								<?php $hideOpdr = 0; ?>
								<?php if (($j <= 1 && $row["producten_id_0"] != null) || ($j == 2) || ($j == 3 && $row["prec_betaald_2"] < 100 && $row["prec_betaald_2"] != null)){ ?>
									<?php if ($j==0 && $j == $row["status"]) { $hideOpdr = 1; } ?>
									<?php if (($j==1 && $hideOpdr!=1) || $j!=1) { ?>
										<input onClick="setWaarde('pagina','status');setWaarde('statusid','<?php echo $j ?>');setWaarde('id','<?php echo $row["id"]; ?>');verzend();" type="checkbox" class="inputcheckbox" <?php if ($j == $row["status"]){ ?> CHECKED <?php } ?> >
									<?php } ?>
								<?php } ?>
								<?php if ($j > 1 && $row["prec_betaald_$j"] != null){ ?><?php echo $row["prec_betaald_".$j] ?>%<?php } ?>
								</td>
							<?php } ?>

							<td <?php echo td(1) ?> width="53">
								<?php
								$q = sprintf("select * from finance_offertes where id = %d", $row["id"]);
								$rs = sql_query($q);
								$r = sql_fetch_assoc($rs);

								//$r = $_offertes[$row["id"]];
								$status = $r["status"];


								/*
								mogelijke situaties:

								offerte zonder definitieve factuur					>	print, bewerk, verwijder
								offerte met definitieve factuur							> print, view
								opdr status met gegevens zonder def fact		> print, bewerk,
								opdr status zonder gegevens met def fact		> bewerk, verwijder
								opdr status met gegevens met def fact				> print, view
								opdr status zonder gegevens met def fact		> niks!
								factuur status met gegevens niet def				> print, bewerk, def, verwijder
								factuur status zonder gegevens niet def			> bewerk, verwijder
								factuur status met gegevens wel def					> print, view
								*/

								$bewerk = 0;
								$print = 0;
								if ($status >= 2){
									if ($r["definitief_$status"] == 0){
										$bewerk = 1;
									}
									if ($r["producten_id_$status"] != null){
										$print = 1;
									}
								}else{
									if ($r["definitief_2"] == 0 && $r["definitief_3"] == 0){
										$bewerk = 1;
									}
									if ($r["producten_id_$status"] != null){
										$print = 1;
									}
								}

								/*
								?>
								<!--
								disabled. alleen voor achteraf debug
								<a href="factuur/projecten.php?pagina=changePrijs&status=<?php echo $row[status] ?>&id=<?php echo $row[id] ?>"><img src="../img/knop_transact.gif" border="0"></a>
								-->
								<?php
								*/

								if ($bewerk == 1 && $print == 1){
								?>
									<a href="Javascript:popUp('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/print.php?id=<?php echo $row["id"]; ?>&status=<?php echo $row["status"]; ?>',600,400);"><img src="../img/knop_print.gif" border="0"></a>
									<a href="Javascript:popup('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/print.php?id=<?php echo $row["id"]; ?>&status=<?php echo $row["status"]; ?>&email=1','email_factuur', 980,720, 1);"><img src="../img/knop_email.gif" border="0"></a>
									<a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a>
								<?php
								}elseif ($print == 1){
								?>
									<a href="Javascript:popUp('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/print.php?id=<?php echo $row["id"]; ?>&status=<?php echo $row["status"]; ?>',600,400);"><img src="../img/knop_print.gif" border="0"></a>
									<a href="Javascript:popup('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/print.php?id=<?php echo $row["id"]; ?>&status=<?php echo $row["status"]; ?>&email=1','email_factuur', 980,720, 1);"><img src="../img/knop_email.gif" border="0"></a>

									<!--<a href="Javascript:popUp('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/print.php?view=1&id=<?php echo $row["id"]; ?>&status=<?php echo $row["status"]; ?>',600,400);"><img src="../img/knop_view.gif" border="0"></a>-->
								<?php
								}
								/*
								if ($bewerk == 1 || $bewerk == 0) {
									?><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a><?php
								}
								*/
								if (($status == 2 || $status == 3) && $r["definitief_$status"] == 0 && $r["prec_betaald_$status"] != null){
								?>
									<a href="Javascript:setWaarde('pagina','setdefinitief');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_rechts.gif" border="0"></a>
								<?php
								}
								if ($r["definitief_2"] == 0 && $r["definitief_3"] == 0){
								?>
									<a href="Javascript:setWaarde('pagina','verwijder');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_verwijder.gif" border="0"></a>
								<?php
								}

								?>
								</td>

						</tr>
						<?php	} ?>
						<?php
							$paging = new Layout_paging();
							$paging->setOptions((int)$_REQUEST["paging"], $total, "javascript: setWaarde('paging', '%%'); verzend();", $GLOBALS["finance"]->pagesize);
							echo "<tr><td colspan='10'>";
							echo( $paging->generate_output() );
							echo "</td></tr>";

						?>
						<?php if ($overzicht != "" && $totaal != ""){ ?>
							<tr><td colspan="4"></td><td colspan="10" <?php echo td(2) ?>>&nbsp;&euro; <?php echo $totaal; ?></td></tr>
						<?php } ?>
				<?php tabel_footer(); ?>
			</td></tr><?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function offertesInvoeren(){
			global $pagina, $id, $chr, $linkCvd, $s, $statusnieuw;

			if ($pagina == "bewerk") {
				$titelp = "bewerk offerte/factuur";
				$sqlQuery = sprintf("SELECT * FROM finance_offertes WHERE id = %d", $id);
				$result = sql_query($sqlQuery);
				$row = sql_fetch_assoc($result);

				$status        = $row["status"];
				$datum         = $row["datum_".$status];
				$producten_id  = $row["producten_id_".$status];
				$html          = $row["html_".$status];
				$prec_betaald  = $row["prec_betaald_".$status];
				$factuur_nr    = $row["factuur_nr_".$status];
				$template_id   = $row["template_id"];
				$definitief    = $row["definitief_".$status];
				$bcard_id      = $row["bcard_id"];

				$font["font"]              = $row["font"];
				$font["fontsize"]          = $row["fontsize"];
				$font["template_setting"]  = $row["template_setting"];

				if ($status && !$producten_id) {
					if (!$datum)
						$datum = $row["datum_".($row["status"]-1)];

					//haal producten uit vorige status-factuur
					//eerst hoogste link_id uit db halen
					//producten_id in een factuur == link_id
					$q = sprintf("SELECT * FROM finance_producten_in_offertes ORDER BY link_id DESC");
					$result2 = sql_query($q);
					if (sql_num_rows($result2) == 0) {
						$producten_id = 0;
					} else {
						$row2 = sql_fetch_assoc($result2);
						$producten_id = $row2["link_id"]+1;
					}
					//producten van vorige status-factuur uitlezen en kopieeren naar
					//de huidige status-factuur
					$producten_id_vorig = $row["producten_id_".($row["status"]-1)];

					$qz = sprintf("SELECT * FROM finance_producten_in_offertes WHERE link_id = %d", $producten_id_vorig);
					$result3 = sql_query($qz);
					while($row3 = sql_fetch_assoc($result3)) {
						$q = sprintf("insert into finance_producten_in_offertes (btw, prijs, producten_id, link_id, aantal, omschrijving)
							values ('%s', '%s', %d, %d, %d, '%s')",
							addslashes($row3["btw"]),
							addslashes($row3["prijs"]),
							$row3["producten_id"],
							$producten_id,
							$row3["aantal"],
							addslashes($row3["omschrijving"]));

						sql_query($q);
					}
					$q = sprintf("update finance_offertes set producten_id_%d = %d where id = %d",
						$row["status"], $producten_id, $id);
					sql_query($q);

					$html = $row["html_".($row["status"]-1)];
				}

				/* copy template */
				$template = new Templates_data();
				$template->templateCopyFinance($producten_id_vorig, $producten_id);

					if ($status > 1) {
						if (!$prec_betaald && !$row["prec_betaald_".($row["status"]-1)])
							$prec_betaald = 50;
						elseif (!$prec_betaald)
							$prec_betaald = 100 - $row["prec_betaald_".($row["status"]-1)];
					}
					$debiteur_nr = $row["address_id"];
					$titel = $row["titel"];
					$uitvoerder = $row["uitvoerder"];
					$bedrijfsnaam = $row["bedrijfsnaam"];

					$btw_tonen = $row["btw_tonen"];
					$btw_prec = $row["btw_prec"];

					$btw_prec = (float)$btw_prec;

			} else {
				$titelp = "nieuw project";
				$datum = date("Ymd");
				$status = $statusnieuw;
				$btw_tonen = 1;
				$btw_prec = (float)"19.0";

				if ($_REQUEST["address_id_new"]) {
					$address_data = new Address_data();
					$address_info = $address_data->getAddressById($_REQUEST["address_id_new"]);
					$debiteur_nr = $address_info["debtor_nr"];
				}
			}
			if (!$html) $html = "<p>[producten hier]</p>";

			if (!$factuur_nr) {
				$sqlQuery = "SELECT * FROM finance_teksten WHERE id = 2";
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_assoc($result);
				$factuur_nr = (int)$row["html"] + 1;
				if ($status > 1)
					$factuur_nieuw = true;
			}
			$statusOmschrijving = array("offerte", "opdracht", "factuur 1", "factuur 2");


			?>
			<script>setWaarde('statusid', '<?php echo $status; ?>');</script>

			<table border="0" cellspacing="0" cellpadding="0"><tr><td valign="top">
			<?php venster_header("offertes/facturen", $titelp." (".$statusOmschrijving[$status].")", $menu, 0, -1); ?>
				<tr>
					<td align="right" <?php echo td(0); ?>>
						<span class="d">&nbsp;referentie / omschrijving</span>
					</td>
					<td>
						<?php if (!$definitief) { ?>
							<input type="text" class="inputtext" name="titel" value="<?php echo $titel ?>" style="width:300px;">
						<?php } else { ?>
							<input type="hidden" name="titel" value="<?php echo $titel ?>">
							<?php echo $titel ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td align="right" <?php echo td(0); ?>>
						<span class="d">&nbsp;debiteurnummer / klant</span>
					</td>
					<td>&nbsp;
						<?php
							if (!$address_data)
								$address_data = new Address_data();

							if ($debiteur_nr)
								$address_id = $address_data->getAddressIdByDebtor($debiteur_nr);

							$address_name = $address_data->getAddressNameById($address_id);

						?>
						<span id="deb_id_txt"><?php echo ($debiteur_nr) ? $debiteur_nr:"--" ?></span>&nbsp;/&nbsp;<span id="klanten_layer"><?php echo $address_name ?></span>
						<?php if (!$definitief) { ?>
							<a href="javascript: popup('/?mod=address&action=searchRel', 'search_address');">kies relatie</a>
							<script type="text/javascript">
								function selectRel(id, str) {
									document.getElementById('klanten_layer').innerHTML = str;

									var ret = loadXMLContent('/?mod=address&action=getDebtornrById&id=' + id);
									document.getElementById('deb_id_txt').innerHTML = ret;
									document.getElementById('deb_id').value = ret;
									document.getElementById('address_id').value = id;
									updateBcards();
								}
								function popupProducten() {
									popup('<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/stap.php?nrstap=6&stapomschr=producten&verbergIface=true&debiteur_nr=' + document.getElementById('deb_id').value + '&producten_id=' + document.getElementById('producten_id').value, 'producten', 800, 600, 1);
								}
								function updateBcards() {
									var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + document.getElementById('address_id').value + '&current=' + document.getElementById('bcard_id_selected').value);
									document.getElementById('bcard_layer').innerHTML = ret;
								}
							</script>
						<?php } else { ?>

						<?php } ?>
						<input type="hidden" id="deb_id" name="debiteurnr" value="<?php echo $debiteur_nr; ?>">
						<input type="hidden" id="address_id" name="address_id" value="<?php echo $address_id; ?>">
					</td>
				</tr>
				<tr>
					<td align="right" <?php echo td(0) ?>>
						<span class="d">Businesscard</span>
					</td>
					<td>
						<input type="hidden" id="bcard_id" name="bcard_id" value="<?php echo $bcard_id ?>">
						<input type="hidden" id="bcard_id_selected" name="bcard_id_selected" value="<?php echo $bcard_id ?>">
						<div id="bcard_layer" style="display: block;"></div>
					</td>
				</tr>
				<tr>
					<td align="right" <?php echo td(0); ?>>
						<span class="d">&nbsp;datum</span>
					</td>
					<td background="img_td_bg.gif">
						<?php if (!$definitief) { ?>
							<?php plaatsDatumVelden($datum, false); ?>
						<?php } else { ?>
							<input type="hidden" name="dag" value="<?php echo substr($datum, 6, 2) ?>">
							<input type="hidden" name="maand" value="<?php echo substr($datum, 4, 2) ?>">
							<input type="hidden" name="jaar" value="<?php echo substr($datum, 0, 4) ?>">
							<?php
								echo sprintf("%d-%d-%d",
									substr($datum, 6, 2),
									substr($datum, 4, 2),
									substr($datum, 0, 4)
								);
							 ?>
						<?php } ?>
					</td>
				</tr>
				<?php if (!$definitief) { ?>
				<tr>
					<td align="right"<?php echo td(0); ?>><span class="d">producten</span></td>
					<td background="img_td_bg.gif">&nbsp;
						<a href="javascript: popupProducten();"><img src="/img/knop_product.gif" border="0"> kies de producten</a>
					</td>
				</tr>
				<?php } ?>
				<tr>
						<td align="right"<?php echo td(0); ?>><span class="d">template</span></td>
						<td background="img_td_bg.gif">&nbsp;
							<script>
								function mkTemplate() {
									if (!document.getElementById('producten_id').value) {
										alert('Kies eerst de producten.');
									} else {
										popup('index.php?mod=templates&action=edit&finance=' + document.getElementById('producten_id').value, 'salesedit', 0, 0, 1);
									}
								}
								function rmTemplate() {
									var cf = confirm('Weet u zeker dat u de brief template die bij deze factuur hoort wilt verwijderen?');
									if (cf == true) {
										popup('index.php?mod=templates&action=delete_finance&finance=' + document.getElementById('producten_id').value, 'salesedit', 0, 0, 1);
									}
								}
							</script>
							<span id="template_edit" style="display: none;">
								<a href="javascript: mkTemplate();"><img src="/img/knop_bewerk.gif" border="0"> brief template bewerken</a><br>&nbsp;
								<a href="javascript: rmTemplate();"><img src="/img/knop_verwijder.gif" border="0"> brief template verwijderen </a>
							</span>
							<span id="template_new" style="display: none;">
								<a href="javascript: mkTemplate();"><img src="/img/knop_nieuw.gif" border="0"> maak een brief template</a> of kies een standaard opmaak:
								&nbsp;
								<?php
									$conversion = new Layout_conversion();
									$fonts = $conversion->getFonts();

									$output = new Layout_output();
									$output->addSelectField("font[font]", $fonts["fonts"], $font["font"]);
									$output->addSelectField("font[fontsize]", $fonts["sizes"], $font["fontsize"]);

									$template = new Templates_data();
									$sel = $template->getTemplateSettings();
									$sel2 = array(
										"standaard" => array(
											0 => "standaard briefpapier"
										)
									);
									foreach ($sel as $k=>$v) {
										$sel2["aangepast"][$k] = $v;

									}
									$output->addSelectField("font[template_setting]", $sel2, $font["template_setting"]);
									echo $output->generate_output();
								?>
							</span>
							<?php
								$q = sprintf("select count(*) from templates where finance = %d", $producten_id);
								$resf = sql_query($q);
								$statef = sql_result($resf,0);
								if ($statef == 1) {
									?><script>document.getElementById('template_edit').style.display = '';</script><?php
								} else {
									?><script>document.getElementById('template_new').style.display = '';</script><?php
								}
								?>
						</td>
					</tr>
						<?php if ($status>1){ ?>
							<tr><td align="right"<?php echo td(0); ?>>
								<span class="d">&nbsp;factuurnummer</span></td><td>
									<input type="hidden" name="factuurnr" value="<?php echo $factuur_nr ?>">
									<?php echo $factuur_nr; ?>
							</td></tr>
							<tr><td align="right"<?php echo td(0); ?>><span class="d">&nbsp;% betalen</span></td>
								<td background="img_td_bg.gif">
									<?php if (!$definitief) { ?>
										<select name="prec_betaald" class="inputselect">
											<optgroup label="standaard percentages" title="standaard percentages">
												<?php for($i=100;$i>0;$i-=25) { ?>
													<?php
														if ($i==$prec_betaald) {
															$ffound = 1;
														} elseif (!$prec_betaald) {
															$ffound = 1;
														}
													?>
													<option value="<?php echo $i ?>" <?php if ($i==$prec_betaald){ ?> selected <?php } ?>><?php echo $i ?></option>
												<?php } ?>
											</optgroup>
											<optgroup label="andere percentages" title="andere percentages">
												<?php for($i=100;$i>0;$i-=1) { ?>
													<?php
														if (!$ffound) {
															if ($i==$prec_betaald) {
																$ffound = 2;
															} elseif (!$prec_betaald) {
																$ffound = 2;
															}
														}
													?>
													<option value="<?php echo $i ?>" <?php if ($i==$prec_betaald && $ffound != 1){ ?> selected <?php } ?>><?php echo $i ?></option>
												<?php } ?>
											</optgroup>
											<?php if ($ffound==0){ ?>
												<option value="<?php echo $prec_betaald ?>" selected><?php echo $prec_betaald ?></option>
											<?php } ?>
										</select>
									<?php } else { ?>
										<input type="hidden" name="prec_betaald" value="<?php echo $prec_betaald ?>">
										<?php echo $prec_betaald ?>%
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
							<!--
							<tr><td align="right"><span class="d">&nbsp;btw tonen</span></td><td background="img_td_bg.gif"><input type="checkbox" name="btw_tonen" class="checkbox" value="1" <?php if($btw_tonen==1){ ?>CHECKED<?php } ?> ></td></tr>
							-->
							<input type="hidden" name="btw_prec" value="19">



							<tr><td align="right" <?php echo td(0); ?> valign="top"><span class="d">&nbsp;begeleidende omschrijving</span></td><td background="img_td_bg.gif">

							<?php
								$output = new Layout_output();
								$output->addHiddenField("html", "");
								$output->addHiddenField("inhoud", "");
								$output->start_javascript();
									$output->addCode("
										function copyHtml() {
											sync_editor_contents();
											document.getElementById('html').value = document.getElementById('contents').value;
											document.getElementById('inhoud').value = document.getElementById('contents').value;
										}
									");
								$output->end_javascript();
								$editor = new Layout_editor();

								$html = str_replace("[producten hier]", "", $html);
								$html = preg_replace("/^<p>\&nbsp\;<\/p>/si", "", $html);

								if (!preg_match("/^<p[^>]*?>/si", $html))
									$html = sprintf("<p>%s&nbsp;</p>", $html);


								$output->addTextArea("contents", $html, array(
									"style" => "width: 700px; height: 400px;"
								));
								$output->addCode( $editor->generate_editor(2, $html) );
								echo $output->generate_output();
							?>

							<!--<br><a href="Javascript:plakTekst('[producten hier]<br><br>');"><span class="d">plak producten </span><img src="../img/knop_rechts.gif" align="absmiddle" border="0"></a>-->
							</td></tr>
							<tr><td colspan="2" align="right">
								<a href="javascript: setWaarde('pagina', ''); verzend();"><img src="../img/knop_links.gif" border="0"></a>&nbsp;
								<a href="javascript: copyHtml(); setWaarde('pagina', '<?php echo $pagina; ?>_db'); verzend();"><img src="../img/knop_ok.gif" border="0"></a>
							</td></tr>

							<script>
								function btw(nm){
									var b = document.velden.btw_prec.value;
									c = parseInt(b);

									var punt = false;
										if(b-c!=0) punt = true;
									if (punt)c+=0.5;
										c=c+nm;
									if(c>0 && c<100){
										document.velden.btw_prec.value = c;
									}
								}
							</script>

				<?php venster_footer(); ?>

				</td></tr></table>

				<input type="hidden" name="bedrijfsnaam" value="<?php echo $bedrijfsnaam; ?>">
				<?php
				if (!$producten_id){
					$producten_id="-1";
				}
				?>
				<input type="hidden" id="producten_id" name="producten_id" value="<?php echo $producten_id; ?>">
				<input type="hidden" name="factuur_nieuw" value="<?php echo $factuur_nieuw; ?>">

				<script>
					function plakTekst(tekst){
						/*
						oudHTML = editor.tbContentElement.FilterSourceCode(editor.tbContentElement.DOM.body.innerHTML);
						*/
						editor.MENU_FILE_SAVE();
						oudHTML = document.velden.html.value + tekst;
						document.getElementById('objEditor').src = '../htmlarea/editor.php?newHtml='+oudHTML;
					}
					updateBcards();

				</script>
<?php }

	//------------------------------------------------------------------------------------------/*
	//deze functie wordt gebruikt door de functie erna
	//neemt een string van producten en berekend het totaal bedrag
	function berekenProdTotaal($producten_id, $factuurPrec){
		//bereken rekening flow
		$q = sprintf("SELECT * FROM finance_producten_in_offertes WHERE link_id = %d", $producten_id);
		$result = sql_query($q);
		$totaal = 0;
		$totaalBtw = 0;
		while ($row = sql_fetch_assoc($result)){
			//$q2="SELECT * FROM producten WHERE id = ".$row["producten_id"]." ;";
			//$result2 = sql_query ($q2);
			//$row2 = sql_fetch_assoc($result2);
				$totaal+=(($row["prijs"]*$row["aantal"])/100)*$factuurPrec;
				$totaalBtw+=(((($row["prijs"]/100)*$row["btw"])*$row["aantal"])/100)*$factuurPrec;
		}
		return array($totaal, $totaalBtw);
	}

	//------------------------------------------------------------------------------------------
	function offertesInvoerenDb() {
		global $pagina, $id, $chr, $btw;
		global $naam, $debiteurnr, $titel, $datum, $html, $dag, $maand, $jaar, $producten_id, $uitvoerder, $statusid, $bedrijfsnaam, $prec_betaald, $factuurnr, $factuur_nieuw, $btw_tonen, $btw_prec, $statusnieuw, $font;
		global $project;

		if ($project["bcard"])
			$businesscard = $project["bcard"];

		$template_id = $_REQUEST["template_id"];

		//tel factuurnummer op (elke nieuwe factuur moet het nummer omhoog)
		if ($factuur_nieuw == true) {
			$sqlQuery = sprintf("UPDATE finance_teksten SET html = '%s' where id = 2", $factuurnr);
			sql_query ($sqlQuery);
		}
		$factuurnr    = (int)$factuurnr;
		$prec_betaald = (int)$prec_betaald;

		//wanneer het een factuur is, zet totaal(+btw-bedrag) bedrag als flow in akties
		if ($factuurnr > 0) {
			$h = berekenProdTotaal($producten_id, $prec_betaald);
			//reken te betalen bedrag uit
			//oude functie voor btw bugt - nu niet meer
			$totaal     = $h[0];
			$totaal_btw = $h[1];
			$totaal_flow = $totaal + $totaal_btw;

			//zien of factuur al in de db staat
			//$result = sql_query ("SELECT * FROM akties WHERE factuur_nr = ".$factuurnr." ;");
			$q = sprintf("select * from finance_omzet_akties where factuur_nr = '%s'", $factuurnr);
			$result = sql_query ($q);
			$timest = mktime(0,0,0,$maand,$dag,$jaar);
			$_count = sql_num_rows($result);
			if ($_count > 1) {
				$q = sprintf("delete from finance_omzet_akties where factuur_nr = '%s'", $factuurnr);
				sql_query($q);
				$_count = 0;
			}
			if ($_count == 0) {
				//bestaat nog niet
				$q = sprintf("insert into finance_omzet_akties (address_id, factuur_nr, rekeningflow,
					rekeningflow_ex, rekeningflow_btw, omschrijving, datum) values (%d, '%s', '%s', '%s', '%s', '%s', %d)",
					$debiteurnr, $factuurnr, $totaal_flow, $totaal, $totaal_btw, $titel, $timest);
			} else {
				$row = sql_fetch_assoc($result);
				$q = sprintf("update finance_omzet_akties set address_id = %d, factuur_nr = '%s',
					rekeningflow_ex = '%s', rekeningflow = '%s', rekeningflow_btw = '%s', omschrijving = '%s',
					datum = %d where id = %d",
					$debiteurnr, $factuurnr, $totaal, $totaal_flow, $totaal_btw, $titel, $timest, $row["id"]);
			}
			sql_query($q);
		}

		if ($debiteurnr) {
			/* note: address_id is debtor number here! */
			$address_data = new Address_data();
			$address_id_real = $address_data->getAddressIdByDebtor($debiteurnr);
			if ($address_id_real)
				$address_name    = $address_data->getAddressNameById($address_id_real);
			else
				$address_name    = "";

			$bedrijfsnaam    = addslashes($address_name);
		}
		$user_data = new User_data();
		switch ($pagina) {
			case "nieuw_db":
				$q = sprintf("insert into finance_offertes (template_id, address_id, titel, bedrijfsnaam,
					datum_%1\$d, producten_id_%1\$d, uitvoerder, html_%1\$d, factuur_nr_%1\$d, factuur_nr,
					btw_tonen, btw_prec, prec_betaald_%1\$d, status, font, fontsize, template_setting, bcard_id)", $statusid);
				$q.= sprintf(" values (%d, %d, '%s', '%s', %d, '%s', '%s', '%s', %d, %d, %d, '%s', %d, %d, '%s', %d, %d, %d)",
					$template_id, $debiteurnr, $titel, $bedrijfsnaam, $jaar.$maand.$dag, $producten_id,
					$user_data->getUsernameById($_SESSION["user_id"]), $html, $factuurnr, $factuurnr,
					$btw_tonen, $btw_prec, $prec_betaald, $statusnieuw, $font["font"], $font["fontsize"], $font["template_setting"], $businesscard);
				break;
			case "bewerk_db":
				$s = (int)$statusid;
				$q = sprintf("update finance_offertes set template_id = %d, address_id = %d, titel = '%s', bedrijfsnaam = '%s',
					datum_$s = %d, producten_id_$s = '%s', uitvoerder = '%s', html_$s = '%s', factuur_nr_$s = %d,
					factuur_nr = %d, btw_tonen = %d, btw_prec = '%s', prec_betaald_$s = %d, font = '%s', fontsize = %d, template_setting = %d, bcard_id = %d where id = %d",
					$template_id, $debiteurnr, $titel, $bedrijfsnaam, $jaar.$maand.$dag, $producten_id,
					$user_data->getUsernameById($_SESSION["user_id"]), $html, $factuurnr, $factuurnr,
					$btw_tonen, $btw_prec, $prec_betaald, $font["font"], $font["fontsize"], $font["template_setting"], $businesscard, $id);
				break;
		}
		sql_query($q);

		//Als het een nieuwe factuur is haal dan het id op
		if (!$id) {
			$id = sql_insert_id("finance_offertes");
		}
		//Werk boekingen bij
		//boekingenInvoerenDb($id, $statusid); // Boekingen bijwerken voor deze aktie
		?>
	<script type="text/javascript">document.location.href='<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/projecten.php?zoeken=<?php echo addslashes($titel) ?>';</script>
	<?php
	}

	//------------------------------------------------------------------------------------------
	function offertesBetaald(){
		global $pagina, $id, $chr;
		global $betaaldnieuw;

		$sqlQuery = "UPDATE offertes SET betaald_prec = $betaaldnieuw WHERE id = $id ;";

		sql_query ($sqlQuery);
		//echo($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	function offertesVerwijder(){ ?>

			<?php venster_header("offertes", "verwijder", array(), 0, -1); ?>
					<tr><td background="img_td_titel_bg.gif" colspan="15" height="1"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><span class="dT">Hiermee wordt de offerte verwijderd. Vergeet hierna niet de oplopende factuurnummering te herstellen.<br><br>Weet u het zeker?</span></td></tr></table></td></tr>
					<tr><td background="img_td_bg.gif" align="center"><a href="Javascript:history.go(-1);"><img src="../img/knop_links.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina', 'verwijder_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>
			<?php venster_footer(); ?>
<?php	}

	//------------------------------------------------------------------------------------------
	function offertesVerwijderDb(){
		global $id;

		$q = sprintf("select factuur_nr_2 from finance_offertes where definitief_2 = 0 and id = %d", $id);
		$res = sql_query($q);
		$nr = sql_result($res,0);

		$q = sprintf("delete from finance_omzet_akties where factuur_nr = %d and (bedrag_betaald = 0.00 or bedrag_betaald = 0 or bedrag_betaald is null)", $nr);
		sql_query($q);

		$sqlQuery = sprintf("DELETE FROM finance_offertes WHERE id = %d", $id);
		sql_query ($sqlQuery);

	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	function offertesStatus(){
		global $id, $statusid, $chr;

    $sqlQuery = sprintf("UPDATE finance_offertes SET status = %d WHERE id = %d",
    	$statusid, $id);
		sql_query ($sqlQuery);

	?><script language="Javascript">setWaarde('pagina', ''); verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	//maak een offerte definitief. daarna is deze niet meer te wijzigen
	function offerteDefinitief(){
		global $id;

		$q = "select * from finance_offertes where id = $id";
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$status = $row["status"];

		$sql = "update finance_offertes set definitief_$status = 1 where id = $id";
		sql_query($sql);

	  boekingenInvoerenDb($id, $status); // Werk boekingen bij voor deze aktie

	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}
	?>

</form>

<?php html_footer(); ?>
