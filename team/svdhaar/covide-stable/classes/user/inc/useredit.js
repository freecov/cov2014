function user_save() {
	var username = document.getElementById('userusername').value;
	/* passwords can contain characters that are not url friendly */
	var passwd1    = escape(document.getElementById('uservpassword').value);
	var passwd2    = escape(document.getElementById('uservpassword1').value);
	var empnum     = document.getElementById('userpers_nr').value;
	var userid     = document.getElementById('userid').value;
	var mailserver = '';
	var mailproto  = 0;
	var mailuser   = '';
	var mailpass   = '';
	var mailsettingscount = 0;
	if (document.getElementById('usermail_server')) {
		mailserver = document.getElementById('usermail_server').value;
		mailsettingscount++;
	}
	if (document.getElementById('usermail_imap')) {
		mailproto  = document.getElementById('usermail_imap').value;
		mailsettingscount++;
	}
	if (document.getElementById('usermail_user_id')) {
		mailuser   = document.getElementById('usermail_user_id').value;
		mailsettingscount++;
	}
	if (document.getElementById('uservmail_password')) {
		mailpass   = escape(document.getElementById('uservmail_password').value);
		mailsettingscount++;
	}
	var url = 'index.php?mod=user&action=useredit_check&username='+username+'&passwd1='+passwd1+'&passwd2='+passwd2+'&empnum='+empnum+'&userid='+userid+'&mailserver='+mailserver+'&mailproto='+mailproto+'&mailuser='+mailuser+'&mailpass='+mailpass;
	if (mailsettingscount == 4) {
		handle_error(6);
	}
	loadXML(url);
}

function user_save_exec() {
	/* copy over mail password box */
	if (document.getElementById('usermail_password')) {
		document.getElementById('usermail_password').value = document.getElementById('uservmail_password').value;
		document.getElementById('uservmail_password').value = '';
	}
	/* copy over user password boxes */
	document.getElementById('userpassword').value   = document.getElementById('uservpassword').value;
	document.getElementById('userpassword1').value  = document.getElementById('uservpassword1').value;
	document.getElementById('uservpassword').value  = '';
	document.getElementById('uservpassword1').value = '';

	/* google pwd */
	document.getElementById('usergoogle_password').value   = document.getElementById('uservgoogle_password').value;
	document.getElementById('uservgoogle_password').value = '';

	document.getElementById('useredit').submit();
}


function handle_error(type) {
	var errordiff = document.getElementById('errordiv');
	switch (type) {
		case 1:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: Passwords don't match");
			errordiff.style.visibility='visible';
			break;
		case 2:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: The employee number already exists.");
			errordiff.style.visibility='visible';
			break;
		case 3:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: Employee number missing.");
			errordiff.style.visibility='visible';
			break;
		case 4:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: The password has to be at least 6 characters long.");
			errordiff.style.visibility='visible';
			break;
		case 5:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: The password needs to be alfanumeric (letters and digits or other special characters).");
			errordiff.style.visibility='visible';
			break;
		case 6:
			errordiff.style.border='2px dotted orange';
			errordiff.innerHTML=gettext("Checking mail settings. Please wait...");
			errordiff.style.visibility='visible';
			break;
		case 7:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: The mail settings are not correct (could not login with provided settings).");
			errordiff.style.visibility='visible';
			break;
		default:
			errordiff.style.border='2px dotted red';
			errordiff.innerHTML=gettext("Error: Data missing. error type "+type);
			errordiff.style.visibility='visible';
	}
}

function user_mailfetch(id) {
	url = 'index.php?mod=email&action=retrieve&user_id='+id;
	popup(url, 'mailfetch', 400, 300, 1);
}

function update_preview(themeid) {
	img = document.getElementById('themepreview');
	img.src = 'themes/previews/thumb_theme'+themeid+'.png';
}

/* attach event handler to the select all attachments checkbox */
if (document.getElementById('usercheckall')) {
	document.getElementById('usercheckall').onclick = function() {
		user_toggle_all( document.getElementById('usercheckall').checked );
	}
}
/* function: mail_toggle_all */
/*  toggle all mail checkbox items to the status set in the parameter */
function user_toggle_all(set_to_status) {
	var frm = document.getElementById('useredit');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^user\[xs_/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

function user_deactivate() {
	var activebox = document.getElementById('useris_active');
	if (activebox) {
		if (confirm(gettext("Deactivate user?"))) {
			activebox.checked = false;
			user_save_exec();
		}
	}
}
function generatePassword() {
	var p = randomPassword(8);
	document.getElementById('newpassword').innerHTML = p;
	document.getElementById('uservpassword').value = p;
	document.getElementById('uservpassword1').value = p;
}
function randomPassword(length) {
  chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  pass = "";
  for(x=0;x<length;x++) {
    i = Math.floor(Math.random() * 62);
    pass += chars.charAt(i);
  }
  return pass;
}
