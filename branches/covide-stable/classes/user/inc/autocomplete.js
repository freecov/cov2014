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
var user_complete_keyevent;

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
				list = list.concat("<li style='margin-left:25px' class='special'>");
			} else if (userid.match(/^G/g)) {
				list = list.concat("<li style='margin-left:25px' class='group'>");
			} else {
				list = list.concat("<li style='margin-left:25px' class='enabled'>");
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
var curNode = 0;
var j = 0;
function user_init_layer(ary) {
	ary = unescape(ary);
	var ret = ary.split('#');
	var flag = 0;
	curNode = 0;
	var styleIE = "";
	var color = "";
	if (navigator.appName == "Opera") {
		var lineheight = 'height: 20px;'; //fix for Opera
	} else if (navigator.appVersion.indexOf("MSIE") != -1) {
		var lineheight = '';
		//fix list-style in IE
		var styleIE= "margin-left:25px;";
	} else {
		var lineheight = 'line-height: 0px;';
	}

	/* prepare the html code */
	var html = '';
	var tmp = '';
	for (i=0; i < ret.length-1; i++) {
		//set different colors in row
		color = (j++ % 2) ? '#FFFFFF' : '#f1f1f1';
		flag++;
		tmp = ret[i].split('|');
		tmp[3] = tmp[0];

		if (user_complete_archive_uid == tmp[1]) {
			tmp[3] = "<li class='special' style="+styleIE+">" + tmp[3];
		} else if (tmp[1].match(/^G/g)) {
			tmp[3] = "<li class='group' style="+styleIE+">" + tmp[3];
		} else {
			tmp[3] = "<li class='enabled' style="+styleIE+">" + tmp[3];
		}

		tmp[0] = '<a class="autocomplete" id=' + j + ' href="javascript: void(0);" onclick="javascript: user_set_complete(\''+tmp[0]+'\', \''+tmp[1]+'\', \''+tmp[2]+'\');"><span style="width:220px;" class="notSelected">'+tmp[3]+'</span></a>';
		html = html.concat('<tr bgcolor='+ color +' style="height:20px;"><td style="'+ lineheight+ '"><span class="d">'+ tmp[0]+ '</span></td></tr>');
	}
	if (flag==0) {
		html = html.concat('<tr><td colspan="3" align="center"><span class="d">geen resultaten</span></td></tr>');
	}

	/* MSIE doesn't like the TABLE tag inside innerHTML directly, workaround: */
	var div = document.createElement("DIV");
	div.innerHTML = "<table cellspacing=0 cellpadding=2 bgcolor='white' style='border: 1px outset black; padding:5px; width: 220px;'>" + html + "</table>";

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
	selectCurrent(curNode);
	ac.onclick = function() {
		setTimeout('user_clear_layer();', 100);
	}
	//remove autosuggestbox when user click on screen
	$(window).click(function() {
		setTimeout('user_clear_layer();', 100);
	})
	//fix for IE remove autosuggestbox
	if (jQuery.browser.msie) {
		$(document).click(function() {
			setTimeout('user_clear_layer();', 100);
		})
	}

	//MSIE render fix!
	if (navigator.appVersion.indexOf("MSIE")!=-1){
		ifr.style.display = '';
		ifr.style.top = ac.style.top;
		ifr.style.left = ac.style.left;
		ifr.style.width = ac.offsetWidth;
		ifr.style.height = ac.offsetHeight;
	}
}

/**
 * Function to select an user, insert class into <span> current or notSelected
 *
 * @param int curNode: curnode is the selected user in the autosuggestbox, select automatic the first user, started with 0
 * @return int selectedNode: the current selected node(user)
*/
function selectCurrent(curNode) {
	var selectedNode = "";
	//get all the users
	for (var i=0; i < $(".autocomplete").length; i++) {
		var oNode = $(".autocomplete").children()[i];
		//for browser Chrome and Safari filter undefined, else javascripts errors
		if (oNode != null) {
			var className = oNode.className;
		}
		//if i in array is same as curNode is the selected node, set this node to current
		if (i == curNode) {
			$(oNode).removeClass("notSelected");
			$(oNode).addClass("current");
			//get the <span> element and change class
			var rowSelected = $(oNode).parent().parent().parent();
			$(rowSelected).removeClass("notSelected");
			$(rowSelected).addClass("current");
			selectedNode = oNode;
		}
		if (className == "current") {
			//change other <span> class elements to notSelected
			$(oNode).removeClass("current");
			$(oNode).addClass("notSelected");
			var rowSelected = $(oNode).parent().parent().parent();
			$(rowSelected).removeClass("current");
			$(rowSelected).addClass("notSelected");
		}
	}
	//return selectedNode when enter is pressed
	return selectedNode;
}

/* load XML function */
function UserloadXMLList() {
	var ret = loadXMLContent('?mod=user&action=autocomplete&str='+user_complete_str+'&archive='+user_complete_archive+'&showgroups='+user_complete_groups+'&showcalendar='+user_complete_calendar);
	user_init_layer(ret);
}

/* function to init autocomplete on the field */
function autouser_complete_field(field, span, targt, archive, multiple, noempty, showgroups, archive_uid, confirmation, showcalendar, e) {
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
	//get the keyCode, for different browsers
	if (window.event) {
		key = window.event.keyCode; //IE and Opera
	} else {
		key = e.which; //Firefox and others
	}
	user_complete_keyevent = key;
	//get last index from search phrase
	str = str.replace(/,/g,' ');
	str = str.replace(/ {1,}/g,' ');
	str = str.split(' ');

	var last = str[str.length-1];
	user_complete_str = last;
	user_complete_archive = archive;
	//if input value is empty remove autosuggestbox
	if ( str == "") {
		user_clear_layer();
	}
	//if enter or arrow is pressed
	if (key == 40 || key == 38 || key == 13) {
		var countUsers = 0;
		//fix for Safari and Chrome-> count span and not autocomplete!!
		var countUsers = $(".autocomplete span").length;
		countUsers = countUsers - 1;
		var oNode = '';
		//arrow down is pressed
		if (key == 40) {
			if (curNode < countUsers) {
				curNode = curNode + 1;
				selectCurrent(curNode);
			}
		}
		//arrow up is pressed
		if (key == 38) {
			if (curNode > 0) {
				curNode = curNode - 1;
				selectCurrent(curNode);
			}
		}
		//enter is pressed, get current user and print user on screen
		if (key == 13) {
			selectedNode = selectCurrent(curNode);
			var test = $(selectedNode).parent()
			$(selectedNode).parent().click();
			//IE doesn't remove the div when enter is pressed...so for IE remove div
			if (jQuery.browser.msie) {
				user_clear_layer();
			}
		}
	} else {
		//get the users from the database
		if (last.length >= 1) {
			user_complete_timer = setTimeout("UserloadXMLList()", 500);
		}
	}
}