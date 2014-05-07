if (document.getElementById('datafield_type')) {
	document.getElementById('datafield_type').onchange = function() {
		detectTypeChange();
	}
	addLoadEvent(detectTypeChange());
}
if (document.getElementById('projectextmeta_field')) {
	document.getElementById('projectextmeta_field').onchange = function() {
		detectMetaSearchType();
	}
	addLoadEvent(detectMetaSearchType());
}


function detectTypeChange() {
	var el = document.getElementById('datafield_type');
	switch (el.value) {
		case '3':
		case '4':
			document.getElementById('selectcheck').style.display = '';
			document.getElementById('fileupload').style.display = 'none';
			break;
		case '5':
			document.getElementById('selectcheck').style.display = 'none';
			document.getElementById('fileupload').style.display = '';
			break;
		default:
			document.getElementById('selectcheck').style.display = 'none';
			document.getElementById('fileupload').style.display = 'none';
	}
}

function showProjectExtTable(metaid, metafield, allow_select) {
	if (!document.getElementById(metaid)) {
		metaid    = 'd'+metaid;
	}
	var metacurrent = document.getElementById(metaid).value;
	popup('?mod=projectext&action=extShowMetaTable&metaid='+metaid+'&metafield='+metafield+'&metacurrent='+metacurrent+'&allow_select='+allow_select, 'metatable', 0, 0, 1);
}
function setSearch(val) {
	document.getElementById('filter').value = val;
	document.getElementById('velden').submit();
}
function detectMetaSearchType() {
	var el = document.getElementById('projectextmeta_field');
	var curval = el.options[el.selectedIndex].text;

	document.getElementById('textsearch').style.display = 'none';
	document.getElementById('datesearch').style.display = 'none';
	document.getElementById('projectextmetatype').value = '';

	curval = curval.split(':');
	if (curval[0] == gettext("tekst")) {
		document.getElementById('textsearch').style.display = 'inline';
		document.getElementById('projectextmetatype').value = 'text';
	} else if (curval[0] == gettext("datum")) {
		document.getElementById('datesearch').style.display = 'inline';
		document.getElementById('projectextmetatype').value = 'date';
	}

}