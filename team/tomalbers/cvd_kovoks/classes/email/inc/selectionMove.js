/* save a new selected relation */
function selectRel(id) {
	document.getElementById('mailaddress_id').value = id;
	document.getElementById('velden').submit();
}

function selectProject(id) {
	document.getElementById('mailproject_id').value = id;
	document.getElementById('velden').submit();
}


function selection_save() {
	document.getElementById('action').value = 'multiple_move';
	document.getElementById('velden').submit();
}