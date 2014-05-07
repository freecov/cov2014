function checkstate() {
	var el = document.getElementById('use_ssl');
	var frm = document.getElementById('login');

	if (el.checked == true) {
		frm.action = frm.action.replace(/^http:\/\//gi, 'https://');
	} else {
		frm.action = frm.action.replace(/^https:\/\//gi, 'http://');
	}
}

function login() {
	login_challenge();

	if (document.getElementById('pt_browser').checked == true && document.getElementById('use_ssl').checked == false) {
		var cf = confirm("Warning: "+gettext("De gekozen combinatie (browser onthoud wachtwoord / geen ssl) is onveilig.")+'\n'+gettext("Weet u zeker dat u verder wilt gaan?"));
	} else {
		var cf = true;
	}
	if (cf == true) {
		document.getElementById("login").submit();
	}
}

function kH(e) {
	var pK = document.all? window.event.keyCode:e.which;
	if (pK==13) {
		var timer2 = setTimeout("login();", 200);
	}
	return pK != 13;
}

/* document handlers */
document.onkeypress = kH;
if (document.layers) document.captureEvents(Event.KEYPRESS);
addLoadEvent(
	function() { document.getElementById('loginusername').focus(); }
);

function check_password_save_type() {
	if (document.getElementById('loginsave_password').checked == true) {
		document.getElementById('password_type_div').style.display = 'block';
	} else {
		document.getElementById('password_type_div').style.display = 'none';
	}
}

if (document.getElementById('loginvis_password')) {
	document.getElementById('loginvis_password').onclick = function() {
		document.getElementById('loginvis_password').value = '';
		document.getElementById('loginuse_cookie_password').value = 0;
		document.getElementById('loginpassword').value = '';
		document.getElementById('loginvis_password').style.backgroundColor = document.getElementById('loginusername').style.backgroundColor;
	}
	document.getElementById('loginsave_password').onclick = function() {
		check_password_save_type();
	}
	addLoadEvent(check_password_save_type());
}

