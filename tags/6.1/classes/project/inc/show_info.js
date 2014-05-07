/* function to run when changing the contact/customer information */
function selectRel(id, relname) {
	/* get projectid from page */
	var project_id = document.getElementById('project_id').value;
	var master = document.getElementById('master').value;
	/* store changes in the db */
	/* this will come back with js function reload_page(); */
	url = 'index.php?mod=project&action=updaterelxml&master='+master+'&project_id='+project_id+'&address_id='+id;
	loadXML(url);
}

/* function to reload page */
function reload_page() {
	document.location.href = document.location.href;
}
