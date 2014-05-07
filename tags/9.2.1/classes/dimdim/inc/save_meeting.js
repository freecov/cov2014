function save_meeting(meeting_id, meeting_name) {
	//handle popups new style vs oldstyle
	var el = '';
	var popupstyle = '';
	if (opener) {
		popupstyle = 'old';
		if (opener.document.getElementById('appointmentdimdim_meeting_id')) {
			el = opener.document;
		}
	} else {
		popupstyle = 'new';
		if (parent.document.getElementById('appointmentdimdim_meeting_id')) {
			el = parent.document;
		}
	}
	if (el != '') {
		el.getElementById('appointmentdimdim_meeting_id').value = meeting_id;
		el.getElementById('meeting_name').innerHTML = meeting_name;
		el.getElementById('dimdim_icon').style.visibility = 'hidden';
		el.getElementById('dimdim_icon_delete').style.visibility = 'visible';
	}
	if (popupstyle == 'old') {
		setTimeout('window.close();', 100);
	} else {
		setTimeout('closepopup();', 100);
	}
}
