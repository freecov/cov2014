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

if (document.getElementById('projectactions')) {
	document.getElementById('projectactions').onchange = function() {
		eval(document.getElementById('projectactions').value);
		setTimeout("document.getElementById('projectactions').value = 'void(0);';", 200);
	}
}

