/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

/* autosave handler */
/* the email has the feature 'autosave' */
/* set some timeouts and save the email every x seconds in the background */
var autosave_timeout = 60;
var autosave_val = autosave_timeout;
var autosave_scale = 10; // (100 / scale) must be an integer
var concept_interval;

/* function to trigger the handler */
function update_autosave_timer() {
	autosave_val--; //minus one second
	var m = '';

	if (autosave_val+autosave_scale <= 0) {
		/* save to db */
		mail_save_db();

	} else {
		/* just update the timer display */
		var perc = parseInt( (autosave_val/autosave_timeout)*100/autosave_scale)+1;
		var xclass = '';

  	for (i=0;i<=100/autosave_scale;i++) {
  	xclass = 'progressborder';
  	if (i==0) {
  		xclass = xclass.concat(' progressleft');
  	} else if (i==100/autosave_scale) {
  		xclass = xclass.concat(' progressright');
  	}
  	if (i<=perc) {
  		xclass = xclass.concat(' progressbar');
  	}
 		m = m.concat('<span class="', xclass ,'">&nbsp;</span>');
  }

	/* display info on screen */
	document.getElementById('autosave_progressbar').innerHTML = m;
	}
}
/* initialize the timer */
var autosave_timer = setInterval('update_autosave_timer()', 1000);

/* set value handler */
function set(k, v) {
	document.getElementById(k).value = v;
}

/* submit the complete the form */
function submitform() {
	document.getElementById('velden').submit();
}

/* convert email to/from html<>text */
function mail_convert() {
	if (document.getElementById('is_text').value == 0) {
		if (confirm(gettext("Converting html to text can cause loss of data. Continue?"))) {
			document.getElementById('convert_on_save').value = 1;
			document.getElementById('js_command').value = "location.href='?mod=email&action=compose&id=" + document.getElementById('id').value + "'";
			mail_save_db();
		}
	} else {
		document.getElementById('convert_on_save').value = 1;
		document.getElementById('js_command').value = "location.href='?mod=email&action=compose&id=" + document.getElementById('id').value + "'";
		mail_save_db();
	}
}

/* save a new selected relation */
function selectRel(id, str) {
	var mail_id = document.getElementById('id').value;
	loadXML('?mod=email&action=change_relation_xml&id=' + mail_id + '&new_relation=' + id +'&update='+escape(str));
}

/* find correct link to show pick_project screen */
function pickProject() {
	var address_id = document.getElementById('mailrelation').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

/* save and select the project */
function selectProject(id) {
	var mail_id = document.getElementById('id').value;
	loadXML('index.php?mod=email&action=change_project_xml&id=' + mail_id + '&project='+id);
}

/* add an additional field to the upload view */
function add_upload_field() {
	var div = document.createElement('div');
	div.innerHTML = document.getElementById('uploadcode').innerHTML.replace(/ id=\"[^\"]*?\"/gi,'');
	document.getElementById('moreuploadcode').appendChild( div );
}

/* upload all selected (local) files */
function mail_upload_files() {
	document.getElementById('uploadform').submit();
	document.getElementById('upload_msg').style.visibility = 'visible';
	document.getElementById('upload_controls').style.visibility = 'hidden';
}

/* reset the upload status to initial */
function reset_upload_status() {
	document.getElementById('binFile').value = '';
	document.getElementById('moreuploadcode').innerHTML = '';
	document.getElementById('upload_msg').style.visibility = 'hidden';
	document.getElementById('upload_controls').style.visibility = 'visible';

	/* refresh uploaded files view */
	mail_upload_update_list();
}

/* delete an attachment */
function attachment_delete(id) {
	/* xmlrpc attachment_delete */
	loadXML('?mod=email&action=delete_attachments_xml&id=' + id);
}

/* update the attachment list from the server */
function mail_update_list(ret) {
	document.getElementById('mail_attachments').innerHTML = unescape(ret);
}

/* xml call to update the attachment list */
function mail_upload_update_list() {
	var mail_id = document.getElementById('id').value;
	//loadXML('?mod=email&action=upload_list&id=' + mail_id, 'mail_update_list(ret);');
	var ret = loadXMLContent('?mod=email&action=upload_list&id=' + mail_id);
	mail_update_list(ret);
}

/* send the current email */
function mail_send() {
	/* check if a valid email address is entered */
	var rcpt = '';
	var ok = false;
	var subject_ok = false;

	if (document.getElementById('mailrcpt')) {
		rcpt = document.getElementById('mailrcpt').value.split(',');
		for (i=0; i<rcpt.length; i++) {
			if (rcpt[i].match(/^.*@.*\..*$/g)) {
				ok = true;
			}
		}
	} else {
		ok = true;
	}
	if (ok == false) {
		alert(gettext("No valid recipient found"));
	} else {

		if (document.getElementById('mailsubject').value == gettext('geen onderwerp') || !document.getElementById('mailsubject').value) {
			if (confirm(gettext('Weet u zeker dat u deze email zonder onderwerp wilt versturen?')) == true) {
				subject_ok = true;
			} else {
				document.getElementById('mailsubject').value = '';
				document.getElementById('mailsubject').focus();
			}
		} else {
			subject_ok = true;
		}
		if (subject_ok == true) {
			document.getElementById('saved').value = 0;
			document.getElementById('js_command').value = "location.href='index.php?mod=email&action=mail_send&dl=1&id="+document.getElementById('id').value+"';";
			mail_save_db();
		}
	}
}
/* exec the send mail command */
/*
function mail_send_exec() {
	if (document.getElementById('saved').value == 1) {
		document.getElementById('saved').value == 0;
		clearInterval(concept_interval);
		location.href='index.php?mod=email&action=mail_send&id=' + document.getElementById('id').value;
	}
}
*/


/* template handler for templates */
function template_handler() {
	if (document.getElementById('mailtemplate')) {
		val = document.getElementById('mailtemplate').value;
		mid = document.getElementById('id').value;
		if (val == 0) {
			document.getElementById('template_view').style.display = 'none';
			document.getElementById('template_view_standard').style.display = 'none';
		} else {
			document.getElementById('template_view_standard').style.display = 'block';
			document.getElementById('template_view').style.display = 'block';
			document.getElementById('template_view').innerHTML = loadXMLContent('?mod=email&action=templateOutput&id='+val+'&mid='+ mid);
		}
	}
}

function add_attachment_covide(ids) {
	var mail_id = document.getElementById('id').value;
	loadXML('?mod=email&action=add_attachment_covide&mail_id='+mail_id+'&ids='+ids);
}
function add_attachment_google(id) {
	var mail_id = document.getElementById('id').value;
	loadXML('?mod=google&action=gattach&mail_id='+mail_id+'&id='+id);
}

function mail_save_db() {
	/* reset autosave data */
	autosave_val = autosave_timeout;
	document.getElementById('autosave_progressbar').innerHTML = '';

	if (document.getElementById('is_text').value == 0) {
		/* if html */
		if (window.sync_editor_contents) {
			sync_editor_contents();
		}
		if (window.updateTextArea) {
			updateTextArea('contents');
		}
		if (document.getElementById('velden').onsubmit) {
			document.getElementById('velden').onsubmit();
		}
	}
	document.getElementById('velden').submit();
}

/* trigger attachment update function once */
mail_upload_update_list();

/* call once at load and attach event on selectbox onchange */
addLoadEvent('template_handler();');
if (document.getElementById('mailtemplate')) {
	document.getElementById('mailtemplate').onchange = function() { template_handler(); }
}

/* toggle the state for public/private */
function toggle_private_state() {
	var mail_id     = document.getElementById('id').value;
	loadXML('?mod=email&action=toggle_private_state_xml&id='+mail_id);
}

function showCcBcc() {
	document.getElementById('mail_cc_layer').style.display = '';
	document.getElementById('mail_bcc_layer').style.display = '';
	document.getElementById('mail_toggle_layer').style.display = 'none';
}

function add_from_filesys() {
	var relation = document.getElementById('mailrelation').value;
	if (document.getElementById('mailproject_id')) {
		var project = document.getElementById('mailproject_id').value;
	} else {
		var project = 0;
	}
	popup('?mod=filesys&subaction=add_attachment&address='+relation+'&project='+project, 'attachment', 950, 550, 1);
}

function setRcptCursor() {
	if (document.getElementById('mailrcpt')) {
		document.getElementById('mailrcpt').focus();
	}
}
function eraseSubject() {
	if (document.getElementById('mailsubject').value == gettext('geen onderwerp')) {
		document.getElementById('mailsubject').value = '';
	}
}
if (document.getElementById('mailsubject')) {
	document.getElementById('mailsubject').onclick = function() { eraseSubject(); }
}

addLoadEvent(setRcptCursor);
addLoadEvent(template_handler);
