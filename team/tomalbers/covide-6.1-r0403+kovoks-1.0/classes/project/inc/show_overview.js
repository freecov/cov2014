function search_showall() {
	document.getElementById('searchkey').value = '*';
	document.getElementById('projectsearch').submit();
}
function focusSearchKey() {
	if (document.getElementById('searchkey')) {
		document.getElementById('searchkey').focus();
	}
}
addLoadEvent(focusSearchKey);
