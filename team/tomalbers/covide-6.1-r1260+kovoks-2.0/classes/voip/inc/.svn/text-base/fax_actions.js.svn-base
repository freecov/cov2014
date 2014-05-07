function delete_fax(id) {
	url = 'index.php?mod=voip&action=deletefax&faxid='+id;
	if (confirm(gettext("Weet u zeker dat u deze fax wilt verwijderen?"))) {
		document.location.href = url;
	}
}

function view_fax(id) {
	url = 'index.php?mod=voip&action=viewfax&faxid='+id
	document.location.href = url;
}

function save_fax(id) {
	popup('?mod=filesys&subaction=save_fax&ids='+id, 'faxes', 750, 550, 1);
}

function preview_fax(id) {
	popup('?mod=voip&action=previewfax&faxid='+id, 'faxes');
}

function alter_relation(faxid) {
	document.getElementById('alterfax').faxid.value = faxid;
	popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);
}

function selectRel(address_id, address_name) {
	document.getElementById('alterfax').address_id.value = address_id;
	var fax_id = document.getElementById('alterfax').faxid.value;
	loadXML('index.php?mod=voip&action=alterfax&faxid='+fax_id+'&address_id='+address_id);
}

function reload_page() {
	document.location.href = 'index.php?mod=voip&action=faxlist';
}
