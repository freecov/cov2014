function submit_meeting() {
	document.getElementById('dimdimedit').submit();
}
/* update the email address list from the server */
function update_mail_list(ret) {
	ret = unescape(ret);
	var ret_test = ret.split(/<br[^>]*?>/g);
	if (ret_test.length > 6) {
		document.getElementById('mail_addresses').style.height = '180px';
		document.getElementById('mail_addresses').style.overflow = 'auto';
	} else {
		document.getElementById('mail_addresses').style.height = '';
		document.getElementById('mail_addresses').style.overflow = '';
	}
	document.getElementById('mail_addresses').innerHTML = ret;
}

/* xml call to update the email list */
function update_mail_list_xml(address_id) {
	var ret = loadXMLContent('?mod=calendar&action=addressemails&address_id=' + address_id);
	var t = setTimeout('update_mail_list(\''+escape(ret)+'\');', 100);
	/* update_mail_list(ret); */
}

/* update the email address list on load */
function update_mail_list_onload() {
	var addressid = document.getElementById('address_id').value;
	if (addressid) {
		update_mail_list_xml(addressid);
	} else {
		document.getElementById('mail_addresses').innerHTML = gettext("No relation selected");
	}
}