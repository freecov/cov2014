function update_check(checkstatus) {
	var el  = document.getElementById('cla_check_layer');
	var el2 = document.getElementById('action_save');
	//checkstatus:
	// 0 == all ok
	// 1 == already in database
	// 2 == no name givven
	if (checkstatus == 1) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("deze classificatie bestaat al");
		el2.style.visibility='hidden';
	} else if (checkstatus == 2) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("geen naam opgegeven");
		el2.style.visibility='hidden';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("alles ok");
		el2.style.visibility='visible';
	}
}

var cla_check = Array('addclaname');
function checkClaName() {
	var uri = '?mod=address&action=checkcla_xml';
	for (i=0;i<cla_check.length;i++) {
		uri = uri.concat('&', cla_check[i].replace(/^addcla/g,''), '=', document.getElementById(cla_check[i]).value);
	}
	loadXML(uri);

}

for (i=0;i<cla_check.length;i++) {
	document.getElementById(cla_check[i]).onchange = function() {
		checkClaName();
	}
}

addLoadEvent( checkClaName() );
