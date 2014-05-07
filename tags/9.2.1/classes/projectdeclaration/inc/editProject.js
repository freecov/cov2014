/**
 * Covide ProjectDeclaration module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function removeAddressSelection(id) {
	/* not used */
}
function syncAddressSelection() {
	var dest_syncAddressSelection = new Array('declarationconstituent', 'declarationclient', 'declarationadversary', 'declarationexpertise');
	var sc = document.getElementById('projectaddress_id');

	var sc_txt = document.getElementById('searchrel').innerHTML;

	var sc_val = sc.value.replace(/^,/g, '').split(',');
	sc_txt = sc_txt.split(/<li[^>]*?>/ig);

	var el = '';
	var curr = '';

	for (i=0; i < dest_syncAddressSelection.length; i++) {
		/* for each destination */
		el = document.getElementById(dest_syncAddressSelection[i]);
		//alert(el.id);
		for (j=0; j < sc_val.length; j++) {
			/* check if val does exists */
			curr = el.value;
			el.value = sc_val[j];
			if (el.value != sc_val[j]) {
				if (navigator.userAgent.indexOf("MSIE") != -1) {
					if (sc_txt[j])
						el[el.length] = new Option(sc_txt[j].replace(/<[^>]*?>/g, '').replace(/\[[^\]]*?\]/g, ''), sc_val[j]);
				} else {
					if (sc_txt[j+1])
						el[el.length] = new Option(sc_txt[j+1].replace(/<[^>]*?>/g, '').replace(/\[[^\]]*?\]/g, ''), sc_val[j]);
				}

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
	document.getElementById('layer_ncnp_perc').style.display = 'none';
	//document.getElementById('declarationprice').value = '';
	switch (el.value) {
		case "1":
			document.getElementById('layer_hourtarif1').style.display = '';
			document.getElementById('layer_hourtarif2').style.display = '';
			document.getElementById('layer_btw').style.display = '';
			break;
		case "2":
			document.getElementById('layer_kilometers').style.display = '';
			break;
		case "5":
			document.getElementById('layer_ncnp_perc').style.display = '';
			document.getElementById('layer_ncnp_verschotten').style.display = '';
			document.getElementById('layer_ncnp').style.display = '';
			document.getElementById('layer_btw').style.display = '';
			break;
		case "4":
			//document.getElementById('declarationprice').value = document.getElementById('declarationdefault_nora').value;
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
	document.getElementById('documentlist').style.visibility = 'hidden';
	document.getElementById('file_id').value = id;
	document.getElementById('velden').submit();
}

function switchBcards(src) {
	/* strip the declaration prefix */
	var dest = document.getElementById(new String().concat('declarationbcard_', src.id.replace(/^declaration/g, '')));
	var layer = document.getElementById(new String().concat('layer_', src.id.replace(/^declaration/g, '')));

	/* get the new business cards value */
	var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + src.value + '&current=' + dest.value);
	ret = ret.replace(/project\[bcard\]/g, 'declaration[bcard_' + src.id.replace(/^declaration/g, '') + ']');
	ret = ret.replace(/projectbcard/g, 'declarationbcard_' + src.id.replace(/^declaration/g, ''));
	layer.innerHTML = ret;
}

function attachSwitchEvent() {
	if (document.getElementById('declarationdeclaration_type')) {
		document.getElementById('declarationdeclaration_type').onchange = function() {
			switchVisibleFields();
		}
		switchVisibleFields();
	}
	var dest = new Array('declarationconstituent', 'declarationclient', 'declarationadversary', 'declarationexpertise');
	for (i=0; i < dest.length; i++) {
		if (document.getElementById(dest[i])) {
			document.getElementById(dest[i]).onchange = function() {
				switchBcards(this);
			}
		}
	}

}
addLoadEvent(attachSwitchEvent());

/* attach an event handler to the select all input checkbox */
/* retrieve input checkbox element */
if (document.getElementById('checkbox_regitem_toggle_all')) {
	document.getElementById('checkbox_regitem_toggle_all').onclick = function() {
		regitem_toggle_all( document.getElementById('checkbox_regitem_toggle_all').checked );
	}
}
/* function: regitem_toggle_all */
/*  toggle all regitem checkbox items to the status set in the parameter */
function regitem_toggle_all(set_to_status) {
	var frm = document.getElementById('regitemform');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_regitem\[/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

function regitem_delete_multi() {
	var frm = document.getElementById('regitemform');
	var doit = false;
	var id = '';
	var tmp = '';
	var cf = confirm(gettext("Are you sure you want to delete this item?"));
	if (cf == true) {
		for (i=0;i<frm.elements.length;i++) {
			if (frm.elements[i].name.match(/^checkbox_regitem\[/gi)) {
				if (frm.elements[i].checked == true) {
					doit = true;
					id = frm.elements[i].name.replace(/[^0-9,]/g,'');
					tmp = loadXML('index.php?mod=projectdeclaration&action=delete_registration&id='+id);
				}
			}
		}
		if (doit) {
			document.location.href=document.location.href;
		} else {
			alert(gettext("No items selected."));
		}
	}

}
