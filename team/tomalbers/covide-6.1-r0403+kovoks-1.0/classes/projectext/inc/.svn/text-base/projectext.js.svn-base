if (document.getElementById('datafield_type')) {
	document.getElementById('datafield_type').onchange = function() {
		detectTypeChange();
	}
	addLoadEvent(detectTypeChange());
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
	var metacurrent = document.getElementById(metaid).value;
	popup('?mod=projectext&action=extShowMetaTable&metaid='+metaid+'&metafield='+metafield+'&metacurrent='+metacurrent+'&allow_select='+allow_select, 'metatable', 0, 0, 1);
}