<!--
var infovak = document.getElementById("infovak");
var testvak = document.getElementById("testvak");

var telvak = document.getElementById("telvak");
var teltestvak = document.getElementById("teltestvak");

var tinfo; /*timer*/
var autoclose = 0;


function ToonInfo(str, obj, altmethod){
w = 400;

	str = '<table class="dlg2" width=\"100%\"><tr><td><span class=\"d\">'+infotekst+str+'</span></td></tr></table>';
	infovak.innerHTML  = unescape(str);
	infovak.style.width=w+'px';
	obj.style.backgroundColor='#e5e5e5';


	//debug: altmethod=0;
	if (altmethod==0){
		//obj.onmouseover=function(){ infovak.style.visibility='visible'; }
		infovak.style.visibility='visible';
		//infovak.style.display = '';
		autoclose=0;
	}else{
		obj.onmousedown=function(){
			infovak.style.visibility='visible';
			//infovak.style.display = '';
			autoclose=0;
		}
	}
	//ival clear method
	if (autoclose==1){
		clearIvalExt();
	}else{
		clearIval();
	}

}

function wisInfo(){
	clearIval();
	infovak.style.visibility = 'hidden';
	//infovak.style.display = 'none';
	infovak.innerHTML = ' ';
}
function clearIvalExt(){
	var autocloseix = document.getElementById("autocloseix");
	autocloseix.innerHTML=autoclosemsg;
	autoclose = 1;
	clearIval();
}
function clearIval(){
	if (tinfo) clearTimeout(tinfo);
}

function wis(obj){
	if (obj){ obj.style.backgroundColor=''; }
	clearIval();
	if (autoclose!=1){
		tinfo = setTimeout('wisInfo();',3000);
	}
}

// tel functions

function TelWisInfo() {
	telvak.style.visibility = 'hidden';
	telvak.innerHTML = ' ';
}
function TelToonInfo(str, header){
	w = 200;
	if (parentparam) {
		str = str.replace(/\.\/adres\/klant\.php/gi, '../adres/klant.php');
	}
	str = '<table class="dlg2" width=\"100%\"><tr><td><span class=\"dT\">'+header+'</span><br><span class=\"d\" style=\"padding-left: 5px; border-left: 1px solid #999999\">'+str+'</span></td></tr></table>';
	telvak.innerHTML  = unescape(str);
	telvak.style.width=w+'px';
	telvak.style.visibility='visible';

}

-->
