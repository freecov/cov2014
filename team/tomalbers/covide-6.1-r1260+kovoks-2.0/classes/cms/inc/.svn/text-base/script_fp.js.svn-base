		function fp(id, titel, rDelete, rView, rManage, rEdit, niveau, color, parentpage, uit, matched, locked, sticky, isactive, isredirect, ismenuitem, ispublic, istemplate, multidel, searchinfo, dateinfo, islijst, metainfo, isform, allowplak, isgallery, pageLabel, versionControl, theme, important){
			var code = "";

			if (navigator.appVersion.indexOf("MSIE")!=-1){
				pageLabel = pageLabel.concat('&nbsp; &nbsp; &nbsp; ');
			}

			code = '<tr bgcolor="#ededed" onMouseOver="this.style.backgroundColor=\'#ffd7a6\';" ';
			code = code.concat('onMouseOut="this.style.backgroundColor=\'#ededed\';"> ');

			code = code.concat('<td class="bleft bbottom"');
			code = code.concat('><nobr>&nbsp;');


			var wz = 0;	wz = niveau*10;
			var wtitel = 0; wtitel = niveau*4;
			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wz+'" height="1">');

			// Enable the possibility to change the sortorder of pages in the sitemap.
			// This only applies to pages that are not a rootpage of the sitemap

			if (matched==1) {
				code = code.concat('<a name="matched"></a>');
			} else {
				code = code.concat('<a name="id'+id+'"></a>');
			}
			if (niveau>0) {
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

			code = code.concat('&nbsp;</td><td class="content bbottom bleft" width="100%" ');

			code = code.concat(' bgcolor="'+color+'" onMouseOver="this.style.backgroundColor=\'#ffeed9\';" onMouseOut="this.style.backgroundColor=\''+color+'\';"><nobr>');

			//var wx = 0;	wx = niveau*8;
			var wx = 0; wx = 3;
			code = code.concat('<img src="img/cms/spacer1.gif" width="'+wx+'" height="1" align="left">');

			code = code.concat('<table width="100%" cellspacing="0" cellpadding="0"><tr><td>');
			if (parentpage!=0) {
				code = code.concat('<img src="img/cms/spacer1.gif" width="'+wtitel+'" height="1">');
				code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\''+id+'\',\'\');">'+titel+'</a>');
			} else {
				code = code.concat('<a class="sitemapLink" href="javascript: void(0);">'+titel+'</a>');
			}
			code = code.concat('</td><td align="right"><span class="sitemapLink">'+pageLabel+'&nbsp;</span></td></tr></table>');

			code = code.concat('</nobr></td><td class="bbottom bleft bright" align="right"><nobr>');

			// User only can edit pages only if they have manage or edit rights
			code = code.concat('<span class="sitemapLink">'+versionControl+'&nbsp;['+id+']&nbsp;</span></nobr></td><nobr><td valign="middle" align="left" class="bbottom bright" style="padding: 1px 3px 1px 3px;">');

			//custom symbols
			if (ispublic==1) {
				code = code.concat('<img width=10 height=15 src="img/cms/ispublic.gif" alt="'+("pagina is publiek toegankelijk")+'" title="'+("pagina is publiek toegankelijk")+'">');
			} else {
				code = code.concat('<img width=10 height=15 src="img/cms/empty.gif">');
			}
			if (ismenuitem==1) {
				code = code.concat('<img width=11 height=15 src="img/cms/ismenuitem.gif" alt="'+("pagina is een menu item")+'" title="'+("pagina is een menu item")+'">');
			} else {
				code = code.concat('<img width=11 height=15 src="img/cms/empty.gif">');
			}
			if (isactive==1) {
				code = code.concat('<img width=11 height=15 src="img/cms/isactive.gif" alt="'+("pagina is actief")+'" title="'+("pagina is actief")+'">');
			} else {
				code = code.concat('<img width=11 height=15 src="img/cms/empty.gif">');
			}
			code = code.concat('<img width=10 height=15 src="img/cms/empty.gif">');

			if (istemplate==1) {
				code = code.concat('<img width=13 height=15 src="img/cms/istemplate.gif" alt="'+("pagina is beschikbaar als template")+'" title="'+("pagina is beschikbaar als template")+'">');
			}
			if (isredirect==1) {
				code = code.concat('<img width=15 height=15 src="img/cms/isredirect.gif" alt="'+("pagina heeft een redirect")+'" title="'+("pagina heeft een redirect")+'">');
			}
			if (searchinfo==1) {
				code = code.concat('<img width=12 height=15 src="img/cms/perf_info.gif" alt="'+("pagina heeft aangepaste zoekmachine informatie")+'" title="'+("pagina heeft aangepaste zoekmachine informatie")+'">');
			}
			if (dateinfo==1) {
				code = code.concat('<img width=12 height=15 src="img/cms/uren.gif" alt="'+("pagina heeft een publicatie range")+'" title="'+("pagina heeft een publicatie range")+'">');
			}
			if (islijst==1) {
				code = code.concat('<img width=12 height=15 src="img/cms/lijst.gif" alt="'+("deze pagina heeft een lijst")+'" title="'+("deze pagina heeft een lijst")+'">');
			}
			if (isform==1) {
				code = code.concat('<img width=12 height=15 src="img/cms/formulier.gif" alt="'+("deze pagina heeft een formulier")+'" title="'+("deze pagina heeft een formulier")+'">');
			}
			if (metainfo==1) {
				code = code.concat('<img width=13 height=15 src="img/cms/meta.gif" alt="'+("deze pagina heeft metadata")+'" title="'+("deze pagina heeft metadata")+'">');
			}
			if (sticky==1) {
				code = code.concat('<img width=15 height=15 src="img/cms/ster.gif" alt="'+("pagina is niet bewerkbaar (sticky)")+'" title="'+("pagina is niet bewerkbaar (sticky)")+'">');
			}
			if (isgallery==1) {
				code = code.concat('<img width=15 height=15 src="img/cms/gallery.gif" alt="'+("pagina bevat een gallery")+'" title="'+("pagina bevat een gallery")+'">');
			}
			if (locked==1) {
				code = code.concat('<img width=15 height=15 src="img/cms/locked.gif" alt="'+("pagina bevat aangepaste rechten")+'" title="'+("pagina bevat aangepaste rechten")+'">');
			}

			code = code.concat('</nobr></td><td valign="middle" align="right" class="bbottom bright" style="padding: 1px 3px 1px 3px;"><nobr>');

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


			if (parentpage!=0) {
				if (rEdit==1) {
					code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\''+id+'\');">');
					code = code.concat(document.getElementById('cms_icon_edit').innerHTML + '</a>');
				} else {
					code = code.concat('<a class="sitemapLink" href="javascript:cmsEdit(\'cmsEditor\',\''+id+'\');">');
					code = code.concat(document.getElementById('cms_icon_show').innerHTML + '</a>');
				}
			} else {
				code = code.concat('<img hspace=1 width=16 height=16 src="img/cms/empty.gif" border="0">');
			}

			// User can delete pages only if they have manage or delete rights
			if (rDelete==1 && parentpage!=0 && sticky!=1){
				// Disable the possibility that an user deletes the sitemap root
				// otherwise sitemap generation will fail!
				if (multidel==0) {
					code = code.concat('<a class="sitemapLink" href="javascript:verwijder(\'index.php?mod=cms&cmd=deletepage&id='+id+'\',\'("Weet u zeker dat u deze pagina wilt verwijderen?")\');">');
				} else {
					code = code.concat('<a class="sitemapLink" href="javascript:verwijderMulti(\'index.php?mod=cms&cmd=deletepage&id='+id+'\',\'("Weet u zeker dat u deze pagina(s) wilt verwijderen?")\',\'("Hiermee wordt deze pagina inclusief ALLE onderliggende items verwijderd. Doorgaan?")\');">');
				}
				code = code.concat(document.getElementById('cms_icon_delete').innerHTML + '</a>');

				code = code.concat('<a class="sitemapLink" href="javascript:copyPages(\'index.php?mod=cms&cmd=copypages&id='+id+'\',\'("Weet u zeker dat u een kopie van de paginastructuur wilt maken?")\');">');
				code = code.concat(document.getElementById('cms_icon_copy').innerHTML + '</a>');

				if (allowplak==0) {
					//code = code.concat('<input type="checkbox" name="knip_'+id+'" onchange="knipchk(this.checked,'+id+')">');
					code = code.concat('<img style="cursor:hand;cursor:pointer;" hspace=2 src="img/cms/f_nee.gif" onclick="knipchk(this,'+id+')" alt="'+("(de)selecteer dit item")+'" title="'+("(de)selecteer dit item")+'">');
				}

			} else {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif" border="0">');
			}

			if (allowplak == 2) {
				code = code.concat('<img width=3 height=12 src="img/cms/empty.gif" border="0"><a class="sitemapLink" href="javascript:plak('+id+')"><img width=15 height=15 src="img/cms/knop_plak.gif" border="0" alt="'+("plak de geknipte items hier")+'" title="'+("plak de geknipte items hier")+'"></a>');
			}
			if (allowplak == 1) {
				code = code.concat('<img width=16 height=16 src="img/cms/empty.gif" border="0">');
			}

			code = code.concat('</td></tr>');
			document.write (code);
		}
