<?php

	function local_venster_header($title = "") {
		?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%" ><tr>
			<td colspan="2" align="left">
				<table border="0" cellspacing="0" cellpadding="0"><tr>
					<td valign="bottom" align="left">
						<span class="onderdeel"><?php echo $title ?></span>
					</td>
					<td valign="top" align="left"><span class="titel">&nbsp;</span></td>
				</tr></table>
			</td>
		</tr><tr>
			<td valign="top" align="left">
				<table class="dlg1" cellpadding="1" cellspacing="0" border="0" width='100%'><tr>
					<td>
						<table border="0" cellspacing="0" cellpadding="0" width='100%'><tr>
							<td align="left">
								<table class="dlg2" cellpadding="5" cellspacing="0" border="0" width='100%' ><tr>
									<td>
		<?php
	}

	function local_venster_footer() {
		?>
									</td>
								</tr></table>
							</td>
							<td class="dlgR" valign="top" width="1"><img src="themes/0/dialoog_r.gif"></td>
							<td valign="top" class="dlgR2" rowspan="2" width="1"><img src="themes/0/dialoog_r2.gif"></td>
						</tr><tr>
							<td class="dlgO" colspan="3"> <img src="themes/0/dialoog_o.gif"></td>
						</tr></table>
					</td>
				</tr></table>
			</td>
		</tr></table>
		<?php
	}

	function local_tabel_header($width = "100%") {
		?>
		<table style="text-align:left;" class="dTd0" cellpadding="1" cellspacing="0" width='100%' border="0"><tr>
			<td>
				<table cellpadding="0" border="0" cellspacing="0" width='100%'><tr>
					<td valign="top">
						<table border="0" bgcolor="#FFFFFF" cellpadding="3" cellspacing="1" width='100%' >
		<?php
	}

	function local_tabel_footer() {
		?>
						</table>
					</td>
				</tr></table>
			</td>
		</tr></table>
		<?php
	}
	require("inc_common.php");
	?>
	<html>
	<head>
	<?php
		ob_clean();
		header("Content-type: text/html; charset=iso-8859-15");
	?>
	<META http-equiv="Content-type" content="text/html; charset=iso-8859-15" />

	<style type="text/css">
		td.dTd1, table.dTd1 {font:11px Arial,Verdana,Tahoma,Helvetica;color:#000000; background-image: url(themes/0/tabel_bg_1.gif);}
		td.dTd2, table.dTd2 {font:11px Arial,Verdana,Tahoma,Helvetica;color:#000000; background-image: url(themes/0/tabel_bg_2.gif);}
		table.dlg1 {font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/dialoog_bg_1.gif)}
		table.dlg2 {font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/dialoog_bg_2.gif)}
		table.dTd0 {font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/tabel_bg_1.gif)}
		 td.dlgR{font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/dialoog_r.gif)}
		td.dlgR2{font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/dialoog_r2.gif)}
		td.dlgO{font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/dialoog_o.gif)}
		body.bBg{font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/bg.gif)}
		td.menuLnk {font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/menu_link_m.gif)}
		td.menuLnkNav {font:11px Arial,Verdana,Tahoma,Helvetica;background-image: url(themes/0/menu_link_m_nav.gif)}
		body,html {font:11px Arial,Verdana,Tahoma,Helvetica;height:100%;}
		span.onderdeel {font:14pt Tahoma,Arial,Verdana,Helvetica;color:#666666;}
		span.titel {font:9pt Tahoma,Arial,Verdana,Helvetica;color:#333333;}
		span.d {font:8pt Tahoma,Arial,Verdana,Helvetica;color:#666666;}
		span.dT {font:8pt Tahoma,Arial,Verdana,Helvetica;color:#666666;font-weight:bold;}
		td.dTd {font:8pt Tahoma,Arial,Verdana,Helvetica;color:#666666;}
		span.menu {font:11px Tahoma,Arial,Verdana,Helvetica;color:#333333;}
		span.datum {font:18px Tahoma,Arial,Verdana,Helvetica;color:#666666;font-weight:bold;}
		span.gebruiker {font:8pt Tahoma,Arial,Verdana,Helvetica;color:#888888;}
		.inputtext { background-color: #FFFFFF; background-image : url("themes/themes/0/inputtext_bg.gif"); font: 8pt Arial,Verdana,Tahoma,Helvetica; border: 1px solid #AAAAAA; padding: 3px; }
		.inputtextarea { background-color: #FFFFFF; background-image : url("themes/0/inputtextarea_bg.gif"); font: 8pt Tahoma,Arial,Verdana,Helvetica; border: 1px solid #AAAAAA; padding: 3px;}
		.inputselect { background-color: #FFFFFF; font: 8pt Tahoma,Helvetica,Arial,Verdana;}
		.inputcheckbox { background-color: #FFFFFF;background-image : url("themes/0/inputtext_bg.gif"); border: 1px solid #D67E6F;}

		a:link { text-decoration:none;color:rgb(120,120,33); }
		a:visited { text-decoration:none;color:rgb(120,120,33); }
		a:hover { text-decoration:underline;color:rgb(190,190,67); }
	</style>
	</head>
	<body>
	<?php
	$klant_id = $_REQUEST["klant_id"];
	$sql = sprintf("SELECT debtor_nr FROM address WHERE id=%d", $klant_id);
	$res = sql_query($sql);
	$row = sql_fetch_assoc($res);
	$debiteur_nr = $row["debtor_nr"];
	#echo $debiteur_nr;
	$q = "select * from overige_posten where debiteur = ".$_REQUEST["klant_id"]." order by datum desc";
	$res = sql_query($q);
	if (sql_num_rows($res)>0){
		local_venster_header("speciale posten");
		?>




											<tr><td>
											<?php local_tabel_header("100%"); ?>
												<tr>
													<td <?phptd(0)?> colspan="2"><span class="dT"><?php echo gettext("grootboekrekeningen") ?></span></td>
													<td <?phptd(0)?>><span class="dT"><?php echo gettext("omschrijving") ?></span></td>
													<td <?phptd(0)?> align="left"><span class="dT"><?php echo gettext("datum") ?></span></td>
													<td <?phptd(0)?> align="right"><span class="dT"><?php echo gettext("bedrag") ?></span></td>
												</tr>
											<?php
											$grootboek["nr"] = array();
											$grootboek["name"] = array();
											$q = "select * from grootboeknummers";
											$res2 = sql_query($q);
											while ($row2 = sql_fetch_array($res2)){
												$grootboek["nr"][$row2["id"]] = $row2["nr"];
												$grootboek["name"][$row2["id"]] = $row2["titel"];
											}

											function shortView($id){
												global $grootboek;
												$code = $grootboek["nr"][$id]." - ". $grootboek["name"][$id];
												return $code;
											}


											while ($row = sql_fetch_array($res)){
												?>
													<tr>
																<td <?phptd(1)?>><span class="d"><?php echo gettext("van") ?>: <?php echo shortView($row["grootboek_id"]) ?></span></td>
																<td <?phptd(1)?>><span class="d"><?php echo gettext("naar") ?>: <?php echo shortView($row["tegenrekening"]) ?></span></td>
																<td <?phptd(1)?> align="left"><span class="d"><?php echo $row["omschrijving"] ?></span></td>
																<td <?phptd(1)?> align="left"><span class="d"><?php echo strftime("%d-%m-%Y",$row["datum"]) ?></span></td>
																<td <?phptd(1)?> align="right"><span class="d"><?php echo number_format($row["bedrag"],2) ?></span></td>
													</tr>
												<?php
											}
											?>

		<?php local_tabel_footer();?>
		</td></tr>
		<?php local_venster_footer();?>
		</td></tr>
		<?php
	}

	$q = "select is_supplier from address where id = $klant_id";
	$res = sql_query($q);
	$row = sql_fetch_array($res);

	if ($row["is_supplier"]!=0){
	?>
		<tr valign="top"><td colspan="2" width="100%">
		<?php local_venster_header(gettext("inkopen"),"",Array(),"100%",-1); ?>
		<tr><td>
		<?php local_tabel_header("100%"); ?>
			<tr>
				<td <?phptd(0)?>><span class="dT"><?php echo gettext("boekstuknr") ?></span></td>
				<td <?phptd(0)?>><span class="dT"><?php echo gettext("omschrijving") ?></span></td>
				<td <?phptd(0)?>><span class="dT"><?php echo gettext("datum") ?></span></td>
				<td <?phptd(0)?> align="right"><span class="dT"><?php echo gettext("bedrag") ?></span></td>
				<td <?phptd(0)?> align="center"><span class="dT"><?php echo gettext("betaald") ?></span></td>
			</tr>

			<?php
				$q = "select * from inkopen where leverancier_nr = $klant_id and datum > ". mktime(0,0,0,1,1,2002) . " order by datum desc";
				$res = sql_query($q);
				while ($row = sql_fetch_array($res)){
					?>
						<tr>
							<td <?phptd(1)?>><span class="d">
								<a href="finance/inkoop.php?action=toondetail&id=<?php echo $row["id"] ?>" target="_blank"><?php echo $row["boekstuknr"] ?></a>
							</span></td>
							<td <?phptd(1)?>><span class="d"><?php echo $row["descr"] ?></span></td>
							<td <?phptd(1)?>><span class="d"><?php echo strftime("%d/%m/%Y",$row["datum"]) ?></span></td>
							<td <?phptd(1)?> align="right"><span class="d"><?php echo $row["bedrag_inc"] ?></span></td>
							<td <?phptd(1)?> align="center"><span class="d">
								<?php if ($row['bedrag_inc']==$row['betaald']){ ?>
									<img src="../img/f_ja.gif">
								<?php }else{ ?>
									<img src="../img/f_nee.gif">
								<?php } ?>
							</span></td>
						</tr>
					<?php
				}
			?>
		<?php local_tabel_footer();?>
		</td></tr>
		<?php venster_footer();?>
		</td></tr>
		<?php
	}
	?>
	</table>
				<script>
					if (parent.mail_resize_frame) {
						parent.mail_resize_frame();
					}
				</script>
			<?php
			echo "</body></html>";
?>
