
var autochecktimer;
function update_conflict(state) {
	if (state) {
		alert(gettext("Username already exists."));
		document.getElementById('action_save').style.visibility='hidden';
		document.getElementById('action_save_bottom').style.visibility='hidden';
		var focustimer = setTimeout("document.getElementById('userusername').focus();", 100);
	} else {
		document.getElementById('action_save').style.visibility='visible';
		document.getElementById('action_save_bottom').style.visibility='visible';
	}
}

var usernamechecktimer;
function checkUsername() {
	var username = document.getElementById('userusername');
	var id = document.getElementById('userid').value;

	username.value = username.value.replace(/[^0-9a-z_\- ]/gi, '');
	var uri = '?mod=user&action=usernamecheckxml&username='+username.value+'&_userid='+id;
	loadXML(uri);
}

document.getElementById('userusername').onkeydown = function() {
	clearTimeout(usernamechecktimer);
	usernamechecktimer = setTimeout('checkUsername();', 300);
}
