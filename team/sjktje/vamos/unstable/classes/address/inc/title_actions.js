function edit_title(id) {
	url = 'index.php?mod=address&action=editTitles&edit_id='+id;
	popup(url, 'edit_title', 700, 550, 1);
}

function del_title(id) {
	if (confirm(gettext("Are you sure you want to delete this title?"))) {
		uri = 'index.php?mod=address&action=removeTitles&id='+id;
		loadXML(uri);
		document.location.href=document.location.href;
	}
}
function save_title() {
	document.getElementById('titleedit').submit();
}
