function remove_issue(id) {
	if (confirm(gettext("Are you sure you want to remove this support call?"))) {
		url = 'index.php?mod=support&action=remove_external_item&xml=1&id='+id;
		loadXML(url);
	}
}

function reload_doc() {
	document.location.href = document.location.href;
}

function forward_issue(id, type, address_id) {
	/*
	if (type == 3 || type == 2) {
		//issue
		popup('?mod=support&action=edit&id=0&support_id='+id);
	} else {
		//note
		popup('?mod=note&action=edit&id=0&support_id='+id+'&address_id='+address_id);
	}
	*/
	popup('?mod=support&action=edit&id=0&support_id='+id);
}
