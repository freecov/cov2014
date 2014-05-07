function submit_data(){
	//document.getElementById('velden').action.value = 'submit';
	document.getElementById('velden').submit();
}
function drop_filter(){
	document.getElementById('start').value = '0';
	document.getElementById('deb').value = '0';
	document.getElementById('searchinfo').value = '';
	document.getElementById('velden').submit();
}
function search(){
	//document.velden.aktie.value = 'zoeken';
	document.getElementById('velden').submit();
}
function erase_data(){
	//projectnaam = opener.document.getElementById('projectnaam');
	//projectnaam.innerHTML = '';
	window.close();
}
function selectProject(projectid, projectname) {
	if (opener && opener.selectProject) {
		opener.selectProject(projectid, projectname);
		setTimeout('window.close();',20);
	}
}
function blader(page) {
	document.getElementById('start').value = page;
	submit_data();
}