/* attach an event handler on the select all input checkbox */
/* retrieve input checkbox element */
if (document.getElementById('checkbox_todo_toggle_all')) {
	document.getElementById('checkbox_todo_toggle_all').onclick = function() {
		todo_toggle_all(document.getElementById('checkbox_todo_toggle_all').checked);
	}
}

/* set the specified element to value provided */
function set(field, value) {
	/* bloody IE cannot getElementById('action') */
	if (field=='action') {
		document.getElementById('todoform').action.value = value;
	} else {
		document.getElementById(field).value = value;
	}
}

/* toggle all the todo checkbox items to the status sin it the parameter */
function todo_toggle_all(set_to_status) {
	var frm = document.getElementById('todoform');
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_todo\[/gi)) {
			frm.elements[i].checked = set_to_status;
		}
	}
}

/* delete multiple selected todos */
function selection_todo_delete() {
	var frm = document.getElementById('todoform');
	var doit = false;
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_todo\[/gi)) {
			if (frm.elements[i].checked == true) {
				doit = true;
			}
		}
	}
	if (doit) {
		if (confirm(gettext("Are you sure you want to delete the selected to-dos?"))) {
			set('action', 'delete_multi');
			document.getElementById('todoform').submit();
		}
	} else {
		alert(gettext("No items selected."));
	}
}

/* alter times of multiple todos */
function selection_todo_edit() {
	var frm = document.getElementById('todoform');
	var doit = false;
	for (i=0;i<frm.elements.length;i++) {
		if (frm.elements[i].name.match(/^checkbox_todo\[/gi)) {
			if (frm.elements[i].checked == true) {
				doit = true;
			}
		}
	}
	if (doit) {
		set('action', 'edit_multi');
		document.getElementById('todoform').submit();
	} else {
		alert(gettext("No items selected."));
	}
}

function todo_delete(id, pop, xml) {
	url = 'index.php?mod=todo&action=delete_todo&todoid='+id;
	if (confirm(gettext("Are you sure you want to delete this todo?"))) {
		if (xml) {
			loadXML(url);
			document.location.href=document.location.href;
		} else {
			if (pop) {
				popup(url + '&hide=1');
			} else {
				document.location.href = url;
			}
		}
	}
}

function todo_edit(id, pop) {
	url = 'index.php?mod=todo&action=edit_todo&todoid='+id;
	if (pop) {
		popup(url + '&hide=1');
	} else {
		document.location.href = url;
	}
}

function todo_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}

	document.getElementById('todoedit').submit();
}

function todo_to_cal(id) {
	url = 'index.php?mod=calendar&action=edit&id=0&todoid='+id;
	popup(url, 'plantodo', 0, 0, 1);
}

function todo_to_reg(id) {
	url = 'index.php?mod=calendar&action=reg_input&todo_id='+id;
	popup(url, 'regtodo', 0, 0, 1);
}

function toonInfo(id) {
	loadXML('index.php?mod=todo&action=show_info&id='+id);
}

function selectRel(id, relname) {
	document.getElementById('todoaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}

function update_check(active) {
	var el  = document.getElementById('todo_check_layer');
	var el2 = document.getElementById('action_save');
	if (active == 1) {
		el.style.border='2px dotted red';
		el.innerHTML=gettext("end time should be after start time");
		el2.style.visibility='hidden';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("no errors");
		el2.style.visibility='visible';
	}
}

var todo_check = Array('todoid','todotimestamp_day','todotimestamp_month','todotimestamp_year','todotimestamp_end_day', 'todotimestamp_end_month','todotimestamp_end_year');
function checkTodoDates() {
	var uri = '?mod=todo&action=xml_check';
	for (i=0;i<todo_check.length;i++) {
		uri = uri.concat('&', todo_check[i].replace(/^todo/g,''), '=', document.getElementById(todo_check[i]).value);
	}
	loadXML(uri);

}

if (document.getElementById(todo_check[1])) {
	for (i=0;i<todo_check.length;i++) {
		document.getElementById(todo_check[i]).onchange = function() {
			checkTodoDates();
		}

		dx = document.getElementById("todotimestamp_day");
		dx.onchange = function() {
			document.getElementById("todotimestamp_end_day").value = dx.value;
		}
	}

	addLoadEvent( checkTodoDates() );
}

function selectProject(id, relname) {
	document.getElementById('todoproject_id').value = id;
	document.getElementById('searchproject').innerHTML = relname;
}

function pickProject() {
	var address_id = document.getElementById('todoaddress_id').value;
	popup('?mod=project&action=searchProject&actief=1&deb='+address_id, 'searchproject', 650, 500, 1);
}

