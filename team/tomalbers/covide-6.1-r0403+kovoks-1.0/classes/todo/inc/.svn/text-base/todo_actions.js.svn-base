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
	if (confirm(gettext("Weet u zeker dat u de geselecteerde todos wilt verwijderen?"))) {
		set('action', 'delete_multi');
		document.getElementById('todoform').submit();
	}
}

/* alter times of multiple todos */
function selection_todo_edit() {
	set('action', 'edit_multi');
	document.getElementById('todoform').submit();
}

function todo_delete(id) {
	url = 'index.php?mod=todo&action=delete_todo&todoid='+id;
	if (confirm(gettext("Weet u zeker dat u deze todo wilt verwijderen?"))) {
		document.location.href = url;
	}
}

function todo_edit(id) {
	url = 'index.php?mod=todo&action=edit_todo&todoid='+id;
	document.location.href = url;
}

function todo_save() {
	document.getElementById('todoedit').submit();
}

function todo_to_cal(id) {
	url = 'index.php?mod=calendar&action=edit&id=0&todoid='+id;
	popup(url, 'plantodo', 0, 0, 1);
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
		el.innerHTML=gettext("start tijd kan niet na de eind tijd liggen");
		el2.style.visibility='hidden';
	} else {
		el.style.border='2px dotted green';
		el.innerHTML=gettext("alles ok");
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
	}

	addLoadEvent( checkTodoDates() );
}

