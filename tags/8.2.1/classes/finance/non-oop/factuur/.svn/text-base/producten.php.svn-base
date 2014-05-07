<?php require('emoe.inc.php'); ?>
<?php html_header(); ?>
<?php pageNav(); ?>
<?php
	switch ($pagina){
		case "": productenOverzicht(); break;
		case "nieuw": productenInvoeren(); break;
		case "bewerk": productenInvoeren(); break;
		case "nieuw_db": productenInvoerenDb(); break;
		case "bewerk_db": productenInvoerenDb(); break;
		case "verwijder": productenVerwijder(); break;
		case "verwijder_db": productenVerwijderDb(); break;
	}

	//------------------------------------------------------------------------------------------
	function productenOverzicht(){
		global $sort, $debiteur_nr, $menu;

			if (!$sort)
				$sort = "titel";

			if (!$debiteur_nr)
				$q = sprintf("SELECT * FROM finance_producten WHERE categorie is null and address_id = 0 ORDER BY %s;", $sort);
			else
				$q = sprintf("SELECT * FROM finance_producten WHERE categorie is null and address_id = %d ORDER BY %s", $debiteur_nr, $sort);

			$result = sql_query($q);
			?>
			<?php venster_header("producten", "", $menu, 0, -1); ?>
				<tr><td colspan="2" align="right"><a href="Javascript:setWaarde('pagina','nieuw');verzend();"><img src="../img/knop_nieuw.gif" border="0"></a></td></tr>
				<tr><td><?php tabel_header(0); ?>
						<tr><td height="21" <?php echo td(0) ?>><span class="dT">&nbsp;korte omschrijving&nbsp;</span></td><td width="29" <?php echo td(0) ?>><span class="kolom">&nbsp;</span></td></tr>
						<?php	while ($row = sql_fetch_assoc($result)){ ?>
						<tr>
							<td width="90%" <?php echo td(1) ?>><span class="record"><nobr>&nbsp;<?php echo substr($row["titel"], 0, 60); ?><?php if(strlen($row["titel"])>60) echo("..."); ?>&nbsp;</nobr></span></td>
							<td width="35" <?php echo td(1) ?>><span class="record"><nobr><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a>&nbsp;
							<a href="Javascript:setWaarde('pagina','verwijder');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_verwijder.gif" border="0"></a>
							</span></td>
						</tr>
						<?php	} ?>
						<?php tabel_footer(); ?></td></tr><?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function productenInvoeren(){
			global $pagina, $id, $debiteur_nr, $menu;

			if ($pagina == "bewerk"){
				$titelp = "bewerk ";
				$sqlQuery = sprintf("SELECT * FROM finance_producten WHERE id = %d", $id);
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_assoc($result);
					$titel = $row["titel"];
					$prijs = $row["prijs"];
					$btw_prec = $row["btw_prec"];
					$prijsperjaar = $row["prijsperjaar"];
					$categorie = $row["categorie"];
					$html = $row["html"];
					$grootboeknummer_id = $row["grootboeknummer_id"];
			}else{
				$titelp = "nieuw ";
			} ?>

			<form name="eenform" method="post" action="?">
				<?php venster_header("soorten producten", $titelp, $menu, 0, -1); ?>
							<tr><td align="right" valign="top"><span class="d">&nbsp;korte omschrijving&nbsp;</span></td><td background="img_td_bg.gif"><textarea type="text" class="inputtextarea" name="titel" style="width:201px;height:70px;"><?php echo $titel; ?></textarea></td></tr>
							<tr><td align="right"><span class="d">&nbsp;prijs in euro's&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="prijs" value="<?php echo $prijs; ?>" style="width:62px;"></td></tr>
							<tr><td align="right"><span class="d">&nbsp;btw percentage&nbsp;</span></td><td background="img_td_bg.gif">
									<?php $precTarief = array("0.0", "0.06", "0.19"); ?>
									<select name="btw_prec" class="inputselect">
										<?php foreach($precTarief as $tarief){ ?>
												<option value="<?php echo $tarief ?>" <?php if($btw_prec == $tarief){ ?>SELECTED<?php } ?> ><?php echo $tarief*100 ?> %
										<?php	} ?>
									</select>
								</td></tr>
							<!--<tr><td align="right"><span class="d">&nbsp;prijs per jaar&nbsp;</span></td><td background="img_td_bg.gif"><input type="checkbox" value="1" name="prijsperjaar" <?php if ($prijsperjaar){ ?> CHECKED <?php } ?> ></td></tr>-->
							<!--<tr><td  align="right"><span class="d">&nbsp;categorie&nbsp;</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="categorie" value="<?php echo $categorie; ?>" style="width:201px;"></td></tr>-->

							<tr><td align="right" valign="top"><span class="d">&nbsp;omschrijving&nbsp;</span></td><td background="img_td_bg.gif">
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

									$output->addTextArea("contents", $html, array(
										"style" => "width: 700px; height: 400px;"
									));
									$output->addCode( $editor->generate_editor("", $html) );
									echo $output->generate_output();
								?>
							</td></tr>

							<?php if ($GLOBALS["covide"]->license["has_finance"]) { ?>
							<tr><td align="right"><span class="d">&nbsp;grootboeknummer&nbsp;</span></td><td background="img_td_bg.gif">
								<select name="grootboeknummer_id" class="inputselect">
								<?php $result = sql_query ("SELECT * FROM finance_grootboeknummers ORDER BY nr;");
										while ($row = sql_fetch_array($result)){ ?>
											<option value="<?php echo $row["id"]; ?>" <?php if ($grootboeknummer_id == $row["id"]){ ?> SELECTED <?php } ?>><?php echo $row["nr"]; ?> - <?php echo $row["titel"]; ?>
									<?php } ?>
								</select>
							</td></tr>
							<?php } else { ?>
								<input type="hidden" name="grootboeknummer" value="0">
							<?php } ?>


							<tr><td align="right" colspan="2"><a href="Javascript:copyHtml(); setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>
				<?php venster_footer(); ?>
			</form>
<?php }

	//------------------------------------------------------------------------------------------
	function productenInvoerenDb(){
		global $pagina, $id;
		global $titel, $categorie, $html, $prijs, $prijsperjaar, $grootboeknummer_id, $debiteur_nr, $btw_prec;

		if ($prijsperjaar == "1"){$prijsperjaar = 1;}else{$prijsperjaar = 0;}

		$prijs = str_replace(",", ".", $prijs);

		if ($pagina == "nieuw_db") {
			$q = sprintf("insert into finance_producten (titel, html, prijs, btw_prec,
				address_id, grootboeknummer_id) values ('%s', '%s', '%s', '%s', %d, %d)",
				$titel, $html, $prijs, $btw_prec, $debiteur_nr, $grootboeknummer_id);
		} else {
			$q = sprintf("update finance_producten set titel = '%s', html = '%s',
				prijs = '%s', btw_prec = '%s', address_id = %d, grootboeknummer_id = %d
				where id = %d",
				$titel, $html, $prijs, $btw_prec, $debiteur_nr, $grootboeknummer_id, $id);
		}
		sql_query ($q);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}

	//------------------------------------------------------------------------------------------

	function productenVerwijder(){
		global $menu; ?>

			<?php venster_header("producten", "verwijder", array(), 0, -1); ?>
					<tr><td background="img_td_titel_bg.gif" colspan="15" height="1"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td><span class="titel">weet u het zeker?</span></td></tr></table></td></tr>
					<tr><td background="img_td_bg.gif" align="center"><a href="Javascript:history.go(-1);"><img src="../img/knop_links.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina', 'verwijder_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>
			<?php venster_footer(); ?>
<?php	}


	//------------------------------------------------------------------------------------------
	function productenVerwijderDb(){
		global $id;

		$q = sprintf("select count(*) from finance_producten_in_offertes where producten_id = %d", $id);
		$res = sql_query($q);
		$aantal = sql_result($res, 0);

		if ($aantal == 0) {
			$sqlQuery = sprintf("DELETE FROM finance_producten WHERE id = %d", $id);
			sql_query ($sqlQuery);
			?>
			<script language="Javascript">
				alert('Het product is uit de lijst verwijderd');
				setWaarde('pagina', '');verzend();
			</script>
			<?php
		} else {
			$sqlQuery = sprintf("update finance_producten set categorie = '-1' WHERE id = %d", $id);
			sql_query ($sqlQuery);
			?>
			<script language="Javascript">
				alert('Het product is uit de lijst gehaald');
				setWaarde('pagina', '');verzend();
			</script>
			<?php
		}
	}
?>

<?php html_footer(); ?>
