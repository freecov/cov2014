/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function pagePrint(page) {
	if (navigator.userAgent.indexOf("MSIE 6") != -1) {
		var printwin = window.open('/text/' + page + '&amp;print=1&amp;close=1','printwin','width=0,height=0,top=0,left=0');
	} else {
		var el = document.getElementById('print_container');
		var ifr = '';
		ifr = ifr.concat('<ifr', 'ame style="visibility: hidden;" src="/text/', page, '&amp;print=1">', '<\/ifr', 'ame>');
		el.innerHTML = ifr;
	}
}
function pageHistory() {
	history.go(-1);
}
function pageText(page) {
	location.href='/text/'+page;
}

function pageSitemapText() {
	location.href='/sitemap_plain.htm';
}
function cmsEdit(id) {
	oldpopup('/?mod=cms&syncweb=1&action=editpage&id='+id, 'cmseditor', 990, 750, 1);
}
function cmsLoginPage(uri) {
	oldpopup('/mode/covidelogin&uri='+uri, 'cmslogin', 600, 500, 1);
}
function cmsLogout(uri, msg, manage) {
	if (manage) {
		var c = confirm(msg + ' [' + manage + ']');
	} else {
		var c = false;
	}
	if (c == true) {
		oldpopup('http://' + manage + '/?mod=user&action=logout&redir=close');
	}
	location.href = '/index.php?mod=user&action=logout&redir=' + uri;
}
function cmsVisitorLogin(uri) {
	oldpopup(uri, 'cmslogin', 600, 500, 1);
}
function cmsVisitorRegistration(uri, siteroot) {
	oldpopup('/?mod=cms&action=registerAccount&uri='+uri+'&siteroot='+siteroot, 'cmslogin', 600, 500, 1);
}
function cmsVisitorPasswordRecover(uri, siteroot) {
	oldpopup('/?mod=cms&action=recoverAccountPassword&uri='+uri+'&siteroot='+siteroot, 'cmslogin', 600, 500, 1);
}
function showFeedbackForm() {
	document.getElementById('give_feedback_message').style.display = 'none';
	document.getElementById('give_feedback_layer').style.display = '';
	//setTimeout('location.href = new String.concat(location.href, "#feedback_position");', 20);
}


function openGalleryItem(id, descr) {
	var galleryitem = oldpopup('/showcms.php?html=1&size=medium&galleryid='+id+'&description='+descr, 'galleryitem', 320, 300, 1);
}
function iframeGalleryItem(id, descr) {
	var uri = '/showcms.php?html=1&size=medium&galleryid='+id+'&description='+descr;
	var iframe = document.getElementById('galleryframe');
	if (iframe.contentDocument) {
		document.getElementById('galleryframe').contentDocument.location.href = uri;
	} else {
		document.frames['galleryframe'].location.href = uri;
	}
}
function blogAdmin() {
	oldpopup('/site.php?mode=blogadmin', 'mvblog', 990, 680, 0);
}
function forum_resize_frame() {
	var iframe = document.getElementById('iframe');
	if (iframe.contentDocument) {
		iframe.style.height = iframe.contentDocument.body.scrollHeight+50;
	} else {
		iframe.style.height = document.frames['iframe'].document.body.scrollHeight + 60;
	}
}

var meta_counter = 0;
var meta_error_msg = '';
var meta_limit = 5;

function changeMetaCounter(obj, id) {
	var state = obj.checked;

	if (state == true) {
		/* if checked */
		if (meta_counter >= meta_limit) {
			/* if limit has been reached */
			obj.checked = false;
			document.getElementById('metalayer_'+id).style.display = 'none';
			alert(meta_error_msg);
		} else {
			/* limit has not been reached */
			meta_counter++;
			document.getElementById('metalayer_'+id).style.display = '';
		}
	} else {
		/* if not checked */
		meta_counter--;

		/* hide the layer */
		document.getElementById('metalayer_'+id).style.display = 'none';
	}
}
function submitMetaForm() {
	document.getElementById('metaform').submit();
}
function execActionsCmd() {
	var val = document.location.href = document.getElementById('cmsactions').value;
	if (val != 0 && val) {
		document.location.href = val;
	}
}
function addActionsHandler() {
	if (document.getElementById('cmsactions')) {
		document.getElementById('cmsactions').onchange = function() {
			execActionsCmd();
		}
	}
}
function menu_loader(id, tpl, tt) {
	var ret = '';
	try {
		/* first try */
		ret = loadXMLContent('/menu/'+id +'&tpl='+tpl+'&type='+tt);
		document.write(ret);
	} catch(e) {
		ret = '';
	}
}
function showPageOptions() {
	document.getElementById('alternative_icon').style.visibility = 'hidden';
	document.getElementById('alternative_footer').style.visibility = 'visible';
}

function showNav() {
	document.getElementById('cms_navigation').style.visibility = 'visible';
}

function initCalOffset(page, start) {
	document.location.href='/calendarpage/'+page+'&calstart='+start+'#calendar_mark';
}

function init_status(page) {
	//setTimeout("window.status = '" + ('Done - page ' + page) + "';", 500);
}
function banner_loader(c, h) {
	var uri = '/mode/sponsors&count='+c+'&h='+h;
	var ret = loadXMLContent(uri);
	document.write(ret);
}
function checkRemoteLogin(rhost) {
	var uri = '';
	var now = Math.round(new Date().getTime()/1000.0);
	uri = uri.concat('http://', rhost, '/loginimage.png?m=', now);
	setTimeout("document.getElementById('remote_login_image').src = '" + uri + "';", 100);
}

function shopAdd(id) {
	var num = 0;
	var uri = '';
	if (document.getElementById('shopcount')) {
		num = document.getElementById('shopcount').value;
		uri = uri.concat('/mode/shopadd&id=', id, '&num=', num);
		loadXML(uri);
	} else {
		//num = prompt(arguments[1], 1);
		num = parseInt(document.getElementById('shopField_'+id).value);
		if (num > 0)
			uri = uri.concat('/mode/shopadd&id=', id, '&num=', num);
			loadXML(uri);
	}
}
function shopEdit(id, num, txt) {
	var msg = document.getElementById('shop_edit_msg').innerHTML;
	msg = msg.concat(': ', txt);

	var p = Prompt.show(msg, num);
	var uri = '';
	if (p) {
		uri = uri.concat('/mode/shopadd&id=', id, '&num=', p, '&reset=1');
		loadXML(uri);
	}
}
function shopDel(id, txt) {
	var msg = document.getElementById('shop_del_msg').innerHTML;
	msg = msg.concat(': ', txt);

	var p = confirm(msg);
	if (p == true) {
		var uri = '';
		uri = uri.concat('/mode/shopadd&id=', id, '&num=0', '&reset=1');
		loadXML(uri);
	}
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
}
function add_upload_field() {
	var div = document.createElement('div');
	div.innerHTML = document.getElementById('uploadcode').innerHTML.replace(/ id=\"[^\"]*?\"/gi,'');
	document.getElementById('moreuploadcode').appendChild( div );
}

function echeck(str) {
	var at="@"
	var dot="."
	var lat=str.indexOf(at)
	var lstr=str.length
	var ldot=str.indexOf(dot)
	if (str.indexOf(at)==-1){
			return false
	}
	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
			return false
	}
	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
			return false
	}
	if (str.indexOf(at,(lat+1))!=-1){
		return false
	}
	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false
	}
	if (str.indexOf(dot,(lat+2))==-1){
		return false
	}
	if (str.indexOf(" ")!=-1){
		return false
	}
	return true
}

function disableDoubleClicks() {
	var el = document.getElementsByTagName('a');
	for (i=0; i < el.length; i++) {
		el[i].ondblclick = function() { return false; };
	}
}

function UA_css(ua, file) {
	if (navigator.userAgent.indexOf(ua) != -1) {
		document.write('<link rel="stylesheet" type="text/css" href="' + file + '">');
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
function handleClassFocus() { void(0) }

function page_loaded(s) {
	if (s)
		window.status = s;

	IE6_png();
	setTimeout('disableDoubleClicks();', 10);
}

function login() {
	/* get visible password and real password fields */
	var pw = document.getElementById('vis_password');
	var realpw = document.getElementById('password');

	/* calculate the hash md5 (challenge + md5(password) ) */
	var str = new String().concat( pw.value , crypt_challenge );
	realpw.value = hex_md5(str);

	/* overwrite the user password with (*) */
	pw.value = '';

	/* submit the form */
	document.getElementById('loginfrm').submit();
}

function load_inline_data(pageid, include) {
	var query = document.location.href;
	var uri = '';
	uri = uri.concat('/include/', include, '&pageid=', pageid, '&uri=', query);
	document.write(loadXMLContent(uri));
}

/* sitemap functions */
function sitemap_img(img) {
	document.write('<img src=\"img/cms/' + img + '.gif?v=1\" alt=\"\">');
}
function sitemap_map(lvl, ext) {
	if (lvl>0)
		document.write('<\/nobr><\/td><\/tr>');

	document.write('<tr><td><nobr>');
	for (i=0; i<lvl; i++) {
		sitemap_img('tree_left');
	}
	sitemap_img('tree_mid');
	if (ext == 1)
		sitemap_img('page_struct');
	else
		sitemap_img('page');

	document.write('<\/nobr><\/td><td>');
}
function sitemap_tbl(s) {
	if (s == 1)
		document.write('<table cellpadding="0" cellspacing="0">');
	else
		document.write('<\/nobr><\/td><\/tr><\/table>');
}


