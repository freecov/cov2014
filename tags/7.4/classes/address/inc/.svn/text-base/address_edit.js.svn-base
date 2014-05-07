function address_save() {
	document.getElementById('addressedit').submit();
}

function randomPassword(length) {
  chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  pass = "";
  for(x=0; x<length; x++) {
    i = Math.floor(Math.random() * 62);
    pass += chars.charAt(i);
  }
  return pass;
}

function address_remove_item(id, addresstype) {
	switch (addresstype) {
		/* Where do you want to go today? */
		case 'private' :
			if (confirm(gettext("Are you sure you want to delete this address")+'?')) {
				document.location.href='index.php?mod=address&action=delete&addresstype=private&id=' + id;
			}
			break;
		default :
			if (confirm(gettext("Are you sure you want to delete this address")+'?')) {
				document.getElementById('addressis_active').value=0;
				document.getElementById('addressis_active').checked=false;
				document.getElementById('addressedit').submit();
			}
			break;
		/* end switch statement */
	}
}

function address_get_debtornr() {
	/* get current debtor prefix */
	var cur = document.getElementById('addressdebtor_nr').value;
	url = 'index.php?mod=address&action=gendebtornr&cur='+cur;
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
		el.innerHTML=gettext("The data you entered is already (partially) in the database");
		el.innerHTML = el.innerHTML.concat('<br>', count, ' ', gettext("address(es) with the same information found"));
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
	if (confirm(gettext("Are you sure you want to delete this field?"))) {
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

/* edit metafield  */
function edit_meta(tablename, meta_id) {
	url = 'index.php?mod=metafields&action=edit_meta&tablename='+tablename+'&record_id=0&meta_id='+meta_id;
	popup(url, 'addmeta', 0, 0, 1);
}

/* remove photo */
function remove_img(id) {
	if (confirm(gettext("Are you sure you want to delete the photo?"))) {
		url = 'index.php?mod=address&action=removerelimg&addresstype=relations&address_id='+id;
		loadXML(url);
		document.location.href=document.location.href;
	}
}
