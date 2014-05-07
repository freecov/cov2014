function specifiedHandler() {
  document.getElementById("specified").onchange = function() { checkIt(); };
}
function checkIt() {
	if (document.getElementById("specified").value == 5) {
		document.getElementById("landSelect").disabled = false;
		document.getElementById("search").disabled = true;
	} else {
		document.getElementById("landSelect").value = 'XX'; //TODO: fix this!
		document.getElementById("landSelect").disabled = true;
		document.getElementById("search").disabled = false;
	}
}
addLoadEvent(checkIt());
addLoadEvent(specifiedHandler());

if (document.getElementById('addressactions')) {
	document.getElementById('addressactions').onchange = function() {
		exportinfo = check_checkbox_selection();
		eval(document.getElementById('addressactions').value);
		setTimeout("document.getElementById('addressactions').value = 'void(0);';", 200);
	}
}

if (document.getElementById('addressactions1')) {
	document.getElementById('addressactions1').onchange = function() {
		exportinfo = check_checkbox_selection();
		eval(document.getElementById('addressactions1').value);
		setTimeout("document.getElementById('addressactions1').value = 'void(0);';", 200);
	}
}

function showActions(id) {
	el = document.getElementById('addressactionicons_'+id);
	if (el.style.display == 'none') {
		el.style.display = 'block';
	} else {
		el.style.display = 'none';
	}
}

function handle_selectionaction() {
	exportinfo = check_checkbox_selection();
}

function check_checkbox_selection() {
	var frm = document.getElementById('deze');
	var ids = '0';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_address\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
			}
		}
	}
	ids = ids.replace(/^0,/g,'');
	if (ids != 0) {
		var ret = loadXMLContent('index.php?mod=address&action=updateExportInfoXML&oldexportinfo='+exportinfo+'&ids='+ids);
		return ret;
	} else {
		return exportinfo;
	}
}
