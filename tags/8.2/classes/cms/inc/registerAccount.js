/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function focusUsername() {
	if (document.getElementById('datausername')) {
		document.getElementById('datausername').focus();
	} else if (document.getElementById('dataemail')) {
		document.getElementById('dataemail').focus();
	}
}
function registerAccount() {
	document.getElementById('formident').submit();
}
function recoverAccount() {
	document.getElementById('formident').submit();
}
function recoverPassword(uri, siteroot, email) {
	location.href = new String().concat('?mod=cms&action=recoverAccountPassword&uri=', uri, '&siteroot=', siteroot, '&email=', email);
}
addLoadEvent(focusUsername());