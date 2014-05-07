<?php require('emoe.inc.php'); ?>

<?php html_header(); ?>
<?php pageNav(); ?>

	<script language="Javascript">
		rvar="";
		function klaar(){
			parent.plakHtml(rvar);
		}

		//window.onload = function(){ window.focus(); }
	</script>

	<table border="0"><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>

		<?php venster_header("offertes", $stapomschr, array(), 0, -1); ?>

		<tr><td align="right" colspan="15" height="1"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td width="90%">&nbsp;</td><td width="1"></td><td width="1"><span class="kolom">&nbsp;</span></td><td width="1"><a href="Javascript: closepopup();"><img src="../img/knop_ok.gif" border="0"></a></td></tr></table></td></tr>
		<tr><td>

<?php
	switch($nrstap){
		case 6: ?>
			<script language="Javascript">
				function naarRechts(){
					if (document.velden.van.value!="") {
						document.location.href="<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/stapproduct.php?nrstap=<?php echo $nrstap ?>&aktie=toevoegen&link_id="+parent.document.velden.producten_id.value+"&producten_id="+document.velden.van.value;
					}else{
						alert("kies eerst een product om toe te voegen");
					}
				}

				function naarLinks(){
					if(document.velden.naar.value!=""){
						document.location.href="<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/stapproduct.php?nrstap=<?php echo $nrstap ?>&aktie=verwijderen&producten_in_offertes_id="+document.velden.naar.value;
					}else{
						alert("kies eerst een product om te verwijderen");
					}
				}

				function kiesProduct(){
						for (var i = 0; i<document.velden.naar.length; i++) {
							if (document.velden.naar.options[i].selected){
								document.velden.huidig_product.value=document.velden.naar.options[i].value;
							}
						}

					document.velden.omschrijving.value = omschrijving[document.velden.huidig_product.value];
					document.velden.aantal.value = aantal[document.velden.huidig_product.value];
					document.velden.btwprec.value = btw[document.velden.huidig_product.value];
				}

				function bijwerken(){
					if(document.velden.huidig_product.value!=""){
						document.location.href="<?php echo $GLOBALS["finance"]->include_dir_factuur ?>/stapproduct.php?nrstap=<?php echo $nrstap ?>&aktie=bijwerken&producten_in_offertes_id="+document.velden.huidig_product.value+"&aantal="+document.velden.aantal.value+"&btwprec="+document.velden.btwprec.value+"&omschrijving="+replace(document.velden.omschrijving.value, "&", "en");
					}else{
						alert("u hebt geen product gekozen en bewerkt");
					}
				}

				function btwP(nm){
					var b = document.velden.btwprec.value;
					c = parseInt(b);

					var punt = false;
						if(b-c!=0) punt = true;
					if (punt)c+=0.5;
						c=c+nm;
					if(c>0 && c<100){
						//document.velden.btwprec.value = c;
						obj = (document.velden.btwprec);

						if (!hasOptions(obj)) {
							return;
						}
						for (var i=0; i<obj.options.length; i++) {
							if (obj.options[i].value == c) {
								obj.options[i].selected = true;
							}else{
								obj.options[i].selected = false;
							}
						}
					}
				}
			</script>

			<span class="d">
			<?php
				$debiteur_nr = (int)$debiteur_nr;
				$q = sprintf("select * from finance_producten where categorie is null and (
					address_id = 0 or address_id = %d) order by titel", $debiteur_nr);
				$result = sql_query($q);
				?>

				<table border="0" cellpadding="3"><tr><td valign="top">
					<span class="d">beschikbare producten:<br></span>
					<select name="van" class="inputselect" size="30" style="width:230px;">
						<?php	while ($row = sql_fetch_array($result)){ ?>
								<option value="<?php echo $row["id"]; ?>"><?php echo $row["titel"]; ?>
						<?php	} ?>
					</select>
				</td><td valign="top"><br><br>
					<a href="Javascript:naarRechts();"><img src="../img/knop_rechts.gif" border="0" vspace="4"></a><br>
				</td><td>
			<?php
				//zien wat $producten_id is. als het nog leeg is (nieuwe offerte) dan moet er
				//een bedacht worden (hoogste in db +1)
				$producten_id = (int)$producten_id;
				if ($producten_id <= 0) {
					$qx = "select max(link_id)+1 from finance_producten_in_offertes";
					$result = sql_query($qx);
					$producten_id = sql_result($result,0);
				}
				$q = sprintf("select * from finance_producten_in_offertes where link_id = %d", $producten_id);
				$result = sql_query($q);
			?>
					<span class="d">gekozen producten:<br></span>
					<select name="naar" class="inputselect" size="18" style="width:250px;" onChange="kiesProduct();">
						<?php
							while ($row = sql_fetch_array($result)) {
								$q = sprintf("select * from finance_producten where id = %d", $row["producten_id"]);
								$result2 = sql_query($q);
								$row2 = sql_fetch_array($result2);
								$laatsteID = $row["id"];
								?>
								<option value="<?php echo $row["id"]; ?>"><?php echo $row2["titel"]; ?></option>
								<?php
							}
						?>
					</select>

					<script language="Javascript">
						//array met eigenschappen van produkten
						omschrijving = new Array;
						aantal = new Array;
						btw = new Array;
						<?php
							$q = sprintf("select * from finance_producten_in_offertes where link_id = %d", $producten_id);
							$result = sql_query($q);
							while ($row = sql_fetch_array($result)){
						?>
							omschrijving[<?php echo $row["id"] ?>] = "<?php echo $row["omschrijving"] ?>";
							aantal[<?php echo $row["id"] ?>] = "<?php echo $row["aantal"] ?>";
							btw[<?php echo $row["id"] ?>] = <?php echo ($row["btw"]!="")?$row["btw"]:"19"; ?>;
						<?php	} ?>
					</script>
					<span class="d"><br>
					<br>
					aantal:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;btw:<br>
					<input class="inputtext" value="" name="aantal" style="width:40px;">&nbsp;&nbsp;&nbsp;

					<select name="btwprec" class="inputselect">
						<option value="0">geen</option>
						<option value="6">6</option>
						<option value="19">19</option>
					</select>
					<!--
					<input class="inputtext" value="" name="btwprec" style="width:40px;" READONLY>&nbsp;<img src="../img/knop_omlaag.gif" border="0" onClick="btwP(-0.5);">&nbsp;<img src="../img/knop_omh.gif" border="0" onClick="btwP(0.5);">&nbsp;%
					-->

					<br><br>extra omschrijving:<br></span>
					<textarea class="inputtextarea" name="omschrijving" style="width:250px;height:80px;"></textarea>
					<input type="hidden" value="" name="huidig_product">
					<br>
					<a href="Javascript:naarLinks();"><img src="../img/knop_verwijder.gif" border="0" vspace="5"></a>
					<a href="Javascript:bijwerken();"><img src="../img/knop_opslaan.gif" border="0" vspace="5"></a>
				</td></tr></table>
			</span>
			<script language="Javascript">
				parent.document.velden.producten_id.value=<?php echo $producten_id ?>;
			</script>

			<?php if($laatsteID!=""){ ?>
					<script language="Javascript">
						lID = <?php echo $laatsteID ?>;
						for (var i = 0; i<document.velden.naar.length; i++) {
							if (document.velden.naar.options[i].value == lID){
								document.velden.naar.options[i].selected = true;
								kiesProduct();
							}
						}
					</script>
			<?php } ?>

	<?php	break;
	}
?>

</td></tr>
<?php venster_footer(); ?>
</td></tr></table>

<?php html_footer(); ?>
