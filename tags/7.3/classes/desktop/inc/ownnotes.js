function save_notes() {
	sync_editor_contents();
	if (document.getElementById('editownnotes').onsubmit) {
		document.getElementById('editownnotes').onsubmit();
	}
	document.getElementById('editownnotes').submit();
}
