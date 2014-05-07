/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function cmsCheckPopupOptions() {
	if (document.getElementById('cmspageRedirectPopup').checked == true) {
		document.getElementById('popup_options').style.display = '';
	} else {
		document.getElementById('popup_options').style.display = 'none';
	}
}
function pickPage() {
	var infile = document.getElementById('cmspageRedirect').value;
	popup('?mod=cms&action=cms_pagelist&in=' + infile, 'pagepick', 640, 400, 1);
}
function cmsCheckSearchOptions() {
	if (document.getElementById('cmssearch_override').checked == true) {
		for (i=1;i<=4;i++) {
			document.getElementById('search'+i).style.display = '';
		}
	} else {
		for (i=1;i<=4;i++) {
			document.getElementById('search'+i).style.display = 'none';
		}
	}
}
function cmsCheckShopOptions() {
	if (document.getElementById('cmsisShop').checked == true) {
		document.getElementById('shop_options').style.display = '';
	} else {
		document.getElementById('shop_options').style.display = 'none';
	}
}
function cmsCheckShopPrice() {
	document.getElementById('cmsshopPrice').value = document.getElementById('cmsshopPrice').value.replace(/,/g, '.');
	document.getElementById('cmsshopPrice').value = document.getElementById('cmsshopPrice').value.replace(/[^0-9\.]/g, '');
}
addLoadEvent(cmsCheckPopupOptions());
addLoadEvent(cmsCheckSearchOptions());
addLoadEvent(cmsCheckShopOptions());

document.getElementById('cmspageRedirectPopup').onchange = function() {
	cmsCheckPopupOptions();
}
document.getElementById('cmssearch_override').onchange = function() {
	cmsCheckSearchOptions();
}
document.getElementById('cmsshopPrice').onchange = function() {
	cmsCheckShopPrice();
}
document.getElementById('cmsisShop').onchange = function() {
	cmsCheckShopOptions();
}
