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
function findPosX(obj){
    var curleft = 0;
    if (obj.offsetParent)
        while (1) {
            curleft += obj.offsetLeft;
            if (!obj.offsetParent)
                break;
            obj = obj.offsetParent;
        }
    else
        if (obj.x)
            curleft += obj.x;
    return curleft;
}

function findPosY(obj){
    var curtop = 0;
    if (obj.offsetParent) 
		while (1) {
			curtop += obj.offsetTop;
			if (!obj.offsetParent) 
				break;
			obj = obj.offsetParent;
		}
	else
		if (obj.y)
			curtop += obj.y;
    return curtop;
}

function xmlhttp_exec_js(ret){
    ret = unescape(ret);
    eval(ret);
}

function extendUrl(url){
    /* add random parameter to the call (current timestamp in msec) */
    /* to prevent caching (i.e. MSIE does) */
    var now = new Date();
    url = url.concat('&req=', now.getTime());

    return url;
}

function callback_test(ret){
    alert(ret);
}


var xmlhttp_suppress_err = 0;
function xmlhttp_error(code){
    if (xmlhttp_suppress_err <= 1) {
        xmlhttp_suppress_err += 1;
    }
    else {
        return false;
        //alert("problem retrieving data: connection to the server was lost, please check your connection settings.\n\nerror details: " + code);
    }
}

function createXMLHttpObject(){
    return jQuery.ajax();
}

function loadXMLDoc(url, xmlhttp_handler, async_call){
    url = filterEuro(url);

    /* add random parameter to the call */
    url = extendUrl(url);

    if (async_call) {
        try {
            jQuery.get(url, '', function(data, textStatus){
                if (textStatus == "success") {
                    eval(new String().concat(" try { ", xmlhttp_handler, "('" + escape("success") + "'); } catch(err) { var err = true; }"));
                }
            });
        } catch (e) {
            // async error, take no action
        }
    }

    if (!async_call) {
        jQuery.get(url, '', function(data, textStatus){
            if (typeof(textStatus) == "undefined" || textStatus == "success") {
                eval(new String().concat(" try { ", xmlhttp_handler, "('" + escape(data) + "'); } catch(err) { var err = true; }"));
            }
        });
    }
}

function loadXMLContent(url){
    url = filterEuro(url);
	var loadXMLContentResult;
	loadXMLContentResult = '';

	jQuery.ajax({
         url: url,
         success: function (data, textStatus) {
				if (typeof(textStatus) == "undefined" || textStatus == "success") {
					loadXMLContentResult = data;
				}
			},
         async: false
    });
	return loadXMLContentResult;
}

function loadXML(url){
    /* if a custom handler has been defined */

    /* ************************************************************************* */
    /* IMPORTANT: the handler will get the xmlhttp response code ENCODED!        */
    /* please use the javascript 'unescape(str);' function to decode any content */
    /* ************************************************************************* */

    if (arguments[1]) {
        var handler = arguments[1];
    }
    else {
        var handler = 'xmlhttp_exec_js';
    }
    if (arguments[2] == true) {
        var background_call = true;
    }
    else {
        var background_call = false;
    }

    /* replace any parameters or statement ends (;) */
    handler = handler.replace(/\(.*\)/g, '');
    handler = handler.replace(/;/g, '');

    /* do the call and generate xmlhttp object instance */
    loadXMLDoc(url, handler, background_call);
}

/* rewrite utf8 euro char handling */
function filterEuro(str){
    var euro = String.fromCharCode(8364); //8364 = utf8 euro sign
    str = str.replace(/\%u20AC/g, euro);
    return str;
}

/* prototype */
function getElementTextNS(prefix, local, parentElem, index){
    var result = parentElem.getElementsByTagName(local)[index];
    if (result) {
        // get text, accounting for possible
        // whitespace (carriage return) text nodes
        if (result.childNodes.length > 1) {
            return result.childNodes[1].nodeValue;
        }
        else {
            return result.firstChild.nodeValue;
        }
    }
    else {
        return "n/a";
    }
}