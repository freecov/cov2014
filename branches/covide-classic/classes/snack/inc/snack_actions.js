function empty_snack() {
if (confirm(gettext("remove snack items?"))) {
	window.location = 'index.php?mod=snack&empty=snacks';
	}
}
function add_snack() {
	url = 'index.php?mod=snack&action=addsnacks';
	popup(url, 'addsnacks', 820, 500, 1);
}
function save_snacks() {
	document.getElementById('snackinput').submit();
}
function save_snacks_to_db() {
	opener.location = "index.php?mod=savesnacks";
}
function close_saved_snacks() {
	opener.location = 'index.php?mod=snack';
	window.close();

}
function add_items(id) {
	url = 'index.php?mod=snack&action=additems&id=' + id;
	popup(url, 'addsnacks', 620, 250, 1);
}
function save_add_items() {
	document.getElementById('additemsform').submit();
	opener.location = 'index.php?mod=snack&action=itemlist';
	var t = setTimeout('window.close();', 100);
	opener.location.reload();
}
function del_items(id) {
if (confirm(gettext("remove snack items?"))) {
	window.location = 'index.php?mod=snack&action=itemlist&empty='+id;
	}
}
function whoHas(snack_id) {
	url = 'index.php?mod=snack&action=who_has&snack_id='+snack_id;
	popup(url, 'addsnacks', 400, 250, 1);
}
