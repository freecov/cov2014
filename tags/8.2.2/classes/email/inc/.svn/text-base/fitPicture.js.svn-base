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

var arrTemp=self.location.href.split("?");
var picUrl = (arrTemp.length>0)?arrTemp[1]:"";
var NS = (navigator.appName=="Netscape")?true:false;
var a = 5;

function FitPic() {
	iWidth = (NS)?window.innerWidth:document.body.clientWidth;
	iHeight = (NS)?window.innerHeight:document.body.clientHeight;
	iWidth = document.images[0].width - iWidth;
	iHeight = document.images[0].height - iHeight;
	window.resizeBy(iWidth+a, iHeight+a);
	self.focus();
};

addLoadEvent(FitPic());
