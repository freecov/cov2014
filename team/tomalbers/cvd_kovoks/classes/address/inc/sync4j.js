function toggleSync(address_id, address_table, action) {
	url = 'index.php?mod=address&action=togglesync&identifier='+address_table+'&address_id='+address_id+'&toggleaction='+action;
	loadXML(url);
}
