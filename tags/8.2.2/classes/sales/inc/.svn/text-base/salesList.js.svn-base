function blader(page) {
	document.getElementById('velden').start.value = page;
	document.getElementById('velden').submit();
}
function sales_delete(id) {
	document.getElementById('id').value = id;
	document.getElementById('velden').action.value = 'delete';
	document.getElementById('velden').submit();
}
function submitform() {
	document.getElementById('start').value = 0;
	document.getElementById('velden').submit();
}
function selectRel(id, str) {
	document.getElementById('searchaddress_id').value = id;
	document.getElementById('layer_relation').innerHTML = str;
}
function selectProject(id, str) {
	document.getElementById('searchproject_id').value = id;
	document.getElementById('layer_projectname').innerHTML = str;
}
function pickProject() {
	var deb = document.getElementById('searchaddress_id').value;
	popup('?mod=project&action=searchProject&deb='+deb, 'searchproject')
}
function exportSale() {
	document.getElementById('export_field').value = '1';
	document.getElementById('velden').submit();
	document.getElementById('export_field').value = '0';
}
function exportSaleXML() {
	document.getElementById('export_xml').value = '1';
	document.getElementById('velden').submit();
	document.getElementById('export_xml').value = '0';
}
