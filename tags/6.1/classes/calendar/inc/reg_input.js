function reg_save() {
	document.getElementById('reginput').submit();
}

function selectProject(id, projectname) {
	document.getElementById('regitemproject_id').value = id;
	document.getElementById('searchproject').innerHTML = projectname;
}

function pickProject() {
	var address_id = document.getElementById('regitemaddress_id').value;
	popup('?mod=project&action=searchproject&actief=1&deb='+address_id, 'searchproject', 0, 0, 1);
}

