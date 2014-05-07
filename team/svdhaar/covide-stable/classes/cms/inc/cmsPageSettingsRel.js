/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function removeRel(id, ltarget, lspan) {
	if (confirm(gettext('Are you sure you want to remove this relation?'))) {
		var el_target = document.getElementById(ltarget);
		var el_span   = document.getElementById(lspan);

		var tg = el_target.value.replace(/^,/g,'').split(',');
		var sp = el_span.innerHTML.split(/<li/gi);

		for (i=0;i<tg.length;i++) {
			if (tg[i] == id) {
				tg.splice(i,1);
				if (navigator.appVersion.indexOf("MSIE")!=-1) {
					sp.splice(i,1);
				} else {
					sp.splice(i+1,1);
				}
			}
		}
		if (tg.count == 0) {
			el_span.innerHTML = '';
		} else {
			if (navigator.appVersion.indexOf("MSIE")!=-1) {
				el_span.innerHTML = '<LI' + sp.join('<LI');
			} else {
				el_span.innerHTML = sp.join('<LI');
			}
		}
		el_target.value = tg.join(',');
	}
}

function selectRel(address_id, relname) {
	el_address = document.getElementById('cmsaddress_id');
	el_span    = document.getElementById('searchrel');

	/* retrieve id's */
	var relations = el_address.value;
	relations = relations.replace(/\|/g, ',');

	/* sometimes the first element is empty */
	relations = relations.replace(/^,/g, '');

	/* split by comma */
	relations = relations.split(',');

	var list = el_span.innerHTML;

	var found = 0;
	for (i=0;i<relations.length;i++) {
		if (relations[i]==address_id) {
			found = 1;
		}
	}
	if (found==0) {
		/* add to array */
		relations[i] = address_id;
		list = list.concat("<li class='enabled'>");
		list = list.concat(relname, " <a href=\"javascript: removeRel('"+address_id+"', 'cmsaddress_id', 'searchrel');\">[X]</a>");
	}
	el_span.innerHTML = list;
	el_address.value = relations.join(',');
}
