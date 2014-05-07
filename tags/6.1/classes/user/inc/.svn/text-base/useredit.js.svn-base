function user_save() {
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

function user_mailfetch(id) {
	url = 'index.php?mod=email&action=retrieve&user_id='+id;
	popup(url, 'mailfetch', 400, 300, 1);
}

function update_preview(themeid) {
	img = document.getElementById('themepreview');
	img.src = 'themes/previews/thumb_theme'+themeid+'.png';
}

/* attach event handler to the select all attachments checkbox */
document.getElementById('usercheckall').onclick = function() {
	user_toggle_all( document.getElementById('usercheckall').checked );
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
