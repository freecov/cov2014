/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function set(item, waarde){
if (document.velden){
		eval("document.velden."+item+".value='"+waarde+"'");
	}
}
function submitform(){
	if (document.velden){
		document.velden.submit();
	}
}
function fAct(pagina){
	if (document.velden){
		document.velden.action = pagina;
	}
}
