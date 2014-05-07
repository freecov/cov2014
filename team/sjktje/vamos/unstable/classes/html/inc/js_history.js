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

var history_el = document.getElementById('history');
var history_group = history_el.childNodes;
var history_color = 0;

for (i=0; i < history_group.length; i++ ) {
	if (history_group[i].label) {
		eval("if (" + history_group[i].label + " == window.name) { history_color=1; } else { history_color=0; } ");
		if (history_color) {
			history_group[i].style.color = '#ff5112';
		}
	}
}

history_el.onchange = function() {
	window.location.href = 'index.php?mod=history&restorepoint=' + history_el.value;
}