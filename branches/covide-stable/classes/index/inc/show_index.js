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
	//update_mail_list_xml(el_target.value);
}

function selectRel(addressid, str) {
	/* retrieve hidden field and span contents */
	var el_address = document.getElementById('searchaddress_id');
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
		list = list.concat(str, " <a href=\"javascript: removeRel('"+addressid+"', 'searchaddress_id', 'searchrel');\">[X]</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');
	rel_complete_initial = 0;
	//update_mail_list_xml(el_address.value);
}
