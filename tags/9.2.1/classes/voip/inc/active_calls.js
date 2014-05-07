var voip_translations = [];
var active_calls_lastcall = '';

function init_voip_layer(ret) {
	ret = unescape(ret);

	var c;
	var i = new Array(0,0);
	var h = new Array('', '');
	var md = '0';

	var el = document.getElementById('infovoip');

	/* if return var do not exist or contains the string "null" (Apple, KHTML?) */
	if (!ret || ret == 'null' || ret == 'no license for this module') {
		el.style.display = 'none';
	} else {
		el.style.display = 'block';
		ret = ret.split('#');

		/* active calls block */
		for (j=0; j<ret.length-1; j++) {
			// split the record
			c = ret[j].split(/\|/);

			//h = h.concat(ret[j]); //debug!

			// if type is voip call
			if (voippopup && c[2] == 0) {
				i[0]++;
				if (i[0] == 1) {
					h[0] = h[0].concat('<table class="view_header"><tr><td colspan="8"><img src="themes/default/icons/data_telephone_business.png" border="0">&nbsp;');
					h[0] = h[0].concat('<b>', 'active voip calls', ':</b></td></tr>');
				}

				//timestamp
				h[0] = h[0].concat('<tr class="list_record_hover">');
				h[0] = h[0].concat('<td>', c[3], '</td>');
				if (c[0] > 0) {
					//name with relcard
					h[0] = h[0].concat('<td>', '<a href="?mod=address&action=relcard&id=', c[0], '">', c[1], '</td>');
					h[0] = h[0].concat('<td>', '<a href="?mod=address&action=relcard&id=', c[0], '"><img src="themes/default/icons/toggle_log.png" border="0"></td>');
				} else {
					//name without relcard (aka unknown)
					h[0] = h[0].concat('<td>', c[1], '</td>');
					h[0] = h[0].concat('<td>&nbsp;</td>');
				}
				h[0] = h[0].concat('</tr>');

			} else if (c[2] == c[6]) {
				// type is invite
				i[1]++;
				if (i[1] == 1) {
					h[1] = h[1].concat('<table class="view_header"><tr><td colspan="8"><img src="themes/default/icons/edu_languages.png" border="0">&nbsp;');
					h[1] = h[1].concat('<b>', 'chat invites', ':</b></td></tr>');
				}
				//timestamp
				h[1] = h[1].concat('<tr class="list_record_hover"><td colspan="2">', c[3], '</td>');

				//name with actions
				h[1] = h[1].concat('<td>', c[1], '</td>');

				//deny/accept
				if (c[4] == 1) {
					h[1] = h[1].concat('<td>', '<a href="');
					h[1] = h[1].concat('javascript: handleVoipAction(\'', c[8], '\',1)');
					h[1] = h[1].concat('"><img src="themes/default/icons/ok.png" border="0"></td>');
				}
				h[1] = h[1].concat('<td>', '<a href="');
				h[1] = h[1].concat('javascript: handleVoipAction(\'', c[8], '\',0)');

				if (c[4] == 2)
					h[1] = h[1].concat('"><img src="themes/default/icons/ok.png" border="0"></td>');
				else
					h[1] = h[1].concat('"><img src="themes/default/icons/cancel.png" border="0"></td>');
			}
			if (c[7] > 0)
				md = md.concat(',', c[8]);
		}
		// end blocks
		if (i[0] > 0)
			h[0] = h[0].concat("</table>");

		if (i[1] > 0)
			h[1] = h[1].concat("</table>");

		if (i[0] > 0 || i[1] > 0)
			el.innerHTML = h[0]+h[1];
		else
			el.style.display = 'none';

		if (md != '0') {
			popup('?mod=chat&action=notice&idents=' + md, 'chat_notice', 400, 350, 1);
		}
	}
	/* initialize a timer */
	setTimeout('getVoipCalls()', 35*1000);
}
function handleVoipAction(i, a) {
	if (a == 1) {
		/* positive, accept and open chat */
		var ret = loadXMLContent('?mod=chat&action=accept&ident=' + i);
		window.open('?mod=chat&ident='+i, 'chat_chan_'+i, 700, 670);
	} else {
		/* negative, remove record */
		var ret = loadXMLContent('?mod=chat&action=cancel&ident=' + i);
		checkVoipCall();
	}
}
function handleVoipResponse(ret) {
	ret = parseInt(unescape(ret));

	if (!ret)
		ret = 0;

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
	if (window.init_voip_layer) {
		loadXML(checkfile, 'handleVoipResponse(ret);', true);
	}
}

/* call once now and schedule then */
var voip_init = setTimeout('checkVoipCall();',10);

if (voip_poll_interval == 0)
	voip_poll_interval = 20; //default 2s

var voip_ival = setInterval('checkVoipCall();', voip_poll_interval * 1000);
