function pagePrint(page) {
	var el = document.getElementById('print_container');
	var ifr = '';
	ifr = ifr.concat('<ifr', 'ame src="/text/', page, '&amp;print=1">', '<\/ifr', 'ame>');
	el.innerHTML = ifr;
}
function pageHistory() {
	history.go(-1);
}
function pageText(page) {
	location.href='/text/'+page;
}

function pageSitemapText() {
	location.href='/sitemap_plain.htm';
}
function cmsEdit(id) {
	popup('?mod=cms&syncweb=1&action=editpage&id='+id, 'cmseditor', 990, 680, 1);
}
function openGalleryItem(id, descr) {
	var galleryitem = popup('showcms.php?html=1&size=medium&galleryid='+id+'&description='+descr, 'galleryitem', 990, 680, 1);
}
function iframeGalleryItem(id, descr) {
	var uri = 'showcms.php?html=1&size=medium&galleryid='+id+'&description='+descr;
	var iframe = document.getElementById('galleryframe');
	if (iframe.contentDocument) {
		document.getElementById('galleryframe').contentDocument.location.href = uri;
	} else {
		document.frames['galleryframe'].location.href = uri;
	}
}
function blogAdmin() {
	popup('/site.php?mode=blogadmin', 'mvblog', 990, 680, 0);
}
function forum_resize_frame() {
	var iframe = document.getElementById('iframe');
	if (iframe.contentDocument) {
		iframe.style.height = iframe.contentDocument.body.scrollHeight+50;
	} else {
		iframe.style.height = document.frames['iframe'].document.body.scrollHeight + 60;
	}
}
