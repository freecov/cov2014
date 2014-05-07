function sales_save() {
	document.getElementById('velden').action.value = 'save';
	document.getElementById('velden').submit();
}

function selectRel(id, str) {
	document.getElementById('salesaddress_id').value = id;
	document.getElementById('layer_relation').innerHTML = str;
}
function selectProject(id, str) {
	document.getElementById('salesproject_id').value = id;
	document.getElementById('layer_projectname').innerHTML = str;
}
function pickProject() {
	var address_id = document.getElementById('salesproject_id').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}