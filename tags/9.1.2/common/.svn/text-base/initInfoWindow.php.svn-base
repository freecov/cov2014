<?php
/**
  * Covide Includes
  *
  * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
  * @version %%VERSION%%
  * @license http://www.gnu.org/licenses/gpl.html GPL
  * @link http://www.covide.net Project home.
  * @copyright Copyright 2000-2008 Covide BV
  * @package Covide
  */

	//layer with some information
	$autoCloseMsg1 = gettext("this window will close automagically");
	$autoCloseMsg1.= " - ";
	$autoCloseMsg1.= gettext("click inside window to disable this");
	$autoCloseMsg2 = gettext("automatically closing disabled");
?>

<div onmousedown="clearIvalExt();" id="infovak" class="navtext"></div>
<div id="testvak" class="testvak" style="visibility:hidden ;position:absolute;"></div>
<script>
	//tel parent paramter
	var parentparam = '<?php echo $parent ?>';

	var autoclosemsg = '<?php echo $autoCloseMsg2 ?>';
	var infotekst = '<a alt=\"<?php echo gettext("close") ?>\" title=\"<?php echo gettext("close") ?>\" href=\"javascript:wisInfo(this)\"><img src=\"<?php echo $parent ?>img/infowin_close.gif\" border=0></a>&nbsp;&nbsp;<b><?php echo gettext("Information window: ") ?></b>&nbsp;<br>(<span id=\"autocloseix\"><?php echo $autoCloseMsg1 ?></span>)<br><br>';
</script>
<?php
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
