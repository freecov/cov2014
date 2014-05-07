function edit_title(id, cid) {
	url = 'index.php?mod=address&action=editTitles&edit_id='+id+'&cid='+cid;
	popup(url, 'edit_title', 700, 550, 1);
}

function del_title(id, cid) {
	if (confirm(gettext("Are you sure you want to delete this title?"))) {
		uri = 'index.php?mod=address&action=removeTitles&id='+id+'&cid='+cid;
		loadXML(uri);
		document.location.href=document.location.href;
	}
}
function save_it() {
	document.getElementById('titleedit').submit();
}
