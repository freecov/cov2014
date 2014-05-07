function bcard_save() {
	if (document.getElementById('editbcard')) {
		document.getElementById('editbcard').submit();
	}
}

function removeRel(id, ltarget, lspan) {
	var el_target = document.getElementById(ltarget);
	var el_span   = document.getElementById(lspan);

	var tg = el_target.value.replace(/^,/g,'').split(',');
	var sp = el_span.innerHTML.split(/<LI/gi);

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
	if (tg.length == 0) {
		el_span.innerHTML = '';
	} else {
		if (navigator.appVersion.indexOf("MSIE")!=-1) {
			el_span.innerHTML = '<LI' + sp.join('<LI');
		} else {
			el_span.innerHTML = sp.join('<LI');
		}
	}
	el_target.value = tg.join(',');
}

function selectRel(address_id, relname) {
	el_address = document.getElementById('bcardaddress_id');
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
		list = list.concat("<a href=\"javascript: removeRel('"+address_id+"', 'bcardaddress_id', 'searchrel');\">", relname, "</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');
}

/* function to remove metafield. Ask first */
function remove_meta(id) {
	if (confirm(gettext("Are you sure you want to delete this field?"))) {
		uri = 'index.php?mod=metafields&action=xml_remove&table=bcards&id='+id;
		loadXML(uri);
		document.location.href=document.location.href;
	}
}

/* add metafield to address record */
function add_meta(tablename, record_id) {
	url = 'index.php?mod=metafields&action=add_meta&tablename='+tablename+'&record_id='+record_id;
	popup(url, 'addmeta', 0, 0, 1);
}

/* remove photo */
function remove_img(id) {
	if (confirm(gettext("Are you sure you want to delete the photo?"))) {
		url = 'index.php?mod=address&action=removerelimg&addresstype=bcards&address_id='+id;
		loadXML(url);
		document.location.href=document.location.href;
	}
}

function bcard_delete(id) {
	if(confirm(gettext('Dit verwijdert de businesscard en alle koppelingen. Weet u dit zeker?'))) {
		document.location.href='index.php?mod=address&action=cardrem&cardid='+id+'&closewin=1';
	}
}
