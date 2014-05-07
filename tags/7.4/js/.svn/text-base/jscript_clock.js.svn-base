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

	if (s % 30 == 0 || arguments[0]) {
		setTimeout("loadXML('classes/html/inc/time.php?t=1', '', true);",10);
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
function timeAlerts() {
	var alert_ival = setInterval('checkAlerts()', 5 * 60 * 1000);
}
function checkAlerts() {
	//get alerts ?
	loadXML('?mod=user&action=show_time', '', true);
}
function initClock() {
	startClock();
	timeAlerts();
}
addLoadEvent(initClock());
