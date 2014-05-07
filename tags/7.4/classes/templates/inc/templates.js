function blader(start) {
	document.getElementById('velden').start.value = start;
	document.getElementById('velden').submit();
}
function save() {
	sync_editor_contents();
	document.getElementById('velden').submit();
}
function init_address_selection() {
	opener.document.getElementById('tplids').value = document.getElementById('ids').value;
	//opener.document.getElementById('address_view').innerHTML = document.getElementById('names').value;
	opener.document.getElementById('tplclassification').value = document.getElementById('classification').value;
	opener.document.getElementById('tplnegative_classification').value = document.getElementById('negative_classification').value;
	opener.document.getElementById('tpland_or').value = document.getElementById('and_or').value;
	if (document.getElementById('addresstype').value == 'bcards') {
		opener.document.getElementById('tpladdress_businesscard_id').value = 1;
	}	else if (document.getElementById('addresstype').value == 'relations') {
		opener.document.getElementById('tpladdress_businesscard_id').value = 0;
	} else {
		opener.document.getElementById('tpladdress_businesscard_id').value = 2;
	}
	opener.document.getElementById('velden').submit();
	window.close();
}

function selectRel(id, str) {
	document.getElementById('tplids').value = id;
	document.getElementById('address_view').innerHTML = gettext("by address")+': '+ str;
	document.getElementById('tplclassification').value = '';
	document.getElementById('tplnegative_classification').value = '';
	document.getElementById('tpland_or').value = '';
	document.getElementById('tpladdress_businesscard_id').value = 0;
}

function printTemplate(pdf) {
	sync_editor_contents();
	document.getElementById('pdf').value = pdf;
	document.getElementById('dl').value = pdf;
	document.getElementById('velden').action.value = 'print';
	if (pdf != 1) {
		document.getElementById('velden').target = '_new';
	}
	document.getElementById('velden').submit();

	document.getElementById('velden').action.value = 'save';
	document.getElementById('velden').target = '_self';
}

function delfile() {
	document.getElementById('velden').action.value = 'del_file';
	document.getElementById('velden').submit();
}
