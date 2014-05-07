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
