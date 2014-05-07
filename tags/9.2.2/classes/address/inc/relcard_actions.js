function new_note(address_id, customercontact) {
	url = 'index.php?mod=note&action=edit&id=0&address_id='+address_id;
	if (customercontact) {
		url = url.concat('&is_custcont=', customercontact);
	}
	popup(url, 'noteedit', 820, 500, 1);
}

function new_support(support_id) {
	url = 'index.php?mod=support&action=edit&relation_id='+support_id;
	popup(url, 'editnewsupport', 900, 600, 1);
}

/* function to toggle customer contact items to archive */
function custcont_togglestate() {
	document.getElementById('custcont').submit();
}

function new_calitem(address_id) {
	url = 'index.php?mod=calendar&action=edit&id=0&address_id='+address_id;
	popup(url, 'calendaredit', 800, 650, 1);
}

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

function calendaritem_remove(id, user) {
	var url = 'index.php?mod=calendar&action=delete&id='+id+'&user_id='+user;
	loadXML(url);
}

function showcalitem(id, userid) {
	loadXML('index.php?mod=calendar&action=show_info&id='+id+'&user_id='+userid);
}

function mail_resize_frame() {
	var iframe = document.getElementById('turnoverinfo');
	if (iframe.contentDocument) {
		var h = iframe.contentDocument.body.scrollHeight+15;
		iframe.style.height = h + 'px';
	} else {
		iframe.style.height = document.frames['turnoverinfo'].document.body.scrollHeight + 16;
	}
}

function load_bcards(address_id, search, funambol_user) {
	var ct = loadXMLContent('?mod=address&action=getBcardsXML&address_id='+address_id+'&output=1&search='+search);
	document.getElementById('bcards_layer').innerHTML = ct;
}

/* initialize the resize on window load */
if (document.getElementById('turnoverinfo')) {
	var timer1 = setTimeout('mail_resize_frame();', 5000);
	addLoadEvent(
		function() {
			if (timer1) {
							clearTimeout(timer1);
			}
			mail_resize_frame();
		}
	);
}
