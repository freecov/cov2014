<?php
	if($aktie=="toevoegen"){

		//lees eerst btw-tarief voor het product uit
		$result = sql_query ("SELECT prijs,btw_prec FROM producten WHERE id = ".$producten_id." ;");
		$row = sql_fetch_array($result);
		$btwP = $row["btw_prec"];
		$btwP = $btwP * 100;
		$prijs = $row["prijs"];

		#if($btwP=="") $btwP="19";
		$btwP = (int)$btwP;

		$q = "INSERT INTO producten_in_offertes SET producten_id=$producten_id, link_id=$link_id, omschrijving='', prijs='$prijs', aantal=1, btw='".$btwP."' ;";
		sql_query($q) or die($q.sql_error());
	} ?>

<?php if($aktie=="verwijderen"){
		$q = "DELETE FROM producten_in_offertes WHERE id = ".$producten_in_offertes_id." ;";
		sql_query($q) or die($q.sql_error());
	} ?>

<?php if($aktie=="bijwerken"){
		require("../email/functions_conversions.php");
		#echo($btwprec);
		$q = "UPDATE producten_in_offertes SET aantal = ".$aantal.", btw = ".$btwprec.", omschrijving=\"".str_replace("\"", "''", utf8_convert($omschrijving))."\" WHERE id = ".$producten_in_offertes_id." ;";
		sql_query($q) or die($q.sql_error());
	} ?>

<script language="Javascript">
	opener.stap(<?php echo $nrstap; ?>);
	window.close();
</script>

