function save_meeting(meeting_id, meeting_name) {
	if (opener && opener.document.getElementById('appointmentdimdim_meeting_id')) {
		opener.document.getElementById('appointmentdimdim_meeting_id').value = meeting_id;
		opener.document.getElementById('meeting_name').innerHTML = meeting_name;
		opener.document.getElementById('dimdim_icon').style.visibility = 'hidden';
		opener.document.getElementById('dimdim_icon_delete').style.visibility = 'visible';
		setTimeout('window.close();', 100);
	}
}