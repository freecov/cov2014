function to_note(id) {
	url = 'index.php?mod=note&action=message&msg_id='+id;
	opener.location.href = url;
}

function to_email(id) {
	url = 'index.php?mod=email&action=open&id='+id;
	opener.location.href = url;
}

function to_calendar(id) {
	loadXML('index.php?mod=calendar&action=show_info&id='+id);
}
