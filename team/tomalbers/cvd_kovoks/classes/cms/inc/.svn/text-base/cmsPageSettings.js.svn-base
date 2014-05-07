function cmsCheckPopupOptions() {
	if (document.getElementById('cmspageRedirectPopup').checked == true) {
		document.getElementById('popup_options').style.display = '';
	} else {
		document.getElementById('popup_options').style.display = 'none';
	}
}
function cmsCheckSearchOptions() {
	if (document.getElementById('cmssearch_override').checked == true) {
		for (i=1;i<=4;i++) {
			document.getElementById('search'+i).style.display = '';
		}
	} else {
		for (i=1;i<=4;i++) {
			document.getElementById('search'+i).style.display = 'none';
		}
	}
}
addLoadEvent(cmsCheckPopupOptions());
addLoadEvent(cmsCheckSearchOptions());

document.getElementById('cmspageRedirectPopup').onchange = function() {
	cmsCheckPopupOptions();
}
document.getElementById('cmssearch_override').onchange = function() {
	cmsCheckSearchOptions();
}