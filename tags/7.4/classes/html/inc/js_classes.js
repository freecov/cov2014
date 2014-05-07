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

/* unset opener object if the opener is not exactly the same domain name */
try {
	var opener_uri = opener.location.href.split("/");
	var current_uri = document.location.href.split("/");
	if (opener_uri[2] != current_uri[2]) {
		opener = null;
	}
} catch(e) {
	opener = null;
}

/* custom onload handler */
/* the window.onload handler has only space for 1 (or a few?) handler(s) */

function addLoadEvent(func) {
	if (window.onload) {
  	var oldonload = window.onload;
  } else {
  	var oldonload = '';
  }
  if (typeof(func) == 'function') {
		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
				oldonload();
				if (func) {
					func();
				}
			}
		}
	}
}
/* function to add selectbox options, msie friendly */
function addSelectBoxOption(obj, val, txt) {
	var found = 0;
	for (i=0; i<obj.length; i++) {
		if (obj[i].value == val)
			found++;
	}
	if (found == 0)
		obj[obj.length] = new Option(val, txt);

	obj.value = val;
}

/* css class focus selector */
function handleClassFocus(obj, state) {
	/* remove focus class from obj */
	obj.className = obj.className.replace(/ inputfocus/gi,'');
	if (state == 1) {
		obj.className = obj.className.concat(' inputfocus');
	}
}

/* function to scan keyCodes */
function scanKeyCode(e) {
	var keycode;
		if (window.event) keycode = window.event.keyCode;
		else if (e) keycode = e.which;
		if(keycode == 13){
			return false;
	}
}

/* function to scan for problematic characters */
function scanSpecialCharacters(obj) {
	obj.value = obj.value.replace(/"/g, "''");
}

/* intercept double clicks */
window.ondblclick = function() {
	return false;
}

/* some expand collapse methods for table rows */
function expandCollapse(state, className) {
	var objects = document.getElementsByTagName('TR');
	var classes = new Array();
	var cmatch = 0;

	if (state == 1)
		var html = document.getElementById('collapse_'+className).innerHTML;
	else
		var html = document.getElementById('expand_'+className).innerHTML;

	for (i=0; i < objects.length; i++) {
		cmatch = 0;
		classes = objects[i].className.split(' ');
		for (j=0; j < classes.length; j++) {
			if (classes[j] == className)
				cmatch = 1;
		}
		if (cmatch == 1) {
			if (state == 1) {
				if (navigator.appVersion.indexOf("MSIE") !=-1 )
					objects[i].style.display = '';
				else
					objects[i].style.display = 'table-row';
			} else {
				objects[i].style.display = 'none';
			}
		}
	}
	document.getElementById('control_'+className).innerHTML = html;
}

function trim(value) {
  value = value.replace(/^s/,'');
  value = value.replace(/s$/,'');
  return value;
}

function setBgColor(obj, enabled) {

	/* temp store */
	var stemp = '';

	/* get child nodes */
	for (i=0; i < obj.childNodes.length; i++) {
		/* get and store current class */
		stemp = ' ' + obj.childNodes[i].className + ' ';

		/* remove focus class from obj */
		if (stemp.match(/ ((list_record)|(list_record_header)) /gi)) {
			obj.childNodes[i].className = stemp.replace(/ list_record_hover /gi, '');
			if (enabled == 1)
				obj.childNodes[i].className = obj.childNodes[i].className.concat(' list_record_hover');
		}
		obj.childNodes[i].className = trim(obj.childNodes[i].className);
	}

	/* remove focus class from obj */
	/*
	obj.className = obj.className.replace(/ list_record_hover/gi,'');
	if (enabled == 1)
		obj.className = obj.className.concat(' list_record_hover');
	*/
}

function showPerformanceInfo() {
	if (document.getElementById('performance_info_trigger') && document.getElementById('performance_info')) {
		document.getElementById('performance_info_trigger').style.display = 'none';
		document.getElementById('performance_info').style.display = 'inline';
	}
}

function gettext(str) {
	var uri = '?mod=user&action=translate&str=' + escape(str);
	var ret = loadXMLContent(uri);

	if (!ret) {
		return str;
	} else {
		return ret;
	}
}
function updatePagesize(num, cmd) {
	if (num > 200)
		var cf = confirm(gettext('Setting the pagesize to this size (>200) could take some time to process. Continue?'));
	else
		var cf = true;

	if (cf == true) {
		var ret = loadXMLContent('?mod=user&action=updatePagesize&pagesize=' + num);
		if (ret.match(/updated to/))
			eval(cmd);
	}
}

function loadHTML() {
	if (document.getElementById('html_page_content')) {
		document.getElementById('html_page_content').style.visibility = 'visible';
	}
}

/* replace image src by DX filter */
function IE6_png() {
	try {
		if (navigator.userAgent.indexOf("MSIE 6") != -1) {
			for (i=0; i < document.images.length; i++) {
				if (document.images[i].src.match(/\.png\?m=\d{1,}$/g)) {
					document.images[i].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + document.images[i].src + "', sizingMethod='image');";
					document.images[i].src = 'img/spacer.gif';
				}
			}
		}
	} catch(e) {
		//null
	}
}


var txtimer = new Array();
function initFadeIn(obj) {
	if (arguments[1])
		obj = document.getElementById(arguments[1]);
	else
		clearTimeout(txtimer[obj.id]);

	if (!obj.id)
		obj.id = 'nav_'+Math.floor(Math.random()*9999999);

	if (!arguments[2])
		if (obj.style.width)
			var pix = parseInt(obj.style.width.replace(/px/g,''));
		else
			var pix = 32;
	else
		var pix = parseInt(arguments[2])+5;

	obj.style.width = pix;
	obj.style.height = pix;

	if (pix < 64)
		txtimer[obj.id] = setTimeout("initFadeIn('', '" + obj.id + "', '" + pix + "');", 5);
}

function initFadeOut(obj) {
	if (arguments[1])
		obj = document.getElementById(arguments[1]);
	else
		clearTimeout(txtimer[obj.id]);

	if (!arguments[2])
		if (obj.style.width)
			var pix = parseInt(obj.style.width.replace(/px/g,''));
		else
			var pix = 64;
	else
		var pix = parseInt(arguments[2])-5;

	obj.style.width = pix;
	obj.style.height = pix;

	if (pix > 32) {
		if (!arguments[1])
			txtimer[obj.id] = setTimeout("initFadeOut('', '" + obj.id + "', '" + pix + "');", 250);
		else
			txtimer[obj.id] = setTimeout("initFadeOut('', '" + obj.id + "', '" + pix + "');", 5);
	}
}


