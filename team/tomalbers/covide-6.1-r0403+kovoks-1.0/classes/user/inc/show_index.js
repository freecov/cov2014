/* admin function. show info about selected active user */
function user_edit() {
	document.getElementById('userselect').submit();
}

/* Activate selected non-active user */
function user_activate() {
	userid = document.getElementById('nonact').value;
	if (userid) {
		if (confirm(gettext("Weet u zeker dat u deze gebruiker wil activeren?"))) {
			url = 'index.php?mod=user&action=activate_xml&user_id='+userid;
			loadXML(url);
		}
	} else {
		alert(gettext("U moet eerst een niet actieve gebruiker selecteren."));
	}
}

/* deactivate selected active user */
function user_deactivate() {
	userid = document.getElementById('act').value;
	if (userid) {
		if (confirm(gettext("Weet u zeker dat u deze gebruiker wil deactiveren?"))) {
			url = 'index.php?mod=user&action=deactivate_xml&user_id='+userid;
			loadXML(url);
		}
	} else {
		alert(gettext("U moet eerst een actieve gebruiker selecteren."));
	}
}

/* refresh the page to get the select boxes right. Will be result of (de)activate function */
function refresh_page() {
	document.location.href=document.location.href;
}
