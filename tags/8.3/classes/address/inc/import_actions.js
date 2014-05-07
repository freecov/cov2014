function import_to_step2() {
	document.getElementById('import').submit();
}

function import_to_step3() {
	/* TODO: add some check to make sure at least one field 
		is candidate for companyname */
	document.getElementById('import').submit();
}

function to_vCard_process() {
	document.getElementById('vcard').submit();
}

function selectRel(id, relname) {
	document.getElementById('vcardaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}

function vCardSave() {
	if (document.getElementById('vcardaddress_id').value == 0) {
		alert(gettext("Please pick a relation"));
	} else {
		document.getElementById('importVcard_process').submit();
	}
}