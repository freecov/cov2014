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

function trigger_newsletter() {
	var cur   = document.getElementById('current');
	var total = document.getElementById('total');

	var mail_id  = document.getElementById('mail_id').value;
	var datafile = document.getElementById('datafile').value;
	var from = document.getElementById('from').value;
	var newsletter_target = document.getElementById('newsletter_target').value;

	var cur_val = parseInt(cur.value);
	var total_val = parseInt(total.value);

	var ifr = document.getElementById('newsletter_sender');

	if (cur_val <= total_val) {
		ifr.src = "?mod=email&action=send_mail_queue&dl=1&id="+mail_id+"&datafile="+datafile+"&from="+from+"&newsletter_target="+newsletter_target;
	} else {
		alert(gettext("newsletter is sent."));
		setTimeout("location.href='?mod=email';", 500);
	}
}

function updateCurrentStatus(ret) {
	/*
	if (voip_ival)
		clearInterval(voip_ival);
	*/

	var cur   = document.getElementById('current');
	var total = document.getElementById('total');
	var mail_id  = document.getElementById('mail_id').value;

	//var ret = loadXMLContent('?mod=email&action=status_mail_queue&id='+mail_id);

	var perc = 0;
	var message = '';
	var total = document.getElementById('total');
	var progress = document.getElementById('newsletter_progressbar');

	var cur_val = parseInt(ret);
	var total_val = parseInt(total.value);

	if (cur_val == total_val) {
		var final = 1;
	} else {
		var final = 0;
	}

	perc = cur_val/total_val*100;
	perc = parseInt(perc);

	cur.value = cur_val;

	message = message.concat(perc, '% (', cur.value, ' of ', total.value, ')\n<br>');
	var xclass = '';
  for (i=0;i<=100;i++) {
  	xclass = 'progressborder';
  	if (i==0) {
  		xclass = xclass.concat(' progressleft');
  	} else if (i==100) {
  		xclass = xclass.concat(' progressright');
  	}
  	if (i<=perc) {
  		xclass = xclass.concat(' progressbar');
  	}
 		message = message.concat('<span class="', xclass ,'">&nbsp;</span>');
  }
	progress.innerHTML = message;

	if (final == 1) {
		setTimeout('update_final();', 2000);
	}
}


function update_final() {
	alert(gettext("Your newsletter has been sent"));
	//location.href='?mod=email';
	window.close();
}

addLoadEvent(setTimeout('trigger_newsletter();', 2000));

