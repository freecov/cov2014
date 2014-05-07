<?php
	require('../inc_common.php');

	//afsluitingen globale zaken
	$xjaar[]=0;
	$q = "select * from finance_jaar_afsluitingen order by jaar";
	$resx = sql_query($q);
	while ($rowx=sql_fetch_array($resx)){
		$xjaar[]=$rowx[jaar];
	}

	function dicht(){
		?><img src="../img/knop_delen_stop.gif" style="cursor:help" alt="jaar is afgesloten" title="jaar is afgesloten"><?php
	}

	// menu ---------------------------------------------------------------------------
	$menu = array();

	$_fi = $GLOBALS["finance"]->include_dir_finance;
	$_fa = $GLOBALS["finance"]->include_dir_factuur;

	$menu[count($menu)] = "facturen";
	$menu[count($menu)] = "Javascript:setFormAktie('".$_fa."/projecten.php');setWaarde('pagina','');setWaarde('overzicht','');verzend();";
	$menu[count($menu)] = "klanten (+producten)";
	$menu[count($menu)] = "Javascript:setFormAktie('".$_fa."/klanten.php');setWaarde('pagina','');verzend();";
	$menu[count($menu)] = "producten (standaard)";
	$menu[count($menu)] = "Javascript:setFormAktie('".$_fa."/producten.php');setWaarde('pagina','');setWaarde('debiteur_nr','');verzend();";
	//$menu[count($menu)] = "soorten bedrijven";
	//$menu[count($menu)] = "Javascript:setFormAktie('".$_fa."/soortenbedrijven.php');setWaarde('pagina','');verzend();";
	//$menu[count($menu)] = "grootboeknummers";
	//$menu[count($menu)] = "Javascript:setFormAktie('factuur/grootboeknummers.php');setWaarde('pagina','');verzend();";
	$menu[count($menu)] = "standaard instellingen";
	$menu[count($menu)] = "Javascript:setFormAktie('".$_fa."/teksten.php');setWaarde('pagina','');verzend();";
	//$menu[count($menu)] = "overzichten";
	//$menu[count($menu)] = "Javascript:setFormAktie('factuur/overzichten.php');setWaarde('pagina','');verzend();";
	$menu[count($menu)] = "covide financieel";
	$menu[count($menu)] = "Javascript:setWaarde('pagina', ''); setFormAktie('?mod=finance');verzend();";

#dbase
$linkCvd = &$db;

// meer functies ---------------------------------------------------------------------------

$euro = 2.20371;
$btw = 0.19;


//------------------------------------------------------------------------------------------


function convertDatum($datum){
   $convertDag = substr($datum, 6, 2);
   $convertMaand = substr($datum, 4, 2);
   $convertJaar = substr($datum, 0, 4);
      $convertDatum = "$convertDag-$convertMaand-$convertJaar";
      return $convertDatum;
}
function convertDatumNaam($datum){
	$maanden = Array("", "januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december");
   $convertDag = substr($datum, 6, 2);
   $convertMaand = substr($datum, 4, 2);
   $convertJaar = substr($datum, 0, 4);
      $convertDatum = "$convertDag ".$maanden[intval($convertMaand)]." $convertJaar";
      return $convertDatum;
}

function plaatsHtmlEditor($breedte, $hoogte, $html){

	$editor = bepaal_editor(2);
	$inhoud = $html;
	require("../email/functions_conversions.php");
	require("../common/editorModule.php");

	?>
	<input type="hidden" name="html" value="">

	<script type="text/javascript" language="Javascript1.2">
		function emulatedEditor(){ return true; };
		emulatedEditor.prototype.MENU_FILE_SAVE = function(){
			html();
			if (document.eenform){
				if (document.eenform.html && document.eenform.inhoud){
					document.eenform.html.value = document.eenform.inhoud.value;
				}
			}
			if (document.velden){
				if (document.velden.html && document.velden.inhoud){
					document.velden.html.value = document.velden.inhoud.value;
				}
			}
			return true;
		}
		var editor = null;
		editor = new emulatedEditor();

	</script>
	<?php

}
function XXplaatsHtmlEditor($breedte, $hoogte, $html){ ?>

		<input name="html" type="hidden" value="">
		<iframe name="editor" class="inputtext" src="../htmleditor/index.php" style="width:<?php echo $breedte ?>px;height:<?php echo $hoogte ?>px;" frameborder="0" scrolling="yes"></center></iframe>
		<script>
			function plak(){
				editor.tbContentElement.DocumentHTML = "<?php echo str_replace("&rdquo;", "'", str_replace("\r\n", "", $html)) ?>";
			}
		</script>
<?php
}

function plaatsDatumVelden($datum, $popup){
	global $xjaar;
	?>

	<table border="0">
        <tr>
              <td><select name="dag" class="inputselect" <?php if ($popup){ ?> onChange="Javascript:opener.document.velden.dag.selectedIndex=this.selectedIndex;maakDatum();" <?php } ?>>
             <?php for ($i=1;$i<=31;$i++){
              if (strlen($i) == 2){ ?>
                 <option value="<?php  echo $i; ?>" <?php if (substr($datum, 6, 2) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
              <?php }else{ ?>
                 <option value="0<?php  echo $i; ?>" <?php if (substr($datum, 6, 2) == "0$i"){ ?>SELECTED<?php } ?> >0<?php echo $i; ?></option>
              <?php } ?>
             <?php } ?>
           </select></td>
           <td>

         <select name="maand" class="inputselect"  <?php if ($popup){ ?> onChange="Javascript:opener.document.velden.maand.selectedIndex=this.selectedIndex;maakDatum();" <?php } ?>>
             <?php for ($i=1;$i<=12;$i++){
              if (strlen($i) == 2){ ?>
                 <option value="<?php  echo $i; ?>" <?php if (substr($datum, 4, 2) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
              <?php }else{ ?>
                 <option value="0<?php  echo $i; ?>" <?php if (substr($datum, 4, 2) == "0$i"){ ?>SELECTED<?php } ?> >0<?php echo $i; ?></option>
              <?php } ?>
             <?php } ?>
           </select></td>
           <td><select name="jaar" class="inputselect"  <?php if ($popup){ ?> onChange="Javascript:opener.document.velden.jaar.selectedIndex=this.selectedIndex;maakDatum();" <?php } ?>>
           <?php for ($i=2000;$i<2010;$i++){ ?>
							<?php if (!in_array($i,$xjaar)){ ?>
               <option value="<?php  echo $i; ?>" <?php if (substr($datum, 0, 4) == $i){ ?>SELECTED<?php } ?> ><?php echo $i; ?></option>
							<?php } ?>
           <?php } ?></select>
           </td>
        </tr>
	</table>

<?php
}

// pagina navigatie ----------------------------------------------------------------------
function pageNav(){ ?>
	<?php global $chr,$sort,$debiteur_nr,$pagina,$id,$statusid,$betaaldnieuw,$statusnieuw; ?>

<script language="Javascript">
	<?php //deze functie verzend/submit het form ?>
	function verzend(){
		document.velden.submit();
	}

	<?php //met deze functie kun je de waarde van een veld aanpassen ?>
	function setWaarde(item, waarde) {
		var el = document.getElementsByName(item);
		for (i=0; i < el.length; i++) {
			if (el[i].form.name == 'velden')
				el[i].value = waarde;
		}
	}

	<?php //met deze functie kun je de waarde van een veld opvragen (bijna nooit nodig,
	//gebruik Request.Form() om een waarde te weten te komen) ?>
	function getWaarde(item) {
		var el = document.getElementsByName(item);
		for (i=0; i < el.length; i++) {
			if (el[i].form.name == 'velden')
				return el[i].value;
		}
	}

	<?php //met deze functie kun je de form-action aanpassen (default is dat de huidige pagina) ?>
	function setFormAktie(pagina){
		setWaarde('sort', '');
		document.velden.action = pagina;
	}

	<?php //met deze functie kun je een popup openen ?>
	function popUp(pagina, breedte, hoogte, links, top){
		if (breedte != null){breedteArg = breedte}else{breedteArg = 300};
		if (hoogte != null){hoogteArg = hoogte}else{hoogteArg = 300};
			eenPopUp = window.open(pagina, "popper", "toolbar=no scrollbars=yes, resize=no, width=" + breedteArg + ", height=" + hoogteArg + ", screenx=100, screeny=100, left=" + links + ", top=" + top);
	}

	var huidigeStap;
	var stapTeller = 0;
	function popUpStap(pagina, breedte, hoogte, links, top){
	if (breedte != null){breedteArg = breedte}else{breedteArg = 300};
	if (hoogte != null){hoogteArg = hoogte}else{hoogteArg = 300};
		eenPopUp = window.open(pagina, "popper"+stapTeller, "toolbar=no scrollbars=no, status=yes, resize=no, width=" + breedteArg + ", height=" + hoogteArg + ", screenx=100, screeny=100, left=" + links + ", top=" + top);
		stapTeller++;
	}

	function replace(string,text,by) {
	    var strLength = string.length, txtLength = text.length;
	    if ((strLength == 0) || (txtLength == 0)) return string;

	    var i = string.indexOf(text);
	    if ((!i) && (text != string.substring(0,txtLength))) return string;
	    if (i == -1) return string;

	    var newstr = string.substring(0,i) + by;
	    if (i+txtLength < strLength)
	        newstr += replace(string.substring(i+txtLength,strLength),text,by);

	    return newstr;
	}
</script>
<form name="velden" method="post" action="<?php echo sprintf("%s/%s", $GLOBALS["finance"]->include_dir_factuur, basename($_SERVER["SCRIPT_NAME"])); ?>">

	<input name="chr" value="<?php echo $chr; ?>" type="hidden">
	<input name="sort" value="<?php echo $sort; ?>" type="hidden">
	<input name="debiteur_nr" value="<?php echo $debiteur_nr; ?>" type="hidden">
	<input name="pagina" value="<?php echo $pagina; ?>" type="hidden">
	<input name="id" value="<?php echo $id; ?>" type="hidden">
	<input name="statusid" value="<?php echo $statusid; ?>" type="hidden">
	<input name="betaaldnieuw" value="<?php echo $betaaldnieuw; ?>" type="hidden">
	<input name="overzicht" value="<?php echo $overzicht; ?>" type="hidden">
	<input name="statusnieuw" value="<?php echo $statusnieuw; ?>" type="hidden">
	<input name="paging" value="" type="hidden">

	<input name="buf_klanten" value="<?php echo $_REQUEST["klanten"] ?>" type="hidden">
	<input name="buf_zoeken" value="<?php echo $_REQUEST["zoeken"] ?>" type="hidden">
	<input name="buf_maand" value="<?php echo $_REQUEST["maand"] ?>" type="hidden">
	<input name="buf_jaar" value="<?php echo $_REQUEST["jaar"] ?>" type="hidden">
<?php } ?>
