<!--
var hX = document.body.clientWidth-15-422;
var hxT = (document.body.clientWidth-200)/2;
var vY = 2;

var nn=(navigator.appName.indexOf("Netscape")!=-1);
var dD=document,dH=dD.html,dB=dD.body,px=dD.layers?'':'px';

function floatMail(iX,iY,id){
	var L = dD.getElementById?dD.getElementById(id):dD.all?dD.all[id]:dD.layers[id];

	this[id+'O']=L;

	if(dD.layers)
	 	L.style=L;
	L.nX=L.iX=iX;
	L.nY=L.iY=iY;

	L.P=function(x,y){
		this.style.left=x+px;this.style.top=y+px;
	};
	L.Fm=function(){

		if (L.style.visibility=='visible' || inited_floater == false){

			inited_floater = true;

			var pX, pY;
			pX=(this.iX >=0)?0:nn?innerWidth:nn&&dH.clientWidth?dH.clientWidth:dB.clientWidth;
			pY=nn?pageYOffset:nn&&dH.scrollTop?dH.scrollTop:dB.scrollTop;
			if(this.iY<0)
				pY+=nn?innerHeight:nn&&dH.clientHeight?dH.clientHeight:dB.clientHeight;

			this.nX = pX+this.iX;
			this.nY = pY+this.iY;

			this.P(this.nX,this.nY);
			setTimeout(this.id+'O.Fm()',400);
		}else{
			setTimeout(this.id+'O.Fm()',200);
		}

	};
	return L;
}

var inited_floater = false;

//if (navigator.appName == "Microsoft Internet Explorer") {
	floatMail(hX,vY,'infovak').Fm();
	floatMail(hxT,vY,'telvak').Fm();
/*
} else {
	document.getElementById('infovak').style.position = 'fixed';
	document.getElementById('infovak').style.top = '2px';
	document.getElementById('infovak').style.left = hX+'px';

	document.getElementById('telvak').style.position = 'fixed';
	document.getElementById('telvak').style.top = '2px';
	document.getElementById('telvak').style.left = hxT+'px';
}
*/
-->
