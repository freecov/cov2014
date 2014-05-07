function newVoipCall() {
	var phonenr = Prompt.show(gettext("Number to call") + ": ");
	if (phonenr) {
		phonenr = phonenr.replace(/[^0-9]/g, '');
		if (phonenr) {
			loadXML('index.php?mod=voip&action=call&number='+phonenr);
		}
	}
}
