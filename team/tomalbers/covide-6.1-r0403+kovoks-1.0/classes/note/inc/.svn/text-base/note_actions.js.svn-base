function note_new() {
	note_edit(0);
}

function note_reply(id) {
	url = 'index.php?mod=note&action=reply&id='+id;
	popup(url, 'notereply', 820, 500, 1);
}

function note_forward(id) {
	url = 'index.php?mod=note&action=forward&id='+id;
	popup(url, 'noteforward', 820, 500, 1);
}

function note_plan(id) {
	url = 'index.php?mod=todo&action=edit_todo&noteid='+id;
	document.location.href = url;
}

function note_print(id) {
	url = 'index.php?mod=note&action=print&id='+id;
	popup(url, 'noteprint', 820, 500, 1);
}

function note_flagdone(id) {
	url = 'index.php?mod=note&action=flagdone&id='+id;
	loadXML(url);
}

function note_edit(id) {
	url = 'index.php?mod=note&action=edit&id=0';
	popup(url, 'noteedit', 920, 500, 1);
}

function zoek() {
	document.getElementById('zoeken').submit();
}

function pick_rel(id) {
	url = 'index.php?mod=address&action=searchRel';
	popup(url, 'noterel', 900, 700, 1);
}

function selectRel(id, name) {
	var noteid = document.getElementById('noteid').value;
	var url = 'index.php?mod=note&action=storeaddressid&address_id='+id+'&noteid='+noteid;
	loadXML(url);
}

function reload_page() {
	var noteid = document.getElementById('noteid').value;
	url = 'index.php?mod=note&action=message&msg_id='+noteid;
	document.location.href = url;
}
