function relationscard_save() {
	if (document.getElementById('editrelationscard')) {
		document.getElementById('editrelationscard').submit();
	}
}

function popup_picker() {
        var i= eval(document.getElementById('relationscardsupplier_id').value);
	var url = '?mod=address&action=pick_bank&deb=' + i;
        popup(url, 'searchbank', 0, 0, 1);
}

function selectBank(id, relname) {
        document.getElementById('relationscardbank_pref').value = id;
        document.getElementById('searchbank').innerHTML = relname;
}

function selectRel(id, relname, classname) {
	var i1=classname;
	var i2='human'+classname;	
        document.getElementById( i1 ).value = id;
        document.getElementById( i2 ).innerHTML = relname;
}

/* function to remove metafield. Ask first */
function remove_meta(id) {
        if (confirm(gettext("Weet u zeker dat u dit veld wilt verwijderen?"))) {
                uri = 'index.php?mod=metafields&action=xml_remove&table=address_relations&id='+id;
                loadXML(uri);
                document.location.href=document.location.href;
        }
}

/* add metafield to address record */
function add_meta(tablename, record_id) {
        url = 'index.php?mod=metafields&action=add_meta&tablename='+tablename+'&record_id='+record_id;
        popup(url, 'addmeta', 0, 0, 1);
}
