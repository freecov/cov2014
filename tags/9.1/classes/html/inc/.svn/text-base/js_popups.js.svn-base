/**
 * Covide Popup Object
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

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

	var opts = '';

	opts = opts.concat("width="+ w +",height="+ h);
	opts = opts.concat(",directories="+nav+", location="+nav+",menubar="+nav+",status="+nav+",toolbar="+nav+",personalbar="+nav+",resizable=yes,scrollbars=yes");

	/* msie places popups outside the screen sometimes */
	if (navigator.userAgent.indexOf("MSIE 6") != -1)
		opts = opts.concat(",left=10,top=6");

	//var controller_window = window.open(url, controller, opts);
	$.showAkModal(url, '', w, h);
}

function closepopup() {
	parent.$.akModalRemove();
}
