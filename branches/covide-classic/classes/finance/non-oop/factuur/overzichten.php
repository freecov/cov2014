<?php require('emoe.inc.php'); ?>
<?php html_header(); ?>
<?php pageNav();?>

<?php 
// Kijk of gebruiker toegang heeft
		$result = sql_query ("SELECT xs_omzetmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_array ($result);
		if ($row["xs_omzetmanage"]!=1) {
			echo "Geen toegang!!!";
			exit();
		}

	switch ($pagina){
		case "": tekstenOverzicht(); break;
	}

	//------------------------------------------------------------------------------------------
	function tekstenOverzicht(){ 
		global $menu; ?>

			<?php venster_header("overzichten", "", $menu, 0, -1); ?>
						<tr>
							<td background="img_td_bg.gif" width="90%"><span class="d">&nbsp;lopende offertes&nbsp;</span></td>
							<td background="img_td_bg.gif" width="26" align="center"><span class="d"><a href="Javascript:setFormAktie('factuur/projecten.php');setWaarde('overzicht','offertes');setWaarde('pagina','');verzend();"><img src="img/knop_rechts.gif" border="0"></a></span></td>
						</tr>
						<tr>
							<td background="img_td_bg.gif" width="90%"><span class="d">&nbsp;lopende opdrachten&nbsp;</span></td>
							<td background="img_td_bg.gif" width="26" align="center"><span class="d"><a href="Javascript:setFormAktie('factuur/projecten.php');setWaarde('overzicht','opdrachten');setWaarde('pagina','');verzend();"><img src="img/knop_rechts.gif" border="0"></a></span></td>
						</tr>
						<tr>
							<td background="img_td_bg.gif" width="90%"><span class="d">&nbsp;lopende facturen (1)&nbsp;</span></td>
							<td background="img_td_bg.gif" width="26" align="center"><span class="d"><a href="Javascript:setFormAktie('factuur/projecten.php');setWaarde('overzicht','facturen1');setWaarde('pagina','');verzend();"><img src="img/knop_rechts.gif" border="0"></a></span></td>
						</tr>
						<tr>
							<td background="img_td_bg.gif" width="90%"><span class="d">&nbsp;lopende facturen (2)&nbsp;</span></td>
							<td background="img_td_bg.gif" width="26" align="center"><span class="d"><a href="Javascript:setFormAktie('factuur/projecten.php');setWaarde('overzicht','facturen2');setWaarde('pagina','');verzend();"><img src="img/knop_rechts.gif" border="0"></a></span></td>
						</tr>
			<?php venster_footer(); ?>
<?php } ?>	

<?php html_footer(); ?>
