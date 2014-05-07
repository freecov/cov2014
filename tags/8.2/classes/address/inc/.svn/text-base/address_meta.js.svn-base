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
	popup(url, 'addmeta', 600, 250, 1);
}

/* edit metafield  */
function edit_meta(tablename, meta_id) {
	url = 'index.php?mod=metafields&action=edit_meta&tablename='+tablename+'&record_id=0&meta_id='+meta_id;
	popup(url, 'addmeta', 600, 250, 1);
}