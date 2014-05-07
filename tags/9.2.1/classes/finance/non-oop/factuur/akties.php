<?php
	require('emoe.inc.php');
	html_header();
	pageNav();

	switch ($pagina){
		case "": aktiesOverzicht(); break;
		case "nieuw": aktiesInvoeren(); break;
		case "bewerk": aktiesInvoeren(); break;
		case "nieuw_db": aktiesInvoerenDb(); break;
		case "bewerk_db": aktiesInvoerenDb(); break;
		case "verwijder": aktiesVerwijder(); break;
	}

	//------------------------------------------------------------------------------------------
	function aktiesOverzicht(){
		global $sort, $debiteur_nr, $linkCvd, $menu; ?>

			<?php
				$result = sql_query ("SELECT * FROM address WHERE debtor_nr = $debiteur_nr ;", $linkCvd);
				$row = sql_fetch_array($result);
				$naam = $row["companyname"];

				if ($sort == null) $sort = "datum";
				$totaal;
				//$result = sql_query ("SELECT * FROM akties WHERE debiteur_nr = $debiteur_nr ORDER BY $sort;");
        //23-09-02 steve
				$result = sql_query ("SELECT * FROM finance_omzet_akties WHERE address_id = $debiteur_nr ORDER BY $sort;"); ?>

				<?php venster_header("akties", $naam, $menu, 0, -1); ?>
					<tr><td align="right"><a href="Javascript:setWaarde('pagina','nieuw');verzend();"><img src="img/knop_nieuw.gif" border="0"></a></td></tr>
					<tr><td><?php tabel_header(0); ?>
						<tr><td <?php echo td(0) ?> height="21" width="100"><span class="dT"><a href="Javascript:setWaarde('sort','factuur_nr');verzend();"><img src="img/knop_sort.gif" align="right" border="0"></a>&nbsp;factuurnummer&nbsp;</span></td><td <?php echo td(0) ?>><span class="dT"><a href="Javascript:setWaarde('sort','omschrijving');verzend();"><img src="img/knop_sort.gif" align="right" border="0"></a>&nbsp;omschrijving&nbsp;</span></td><td <?php echo td(0) ?> width="80"><span class="dT"><a href="Javascript:setWaarde('sort','rekeningflow');verzend();"><img src="img/knop_sort.gif" align="right" border="0"></a><nobr>&nbsp;rekeningflow&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</nobr></span></td><td <?php echo td(0) ?> width="80"><span class="dT"><a href="Javascript:setWaarde('sort','datum');verzend();"><img src="img/knop_sort.gif" align="right" border="0"></a>&nbsp;datum&nbsp;</span></td><td <?php echo td(0) ?> width="1"><span class="dT">&nbsp;</span></td></tr>
						<?php	while ($row = sql_fetch_array($result)){ ?>
						<tr>
							<td <?php echo td(1) ?> valign="top"><span class="record"><?php echo $row["factuur_nr"]; ?>&nbsp;</span></td>
							<td <?php echo td(1) ?> valign="top"><span class="record"><?php echo str_replace("\n", "<br>", $row["omschrijving"]); ?>&nbsp;</span></td>
							<td <?php echo td(1) ?> valign="top"><span class="record">&nbsp;<?php echo $row["rekeningflow"]; ?>&nbsp;</span></td>
							<td <?php echo td(1) ?> valign="top"><span class="record"><nobr>&nbsp;<?php//convertDatum($row["datum"]);?><?php echo convertDatum(strftime("%Y%m%d",$row["datum"])); ?>&nbsp;</nobr></span></td>
							<td <?php echo td(1) ?> valign="top" width="35"><span class="record"><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id', '<?php echo $row["id"]; ?>');verzend();"><img src="img/knop_bewerk.gif" border="0"></a></span></td>
						</tr>
						<?php	$totaal+=$row["rekeningflow"];
							} ?>
						<?php if ($totaal!=null){ ?><tr><td colspan="2"></td><td <?php echo td(2) ?> valign="top"><span class="d">&nbsp;<?php echo $totaal; ?>&nbsp;</span></td><td colspan="2"></td></tr><?php } ?>
						<?php tabel_footer(); ?></td></tr>
				<?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function aktiesInvoeren(){
			global $pagina, $id, $debiteur_nr, $menu;

			if ($pagina == "bewerk"){
				$titel = "bewerk aktie";
				//$sqlQuery = "SELECT * FROM akties WHERE id = $id ;";
				//23-09-02 steve
        $sqlQuery = "SELECT * FROM finance_omzet_akties WHERE id = $id ;";
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_array($result);
					$factuur_nr = $row["factuur_nr"];
					$debiteur_nr = $row["debiteur_nr"];
					$omschrijving = $row["omschrijving"];
					$rekeningflow = $row["rekeningflow"];
					$rekeningflow_btw = $row["rekeningflow_btw"];
          //$datum = $row["datum"];
          //24-09-02 steve
          $datum = strftime("%Y%m%d",$row["datum"]);
					$grootboeknummer_id = $row["grootboeknummer_id"];
			}else{
				$titel = "nieuwe aktie";
					$rekeningflow = 0;
					$datum = date("Ymd");
			} ?>


				<?php venster_header("akties", $titel, $menu, 0, -1); ?>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="d">&nbsp;factuurnummer&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="factuur_nr" value="<?php echo $factuur_nr; ?>" style="width:60px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right" valign="top"><span class="d">&nbsp;omschrijving&nbsp;</span></td><td background="img_td_bg.gif"><textarea class="inputtext" name="omschrijving" style="width:252px;height:150px;"><?php echo $omschrijving; ?></textarea></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="d">&nbsp;rekeningflow&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="rekeningflow" value="<?php echo $rekeningflow; ?>" style="width:60px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="d">&nbsp;rekeningflow btw&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="rekeningflow_btw" value="<?php echo $rekeningflow_btw; ?>" style="width:60px;"></td></tr>
							<tr><td background="img_td_kolom_bg.gif" align="right"><span class="d">&nbsp;grootboeknummer&nbsp;</span></td><td background="img_td_bg.gif">
								<select name="grootboeknummer_id" class="inputselect">
								<?php $result = sql_query ("SELECT * FROM finance_grootboeknummers ORDER BY nr;");
										while ($row = sql_fetch_array($result)){ ?>
											<option value="<?php echo $row["id"]; ?>" <?php if ($grootboeknummer_id == $row["id"]){ ?> SELECTED <?php } ?>><?php echo $row["nr"]; ?> - <?php echo $row["titel"]; ?>
									<?php } ?>
								</select>
							</td></tr>
							<tr><td align="right"><span class="d">&nbsp;datum&nbsp;</span></td><td background="img_td_bg.gif">

									<table border="0">
							         <tr>
							               <td><select name="dag" class="inputselect">
							              <?php for ($i=1;$i<=31;$i++){
							               if (strlen($i) == 2){ ?>
							                  <option value="<?php  echo $i; ?>" <?php if (substr($datum, 6, 2) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
							               <?php }else{ ?>
							                  <option value="0<?php  echo $i; ?>" <?php if (substr($datum, 6, 2) == "0$i"){ ?>SELECTED<?php } ?> >0<?php echo $i; ?></option>
							               <?php } ?>
							              <?php } ?>
							            </select></td>
							            <td><form name="DatumForm">
							          <select name="maand" class="inputselect">
							              <?php for ($i=1;$i<=12;$i++){
							               if (strlen($i) == 2){ ?>
							                  <option value="<?php  echo $i; ?>" <?php if (substr($datum, 4, 2) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
							               <?php }else{ ?>
							                  <option value="0<?php  echo $i; ?>" <?php if (substr($datum, 4, 2) == "0$i"){ ?>SELECTED<?php } ?> >0<?php echo $i; ?></option>
							               <?php } ?>
							              <?php } ?>
							            </select></td>
							            <td><select name="jaar" class="inputselect">
							            <?php for ($i=2000;$i<2030;$i++){ ?>
							                <option value="<?php  echo $i; ?>" <?php if (substr($datum, 0, 4) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
							            <?php } ?></select>
							            </td>
							         </tr>
									</table>

							</td></tr>
							<tr><td  align="right" colspan="2"><a href="Javascript:setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="img/knop_ok.gif" border="0"></a></td></tr>
				<?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function aktiesInvoerenDb(){
		global $pagina, $id, $debiteur_nr;
		global $factuur_nr, $omschrijving, $rekeningflow, $rekeningflow_btw, $dag, $maand, $jaar,  $grootboeknummer_id;

		if ($pagina == "nieuw_db") $sqlQuery = "INSERT INTO ";
		if ($pagina == "bewerk_db") $sqlQuery = "UPDATE ";
		//$sqlQuery .= "akties SET ".
		//23-09-02 steve
    $timest = mktime(0,0,0,$maand,$dag,$jaar);
    $sqlQuery .= "finance_omzet_akties SET ".
			"factuur_nr = $factuur_nr, ".
			"omschrijving = '$omschrijving', ".
			"rekeningflow = $rekeningflow, ".
			"rekeningflow_btw = $rekeningflow_btw, ".
			"grootboeknummer_id = $grootboeknummer_id, ".
			"datum = $timest ";
		if ($pagina == "nieuw_db") $sqlQuery .= ", debiteur_nr = $debiteur_nr ";
		if ($pagina == "bewerk_db") $sqlQuery .= " WHERE id = $id ";
		$sqlQuery .= ";";

		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------
	function aktiesVerwijder(){
		global $id, $debiteur_nr;;

		//$sqlQuery = "DELETE FROM akties WHERE id = $id ;";
    //23-09-02 steve
		$sqlQuery = "DELETE FROM finance_omzet_akties WHERE id = $id ;";
		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}
?>

<?php html_footer(); ?>

