function showOnlineUsers() {
	if (arguments[0])
		var str = arguments[0];
	else
		var str = '';

	if (arguments[1])
		var mode = arguments[1];
	else
		var mode = 1;

	if (arguments[2]) {
		var channel = arguments[2];
		setTimeout('window.focus();', 200);
	} else {
		var channel = '';
	}

	var uri = '';
	uri = uri.concat(webroot, '?mod=user&action=show_online&q=', str, '&mode=', mode, '&channel=', channel);
	var code = loadXMLContent(uri);
	infoLayer(code);

	document.getElementById('searchstringonline').focus();
	exec_searchstringonline();

	if (channel != '') {
		document.getElementById('layer_all').style.display = 'none';
		document.getElementById('layer_online').style.display = 'none';
	} else {
		document.getElementById('layer_all').style.display = '';
		document.getElementById('layer_online').style.display = 'none';
	}
}
function searchUser() {
	var e = document.getElementById('searchstringonline');
	if (e)
			showOnlineUsers(e.value, '0');
}
function toggleStatus() {
	var e = document.getElementById('searchstringonline');
	showOnlineUsers('', '0');
	document.getElementById('layer_all').style.display = 'none';
	document.getElementById('layer_online').style.display = '';
}
function init_chat(uids) {
	alert('chat!')
}

var searchstringonline_timer;
function exec_searchstringonline() {
	var e = document.getElementById('searchstringonline');
	if (e) {
		e.onkeyup = function() {
			clearTimeout(searchstringonline_timer);
			searchstringonline_timer = setTimeout('searchUser();', 500);
		}
	}
}
function initPrivateChat() {
	if (arguments[0])
		var user = arguments[0];
	else
		var user = '';

	if (arguments[1])
		var channel = arguments[1];
	else
		var channel = '';

	var uri = '';
	uri = uri.concat(webroot,'?mod=chat&action=private&user=', user, '&channel=', channel);

	if (user != '') {
		var ret = loadXMLContent(uri);
		alert(gettext('The invitation is send to the user'));
	} else {
		popup('?mod=chat', 'chat_global', 710, 670, 1);
	}
	hideInfoLayer();
}