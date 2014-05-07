/**
 * Covide JS CLasses
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

var nn=(navigator.appName.indexOf("Netscape")!=-1);
var dD=document,dH=dD.html,dB=dD.body,px=dD.layers?'':'px';

function floatLayer(iX,iY,id) {
	var L = dD.getElementById?dD.getElementById(id):dD.all?dD.all[id]:dD.layers[id];

	this[id+'O']=L;

	if(dD.layers)
	 	L.style=L;
	L.nX=L.iX=iX;
	L.nY=L.iY=iY;

	L.P=function(x,y){
		this.style.left=x+'px';this.style.top=y+'px';
	};
	L.Fm=function(){

		if (L.style.display!='none' || inited_floater == false) {

			inited_floater = true;

			var pX, pY;
			pX=(this.iX >=0)?0:nn?innerWidth:nn&&dH.clientWidth?dH.clientWidth:dB.clientWidth;
			pY=nn?pageYOffset:nn&&dH.scrollTop?dH.scrollTop:dB.scrollTop;
			if(this.iY<0)
				pY+=nn?innerHeight:nn&&dH.clientHeight?dH.clientHeight:dB.clientHeight;

			this.nX = pX+this.iX;
			this.nY = pY+this.iY;
			this.P(this.nX,this.nY);

			setTimeout(this.id+'O.Fm()',200);
		}else{
			setTimeout(this.id+'O.Fm()',1000);
		}

	};
	return L;
}

var inited_floater = false;
if (navigator.userAgent.indexOf("MSIE 6") != -1) {
	document.getElementById('infocontainer').style.position = 'absolute';
	var IX = document.body.clientWidth-502;
	floatLayer(IX, 20, 'infocontainer').Fm();

} else {
	/* all other browser do render position: fixed correctly */
	document.getElementById('infocontainer').style.position = 'fixed';
}