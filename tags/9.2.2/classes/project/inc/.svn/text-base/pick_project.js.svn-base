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
}
function selectProject(projectid, projectname) {
	if (parent && parent.selectProject) {
		/* If the parent module wo called this function is Email, do not close the popup window. */
		/* allow user multiple project selection */
		if(parent.document.getElementById('mod')!=null && parent.document.getElementById('mod').value=='email') {			if(projectid>0) {
				document.getElementById('addproject').style.display = "";
				document.getElementById('addproject').innerHTML = "";
				document.getElementById('addproject').innerHTML = "<br/>"+projectname+" Project Added";
				parent.selectProject(projectid, projectname);
			} else {
				parent.selectProject(projectid, projectname);
				setTimeout('closepopup();',20);
			}
		} else {
			parent.selectProject(projectid, projectname);
			setTimeout('closepopup();',20);
		}
	}
}
function blader(page) {
	document.getElementById('start').value = page;
	submit_data();
}
