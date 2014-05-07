function settype(v) {
	location.href='cmsSitemap.php?settype='+v;
}
function cmsEdit(controller, id, parentpage) {
	popup('?mod=cms&action=editpage&id='+id+'&parentpage='+parentpage, controller, 990, 680, 1);
}

function cmsSearch() {
	if (document.getElementById('cmssearch').value) {
		document.getElementById('cmd').value = 'search';
		document.getElementById('velden').submit();
	}
}
function cmsExpand(id) {
	if (id != -1) {
		document.getElementById('id').value = id;
		document.getElementById('cmd').value = 'expand';
		document.getElementById('velden').submit();
	} else {
		if (confirm(gettext("Hiermee wordt de complete siteboom uitgeklapt. Dit kan tot lange laadtijden resulteren afhankelijk van de grootte van uw site. Wilt u doorgaan?")) == true) {
			document.getElementById('cmd').value = 'expandAll';
			document.getElementById('velden').submit();
		}
	}
}
function cmsCollapse(id) {
	if (id != -1) {
		document.getElementById('id').value = id;
		document.getElementById('cmd').value = 'collapse';
	} else {
		document.getElementById('cmd').value = 'collapseAll';
	}
	document.getElementById('velden').submit();
}
function cmsReload() {
	document.getElementById('velden').submit();
}
function cmsSearchPage() {
	var id = document.getElementById('cmssearch').value;
	var ret = loadXMLContent('?mod=cms&action=searchPageXML&id='+id);
	if (ret == "1") {
		cmsEdit('cmsEditor', id);
	} else {
		alert(gettext('Pagina kan niet gevonden worden'));
	}
}
function saveSettings() {
	document.getElementById('velden').submit();
}

function hl_auth(user, level) {
	hl_disable(user);
	document.getElementById('td_'+user+'_'+level).style.backgroundColor = '#dddddd';
}

function templateSelectTab(tab) {
	document.getElementById('tab_template').style.display = 'none';
	document.getElementById('tab_media').style.display = 'none';
	document.getElementById('tab_includes').style.display = 'none';
	document.getElementById('tab_help').style.display = 'none';

	document.getElementById('span_template').style.backgroundColor = '';
	document.getElementById('span_media').style.backgroundColor = '';
	document.getElementById('span_includes').style.backgroundColor = '';
	document.getElementById('span_help').style.backgroundColor = '';

	switch (tab) {
		case 'template':
			document.getElementById('tab_template').style.display = 'block';
			document.getElementById('span_template').style.backgroundColor = '#ffebb0';
			break;
		case 'media':
			document.getElementById('tab_media').style.display = 'block';
			document.getElementById('span_media').style.backgroundColor = '#ffebb0';
			break;
		case 'includes':
			document.getElementById('tab_includes').style.display = 'block';
			document.getElementById('span_includes').style.backgroundColor = '#ffebb0';
			break;
		case 'help':
			document.getElementById('tab_help').style.display = 'block';
			document.getElementById('span_help').style.backgroundColor = '#ffebb0';
			break;
	}
}

function redrawopts(val) {
	document.getElementById('dtext1').style.display = 'none';
	document.getElementById('dtext2').style.display = 'none';
	document.getElementById('ddate1').style.display = 'none';
	document.getElementById('ddate2').style.display = 'none';
	document.getElementById('dday1').style.display = 'none';

	if (val == 'daterange') {
		document.getElementById('ddate1').style.display = '';
		document.getElementById('ddate2').style.display = '';
	} else if (val == 'dateday') {
		document.getElementById('dday1').style.display = '';
	} else {
		document.getElementById('dtext1').style.display = '';
		document.getElementById('dtext2').style.display = '';
	}
}
function checkSort(newel) {
	var el0 = document.getElementById('order0_sort');
	var el1 = document.getElementById('order1_sort');
	var el2 = document.getElementById('order2_sort');

	var count = 0;
	if (el0.value.match(/^meta/g)) {
		count++;
	}
	if (el1.value.match(/^meta/g)) {
		count++;
	}
	if (el2.value.match(/^meta/g)) {
		count++;
	}
	if (count > 1) {
		alert(gettext("Waarschuwing: er kan maar op 1 meta veld tegelijk gesorteerd worden!"));
		newel.value = 0;
	}

}

if (document.getElementById('order0_sort')) {
	document.getElementById('order0_sort').onchange = function() {
		checkSort(document.getElementById('order0_sort'));
	}
	document.getElementById('order1_sort').onchange = function() {
		checkSort(document.getElementById('order1_sort'));
	}
	document.getElementById('order2_sort').onchange = function() {
		checkSort(document.getElementById('order2_sort'));
	}
}
