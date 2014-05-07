/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

		var current_table_row = 0;
		var current_date = new Date();

		function cms_toggle_check_all() {
			if (document.getElementById('checkbox_cms_toggle_all')) {
				document.getElementById('checkbox_cms_toggle_all').onclick = function() {
					cms_toggle_all( document.getElementById('checkbox_cms_toggle_all').checked );
				}
			}
		}
		function cms_toggle_all(set_to_status) {
			var frm = document.getElementById('velden');
			for (i=0;i<frm.elements.length;i++) {
				if (frm.elements[i].type == 'checkbox') {
					frm.elements[i].checked = set_to_status;
				}
			}
		}
		addLoadEvent(cms_toggle_check_all);

		function browse(offset) {
			document.getElementById('jump_to_anchor').value = 'id0';
			document.getElementById('offset').value = offset;
			cmsReload();
		}

		function show_options_info(id) {
			loadXML('?mod=cms&action=show_options_info&id='+id);
		}

		function toggle_cms_table() {
			if (document.getElementById('cms_options_2')) {
				if (document.getElementById('cms_options_2').style.display == 'none')
					var setmode = '';
				else
					var setmode = 'none';
			}

			/* set options state */
			document.getElementById('options_state').value = setmode;

			for (i=2; i<=current_table_row; i++) {
				document.getElementById('cms_options_'+i).style.display = setmode;
			}
		}
		function check_cms_table() {
			if (document.getElementById('options_state')) {
				if (document.getElementById('options_state').value != 'none') {
					toggle_cms_table();
				}
			} else {
				var ftx = setTimeout('check_cms_table();', 400);
			}
		}
		addLoadEvent(check_cms_table());

		var colImages = new Array();

		function fp_addcol(t, code) {
			if (t) {
				img = cms_actions_icons['cms_icon_'+t];

				if (navigator.userAgent.indexOf("MSIE 6.0") != -1) {
					return code.concat('&nbsp;<img border="0" src="img/spacer.gif" style="filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'', img ,'.png\', sizingMethod=\'image\');\">');
				} else {
					return code.concat('&nbsp;<img border="0" src="', img, '.png">');
				}
			} else {
				return code;
			}
		}
		function fp(id, titel, rDelete, rView, rManage, rEdit, niveau, color, parentpage, uit, matched, locked, sticky, isactive, isredirect, ismenuitem, ispublic, istemplate, multidel, searchinfo, dateinfo, daterange, islijst, metainfo, isform, allowplak, isgallery, pageLabel, versionControl, theme, important, address){
			current_table_row++;
			var code = "";

			/* tr + hover */
			code = '<tr style="height: 22px;" class="cms_hover color_type_record">';


			if (navigator.userAgent.indexOf('MSIE') != -1)
				code = code.concat('<td class="cms_right cms_level_msie cms_level cms_top3"><nobr>');
			else
				code = code.concat('<td class="cms_right cms_level cms_top3"><nobr>');

			if (parentpage == 0)
				code = code.concat('<img src="img/spacer.gif?v=1" class="cms_img">');

			/*
			var wz = 0;	wz = niveau*10;
			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wz+'" height="1">');
			*/
			for (i = 1; i < niveau; i++) {
				if (i == niveau -1)
					code = code.concat('<img src="img/cms/tree_mid.gif?v=1">');
				else
					code = code.concat('<img src="img/cms/tree_left.gif?v=1">');
			}
			var wtitel = 0; wtitel = niveau*4+4;

			// Enable the possibility to change the sortorder of pages in the sitemap.
			// This only applies to pages that are not a rootpage of the sitemap

			if (matched==1) {
				code = code.concat('<a name="matched"></a>');
			} else {
				if (parentpage == 0)
					code = code.concat('<a name="id0"></a>');
				else
					code = code.concat('<a name="id'+id+'"></a>');
			}
			if (niveau > 0) {
				switch (uit) {
					case 0:
						code = code.concat('<a href="javascript: cmsExpand(\''+id+'\');">');
						code = code.concat('<img src="img/cms/plus.gif?v=1" class="cms_img" ');
						code = code.concat(' alt="uitklappen"></a>');
						break;
					case 1:
						code = code.concat('<a href="javascript: cmsCollapse(\''+id+'\');">');
						code = code.concat('<img src="img/cms/min.gif?v=1" class="cms_img" ');
						code = code.concat('alt="inklappen"></a>');
						break;
					case -1:
						code = code.concat('<img src="img/cms/page.gif?v=1" class="cms_img">');
						break;
					case -2:
						code = code.concat('<img src="img/cms/page_struct.gif?v=1" class="cms_img">');
						break;
				}
			}
			if (parentpage == 0)
				code = code.concat('&nbsp;');

			code = code.concat('</nobr></td><td class="content cms_right cms_bottom '+color+'" width="100%">');

			//var wx = 0;	wx = niveau*8;
			var wx = 0; wx = 3;

			code = code.concat('<div style="float: left;">');
			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wtitel+'" height="1">');
			if (parentpage == 0 || rEdit == 0)
				code = code.concat(titel);
			else
				code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\''+id+'\',\'\');">'+titel+'</a>');

			code = code.concat('</div>');
			code = code.concat('<div style="float: right;">');
			code = code.concat('&nbsp;'+pageLabel);
			/*
			if (parentpage != 0) {
				code = code.concat('<a style="padding-left: 3px;" class="sitemapLink" href="javascript: cmsCollapse(-1, '+id+');">');
				code = code.concat('<img border="0" src="img/cms/plus_select.gif" alt="expand this tree" title="expand this tree"></a>');
			}
			*/
			code = code.concat('</div></td>');


			// User only can edit pages only if they have manage or edit rights
			code = code.concat('<td class="cms_right cms_bottom" align="right">&nbsp;'+id+'&nbsp;</td><td valign="middle" align="left" class="cms_top2" style="padding: 1px 3px 1px 3px;">');

			if (niveau == 0) {
				code = code.concat('&nbsp;</td><td class="cms_top2">&nbsp;');

			} else {
				code = code.concat('<a class="sitemapLink" href="javascript:show_options_info('+id+');">');
				code = code.concat(document.getElementById('cms_icon_info').innerHTML + '</a></td><td class="cms_top2">&nbsp;<nobr style="display: none;" id="cms_options_', current_table_row , '">');

				//custom symbols
				code = code.concat('<a alt="click for info" title="click for info" href="javascript: show_options_info(', id, ');" style="text-decoration: none;">');

				if (isactive==1)
					code = fp_addcol('isactive', code)
				else if (niveau > 0)
					code = fp_addcol('level', code);

				if (ispublic==1)
					code = fp_addcol('ispublic', code);

				if (ismenuitem==1)
					code = fp_addcol('ismenuitem', code);

				if (istemplate==1)
					code = fp_addcol('istemplate', code);

				if (isredirect==1)
					code = fp_addcol('isredirect', code);

				if (searchinfo==1)
					code = fp_addcol('searchinfo', code);

				if (dateinfo==1)
					code = fp_addcol('dateinfo', code);

				if (daterange==1)
					code = fp_addcol('daterange', code);

				if (islijst==1)
					code = fp_addcol('islist', code);

				if (isform==1)
					code = fp_addcol('isform', code);

				if (metainfo==1)
					code = fp_addcol('metainfo', code);

				if (sticky==1)
					code = fp_addcol('sticky', code);

				if (isgallery==1)
					code = fp_addcol('isgallery', code);

				if (locked==1)
					code = fp_addcol('locked', code);

				if (address==1)
					code = fp_addcol('address1', code);

				if (address==2)
					code = fp_addcol('address2', code);

				//code = code.concat('</tr></table></a>');


			}
			code = code.concat('<nobr></td><td valign="middle" align="right" class="cms_top2" style="padding: 1px 3px 1px 3px;"><nobr>');

			if (important == 1){
				code = code.concat(document.getElementById('cms_icon_important').innerHTML);
			} else {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif">');
			}

			if (rDelete == 1) {
				//delete and insert permissions are linked together
				code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\'\',\''+id+'\');">');
				code = code.concat(document.getElementById('cms_icon_new').innerHTML + '</a>');
			} else {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif">');
			}

			// User can delete pages only if they have manage or delete rights
			if (rDelete==1 && parentpage!=0 && sticky!=1){
				// Disable the possibility that an user deletes the sitemap root
				// otherwise sitemap generation will fail!
				code = code.concat('<a class="sitemapLink" href="javascript: popup(\'index.php?mod=cms&action=deletepage&id='+id+'\', \'deletepage\', 640, 500, 1);">');
				code = code.concat(document.getElementById('cms_icon_delete').innerHTML + '</a>');

				//if (allowplak == 0) {
					//code = code.concat('<input type="checkbox" name="knip_'+id+'" onchange="knipchk(this.checked,'+id+')">');
					//code = code.concat('<img style="cursor:hand;cursor:pointer;" hspace=2 src="img/cms/f_nee.gif" onclick="knipchk(this,'+id+')" alt="'+("(de)selecteer dit item")+'" title="'+("(de)selecteer dit item")+'">');
					code = code.concat('<input value="'+id+'" type="checkbox" name="page['+id+']" id="page_'+id+'">');
				//}

			} else {
				code = code.concat('<img width=38 height=16 src="img/cms/empty.gif" border="0">');
			}

			if (allowplak == 2) {
				code = code.concat('<a class="sitemapLink" href="index.php?mod=cms&cmd=pastebuffer&id='+id+'">');
				code = code.concat(document.getElementById('cms_icon_paste').innerHTML + '</a>');

				code = code.concat('<a class="sitemapLink" href="index.php?mod=cms&cmd=copybuffer&id='+id+'">');
				code = code.concat(document.getElementById('cms_icon_copy').innerHTML + '</a>');
			}
			if (allowplak == 1) {
				code = code.concat('<img width=36 height=16 src="img/cms/empty.gif" border="0">');
			}

			code = code.concat('</nobr></td></tr>');
			document.write (code);
		}