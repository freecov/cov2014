var clock = document.getElementById("clock");
var t;

function updateClockSeconds(s) {
	var str = new String();
	if (s < 10) {
		str = str.concat('0', s);
	} else {
		str = s;
	}
	document.getElementById('clock_seconds').innerHTML = str;
}
function updateClock() {
	var d = new Date();
	var s = d.getSeconds();

	if (s % 10 == 0) {
		setTimeout("loadXML('?mod=user&action=show_time', '', true);",10);
	}
	updateClockSeconds(s);
}

function startClock() {
	var d = new Date();
	var s = d.getSeconds();

	updateClockSeconds(s); /* update seconds now */
	t = setInterval('updateClock();',1000);
}

function stopClock() {
	if (t) {
		clearInterval(t);
	}
}

addLoadEvent(startClock());