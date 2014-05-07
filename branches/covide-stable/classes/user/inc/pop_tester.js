/* test popups */
var popuptest = window.open('blank.htm', 'popuptest', "left=1600,top=1200,width=2,height=2,directories=no,location=no,menubar=no,status=no,toolbar=no,personalbar=no,resizable=no,scrollbars=no");
var tx = setTimeout('check_popup()', 500);

function alert_popupblocker() {
	var msg = gettext("This computer blocks popups, and therefor some functionality in Covide wont work correctly.");
	msg = msg.concat('\n\n', gettext("We advise you to not block popups inside Covide"), ' ',
		gettext("You can stop blocking popups for covide by clicking the yellow bar on top of this page and always allow them."));

	if (document.getElementById('popup_tester_msg')) {
		document.getElementById('popup_tester_msg').style.display = '';
	} else {
		alert(msg);
	}
}

function check_popup() {
	if (!popuptest) {
		alert_popupblocker();
	} else {
		/* in Opera the var popuptest is set even when the popup is blocked. */
		/* So let's try the close method. if it fails opera blocked the popup */
		try {
			popuptest.close();
		} catch (eOpera) {
			alert_popupblocker();
		}
	}
}
