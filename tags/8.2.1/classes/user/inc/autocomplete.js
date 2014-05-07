/* some variables we use global */
var user_complete_timer;
var user_complete_field;
var user_complete_span;
var user_complete_target;
var user_complete_str;
var user_complete_archive;
var user_complete_multi;
var user_complete_noempty;
var user_complete_groups;
var user_complete_confirm;
var user_complete_archive_uid;
var user_complete_initial = 1;
var user_complete_calendar;

/* caller function */
function user_select_popup(id, multiple, showInActive, showEmpty, showArchiveUser, no_empty, showGroups, confirmation, showCalendar) {
	user_complete_initial = 0;
	var str = 'index.php?mod=user&action=pick_user&sub_action=init&field_id=';
	popup(str.concat(id, '&multiple=', multiple, '&inactive=', showInActive, '&empty=', showEmpty, '&archive=', showArchiveUser, '&no_empty=', no_empty, '&showGroups=', showGroups, '&confirm=', confirmation, '&showCalendar=', showCalendar), 'user_select', 700, 410, 1);
}

/* function to erase the completion layer contents */
function user_clear_layer() {
	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		var ifr = document.getElementById('user_layer_iframe');
		ifr.style.display = 'none';
		ifr.style.top = 0;
		ifr.style.left = 0;
		ifr.style.width = '0px';
		ifr.style.height = '0px';
	}
	//end fix

	clearTimeout(user_complete_timer);
	document.getElementById('user_layer_autocomplete').style.visibility = 'hidden';
}

function remove_user(id, ltarget, lspan, override_noempty) {
	var el_target = document.getElementById(ltarget);
	var el_span   = document.getElementById(lspan);

	var tg = el_target.value.replace(/^,/g,'').split(',');
	var sp = el_span.innerHTML.split(/<LI/gi);

	if (override_noempty) {
		user_complete_noempty = override_noempty;
	}

	/* if array count is only one, do not allow deletes */
	if (tg.length == 1 && user_complete_noempty == 1) {
		alert(gettext("You should at least pick a user."));
	} else {
		for (i=0;i<tg.length;i++) {
			if (tg[i] == id) {
				tg.splice(i,1);
				if (navigator.appVersion.indexOf("MSIE")!=-1) {
					sp.splice(i,1);
				} else {
					sp.splice(i+1,1);
				}
			}
		}
		if (tg.length == 0) {
			el_span.innerHTML = '';
		} else {
			if (navigator.appVersion.indexOf("MSIE")!=-1) {
				el_span.innerHTML = '<LI' + sp.join('<LI');
			} else {
				el_span.innerHTML = sp.join('<LI');
			}
		}
		el_target.value = tg.join(',');
	}
}

function user_set_complete(str, userid, archive) {
	/* retrieve hidden field and span contents */
	var el_users = document.getElementById(user_complete_target);
	var el_span  = document.getElementById(user_complete_span);

	/* retrieve id's */
	var users = el_users.value;
	users = users.replace(/\|/g, ',');

	/* sometimes the first element is empty */
	users = users.replace(/^,/g, '');

	/* split by comma */
	users = users.split(',');

	var list = el_span.innerHTML;

	if (user_complete_multi==1) {
		/* multi usermode */
		var found = 0;
		for (i=0;i<users.length;i++) {
			if (users[i]==userid) {
				found = 1;
			}
		}
		if (found==0) {
			/* add to array */
			users[i] = userid;
			if (user_complete_archive_uid == userid) {
				list = list.concat("<li class='special'>");
			} else if (userid.match(/^G/g)) {
				list = list.concat("<li class='group'>");
			} else {
				list = list.concat("<li class='enabled'>");
			}
			if (user_complete_confirm) {
				list = list.concat(str, " <a onclick=\"return confirm(gettext('Are you sure you want to remove this user / group?'));\" href=\"javascript: remove_user('"+userid+"', '"+user_complete_target+"', '"+user_complete_span+"', '"+user_complete_noempty+"');\">[X]</a>");
			} else {
				list = list.concat(str, " <a href=\"javascript: remove_user('"+userid+"', '"+user_complete_target+"', '"+user_complete_span+"', '"+user_complete_noempty+"');\">[X]</a> ");
			}
		}
		el_span.innerHTML = list;
		el_users.value = users.join(',');
	} else {
		/* single user mode */
		if (user_complete_archive_uid == userid) {
			list = "<li class='special'>";
		} else {
			list = "<li class='enabled'>";
		}
		if (user_complete_confirm) {
			list = list.concat(str, " <a onclick=\"return confirm(gettext('Are you sure you want to remove this user / group?'));\" href=\"javascript: remove_user('"+userid+"', '"+user_complete_target+"', '"+user_complete_span+"', '"+user_complete_noempty+"');\">[X]</a>");
		} else {
			list = list.concat(str, " <a href=\"javascript: remove_user('"+userid+"', '"+user_complete_target+"', '"+user_complete_span+"', '"+user_complete_noempty+"');\">[X]</a>");
		}
		el_span.innerHTML = list;
		el_users.value = userid;
	}
	user_complete_initial = 0;

	document.getElementById(user_complete_field).value = '';
}

/* function to init the completion layer (called by the xmlhttp request) */
function user_init_layer(ary) {
	ary = unescape(ary);
	var ret = ary.split('#');
	var flag = 0;


	if (navigator.appName == "Opera")
		var lineheight = 'height: 20px;'; //fix for Opera
	else if (navigator.appVersion.indexOf("MSIE") != -1)
		var lineheight = '';
	else
		var lineheight = 'line-height: 0px;';

	/* prepare the html code */
	var html = '';
	var tmp = '';
	for (i=0;i<ret.length-1;i++) {
		flag++;
		tmp = ret[i].split('|');
		tmp[3] = tmp[0];

		if (user_complete_archive_uid == tmp[1]) {
			tmp[3] = "<li class='special'>" + tmp[3];
		} else if (tmp[1].match(/^G/g)) {
			tmp[3] = "<li class='group'>" + tmp[3];
		} else {
			tmp[3] = "<li class='enabled'>" + tmp[3];
		}

		tmp[0] = '<a class="autocomplete" href="javascript: void(0);" onclick="javascript: user_set_complete(\''+tmp[0]+'\', \''+tmp[1]+'\', \''+tmp[2]+'\');">'+tmp[3]+'</a>';
		html = html.concat('<tr><td style="', lineheight, '"><span class="d">', tmp[0], '</span></td></tr>');
	}
	if (flag==0) {
		html = html.concat('<tr><td colspan="3" align="center"><span class="d">geen resultaten</span></td></tr>');
	}
	html = html.concat('<tr><td style="border-top: 1px solid #999;"><span class="d"><a href="javascript: void(0);" onclick="return true;">sluiten</a></span></td><td style="border-top: 1px solid #999;" colspan="2" align="right">&nbsp;</td></tr>');

	/* MSIE doesn't like the TABLE tag inside innerHTML directly, workaround: */
	var div = document.createElement("DIV");
	div.innerHTML = "<table cellspacing=0 cellpadding=2 bgcolor='white' style='border: 1px outset black; width: 220px;'>" + html + "</table>";

	//retrieve target element
	var el = document.getElementById(user_complete_field);
	var ac = document.getElementById('user_layer_autocomplete');
	var ifr = document.getElementById('user_layer_iframe'); //needed for msie compatibility;

	var posx = parseInt(findPosX(el)) + 3;
	var posy = parseInt(findPosY(el)) + 26;

	ac.style.visibility = 'visible';
	ac.style.top = posy+'px';
	ac.style.left = posx+'px';

	ac.innerHTML = ' ';
	ac.appendChild(div);

	ac.onclick = function() {
		setTimeout('user_clear_layer();', 100);
	}
	user_complete_timer = setTimeout('user_clear_layer()',8000);

	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		ifr.style.display = '';
		ifr.style.top = ac.style.top;
		ifr.style.left = ac.style.left;
		ifr.style.width = ac.offsetWidth;
		ifr.style.height = ac.offsetHeight;
	}
}


/* load XML function */
function UserloadXMLList() {
	var ret = loadXMLContent('?mod=user&action=autocomplete&str='+user_complete_str+'&archive='+user_complete_archive+'&showgroups='+user_complete_groups+'&showcalendar='+user_complete_calendar);
	user_init_layer(ret);
}

/* function to init autocomplete on the field */
function autouser_complete_field(field, span, targt, archive, multiple, noempty, showgroups, archive_uid, confirmation, showcalendar) {
	var str = document.getElementById(field).value;

	clearTimeout(user_complete_timer);
	user_complete_field       = field;
	user_complete_span        = span;
	user_complete_target      = targt;
	user_complete_multi       = multiple;
	user_complete_noempty     = noempty;
	user_complete_groups      = showgroups;
	user_complete_archive_uid = archive_uid;
	user_complete_confirm     = confirmation;
	user_complete_calendar    = showcalendar;

	//get last index from search phrase
	str = str.replace(/,/g,' ');
	str = str.replace(/ {1,}/g,' ');
	str = str.split(' ');

	var last = str[str.length-1];
	user_complete_str = last;
	user_complete_archive = archive;

	if (last.length >= 1) {
		user_complete_timer = setTimeout("UserloadXMLList()", 500);
	} else {
		user_clear_layer();
	}
}
