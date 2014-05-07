function save_project() {
	document.getElementById('projectedit').submit();
}

function delete_project() {
	if (confirm(gettext('Wilt u dit project verwijderen?'))==true) {
		document.getElementById('action').value = 'delete_project';
		document.getElementById('projectedit').submit();
	}
}

function selectRel(id, relname) {
	document.getElementById('projectaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}


function extUpdateActivities() {
	var projectid = document.getElementById('projectid').value;
	var ret = loadXMLContent('?mod=projectext&action=dynamicFields&output_buffer=1&activity_id=' + document.getElementById('extactivity').value + '&project_id=' + projectid);
	var el = document.getElementById('project_extrafields').innerHTML = ret;

}
if (document.getElementById('extactivity')) {
	document.getElementById('extactivity').onchange = function() { extUpdateActivities(); }
	addLoadEvent( extUpdateActivities() );
}

function mergeFile(file) {
	document.getElementById('file_name').value = file;
	document.getElementById('velden').submit();
}
