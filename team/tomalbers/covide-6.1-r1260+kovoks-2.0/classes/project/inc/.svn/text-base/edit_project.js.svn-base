function save_project() {
	document.getElementById('projectedit').submit();
}

function delete_project() {
	if (confirm(gettext('Wilt u dit project verwijderen?'))==true) {
		if (confirm(gettext("Dit zal ook alle aan dit project gekoppelde bestanden en urenregistratie items verwijderen. Weet u dit zeker?"))) {
			document.getElementById('action').value = 'delete_project';
			document.getElementById('projectedit').submit();
		}
	}
}

/* old function, obsolete */
function selectRelX(id, relname) {
	document.getElementById('projectaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}


function extUpdateActivities() {
	var projectid = document.getElementById('projectid').value;
	var ret = loadXMLContent('?mod=projectext&action=dynamicFields&output_buffer=1&activity_id=' + document.getElementById('extactivity').value + '&project_id=' + projectid);
	var el = document.getElementById('project_extrafields').innerHTML = ret;

	loadXML('?mod=projectext&action=dynamicFields&output_buffer=1&exec_javascript=1&activity_id=' + document.getElementById('extactivity').value + '&project_id=' + projectid);
}
if (document.getElementById('extactivity')) {
	document.getElementById('extactivity').onchange = function() { extUpdateActivities(); }
	addLoadEvent( extUpdateActivities() );
}

function mergeFile(file) {
	document.getElementById('file_name').value = file;
	document.getElementById('velden').submit();
}

function removeRel(id, ltarget, lspan) {
	var el_target = document.getElementById(ltarget);
	var el_span   = document.getElementById(lspan);

	var tg = el_target.value.replace(/^,/g,'').split(',');
	var sp = el_span.innerHTML.split(/<li/gi);

	for (i=0;i<tg.length;i++) {
		if (tg[i] == id) {
			tg.splice(i,1);
			if (navigator.appVersion.indexOf("MSIE")!=-1) {
				sp.splice(i,1);
			} else {
				sp.splice(i+1,1);
			}
		}
	}
	if (tg.count == 0) {
		el_span.innerHTML = '';
	} else {
		if (navigator.appVersion.indexOf("MSIE")!=-1) {
			el_span.innerHTML = '<LI' + sp.join('<LI');
		} else {
			el_span.innerHTML = sp.join('<LI');
		}
	}
	el_target.value = tg.join(',');
	updateBcards();
}

function selectRel(address_id, relname) {
	el_address = document.getElementById('projectaddress_id');
	el_span    = document.getElementById('searchrel');

	/* retrieve id's */
	var relations = el_address.value;
	relations = relations.replace(/\|/g, ',');

	/* sometimes the first element is empty */
	relations = relations.replace(/^,/g, '');

	/* split by comma */
	relations = relations.split(',');

	var list = el_span.innerHTML;

	var found = 0;
	for (i=0;i<relations.length;i++) {
		if (relations[i]==address_id) {
			found = 1;
		}
	}
	if (found==0) {
		/* add to array */
		relations[i] = address_id;
		list = list.concat("<li class='enabled'>");
		list = list.concat("<a href=\"javascript: removeRel('"+address_id+"', 'projectaddress_id', 'searchrel');\">", relname, "</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');

	updateBcards();
}

function updateBcards() {
	var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + document.getElementById('projectaddress_id').value + '&current=' + document.getElementById('projectbcard').value);
	document.getElementById('project_bcard_layer').innerHTML = ret;
}

