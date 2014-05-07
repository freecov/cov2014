function handleClassFocus() {
	void(0);
}
function scanSpecialCharacters() {
	void(0);
}
function submitSupportCall() {
	var ok = 1;
	if (ok == 1 && !document.getElementById('supportname').value) {
		alert(document.getElementById('alertname').value);
		ok = 0;
	}
	if (ok == 1 && !document.getElementById('supportemail').value) {
		alert(document.getElementById('alertemail').value);
		ok = 0;
	}
	if (ok == 1 && !document.getElementById('supportdescription').value) {
		alert(document.getElementById('alertdescription').value);
		ok = 0;
	}
	if (ok == 1) {
		document.getElementById('supportform').submit();
	}
}