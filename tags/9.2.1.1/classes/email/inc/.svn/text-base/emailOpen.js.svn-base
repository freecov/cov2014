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

var hidden_fields = 1;
var description_check = '<b>- '+gettext("not saved")+' -</b>';
/* add an event handler to some objects */
/* add handler to user selection box */
if (document.getElementById('mailfolder')) {
	document.getElementById('mailfolder').onchange = function() {
		mail_update_folder();
	}
}

/* description onchange handler */
var description_timer;
if (document.getElementById('maildescription')) {
	document.getElementById('maildescription').onkeydown = function() {
		clearTimeout(description_timer);
		description_timer = setTimeout('upd_description_text();', 200);
	}
}
function upd_description_text() {
	if (document.getElementById('description_notify').innerHTML != description_check)
		document.getElementById('description_notify').innerHTML = description_check;
}

/* initialize the resize on window load */
if (document.getElementById('mailContent')) {
	var timer1 = setTimeout('mail_resize_frame();',5000);
	addLoadEvent(
		function() {
			if (timer1) {
				clearTimeout(timer1);
			}
			mail_resize_frame();
		}
	);
}

/* function set */
/* set a value to an element */
function set(k, v) {
	document.getElementById(k).value = v;
}

/* function submitForm */
/* submits the default form */
function submitform() {
	document.getElementById('velden').submit();
}

/* function mail_action */
/* email action handler */
function mail_action(a) {
	var id = document.getElementById('id').value;
	var viewmode = document.getElementById('viewmode').value;
	var from = document.getElementById('mailfrom').value;
	var relation = document.getElementById('mailrelation').value;
	var project = document.getElementById('mailproject').value;
	var popup_newwindow = document.getElementById('popup_newwindow').value;
	
	switch (a) {
		case 'new':
			/* go to the compose screen */
			if (popup_newwindow == 1) {
				oldpopup('?mod=email&action=compose', '', 980, 700, 1);
			} else {
				popup('?mod=email&action=compose', '', 980, 700, 1);
			}
			break;
		case 'info':
			/* show additional info */
			popup('?mod=email&action=headerinfo&id='+id, 'mail_info', 700, 350, 1);
			break;
		case 'print':
			/* print the email */
			window.open('?mod=email&action=print&id='+id, 'mail_print', 700, 400);
			break;
		case 'reply_all':
			/* reply to all (from + cc) */
			if (popup_newwindow == 1) {
				oldpopup('?mod=email&action=compose&ref_id='+id+'&ref_type=reply_all&viewmode='+viewmode+'&from='+from, '', 980, 700, 1);
			} else {
				popup('?mod=email&action=compose&ref_id='+id+'&ref_type=reply_all&viewmode='+viewmode+'&from='+from, '', 980, 500, 1);
			}
			break;
		case 'reply':
			/* reply to the sender */
			if (popup_newwindow == 1) {
				oldpopup('?mod=email&action=compose&ref_id='+id+'&ref_type=reply&viewmode='+viewmode+'&from='+from, '', 980, 700, 1);
			} else {
				popup('?mod=email&action=compose&ref_id='+id+'&ref_type=reply&viewmode='+viewmode+'&from='+from, '', 980, 500, 1);
			}
			break;
		case 'forward':
			/* forward this email */
			if (popup_newwindow == 1) {
				oldpopup('?mod=email&action=compose&ref_id='+id+'&ref_type=forward&viewmode='+viewmode+'&from='+from, '', 980, 700, 1);
			} else {
				popup('?mod=email&action=compose&ref_id='+id+'&ref_type=forward&viewmode='+viewmode+'&from='+from, '', 980, 500, 1);
			}
			break;
		case 'back':
			/* go back in history */
			history.go(-1);
			break;
		case 'delete':
			/* delete this email */
			/* first xml rpc call, then history call */
			loadXML('index.php?mod=email&action=delete_xml&id='+id);
			break;
		case 'tracking':
			popup('?mod=email&action=tracking&id='+id, 'mail_tracking', 700, 450, 1);
			break;
		case 'resend':
			if (popup_newwindow == 1) {
				oldpopup('?mod=email&action=resend&id='+id, new String().concat('mail_compose_', Math.random()), 980, 700, 1);
			} else {
				popup('?mod=email&action=resend&id='+id, new String().concat('mail_compose_', Math.random()), 980, 500, 1);
			}
			break;
		case 'support':
			popup('?mod=support&action=edit&id=0&mail_id='+id+'&relation_id='+relation+'&project_id='+project, 'mail_support', 900, 600, 1);
			break;
		case 'toggle_state':
			var newstate = loadXMLContent('index.php?mod=email&action=toggle_state_xml&mail_id='+id);
			/*
			if (newstate == 1) {
				document.getElementById('h_isnew').innerHTML=gettext('new');
			} else {
				document.getElementById('h_isnew').innerHTML=gettext('read');
			}
			*/
			break;
		case 'new_todo':
			popup('?mod=todo&action=edit_todo&hide=1&id=0&mail_id='+id+'&address_id='+relation+'&project_id='+project+'', 'mail_todo', 900, 600, 1);
			break;
	}
}

function handleMailtoLinks(mailto) {
	/* compos to address */
	var id = document.getElementById('id').value;
	var viewmode = document.getElementById('viewmode').value;
	var from = document.getElementById('mailfrom').value;
	popup('?mod=email&action=compose&ref_id='+id+'&ref_type=forward&viewmode='+viewmode+'&from='+from+'&to='+mailto, '', 980, 700, 1);
}

/* function mail_update_folder */
/* update mail folder with a xml request */
function mail_update_folder() {
	var mail_id     = document.getElementById('id').value;
	var mail_folder = document.getElementById('mailfolder').value;
	//setTimeout('description_save();',10);
	var descr       = escape( document.getElementById('maildescription').value );

	loadXML('index.php?mod=email&action=change_folder_xml&id=' + mail_id + '&folder=' + mail_folder + '&description=' + descr);
}

/* function selectRel */
/* select and update the relation */
function selectRel(id) {
	var mail_id = document.getElementById('id').value;
	setTimeout('description_save();',10);
	location.href='?mod=email&action=open&id=' + mail_id + '&new_relation=' + id;
}

/* function selectProject */
/* update the project */
function selectProject(id, name) {
	var mail_id = document.getElementById('id').value;
	var url = 'index.php?mod=email&action=change_project_xml&id=' + mail_id + '&project='+id;
	loadXML(url);
	document.getElementById('mailproject').value = id;
}

/* function selectPrivate*/
/* update the private */
function selectPrivate(id, name) {
	var mail_id = document.getElementById('id').value;
	var url = 'index.php?mod=email&action=change_private_xml&id=' + mail_id + '&private='+id;
	loadXML(url);
}

/* function mail_view_html */
/* switch between html and text view */
function mail_view_html(htmlmode) {
	document.getElementById('velden').viewmode.value = htmlmode;
	document.getElementById('velden').submit();
}

/* function mail_resize_frame */
/* resize the html iframe */
function mail_resize_frame() {
	var iframe = document.getElementById('mailContent');
	if (iframe.contentDocument) {
		var h = iframe.contentDocument.body.scrollHeight + 5;

		/* cap the height, as firefox has an buffer overflow with iframes > 32194? px */
		if (h > Math.pow(2,16-2)) {
			h = window.innerHeight-100;
			alert(gettext("Email to large, scrollbars are capped."));
		}
		iframe.style.height = h + 'px';
	} else {
		iframe.style.height = document.frames['mailContent'].document.body.scrollHeight + 6;
	}
}

/* function attachment */
/* function to show/download/remove attachments */
function attachment(id, subaction) {
	if (arguments[2]) {
		/* if triggered from the email list */
		var mail_id = arguments[2];
	} else {
		/* if triggered from the email open view */
		var mail_id = 0;
	}
	switch (subaction) {
		case 'view':
			/* view the attachment */
			popup('?mod=email&action=view_attachment&id='+id, 'attachment_info', 800, 550, 1);
			break;
		case 'download':
			/* download the attachment */
			location.href='?dl=1&mod=email&action=download_attachment&id='+id;
			break;
		case 'delete':
			/* delete the attachment */
			if (confirm(gettext("Are you sure you want to remove this attachment?"))==true) {
				location.href='?mod=email&action=delete_attachments&attachment_id='+id+'&id='+mail_id;
			}
			break;
	}
}

/* function description_save */
/* function to save the description */
function description_save() {
	var mail_id     = document.getElementById('id').value;
	var descr       = escape( document.getElementById('maildescription').value );
	loadXML('?mod=email&action=change_description&id=' + mail_id + '&description=' + descr);
}

/* function to copy the email to another user */
function user_copy() {
	var user_id     = document.getElementById('mailusers').value;
	var mail_id     = document.getElementById('id').value;
	loadXML('?mod=email&action=user_copy&id=' + mail_id + '&user_id=' + user_id);
}

/* function to move the email to another user */
function user_move() {

	if (arguments[0]==1) {
		var note = 1;
	} else {
		var note = 0;
	}

	var user_id     = document.getElementById('mailusers').value;
	var mail_id     = document.getElementById('id').value;
	var descr       = escape( document.getElementById('maildescription').value );

	loadXML('?mod=email&action=user_move&id=' + mail_id + '&user_id=' + user_id + '&note='+note+'&description='+descr);
}

/* multiple download of attachments as zip file */
function multiple_download() {
	var mail_id     = document.getElementById('id').value;
	location.href='index.php?dl=1&mod=email&action=multi_download_zip&mail_id='+mail_id;
}

/* toggle the state for public/private */
function toggle_private_state() {
	var mail_id     = document.getElementById('id').value;
	loadXML('?mod=email&action=toggle_private_state_xml&id='+mail_id);
}


function save_attachments(ids) {
	popup('?mod=filesys&subaction=save_attachment&ids='+ids, 'attachment', 750, 550, 1);
}

function pickProject() {
	var address_id = document.getElementById('mailrelation').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

function enable_inline_images() {
	var iframe = document.getElementById('mailContent');
	var uri = '?mod=email&action=viewhtml&no_filter=1&id=' + document.getElementById('id').value;
	iframe.src = uri;
	document.getElementById('js_show_inline').style.visibility = 'hidden';

}
function pickPrivate() {
	popup('?mod=address&action=searchRelPrivate', 'searchprivate', 650, 500, 1);
}

function show_extra_fields() {
	// find all elemements with 'list_hidden' style
	if (hidden_fields == 1) {
		var re = new RegExp('(^| )list_hidden( |$)');
		var newclass = 'list_hidden_no';
		var newstate = 0;
	} else {
		var re = new RegExp('(^| )list_hidden_no( |$)');
		var newclass = 'list_hidden';
		var newstate = 1;
	}
	var els = document.getElementsByTagName("*");
	for (i = 0; i < els.length; i++) {
		if(re.test(els[i].className)) {
			els[i].className=newclass;
		}
	}
	hidden_fields = newstate;
}

if (document.getElementById('emailactions')) {
	document.getElementById('emailactions').onchange = function() {
		eval(document.getElementById('emailactions').value);
		setTimeout("document.getElementById('emailactions').value = 'void(0);';", 200);
	}
}

function updateBcards() {
	fetch_url = '?mod=address&action=bcardsxml&address_id=' + document.getElementById('mailrelation').value + '&current=' + document.getElementById('mailbcard_selected').value;
	var ret = loadXMLContent(fetch_url);
	document.getElementById('mail_bcard_layer').innerHTML = ret;
	document.getElementById('projectbcard').onchange = function() {
		//ajax call to save the new bcard
		var mail_id     = document.getElementById('id').value;
		var bcard_id    = document.getElementById('projectbcard').value;
		url = 'index.php?mod=email&action=save_bcard_xml&mail_id='+mail_id+'&bcard_id='+bcard_id;
		loadXML(url);
	}
}

if (document.getElementById('readstatus')) {
	document.getElementById('readstatus').onchange = function() {
		mail_action('toggle_state');
	}
}
