function save_activity(id) {
	document.getElementById('activity_'+id).submit();
}

function remove_activity(id) {
	if (confirm(gettext("Are you sure you want to delete this activity?"))) {
		document.getElementById('activity_'+id).subaction.value='delete';
		document.getElementById('activity_'+id).submit();
	}
}
