var active_calls_translation = '';
var active_calls_lastcall = '';

function init_voip_layer(ret) {
	ret = unescape(ret);
	var el = document.getElementById('infovoip');

	/* if return var do not exist or contains the string "null" (Apple, KHTML?) */
	if (!ret || ret == 'null') {
		el.style.display = 'none';
	} else {
		el.style.display = 'block';

		if (!active_calls_translation)
			active_calls_translation = gettext("Active phonecalls");

		var html = '<table><tr><td>' + document.getElementById('voip_image').innerHTML + '</td>';
		html = html.concat('<td><b>', active_calls_translation, ':</b></td></tr>');
		var c = '';

		ret = ret.split('#');
		for (i=0; i<ret.length-1;i++) {
			c = ret[i].split(/\|/);
			if (c[0]) {
				html = html.concat("<tr><td colspan=\"2\"><a href='?mod=address&action=relcard&id=", c[0], "'>", c[1], "</a></td></tr>");
			} else {
				html = html.concat("<tr><td colspan=\"2\">", c[1], "</td></tr>");
			}
		}
		html = html.concat("</table>");
		el.innerHTML = html;
	}
	/* initialize a timer */
	setTimeout('getVoipCalls()', 35*1000);
}
function handleVoipResponse(ret) {
	ret = parseInt(unescape(ret));
	if (ret != active_calls_lastcall) {
		active_calls_lastcall = ret;
		getVoipCalls();
	}
}
function getVoipCalls() {
	loadXML('?mod=voip&action=active_calls', 'init_voip_layer(ret);', true);
}
function checkVoipCall() {
	var checkfile = new String();
	checkfile = checkfile.concat('tmp/lastcall_', covide_code, '.txt?c=1');
	if (window.init_voip_layer)
		loadXML(checkfile, 'handleVoipResponse(ret);', true);
}

/* call once now and schedule then */
var voip_init = setTimeout('checkVoipCall();',10);
var voip_ival = setInterval('checkVoipCall();', 2000);