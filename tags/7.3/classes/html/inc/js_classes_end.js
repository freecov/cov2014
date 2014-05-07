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
	var rendertime_end = new Date();
	var diff = rendertime_end.getTime() - rendertime_start.getTime();
	if (document.getElementById('performance_clienttime')) {
		document.getElementById('performance_clienttime').innerHTML = (Math.round(diff/10)/100);
	}
}

addLoadEvent(
	function() {
		var performance_timer = setTimeout("client_rendertime()", 10);
	}
);
