var email_select_from_timer = '';

function emailSelectFromCenterVertical() {
	var el = document.getElementById('email_sender_layer');

	var ypos = 200+document.body.scrollTop;
	el.style.top = ypos;
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

	if (arguments[0]) {
		email = arguments[0];
	}
	if (arguments[1]) {
		relation = arguments[1];
	}
	/* detect prepared form */
	if (document.getElementById('alt_mail_to') &&
		document.getElementById('alt_mail_show_select_form').value == 1) {

		document.getElementById('alt_mail_to').value = email;
		document.getElementById('alt_mail_relation').value = relation;

		var el = document.getElementById('email_sender_layer');
		var xpos = (document.body.clientWidth/2)-220;
		var ypos = 200; //+document.body.scrollTop;

		el.style.left = xpos;
		el.style.top = ypos;
		el.style.visibility = 'visible';

		//yet another msie fix
		if (navigator.appVersion.indexOf("MSIE")!=-1){
			el.style.position = 'absolute';
			email_select_from_timer = setInterval('emailSelectFromCenterVertical()', 100);
		} else {
			el.style.position = 'fixed';
		}
	} else {
		location.href='index.php?mod=email&action=compose&to='+email+'&relation='+relation;
	}
}