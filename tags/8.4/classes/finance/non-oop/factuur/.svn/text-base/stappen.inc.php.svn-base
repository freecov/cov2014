<?php $stappen = array("datum", "referentie", "bedrijfsnaam", "frames", "voorwaarden", "producten", "namens"); ?>

<?php venster_header("projecten", "stappen", array(), 0, -1); ?>
			<tr><td height="1" align=""><span class="titel">stappen</span></td><td align="right" colspan="2"><a href="Javascript:stap();"><img src="../img/knop_rechts.gif" border="0"></a></td></tr>
			<?php $i = 1;
				foreach ($stappen as $stap){ ?>
				<tr><td colspan="2"><nobr><a href="Javascript:stap(<?php echo $i; ?>);"><img src="../img/knop_rechts.gif" align="absmiddle" border="0"><span class="d">&nbsp;<?php echo $stap; ?></span></nobr></a></nobr></td><td align="right" id="tdstap<?php echo $i; ?>"><nobr><span class="d"><b><?php echo $i++; ?></b></span></nobr></td></tr>
			<?php } ?>
<?php venster_footer(""); ?>

<script>
	var aantalStappen = <?php echo $i; ?>;
	var huidigeStap = 0;
	var vorigeStap = 0;

	stappen = new Array;
	<?php $i = 1;
		foreach ($stappen as $stap){ ?>
			stappen[<?php echo $i++; ?>] = '<?php echo $stap ?>';
	<?php } ?>

	function stap(nrStap){
		if (!document.velden.debiteurnr.value && nrStap > 4){
			alert('nog geen debiteur/bedrijfsnaam gekozen!');
		}else{


			if(nrStap==null && huidigeStap>=aantalStappen-1) return;

				vorigeStap = huidigeStap;
				if (nrStap!=null){huidigeStap = nrStap;}else{huidigeStap++};
					/*
					if (vorigeStap!=0){
						eval("tdstap"+vorigeStap+".background='img_td_titel_bg.gif';");
					}
					eval("tdstap"+huidigeStap+".background='';");
					*/

				//open de popup
				//var velden;
				//velden = (document.velden);

				popUpStap("stap.php?nrstap="+huidigeStap+"&stapomschr="+stappen[huidigeStap]+"&debiteur_nr="+document.velden.debiteurnr.value+"&producten_id="+document.velden.producten_id.value+"&theme=<?php echo $theme ?>&verbergIface=true&s=<?php echo $s ?>&statusnieuw=<?php echo $statusnieuw ?>", 601, 401, 50, 50);

			}
	}

	function plakHtml(html){

		editor.MENU_FILE_SAVE();
		oudHTML = document.velden.html.value + html;


		document.getElementById('objEditor').src = '../htmlarea/editor.php?newHtml='+oudHTML;


	}

</script>
