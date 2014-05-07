function reg_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}
	var activity = document.getElementById('regitemactivity_id');
	if (activity.options[activity.selectedIndex].value != 0) {
		document.getElementById('reginput').submit();
	} else {
		alert(gettext("no activity specified"));
	}
}

function selectProject(id, projectname) {
	document.getElementById('regitemproject_id').value = id;
	document.getElementById('searchproject').innerHTML = projectname;
}

function pickProject() {
	var address_id = document.getElementById('regitemaddress_id').value;
	popup('?mod=project&action=searchproject&actief=1&deb='+address_id, 'searchproject', 0, 0, 1);
}

