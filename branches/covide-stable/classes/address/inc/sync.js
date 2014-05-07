function toggleSync(address_id, address_table, action) {
	if (document.getElementById('funambol_user')) {
		var user = document.getElementById('funambol_user').value;
	} else {
		var user = 0;
	}
	url = 'index.php?mod=address&action=togglesync&identifier='+address_table+'&address_id='+address_id+'&toggleaction='+action+'&funambol_user='+user;
	if (address_id.match(/^sel_/g)) {
		popup('index.php?mod=address&action=togglesync&subact=notify', 'sync_window', 400, 150, 1);
		setTimeout("popup('" + url + "', 'sync_window', 300, 150, 1);", 3500);
	} else {
		loadXML(url);
	}
}

function toggleGoogleSync(bc_id, action) {
	if (document.getElementById('funambol_user')) {
		var user = document.getElementById('funambol_user').value;
	} else {
		var user = 0;
	}
	url = 'index.php?mod=address&action=togglegooglesync&bc_id='+bc_id+'&toggleaction='+action+'&user_id='+user;
	if (bc_id.match(/^sel_/g)) {
		popup('index.php?mod=address&action=togglegooglesync&subact=notify', 'sync_window', 400, 150, 1);
		setTimeout("popup('" + url + "', 'sync_window', 300, 150, 1);", 3500);
	} else {
		$('#infowait').show();
		loadXML(url);
	}
}
