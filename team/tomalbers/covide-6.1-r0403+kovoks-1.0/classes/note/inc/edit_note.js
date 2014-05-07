function note_save() {

	if (window.sync_editor_mini) {
		sync_editor_mini();
	}

	if (!document.getElementById('noteto').value) {
		alert(gettext("Geen ontvanger gekozen"));
	} else {
		document.getElementById('action').value = 'store';
		document.getElementById('noteinput').submit();
	}
}

function selectRel(id, relname) {
	document.getElementById('noteaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}

function selectProject(id, relname) {
	document.getElementById('noteproject_id').value = id;
	document.getElementById('searchproject').innerHTML = relname;
}
