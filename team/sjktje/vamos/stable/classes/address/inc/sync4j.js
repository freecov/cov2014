function toggleSync(address_id, address_table, action) {
	url = 'index.php?mod=address&action=togglesync&identifier='+address_table+'&address_id='+address_id+'&toggleaction='+action;
	if (address_id.match(/^sel_/g))
		popup(url, 'sync', 300, 200, 1);
	else
		loadXML(url);
}
