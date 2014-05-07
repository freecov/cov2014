function remove_feed(id) {
	if (id) {
		if (confirm(gettext("Weet u zeker dat u deze feed wilt verwijderen?"))) {
			url = 'index.php?mod=rss&action=removeFeed&id='+id;
			document.location.href=url;
		}
	}
}
