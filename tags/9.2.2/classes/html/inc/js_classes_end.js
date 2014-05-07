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

/* js include file executed at page end */

function client_rendertime() {
	if (document.getElementById('performance_clienttime')) {
		var rendertime_end = new Date();
		var diff = rendertime_end.getTime() - rendertime_start.getTime();
		diff = Math.round(diff/10)/100;

		var str = '';
		str = str.concat(diff, 's');

		document.getElementById('performance_clienttime').innerHTML = str;
	}
}
function disableDoubleClicks() {
	var el = document.getElementsByTagName('a');
	for (i=0; i < el.length; i++) {
		el[i].ondblclick = function() { return false; };
	}
}
/* === some final loaders === */
/* calculate render time */
addLoadEvent(
	function() {
		var performance_timer = setTimeout("client_rendertime()", 10);

		// this is a fix for slower systems. A floating layer decreases performance by 3-4 times
		// when we have no scrollbar, the layer is position absolute at the bottom. Since there is no scrolling, no redraws
		// will be done, and no performance panelties are taken. If there is a scrollbar, we can move the
		// layer to the bottom of the screen to avoid any screen redraws.
		// TODO: the actions menu at the bottom should really expand to the top .. but that's for another task for another developer
		if (document.getElementById('covide_info')) {
			var vHeight = 0;
			if (document.all) {
				if (document.documentElement) {
					vHeight = document.documentElement.clientHeight;
				} else {
					vHeight = document.body.clientHeight
				}
			} else {
				vHeight = window.innerHeight;
			}
			var covideInfo = document.getElementById('covide_info');
			if (document.body.offsetHeight > vHeight) {
	  			// when theres no scrollbar
				covideInfo.style.position = 'relative';
			} else {
				covideInfo.style.position = 'absolute';
				covideInfo.style.bottom = '0px';
			}
		}
	}
);
/* try disable double clicks */
addLoadEvent(disableDoubleClicks());

/* if development, show stats by default */
if (dev_version != 0)
	addLoadEvent(showPerformanceInfo());

