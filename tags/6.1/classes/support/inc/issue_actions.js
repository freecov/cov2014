function show_support_item(id) {
	url = 'index.php?mod=support&action=showitem&id='+id;
	popup(url, 'issueinfo', 0, 0, 1);
}

function edit_support(id) {
	url = 'index.php?mod=support&action=edit&id='+id;
	popup(url, 'issueedit', 0, 0, 0);
}

function save_support() {
	document.getElementById('issueedit').submit();
}

function selectRel(id, relname) {
	document.getElementById('issueaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}

function selectProject(id, projectname) {
	document.getElementById('issueproject_id').value = id;
	document.getElementById('searchproject').innerHTML = projectname;
}

function search () {
	document.getElementById('searchissue').submit();
}
