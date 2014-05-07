/* js include file executed at page end */

function client_rendertime() {
	var rendertime_end = new Date();
	var diff = rendertime_end.getTime() - rendertime_start.getTime();
	if (document.getElementById('performance_clienttime')) {
		document.getElementById('performance_clienttime').innerHTML = (Math.round(diff/10)/100);
	}
}

addLoadEvent(
	function() {
		var performance_timer = setTimeout("client_rendertime()", 10);
	}
);
