var clock = document.getElementById("clock");
var tclock;

function updateClockSeconds(s) {
	var str = new String();
	if (s < 10) {
		str = str.concat('0', s);
	} else {
		str = parseInt(s);
	}
	clock_start = s;
	document.getElementById('clock_seconds').innerHTML = str;
}
function updateClock() {
	var s = parseInt(clock_start);
	if (s >= 59) {
		var updt = 1; //wait for server clock update!
		s = 0;
	} else {
		var updt = 0; s += 1;
	}
	if (s % 15 == 0 || s == 59 || arguments[0])
		loadXML('classes/html/inc/time.php?t='+updt, '', true);

	if (s < 60)
		updateClockSeconds(s);
}
function startClock() {
	var s = parseInt(clock_start);

	updateClockSeconds(s); /* update seconds now */
	tclock = setInterval('updateClock();',1000);
}
function stopClock() {
	if (tclock) {
		clearInterval(tclock);
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
