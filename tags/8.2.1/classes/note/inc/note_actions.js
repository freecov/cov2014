function note_new() {
	note_edit(0);
}

function note_reply(id) {
	url = 'index.php?mod=note&action=reply&id='+id;
	popup(url, 'notereply', 820, 500, 1);
}
function note_reply_single(id) {
	url = 'index.php?mod=note&action=reply_single&id='+id;
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
	url = 'index.php?mod=note&action=edit&id='+id;
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
	if (document.getElementById('noteid')) {
		var noteid = document.getElementById('noteid').value;
		var url = 'index.php?mod=note&action=storeaddressid&address_id='+id+'&noteid='+noteid;
		loadXML(url);
	} else {
		document.getElementById('searchaddress_id').value = id;
		document.getElementById('searchrel').innerHTML = name;
	}
}

function reload_page() {
	var noteid = document.getElementById('noteid').value;
	url = 'index.php?mod=note&action=message&msg_id='+noteid;
	document.location.href = url;
}

function pickProject() {
	var address_id = document.getElementById('addressid').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function selectProject(id, name) {
	if (document.getElementById('noteid')) {
		var noteid = document.getElementById('noteid').value;
		var url = 'index.php?mod=note&action=storeprojectid&project_id='+id+'&noteid='+noteid;
		loadXML(url);
	}
}

