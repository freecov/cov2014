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

jQuery.extend({
	showPopup:function(url, title, boxwidth, boxheight) {
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

		$('body').append("<div id='modal_div' style='padding-top: 0px; border: 1px solid black; background-color: #FFF; position: absolute; z-index: 1000; display: none;'><div style='background-color: #ff4500; color: white; display: block; padding-top: 0px; margin-top: 0px; font-family: georgia;'><b>"+title+"</b><img src='themes/default/icons/cross.png' id='close' style='position: absolute; cursor:pointer;'></div><iframe width='"+boxwidth+"' height='"+boxheight+"' frameborder='0' marginwidth='0' marginheight='0' scrolling='auto' name='frmTest' src='"+url+"'></iframe></div>");

		$('#modal_div').css({ left: win_left, top: win_top });

		$("#close").click(function() {
			$("#modal_div").hide();
			$("#modal_div").remove();
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

	var w = $(window).width() - 40;
	var h = $(window).height() - 30;

	var opts = '';

	opts = opts.concat("width="+ w +",height="+ h);
	opts = opts.concat(",directories="+nav+", location="+nav+",menubar="+nav+",status="+nav+",toolbar="+nav+",personalbar="+nav+",resizable=yes,scrollbars=yes");

	/* msie places popups outside the screen sometimes */
	if (navigator.userAgent.indexOf("MSIE 6") != -1)
		opts = opts.concat(",left=10,top=6");

	//var controller_window = window.open(url, controller, opts);
	$.showPopup(url, '', w, h);
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
