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

?>

<?php 
	switch ($pagina){
		case "": bedrijvenOverzicht(); break;
		case "nieuw": bedrijvenInvoeren(); break;
		case "bewerk": bedrijvenInvoeren(); break;
		case "nieuw_db": bedrijvenInvoerenDb(); break;
		case "bewerk_db": bedrijvenInvoerenDb(); break;
		case "verwijder": bedrijvenVerwijder(); break;
	}

	//------------------------------------------------------------------------------------------
	function bedrijvenOverzicht(){
		global $sort, $menu;
		
			if ($sort == null) $sort = "omschrijving";
			$result = sql_query ("SELECT * FROM soortbedrijf ORDER BY $sort;"); ?>
			
			<?php venster_header("soorten bedrijven", "", $menu, 0, -1); ?>
						<tr><td align="right"><a href="Javascript:setWaarde('pagina','nieuw');verzend();"><img src="../img/knop_nieuw.gif" border="0"></a></td></tr>
						<tr><td><?php tabel_header(0); ?>
						<tr><td  <?php echo td(0) ?> ><span class="kolom"><span class="dT">omschrijving&nbsp;</span></td><td <?php echo td(0) ?> ><span class="dT">&nbsp;</span></td></tr>
						<?php	while ($row = sql_fetch_array($result)){ ?>
						<tr>
							<td width="90%" <?php echo td(1) ?> ><span class="d">&nbsp;<?php echo $row["omschrijving"]; ?>&nbsp;</span></td>
							<td  width="35" <?php echo td(1) ?> ><span class="d"><nobr><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a>&nbsp;<a href="Javascript:setWaarde('pagina','verwijder');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_verwijder.gif" border="0"></a></span></td>
						</tr>
						<?php	} ?>
						<?php tabel_footer(); ?></td></tr><?php venster_footer(); ?>
<?php }

	//------------------------------------------------------------------------------------------
	function bedrijvenInvoeren(){
			global $pagina, $id, $aantalwerknemersOmschrijving, $menu;
			
			if ($pagina == "bewerk"){
				$titel = "bewerk soort bedrijf";
				$sqlQuery = "SELECT * FROM soortbedrijf WHERE id = $id ;";
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_array($result);
					$omschrijving = $row["omschrijving"];
			}else{
				$titel = "nieuw soort bedrijf";
			} ?>
			
			<form name="eenform" method="post" action="?">

			<?php venster_header("soorten bedrijven", $titel, $menu, 0, -1); ?>
				<tr><td align="right"><span class="d">&nbsp;omschrijving</span></td><td background="img_td_bg.gif"><input type="text" class="inputtext" name="omschrijving" value="<?php echo $omschrijving; ?>"></td></tr>
				<tr><td colspan="2" align="right"><a href="Javascript:setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>
			<?php venster_footer(); ?>

			</form>
<?php }

	//------------------------------------------------------------------------------------------
	function bedrijvenInvoerenDb(){
		global $pagina, $id;
		global $omschrijving;

		if ($pagina == "nieuw_db") $sqlQuery = "INSERT INTO ";
		if ($pagina == "bewerk_db") $sqlQuery = "UPDATE ";
		$sqlQuery .= "soortbedrijf SET omschrijving = '$omschrijving' ";
		if ($pagina == "bewerk_db") $sqlQuery .= " WHERE id = $id ";
		$sqlQuery .= ";";
		
		sql_query ($sqlQuery); 
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php	
	}

	//------------------------------------------------------------------------------------------
	function bedrijvenVerwijder(){
		global $id;
		
		$sqlQuery = "DELETE FROM soortbedrijf WHERE id = $id ;";
		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php	
	}
?>	

<?php html_footer(); ?>
