function toggle_hours(id) {
	url = 'index.php?mod=project&action=toggleHours&id='+id;
	loadXML(url);
}

function delete_hours(id) {
	if (confirm(gettext("Are you sure you want to delete this item?"))) {
		url = 'index.php?mod=calendar&action=reg_delete_xml&id='+id;
		loadXML(url);
	}
}
function hours_refresh_page() {
	document.location.href = document.location.href;
}

function toggle_active(id, master) {
	url = 'index.php?mod=project&action=toggleActive&id='+id+'&master='+master;
	loadXML(url);
}

function print_active(html) {
	span = document.getElementById('pr_is_active');
	span.innerHTML = html;
}

if (document.getElementById('projectactions')) {
	document.getElementById('projectactions').onchange = function() {
		eval(document.getElementById('projectactions').value);
		setTimeout("document.getElementById('projectactions').value = 'void(0);';", 200);
	}
}
