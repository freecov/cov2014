/**
 * Covide Popup Object
 *
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */

function setOverlays(val){
	//alert("set: "+val);
	var expires = new Date();
	expires.setMonth(expires.getMonth() + 1);
	document.cookie = "number_of_overlays=" + val + ";expires=" + expires + ";path=/";
}

// increase the number of overlays
function increaseOverlays() {
	//hide scrollbar
	document.body.style.overflowY = "hidden";
	$("body").css("overflow", "hidden");
	val = getOverlays();
	val++;
	setOverlays(val);
}

// decrease the number of overlays
function decreaseOverlays() {
	val = parseInt(getOverlays());
	val--;
	setOverlays(val);
	document.body.style.overflowY = 'scroll';
	$("body").css("overflow", "auto");

	if (parent.$('#modal_div') == null) {
		clearOverlays();
	}
}

// read the number of active overlays
function getOverlays() {
	var nameEQ = 'number_of_overlays=';
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

// this deletes the cookie when called
function clearOverlays() {
	name_cookie = "number_of_overlays";
	path = "/";
	domain = "";
	if ( getOverlays( name_cookie ) ) document.cookie = name_cookie + "=" +
		( ( path ) ? ";path=" + path : "") +
		( ( domain ) ? ";domain=" + domain : "" ) +
	";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

if (!getOverlays()) {
	setOverlays('0');
}

jQuery.extend({
	showPopup:function(url, title, boxwidth, boxheight, closefunction) {
		var options ={
			margin:1,
			border:1,
			padding:1,
			scroll:0
		};

		var win_width      = $(window).width();
		var win_height     = $(window).height();
		var scrollToLeft   = $(window).scrollLeft();
		var scrollToBottom = $(window).scrollTop();
		var win_left       = ((win_width/2-boxwidth/2)+scrollToLeft)+'px';
		var win_top        = ((win_height/2-boxheight/2)+scrollToBottom)+'px';

		increaseOverlays();
		var parentElement = parent;
		for (var i=1; i < getOverlays(); i++) {
			parentElement = parentElement.parent;
		}
		//$('#'+parentElement.id).append('<div style="position: absolute; height: 100%; width: 100% background-color: lime;">blaat.</div>');
		$('body').append("<div id='modal_div' style='overflow-x:hidden; padding-top: 0px; border: 1px solid black; background-color: #FFF; position: absolute; z-index: 1000; display: none;'><div style='background-color: #ff4500; color: white; display: block; padding-top: 0px; margin-top: 0px; font-family: georgia;'><b>"+title+"</b><img src='themes/default/icons/cross.png' id='close' style='position: absolute; cursor:pointer;'></div><iframe width='"+boxwidth+"' height='"+boxheight+"' frameborder='0' marginwidth='0' marginheight='0' id='frmTest' scrolling='auto' name='frmTest' src='"+url+"&overlay="+getOverlays()+"'></iframe></div>");
		$('#modal_div').css({ left: win_left, top: win_top });

		$("#close").click(function() {
			$("#modal_div").hide();
			$("#modal_div").remove();
			decreaseOverlays();
		});

		$('#modal_div').show();

		var offset = {}
		offset=$("#modal_div").offset({ scroll: false })

		X_left=boxwidth-16;
		X_top=offset.top;

		$('#close').css({left:X_left,top:0});
	},

	removePopup:function() {
		$('#modal_div').hide();
		$('#modal_div').remove();
		decreaseOverlays();
	}
});

function popup(url, controller) {
	/* usage:
		 url        - uri of the resource
		 controller - name of the controller object (alias for window)
		 width      - width of the window (in px)
		 height     - height of the window (in px)
		 hidenav    - boolean (1 = hide the navigation items)
	*/
	var w = 950;
	var h = 600;
	var nav = "yes";
	var modal = "no";
	var closefunction = 0;

	if (arguments[2]) {
		w = arguments[2];
	}
	if (arguments[3]) {
		h = arguments[3];
	}
	if (arguments[4]==1) {
		nav = "no";
		modal = "yes";
	} else if (arguments[4] == 2) {
		closefunction = 1
	}

	var w = $(window).width() - 40;
	var h = $(window).height() - 30;

	var opts = '';

	opts = opts.concat("width="+ w +",height="+ h);
	opts = opts.concat(",directories="+nav+", location="+nav+",menubar="+nav+",status="+nav+",toolbar="+nav+",personalbar="+nav+",resizable=yes,scrollbars=yes");

	/* msie places popups outside the screen sometimes */
	if (navigator.userAgent.indexOf("MSIE 6") != -1)
		opts = opts.concat(",left=10,top=6");

	//var controller_window = window.open(url, controller, opts);
	$.showPopup(url+'&hidenav=1', '', w, h, closefunction);
}

function closepopup() {
	parent.$.removePopup();
}

function oldpopup(url, controller) {
	/* usage:
		 url        - uri of the resource
		 controller - name of the controller object (alias for window)
		 width      - width of the window (in px)
		 height     - height of the window (in px)
		 hidenav    - boolean (1 = hide the navigation items)
	*/
	var w = screen.width - 50;
	var h = screen.height - 200;
	var nav = "yes";
	var modal = "no";

	if (arguments[2]) {
		w = arguments[2];
	}
	if (arguments[3]) {
		h = arguments[3];
	}
	if (arguments[4]==1) {
		nav = "no";
		modal = "yes";
	}

	var opts = '';

	opts = opts.concat("width="+ w +",height="+ h);
	opts = opts.concat(",directories="+nav+", location="+nav+",menubar="+nav+",status="+nav+",toolbar="+nav+",personalbar="+nav+",resizable=yes,scrollbars=yes");

	/* msie places popups outside the screen sometimes */
	if (navigator.userAgent.indexOf("MSIE 6") != -1)
		opts = opts.concat(",left=10,top=6");

	var controller_window = window.open(url, controller, opts);
}
