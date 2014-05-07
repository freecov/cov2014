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
		saveRestorePoint();
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
var autosave_timer = '';
function init_autosave_timer() {
	if (document.getElementById('block_autosave').value == 0) {
		setInterval('update_autosave_timer()', 1000);
	} else if (document.getElementById('block_autosave').value == 1)  {
		document.getElementById('autosave_progressbar').innerHTML = '<b>' + gettext('herstelpunten opslaan tijdelijk uitgeschakeld. Maak eerst een keuze voor de herstelaktie.') + '</b>';
	}
}
function savePage() {
	clearInterval(autosave_timer);

	/* sync html contents */
	sync_editor_contents();
	if (document.getElementById('velden').onsubmit) {
		document.getElementById('velden').onsubmit();
	}

	document.getElementById('velden').action.value = 'savePage';
	document.getElementById('velden').submit();
}

function saveRestorePoint() {
	/* reset autosave data */
	autosave_val = autosave_timeout;
	document.getElementById('autosave_progressbar').innerHTML = '';

	/* if html */
	sync_editor_contents();
	if (document.getElementById('velden').onsubmit) {
		document.getElementById('velden').onsubmit();
	}

	document.getElementById('velden').action.value = 'saveRestorePoint';
	document.getElementById('velden').submit();
}

function focusTitle() {
	/* focus title */
	if (document.getElementById('cmspageTitle')) {
		document.getElementById('cmspageTitle').focus();
	}
	/* add blur event, event to start auto saving */
	document.getElementById('cmspageTitle').onblur = function() {
		init_autosave_timer();
	}
	document.getElementById('cmspageTitle').onkeydown = function() {
		init_autosave_timer();
	}
}

var restore_tx;
function loadRestorePoint() {
	var calle = "loadXML('?mod=cms&action=loadRestorePoint&id=" + document.getElementById('cmsid').value + "');";
	clearTimeout(restore_tx);
	restore_tx = setTimeout(calle, 100);
}

function truncateRestorePoint() {
	document.getElementById('autosave_info').style.display = 'none';
	document.getElementById('block_autosave').value = 0;
	loadXML('?mod=cms&action=truncateRestorePoint&id='+document.getElementById('cmsid').value);
	init_autosave_timer();
}

function closePage() {
	var cf = confirm(gettext("Close this page without saving?"));
	if (cf == true) {
		loadXML('?mod=cms&action=truncateRestorePoint&close_window=1&id='+document.getElementById('cmsid').value);
	}
}

var aliaschecktimer;
function checkAlias() {
	var alias = document.getElementById('cmspageAlias');
	var id = document.getElementById('cmsid').value;

	clearTimeout(aliaschecktimer);
	alias.value = alias.value.replace(/[^0-9a-z_\-]/gi, '');
	var ret = loadXMLContent('?mod=cms&action=checkalias&id='+id+'&alias='+alias.value);
	ret = ret.split('|');
	if (ret[0]==1) {
		document.getElementById('save_page_layer').style.visibility = 'visible';
	} else {
		document.getElementById('save_page_layer').style.visibility = 'hidden';
	}
	document.getElementById('alias_layer').innerHTML = ret[1];
}

document.getElementById('cmspageAlias').onkeydown = function() {
	clearTimeout(aliaschecktimer);
	aliaschecktimer = setTimeout('checkAlias();', 500);
}
addLoadEvent(checkAlias());
addLoadEvent(focusTitle());
