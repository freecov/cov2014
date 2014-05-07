function new_note(address_id, customercontact) {
	url = 'index.php?mod=note&action=edit&id=0&address_id='+address_id;
	if (customercontact) {
		url = url.concat('&is_custcont=', customercontact);
	}
	popup(url, 'noteedit', 0, 0, 1);
}

/* function to toggle customer contact items to archive */
function custcont_togglestate() {
	document.getElementById('custcont').submit();
}

function new_calitem(address_id) {
	url = 'index.php?mod=calendar&action=edit&id=0&address_id='+address_id;
	popup(url, 'calendaredit', 800, 650, 1);
}

function mail_resize_frame() {
	var iframe = document.getElementById('turnoverinfo');
	if (iframe.contentDocument) {
					iframe.style.height = iframe.contentDocument.body.scrollHeight+15;
	} else {
					iframe.style.height = document.frames['turnoverinfo'].document.body.scrollHeight + 16;
	}
}

function delete_bcard(relationcardid, cardid, addressid) {
	if (addressid == relationcardid) {
		if(confirm('This will delete the card. Are you sure you want to continue?')){
			document.location.href='index.php?mod=address&action=relcard&id='+relationcardid+'&relcardaction=cardrem&cardid='+cardid;
		}
	} else {
		alert("This businesscard belongs to another relation. You can not delete it here");
	}
}


/* initialize the resize on window load */
if (document.getElementById('turnoverinfo')) {
	var timer1 = setTimeout('mail_resize_frame();',5000);
	addLoadEvent(
		function() {
			if (timer1) {
							clearTimeout(timer1);
			}
			mail_resize_frame();
		}
	);
}
