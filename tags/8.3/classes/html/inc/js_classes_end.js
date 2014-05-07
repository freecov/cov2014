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
	}
);
/* try disable double clicks */
addLoadEvent(disableDoubleClicks());

/* if development, show stats by default */
if (dev_version != 0)
	addLoadEvent(showPerformanceInfo());

