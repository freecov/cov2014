function cla_edit(id) {
	url = 'index.php?mod=classification&action=cla_edit&id='+id
	popup(url, 'cla_edit', 0, 0, 1);
}

function cla_remove(id) {
	if (confirm(gettext('Delete classification?'))) {
		document.getElementById('action').value = 'remove';
		document.getElementById('id').value = id;
		document.getElementById('claform').submit();
	}
}

function cla_save() {
	if (document.getElementById('claedit')) {
		document.getElementById('claedit').submit();
	}
}
