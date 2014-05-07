function calendaritem_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}
	document.getElementById('action').value = 'save';
	document.getElementById('calendarinput').submit();
}

function calendaritem_remove(id, is_repeat) {
	if (confirm(gettext("remove all recurrences ?"))) {
		remove_all = 1;
	} else {
		remove_all = 0;
	}
	if (confirm(gettext("remove calendar item?"))) {
		opener.document.getElementById('calendarform').action.value = 'delete';
		opener.document.getElementById('calendarform').id.value = id;
		opener.document.getElementById('calendarform').appointmentremove_all = remove_all;
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
	// 3 == already have an appointment in this timeframe. not an error but a warning
	// 4 == all day event
	if (active == 1) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("start time should be before end time");
		el2.style.visibility='hidden';
		el3.style.visibility='hidden';
	} else if (active == 2) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("start and end time cannot be the same");
		el2.style.visibility='hidden';
		el3.style.visibility='hidden';
	} else if (active == 3) {
		el.style.border='2px dotted orange';
		el.innerHTML=gettext("already have an appointment at this time");
		el2.style.visibility='visible';
		el3.style.visibility='visible';
	} else if (active == 4) {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("Event");
		el2.style.visibility='visible';
		el3.style.visibility='visible';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("no conflicts");
		el2.style.visibility='visible';
		el3.style.visibility='visible';
	}
}

var calendar_check = Array('appointmentid','appointmentfrom_day','appointmentfrom_month','appointmentfrom_year','appointmentfrom_hour', 'appointmentfrom_min','appointmentto_hour', 'appointmentto_minute', 'appointmentuser', 'appointmentis_event', 'appointmentuser_id', 'appointmentgroup_id');
var lastCheckCalendarAppointmentUri = new String();
function checkCalendarAppointment() {
	//update_conflict(0);
	//return 0;
	// When is_event is checked, we want the start and end time disabled!
	var event = document.getElementById('appointmentis_event').checked;
	document.getElementById('appointmentto_minute').disabled = event;
	document.getElementById('appointmentto_hour').disabled = event;
	document.getElementById('appointmentfrom_min').disabled = event;
	document.getElementById('appointmentfrom_hour').disabled = event;

	/* TODO: If event is checked disallow multiple selections. Assigned to "is_event" but should be made for "is_crossing_the_day" in the future */
	//if(event == true) {	document.getElementById('appointmentfrom_day').multiple = false; }
	//if(event == false) {	document.getElementById('appointmentfrom_day').multiple = true; }

	// The verification
	var uri = '?mod=calendar&action=xml_check';
	for (i=0;i<calendar_check.length;i++) {
		// checkboxes cant be queried with .value, so special treatment...
		if (document.getElementById(calendar_check[i]).type == "checkbox")
			uri = uri.concat('&', calendar_check[i].replace(/^appointment/g,''), '=', document.getElementById(calendar_check[i]).checked);
		else
			uri = uri.concat('&', calendar_check[i].replace(/^appointment/g,''), '=', document.getElementById(calendar_check[i]).value);
	}
	if (uri != lastCheckCalendarAppointmentUri) {
		lastCheckCalendarAppointmentUri = uri;
		loadXML(uri);
	}

}

var ti = setInterval('checkCalendarAppointment();', 1000);

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
		alert(gettext("year")+' '+dyear+' '+gettext("not found"));
	}
}

/* update the email address list from the server */
function update_mail_list(ret) {
	ret = unescape(ret);
	var ret_test = ret.split(/<br[^>]*?>/g);
	if (ret_test.length > 6) {
		document.getElementById('mail_addresses').style.height = '180px';
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
	var t = setTimeout('update_mail_list(\''+escape(ret)+'\');', 100);
	/* update_mail_list(ret); */
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
function pickPrivate() {
	popup('?mod=address&action=searchRelPrivate', 'searchprivate', 650, 500, 1);
}
function selectPrivate(id, name) {
		/* retrieve hidden field and span contents */
	var el_address_p = document.getElementById('appointmentprivate_id');
	var el_span_p    = document.getElementById('searchprivate');

	/* retrieve id's */
	var relations_p = el_address_p.value;
	relations_p = relations_p.replace(/\|/g, ',');

	/* sometimes the first element is empty */
	relations_p = relations_p.replace(/^,/g, '');

	/* split by comma */
	relations_p = relations_p.split(',');

	var list_p = el_span_p.innerHTML;

	var found_p = 0;
	for (i=0;i<relations_p.length;i++) {
		if (relations_p[i]==id) {
			found_p = 1;
		}
	}
	if (found_p==0) {
		/* add to array */
		relations_p[i] = id;
		list_p = list_p.concat("<li class='enabled'>");
		list_p = list_p.concat("<a href=\"javascript: removeRel('"+id+"', 'appointmentprivate_id', 'searchprivate');\">", name, "</a>");
	}
	el_span_p.innerHTML = list_p;
	el_address_p.value = relations_p.join(',');
}

function getDistance() {
	if (document.getElementById("appointmentlocation").value) {
		var location = document.getElementById("appointmentlocation").value;
	} else {
		var location = 0;
	}
	var id = document.getElementById("appointmentaddress_id").value;
	var url = '?mod=googlemaps&action=show_map';
	if (location) {
		url += '&location='+location;
	}
	if (id) {
		url += '&id='+id;
	}
	popup(url, 'googlemaps', 580, 650, 1);
}
