function reg_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}
	if (window.updateTextArea) {
		updateTextArea('contents');
	}
	if (document.getElementById('regitemactivity_id')) {
		var activity = document.getElementById('regitemactivity_id');
		if (activity.options[activity.selectedIndex].value != 0) {
			document.getElementById('reginput').submit();
		} else {
			alert(gettext("no activity specified"));
		}
	} else {
		document.getElementById('reginput').submit();
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

if (document.getElementById('regitemcostid')) {
	document.getElementById('regitemcostid').onchange = function() {
		var el = document.getElementById('regitemcostid');
		var selectedel = el.options[el.selectedIndex].value;
		var selectedtarif = 'costtarif_'+selectedel;
		document.getElementById('regitemprice').value = document.getElementById(selectedtarif).value;
	}
}
