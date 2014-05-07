/* set type */
function settype(v) {
	location.href='cmsSitemap.php?settype='+v;
}
/* cmsEdit */
function cmsEdit(controller, id, parentpage) {
	popup('?mod=cms&action=editpage&id='+id+'&parentpage='+parentpage, controller, 990, 680, 1);
}
/* fillBuffer */
function fillBuffer() {
	document.getElementById('cmd').value = 'fillbuffer';
	document.getElementById('velden').submit();
}
/* cmsSearch */
function cmsSearch() {
	if (document.getElementById('cmssearch').value) {
		document.getElementById('cmd').value = 'search';
		document.getElementById('velden').submit();
	}
}
/* cmsExpand */
function cmsExpand(id) {
	if (id != -1) {
		document.getElementById('id').value = id;
		document.getElementById('cmd').value = 'expand';
		document.getElementById('jump_to_anchor').value = 'id'+id;
		document.getElementById('velden').submit();
	} else {
		if (confirm(gettext("This will expand the whole sitetree. This can take a long time depending on the size of the tree. Continue?")) == true) {
			document.getElementById('cmd').value = 'expandAll';
			document.getElementById('velden').submit();
		}
	}
}
/* cmsCollapse */
function cmsCollapse(id) {
	if (id != -1) {
		document.getElementById('id').value = id;
		document.getElementById('cmd').value = 'collapse';
	} else {
		document.getElementById('cmd').value = 'collapseAll';
	}
	document.getElementById('velden').submit();
}
/* cmsReload */
function cmsReload() {
	document.getElementById('velden').submit();
}
/* cmsSearchPage */
function cmsSearchPage() {
	var id = document.getElementById('cmssearch').value;
	var ret = loadXMLContent('?mod=cms&action=searchPageXML&id='+id);
	if (ret == "1") {
		cmsEdit('cmsEditor', id);
	} else {
		alert(gettext("The requested page cannot be found."));
	}
}
/* saveSettings */
function saveSettings() {
	document.getElementById('velden').submit();
}
function saveSettingsGallery() {
	document.getElementById('layer_busy').style.display = '';
	document.getElementById('layer_actions').style.display = 'none';
	document.getElementById('marquee_progressbar').style.visibility = 'visible';
	document.getElementById('velden').submit();
}

/* hl_auth */
function hl_auth(user, level) {
	hl_disable(user);
	document.getElementById('td_'+user+'_'+level).style.backgroundColor = '#dddddd';
}
/* templateSelectTab */
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
		alert(gettext("Warning: it's not possible to sort on more then 1 meta field!"));
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

function exec_buffer() {
	document.getElementById('cmd').value = document.getElementById('cmsbuffer').value;
	if (document.getElementById('cmd').value == 'deletebuffer') {
		/* retreive currently selected sitemap root */
		var siteroot = document.getElementById('cmssiteroot').value;
		popup('index.php?mod=cms&action=deletepage&id=buffer&siteroot='+siteroot, 'deletepage', 640, 500, 1);
		document.getElementById('cmd').value = '';
		document.getElementById('cmsbuffer').value = '';
	} else {
		document.getElementById('velden').submit();
	}
}

function addSiteRoot() {
	var str = prompt(gettext("Please enter the new site root name"));
	if (str != '' && str != false && str != null) {
		location.href = '?mod=cms&action=addSiteRoot&name='+str;
	}
}
function checkSiteRootChange() {
	if (document.getElementById('siteroot')) {
		document.getElementById('siteroot').onchange = function() {
			location.href='?mod=cms&cmd=switchsiteroot&id='+document.getElementById('siteroot').value;
		}
		document.getElementById('menuitems').onchange = function() {
			eval(document.getElementById('menuitems').value);
			document.getElementById('menuitems').value = "void(0);";
		}
	}
}
function startLinkChecker() {
	popup('?mod=cms&action=startlinkchecker', 'linkchecker', 300, 200, 1);
}
function toggleSiteRootPublicState() {
	document.getElementById('cmd').value = 'toggle_siteroot_public_state';
	document.getElementById('velden').submit();
}
