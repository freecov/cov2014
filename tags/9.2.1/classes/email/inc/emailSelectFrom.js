/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

var email_select_from_timer = '';

function posTop() {
	return typeof window.pageYOffset != 'undefined' ?  window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;
}

function emailSelectFromCenterVertical() {
	var el = document.getElementById('email_sender_layer');

	var ypos = posTop() + 200;
	el.style.top = ypos;
}

function submitEmailSelectForm() {
	var to = document.getElementById('alt_mail_to').value;
	var relation = document.getElementById('alt_mail_relation').value;
	var campaign = document.getElementById('alt_mail_campaign').value;
	var mail_from = document.getElementById('mailfrom').value;
	var show_select_from = document.getElementById('alt_mail_show_select_form').value;
	var popup_newwindow = document.getElementById('popup_newwindow').value;
	url = 'index.php?mod=email&action=compose&to='+to+'&relation='+relation+'&campaign='+campaign+'&mail[from]='+mail_from+'&alt_mail_show_select_from='+show_select_from;
	if (popup_newwindow == 1) {
		oldpopup(url, document.getElementById('email_use_new_window').value, 980, 700, 1);
	} else {
		popup(url, document.getElementById('email_use_new_window').value, 980, 600, 1);
	}
	emailSelectFromHide();
}

function emailSelectFromHide() {
	//hide the layer
	var el = document.getElementById('email_sender_layer');
	el.style.visibility = 'hidden';
	clearInterval(email_select_from_timer);
}

function emailSelectFrom() {
	var email = '';
	var relation = '';
	var campaign_id = '';
	
	if (arguments[0]) {
		email = arguments[0];
	}
	if (arguments[1]) {
		relation = arguments[1];
	}
	if (arguments[2]) {
		campaign_id = arguments[2];
	}
	if (disable_basics == 1) {
		location.href='mailto:'+email;
	} else {
		/* detect prepared form */
		if (//XXX: ghjacobs: why is this line here? it breaks stuff document.getElementById('alt_mail_campaign').value &&
			document.getElementById('alt_mail_to') &&
			document.getElementById('alt_mail_show_select_form').value == 1) {

			document.getElementById('alt_mail_to').value = email;
			document.getElementById('alt_mail_relation').value = relation;
			document.getElementById('alt_mail_campaign').value = campaign_id;
			
			var el = document.getElementById('email_sender_layer');
			var xpos = (document.body.clientWidth/2)-220;
			var ypos = 200; //+document.body.scrollTop;

			el.style.left = xpos + 'px';
			el.style.top = ypos  + 'px';
			el.style.visibility = 'visible';

			//yet another msie fix
			if (navigator.appVersion.indexOf("MSIE") != -1) {
				el.style.position = 'absolute';
				email_select_from_timer = setInterval('emailSelectFromCenterVertical()', 100);
			} else {
				el.style.position = 'fixed';
			}
		} else {
			popup('index.php?mod=email&action=compose&to='+email+'&relation='+relation+'&campaign_id='+campaign_id, new String().concat('mail_compose_', Math.random()) , 980, 700, 1);
		}
	}
}
