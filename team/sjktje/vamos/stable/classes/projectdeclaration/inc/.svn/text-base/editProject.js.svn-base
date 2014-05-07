function removeAddressSelection(id) {
	/* not used */
}
function syncAddressSelection() {
	var dest = new Array('declarationconstituent', 'declarationclient', 'declarationadversary', 'declarationexpertise');
	var sc = document.getElementById('projectaddress_id');
	var sc_txt = document.getElementById('searchrel').innerHTML;

	var sc_val = sc.value.split(',');
	sc_txt = sc_txt.split(/<li[^>]*?>/g);


	var el = '';
	var curr = '';

	for (i=0; i < dest.length; i++) {
		/* for each destination */
		el = document.getElementById(dest[i]);
		for (j=0; j < sc_val.length; j++) {
			/* check if val does exists */
			curr = el.value;
			el.value = sc_val[j];
			if (el.value != sc_val[j]) {
				el.options[el.length] = new Option(sc_txt[j+1].replace(/<[^>]*?>/g, ''), sc_val[j]);
			}
			el.value = curr;
		}
	}
}
function switchVisibleFields() {
	var el = document.getElementById('declarationdeclaration_type');
	document.getElementById('layer_ncnp_verschotten').style.display = 'none';
	document.getElementById('layer_btw').style.display = 'none';
	document.getElementById('layer_kilometers').style.display = 'none';
	document.getElementById('layer_hourtarif1').style.display = 'none';
	document.getElementById('layer_hourtarif2').style.display = 'none';
	document.getElementById('layer_ncnp').style.display = 'none';
	document.getElementById('declarationprice').value = '';
	switch (el.value) {
		case "1":
			document.getElementById('layer_hourtarif1').style.display = '';
			document.getElementById('layer_hourtarif2').style.display = '';
			document.getElementById('layer_btw').style.display = '';
			break;
		case "2":
			document.getElementById('layer_kilometers').style.display = '';
			break;
		case "4":
			document.getElementById('declarationprice').value = document.getElementById('declarationdefault_nora').value;
			/* no break */
		default:
			document.getElementById('layer_ncnp_verschotten').style.display = '';
	document.getElementById('layer_ncnp').style.display = '';
			document.getElementById('layer_btw').style.display = '';
			break;
	}
}
function delete_registration(id) {
	var cf = confirm(gettext("Are you sure you want to delete this item?"));
	if (cf == true) {
		location.href='?mod=projectdeclaration&action=delete_registration&id='+id;
	}
}
function generateDocument(id) {
	document.getElementById('file_id').value = id;
	document.getElementById('velden').submit();
}
function attachSwitchEvent() {
	if (document.getElementById('declarationdeclaration_type')) {
		document.getElementById('declarationdeclaration_type').onchange = function() {
			switchVisibleFields();
		}
		switchVisibleFields();
	}
}
addLoadEvent(attachSwitchEvent());