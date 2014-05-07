function calendaritem_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}
	document.getElementById('action').value = 'save';
	document.getElementById('calendarinput').submit();
}

function calendaritem_remove(id) {
	if (confirm(gettext("remove calendar item ?"))) {
		opener.document.getElementById('action').value = 'delete';
		opener.document.getElementById('id').value = id;
		opener.document.getElementById('calendarform').submit();
	}
	var t = setTimeout('window.close();', 100);
}

function selectProject(id, projectname) {
	document.getElementById('appointmentproject_id').value = id;
	document.getElementById('searchproject').innerHTML = projectname;
}

function pickProject() {
	var address_id = document.getElementById('appointmentaddress_id').value;
	popup('?mod=project&action=searchproject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function update_conflict(active) {
	var el  = document.getElementById('calendar_check_layer');
	var el2 = document.getElementById('action_save');
	var el3 = document.getElementById('action_save_top');
	//active:
	// 0 == all ok
	// 1 == endtime before starttime
	// 2 == starttime and endtime are the same
	// 3 == conflicts
	if (active == 1) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("start tijd kan niet voor de eind tijd liggen");
		el2.style.visibility='hidden';
		el3.style.visibility='hidden';
	} else if (active == 2) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("start en eind tijd kunnen niet hetzelfde zijn");
		el2.style.visibility='hidden';
		el3.style.visibility='hidden';
	} else if (active == 3) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("er is al een afspraak op deze tijd");
		el2.style.visibility='hidden';
		el3.style.visibility='hidden';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("geen conflicten");
		el2.style.visibility='visible';
		el3.style.visibility='visible';
	}
}

var calendar_check = Array('appointmentid','appointmentfrom_day','appointmentfrom_month','appointmentfrom_year','appointmentfrom_hour', 'appointmentfrom_min','appointmentto_hour', 'appointmentto_minute', 'appointmentuser');
function checkCalendarAppointment() {
	var uri = '?mod=calendar&action=xml_check';
	for (i=0;i<calendar_check.length;i++) {
		uri = uri.concat('&', calendar_check[i].replace(/^appointment/g,''), '=', document.getElementById(calendar_check[i]).value);
	}
	loadXML(uri);

}

for (i=0;i<calendar_check.length;i++) {
	document.getElementById(calendar_check[i]).onchange = function() {
		checkCalendarAppointment();
	}
}

addLoadEvent( checkCalendarAppointment() );
addLoadEvent( update_mail_list_onload() );

function calendarPopUp_agenda() {
	eval("var wx = window.open('common/calendar.php?veld=agenda&start=1041375600&eind=1230764400&sday='+document.getElementById('appointmentfrom_day').value+'&smonth='+document.getElementById('appointmentfrom_month').value+'&syear='+document.getElementById('appointmentfrom_year').value, 'wx', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=200,height=220,left = 412,top = 284');");
}

function upd_agenda(dday, dmonth, dyear){
	document.getElementById('agenda_dag').value=parseInt(dday);
	document.getElementById('agenda_maand').value=parseInt(dmonth);
	document.getElementById('agenda_jaar').value=parseInt(dyear);
	if (document.getElementById('agenda_jaar').value!=parseInt(dyear)){
		alert(gettext("jaar")+' '+dyear+' '+gettext("niet gevonden"));
	}
}

/* update the email address list from the server */
function update_mail_list(ret) {
	ret = unescape(ret);
	var ret_test = ret.split(/<br[^>]*?>/g);
	if (ret_test.length > 6) {
		document.getElementById('mail_addresses').style.height = '180px;';
		document.getElementById('mail_addresses').style.overflow = 'auto';
	} else {
		document.getElementById('mail_addresses').style.height = '';
		document.getElementById('mail_addresses').style.overflow = '';
	}
	document.getElementById('mail_addresses').innerHTML = ret;
}

/* xml call to update the email list */
function update_mail_list_xml(address_id) {
	var ret = loadXMLContent('?mod=calendar&action=addressemails&address_id=' + address_id);
	update_mail_list(ret);
}

/* update the email address list on load */
function update_mail_list_onload() {
	var addressid = document.getElementById('appointmentaddress_id').value;
	update_mail_list_xml(addressid);
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
	update_mail_list_xml(el_target.value);
}

function selectRel(addressid, str) {
	/* retrieve hidden field and span contents */
	var el_address = document.getElementById('appointmentaddress_id');
	var el_span    = document.getElementById('searchrel');

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
		if (relations[i]==addressid) {
			found = 1;
		}
	}
	if (found==0) {
		/* add to array */
		relations[i] = addressid;
		list = list.concat("<li class='enabled'>");
		list = list.concat("<a href=\"javascript: removeRel('"+addressid+"', 'appointmentaddress_id', 'searchrel');\">", str, "</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');
	rel_complete_initial = 0;
	update_mail_list_xml(el_address.value);
}