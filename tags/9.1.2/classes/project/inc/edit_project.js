function save_project() {
	document.getElementById('projectedit').submit();
}
function updateBcards() {
	return true;
}

function delete_project() {
	if (confirm(gettext('Are you sure you want to remove this project?'))==true) {
		if (confirm(gettext("This vill remove all the linked files and hour registration items. Are you sure ?"))) {
			document.getElementById('action').value = 'delete_project';
			document.getElementById('projectedit').submit();
		}
	}
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

function removeRel(id, ltarget, lspan) {
	if (confirm(gettext('Are you sure you want to remove this relation?'))) {
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
}

var sync_timer;

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
		list = list.concat(relname, " <a href=\"javascript: removeRel('"+address_id+"', 'projectaddress_id', 'searchrel');\">[X]</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');

	updateBcards();

	/* detect declaration module and call syncer */
	if (document.getElementById('declarationconstituent')) {
		//clearTimeout(sync_timer);
		//sync_timer = setTimeout('syncAddressSelection();', 200);
		syncAddressSelection();
	}
}

function updateBcards() {
	var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + document.getElementById('projectaddress_id').value + '&current=' + document.getElementById('projectbcard_selected').value);
	document.getElementById('project_bcard_layer').innerHTML = ret;
}

function setProjectName(str) {
	document.getElementById('projectname').value = str;
	document.getElementById('project_name_suggest').innerHTML = '';
}

function autocomplete_project_xml(str) {
	var ret = loadXMLContent('?mod=project&action=autocomplete_project_name&str='+str);
	if (ret.match(/[a-z0-9]/gi)) {
		var ret2 = '';
		ret = ret2.concat(' ', gettext("next free number"), ': ', '<a href="javascript: setProjectName(\'', ret, '\');">', ret, '</a>');
		document.getElementById('project_name_suggest').innerHTML = ret;
	} else {
		document.getElementById('project_name_suggest').innerHTML = '';
	}
}
function autocomplete_project_name() {
	clearTimeout(autocomplete_project_timer);
	var str = document.getElementById('projectname').value.replace(/\'/g, '');;
	if (str.length >= 3) {
		autocomplete_project_timer = setTimeout("autocomplete_project_xml('" + str + "');", 500);
	} else {
		document.getElementById('project_name_suggest').innerHTML = '';
	}
}

if (document.getElementById('projectname')) {
	var autocomplete_project_timer;
	document.getElementById('projectname').onkeyup = function() { autocomplete_project_name(); }
}

