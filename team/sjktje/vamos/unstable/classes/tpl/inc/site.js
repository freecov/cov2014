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
	popup('/?mod=cms&syncweb=1&action=editpage&id='+id, 'cmseditor', 990, 680, 1);
}
function cmsLoginPage(uri) {
	popup('/mode/covidelogin', 'cmslogin', 600, 500, 1);
}
function cmsLogout(uri, msg, manage) {
	if (manage) {
		var c = confirm(msg + ' [' + manage + ']');
	} else {
		var c = false;
	}
	if (c == true) {
		popup('http://' + manage + '/?mod=user&action=logout&redir=close');
	}
	location.href = '?mod=user&action=logout&redir=' + uri;
}
function cmsVisitorLogin(uri) {
	popup(uri, 'cmslogin', 600, 500, 1);
}
function cmsVisitorRegistration(uri, siteroot) {
	popup('/?mod=cms&action=registerAccount&uri='+uri+'&siteroot='+siteroot, 'cmslogin', 600, 500, 1);
}
function cmsVisitorPasswordRecover(uri, siteroot) {
	popup('/?mod=cms&action=recoverAccountPassword&uri='+uri+'&siteroot='+siteroot, 'cmslogin', 600, 500, 1);
}
function showFeedbackForm() {
	document.getElementById('give_feedback_message').style.display = 'none';
	document.getElementById('give_feedback_layer').style.display = '';
	//setTimeout('location.href = new String.concat(location.href, "#feedback_position");', 20);
}


function openGalleryItem(id, descr) {
	var galleryitem = popup('/showcms.php?html=1&size=medium&galleryid='+id+'&description='+descr, 'galleryitem', 990, 680, 1);
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
	popup('/site.php?mode=blogadmin', 'mvblog', 990, 680, 0);
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
	try {
		addLoadEvent( document.write( loadXMLContent('/menu/'+id +'&tpl='+tpl+'&type='+tt) ) );
	} catch(e) {
		void(0);
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
	setTimeout("window.status = '" + ('Done - page ' + page) + "';", 500);
}
addLoadEvent(setTimeout('addActionsHandler()', 500));

