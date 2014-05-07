function trigger_newsletter() {
	var cur   = document.getElementById('current');
	var total = document.getElementById('total');

	var mail_id  = document.getElementById('mail_id').value;
	var datafile = document.getElementById('datafile').value;
	var from = document.getElementById('from').value;
	var newsletter_target = document.getElementById('newsletter_target').value;

	var cur_val = parseInt(cur.value);
	var total_val = parseInt(total.value);

	if (cur_val <= total_val) {
		setTimeout("loadXML('?mod=email&action=send_mail_queue&id="+mail_id+"&datafile="+datafile+"&from="+from+"&newsletter_target="+newsletter_target+"');", 10);
		//alert("?mod=email&action=send_mail_queue&id="+mail_id+"&datafile="+datafile+"&from="+from+"&newsletter_target="+newsletter_target);
	} else {
		alert(gettext("nieuwsbrief verzonden"));
		setTimeout("location.href='?mod=email';",2500);
	}
}
function update_stats(block_size) {
	var perc = 0;
	var message = '';
	var cur   = document.getElementById('current');
	var total = document.getElementById('total');
	var progress = document.getElementById('newsletter_progressbar');

	var cur_val = parseInt(cur.value);
	var total_val = parseInt(total.value);

	cur_val += parseInt(block_size);
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

	setTimeout("trigger_newsletter();",10);
}
trigger_newsletter();

function update_final() {
	alert(gettext("Uw nieuwsbrief is verstuurd"));
	location.href='?mod=email';
}
