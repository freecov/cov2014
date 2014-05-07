<?
	//layer with some information
	$autoCloseMsg1 = gettext("dit venster sluit automatisch");
	$autoCloseMsg1.= " - ";
	$autoCloseMsg1.= gettext("klik in het venster om dit uit te schakelen");
	$autoCloseMsg2 = gettext("automatisch sluiten uitgeschakeld");
?>

<div onmousedown="clearIvalExt();" id="infovak" class="navtext"></div>
<div id="testvak" class="testvak" style="visibility:hidden ;position:absolute;"></div>
<script>
	//tel parent paramter
	var parentparam = '<?=$parent?>';

	var autoclosemsg = '<?=$autoCloseMsg2?>';
	var infotekst = '<a alt=\"<?=gettext("sluiten")?>\" title=\"<?=gettext("sluiten")?>\" href=\"javascript:wisInfo(this)\"><img src=\"<?=$parent?>img/infowin_close.gif\" border=0></a>&nbsp;&nbsp;<b><?=gettext("Informatie venster: ")?></b>&nbsp;<br>(<span id=\"autocloseix\"><?=$autoCloseMsg1?></span>)<br><br>';
</script>
<?
function toonInfoSpan($text, $short, $disableCursor=0){
		global $instelling;
		if ($disableCursor==0){
			$style = " style=\"cursor:help\" ";
		}
		$code = "<span $style onMouseOver=\"javascript:ToonInfo('".str_replace("'", "`",$text)."', this, ".$instelling["infowin_altmethod"].");\" onMouseOut=\"javascript:wis(this);\">".$short."</span>";

		return ($code);
}

?>
<div id="telvak" class="navtext"></div>
<div id="teltestvak" class="testvak" style="visibility:hidden ;position:absolute;"></div>
