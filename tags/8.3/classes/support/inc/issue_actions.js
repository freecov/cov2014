function show_support_item(id) {
	url = 'index.php?mod=support&action=showitem&id='+id;
	popup(url, 'issueinfo', 600, 500, 1);
}

function register_support(id) {
	url = 'index.php?mod=calendar&action=edit&id=0&supportid='+id;
	popup(url, 'issueregister', 800, 500, 1);
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
	updateBcards();
}

function selectProject(id, projectname) {
	document.getElementById('issueproject_id').value = id;
	document.getElementById('searchproject').innerHTML = projectname;
}

function search () {
	document.getElementById('searchissue').submit();
}

function pickProject() {
	var address_id = document.getElementById('issueaddress_id').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function updateBcards() {
	var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + document.getElementById('issueaddress_id').value + '&current=' + document.getElementById('issuebcard_selected').value);
	document.getElementById('issue_bcard_layer').innerHTML = ret;
}

