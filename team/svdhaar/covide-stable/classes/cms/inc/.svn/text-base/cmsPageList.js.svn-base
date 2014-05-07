function blader(page) {
	document.getElementById('start').value = page;
	document.getElementById('velden').submit();
}
function cmsPreview(id, webroot) {
	if (parent.document.getElementById('f_href')) {
		parent.document.getElementById('f_href').value = webroot + 'page/'+id;
		if (parent.onPreview)
			parent.onPreview();
	} else if (parent.document.getElementById('f_url')) {
		parent.document.getElementById('f_url').value = webroot + 'page/'+id;
		parent.onPreview();
	} else if (parent.document.getElementById('cmspageRedirect')) {
		parent.document.getElementById('cmspageRedirect').value = '/page/'+id;
		closepopup();
	} else if (parent.document.getElementById('cmscms_name')) {
		parent.pageValue(id);
		closepopup();
	}
	void(0);
}
addLoadEvent(document.getElementById('search').focus());
