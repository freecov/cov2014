function search_showall() {
	document.getElementById('searchkey').value = '*';
	setTimeout("document.getElementById('projectsearch').submit();", 100);
}
function focusSearchKey() {
	if (document.getElementById('searchkey')) {
		document.getElementById('searchkey').focus();
	}
}
addLoadEvent(focusSearchKey);
