/* history javascript handler         */
/* for identifying the current window */

/* get the history identifier to be updated */
function js_history_generate() {
	var history_identifier = document.getElementById('history_identifier').value;
	var window_name = '';

	if (window.name.match(/^covide\_/g)) {
		window_name = window.name;
	} else {
		var rand = new String(10000*Math.random());
		window_name = window_name.concat('covide_', rand.replace(/[^0-9]/g,''));
		window.name = window_name;
	}

	var descr = '';
	if (document.getElementById('venster_onderdeel')) {
		descr = descr.concat(document.getElementById('venster_onderdeel').innerHTML);
	}
	if (document.getElementById('venster_titel')) {
		descr = descr.concat(' - ',document.getElementById('venster_titel').innerHTML);
	}
	setTimeout("js_history_generate_generate_checkpoint('"+history_identifier+"','"+window_name+"','"+descr+"');", 10);

}

function js_history_generate_generate_checkpoint(history_identifier, window_name, descr) {
	loadXML("index.php?mod=history&id="+history_identifier+"&scope="+window_name+"&descr="+descr);
}
function js_history_retrieve() {
	alert('test');
}

function history_goback() {
	if (arguments[0]) {
		var steps = arguments[0];
	} else {
		var steps = 1;
	}
	if (steps < 1) {
		steps = 1;
	}
	//get window scope
	var url = '?mod=history&restorepoint=';
	eval('url = url.concat(' + window.name + ');');
	window.location.href = url.concat('&steps=', steps);
}