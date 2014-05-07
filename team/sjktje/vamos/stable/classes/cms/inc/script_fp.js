		var current_table_row = 0;

		function show_options_info(id) {
			loadXML('?mod=cms&action=show_options_info&id='+id);
		}

		function toggle_cms_table() {
			if (document.getElementById('cms_options_2').style.display == 'none')
				var setmode = '';
			else
				var setmode = 'none';

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

		function fp_addcol(img, code) {
			if (img)
				return code.concat('<td height="18"><img border="0" src="themes/default/icons/', img, '.png">&nbsp;</td>');
			else
				return code;
		}

		function fp(id, titel, rDelete, rView, rManage, rEdit, niveau, color, parentpage, uit, matched, locked, sticky, isactive, isredirect, ismenuitem, ispublic, istemplate, multidel, searchinfo, dateinfo, daterange, islijst, metainfo, isform, allowplak, isgallery, pageLabel, versionControl, theme, important){
			current_table_row++;
			var code = "";

			if (navigator.appVersion.indexOf("MSIE")!=-1){
				pageLabel = pageLabel.concat('&nbsp; &nbsp; &nbsp; ');
			}

			code = '<tr bgcolor="#ededed" onMouseOver="this.style.backgroundColor=\'#ffd7a6\';" ';
			code = code.concat('onMouseOut="this.style.backgroundColor=\'#ededed\';"> ');

			code = code.concat('<td class="bleft bbottom"');
			code = code.concat('><nobr>&nbsp;');


			var wz = 0;	wz = niveau*10;
			var wtitel = 0; wtitel = niveau*4+4;
			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wz+'" height="1">');

			// Enable the possibility to change the sortorder of pages in the sitemap.
			// This only applies to pages that are not a rootpage of the sitemap

			if (matched==1) {
				code = code.concat('<a name="matched"></a>');
			} else {
				code = code.concat('<a name="id'+id+'"></a>');
			}
			if (niveau > 0) {
				if (uit==0) {
					code = code.concat('<a href="javascript: cmsExpand(\''+id+'\');">');
					code = code.concat('<img width=10 height=10 src="img/cms/plus.gif" border=0 ');
					code = code.concat('alt="uitklappen"></a>');
				} else if (uit==1) {
					code = code.concat('<a href="javascript: cmsCollapse(\''+id+'\');">');
					code = code.concat('<img width=10 height=10 src="img/cms/min.gif" border=0 ');
					code = code.concat('alt="inklappen"></a>');
				} else {
					code = code.concat('<img width=10 height=10 src="img/cms/page.gif" border=0>');
				}
			}

			code = code.concat('&nbsp;</nobr></td><td class="content bbottom bleft" width="100%" ');
			code = code.concat(' bgcolor="'+color+'" onMouseOver="this.style.backgroundColor=\'#ffeed9\';" onMouseOut="this.style.backgroundColor=\''+color+'\';">');

			//var wx = 0;	wx = niveau*8;
			var wx = 0; wx = 3;

			code = code.concat('<table cellspacing="0" cellpadding="0" width="100%"><tr><td>');

			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wtitel+'" height="1">');
			if (parentpage != 0) {
				code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\''+id+'\',\'\');">'+titel+'</a>');
			} else {
				code = code.concat('<a class="sitemapLink" href="javascript: void(0);">'+titel+'</a>');
			}
			code = code.concat('</td><td class="bbottom bleft bright" align="right">');
			code = code.concat(pageLabel+'&nbsp;</td></tr></table></td>');

			// User only can edit pages only if they have manage or edit rights
			code = code.concat('<td>'+versionControl+'&nbsp;['+id+']&nbsp;</td><td valign="middle" align="left" class="bbottom bright" style="padding: 1px 3px 1px 3px;">');

			if (niveau == 0) {
				code = code.concat('</td><td>');
			} else {
				code = code.concat('<a class="sitemapLink" href="javascript:show_options_info('+id+');">');
				code = code.concat(document.getElementById('cms_icon_info').innerHTML + '</a></td><td>');

				//custom symbols
				code = code.concat('<a href="javascript: show_options_info(', id, ');" style="text-decoration: none;">');
				code = code.concat('<table cellspacing="0" cellpadding="0" style="display: none;" id="cms_options_', current_table_row , '"><tr>');

				if (isactive==1)
					code = fp_addcol('folder_green', code)
				else if (niveau > 0)
					code = fp_addcol('folder_red', code);

				if (ispublic==1)
					code = fp_addcol('kdmconfig', code);

				if (ismenuitem==1)
					code = fp_addcol('desktop', code);

				if (istemplate==1)
					code = fp_addcol('spreadsheet', code);

				if (isredirect==1)
					code = fp_addcol('legalmoves', code);

				if (searchinfo==1)
					code = fp_addcol('viewmag', code);

				if (dateinfo==1)
					code = fp_addcol('today', code);

				if (daterange==1)
					code = fp_addcol('clock', code);

				if (islijst==1)
					code = fp_addcol('view_sidetree', code);

				if (isform==1)
					code = fp_addcol('personal', code);

				if (metainfo==1)
					code = fp_addcol('pencil', code);

				if (sticky==1)
					code = fp_addcol('bottom', code);

				if (isgallery==1)
					code = fp_addcol('image', code);

				if (locked==1)
					code = fp_addcol('lock', code);

				code = code.concat('</tr></table></a>');


			}
			code = code.concat('</td><td valign="middle" align="right" class="bbottom bright" style="padding: 1px 3px 1px 3px;"><nobr>');

			if (important==1){
				code = code.concat(document.getElementById('cms_icon_important').innerHTML);
			} else {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif">');
			}

			if (rDelete==1){
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

				code = code.concat('<a class="sitemapLink" href="javascript:copyPages(\'index.php?mod=cms&cmd=copypages&id='+id+'\',\'("Weet u zeker dat u een kopie van de paginastructuur wilt maken?")\');">');
				code = code.concat(document.getElementById('cms_icon_copy').innerHTML + '</a>');

				if (allowplak == 0) {
					//code = code.concat('<input type="checkbox" name="knip_'+id+'" onchange="knipchk(this.checked,'+id+')">');
					//code = code.concat('<img style="cursor:hand;cursor:pointer;" hspace=2 src="img/cms/f_nee.gif" onclick="knipchk(this,'+id+')" alt="'+("(de)selecteer dit item")+'" title="'+("(de)selecteer dit item")+'">');
					code = code.concat('<input value="'+id+'" type="checkbox" name="page['+id+']" id="page_'+id+'">');
				}

			} else {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif" border="0">');
			}

			if (allowplak == 2) {
				//code = code.concat('<img width=16 height=16 src="img/cms/empty.gif" border="0"><a class="sitemapLink" href="javascript:plak('+id+')"><img width=15 height=15 src="img/cms/knop_plak.gif" border="0" alt="'+("plak de geknipte items hier")+'" title="'+("plak de geknipte items hier")+'"></a>');
				code = code.concat('<a class="sitemapLink" href="index.php?mod=cms&cmd=pastebuffer&id='+id+'">');
				code = code.concat(document.getElementById('cms_icon_paste').innerHTML + '</a>');

			}
			if (allowplak == 1) {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif" border="0">');
			}

			code = code.concat('</nobr></td></tr>');
			document.write (code);
		}
