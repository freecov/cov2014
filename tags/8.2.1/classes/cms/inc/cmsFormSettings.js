function save_formsettings() {
	document.getElementById('cmsFormSettings').submit();
}

function add_usercla(clafield, id) {
	var url = 'index.php?mod=cms&action=addUserCla&number='+clafield+'&id='+id;
	loadXML(url);
}
