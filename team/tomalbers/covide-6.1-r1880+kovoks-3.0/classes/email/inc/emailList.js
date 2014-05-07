/* attach an event handler to the select all input checkbox */
/* retrieve input checkbox element */
if (document.getElementById('checkbox_mail_toggle_all')) {
	document.getElementById('checkbox_mail_toggle_all').onclick = function() {
		mail_toggle_all( document.getElementById('checkbox_mail_toggle_all').checked );
	}
}
/* attach event handler to the select all attachments checkbox */
if (document.getElementById('checkbox_attachment_toggle_all')) {
	document.getElementById('checkbox_attachment_toggle_all').onclick = function() {
		attachment_toggle_all( document.getElementById('checkbox_attachment_toggle_all').checked );
	}
}
/* function set */
/* set a value to an element */
function set(k, v) {
	if (k=='action') {
		document.getElementById('velden').action.value = v;
	} else {
		document.getElementById(k).value = v;
	}
}

/* function submitForm */
/* submits the default form */
function submitform() {
	document.getElementById('velden').submit();
}

/* function: mail_toggle_all */
/*  toggle all mail checkbox items to the status set in the parameter */
function mail_toggle_all(set_to_status) {
	var frm = document.getElementById('velden');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_mail\[/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

/* function: attachment_toggle_all */
/*  toggle all attachment checkbox items to the status set in the parameter */
function attachment_toggle_all(set_to_status) {
	var frm = document.getElementById('velden');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_attachment\[/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

/* function: blader */
/* page through multiple pages in email view */
function blader(page) {
	document.getElementById('list_from').value = page;
	document.getElementById('velden').submit();
}

/* function: setFolder */
/* set and go to a specific folder */
function setFolder(id, archive) {
	set('list_from', '');
	set('search', '');

	if (archive==0) {
		document.getElementById('list_of_address').value = 0;
	}
	document.getElementById('folder_id').value = id;
	blader(0);
}

/* function: selection_attachments_delete */
/* delete the selected attachments */
function selection_attachments_delete() {
	var cf = confirm( gettext("Weet u zeker dat u de geselecteerde attachments wilt verwijderen?") );
	if (cf == true) {
		set('action', 'delete_multi_attachments');
		document.getElementById('velden').submit();
	}
}
/* function:  selection_email_delete */
/* delete the selected emails */
function selection_email_delete() {
	set('action', 'delete_multi');
	document.getElementById('velden').submit();
}

/* function: selection_attachments_download */
/* download the selected attachments */
function selection_attachments_download() {
	var frm = document.getElementById('velden');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_attachment\[/gi)) {
			if (frm.elements[i].checked == true) {
				add_download(frm.elements[i].name);
				frm.elements[i].checked = false;
			}
		}
	}
	document.getElementById('checkbox_attachment_toggle_all').checked = false;
}

/* function selection_attachments_zip */
/* download the selection as one zip file */
function selection_attachments_zip() {
	var frm = document.getElementById('velden');
	var ids = '0';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_attachment\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
				frm.elements[i].checked = false;
			}
		}
	}
	if (ids != 0) {
		add_download_zip(ids.replace(/^0,/g,''));
		document.getElementById('checkbox_attachment_toggle_all').checked = false;
	}
}

function selection_attachments_save() {
	var frm = document.getElementById('velden');
	var ids = '0';
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_attachment\[/gi)) {
			if (frm.elements[i].checked == true) {
				ids = ids.concat(',', frm.elements[i].name).replace(/[^0-9,]/g,'');
				frm.elements[i].checked = false;
			}
		}
	}
	ids = ids.replace(/^0,/g,'');
	if (ids != 0) {
		document.getElementById('checkbox_attachment_toggle_all').checked = false;
		popup('?mod=filesys&subaction=save_attachment&ids='+ids, 'attachment', 750, 550, 1);
	}
}

/* function add_download_zip */
/* queue one additional item in the download list */
function add_download_zip(attachment_ids) {
	var div = document.createElement('div');
	div.innerHTML = '<iframe src="?dl=1&mod=email&action=multi_download_zip&attachment_ids='+attachment_ids+'"></iframe>';
	document.getElementById('download_container').appendChild(div);
}

/* function add_download */
/* queue one additional item in the download list */
function add_download(attachment_id) {
	var div = document.createElement('div');
	attachment_id = attachment_id.replace(/[^0-9]/g,'');
	div.innerHTML = '<iframe src="?dl=1&mod=email&action=download_attachment&id='+attachment_id+'"></iframe>';
	document.getElementById('download_container').appendChild(div);
}


/* function selection_email_togglestate */
/* toggle the email selection */
function selection_email_togglestate() {
	set('action', 'toggle_state');
	document.getElementById('velden').submit();
}

/* function selection_email_move */
/* move the selected emails to another folder/relation/project */
function selection_email_move() {
	set('action', 'selection_move');
	document.getElementById('velden').submit();
}

/* function showInfo */
/* show additional information about the selected email */
function showInfo(id) {
	loadXML('?mod=email&action=show_info&id=' + id);
}

/* function createNewFolder */
/* create a new folder inside the current folder */
function createNewFolder() {
	var str = prompt( gettext("Geef de naam voor de map op") );
	if (str && str != null && str != 'null' && str != '') {
		set('action', 'create_folder');
		set('action_value', str);
		document.getElementById('velden').submit();
	}
}

/* function editCUrrentFolder */
/* changes the name of the currently selected folder */
function editCurrentFolder() {
	var str = prompt(gettext("Geef de nieuwe naam voor de map op:"));
	if (str && str != null && str != 'null' && str != '') {
		set('action', 'edit_folder');
		set('action_value', str);
		document.getElementById('velden').submit();
	}
}

/* function selectRelation */
/* changes the relation of the currently selected email */
function selectRelation(id, relation) {
	loadXML('index.php?mod=email&action=change_relation_xml_list&id='+id+'&new_relation='+relation);
}

/* function moveCurrentFolder */
/* moves the current folder to another folder */
function moveCurrentFolder() {
	set('action', 'move_folder');
	document.getElementById('velden').submit();
}

/* function deleteCurrentFolder */
/* deletes the current folder (if in deleted items */
function deleteCurrentFolder() {
	set('action', 'delete_folder');
	document.getElementById('velden').submit();
}

/* function mailTracking */
function showTracking(id) {
	popup('?mod=email&action=tracking&id='+id, 'mail_tracking', 860, 570, 1);
}

/* update project view onchange handler */
function updateProject() {
	var project_id = document.getElementById('project_id').value;
	var folder_id  = document.getElementById('folder_id').value;
	document.location.href = '?mod=email&project_id='+project_id+'&folder_id='+folder_id;
}
if (document.getElementById('project_id')) {
	document.getElementById('project_id').onchange = function() { updateProject(); }
}

function deleteMail(id) {
	/* uncheck all */
	mail_toggle_all( false );
	/* check the selected email */
	document.getElementById('checkbox_mail'+id).checked = true;
	/* delete the single selection */
	selection_email_delete();
}

function focusSearch() {
	if (document.getElementById('search')) {
		document.getElementById('search').focus();
	}
}

function toggle_shortview() {
	if (document.getElementById('short_view').value == 1) {
		document.getElementById('short_view').value = 0;
	} else {
		document.getElementById('short_view').value = 1;
	}
	submitform();
}
addLoadEvent(focusSearch);
