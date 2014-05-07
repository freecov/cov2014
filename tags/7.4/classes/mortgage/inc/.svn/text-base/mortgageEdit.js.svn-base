function mortgage_save() {
	document.getElementById('velden').action.value = 'save';
	document.getElementById('velden').submit();
}

function selectRel(id, str) {
	document.getElementById('mortgageaddress_id').value = id;
	document.getElementById('layer_relation').innerHTML = str;
}

function detectSubType() {
	var el = document.getElementById('mortgagetype');
	if (el.value == 1) {
		document.getElementById('mortgageinsurancer').value = 0;
		document.getElementById('mortgageinsurancer').style.visibility = 'hidden';
		document.getElementById('mortgageinvestor').style.visibility = 'visible';
		document.getElementById('mortgageyear_payement').style.visibility = 'visible';
	} else {
		document.getElementById('mortgageinsurancer').style.visibility = 'visible';
		document.getElementById('mortgageinvestor').value = 0;
		document.getElementById('mortgageinvestor').style.visibility = 'hidden';
		document.getElementById('mortgageyear_payement').value = 0;
		document.getElementById('mortgageyear_payement').style.visibility = 'hidden';
	}
}
addLoadEvent(detectSubType);
if (document.getElementById('mortgagetype')) {
	document.getElementById('mortgagetype').onclick = function() { detectSubType(); }
}