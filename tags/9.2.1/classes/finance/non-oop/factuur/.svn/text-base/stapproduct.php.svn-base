<?php
	require("emoe.inc.php");

	if ($_SESSION["user_id"]) {
		switch ($aktie) {
			case "toevoegen":
				//lees eerst btw-tarief voor het product uit
				$q = sprintf("select prijs, btw_prec from finance_producten where id = %d", $producten_id);
				$result = sql_query($q);
				$row = sql_fetch_assoc($result);

				$btwP = $row["btw_prec"];
				$btwP = $btwP * 100;
				$prijs = $row["prijs"];

				#if($btwP=="") $btwP="19";
				$btwP = (int)$btwP;

				$q = sprintf("insert into finance_producten_in_offertes (producten_id, link_id, omschrijving, prijs,
					aantal, btw) values (%d, %d, '', '%s', %d, %d)",
					$producten_id,
					$link_id,
					$prijs,
					1,
					$btwP
				);
				sql_query($q);
				#echo $q;

				break;
			case "verwijderen":
				$q = sprintf("DELETE FROM finance_producten_in_offertes WHERE id = %d", $producten_in_offertes_id);
				sql_query($q);
				break;
			case "bijwerken":
				#echo($btwprec);
				$q = sprintf("update finance_producten_in_offertes set aantal = %d, btw = %d,
					omschrijving = '%s' where id = %d",
					$aantal, $btwprec,
					addslashes(str_replace("\"", "''", $omschrijving)),
					$producten_in_offertes_id
				);
				sql_query($q);
				break;
		}
	}
?>
<script language="Javascript">
	//parent.popupProducten();
	document.location.href = '<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/stap.php?nrstap=6&stapomschr=producten&verbergIface=true&debiteur_nr=' + parent.document.getElementById('deb_id').value + '&producten_id=' + parent.document.getElementById('producten_id').value;
</script>
