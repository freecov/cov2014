function save_activity(id) {
	document.getElementById('activity_'+id).submit();
}

function remove_activity(id) {
	if (confirm(gettext("Weet u zeker dat u deze activiteit wilt verwijderen?"))) {
		document.getElementById('activity_'+id).subaction.value='delete';
		document.getElementById('activity_'+id).submit();
	}
}
