function user_save() {
	var username = document.getElementById('userusername').value;
	var passwd1  = document.getElementById('uservpassword').value;
	var passwd2  = document.getElementById('uservpassword1').value;
	var empnum   = document.getElementById('userpers_nr').value;
	var userid   = document.getElementById('userid').value;
	var url = 'index.php?mod=user&action=useredit_check&username='+username+'&passwd1='+passwd1+'&passwd2='+passwd2+'&empnum='+empnum+'&userid='+userid;
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

	document.getElementById('useredit').submit();
}


function handle_error(type) {
	var errordiff = document.getElementById('errordiv');
	if (type == 1) {
		errordiff.style.border='2px dotted red';
		errordiff.innerHTML=gettext("Error: Passwords don't match");
		errordiff.style.visibility='visible';
	} else if (type == 2) {
		errordiff.style.border='2px dotted red';
		errordiff.innerHTML=gettext("Error: The employee number already exists.");
		errordiff.style.visibility='visible';
	} else if (type == 3) {
		errordiff.style.border='2px dotted red';
		errordiff.innerHTML=gettext("Error: Data missing.");
		errordiff.style.visibility='visible';
	} else if (type == 4) {
		errordiff.style.border='2px dotted red';
		errordiff.innerHTML=gettext("Error: The password has to be at least 6 characters long.");
		errordiff.style.visibility='visible';
	} else {
		errordiff.style.border='2px dotted red';
		errordiff.innerHTML=gettext("Error: Data missing.");
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
		activebox.checked = false;
		user_save_exec();
	}
}
