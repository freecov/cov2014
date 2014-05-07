function show_support_item(id) {
	url = 'index.php?mod=support&action=showitem&id='+id;
	popup(url, 'issueinfo', 600, 500, 1);
}

function edit_support(id) {
	url = 'index.php?mod=support&action=edit&id='+id;
	popup(url, 'issueedit', 900, 600, 0);
}

function save_support() {
	if (document.getElementById('issueis_solved').checked && !document.getElementById('issueuser_id').value) {
		alert(gettext("No user specified. Cannot mark item as done with no user. Please select an user first."));
	} else {
		document.getElementById('issueedit').submit();
	}
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
