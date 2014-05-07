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
	} else if (opener.document.getElementById('cmspageRedirect')) {
		opener.document.getElementById('cmspageRedirect').value = '/page/'+id;
		window.close();
	} else if (opener.document.getElementById('cmscms_name')) {
		opener.pageValue(id);
		window.close();
	}
	void(0);
}
addLoadEvent(document.getElementById('search').focus());