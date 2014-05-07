/* some variables we use global */
var email_complete_timer;
var email_complete_field;
var email_complete_str;
var email_complete_related;
var email_complete_noclosetimer;
var email_complete_expand_results = 0;

/* list of fields to autocomplete */
var email_complete_fields = new Array('mailrcpt', 'mailcc', 'mailbcc');

/* function to erase the completion layer contents */
function clear_layer() {
	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		var ifr = document.getElementById('layer_iframe');
		ifr.style.display = 'none';
		ifr.style.top = 0;
		ifr.style.left = 0;
		ifr.style.width = '0px';
		ifr.style.height = '0px';
	}
	//end fix

	clearTimeout(email_complete_timer);
	document.getElementById('layer_autocomplete').style.visibility = 'hidden';
}

/* function to open the address book */
function openadres_search() {
	set_complete(email_complete_field, '');
	document.getElementById('adresboek_search').value = email_complete_str;
	setTimeout('openadres();',100);
}

function autocomplete_toggle_expand() {
	if (email_complete_expand_results == 1) {
		email_complete_expand_results = 0;
		email_complete_noclosetimer = 0;
	} else {
		email_complete_expand_results = 1;
		email_complete_noclosetimer = 1;
	}
	loadXMLList();
}

/* function to init the completion layer (called by the xmlhttp request) */
function init_layer(ary) {
	ary = unescape(ary);
	var ret = ary.split('#');
	var field = ret[0];
	var titles = ret[1].split('|');
	var flag = 0;

	/* if no field is found exit code */
	if (!field) return true;

	/* prepare the html code */
	var html = '';
	html = html.concat('<tr><td style="border-bottom: 1px solid #999; text-align: left;"><span class="d"><b>', titles[0], '</b></span></td><td style="border-bottom: 1px solid #999; text-align: left;"><span class="d"><b>', titles[1], '</b></span></td><td style="border-bottom: 1px solid #999; text-align: left;"><span class="d"><b>', titles[2], '</b></span></td></tr>');
	var tmp = '';
	for (i=2;i<ret.length-1;i++) {
		flag++;
		tmp = ret[i].split('|');
		tmp[0] = '<a href="javascript: void(0);" onclick="javascript: set_complete(\''+field+'\', \''+tmp[0]+'\');">&lt;'+tmp[0]+'&gt;</a>';
		html = html.concat('<tr><td style="text-align: left;"><span class="d">', tmp[0], '</span></td><td style="text-align: left;"><span class="d">', tmp[1], '</span></td><td style="text-align: left;"><span class="d">', tmp[2], '</span></td></tr>');
	}
	if (flag==0) {
		html = html.concat('<tr><td colspan="3" align="center"><span class="d">'+ complete_msg_noresults +'</span></td></tr>');
	}
	html = html.concat('<tr><td style="border-top: 1px solid #999;"><span class="d"><a href="javascript: void(0);" onclick="setTimeout(\'clear_layer();\', 100);"><b>&lt;sluiten&gt;</b></a></span></td><td style="border-top: 1px solid #999;" colspan="2" align="right">');
	html = html.concat('<span class="d"><a href="javascript: void(0);" onclick="setTimeout(\'autocomplete_toggle_expand();\',100);"><b>&nbsp;');
	if (ret.length >= 20) {
		if (email_complete_expand_results == 1) {
			html = html.concat('&lt;', gettext("less results"), '&gt;');
		} else {
			html = html.concat('&lt;', gettext("more results"), '&gt;');
		}
	}
	html = html.concat('</b></a></span>');
	html = html.concat('</td></tr>');

	if (ret.length > 20 && email_complete_expand_results == 1) {
		/* limit the length of the layer */
		var xstyle = "overflow: auto; height: 550px;";
	} else {
		var xstyle = "";
	}

	/* MSIE doesn't like the TABLE tag inside innerHTML directly, workaround: */
	var div = document.createElement("DIV");
	div.innerHTML = "<div id='email_layer_overflow' style='"+xstyle+"'><table cellspacing=0 cellpadding=2 bgcolor='white' style='border: 1px outset black'>" + html + "</table></div>";

	//retrieve target element
	var el = document.getElementById(field);
	var ac = document.getElementById('layer_autocomplete');
	var ifr = document.getElementById('layer_iframe'); //needed for msie compatibility;

	var posx = parseInt(findPosX(el)) + 30;
	var posy = parseInt(findPosY(el)) + parseInt(el.style.height.replace(/px/g,'')) + 3;

	ac.style.visibility = 'visible';
	ac.style.top = posy+'px';
	ac.style.left = posx+'px';

	ac.innerHTML = ' ';
	ac.appendChild(div);

	if (email_complete_noclosetimer == 0) {
		ac.onclick = function() {
			setTimeout('clear_layer();', 100);
		}
		email_complete_timer = setTimeout('clear_layer()',8000);
	} else {
		ac.onclick = function() { void(0); }
	}

	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		ifr.style.display = '';
		ifr.style.top = ac.style.top;
		ifr.style.left = ac.style.left;
		ifr.style.width = ac.offsetWidth;
		ifr.style.height = ac.offsetHeight;
	}
}

/* autocomplete the field (set value) */
function set_complete(field, email) {
	var el = document.getElementById(field);
	var str = el.value;
	var repl = '';

	str = str.replace(/,/g,' ');
	str = str.replace(/ {1,}/g,' ');
	str = str.split(' ');
	str.pop();

	for (i=0;i<str.length;i++) {
		repl = repl.concat(str[i], ', ');
	}
	repl = repl.concat(email);
	el.value = repl+', ';
	el.focus();
}

/* load XML function */
function loadXMLList() {
	//loadXML('?mod=email&action=autocomplete&field='+email_complete_field+'&str='+email_complete_str+'&rel='+email_complete_related+'&expand='+email_complete_expand_results, 'init_layer(ret);');
	var ret = loadXMLContent('?mod=email&action=autocomplete&field='+email_complete_field+'&str='+email_complete_str+'&rel='+email_complete_related+'&expand='+email_complete_expand_results);
	init_layer(ret);
}

/* function to init autocomplete on the field */
function autoemail_complete_field(field) {
	var mail_id = '';
	var mail_relation = '';
	email_complete_noclosetimer = 0;

	if (arguments[1]) {
		mail_id = arguments[1];
		email_complete_noclosetimer = 1;
	}
	if (arguments[2]) {
		mail_relation = arguments[2];
		email_complete_noclosetimer = 1;
	}

	var str = document.getElementById(field).value
	clearTimeout(email_complete_timer);
	email_complete_field = field;
	email_complete_related = mail_id+'|'+mail_relation;

	//get last index from search phrase
	str = str.replace(/,/g,' ');
	str = str.replace(/ {1,}/g,' ');
	str = str.split(' ');

	var last = str[str.length-1];
	email_complete_str = last;

	if (last.length >= 2 || mail_id) {
		email_complete_timer = setTimeout("loadXMLList()", 500);
	} else {
		clear_layer();
	}
}

/* attach onchange autocomplete handler to the selected fields */
for (i=0;i<3;i++) {
	if (document.getElementById(email_complete_fields[i])) {
		document.getElementById(email_complete_fields[i]).onkeyup = function() { autoemail_complete_field(this.id); }
	}
}
