function selectRel(id) {
	if (arguments[1]) {
		var str = arguments[1];
	} else {
		var str = '';
	}
	if (id < 0) {
		id = 0;
	}
	document.getElementById('filteraddress_id').value = id;
	document.getElementById('layer_relation').innerHTML = str;
}

function pickProject() {
	var address_id = document.getElementById('filteraddress_id').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function selectProject(id, str) {
	if (id < 0) {
		id = 0;
	}
	document.getElementById('filterproject_id').value = id;
	document.getElementById('layer_project').innerHTML = str;
}
