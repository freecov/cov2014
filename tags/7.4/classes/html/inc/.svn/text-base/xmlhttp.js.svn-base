/**
 * Covide XMLHttp Object
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function findPosX(obj) {
	var curleft = 0;
	if(obj.offsetParent)
			while(1)
			{
				curleft += obj.offsetLeft;
				if(!obj.offsetParent)
					break;
				obj = obj.offsetParent;
			}
	else if(obj.x)
			curleft += obj.x;
	return curleft;
}

function findPosY(obj) {
	var curtop = 0;
	if(obj.offsetParent)
			while(1)
			{
				curtop += obj.offsetTop;
				if(!obj.offsetParent)
					break;
				obj = obj.offsetParent;
			}
	else if(obj.y)
			curtop += obj.y;
	return curtop;
}

function xmlhttp_exec_js(ret) {
	ret = unescape(ret);
	eval(ret);
}

function extendUrl(url) {
	/* add random parameter to the call (current timestamp in msec) */
	/* to prevent caching (i.e. MSIE does) */
	var now = new Date();
	url = url.concat('&req=', now.getTime() );

	return url;
}

function callback_test(ret) {
	alert(ret);
}


var xmlhttp_suppress_err = 0;
function xmlhttp_error(code) {
	if (xmlhttp_suppress_err <= 1) {
		xmlhttp_suppress_err += 1;
	} else {
		return false;
		//alert("problem retrieving data: connection to the server was lost, please check your connection settings.\n\nerror details: " + code);
	}
}

function createXMLHttpObject() {
	if (window.ActiveXObject) {
		try {
			/* new method */
			var ax = new ActiveXObject("MSXML2.XMLHTTP.3.0");
		} catch(e) {
			/* old method */
			var ax = new ActiveXObject("Microsoft.XMLHttp");
		}
		return ax;
	} else if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else {
		return false;
	}
}

function loadXMLDoc(url, xmlhttp_handler, async_call) {
	url = filterEuro(url);

	/* add random parameter to the call */
	url = extendUrl(url);

	/* do a XMLhttp background call */
	/* this function does not work perfectly in firefox due browser bugs */
	var xmlhttp = createXMLHttpObject();

	/* prepare the call */
	xmlhttp.open("GET", url, async_call)

	if (async_call) {
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState==4) {
				if (typeof(xmlhttp.status) == "undefined") {
					var xmlstatus = 200;
					var ret = '';
				} else {
					var xmlstatus = xmlhttp.status;
					var ret = xmlhttp.responseText;
				}
				if (xmlstatus == 200 || xmlstatus == 205) {
					eval( new String().concat(" try { ", xmlhttp_handler, "('" + escape(ret) + "'); } catch(err) { var err = true; }") );
				} else {
					if (xmlstatus != 0) {
						xmlhttp_error(xmlhttp.statusText + " (" + xmlhttp.status + " / " + xmlstatus + ")");
					}
				}
			}
		}
	}

	/* send the call */
	try {
		xmlhttp.send(' ');
	} catch(e) {
		var xmlhttp_send_err = 1;
	}

	if (!async_call) {
		if (typeof(xmlhttp.status) == "undefined" || xmlhttp.status == 200 || xmlhttp.status == 205) {
			var ret = xmlhttp.responseText;
			eval( new String().concat(" try { ", xmlhttp_handler, "('" + escape(ret) + "'); } catch(err) { var err = true; }") );
		}
	}
}

function loadXMLContent(url) {
	url = filterEuro(url);
	var xmlhttp = createXMLHttpObject();

	/* exec the call */
	xmlhttp.open("GET", url, false)
	xmlhttp.send(' ')

	if (typeof(xmlhttp.status) == "undefined" || xmlhttp.status == 200 || xmlhttp.status == 205) {
		var ret = new String(xmlhttp.responseText);
		if (ret == 'null') {
			ret = '';
		}
		return ret;
	}
}


function loadXML(url) {
	/* if a custom handler has been defined */

	/* ************************************************************************* */
	/* IMPORTANT: the handler will get the xmlhttp response code ENCODED!        */
	/* please use the javascript 'unescape(str);' function to decode any content */
	/* ************************************************************************* */

	if (arguments[1]) {
		var handler = arguments[1];
	} else {
		var handler = 'xmlhttp_exec_js';
	}
	if (arguments[2] == true) {
		var background_call = true;
	} else {
		var background_call = false;
	}

	/* replace any parameters or statement ends (;) */
	handler = handler.replace(/\(.*\)/g, '');
	handler = handler.replace(/;/g, '');

	/* do the call and generate xmlhttp object instance */
	loadXMLDoc(url, handler, background_call);
}

/* rewrite utf8 euro char handling */
function filterEuro(str) {
	var euro = String.fromCharCode(8364); //8364 = utf8 euro sign
	str = str.replace(/\%u20AC/g, euro);
	return str;
}

/* prototype */
function getElementTextNS(prefix, local, parentElem, index) {
	var result = parentElem.getElementsByTagName(local)[index];
	if (result) {
		// get text, accounting for possible
		// whitespace (carriage return) text nodes
		if (result.childNodes.length > 1) {
			return result.childNodes[1].nodeValue;
		} else {
			return result.firstChild.nodeValue;
		}
	} else {
		return "n/a";
	}
}
