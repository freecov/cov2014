/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function toggleStartDate() {
	var state = document.getElementById('cmss_timestamp_enable').checked;
	if (state == true) {
		document.getElementById('s_timestamp_layer').style.visibility = 'visible';
	} else {
		document.getElementById('s_timestamp_layer').style.visibility = 'hidden';
	}
}
function toggleEndDate() {
	var state = document.getElementById('cmse_timestamp_enable').checked;
	if (state == true) {
		document.getElementById('e_timestamp_layer').style.visibility = 'visible';
	} else {
		document.getElementById('e_timestamp_layer').style.visibility = 'hidden';
	}
}
addLoadEvent(toggleStartDate());
document.getElementById('cmss_timestamp_enable').onchange = function() {
	toggleStartDate();
}
addLoadEvent(toggleEndDate());
document.getElementById('cmse_timestamp_enable').onchange = function() {
	toggleEndDate();
}