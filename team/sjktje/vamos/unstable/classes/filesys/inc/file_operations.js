
/* function set */
/* set a value to an element */
function set(k, v) {
	if (k=='action') {
		document.getElementById('velden').action.value = v;
	} else {
		document.getElementById(k).value = v;
	}
}

function submitform() {
	document.getElementById('velden').submit();
}

function download(id) {
	url = 'index.php?dl=1&mod=filesys&action=fdownload&id='+id;
	document.location.href=url;
}

function file_remove(id, folderid) {
	url = 'index.php?mod=filesys&action=fremove&fileid='+id+'&folderid='+folderid;
	if (confirm(gettext("Are you sure you want to remove this file?"))) {
		document.location.href=url;
	}
}

function file_remove_multi() {
	document.getElementById('velden').action.value = 'fremove_multi';
	document.getElementById('velden').submit();
}

function file_edit(id) {
	url = 'index.php?mod=filesys&action=fedit&fileid='+id;
	popup(url, 'fedit', 550, 300, 1);
}

function editFolder(id) {
	url = 'index.php?mod=filesys&action=folderedit&folder='+id;
	popup(url, 'fedit', 550, 300, 1);
}

function editPermissions(id) {
	url = 'index.php?mod=filesys&action=set_permissions&folder='+id;
	popup(url, 'fedit', 790, 450, 1);
}

function deleteFolder(id) {
	url = 'index.php?mod=filesys&action=delete_folder&folder='+id;
	popup(url, 'fedit', 790, 450, 1);
}

function cutFolder(id) {
	url = 'index.php?mod=filesys&action=cut_folder&folder='+id;
	popup(url, 'fedit', 790, 450, 1);
}

function fsave() {
	var f = document.getElementById('fedit');
	f.submit();
}

function selection_undo() {
	document.getElementById('pastebuffer').value = '';
	document.getElementById('velden').submit();
}
function selection_paste() {
	var url = '';
	url = '?mod=filesys&action=paste_exec';
	url = url.concat('&pastebuffer=', document.getElementById('pastebuffer').value, '&id=', document.getElementById('id').value);
	document.location.href=url;
}

function filesys_upload_files() {
	document.getElementById('uploadform').submit();
	document.getElementById('upload_msg').style.visibility = 'visible';
}

function reset_upload_status() {
	document.location.href = document.location.href;
}

function filesys_create_dir() {
	document.getElementById('createfolder').submit();
}

function blader(start) {
	document.getElementById('velden').top.value = start;
	document.getElementById('velden').submit();
}

/* permission specific functions */
function permissions_action(val){
	document.getElementById('velden').subaction.value = val;
	document.getElementById('velden').submit();
}
function permissions_add_read(){
	permissions_action('add_read');
}
function permissions_del_complete(){
	permissions_action('del_complete');
}
function permissions_add_write(){
	permissions_action('add_write');
}

if (document.getElementById('checkbox_files_toggle_all')) {
	document.getElementById('checkbox_files_toggle_all').onclick = function() {
		files_toggle_all( document.getElementById('checkbox_files_toggle_all').checked );
	}
}

function files_toggle_all(set_to_status) {
	var frm = document.getElementById('velden');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_file\[/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

/* multi download operations */

/* function add_download_zip */
/* queue one additional item in the download list */
function add_download_zip(ids) {
	var div = document.createElement('div');
	div.innerHTML = '<iframe src="?dl=1&mod=filesys&action=multi_download_zip&ids='+ids+'"></iframe>';
	document.getElementById('download_container').appendChild(div);
}

/* function add_download */
/* queue one additional item in the download list */
function add_download(id) {
	var div = document.createElement('div');
	id = id.replace(/[^0-9]/g,'');
	div.innerHTML = '<iframe src="?dl=1&mod=filesys&action=fdownload&id='+id+'"></iframe>';
	document.getElementById('download_container').appendChild(div);
}

/* function: selection_attachments_download */
/* download the selected attachments */
function selection_files_download() {
	var frm = document.getElementById('velden');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_file\[/gi)) {
			if (frm.elements[i].checked == true) {
				add_download(frm.elements[i].name);
				frm.elements[i].checked = false;
			}
		}
	}
	document.getElementById('checkbox_files_toggle_all').checked   = false;
	if (document.getElementById('checkbox_folders_toggle_all')) {
		document.getElementById('checkbox_folders_toggle_all').checked = false;
	}
}

/* function selection_attachments_zip */
/* download the selection as one zip file */
function selection_files_zip() {
	var frm = document.getElementById('velden');
	var ids = '0';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_file\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
				frm.elements[i].checked = false;
			}
		}
	}
	add_download_zip(ids.replace(/^0,/g,''));
	document.getElementById('checkbox_files_toggle_all').checked   = false;
	if (document.getElementById('checkbox_folders_toggle_all')) {
		document.getElementById('checkbox_folders_toggle_all').checked = false;
	}
}


function selection_files_move() {
	var frm = document.getElementById('velden');
	var ids = 'file';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_file\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
				frm.elements[i].checked = false;
			}
		}
	}
	if (ids == 'file') {
		alert(gettext('No files selected.'));
	} else {
		document.getElementById('velden').pastebuffer.value = 'file'+ids;
		document.getElementById('velden').submit();
	}
}

function file_attach_multi() {
	var frm = document.getElementById('velden');
	var ids = '0';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_file\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
				frm.elements[i].checked = false;
			}
		}
	}
	ids = ids.replace(/^0,/g,'');
	opener.add_attachment_covide(ids);
	window.close();
}

function save_attachment() {
	var description = prompt(gettext("Supply a description"));
	if (typeof(description) == "string") {
		document.getElementById('description').value = description;
		document.getElementById('velden').action.value = 'save_attachment';
		document.getElementById('velden').submit();
	}
}

/* save a fax in the filesys */
function save_fax() {
	var description = prompt(gettext("Supply a description"));
	if (typeof(description) == "string") {
		document.getElementById('description').value = description;
		document.getElementById('velden').action.value = 'save_fax';
		document.getElementById('velden').submit();
	}
}
/* view a file */
function view_file(id) {
	popup('?mod=filesys&action=view_file&id='+id, 'file_info', 800, 550, 1);
}

/* set a search command */
function setSearch(cmd) {
	document.getElementById('search').value = cmd;
	set('top', '0');
	submitform();
}
function cmsPreview(id, webroot) {
	if (parent.document.getElementById('f_href')) {
		parent.document.getElementById('f_href').value = webroot + 'cmsfile/'+id;
	} else {
		parent.document.getElementById('f_url').value = webroot + 'cmsfile/'+id;
		parent.onPreview();
	}
	void(0);
}

/* cmsEdit */
function cmsEdit(controller, id, parentpage) {
	popup('?mod=cms&action=editpage&id='+id+'&parentpage='+parentpage, controller, 990, 680, 1);
}
function create_subdir(id) {
	url = 'index.php?mod=filesys&action=create_subdir&id='+id;
	popup(url, 'createsubdir', 620, 250, 1);
}