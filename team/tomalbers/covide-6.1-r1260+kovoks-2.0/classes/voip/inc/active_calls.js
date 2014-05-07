function init_voip_layer(ret) {
	ret = unescape(ret);
	var el = document.getElementById('infovoip');

	/* if return var do not exist or contains the string "null" (Apple, KHTML?) */
	if (!ret || ret == 'null') {
		el.style.display = 'none';
	} else {
		el.style.display = 'block';

		var html = '<table><tr><td>' + document.getElementById('voip_image').innerHTML + '</td>';
		html = html.concat('<td><b>', gettext("Actieve telefoongesprekken"), ':</b></td></tr>');
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
}

function checkVoipCall() {
	if (window.init_voip_layer)
		loadXML('?mod=voip&action=active_calls', 'init_voip_layer(ret);', true);
}

/* call once now and schedule then */
var voip_init = setTimeout('checkVoipCall();',10);
var voip_ival = setInterval('checkVoipCall();', 2000);
