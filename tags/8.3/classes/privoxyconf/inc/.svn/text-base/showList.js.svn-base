function editSite(data) {
	var s = Prompt.show(gettext("Enter website address (without the http://")+' :');
	if (s) {
		document.location.href='index.php?mod=privoxyconf&action=edit&newdata='+s+'&olddata='+data;
	}
}

function deleteSite(data) {
	if (confirm(gettext("Are you sure you want to remove site ")+data)==true) {
		document.location.href='index.php?mod=privoxyconf&action=delete&site='+data;
	}
}

function addSite() {
	var s = Prompt.show(gettext("Enter website address (without the http://")+' :');
	if (s) {
		document.location.href='index.php?mod=privoxyconf&action=add&site='+s;
	}
}
