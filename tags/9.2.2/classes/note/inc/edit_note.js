function note_save() {

	if (window.sync_editor_mini) {
		sync_editor_mini();
	}

	if (window.updateTextArea) {
		updateTextArea('contents');
	}

	if (!document.getElementById('noteto').value) {
		alert(gettext("No recipient found"));
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

function pickProject() {
	var address_id = document.getElementById('noteaddress_id').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function note_draft() {

	if (window.sync_editor_mini) {
		sync_editor_mini();
	}

	if (window.updateTextArea) {
		updateTextArea('contents');
	}

	var is_draft = document.getElementById('noteis_draft');
	is_draft.value = 1;
	if (is_draft.value == 1) {
		document.getElementById('action').value = 'store';
		document.getElementById('noteinput').submit();
	}
}

