/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

var usernamechecktimer;
function checkusername() {
	var username = document.getElementById('cmsusername');
	var id = document.getElementById('id').value;

	username.value = username.value.replace(/[^0-9a-z_\-]/gi, '');
	var ret = loadXMLContent('?mod=cms&action=checkusername&id='+id+'&username='+username.value);
	ret = ret.split('|');
	if (ret[0]==1) {
		document.getElementById('save_page_layer').style.visibility = 'visible';
	} else {
		document.getElementById('save_page_layer').style.visibility = 'hidden';
	}
	document.getElementById('username_layer').innerHTML = ret[1];
}

document.getElementById('cmsusername').onkeydown = function() {
	clearTimeout(usernamechecktimer);
	usernamechecktimer = setTimeout('checkusername();', 500);
}
addLoadEvent(checkusername());
