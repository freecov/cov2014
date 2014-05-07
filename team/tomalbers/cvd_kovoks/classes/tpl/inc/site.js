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