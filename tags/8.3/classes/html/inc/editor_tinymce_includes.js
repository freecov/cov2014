var skip_window_resize = false;
var skip_window_focus = false;

function onPreview() {
	mcTabs.displayTab('general_tab','general_panel');
	/* detect media window */
	if (document.getElementById('media_type')) {
		document.getElementById('src').value = document.getElementById('f_url').value;
		switchType(document.getElementById('src').value);
		generatePreview();
	} else {
		if (document.getElementById('f_url'))
			ImageDialog.showPreviewImage(document.getElementById('f_url').value);
	}

}
function init_iframe(stype) {
	var uri = '../../../../../';
	var infile = '';
	switch (stype) {
		case 'files':
			/* detect media popup */
			if (document.getElementById('media_type'))
				infile = document.getElementById('src').value;
			/* detect link popup */
			else if (document.getElementById('f_href'))
				infile = document.getElementById('f_href').value;
			/* detect other popup */
			else if (document.getElementById('f_url'))
				infile = document.getElementById('f_url').value;

			uri = uri.concat('?mod=cms&action=media_gallery&ftype=cmsfile&in=', infile);
			document.getElementById('ifr_cms_files').src = uri;
			break;
		case 'img':
			infile = document.getElementById('f_url').value;
			uri = uri.concat('?mod=cms&action=media_gallery&ftype=cmsimage&in=', infile);
			document.getElementById('ifr_cms_image').src = uri;
			break;
		case 'att':
			if (parent.document.getElementById('mod').value == 'email' && parent.document.getElementById('mailrelated_id')) {
				/* email */
				infile = parent.document.getElementById('id').value;
				uri = uri.concat('?mod=email&action=media_gallery&fullhtml=1&mail_id=', infile);
			}
			document.getElementById('ifr_mail_att').src = uri;
			break;
		case 'ban':
			/* cms banners */
			infile = document.getElementById('f_url').value;
			uri = uri.concat('?mod=cms&action=pick_banner&in=', infile);
			document.getElementById('ifr_cms_banner').src = uri;
			break;
		case 'pages':
			/* cms pages */
			infile = document.getElementById('f_href').value;
			uri = uri.concat('?mod=cms&action=cms_pagelist&in=', infile);
			document.getElementById('ifr_cms_pages').src = uri;
			break;
	}
}
function init_covide() {
	if (arguments[0])
		skip_window_resize = true;

	var tx = setTimeout('init_covide_exec();', 50);
	var tx = setTimeout('init_covide_exec();', 1000);
	var tx = setTimeout('init_covide_exec();', 2500);
}
function init_covide_exec() {
	/* add focus */
	if (!skip_window_focus) {
		var fld = new Array('src', 'f_href', 'f_url', 'cols');
		var fcs = false;
		for (i=0; i < fld.length; i++) {
			if (!fcs && document.getElementById(fld[i])) {
				document.getElementById(fld[i]).focus();
				fcs = true;
			}
		}
	}
	skip_window_focus = true;

	/* tab displays */
	if (parent.document.getElementById('mod').value == 'cms') {
		if (document.getElementById('covide_img_tab'))
			document.getElementById('covide_img_tab').style.display = '';
		if (document.getElementById('covide_ban_tab'))
			document.getElementById('covide_ban_tab').style.display = '';
		if (document.getElementById('covide_files_tab'))
			document.getElementById('covide_files_tab').style.display = '';
		if (document.getElementById('covide_pages_tab'))
			document.getElementById('covide_pages_tab').style.display = '';

	} else if (parent.document.getElementById('mod').value == 'email') {
		if (parent.document.getElementById('mailrelated_id')) {
			if (document.getElementById('covide_att_tab'))
				document.getElementById('covide_att_tab').style.display = '';
		}
		if (document.getElementById('covide_img_tab'))
			document.getElementById('covide_img_tab').style.display = '';
		if (document.getElementById('covide_files_tab'))
			document.getElementById('covide_files_tab').style.display = '';
	}
	/* adjust non-covide panel width */
	if (!skip_window_resize) {
		var panels = new Array('general_panel', 'advanced_panel', 'appearance_panel', 'popup_panel', 'events_panel');
		for (i=0; i < panels.length; i++) {
			if (document.getElementById(panels[i]))
				document.getElementById(panels[i]).style.width = '480px';
		}
	}
}