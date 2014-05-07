var grootboek_complete_timer;
var grootboek_search;

/* function to init the completion layer (called by the xmlhttp request) */
function grootboek_init_layer(ary) {
	ary = unescape(ary);
	var ret = ary.split('\n');
	var flag = 0;

	/* prepare the html code */
	var html = '';
	var tmp = '';
	for (i=1;i<ret.length-1;i++) {
		flag++;
		tmp = ret[i].split('|');
		tmp[0] = '<a href="javascript: void(0);" onclick="javascript: grootboek_set_complete(\''+tmp[0]+'\', \''+tmp[1]+'\');">'+tmp[1]+'</a>';
		html = html.concat('<tr><td><span class="d">', tmp[0], '</span></td></tr>');
	}
	html = html.concat('<tr><td style="border-top: 1px solid #999;"><span class="d"><a href="javascript: void(0);" onclick="setTimeout(\'grootboek_clear_layer();\', 100);"><b>&lt;sluiten&gt;</b></a></span></td></tr>');

	if (ret.length > 10) {
		/* limit the length of the layer */
		var xstyle = "overflow: auto; height: 250px;";
	} else {
		var xstyle = "";
	}

	/* MSIE doesn't like the TABLE tag inside innerHTML directly, workaround: */
	var div = document.createElement("DIV");
	div.innerHTML = "<div id='grootboek_layer_overflow' style='"+xstyle+"'><table cellspacing=0 cellpadding=2 bgcolor='white' style='border: 1px outset black'>" + html + "</table></div>";

	//retrieve target element
	var el = document.getElementById('grootboek_autocomplete');
	var ac = document.getElementById('grootboek_autocomplete_layer');
	var ifr = document.getElementById('grootboek_layer_iframe'); //needed for msie compatibility;

	var posx = parseInt(findPosX(el)) + 3;
	var posy = parseInt(findPosY(el)) + 23;

	ac.style.visibility = 'visible';
	ac.style.top = posy+'px';
	ac.style.left = posx+'px';

	ac.innerHTML = ' ';
	ac.appendChild(div);

	grootboek_complete_timer = setTimeout('grootboek_clear_layer()',8000);

	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		ifr.style.display = '';
		ifr.style.top = ac.style.top;
		ifr.style.left = ac.style.left;
		ifr.style.width = ac.offsetWidth;
		ifr.style.height = ac.offsetHeight;
	}
}

/* function to erase the completion layer contents */
function grootboek_clear_layer() {
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

	clearTimeout(grootboek_complete_timer);
	document.getElementById('grootboek_autocomplete_layer').style.visibility = 'hidden';
}

/* autocomplete the field (set value) */
function grootboek_set_complete(id, str) {
	document.getElementById('grootboek_id').value = id;
	document.getElementById('grootboek_name').innerHTML = str;
	grootboek_clear_layer();
	document.getElementById('grootboek_autocomplete').value = '';
}

function grootboek_loadXMLList() {
	var ret = loadXMLContent('?mod=finance&action=autocomplete&str='+grootboek_search);
	//document.getElementById('grootboek_autocomplete_layer').innerHTML = ret;
	grootboek_init_layer(ret);
}

/* function to init autocomplete on the field */
function autogrootboek_complete_field(field) {
	var str = document.getElementById(field).value
	clearTimeout(grootboek_complete_timer);

	grootboek_search = str;
	if (str.length >= 2) {
		grootboek_complete_timer = setTimeout("grootboek_loadXMLList()", 500);
	} else {
		grootboek_clear_layer();
	}
}


if (document.getElementById('grootboek_autocomplete')) {
	document.getElementById('grootboek_autocomplete').onkeyup = function() { autogrootboek_complete_field(this.id); }
	document.getElementById('grootboek_autocomplete').onclick = function() { document.getElementById('grootboek_autocomplete').value = ''; }
}