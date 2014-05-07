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


function loadXMLDoc(url, xmlhttp_handler, async_call) {

	/* add random parameter to the call */
	url = extendUrl(url);

	/* do a XMLhttp background call */
	/* this function does not work perfectly in firefox due browser bugs */
	if (window.XMLHttpRequest) {
		/* for Mozilla/Gecko */
		var xmlhttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		/* for MSIE */
		var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		/* for others */
		return false;
	}

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
				if (xmlstatus==200) {
					eval( new String().concat(" try { ", xmlhttp_handler, "('" + escape(ret) + "'); } catch(err) { var err = true; }") );
				} else {
					if (xmlhttp_unload == 0 && xmlstatus != 0) {
						alert("Problem retrieving data:" + xmlhttp.statusText + " code is:" + xmlhttp.status);
					}
				}
			}
		}
	}

	/* send the call */
	xmlhttp.send(' ')

	if (!async_call) {
		if (typeof(xmlhttp.status) == "undefined" || xmlhttp.status == 200) {
			var ret = xmlhttp.responseText;
			eval( new String().concat(" try { ", xmlhttp_handler, "('" + escape(ret) + "'); } catch(err) { var err = true; }") );
		}
	}
}

function loadXMLContent(url) {

	if (window.XMLHttpRequest) {
		/* for Mozilla/Gecko */
		var xmlhttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		/* for MSIE */
		var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		/* for others */
		return false;
	}

	/* exec the call */
	xmlhttp.open("GET", url, false)
	xmlhttp.send(' ')

	if (typeof(xmlhttp.status) == "undefined" || xmlhttp.status == 200) {
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
