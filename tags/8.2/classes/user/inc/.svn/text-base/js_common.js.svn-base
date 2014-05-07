function checkstate() {
	if (document.getElementById('use_ssl')) {
		var el = document.getElementById('use_ssl');
		var frm = document.getElementById('login');

		if (el.checked == true) {
			frm.action = 'https://' + document.getElementById('basepath').value;
		} else {
			frm.action = 'http://' + document.getElementById('basepath').value;
		}
	}
}

function getuserinfo() {
	var username = document.getElementById('loginusername').value;
	loadXML('index.php?mod=user&action=getUserinfoXML&username='+username);
}

function login_challenge() {
	/* get visible password and real password fields */
	var pw = document.getElementById('loginvis_password');
	var realpw = document.getElementById('loginpassword');

	/* if password is set by user  */
	if (document.getElementById('loginuse_cookie_password').value == 0) {
		/* calculate the hash md5 (challenge + md5(password) ) */
		var str = new String().concat( hex_md5( pw.value ), crypt_challenge );
		realpw.value = hex_md5(str);
	} else {
		/* if set by cookie */
		var str = new String().concat(realpw.value, crypt_challenge );
		realpw.value = hex_md5(str);
	}
	/* overwrite the user password with (*) */
	if (document.getElementById('pt_browser').checked == false) {
		pw.value = '';
	}
}

function login() {
	if (check_radius == 1) {
		/* fetch radius_auth flag for this user */
		var username = document.getElementById('loginusername').value;
		loadXML('index.php?mod=user&action=getUserinfoXML&username='+username);
	} else {
		radius_auth = 0;
	}
	if (radius_auth == 0) {
		login_challenge();

		try {
			if (document.getElementById('loginsave_password').checked == true
				&& document.getElementById('pt_browser').checked == true
				&& document.getElementById('use_ssl').checked == false) {

				var cf = confirm("Warning: "+gettext("This combination (browser remembers password / no ssl encryption) is unsafe.")+'\n'+gettext("Are you sure you want to continue?"));
			} else {
				var cf = true;
			}
		} catch(e) {
			var cf = true;
		}
	} else {
		/* get visible password and real password fields */
		var pw = document.getElementById('loginvis_password');
		var realpw = document.getElementById('loginpassword');
		/* reset password fields so no 'save password' dialog is shown */
		realpw.value = pw.value;
		pw.value = '';

		/* force ssl for radius logins */
		document.getElementById('use_ssl').checked == true;

		var cf = true;
	}
	if (cf == true) {
		try {
			document.getElementById('progressbar').style.visibility = 'visible';
			document.getElementById('login_button').style.visibility = 'hidden';
		} catch(e) {
			//null
		}
		if (document.getElementById('mobile').value == 1) {
			var uri = new String();
			try {
				if (document.getElementById('use_ssl').checked == 1) {
					uri = uri.concat('https://');
				} else {
					uri = uri.concat('http://');
				}
			} catch(e) {
				uri = uri.concat('http://');
			}
			uri = uri.concat(document.getElementById('mobile_uri').value, '/');
			uri = uri.concat('index.php?mod=user&subaction=validate&login[username]=');
			uri = uri.concat(document.getElementById('loginusername').value);
			uri = uri.concat('&login[password]=', document.getElementById('loginpassword').value);

			top.location.href = uri;
		} else {
			/* TODO: someone really fucked up password clearing earlier somewhere, 
			 *  the password was unecnrypted submitted inside the password field.
			 *  this should fix things until the issue earlier is found.
			*/
			if (document.getElementById('loginsave_password').checked == false)
				document.getElementById('loginvis_password').value = '';

			document.getElementById("login").submit();
		}
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

