function calendaritem_edit(id, user, datemask) {
	if (user) {
		var url = 'index.php?mod=calendar&action=edit&id='+id+'&user='+user;
	} else {
		var url = 'index.php?mod=calendar&action=edit&id='+id;
	}
	if (datemask) {
		var url = url+'&datemask='+datemask;
	}
	popup(url, 'calendaredit', 800, 650, 1);
}

function calendaritem_remove(id) {
	document.getElementById('action').value = 'delete';
	document.getElementById('id').value = id;
	if (confirm(gettext("verwijder agendapunt")+' ?')) {
		document.getElementById('calendarform').submit();
	}
}

function calendaritem_reg(id,userid,timestamp) {
	var url = 'index.php?mod=calendar&action=reg_input&id='+id+'&timestamp='+timestamp;
	popup(url, 'hourreginput', 0, 0, 0);
}

function todos_print() {
	var url = 'index.php?mod=todo&print=1';
	popup(url, 'printtodo', 0, 0, 1, 0);
}

function toonInfo(id) {
	loadXML('index.php?mod=calendar&action=show_info&id='+id);
}

function date_jump() {
	var day = document.getElementById('search_day').value;
	var month = document.getElementById('search_month').value;
	var year = document.getElementById('search_year').value;
	var extrauser = document.getElementById('extrauser').value;
	var url = 'index.php?mod=calendar';
	url = url.concat('&day=', day, '&month=', month, '&year=', year, '&extrauser=', extrauser);
	document.location.href = url;
}

function search_jump() {
	var searchkey = document.getElementById('search_term').value;
	var extrauser = document.getElementById('extrauser').value;
	var url = 'index.php?mod=calendar&action=search&searchkey='+searchkey+'&extrauser='+extrauser;
	document.location.href = url;
}

function eraseSearchTerm() {
	if (document.getElementById('search_term').value = gettext('zoekwoord')) {
		document.getElementById('search_term').value = '';
	}
}

if (document.getElementById('search_term')) {
	document.getElementById('search_term').onclick = function() { eraseSearchTerm(); }
}

