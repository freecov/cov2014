function selectCampaign(u) {
	switch (u) {
		case '1':
			//document.getElementById('mod').value = 'newsletter';
			document.getElementById('velden').action.value = 'new2';
			document.getElementById('velden').type.value = '1';
			document.getElementById('velden').submit();
			break;
		default:
			window.resizeTo(960,600);
			var negative = document.getElementById('hidden_negative').value;
			document.location.href='?mod=address&action=zoekcla&classifications[negative]='+negative+'&campaign=' + u;
			break;
	}
}
function removeCampaign(id) {
	url = 'index.php?mod=campaign&history=1&action=delete&id='+id;
	if (confirm(gettext("Are you sure you want to remove this campaign?"))) {
		document.location.href=url;
	}
}