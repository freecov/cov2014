function trigger_migration() {
	var cur   = document.getElementById('current');
	var total = document.getElementById('total');

	var cur_val = parseInt(cur.value);
	var total_val = parseInt(total.value);

	if (cur_val <= total_val) {
		setTimeout("loadXML('?mod=email&action=migrate');", 10);
	} else {
		alert(total_val);
		alert(gettext("migration complete"));
		setTimeout("location.href='?mod=email';",2500);
	}
}
function update_stats(blocksize) {
	var perc = 0;
	var message = '';
	var cur   = document.getElementById('current');
	var total = document.getElementById('total');
	var progress = document.getElementById('migration_progressbar');

	var cur_val = parseInt(cur.value);
	var total_val = parseInt(total.value);

	cur_val += parseInt(blocksize);
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

	setTimeout("trigger_migration();",10);

}
trigger_migration();
