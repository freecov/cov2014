function retrieve_userlist() {
	var users = new Array();
	var users_selected = new Array();

	var sel = opener.document.getElementById(document.getElementById('object').value);
	if (sel.multiple == true) {
		document.getElementById('multiple') = 1;
	}
	for (i=0;i<sel.length;i++) {
		users[i]=sel[i].value;
		if (sel[i].selected == true) {
			users_selected[i]=users[i];
		}
	}
	document.getElementById('users').value = users.join(',');
	document.getElementById('users_selected').value = users_selected.join(',').replace(/,{1,}/g,',').replace(/(^,)|(,$)/g,'');
	document.getElementById('frm').submit();
}