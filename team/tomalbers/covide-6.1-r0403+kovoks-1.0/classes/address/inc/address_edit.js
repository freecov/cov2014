function address_save() {
	document.getElementById('addressedit').submit();
}

function address_remove_item(id, addresstype) {
	switch (addresstype) {
		/* Where do you want to go today? */
		case 'private' : 
			if (confirm(gettext("Weet u zeker dat u dit adres wilt verwijderen")+'?')) {
				document.location.href='index.php?mod=address&action=delete&addresstype=private&id=' + id;
			}
			break;
		default : 
			if (confirm(gettext("Weet u zeker dat u dit adres wilt verwijderen")+'?')) {
				document.getElementById('addressis_active').value=0;
				document.getElementById('addressis_active').checked=false;
				document.getElementById('addressedit').submit();
			}
			break;
		/* end switch statement */
	}
}

function address_get_debtornr() {
	url = 'index.php?mod=address&action=gendebtornr';
	loadXML(url);
}

function update_debtor_nr(nr) {
	document.getElementById('addressdebtor_nr').value = nr;
}

function update_double(count) {
	var el  = document.getElementById('address_check_layer');
	var el2 = document.getElementById('action_save_span');
	//count:
	// 0 == all ok
	// n == n addresses match provided info
	if (count > 0) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("U heeft gegevens ingevoerd die al bekend zijn in de database");
		el.innerHTML = el.innerHTML.concat('<br>', count, ' ', gettext("adres(sen) met overeenkomende gegevens"));
		el2.style.visibility='hidden';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("no conflicts");
		el2.style.visibility='visible';
	}
}

var address_check = Array('addressid','addresszipcode','addressphone_nr');

function checkAddress() {
	if (skip_checks != 1) {
		var uri = '?mod=address&action=check_double';
		for (i=0;i<address_check.length;i++) {
			uri = uri.concat('&', address_check[i].replace(/^address/g,''), '=', document.getElementById(address_check[i]).value);
		}
		loadXML(uri);
	}
}

for (i=0;i<address_check.length;i++) {
	document.getElementById(address_check[i]).onchange = function() {
		checkAddress();
	}
}
if (document.getElementById('addressforce_double')) {
	document.getElementById('addressforce_double').onclick = function() {
		force_double( document.getElementById('addressforce_double').checked );
	}
}

function force_double(set_to_status) {
	if (set_to_status) {
		update_double(0);
	} else {
		checkAddress();
	}
}

addLoadEvent( checkAddress() );

/* function to remove metafield. Ask first */
function remove_meta(id) {
	if (confirm(gettext("Weet u zeker dat u dit veld wilt verwijderen?"))) {
		uri = 'index.php?mod=metafields&action=xml_remove&table=adres&id='+id;
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
	if (confirm(gettext("Weet u zeker dat u de foto wilt verwijderen?"))) {
		url = 'index.php?mod=address&action=removerelimg&addresstype=relations&address_id='+id;
		loadXML(url);
		document.location.href=document.location.href;
	}
}

function selectRel(id, relname, classname) {
        var i1=classname;
        var i2='human'+classname;
        document.getElementById( i1 ).value = id;
        document.getElementById( i2 ).innerHTML = relname;
}

