/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function check_repeat_type() {
	var el = document.getElementById('cmsrepeat_type');
	if (el.value == 1) {
		document.getElementById('repeat_layer').style.display = 'block';
	} else {
		document.getElementById('repeat_layer').style.display = 'none';
	}
}
document.getElementById('cmsrepeat_type').onchange = function() {
	check_repeat_type();
}
addLoadEvent(check_repeat_type());