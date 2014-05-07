function note_edit(id) {
	url = 'index.php?mod=note&action=edit&id='+id;
	document.location.href = url;
}

function close_window() {
	opener.document.location.href = opener.document.location.href;
	window.close();
}
